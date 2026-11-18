<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Bank_master extends MY_Controller {
	public function __construct() {
		parent::__construct();
		$this->load_global();
		$this->load->model('bank_master_model', 'bank_master');
	}

	public function index() {
		$this->view();
	}

	public function add() {
		$this->permission_check('accounts_add');
		$data = $this->data;
		$data['page_title'] = (!empty($this->lang->line('bank_master'))) ? $this->lang->line('bank_master') : 'Bank Master';
		$this->load->view('accounts/bank_master', $data);
	}

	public function newbank() {
		$this->form_validation->set_rules('account_number', 'Account Number', 'trim|required');
		if ($this->form_validation->run() == TRUE) {
			$result = $this->bank_master->verify_and_save();
			echo $result;
		} else {
			echo "Please Enter Account Number.";
		}
	}

	public function update($id) {
		$this->belong_to('db_bank_master', $id);
		$this->permission_check('accounts_edit');
		$data = $this->data;

		$result = $this->bank_master->get_details($id, $data);
		$data = array_merge($data, $result);
		$data['page_title'] = (!empty($this->lang->line('bank_master'))) ? $this->lang->line('bank_master') : 'Bank Master';
		$this->load->view('accounts/bank_master', $data);
	}

	public function update_bank() {
		$this->form_validation->set_rules('account_number', 'Account Number', 'trim|required');
		$this->form_validation->set_rules('q_id', '', 'trim|required');

		if ($this->form_validation->run() == TRUE) {
			$result = $this->bank_master->update_bank();
			echo $result;
		} else {
			echo "Please Enter Account Number.";
		}
	}

	public function view() {
		$this->permission_check('accounts_view');
		$data = $this->data;
		$data['page_title'] = (!empty($this->lang->line('bank_master_list'))) ? $this->lang->line('bank_master_list') : 'Bank Master List';
		$this->load->view('accounts/bank_master_list', $data);
	}

	public function ajax_list() {
		$list = $this->bank_master->get_datatables();

		$data = array();
		$no = $_POST['start'];
		foreach ($list as $bank) {
			$no++;
			$row = array();
			$row[] = '<input type="checkbox" name="checkbox[]" value="' . $bank->id . '" class="checkbox column_checkbox" >';
			$row[] = $bank->account_number;
			$row[] = $bank->branch_name;
			$row[] = $bank->iban_number;
			$row[] = $bank->bank_details;
			$row[] = store_number_format($bank->opening_balance);

			if ($bank->status == 1) {
				$str_status = "<span onclick='update_status(" . $bank->id . ",0)' id='span_" . $bank->id . "' class='label label-success' style='cursor:pointer'>Active </span>";
			} else {
				$str_status = "<span onclick='update_status(" . $bank->id . ",1)' id='span_" . $bank->id . "' class='label label-danger' style='cursor:pointer'> Inactive </span>";
			}
			$row[] = $str_status;

			$str_action = '<div class="btn-group" title="Action">
						<a class="btn btn-primary btn-o dropdown-toggle" data-toggle="dropdown" href="#">
							Action <span class="caret"></span>
						</a>
						<ul role="menu" class="dropdown-menu dropdown-light pull-right">';

			if ($this->permissions('accounts_edit')) {
				$str_action .= '<li>
							<a title="Edit Record ?" href="' . base_url() . 'bank_master/update/' . $bank->id . '">
								<i class="fa fa-fw fa-edit text-blue"></i>Edit
							</a>
						</li>';
			}

			if ($this->permissions('accounts_delete')) {
				$str_action .= '<li>
							<a style="cursor:pointer" title="Delete Record ?" onclick="delete_bank(' . $bank->id . ')">
								<i class="fa fa-fw fa-trash text-red"></i>Delete
							</a>
						</li>';
			}
			$str_action .= '</ul></div>';

			$row[] = $str_action;
			$data[] = $row;
		}

		$output = array(
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->bank_master->count_all(),
			"recordsFiltered" => $this->bank_master->count_filtered(),
			"data" => $data,
		);
		echo json_encode($output);
	}

	public function update_status() {
		$this->permission_check_with_msg('accounts_edit');
		$id = $this->input->post('id');
		$status = $this->input->post('status');

		$result = $this->bank_master->update_status($id, $status);
		return $result;
	}

	public function delete_bank() {
		$this->permission_check_with_msg('accounts_delete');
		$id = $this->input->post('q_id');
		return $this->bank_master->delete_bank_from_table($id);
	}

	public function multi_delete() {
		$this->permission_check_with_msg('accounts_delete');
		$ids = implode(",", $_POST['checkbox']);
		return $this->bank_master->delete_bank_from_table($ids);
	}
}
