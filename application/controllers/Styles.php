<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Styles extends MY_Controller {
	public function __construct() {
		parent::__construct();
		$this->load_global();
		$this->load->model('style_model', 'style');
	}

	public function index() {
		$this->view();
	}

	public function add() {
		$this->permission_check('brand_add');
		$data = $this->data;
		$data['page_title'] = (!empty($this->lang->line('style'))) ? $this->lang->line('style') : 'Style';
		$this->load->view('style', $data);
	}

	public function newstyle() {
		$this->form_validation->set_rules('style', 'Style', 'trim|required');
		if ($this->form_validation->run() == TRUE) {
			$this->load->model('style_model');
			$result = $this->style_model->verify_and_save();
			echo $result;
		} else {
			echo "Please Enter Style name.";
		}
	}

	public function update($id) {
		$this->belong_to('db_styles', $id);
		$this->permission_check('brand_edit');
		$data = $this->data;

		$this->load->model('style_model');
		$result = $this->style_model->get_details($id, $data);
		$data = array_merge($data, $result);
		$data['page_title'] = (!empty($this->lang->line('style'))) ? $this->lang->line('style') : 'Style';
		$this->load->view('style', $data);
	}

	public function update_style() {
		$this->form_validation->set_rules('style', 'Style', 'trim|required');
		$this->form_validation->set_rules('q_id', '', 'trim|required');

		if ($this->form_validation->run() == TRUE) {
			$this->load->model('style_model');
			$result = $this->style_model->update_style();
			echo $result;
		} else {
			echo "Please Enter Style name.";
		}
	}

	public function view() {
		$this->permission_check('brand_view');
		$data = $this->data;
		$data['page_title'] = (!empty($this->lang->line('styles_list'))) ? $this->lang->line('styles_list') : 'Styles List';
		$this->load->view('style-view', $data);
	}

	public function ajax_list() {
		$list = $this->style->get_datatables();

		$data = array();
		$no = $_POST['start'];
		foreach ($list as $style) {
			$no++;
			$row = array();
			$row[] = '<input type="checkbox" name="checkbox[]" value=' . $style->id . ' class="checkbox column_checkbox" >';
			$row[] = $no;
			$row[] = $style->style_name;
			$row[] = $style->description;

			if ($style->status == 1) {
				$str = "<span onclick='update_status(" . $style->id . ",0)' id='span_" . $style->id . "' class='label label-success' style='cursor:pointer'>Active </span>";
			} else {
				$str = "<span onclick='update_status(" . $style->id . ",1)' id='span_" . $style->id . "' class='label label-danger' style='cursor:pointer'> Inactive </span>";
			}
			$row[] = $str;
			$str2 = '<div class="btn-group" title="View Account">
						<a class="btn btn-primary btn-o dropdown-toggle" data-toggle="dropdown" href="#">
							Action <span class="caret"></span>
						</a>
						<ul role="menu" class="dropdown-menu dropdown-light pull-right">';

			if ($this->permissions('brand_edit')) {
				$str2 .= '<li>
							<a title="Edit Record ?" href="' . base_url() . 'styles/update/' . $style->id . '">
								<i class="fa fa-fw fa-edit text-blue"></i>Edit
							</a>
						</li>';
			}

			if ($this->permissions('brand_delete')) {
				$str2 .= '<li>
							<a style="cursor:pointer" title="Delete Record ?" onclick="delete_style(' . $style->id . ')">
								<i class="fa fa-fw fa-trash text-red"></i>Delete
							</a>
						</li>
					</ul>
				</div>';
			}

			$row[] = $str2;
			$data[] = $row;
		}

		$output = array(
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->style->count_all(),
			"recordsFiltered" => $this->style->count_filtered(),
			"data" => $data,
		);
		//output to json format
		echo json_encode($output);
	}

	public function update_status() {
		$this->permission_check_with_msg('brand_edit');
		$id = $this->input->post('id');
		$status = $this->input->post('status');

		$this->load->model('style_model');
		$result = $this->style_model->update_status($id, $status);
		return $result;
	}

	public function delete_style() {
		$this->permission_check_with_msg('brand_delete');
		$id = $this->input->post('q_id');
		return $this->style->delete_styles_from_table($id);
	}

	public function multi_delete() {
		$this->permission_check_with_msg('brand_delete');
		$ids = implode(",", $_POST['checkbox']);
		return $this->style->delete_styles_from_table($ids);
	}

	//ITS FROM POP UP MODAL
	public function add_style_modal(){
		$this->form_validation->set_rules('style', 'Style Name', 'trim|required');
		if ($this->form_validation->run() == TRUE) {
			$result=$this->style->verify_and_save();
			//fetch latest item details
			$res=array();
			$query=$this->db->select("id,style_name")
						->where('store_id',get_current_store_id())
						->from('db_styles')
						->order_by('id','desc')
						->limit(1)->get();
			$res['id']=$query->row()->id;
			$res['style']=$query->row()->style_name;
			$res['result']=$result;
			
			echo json_encode($res);

		} 
		else {
			echo "Please Fill Compulsory(* marked) Fields.";
		}
	}
	//END
}
