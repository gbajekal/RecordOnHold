<?php
require_once 'vendor\autoload.php';
require_once  '../common/functions.php';
require_once  'vendor\microsoft\windowsazure\src\WindowsAzure.php';


use WindowsAzure\Common\ServicesBuilder;
use MicrosoftAzure\Storage\Blob\Models\CreateContainerOptions;
use MicrosoftAzure\Storage\Blob\Models\PublicAccessType;
use MicrosoftAzure\Storage\Common\ServiceException;



echo "In UploadBlob";



$containerName = getUserName();
$containerName = strtolower($containerName);
$containerName = str_replace(' ', '', $containerName);

echo "Container Name = ".$containerName;


if( empty($containerName) )
{
	$containerName = "defaultcontainer";
//die("Container Name is Empty");
	
}
$accountName="spotonstorage";

$connectionString = "DefaultEndpointsProtocol=http;AccountName=spotonstorage;AccountKey=uF6zPa+h+eAxLaudTWBQo+nFkN5GzK2cFOZUmiurVUKkbW/ySfAD/7i+FTUinyFliuuFYzq8SVFtmOuY0W4oiw==";

$blobRestProxy = ServicesBuilder::getInstance()->createBlobService($connectionString);
// OPTIONAL: Set public access policy and metadata.
// Create container options object.
$createContainerOptions = new CreateContainerOptions();

// Set public access policy. Possible values are
// PublicAccessType::CONTAINER_AND_BLOBS and PublicAccessType::BLOBS_ONLY.
// CONTAINER_AND_BLOBS:
// Specifies full public read access for container and blob data.
// proxys can enumerate blobs within the container via anonymous
// request, but cannot enumerate containers within the storage account.
//
// BLOBS_ONLY:
// Specifies public read access for blobs. Blob data within this
// container can be read via anonymous request, but container data is not
// available. proxys cannot enumerate blobs within the container via
// anonymous request.
// If this value is not specified in the request, container data is
// private to the account owner.
$createContainerOptions->setPublicAccess(PublicAccessType::CONTAINER_AND_BLOBS);

// Set container metadata.
$createContainerOptions->addMetaData("key1", "value1");
$createContainerOptions->addMetaData("key2", "value2");

try    {
    // Check if container exists
	$blobRestProxy->getContainerProperties($containerName);
	echo "Container already Exists!!";
    
}
catch(ServiceException $e){
    // Handle exception based on error codes and messages.
    // Error codes and messages are here:
    // http://msdn.microsoft.com/library/azure/dd179439.aspx
    $code = $e->getCode();
	if( $code = "404")
	{
		$blobRestProxy->createContainer($containerName, $createContainerOptions);
	    echo "Container " .$containerName .  " created in storage account";
	}
   else
   {
    $error_message = $e->getMessage();
    echo $code.": ".$error_message."<br />";
   }
}

// Now add a BLOB to the container
// pull the raw binary data from the POST array
//if(!is_dir("recordings")){
	//$res = mkdir("recordings",0777); 
}

// pull the raw binary data from the POST array
$data = substr($_POST['data'], strpos($_POST['data'], ",") + 1);
// decode it
$decodedData = base64_decode($data);
// print out the raw data, 
//echo ($decodedData);
$filename = 'audio_recording_' . date( 'Y-m-d-H-i-s' ) .'.mp3';
// write the data out to the file
//$fp = fopen('recordings/'.$filename, 'wb');
//fwrite($fp, $decodedData);
//fclose($fp);



// print out the raw data, 
//echo ($decodedData);
$blob_name = 'audio_recording_' . date( 'Y-m-d-H-i-s' ) .'.mp3';
$url = "https://".$accountName.".blob.core.windows.net/".$containerName."/".$blob_name;

try    {
    //Upload blob
    $blobRestProxy->createBlockBlob($containerName, $blob_name, $decodedData);
	echo 'Uploaded recording at the following link <a href=' .'"'.$url .'">'.$blob_name."</a>";
	
}
catch(ServiceException $e){
    // Handle exception based on error codes and messages.
    // Error codes and messages are here:
    // http://msdn.microsoft.com/library/azure/dd179439.aspx
    $code = $e->getCode();
    $error_message = $e->getMessage();
    echo $code.": ".$error_message."<br />";
}





?>