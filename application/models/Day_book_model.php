<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Day Book: daily sales / purchase / payment summary + cash drawer declaration.
 * Cash declarations are stored in `db_daybook_cash` (one row per store per date).
 */
class Day_book_model extends CI_Model {

	public $table = 'db_daybook_cash';

	// Drawer denominations shown on the Day Book page
	public $notes = array('1000', '500', '200', '100', '50', '20', '10', '5');
	public $coins = array('1.00', '0.50', '0.25', '0.10');

	public function __construct()
	{
		parent::__construct();
		$this->ensure_table();
	}

	/** Create the declaration table the first time the Day Book is opened */
	public function ensure_table()
	{
		$this->db->query("CREATE TABLE IF NOT EXISTS `db_daybook_cash` (
			`id` INT(11) NOT NULL AUTO_INCREMENT,
			`store_id` INT(11) NOT NULL,
			`declaration_date` DATE NOT NULL,
			`denominations` TEXT NULL,
			`notes_total` DOUBLE(20,2) NOT NULL DEFAULT 0,
			`coins_total` DOUBLE(20,2) NOT NULL DEFAULT 0,
			`total_counted` DOUBLE(20,2) NOT NULL DEFAULT 0,
			`opening_cash` DOUBLE(20,2) NOT NULL DEFAULT 0,
			`expected_cash` DOUBLE(20,2) NOT NULL DEFAULT 0,
			`cash_difference` DOUBLE(20,2) NOT NULL DEFAULT 0,
			`created_by` VARCHAR(50) NULL DEFAULT NULL,
			`created_date` DATE NULL DEFAULT NULL,
			`created_time` VARCHAR(50) NULL DEFAULT NULL,
			`updated_by` VARCHAR(50) NULL DEFAULT NULL,
			`updated_date` DATE NULL DEFAULT NULL,
			`updated_time` VARCHAR(50) NULL DEFAULT NULL,
			`system_ip` VARCHAR(100) NULL DEFAULT NULL,
			`status` INT(11) NOT NULL DEFAULT 1,
			PRIMARY KEY (`id`),
			UNIQUE KEY `store_date` (`store_id`, `declaration_date`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8");
	}

	/** Accept only YYYY-MM-DD; anything else falls back to today */
	public function clean_date($date)
	{
		$date = trim((string) $date);
		if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date) && strtotime($date) !== false) {
			return date('Y-m-d', strtotime($date));
		}
		return date('Y-m-d');
	}

	/** Run a query that returns one number in column `v` */
	private function val($sql, $params)
	{
		$q = $this->db->query($sql, $params);
		if (!$q) {
			return 0.0;
		}
		$row = $q->row();
		return ($row && isset($row->v)) ? (float) $row->v : 0.0;
	}

	private function vat_label($vat, $base)
	{
		if ($base > 0 && $vat > 0) {
			$pct = ($vat / $base) * 100;
			if (abs($pct - round($pct)) < 0.05) {
				return 'VAT (' . (int) round($pct) . '%)';
			}
		}
		return 'VAT';
	}

	/** Saved cash declaration for a date (or null) */
	public function get_declaration($date, $store_id = null)
	{
		$store_id = ($store_id === null) ? get_current_store_id() : $store_id;
		$q = $this->db->where('store_id', $store_id)->where('declaration_date', $date)->get($this->table);
		return ($q && $q->num_rows() > 0) ? $q->row() : null;
	}

