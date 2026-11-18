<?php

include('MyPDF.php');

class Sales_return extends MyPDF{

	protected $return_id = null;

	protected $customer = array();

	protected $sales_return = array();

	public function __construct(array $param=array())
	{
		$this->return_id = isset($param['return_id']) ? $param['return_id'] : null;

		parent::__construct();

		$this->return_id = isset($param['return_id']) ? $param['return_id'] : null;

		$this->sales_return = get_sales_return_details($this->return_id);
		
		$this->customer = get_customer_details($this->sales_return->customer_id);
	}

	public function _get_customer_details()
    {   
    	$customer = $this->customer;
    	$store = $this->store;

    	//Customer Records
	    $state = (!empty($customer->state_id)) ? get_state_details($customer->state_id) : '';
	    $customer_state_name = (!empty($state)) ? $state->state : $store->state;

        $w = ($this->getPageWidth() - 12) / 2;
        $h = 40;

        $custmer_details = '<span style="color:rgb(65, 59, 212);font-style:italic;">'.$this->CI->lang->line('customer_details').'</span>';
        $custmer_details .= "<br><b>".$this->CI->lang->line('name')." :</b> ".$customer->customer_name;
        $custmer_details .= "<br><b>".$this->CI->lang->line('address')." :</b> ".$customer->address;
        $custmer_details .= "<br><b>".$this->CI->lang->line('postcode')." :</b> ".$customer->postcode;
        $custmer_details .= "<br><b>".$this->CI->lang->line('mobile')." :</b> ".$customer->mobile;
        $custmer_details .= "<br><b>".$this->CI->lang->line('email')." :</b> ".$customer->email;
        $custmer_details .= "<br><b>".$this->CI->lang->line('tax_number')." :</b> ".$customer->tax_number;

        $this->setCellPaddings(2,1,1,1);
        $this->setFont($this->get_font_name(), '', 9);
        $this->setFillColor(255, 255, 255);

        $this->writeHTMLCell($w, $h, $x ='6', $y='52', $custmer_details, 1, 0, 1, true, 'J', true);
        return $this;
    } 

    public function _get_invoice_details()
    {
    	$sales_return = $this->sales_return;

        $w = ($this->getPageWidth() - 12) / 2;
        $h = 40;
        $invoice_details = '<span style="color:rgb(65, 59, 212);font-style:italic;">'.$this->CI->lang->line('return_invoice').'</span>';
        $invoice_details .= '<br><b>'.$this->CI->lang->line('invoice_no').' :</b> <span style="font-size:16px;">'.$sales_return->return_code.'</span>';
        $invoice_details .= '<br><b>'.$this->CI->lang->line('date').' :</b> <span style="">'.show_date($sales_return->return_date).'</span>';
        $invoice_details .= '<br><b>'.$this->CI->lang->line('reference_no').' :</b> <span style="">'.$sales_return->reference_no.'</span>';

        $this->writeHTMLCell($w, $h, $x = (6 + $w), $y='', $invoice_details, 1, 1, 1, true, 'J', true);
        return $this;
    }

    public function _get_shipping_address()
    {
        $w = ($this->getPageWidth() - 12) / 2;
        $h = 40;

        $customer = $this->customer;
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

        $custmer_details = '<span style="color:rgb(65, 59, 212);font-style:italic;">'.$this->CI->lang->line('ship_to').'</span>';
        $custmer_details .= "<br><b>".$this->CI->lang->line('name')." :</b> ".$customer->customer_name;
        $custmer_details .= "<br><b>".$this->CI->lang->line('mobile')." :</b> ".$customer->mobile;
        $custmer_details .= "<br><b>".$this->CI->lang->line('address')." :</b> ".$address;
        $custmer_details .= "<br><b>".$this->CI->lang->line('postcode')." :</b> ".$postcode;
        $custmer_details .= "<br><b>".$this->CI->lang->line('city')." :</b> ".$city;
        $custmer_details .= "<br><b>".$this->CI->lang->line('state')." :</b> ".$state;

        $this->writeHTMLCell($w, $h, $x ='6', $y='90', $custmer_details, 1, 0, 1, true, 'J', true);
        return $this;
    }

    public function _get_bank_details()
    {
    	$store = $this->store;
        $w = ($this->getPageWidth() - 12) / 2;
        $h = 40;
        $invoice_details = '<span style="color:rgb(65, 59, 212);font-style:italic;">'.$this->CI->lang->line("bank_details").'</span><br>';
        $invoice_details .= nl2br($store->bank_details);

        $this->writeHTMLCell($w, $h, $x = (6 + $w), $y='', $invoice_details, 1, 1, 1, true, 'J', true);
        return $this;
    }

