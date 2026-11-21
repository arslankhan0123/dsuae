<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Day Book: daily sales / purchase / payment summary + cash drawer count.
 * The drawer count is stored row-wise in `bank_note_coin` (one row per note / coin per branch per date).
 */
class Day_book_model extends CI_Model {

	public $table = 'bank_note_coin';

	// Drawer denominations shown on the Day Book page
	public $notes = array('1000', '500', '200', '100', '50', '20', '10', '5');
	public $coins = array('1.00', '0.50', '0.25', '0.10');

	public function __construct()
	{
		parent::__construct();
		$this->ensure_table();
	}

	/** Create the bank note / coin table the first time the Day Book is opened */
	public function ensure_table()
	{
		$this->db->query("CREATE TABLE IF NOT EXISTS `bank_note_coin` (
			`id` INT(11) NOT NULL AUTO_INCREMENT,
			`store_id` INT(11) NOT NULL,
			`entry_date` DATE NOT NULL,
			`currency` DECIMAL(10,2) NOT NULL,
			`type` VARCHAR(10) NOT NULL,
			`qty` INT(11) NOT NULL DEFAULT 0,
			`amount` DOUBLE(20,2) NOT NULL DEFAULT 0,
			`created_by` VARCHAR(50) NULL DEFAULT NULL,
			`created_date` DATE NULL DEFAULT NULL,
			`created_time` VARCHAR(50) NULL DEFAULT NULL,
			`updated_by` VARCHAR(50) NULL DEFAULT NULL,
			`updated_date` DATE NULL DEFAULT NULL,
			`updated_time` VARCHAR(50) NULL DEFAULT NULL,
			PRIMARY KEY (`id`),
			KEY `store_date` (`store_id`, `entry_date`)
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

	/** Which Payment Summary row a payment type belongs to */
	private function payment_bucket($type)
	{
		$t = strtolower(trim((string) $type));
		if ($t === '') {
			return 'other';
		}
		if (strpos($t, 'card') !== false || strpos($t, 'visa') !== false || strpos($t, 'master') !== false
			|| strpos($t, 'amex') !== false || strpos($t, 'amax') !== false || strpos($t, 'american express') !== false || strpos($t, 'mada') !== false) {
			return 'card';
		}
		if (strpos($t, 'cash') !== false) {
			return 'cash';
		}
		if (strpos($t, 'cheque') !== false || strpos($t, 'check') !== false) {
			return 'cheque';
		}
		if (strpos($t, 'wallet') !== false) {
			return 'wallet';
		}
		if (strpos($t, 'credit') !== false) {
			return 'credit';
		}
		return 'other';
	}

	/** Saved rows of one date (array of row objects) */
	public function get_rows($date, $store_id = null)
	{
		$store_id = ($store_id === null) ? get_current_store_id() : $store_id;
		$q = $this->db->where('store_id', $store_id)->where('entry_date', $date)->order_by('id', 'asc')->get($this->table);
		return $q ? $q->result() : array();
	}

	/** Everything the Day Book page shows for one date */
	public function get_summary($date)
	{
		$store_id = get_current_store_id();
		$p = array($store_id, $date);
		// Branch TRN empty (Settings > Branch) => no VAT rows on the Day Book
		$show_vat = is_branch_tax_enabled($store_id);

		/* ---------------- Sales ---------------- */
		$sales_incl = $this->val("SELECT COALESCE(SUM(grand_total),0) AS v FROM db_sales WHERE store_id=? AND sales_date=? AND sales_status='Final'", $p);
		$sales_vat = $this->val("SELECT COALESCE(SUM(si.tax_amt),0) AS v FROM db_salesitems si INNER JOIN db_sales s ON s.id=si.sales_id WHERE s.store_id=? AND s.sales_date=? AND s.sales_status='Final'", $p);
		// tot_discount_to_all_amt already includes the row discounts on new invoices, so take the larger of the two
		$coupon_sql = $this->db->field_exists('coupon_amt', 'db_sales') ? " + COALESCE(s.coupon_amt,0)" : "";
		$sales_disc = $this->val("SELECT COALESCE(SUM(GREATEST(COALESCE(s.tot_discount_to_all_amt,0), (SELECT COALESCE(SUM(d.discount_amt),0) FROM db_salesitems d WHERE d.sales_id=s.id))" . $coupon_sql . "),0) AS v FROM db_sales s WHERE s.store_id=? AND s.sales_date=? AND s.sales_status='Final'", $p);
		$sret_incl = $this->val("SELECT COALESCE(SUM(grand_total),0) AS v FROM db_salesreturn WHERE store_id=? AND return_date=?", $p);
		$sret_vat = $this->val("SELECT COALESCE(SUM(ri.tax_amt),0) AS v FROM db_salesitemsreturn ri INNER JOIN db_salesreturn r ON r.id=ri.return_id WHERE r.store_id=? AND r.return_date=?", $p);
		if (!$show_vat) {
			$sales_vat = 0.0;
			$sret_vat = 0.0;
		}
		$sales_excl = $sales_incl - $sales_vat;
		$sret_excl = $sret_incl - $sret_vat;
		$sales = array(
			'gross'     => $sales_excl + $sales_disc,   // sum of sales (before discount)
			'discount'  => $sales_disc,                 // sum of discount
			'returns'   => $sret_excl,                  // sum of returns
			'net_excl'  => $sales_excl - $sret_excl,    // taxable amount
			'vat'       => $sales_vat - $sret_vat,      // sum of VAT
			'net_incl'  => $sales_incl - $sret_incl,
			'show_vat'  => $show_vat,
			'vat_label' => $this->vat_label($sales_vat, $sales_excl),
			'net_label' => $show_vat ? 'Net Sales (incl. VAT)' : 'Net Sales',
		);

		/* ---------------- Purchase ---------------- */
		$pur_incl = $this->val("SELECT COALESCE(SUM(grand_total),0) AS v FROM db_purchase WHERE store_id=? AND purchase_date=?", $p);
		$pur_vat = $this->val("SELECT COALESCE(SUM(pit.tax_amt),0) AS v FROM db_purchaseitems pit INNER JOIN db_purchase pu ON pu.id=pit.purchase_id WHERE pu.store_id=? AND pu.purchase_date=?", $p);
		$pur_disc = $this->val("SELECT COALESCE(SUM(COALESCE(pu.tot_discount_to_all_amt,0) + (SELECT COALESCE(SUM(d.discount_amt),0) FROM db_purchaseitems d WHERE d.purchase_id=pu.id)),0) AS v FROM db_purchase pu WHERE pu.store_id=? AND pu.purchase_date=?", $p);
		$pret_incl = $this->val("SELECT COALESCE(SUM(grand_total),0) AS v FROM db_purchasereturn WHERE store_id=? AND return_date=?", $p);
		$pret_vat = $this->val("SELECT COALESCE(SUM(ri.tax_amt),0) AS v FROM db_purchaseitemsreturn ri INNER JOIN db_purchasereturn r ON r.id=ri.return_id WHERE r.store_id=? AND r.return_date=?", $p);
		if (!$show_vat) {
			$pur_vat = 0.0;
			$pret_vat = 0.0;
		}
		$pur_excl = $pur_incl - $pur_vat;
		$pret_excl = $pret_incl - $pret_vat;
		$purchase = array(
			'gross'     => $pur_excl + $pur_disc,       // sum of purchase (before discount)
			'discount'  => $pur_disc,                   // sum of purchase discount
			'returns'   => $pret_excl,                  // sum of purchase returns
			'net_excl'  => $pur_excl - $pret_excl,      // purchase - discount - returns
			'vat'       => $pur_vat - $pret_vat,        // sum of VAT
			'net_incl'  => $pur_incl - $pret_incl,
			'show_vat'  => $show_vat,
			'vat_label' => $this->vat_label($pur_vat, $pur_excl),
			'net_label' => $show_vat ? 'Net Purchase (incl. VAT)' : 'Net Purchase',
		);

		/* ---------------- Payment summary (money received on this date) ---------------- */
		$buckets = array('cash' => 0.0, 'card' => 0.0, 'credit' => 0.0, 'cheque' => 0.0, 'wallet' => 0.0, 'other' => 0.0);
		$q_pay = $this->db->query("SELECT COALESCE(payment_type,'') AS t, COALESCE(SUM(payment),0) AS v FROM db_salespayments WHERE store_id=? AND payment_date=? GROUP BY COALESCE(payment_type,'')", $p);
		if ($q_pay) {
			foreach ($q_pay->result() as $r) {
				$buckets[$this->payment_bucket($r->t)] += (float) $r->v;
			}
		}
		// Credit = unpaid balance of this date's sales (sold on credit) + anything recorded with a "Credit" payment type
		$buckets['credit'] += $this->val("SELECT COALESCE(SUM(GREATEST(COALESCE(grand_total,0)-COALESCE(paid_amount,0),0)),0) AS v FROM db_sales WHERE store_id=? AND sales_date=? AND sales_status='Final'", $p);
		$payments = array(
			array('label' => 'Cash',   'amount' => $buckets['cash']),
			array('label' => 'Card',   'amount' => $buckets['card']),     // Visa + Mastercard + Amex
			array('label' => 'Credit', 'amount' => $buckets['credit']),
			array('label' => 'Cheque', 'amount' => $buckets['cheque']),
			array('label' => 'Wallet', 'amount' => $buckets['wallet']),
		);
		// Payment types that fit none of the rows above (e.g. bank transfer) are kept so the total stays correct
		if (abs($buckets['other']) >= 0.005) {
			$payments[] = array('label' => 'Other', 'amount' => $buckets['other']);
		}
		$payment_total = 0.0;
		foreach ($payments as $row) {
			$payment_total += $row['amount'];
		}

		/* ---------------- Cash reconciliation ---------------- */
		// Opening Cash = sum of the Opening Cash entries of this date (Accounts > Opening Cash)
		$opening = 0.0;
		if ($this->db->table_exists('db_opening_cash')) {
			$opening = $this->val("SELECT COALESCE(SUM(amount),0) AS v FROM db_opening_cash WHERE store_id=? AND opening_date=?", $p);
		}
		$cash_sales = $this->val("SELECT COALESCE(SUM(payment),0) AS v FROM db_salespayments WHERE store_id=? AND payment_date=? AND LOWER(payment_type) LIKE '%cash%'", $p);
		$cash_refunds = $this->val("SELECT COALESCE(SUM(payment),0) AS v FROM db_salespaymentsreturn WHERE store_id=? AND payment_date=? AND LOWER(payment_type) LIKE '%cash%'", $p);
		// Cash Paid Out = expenses of this date paid in cash
		$cash_paid_out = $this->val("SELECT COALESCE(SUM(expense_amt),0) AS v FROM db_expense WHERE store_id=? AND expense_date=? AND LOWER(payment_type) LIKE '%cash%'", $p);
		$expected = $opening + $cash_sales - $cash_refunds - $cash_paid_out;

		/* ---------------- Saved bank note / coin count of this date ---------------- */
		$counts = array('notes' => array(), 'coins' => array());
		foreach ($this->notes as $n) { $counts['notes'][$n] = 0; }
		foreach ($this->coins as $c) { $counts['coins'][$c] = 0; }
		$counted = 0.0;
		$saved = null;
		foreach ($this->get_rows($date, $store_id) as $r) {
			$counted += (float) $r->amount;
			if (strcasecmp((string) $r->type, 'Coin') === 0) {
				$key = number_format((float) $r->currency, 2, '.', '');
				if (isset($counts['coins'][$key])) { $counts['coins'][$key] = max(0, (int) $r->qty); }
			} else {
				$key = (string) (int) round((float) $r->currency);
				if (isset($counts['notes'][$key])) { $counts['notes'][$key] = max(0, (int) $r->qty); }
			}
			if ($saved === null) {
				$saved = array(
					'by' => !empty($r->updated_by) ? $r->updated_by : $r->created_by,
					'on' => !empty($r->updated_date) ? $r->updated_date : $r->created_date,
				);
			}
		}

		return array(
			'date'      => $date,
			'is_today'  => ($date === date('Y-m-d')),
			'show_vat'  => $show_vat,
			'sales'     => $sales,
			'purchase'  => $purchase,
			'payments'  => $payments,
			'payment_total' => $payment_total,
			'cash'      => array(
				'opening'   => $opening,
				'sales'     => $cash_sales,
				'refunds'   => $cash_refunds,
				'paid_out'  => $cash_paid_out,
				'expected'  => $expected,
				'counted'   => round($counted, 2),
			),
			'notes'     => $this->notes,
			'coins'     => $this->coins,
			'counts'    => $counts,
			'saved'     => $saved,
		);
	}

	/** Save the count of one date: one row per note / coin (re-saving a date replaces its rows) */
	public function save_declaration($date, $counts)
	{
		$store_id = get_current_store_id();
		$user = $this->session->userdata('inv_username');

		$old = $this->get_rows($date, $store_id);
		$first = !empty($old) ? $old[0] : null;

		$rows = array();
		$notes_total = 0.0;
		$coins_total = 0.0;
		foreach (array('Note' => $this->notes, 'Coin' => $this->coins) as $type => $list) {
			$group = ($type === 'Note') ? 'notes' : 'coins';
			foreach ($list as $den) {
				$qty = (isset($counts[$group][$den])) ? max(0, (int) $counts[$group][$den]) : 0;
				$amount = round($qty * (float) $den, 2);
				if ($type === 'Note') { $notes_total += $amount; } else { $coins_total += $amount; }
				$rows[] = array(
					'store_id'     => $store_id,
					'entry_date'   => $date,
					'currency'     => number_format((float) $den, 2, '.', ''),
					'type'         => $type,
					'qty'          => $qty,
					'amount'       => $amount,
					'created_by'   => $first ? $first->created_by : $user,
					'created_date' => $first ? $first->created_date : date('Y-m-d'),
					'created_time' => $first ? $first->created_time : date('H:i:s'),
					'updated_by'   => $first ? $user : null,
					'updated_date' => $first ? date('Y-m-d') : null,
					'updated_time' => $first ? date('H:i:s') : null,
				);
			}
		}
		$notes_total = round($notes_total, 2);
		$coins_total = round($coins_total, 2);
		$total = round($notes_total + $coins_total, 2);

		$this->db->trans_start();
		$this->db->where('store_id', $store_id)->where('entry_date', $date)->delete($this->table);
		$this->db->insert_batch($this->table, $rows);
		$this->db->trans_complete();
		$ok = $this->db->trans_status();

		$summary = $this->get_summary($date);
		$expected = round((float) $summary['cash']['expected'], 2);

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
