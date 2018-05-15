<?php
require("vendor/autoload.php");
require 'mysql.php';

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require 'vendor/autoload.php';

$app = new \Slim\App;
$app->get('/', function (Request $request, Response $response) {
});
$app->post('/login',
function ($request, $response, $args)
	{
	login($request->getParsedBody());
	});
$app->post('/cases',
function ($request, $response, $args)
		{
		cases($request->getParsedBody());
		});	
$app->post('/fees',
function ($request, $response, $args)
			{
			fees($request->getParsedBody());
			});	
$app->post('/savestatus',
function ($request, $response, $args)
						{
						savestatus($request->getParsedBody());
						});	
						
$app->post('/triggers',
function ($request, $response, $args)
		{
		triggers($request->getParsedBody());
			});								
$app->post('/dynamiccase',
function ($request, $response, $args)
		{
		cases_dynamic($request->getParsedBody());
		});			
$app->run();
function getemp(){
$db = connect_db();
	$sql = "SELECT * FROM consultant_details where consultant_email='$email' and agreement_number='$password'";
	$exe = $db->query($sql);
	if($exe==!null){
		$result['code'] = 0;
		$result['desc'] = "Invitation success";
		echo json_encode($result);
		return;
}
	else{
					$result['code'] = 1000;
					$result['desc'] = "Invitation fail";
					echo json_encode($result);
					return;
	}
}
function countstats($conid){
	$db=connect_db();
	$response=array();
	$sql="SELECT DISTINCT(select COUNT(assign_status) from case_assignment where assign_status='pending' AND consultant_id='$conid') as Pending ,(select COUNT(assign_status) from case_assignment where assign_status='submitted' AND consultant_id='$conid') as Submitted ,(select COUNT(assign_status) from case_assignment where assign_status='saved' AND consultant_id='$conid') as Saved,(select COUNT(assign_status) from case_assignment where assign_status='raise_query' AND consultant_id='$conid') as Raise_query from case_assignment WHERE consultant_id='$conid'";
	$exe = $db->query($sql);
	$data = $exe->fetch_all(MYSQLI_ASSOC);
	$db = null;
	foreach ($data as $record) {
		$response['pending']=$record['Pending'];
		$response['saved']=$record['Saved'];
		$response['submitted']=$record['Submitted'];
		$response['raise_query']=$record['Raise_query'];
	}
	return json_encode($response);
}
function login($data)	{
	$email=$data['username'];
	$password=$data['password'];	
	$db = connect_db();
	$sql = "SELECT * FROM consultant_details where consultant_email='$email' and agreement_number='$password'";
	$exe = $db->query($sql);
	$data = $exe->fetch_all(MYSQLI_ASSOC);
	$db = null;
	foreach($data as $record){		
		$status = $record["status"];
		$conid=$record["consultant_id"];
		$countstatus=countstats($conid);
		$count=json_decode($countstatus,true);
		//$statuscount=var_dump($count);
		if ($status == "accepted"||$status=="Accepted"||$status=="ACCEPTED"){
			$response=array();
			$response['Pending']=$count["pending"];
			$response["Saved"]=$count["saved"];
			$response['Submitted']=$count["submitted"];
			$response['consultant_id']=$record["consultant_id"];
			$response['consultant_Name']=$record["consultant_name"];
			$response['agreementNumber']=$record["agreement_number"];
			$response['consultant_email']=$record["consultant_email"];
			$response['primary_phone_no']=$record["primary_phone_no"];
			$response['secondary_phone_no']=$record["secondary_phone_no"];
			$response['father_name']=$record["father_name"];
			$response['Date date_of_birth']=$record["date_of_birth"];
			$response['aadhar_card_number']=$record["aadhar_card_number"];
			$response['driving_license_number']=$record["driving_license_number"];
			$response['permanent_address']=$record["permanent_address"];
			$response['present_address']=$record["present_address"];
			$response['state_id']=$record["state_id"];
			$response['city_id']=$record["city_id"];
			$response['pincode']=$record["pincode"];
			$response['qualification']=$record["qualification"];
			$response['nickname']=$record["nickname"];
			$response['account_name']=$record["account_name"];
			$response['account_number']=$record["account_number"];
			$response['bank_name']=$record["bank_name"];
			$response['branch_location']=$record["branch_location"];
			$response['ifsc_code']=$record["ifsc_code"];
			$response['status']=$record["status"];

			echo json_encode($response);
			return ;
		}
	    else if ($status == "rejected") {
			$response['code']=1001;
			$response['desc']="rejected";
			echo json_encode($response);
			return ;
		}
        else if ($status == "pending") {
			$response['code']=1002;
			$response['desc']="pending";
			echo json_encode($response);
			return ;	
			
		}
	}	
	return null;
}

