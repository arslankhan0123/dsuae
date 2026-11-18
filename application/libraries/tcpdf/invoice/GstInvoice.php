<?php

include('MyPDF.php');

class GstInvoice extends MyPDF{
	//public $CI=null;

	public $sales_id =null;

	public $store =array();

	protected $customer =array();

	protected $sales =array();
	
	protected $sales_payment = [];

	public $customer_state_name = null;

	public $with_part_number = false;


	public function __construct(array $param=array())
	{
		$this->sales_id = $param['sales_id'];
		$this->with_part_number = !empty($param['with_part_number']);
		parent::__construct();

		$this->sales_id = $param['sales_id'];

		//$this->CI =& get_instance();

		$this->sales = get_sales_details($this->sales_id);

		$this->store = get_store_details(!empty($this->sales->store_id) ? $this->sales->store_id : null);//Declared in MyPDF Pa

		$this->customer = get_customer_details($this->sales->customer_id);

	}
	/** Label: English only (Arabic removed from invoice body labels) */
	protected function _bi($en, $ar = '')
	{
		return '<nobr>' . htmlspecialchars((string)$en, ENT_QUOTES, 'UTF-8') . '</nobr>';
	}

	/** Give freed column width to unit/qty/rate/amount (not all to Part Name) */
	protected function _give_col_width(&$widthArray, $freed)
	{
		$freed = (float) $freed;
		if ($freed <= 0) {
			return;
		}
		$targets = array();
		foreach (array('unit', 'qty', 'rate', 'amount', 'dis', 'tax') as $key) {
			if (isset($widthArray[$key])) {
				$targets[] = $key;
			}
		}
		if (empty($targets)) {
			if (isset($widthArray['description'])) {
				$widthArray['description'] = (string) ((float) $widthArray['description'] + $freed);
			}
			return;
		}
		$each = $freed / count($targets);
		foreach ($targets as $key) {
			$widthArray[$key] = (string) round((float) $widthArray[$key] + $each, 2);
		}
	}

	protected function invoice_detail_table(array $rows, $label_width = 42) {
        $value_width = 97 - $label_width;
        $html = '<table border="0" cellpadding="0" cellspacing="0" style="width:100%;">';
        foreach ($rows as $label => $value) {
            $html .= '<tr><td style="width:'.$label_width.'%;white-space:nowrap;"><b>'.htmlspecialchars($label, ENT_QUOTES, 'UTF-8').'</b></td>'
                . '<td style="width:3%;"><b>:</b></td>'
                . '<td style="width:'.$value_width.'%;">'.nl2br(htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8')).'</td></tr>';
        }
        return $html.'</table>';
    }

    public function _get_customer_details()
    {   
        $sales = $this->sales;
    	$customer = $this->customer;//array()
    	
    // 	print_r($customer);
        // print_r($sales);

    	$store = $this->store;//array()
    	
    // 	print_r($store);
    	
    	//Customer Records
	    $state = (!empty($customer->state_id)) ? get_state_details($customer->state_id) : '';
	    $this->customer_state_name = (!empty($state)) ? $state->state : $store->state;

        $customer_phone = !empty($customer->mobile) ? $customer->mobile : $customer->phone;
        $customer_trn = '';
        if (!empty($sales->customer_trn_no)) {
            $customer_trn = trim((string)$sales->customer_trn_no);
        } elseif (!empty($customer->tax_number) && (int)$customer->id !== 2) {
            $customer_trn = trim((string)$customer->tax_number);
        }
        $show_tax = is_branch_tax_enabled(!empty($store->id) ? $store->id : '');
        $customer_no = !empty($customer->customer_code) ? $customer->customer_code : '';
        $salesperson_name = '';
        if (!empty($sales->salesman_id)) {
            $salesperson = $this->CI->db->select('salesman_name')->where('id', (int)$sales->salesman_id)->get('db_salesman')->row();
            if (!empty($salesperson)) $salesperson_name = $salesperson->salesman_name;
        }
        $customer_rows = array(
            'Customer No' => $customer_no,
            'Customer Name' => $customer->customer_name,
            'Mobile No' => !empty($sales->customer_mobile) ? $sales->customer_mobile : $customer_phone,
        );
        if ($show_tax) $customer_rows['Customer TRN'] = $customer_trn;
        $customer_rows['Address'] = !empty($sales->address) ? $sales->address : $customer->address;
        $customer_rows['Salesperson'] = $salesperson_name;
        $customer_rows['Remark'] = isset($sales->reference_no) ? $sales->reference_no : '';
        $customer_details = $this->invoice_detail_table($customer_rows);
        // Drawn later as one combined box in _get_invoice_details()
        $this->_customer_block_html = $customer_details;
        
        return $this;
    } 

    public function _get_invoice_details()
    {
    	$sales = $this->sales;//array()
    	//print_r($sales);
    	$customer = $this->customer;
    // 	print_r($customer);
    	
        $pageWidth = $this->getPageWidth();
        $printableWidth = $pageWidth - 12;
        // Slightly more room on invoice side for bilingual labels on one line
        $ratio_cust = ($pageWidth < 160) ? 0.50 : 0.55;
        $ratio_inv = 1 - $ratio_cust;
        $w_customer = $printableWidth * $ratio_cust;
        $w_invoice = $printableWidth * $ratio_inv;
        $w = $w_invoice;
		$isA5 = ($pageWidth < 160);
		$h = $isA5 ? 26 : 30;
        
        $payments = $this->CI->db->select('payment_type')
                            ->from('db_salespayments')
                            ->where('sales_id', $sales->id)
                            ->order_by('id', 'asc')
                            ->get();

        $payment_types = array();
        $payment_type_keys = array();
        foreach($payments->result() as $payment){
            $payment_type = trim($payment->payment_type);
            $payment_type_key = strtoupper($payment_type);
            if($payment_type !== '' && strcasecmp($payment_type, 'Advance') !== 0 && !in_array($payment_type_key, $payment_type_keys, true)){
                $payment_types[] = $payment_type;
                $payment_type_keys[] = $payment_type_key;
            }
        }

        $inv_type = !empty($payment_types) ? implode(', ', $payment_types) : 'CREDIT';
        $primary_inv_type = !empty($payment_types) ? strtoupper($payment_types[0]) : 'CREDIT';
        $this->primary_payment_type = $primary_inv_type;

        // Show Due Date + Due Days when due_date OR payment_terms (e.g. "30 days") is set — not by payment type
        $due_date_raw = isset($sales->due_date) ? trim((string)$sales->due_date) : '';
        if ($due_date_raw === '' || $due_date_raw === '0000-00-00' || $due_date_raw === '1970-01-01') {
            $due_date_raw = '';
        }
        $payment_terms = isset($sales->payment_terms) ? trim((string)$sales->payment_terms) : '';
        $due_days = null;
        if ($payment_terms !== '' && preg_match('/(\d+)/', $payment_terms, $ptm)) {
            $due_days = (int)$ptm[1];
        }
        if ($due_date_raw !== '' && !empty($sales->sales_date)) {
            $inv_ts = strtotime($sales->sales_date);
            $due_ts = strtotime($due_date_raw);
            if ($inv_ts !== false && $due_ts !== false) {
                $calc_days = (int) floor(($due_ts - $inv_ts) / 86400);
                if ($calc_days < 0) {
                    $calc_days = 0;
                }
                if ($due_days === null) {
                    $due_days = $calc_days;
                }
            }
        }
        // If only payment_terms set, derive due date from invoice date + days
        if ($due_date_raw === '' && $due_days !== null && !empty($sales->sales_date)) {
            $base_ts = strtotime($sales->sales_date);
            if ($base_ts !== false) {
                $due_date_raw = date('Y-m-d', strtotime('+'.$due_days.' days', $base_ts));
            }
        }
        $show_due = ($due_days !== null || $due_date_raw !== '');
        if ($show_due && $due_days === null) {
            $due_days = 0;
        }
        $due_date = ($show_due && $due_date_raw !== '') ? show_date($due_date_raw) : '';

        $invoice_type = !empty($sales->invoice_type) ? $sales->invoice_type : (!empty($payment_types) ? (in_array('CASH', $payment_type_keys, true) ? 'Cash' : 'Bank') : 'Credit');
        $payment_type = !empty($sales->payment_type) ? $sales->payment_type : implode(', ', $payment_types);
        if (strcasecmp($invoice_type, 'Credit') === 0) $payment_type = '';
        $card_type = isset($sales->card_type) ? $sales->card_type : '';
        $inv_date = show_date($sales->sales_date);
        $inv_time = !empty($sales->created_time) ? strtoupper(date('g:i A', strtotime($sales->created_time))) : '';
        $invoice_datetime = trim($inv_date . ($inv_time !== '' ? ' '.$inv_time : ''));
        $invoice_rows = array(
            'Invoice No' => $sales->sales_code,
            'Invoice Date' => $invoice_datetime,
            'Invoice Type' => $invoice_type,
            'Payment Type' => $payment_type,
            'Card Type' => $card_type,
            'Due Days' => $due_days !== null ? $due_days : '',
            'Due Date' => $due_date,
        );
        $invoice_details = $this->invoice_detail_table($invoice_rows, 42);
        // Single outer box — no vertical divider between customer / invoice
        $customer_html = !empty($this->_customer_block_html) ? $this->_customer_block_html : '';
        $cust_pct = round($ratio_cust * 100, 2);
        $inv_pct = round(100 - $cust_pct, 2);
        $combined = '<table border="0" cellpadding="0" cellspacing="0" style="width:100%;">'
                  . '<tr>'
                  . '<td style="width:'.$cust_pct.'%;vertical-align:top;border:none;">'.$customer_html.'</td>'
                  . '<td style="width:'.$inv_pct.'%;vertical-align:top;border:none;">'.$invoice_details.'</td>'
                  . '</tr></table>';

        $this->setCellPaddings(2,1,1,1);
		$this->setFont('dejavusans', '', $isA5 ? 6.5 : 8, '', true);
        $this->setFillColor(255, 255, 255);
		$this->SetDrawColor(170, 170, 170); // light border (match datatable)
		$invoice_y = !empty($this->title_bottom_y) ? ($this->title_bottom_y + 1) : (!empty($this->header_bottom_y) ? ($this->header_bottom_y + 12) : ($isA5 ? 32 : 34));
		// h=0 so border wraps full content (fixes missing bottom line)
		$this->writeHTMLCell($printableWidth, 0, $x = 6, $y=$invoice_y, $combined, 1, 1, 1, true, 'L', true);


		$this->Ln(1);
            
        return $this;
    }

    public function _get_shipping_address()
    {
        return null;
        $w = 100;
        $h = 40;

        $customer = $this->customer;//array()
        //Customer Shipping Address Records
	    $country='';
	    $state='';
	    $city='';
	    $address='';
	    $postcode='';
	    if(!empty($customer->shippingaddress_id)){
	        $Q2 = $this->CI->db->select("c.country,s.state,a.city,a.postcode,a.address")
	                        ->where("a.id",$customer->shippingaddress_id)
	                        ->from("db_shippingaddress a")
	                        ->join("db_country c","c.id = a.country_id",'left')
	                        ->join("db_states s","s.id = a.state_id",'left')
	                        ->get();                    
	        if($Q2->num_rows()>0){
	          $country=$Q2->row()->country;
	          $address=$Q2->row()->address;
	          $state=$Q2->row()->state;
	          $city=$Q2->row()->city;
	          $postcode=$Q2->row()->postcode;
	        }
	      }

        $custmer_details = '<span style="color:rgb(0, 0, 128);font-style:italic;">'.$this->CI->lang->line('shipping_address').'</span>';

        $custmer_details .= "<br><b>".$this->CI->lang->line('mobile')." :</b> ".$customer->mobile;
        $custmer_details .= "<br><b>".$this->CI->lang->line('name')." :</b> ".$customer->customer_name;
        $custmer_details .= "<br><b>".$this->CI->lang->line('address')." :</b> ".$address;
        $custmer_details .= "<br><b>".$this->CI->lang->line('postcode')." :</b> ".$postcode;
        $custmer_details .= "<br><b>".$this->CI->lang->line('city')." :</b> ".$city;
        $custmer_details .= "<br><b>".$this->CI->lang->line('state')." :</b> ".$state;

        $this->writeHTMLCell($w, $h, $x ='6', $y='90', $custmer_details, 1, 0, 1, true, 'J', true);
        return $this;
    }

    public function _get_bank_details()
    {
        return null;
    	$store = $this->store;
        $w = 100;
        $h = 25;
        $invoice_details = "";
        $invoice_details = '<span style="color:rgb(0, 0, 128);font-style:italic;">'.$this->CI->lang->line("bank_details").'</span><br>';
        $invoice_details .= nl2br($store->bank_details);

        $this->writeHTMLCell($w, $h, $x ='104', $y='', $invoice_details, 1, 1, 1, true, 'J', true);
        return $this;
    }
    
    
    public function _get_invoice_title_name()
    {

        $pageWidth = $this->getPageWidth();
        $w = $pageWidth - 12;
		$isA5 = ($pageWidth < 160);
		$h = $isA5 ? 13 : 14;

		$store_trn = (!empty($this->store->vat_no)) ? trim((string)$this->store->vat_no) : '';
		// Store TRN present => TAX INVOICE; otherwise SALES INVOICE
		$invoice_title = ($store_trn !== '') ? 'TAX INVOICE' : 'SALES INVOICE';

		$this->setCellMargins(0,0,0,0);
        $this->setCellPaddings(2, 2, 2, 2);
		$this->setFont($this->get_font_name(), '', ($pageWidth < 160) ? 9 : 11);
        $this->setFillColor(255, 255, 255);
		$this->SetDrawColor(170, 170, 170); // light border (match datatable)

		$title_y = !empty($this->header_bottom_y) ? ($this->header_bottom_y + 1) : ($isA5 ? 30 : 36);
		$title_x = 6;
        // Use fixed-height cells: TCPDF HTML tables do not vertically center short labels.
        $this->Rect($title_x, $title_y, $w, $h);
        $this->setCellPaddings(2, 0, 2, 0);
        $this->setFont($this->get_font_name(), 'B', $isA5 ? 6.75 : 8.25);
        $trn_text = $store_trn !== '' ? 'TRN No. : '.$store_trn : '';
        $this->MultiCell($w * 0.35, $h, $trn_text, 0, 'L', false, 0, $title_x, $title_y, true, 0, false, true, $h, 'M', true);
        $this->setFont($this->get_font_name(), 'B', $isA5 ? 12 : 19.5);
        $this->MultiCell($w * 0.30, $h, $invoice_title, 0, 'C', false, 0, $title_x + $w * 0.35, $title_y, true, 0, false, true, $h, 'M', true);
		$this->setCellPaddings(2, 2, 2, 2);
		$this->title_bottom_y = $title_y + $h;
        // Invoice barcode belongs in the right-hand cell of the title band.
        $bc_code = (string)$this->sales->sales_code;
        $bc_w = min($isA5 ? 42 : 52, $w * 0.35 - 4);
        $bc_h = $isA5 ? 9 : 10;
        $bc_x = $title_x + $w - $bc_w - 2;
        $bc_y = $title_y + ($h - $bc_h) / 2;
        $saved_x = $this->GetX();
        $saved_y = $this->GetY();
        $style = array(
            'position' => '', 'align' => 'C', 'stretch' => false,
            'fitwidth' => true, 'cellfitalign' => '', 'border' => false,
            'hpadding' => 'auto', 'vpadding' => 0,
            'fgcolor' => array(0, 0, 0), 'bgcolor' => false, 'text' => false,
        );
        // TCPDF subtracts cell padding from the bars even when text is disabled.
        // Clear it so the visible bars occupy the full centered barcode height.
        $this->setCellPaddings(0, 0, 0, 0);
        $this->write1DBarcode($bc_code, 'C128', $bc_x, $bc_y, $bc_w, $bc_h, 0.35, $style, 'N');
        $this->setCellPaddings(2, 2, 2, 2);
        $this->SetXY($saved_x, $saved_y);
        
        return $this;
    }

    // Generate (left) + Page (right) rendered by MyPDF::Footer just above address line
    public function Footer()
    {
		$gen_date = !empty($this->sales->sales_date) ? show_date($this->sales->sales_date) : '';
		$gen_time = !empty($this->sales->created_time) ? strtoupper(date('g:i A', strtotime($this->sales->created_time))) : '';
		$this->footer_generate_text = 'Generate: ' . trim($gen_date . ($gen_time !== '' ? ' '.$gen_time : ''));

        parent::Footer();
    }

   
	public function show_pdf()
	{
		$this->_main_body();
	}

	public function print()
	{
		$sales 		= $this->sales; //array()
		$store 		= $this->store; //array()
		$customer 	= $this->customer; //array()



		$this->_invoice_name = "Tax Invoice";

		$this->page_title = "Tax Invoice";

		//Don't change this
		$this->_invoice_format = 'GST';

		//$this->_QRCODE = $sales->sales_code;

		// set font
		$this->setFont($this->get_font_name(), 'B', 20);

		// add a page
		$this->AddPage();


		//invoice title
		$this->_get_invoice_title_name();

		// Cusomer Details
		$this->_get_customer_details();

		// Cusomer Details
		$this->_get_invoice_details();

		// Shipping Details
		$this->_get_shipping_address();

		// Bank Details
		$this->_get_bank_details();

		//Set document name (footer -R)
		$this->_set_document_name('Printed on');

		//Sey document number (footer -R)
		$this->_set_document_number(date('d-m-Y h:i:s a'));

		//Search Coupon Details
		$coupon_code = $coupon_type = '';
		$coupon_value = 0;
		if (!empty($sales->coupon_id)) {
			$coupon_details = get_customer_coupon_details($sales->coupon_id);
			$coupon_code = $coupon_details->code;
			$coupon_value = $coupon_details->value;
			$coupon_type = $coupon_details->type;
		}
		$this->setFont($this->get_font_name(), '', 8);

		// set cell padding
		$this->setCellPaddings(1, 1, 1, 1);

		// set cell margins
		//$this->setCellMargins(1, 1, 1, 1);

		// set color for background
		$this->setFillColor(255, 255, 255);

		$this->Ln(0);



		$this->setFont($this->get_font_name(), '', 8);


		$tbl = '
		<style type="text/css">
			table {
			    border-collapse: collapse;
			}
			#print, .totals-table, .signatures-table {
			    border: 0.5px solid #AAAAAA;
			}
			th {
			    border: 0.5px solid #AAAAAA;
			}
			.totals-table td {
			    border: 0.5px solid #AAAAAA;
			}
			table + table, table + table tr:first-child th, table + table tr:first-child td {
			    border-top: 0;
			}
			.text-right{
				text-align: right;
			}
			.text-center{
				text-align: center;
			}
			.text-bold{
				font-weight: bold;
			}
			.bg-light-blue{
				background-color: #e4eaff;
			}
			
			#print tbody td {
				border-top: none;
				border-bottom: none;
			}
		</style>
		<table id="print" >';

