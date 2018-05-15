<?php
require 'vendor/autoload.php';
include('lib/Way2SMS/way2sms-api.php');
require 'mysql.php';
$app = new Slim\App();
$app->get('/', 'get_profile');
$app->get('/id', 'id_generate');
$app->get('/membershipfeeinfo', 'membershipfeeinfo');
$app->post('/membership', function($request, $response, $args) {
	membership($request->getParsedBody());
	//R	equest object�s <code>getParsedBody()</code> method to parse the HTTP request 
	});
	$app->get('/members/{MemID}',
function ($request, $response, $args)
	{
	get_profile($args['MemID']);
	});
	$app->get('/Events/{Event}',
function ($request, $response, $args)
{
events($args['Event']);
});
$app->post('/profile_update', function($request, $response, $args) {
	profile_update($request->getParsedBody());
		//R	equest object�s <code>getParsedBody()</code> method to parse the HTTP request 
});
$app->post('/contactus', function($request, $response, $args) {
	contactus($request->getParsedBody());
		//R	equest object�s <code>getParsedBody()</code> method to parse the HTTP request 
});
$app->post('/add_event', function($request, $response, $args) {
add_event($request->getParsedBody());
//R	equest object�s <code>getParsedBody()</code> method to parse the HTTP request 
});
$app->post('/register', function($request, $response, $args) {
		add_member($request->getParsedBody());
	//R	equest object�s <code>getParsedBody()</code> method to parse the HTTP request 
});
$app->put('/update_employee', function($request, $response, $args) {
	update_employee($request->getParsedBody());
	});
$app->delete('/delete_employee', function($request, $response, $args) {
	delete_employee($request->getParsedBody());
	});
$app->post('/pinverification',function($request,$response,$args){
	verification($request->getParseBody());
	});
$app->get('/ee/{mobile}',
function ($request, $response, $args)
{verify_mobile($args['mobile']);
	});
$app->post('/otpreg',
function ($request, $response, $args)
{reg_otp($request->getParsedBody());
	//R	equest object’s <code>getParsedBody()</code> method to parse the HTTP Request
	});
$app->post('/verifyotp',
function ($request, $response, $args)
{verifyotp($request->getParsedBody());
	//R	equest object’s <code>getParsedBody()</code> method to parse the HTTP Request
	});
$app->run();
function generatePIN($digits = 6)
{
	$i = 0;
	$otp = "";
	while ($i < $digits)
	{
		$otp.= mt_rand(0, 9);
		$i++;
		}
	return $otp;
	}
	function validate_email($email){
		 if (!filter_var($email, FILTER_VALIDATE_EMAIL)){
			 return true;
			 }
			 return false;
    echo json_encode($email);
	}
/**function checkMobile($mobile)	{	$db = connect_db();	$sql = "SELECT * FROM members where mobile = '$mobile'";	$exe = $db->query($sql);	$db = null;	if ($exe->num_rows > 0)		{		return true;		}	return false;	}**/
function get_profile($MemID) {
	$db = connect_db();
	$sql = "SELECT * FROM membership where MemID='$MemID'";
	$exe = $db->query($sql);
	$data = $exe->fetch_all(MYSQLI_ASSOC);
	$db = null;
	echo json_encode($data);
	}
function verifyMobile($mobile){
	$db=connect_db();
	$sql="select * from reg_members where mobile='$mobile'";
	$exe=$db->query($sql);
	$db=null;
	if($exe->num_rows > 0){
		return true;
		}
	return false;
	}
function insertOTP($otp, $mobile)
{
	$db = connect_db();
	if(verifyMobile($mobile)){
		$sql="update reg_members set otp='$otp' where mobile='$mobile'";
		$exe = $db->query($sql);
		//			echo $response;
		}
	else{
		$sql = "insert into reg_members (mobile,otp)" . " VALUES($mobile,$otp)";
		$exe = $db->query($sql);
		}
	$db = null;
	}
	


