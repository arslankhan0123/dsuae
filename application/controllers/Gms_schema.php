<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/** Additive schema update for the Sales/Item/PDF changes. No data reset. */
class Gms_schema extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->library('session');
        $this->load->helper(array('url', 'form'));
        // Use the primary administrator, not a public standalone PHP endpoint.
        if (!$this->session->userdata('logged_in') || (int)$this->session->userdata('inv_userid') !== 1) {
            show_error('Sign in as the primary administrator to run database updates.', 403);
        }
    }

    private function definitions() {
        return array(
            'db_sales' => array(
                'customer_trn_no' => 'VARCHAR(100) NULL DEFAULT NULL',
                'invoice_type' => "VARCHAR(50) NULL DEFAULT 'Cash'",
                'payment_type' => "VARCHAR(50) NULL DEFAULT 'Cash'",
                'card_type' => 'VARCHAR(50) NULL DEFAULT NULL',
                'customer_mobile' => 'VARCHAR(50) NULL DEFAULT NULL',
                'customer_email' => 'VARCHAR(100) NULL DEFAULT NULL',
                'payment_terms' => 'VARCHAR(100) NULL DEFAULT NULL',
                'address' => 'VARCHAR(500) NULL DEFAULT NULL',
                'whatsapp_no' => 'VARCHAR(50) NULL DEFAULT NULL',
                'salesman_id' => 'INT(11) NULL DEFAULT NULL',
            ),
            'db_salesreturn' => array('customer_trn_no' => 'VARCHAR(100) NULL DEFAULT NULL'),
            'db_salespayments' => array('advance_adjusted' => 'DECIMAL(20,4) NULL DEFAULT 0'),
            'db_items' => array('item_image' => 'TEXT NULL', 'item_name_arabic' => 'VARCHAR(255) NULL DEFAULT NULL'),
            'db_store' => array(
                'pdf_part_number_popup' => 'TINYINT(1) NOT NULL DEFAULT 1',
                'location_url' => 'TEXT NULL',
                'whatsapp_number' => 'VARCHAR(50) NULL DEFAULT NULL',
                'item_barcode_label' => "VARCHAR(20) NOT NULL DEFAULT 'part_no'",
                'invoice_declaration' => 'TEXT NULL',
                'paid_seal' => 'TINYINT(1) NOT NULL DEFAULT 0',
                'default_tax_type' => "VARCHAR(20) NOT NULL DEFAULT 'Inclusive'",
                'default_tax_id' => 'INT(11) NULL DEFAULT NULL',
                'store_name_arabic' => 'VARCHAR(255) NULL DEFAULT NULL',
                'address_arabic' => 'TEXT NULL',
                'cr_no' => 'VARCHAR(100) NULL DEFAULT NULL',
                'bank_name' => 'VARCHAR(255) NULL DEFAULT NULL',
                'account_name' => 'VARCHAR(255) NULL DEFAULT NULL',
                'account_number' => 'VARCHAR(100) NULL DEFAULT NULL',
                'iban_number' => 'VARCHAR(100) NULL DEFAULT NULL',
                'routing_number' => 'VARCHAR(100) NULL DEFAULT NULL',
                'show_website_qr' => 'TINYINT(1) NOT NULL DEFAULT 1',
                'show_location_qr' => 'TINYINT(1) NOT NULL DEFAULT 1',
                'show_whatsapp_qr' => 'TINYINT(1) NOT NULL DEFAULT 1',
                'show_invoice_qr' => 'TINYINT(1) NOT NULL DEFAULT 1',
                'show_zatca_qr' => 'TINYINT(1) NOT NULL DEFAULT 1',
            ),
            'db_tax' => array('is_default' => 'TINYINT(1) NOT NULL DEFAULT 0'),
        );
    }

    public function index() {
        $this->output->set_header('Cache-Control: no-store');
        $this->output->set_header('X-Content-Type-Options: nosniff');
        $apply = $this->input->method() === 'post' && $this->input->post('apply') === '1';
        $rows = array(); $missing = 0; $errors = 0; $locked = false;
        $debug = $this->db->db_debug;
        $this->db->db_debug = false;
        $lockName = 'gms-schema-'.substr(hash('sha256', $this->db->database), 0, 24);
        try {
            if ($apply) {
                $lock = $this->db->query('SELECT GET_LOCK(?, 5) AS acquired', array($lockName));
                if (!$lock || (int)$lock->row()->acquired !== 1) {
                    show_error('Another schema update is running. Try again shortly.', 409);
                    return;
                }
                $locked = true;
            }
            foreach ($this->definitions() as $table => $columns) {
                if (!$this->db->table_exists($table)) {
                    $rows[] = array($table, '', 'Base table missing: import the original GMS database first.');
                    $errors++;
                    continue;
                }
                // One metadata read per table; no reliance on cached field_exists after ALTER.
                $fields = $this->db->query('SHOW COLUMNS FROM `'.$table.'`');
                if (!$fields) { $errors++; $rows[] = array($table, '', 'Cannot inspect columns.'); continue; }
                $existing = array();
                foreach ($fields->result() as $field) $existing[] = $field->Field;
                foreach ($columns as $column => $definition) {
                    if (in_array($column, $existing, true)) {
                        $rows[] = array($table, $column, 'Already exists — unchanged');
                        continue;
                    }
                    if (!$apply) {
                        $missing++;
                        $rows[] = array($table, $column, 'Missing — will add');
                        continue;
                    }
                    if ($this->db->query('ALTER TABLE `'.$table.'` ADD COLUMN `'.$column.'` '.$definition)) {
                        $rows[] = array($table, $column, 'Added');
                    } else {
                        $errors++;
                        $rows[] = array($table, $column, 'Failed — check database ALTER privileges and application log.');
                    }
                }
            }
        } finally {
            if ($locked) $this->db->query('SELECT RELEASE_LOCK(?)', array($lockName));
            $this->db->db_debug = $debug;
        }
        $html = '<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>GMS database update</title>'
            .'<style>body{font:15px Arial;max-width:1000px;margin:30px auto;padding:0 16px}table{border-collapse:collapse;width:100%}td,th{border:1px solid #ddd;padding:8px;text-align:left}th{background:#d9edf7}button{padding:12px;background:#1686b5;color:white;border:0;cursor:pointer}</style></head><body>'
            .'<h1>GMS database update</h1><p>Adds missing columns only. Existing columns and records are preserved. Take a database backup before applying. ALTER TABLE may briefly lock a table.</p>';
        if ($apply) $html .= '<p><strong>'.($errors ? 'Completed with errors; review below. It is safe to rerun after resolving them.' : 'Schema check/update completed successfully.').'</strong></p>';
        else $html .= '<p>Preview only. Missing columns: '.(int)$missing.'.</p>';
        if (!$errors && $missing > 0) {
            $html .= form_open('gms_schema').'<input type="hidden" name="apply" value="1"><button type="submit">Add missing columns</button>'.form_close();
        }
        $html .= '<p><a href="'.html_escape(site_url('gms_schema')).'">Refresh schema status</a></p><table><thead><tr><th>Table</th><th>Column</th><th>Status</th></tr></thead><tbody>';
        foreach ($rows as $row) {
            $html .= '<tr>';
            foreach ($row as $value) $html .= '<td>'.html_escape($value).'</td>';
            $html .= '</tr>';
        }
        $html .= '</tbody></table><p>After deployment, remove application/controllers/Gms_schema.php from the server.</p></body></html>';
        $this->output->set_content_type('text/html', 'utf-8')->set_output($html);
    }
}
