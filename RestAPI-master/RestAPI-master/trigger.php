<?php
require 'mysql.php';

$target_dir ="files/audio/";

$data=array();
$data['case_assignment_id']=$_POST["case_assignment_id"];
$data['flag']=$_POST["flag"];
$data['case_trigger_id']=$_POST["case_trigger_id"];
$data['trigger_answer']=$_POST["trigger_answer"];
//$data['trigger_name']=$_POST["trigger_name"];
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
   $moveupload= move_uploaded_file($_FILES['file']['tmp_name'][$i],$target_dir.'documents/'.$filename[$i]);
}
     // Upload file
  if($moveupload){
    $caseassignid=$data['case_assignment_id'];
    $flag=$data['flag'];
    if($flag==1){
     $casetrigger_id=$data['case_trigger_id'];
     $trigger_ans=$data['trigger_answer'];
    // $trigger_file=$data['trigger_file'];
     $triggid=count($casetrigger_id);
     $res="";
     for($i=0;$i<$triggid;$i++){
     $db = connect_db();	
     $today = date("Y-m-d H:i:s");
     $sql = "update case_triggers set trigger_answer='$trigger_ans[$i]',trigger_file='$filename[$i]',modified_date='$today'" . " where case_assignment_id='$caseassignid' and case_trigger_id='$casetrigger_id[$i]'";
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
         $response['code']=1017;
         $response['desc']="update failed";
         echo json_encode($response);
         return;
     }
     return;
 }
 return null;

  }
  return;
}

?>