<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/CreatorJwt.php';
require APPPATH . '/libraries/AWSS3.php';

class Company extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model('company_model');
		$this->objOfJwt = new CreatorJwt();
		$this->AWSS3 = new AWSS3();
		header('Content-Type: application/json');
	}

	public function index(){
		$validToken = $this->validToken();
		$this->setAuditLog($validToken);
		$data = file_get_contents('php://input');
		$search = json_decode($data,true);
		$auditLog = $this->audit_model->getRecordsBySorting($search);
	}

	public function companyList(){
		$validToken = $this->validToken();
		$data = file_get_contents('php://input');
		$search = json_decode($data,true);
		$this->setAuditLog($validToken,15);
		if(is_null($search)){
			$this->show_400();
		}
		$company = $this->company_model->getCompanyLists($search);
	}

	public function companyListFilter(){
		$validToken = $this->validToken();
		$data = file_get_contents('php://input');
		$search = json_decode($data,true);
		$this->setAuditLog($validToken,15);
		if(is_null($search)){
			$this->show_400();
		}
		$company = $this->company_model->getCompanyListsFilter($search);
	}

	public function companyListName(){
		$validToken = $this->validToken();
		$data = file_get_contents('php://input');
		$search = json_decode($data,true);
		$this->setAuditLog($validToken,15);
		if(is_null($search)){
			$this->show_400();
		}
		$company = $this->company_model->getCompanyListsName($search);
	}

	public function getInactiveCompanies(){
		$validToken = $this->validToken();
		$sales_db = $this->load->database("sales", true);
		$companies = $this->db->select('id, company_id, company_name, uen')->from('company')
						->get()->result();
		$inactiveCompanies = [];
		$threshold = strtotime("-90 days");
		foreach($companies as $company){
			$last_date = $sales_db->select('datetime_created')->from('invoices')
							->where('company_id', $company->company_id)
							->order_by('datetime_created', 'desc')
							->limit(1)
							->get()->row();
			if(!is_null($last_date)){
				if($last_date->datetime_created >= $threshold){
					$inactiveCompanies[] = array(
												"company_id" => $company->company_id,
												"company_name" => $company->company_name,
												"uen" => $company->uen,
												"last_date" => $last_date->datetime_created
											);
				}
			}else{
				$inactiveCompanies[] = array(
					"company_id" => $company->company_id,
					"company_name" => $company->company_name,
					"uen" => $company->uen,
					"last_date" => null
				);
			}
		}
		http_response_code('200');
		echo json_encode(array("status" => true,"message" => "","data" => $inactiveCompanies));exit;
	}

	public function getCategories(){
		$validToken = $this->validToken();
		$courses_db = $this->load->database('courses',TRUE);
		$sql = $courses_db->select('id, trade_type_name')->from('trade_type')
				->order_by('trade_type_name','asc')->get();
		$cats = array();
		foreach($sql->result() as $cat){
			$cats[] = $cat;
		}
		if(is_null($cats)){
			$this->show_400();
		}else{
			http_response_code('200');
			echo json_encode(array( "status" => true, "message" => 'Success',"data" =>$cats));exit;
		}
	}

	public function getCourses(){
		$validToken = $this->validToken();
		$courses_db = $this->load->database('courses',TRUE);
		$sql = $courses_db->select('id, course_name')->from('courses')
				->order_by('course_name','asc')->get();
		$courses = array();
		foreach($sql->result() as $course){
			$courses[] = $course;
		}
		if(is_null($courses)){
			$this->show_400();
		}else{
			http_response_code('200');
			echo json_encode(array( "status" => true, "message" => 'Success',"data" =>$courses));exit;
		}
	}

	public function getCompaniesByRole(){
		$validToken = $this->validToken();
		$data = file_get_contents('php://input');
		$search = json_decode($data,true);
		$accounts_db = $this->load->database('account', true);
		$role = $accounts_db->select("role")->from("accounts")
				->where("user_id", $search['filter_by_value'])
				->get()->row();
		
		$sql = $this->db->select('company_id, company_name, sales_person')->from('company')
				->order_by('company_name','asc')->get();
		$companies = array();
		foreach($sql->result() as $co){
			if(!in_array($role->role, array(1, 2, 3, 5, 9, 10))){
				$exists =  strstr($co->sales_person, $search['filter_by_value']);
				//echo $exists;exit(0);
				if($exists != ""){
					$companies[] = $co;
				}
			}else{
				$companies[] = $co;
			}
		}
		if(is_null($companies)){
			http_response_code('200');
			echo json_encode(array( "status" => false, "message" => 'failure',"data" =>null));exit;
		}else{
			http_response_code('200');
			echo json_encode(array( "status" => true, "message" => 'Success',"data" =>$companies));exit;
		}
	}

	public function getCompanies(){
		$validToken = $this->validToken();
		$sql = $this->db->select('company_id, company_name')->from('company')
				->order_by('company_name','asc')->get();
		$companies = array();
		foreach($sql->result() as $co){
			$companies[] = $co;
		}
		if(is_null($companies)){
			$this->show_400();
		}else{
			http_response_code('200');
			echo json_encode(array( "status" => true, "message" => 'Success',"data" =>$companies));exit;
		}
	}

	private function getSelectedLearners($iid){
		$sales_db = $this->load->database("sales", TRUE);
		$existing = $sales_db->select("learner_id")->from("invoice_items_learners")
					->where("invoice_items_id", $iid)
					->get()->result();
		$selected = array();
		foreach($existing as $e){
			$selected[] = $e->learner_id;
		}
		return $selected;
	}

	public function getLearnersUnderCompany(){
		$validToken = $this->validToken();
		$data = file_get_contents('php://input');
		$company = json_decode($data,true);
		$applications = $this->db->select('a.learner_id, l.name, l.nric, l.work_permit, l.fin')
						->from('application a')
						->where('a.company_id', $company['company_id'])
						->join("learner l","a.learner_id = l.learner_id","left")
						->group_by('l.learner_id')
						->get()->result();
		echo $this->db->last_query();exit(0);
		if(!is_null($applications)){
			http_response_code("200");
			echo json_encode(array("status" => true, "message" => "Learners Found", "data" => $applications));
		}else{
			http_response_code("200");
			echo json_encode(array("status" => false
			, "message" => "No Learners found","data"=>null));exit;
		}
	}

	public function getCompanyLearners(){
		$validToken = $this->validToken();
		$data = file_get_contents('php://input');
		$company = json_decode($data,true);
		$learners = $this->db->select('*')->from('learner')
					->where('company', $company['company_id'])
					->get();
		$data = array();
		if($learners->num_rows() > 0){
			//filter learners to application
			$courses = $this->db->select("learner_id")->from("application")
						->where('invoice_id', $company['invoice_id'])
						->where('company_id', $company['company_id'])
						->where('course_id', $company['course_id'])
						->get()->result();
			$registeredLearners = array();
			foreach($courses as $course){
				$registeredLearners[] = $course->learner_id;
			}
			//get selected learners
			$selected = $this->getSelectedLearners($company['invoice_items_id']);
			foreach($learners->result() as $row){
				if(in_array($row->learner_id, $registeredLearners)){
					$maskedNric = $this->mask($row->nric);
					$row->nric = $maskedNric;
					$maskedWP = $this->mask($row->work_permit);
					$row->work_permit = $maskedWP;
					$row->selected = FALSE;
					if(in_array($row->learner_id, $selected)){
						$row->selected = TRUE;
					}
					$data[] = $row;
				}
			}
			if(!empty($data)){
				http_response_code('200');
				echo json_encode(array( "status"=> true, "message" => "Learners Retrieved", "data"=>$data));exit;
			}else{
				http_response_code('200');
				echo json_encode(array( "status"=> false, "message" => "No Learners Found","data"=>null));exit;
			}
		}else{
			http_response_code('200');
			echo json_encode(array( "status"=> false, "message" => "No Learners Found","data"=>null));exit;
		}
	}

	public function deactivateCompany(){
		$validToken = $this->validToken();
		$data = file_get_contents('php://input');
		$deactivate = json_decode($data,true);
		$this->db->where('company_id',$deactivate['company_id']);
		$this->db->update('company',array('status' => 0));
		http_response_code('200');
		echo json_encode(array("status" => true, "message" => "Company Deleted", "data"=>null));exit;
	}

	public function getSalespersons(){
		$validToken = $this->validToken();
		$account_db = $this->load->database('account', TRUE);
		$sql = $account_db->select('user_id, name')->from('accounts')
				->order_by('name','asc')->get();
		$salespersons = array();
		foreach($sql->result() as $salesperson){
			$salespersons[] = $salesperson;
		}
		if(is_null($salespersons)){
			$this->show_400();
		}else{
			http_response_code('200');
			echo json_encode(array( "status" => true, "message" => 'Success',"data" =>$salespersons));exit;
		}
	}

	public function getStatuses(){
		$validToken = $this->validToken();
		$sql = $this->db->select('application_status_id, application_status_name'
				)->from('application_status')
				->order_by('application_status_name','asc')->get();
		$statuses = array();
		foreach($sql->result() as $status){
			$statuses[] = $status;
		}
		if(is_null($statuses)){
			$this->show_400();
		}else{
			http_response_code('200');
			echo json_encode(array( "status" => true, "message" => 'Success',"data" =>$statuses));exit;
		}
	}

	public function autofill(){
		$validToken = $this->validToken();
		$data = file_get_contents('php://input');
		$company = json_decode($data, true);
		$sql = $this->db->select('*')->from('company')
				->where('company_id',$company['company_id'])
				->get()->row();
		http_response_code('200');
		echo json_encode(array( "status" => true, "message" => 'Success',"data" =>$sql));exit;
	}

	public function view(){
		$validToken = $this->validToken();
		$this->setAuditLog($validToken,16);
		$data = file_get_contents('php://input');
		$company = json_decode($data, true);
		if(is_null($company)){
			$this->show_400();
		}
		$id = $this->company_model->getCompanyById($company);
		if(is_null($id)){
			$this->show_404();
		}	
	}

	public function addCompany(){
		$validToken  = $this->validToken();
		$data = file_get_contents('php://input');
		$companyData = json_decode($data,true);
		if(is_null($companyData)){
			$this->show_400();
		}
		$exists = $this->db->select('uen')->from('company')
					->where('uen', $companyData['uen'])
					->get();
		if($exists->num_rows() > 0){
			http_response_code('200');
			echo json_encode(array('status' => false, 'message' => 'UEN already exists', "data" =>null));exit;
		}
		$companyData['company_id'] = hash('sha256',$companyData['uen']);
		//$companyData['sales_person'] = implode(",",$companyData['sales_person']);
		$this->db->insert('company',$companyData);
		$insert_id = $this->db->insert_id();
		$sales = json_decode($companyData['sales_person']);
		foreach($sales[0] as $person){
			$this->db->insert('sales_assigned_log',array("salesperson_id" => $person
			, "company_id" => $companyData['company_id']));
		}
		if($insert_id){
			$this->setAuditLog($validToken,25);
			http_response_code('200');
			$data =  $this->db->select('*')->get_where('company',array('id'=>$insert_id))->row();
			echo json_encode(array( "status" => true, "message" => 'Success',"data" =>$data));exit;
		}else{
			http_response_code('200');
			echo json_encode(array('status' => false, 'message' => 'Failed to create new company. Please contact system admin.', "data"=>null));
		}
	}

	public function updateCompany(){
		$validToken  = $this->validToken();
		$data = file_get_contents('php://input');
		$body = json_decode($data,true);
		if(is_null($body)){
			$this->show_400();
		}
		//update company table
		$this->db->where('company_id', $body['company_id'])->update('company', $body);
		//get sales_assigned_log table records
		$test = [];
		$original = [];
		$all_co_salespersons = $this->db->select('salesperson_id')->from('sales_assigned_log')
								->where('company_id', $body['company_id'])
								->get();
		foreach($all_co_salespersons->result() as $sp){
			$original[] = $sp->salesperson_id;
		}
		foreach(json_decode($body['sales_person'])[0] as $sp){
			$exists = $this->db->select('salesperson_id')->from('sales_assigned_log')
			->where('salesperson_id', $sp)
			->where('company_id', $body['company_id'])
			->get();
			if($exists->num_rows() <= 0){
				$this->db->insert('sales_assigned_log',array('salesperson_id'=>$sp,'company_id'=>$body['company_id']));
			}
			/*if(!in_array($sp ,$original)){
				$this->db->where('company_id',$body['company_id']);
				$this->db->where('salesperson_id',$sp);
				$this->db->delete('sales_assigned_log');
			}*/
		}
		http_response_code('200');
		echo json_encode(array( "status" => true, "message" => 'Success',"data" => $original ));exit;
	}

	//list all the applications on http://localhost:3000/app/sales/applications
	public function applications(){
		$validToken  = $this->validToken();
		$applications = $this->company_model->applications();
	}

	public function filterApplications(){
		$validToken  = $this->validToken();
		$data = file_get_contents('php://input');
		$searchKeyword = json_decode($data,true);
		$applications = $this->company_model->filterApplications($searchKeyword);
	}

	public function checkUEN(){
		$data = file_get_contents('php://input');
		$uen = json_decode($data,true);
		if(is_null($uen)){
			http_response_code('200');
			echo json_encode(array( "status" => false, "message" => 'Bad Request',"data"=>null));exit;
		}else{
			$validToken = $this->validToken();
			$notExists = $this->company_model->checkUEN($uen['uen']);
			if($notExists === true){
				http_response_code('200');
				echo json_encode(array( "status" => true, "message" => "Success", "data"=>null));exit;
			}else{
				http_response_code('200');
				echo json_encode(array( "status" => false, "message" => "UEN not found", "data"=>null));exit;
			}
		}
	}

	public function latestApplication(){
		$validToken = $this->validToken();
		$data = file_get_contents('php://input');
		$company = json_decode($data,true);
		$latest = $this->db->select("datetime_created as date")->from("application")
					->where("company_id", $company["company_id"])
					->order_by("datetime_created desc")->limit(1)->get()->row();
		if(is_null($latest)){
			http_response_code('200');
			echo json_encode(array("status" => false, "message" => "No applications found", "data"=>null));exit;
		}else{
			http_response_code('200');
			echo json_encode(array( "status" => true, "message" => 'Latest Application on '.$latest->date
			, "data" => $latest->date));exit;
		}
	}

	public function getExpiringCoreTrades(){
		$validToken = $this->validToken();
		$data = file_get_contents('php://input');
		if(is_null($data)){
			http_response_code('200');
			echo json_encode(array( "status" => false, "message" => 'Bad Request', "data" =>null));exit;
		}
		$learner =  json_decode($data,true);
		$expiring = $this->db->select("a.company_id, c.company_name, a.learner_id
						, l.name as learner_name, a.course_id, a.datetime_updated")
						->from("application a")
						->join("learner l","a.learner_id = l.learner_id","left")
						->join("company c","c.company_id = a.company_id","left")
						->where("a.status", 2) //@todo may need to revise this once learners_results are ready
						->order_by("a.datetime_updated","desc")
						->get()->result();
		$exp = array();
		if(!is_null($expiring)){
			$courses_db = $this->load->database('courses', true);
			foreach($expiring as $e){
				$course = $courses_db->select("course_name")->from("courses")
								->where('id',$e->course_id)->get()->row();
				$e->course_name = $course->course_name;
				$date = date_create(date("Y-m-d", strtotime($e->datetime_updated)));
				$target = date_create(date("Y-m-d", strtotime("-21 months")));
				$diff = date_diff($date, $target);
				$diff = $diff->days;
				if($diff < 588){
					$exp[] = $e;
				}
			}
			http_response_code('200');
			echo json_encode(array("status" => true, "message" => "Fetched Records Successfully"
			, "data"=>$exp));
		}
	}

	public function newApplication(){
		$validToken = $this->validToken();
		$data = file_get_contents('php://input');

		if(is_null($data)){
			http_response_code('200');
			echo json_encode(array( "status" => false, "message" => 'Bad Request', "data"=>null));exit;
		}else{
			//echo json_encode(array( "status" => false, "message" => $data));exit;

			$applicationData = json_decode($data,true);
			$bucket = 'ri-company-service';
			//$this->setAuditLog($validToken,45);
			$nricCopy = $applicationData['nricCopy'];
			$cet = $applicationData['cet'];
			$receipt = $applicationData['fullPayment'];
			if($nricCopy != ""){
				$data = $nricCopy;
				list($type, $data) = explode(';', $data);
				list(, $data)      = explode(',', $data);
				$data = base64_decode($data);
				$randomId = uniqid();
				$imageName = $randomId.'.jpg';
				$_FILES['image']['name'] = 'learners/nric/'.$imageName;
				$_FILES['image']['tmp_name'] = $data;
				$this->AWSS3->uploadS3($_FILES, $bucket);
				$applicationDoc['filepath'] = 'learners/nric/'.$imageName;
				$applicationDoc['application_doc_type'] = 1;
				$applicationDoc['application_doc_id'] = hash('sha256',$imageName);
				$this->db->insert('application_doc',$applicationDoc);
			}
			if($cet != ""){
				$data = $cet;
				list($type, $data) = explode(';', $data);
				list(, $data)      = explode(',', $data);
				$data = base64_decode($data);
				$randomId = uniqid();
				$imageName = $randomId.'.jpg';
				$_FILES['image']['name'] = 'learners/cet/'.$imageName;
				$_FILES['image']['tmp_name'] = $data;
				$this->AWSS3->uploadS3($_FILES, $bucket);
				$applicationDoc['filepath'] = 'learners/cet/'.$imageName;
				$applicationDoc['application_doc_type'] = 2;
				$applicationDoc['application_doc_id'] = hash('sha256',$imageName);
				$this->db->insert('application_doc',$applicationDoc);
			}
			if($receipt != ""){
				$data = $cet;
				list($type, $data) = explode(';', $data);
				list(, $data)      = explode(',', $data);
				$data = base64_decode($data);
				$randomId = uniqid();
				$imageName = $randomId.'.jpg';
				$_FILES['image']['name'] = 'learners/receipt/'.$imageName;
				$_FILES['image']['tmp_name'] = $data;
				$this->AWSS3->uploadS3($_FILES, $bucket);
				$applicationDoc['filepath'] = 'learners/receipt/'.$imageName;
				$applicationDoc['application_doc_type'] = 2;
				$applicationDoc['application_doc_id'] = hash('sha256',$imageName);
				$this->db->insert('application_doc',$applicationDoc);
			}
			$create = $this->company_model->newApplication($applicationData);
			if($create){
				http_response_code('200');
				echo json_encode(array( "status" => true, "message" => "Success", "data"=>null));exit;
			}
		}
	}

	public function updateApplication(){
		$validToken = $this->validToken();
		$data = file_get_contents('php://input');

		if(is_null($data)){
			http_response_code('200');
			echo json_encode(array( "status" => false, "message" => 'Bad Request', "data"=>null));exit;
		}else{
			//echo json_encode(array( "status" => false, "message" => $data));exit;

			$applicationData = json_decode($data,true);
			$bucket = 'ri-company-service';
			//$this->setAuditLog($validToken,45);
			$nricCopy = $applicationData['nricCopy'];
			$cet = $applicationData['cet'];
			$receipt = $applicationData['fullPayment'];
			if($nricCopy != ""){
				$data = $nricCopy;
				list($type, $data) = explode(';', $data);
				list(, $data)      = explode(',', $data);
				$data = base64_decode($data);
				$randomId = uniqid();
				$imageName = $randomId.'.jpg';
				$_FILES['image']['name'] = 'learners/nric/'.$imageName;
				$_FILES['image']['tmp_name'] = $data;
				$this->AWSS3->uploadS3($_FILES, $bucket);
				$applicationDoc['filepath'] = 'learners/nric/'.$imageName;
				$applicationDoc['application_doc_type'] = 1;
				$applicationDoc['application_doc_id'] = hash('sha256',$imageName);
				$this->db->insert('application_doc',$applicationDoc);
			}
			if($cet != ""){
				$data = $cet;
				list($type, $data) = explode(';', $data);
				list(, $data)      = explode(',', $data);
				$data = base64_decode($data);
				$randomId = uniqid();
				$imageName = $randomId.'.jpg';
				$_FILES['image']['name'] = 'learners/cet/'.$imageName;
				$_FILES['image']['tmp_name'] = $data;
				$this->AWSS3->uploadS3($_FILES, $bucket);
				$applicationDoc['filepath'] = 'learners/cet/'.$imageName;
				$applicationDoc['application_doc_type'] = 2;
				$applicationDoc['application_doc_id'] = hash('sha256',$imageName);
				$this->db->insert('application_doc',$applicationDoc);
			}
			if($receipt != "0" && $receipt != ""){
				$data = $cet;
				list($type, $data) = explode(';', $data);
				list(, $data)      = explode(',', $data);
				$data = base64_decode($data);
				$randomId = uniqid();
				$imageName = $randomId.'.jpg';
				$_FILES['image']['name'] = 'learners/receipt/'.$imageName;
				$_FILES['image']['tmp_name'] = $data;
				$this->AWSS3->uploadS3($_FILES, $bucket);
				$applicationDoc['filepath'] = 'learners/receipt/'.$imageName;
				$applicationDoc['application_doc_type'] = 2;
				$applicationDoc['application_doc_id'] = hash('sha256',$imageName);
				$this->db->insert('application_doc',$applicationDoc);
			}
			$create = $this->company_model->updateApplication($applicationData);
			if($create){
				http_response_code('200');
				echo json_encode(array( "status" => true, "message" => "Success","data"=>null));exit;
			}else{
				$this->show_error_500();
			}
		}
	}

	public function getApplication(){
		$data = file_get_contents('php://input');
		if(is_null($data)){
			http_response_code('200');
			echo json_encode(array( "status" => false, "message" => 'Bad Request',"data"=>null));exit;
		}else{
			$validToken = $this->validToken();
			$applicationData = json_decode($data,true);
			//$this->setAuditLog($validToken,45);
			$application = $this->company_model->getApplication($applicationData);
			if($application){
				http_response_code('200');
				echo json_encode(array( "status" => true, "message" => "Success", "data"=>null));exit;
			}
		}
	}

	public function addLearner(){
		$validToken = $this->validToken();
		$data = file_get_contents('php://input');
		$learnerData = json_decode($data,true);
		if(is_null($learnerData)){
			$this->show_400();
		}
		$checkNRCPP = $this->checkNRCPP($learnerData['nric']);
		$learnerData['learner_id'] = hash('sha256',$learnerData['nric']);
		$learnerData['learner_manager'] = hash('sha256',$learnerData['learner_manager']);
		$photocopy_id = $learnerData['photocopy_id'];
		$ct_ms_expiry = $learnerData['ct_ms_expiry'];
		unset($learnerData['photocopy_id']);
		unset($learnerData['ct_ms_expiry']);

		if($photocopy_id){
			$data = $photocopy_id;
			list($type, $data) = explode(';', $data);
			list(, $data)      = explode(',', $data);
			$data = base64_decode($data);
			$randomId = uniqid();
			$imageName = $randomId.'.jpg';
			file_put_contents('./assets/learner/'.$randomId.'.jpg', $data);
			$applicationDoc['filepath'] = 'assets/learner/'.$randomId.'.jpg';
			$applicationDoc['application_doc_type'] = 1;
			$applicationDoc['application_doc_id'] = hash('sha256',$imageName);
			$this->db->insert('application_doc',$applicationDoc);
		}

		if($ct_ms_expiry){
			$data = $ct_ms_expiry;
			list($type, $data) = explode(';', $data);
			list(, $data)      = explode(',', $data);
			$data = base64_decode($data);
			$randomId = uniqid();
			$imageName = $randomId.'.jpg';
			file_put_contents('./assets/learner/'.$randomId.'.jpg', $data);
			$applicationDoc['filepath'] = 'assets/learner/'.$randomId.'.jpg';
			$applicationDoc['application_doc_type'] = 2;
			$applicationDoc['application_doc_id'] = hash('sha256',$imageName);
			$this->db->insert('application_doc',$applicationDoc);
		}
		$learnerExists = $this->db->select('learner_id')->from('learner')
						->where('learner_id', $learnerData['learner_id'])
						->get()->num_rows();
		if($learnerExists > 0){
			$this->db->insert('learner',$learnerData);
			$insert_id =  $this->db->insert_id();
			if($insert_id){
				$this->setAuditLog($validToken,17);
				http_response_code('200');
				$data =  $this->db->select('*')->get_where('learner',array('id'=>$insert_id))->row();
				echo json_encode(array( "status" => true, "message" => 'Success',"data" =>$data));exit;
			}else{
				$this->show_error_500();
			}
		}
	}

	public function addLearnerManager(){
		$validToken = $this->validToken();
		$data = file_get_contents('php://input');
		$learnerManagerData = json_decode($data,true);
		if(is_null($learnerManagerData)){
			$this->show_400();
		}
		$learnerManagerData['learner_manager_id'] = hash('sha256',$learnerManagerData['nric']);
		$this->db->insert('learner_manager',$learnerManagerData);
		$insert_id =  $this->db->insert_id();
		if($insert_id){
			$this->setAuditLog($validToken,26);
			http_response_code('200');
			$data =  $this->db->select('*')->get_where('learner_manager',array('id'=>$insert_id))->row();
			echo json_encode(array( "status" => true, "message" => 'Success',"data" =>$data));exit;
		}else{
			$this->show_error_500();
		}
	}

	public function getCompanyUEN(){
		$validToken = $this->validToken();
		$data = file_get_contents('php://input');
		$companyData = json_decode($data,true);
		if(is_null($companyData)){
			$this->show_400();
		}
		$data = $this->db->select('uen')->get_where('company',array('company_name'=>$companyData['company_name']))->row();
		if(is_null($data)){
			$this->show_404();
		}else{
			$this->setAuditLog($validToken,27);
			http_response_code('200');
			echo json_encode(array( "status" => true, "message" => 'Success',"data"=>$data));exit;
		}
	}

	public function getCompanyName(){
		$validToken = $this->validToken();
		$data = file_get_contents('php://input');
		$companyData = json_decode($data,true);
		if(is_null($companyData)){
			$this->show_400();
		}
		$data = $this->db->select('company_name')->get_where('company',array('uen'=>$companyData['uen']))->row();
		if(is_null($data)){
			$this->show_404();
		}else{
			$this->setAuditLog($validToken,28);
			http_response_code('200');
			echo json_encode(array( "status" => true, "message" => 'Success',"data"=>$data));exit;
		}
	}

	public function viewLearners(){
		$validToken = $this->validToken();
		$data = file_get_contents('php://input');
		$companyData = json_decode($data,true);
		if(is_null($companyData)){
			$this->show_400();
		}

		$this->db->select('learner.*');
		$this->db->from('company');
		$this->db->join('learner_manager', 'learner_manager.company_id = company.company_id');
		$this->db->join('learner','learner.learner_manager = learner_manager.learner_manager_id');
		$this->db->where('company.company_id',$companyData['company_id']);
		$q = $this->db->get();
		$data = array();
		if ($q->num_rows() > 0) {
			foreach (($q->result()) as $row) {
				$data[] = $row;
			}
		}
		if(empty($data)){
			$this->show_404();
		}else{
			$this->setAuditLog($validToken,29);
			http_response_code('200');
			echo json_encode(array( "status" => true, "message" => 'Success',"data" =>$data));exit;
		}
	}

	public function viewLearnerManagers(){
		$validToken = $this->validToken();
		$data = file_get_contents('php://input');
		$companyData = json_decode($data,true);
		if(is_null($companyData)){
			$this->show_400();
		}

		$this->db->select('learner_manager.*');
		$this->db->from('company');
		$this->db->join('learner_manager', 'learner_manager.company_id = company.company_id');
		$this->db->where('company.company_id',$companyData['company_id']);
		$q = $this->db->get();
		$data = array();
		if ($q->num_rows() > 0) {
			foreach (($q->result()) as $row) {
				$data[]  = $row;
			}
		}
		if(empty($data)){
			$this->show_404();
		}else{
			$this->setAuditLog($validToken,30);
			http_response_code('200');
			echo json_encode(array( "status" => true, "message" => 'Success',"data" =>$data));exit;
		}
	}

	public function changeAccountStatus(){
		$validToken = $this->validToken();
		$data = file_get_contents('php://input');
		$userData = json_decode($data,true);
		if(is_null($data)){
			http_response_code('200');
			echo json_encode(array( "status" => false, "message" => 'Bad Request',"data"=>null));exit;
		}else{
			$status = $this->db->select("status, name")->from("learner")
						->where("learner_id", $userData["learner_id"])
						->get()->row();
			$newStatus = 0;
			if($status->status == 0){
				$newStatus = 1;
			}
			$this->db->where("learner_id", $userData["learner_id"]);
			$update = $this->db->update("learner", array("status" => $newStatus));
			http_response_code('200');
			echo json_encode(array( "status" => true
			, "message" => "Updated ".$status->name."'s Status Successfully", "data"=>null));exit;
		}
	}

	public function deactivateLearner(){
		$data = file_get_contents('php://input');
		$userData = json_decode($data,true);
		if(is_null($data)){
			http_response_code('200');
			echo json_encode(array( "status" => false, "message" => 'Bad Request', "data"=>null));exit;
		}else{
			$validToken = $this->validToken();
			$id = $this->company_model->getRecordByLearnerId($userData['id']);
			$userData['account_status'] = 0;
			$this->setAuditLog($validToken,31);
			$update = $this->company_model->updateAccountStatus($userData,'learner');
		}
	}

	public function deactivateLearnerManager(){
		$validToken = $this->validToken();
		$data = file_get_contents('php://input');
		$userData = json_decode($data,true);
		if(is_null($data)){
			http_response_code('200');
			echo json_encode(array( "status" => false, "message" => 'Bad Request',"data"=>null));exit;
		}else{
			$validToken = $this->validToken();
			$id = $this->company_model->getRecordByLearnerManagerId($userData['id']);
			$userData['account_status'] = 0;
			$this->setAuditLog($validToken,32);
			$update = $this->company_model->updateAccountStatus($userData,'learner_manager');
		}
	}

	public function paymentTerms(){
		$validToken = $this->validToken();
		$data = file_get_contents('php://input');
		$res = $this->company_model->paymentTerms();
	}

	public function getThreshold(){
		$validToken = $this->validToken();
		$threshold = $this->db->select('threshold')->from('threshold')
					->where('id', 1)->get()->row();
		if(!is_null($threshold)){
			http_response_code('200');
			echo json_encode(array("status" => true, "message" => "Fetched Threshold Successfully", "data" => $threshold->threshold));
		}else{
			$this->show_error_500();
		}
	}

	public function saveThreshold(){
		$validToken = $this->validToken();
		$data = file_get_contents('php://input');
		$threshold = json_decode($data, true);
		$this->db->where('id', 1);
		$update = $this->db->update('threshold',array('threshold' => $threshold['filter_by_value']));
		if($update){
			http_response_code('200');
			echo json_encode(array("status" => true, "message" => "Updated Threshold Successfully", "data"=>null));exit;
		}else{
			$this->show_error_500();exit;
		}
	}

	public function checkThreshold(){
		$validToken = $this->validToken();
		$data = file_get_contents('php://input');
		$company = json_decode($data, true);
		$companyID = $company['company_id'];
		$threshold = $this->db->select('threshold')->from('threshold')
					->where('id', 1)->get()->row();
		http_response_code("200");
		if(!is_null($threshold)){
			$receivables = $this->companyReceivables($companyID);
			if($receivables > $threshold->threshold){
				echo json_encode(array("status" => true
										, "message" => "Exceeded Credit Threshold. Unable to Proceed."
										, "data" => $receivables));exit;
			}else{
				echo json_encode(array("status" => false, "message" => "Not Exceeded", "data" => "0.00"));exit;
			}
		}else{
			echo json_encode(array("status" => false, "message" => "No Receivables", "data" => "0.00"));exit;
		}
	}

	private function companyReceivables($companyID){
		$sales_db = $this->load->database('sales', true);
		$course_db = $this->load->database('courses', true);
		$sql = $sales_db->select('*')->from('invoices i')
					->where('i.status !=',1)
					->where('i.status !=',2)
					->where('company_id', $companyID)
					->get()->result();
		$res = array();
		$coUnpaid = array();
		$unpaid = array();
		$discount = array();
		if(!empty($sql)){
			$total_price = 0;
			foreach($sql as $invoice){
				$unpaid[$invoice->invoice_id] = 0;
				$discount[$invoice->invoice_id] = 0;
				$items = $sales_db->select("course_id, quantity")->from("invoice_items")
							->where("invoice_id", $invoice->invoice_id)
							->get()->result();
				$invoice_discounts = $sales_db->select("discount_amount")->from("invoice_discount_items")
									->where("invoice_id", $invoice->invoice_id)
									->get()->result();
				foreach($invoice_discounts as $invoice_discount){
					$discount[$invoice->invoice_id] += $invoice_discount->discount_amount;
				}
				if(!empty($items)){
					foreach($items as $item){
						$item_price = $course_db->select("sum(training_fees + test_fees) as total_fees")
						->from("courses")->where("id", $item->course_id)
						->get()->row();
						$price = $item->quantity * $item_price->total_fees;
						$unpaid[$invoice->invoice_id] += $price;
					}
				}
				$invoice->unpaid = round(($unpaid[$invoice->invoice_id] - $discount[$invoice->invoice_id]) * 1.07, 2);
				$total_price += $invoice->unpaid;
			}
			$res['receivables'] = round($total_price, 2);
			array_push($coUnpaid, $res);
			return $res['receivables'];
		}else{
			return "0.00";
		}
	}

	public function companiesReceivables(){
		$validToken = $this->validToken();
		$sales_db = $this->load->database('sales', true);
		$course_db = $this->load->database('courses', true);
		$companies = $this->db->select('company_id, company_name, uen
						, street, unit, postal_code')
						->from('company')->get()->result();
		$coUnpaid = array();
		$unpaid = array();
		$discount = array();
		foreach($companies as $co){
			$sql = $sales_db->select('*')->from('invoices i')
					->where('i.status !=',1)
					->where('i.status !=',2)
					->where('company_id', $co->company_id)
					->get()->result();
			$res = array();
			if(!empty($sql)){
				$total_price = 0;
				foreach($sql as $invoice){
					$unpaid[$invoice->invoice_id] = 0;
					$discount[$invoice->invoice_id] = 0;
					$items = $sales_db->select("course_id, quantity")->from("invoice_items")
								->where("invoice_id", $invoice->invoice_id)
								->get()->result();
					$invoice_discounts = $sales_db->select("discount_amount")->from("invoice_discount_items")
										->where("invoice_id", $invoice->invoice_id)
										->get()->result();
					foreach($invoice_discounts as $invoice_discount){
						$discount[$invoice->invoice_id] += $invoice_discount->discount_amount;
					}
					if(!empty($items)){
						foreach($items as $item){
							$item_price = $course_db->select("sum(training_fees + test_fees) as total_fees")
							->from("courses")->where("id", $item->course_id)
							->get()->row();
							$price = $item->quantity * $item_price->total_fees;
							$unpaid[$invoice->invoice_id] += $price;
						}
					}
					$invoice->unpaid = round(($unpaid[$invoice->invoice_id] - $discount[$invoice->invoice_id]) * 1.07, 2);
					$total_price += $invoice->unpaid;
				}
				$res['company_name'] = $co->company_name;
				$res['company_id'] = $co->company_id;
				$res['uen'] = $co->uen;
				$res['address'] = $co->street.' '.$co->unit.' Singapore '.$co->postal_code;
				$res['receivables'] = round($total_price, 2);
				array_push($coUnpaid, $res);
			}
		}
		if(!is_null($coUnpaid)){
			http_response_code('200');
			echo json_encode(array("status" => true, "message" => "Companies with Pending Payment Found",
									"data" => $coUnpaid));
		}else{
			http_response_code('200');
			echo json_encode(array("status" => false
			, "message" => "No Data found","data"=>null));exit;
		}
	}

	public function companiesReceivablesFilter(){
		$validToken = $this->validToken();
		$data = file_get_contents('php://input');
		$filtered = json_decode($data,true);
		if(is_null($data)){
			http_response_code('200');
			echo json_encode(array( "status" => false, "message" => 'Bad Request', "data"=>null));exit;
		}
		$sales_db = $this->load->database('sales', true);
		$course_db = $this->load->database('courses', true);
		$companies = $this->db->select('company_id, company_name, uen
						, street, unit, postal_code')
						->from('company')
						->where('company_id', $filtered['company_id'])
						->get()->result();
		$coUnpaid = array();
		$unpaid = array();
		$discount = array();
		foreach($companies as $co){
			$sql = $sales_db->select('*')->from('invoices i')
					->where('i.status !=',1)
					->where('company_id', $co->company_id)
					->get()->result();
			$res = array();
			if(!empty($sql)){
				$total_price = 0;
				foreach($sql as $invoice){
					$unpaid[$invoice->invoice_id] = 0;
					$discount[$invoice->invoice_id] = 0;
					$items = $sales_db->select("course_id, quantity")->from("invoice_items")
								->where("invoice_id", $invoice->invoice_id)
								->get()->result();
					$invoice_discounts = $sales_db->select("discount_amount")->from("invoice_discount_items")
										->where("invoice_id", $invoice->invoice_id)
										->get()->result();
					foreach($invoice_discounts as $invoice_discount){
						$discount[$invoice->invoice_id] += $invoice_discount->discount_amount;
					}
					if(!empty($items)){
						foreach($items as $item){
							$item_price = $course_db->select("sum(training_fees + test_fees) as total_fees")
							->from("courses")->where("id", $item->course_id)
							->get()->row();
							$price = $item->quantity * $item_price->total_fees;
							$unpaid[$invoice->invoice_id] += $price;
						}
					}
					$invoice->unpaid = round(($unpaid[$invoice->invoice_id] - $discount[$invoice->invoice_id]) * 1.07, 2);
					$total_price += $invoice->unpaid;
				}
				$res['company_name'] = $co->company_name;
				$res['company_id'] = $co->company_id;
				$res['uen'] = $co->uen;
				$res['address'] = $co->street.' '.$co->unit.' Singapore '.$co->postal_code;
				$res['receivables'] = round($total_price, 2);
				array_push($coUnpaid, $res);
			}
		}
		if(!is_null($coUnpaid)){
			http_response_code('200');
			echo json_encode(array("status" => true, "message" => "Companies with Pending Payment Found",
									"data" => $coUnpaid));
		}else{
			http_response_code('200');
			echo json_encode(array("status" => false
			, "message" => "No data found","data"=>null));exit;
		}
	}

	public function getUnpaidInvoices(){
		$validToken = $this->validToken();
		$data = file_get_contents('php://input');
		$companyData = json_decode($data,true);
		$companyID = $companyData['company_id'];
		$sales_db = $this->load->database('sales', true);
		$course_db = $this->load->database('courses', true);
		$unpaid = $sales_db->select('*')->from('invoices i')
					->where('i.status !=',1)
					->where('i.company_id', $companyID)
					->get()->result();
		$invoice_items = [];
		$invoice_discount_items = [];
		$total_fees = 0;
		$total_discount = 0;
		$grand_total = 0;
		if(!empty($unpaid)){
			foreach($unpaid as $u){
				$invoice_id = $u->invoice_id;
				$invoice_items[$invoice_id] = $sales_db->select('*')->from('invoice_items ii')
												->where('ii.invoice_id', $invoice_id)
												->get()->result();
				foreach($invoice_items[$invoice_id] as $ii){
					$fees = $course_db->select("sum(training_fees + test_fees) as total_fees")
							->from('courses')->where('id', $ii->course_id)
							->get()->row();
					$ii->fees = $fees->total_fees;
					$total_fees += $fees->total_fees;
				}
				$invoice_discount_items[$invoice_id] = $sales_db->select('*')->from('invoice_discount_items')
														->where('invoice_id', $invoice_id)
														->get()->result();
				foreach($invoice_discount_items[$invoice_id] as $idi){
					$total_discount += $idi->discount_amount;
				}
				$u->invoice_items = $invoice_items[$invoice_id];
				$u->invoice_discount_items = $invoice_discount_items[$invoice_id];
			}
			$grand_total = $total_fees - $total_discount;
			array_push($unpaid, array('grand_total' => $grand_total));
			http_response_code('200');
			echo json_encode(array("status" => true, "message" => "Unpaid Invoices Found", "data" => $unpaid));exit;
		}else{
			http_response_code('200');
			echo json_encode(array("status" => false
			, "message" => "No data found","data"=>null));exit;
		}
	}

	public function addLearnerRemarks()
	{
		$validToken = $this->validToken();
		$data = file_get_contents('php://input');
		$remarksData = json_decode($data,true);
			
		if(is_null($remarksData)){
			$this->show_400();
		}

		$insertData = array("learner_id" => $remarksData['learner_id']
						,"trainer_id" => $remarksData["trainer_id"]
						
						, "remarks" => $remarksData["remarks"]);
		
		$this->db->insert("learner_remarks", $insertData);
		$insert_id = $this->db->insert_id();
		if($insert_id)
		{
		http_response_code('200');
		$data =  $this->db->select('*')->get_where('learner_remarks',array('id'=>$insert_id))->row();
		echo json_encode(array( "status" => true, "message" => 'Success',"data" =>$data));exit;
		}
		else
		{
			http_response_code('200');
			echo json_encode(array( "status" => false, "message" => 'No Remarks added',"data" =>null));exit;
		}


	}

	
	public function getLearnerRemarks(){
		$validToken = $this->validToken();
		$data = file_get_contents('php://input');
	
		$learnerRemarks = json_decode($data,true);
		if(is_null($learnerRemarks)){
			$this->show_400();
		}
		$event = $this->db->select('*')->from('learner_remarks')
				->where('learner_id',$learnerRemarks['learner_id'])->get();
		if($event->num_rows() > 0){
			$learners = $event->row();
			

			$learners = $this->db->select("*")
					->from("learner")
					->where("learner_id", $learnerRemarks['learner_id'])
					->get()->result();
			
			$rows = [];
		foreach($learners as $learner){
			$name = $this->db->select("l.learner_id, l.name, l.nric, l.fin, l.work_permit as wp, l.company
					,l.status as learner_status, a.invoice_id, a.course_id, a.sponsor_company
					,a.status as application_status")->from("learner l")
					->join("application a", "l.learner_id = a.learner_id", "left")
					->where("l.learner_id", $learner->learner_id)
					->get()->row();
			if(!is_null($name)){
				$learner->learner_id = $name->learner_id;
				$learner->value = $name->learner_id;
				$learner->learner_name = $name->name;
				$learner->learner_nric = $name->nric;
				$learner->nric = $name->nric;
				$learner->learner_fin = $name->fin;
				$learner->fin = $name->fin;
				$learner->learner_wp = $name->wp;
				$learner->wp = $name->wp;
				$learner->learner_status = $name->learner_status;
				$learner->invoice_id = $name->invoice_id;
				$learner->course_id = $name->course_id;
				$learner->sponsor_company = $name->sponsor_company;
				$learner->application_status = $name->application_status;
				$company = $this->db->select("company_name")->from("company")
							->where("company_id", $name->company)->get()->row();
				$learner->company_name = $company->company_name;
				$event = $this->db->select('*')->from('learner_remarks')
						->where('learner_id',$learnerRemarks['learner_id'])->get()->row();
				$learner->learner_remarks = $event;
			}
			$rows[] = $learner;
		}
		
			http_response_code('200');
			echo json_encode(array("status" => true, "message" => "Success"
			, "data" => $learner));exit;
		}else{
			http_response_code('200');
			echo json_encode(array("status" => false
			, "message" => "No event found","data"=>null));exit;
		

		}

	}

	public function updateLearnerRemarks()
	{
		$validToken = $this->validToken();
		$data = file_get_contents('php://input');
		$remarksData = json_decode($data,true);
			
		if(is_null($remarksData)){
			$this->show_400();
		}
		http_response_code('200');
		$update = $this->db->where('id', $remarksData['id'])
					->update('learner_remarks', $remarksData);
		if($update){
			$data =  $this->db->select('*')
						->get_where('learner_remarks',array('id'=>$remarksData['id']))->row();
			echo json_encode(array("status" => true, "message" => "Successfully Updated"
			, "data" => $data));
		}else{
			
			http_response_code('200');
			echo json_encode(array("status" => false, "message" => "Error Updating","data"=>null));
		}
		exit;

	}



	private function mask($string){
        $strMaskLen = strlen($string) - 4;
        $strMask = "";
        for($i = 0; $i < $strMaskLen; $i++){
            $strMask .= "X";
        }
        return $strMask.substr($string, $strMaskLen, 4);
    }

	private function checkNRCPP($data){
		$data = $this->db->select('*')->get_where('learner',array('nric'=>$data))->row();
		if(is_null($data)){
			return TRUE;
		}else{
			http_response_code('200');
			echo json_encode(array( "status" => false, "message" => 'NRIC/PP is already exit. Method not allowed.',"data"=>null));exit;
		}
	}





	private function setAuditLog($data,$api_id){
		$audit_db = $this->load->database('audit_log',TRUE);
		$logs = array(
			'api_id' => $api_id,
			'service' => 3 ,
			'subject_company' => $data->company,
			'action_by' => $data->user_id
		);
		return $audit_db->insert('audit_log',$logs);
	}


	private function validToken(){
		$account_db = $this->load->database('account',TRUE);
		$authToken = $this->input->get_request_header('Authorization', TRUE);
		if(is_null($authToken)){
			http_response_code('200');
			echo json_encode(array( "status" => false, "message" => 'Bad Request, Auth Token is required.',"data"=>null));exit;
		}else{
			$checkToken = $account_db->select('*')->get_where('auth_tokens',array('auth_token'=>$authToken))->row();

			if(is_null($checkToken)){
				http_response_code('200');
				echo json_encode(array( "status" => false, "message" => 'Invalid Authentication Token.',"data"=>null));exit;
			}
			$now = time();
			$expiryDateString = strtotime($checkToken->auth_token_expiry_date);
			if($expiryDateString < $now){
				http_response_code('200');
				echo json_encode(array( "status" => false, "message" => 'Authentication Token has expired.',"data"=>null));exit;
			}
			$decodeJWT = $this->objOfJwt->DecodeToken($checkToken->issued_to);
			$data = $account_db->select('*')->get_where('accounts',array('user_id'=>$decodeJWT['user_id']))->row();
			if(is_null($data)){
				$this->show_error_500();
			}
			return $data;
		}
	}

	private function show_204(){
		http_response_code('200');
		echo json_encode(array( "status" => false, "message" => 'Not Content Found.',"data"=>null));exit;
	}

	private function show_404(){
		http_response_code('200');
		echo json_encode(array( "status" => false, "message" => 'Not Found.',"data"=>null));exit;
	}

	private function show_400(){
		http_response_code('200');
		echo json_encode(array( "status" => false, "message" => 'Bad Request.', "data"=>null));exit;
	}

	private function show_error_500(){
		http_response_code('200');
		$message = 'Internal Server Error.';
		echo json_encode(array( "status" => false, "message" => $message, "data"=>null));exit;
	}



}
