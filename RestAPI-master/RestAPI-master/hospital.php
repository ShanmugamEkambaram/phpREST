<?php
require 'mysql.php';

$method = $_SERVER['REQUEST_METHOD'];
$target_dir ="files/audio/";

$data=array();
$data['conid']=$_POST["company_authorization_letter"];
$data['casetype']=$_POST["investigation_report_form"];
$data['status']=$_POST["insured_questionarie"];
$data['casetypeid']=$_POST["treating_doc_questionarie"];
$data['caseid']=$_POST["other_file"];
$data['caseassignid']=$_POST["evidance_for_trigger"];
$data['claimno']=$_POST["trigger_finding"];
$fileinfo = pathinfo($_FILES["fileToUpload"]['name']);
// getting the file extension

$extension = $fileinfo['extension'];
//$response = array();

// file path to upload in the server

$file_path = $target_dir . '.' . $extension;
$target_file = $target_dir .basename($_FILES["fileToUpload"]["name"]);
$data['targetfile']=$target_file;
// file url to store in the database

$uploadOk = 1;
$audioFileType = pathinfo($target_file, PATHINFO_EXTENSION);

// Allow certain file formats

if ($audioFileType != "mp3" && $audioFileType != "Wav" && $audioFileType != "ogg" && $audioFileType != "m4a" && $audioFileType != "3gp")
	{
	

 	echo "Sorry, only mp3, m4a, ogg & mp4 files are allowed.";

	$uploadOk = 0;

	}

// Check if image file is a actual image or fake image

if (isset($_POST["submit"]))
	{
	$check = filesize($_FILES["fileToUpload"]["tmp_name"]);
	if ($check !== false)
		{
		
		echo "File is an audio - " . $check["mime"] . ".";
		
		$uploadOk = 1;
		}
	  else
		{
		
		echo "File is not an audio.";
		 
		$uploadOk = 0;
		}
	}

// Check if file already exists

if (file_exists($target_file))
	{
	
	 echo "Sorry, file already exists.";
	 
	$uploadOk = 0;
	}

// Check file size

if ($_FILES["fileToUpload"]["size"] > 50000000000)
	{
	
	echo "Sorry, your file is too large.";

	$uploadOk = 0;
	}

// Allow certain file formats

if ($audioFileType != "mp3" && $audioFileType != "m4a" && $audioFileType != "Wav" && $audioFileType != "ogg" && $audioFileType != "3gp")
	{
	
		echo "Sorry, only mp3, m4a, ogg & Wav files are allowed.";

	$uploadOk = 0;
	}

// Check if $uploadOk is set to 0 by an error

if ($uploadOk == 0)
	{
	

	echo "Sorry, your file was not uploaded.";

	// if everything is ok, try to upload file

	}
  else
	{
	if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file))
		{

		// $empcode = $_POST['emp_code'];

		updateToDB($data);
		
		echo "The file " . basename($_FILES["fileToUpload"]["name"]) . " has been uploaded.";
		
		}
	  else
		{
		
		echo "Sorry, there was an error uploading your file.";
		//echo $response;
		}
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
			if(in_array($casetype,array("default"))){
				return 4;
			}
			elseif(in_array($casetype,array("dynamic"))){
				return 5;
			}
			return 6;
		}
		elseif (in_array($status,array("submitted","Submitted","SUBMITTED"))) {
			return 7;
		}
		else{
			return 8;
		}
		return 0;
	}
	return null;
	}