function cases($data)	{
	$id=$data['consultant_id'];
	$status=$data['status'];
	$db = connect_db();
	$sql = "SELECT * FROM case_assignment where consultant_id='$id' and assign_status='$status'";
	$exe = $db->query($sql);
	$data = $exe->fetch_all(MYSQLI_ASSOC);
	$db = null;
	foreach($data as $record){		
		$assign_status=$record["assign_status"];
		$case_id=$record["case_id"];
		if (in_array($assign_status,array("pending","Pending","PENDING"))) {
			$response=array();
			$db = connect_db();
			$sql = "SELECT case_id,case_type,case_type_id,other_case_type_id,case_assignment_id,case_assigned_on,assign_status,patient_name,claim_no,policy_number,company_name FROM case_registration b LEFT JOIN case_assignment a ON b.case_registration_id=a.case_id LEFT JOIN insurance_companies c ON b.company_id=c.company_id WHERE a.consultant_id='$id' AND assign_status='$assign_status'";
			$exe = $db->query($sql);
			$data = $exe->fetch_all(MYSQLI_ASSOC);
			$db = null;
			$response=$data;
			echo json_encode($response);
			return;
		}
        else if(in_array($assign_status,array("saved","Saved","SAVED"))) {
			$response=array();
			$db = connect_db();
			$sql = "SELECT case_id,case_type,case_type_id,other_case_type_id,case_assignment_id,case_assigned_on,assign_status,patient_name,claim_no,policy_number,company_name FROM case_registration b LEFT JOIN case_assignment a ON b.case_registration_id=a.case_id LEFT JOIN insurance_companies c ON b.company_id=c.company_id WHERE a.consultant_id='$id' AND assign_status='$assign_status'";
			$exe = $db->query($sql);
			$data = $exe->fetch_all(MYSQLI_ASSOC);
			$db = null;
			$response=$data;
			echo json_encode($response);
			return;
			
		}
		else if (in_array($assign_status,array("submitted","Submitted","SUBMITTED"))) {
			$response=array();
			$db = connect_db();
			$sql = "SELECT case_id,case_type,case_type_id,other_case_type_id,case_assignment_id,case_assigned_on,assign_status,patient_name,claim_no,policy_number,company_name FROM case_registration b LEFT JOIN case_assignment a ON b.case_registration_id=a.case_id LEFT JOIN insurance_companies c ON b.company_id=c.company_id WHERE a.consultant_id='$id' AND assign_status='$assign_status'";
			$exe = $db->query($sql);
			$data = $exe->fetch_all(MYSQLI_ASSOC);
			$db = null;
			$response=$data;
			echo json_encode($response);
			return;
			
		}
		elseif (in_array($assign_status,array("aprove_raise_query","Aprove_raise_query","APROVE_RAISE_QUERY"))) {
			$caseassignid=$record['case_assignment_id'];
			$response=array();
			$db = connect_db();
			$sql = "SELECT case_query_id,case_type,qc_case_query,qc_query_file,case_assignment_id,qc_query_on FROM case_queries where case_assignment_id='$caseassignid'";
			$exe = $db->query($sql);
			$data = $exe->fetch_all(MYSQLI_ASSOC);
			$db = null;
			$response=$data;
			echo json_encode($response);
			return;
		}
		elseif (in_array($assign_status,array("raise_query","Raise_query","RAISE_QUERY"))) {
			$caseassignid=$record['case_assignment_id'];
			$response=array();
			$db = connect_db();
			$sql = "SELECT case_query_id,case_type,qc_case_query,qc_query_file,case_assignment_id,qc_query_on FROM case_queries where case_assignment_id='$caseassignid'";
			$exe = $db->query($sql);
			$data = $exe->fetch_all(MYSQLI_ASSOC);
			$db = null;
			$response=$data;
			echo json_encode($response);
			return;
		}
     else{
		 $response['code']=1005;
		 $response['desc']="Not in the LIST";
		 echo json_encode($response);
		 return;
	 }
	 $response['code']=1006;
	 $response['desc']="Outside scope";
	 echo json_encode($response);
	 return;
	}	
	return;
}
function checkfeestatus($conid,$status,$caseassignid){
	$db = connect_db();
	$sql = "SELECT * FROM case_assignment where fee_is_paid='$status' and consultant_id='$conid'";
	$exe = $db->query($sql);
	$data = $exe->fetch_all(MYSQLI_ASSOC);
	$db = null;
	foreach($data as $record){		
		$status = $record["fee_is_paid"];
		$conveyance=$record["enable_conveyance"];
		if(in_array($status,array("pending","Pending","PENDING"))){
			if(in_array($conveyance,array("yes","Yes","YES"))){
			return 1;
			}
			return 2;
		}
		elseif(in_array($status,array("reserved","Reserved","RESERVED"))){
			if(in_array($conveyance,array("yes","Yes","YES"))){
				return 3;
				}
				return 4;
		}elseif (in_array($status,array("confirmed","Confirmed","CONFIRMED"))) {
			if(in_array($conveyance,array("yes","Yes","YES"))){
				return 5;
				}
				return 6;
		}
		return 7;
	}
	return 0;
}
function fees($data){
	$conid=$data['consultant_id'];
	$status=$data['fee_is_paid'];
	$feestats=checkfeestatus($conid,$status);
	$response=array();
	if ($feestats==3) {
		$db = connect_db();
		$sql = "SELECT mrd_amount,pay_conveyance,consultant_fee,consult_insentivies,case_type,claim_no,patient_name FROM case_assignment a,case_registration b where fee_is_paid='$status' and consultant_id='$conid' and b.case_registration_id=a.case_id";
		$exe = $db->query($sql);
		$data = $exe->fetch_all(MYSQLI_ASSOC);
		$db = null;
		$response=$data;
		echo json_encode($response);
		return;
	} elseif ($feestats==5) {
		$db = connect_db();
		$sql = "SELECT mrd_amount,pay_conveyance,consultant_fee,consult_insentivies,case_type,claim_no,patient_name FROM case_assignment a,case_registration b where fee_is_paid='$status' and consultant_id='$conid' and b.case_registration_id=a.case_id";
		$exe = $db->query($sql);
		$data = $exe->fetch_all(MYSQLI_ASSOC);
		$db = null;
		$response=$data;
		echo json_encode($response);
		return;
	}else{
		$response['code']=1011;
		$response['desc']="not found";
		echo json_encode($response);
		return;
	}
   return null;
}
function checksave($status,$conid){
	$db = connect_db();
	$sql = "SELECT * FROM case_assignment where assign_status='$status' and consultant_id='$conid'";
	$exe = $db->query($sql);
	$data = $exe->fetch_all(MYSQLI_ASSOC);
	$db = null;
	foreach($data as $record){		
		$status = $record["assign_status"];
		$casetype=$record["case_type"];
	if(in_array($status,array("saved","Saved","SAVED"))){
		if(in_array($casetype,array("default"))){
			return 1;
		}
		elseif(in_array($casetype,array("dynamic"))){
			return 2;
		}
		return 3;
	}
	elseif (in_array($status,array("pending","Pending","PENDING"))) {
		return 4;
	}
	elseif (in_array($status,array("confirmed","Confirmed","CONFIRMED"))) {
		return 5;
	}
	else{
		return 6;
	}
	return 0;
}
return null;
}
function savestatus($data){
	$conid=$data['consultant_id'];
	$status=$data['assign_status'];
	$caseid=$data['case_id'];
	$caseassignid=$data['case_assignment_id'];
	$casetype=$data['case_type'];
	$assign_status=checksave($status,$conid);
	if(in_array($assign_status,array(1,2))){
	if($casetype == "default"){ 
		$casetypeid=$data["case_type_id"];
		 if($casetypeid == "1"){
			$db = connect_db();
			$sql = "SELECT * FROM blk_hospital_part a,case_assignment b where a.case_assignment_id='$caseassignid' and a.case_assignment_id=b.case_assignment_id and b.consultant_id='$conid'";
			$exe = $db->query($sql);
			$data = $exe->fetch_all(MYSQLI_ASSOC);
			$db = null;
			$response=$data;
			echo json_encode($response);
			return;
			
		 }elseif ($casetypeid == "2") {
			$db = connect_db();
			$sql = "SELECT * FROM blk_patient_part a,case_assignment b where a.case_assignment_id='$caseassignid' and a.case_assignment_id=b.case_assignment_id";
			$exe = $db->query($sql);
			$data = $exe->fetch_all(MYSQLI_ASSOC);
			$db = null;
			$response=$data;
			echo json_encode($response);
			return;
			
		 }elseif ($casetypeid == "3") {
			$db = connect_db();
			$sql = "SELECT * FROM blk_sme a,case_assignment b where a.case_assignment_id='$caseassignid' and a.case_assignment_id=b.case_assignment_id";
			$exe = $db->query($sql);
			$data = $exe->fetch_all(MYSQLI_ASSOC);
			$db = null;
			$response=$data;
			echo json_encode($response);
			return;
			
		 }elseif ($casetypeid == "4") {
			$db = connect_db();
			$sql = "SELECT * FROM blk_death_claim a,case_assignment b where a.case_assignment_id='$caseassignid' and a.case_assignment_id=b.case_assignment_id";
			$exe = $db->query($sql);
			$data = $exe->fetch_all(MYSQLI_ASSOC);
			$db = null;
			$response=$data;
			echo json_encode($response);
			return;
			
		 }elseif ($casetypeid == "5") {
			$db = connect_db();
			$sql = "SELECT * FROM blk_disablility a,case_assignment b where a.case_assignment_id='$caseassignid' and a.case_assignment_id=b.case_assignment_id";
			$exe = $db->query($sql);
			$data = $exe->fetch_all(MYSQLI_ASSOC);
			$db = null;
			$response=$data;
			echo json_encode($response);
			return;
			
		 }elseif ($casetypeid == "6") {
			$db = connect_db();
			$sql = "SELECT * FROM blk_personal_accident_claim a,case_assignment b where a.case_assignment_id='$caseassignid' and a.case_assignment_id=b.case_assignment_id";
			$exe = $db->query($sql);
			$data = $exe->fetch_all(MYSQLI_ASSOC);
			$db = null;
			$response=$data;
			echo json_encode($response);
			return;
			
		 }elseif ($casetypeid == "7") {
			$db = connect_db();
			$sql = "SELECT * FROM blk_bill_verification_hospital a,case_assignment b where a.case_assignment_id='$caseassignid' and a.case_assignment_id=b.case_assignment_id";
			$exe = $db->query($sql);
			$data = $exe->fetch_all(MYSQLI_ASSOC);
			$db = null;
			$response=$data;
			echo json_encode($response);
			return;
			
		}elseif ($casetypeid == "8") {
			$db = connect_db();
			$sql = "SELECT * FROM blk_bill_verific_pharmacy a,case_assignment b where a.case_assignment_id='$caseassignid' and a.case_assignment_id=b.case_assignment_id";
			$exe = $db->query($sql);
			$data = $exe->fetch_all(MYSQLI_ASSOC);
			$db = null;
			$response=$data;
			echo json_encode($response);
			return;
			
		}elseif ($casetypeid == "9") {
			$db = connect_db();
			$sql = "SELECT * FROM blk_document_verification a,case_assignment b where a.case_assignment_id='$caseassignid' and a.case_assignment_id=b.case_assignment_id";
			$exe = $db->query($sql);
			$data = $exe->fetch_all(MYSQLI_ASSOC);
			$db = null;
			$response=$data;
			echo json_encode($response);
			return;
			
		}elseif ($casetypeid == "10") {
			$db = connect_db();
			$sql = "SELECT * FROM blk_caseless a,case_assignment b where a.case_assignment_id='$caseassignid' and a.case_assignment_id=b.case_assignment_id";
			$exe = $db->query($sql);
			$data = $exe->fetch_all(MYSQLI_ASSOC);
			$db = null;
			$response=$data;
			echo json_encode($response);
			return;
			
		}elseif ($casetypeid == "11") {
			$db = connect_db();
			$sql = "SELECT * FROM blk_initimation_case a,case_assignment b where a.case_assignment_id='$caseassignid' and a.case_assignment_id=b.case_assignment_id";
			$data = $exe->fetch_all(MYSQLI_ASSOC);
			$db = null;
			$response=$data;
			echo json_encode($response);
			return;
			
		}
	}
	elseif ($casetype == "dynamic" ) {
		$othercasetypeid=$data["other_case_type_id"];
		$db = connect_db();
		$sql = "SELECT 'case_module_id','case_assignment_id','company_authorization_letter','investigation_report_form','insured_questionarie','treating_doc_questionarie','other_file','evidance_for_trigger','trigger_finding','any_comments_visit','created_date','modified_date',pay_conveyance FROM case_module_hidden_video a,case_assignment b where a.case_assignment_id='$caseassignid' and a.case_assignment_id=b.case_assignment_id";
		$exe = $db->query($sql);
		$data = $exe->fetch_all(MYSQLI_ASSOC);
		$db = null;
		$response=$data;
		echo json_encode($response);
		return;
	}
}else{
	$response['code']=1010;
	$response['desc']="casetype not found";
	echo json_encode($response);
	return;

}
return null;
}
function triggers($data){
$caseassignid=$data['case_assignment_id'];
$flag=$data['flag'];
if($flag==0){
$db=connect_db();
$sql="select case_trigger_id,trigger_name,trigger_file,trigger_answer from case_triggers where case_assignment_id='$caseassignid'";
$exe=$db->query($sql);
$data = $exe->fetch_all(MYSQLI_ASSOC);
$db = null;
$response=array();
$response=$data;
	if($exe){
	echo json_encode($response);
	return;
	}
	else{
		$response['code']=1020;
		$response['desc']="failed";
		echo json_encode($response);
		return;
	}
	return;
}
else{
	return;
}
return null;
}

