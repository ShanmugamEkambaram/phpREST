<?php
require 'mysql.php';


$target_dir ="files/audio/";

$data=array();
$data['case_assignment_id']=$_POST["case_assignment_id"];
$data['flag']=$_POST["flag"];
$data['case_type']=$_POST["case_type"];
$data['assign_status']=$_POST["assign_status"];
$data['consultant_id']=$_POST["consultant_id"];
$data['attach_doc_name_id']=$_POST["attach_doc_name_id"];
$data['case_module_id']=$_POST["case_module_id"];
$today = date("Y-m-d H:i:s");
$filename=array();
$countfiles = count($_FILES['file']['name']);
// Looping all files
for($i=0;$i<$countfiles;$i++){
  $filename[$i] = $_FILES['file']['name'][$i];
  array_push($filename,$filename[$i]);
}
// getting the file extension
$file=array();
if (isset($_POST["submit"])){
for($i=0;$i<count($filename)-1;$i++){
	 // Upload file
	 $moveupload=move_uploaded_file($_FILES['file']['tmp_name'][$i],$target_dir.'documents/'.$filename[$i]);
}
  if($moveupload){
    $caseassignid=$data['case_assignment_id'];
    $flag=$data['flag'];
	$status=$data['assign_status'];
	$conid=$data['consultant_id'];
		$casetype=$data['case_type'];
		$doc_id=$data['attach_doc_name_id'];
		$fileid=sizeof($doc_id);
		$statuscheck=checkstats($status,$conid);
		$res="";
		if($casetype=="dynamic"){
		if($statuscheck==1){
			$db = connect_db();	
			$sql = "update case_assignment set assign_status='$status',submitted_on='$today'". " where consultant_id='$conid'";
			$exe = $db->query($sql);
			$db = null;
		}
		elseif($statuscheck==2){
			$db = connect_db();	
			$sql = "update case_assignment set assign_status='$status',submitted_on='$today'". " where consultant_id='$conid'";
			$exe = $db->query($sql);
			$db = null;
		}
		for ($i=0; $i<$fileid;$i++) 
		{
			
			$db = connect_db();	
			$sql = "update attach_docs_files set attach_file_name='$filename[$i]'". " where attach_doc_name_id='$doc_id[$i]'";
			$exe = $db->query($sql);
			$res=$exe;
			$db = null;
		}
		if($res){
			$response['code']=0;
			$response['desc']="success";
			echo json_encode($response);
			return;
		}
		else{
			$response['code']=1018;
			$response['desc']="failed";
			echo json_encode($response);
			return;
		}
		return;
	}
 return null;

  }
  return;
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


?>