function verifyPIN($mobile, $otp)
{
	$db = connect_db();
	$sql = "SELECT * FROM reg_members where mobile = '$mobile'and otp = '$otp' ";
	$exe = $db->query($sql);
	$db = null;
	if ($exe->num_rows>0)
	{
	return true;
		}
	return false;
	}
function id_generate(){
	$db=connect_db();
	$sql="SELECT ID from membership order by ID desc";
	$exe=$db->query($sql);
	// 	$data = $exe->fetch_all(MYSQLI_ASSOC);
	// 	$db = null;
	$ID=$exe->num_rows + 1;
	//e	cho json_encode($ID);
	return $ID;
	}
function checkmember($Email){
	$db=connect_db();
	$sql="select * from membership where EmailID1='$Email'";
	$exe=$db->query($sql);
	if($exe->num_rows>0){
		return true;
		}
	return false;
	}
function checkmembermobile($mobile){
	$db=connect_db();
	$sql="select * from membership where Mobile1='$mobile'";
	$exe=$db->query($sql);
	if($exe->num_rows>0){
		return true;
		}
	return false;
	}
function checkreg_members($mobile){
	$db=connect_db();
	$sql="select verified_date from reg_members where mobile='$mobile'";
	$exe=$db->query($sql);
	$data = $exe->fetch_array(MYSQLI_ASSOC);
	$sa=$exe->fetch_field();
	if($sa->length==$sa->max_length){
		return true;
		}
	return false;
echo json_encode($data);
echo json_encode($sa);
	}
function add_member($data) {
	$db = connect_db();
	$ID=id_generate();
	$MemID=$ID.time();
	$name=$data['FirstName'];
	$email=$data['EmailID1'];
    $gender=$data['Gender'];
    $dob=$data['DOB'];	
    $country=$data['Country'];	
    $result=array();
	$today = date("Y-m-d H:i:s");
	$mobile=$data['Mobile1'];
	if(!checkmember($email)&&(!checkmembermobile($mobile))){
			if((validate_email($email))||(!preg_match('/^\+?([0-9]{1,4})\)?[-. ]?([0-9]{9})$/',$mobile))) {
				$result['code']=1010;
			    $result['desc']="Mobile number emailid or not valid";
				}
		else{
			$sql = "insert into membership(ID,MemID,FirstName,EmailID1,Mobile1,Gender,DOB,Country,StartDate)"
			."VALUES('$ID','$MemID','$name','$email','$mobile','$gender','$dob','$country','$today')";
			$exe = $db->query($sql);
			$db = null;
			if($exe==true){
			   $otp=generatePIN();
				insertOTP($otp,$mobile);
				$res = sendWay2SMS('7904446431','mob1234', $mobile, $otp."S4I APP Registration Confirmation Code");
				$result['code']=0;
				$result['desc']="success";
				$result['MemID']=$MemID;
				$result['ID']=$ID;
				}
				}
			}
	else if(!checkmembermobile($mobile)||!checkmember($email)){
		
		
			if((validate_email($email))||(!preg_match('/^\+?([0-9]{1,4})\)?[-. ]?([0-9]{9})$/',$mobile))) {
			$result['code']=1010;
			$result['desc']="Mobile number or emailid not valid";
			}
		else{
			$db=connect_db();
			$sql="update membership set ID='$ID',MemID='$MemID',FirstName='$name',EmailID1='$email',Mobile1='$mobile',Gender='$gender',DOB='$dob',Country='$country',StartDate='$today' where Mobile1='$mobile' OR EmailID1='$email'";
			$exe=$db->query($sql);
			if($exe==true){
			$otp=generatePIN();
			insertOTP($otp,$mobile);
			$res = sendWay2SMS('7904446431','mob1234', $mobile, $otp."S4I APP Registration Confirmation Code");
			$db=null;
			$result['code']=0;
			$result['desc']="update success";
			$result['MemID']=$MemID;
			$result['ID']=$ID;
			}
		
			}
	}
		else {
			if(checkreg_members($mobile)){
						$result['code']=1004;
		                $result['desc']="Already registered";
				
}
		else{
			if((validate_email($email)==true)||(!preg_match('/^\+?([0-9]{1,4})\)?[-. ]?([0-9]{9})$/',$mobile))){ 
	        $result['code']=1010;
			$result['desc']="Mobile number or emailid  not valid";
		 }
      else
{
			    $db=connect_db();
				$sql="update membership set ID='$ID',MemID='$MemID',FirstName='$name',EmailID1='$email',Mobile1='$mobile',Gender='$gender',DOB='$dob',Country='$country',StartDate='$today' where Mobile1='$mobile'";
			    $exe=$db->query($sql);
			    $db=null;
				if($exe==true){
				$otp=generatePIN();
                insertOTP($otp,$mobile);
				$res = sendWay2SMS('7904446431','mob1234', $mobile, $otp."S4I APP Registration Confirmation Code");
				$result['code']=0;
				$result['desc']="success";
				$result['MemID']=$MemID;
				$result['ID']=$ID;
		}
		      
		}
	}
		}
	echo json_encode($result);
}
function verifyotp($data)
{
	$mobile = $data["Mobile1"];
	$otp = $data["otp"];
	$result = array();
	if(checkreg_members($mobile)){
		$result['code']=1004;
		$result['desc']="Already Registered and verified User";
	}
	else{
	if (verifyPIN($mobile, $otp))
	{
		  $today = date("Y-m-d H:i:s");
		  $db=connect_db();
	      $sql="update reg_members set verified_date='$today' where mobile='$mobile'";
		  $exe=$db->query($sql);
		$result['code'] = 0;
		$result['desc'] = "Success";
		}
		else{
		$result['code'] = 1002;
		$result['desc'] = "mobile number or otp pin is wrong";
		}
	}
	echo json_encode($result);
	}
