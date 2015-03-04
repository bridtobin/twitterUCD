<!--Program to upload a text file.  Once file is uploaded successfully, records are inserted into corpusTraining -->
<html>
<head>
	<!--Use the same stylesheet for all pages twittercss.css-->
	<link href="twittercss.css" rel="stylesheet" type="text/css"/>
	<title>Train Twitter Machine</title>
	<!--Had to use the following character set to display certain characters -->
	<meta http-equiv="Content-Type" content="text/html; charset=windows-1252" />
	<meta name="description" content="Twitter Sentiment Analysis">
<script>

function ConfirmReplace() {
	 if (document.getElementById('replaceTraining').checked) {
      	var x = confirm("Are you sure you want to replace existing training data?");
      	if (x)
        	  return true;
      	else
        	return false;
	 } else {
		return true;
	 }
}

function validateInput() {
	//check if no of tweets entered
	 var getImportType=document.getElementsByName('appendOrReplace') ;
     var ischecked_type = false;
	 for ( var i = 0; i < getImportType.length; i++) {
  		if(getImportType[i].checked) {
    		ischecked_type = true;
       		break;
		}
	 }
	 if (ischecked_type==false)  {
		alert ("You must select whether to append or replace existing corpus") ;
		return false;
	 } else {
		 if(ConfirmReplace()) {
			return  true;
		 }
	}
}

</script>
</head>
<body>
	<div id="wrapper">
    <div id="header">
	<div class="pagename">Upload Training Corpus text file (See manual for file format)</div></div>
    <div id="inputDiv">
	<form enctype="multipart/form-data"  method="POST">
	<input type="hidden" name="MAX_FILE_SIZE" value="300000000" />
	Choose a file to upload: <input name="uploadedfile" type="file" /><br />
	<input type="radio" name="appendOrReplace" id="appendToTraining" value="appendToTraining">Append to the existing Training Database<br>
	<input type="radio" name="appendOrReplace" id="replaceTraining" value="replaceTraining">Replace the existing Training Database<br><br>
	<input type="submit" name="uploadButton" class="submitButton" value="Upload Chosen File" Onclick="return validateInput();"/>
	</form>
<?php

if (!empty($_POST['uploadButton'])) { 
	ini_set('max_execution_time', 0); 
	include_once "functionFile.php" ;
	$_SESSION['dbconnection'] = new mysqli("csserver.ucd.ie","bdelap", "uryafql1", "bdelap");
	if(!$_SESSION['dbconnection']) {
		die('Connection failed: ' . $_SESSION['dbconnection']->error());
	}
	$target_path = "uploads/";

	// Add the original filename to our target path.  Result is "uploads/filename.extension" 
	$target_path = $target_path . basename( $_FILES['uploadedfile']['name']); 
	if(move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $target_path)) {
/*    echo "The file ".  basename( $_FILES['uploadedfile']['name']). 
    " has been uploaded";*/
	
		if (($handle = fopen($target_path, "r")) !== FALSE) {
			$countLines=0 ;
    		while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
				if(isset($data[0]) and isset($data[1]) and isset($data[2]) and isset($data[3])) {
					if($countLines==0) {
						if($_POST['appendOrReplace']=="replaceTraining") {
							$sql="TRUNCATE TABLE corpusTraining" ;
							$result=$_SESSION['dbconnection']->query($sql) ;
							if(!$result) {
								errorFunction($_SESSION['dbconnection']) ;
								break;
							} // if (!result)
						} // if($_POST['appendOrReplace']......
					} // if($countLines==1)
					$sql="INSERT INTO corpusTraining (tweetID, tweetText, tweetSubject, tweetPolarityInt ) values (\"" .
					//replace double quotes with single quotes - causes formatting problems.
					$data[0] . "\",\"" . str_replace("\"","'",$data[1]) . "\",\"" . str_replace("\"","'",$data[2]) . "\"," . $data[3] .")" ;
      				$result = $_SESSION['dbconnection']->query($sql); 
				
					if (!$result) {
						errorFunction($_SESSION['dbconnection']) ;
					} else { // if(!$result3)
						$countLines++ ;
					}
					//echo "</div>" ;				

				} else {
					msgHandling("There was an error with this file on Line " . $countLines) ;
					break ;
				} // if(isset($data[0])....  
    		} // while
    		fclose($handle); 
			msgHandling("Process Complete.  " . $countLines . " Tweets have been added to Training Corpus") ;
		} // if (($handle = fopen($target_path, "r")) !== FALSE)
	} else{
    	msgHandling("There was an error uploading the file, please try again!") ;
	} //(move_uploaded_file($_FILES......
  } // if(!empty($_POST['uploadButton']))....
?>
<form method="GET" action="trainFrontScreen.php" >
    <input type="submit" class="submitSmallButton" value="< Back">
</form>
</div>
</div>
</body>
</html>