	public function show_pdf()
	{
		$this->_main_body();
	}

	public function _main_body()
	{	
		$sales_return = $this->sales_return;
		$store 		  = $this->store;
		$customer 	  = $this->customer;
			
		$this->_invoice_name = "Sales Return Invoice";

		// set font
		$this->setFont($this->get_font_name(), 'B', 20);

		// add a page
		$this->AddPage();

		// Customer Details
		$this->_get_customer_details(); 

		// Invoice Details
		$this->_get_invoice_details();

		// Shipping Details
		$this->_get_shipping_address(); 

		// Bank Details
		$this->_get_bank_details();

		// Set document name (footer -R)
		$this->_set_document_name($this->CI->lang->line("return_invoice"));
		
		// Set document number (footer -R)
		$this->_set_document_number($sales_return->return_code);

		// Search Coupon Details
		$coupon_code = $coupon_type = '';
	    $coupon_value=0;
	    if(!empty($sales_return->coupon_id)){
	      $coupon_details = get_customer_coupon_details($sales_return->coupon_id);
	      $coupon_code =$coupon_details->code;
	      $coupon_value =$coupon_details->value;
	      $coupon_type =$coupon_details->type;
	    } 
		$this->setFont($this->get_font_name(), '', 8);

		// set cell padding
		$this->setCellPaddings(1, 1, 1, 1);

		// set color for background
		$this->setFillColor(255, 255, 255);

		$this->Ln(0);
		
		$show_tax = true;

		$this->setFont($this->get_font_name(), '', 8);
		
		$tbl = '
		<style type="text/css">
			table, td, th {
			    border-collapse: collapse;
			    border: 0.5px solid #000000;
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
		</style>
		<table>';

			$widthArray = array(
				'sl_no' 		=> '7',
				'description' 	=> '35',
				'unit_cost' 	=> '10',
				'qty' 			=> '8',
				'tax' 			=> '10',
				'tax_amt'		=> '10',
				'disc' 			=> '10',
				'amount' 		=> '10',
			);

			$tot_col = count($widthArray);
			$equalize = 0;

			if(!$show_tax){
				$tot_col -=2;
				$equalize = 20/$tot_col;
			}

			//Sum the value
			$sumOfWidth = 0;
			$colW = array();
			foreach($widthArray as $key => $val){
				if(!$show_tax){
					$makeZero = array('tax','tax_amt');
					$val = (in_array($key,$makeZero)) ? 0 : $val+$equalize;
				} else {
					$val +=$equalize;
				}
				$colW[$key] = $val.'%';
				$sumOfWidth+=$val;
			}

		    $tbl .='<thead>
		        <tr style="background-color: #e4eaff ;" class="text-bold">
			        <th colspan="1" style="width: '.$colW['sl_no'].'">'.$this->CI->lang->line("sl_no").'</th>
			        <th colspan="1" style="width: '.$colW['description'].'" >'.$this->CI->lang->line("description").'</th>
			        <th colspan="1" style="width: '.$colW['unit_cost'].'">'.$this->CI->lang->line("unit_cost").'</th>
			        <th colspan="1" style="width: '.$colW['qty'].'">'.$this->CI->lang->line("qty").'</th>';
			        if($show_tax){
			        	$tbl .='<th colspan="1" style="width: '.$colW['tax'].'">'.$this->CI->lang->line("tax").'</th>';
			        	$tbl .='<th colspan="1" style="width: '.$colW['tax_amt'].'">'.$this->CI->lang->line("tax_amt").'</th>';
			    	}
			        $tbl .='<th colspan="1" style="width: '.$colW['disc'].'">'.$this->CI->lang->line("discount_per").'</th>
			        <th colspan="1" style="width: '.$colW['amount'].'">'.$this->CI->lang->line("amount").'</th>
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

              $this->CI->db->select(" a.description,c.item_name, a.return_qty as sales_qty, a.tax_type,
                                  a.price_per_unit, b.tax,b.tax_name,a.tax_amt,
                                  a.discount_input,a.discount_amt, a.unit_total_cost,
                                  a.total_cost , d.unit_name,c.sku,c.hsn
                              ");
              $this->CI->db->where("a.return_id",$this->return_id);
              $this->CI->db->from("db_salesitemsreturn a");
              $this->CI->db->join("db_tax b","b.id=a.tax_id","left");
              $this->CI->db->join("db_items c","c.id=a.item_id","left");
              $this->CI->db->join("db_units d","d.id = c.unit_id","left");
              
              $q2=$this->CI->db->get();

		        foreach ($q2->result() as $res2) {
                  $discount = (empty($res2->discount_input)||$res2->discount_input==0)? '0':$res2->discount_input."%";
                  $discount_amt = (empty($res2->discount_amt)||$res2->discount_input==0)? '0':$res2->discount_amt."";
                  $before_tax=$res2->unit_total_cost;
                  $tot_cost_before_tax=$before_tax * $res2->sales_qty;

                  $price_per_unit = $res2->price_per_unit;
                  if(!empty($res2->tax_type) && $res2->tax_type=='Inclusive'){
                    $price_per_unit -= ($res2->tax_amt/$res2->sales_qty);
                  }

                  $tot_price = $price_per_unit * $res2->sales_qty;

                  $tbl .='<tr nobr="true">';
				      $tbl .='<td colspan="1" style="width: '.$colW['sl_no'].'">'.$i++.'</td>';
				      $tbl .='<td colspan="1" style="width: '.$colW['description'].'" >';
				      $tbl .= $res2->item_name;
				      $tbl .= (!empty($res2->description)) ? "<br><i>[".nl2br($res2->description)."]</i>" : '';
				      $tbl .= '</td>';
				      $tbl .='<td colspan="1" class="text-right" style="width: '.$colW['unit_cost'].'">'.store_number_format($price_per_unit).'</td>';
				      $tbl .='<td colspan="1" style="width: '.$colW['qty'].'">'.format_qty($res2->sales_qty).'</td>';
				      if($show_tax){
					      $tbl .='<td colspan="1" style="width: '.$colW['tax'].'">'.$res2->tax_name.'</td>';
					      $tbl .='<td colspan="1" class="text-right" style="width: '.$colW['tax_amt'].'">'.store_number_format($res2->tax_amt).'</td>';
					  }
				      $tbl .='<td colspan="1" class="text-right" style="width: '.$colW['disc'].'">'.store_number_format($discount_amt).'</td>';
				      $tbl .='<td colspan="1" class="text-right" style="width: '.$colW['amount'].'">'.store_number_format($res2->total_cost).'</td>';
		          $tbl .='</tr>';

                  $tot_qty +=$res2->sales_qty;
                  $tot_sales_price +=$price_per_unit;
                  $tot_tax_amt +=$res2->tax_amt;
                  $tot_discount_amt +=$res2->discount_amt;
                  $tot_unit_total_cost +=$res2->unit_total_cost;
                  $tot_before_tax +=$before_tax;
                  $tot_total_cost +=$res2->total_cost;

                  $tot_price_per_unit +=$price_per_unit;
                  $sum_of_tot_price +=$tot_price;
		        }

		        //Pad empty rows
		        for($j=1;$j<=1;$j++){
		        	$tbl .='<tr nobr="true">';
				      $tbl .='<td colspan="1" style="width: '.$colW['sl_no'].'"></td>';
				      $tbl .='<td colspan="1" style="width: '.$colW['description'].'" ></td>';
				      $tbl .='<td colspan="1" class="text-right" style="width: '.$colW['unit_cost'].'"></td>';
				      $tbl .='<td colspan="1" style="width: '.$colW['qty'].'"></td>';
				      if($show_tax){
					      $tbl .='<td colspan="1" style="width: '.$colW['tax'].'"></td>';
					      $tbl .='<td colspan="1" class="text-right" style="width: '.$colW['tax_amt'].'"></td>';
					  }
				      $tbl .='<td colspan="1" class="text-right" style="width: '.$colW['disc'].'"></td>';
				      $tbl .='<td colspan="1" class="text-right" style="width: '.$colW['amount'].'"></td>';
		          $tbl .='</tr>';
		        }

		        //Row Total
		        $tbl .='<tr class="text-bold" nobr="true">';
				      $tbl .='<td colspan="1" class="text-right" style="width: '.($colW['sl_no']+$colW['description']).'">'.$this->CI->lang->line("total").'</td>';
				      $tbl .='<td colspan="1" class="text-right" style="width: '.$colW['unit_cost'].'">'.store_number_format($tot_sales_price).'</td>';
				      $tbl .='<td colspan="1" style="width: '.$colW['qty'].'">'.format_qty($tot_qty).'</td>';
				      if($show_tax){
					      $tbl .='<td colspan="1" style="width: '.$colW['tax'].'"></td>';
					      $tbl .='<td colspan="1" class="text-right" style="width: '.$colW['tax_amt'].'">'.store_number_format($tot_tax_amt).'</td>';
					  }
				      $tbl .='<td colspan="1" class="text-right" style="width: '.$colW['disc'].'">'.store_number_format($tot_discount_amt).'</td>';
				      $tbl .='<td colspan="1" class="text-right" style="width: '.$colW['amount'].'">'.store_number_format($tot_total_cost).'</td>';
		          $tbl .='</tr>';

		          //Other calculations
		          $tbl .='<tr nobr="true">';
				      $tbl .='<td colspan="'.((!$show_tax)?5:7).'" class="text-right" style="width: '.($sumOfWidth-($colW['disc']+$colW['amount'])).'%;border-right: none;"><b>'.$this->CI->lang->line("subtotal").'</b></td>';
				      $tbl .='<td colspan="2" class="text-right" style="width: '.($colW['disc']+$colW['amount']).'%;border-left: none;">'.store_number_format($tot_total_cost).'</td>';
		          $tbl .='</tr>';

		          $tbl .='<tr nobr="true">';
				      $tbl .='<td colspan="'.((!$show_tax)?5:7).'" class="text-right" style="width: '.($sumOfWidth-($colW['disc']+$colW['amount'])).'%;border-right: none;"><b>'.$this->CI->lang->line("other_charges").'</b></td>';
				      $tbl .='<td colspan="2" class="text-right" style="width: '.($colW['disc']+$colW['amount']).'%;border-left: none;">'.store_number_format($sales_return->other_charges_amt).'</td>';
		          $tbl .='</tr>';

		          if(!empty($coupon_code)){
		          $tbl .='<tr nobr="true">';
				      $tbl .='<td colspan="'.((!$show_tax)?5:7).'" class="text-right" style="width: '.($sumOfWidth-($colW['disc']+$colW['amount'])).'%;border-right: none;"><b>'.$this->CI->lang->line("couponDiscount").' ['.getTruncatedCCNumber($coupon_code).']</b></td>';
				      $tbl .='<td colspan="2" class="text-right" style="width: '.($colW['disc']+$colW['amount']).'%;border-left: none;">'.store_number_format($sales_return->coupon_amt).'</td>';
		          $tbl .='</tr>';
		      	  }

		          $tbl .='<tr nobr="true">';
				      $tbl .='<td colspan="'.((!$show_tax)?5:7).'" class="text-right" style="width: '.($sumOfWidth-($colW['disc']+$colW['amount'])).'%;border-right: none;"><b>'.$this->CI->lang->line("discount_to_all").'('.store_number_format($sales_return->discount_to_all_input).' '.$sales_return->discount_to_all_type.')</b></td>';
				      $tbl .='<td colspan="2" class="text-right" style="width: '.($colW['disc']+$colW['amount']).'%;border-left: none;">'.store_number_format($sales_return->tot_discount_to_all_amt).'</td>';
		          $tbl .='</tr>';

		          $tbl .='<tr nobr="true">';
				      $tbl .='<td colspan="'.((!$show_tax)?5:7).'" class="text-right" style="width: '.($sumOfWidth-($colW['disc']+$colW['amount'])).'%;border-right: none;"><b>'.$this->CI->lang->line("round_off").'</b></td>';
				      $tbl .='<td colspan="2" class="text-right" style="width: '.($colW['disc']+$colW['amount']).'%;border-left: none;">'.store_number_format($sales_return->round_off).'</td>';
		          $tbl .='</tr>';

		          $tbl .='<tr nobr="true">';
				      $tbl .='<td colspan="'.((!$show_tax)?5:7).'" class="text-right" style="width: '.($sumOfWidth-($colW['disc']+$colW['amount'])).'%;border-right: none;"><b>'.$this->CI->lang->line("grand_total").'</b></td>';
				      $tbl .='<td colspan="2" class="text-right" style="width: '.($colW['disc']+$colW['amount']).'%;border-left: none;"><b>'.store_number_format($sales_return->grand_total).'</b></td>';
		          $tbl .='</tr>';

		          $tbl .='<tr nobr="true">';
				      $tbl .='<td colspan="'.((!$show_tax)?5:7).'" class="text-right" style="width: '.($sumOfWidth-($colW['disc']+$colW['amount'])).'%;border-right: none;"><b>'.$this->CI->lang->line("paid_amount").'</b></td>';
				      $tbl .='<td colspan="2" class="text-right" style="width: '.($colW['disc']+$colW['amount']).'%;border-left: none;"><b>'.store_number_format($sales_return->paid_amount).'</b></td>';
		          $tbl .='</tr>';

		          //Amount in words
		          $tbl .='<tr nobr="true">';
				      $tbl .='<td colspan="'.((!$show_tax)?7:9).'" class="text-left" style="width: '.($sumOfWidth).'%;border-right: none;">'.$this->CI->lang->line("in_words").': <b>'.no_to_words(round_off_amount($sales_return->grand_total)).'</b></td>';
		          $tbl .='</tr>';

		            if(!empty($sales_return->return_note)){
		            	$tbl .='<tr nobr="true">
		                	<td colspan="'.((!$show_tax)?7:9).'" style="width:'.($sumOfWidth).'%; height:30px;">';
		                	$tbl .="<b>".$this->CI->lang->line("note")."</b>: ".nl2br($sales_return->return_note);
		                	$tbl .='</td>';
		                $tbl .='</tr>';
		            }

		            $tbl .='</tbody>
		        </table>';

		$tbl .='<table nobr="true">
		            <tbody>
		                <tr nobr="true">
		                    <td colspan="12"><div style="font-size:10px;text-align:justify;"><span style="color:rgb(65, 59, 212);font-style:italic;">'.$this->CI->lang->line("customer_signature").':</span><br>___________________</div></td>
		                    <td colspan="6" style="vertical-align:bottom;text-align:center;min-height:60px;height:60px;"><div style="font-size:10px;"><span style="color:rgb(65, 59, 212);font-style:italic;vertical-align:bottom;">'.$this->CI->lang->line("authorised_signatory").':</span><br>___________________</div></td>
		                </tr>
		                <tr nobr="true">
		                    <td colspan="18" class="text-center">';
		                        	$tbl .=nl2br($store->sales_invoice_footer_text);
		                    		$tbl .='
		                    </td>
		                </tr>';
		        
		        $inv_datetime = (!empty($sales_return->return_date) ? $sales_return->return_date : date('Y-m-d')) . ' ' . (!empty($sales_return->created_time) ? $sales_return->created_time : '00:00:00');
		        $zatca_vat_no = !empty($store->vat_no) ? $store->vat_no : (!empty($store->gst_no) ? $store->gst_no : '');
		        $zatca_qr_img = get_zatca_qr_code_image($store->store_name, $zatca_vat_no, $inv_datetime, $sales_return->grand_total, $tot_tax_amt);

		        $has_zatca = !empty($zatca_qr_img);
		        $has_whatsapp = !empty($store->qr_image);

		        if($has_zatca || $has_whatsapp){
		        	$tbl .='<tr nobr="true">
		                    <td colspan="18" class="text-center"><br>
		                        <table border="0" cellpadding="5" cellspacing="0" style="width:100%; text-align:center;">
		                            <tr>';
		            if($has_zatca){
		            	$tbl .='<td style="text-align:center; vertical-align:middle; width:'.($has_whatsapp ? '50%' : '100%').';">
		                            <table border="1" cellpadding="6" cellspacing="0" style="margin:auto; display:inline-table; width:120px; border-collapse:collapse;">
		                                <tr>
		                                    <td style="text-align:center; border:1px solid #333;">
		                                        <img src="'.$zatca_qr_img.'" width="65" height="65"><br>
		                                        <span style="font-size:8px; font-weight:bold;">ZATCA</span>
		                                    </td>
		                                </tr>
		                            </table>
		                        </td>';
		            }
		            if($has_whatsapp){
		            	$tbl .='<td style="text-align:center; vertical-align:middle; width:'.($has_zatca ? '50%' : '100%').';">
		                            <table border="1" cellpadding="6" cellspacing="0" style="margin:auto; display:inline-table; width:120px; border-collapse:collapse;">
		                                <tr>
		                                    <td style="text-align:center; border:1px solid #333;">
		                                        <img src="'.base_url($store->qr_image).'" width="65" height="65"><br>
		                                        <span style="font-size:8px; font-weight:bold;">WhatsApp</span>
		                                    </td>
		                                </tr>
		                            </table>
		                        </td>';
		            }
		            $tbl .='</tr>
		                        </table>
		                    </td>
		                </tr>';
		        }

		        $tbl .='</tbody>
		        </table>';

		$this->writeHTMLCell('', '', $x ='', $y='', $tbl, 0, 1, 1, true, 'J', true);

		$this->Output('sales_return_'.$this->return_id.'.pdf', 'I');
	}

}