function update_profile($data) {
$db = connect_db();
$sql = "update membership SET FirstName='$data[FirstName]',EmailID1='$data[EmailID1]',Mobile1 ='$data[Mobile1]',Gender='$data[Gender]',DOB='$data[DOB]',Country='$data[Country]'"
	." WHERE EmailID1='$data[EmailID1]'";
	$exe = $db->query($sql);
	$last_id = $db->affected_rows;
	$db = null;
	if (!empty($last_id))
	echo $last_id;
	}
function delete_member($data) {
	$db = connect_db();
	$result=array();
	$sql = "DELETE FROM membership WHERE Mobile1 = '$data[Mobile1]'";
	$exe = $db->query($sql);
	$db = null;
	if (!empty($last_id))
	echo $last_id;
	else
	echo false;
	}
function membership($data){
	$db=connect_db();
	$today = date("Y-m-d H:i:s");
	$result=array();
	if($data['reg_type']=="Member"){
	$sql="update membership set MemState='$data[MemState]',MemDis='$data[MemDis]',MemType='$data[MemType]',MemPlan='$data[MemPlan]',reg_type='$data[reg_type]',StartDate='$today' where MemID='$data[MemID]'";
	$exe=$db->query($sql);
	$db=null;
	if($exe==true){
	    $result['code']=0;
        $result['desc']="success";
			echo json_encode($result);
			return;
	}
	else{
		$result['code']=1003;
        $result['desc']="MemID not found";
			echo json_encode($result);
			return;
	}
	return;
	}
	else if($data['reg_type']=="VOLUNTEER"){
	$sql="update membership set reg_type='$data[reg_type]',StartDate='$today' where MemID='$data[MemID]'";
	$exe=$db->query($sql);
	$db=null;
	if($exe==true){
	$result['code']=0;
    $result['desc']="success";
		echo json_encode($result);
		return;
	}
	else{
	$result['code']=1003;
    $result['desc']="MemID not found";
		echo json_encode($result);
		return;
	}
	return;
	}
	else if($data['reg_type']=="SPONSOR"){
	$sql="update membership set reg_type='$data[reg_type]',StartDate='$today' where MemID='$data[MemID]'";
	$exe=$db->query($sql);
		$db=null;
	if($exe==true){
	    $result['code']=0;
        $result['desc']="success";
			echo json_encode($result);
			return;
	}
	else{
	$result['code']=1003;
    $result['desc']="MemID not found";
		echo json_encode($result);
		return;
	}
	return;
	}
	else if($data['reg_type']=="PARTNER"){
   $sql="update membership set reg_type='$data[reg_type]',StartDate='$today' where MemID='$data[MemID]'";
	$exe=$db->query($sql);
	$db=null;
	if($exe==true){
	$result['code']=0;
	$result['desc']="success";
		echo json_encode($result);
		return;
	}
	else{
		$result['code']=1003;
	    $result['desc']="MemID not found";
			echo json_encode($result);
			return;
	}
	return;
	}
	else if($data['reg_type']=="MENTOR"){
	$sql="update membership set reg_type='$data[reg_type]',StartDate='$today' where MemID='$data[MemID]'";
	$exe=$db->query($sql);
	$db=null;
	if($exe==true){
	$result['code']=0;
    $result['desc']="success";
		echo json_encode($result);
		return;
	}
	else{
	$result['code']=1003;
    $result['desc']="MemID not found";
		echo json_encode($result);
		return;
	}
	return;
}

	}
	function profile_update($data){
		$db=connect_db();
		$result=array();
		if(!empty($data['FirstName'])){
        $sql="update membership set FirstName='$data[FirstName]' where MemID='$data[MemID]'";
	    $exe = $db->query($sql);
	    $db = null;
        $result['code'] = 0;
		$result['desc'] = "success";
		echo json_encode($result);
		return;
		}
		else if(!empty($data['Mobile2'])){
		if(!preg_match('/^\+?([0-9]{1,4})\)?[-. ]?([0-9]{9})$/',$data['Mobile2'])){
				$result['code']=1010;
				$result['desc']="mobile number not valid";
				echo json_encode($result);
				return;
			}
		
		else{
		$sql="update membership set Mobile2='$data[Mobile2]' where MemID='$data[MemID]'";
	    $exe = $db->query($sql);
	    $db = null;
        $result['code'] = 0;
		$result['desc'] = "success";
		echo json_encode($result);
		return;
        }
		}
		else if(!empty($data['EmailID2'])){
			if(validate_email($data['EmailID2'])==true){
				$result['code']=10;
				$result['desc']="Emailid not valid";
				echo json_encode($result);
				return;
			}
			else{
		$sql="update membership set EmailID2='$data[EmailID2]' where MemID='$data[MemID]'";
	    $exe = $db->query($sql);
	    $db = null;
		$result['code'] = 0;
		$result['desc'] = "success";
		echo json_encode($result);
		return;
			}
		}
			else if(!empty($data['Gender'])){

		$sql="update membership set Gender='$data[Gender]' where MemID='$data[MemID]'";
	    $exe = $db->query($sql);
	    $db = null;
        $result['code'] = 0;
		$result['desc'] = "success";
		echo json_encode($result);
		return;

	}
	else if(!empty($data['HouseNo'])){

		$sql="update membership set HouseNo='$data[HouseNo]' where MemID='$data[MemID]'";
	    $exe = $db->query($sql);
	    $db = null;
        $result['code'] = 0;
		$result['desc'] = "success";
		echo json_encode($result);
		return;

	}
	else if(!empty($data['Town'])){

		$sql="update membership set Town='$data[Town]' where MemID='$data[MemID]'";
	    $exe = $db->query($sql);
	    $db = null;
        $result['code'] = 0;
		$result['desc'] = "success";
		echo json_encode($result);
		return;

	}
	else if(!empty($data['State'])){

		$sql="update membership set State='$data[State]' where MemID='$data[MemID]'";
	    $exe = $db->query($sql);
	    $db = null;
        $result['code'] = 0;
		$result['desc'] = "success";
		echo json_encode($result);
		return;

	}
	else if(!empty($data['PIN'])){

		$sql="update membership set PIN='$data[PIN]' where MemID='$data[MemID]'";
	    $exe = $db->query($sql);
	    $db = null;
        $result['code'] = 0;
		$result['desc'] = "success";
		echo json_encode($result);
		return;

	}
	else if(!empty($data['CompState'])){

		$sql="update membership set CompState='$data[CompState]' where MemID='$data[MemID]'";
	    $exe = $db->query($sql);
	    $db = null;
        $result['code'] = 0;
		$result['desc'] = "success";
		echo json_encode($result);
		return;

	}
	else if(!empty($data['CompPIN'])){

		$sql="update membership set CompPIN='$data[CompPIN]' where MemID='$data[MemID]'";
	    $exe = $db->query($sql);
	    $db = null;
        $result['code'] = 0;
		$result['desc'] = "success";
		echo json_encode($result);
		return;

	}
	else if(!empty($data['CompNumber'])){

		$sql="update membership set CompNumber='$data[CompNumber]' where MemID='$data[MemID]'";
	    $exe = $db->query($sql);
	    $db = null;
        $result['code'] = 0;
		$result['desc'] = "success";
		echo json_encode($result);
		return;

	}
		else if(!empty($data['CompTown'])){

		$sql="update membership set CompTown='$data[CompTown]' where MemID='$data[MemID]'";
	    $exe = $db->query($sql);
	    $db = null;
        $result['code'] = 0;
		$result['desc'] = "success";
		echo json_encode($result);
		return;

	}
		else if(!empty($data['MemPlan'])){

		$sql="update membership set MemPlan='$data[MemPlan]' where MemID='$data[MemID]'";
	    $exe = $db->query($sql);
	    $db = null;
		if($exe==true){
        $result['code'] = 0;
		$result['desc'] = "success";
		echo json_encode($result);
		return;
   }
   else{
		 $result['code'] =1004;
		$result['desc'] = "Updation failed";
		echo json_encode($result);
		return;
		}
	}	
		else if(!empty($data['MemType'])){

		$sql="update membership set MemType='$data[MemType]' where MemID='$data[MemID]'";
	    $exe = $db->query($sql);
	    $db = null;
		if($exe==true){
        $result['code'] = 0;
		$result['desc'] = "success";
		echo json_encode($result);
		return;
		}
		else{
		 $result['code'] =1004;
		$result['desc'] = "Updation failed";
		echo json_encode($result);
		return;
		}

	}
	}
	function contactus($data){
		$result=array();
		$additionalsetting="";
		$enquiry_date = date("Y-m-d");
		$db=connect_db();
		$sql="insert into sfi_contact_form_7(title,form,mail,Subject,Messages,additional_settings,enquiry_date)"."values('$data[Name]','$data[Form]','$data[Mail]','$data[Subject]','$data[Messages]','$additionalsetting','$enquiry_date')";
		$exe=$db->query($sql);
		if($exe==true){
			$result['code']=0;
			$result['desc']="Success";
			$db=null;
		}
	else{
		return;
	}
	echo json_encode($result);
}
function events($Event){
$db=connect_db();
if($Event=="PAST"){
$sql="select * from event  where event_date between (NOW()-INTERVAL 7 MONTH) and (NOW()-INTERVAL 1 day)";
//<=last_day(now())+ INTERVAL 1 DAY -INTERVAL 3 MONTH OR event_date<now()";
$exe=$db->query($sql);
$data = $exe->fetch_all(MYSQLI_ASSOC);
$db=null;
echo json_encode($data);
return;
}
else if($Event=="Current"){
$sql="select * from event where event_date>=CURRENT_DATE";
$exe=$db->query($sql);
$data = $exe->fetch_all(MYSQLI_ASSOC);
$db=null;
echo json_encode($data);
return;
}
}
function membershipfeeinfo()
{
$db=connect_db();
$result=array();
$sql="select * from memfeeinfo";
$exe=$db->query($sql);
$data = $exe->fetch_all(MYSQLI_ASSOC);
$db=null;
if($exe==true){
   echo json_encode($data);
	}
	else{
		return;
	}
	
}
?>