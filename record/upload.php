<?php
  ob_start();
  // ... my code here

require_once  '../common/functions.php';

echo "In upload.php <br>";
echo "RecId=".$_POST['recId'];
echo "Content=".$_POST['content'];


$containerName = getUserName();
if(empty($containerName))
{
    $containerName = strtolower($containerName);
    $containerName = str_replace(' ', '', $containerName);
}
else
{
    // Temporary directory 
    $containerName="tmp";
}


$folderName = "recordings/".$containerName;


try
{
if(!is_dir($containerName)){
	$res = mkdir($folderName,0777,true); 
	echo "Created Folder= ".$containerName;
}
else
{
	echo "Folder exists <br>";
}

}
catch(Exception $e)
{
 echo "Folder could not be created";	
	
	
}

// pull the raw binary data from the POST array
$data = substr($_POST['content'], strpos($_POST['content'], ",") + 1);
// decode it
$decodedData = base64_decode($data);
// print out the raw data, 
echo ("Server Name=".$_SERVER['SERVER_NAME']);

//*********************************************
// GRB: Get the filename from the POST parameters
// and if empty create a new one
//**********************************************
$filename = $_POST['recId'];
if( empty($filename) )
	$filename = 'audio_recording_' . date( 'Y-m-d-H-i-s' ) .'.mp3';
// write the data out to the file
$fp = fopen($folderName.'/'.$filename, 'wb');
fwrite($fp, $decodedData);
fclose($fp);

//***********************************************
// Create the URL and add it to the Cookie
// for further processing
//***********************************************
$cookie_name = 'recordings';
if($DEBUG)
	$recordingURL = 'https://'.$_SERVER['SERVER_NAME']."/recordonhold/record/".$folderName."/".$filename;
else
	$recordingURL = 'https://'.$_SERVER['SERVER_NAME']."/record/".$folderName."/".$filename;

 //*******************************
 // ; delimited cookie to 
// store recording URLs on server
//********************************
	$recordingURL = $_COOKIE[$cookie_name].$recordingURL .',';
	setcookie($cookie_name, $recordingURL, time() + (86400 * 30), "/");

	




echo 'Stored file at the following link <a href="'.$recordingURL.'">'.$filename.'</a>';

ob_flush();

?>
