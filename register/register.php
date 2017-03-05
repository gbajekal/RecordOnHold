<?php
//**********************************************************
// Register.php - This module is a registration form that
// surfaces when the cookie on the users client expires
// As we are not using databases or any OAuth authentication,
// we use cookies to store user information
// The form also uses captcha to prevent bots from loading
// content.
// All form input will be checked for Cross-site Ingestion attacks
//*****************************************************************



//include 'dbconnection.php




require_once "../common/functions.php";
$fname = $lname = $email = $phone =  $captcha ="";
$fnameErr = $lnameErr = $emailErr = $phoneErr = $captchaErr = "";


//*****************************************
// This function validates the form fields
// for special chars and escapes them
//******************************************

function test_input($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
  
};
//*****************************************
// This function validates the form &
// sets appropriate error message
//******************************************

function validateForm()
{
	global $fname, $lname, $email, $phone, $captcha;
	global $fnameErr, $lnameErr, $emailErr, $phoneErr, $captchaErr;
	$status = true;
	
	debugStr(" In Validate form");
	if( empty($_POST["fname"]) )
	{   
		debugStr("First Name is Empty");
		$fnameErr = "First Name is required";
		$status = false;
	}
	else
	{
		 
		$fname = test_input($_POST["fname"]);
		
	}
	
	if( empty($_POST["lname"]) )
	{
		 $lnameErr = "Last Name is required";
		 debugStr("Last Name is Empty");
		 $status = false;
	}
	else
	{
		 $lname = test_input($_POST["lname"]);
		
	}
	
	if( empty($_POST["email"]) )
	{
		$emailErr = "Email is required";
		 debugStr("Email is Empty");
		$status = false;
	}
	else
	{
		$email = test_input($_POST["email"]);
		
	}
	
	if( empty($_POST["phone"]) )
	{
		$phoneErr = "Phone is required";
		debugStr("Phone is Empty");
		$status = false;
	}
	else
	{
		$phone = test_input($_POST["phone"]);
		
	}
	
	if( empty ($_POST["captcha"]) )
	{
		debugStr("Captcha is Empty");
		$captchaErr = "Captcha needs to be entered!";
		$status = false;
	}
	else
	{
	  $captcha = $_POST["captcha"];	
	 
	}
	
	debugStr("Status = ".$status);
	return $status;
}


//**********************************************
// This function validates the captcha entered
// by the user versus one generaed by the system
//************************************************ 

function validateCaptcha()
{
   global $captcha, $captchaErr;
   session_start();
   debugStr("Entered Captcha = " .$captcha);
   debugStr("System Capcha = ".$_SESSION['digit']);
   
    
    if($captcha != $_SESSION['digit'])
    {		
      $captchaErr = "Sorry, the CAPTCHA code entered was incorrect!";
		
	//die();
	 session_destroy();

	 return false;
	}
else
{
   debugStr("Captcha Matches");
   $captchaErr = "";
    

   return true;
	
	
}
	
   

}



			  
	
	
	
	


//*********************************************
// Updates Cookies with User name and Email
//**********************************************
function updateCookies()
{
  debugStr("Entered updateCookies() ");
  global $fname, $lname, $email;

   $cookie_name = "user_fname";
   $cookie_value = $fname;
   setcookie($cookie_name, $cookie_value, time() + (86400 * 1), "/"); // 86400 = 1 day  
   
   $cookie_name = "user_lname";
   $cookie_value = $lname;
   setcookie($cookie_name, $cookie_value, time() + (86400 * 1), "/"); // 86400 = 1 day 
   
   $cookie_name = "user_email";
   $cookie_value = $email;
   setcookie($cookie_name, $cookie_value, time() + (86400 * 1), "/"); // 86400 = 1 day 
	
  debugStr("Exited updateCookies() ");
	
}



//******************


if($_SERVER["REQUEST_METHOD"] == "POST")
{
	//**********************************************************
	// If thisis a form resubmission do the following
	// a) Validate the form fields
	// b) Validate captcha
	// c) Flag errors if necessary
	// If all OK then do the following
	// d) Register user
	// e) Set cookies
	// f) Redirect to index page which will redirect to recorder
	//*************************************************************
	
	if( validateForm() == true)
	{
		debugStr("Form Validated");
		if( validateCaptcha() == true)
		{
		   
		   debugStr("OK to register User");
		   
		    
			
			updateCookies();
			
			header("Location: ../index.php");
		    die();
		   
              		   
			
		}
		else
		{
		  debugStr("Captcha could not be validated");	
			
		}
	
	}
else
{
     debugStr("Form could not be validated");		
}
	
	
	
}




?>

<html>
	<head>
	 <link rel="stylesheet" href="../css/flex.css">
	 <meta name="viewport" content="width=device-width, initial-scale=1.0">
	</head>
		<body>
		 <?php include "../common/header.php" ?>
		 <?php include "../common/menu.php" ?>
		 <article>
				 <h1>Welcome to the Portal. Please Register with the Portal </h1>
				 <form  method="post" name="registration"  action="register.php" >
				 <table>
					<tr><th>First Name:</th><td><input name="fname" value = " <?php echo $fname; ?>"><span class ="error"><?php echo '*'. $fnameErr;?></span></td></tr>
					<tr><th>Last Name:</th><td><input name="lname" value = " <?php echo $lname; ?>"><span class ="error"><?php echo '*'.$lnameErr;?></span></td></tr>
					<tr><th>Email:</th><td><input name="email" value = " <?php echo $email; ?>"><span class ="error"><?php echo'*'. $emailErr;?></span></td></tr>
					<tr><th>Phone:</th><td><input name="phone" value = " <?php echo $phone; ?>"><span class ="error"><?php echo '*'.$phoneErr;?></span></td></tr>
					<tr><td></td><td>* - required field</td></tr>
					<tr><td></td><td></td></tr>
					<tr><td></td><td></td></tr>
					<tr><td></td><td></td></tr>
					<tr><th></th><td><img src="captcha.php" width="120" height="30" border="1" alt="CAPTCHA"></td></tr>
					<tr><th></th><td><input type="text" size="6" maxlength="5" name="captcha" value=""><span class ="error"><?php echo '*'.$captchaErr;?></span></td></tr>
					<tr><th></th><td><small>copy the digits from the image into this box</small></td></tr>
					<tr><td></td><td></td></tr>
					<tr><td></td><td></td></tr>
					<tr><th></th><td><input type="submit"  value="Submit"></td></tr>
				</table>
				</form>
	  </article>
<?php include "../common/footer.php" ?>

	
	
	
	
		</body>
</html>