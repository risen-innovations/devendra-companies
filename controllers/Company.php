<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/CreatorJwt.php';

class Company extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model('company_model');
		$this->objOfJwt = new CreatorJwt();
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
		$companyData['company_id'] = hash('sha256',$companyData['uen']);
		$companyData['sales_person'] = implode(",",$companyData['sales_person']);
		$this->db->insert('company',$companyData);
		$insert_id = $this->db->insert_id();
		if($insert_id){
			$this->setAuditLog($validToken,25);
			http_response_code('200');
			$data =  $this->db->select('*')->get_where('company',array('id'=>$insert_id))->row();
			echo json_encode(array( "status" => true, "message" => 'Success',"data" =>$data));exit;
		}else{
			$this->show_error_500();
		}
	}

	public function updateCompany(){
		$validToken  = $this->validToken();
		$data = file_get_contents('php://input');
		$body = json_decode($data,true);
		if(is_null($body)){
			$this->show_400();
		}
		$this->db->where('company_id', $body['company_id'])->update('company', $body);
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
