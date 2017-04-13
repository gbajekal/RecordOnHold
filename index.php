<?php

//include 'dbconnection.php';
require_once "./common/functions.php";


//*********************************
// Check if the Browser is Chrome
// and if not exit  gracefully
//*********************************


if(strpos($_SERVER['HTTP_USER_AGENT'], 'Chrome') == FALSE) 
			die("<h1> Need a Google Chrome Browser to work with this application </h1>");
//******************************
// Check if it is a GET or POST
// Request
//********************************

if (isset($_GET ))
{
	// read cookie and check if registered earlier
	
	
	   header("Location: ./record/record.php");
		die();
     	   
		
		
	

	
	
} 
else 
{
	 //********************************************************************
    // Request is a POST. If so verify Captcha and register the user_error
    // also communicate any errors if any	
	//**********************************************************************
	
	
	
	
	
}






?>





