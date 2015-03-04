<?php
/*This program creates a sentiment classifier.  When this program is complete an SQL table will exist called "featureListSentiment".  This table will contain a unique entry for individual words from the trainingCorpus with a polarity calculation for that word.  The primary key is tweetWord + tweetPolarity.  The polarity for each word will add up to 1.*/
session_start();
error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('max_execution_time', 0); 
include_once "functionFile.php" ;
// Use server url, user name, user password and database name
$dbconnection = new mysqli("server","user", "password", "database name");
if(!$dbconnection) {
	die('Connection failed: ' . $dbconnection->error());
}
?>
<html>
<head>
	<!--Use the same stylesheet for all pages twittercss.css-->
	<link href="twittercss.css" rel="stylesheet" type="text/css"/>
	<title>Train Twitter Machine</title>
	<!--Had to use the following character set to display certain characters -->
	<meta http-equiv="Content-Type" content="text/html; charset=windows-1252" />
	<meta name="description" content="Train Twitter Classifier">

</head>
<body>
<div id="wrapper">
<div id="header">
<div class="pagename">Train Twitter Classifier</div>
</div>
<div id="inputDiv"><br /><br /><br />
<?php
//define two arrays
$featureList=array();
$tweetFeatures=array();


//Add the words that we do not wish to analyse to the "stopwords" array - function in functionFile
$stopWords=addStopWordsToArray($dbconnection) ;
//A table "featureVector" is used to hold each word from each tweet and to hold the polarity of that tweet. Firstly remove all existing records.
$sql= "TRUNCATE TABLE featureVector" ;
$result=$dbconnection->query($sql) ;
//Get the training data from corpusTraining table
$sql="SELECT tweetID, tweetText, tweetPolarityInt, tweetSubject FROM corpusTraining" ;
$result=$dbconnection->query($sql) ;
if($result) {
	while ($row = $result->fetch_assoc()) {
		$tweet = $row['tweetText'] ;
		$tweetID= $row['tweetID'] ;
		$tweetSubject=$row['tweetSubject'] ;
		$tweetPolarity=$row['tweetPolarityInt'] ;
		//Call the preProcess function in functionFile - this removes some punctuation and stop words
		$tweet=preProcessTweet($tweet) ;
		//create a vector of words from each $tweet 
		$featureVector=addToFeatureVector($tweet, $stopWords, $tweetSubject) ;
		//store the individual words from that tweet in a table with tweetID as foreign key.  We will be able to get polarity by joining that table
		//to the original table where the training data is stored.
		foreach ($featureVector as $word) {
			if (strlen($word)>0) {
				$sql="INSERT INTO featureVector (tweetID, tweetPolarity, tweetWord) values ('" . $tweetID . "','" . $tweetPolarity . "','" 
				. $word ."')" ;
				$result2=$dbconnection->query($sql) ;
			}
		}
		//add the words in the $featureVector to the feature list (where each word is stored with a probability in the future).
		//Store the feature words and the sentiment for each tweet in array $tweetFeatures. [][]
	}
	$result->close() ;
	//We also insert the words from the lexicon table of positive and negative words as these include many sentiment words
	//that may not have been included in the training data.

	$sql="INSERT INTO featureVector (tweetPolarity, tweetWord) select polarityInt, lexiconWord FROM lexicon" ;
	$result2=$dbconnection->query($sql) ;

	//once all the words are written to the feature vector, now group each word of the same sentiment and assign a probability
	//to that word using the naive bayes formula. Write that word and its sentiment to the table "featureListSentiment".  This is all done in the
	//trainClassifier function
	trainClassifier($dbconnection) ;
} // if($result)
?>
<form method="GET" action="trainFrontScreen.php" >
    <input type="submit" class="submitSmallButton" value="< Back">
</form>

</div>
</div>
</body>