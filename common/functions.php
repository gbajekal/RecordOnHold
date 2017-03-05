<?php

//******************************************************
// This is a functions file that contains utility
// functions
//********************************************************
include_once "debug.php";


function debugStr($message)
        {
            global $DEBUG;
            if($DEBUG)
              {
                echo $message.'<br>';
                syslog(LOG_INFO, $message);
                }
        };
        


//**********************************************************
// Check user registered - Checks if the user is registered
// by checking the cookie If user has previously registered,
// it returns true else false;
//*************************************************************

function isUserRegistered()
{
   $statusFlag = FALSE;
   
   // if user has previously registered, return true;
   if( isset($_COOKIE["user_email"] ))
      $statusFlag = TRUE;
	  
return $statusFlag;

};

//******************************
// Gets the user name from Cookie
//*********************************
function getUserName()
{
	$fname = $_COOKIE["user_fname"];
	$lname  =$_COOKIE["user_lname"];
	$full_name = $fname . ' '. $lname;
	return $full_name;
	
	
	
}

//***************************************
// Renders Registration form for user to 
// register with portal. Form will
// also include Capctha
//****************************************
function renderRegistrationForm()
{
	debugStr("Entered renderRegForm()");
	
	echo '<h1>Please Register with the Portal</h1>';
	validateForm();
	echo '<body>';
	echo '<form onSubmit="checkForm();" method="post" name="registration"  action="index.php" >';
	
	echo '<table>';
	echo  '<tr><th>First Name*:</th><td><input name="fname"></td></tr>';
	echo  '<tr><th>Last Name*:</th><td><input name="lname"></td></tr>';
	echo  '<tr><th>Email*:</th><td><input name="email"></td></tr>';
	echo  '<tr><th>Phone*:</th><td><input name="phone"></td></tr>';
	echo '<tr><td></td><td></td></tr>';
	echo '<tr><td></td><td></td></tr>';
	echo '<tr><td></td><td></td></tr>';
	echo '<tr><td></td><td></td></tr>';
	echo '<tr><th></th><td><img src="captcha.php" width="120" height="30" border="1" alt="CAPTCHA"></td></tr>';
	echo '<tr><th></th><td><input type="text" size="6" maxlength="5" name="captcha" value=""></td></tr>';
	echo '<tr><th></th><td><small>copy the digits from the image into this box</small></td></tr>';
	echo '<tr><td></td><td></td></tr>';
	echo '<tr><td></td><td></td></tr>';
	echo  '<tr><th></th><td><input type="submit"  value="Submit"></td></tr>';
	echo '</table>';
	
	
	
	
	
	
	echo '</form>';
	echo '</body>';
	echo '</html>';
	
	
	
	
	debugStr("Exited renderRegForm()");
}





?>