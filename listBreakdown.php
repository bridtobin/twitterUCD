<!--Program to display how sentiment has been calculated by displaying contents of tweetFeatureVector, joining it with featureListSentiment
and recalculating sentiment probability-->
<html>
<head>
<script>
</script>
</head>
<body>
<?php
$dbconnection = new mysqli("csserver.ucd.ie","bdelap", "uryafql1", "bdelap");
if(!$dbconnection) {
	die('Connection failed: ' . $dbconnection->error());
}
//q is sent as a parameter from the function showBreakdown() in googleChart.php
$q = $_GET['q'];
$sql="SELECT tweetID, tweetFeatureVector.tweetWord as tweetWord, tweetPolarityInt, SUM( tweetProbability ) AS sumProb
				FROM tweetFeatureVector
				JOIN featureListSentiment ON tweetFeatureVector.tweetWord = featureListSentiment.tweetWord 
				WHERE tweetID=\"" . $q . "\"" .
				"GROUP BY tweetID, tweetFeatureVector.tweetWord, tweetPolarityInt" ;
				
$result=$dbconnection->query($sql) ;
echo "<strong>Breakdown of tweet polarity</strong><br>" ; 
echo "<select name=\"listbreakdown\" id=\"listbreakdown\"  size=14 >" ;	
      while ($row = $result->fetch_assoc()) {
		  echo "<option value = '" . $row['tweetID'] . "'>" ;
		  echo $row['tweetPolarityInt'] . "&nbsp;&nbsp;&nbsp;" .  $row['tweetWord'] . "&nbsp;&nbsp;" .$row['sumProb'] ;
		  echo "</option>" ;
	 }	
?>
</select>
</body>
</html>


