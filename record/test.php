<?php

require_once 'vendor\autoload.php';
require_once  '../common/functions.php';
require_once  'vendor\microsoft\windowsazure\src\WindowsAzure.php';


use WindowsAzure\Common\ServicesBuilder;
use MicrosoftAzure\Storage\Blob\Models\CreateContainerOptions;
use MicrosoftAzure\Storage\Blob\Models\PublicAccessType;
use MicrosoftAzure\Storage\Common\ServiceException;




echo "In UploadBlob";



$containerName = "defaultcontainer";
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

?>