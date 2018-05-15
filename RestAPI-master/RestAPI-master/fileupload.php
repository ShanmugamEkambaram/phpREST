<?php
require 'mysql.php';

$method = $_SERVER['REQUEST_METHOD'];
$target_dir ="files/audio/";

$data=array();
$data['case_assignment_id']=$_POST["case_assignment_id"];
$data['flag']=$_POST["flag"];
$data['case_type']=$_POST["case_type_id"];
$data['case_type_id']=$_POST["case_type"];
$data['assign_status']=$_POST["assign_status"];
$data['consultant_id']=$_POST["consultant_id"];
$data['attach_doc_name_id']=$_POST["attach_doc_name_id"];
$data['case_module_id']=$_POST["case_module_id"];
$today = date("Y-m-d H:i:s");
$filename=array();
// getting the file extension
//$filename=array();
//$countfiles = count($_FILES['file']['name']);
if($data['case_type']=="dynamic"){
$authorization_letter=$_FILES['authorization_letter']['name'];
$investigation_report=$_FILES['investigation_report']['name'];
$insured_questionarie=$_FILES['insured_questionarie']['name'];
$treating_doc_questionarie=$_FILES['treating_doc_questionarie']['name'];
$testdocument1=$_FILES['testdocument1']['name'];
$testdocument2=$_FILES['testdocument2']['name'];
$testdocument3=$_FILES['testdocument3']['name'];
$otherfile=$_FILES['otherfile']['name'];
$evidence_for_trigger=$_FILES['evidence_for_trigger']['name'];
$conveyance_file=$_FILES['conveyance_file']['name'];
}
elseif($data['case_type']=="default"){

	if($data['case_type_id']=="1"){
		$patient_doc_questionnnarie=$_FILES['patient_doc_questionarie']['name'];
		$investigation_report=$_FILES['investigation_report']['name'];
		$insured_questionarie=$_FILES['insured_questionarie']['name'];
		$treating_doc_questionarie=$_FILES['treating_doc_questionarie']['name'];
		$testdocument1=$_FILES['testdocument1']['name'];
		$testdocument2=$_FILES['testdocument2']['name'];
		$testdocument3=$_FILES['testdocument3']['name'];
		$otherfile=$_FILES['otherfile']['name'];
		$evidence_for_trigger=$_FILES['evidence_for_trigger']['name'];
		$conveyance_file=$_FILES['conveyance_file']['name'];
	}
}
// Looping all files
/* for($i=0;$i<$countfiles;$i++){
  $filename[$i] = $_FILES['file']['name'][$i];
  array_push($filename,$filename[$i]);
} */
$extension = $fileinfo['extension'];
$response = array();
$target="";
// file path to upload in the server
if($data['casetype']=='default'){
if($data['casetypeid']==1){
	$target="blk_hospital/";
}elseif($data['casetypeid']==2){
	$target="blk_patient/";
}
elseif($data['casetypeid']==3){
	$target="blk_patient/";
}
elseif($data['casetypeid']==4){
	$target="blk_patient/";
}
elseif($data['casetypeid']==5){
	$target="blk_patient/";
}
elseif($data['casetypeid']==6){
	$target="blk_patient/";
}
elseif($data['casetypeid']==7){
	$target="blk_patient/";
}
elseif($data['casetypeid']==8){
	$target="blk_patient/";
}
elseif($data['casetypeid']==9){
	$target="blk_patient/";
}
elseif($data['casetypeid']==10){
	$target="blk_patient/";
}
elseif($data['casetypeid']==11){
	$target="blk_patient/";
}
}
elseif($data['casetype']=='dynamic'){
	$target="case_module_hidden/";
}
$file_path = $target_dir . '.' . $extension;
$target_file=$target_dir.$target.basename($_FILES["fileToUpload"]["name"]);
//$target_file = $target_dir .basename($_FILES["fileToUpload"]["name"]);
$target=$target_dir .basename($_FILES["fileToUpload"]["name"]);

//$data['targetfile']=$target_file;
// file url to store in the database

$uploadOk = 1;
$audioFileType = pathinfo($target, PATHINFO_EXTENSION);

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

if (file_exists($target))
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
	//	$target_file=$data['targetfile'];
		$claimno=$data['claimno'];
		$casetype=$data['casetype'];
	//  $push_id=$data['push_id'];
		
	$response = array();
	//$audio_url='http://'.$_SERVER['HTTP_HOST'].'$claimno'.$target_file;
	$audio=$claimno." ".$_FILES["fileToUpload"]['name']."".$today = date("Y-m-d H:i:s");;
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
		}
	else{
		$response['code'] =1015;
		$response['desc'] = "upload failed"; 
	}
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
			}
		else{
			$response['code'] =1015;
			$response['desc'] = "upload failed"; 
		}
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
			}
		else{
			$response['code'] =1015;
			$response['desc'] = "upload failed"; 
		}
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
			}
		else{
			$response['code'] =1015;
			$response['desc'] = "upload failed"; 
		}
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
			}
		else{
			$response['code'] =1015;
			$response['desc'] = "upload failed"; 
		}
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
			}
		else{
			$response['code'] =1015;
			$response['desc'] = "upload failed"; 
		}
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
		}
	else{
		$response['code'] =1015;
		$response['desc'] = "upload failed"; 
	}
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
		}
	else{
		$response['code'] =1015;
		$response['desc'] = "upload failed"; 
	}
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
		}
	else{
		$response['code'] =1015;
		$response['desc'] = "upload failed"; 
	}
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
		}
	else{
		$response['code'] =1015;
		$response['desc'] = "upload failed"; 
	}
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
			}
		else{
			$response['code'] =1015;
			$response['desc'] = "upload failed"; 
		}
		}
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
			}
		else{
			$response['code'] =1015;
			$response['desc'] = "upload failed"; 
		}
	}
}else{
	$response['code']=1010;
	$response['desc']="casetype not found";

}
	echo json_encode($response);
	
	}
?>