		$pageWidth = $this->getPageWidth();
		$item_font_size = ($pageWidth < 160) ? 8 : 12;
		$with_part_number = !empty($this->with_part_number);
		// Show VAT column + Taxable/VAT summary when store TRN enabled (TAX INVOICE)
		$show_tax = is_branch_tax_enabled(!empty($store->id) ? $store->id : '');
		$show_vat_summary = $show_tax;
		// Hide Dis column + SubTotal/Discount when invoice discount is 0
		$is_pos_sale_preview = (isset($sales->pos) && (int) $sales->pos === 1);
		$item_disc_row = $this->CI->db->select_sum('discount_amt')->where('sales_id', $this->sales_id)->get('db_salesitems')->row();
		$item_disc_sum = (float) (isset($item_disc_row->discount_amt) ? $item_disc_row->discount_amt : 0);
		$preview_invoice_discount = $is_pos_sale_preview
			? (float) (isset($sales->tot_discount_to_all_amt) ? $sales->tot_discount_to_all_amt : 0)
			: $item_disc_sum + (float) (isset($sales->tot_discount_to_all_amt) ? $sales->tot_discount_to_all_amt : 0) + (float) (isset($sales->coupon_amt) ? $sales->coupon_amt : 0);
		$show_discount = ($preview_invoice_discount > 0);
		$description_label = (get_item_barcode_label_key() === 'barcode_no')
			? $this->_bi('Item Name', 'Item Name')
			: $this->_bi('Part Name', 'Part Name');
		$barcode_label = (get_item_barcode_label_key() === 'barcode_no')
			? $this->_bi('Barcode No', 'Barcode No')
			: $this->_bi('Part No', 'Part No');
		if ($pageWidth < 160) {
			$widthArray = array(
				'sl_no' 		=> '8',
				'barcode' 		=> '11',
				'description' 	=> '20',
				'unit' 			=> '8',
				'qty' 			=> '9',
				'rate' 	        => '11',
				'dis'           => '10',
				'tax'           => '10',
				'amount' 		=> '13',
			);
		} else {
			$widthArray = array(
				'sl_no' 		=> '8',
				'barcode' 		=> '12',
				'description' 	=> '24',
				'unit' 			=> '8',
				'qty' 			=> '9',
				'rate' 	        => '10',
				'dis'           => '8',
				'tax'           => '10',
				'amount' 		=> '11',
			);
		}

		// Hide optional columns â€” freed width goes to Unit/Qty/Price/Amount (keep Part Name compact)
		if (!$with_part_number) {
			$freed = (float) $widthArray['barcode'];
			unset($widthArray['barcode']);
			$this->_give_col_width($widthArray, $freed);
		}

		if (!$show_tax) {
			$freed = (float) $widthArray['tax'];
			unset($widthArray['tax']);
			$this->_give_col_width($widthArray, $freed);
		}

		if (!$show_discount) {
			$freed = (float) $widthArray['dis'];
			unset($widthArray['dis']);
			$this->_give_col_width($widthArray, $freed);
		}

		//Sum the value
		$sumOfWidth = 0;
		$colW = array();

		foreach ($widthArray as $key => $val) {

			//Update value
			$colWidthSize[$key] = $val;

			//New Array => Reasssign % symbol
			$colW[$key] = $val . '%';

			//Sum of value
			$sumOfWidth += $val;
		}



		$tbl .= '<thead>
		        <tr class="bg-light-blue text-bold" style="width: 100%;font-size:' . $item_font_size . 'px;">
			        <th colspan="1" style="text-align:center;width: ' . $colW['sl_no'] . '">' . $this->_bi('S/N', 'Ù…Ø³Ù„Ø³Ù„') . '</th>';
		if ($with_part_number) {
			$tbl .= '<th colspan="1" class="text-center" style="width: ' . $colW['barcode'] . '">' . $barcode_label . '</th>';
		}
		$tbl .= '
			        <th colspan="1" class="text-center" style="width: ' . $colW['description'] . '" >' . $description_label . '</th>
			        <th colspan="1" class="text-center" style="width: ' . $colW['unit'] . '">' . $this->_bi('Unit', 'ÙˆØ­Ø¯Ø©') . '</th>
			        <th colspan="1" style="text-align:center;width: ' . $colW['qty'] . '">' . $this->_bi('Qty', 'Ø§Ù„ÙƒÙ…ÙŠØ©') . '</th>
			        <th colspan="1" style="text-align:right;width: ' . $colW['rate'] . '">' . $this->_bi('Price', 'Ø§Ù„Ø³Ø¹Ø±') . '</th>';
		if ($show_discount) {
			$tbl .= '<th colspan="1" style="text-align:right;width: ' . $colW['dis'] . '">' . $this->_bi('Dis', 'Ø®ØµÙ…') . '</th>';
		}
			if ($show_tax) {
			$tbl .= '<th colspan="1" style="text-align:right;width: ' . $colW['tax'] . '">' . $this->_bi('VAT Amt', 'Ø§Ù„Ø¶Ø±ÙŠØ¨Ø©') . '</th>';
			}
			$tbl .= '
			        <th colspan="1" style="text-align:right;width: ' . $colW['amount'] . '">' . $this->_bi('Amount', 'Ø§Ù„Ù…Ø¨Ù„Øº') . '</th>
		        </tr>
		    </thead>
		    <tbody>';

