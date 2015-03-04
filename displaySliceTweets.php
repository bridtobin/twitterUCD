<!-- This program is used to display a list of tweets related to a slice in the pie chart.  The polarity code is passed to this
program and the a select statement is called using that code.  An HTML select option list is then displayed showing these tweets-->
<html>
<head>
	<!--Use the same stylesheet for all pages twittercss.css-->
	<link href="twittercss.css" rel="stylesheet" type="text/css"/>
	<title>Twitter Sentiment Analysis</title>
	<!--Had to use the following character set to display certain characters -->
	<meta http-equiv="Content-Type" content="text/html; charset=windows-1252" />
	<meta name="description" content="Twitter Sentiment Analysis">
<script>
</script>
</head>
<body>
<?php
session_start() ;
//q is passed as a parameter to this program from googleChart.php - selectHandler() function.
$q=$_GET['q'] ;
switch ($q) {
    case "Positive" :
		$polarity= 4;
        break;
    case "Negative" :
		$polarity= 0;
        break;
    case "Neutral" :
		$polarity= 2;
        break;
	/*case "Not Analysed" ;
		$polarity=5;
		break;*/
	}
//The session variable holding the database connection does not seem to hold when calling this program.  Session variables with primitive types
//seem to exist but not object variables.  We therefore have to open the database connection again.
$_SESSION['dbconnection'] = new mysqli("csserver.ucd.ie","bdelap", "uryafql1", "bdelap");
if(!$_SESSION['dbconnection']) {
	die('Connection failed: ' . $_SESSION['dbconnection']->error());
}

//The tweets with polarity 0,2 and 4 are selected in one way.  The "Not Analysed" tweets which I have given a polarity of 5 actually contain
//a null value in the tweetPolarity field, therefore, we need to select it differently.
if($polarity<>2) {
   $sql="SELECT tweetID, tweetText, tweetPolarity from tweets where tweetPolarity=\"" . $polarity . "\"" ;
} else {
	$sql="SELECT tweetID, tweetText, if(isnull(tweetPolarity),2,tweetPolarity) as tweetPolarity from tweets where isnull(tweetPolarity) " .
	" or tweetPolarity=2"	;
}
$result=$_SESSION['dbconnection']->query($sql) ;
//Different select statements for sentiment140 classifier.
//if we are using the sentiment140 classifier as we do not hold the breakdown of how the sentiment was calculated.
echo "<div id=\"listTweets\">" ;
if($_SESSION['tool']=="sentiment140") {
	echo "<script type='text/javascript'>alert('". $sql . "');</script>";
	echo "<strong>Breakdown of tweets is not available from Sentiment140</strong><br>" ;
	echo "<select name=\"listTweets\"  title=\"Double Click tweet for breakdown\"  size=14 >" ;	
} else {
	echo "<strong>Double click tweet to view breakdown of " . $q .  " polarity</strong><br>" ; 
	echo "<select name=\"listTweets\"  title=\"Double Click tweet for breakdown\"  size=14 onchange=\"showBreakdown(this.value)\">" ;
}
while ($row = $result->fetch_assoc()) {
  echo "<option value = '" . $row['tweetID'] . "'>" ;
  echo $row['tweetText'] ;
  echo "</option>" ;
}	

?>
</select>
</div>
</body>
<?php
   mysqli_close($_SESSION['dbconnection']) ;
?>

</html>


