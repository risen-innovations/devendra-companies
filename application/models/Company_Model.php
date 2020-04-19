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
		//get sales persons
		$acc_db = $this->load->database('account',TRUE);
		$sales_names = array();
		$datetime = array();
		foreach(json_decode($details->sales_person)[0] as $sp){
			$row = $acc_db->select('user_id,name')->from('accounts')
					->where('user_id',$sp)
					->get();
			$sales_names[] = $row->row();
			$dt = $this->db->select('datetime_created')->from('sales_assigned_log')
					->where('salesperson_id',$sp)
					->where('company_id',$id['filter_by_value'])
					->get();
			$res = $dt->row();
			$dateRes = date_create($res->datetime_created);
			$date = date_format($dateRes, 'd-m-y');
			$datetime[] = $date;
		}
		$details->sales_name = json_encode($sales_names);
		$details->sales_assigned = json_encode($datetime);

		http_response_code('200');
		echo json_encode(array( "status" => true, "message" => 'Success',"data" =>$details));exit;
	}

	public function checkUEN($UEN){
		$sql = $this->db->select('uen')->from('company')
		->where('uen',$UEN)->get();
		$check = $sql->num_rows();
		if($check > 0){
			return true;
		}else{
			return false;
		}
	}

	public function applications(){
		$courses_db = $this->load->database('courses',true);
		$account_db = $this->load->database('account',true);

		$res = $this->db->select('*, a.id as id, a.company_id as company_id
				,a.datetime_created as datetime_created')
				->from('application a')
				->join('company cy','a.company_id = cy.company_id','left')
				->join('application_status as','a.status = as.application_status_id','left')
				->order_by('a.datetime_created','desc')
				->get();
		$applications = array();

		if($res->num_rows() > 0){
			foreach($res->result() as $row){
				$newDate = $row->datetime_created;
				$newDate = new DateTime($newDate);
				$date = $newDate->format('d-m-Y');
				$time = $newDate->format('H:i');
				$row->date = $date;
				$row->time = $time.' HR';

				$course = $courses_db->select('*')->from('courses c')
						->join('trade_type tt','c.trade_type = tt.id','left')
						->where('c.id',$row->course_id)
						->get()->row();
				$row->course_name = $course->course_name;
				$row->trade_type_name = $course->trade_type_name;

				$personnel = $account_db->select('*')->from('accounts a')
						->where('a.user_id',$row->created_by)
						->get()->row();
				$row->personnel = $personnel->name;

				$applications[] = $row;
			}
		}
		
		if(empty($applications)){
			$this->show_204();
		}else{
			http_response_code('200');
			echo json_encode(array( "status" => true, "message" => 'Success',"data" =>$applications));exit;
		}
	}

	public function newApplication($applicationData)
	{
		//learner
		$learner_data['learner_id'] = hash('sha256',$applicationData['applicantNRIC']);
		$learner_data['name'] = $applicationData['applicantName'];
		$learner_data['nric'] = $applicationData['applicantNRIC'];
		$learner_data['work_permit'] = $applicationData['applicantWorkPermit'];
		$learner_data['nationality'] = $applicationData['nationality'];
		$learner_data['dob'] = $applicationData['dob'];
		//$learner_data['age'] = $applicationData['age'];
		$learner_data['sex'] = $applicationData['sex'];
		$learner_data['contact_no'] = $applicationData['applicantHP'];
		$learner_data['coretrade_expiry'] = $applicationData['ctexp'];
		$learner = $this->db->insert('learner',$learner_data);

		//company if new company name exists
		if(isset($applicationData['newCompanyName'])){
			$co_data['company_id'] = hash('sha256',$applicationData['uen']);
			$co_data['company_name'] = $applicationData['newCompanyName'];
			$co_data['uen'] = $applicationData['uen'];
			$co_data['contact_person'] = $applicationData['contactPerson'];
			$co_data['contact_number'] = $applicationData['contactPersonHP'];
			$co_data['fax'] = $applicationData['contactPersonFax'];
			$co_data['contact_email'] = $applicationData['contactPersonEmail'];
			$co_data['postal_code'] = $applicationData['postal'];
			$co_data['street'] = $applicationData['street'];
			$co_data['unit'] = $applicationData['unit'];
			$company = $this->db->insert('company', $co_data);
		}

		//application table
		$company_id = $co_data['company_id'] ?: $applicationData['company'];
		$app_data['application_id'] = md5(date("Y/m/d h:i:s"),false);
		$app_data['quotation_id'] = $applicationData['quotation'];
		$app_data['course_id'] = $applicationData['course'];
		$app_data['learner_id'] = $learner_data['learner_id'];
		$app_data['company_id'] = $company_id;
		$app_data['photocopy_id'] =  hash('sha256',$applicationData['nricCopy']);
		$app_data['cet_acknowledgement'] = hash('sha256',$applicationData['cet']);
		$app_data['receipt'] = hash('sha256',$applicationData['fullPayment']);
		$app_data['full_payment'] = $applicationData["fullPaymentChecked"];
		if($app_data['full_payment'] == true){
			$app_data['status'] = 3;
		}
		$app_data['created_by'] = $applicationData['createdBy'];
		$application = $this->db->insert('application', $app_data);
		
		http_response_code('200');
		echo json_encode(array( "status" => true, "message" => 'Success',"data" =>$application));exit;
	}

	public function getApplication($application){
		$courses_db = $this->load->database('courses', true);
		$application = $this->db->select('*,l.name as applicant_name,c.id as co_id')->from('application a')
						->join('learner l','a.learner_id = l.learner_id','left')
						->join('company c','a.company_id = c.company_id','left')
						->where('application_id',$application['application_id'])
						->get()->row();
		$course_name = $courses_db->select('course_name')->from('courses')
						->where('id',$application->course_id)->get()->row();
		$application->course_name = $course_name->course_name;
		http_response_code('200');
		echo json_encode(array( "status" => true, "message" => 'Success',"data" =>$application));exit;
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

	private function show_204(){
		http_response_code('204');
		echo json_encode(array( "status" => false, "message" => 'No Content Found.'));exit;
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