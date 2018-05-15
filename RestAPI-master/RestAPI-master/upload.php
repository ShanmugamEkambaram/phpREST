<?php
require 'mysql.php';

$target_dir ="files/";

// $upload_url = 'F:wamp64wwwCVIACAppAPIImage';

$MemID = $_POST["consultant_id"];
$shan=$_POST["shan"];
$ram=$_POST["ram"];
$sethu=$_POST["sethu"];

$fileinfo = pathinfo($_FILES["fileToUpload"]['name']);

// getting the file extension

$extension = $fileinfo['extension'];
//$response = array();

// file path to upload in the server

$file_path = $target_dir . '.' . $extension;
$target_file =$target_dir . basename($_FILES["fileToUpload"]["name"]);

// file url to store in the database

$uploadOk = 1;
$imageFileType = pathinfo($target_file, PATHINFO_EXTENSION);

// Allow certain file formats

if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif")
	{
	$result=array();
	$result['code']=1004;
	$result['desc']="Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
 	echo json_encode($result);
	return;
 	//echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";

	$uploadOk = 0;
	}

// Check if image file is a actual image or fake image

if (isset($_POST["submit"]))
	{
	$check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
	if ($check !== false)
		{
	$result=array();
	$result['code']=1005;
	$result['desc']="File is an image - " . $check["mime"] . ".";
		echo json_encode($result); 
		return;
		$uploadOk = 1;
		}
	  else
		{
	$result=array();
	$result['code']=1006;
	$result['desc']="File is not an image.";
		echo json_encode($result); 
		 return;
		$uploadOk = 0;
		}
	}

// Check if file already exists

/**if (file_exists($target_file))
	{
	$result=array();
	$result['code']=1007;
	$result['desc']="Sorry, file already exists.";
	 echo json_encode($result);
	 
	$uploadOk = 0;
	}**/

// Check file size

if ($_FILES["fileToUpload"]["size"] > 500000)
	{
	$result=array();
	$result['code']=1008;
	$result['desc']="Sorry, your file is too large.";
	 echo json_encode($result);
	 return;
	

	$uploadOk = 0;
	}

// Allow certain file formats

if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif")
	{
	$result=array();
	$result['code']=1009;
	$result['desc']="Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
	 echo json_encode($result);
    return;
	$uploadOk = 0;
	}

// Check if $uploadOk is set to 0 by an error

if ($uploadOk == 0)
	{
	
	$result=array();
	$result['code']=1011;
	$result['desc']="Sorry, your file was not uploaded.";
	 echo json_encode($result);
     return;

	// if everything is ok, try to upload file

	}
  else
	{
	if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file))
		{

		// $empcode = $_POST['emp_code'];

		updateToDB($MemID, $target_file);

		
		}
	  else
		{
	$result=array();
	$result['code']=1012;
	$result['desc']="Sorry, there was an error uploading your file.";
 	echo json_encode($result);
		//echo $response;
		return;
		}
	}

function updateToDB($MemID,$target_file)
	{

	//  $push_id=$data['push_id'];

	$result = array();
	$db = connect_db();
	$Image_url='http://'.$_SERVER['HTTP_HOST'].'/S4I/'.$target_file;
	$sql = "update membership set image_url='$Image_url'" . " where MemID='$MemID'";
	$exe = $db->query($sql);
	$last_id = $db->affected_rows;
	$db = null;
	if (!empty($last_id)){
	
		$result['code'] = 0;
		$result['imageUrl'] =$Image_url;
		$result['desc'] = "success";
		}
	  else
		{
		$result['code'] = 1003;
		$result['desc'] = "MemID not found";
		}

	echo json_encode($result);
	}

?>