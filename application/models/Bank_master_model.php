<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Bank_master_model extends CI_Model {

	var $table = 'db_bank_master';
	var $column_order = array(null, 'account_number', 'branch_name', 'iban_number', 'bank_details', 'opening_balance', null);
	var $column_search = array('account_number', 'branch_name', 'iban_number', 'bank_details', 'opening_balance');
	var $order = array('id' => 'desc');

	private function _get_datatables_query()
	{
		$this->db->from($this->table);
		$this->db->where("store_id", get_current_store_id());
		
		$i = 0;
		foreach ($this->column_search as $item)
		{
			if(isset($_POST['search']['value']) && $_POST['search']['value'])
			{
				if($i===0)
				{
					$this->db->group_start();
					$this->db->like($item, $_POST['search']['value']);
				}
				else
				{
					$this->db->or_like($item, $_POST['search']['value']);
				}

				if(count($this->column_search) - 1 == $i)
					$this->db->group_end();
			}
			$i++;
		}
		
		if(isset($_POST['order']))
		{
			$this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
		} 
		else if(isset($this->order))
		{
			$order = $this->order;
			$this->db->order_by(key($order), $order[key($order)]);
		}
	}

	function get_datatables()
	{
		$this->_get_datatables_query();
		if(isset($_POST['length']) && $_POST['length'] != -1)
			$this->db->limit($_POST['length'], $_POST['start']);
		$query = $this->db->get();
		return $query->result();
	}

	function count_filtered()
	{
		$this->_get_datatables_query();
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all()
	{
		$this->db->where("store_id", get_current_store_id());
		$this->db->from($this->table);
		return $this->db->count_all_results();
	}

	public function verify_and_save(){
		extract($this->security->xss_clean(html_escape(array_merge($this->data, $_POST))));
		
		$store_id = (store_module() && is_admin()) ? $store_id : get_current_store_id();	
		$account_number = trim($account_number);
		$query = $this->db->query("select * from db_bank_master where upper(account_number)=upper('$account_number') and store_id=$store_id");
		if($query->num_rows() > 0){
			return "This Account Number already exists.";
		}
		else{
			$info = array(
				'account_number'  => $account_number, 
				'branch_name'     => $branch_name,
				'iban_number'     => $iban_number,
				'bank_details'    => $bank_details,
				'opening_balance' => empty($opening_balance) ? 0 : $opening_balance,
				'status'          => 1,
				'created_date'    => date("Y-m-d"),
				'created_time'    => date("H:i:s"),
				'created_by'      => $this->session->userdata('inv_username'),
				'store_id'        => $store_id,
			);

			$q1 = $this->db->insert('db_bank_master', $info);
			if ($q1){
				$this->session->set_flashdata('success', 'Success!! New Bank Added Successfully!');
				return "success";
			}
			else{
				return "failed";
			}
		}
	}

	public function get_details($id, $data){
		$query = $this->db->query("select * from db_bank_master where upper(id)=upper('$id')");
		if($query->num_rows() == 0){
			show_404(); exit;
		}
		else{
			$query = $query->row();
			$data['q_id'] = $query->id;
			$data['account_number'] = $query->account_number;
			$data['branch_name'] = $query->branch_name;
			$data['iban_number'] = $query->iban_number;
			$data['bank_details'] = $query->bank_details;
			$data['opening_balance'] = $query->opening_balance;
			$data['store_id'] = $query->store_id;
			return $data;
		}
	}

	public function update_bank(){
		extract($this->security->xss_clean(html_escape(array_merge($this->data, $_POST))));

		$store_id = (store_module() && is_admin()) ? $store_id : get_current_store_id();	
		$account_number = trim($account_number);
		$query = $this->db->query("select * from db_bank_master where upper(account_number)=upper('$account_number') and id<>$q_id and store_id=$store_id");
		if($query->num_rows() > 0){
			return "This Account Number already exists.";
		}
		else{
			$info = array(
				'account_number'  => $account_number, 
				'branch_name'     => $branch_name,
				'iban_number'     => $iban_number,
				'bank_details'    => $bank_details,
				'opening_balance' => empty($opening_balance) ? 0 : $opening_balance,
				'store_id'        => $store_id,
			);

			$q1 = $this->db->where('id', $q_id)->update('db_bank_master', $info);
		
			if ($q1){
				$this->session->set_flashdata('success', 'Success!! Bank Updated Successfully!');
				return "success";
			}
			else{
				return "failed";
			}
		}
	}

	public function update_status($id, $status){
		if (set_status_of_table($id, $status, 'db_bank_master')){
			echo "success";
		}
		else{
			echo "failed";
		}
	}

	public function delete_bank_from_table($ids){
		$this->db->trans_begin();

		$this->db->where("id in ($ids)");
		if(!is_admin()){
			$this->db->where("store_id", get_current_store_id());
		}

		$query1 = $this->db->delete("db_bank_master");

		if ($query1){
			$this->db->trans_commit();
			echo "success";
		}
		else{
			echo "failed";
		}
	}
}
