<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Store_profile_model extends CI_Model {

	public function update_store(){

		//Filtering XSS and html escape from user inputs 
		extract($this->security->xss_clean(html_escape(array_merge($this->data,$_POST,$_GET))));
		 	//echo "<pre>";print_r($this->security->xss_clean(html_escape(array_merge($this->data,$_POST))));exit();

		//if not admin
		if(!is_admin()){
			if($q_id!=get_current_store_id()){
				echo "Something Went Wrong";exit();
			}
		}

		$branch_first_name = isset($branch_first_name) ? trim($branch_first_name) : '';
		$branch_last_name = isset($branch_last_name) ? trim($branch_last_name) : '';
		if($branch_first_name === '' || $branch_last_name === ''){
			echo 'Branch First Name and Branch Last Name are required.';
			exit();
		}
		$store_name = trim($branch_first_name.' '.$branch_last_name);

		$required_fields = array(
			'country'      => 'Country',
			'state'        => 'State',
			'address'      => 'Address',
		);
		foreach ($required_fields as $field => $label) {
			if (!isset($_POST[$field]) || trim($_POST[$field]) === '') {
				echo $label.' is required.';
				exit();
			}
		}

		$mobile = trim(preg_replace('/\s+/', '', $mobile), ',');
		if(!preg_match('/^\+?\d+(,\+?\d+)*$/', $mobile)){
			echo 'Enter valid mobile numbers separated by commas.';
			exit();
		}

		$this->db->trans_begin();
		
		$store_logo='';
		if(!empty($_FILES['store_logo']['name'])){
			$config['upload_path']          = './uploads/store/';
	        $config['allowed_types']        = 'gif|jpg|jpeg|png';
	        $config['max_size']             = 1000;
	        $config['max_width']            = 1000;
	        $config['max_height']           = 1000;

	        $this->load->library('upload', $config);

	        if ( ! $this->upload->do_upload('store_logo'))
	        {
	                $error = array('error' => $this->upload->display_errors());
	                return $error['error'];
	                exit();
	        }
	        else
	        {
	        	   $store_logo='uploads/store/'.$this->upload->data('file_name');
	        }
		}

		$change_return = (isset($change_return)) ? 1 : 0;
		$mrp_column = (isset($mrp_column) && (int)$mrp_column === 1) ? 1 : 0;
		$previous_balance_bit = (isset($previous_balance_bit)) ? 1 : 0;
		$round_off = (isset($round_off)) ? 1 : 0;
		$allow_negative_stock = (isset($allow_negative_stock) && (int)$allow_negative_stock === 1) ? 1 : 0;
		ensure_pdf_part_number_popup_setting();
		ensure_item_barcode_label_setting();
		ensure_paid_seal_setting();
		ensure_invoice_declaration_setting();
		ensure_location_url_setting();
		ensure_whatsapp_number_setting();
		ensure_store_profile_columns();
		$store_name_arabic = isset($store_name_arabic) ? trim($store_name_arabic) : '';
		$address_arabic = isset($address_arabic) ? trim($address_arabic) : '';
		$pdf_part_number_popup = (isset($pdf_part_number_popup) && (int)$pdf_part_number_popup === 1) ? 1 : 0;
		$item_barcode_label = (isset($item_barcode_label) && $item_barcode_label === 'barcode_no') ? 'barcode_no' : 'part_no';
		$paid_seal = (isset($paid_seal) && (int)$paid_seal === 1) ? 1 : 0;
		// Keep raw URL (do not html_escape) so Google Maps query params stay valid for QR
		$location_url = isset($_POST['location_url']) ? trim($_POST['location_url']) : '';
		$whatsapp_number = isset($_POST['whatsapp_number']) ? trim($_POST['whatsapp_number']) : '';
		$whatsapp_digits = normalize_whatsapp_number($whatsapp_number);
		$qr_image = '';
		if($whatsapp_digits !== ''){
			$qr_image = generate_store_whatsapp_qr_image($whatsapp_digits, $q_id);
			if($qr_image === ''){
				echo 'Failed to generate WhatsApp QR. Please try again.';
				exit();
			}
			$whatsapp_number = $whatsapp_digits;
		}
		$show_website_qr = (isset($show_website_qr)) ? 1 : 0;
		$show_location_qr = (isset($show_location_qr)) ? 1 : 0;
		$show_whatsapp_qr = (isset($show_whatsapp_qr)) ? 1 : 0;
		$show_invoice_qr = (isset($show_invoice_qr)) ? 1 : 0;
		$show_zatca_qr = (isset($show_zatca_qr)) ? 1 : 0;

		ensure_store_item_tax_defaults();
		$default_tax_type = (isset($default_tax_type) && in_array($default_tax_type, array('Inclusive','Exclusive'), true))
			? $default_tax_type
			: 'Inclusive';
		$default_tax_id = (!empty($default_tax_id)) ? (int)$default_tax_id : null;

		

		$data = array(
		    				'store_code'				=> $store_code,
		    				'store_name'				=> $store_name,
							'branch_first_name'		=> $branch_first_name,
							'branch_last_name'		=> $branch_last_name,
							'store_name_arabic'		=> $store_name_arabic,
							'cr_no'					=> isset($cr_no) ? $cr_no : '',
		    				'store_website'				=> $store_website,
		    				'location_url'				=> $location_url,
		    				'whatsapp_number'			=> $whatsapp_number,
		    				'mobile'					=> $mobile,
		    				'phone'						=> $phone,
		    				'email'						=> $email,
		    				'country'					=> $country,
		    				'state'						=> $state,
		    				'city'						=> $city,
		    				'address'					=> $address,
		    				'address_arabic'			=> $address_arabic,
		    				'postcode'					=> $postcode,
		    				'bank_details'				=> isset($bank_details) ? $bank_details : '',
		    				'bank_name'					=> isset($bank_name) ? $bank_name : '',
		    				'account_name'				=> isset($account_name) ? $account_name : '',
		    				'account_number'			=> isset($account_number) ? $account_number : '',
		    				'iban_number'				=> isset($iban_number) ? $iban_number : '',
		    				'routing_number'			=> isset($routing_number) ? $routing_number : '',
		    				'show_website_qr'			=> $show_website_qr,
		    				'show_location_qr'			=> $show_location_qr,
		    				'show_whatsapp_qr'			=> $show_whatsapp_qr,
		    				'show_invoice_qr'			=> $show_invoice_qr,
		    				'show_zatca_qr'				=> $show_zatca_qr,
		    				'department_init'			=> $department_init,
		    				'category_init'				=> $category_init,
		    				'subcategory_init'			=> $subcategory_init,
		    				'item_init'					=> $item_init,
		    				'supplier_init'				=> $supplier_init,
		    				'purchase_init'				=> $purchase_init,
		    				'purchase_return_init'		=> $purchase_return_init,
		    				'customer_init'				=> $customer_init,
		    				'sales_init'				=> $sales_init,
		    				'sales_return_init'			=> $sales_return_init,
		    				'expense_init'				=> $expense_init,
		    				'quotation_init'			=> $quotation_init,
		    				'money_transfer_init'		=> $money_transfer_init,
		    				'accounts_init'				=> $accounts_init,
		    				'currency_id'				=> $currency,
		    				'currency_placement'		=> $currency_placement,
		    				'timezone'					=> $timezone,
		    				'date_format'				=> $date_format,
		    				'time_format'				=> $time_format,
		    				'sales_discount'			=> $sales_discount,
		    				'sales_discount'			=> $sales_discount,
		    				'default_tax_type'			=> $default_tax_type,
		    				'default_tax_id'			=> $default_tax_id,
		    				'change_return'				=> $change_return,
		    				'sales_invoice_format_id'	=> $sales_invoice_format_id,
		    				'pos_invoice_format_id'		=> $pos_invoice_format_id,
		    				'pdf_format'				=> $pdf_format,
		    				'sales_invoice_footer_text'	=> $sales_invoice_footer_text,
		    				'invoice_terms'				=> $invoice_terms,
						'round_off'					=> $round_off,
						'allow_negative_stock'		=> $allow_negative_stock,
						'pdf_part_number_popup'		=> $pdf_part_number_popup,
						'item_barcode_label'		=> $item_barcode_label,
						'paid_seal'					=> $paid_seal,
		    				'language_id'				=> $language_id,
		    				'barcode_type'				=> $barcode_type,
		    				'decimals'					=> $decimals,
		    				'qty_decimals'					=> $qty_decimals,
		    				'sales_payment_init'		=> $sales_payment_init,
		    				'sales_return_payment_init'	=> $sales_return_payment_init,
		    				'purchase_payment_init'		=> $purchase_payment_init,
		    				'purchase_return_payment_init'	=> $purchase_return_payment_init,
		    				'expense_payment_init'	=> $expense_payment_init,
		    				'cust_advance_init'	=> $cust_advance_init,
		    				'mrp_column'	=> $mrp_column,
		    				'previous_balance_bit'	=> $previous_balance_bit,
		    				't_and_c_status'	=> $t_and_c_status,
		    				't_and_c_status_pos'	=> $t_and_c_status_pos,
		    				'number_to_words'	=> $number_to_words,
		    				'number_to_words_pos'	=> $number_to_words_pos,
							);

		// Keep branch settings save-compatible with older databases where this
		// optional column has not been created yet.
		if($this->db->field_exists('invoice_declaration', 'db_store')){
			$data['invoice_declaration'] = isset($invoice_declaration) ? $invoice_declaration : '';
		}

		if(!empty($store_logo)){
			$data['store_logo']=$store_logo;
		}
		// Always refresh WhatsApp QR from number (or clear when number empty)
		$data['qr_image'] = $qr_image;
		/*custom helper*/
		if(vat_number()){
			$data['vat_no']=$vat_no;
		}
		/*end*/

		
			$store_code_count=$this->db->query("select count(*) as store_code_count from db_store where upper(store_code)=upper('$store_code') and id<>$q_id")->row()->store_code_count;
			if($store_code_count>0){
				echo "Sorry! Store Code Already Exist!\nPlease Change Store Code";exit();
			}

			$q1 = $this->db->where('id',$q_id)->update('db_store', $data);
			if($q1){
				$this->db->trans_commit();
				$this->session->unset_userdata('currency');
				//$this->session->set_flashdata('success', 'Success!! Record Updated Successfully! ');
				echo "success";
			}

		

		exit();
	}

	//Get store_details
	public function get_details($id){
		$data=$this->data;
		ensure_item_barcode_label_setting();
		ensure_paid_seal_setting();
		ensure_invoice_declaration_setting();
		ensure_location_url_setting();
		ensure_whatsapp_number_setting();
		ensure_store_profile_columns();

		$query1=$this->db->query("select * from db_store where upper(id)=upper('$id')");
		if($query1->num_rows()==0){
			show_404();exit;
		}
		else{
			/* QUERY 1*/
			$data['q_id']=$query1->row()->id;
			return array_merge($data,$query1->row_array());
			return $data;
		}
	}

}
