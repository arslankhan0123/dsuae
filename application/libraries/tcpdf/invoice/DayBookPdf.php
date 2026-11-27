<?php
defined('BASEPATH') OR exit('No direct script access allowed');

include_once('MyPDF.php');

/**
 * Day Book PDF — same company header, title band, bordered tables and footer as the sales invoice PDF.
 * Usage: $this->load->library('tcpdf/invoice/DayBookPdf', array('date' => 'YYYY-MM-DD', 'data' => $summary));
 */
class DayBookPdf extends MyPDF {

	protected $daybook_date = '';
	protected $daybook = array();

	public function __construct(array $param = array())
	{
		$this->daybook_date = !empty($param['date']) ? $param['date'] : date('Y-m-d');
		$this->daybook = !empty($param['data']) ? $param['data'] : array();
		parent::__construct();
		$this->_invoice_name = 'Day Book';
		$this->page_title = 'Day Book ' . $this->daybook_date;
	}

	// Footer: "Generate: <now>" + "Page X of Y" above the centered address line (from MyPDF)
	public function Footer()
	{
		$this->footer_generate_text = 'Generate: ' . trim(show_date(date('Y-m-d')) . ' ' . strtoupper(date('g:i A')));
		parent::Footer();
	}

	protected function amt($v)
	{
		return store_number_format((float) $v);
	}

	/** Title band: TRN (left) | DAY BOOK (center) | Date (right) — same box as the invoice title */
	protected function draw_title()
	{
		$pageWidth = $this->getPageWidth();
		$isA5 = ($pageWidth < 160);
		$w = $pageWidth - 12;
		$h = $isA5 ? 13 : 14;
		$x = 6;
		$y = !empty($this->header_bottom_y) ? ($this->header_bottom_y + 1) : ($isA5 ? 30 : 36);

		$store_trn = (!empty($this->store->vat_no)) ? trim((string) $this->store->vat_no) : '';

		$this->setCellMargins(0, 0, 0, 0);
		$this->setFillColor(255, 255, 255);
		$this->SetDrawColor(170, 170, 170);
		$this->SetTextColor(0, 0, 0);
		$this->Rect($x, $y, $w, $h);
		$this->setCellPaddings(2, 0, 2, 0);
		$this->setFont($this->get_font_name(), 'B', $isA5 ? 6.75 : 8.25);
		$this->MultiCell($w * 0.33, $h, ($store_trn !== '' ? 'TRN No. : ' . $store_trn : ''), 0, 'L', false, 0, $x, $y, true, 0, false, true, $h, 'M', true);
		$this->MultiCell($w * 0.33, $h, 'Date : ' . show_date($this->daybook_date), 0, 'R', false, 0, $x + $w * 0.67, $y, true, 0, false, true, $h, 'M', true);
		$this->setFont($this->get_font_name(), 'B', $isA5 ? 12 : 19.5);
		$this->MultiCell($w * 0.34, $h, 'DAY BOOK', 0, 'C', false, 0, $x + $w * 0.33, $y, true, 0, false, true, $h, 'M', true);
		$this->setCellPaddings(0, 0, 0, 0);
		$this->title_bottom_y = $y + $h;
		$this->SetY($this->title_bottom_y + 3);
	}

	/**
	 * One summary box.
	 * $rows: array of array(label, value, type) — type: '' normal, 'strong' bold row, 'total' highlighted last row
	 */
	protected function box_html($title, array $rows, $fs)
	{
		$b = '0.5px solid #AAAAAA';
		$html = '<table cellpadding="4" cellspacing="0" style="width:100%;font-size:' . $fs . 'px;">';
		$html .= '<tr style="background-color:#e4eaff;"><td colspan="2" style="width:100%;border:' . $b . ';font-weight:bold;font-size:' . ($fs + 1) . 'px;">' . $title . '</td></tr>';
		$last = count($rows) - 1;
		foreach ($rows as $i => $r) {
			$type = isset($r[2]) ? $r[2] : '';
			$weight = ($type === 'strong' || $type === 'total') ? 'font-weight:bold;' : '';
			$bg = ($type === 'total') ? 'background-color:#f1f4ff;' : '';
			$top = ($type === 'total') ? 'border-top:' . $b . ';' : '';
			$bottom = ($i === $last) ? 'border-bottom:' . $b . ';' : '';
			$size = ($type === 'total') ? 'font-size:' . ($fs + 1.5) . 'px;' : '';
			$html .= '<tr style="' . $bg . '">'
				. '<td style="width:60%;border-left:' . $b . ';' . $top . $bottom . $weight . $size . '">' . $r[0] . '</td>'
				. '<td style="width:40%;text-align:right;border-right:' . $b . ';' . $top . $bottom . $weight . $size . '"><nobr>' . $r[1] . '</nobr></td>'
				. '</tr>';
		}
		return $html . '</table>';
	}

