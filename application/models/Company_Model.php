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
			->where('c.status != 0')
			->order_by('c.'.$search['name'],$search['sorting'])
			->get();
		}else{
			$company = $this->db->select('*')->from('company c')
			->join('status s','c.status = s.status_id')
			->where('c.id',$search['filter_by_value'])
			->join('status s','c.status = s.status_id')
			->where('c.status != 0')
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
			echo json_encode(array( "status" => true, "message" => 'No result', "data"=>array()));exit;
		}
	}

	public function getCompanyListsBySales($search){
		if(is_null($search)){
			$search['name'] = 'datetime_created';
			$search['sorting'] = 'DESC';
			$search['filter_by_name'] = '';
			$search['filter_by_value'] = '';
		}
		if($search['filter_by_name'] == ''){
			$company = $this->db->select('*')->from('company c')
			->join('status s','c.status = s.status_id')
			->where('c.status != 0')
			->order_by('c.'.$search['name'],$search['sorting'])
			->get();
		}else{
			$company = $this->db->select('*')->from('company c')
			->join('status s','c.status = s.status_id')
			->where('c.id',$search['filter_by_value'])
			->join('status s','c.status = s.status_id')
			->where('c.status != 0')
			->order_by('c.'.$search['name'],$search['sorting'])->get();
		}
		http_response_code('200');
		if($company->num_rows() > 0){
			$data = array();
			foreach (($company->result()) as $row) {
				foreach(json_decode($row->sales_person)[0] as $sp){
					if($sp == $search['filter_by_value']){
						$data[] = $row;
						continue;
					}
				}
			}
			echo json_encode(array( "status" => true, "message" => 'Success',"data" =>$data));exit;
		}else{
			echo json_encode(array( "status" => true, "message" => 'No result', "data"=>array()));exit;
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
			echo json_encode(array( "status" => true, "message" => 'No result', "data"=>array()));exit;
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
			echo json_encode(array( "status" => true, "message" => 'No result', "data"=>array()));exit;
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
			if($dt->num_rows() > 0){
				$res = $dt->row();
				$dateRes = date_create($res->datetime_created);
				$date = date_format($dateRes, 'd-m-y');
				$datetime[] = $date;
			}else{
				$datetime[] = "";
			}
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
				->join('application_status as','a.status = as.application_status_id','left')
				->join('learner l','a.learner_id = l.learner_id', 'left')
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

				$company_name = $this->db->select('company_name')->from('company')
								->where('company_id', $row->company_id)
								->get()->row();
				$row->company_name = "";
				if(!is_null($company_name)){
					$row->company_name = $company_name->company_name;
				}

				$course = $courses_db->select('*')->from('courses c')
						->join('trade_type tt','c.trade_type = tt.id','left')
						->join('trade_level tl','c.trade_level = tl.id','left')
						->where('c.id',$row->course_id)
						->get()->row();
				$row->course_name = $course->course_name;
				$row->trade_type_name = $course->trade_type_name;
				$row->trade_level_name = $course->trade_level_name;

				$personnel = $account_db->select('*')->from('accounts a')
						->where('a.user_id',$row->created_by)
						->get()->row();
				$row->personnel = $personnel->name;

				$applications[] = $row;
			}
		}
		
		if(empty($applications)){
			http_response_code('200');
			echo json_encode(array( "status" => false, "message" => 'No Rows Found',"data" =>array()));exit;
		}else{
			http_response_code('200');
			echo json_encode(array( "status" => true, "message" => 'Success',"data" =>$applications));exit;
		}
	}

	public function filterApplications($searchKeyword){
		$courses_db = $this->load->database('courses',true);
		$account_db = $this->load->database('account',true);

		$this->db->select('*, a.id as id, a.company_id as company_id
		,a.datetime_created as datetime_created')
		->from('application a')
		->join('company cy','a.company_id = cy.company_id','left')
		->join('application_status as','a.status = as.application_status_id','left');
		if($searchKeyword['filter_column'] == 'company'){
			$this->db->where('a.company_id', $searchKeyword['filter_value']);
		}
		if($searchKeyword['filter_column'] == 'status'){
			$this->db->where('a.status', $searchKeyword['filter_value']);
		}
		if($searchKeyword['filter_column'] == 'sales'){
			$this->db->where('a.created_by', $searchKeyword['filter_value']);
		}
		$this->db->order_by('a.datetime_created','desc');
		$res = $this->db->get();
		$applications = array();
		if($res->num_rows() > 0){
			foreach($res->result() as $row){
				$newDate = $row->datetime_created;
				$newDate = new DateTime($newDate);
				$date = $newDate->format('d-m-Y');
				$time = $newDate->format('H:i');
				$row->date = $date;
				$row->time = $time.' HR';

				$courses_db->select('c.course_name, tt.trade_type_name')
				->from('courses c')
				->join('trade_type tt','c.trade_type = tt.id', 'left')
				->where('c.id', $row->course_id);
				if($searchKeyword['filter_column'] == 'category'){
					$courses_db->where('c.trade_type', $searchKeyword['filter_value']);
				}
				if($searchKeyword['filter_column'] == 'course'){
					$courses_db->where('c.id', $searchKeyword['filter_value']);
				}
				$course_res = $courses_db->get();
				if($course_res->num_rows() == 0){
					$applications = array();
					echo json_encode(array("status" => false, "message" => 'No Rows Found1',"data" => $applications));
					exit;
				}else{
					$course_res = $course_res->row();
				}
				$personnel_res = $account_db->select('*')->from('accounts a')
						->where('a.user_id',$row->created_by)
						->get()->row();
				if(empty($personnel_res)){
					$applications = array();
					echo json_encode(array("status" => false, "message" => 'No Rows Found2',"data" => $applications));
					exit;
				}
				$learner = $this->db->select('name as learner_name')->from('learner')
								->where('learner_id', $row->learner_id)
								->get()->row();
				$row->name = $learner->learner_name;
				$row->course_name = $course_res->course_name;
				$row->trade_type_name = $course_res->trade_type_name;
				$row->personnel = $personnel_res->name;
				$applications[] = $row;
			}
		}
		if(empty($applications)){
			http_response_code('200');
			echo json_encode(array( "status" => false, "message" => 'No Rows Found3',"data" =>array()));exit;
		}else{
			http_response_code('200');
			echo json_encode(array( "status" => true, "message" => 'Success',"data" =>$applications));exit;
		}
	}

	private function checkUENExists($uen){
		$co = $this->db->select('company_id')->from('company')
				->where('uen',$uen)->get();
		$hash = null;
		if($co->num_rows > 0){
			$hash = $co->row();
			$hash = $hash->company_id;
		}	
		return $hash;
	}

	public function newApplication($applicationData)
	{
		$uenExists = $this->checkUENExists($applicationData['uen']);
		$UENHash = $uenExists;
		if(is_null($uenExists)){
			$UENHash = hash('sha256',$applicationData['uen']);
		}
		//learner
		$learner_data['learner_id'] = hash('sha256',$applicationData['applicantNRIC']);
		if($applicationData['applicantNRIC'] == "-"){
			$learner_data['learner_id'] = hash('sha256',$applicationData['applicantFIN']);
		}
		$learner_data['name'] = $applicationData['applicantName'];
		$learner_data['nric'] = $applicationData['applicantNRIC'];
		$learner_data['work_permit'] = $applicationData['applicantWorkPermit'];
		$learner_data['fin'] = $applicationData['applicantFIN'];
		$learner_data['nationality'] = $applicationData['nationality'];
		$learner_data['dob'] = $applicationData['dob'];
		$learner_data['sex'] = $applicationData['sex'];
		$learner_data['contact_no'] = $applicationData['applicantHP'];
		$learner_data['coretrade_expiry'] = $applicationData['ctexp'];
		$learner_data['coretradeRegNo'] = $applicationData['coretradeRegNo'];
		$learner_data['ANExpiry'] = $applicationData['ANExpiry'];
		$learner_data['company'] = $UENHash;
		$learnerExists = $this->db->select("learner_id")->from("learner")
			->where("nric", $applicationData['applicantNRIC'])
			->where("nric !=", "-")
			->get()->num_rows();
		if($learnerExists <= 0){
			$learner = $this->db->insert('learner',$learner_data);
		}

		//company if new company name exists
		if(isset($applicationData['newCompanyName'])){
			$co_data['company_id'] = $UENHash;
			$co_data['company_name'] = $applicationData['newCompanyName'];
			$co_data['uen'] = $applicationData['uen'];
			$co_data['contact_person'] = $applicationData['contactPerson'];
			$co_data['contact_number'] = $applicationData['contactPersonHP'];
			$co_data['fax'] = $applicationData['contactPersonFax'];
			$co_data['contact_email'] = $applicationData['contactPersonEmail'];
			$co_data['postal_code'] = $applicationData['postal'];
			$co_data['street'] = $applicationData['street'];
			$co_data['unit'] = $applicationData['unit'];
			$coExists = $this->db->select("company_id")->from("company")
						->where("uen", $applicationData['uen'])
						->get()->num_rows();
			if($coExists <= 0){
				$company = $this->db->insert('company', $co_data);
			}
		}

		//application table
		$app_data['application_id'] = md5(date("Y/m/d h:i:s"),false);
		$app_data['invoice_id'] = $applicationData['quotation'];
		$app_data['course_id'] = $applicationData['course'];
		$app_data['learner_id'] = $learner_data['learner_id'];
		$app_data['company_id'] = $UENHash;
		if($applicationData['nricCopy'] != ""){
			$app_data['photocopy_id'] =  hash('sha256',$applicationData['nricCopy']);
		}
		if($applicationData['cet'] != ""){
			$app_data['cet_acknowledgement'] = hash('sha256',$applicationData['cet']);
		}
		if($applicationData['fullPayment'] != ""){
			$app_data['receipt'] = hash('sha256',$applicationData['fullPayment']);
		}
		$app_data['full_payment'] = $applicationData["fullPaymentChecked"];
		if($app_data['full_payment'] == true){
			$app_data['status'] = 3;
		}
		$app_data['created_by'] = $applicationData['createdBy'];
		if(isset($applicationData['sponsorCompany'])){
			$app_data['sponsor_company'] = $applicationData['sponsorCompany'];
		}
		$app_data['trade_type'] = $applicationData['trade_type'];
		$app_data['application_options'] = $applicationData['application_option'];
		$application = $this->db->insert('application', $app_data);
		
		http_response_code('200');
		echo json_encode(array( "status" => true, "message" => 'Success',"data" =>$application));exit;
	}

	public function updateApplication($applicationData)
	{
		$applicationID = $applicationData['application_id'];
		unset($applicationData['application_id']);
		$application = $this->db->select("*")->from("application")
						->where("application_id", $applicationID)
						->get()->row();
		//learner
		$learner_data['learner_id'] = hash('sha256',$applicationData['applicantNRIC']);
		$learner_data['name'] = $applicationData['applicantName'];
		$learner_data['nric'] = $applicationData['applicantNRIC'];
		$learner_data['work_permit'] = $applicationData['applicantWorkPermit'];
		$learner_data['fin'] = $applicationData['applicantFIN'];
		$learner_data['nationality'] = $applicationData['nationality'];
		$learner_data['dob'] = $applicationData['dob'];
		$learner_data['sex'] = $applicationData['sex'];
		$learner_data['contact_no'] = $applicationData['applicantHP'];
		$learner_data['coretrade_expiry'] = $applicationData['ctexp'];
		$learner_data['coretradeRegNo'] = $applicationData['coretradeRegNo'];
		$learner_data['ANExpiry'] = $applicationData['ANExpiry'];
		$this->db->where("learner_id", $application->learner_id);
		$update = $this->db->update('learner',$learner_data);
		//application table
		$app_data['learner_id'] = $learner_data['learner_id'];
		if($applicationData['nricCopy'] != ""){
			$app_data['photocopy_id'] =  hash('sha256',$applicationData['nricCopy']);
		}
		if($applicationData['cet'] != ""){
			$app_data['cet_acknowledgement'] = hash('sha256',$applicationData['cet']);
		}
		if($applicationData['fullPayment'] != ""){
			$app_data['receipt'] = hash('sha256',$applicationData['fullPayment']);
		}
		$app_data['full_payment'] = $applicationData["fullPaymentChecked"];
		if($app_data['full_payment'] == true){
			$app_data['status'] = 3;
		}
		if(isset($applicationData['sponsorCompany'])){
			$app_data['sponsor_company'] = $applicationData['sponsorCompany'];
		}
		$this->db->where('application_id', $applicationID);
		$application = $this->db->update('application', $app_data);
		
		http_response_code('200');
		echo json_encode(array( "status" => true, "message" => 'Success',"data" =>$application));exit;
	}

	public function getApplication($application){
		$courses_db = $this->load->database('courses', true);
		$sales_db = $this->load->database('sales', true);
		$application = $this->db->select('*,l.name as applicant_name,c.id as co_id
						,a.sponsor_company as sco_id,a.sponsor_company as sco_id
						,wpt.name as work_permit_type_name')
						->from('application a')
						->join('learner l','a.learner_id = l.learner_id','left')
						->join('company c','a.company_id = c.company_id','left')
						->join('work_permit_types wpt','l.work_permit_type = wpt.id','left')
						->where('application_id',$application['application_id'])
						->get()->row();
		$course_name = $courses_db->select('course_name')->from('courses')
						->where('id',$application->course_id)->get()->row();
		if(isset($application->quotation_id) && !is_null($application->quotation_id)){
			$quotation_id = $sales_db->select('id')->from('quotations')
							->where('quotation_id',$application->quotation_id)->get()->row();
		}
		$application->q_id = null;
		if(isset($quotation_id)){
			$application->q_id = $quotation_id->id;
		}
		if($application->sponsor_company != "0"){
			$sponsor_company_name = $this->db->select("company_name")->from("company")
													->where("company_id", $application->sponsor_company)
													->get()->row();
			$application->sponsor_company_name = $sponsor_company_name->company_name;
		}
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
			echo json_encode(array( "status" => $status, "message" => $message, "data"=>array()));exit;
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
		http_response_code('200');
		echo json_encode(array( "status" => false, "message" => 'No Content Found.', "data"=>array()));exit;
	}

	private function show_error_404($message){
		http_response_code('200');
		echo json_encode(array( "status" => false, "message" => $message, "data"=>array()));exit;
	}

	private function show_404(){
		http_response_code('200');
		echo json_encode(array( "status" => false, "message" => 'Company Not Found.', "data"=>array()));exit;
	}

}
