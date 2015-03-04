<?php
header('Content-Type: text/html; charset=utf-8');

session_start();
error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('max_execution_time', 0); 
$dbconnection = new mysqli("csserver.ucd.ie","bdelap", "uryafql1", "bdelap");
if(!$dbconnection) {
	die('Connection failed: ' . $dbconnection->error());
}
$_SESSION['tool'] = "bridDelap" ;
?>
<!DOCTYPE html>
<html lang='en'>
<head>
	<!--Use the same stylesheet for all pages twittercss.css-->
	<link href="twittercss.css" rel="stylesheet" type="text/css"/>
	<title>Twitter Sentiment Analysis</title>
	<!--Had to use the following character set to display certain characters -->
	<meta http-equiv="Content-Type" content="text/html; charset=windows-1252" />
	<meta name="description" content="Twitter Sentiment Analysis">
    <script>

		function hourglass()  {
            document.body.style.cursor  = 'wait'; 
			document.getElementById("waitBar").style.visibility="visible" ;
		}
		
		function defaultcursor()  {
		   document.body.style.cursor  = 'default'; 
		}
		
		function validateInput() {
			var searchPhrase = document.getElementById('searchPhrase').value;
			var numberTweets = document.getElementsByName("noOfTweets") ; 
			//check if no of tweets entered
			var ischecked_number = false;
			for ( var i = 0; i < numberTweets.length; i++) {
  			if(numberTweets[i].checked) {
       			   ischecked_number = true;
       			  break;
				}
			 }
	 		 //check if classifier type entered
			 var getClassifier=document.getElementsByName('typeAnalysis') ;
			 var ischecked_classifier = false;
			 for ( var i = 0; i < getClassifier.length; i++) {
    			if(getClassifier[i].checked) {
       			   ischecked_classifier = true;
       			   break;
				}
			 }
 			 if (searchPhrase==null || searchPhrase=="" || ischecked_number==false || ischecked_classifier==false)  {
				 alert ("You must select which classifier, how many tweets and enter a search phrase") ;
				 return false;
			 } else {
				hourglass();
				return  true;
    		}
		}
	</script>
</head>
<body>
	 	<div id="wrapper">
		<div id="header">
  		<div class="pagename">Twitter Sentiment Analysis</div>
        </div><br>
        <div id="wrapTop">
 		<div id="inputDiv">
	<form method="post" >
    	<strong>
    	<input type="radio" name="typeAnalysis"  required value="bridDelap" checked="checked">Brid's Analysis Tool&nbsp;&nbsp;
		<input type="radio" name="typeAnalysis" value="sentiment140">Sentiment 140<br><br>
 		<?php 	

        echo "Search Phrase&nbsp;&nbsp;<input type=\"text\" id=\"searchPhrase\" name=\"searchPhrase\"" ; 
		if(!empty($_POST['searchPhrase'])) {
			echo "value=\"" . $_POST['searchPhrase'] . "\"" ;
		}
		echo "><br><br>" ; 
		?> 
        No of tweets *
        <input type="radio" name="noOfTweets" required value=100 checked="checked">100&nbsp;&nbsp;
		<input type="radio" name="noOfTweets" required value=500>500&nbsp;&nbsp;
		<input type="radio" name="noOfTweets" required value=1000>1000&nbsp;&nbsp;
     	<input type="radio" name="noOfTweets" required value=2000>2000&nbsp;&nbsp;
 		<input type="radio" name="noOfTweets" required value=3000>3000&nbsp;&nbsp;
 		<div class="smallItalicFont">(20 seconds per thousand)</div>
        <br></strong><br>
        * Please note that there are twitter limitations.  Fewer tweets may be returned.<br><br>
        
        <input type="submit"  class="submitButton" value="Download & Classify"  name="submitButton" 
         onClick="return validateInput();"/>
    </form>
      <div id="waitBar"><img src="ajax-loader.gif"></div>
	  </div>
</div>
<?php      
	/*if(empty($_POST['submitButton'])) {
	  echo"<div id=\"chart_div\"></div></div>" ;
	}*/




if (!empty($_POST['submitButton'])) { 
	if(!empty($_POST['searchPhrase'])) {
		$_SESSION['tool']=$_POST['typeAnalysis'] ;
		$countFromTwitter=0;
		include_once "addTweetsToTable.php" ;
		if($countFromTwitter>0) {
			if ($_POST['typeAnalysis']=="sentiment140") {
				include_once "sentiment140.php" ;
			} else {
				include_once "classifyTweets.php" ;
			}
			include_once "googleChart.php" ;
		} 
	}
} 
?> 
</div>	
<script>defaultCursor()</script>
</body>
</html>