	/** Everything the Day Book page shows for one date */
	public function get_summary($date)
	{
		$store_id = get_current_store_id();
		$p = array($store_id, $date);

		/* ---------------- Sales ---------------- */
		$sales_incl = $this->val("SELECT COALESCE(SUM(grand_total),0) AS v FROM db_sales WHERE store_id=? AND sales_date=? AND sales_status='Final'", $p);
		$sales_vat = $this->val("SELECT COALESCE(SUM(si.tax_amt),0) AS v FROM db_salesitems si INNER JOIN db_sales s ON s.id=si.sales_id WHERE s.store_id=? AND s.sales_date=? AND s.sales_status='Final'", $p);
		// tot_discount_to_all_amt already includes the row discounts on new invoices, so take the larger of the two
		$coupon_sql = $this->db->field_exists('coupon_amt', 'db_sales') ? " + COALESCE(s.coupon_amt,0)" : "";
		$sales_disc = $this->val("SELECT COALESCE(SUM(GREATEST(COALESCE(s.tot_discount_to_all_amt,0), (SELECT COALESCE(SUM(d.discount_amt),0) FROM db_salesitems d WHERE d.sales_id=s.id))" . $coupon_sql . "),0) AS v FROM db_sales s WHERE s.store_id=? AND s.sales_date=? AND s.sales_status='Final'", $p);
		$sret_incl = $this->val("SELECT COALESCE(SUM(grand_total),0) AS v FROM db_salesreturn WHERE store_id=? AND return_date=?", $p);
		$sret_vat = $this->val("SELECT COALESCE(SUM(ri.tax_amt),0) AS v FROM db_salesitemsreturn ri INNER JOIN db_salesreturn r ON r.id=ri.return_id WHERE r.store_id=? AND r.return_date=?", $p);

		$sales_excl = $sales_incl - $sales_vat;
		$sret_excl = $sret_incl - $sret_vat;
		$sales = array(
			'gross'     => $sales_excl + $sales_disc,
			'discount'  => $sales_disc,
			'returns'   => $sret_excl,
			'net_excl'  => $sales_excl - $sret_excl,
			'vat'       => $sales_vat - $sret_vat,
			'net_incl'  => $sales_incl - $sret_incl,
		);
		$sales['vat_label'] = $this->vat_label($sales_vat, $sales_excl);

		/* ---------------- Purchase ---------------- */
		$pur_incl = $this->val("SELECT COALESCE(SUM(grand_total),0) AS v FROM db_purchase WHERE store_id=? AND purchase_date=?", $p);
		$pur_vat = $this->val("SELECT COALESCE(SUM(pit.tax_amt),0) AS v FROM db_purchaseitems pit INNER JOIN db_purchase pu ON pu.id=pit.purchase_id WHERE pu.store_id=? AND pu.purchase_date=?", $p);
		$pur_disc = $this->val("SELECT COALESCE(SUM(COALESCE(pu.tot_discount_to_all_amt,0) + (SELECT COALESCE(SUM(d.discount_amt),0) FROM db_purchaseitems d WHERE d.purchase_id=pu.id)),0) AS v FROM db_purchase pu WHERE pu.store_id=? AND pu.purchase_date=?", $p);
		$pret_incl = $this->val("SELECT COALESCE(SUM(grand_total),0) AS v FROM db_purchasereturn WHERE store_id=? AND return_date=?", $p);
		$pret_vat = $this->val("SELECT COALESCE(SUM(ri.tax_amt),0) AS v FROM db_purchaseitemsreturn ri INNER JOIN db_purchasereturn r ON r.id=ri.return_id WHERE r.store_id=? AND r.return_date=?", $p);

		$pur_excl = $pur_incl - $pur_vat;
		$pret_excl = $pret_incl - $pret_vat;
		$purchase = array(
			'gross'     => $pur_excl + $pur_disc,
			'discount'  => $pur_disc,
			'returns'   => $pret_excl,
			'net_excl'  => $pur_excl - $pret_excl,
			'vat'       => $pur_vat - $pret_vat,
			'net_incl'  => $pur_incl - $pret_incl,
		);
		$purchase['vat_label'] = $this->vat_label($pur_vat, $pur_excl);

		/* ---------------- Payment summary (money received on this date) ---------------- */
		$payments = array(); // KEY => array(label, amount)
		$q_types = $this->db->query("SELECT payment_type FROM db_paymenttypes WHERE store_id=? AND status=1 ORDER BY id ASC", array($store_id));
		if ($q_types) {
			foreach ($q_types->result() as $t) {
				$key = strtoupper(trim((string) $t->payment_type));
				if ($key !== '' && !isset($payments[$key])) {
					$payments[$key] = array('label' => ucwords(strtolower(trim((string) $t->payment_type))), 'amount' => 0.0);
				}
			}
		}
		$q_pay = $this->db->query("SELECT UPPER(TRIM(COALESCE(payment_type,''))) AS t, COALESCE(SUM(payment),0) AS v FROM db_salespayments WHERE store_id=? AND payment_date=? GROUP BY UPPER(TRIM(COALESCE(payment_type,'')))", $p);
		if ($q_pay) {
			foreach ($q_pay->result() as $r) {
				$key = ($r->t === '' || $r->t === null) ? 'OTHER' : $r->t;
				if (!isset($payments[$key])) {
					$payments[$key] = array('label' => ucwords(strtolower($key)), 'amount' => 0.0);
				}
				$payments[$key]['amount'] += (float) $r->v;
			}
		}
		$on_account = $this->val("SELECT COALESCE(SUM(GREATEST(COALESCE(grand_total,0)-COALESCE(paid_amount,0),0)),0) AS v FROM db_sales WHERE store_id=? AND sales_date=? AND sales_status='Final'", $p);
		$payment_total = $on_account;
		foreach ($payments as $row) {
			$payment_total += $row['amount'];
		}

		/* ---------------- Cash reconciliation ---------------- */
		$cash_sales = $this->val("SELECT COALESCE(SUM(payment),0) AS v FROM db_salespayments WHERE store_id=? AND payment_date=? AND LOWER(payment_type) LIKE '%cash%'", $p);
		$cash_refunds = $this->val("SELECT COALESCE(SUM(payment),0) AS v FROM db_salespaymentsreturn WHERE store_id=? AND payment_date=? AND LOWER(payment_type) LIKE '%cash%'", $p);
		$cash_expense = $this->val("SELECT COALESCE(SUM(expense_amt),0) AS v FROM db_expense WHERE store_id=? AND expense_date=? AND LOWER(payment_type) LIKE '%cash%'", $p);
		$cash_purchase = $this->val("SELECT COALESCE(SUM(payment),0) AS v FROM db_purchasepayments WHERE store_id=? AND payment_date=? AND LOWER(payment_type) LIKE '%cash%'", $p);
		$cash_purchase_ret = $this->val("SELECT COALESCE(SUM(payment),0) AS v FROM db_purchasepaymentsreturn WHERE store_id=? AND payment_date=? AND LOWER(payment_type) LIKE '%cash%'", $p);
		$cash_paid_out = $cash_expense + $cash_purchase - $cash_purchase_ret;

		// Opening cash = cash counted in the most recent saved declaration before this date
		$opening = $this->val("SELECT total_counted AS v FROM db_daybook_cash WHERE store_id=? AND declaration_date<? ORDER BY declaration_date DESC LIMIT 1", $p);
		// If Opening Cash was entered for this date (Accounts > Opening Cash), that amount is used instead
		if ($this->db->table_exists('db_opening_cash')) {
			$oc = $this->db->query("SELECT COUNT(*) AS c, COALESCE(SUM(amount),0) AS v FROM db_opening_cash WHERE store_id=? AND opening_date=?", $p)->row();
			if (!empty($oc) && (int) $oc->c > 0) {
				$opening = (float) $oc->v;
			}
		}
		$expected = $opening + $cash_sales - $cash_refunds - $cash_paid_out;

		/* ---------------- Saved declaration ---------------- */
		$decl = $this->get_declaration($date, $store_id);
		$counts = array('notes' => array(), 'coins' => array());
		$saved = ($decl && !empty($decl->denominations)) ? json_decode($decl->denominations, true) : array();
		foreach ($this->notes as $n) {
			$counts['notes'][$n] = (isset($saved['notes'][$n])) ? max(0, (int) $saved['notes'][$n]) : 0;
		}
		foreach ($this->coins as $c) {
			$counts['coins'][$c] = (isset($saved['coins'][$c])) ? max(0, (int) $saved['coins'][$c]) : 0;
		}

		return array(
			'date'      => $date,
			'is_today'  => ($date === date('Y-m-d')),
			'sales'     => $sales,
			'purchase'  => $purchase,
			'payments'  => $payments,
			'on_account'    => $on_account,
			'payment_total' => $payment_total,
			'cash'      => array(
				'opening'   => $opening,
				'sales'     => $cash_sales,
				'refunds'   => $cash_refunds,
				'paid_out'  => $cash_paid_out,
				'expected'  => $expected,
				'counted'   => $decl ? (float) $decl->total_counted : 0.0,
			),
			'notes'     => $this->notes,
			'coins'     => $this->coins,
			'counts'    => $counts,
			'declaration' => $decl,
		);
	}

