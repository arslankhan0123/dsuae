<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Dompdf\Dompdf;
use Dompdf\Options;

class Pdf extends MY_Controller {
	public function __construct(){
		parent::__construct();
		if ($this->router->fetch_method() !== 'sales_public'
			&& $this->router->fetch_method() !== 'sales_return_public'
			&& $this->router->fetch_method() !== 'quotation_public'
			&& $this->router->fetch_method() !== 'deliverynote_public') {
			$this->load_global();
		} else {
			$this->load_info();
		}
	}

	/**
	 * Sales Return invoices - Public access using a unique token
	 */
	public function sales_return_public($return_id=null, $token=null){
		$salt = $this->config->item('encryption_key');
		if (empty($salt)) {
			$salt = 'retail_app_secret_salt';
		}
		if (empty($return_id) || empty($token) || $token !== md5($return_id . $salt)) {
			show_404();
			return;
		}

		$CI =& get_instance();
		$return = $CI->db->select('store_id')->where('id', $return_id)->get('db_salesreturn')->row();
		if (empty($return)) {
			show_404();
			return;
		}

		$this->session->set_userdata('store_id', $return->store_id);
		if (empty($this->session->userdata('language'))) {
			$this->session->set_userdata('language', 'English');
		}

		$params = array();
		$params['return_id'] = $return_id;
		$params['with_part_number'] = ($this->input->get('with_part_number') == '1');

		$invoice_format_id = $CI->db->select('sales_invoice_format_id')->where('id', $return->store_id)->get('db_store')->row()->sales_invoice_format_id;
		if($invoice_format_id==4){
			$this->load->library('tcpdf/invoice/GstSalesReturn',$params);
			$this->gstsalesreturn->show_pdf();
		} else {
			$this->load->library('tcpdf/invoice/Sales_return',$params);
			$this->sales_return->show_pdf();
		}
	}

	/**
	 * Sales Return invoices
	 */
	public function sales_return($return_id=null){
		$this->belong_to('db_salesreturn',$return_id);
		if(!$this->permissions('sales_return_add') && !$this->permissions('sales_return_edit')){
			$this->show_access_denied_page();
		}

		$params = array();
		$params['return_id'] = $return_id;
		$params['with_part_number'] = ($this->input->get('with_part_number') == '1');

		$invoice_format_id = get_invoice_format_id();
		if($invoice_format_id==4){
			$this->load->library('tcpdf/invoice/GstSalesReturn',$params);
			$this->gstsalesreturn->IncludeJS("print();");
			$this->gstsalesreturn->show_pdf();
		} else {
			$this->load->library('tcpdf/invoice/Sales_return',$params);
			$this->sales_return->IncludeJS("print();");
			$this->sales_return->show_pdf();
		}
	}

	/**
	 * Sales invoices - Public access using a unique token
	 */
	public function sales_public($sales_id=null, $token=null){
		$salt = $this->config->item('encryption_key');
		if (empty($salt)) {
			$salt = 'retail_app_secret_salt';
		}
		if (empty($sales_id) || empty($token) || $token !== md5($sales_id . $salt)) {
			show_404();
			return;
		}

		$CI =& get_instance();
		$sale = $CI->db->select('store_id')->where('id', $sales_id)->get('db_sales')->row();
		if (empty($sale)) {
			show_404();
			return;
		}

		$this->session->set_userdata('store_id', $sale->store_id);
		if (empty($this->session->userdata('language'))) {
			$this->session->set_userdata('language', 'English');
		}

		$params = array();
		$params['sales_id'] = $sales_id;
		$params['with_part_number'] = ($this->input->get('with_part_number') == '1');

		$invoice_format_id = $CI->db->select('sales_invoice_format_id')->where('id', $sale->store_id)->get('db_store')->row()->sales_invoice_format_id;

		if($invoice_format_id==4){
			$this->load->library('tcpdf/invoice/GstInvoice',$params);
			$this->gstinvoice->show_pdf();
		}
		else{
			$this->load->library('tcpdf/invoice/Sales',$params);
			$this->sales->show_pdf();
		}
	}

	/**
	 * Sales invoices
	 * 3. Default Format
	 * 4. GST invoice Format
	 */
	public function sales($sales_id=null){
		$params = array();

		$this->belong_to('db_sales',$sales_id);
		if(!$this->permissions('sales_add') && !$this->permissions('sales_edit')){
			$this->show_access_denied_page();
		}

		$invoice_format_id = get_invoice_format_id();

		$params['sales_id'] = $sales_id;
		$params['with_part_number'] = ($this->input->get('with_part_number') == '1');

		if($invoice_format_id==4){
			$this->load->library('tcpdf/invoice/GstInvoice',$params);
			$this->gstinvoice->IncludeJS("print();");
			$this->gstinvoice->show_pdf();
		}
		else{
			$this->load->library('tcpdf/invoice/Sales',$params);
			$this->sales->show_pdf();
		}
	}