function checkstats($status,$conid){
	$db = connect_db();
	$sql = "SELECT * FROM case_assignment where assign_status='$status' and consultant_id='$conid'";
	$exe = $db->query($sql);
	$data = $exe->fetch_all(MYSQLI_ASSOC);
	$db = null;
	foreach($data as $record){		
		$status = $record["assign_status"];
		$casetype=$record["case_type"];
	if(in_array($status,array("saved","Saved","SAVED"))){
		return 1;
			}
	elseif (in_array($status,array("pending","Pending","PENDING"))) {
		return 2;
	}
	else{
		return 3;
	}
	return 0;
}
return null;
}
function cases_dynamic($data){
	$caseassignid=$data['case_assignment_id'];
	$status=$data['assign_status'];
	$conid=$data['consultant_id'];
	$flag=$data['flag'];
	$today = date("Y-m-d H:i:s");
	if($flag=="0"){
	$db=connect_db();
	$sql="select b.attach_file_name,a.document_name,a.attach_doc_name_id,a.case_module_id from attach_documents_names a,attach_docs_files b where a.case_module_id=b.case_module_id and a.attach_doc_name_id=b.attach_doc_name_id and b.case_assignment_id='$caseassignid'";
	$exe=$db->query($sql);
	$data = $exe->fetch_all(MYSQLI_ASSOC);
	$db = null;	
	$response=array();
	$response=$data;
	if($exe){
	echo json_encode($response);
	return;
	}
	else{
		$response['code']=1020;
		$response['desc']="failed";
		echo json_encode($response);
		return;
	}
	return;
	}
}
?>