function updateToDB($data)
	{
		$conid=$data['conid'];
		$status=$data['status'];
		$caseassignid=$data['caseassignid'];
		$target_file=$data['targetfile'];
		$claimno=$data['claimno'];
		$casetype=$data['casetype'];
	//  $push_id=$data['push_id'];
		
	$response = array();
	//$audio_url='http://'.$_SERVER['HTTP_HOST'].'$claimno'.$target_file;
	$audio=$claimno." ".$target_file;
	$statuscheck=checksave($status,$conid);
	if(in_array($statuscheck,array(1,2,4,5))){
	if($casetype == "default"){ 
	$casetypeid=$data["casetypeid"];
	if($casetypeid=="1"){
	$db = connect_db();	
	$sql = "update blk_hospital_part set hidden_video_name1='$audio'" . " where case_assignment_id='$caseassignid'";
	$exe = $db->query($sql);
	$last_id = $db->affected_rows;
	$db = null;
	if($exe){
	
		$response['code'] = 0;
		$response['desc'] = "success";
		echo json_encode($response);
		return;
		}
	else{
		$response['code'] =1015;
		$response['desc'] = "upload failed"; 
		echo json_encode($response);
		return;
	}
	return;
	}
	elseif($casetypeid=="2"){
		$db = connect_db();	
		$sql = "update blk_patient_part set hidden_video_name1='$audio'" . " where case_assignment_id='$caseassignid'";
		$exe = $db->query($sql);
		$last_id = $db->affected_rows;
		$db = null;
		if($exe){
	
			$response['code'] = 0;
			$response['desc'] = "success";
			echo json_encode($response);
			return;
			}
		else{
			$response['code'] =1015;
			$response['desc'] = "upload failed"; 
			echo json_encode($response);
			return;
		}
		return;
	}
	elseif ($casetypeid=="3") {
		$db = connect_db();	
		$sql = "update blk_sme set hidden_video_name1='$audio'" . " where case_assignment_id='$caseassignid'";
		$exe = $db->query($sql);
		$last_id = $db->affected_rows;
		$db = null;
		if($exe){
	
			$response['code'] = 0;
			$response['desc'] = "success";
			echo json_encode($response);
			return;
			}
		else{
			$response['code'] =1015;
			$response['desc'] = "upload failed"; 
			echo json_encode($response);
			return;
		}
		return;
	}
	elseif($casetypeid=="4"){
		$db = connect_db();	
		$sql = "update blk_death_claim set hidden_video_name1='$audio'" . " where case_assignment_id='$caseassignid'";
		$exe = $db->query($sql);
		$last_id = $db->affected_rows;
		$db = null;
		if($exe){
	
			$response['code'] = 0;
			$response['desc'] = "success";
			echo json_encode($response);
			return;
			}
		else{
			$response['code'] =1015;
			$response['desc'] = "upload failed"; 
			echo json_encode($response);
			return;
		}
		return;
	}
	elseif ($casetypeid=="5") {
		$db = connect_db();	
		$sql = "update blk_disablility set hidden_video_name1='$audio'" . " where case_assignment_id='$caseassignid'";
		$exe = $db->query($sql);
		$last_id = $db->affected_rows;
		$db = null;
		if($exe){
	
			$response['code'] = 0;
			$response['desc'] = "success";
			echo json_encode($response);
			return;
			}
		else{
			$response['code'] =1015;
			$response['desc'] = "upload failed"; 
			echo json_encode($response);
			return;
		}
		return;
	}
	elseif($casetypeid=="6"){
		$db = connect_db();	
		$sql = "update blk_personal_accident_claim set hidden_video_name1='$audio'" . " where case_assignment_id='$caseassignid'";
		$exe = $db->query($sql);
		$last_id = $db->affected_rows;
		$db = null;
		if($exe){
	
			$response['code'] = 0;
			$response['desc'] = "success";
			echo json_encode($response);
			return;
			}
		else{
			$response['code'] =1015;
			$response['desc'] = "upload failed"; 
			echo json_encode($response);
			return;
		}
		return;
	}
	elseif ($casetypeid=="7") {
	$db = connect_db();	
	$sql = "update blk_bill_verification_hospital set hidden_video_name1='$audio'" . " where case_assignment_id='$caseassignid'";
	$exe = $db->query($sql);
	$last_id = $db->affected_rows;
	$db = null;	
	if($exe){
	
		$response['code'] = 0;
		$response['desc'] = "success";
		echo json_encode($response);
		return;
		}
	else{
		$response['code'] =1015;
		$response['desc'] = "upload failed"; 
		echo json_encode($response);
		return;
	}
	return;
	}
	elseif($casetypeid=="8"){
	$db = connect_db();	
	$sql = "update blk_bill_verific_pharmacy set hidden_video_name1='$audio'" . " where case_assignment_id='$caseassignid'";
	$exe = $db->query($sql);
	$last_id = $db->affected_rows;
	$db = null;	
	if($exe){
	
		$response['code'] = 0;
		$response['desc'] = "success";
		echo json_encode($response);
		return;
		}
	else{
		$response['code'] =1015;
		$response['desc'] = "upload failed"; 
		echo json_encode($response);
		return;
	}
	return;
	}
	elseif ($casetypeid=="9") {
	$db = connect_db();	
	$sql = "update blk_document_verification set hidden_video_name1='$audio'" . " where case_assignment_id='$caseassignid'";
	$exe = $db->query($sql);
	$last_id = $db->affected_rows;
	$db = null;	
	if($exe){
	
		$response['code'] = 0;
		$response['desc'] = "success";
		echo json_encode($response);
		return;
		}
	else{
		$response['code'] =1015;
		$response['desc'] = "upload failed"; 
		echo json_encode($response);
		return;
	}
	return;
	}
	elseif ($casetypeid=="10") {
	$db = connect_db();	
	$sql = "update blk_caseless set hidden_video_name1='$audio'" . " where case_assignment_id='$caseassignid'";
	$exe = $db->query($sql);
	$last_id = $db->affected_rows;
	$db = null;		
	if($exe){
	
		$response['code'] = 0;
		$response['desc'] = "success";
		echo json_encode($response);
		return;
		}
	else{
		$response['code'] =1015;
		$response['desc'] = "upload failed"; 
		echo json_encode($response);
		return;
	}
	return;
	}
	elseif ($casetypeid=="11") {
		$db = connect_db();	
		$sql = "update blk_initimation_case set hidden_video_name1='$audio'" . " where case_assignment_id='$caseassignid'";
		$exe = $db->query($sql);
		$last_id = $db->affected_rows;
		$db = null;		
		if($exe){
	
			$response['code'] = 0;
			$response['desc'] = "success";
			echo json_encode($response);
			return;
			}
		else{
			$response['code'] =1015;
			$response['desc'] = "upload failed"; 
			echo json_encode($response);
			return;
		}
		return;
		}
		return;
	}
	elseif ($casetype == "dynamic" ) {
		$othercasetypeid=$data["casetypeid"];
		$db = connect_db();
		$sql = "update case_module_hidden_video set hidden_video_name1='$audio'" . " where case_assignment_id='$caseassignid'";
		$exe = $db->query($sql);
		//$last_id = $db->affected_rows;
		$db = null;
		//if($last_id==0 || $last_id >0){
			if($exe){
	
			$response['code'] = 0;
			$response['desc'] = "success";
			echo json_encode($response);
			return;
			}
		else{
			$response['code'] =1015;
			$response['desc'] = "upload failed"; 
			echo json_encode($response);
			return;
		}
	}
}else{
	$response['code']=1010;
	$response['desc']="casetype not found";
	echo json_encode($response);
	return;

}
	return;
	
	}
?>