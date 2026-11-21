<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Opening Cash: one screen to add / edit / delete opening cash entries per branch.
 * Records are stored in `db_opening_cash`.
 */
class Opening_cash_model extends CI_Model {

	public $table = 'db_opening_cash';

	public function __construct()
	{
		parent::__construct();
		$this->ensure_table();
	}

	/** Create the table the first time the Opening Cash page is opened */
	public function ensure_table()
	{
		$this->db->query("CREATE TABLE IF NOT EXISTS `db_opening_cash` (
			`id` INT(11) NOT NULL AUTO_INCREMENT,
			`store_id` INT(11) NOT NULL,
			`opening_date` DATE NOT NULL,
			`opening_time` VARCHAR(50) NULL DEFAULT NULL,
			`cash_type` VARCHAR(50) NOT NULL DEFAULT 'Opening Cash',
			`amount` DOUBLE(20,2) NOT NULL DEFAULT 0,
			`created_by` VARCHAR(50) NULL DEFAULT NULL,
			`created_date` DATE NULL DEFAULT NULL,
			`created_time` VARCHAR(50) NULL DEFAULT NULL,
			`updated_by` VARCHAR(50) NULL DEFAULT NULL,
			`updated_date` DATE NULL DEFAULT NULL,
			`updated_time` VARCHAR(50) NULL DEFAULT NULL,
			`system_ip` VARCHAR(100) NULL DEFAULT NULL,
			`status` INT(11) NOT NULL DEFAULT 1,
			PRIMARY KEY (`id`),
			KEY `store_date` (`store_id`, `opening_date`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8");
	}

	/** All entries of the current branch, newest first */
	public function get_list()
	{
		$q = $this->db->where('store_id', get_current_store_id())
			->order_by('opening_date', 'desc')
			->order_by('id', 'desc')
			->get($this->table);
		$rows = array();
		if ($q) {
			foreach ($q->result() as $r) {
				$time = '';
				if (!empty($r->opening_time)) {
					$ts = strtotime($r->opening_time);
					$time = ($ts !== false) ? strtoupper(date('g:i A', $ts)) : $r->opening_time;
				}
				$rows[] = array(
					'id'         => (int) $r->id,
					'date'       => show_date($r->opening_date),
					'time'       => $time,
					'type'       => $r->cash_type,
					'amount'     => store_number_format((float) $r->amount),
					'amount_raw' => store_number_format((float) $r->amount, false),
					'created_by' => (string) $r->created_by,
				);
			}
		}
		return $rows;
	}

	/** Insert a new entry, or update the entry with the given id */
	public function save($id, $date, $amount)
	{
		$store_id = get_current_store_id();
		$user = $this->session->userdata('inv_username');

		if ((int) $id > 0) {
			$existing = $this->db->where('id', (int) $id)->where('store_id', $store_id)->get($this->table)->row();
			if (empty($existing)) {
				return 'Record not found.';
			}
			$ok = $this->db->where('id', (int) $id)->where('store_id', $store_id)->update($this->table, array(
				'opening_date' => $date,
				'amount'       => $amount,
				'updated_by'   => $user,
				'updated_date' => date('Y-m-d'),
				'updated_time' => date('H:i:s'),
			));
			return $ok ? 'success' : 'failed';
		}

		$ok = $this->db->insert($this->table, array(
			'store_id'     => $store_id,
			'opening_date' => $date,
			'opening_time' => date('H:i:s'),
			'cash_type'    => 'Opening Cash',
			'amount'       => $amount,
			'created_by'   => $user,
			'created_date' => date('Y-m-d'),
			'created_time' => date('H:i:s'),
			'system_ip'    => isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : null,
			'status'       => 1,
		));
		return $ok ? 'success' : 'failed';
	}

	public function delete($id)
	{
		$ok = $this->db->where('id', (int) $id)->where('store_id', get_current_store_id())->delete($this->table);
		return ($ok && $this->db->affected_rows() > 0) ? 'success' : 'failed';
	}
}