		$i = 1;
		$tot_qty = 0;
		$tot_sales_price = 0;
		$tot_tax_amt = 0;
		$tot_discount_amt = 0;
		$tot_unit_total_cost = 0;
		$tot_total_cost = 0;
		$tot_before_tax = 0;

		$tot_price_per_unit = 0;
		$sum_of_tot_price = 0;
		$sub_total = 0;



		$this->CI->db->select(" a.description,c.item_name,c.item_code,c.custom_barcode, a.sales_qty,a.tax_type,
                                  a.price_per_unit, b.tax,b.tax_name,a.tax_amt,
                                  a.discount_input,a.discount_amt, a.unit_total_cost,
                                  a.total_cost, d.unit_name,c.sku,c.hsn
                              ");
		$this->CI->db->where("a.sales_id", $this->sales_id);
		$this->CI->db->from("db_salesitems a");
		$this->CI->db->join("db_tax b", "b.id=a.tax_id", "left");
		$this->CI->db->join("db_items c", "c.id=a.item_id", "left");
		$this->CI->db->join("db_units d", "d.id = c.unit_id", "left");

		$q2 = $this->CI->db->get();

		//   print_r(count($q2));
		//   print_r($q2->result());

		$items_rows = $q2->result();
		$is_pos_sale = (isset($sales->pos) && (int) $sales->pos === 1);
		$invoice_level_discount = (float) (isset($sales->tot_discount_to_all_amt) ? $sales->tot_discount_to_all_amt : 0)
			+ (float) (isset($sales->coupon_amt) ? $sales->coupon_amt : 0);
		if ($is_pos_sale) {
			$invoice_level_discount = (float) (isset($sales->tot_discount_to_all_amt) ? $sales->tot_discount_to_all_amt : 0);
		}
		$line_bases = array();
		$total_base_for_disc = 0.0;
		foreach ($items_rows as $idx => $res2) {
			$qty = (float) $res2->sales_qty;
			if ($res2->tax_type == 'Inclusive') {
				$base = ((float) $res2->price_per_unit * $qty) - (float) $res2->tax_amt;
			} else {
				$base = (float) $res2->price_per_unit * $qty;
			}
			$line_bases[$idx] = max(0, $base);
			$total_base_for_disc += $line_bases[$idx];
		}
		$distributed = array();
		$distributed_sum = 0.0;
		$item_count = count($items_rows);
		foreach ($items_rows as $idx => $res2) {
			$share = ($total_base_for_disc > 0 && $invoice_level_discount > 0)
				? (($line_bases[$idx] / $total_base_for_disc) * $invoice_level_discount)
				: 0.0;
			$distributed[$idx] = $share;
			$distributed_sum += $share;
		}
		if ($item_count > 0 && $invoice_level_discount > 0) {
			$distributed[$item_count - 1] += ($invoice_level_discount - $distributed_sum);
		}

		foreach ($items_rows as $idx => $res2) {
			// print_r($res2);
			$barcode_no = (!empty($res2->custom_barcode)) ? $res2->custom_barcode : $res2->item_code;
			$discount = (empty($res2->discount_input) || $res2->discount_input == 0) ? store_number_format(0) : store_number_format($res2->discount_input) . "%";
			$line_discount_amt = $is_pos_sale
				? (float) $distributed[$idx]
				: ((float) $res2->discount_amt + (float) $distributed[$idx]);
			$discount_amt = $line_discount_amt;
			$before_tax = $res2->price_per_unit; // * $res2->sales_qty;
			$tot_cost_before_tax = $res2->total_cost; //$before_tax * $res2->sales_qty;

			$tax_type = ($res2->tax_type == 'Exclusive') ? 'Exc.' : 'Inc.';
			$tbl .= '<tr style="" nobr="true" style="width: 100%;">';
			$tbl .= '<td colspan="1" style="text-align:center;width: ' . $colW['sl_no'] . ';font-size:' . $item_font_size . 'px;border-left:0.5px solid #AAAAAA;border-right:0.5px solid #AAAAAA;border-top:none;border-bottom:none;">' . $i++ . '</td>';
			if ($with_part_number) {
				$tbl .= '<td colspan="1" style="text-align:left;width: ' . $colW['barcode'] . ';font-size:' . $item_font_size . 'px;border-left:0.5px solid #AAAAAA;border-right:0.5px solid #AAAAAA;border-top:none;border-bottom:none;">' . $barcode_no . '</td>';
			}
			$tbl .= '<td colspan="1" style="width: ' . $colW['description'] . ';font-size:' . $item_font_size . 'px;border-left:0.5px solid #AAAAAA;border-right:0.5px solid #AAAAAA;border-top:none;border-bottom:none;" >';
			$tbl .= '<nobr>' . $res2->item_name . '</nobr>';
			$tbl .= (!empty($res2->description)) ? "<br><i>[" . nl2br($res2->description) . "]</i>" : '';
			$tbl .= '</td>';
			$tbl .= '<td colspan="1" style="width: ' . $colW['unit'] . ';border-left:0.5px solid #AAAAAA;border-right:0.5px solid #AAAAAA;border-top:none;border-bottom:none;">' . $res2->unit_name . '</td>';

			$tbl .= '<td colspan="1" style="text-align:center;width: ' . $colW['qty'] . ';font-size:' . $item_font_size . 'px;border-left:0.5px solid #AAAAAA;border-right:0.5px solid #AAAAAA;border-top:none;border-bottom:none;">' . format_qty($res2->sales_qty) . '</td>';

			$tbl .= '<td colspan="1" style="text-align:right;width: ' . $colW['rate'] . ';font-size:' . $item_font_size . 'px;border-left:0.5px solid #AAAAAA;border-right:0.5px solid #AAAAAA;border-top:none;border-bottom:none;"><nobr>' . store_number_format($res2->price_per_unit) . '</nobr></td>';
			if ($show_discount) {
				$tbl .= '<td colspan="1" style="text-align:right;width: ' . $colW['dis'] . ';font-size:' . $item_font_size . 'px;border-left:0.5px solid #AAAAAA;border-right:0.5px solid #AAAAAA;border-top:none;border-bottom:none;"><nobr>' . store_number_format($line_discount_amt) . '</nobr></td>';
			}
			if ($show_tax) {
				$tbl .= '<td colspan="1" style="text-align:right;width: ' . $colW['tax'] . ';font-size:' . $item_font_size . 'px;border-left:0.5px solid #AAAAAA;border-right:0.5px solid #AAAAAA;border-top:none;border-bottom:none;"><nobr>' . store_number_format($res2->tax_amt) . '</nobr></td>';
			}
			$tbl .= '<td colspan="1" style="text-align:right;width: ' . $colW['amount'] . ';font-size:' . $item_font_size . 'px;border-left:0.5px solid #AAAAAA;border-right:0.5px solid #AAAAAA;border-top:none;border-bottom:none;"><nobr>' . (store_number_format($res2->total_cost)) . '</nobr></td>';

			$tbl .= '</tr>';

			$tot_qty += $res2->sales_qty;
			$tot_sales_price += $res2->price_per_unit;
			$tot_tax_amt += $res2->tax_amt;
			$tot_discount_amt += $res2->discount_amt;
			$tot_unit_total_cost += $res2->unit_total_cost;
			$tot_before_tax += $before_tax;
			$tot_total_cost += $tot_cost_before_tax;
			if ($res2->tax_type == 'Inclusive') {
				$unit_price_before_tax = $res2->price_per_unit - ($res2->tax_amt / ($res2->sales_qty > 0 ? $res2->sales_qty : 1));
				$sub_total += $unit_price_before_tax * $res2->sales_qty;
			} else {
				$sub_total += $res2->price_per_unit * $res2->sales_qty;
			}
		}

		$mCount = count($q2->result());

		// Fill remaining page space with empty rows so Total sits just above Taxable block
		$pageH = (float) $this->getPageHeight();
		$currY = (float) $this->GetY();
		$bottom_reserve = ($pageWidth < 160) ? 82 : 92; // room for totals/QR/thank you + footer
		$avail_mm = max(40, $pageH - $currY - $bottom_reserve);
		$row_h_mm = ($pageWidth < 160) ? 3.4 : 3.5;
		$target_rows = max($mCount, (int) floor($avail_mm / $row_h_mm));
		$max_rows = ($pageWidth < 160) ? 23 : 35;
		$target_rows = min($target_rows, $max_rows);
		$empty_needed = max(0, $target_rows - $mCount);
		$cell_v = 'border-left:0.5px solid #AAAAAA;border-right:0.5px solid #AAAAAA;border-top:none;border-bottom:none;';
		$empty_pad = '<br/>';
		for ($er = 0; $er < $empty_needed; $er++) {
			$tbl .= '<tr nobr="true">';
			$tbl .= '<td style="width: ' . $colW['sl_no'] . ';' . $cell_v . '">'.$empty_pad.'</td>';
			if ($with_part_number) {
				$tbl .= '<td style="width: ' . $colW['barcode'] . ';' . $cell_v . '">'.$empty_pad.'</td>';
			}
			$tbl .= '<td style="width: ' . $colW['description'] . ';' . $cell_v . '">'.$empty_pad.'</td>';
			$tbl .= '<td style="width: ' . $colW['unit'] . ';' . $cell_v . '">'.$empty_pad.'</td>';
			$tbl .= '<td style="width: ' . $colW['qty'] . ';' . $cell_v . '">'.$empty_pad.'</td>';
			$tbl .= '<td style="width: ' . $colW['rate'] . ';' . $cell_v . '">'.$empty_pad.'</td>';
			if ($show_discount) {
				$tbl .= '<td style="width: ' . $colW['dis'] . ';' . $cell_v . '">'.$empty_pad.'</td>';
			}
			if ($show_tax) {
				$tbl .= '<td style="width: ' . $colW['tax'] . ';' . $cell_v . '">'.$empty_pad.'</td>';
			}
			$tbl .= '<td style="width: ' . $colW['amount'] . ';' . $cell_v . '">'.$empty_pad.'</td>';
			$tbl .= '</tr>';
		}

		$col_before_qty_width = ($colWidthSize['sl_no'] + ($with_part_number ? (float)$colWidthSize['barcode'] : 0) + (float)$colWidthSize['description'] + (float)$colWidthSize['unit']) . '%';
		$tot_label_colspan = $with_part_number ? 4 : 3;
		$tbl .= '<tr nobr="true" class="text-bold" style="font-size:' . $item_font_size . 'px;">';
		$tbl .= '<td colspan="' . $tot_label_colspan . '" class="text-right text-bold" style="width: ' . $col_before_qty_width . ';border-top:0.5px solid #AAAAAA;border-bottom:0.5px solid #AAAAAA;border-left:0.5px solid #AAAAAA;border-right:0.5px solid #AAAAAA;font-weight:bold;">' . $this->_bi('Total', 'Ø§Ù„Ø¥Ø¬Ù…Ø§Ù„ÙŠ') . '&nbsp;</td>';
		$tbl .= '<td colspan="1" class="text-center text-bold" style="width: ' . $colW['qty'] . ';border-top:0.5px solid #AAAAAA;border-bottom:0.5px solid #AAAAAA;border-left:0.5px solid #AAAAAA;border-right:0.5px solid #AAAAAA;font-weight:bold;">' . format_qty($tot_qty) . '</td>';
		$tbl .= '<td colspan="1" style="width: ' . $colW['rate'] . ';border-top:0.5px solid #AAAAAA;border-bottom:0.5px solid #AAAAAA;border-left:0.5px solid #AAAAAA;border-right:0.5px solid #AAAAAA;">&nbsp;</td>';
		if ($show_discount) {
			$tbl .= '<td colspan="1" style="width: ' . $colW['dis'] . ';border-top:0.5px solid #AAAAAA;border-bottom:0.5px solid #AAAAAA;border-left:0.5px solid #AAAAAA;border-right:0.5px solid #AAAAAA;">&nbsp;</td>';
		}
		if ($show_tax) {
			$tbl .= '<td colspan="1" style="width: ' . $colW['tax'] . ';border-top:0.5px solid #AAAAAA;border-bottom:0.5px solid #AAAAAA;border-left:0.5px solid #AAAAAA;border-right:0.5px solid #AAAAAA;">&nbsp;</td>';
		}
		$tbl .= '<td colspan="1" style="width: ' . $colW['amount'] . ';border-top:0.5px solid #AAAAAA;border-bottom:0.5px solid #AAAAAA;border-left:0.5px solid #AAAAAA;border-right:0.5px solid #AAAAAA;">&nbsp;</td>';
		$tbl .= '</tr>';

		$tbl .= '</tbody>
		    
		</table>
		';

		// POS stores the complete discount (item + invoice discount) in
		// tot_discount_to_all_amt, so adding item discounts again duplicates them.
		$invoice_discount_amt = (isset($sales->pos) && (int) $sales->pos === 1)
			? (float) $sales->tot_discount_to_all_amt
			: (float) $tot_discount_amt + (isset($sales->tot_discount_to_all_amt) ? (float) $sales->tot_discount_to_all_amt : 0) + (isset($sales->coupon_amt) ? (float) $sales->coupon_amt : 0);
		$show_discount = ($invoice_discount_amt > 0);

		$calc_subtotal = (float) $sub_total;
		$taxable_base = max(0, $calc_subtotal - $invoice_discount_amt);

		if ($calc_subtotal > 0 && $invoice_discount_amt > 0) {
			$discount_ratio = $taxable_base / $calc_subtotal;
			$tot_tax_amt = 0;
			foreach ($q2->result() as $r_tax) {
				$r_qty = (float) $r_tax->sales_qty;
				$r_rate = (float) $r_tax->price_per_unit;
				$r_tax_pct = (float) $r_tax->tax;
				if ($r_tax->tax_type == 'Inclusive') {
					$r_base = ($r_rate * $r_qty - (float) $r_tax->tax_amt);
				} else {
					$r_base = ($r_rate * $r_qty);
				}
				$r_discounted_base = $r_base * $discount_ratio;
				$tot_tax_amt += ($r_discounted_base * ($r_tax_pct / 100));
			}
		}

		if (!$show_vat_summary) {
			$tot_tax_amt = 0;
		}

		$vat_label = 'VAT';
		if ($show_vat_summary && isset($q2) && $q2->num_rows() > 0) {
			$vat_pct = null;
			$mixed_vat = false;
			foreach ($q2->result() as $r_vat) {
				$pct = (float) $r_vat->tax;
				if ($vat_pct === null) {
					$vat_pct = $pct;
				} elseif (abs($vat_pct - $pct) > 0.0001) {
					$mixed_vat = true;
					break;
				}
			}
			if (!$mixed_vat && $vat_pct !== null) {
				$vat_label = 'VAT ' . rtrim(rtrim(number_format($vat_pct, 2, '.', ''), '0'), '.') . '%';
			}
		}

		$other_charges = isset($sales->other_charges_amt) ? (float) $sales->other_charges_amt : 0;
		$invoice_net_total = round_off_amount($taxable_base + $tot_tax_amt + $other_charges);
		$change_return_amount = (float) get_change_return_amount($sales->id);

		// TASK-30: QR codes (left of totals) — generate before summary table
		$show_web_qr = ((int) (isset($store->show_website_qr) ? $store->show_website_qr : 0) === 1);
		$show_loc_qr = ((int) (isset($store->show_location_qr) ? $store->show_location_qr : 0) === 1);
		$show_wa_qr  = ((int) (isset($store->show_whatsapp_qr) ? $store->show_whatsapp_qr : 0) === 1);
		$show_inv_qr = ((int) (isset($store->show_invoice_qr) ? $store->show_invoice_qr : 0) === 1);
		$show_za_qr  = ((int) (isset($store->show_zatca_qr) ? $store->show_zatca_qr : 0) === 1);

		if (file_exists(APPPATH . 'libraries/tcpdf/tcpdf_barcodes_2d.php') && !class_exists('TCPDF2DBarcode', false)) {
			require_once APPPATH . 'libraries/tcpdf/tcpdf_barcodes_2d.php';
		}

		$web_qr_img = '';
		if ($show_web_qr) {
			$web_url = !empty($store->website) ? (preg_match('/^https?:\/\//i', $store->website) ? $store->website : 'https://' . $store->website) : 'https://www.sap.com/';
			try {
				$b = new TCPDF2DBarcode($web_url, 'QRCODE,M');
				$png = $b->getBarcodePngData(8, 8);
				if (!empty($png)) { $web_qr_img = '@' . base64_encode($png); }
			} catch (Exception $e) {}
		}

		$loc_qr_img = '';
		if ($show_loc_qr) {
			$loc_url = get_store_location_url_for_qr($store);
			if (!empty($loc_url)) {
				try {
					$b = new TCPDF2DBarcode($loc_url, 'QRCODE,M');
					$png = $b->getBarcodePngData(8, 8);
					if (!empty($png)) { $loc_qr_img = '@' . base64_encode($png); }
				} catch (Exception $e) {}
			}
		}

		$wa_qr_img = '';
		if ($show_wa_qr) {
			if (!empty($store->qr_image) && file_exists($store->qr_image)) {
				$wa_qr_img = base_url($store->qr_image);
			} else {
				$wa_src = !empty($store->whatsapp_number) ? $store->whatsapp_number : (!empty($store->mobile) ? $store->mobile : '');
				$wa_no = normalize_whatsapp_number($wa_src);
				if (!empty($wa_no)) {
					try {
						$b = new TCPDF2DBarcode('https://wa.me/' . $wa_no, 'QRCODE,M');
						$png = $b->getBarcodePngData(8, 8);
						if (!empty($png)) { $wa_qr_img = '@' . base64_encode($png); }
					} catch (Exception $e) {}
				}
			}
		}

		$inv_qr_img = '';
		if ($show_inv_qr) {
			$salt = $this->CI->config->item('encryption_key');
			if (empty($salt)) { $salt = 'retail_app_secret_salt'; }
			$token = md5($sales->id . $salt);
			$inv_dl_url = base_url("pdf/sales_public/{$sales->id}/{$token}");
			try {
				$b = new TCPDF2DBarcode($inv_dl_url, 'QRCODE,M');
				$png = $b->getBarcodePngData(8, 8);
				if (!empty($png)) { $inv_qr_img = '@' . base64_encode($png); }
			} catch (Exception $e) {}
		}

		$zatca_qr_img = '';
		if ($show_za_qr) {
			$inv_datetime = (!empty($sales->sales_date) ? $sales->sales_date : date('Y-m-d')) . ' ' . (!empty($sales->created_time) ? $sales->created_time : '00:00:00');
			$zatca_vat_no = !empty($store->vat_no) ? $store->vat_no : (!empty($store->gst_no) ? $store->gst_no : '');
			$zatca_qr_img = get_zatca_qr_code_image($store->store_name, $zatca_vat_no, $inv_datetime, $sales->grand_total, $tot_tax_amt);
		}

		$paid_seal_img = '';
		$show_paid_seal = should_show_paid_seal(!empty($sales->payment_status) ? $sales->payment_status : null);
		if ($show_paid_seal) {
			$paid_seal_img = get_paid_seal_img_src();
			if ($paid_seal_img === '') {
				$show_paid_seal = false;
			}
		}

		// Side QR strip (Location / WhatsApp / Invoice — replaces bank details)
		$side_qr_size = ($pageWidth < 160) ? 52 : 72;
		$side_qr_label = ($pageWidth < 160) ? 8 : 9;
		$qr_side_items = array();
		if ($show_loc_qr && !empty($loc_qr_img)) { $qr_side_items[] = array($loc_qr_img, 'Location'); }
		if ($show_wa_qr && !empty($wa_qr_img)) { $qr_side_items[] = array($wa_qr_img, 'WhatsApp'); }
		if ($show_inv_qr && !empty($inv_qr_img)) { $qr_side_items[] = array($inv_qr_img, 'Invoice'); }
		if ($show_web_qr && !empty($web_qr_img)) { $qr_side_items[] = array($web_qr_img, 'Website'); }
		if ($show_za_qr && !empty($zatca_qr_img)) { $qr_side_items[] = array($zatca_qr_img, 'ZATCA'); }
		if ($show_paid_seal && !empty($paid_seal_img)) { $qr_side_items[] = array($paid_seal_img, 'Paid'); }

		$qr_side_inner = '';
        if (!empty($qr_side_items)) {
            $qr_count = count($qr_side_items);
            $qr_strip_mm = ($pageWidth - 12) * (($pageWidth < 160) ? 0.52 : 0.72);
            $qr_cell_px = ($qr_strip_mm - 4) / $this->pixelsToUnits(1) / $qr_count;
            $side_qr_size = min($side_qr_size, max(12, (int)floor($qr_cell_px - 8)));
            $qr_cell_width = 100 / $qr_count;
            $qr_side_inner = '<table border="0" cellpadding="2" cellspacing="0" style="width:100%;"><tr>';
			foreach ($qr_side_items as $qr_index => $qi) {
				$qr_side_inner .= '<td width="'.$qr_cell_width.'%" align="center" style="text-align:center; vertical-align:middle;">'
					. '<img src="'.$qi[0].'" width="'.$side_qr_size.'" height="'.$side_qr_size.'" />'
					. '<br/><span style="font-size:'.$side_qr_label.'px;font-weight:bold;">'.$qi[1].'</span>'
					. '</td>';
			}
			$qr_side_inner .= '</tr></table>';
		}

		$summary_gap = ($pageWidth < 160) ? 2 : 4;
		$totals_html = '<div style="font-size:'.$summary_gap.'px;line-height:'.$summary_gap.'px;">&nbsp;</div>';
		$totals_html .= '<table class="totals-table">';
		$totals_html .= '<tbody>';

		$col_left = ($pageWidth < 160) ? 52 : 72;
		$col_right = 100 - $col_left;
		$col_r_left = $col_right * (($pageWidth < 160) ? 0.40 : 0.48);
		$col_r_right = $col_right - $col_r_left;

		// TASK-30: QR strip spans summary rows (bank details removed)
		$qr_side_rows = 0;
		if ($show_tax || $show_discount) {
			$qr_side_rows++; // Subtotal
		}
		if ($show_discount) {
			$qr_side_rows++; // Discount
		}
		if ($show_vat_summary) {
			$qr_side_rows += 2;
		}
		if ($change_return_amount > 0) {
			$qr_side_rows++;
		}
		$qr_side_cell = '';
		if ($qr_side_rows > 0) {
			$qr_side_cell = '<td rowspan="'.$qr_side_rows.'" colspan="3" style="border:none;border-bottom:none;padding-top:2px;width:'.$col_left.'%; vertical-align:middle;">'
				. $qr_side_inner
				. '</td>';
		}

		if ($show_tax || $show_discount) {
			$totals_html .= '<tr>';
			$totals_html .= $qr_side_cell;
			$qr_side_cell = '';
			$totals_html .= '<td class="text-left" style="height:22px; width: '.$col_r_left.'%; font-size:15px; border-bottom:0.5px solid #AAAAAA; border-left:0.5px solid #AAAAAA; border-right:0.5px solid #AAAAAA;">'.$this->_bi('Subtotal', 'Ø§Ù„Ù…Ø¬Ù…ÙˆØ¹ Ø§Ù„ÙØ±Ø¹ÙŠ').'</td>';
			$totals_html .= '<td style="height:22px; font-size:15px; width: '.$col_r_right.'%; border-bottom:0.5px solid #AAAAAA; border-left:0.5px solid #AAAAAA; border-right:0.5px solid #AAAAAA;" class="text-right">';
			$totals_html .= store_number_format($calc_subtotal);
			$totals_html .= '</td>';
			$totals_html .= '</tr>';

		}

		if ($show_discount) {
			$discount_border_bottom = $show_vat_summary || $change_return_amount > 0 ? '0.5px solid #AAAAAA' : 'none';
			$totals_html .= '<tr>';
			$totals_html .= '<td class="text-left" style="height:22px; width: '.$col_r_left.'%; font-size:15px; border-bottom:'.$discount_border_bottom.'; border-left:0.5px solid #AAAAAA; border-right:0.5px solid #AAAAAA;">'.$this->_bi('Discount', 'Ø§Ù„Ø®ØµÙ…').'</td>';
			$totals_html .= '<td class="text-right" style="height:22px; font-size:15px; width: '.$col_r_right.'%; border-bottom:'.$discount_border_bottom.'; border-left:0.5px solid #AAAAAA; border-right:0.5px solid #AAAAAA;">';
			$totals_html .= store_number_format($invoice_discount_amt);
			$totals_html .= '</td>';
			$totals_html .= '</tr>';
		}

		if ($show_vat_summary) {
			$totals_html .= '<tr>';
			if ($qr_side_cell !== '') {
				$totals_html .= $qr_side_cell;
				$qr_side_cell = '';
			}
			$totals_html .= '<td class="text-left" style="height:22px; width: '.$col_r_left.'%; font-size:15px; border-bottom:0.5px solid #AAAAAA; border-left:0.5px solid #AAAAAA; border-right:0.5px solid #AAAAAA;">'.$this->_bi('Taxable', 'Ø§Ù„Ø®Ø§Ø¶Ø¹ Ù„Ù„Ø¶Ø±ÙŠØ¨Ø©').'</td>';
			$totals_html .= '<td class="text-right" style="height:22px; font-size:15px; width: '.$col_r_right.'%; border-bottom:0.5px solid #AAAAAA; border-left:0.5px solid #AAAAAA; border-right:0.5px solid #AAAAAA;">';
			$totals_html .= store_number_format($taxable_base);
			$totals_html .= '</td>';
			$totals_html .= '</tr>';
			$totals_html .= '<tr>';
			$totals_html .= '<td class="text-left" style="height:22px; width: '.$col_r_left.'%; font-size:15px; border-bottom:none; border-left:0.5px solid #AAAAAA; border-right:0.5px solid #AAAAAA;"><nobr>'.$vat_label.'</nobr></td>';
			$totals_html .= '<td class="text-right" style="height:22px; font-size:15px; width: '.$col_r_right.'%; border-bottom:none; border-left:0.5px solid #AAAAAA; border-right:0.5px solid #AAAAAA;">';
			$totals_html .= store_number_format($tot_tax_amt);
			$totals_html .= '</td>';
			$totals_html .= '</tr>';
		}

		if ($change_return_amount > 0) {
			$totals_html .= '<tr>';
			if ($qr_side_cell !== '') {
				$totals_html .= $qr_side_cell;
				$qr_side_cell = '';
			}
			$totals_html .= '<td class="text-left" style="height:22px; width: '.$col_r_left.'%; font-size:15px; border-bottom:none; border-left:0.5px solid #AAAAAA; border-right:0.5px solid #AAAAAA;">Change</td>';
			$totals_html .= '<td class="text-right" style="height:22px; font-size:15px; width: '.$col_r_right.'%; border-bottom:none; border-left:0.5px solid #AAAAAA; border-right:0.5px solid #AAAAAA;">';
			$totals_html .= store_number_format($change_return_amount);
			$totals_html .= '</td>';
			$totals_html .= '</tr>';
		}

		// QR-only row: keep QR codes visible when no summary rows exist (e.g. store TRN empty, no discount)
		if ($qr_side_rows === 0 && $qr_side_inner !== '') {
			$totals_html .= '<tr>';
			$totals_html .= '<td colspan="3" style="border:none;border-bottom:none;padding-top:2px;width:'.$col_left.'%; vertical-align:middle;">'.$qr_side_inner.'</td>';
			$totals_html .= '<td style="width: '.$col_r_left.'%; border-bottom:none; border-left:0.5px solid #AAAAAA; border-right:none;">&nbsp;</td>';
			$totals_html .= '<td style="width: '.$col_r_right.'%; border-bottom:none; border-left:none; border-right:0.5px solid #AAAAAA;">&nbsp;</td>';
			$totals_html .= '</tr>';
		}

		// Final row: Amount in Words + Net Total
		$totals_html .= '<tr>';
		$totals_html .= '<td colspan="3" style="width:'.$col_left.'%;border-top:none;"><div style="font-size:12px;">';
		if (show_number_to_words_sales()) {
			$totals_html .= '<b>'.$this->CI->lang->line("amount_in_words").':</b> '.no_to_words($invoice_net_total);
			$tot_expl = explode('.', store_number_format($invoice_net_total));
			if (!empty($tot_expl[1])) {
				$totals_html .= " and ". no_to_words($tot_expl[1]). " Fills";
			}
		}
		$totals_html .= '</div>';
		$totals_html .= '</td>';

		$totals_html .= '<td class="text-left text-bold" style="width: '.$col_r_left.'%; height:24px; font-size:15px; border-top:none; border-bottom:0.5px solid #AAAAAA; border-left:0.5px solid #AAAAAA; border-right:0.5px solid #AAAAAA;"><nobr>'.$this->_bi('Net Total', 'Ø§Ù„ØµØ§ÙÙŠ').'</nobr></td>';
		$totals_html .= '<td class="text-right text-bold" style="width: '.$col_r_right.'%; height:24px; font-size:15px; border-top:none; border-bottom:0.5px solid #AAAAAA; border-left:0.5px solid #AAAAAA; border-right:0.5px solid #AAAAAA;"><nobr>' . store_number_format($invoice_net_total) . '</nobr></td>';
		$totals_html .= '</tr>';

		$totals_html .= '</tbody>';
		$totals_html .= '</table>';

		$signature_font_size = ($pageWidth < 160) ? 8 : 10;
		$policy_font_size = ($pageWidth < 160) ? 8 : 11;
		$policy_text_size = ($pageWidth < 160) ? 7 : 10;

		$show_invoice_terms = (isset($store->t_and_c_status) && (int)$store->t_and_c_status === 1);
		$invoice_terms_html = '';
		if ($show_invoice_terms && !empty(trim((string)(isset($store->invoice_terms) ? $store->invoice_terms : '')))) {
			$invoice_terms_html = '<span style="font-family:dejavusans;">'.nl2br(html_entity_decode((string)$store->invoice_terms, ENT_QUOTES, 'UTF-8')).'</span>';
		}
		$thank_you_html = nl2br(html_entity_decode((string)$store->sales_invoice_footer_text));
		$center_msg = '';
		if ($invoice_terms_html !== '') {
			$center_msg .= '<div style="font-size:'.$policy_text_size.'px; line-height:1.3;">'.$invoice_terms_html.'</div>';
		}
		$center_msg .= '<div style="font-weight:bold; font-size:'.$policy_font_size.'px; line-height:1.35;">'.$thank_you_html.'</div>';

		// TASK-30: one outer box — Receiver | thank you/terms | Authorised
		$bottom_html = '<style>.totals-table{border:0.5px solid #AAAAAA;} .totals-table td{border:0.5px solid #AAAAAA;} .text-right{text-align:right;} .text-center{text-align:center;} .text-bold{font-weight:bold;}</style>';
		$bottom_html .= isset($totals_html) ? $totals_html : '';
		$bottom_html .= '<div style="font-size:4px;line-height:4px;">&nbsp;</div>';
		$bottom_html .= '<table cellpadding="8" nobr="true" style="width:100%; border:1px solid #000000;"><tbody><tr nobr="true">'
			. '<td width="22%" style="border:none; text-align:center; vertical-align:middle;">'
			. '<span style="font-weight:bold; font-size:'.$signature_font_size.'px;">Receiver\'s Sign</span><br/><br/>'
			. '<span style="color:#AAAAAA;">___________________</span></td>'
			. '<td width="56%" style="border:none; text-align:center; vertical-align:middle;">'.$center_msg.'</td>'
			. '<td width="22%" style="border:none; text-align:center; vertical-align:middle;">'
			. '<span style="font-weight:bold; font-size:'.$signature_font_size.'px;">Authorised Signature</span><br/><br/>'
			. '<span style="color:#AAAAAA;">___________________</span></td>'
			. '</tr></tbody></table>';
		$this->writeHTML($tbl, true, false, true, true, 'j');
		$bottom_h = 68;
		if ($invoice_terms_html !== '') {
			$bottom_h += 8;
		}
		if (!empty($qr_side_items)) {
			$bottom_h += 16;
		}
		// Keep the signature/thank-you block above the generated date and page footer.
        $this->write_bottom_html($bottom_html, $bottom_h + 10);
		return;

		//$this->IncludeJS("print(true);");
		//return $this->Output('invoice_100.pdf', 'I');
			//return $tbl;
	}

