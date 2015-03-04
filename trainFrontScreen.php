<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('max_execution_time', 0); 

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
//This function displays an hourglass and the wait bar when a user selects to trian the classifier.
function hourglass()  {
    document.body.style.cursor  = 'wait'; 
	document.getElementById("waitBar").style.visibility="visible" ;
}

function validateInput() {
	//check if no of tweets entered
	 var getTable=document.getElementsByName('whichTable') ;
     var ischecked_table = false;
	 for ( var i = 0; i < getTable.length; i++) {
  		if(getTable[i].checked) {
    		ischecked_table = true;
       		break;
		}
	 }
	 if (ischecked_table==false)  {
		alert ("You must select which table to view/change") ;
		return false;
	 } else {
		return true;
	}
}		

function ConfirmTrainClassifier()    {
      	var x = confirm("This action will take 5 seconds per 1000 records in Training Corpus.  Are you sure you wish to train classifer?  " );
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
<div class="pagename">Twitter Sentiment Analysis</div></div>
<!--Main screen for classifier maintenance-->
<div id="inputDiv">
<form method="post" name="trainFrontScreen" action="showList.php">
		<br>The following tables are used to train the classifier.  
        If you wish to view/change those tables - choose which table.<br/><br/>
    	<input type="radio" name="whichTable"  required value="corpusTraining">Tweets Training Corpus<br>
		<input type="radio" name="whichTable" required value="lexicon" >Dictionary<br>
   		<input type="radio" name="whichTable" required value="stopWords">Stop Words<br><br>
  		<input type='submit' class="submitButton" value='View/Change' name='trainClassifierButton' Onclick="return validateInput();"/><br />
 </form>
 <form method="post" name="importTrainingScreen" action="getFileToUpload.php">
 		<br>You can also import a new text file to the training database.<br/><br/> 
   		<input type='submit' class="submitButton" value='Import Training Text File' name='importButton'  /><br />
</form>
 <form method="post" name="trainClassifier" action="trainClassifier.php">
 		<br>Train the classifer. Please be aware this will take approximately 5 seconds per 1000 records in Training Corpus.<br/><br/> 
   		<input type='submit' class="submitButton" value='Train Classifier' name='trainClassifer' Onclick="return ConfirmTrainClassifier();"  />
        <br><br>
        <div id="waitBar"><img src="ajax-loader.gif"></div>
</form>
</div>
</div>
</body>
</html>