	/** Save (insert or update) the cash declaration for a date */
	public function save_declaration($date, $counts)
	{
		$store_id = get_current_store_id();
		$clean = array('notes' => array(), 'coins' => array());
		$notes_total = 0.0;
		$coins_total = 0.0;
		foreach ($this->notes as $n) {
			$qty = (isset($counts['notes'][$n])) ? max(0, (int) $counts['notes'][$n]) : 0;
			$clean['notes'][$n] = $qty;
			$notes_total += $qty * (float) $n;
		}
		foreach ($this->coins as $c) {
			$qty = (isset($counts['coins'][$c])) ? max(0, (int) $counts['coins'][$c]) : 0;
			$clean['coins'][$c] = $qty;
			$coins_total += $qty * (float) $c;
		}
		$notes_total = round($notes_total, 2);
		$coins_total = round($coins_total, 2);
		$total = round($notes_total + $coins_total, 2);

		$summary = $this->get_summary($date);
		$expected = round((float) $summary['cash']['expected'], 2);

		$user = $this->session->userdata('inv_username');
		$info = array(
			'denominations'   => json_encode($clean),
			'notes_total'     => $notes_total,
			'coins_total'     => $coins_total,
			'total_counted'   => $total,
			'opening_cash'    => round((float) $summary['cash']['opening'], 2),
			'expected_cash'   => $expected,
			'cash_difference' => round($total - $expected, 2),
			'system_ip'       => isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : null,
			'status'          => 1,
		);

		$existing = $this->get_declaration($date, $store_id);
		if ($existing) {
			$info['updated_by'] = $user;
			$info['updated_date'] = date('Y-m-d');
			$info['updated_time'] = date('h:i:s a');
			$ok = $this->db->where('id', $existing->id)->update($this->table, $info);
		} else {
			$info['store_id'] = $store_id;
			$info['declaration_date'] = $date;
			$info['created_by'] = $user;
			$info['created_date'] = date('Y-m-d');
			$info['created_time'] = date('h:i:s a');
			$ok = $this->db->insert($this->table, $info);
		}

		return array(
			'status'      => $ok ? 'success' : 'failed',
			'notes_total' => $notes_total,
			'coins_total' => $coins_total,
			'total'       => $total,
			'expected'    => $expected,
			'difference'  => round($total - $expected, 2),
		);
	}
}