	public function _main_body()
	{	
		$sales 		= $this->sales;//array()
		$store 		= $this->store;//array()
		$customer 	= $this->customer;//array()
			
	

		$this->_invoice_name = "Tax Invoice";

		$this->page_title = "Tax Invoice";

		//Don't change this
		$this->_invoice_format = 'GST';

		//$this->_QRCODE = $sales->sales_code;

		// set font
		$this->setFont($this->get_font_name(), 'B', 20);

		// add a page
		$this->AddPage();
		
		
		//invoice title
		$this->_get_invoice_title_name();
		
		// Cusomer Details
		$this->_get_customer_details(); 

		// Cusomer Details
		$this->_get_invoice_details();

		// Shipping Details
		$this->_get_shipping_address(); 

		// Bank Details
		$this->_get_bank_details();
	
		//Set document name (footer -R)
		$this->_set_document_name('Printed on');
		
		//Sey document number (footer -R)
		$this->_set_document_number(date('d-m-Y h:i:s a'));

		//Search Coupon Details
		$coupon_code = $coupon_type = '';
	    $coupon_value=0;
	    if(!empty($sales->coupon_id)){
	      $coupon_details =get_customer_coupon_details($sales->coupon_id);
	      $coupon_code =$coupon_details->code;
	      $coupon_value =$coupon_details->value;
	      $coupon_type =$coupon_details->type;
	    } 
		$this->setFont($this->get_font_name(), '', 8);

		// set cell padding
		$this->setCellPaddings(1, 1, 1, 1);

		// set cell margins
		//$this->setCellMargins(1, 1, 1, 1);

		// set color for background
		$this->setFillColor(255, 255, 255);

		$this->Ln(0);

		

		$this->setFont($this->get_font_name(), '', 8);
		
		
		$tbl = '
		<style type="text/css">
			table {
			    border-collapse: collapse;
			}
			#print, .totals-table, .signatures-table {
			    border: 0.5px solid #AAAAAA;
			}
			th {
			    border: 0.5px solid #AAAAAA;
			}
			.totals-table td {
			    border: 0.5px solid #AAAAAA;
			}
			table + table, table + table tr:first-child th, table + table tr:first-child td {
			    border-top: 0;
			}
			.text-right{
				text-align: right;
			}
			.text-center{
				text-align: center;
			}
			.text-bold{
				font-weight: bold;
			}
			.bg-light-blue{
				background-color: #e4eaff;
			}
			#print tbody td {
				border-top: none;
				border-bottom: none;
			}
		</style>
		<table id="print" >';

		$pageWidth = $this->getPageWidth();
		$item_font_size = ($pageWidth < 160) ? 8 : 12;
		$with_part_number = !empty($this->with_part_number);
		// Show VAT column + Taxable/VAT summary when store TRN enabled (TAX INVOICE)
		$show_tax = is_branch_tax_enabled(!empty($store->id) ? $store->id : '');
		$show_vat_summary = $show_tax;
		// Hide Dis column + SubTotal/Discount when invoice discount is 0
		$is_pos_sale_preview = (isset($sales->pos) && (int) $sales->pos === 1);
		$item_disc_row = $this->CI->db->select_sum('discount_amt')->where('sales_id', $this->sales_id)->get('db_salesitems')->row();
		$item_disc_sum = (float) (isset($item_disc_row->discount_amt) ? $item_disc_row->discount_amt : 0);
		$preview_invoice_discount = $is_pos_sale_preview
			? (float) (isset($sales->tot_discount_to_all_amt) ? $sales->tot_discount_to_all_amt : 0)
			: $item_disc_sum + (float) (isset($sales->tot_discount_to_all_amt) ? $sales->tot_discount_to_all_amt : 0) + (float) (isset($sales->coupon_amt) ? $sales->coupon_amt : 0);
		$show_discount = ($preview_invoice_discount > 0);
		$description_label = (get_item_barcode_label_key() === 'barcode_no')
			? $this->_bi('Item Name', 'Item Name')
			: $this->_bi('Part Name', 'Part Name');
		$barcode_label = (get_item_barcode_label_key() === 'barcode_no')
			? $this->_bi('Barcode No', 'Barcode No')
			: $this->_bi('Part No', 'Part No');
		if ($pageWidth < 160) {
			$widthArray = array(
				'sl_no' 		=> '8',
				'barcode' 		=> '11',
				'description' 	=> '20',
				'unit' 			=> '8',
				'qty' 			=> '9',
				'rate' 	        => '11',
				'dis'           => '10',
				'tax'           => '10',
				'amount' 		=> '13',
			);
		} else {
			$widthArray = array(
				'sl_no' 		=> '8',
				'barcode' 		=> '12',
				'description' 	=> '24',
				'unit' 			=> '8',
				'qty' 			=> '9',
				'rate' 	        => '10',
				'dis'           => '8',
				'tax'           => '10',
				'amount' 		=> '11',
			);
		}

		// Hide optional columns â€” freed width goes to Unit/Qty/Price/Amount (keep Part Name compact)
		if (!$with_part_number) {
			$freed = (float) $widthArray['barcode'];
			unset($widthArray['barcode']);
			$this->_give_col_width($widthArray, $freed);
		}

		if (!$show_tax) {
			$freed = (float) $widthArray['tax'];
			unset($widthArray['tax']);
			$this->_give_col_width($widthArray, $freed);
		}

		if (!$show_discount) {
			$freed = (float) $widthArray['dis'];
			unset($widthArray['dis']);
			$this->_give_col_width($widthArray, $freed);
		}

			//Sum the value
			$sumOfWidth = 0;
			$colW =array();
			
			foreach($widthArray as $key => $val){
				
				//Update value
				$colWidthSize[$key] = $val;

				//New Array => Reasssign % symbol
				$colW[$key] = $val.'%';

				//Sum of value
				$sumOfWidth+=$val;
			}

			
			
		    $tbl .='<thead>
		        <tr class="bg-light-blue text-bold" style="width: 100%;font-size:'.$item_font_size.'px;">
			        <th colspan="1" style="text-align:center;width: '.$colW['sl_no'].'">'.$this->_bi('S/N', 'Ù…Ø³Ù„Ø³Ù„').'</th>';
			if ($with_part_number) {
				$tbl .='<th colspan="1" class="text-center" style="width: '.$colW['barcode'].'">'.$barcode_label.'</th>';
			}
			$tbl .='
			        <th colspan="1" class="text-center" style="width: '.$colW['description'].'" >'.$description_label.'</th>
			        <th colspan="1" class="text-center" style="width: '.$colW['unit'].'">'.$this->_bi('Unit', 'ÙˆØ­Ø¯Ø©').'</th>
			        <th colspan="1" style="text-align:center;width: '.$colW['qty'].'">'.$this->_bi('Qty', 'Ø§Ù„ÙƒÙ…ÙŠØ©').'</th>
			        <th colspan="1" style="text-align:right;width: '.$colW['rate'].'">'.$this->_bi('Price', 'Ø§Ù„Ø³Ø¹Ø±').'</th>';
			if ($show_discount) {
				$tbl .='<th colspan="1" style="text-align:right;width: '.$colW['dis'].'">'.$this->_bi('Dis', 'Ø®ØµÙ…').'</th>';
			}
				if ($show_tax) {
				$tbl .='<th colspan="1" style="text-align:right;width: '.$colW['tax'].'">'.$this->_bi('VAT Amt', 'Ø§Ù„Ø¶Ø±ÙŠØ¨Ø©').'</th>';
				}
				$tbl .='
			        <th colspan="1" style="text-align:right;width: '.$colW['amount'].'">'.$this->_bi('Amount', 'Ø§Ù„Ù…Ø¨Ù„Øº').'</th>
		        </tr>
		    </thead>
		    <tbody>';
		        
		      $i=1;
              $tot_qty=0;
              $tot_sales_price=0;
              $tot_tax_amt=0;
              $tot_discount_amt=0;
              $tot_unit_total_cost=0;
              $tot_total_cost=0;
              $tot_before_tax=0;
              
              $tot_price_per_unit=0;
              $sum_of_tot_price=0;
              $sub_total = 0;

          

              $this->CI->db->select(" a.description,c.item_name,c.item_code,c.custom_barcode, a.sales_qty,a.tax_type,
                                  a.price_per_unit, b.tax,b.tax_name,a.tax_amt,
                                  a.discount_input,a.discount_amt, a.unit_total_cost,
                                  a.total_cost, d.unit_name,c.sku,c.hsn
                              ");
              $this->CI->db->where("a.sales_id",$this->sales_id);
              $this->CI->db->from("db_salesitems a");
              $this->CI->db->join("db_tax b","b.id=a.tax_id","left");
              $this->CI->db->join("db_items c","c.id=a.item_id","left");
              $this->CI->db->join("db_units d","d.id = c.unit_id","left");

              $q2=$this->CI->db->get();
              
            //   print_r(count($q2));
            //   print_r($q2->result());

		$items_rows = $q2->result();
		$is_pos_sale = (isset($sales->pos) && (int) $sales->pos === 1);
		$invoice_level_discount = (float) (isset($sales->tot_discount_to_all_amt) ? $sales->tot_discount_to_all_amt : 0)
			+ (float) (isset($sales->coupon_amt) ? $sales->coupon_amt : 0);
		if ($is_pos_sale) {
			$invoice_level_discount = (float) (isset($sales->tot_discount_to_all_amt) ? $sales->tot_discount_to_all_amt : 0);
		}
		$line_bases = array();
		$total_base_for_disc = 0.0;
		foreach ($items_rows as $idx => $res2) {
			$qty = (float) $res2->sales_qty;
			if ($res2->tax_type == 'Inclusive') {
				$base = ((float) $res2->price_per_unit * $qty) - (float) $res2->tax_amt;
			} else {
				$base = (float) $res2->price_per_unit * $qty;
			}
			$line_bases[$idx] = max(0, $base);
			$total_base_for_disc += $line_bases[$idx];
		}
		$distributed = array();
		$distributed_sum = 0.0;
		$item_count = count($items_rows);
		foreach ($items_rows as $idx => $res2) {
			$share = ($total_base_for_disc > 0 && $invoice_level_discount > 0)
				? (($line_bases[$idx] / $total_base_for_disc) * $invoice_level_discount)
				: 0.0;
			$distributed[$idx] = $share;
			$distributed_sum += $share;
		}
		if ($item_count > 0 && $invoice_level_discount > 0) {
			$distributed[$item_count - 1] += ($invoice_level_discount - $distributed_sum);
		}

		        foreach ($items_rows as $idx => $res2) {
		          // print_r($res2);
				  $barcode_no = (!empty($res2->custom_barcode)) ? $res2->custom_barcode : $res2->item_code;
                  $discount = (empty($res2->discount_input)||$res2->discount_input==0)? store_number_format(0):store_number_format($res2->discount_input)."%";
                  $line_discount_amt = $is_pos_sale
				? (float) $distributed[$idx]
				: ((float) $res2->discount_amt + (float) $distributed[$idx]);
                  $discount_amt = $line_discount_amt;
                  $before_tax=$res2->price_per_unit;// * $res2->sales_qty;
                  $tot_cost_before_tax=$res2->total_cost;//$before_tax * $res2->sales_qty;

                 $tax_type = ($res2->tax_type=='Exclusive') ? 'Exc.' : 'Inc.';
                  $tbl .='<tr style="" nobr="true" style="width: 100%;">';
				      $tbl .='<td colspan="1" style="text-align:center;width: '.$colW['sl_no'].';font-size:'.$item_font_size.'px;border-left:0.5px solid #AAAAAA;border-right:0.5px solid #AAAAAA;border-top:none;border-bottom:none;">'.$i++.'</td>';
				      if ($with_part_number) {
				      $tbl .='<td colspan="1" style="text-align:left;width: '.$colW['barcode'].';font-size:'.$item_font_size.'px;border-left:0.5px solid #AAAAAA;border-right:0.5px solid #AAAAAA;border-top:none;border-bottom:none;">'.$barcode_no.'</td>';
				      }
				      $tbl .= '<td colspan="1" style="width: '.$colW['description'].';font-size:'.$item_font_size.'px;border-left:0.5px solid #AAAAAA;border-right:0.5px solid #AAAAAA;border-top:none;border-bottom:none;" >';
				      $tbl .= '<nobr>' . $res2->item_name . '</nobr>';
				      $tbl .= (!empty($res2->description)) ? "<br><i>[".nl2br($res2->description)."]</i>" : '';
				      $tbl .= '</td>';
				      $tbl .='<td colspan="1" style="width: '.$colW['unit'].';border-left:0.5px solid #AAAAAA;border-right:0.5px solid #AAAAAA;border-top:none;border-bottom:none;">'.$res2->unit_name.'</td>';
				      
				      $tbl .='<td colspan="1" style="text-align:center;width: '.$colW['qty'].';font-size:'.$item_font_size.'px;border-left:0.5px solid #AAAAAA;border-right:0.5px solid #AAAAAA;border-top:none;border-bottom:none;">'.format_qty($res2->sales_qty).'</td>';

				      $tbl .='<td colspan="1" style="text-align:right;width: '.$colW['rate'].';font-size:'.$item_font_size.'px;border-left:0.5px solid #AAAAAA;border-right:0.5px solid #AAAAAA;border-top:none;border-bottom:none;"><nobr>'.store_number_format($res2->price_per_unit).'</nobr></td>';
			if ($show_discount) {
				      $tbl .='<td colspan="1" style="text-align:right;width: '.$colW['dis'].';font-size:'.$item_font_size.'px;border-left:0.5px solid #AAAAAA;border-right:0.5px solid #AAAAAA;border-top:none;border-bottom:none;"><nobr>'.store_number_format($line_discount_amt).'</nobr></td>';
			}
			if ($show_tax) {
			      $tbl .='<td colspan="1" style="text-align:right;width: '.$colW['tax'].';font-size:'.$item_font_size.'px;border-left:0.5px solid #AAAAAA;border-right:0.5px solid #AAAAAA;border-top:none;border-bottom:none;"><nobr>'.store_number_format($res2->tax_amt).'</nobr></td>';
			}
			      $tbl .='<td colspan="1" style="text-align:right;width: '.$colW['amount'].';font-size:'.$item_font_size.'px;border-left:0.5px solid #AAAAAA;border-right:0.5px solid #AAAAAA;border-top:none;border-bottom:none;"><nobr>'.(store_number_format($res2->total_cost)).'</nobr></td>';
				      
		          $tbl .='</tr>';

                  $tot_qty +=$res2->sales_qty;
                  $tot_sales_price +=$res2->price_per_unit;
                  $tot_tax_amt +=$res2->tax_amt;
                  $tot_discount_amt +=$res2->discount_amt;
                  $tot_unit_total_cost +=$res2->unit_total_cost;
                  $tot_before_tax +=$before_tax;
                  $tot_total_cost +=$tot_cost_before_tax;
			if ($res2->tax_type == 'Inclusive') {
				$unit_price_before_tax = $res2->price_per_unit - ($res2->tax_amt / ($res2->sales_qty > 0 ? $res2->sales_qty : 1));
				$sub_total += $unit_price_before_tax * $res2->sales_qty;
			} else {
				$sub_total += $res2->price_per_unit * $res2->sales_qty;
			}
		}

		$mCount = count($q2->result());

		// Fill remaining page space with empty rows so Total sits just above Taxable block
		$pageH = (float) $this->getPageHeight();
		$currY = (float) $this->GetY();
		$bottom_reserve = ($pageWidth < 160) ? 82 : 92; // room for totals/QR/thank you + footer
		$avail_mm = max(40, $pageH - $currY - $bottom_reserve);
		$row_h_mm = ($pageWidth < 160) ? 3.4 : 3.5;
		$target_rows = max($mCount, (int) floor($avail_mm / $row_h_mm));
		$max_rows = ($pageWidth < 160) ? 23 : 35;
		$target_rows = min($target_rows, $max_rows);
		$empty_needed = max(0, $target_rows - $mCount);
		$cell_v = 'border-left:0.5px solid #AAAAAA;border-right:0.5px solid #AAAAAA;border-top:none;border-bottom:none;';
		$empty_pad = '<br/>';
		for ($er = 0; $er < $empty_needed; $er++) {
			$tbl .= '<tr nobr="true">';
			$tbl .= '<td style="width: ' . $colW['sl_no'] . ';' . $cell_v . '">'.$empty_pad.'</td>';
			if ($with_part_number) {
				$tbl .= '<td style="width: ' . $colW['barcode'] . ';' . $cell_v . '">'.$empty_pad.'</td>';
			}
			$tbl .= '<td style="width: ' . $colW['description'] . ';' . $cell_v . '">'.$empty_pad.'</td>';
			$tbl .= '<td style="width: ' . $colW['unit'] . ';' . $cell_v . '">'.$empty_pad.'</td>';
			$tbl .= '<td style="width: ' . $colW['qty'] . ';' . $cell_v . '">'.$empty_pad.'</td>';
			$tbl .= '<td style="width: ' . $colW['rate'] . ';' . $cell_v . '">'.$empty_pad.'</td>';
			if ($show_discount) {
				$tbl .= '<td style="width: ' . $colW['dis'] . ';' . $cell_v . '">'.$empty_pad.'</td>';
			}
			if ($show_tax) {
				$tbl .= '<td style="width: ' . $colW['tax'] . ';' . $cell_v . '">'.$empty_pad.'</td>';
			}
			$tbl .= '<td style="width: ' . $colW['amount'] . ';' . $cell_v . '">'.$empty_pad.'</td>';
			$tbl .= '</tr>';
		}

		$col_before_qty_width = ($colWidthSize['sl_no'] + ($with_part_number ? (float)$colWidthSize['barcode'] : 0) + (float)$colWidthSize['description'] + (float)$colWidthSize['unit']) . '%';
		$tot_label_colspan = $with_part_number ? 4 : 3;
		$tbl .= '<tr nobr="true" class="text-bold" style="font-size:' . $item_font_size . 'px;">';
		$tbl .= '<td colspan="' . $tot_label_colspan . '" class="text-right text-bold" style="width: ' . $col_before_qty_width . ';border-top:0.5px solid #AAAAAA;border-bottom:0.5px solid #AAAAAA;border-left:0.5px solid #AAAAAA;border-right:0.5px solid #AAAAAA;font-weight:bold;">' . $this->_bi('Total', 'Ø§Ù„Ø¥Ø¬Ù…Ø§Ù„ÙŠ') . '&nbsp;</td>';
		$tbl .= '<td colspan="1" class="text-center text-bold" style="width: ' . $colW['qty'] . ';border-top:0.5px solid #AAAAAA;border-bottom:0.5px solid #AAAAAA;border-left:0.5px solid #AAAAAA;border-right:0.5px solid #AAAAAA;font-weight:bold;">' . format_qty($tot_qty) . '</td>';
		$tbl .= '<td colspan="1" style="width: ' . $colW['rate'] . ';border-top:0.5px solid #AAAAAA;border-bottom:0.5px solid #AAAAAA;border-left:0.5px solid #AAAAAA;border-right:0.5px solid #AAAAAA;">&nbsp;</td>';
		if ($show_discount) {
			$tbl .= '<td colspan="1" style="width: ' . $colW['dis'] . ';border-top:0.5px solid #AAAAAA;border-bottom:0.5px solid #AAAAAA;border-left:0.5px solid #AAAAAA;border-right:0.5px solid #AAAAAA;">&nbsp;</td>';
		}
		if ($show_tax) {
		$tbl .= '<td colspan="1" style="width: ' . $colW['tax'] . ';border-top:0.5px solid #AAAAAA;border-bottom:0.5px solid #AAAAAA;border-left:0.5px solid #AAAAAA;border-right:0.5px solid #AAAAAA;">&nbsp;</td>';
		}
		$tbl .= '<td colspan="1" style="width: ' . $colW['amount'] . ';border-top:0.5px solid #AAAAAA;border-bottom:0.5px solid #AAAAAA;border-left:0.5px solid #AAAAAA;border-right:0.5px solid #AAAAAA;">&nbsp;</td>';
		$tbl .= '</tr>';

		$tbl .= '</tbody>
		    
		</table>
		';

		// POS stores the complete discount (item + invoice discount) in
		// tot_discount_to_all_amt, so adding item discounts again duplicates them.
		$invoice_discount_amt = (isset($sales->pos) && (int) $sales->pos === 1)
			? (float) $sales->tot_discount_to_all_amt
			: (float) $tot_discount_amt + (isset($sales->tot_discount_to_all_amt) ? (float) $sales->tot_discount_to_all_amt : 0) + (isset($sales->coupon_amt) ? (float) $sales->coupon_amt : 0);
		$show_discount = ($invoice_discount_amt > 0);

		$calc_subtotal = (float) $sub_total;
		$taxable_base = max(0, $calc_subtotal - $invoice_discount_amt);

		if ($calc_subtotal > 0 && $invoice_discount_amt > 0) {
			$discount_ratio = $taxable_base / $calc_subtotal;
			$tot_tax_amt = 0;
			foreach ($q2->result() as $r_tax) {
				$r_qty = (float) $r_tax->sales_qty;
				$r_rate = (float) $r_tax->price_per_unit;
				$r_tax_pct = (float) $r_tax->tax;
				if ($r_tax->tax_type == 'Inclusive') {
					$r_base = ($r_rate * $r_qty - (float) $r_tax->tax_amt);
				} else {
					$r_base = ($r_rate * $r_qty);
				}
				$r_discounted_base = $r_base * $discount_ratio;
				$tot_tax_amt += ($r_discounted_base * ($r_tax_pct / 100));
			}
		}

		if (!$show_vat_summary) {
			$tot_tax_amt = 0;
		}

		$vat_label = 'VAT';
		if ($show_vat_summary && isset($q2) && $q2->num_rows() > 0) {
			$vat_pct = null;
			$mixed_vat = false;
			foreach ($q2->result() as $r_vat) {
				$pct = (float) $r_vat->tax;
				if ($vat_pct === null) {
					$vat_pct = $pct;
				} elseif (abs($vat_pct - $pct) > 0.0001) {
					$mixed_vat = true;
					break;
				}
			}
			if (!$mixed_vat && $vat_pct !== null) {
				$vat_label = 'VAT ' . rtrim(rtrim(number_format($vat_pct, 2, '.', ''), '0'), '.') . '%';
			}
		}

		$other_charges = isset($sales->other_charges_amt) ? (float) $sales->other_charges_amt : 0;
		$invoice_net_total = round_off_amount($taxable_base + $tot_tax_amt + $other_charges);
		$change_return_amount = (float) get_change_return_amount($sales->id);

		// TASK-30: QR codes (left of totals) — generate before summary table
		$show_web_qr = ((int) (isset($store->show_website_qr) ? $store->show_website_qr : 0) === 1);
		$show_loc_qr = ((int) (isset($store->show_location_qr) ? $store->show_location_qr : 0) === 1);
		$show_wa_qr  = ((int) (isset($store->show_whatsapp_qr) ? $store->show_whatsapp_qr : 0) === 1);
		$show_inv_qr = ((int) (isset($store->show_invoice_qr) ? $store->show_invoice_qr : 0) === 1);
		$show_za_qr  = ((int) (isset($store->show_zatca_qr) ? $store->show_zatca_qr : 0) === 1);

		if (file_exists(APPPATH . 'libraries/tcpdf/tcpdf_barcodes_2d.php') && !class_exists('TCPDF2DBarcode', false)) {
			require_once APPPATH . 'libraries/tcpdf/tcpdf_barcodes_2d.php';
		}

		$web_qr_img = '';
		if ($show_web_qr) {
			$web_url = !empty($store->website) ? (preg_match('/^https?:\/\//i', $store->website) ? $store->website : 'https://' . $store->website) : 'https://www.sap.com/';
			try {
				$b = new TCPDF2DBarcode($web_url, 'QRCODE,M');
				$png = $b->getBarcodePngData(8, 8);
				if (!empty($png)) { $web_qr_img = '@' . base64_encode($png); }
			} catch (Exception $e) {}
		}

		$loc_qr_img = '';
		if ($show_loc_qr) {
			$loc_url = get_store_location_url_for_qr($store);
			if (!empty($loc_url)) {
				try {
					$b = new TCPDF2DBarcode($loc_url, 'QRCODE,M');
					$png = $b->getBarcodePngData(8, 8);
					if (!empty($png)) { $loc_qr_img = '@' . base64_encode($png); }
				} catch (Exception $e) {}
			}
		}

		$wa_qr_img = '';
		if ($show_wa_qr) {
			if (!empty($store->qr_image) && file_exists($store->qr_image)) {
				$wa_qr_img = base_url($store->qr_image);
			} else {
				$wa_src = !empty($store->whatsapp_number) ? $store->whatsapp_number : (!empty($store->mobile) ? $store->mobile : '');
				$wa_no = normalize_whatsapp_number($wa_src);
				if (!empty($wa_no)) {
					try {
						$b = new TCPDF2DBarcode('https://wa.me/' . $wa_no, 'QRCODE,M');
						$png = $b->getBarcodePngData(8, 8);
						if (!empty($png)) { $wa_qr_img = '@' . base64_encode($png); }
					} catch (Exception $e) {}
				}
			}
		}

		$inv_qr_img = '';
		if ($show_inv_qr) {
			$salt = $this->CI->config->item('encryption_key');
			if (empty($salt)) { $salt = 'retail_app_secret_salt'; }
			$token = md5($sales->id . $salt);
			$inv_dl_url = base_url("pdf/sales_public/{$sales->id}/{$token}");
			try {
				$b = new TCPDF2DBarcode($inv_dl_url, 'QRCODE,M');
				$png = $b->getBarcodePngData(8, 8);
				if (!empty($png)) { $inv_qr_img = '@' . base64_encode($png); }
			} catch (Exception $e) {}
		}

		$zatca_qr_img = '';
		if ($show_za_qr) {
			$inv_datetime = (!empty($sales->sales_date) ? $sales->sales_date : date('Y-m-d')) . ' ' . (!empty($sales->created_time) ? $sales->created_time : '00:00:00');
			$zatca_vat_no = !empty($store->vat_no) ? $store->vat_no : (!empty($store->gst_no) ? $store->gst_no : '');
			$zatca_qr_img = get_zatca_qr_code_image($store->store_name, $zatca_vat_no, $inv_datetime, $sales->grand_total, $tot_tax_amt);
		}

		$paid_seal_img = '';
		$show_paid_seal = should_show_paid_seal(!empty($sales->payment_status) ? $sales->payment_status : null);
		if ($show_paid_seal) {
			$paid_seal_img = get_paid_seal_img_src();
			if ($paid_seal_img === '') {
				$show_paid_seal = false;
			}
		}

		// Side QR strip (Location / WhatsApp / Invoice — replaces bank details)
		$side_qr_size = ($pageWidth < 160) ? 52 : 72;
		$side_qr_label = ($pageWidth < 160) ? 8 : 9;
		$qr_side_items = array();
		if ($show_loc_qr && !empty($loc_qr_img)) { $qr_side_items[] = array($loc_qr_img, 'Location'); }
		if ($show_wa_qr && !empty($wa_qr_img)) { $qr_side_items[] = array($wa_qr_img, 'WhatsApp'); }
		if ($show_inv_qr && !empty($inv_qr_img)) { $qr_side_items[] = array($inv_qr_img, 'Invoice'); }
		if ($show_web_qr && !empty($web_qr_img)) { $qr_side_items[] = array($web_qr_img, 'Website'); }
		if ($show_za_qr && !empty($zatca_qr_img)) { $qr_side_items[] = array($zatca_qr_img, 'ZATCA'); }
		if ($show_paid_seal && !empty($paid_seal_img)) { $qr_side_items[] = array($paid_seal_img, 'Paid'); }

		$qr_side_inner = '';
        if (!empty($qr_side_items)) {
            $qr_count = count($qr_side_items);
            $qr_strip_mm = ($pageWidth - 12) * (($pageWidth < 160) ? 0.52 : 0.72);
            $qr_cell_px = ($qr_strip_mm - 4) / $this->pixelsToUnits(1) / $qr_count;
            $side_qr_size = min($side_qr_size, max(12, (int)floor($qr_cell_px - 8)));
            $qr_cell_width = 100 / $qr_count;
            $qr_side_inner = '<table border="0" cellpadding="2" cellspacing="0" style="width:100%;"><tr>';
			foreach ($qr_side_items as $qr_index => $qi) {
				$qr_side_inner .= '<td width="'.$qr_cell_width.'%" align="center" style="text-align:center; vertical-align:middle;">'
					. '<img src="'.$qi[0].'" width="'.$side_qr_size.'" height="'.$side_qr_size.'" />'
					. '<br/><span style="font-size:'.$side_qr_label.'px;font-weight:bold;">'.$qi[1].'</span>'
					. '</td>';
			}
			$qr_side_inner .= '</tr></table>';
		}

		$summary_gap = ($pageWidth < 160) ? 2 : 4;
		$totals_html = '<div style="font-size:'.$summary_gap.'px;line-height:'.$summary_gap.'px;">&nbsp;</div>';
		$totals_html .= '<table class="totals-table">';
		$totals_html .= '<tbody>';

		$col_left = ($pageWidth < 160) ? 52 : 72;
		$col_right = 100 - $col_left;
		$col_r_left = $col_right * (($pageWidth < 160) ? 0.40 : 0.48);
		$col_r_right = $col_right - $col_r_left;

		// TASK-30: QR strip spans summary rows (bank details removed)
		$qr_side_rows = 0;
		if ($show_tax || $show_discount) {
			$qr_side_rows++; // Subtotal
		}
		if ($show_discount) {
			$qr_side_rows++; // Discount
		}
		if ($show_vat_summary) {
			$qr_side_rows += 2;
		}
		if ($change_return_amount > 0) {
			$qr_side_rows++;
		}
		$qr_side_cell = '';
		if ($qr_side_rows > 0) {
			$qr_side_cell = '<td rowspan="'.$qr_side_rows.'" colspan="3" style="border:none;border-bottom:none;padding-top:2px;width:'.$col_left.'%; vertical-align:middle;">'
				. $qr_side_inner
				. '</td>';
		}

		if ($show_tax || $show_discount) {
			$totals_html .= '<tr>';
			$totals_html .= $qr_side_cell;
			$qr_side_cell = '';
			$totals_html .= '<td class="text-left" style="height:22px; width: '.$col_r_left.'%; font-size:13px; border-bottom:0.5px solid #AAAAAA; border-left:0.5px solid #AAAAAA; border-right:0.5px solid #AAAAAA;">'.$this->_bi('Subtotal', 'Ø§Ù„Ù…Ø¬Ù…ÙˆØ¹ Ø§Ù„ÙØ±Ø¹ÙŠ').'</td>';
			$totals_html .= '<td style="height:22px; font-size:13px; width: '.$col_r_right.'%; border-bottom:0.5px solid #AAAAAA; border-left:0.5px solid #AAAAAA; border-right:0.5px solid #AAAAAA;" class="text-right">';
			$totals_html .= store_number_format($calc_subtotal);
			$totals_html .= '</td>';
			$totals_html .= '</tr>';

		}

		if ($show_discount) {
			$discount_border_bottom = $show_vat_summary || $change_return_amount > 0 ? '0.5px solid #AAAAAA' : 'none';
			$totals_html .= '<tr>';
			$totals_html .= '<td class="text-left" style="height:22px; width: '.$col_r_left.'%; font-size:13px; border-bottom:'.$discount_border_bottom.'; border-left:0.5px solid #AAAAAA; border-right:0.5px solid #AAAAAA;">'.$this->_bi('Discount', 'Ø§Ù„Ø®ØµÙ…').'</td>';
			$totals_html .= '<td class="text-right" style="height:22px; font-size:13px; width: '.$col_r_right.'%; border-bottom:'.$discount_border_bottom.'; border-left:0.5px solid #AAAAAA; border-right:0.5px solid #AAAAAA;">';
			$totals_html .= store_number_format($invoice_discount_amt);
			$totals_html .= '</td>';
			$totals_html .= '</tr>';
		}

		if ($show_vat_summary) {
			$totals_html .= '<tr>';
			if ($qr_side_cell !== '') {
				$totals_html .= $qr_side_cell;
				$qr_side_cell = '';
			}
			$totals_html .= '<td class="text-left" style="height:22px; width: '.$col_r_left.'%; font-size:13px; border-bottom:0.5px solid #AAAAAA; border-left:0.5px solid #AAAAAA; border-right:0.5px solid #AAAAAA;">'.$this->_bi('Taxable', 'Ø§Ù„Ø®Ø§Ø¶Ø¹ Ù„Ù„Ø¶Ø±ÙŠØ¨Ø©').'</td>';
			$totals_html .= '<td class="text-right" style="height:22px; font-size:13px; width: '.$col_r_right.'%; border-bottom:0.5px solid #AAAAAA; border-left:0.5px solid #AAAAAA; border-right:0.5px solid #AAAAAA;">';
			$totals_html .= store_number_format($taxable_base);
			$totals_html .= '</td>';
			$totals_html .= '</tr>';
			$totals_html .= '<tr>';
			$totals_html .= '<td class="text-left" style="height:22px; width: '.$col_r_left.'%; font-size:13px; border-bottom:none; border-left:0.5px solid #AAAAAA; border-right:0.5px solid #AAAAAA;"><nobr>'.$vat_label.'</nobr></td>';
			$totals_html .= '<td class="text-right" style="height:22px; font-size:13px; width: '.$col_r_right.'%; border-bottom:none; border-left:0.5px solid #AAAAAA; border-right:0.5px solid #AAAAAA;">';
			$totals_html .= store_number_format($tot_tax_amt);
			$totals_html .= '</td>';
			$totals_html .= '</tr>';
		}

		if ($change_return_amount > 0) {
			$totals_html .= '<tr>';
			if ($qr_side_cell !== '') {
				$totals_html .= $qr_side_cell;
				$qr_side_cell = '';
			}
			$totals_html .= '<td class="text-left" style="height:22px; width: '.$col_r_left.'%; font-size:13px; border-bottom:none; border-left:0.5px solid #AAAAAA; border-right:0.5px solid #AAAAAA;">Change</td>';
			$totals_html .= '<td class="text-right" style="height:22px; font-size:13px; width: '.$col_r_right.'%; border-bottom:none; border-left:0.5px solid #AAAAAA; border-right:0.5px solid #AAAAAA;">';
			$totals_html .= store_number_format($change_return_amount);
			$totals_html .= '</td>';
			$totals_html .= '</tr>';
		}

		// QR-only row: keep QR codes visible when no summary rows exist (e.g. store TRN empty, no discount)
		if ($qr_side_rows === 0 && $qr_side_inner !== '') {
			$totals_html .= '<tr>';
			$totals_html .= '<td colspan="3" style="border:none;border-bottom:none;padding-top:2px;width:'.$col_left.'%; vertical-align:middle;">'.$qr_side_inner.'</td>';
			$totals_html .= '<td style="width: '.$col_r_left.'%; border-bottom:none; border-left:0.5px solid #AAAAAA; border-right:none;">&nbsp;</td>';
			$totals_html .= '<td style="width: '.$col_r_right.'%; border-bottom:none; border-left:none; border-right:0.5px solid #AAAAAA;">&nbsp;</td>';
			$totals_html .= '</tr>';
		}

		// Final row: Amount in Words + Net Total
		$totals_html .= '<tr>';
		$totals_html .= '<td colspan="3" style="width:'.$col_left.'%;border-top:none;"><div style="font-size:12px;">';
		if (show_number_to_words_sales()) {
			$totals_html .= '<b>'.$this->CI->lang->line("amount_in_words").':</b> '.no_to_words($invoice_net_total);
			$tot_expl = explode('.', store_number_format($invoice_net_total));
			if (!empty($tot_expl[1])) {
				$totals_html .= " and ". no_to_words($tot_expl[1]). " Fills";
			}
		}
		$totals_html .= '</div>';
		$totals_html .= '</td>';

		$totals_html .= '<td class="text-left text-bold" style="width: '.$col_r_left.'%; height:24px; font-size:15px; border-top:none; border-bottom:0.5px solid #AAAAAA; border-left:0.5px solid #AAAAAA; border-right:0.5px solid #AAAAAA;"><nobr>'.$this->_bi('Net Total', 'Ø§Ù„ØµØ§ÙÙŠ').'</nobr></td>';
		$totals_html .= '<td class="text-right text-bold" style="width: '.$col_r_right.'%; height:24px; font-size:15px; border-top:none; border-bottom:0.5px solid #AAAAAA; border-left:0.5px solid #AAAAAA; border-right:0.5px solid #AAAAAA;"><nobr>' . store_number_format($invoice_net_total) . '</nobr></td>';
		$totals_html .= '</tr>';

		$totals_html .= '</tbody>';
		$totals_html .= '</table>';

		$signature_font_size = ($pageWidth < 160) ? 8 : 10;
		$policy_font_size = ($pageWidth < 160) ? 8 : 11;
		$policy_text_size = ($pageWidth < 160) ? 7 : 10;

		$show_invoice_terms = (isset($store->t_and_c_status) && (int)$store->t_and_c_status === 1);
		$invoice_terms_html = '';
		if ($show_invoice_terms && !empty(trim((string)(isset($store->invoice_terms) ? $store->invoice_terms : '')))) {
			$invoice_terms_html = '<span style="font-family:dejavusans;">'.nl2br(html_entity_decode((string)$store->invoice_terms, ENT_QUOTES, 'UTF-8')).'</span>';
		}
		$thank_you_html = nl2br(html_entity_decode((string)$store->sales_invoice_footer_text));
		$center_msg = '';
		if ($invoice_terms_html !== '') {
			$center_msg .= '<div style="font-size:'.$policy_text_size.'px; line-height:1.3;">'.$invoice_terms_html.'</div>';
		}
		$center_msg .= '<div style="font-weight:bold; font-size:'.$policy_font_size.'px; line-height:1.35;">'.$thank_you_html.'</div>';

		// TASK-30: one outer box — Receiver | thank you/terms | Authorised
		$bottom_html = '<style>.totals-table{border:0.5px solid #AAAAAA;} .totals-table td{border:0.5px solid #AAAAAA;} .text-right{text-align:right;} .text-center{text-align:center;} .text-bold{font-weight:bold;}</style>';
		$bottom_html .= isset($totals_html) ? $totals_html : '';
		$bottom_html .= '<div style="font-size:4px;line-height:4px;">&nbsp;</div>';
		$bottom_html .= '<table cellpadding="8" nobr="true" style="width:100%; border:1px solid #000000;"><tbody><tr nobr="true">'
			. '<td width="22%" style="border:none; text-align:center; vertical-align:middle;">'
			. '<span style="font-weight:bold; font-size:'.$signature_font_size.'px;">Receiver\'s Sign</span><br/><br/>'
			. '<span style="color:#AAAAAA;">___________________</span></td>'
			. '<td width="56%" style="border:none; text-align:center; vertical-align:middle;">'.$center_msg.'</td>'
			. '<td width="22%" style="border:none; text-align:center; vertical-align:middle;">'
			. '<span style="font-weight:bold; font-size:'.$signature_font_size.'px;">Authorised Signature</span><br/><br/>'
			. '<span style="color:#AAAAAA;">___________________</span></td>'
			. '</tr></tbody></table>';
		$this->writeHTMLCell('', '', $x ='', $y='', $tbl, 0, 1, 1, true, 'J', true);
		$bottom_h = ($pageWidth < 160) ? 58 : 68;
		if ($invoice_terms_html !== '') {
			$bottom_h += ($pageWidth < 160) ? 6 : 8;
		}
		if (!empty($qr_side_items)) {
			$bottom_h += 16;
		}
		// Keep the signature/thank-you block above the generated date and page footer.
        $this->write_bottom_html($bottom_html, $bottom_h + 10);
		$this->IncludeJS("print(true);");
		$this->Output('invoice_100.pdf', 'I');
	}


}