	/**
	 * Quotation - Public access
	 */
	public function quotation_public($quotation_id=null, $token=null){
		$salt = $this->config->item('encryption_key');
		if (empty($salt)) {
			$salt = 'retail_app_secret_salt';
		}
		if (empty($quotation_id) || empty($token) || $token !== md5($quotation_id . $salt)) {
			show_404();
			return;
		}

		$CI =& get_instance();
		$quotation = $CI->db->select('store_id')->where('id', $quotation_id)->get('db_quotation')->row();
		if (empty($quotation)) {
			show_404();
			return;
		}

		$this->session->set_userdata('store_id', $quotation->store_id);
		if (empty($this->session->userdata('language'))) {
			$this->session->set_userdata('language', 'English');
		}

		$params = array();
		$params['quotation_id'] = $quotation_id;
		$params['with_part_number'] = ($this->input->get('with_part_number') == '1');

		$format_row = $CI->db->select('sales_invoice_format_id')->where('id', $quotation->store_id)->get('db_store')->row();
		$invoice_format_id = !empty($format_row) ? $format_row->sales_invoice_format_id : null;
		if($invoice_format_id==4){
			$this->load->library('tcpdf/invoice/GstQuotation',$params);
			$this->gstquotation->show_pdf();
		} else {
			$this->stream_dompdf_view('quotation/print-quotation-invoice-2', array(
				'quotation_id' => $quotation_id,
				'page_title' => 'Quotation'
			), "Quotation_{$quotation_id}.pdf");
		}
	}

	/**
	 * Quotation PDF
	 */
	public function quotation($quotation_id=null){
		$this->belong_to('db_quotation',$quotation_id);
		if(!$this->permissions('quotation_add') && !$this->permissions('quotation_edit')){
			$this->show_access_denied_page();
		}

		$params = array();
		$params['quotation_id'] = $quotation_id;
		$params['with_part_number'] = ($this->input->get('with_part_number') == '1');

		$invoice_format_id = get_invoice_format_id();
		if($invoice_format_id==4){
			$this->load->library('tcpdf/invoice/GstQuotation',$params);
			$this->gstquotation->IncludeJS("print();");
			$this->gstquotation->show_pdf();
		} else {
			$this->stream_dompdf_view('quotation/print-quotation-invoice-2', array(
				'quotation_id' => $quotation_id,
				'page_title' => $this->lang->line('quotation_invoice')
			), "Quotation_{$quotation_id}.pdf");
		}
	}

	/**
	 * Delivery Note - Public access
	 */
	public function deliverynote_public($deliverynote_id=null, $token=null){
		$salt = $this->config->item('encryption_key');
		if (empty($salt)) {
			$salt = 'retail_app_secret_salt';
		}
		if (empty($deliverynote_id) || empty($token) || $token !== md5($deliverynote_id . $salt)) {
			show_404();
			return;
		}

		$CI =& get_instance();
		$deliverynote = $CI->db->select('store_id')->where('id', $deliverynote_id)->get('db_deliverynote')->row();
		if (empty($deliverynote)) {
			show_404();
			return;
		}

		$this->session->set_userdata('store_id', $deliverynote->store_id);
		if (empty($this->session->userdata('language'))) {
			$this->session->set_userdata('language', 'English');
		}

		$params = array();
		$params['deliverynote_id'] = $deliverynote_id;
		$params['with_part_number'] = ($this->input->get('with_part_number') == '1');

		$format_row = $CI->db->select('sales_invoice_format_id')->where('id', $deliverynote->store_id)->get('db_store')->row();
		$invoice_format_id = !empty($format_row) ? $format_row->sales_invoice_format_id : null;
		if($invoice_format_id==4){
			$this->load->library('tcpdf/invoice/GstDeliveryNote',$params);
			$this->gstdeliverynote->show_pdf();
		} else {
			$this->stream_dompdf_view('delivery_note/print-delivery_note-invoice-2', array(
				'deliverynote_id' => $deliverynote_id,
				'page_title' => 'Delivery Note'
			), "Delivery_note_{$deliverynote_id}.pdf");
		}
	}

	/**
	 * Delivery Note PDF
	 */
	public function deliverynote($deliverynote_id=null){
		$this->belong_to('db_deliverynote',$deliverynote_id);
		if(!$this->permissions('deliverynote_add') && !$this->permissions('deliverynote_edit')){
			$this->show_access_denied_page();
		}

		$params = array();
		$params['deliverynote_id'] = $deliverynote_id;
		$params['with_part_number'] = ($this->input->get('with_part_number') == '1');

		$invoice_format_id = get_invoice_format_id();
		if($invoice_format_id==4){
			$this->load->library('tcpdf/invoice/GstDeliveryNote',$params);
			$this->gstdeliverynote->IncludeJS("print();");
			$this->gstdeliverynote->show_pdf();
		} else {
			$this->stream_dompdf_view('delivery_note/print-delivery_note-invoice-2', array(
				'deliverynote_id' => $deliverynote_id,
				'page_title' => $this->lang->line('deliverynote_invoice')
			), "Delivery_note_{$deliverynote_id}.pdf");
		}
	}