	/** Draw two boxes side by side and move below the taller one */
	protected function two_boxes($left_html, $right_html)
	{
		$pageWidth = $this->getPageWidth();
		$w = $pageWidth - 12;
		$gap = 4;
		$half = ($w - $gap) / 2;
		$y = $this->GetY();
		$this->writeHTMLCell($half, 0, 6, $y, $left_html, 0, 1, false, true, 'L', true);
		$y_left = $this->GetY();
		$this->writeHTMLCell($half, 0, 6 + $half + $gap, $y, $right_html, 0, 1, false, true, 'L', true);
		$this->SetY(max($y_left, $this->GetY()) + 3);
	}

	public function show_pdf()
	{
		$d = $this->daybook;
		$pageWidth = $this->getPageWidth();
		$isA5 = ($pageWidth < 160);
		$fs = $isA5 ? 8 : 11;
		$b = '0.5px solid #AAAAAA';

		$this->setFont($this->get_font_name(), '', 8);
		$this->AddPage();
		$this->draw_title();
		$this->setFont($this->get_font_name(), '', $isA5 ? 6.5 : 8.5);
		$this->setCellPaddings(0, 0, 0, 0);
		$this->SetTextColor(0, 0, 0);

		$s = $d['sales'];
		$p = $d['purchase'];
		$c = $d['cash'];

		/* Sales | Purchase */
		// Branch TRN empty => no "excl. VAT" / VAT rows, and the total is just "Net Sales" / "Net Purchase"
		$sales_rows = array(
			array('Gross Sales', $this->amt($s['gross'])),
			array('Sales Discount', '- ' . $this->amt(abs($s['discount']))),
			array('Sales Returns', '- ' . $this->amt(abs($s['returns']))),
		);
		if (!empty($s['show_vat'])) {
			$sales_rows[] = array('Net Sales excl. VAT', $this->amt($s['net_excl']), 'strong');
			$sales_rows[] = array($s['vat_label'], $this->amt(abs($s['vat'])));
		}
		$sales_rows[] = array($s['net_label'], $this->amt($s['net_incl']), 'total');
		$sales_html = $this->box_html('Sales Summary', $sales_rows, $fs);

		$purchase_rows = array(
			array($p['gross_label'], $this->amt($p['gross'])),
			array('Purchase Discount', '- ' . $this->amt(abs($p['discount']))),
			array('Purchase Returns', '- ' . $this->amt(abs($p['returns']))),
		);
		if (!empty($p['show_vat'])) {
			$purchase_rows[] = array('Net Purchase excl. VAT', $this->amt($p['net_excl']), 'strong');
			$purchase_rows[] = array($p['vat_label'], $this->amt(abs($p['vat'])));
		}
		$purchase_rows[] = array($p['net_label'], $this->amt($p['net_incl']), 'total');
		$purchase_html = $this->box_html('Purchase Summary', $purchase_rows, $fs);
		$this->two_boxes($sales_html, $purchase_html);

		/* Cash reconciliation | Payment summary */
		$diff = round((float) $c['counted'] - (float) $c['expected'], 2);
		$diff_label = 'Cash Difference' . ($diff > 0 ? ' (over)' : '');
		$diff_value = ($diff < 0 ? '- ' : '') . $this->amt(abs($diff));
		$paid_out_value = ($c['paid_out'] < 0 ? '' : '- ') . $this->amt(abs($c['paid_out']));
		$cash_rows = array(
			array('Opening Cash', $this->amt($c['opening'])),
			array('Cash Sales', $this->amt(abs($c['sales']))),
			array('Cash Refunds', '- ' . $this->amt(abs($c['refunds']))),
			array('Cash Paid Out', $paid_out_value),
			array('Expected Cash in Drawer', $this->amt($c['expected']), 'strong'),
			array('Actual Cash Counted', $this->amt($c['counted']), 'strong'),
			array($diff_label, $diff_value, 'total'),
		);
		$pay_rows = array();
		foreach ($d['payments'] as $pay) {
			$pay_rows[] = array(htmlspecialchars((string) $pay['label'], ENT_QUOTES, 'UTF-8'), $this->amt($pay['amount']));
		}
		// Keep both boxes the same height so the page reads as one grid
		while (count($pay_rows) < count($cash_rows) - 1) {
			$pay_rows[] = array('&nbsp;', '&nbsp;');
		}
		$pay_rows[] = array('Total Payments', $this->amt($d['payment_total']), 'total');
		while (count($cash_rows) < count($pay_rows)) {
			array_splice($cash_rows, count($cash_rows) - 1, 0, array(array('&nbsp;', '&nbsp;')));
		}
		$this->two_boxes($this->box_html('Cash Reconciliation', $cash_rows, $fs), $this->box_html('Payment Summary', $pay_rows, $fs));

		/* Denomination count (full width): notes on the left, coins on the right */
		$notes = $d['notes'];
		$coins = array_values(array_filter($d['coins'], function ($coin) { return (float) $coin !== 0.10; }));
		$notes_total = 0.0;
		$coins_total = 0.0;
		$th = 'border:' . $b . ';font-weight:bold;';
		$den = '<table cellpadding="4" cellspacing="0" style="width:100%;font-size:' . $fs . 'px;">';
		$den .= '<tr style="background-color:#e4eaff;"><td colspan="6" style="width:100%;border:' . $b . ';font-weight:bold;font-size:' . ($fs + 1) . 'px;">Denomination Count</td></tr>';
		$den .= '<tr style="background-color:#f1f4ff;">'
			. '<td style="width:17%;' . $th . '">Note</td><td style="width:13%;text-align:center;' . $th . '">Qty</td><td style="width:20%;text-align:right;' . $th . '">Subtotal</td>'
			. '<td style="width:17%;' . $th . '">Coin</td><td style="width:13%;text-align:center;' . $th . '">Qty</td><td style="width:20%;text-align:right;' . $th . '">Subtotal</td>'
			. '</tr>';
		$rows = max(count($notes), count($coins));
		$side = 'border-left:' . $b . ';border-right:' . $b . ';';
		for ($i = 0; $i < $rows; $i++) {
			$den .= '<tr>';
			if (isset($notes[$i])) {
				$q = (int) $d['counts']['notes'][$notes[$i]];
				$sub = $q * (float) $notes[$i];
				$notes_total += $sub;
				$den .= '<td style="width:17%;' . $side . 'font-weight:bold;">' . number_format((float) $notes[$i]) . '</td><td style="width:13%;text-align:center;' . $side . '">' . $q . '</td><td style="width:20%;text-align:right;' . $side . '">' . $this->amt($sub) . '</td>';
			} else {
				$den .= '<td style="width:17%;' . $side . '">&nbsp;</td><td style="width:13%;' . $side . '">&nbsp;</td><td style="width:20%;' . $side . '">&nbsp;</td>';
			}
			if (isset($coins[$i])) {
				$q = (int) $d['counts']['coins'][$coins[$i]];
				$sub = $q * (float) $coins[$i];
				$coins_total += $sub;
				$den .= '<td style="width:17%;' . $side . 'font-weight:bold;">' . $coins[$i] . '</td><td style="width:13%;text-align:center;' . $side . '">' . $q . '</td><td style="width:20%;text-align:right;' . $side . '">' . $this->amt($sub) . '</td>';
			} else {
				$den .= '<td style="width:17%;' . $side . '">&nbsp;</td><td style="width:13%;' . $side . '">&nbsp;</td><td style="width:20%;' . $side . '">&nbsp;</td>';
			}
			$den .= '</tr>';
		}
		$den .= '<tr style="background-color:#f1f4ff;">'
			. '<td colspan="2" style="width:30%;' . $th . '">Notes Total</td><td style="width:20%;text-align:right;' . $th . '">' . $this->amt($notes_total) . '</td>'
			. '<td colspan="2" style="width:30%;' . $th . '">Coins Total</td><td style="width:20%;text-align:right;' . $th . '">' . $this->amt($coins_total) . '</td>'
			. '</tr>';
		$den .= '<tr style="background-color:#e4eaff;">'
			. '<td colspan="5" style="width:80%;' . $th . 'font-size:' . ($fs + 1.5) . 'px;">Total Cash Counted</td><td style="width:20%;text-align:right;' . $th . 'font-size:' . ($fs + 1.5) . 'px;">' . $this->amt($notes_total + $coins_total) . '</td>'
			. '</tr>';
		$den .= '</table>';
		$this->writeHTMLCell($pageWidth - 12, 0, 6, $this->GetY(), $den, 0, 1, false, true, 'L', true);

		/* Signatures */
		$this->Ln(10);
		$sig = '<table cellpadding="4" cellspacing="0" style="width:100%;font-size:' . ($fs - 1) . 'px;"><tr>'
			. '<td style="width:35%;text-align:center;border-top:' . $b . ';"><b>Prepared By</b></td>'
			. '<td style="width:30%;">&nbsp;</td>'
			. '<td style="width:35%;text-align:center;border-top:' . $b . ';"><b>Authorised Signature</b></td>'
			. '</tr></table>';
		$this->writeHTMLCell($pageWidth - 12, 0, 6, $this->GetY(), $sig, 0, 1, false, true, 'L', true);

		if (ob_get_length()) {
			@ob_end_clean();
		}
		$this->Output('day-book-' . $this->daybook_date . '.pdf', 'I');
	}
}
