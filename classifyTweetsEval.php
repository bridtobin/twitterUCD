<?php
//This file is used to classify the tweets download from twitter and assign a sentiment.
include_once "evalfunctionFile.php" ;
//open the database connection
$featureList=array();
$tweetFeatures=array();
//Add stopwords to array
$dbconnection = new mysqli("csserver.ucd.ie","bdelap", "uryafql1", "bdelap");
if(!$dbconnection) {
	die('Connection failed: ' . $dbconnection->error());
}
$stopWords=addStopWordsToArray($dbconnection) ;

//Remove any existing records from the table tweetFeatureVector.
$sql= "TRUNCATE TABLE tweetFeatureVector" ;
$result=$dbconnection->query($sql) ;

/* select tweets from tweets table and insert the words in to a table called "tweetFeatureVector" but running the functions first to preprocess and
remove stopword which is all carried out in preProcess */
$sql="SELECT tweetID, tweetText, tweetSubject from tweets" ;
$result=$dbconnection->query($sql) ;

if($result) {
	while ($row = $result->fetch_assoc()) {
//	Preprocess the tweet - convert to lower case, convert @username to at_user, remove all spaces greater than one.
		$tweet=preProcessTweet($row['tweetText']) ;
		$featureVector=addToFeatureVector($tweet, $stopWords, $row['tweetSubject']) ;

		foreach ($featureVector as $word) {
			if (strlen($word)>0) {
				$sql="INSERT INTO tweetFeatureVector (tweetID, tweetWord) values (\"" .
				 $row['tweetID'] . "\",\"" . $word . "\")" ;
						$result2=$dbconnection->query($sql) ;

			}
		}
	}
    //close the crecordset
	$result->close();
	//Classify those tweets using tweetFeatureVector
	classifyTweets($dbconnection) ;
}
