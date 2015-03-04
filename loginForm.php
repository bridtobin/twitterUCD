<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('max_execution_time', 0); 
$_SESSION['loggedIn']=0;
?>
<!DOCTYPE html>
<html>
<head>
	<!--Use the same stylesheet for all pages twittercss.css-->
	<link href="twittercss.css" rel="stylesheet" type="text/css"/>
	<title>Train Twitter Machine</title>
	<!--Had to use the following character set to display certain characters -->
	<meta http-equiv="Content-Type" content="text/html; charset=windows-1252" />
	<meta name="description" content="Twitter Sentiment Analysis">

<script>

function ConfirmTrainClassifier()    {
      	var x = confirm("This action will take 30 seconds per 1000 records in Training Corpus.  Are you sure you wish to train classifer?  " );
      	if (x) {
			  hourglass();
        	  return true;
		} else {
        	return false;
		}
}

</script>
</head>
<body>

<div id="wrapper">
<div id="header">
<div class="pagename">Train Twitter Classifier</div></div>
<div id="inputDiv">
<br><br>
<?php 
if(!empty($_POST['loginButton'])) {
	$dbconnection = new mysqli("[insert server name]",$_POST['userName'], $_POST['passwordField'], "[insert database name]");
	if ($dbconnection->connect_error) {
		echo "Login Failed. Please Try again" ;
		$_SESSION['loggedIn']=0 ;
	} else {
		$_SESSION['loggedIn']=1 ;
	}
}
if($_SESSION['loggedIn']==0) {
?>
	<form method="post" name="loginScreen">
		<div align="center">
			User Name&nbsp;<input type="text" name="userName"><br><br>
			Password&nbsp;&nbsp;&nbsp;<input type="password" name="passwordField"><br><br><br>
		<input type='submit' class="submitButton" value='Login' name='loginButton' /><br />
		</div>
 	</form>
<?php
} else {
?>
	<form method="post" name="loginScreen" action="trainFrontScreen.php">
		<div align="center">
		Successful login<br><br>
		<input type='submit' class="submitButton" value='Continue' name='continueButton' /><br />
		</div>
 	</form>
<?php	 
}
?>
 </div>
 </div>
</body>
</html>