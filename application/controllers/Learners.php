<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/CreatorJwt.php';
require APPPATH . '/libraries/AWSS3.php';

class Learners extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model('company_model');
		$this->objOfJwt = new CreatorJwt();
		$this->AWSS3 = new AWSS3();
        // Instantiate an Amazon S3 client.
		$this->s3Client = new Aws\S3\S3Client([
			'region'  => 'ap-southeast-1',
			'version' => 'latest',
			'credentials' => [
				'key'    => "AKIAQAUZKAGO5UVGEST5",
				'secret' => "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"
			]
		]);
		header('Content-Type: application/json');
    }

    public function getLearners(){
        $validToken = $this->validToken();
        $learners = $this->db->select('*, c.country_name as nationality')->from('learner l')
                    ->join('countries c','l.nationality = c.id','left')
                    //->where("l.status", 1)
                    ->get();
        $data = array();
        if($learners->num_rows() > 0){
            foreach($learners->result() as $row){
                $maskedNric = $this->mask($row->nric);
                $row->nric = $maskedNric;
                $maskedWP = $this->mask($row->work_permit);
                $row->work_permit = $maskedWP;
				if($row->sex == 0){
					$row->sex = "F";
				}else{
					$row->sex = "M";
				}
				if($row->status == 0){
					$row->status = "Inactive";
				}else{
					$row->status = "Active";
				}
                $data[] = $row;
            }
            $data = $learners->result();
            http_response_code('200');
		    echo json_encode(array( "status" => true, "message" => 'Success',"data" =>$data));exit;
        }
        http_response_code('200');
		echo json_encode(array( "status" => false, "message" => 'No learners found'));exit;
    }

    public function searchLearner(){
        $validToken = $this->validToken();
        $data = file_get_contents('php://input');
        $keyword = json_decode($data,true);
        
        $learners = $this->db->select('*, c.country_name as nationality')
                    ->from('learner l')
                    ->join('countries c','l.nationality = c.id','left')
                    ->where('l.nric', $keyword['keyword'])
                    ->or_where('l.work_permit', $keyword['keyword'])
                    ->or_like('l.name', $keyword['keyword'])
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
            $data = $learners->result();
            http_response_code('200');
            echo json_encode(array( "status" => true, "message" => 'Learner Found',"data" =>$data));exit;
        }

        http_response_code('200');
        echo json_encode(array( "status" => true, "message" => 'No Learners found for that Keyword', "data" =>$data));exit;
    }

    public function getLearner(){
        $validToken = $this->validToken();
        $data = file_get_contents('php://input');
		$learnerData = json_decode($data,true);
		if(is_null($learnerData)){
			$this->show_400();
		}
        $learner = $this->db->select('*, co.id as company_id, a.id as application_id')->from('learner l')
                    ->join('countries c','l.nationality = c.id','left')
                    ->join('company co','l.company = co.company_id','left')
                    ->join('application a','l.learner_id = a.learner_id','left')
                    ->where('l.learner_id', $learnerData['learner_id'])
                    ->get();
        if($learner->num_rows() > 0){
            $row = $learner->row();
            $maskedNric = $this->mask($row->nric);
            $row->nric = $maskedNric;
            $maskedWP = $this->mask($row->work_permit);
            $row->work_permit = $maskedWP;

            $documents = $this->db->select('id as doc_id, filepath, application_doc_type, application_doc_id')->from('application_doc')
                                ->where('application_id', $row->application_id)
                                ->get()->result();
            $id = [];
            $cet = [];
            foreach($documents as $doc){
                switch($doc->application_doc_type){
                    case 0:
                        $id[] = $doc;
                        break;
                    case 1:
                        $cet[] = $doc;
                        break;
                }
            }
            $row->id_copy = $id;
            $row->cet = $cet;
            
            http_response_code('200');
		    echo json_encode(array( "status" => true, "message" => 'Success',"data" =>$row));exit;
        }
        http_response_code('200');
		echo json_encode(array( "status" => false, "message" => 'No learners found'));exit;
    }

    public function getLearnerDoc(){
        $validToken = $this->validToken();
        $data = file_get_contents('php://input');
        $docData = json_decode($data,true);
        $file = $this->db->select('filepath')->from('application_doc')
                ->where('id', $docData['doc_id'])
                ->get()->row();
        if(isset($file)){
            $fileName = explode('/',$file->filepath);
            $len = count($fileName);
            $ext = explode('.',$fileName[$len - 1]);
            $extlen = count($ext);
            $ext = $ext[$extlen -1];
            $fileName = $fileName[$len - 1];
            //$path = 'assets/'.$fileName;
            $contentType = "image/png";
            switch(strtolower($ext)){
                case 'jpg':
                    $contentType = 'image/jpeg';
                    break;
                case 'jpeg':
                        $contentType = 'image/jpeg';
                        break;
                case 'pdf':
                    $contentType = 'application/pdf';
                    break;
                case 'default':
                    $contentType = 'image/jpeg';
                    break;
            }
            try{
                //$this->setAuditLog($validToken,48);
                $object = $this->s3Client->getObject(array(
                    'Bucket' => 'ri-company-service',
                    'Key'    => $file->filepath,
                    //'@http'  => ['sink' => $path]
                ));
                header('Content-Description: File Transfer');
                //this assumes content type is set when uploading the file.
                header('Content-Disposition: attachment; filename=' . $fileName);
                header("Content-Type: {$object['ContentType']}");
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                //send file to browser for download.
                //echo $object["Body"];
                $fileBase64 = base64_encode($object["Body"]);
                $fileURI = 'data: '.$contentType.';base64,'.$fileBase64;
                http_response_code('200');
                echo json_encode(array( "status" => true, "message" => 'File found on S3', "data" => $fileURI));exit;
            }catch (Exception $e){
                http_response_code('404');
                echo json_encode(array( "status" => false, "message" => $e->getMessage().PHP_EOL));exit;
            }
        }
    }

    public function updateLearner(){
        $validToken = $this->validToken();
        $data = file_get_contents('php://input');
        $learner = json_decode($data,true);
        $this->db->where('learner_id', $learner['learner_id']);
        $this->db->update('learner', array('coretrade_expiry' => $learner['CTExpDate']));
        http_response_code('200');
        echo json_encode(array( "status" => true, "message" => 'Updated Learner'));exit;
    }

    public function getWorkPermitTypes(){
        $validToken = $this->validToken();
        $wpTypes = $this->db->order_by('name', 'asc')->get('work_permit_types');
        if($wpTypes->num_rows() > 0){
            http_response_code('200');
            echo json_encode(array( "status" => true, "message" => 'Success', "data" => $wpTypes->result()));exit;
        }else{
            http_response_code('200');
            echo json_encode(array( "status" => false, "message" => 'WP Types No Rows Found', "data"=>array()));exit;
        }
    }

    public function getApplicationOptions(){
        $validToken = $this->validToken();
        $course_db = $this->load->database('courses', true);
        $res = $course_db->order_by('trade_type_name', 'asc')->get('trade_type');
        if($res->num_rows() > 0){
            http_response_code('200');
            echo json_encode(array( "status" => true, "message" => 'Success', "data" => $res->result()));exit;
        }else{
            http_response_code('200');
            echo json_encode(array( "status" => false, "message" => 'No Rows Found', "data"=>array()));exit;
        }
    }

    public function getTradeTypes(){
        $validToken = $this->validToken();
        $course_db = $this->load->database('courses', true);
        $res = $course_db->order_by('trade_level_name', 'asc')->get('trade_level');
        if($res->num_rows() > 0){
            http_response_code('200');
            echo json_encode(array( "status" => true, "message" => 'Success', "data" => $res->result()));exit;
        }else{
            http_response_code('200');
            echo json_encode(array( "status" => false, "message" => 'No Rows Found', "data"=>array()));exit;
        }
    }

    public function checkLearnerAssigned(){
        $validToken = $this->validToken();
        $data = file_get_contents('php://input');
        $learner = json_decode($data,true);
        $exists = $this->db->or_where('nric', $learner['id'])
                    ->or_where('work_permit', $learner['id'])
                    ->or_where('fin', $learner['id'])
                    ->get('learner');
        $count = 0;
        if($exists->num_rows() > 0){
            $exists = $exists->row();
            $learner_id = $exists->learner_id;
            $scheduling_db = $this->load->database("scheduling", true);
            $res = $scheduling_db->select("*, e.id as eid")->from("events_learners el")
                    ->join("events e", "e.id = el.event_id", "left")
                    ->where("el.learner_id", $learner_id)->get();
            if($res->num_rows() > 0){
                foreach($res->result() as $r){
                    $lres = $this->db->select("*")->from("learners_results")
                    ->where("event_id", $r->eid)
                    ->where("learner_id", $r->learner_id)
                    ->get();
                    if($lres->num_rows() > 0){
                        $count++;
                    }
                }
            }else{
                http_response_code("200");
                echo json_encode(array("status" => false, "message" => "No Pending Training Found", "data"=>array())); exit;
            }
            if($count == 0){ //check whether learner has taken exams; If taken, allowed to take another course
                http_response_code("200");
                echo json_encode(array("status" => true, "message" => "Pending Training Found", "data"=>array())); exit;
            }else{
                http_response_code("200");
                echo json_encode(array("status" => false, "message" => "No Pending Training Found", "data"=>array())); exit;
            }
        }else{
            http_response_code("200");
            echo json_encode(array("status" => false, "message" => "No Pending Training Found", "data"=>array())); exit;
        }
    }

    public function getAssessmentResults(){
        $validToken = $this->validToken();
        $data = file_get_contents('php://input');
        $filter = json_decode($data,true);
        $res = $this->db->select('result, event_id')
                ->from('learners_results')
                ->get();
        if($res->num_rows() > 0){
            $results = $res->result();
            $courses = array();
            $scheduling_db = $this->load->database("scheduling", true);
            foreach($results as $idx => $r){
                $event_id = $r->event_id;
                if($filter['filter_by_value'] != 0){
                    $event = $scheduling_db->select('id, course_id, datetime_created')
                            ->from('events')
                            ->where('id', $event_id)
                            ->where('course_id', $filter['filter_by_value'])
                            ->get();
                }else{
                    $event = $scheduling_db->select('id, course_id, datetime_created')
                                ->from('events')->where('id', $event_id)->get();
                }
                if($event->num_rows() > 0){
                    $event = $event->row();
                    if($r->event_id = $event->id){
                        $r->course_id = $event->course_id;
                        $r->datetime_created = $event->datetime_created;
                    }else{
                        $r->course_id = null;
                    }
                }else{
                    unset($results[$idx]);
                }
            }
            $mth5 = date_create(date('Y-m-01'))->format('F');
            $mth4 = date_create(date('Y-m-01'))->modify('first day of last month')->format('F');
            $mth3 = date_create(date('Y-m-01'))->modify('first day of -2 month')->format('F');
            $mth2 = date_create(date('Y-m-01'))->modify( 'first day of -3 month' )->format('F');
            $mth1 = date_create(date('Y-m-01'))->modify( 'first day of -4 month' )->format('F');
            $assessmentResults = array(
                                    'retest'=>array($mth1=>0,$mth2=>0,$mth3=>0,$mth4=>0,$mth5=>0),
                                    'passed'=>array($mth1=>0,$mth2=>0,$mth3=>0,$mth4=>0,$mth5=>0),
                                    'nyc'=>array($mth1=>0,$mth2=>0,$mth3=>0,$mth4=>0,$mth5=>0),
                                );
            foreach($results as $r){
                if($r->result === '3'){
                    switch(date_create($r->datetime_created)->format('F')){
                        case $mth1:
                            $assessmentResults['retest'][$mth1] += 1;
                            break;
                        case $mth2:
                            $assessmentResults['retest'][$mth2] += 1;
                            break;
                        case $mth3:
                            $assessmentResults['retest'][$mth3] += 1;
                            break;
                        case $mth4:
                            $assessmentResults['retest'][$mth4] += 1;
                            break;
                        case $mth5:
                            $assessmentResults['retest'][$mth5] += 1;
                            break;
                    }
                }
                if($r->result === '1'){
                    switch(date_create($r->datetime_created)->format('F')){
                        case $mth1:
                            $assessmentResults['passed'][$mth1] += 1;
                            break;
                        case $mth2:
                            $assessmentResults['passed'][$mth2] += 1;
                            break;
                        case $mth3:
                            $assessmentResults['passed'][$mth3] += 1;
                            break;
                        case $mth4:
                            $assessmentResults['passed'][$mth4] += 1;
                            break;
                        case $mth5:
                            $assessmentResults['passed'][$mth5] += 1;
                            break;
                    }
                }
                if($r->result === '2'){
                    switch(date_create($r->datetime_created)->format('F')){
                        case $mth1:
                            $assessmentResults['nyc'][$mth1] += 1;
                            break;
                        case $mth2:
                            $assessmentResults['nyc'][$mth2] += 1;
                            break;
                        case $mth3:
                            $assessmentResults['nyc'][$mth3] += 1;
                            break;
                        case $mth4:
                            $assessmentResults['nyc'][$mth4] += 1;
                            break;
                        case $mth5:
                            $assessmentResults['nyc'][$mth5] += 1;
                            break;
                    }
                }
            }
            $retestResultsArr = array();
            foreach($assessmentResults['retest'] as $a){
                array_push($retestResultsArr, $a);
            }
            $passedResultsArr = array();
            foreach($assessmentResults['passed'] as $a){
                array_push($passedResultsArr, $a);
            }
            $nycResultsArr = array();
            foreach($assessmentResults['nyc'] as $a){
                array_push($nycResultsArr, $a);
            }
            $assessmentResultsArr = array('retest'=>$retestResultsArr, 
                                    'passed'=>$passedResultsArr,
                                    'nyc'=>$nycResultsArr);
            http_response_code("200");
            echo json_encode(array("status" => true, "message" => "Success", 
                                "data" => $assessmentResultsArr));exit;
        }else{
            http_response_code("200");
            echo json_encode(array("status" => false, "message" => "No Rows Found", "data" => array()));exit;
        }
    }

    private function mask($string){
        $strMaskLen = strlen($string) - 4;
        $strMask = "";
        for($i = 0; $i < $strMaskLen; $i++){
            $strMask .= "X";
        }
        return $strMask.substr($string, $strMaskLen, 4);
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
