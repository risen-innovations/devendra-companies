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

	public function getCompanyLearners(){
		$validToken = $this->validToken();
		$data = file_get_contents('php://input');
		$company = json_decode($data,true);
		$learners = $this->db->select('*')->from('learner')
					->where('company', $company['company_id'])
					->get();
		$data = array();
		if($learners->num_rows() > 0){
			foreach($learners->result() as $row){
                $maskedNric = $this->mask($row->nric);
                $row->nric = $maskedNric;
                $maskedWP = $this->mask($row->work_permit);
                $row->work_permit = $maskedWP;
                $data[] = $row;
            }
			http_response_code('200');
			echo json_encode(array( "status"=> true, "message" => "Learners Retrieved", "data"=>$data));exit;
		}else{
			http_response_code('200');
			echo json_encode(array( "status"=> false, "message" => "No Learners Found"));exit;
		}
	}

	public function deactivateCompany(){
		$validToken = $this->validToken();
		$data = file_get_contents('php://input');
		$deactivate = json_decode($data,true);
		$this->db->where('company_id',$deactivate['company_id']);
		$this->db->update('company',array('status' => 0));
		http_response_code('200');
		echo json_encode(array("status" => true, "message" => "Company Deleted"));exit;
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
			echo json_encode(array('status' => false, 'message' => 'UEN already exists'));exit;
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
			echo json_encode(array('status' => false, 'message' => 'Failed to create new company. Please contact system admin.'));
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
			http_response_code(400);
			echo json_encode(array( "status" => false, "message" => 'Bad Request'));exit;
		}else{
			$validToken = $this->validToken();
			$notExists = $this->company_model->checkUEN($uen['uen']);
			if($notExists === true){
				http_response_code(200);
				echo json_encode(array( "status" => true, "message" => "Success"));exit;
			}else{
				http_response_code(200);
				echo json_encode(array( "status" => true, "message" => "UEN not found"));exit;
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
			echo json_encode(array("status" => false, "message" => "No applications found"));exit;
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
			http_response_code(400);
			echo json_encode(array( "status" => false, "message" => 'Bad Request'));exit;
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
			$courses_db = $this->load->database('course', true);
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
			http_response_code(400);
			echo json_encode(array( "status" => false, "message" => 'Bad Request'));exit;
		}else{
			//echo json_encode(array( "status" => false, "message" => $data));exit;

			$applicationData = json_decode($data,true);
			$bucket = 'ri-company-service';
			//$this->setAuditLog($validToken,45);
			$nricCopy = $applicationData['nricCopy'];
			$cet = $applicationData['cet'];
			$receipt = $applicationData['fullPayment'];
			if($nricCopy){
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
			if($cet){
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
			if($receipt){
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
				echo json_encode(array( "status" => true, "message" => "Success"));exit;
			}
		}
	}

	public function getApplication(){
		$data = file_get_contents('php://input');
		if(is_null($data)){
			http_response_code(400);
			echo json_encode(array( "status" => false, "message" => 'Bad Request'));exit;
		}else{
			$validToken = $this->validToken();
			$applicationData = json_decode($data,true);
			//$this->setAuditLog($validToken,45);
			$application = $this->company_model->getApplication($applicationData);
			if($application){
				http_response_code('200');
				echo json_encode(array( "status" => true, "message" => "Success"));exit;
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
			echo json_encode(array( "status" => true, "message" => 'Success','data'=>$data));exit;
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
			echo json_encode(array( "status" => true, "message" => 'Success','data'=>$data));exit;
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
			http_response_code(400);
			echo json_encode(array( "status" => false, "message" => 'Bad Request'));exit;
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
			if($update){
				http_response_code(200);
				echo json_encode(array( "status" => true
				, "message" => "Updated ".$status->name."'s Status Successfully"));exit;
			}else{
				http_response_code(500);
				echo json_encode(array( "status" => false, "message" => 'Failed to update'));exit;
			}
		}
	}

	public function deactivateLearner(){
		$data = file_get_contents('php://input');
		$userData = json_decode($data,true);
		if(is_null($data)){
			http_response_code(400);
			echo json_encode(array( "status" => false, "message" => 'Bad Request'));exit;
		}else{
			$validToken = $this->validToken();
			$id = $this->company_model->getRecordByLearnerId($userData['id']);
			$userData['account_status'] = 0;
			$this->setAuditLog($validToken,31);
			$update = $this->company_model->updateAccountStatus($userData,'learner');
		}
	}

	public function deactivateLearnerManager(){
		$data = file_get_contents('php://input');
		$userData = json_decode($data,true);
		if(is_null($data)){
			http_response_code(400);
			echo json_encode(array( "status" => false, "message" => 'Bad Request'));exit;
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
			http_response_code('405');
			echo json_encode(array( "status" => false, "message" => 'NRIC/PP is already exit. Method not allowed.'));exit;
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
			http_response_code('400');
			echo json_encode(array( "status" => false, "message" => 'Bad Request, Auth Token is required.'));exit;
		}else{
			$checkToken = $account_db->select('*')->get_where('auth_tokens',array('auth_token'=>$authToken))->row();

			if(is_null($checkToken)){
				http_response_code('403');
				echo json_encode(array( "status" => false, "message" => 'Invalid Authentication Token.'));exit;
			}
			$now = time();
			$expiryDateString = strtotime($checkToken->auth_token_expiry_date);
			if($expiryDateString < $now){
				http_response_code('401');
				echo json_encode(array( "status" => false, "message" => 'Authentication Token has expired.'));exit;
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
		http_response_code('204');
		echo json_encode(array( "status" => false, "message" => 'Not Content Found.'));exit;
	}

	private function show_404(){
		http_response_code('404');
		echo json_encode(array( "status" => false, "message" => 'Not Found.'));exit;
	}

	private function show_400(){
		http_response_code('400');
		echo json_encode(array( "status" => false, "message" => 'Bad Request.'));exit;
	}

	private function show_error_500(){
		http_response_code('500');
		$message = 'Internal Server Error.';
		echo json_encode(array( "status" => false, "message" => $message));exit;
	}



}