	/**
	 * Purchase invoices
	 * 4. GST invoice format (TCPDF). Other formats use the existing Dompdf view.
	 */
	public function purchase($purchase_id=null){
		$this->belong_to('db_purchase',$purchase_id);
		if(!$this->permissions('purchase_add') && !$this->permissions('purchase_edit')){
			$this->show_access_denied_page();
		}

		$params = array();
		$params['purchase_id'] = $purchase_id;
		$params['with_part_number'] = ($this->input->get('with_part_number') == '1');

		$invoice_format_id = get_invoice_format_id();
		if($invoice_format_id==4){
			$this->load->library('tcpdf/invoice/GstPurchase',$params);
			$this->gstpurchase->IncludeJS("print();");
			$this->gstpurchase->show_pdf();
		} else {
			$data = $this->data;
			$data['purchase_id'] = $purchase_id;
			$data['page_title'] = $this->lang->line('purchase_invoice');
			$data['with_part_number'] = $params['with_part_number'];
			$this->stream_dompdf_view(
				'print-purchase-invoice-2',
				$data,
				"Purchase-invoice-{$purchase_id}-".date('M')."_".date('d')."_".date('Y')
			);
		}
	}

	/**
	 * Order invoices
	 * 4. GST invoice format (TCPDF). Other formats use the existing Dompdf view.
	 */
	public function order($order_id=null){
		$this->belong_to('db_orders',$order_id);
		if(!$this->permissions('order_add') && !$this->permissions('order_edit')){
			$this->show_access_denied_page();
		}

		$params = array();
		$params['order_id'] = $order_id;
		$params['with_part_number'] = ($this->input->get('with_part_number') == '1');

		$invoice_format_id = get_invoice_format_id();
		if($invoice_format_id==4){
			$this->load->library('tcpdf/invoice/GstOrder',$params);
			$this->gstorder->IncludeJS("print();");
			$this->gstorder->show_pdf();
		} else {
			$data = $this->data;
			$data['order_id'] = $order_id;
			$data['page_title'] = 'Order Invoice';
			$data['with_part_number'] = $params['with_part_number'];
			$this->stream_dompdf_view(
				'print-order-invoice-2',
				$data,
				"Order-invoice-{$order_id}-".date('M')."_".date('d')."_".date('Y')
			);
		}
	}

	/**
	 * Purchase return invoices (supplier-facing)
	 * 4. GST invoice format (TCPDF). Other formats use the existing Dompdf view.
	 */
	public function purchase_return($return_id=null){
		$this->belong_to('db_purchasereturn',$return_id);
		if(!$this->permissions('purchase_return_add') && !$this->permissions('purchase_return_edit')){
			$this->show_access_denied_page();
		}

		$params = array();
		$params['return_id'] = $return_id;
		$params['with_part_number'] = ($this->input->get('with_part_number') == '1');

		$invoice_format_id = get_invoice_format_id();
		if($invoice_format_id==4){
			$this->load->library('tcpdf/invoice/GstPurchaseReturn',$params);
			$this->gstpurchasereturn->IncludeJS("print();");
			$this->gstpurchasereturn->show_pdf();
		} else {
			$data = $this->data;
			$data['return_id'] = $return_id;
			$data['page_title'] = $this->lang->line('purchase_return_invoice');
			$data['with_part_number'] = $params['with_part_number'];
			$this->stream_dompdf_view(
				'print-purchase-return-invoice-2',
				$data,
				"Purchase-return-{$return_id}-".date('M')."_".date('d')."_".date('Y')
			);
		}
	}

	/**
	 * Render a print view via Dompdf
	 */
	private function stream_dompdf_view($view, $data, $filename){
		$this->load->view($view, $data);
		$html = sanitize_html_for_dompdf($this->output->get_output());

		if (!class_exists('Dompdf\Dompdf', false) && file_exists(APPPATH . 'libraries/dompdf/autoload.inc.php')) {
			require_once APPPATH . 'libraries/dompdf/autoload.inc.php';
		}

		$options = new Options();
		$options->set('isRemoteEnabled', true);
		$dompdf = new Dompdf($options);
		$dompdf->loadHtml($html, 'UTF-8');
		$dompdf->setPaper('A4', 'portrait');
		$dompdf->render();
		$dompdf->stream($filename, array("Attachment" => 0));
	}
}
