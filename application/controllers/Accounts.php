<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Accounts extends MY_Controller {
	public function __construct(){
		parent::__construct();
		$this->load_global();
		$this->load->model('accounts_model','accounts');
	}
	public function index()
	{
		$this->permission_check('accounts_view');
		$data=$this->data;
		$data['page_title']=$this->lang->line('accounts_list');
		$this->load->view('accounts/accounts_list',$data);
	}

	public function book($account_id)
	{
		$this->belong_to('ac_accounts',$account_id);
		$this->permission_check('accounts_view');
		$data=$this->data;
		$data['page_title']=$this->lang->line('account_book');
		$data['account_id']=$account_id;
		$this->load->view('accounts/account_book',$data);
	}
	public function cash_transactions()
	{
		$this->permission_check('accounts_view');
		$data=$this->data;
		$data['page_title']=$this->lang->line('cash_transactions');
		$this->load->view('accounts/cash_transactions',$data);
	}

	/* ---------------- Day Book ---------------- */
	public function day_book()
	{
		$this->permission_check('accounts_view');
		$data=$this->data;
		$data['page_title']='Day Book';
		$this->load->model('day_book_model','day_book');
		$date=$this->day_book->clean_date($this->input->get('date'));
		$data['daybook']=$this->day_book->get_summary($date);
		$this->load->view('accounts/day_book',$data);
	}

	// Save the cash drawer declaration (notes + coins) for one date
	public function day_book_save()
	{
		header('Content-Type: application/json');
		if(!$this->permissions('accounts_view')){
			echo json_encode(array('status'=>'failed','message'=>'You do not have permission to save the Day Book.'));
			return;
		}
		$this->load->model('day_book_model','day_book');
		$date=$this->day_book->clean_date($this->input->post('date'));
		$counts=json_decode((string)$this->input->post('counts'), true);
		if(!is_array($counts)){
			echo json_encode(array('status'=>'failed','message'=>'Invalid cash count.'));
			return;
		}
		echo json_encode($this->day_book->save_declaration($date,$counts));
	}

	// Export the Day Book of one date as PDF
	public function day_book_pdf()
	{
		$this->permission_check('accounts_view');
		$this->load->model('day_book_model','day_book');
		$date=$this->day_book->clean_date($this->input->get('date'));
		$d=$this->day_book->get_summary($date);

		$f=function($v){ return store_number_format($v); };
		$row=function($label,$value,$bold=false){
			$b1=$bold ? '<b>' : ''; $b2=$bold ? '</b>' : '';
			return '<tr><td width="62%">'.$b1.$label.$b2.'</td><td width="38%" align="right">'.$b1.$value.$b2.'</td></tr>';
		};
		$block=function($title,$rows){
			return '<h3 style="color:#1a9fe0;">'.$title.'</h3><table cellpadding="4" border="0" style="border-bottom:1px solid #dddddd;">'.$rows.'</table><br/>';
		};

		$s=$d['sales']; $pu=$d['purchase']; $c=$d['cash'];
		$html='<h1>Day Book</h1><div>Date: '.show_date($date).'</div><br/>';

		$html.=$block('Sales Summary',
			$row('Gross Sales',$f($s['gross'])).
			$row('Sales Discount','- '.$f($s['discount'])).
			$row('Sales Returns','- '.$f($s['returns'])).
			$row('Net Sales excl. VAT',$f($s['net_excl']),true).
			$row($s['vat_label'],'+ '.$f($s['vat'])).
			$row('Net Sales (incl. VAT)',$f($s['net_incl']),true));

		$html.=$block('Purchase Summary',
			$row('Gross Purchase',$f($pu['gross'])).
			$row('Purchase Discount','- '.$f($pu['discount'])).
			$row('Purchase Returns','- '.$f($pu['returns'])).
			$row('Net Purchase excl. VAT',$f($pu['net_excl']),true).
			$row($pu['vat_label'],'+ '.$f($pu['vat'])).
			$row('Net Purchase (incl. VAT)',$f($pu['net_incl']),true));

		$diff=$c['counted']-$c['expected'];
		$diff_label='Cash Difference'.($diff<0 ? ' (short)' : ($diff>0 ? ' (over)' : ''));
		$html.=$block('Cash Reconciliation',
			$row('Opening Cash',$f($c['opening'])).
			$row('Cash Sales','+ '.$f($c['sales'])).
			$row('Cash Refunds','- '.$f($c['refunds'])).
			$row('Cash Paid Out','- '.$f($c['paid_out'])).
			$row('Expected Cash in Drawer',$f($c['expected']),true).
			$row('Actual Cash Counted',$f($c['counted']),true).
			$row($diff_label,$f($diff),true));

		$pay_rows='';
		foreach($d['payments'] as $pay){ $pay_rows.=$row(html_escape($pay['label']),$f($pay['amount'])); }
		$pay_rows.=$row('On Account (credit)',$f($d['on_account']));
		$pay_rows.=$row('Total Received',$f($d['payment_total']),true);
		$html.=$block('Payment Summary',$pay_rows);

		$den='<tr style="background-color:#f2f6fa;"><td width="40%"><b>Denomination</b></td><td width="20%" align="right"><b>Qty</b></td><td width="40%" align="right"><b>Subtotal</b></td></tr>';
		foreach($d['notes'] as $n){ $q=(int)$d['counts']['notes'][$n]; $den.='<tr><td>'.number_format((float)$n).'</td><td align="right">'.$q.'</td><td align="right">'.$f($q*(float)$n).'</td></tr>'; }
		foreach($d['coins'] as $cn){ $q=(int)$d['counts']['coins'][$cn]; $den.='<tr><td>'.$cn.'</td><td align="right">'.$q.'</td><td align="right">'.$f($q*(float)$cn).'</td></tr>'; }
		$den.='<tr><td colspan="2"><b>Total Counted</b></td><td align="right"><b>'.$f($c['counted']).'</b></td></tr>';
		$html.='<h3 style="color:#1a9fe0;">Denomination Count</h3><table cellpadding="4" border="0">'.$den.'</table>';

		require_once(APPPATH.'libraries/tcpdf/tcpdf.php');
		$pdf=new TCPDF('P','mm','A4',true,'UTF-8',false);
		$pdf->SetCreator('Day Book');
		$pdf->SetTitle('Day Book '.$date);
		$pdf->setPrintHeader(false);
		$pdf->setPrintFooter(false);
		$pdf->SetMargins(12,12,12);
		$pdf->SetAutoPageBreak(true,12);
		$pdf->AddPage();
		$pdf->SetFont('dejavusans','',9);
		$pdf->writeHTML($html,true,false,true,false,'');
		if(ob_get_length()){ @ob_end_clean(); }
		$pdf->Output('day-book-'.$date.'.pdf','I');
	}

	public function add()
	{
		$this->permission_check('accounts_add');
		$data=$this->data;
		$data['page_title']=$this->lang->line('add_account');
		$this->load->view('accounts/accounts',$data);
	}
	
	
	public function newaccounts(){
		$this->permission_check('accounts_add');
		$this->form_validation->set_rules('account_code', 'Account Code', 'trim|required');
		$this->form_validation->set_rules('account_name', 'Account Name', 'trim|required');
		$this->form_validation->set_rules('opening_balance', 'Oprning Balance', 'trim|required');
		
		if ($this->form_validation->run() == TRUE) {
			$result=$this->accounts->verify_and_save();
			echo $result;
		} else {
			echo "Please Fill Compulsory(* marked) Fields.";
		}
	}
	public function update($id){
		$this->permission_check('accounts_edit');
		$this->belong_to('ac_accounts',$id);
		$data=$this->data;
		$result=$this->accounts->get_details($id,$data);
		$data=array_merge($data,$result);
		$data['page_title']=$this->lang->line('accounts');
		$this->load->view('accounts/accounts', $data);
	}
	public function update_accounts(){
		$this->form_validation->set_rules('account_code', 'Account Code', 'trim|required');
		$this->form_validation->set_rules('account_name', 'Account Name', 'trim|required');
		//$this->form_validation->set_rules('opening_balance', 'Oprning Balance', 'trim|required');

		if ($this->form_validation->run() == TRUE) {
			$result=$this->accounts->update_accounts();
			echo $result;
		} else {
			echo "Please Fill Compulsory(* marked) Fields.";
		}
	}

	public function ajax_list()
	{
		$list = $this->accounts->get_datatables();
		
		$data = array();
		$no = $_POST['start'];
		foreach ($list as $accounts) {
			$no++;
			$row = array();
			$row[] = ($accounts->delete_bit) ? '<span data-toggle="tooltip" title="Resticted" class="text-danger fa fa-fw fa-ban"></span>' : '<input type="checkbox" name="checkbox[]" value='.$accounts->id.' class="checkbox column_checkbox" >';
			//$row[] = get_store_name($accounts->store_id);
			$row[] = $accounts->account_code;
			$row[] = $accounts->account_name;
			$row[] = get_account_name($accounts->parent_id);
			$row[] = store_number_format($accounts->balance);
			
			$row[] = ucfirst($accounts->created_by);			
				     $str2 = '<div class="btn-group" title="View Account">
										<a class="btn btn-primary btn-o dropdown-toggle" data-toggle="dropdown" href="#">
											Action <span class="caret"></span>
										</a>
										<ul role="menu" class="dropdown-menu dropdown-light pull-right">';

											if($this->permissions('accounts_edit'))
											$str2.='<li>
												<a data-toggle="tooltip" title="Edit Record ?" href="'.base_url().'accounts/update/'.$accounts->id.'">
													<i class="fa fa-fw fa-edit text-blue"></i>Edit
												</a>
											</li>';

											if($this->permissions('accounts_view'))
											$str2.='<li>
												<a data-toggle="tooltip" title="Click to check Account!" href="'.base_url().'accounts/book/'.$accounts->id.'">
													<i class="fa fa-fw fa-book text-blue"></i>Account Book
												</a>
											</li>';

											if($this->permissions('accounts_delete') && !$accounts->delete_bit)
											$str2.='<li>
												<a style="cursor:pointer" data-toggle="tooltip" title="Delete Record ?" onclick="delete_accounts('.$accounts->id.')">
													<i class="fa fa-fw fa-trash text-red"></i>Delete
												</a>
											</li>
											
										</ul>
									</div>';			
			$row[] = $str2;

			$data[] = $row;
		}

		$output = array(
						"draw" => $_POST['draw'],
						"recordsTotal" => $this->accounts->count_all(),
						"recordsFiltered" => $this->accounts->count_filtered(),
						"data" => $data,
				);
		//output to json format
		echo json_encode($output);
	}
	
	public function delete_accounts(){
		$this->permission_check_with_msg('accounts_delete');
		$id=$this->input->post('q_id');
		echo $this->accounts->delete_accounts_from_table($id);
	}
	public function multi_delete_accounts(){
		$this->permission_check_with_msg('accounts_delete');
		$ids=implode (",",$_POST['checkbox']);
		echo $this->accounts->delete_accounts_from_table($ids);
	}
	


}
