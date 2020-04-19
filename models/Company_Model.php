<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Company_model extends CI_Model
{

	public function __construct()
	{
		parent::__construct();
	}

	public function getCompanyLists($search){
		if(is_null($search)){
			$search['name'] = 'datetime_created';
			$search['sorting'] = 'DESC';
			$search['filter_by_name'] = '';
			$search['filter_by_value'] = '';
		}
		if($search['filter_by_name'] == ''){
			$company = $this->db->select('*')->from('company c')
			->join('status s','c.status = s.status_id')
			->order_by('c.'.$search['name'],$search['sorting'])
			->get();
		}else{
			$company = $this->db->select('*')->from('company c')
			->join('status s','c.status = s.status_id')
			->where('c.id',$search['filter_by_value'])
			->order_by('c.'.$search['name'],$search['sorting'])->get();
		}
		http_response_code('200');
		if($company->num_rows() > 0){
			$data = array();
			foreach (($company->result()) as $row) {
				$data[] = $row;
			}
			echo json_encode(array( "status" => true, "message" => 'Success',"data" =>$data));exit;
		}else{
			echo json_encode(array( "status" => true, "message" => 'No result'));exit;
		}
	}
	
	public function getCompanyListsFilter($search){
		if(is_null($search)){
			$search['name'] = 'datetime_created';
			$search['sorting'] = 'DESC';
			$search['filter_by_name'] = '';
			$search['filter_by_value'] = '';
		}
		$company = $this->db->select('*')->from('company c')
		->join('status s','c.status = s.status_id')
		->like('c.sales_person',$search['filter_by_value'])
		->order_by('c.'.$search['name'],$search['sorting'])
		->get();
		http_response_code('200');
		if($company->num_rows() > 0){
			$data = array();
			foreach (($company->result()) as $row) {
				$data[] = $row;
			}
			echo json_encode(array( "status" => true, "message" => 'Success',"data" =>$data));exit;
		}else{
			echo json_encode(array( "status" => true, "message" => 'No result'));exit;
		}
	}

	public function getCompanyListsName($search){
		if(is_null($search)){
			$search['name'] = 'datetime_created';
			$search['sorting'] = 'DESC';
			$search['filter_by_name'] = '';
			$search['filter_by_value'] = '';
		}
		$company = $this->db->select('*')->from('company c')
		->join('status s','c.status = s.status_id')
		->like('c.company_name',$search['filter_by_value'])
		->order_by('c.'.$search['name'],$search['sorting'])
		->get();
		http_response_code('200');
		if($company->num_rows() > 0){
			$data = array();
			foreach (($company->result()) as $row) {
				$data[] = $row;
			}
			echo json_encode(array( "status" => true, "message" => 'Success',"data" =>$data));exit;
		}else{
			echo json_encode(array( "status" => true, "message" => 'No result'));exit;
		}
	}

	public function getCompanyById($id){
		if(is_null($id)){
			$id['filter_by_value'] = '';
		}
		$company = $this->db->where('c.company_id',$id['filter_by_value'])
					->from('company c')
					->join('status s','c.status = s.status_id','left')
					->join('payment_terms p','c.payment_terms = p.id','left')
					->get();
		if(is_null($company)){
			$this->show_404();
		}
		$details = $company->row();
		$acc_db = $this->load->database('account',TRUE);
		$data = array();
		foreach(json_decode($details->sales_person) as $sp){
			$row = $acc_db->select('user_id,name')->from('accounts')
			->where('user_id',$sp)
			->get();
			$data[] = $row->row();
		}
		$details->sales_name = json_encode($data);

		http_response_code('200');
		echo json_encode(array( "status" => true, "message" => 'Success',"data" =>$details));exit;
	}

	public function getRecordByLearnerId($id){
		$query = $this->db->get_where('learner',array('id'=>$id));
		if($query->num_rows() > 0){
			return $query->row();
		}else{
			$message = 'Learner Not found.';
			$this->show_error_404($message);
		}
	}

	public function getRecordByLearnerManagerId($id){
		$query = $this->db->get_where('learner_manager',array('id'=>$id));
		if($query->num_rows() > 0){
			return $query->row();
		}else{
			$message = 'Learner Manager Not found.';
			$this->show_error_404($message);
		}
	}

	public function updateAccountStatus($userData,$table){
		$update = $this->db->update($table,$userData,array('id' => $userData['id']));
		if($update){
			http_response_code('200');
			$message = 'Deactivated';
			$status = true;
			echo json_encode(array( "status" => $status, "message" => $message));exit;
		}else{
			$this->show_error_500();
		}
	}

	public function paymentTerms(){
		$res = $this->db->select('*')->from('payment_terms')->get();
		http_response_code('200');
		if($res->num_rows() > 0){
			$data = array();
			foreach (($res->result()) as $row) {
				$data[] = $row;
			}
			echo json_encode(array( "status" => true, "message" => 'Success', "data" => $data));exit;
		}else{
			$this->show_error_404();
		}
	}

	private function show_error_404($message){
		http_response_code('404');
		echo json_encode(array( "status" => false, "message" => $message));exit;
	}

	private function show_404(){
		http_response_code('404');
		echo json_encode(array( "status" => false, "message" => 'Company Not Found.'));exit;
	}

}
