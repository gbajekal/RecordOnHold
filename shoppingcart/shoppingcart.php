<?php
//***************************************
// This is a temporary Shopping Cart
// that reads the Recording URLs from 
// the Cookie and displays it to the user
// for checkout
//*****************************************
require_once "../common/functions.php";

if($DEBUG)
	print_r($_COOKIE);

$cookie_name = 'shoppingCart';

if(!isset($_COOKIE[$cookie_name]) )

{
		
  die("Cookie not set");
 }
else
{
  $recordings = $_COOKIE[$cookie_name];
  debugStr($recordings);
    
  $recordingURLs = explode( ';', $recordings);
  
  
  


} 




?>
<!DOCTYPE html>

<html>
	<head>

		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		
		<title>Live input record and playback</title>
		<link rel="stylesheet" href="../css/flex.css">
		 
	</head>
				<body> 
			<div class="container">

			  <?php include "../common/header.php" ?>
			  <?php include "../common/menu.php" ?>
						<article>
						 <p> This is a Dummy Shopping Cart </p>
						 <p> The Shopping Cart contains the following recordings:-</p>
						 <?php
						   $i = 1;
						  foreach($recordingURLs as $url)
						  {
							 $filename = basename($url);
							 if($filename == "")
								 break;
							 echo ('<p>'.$i.') <a href= "'.$url.'">'.$filename.'</a></p>');
							 $i++;
						  }
						  
						 echo '<p><input type="submit"; value="Checkout"></p>';
						 ?>

						 </article>
			 
			 
			  </div>
			  <?php include "../common/footer.php" ?>
			</body>
</html>
