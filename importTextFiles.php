<!--Temporary php program used to import data text files-->
<?php
ini_set('max_execution_time', 0); 
// Use server url, user name, user password and database name
$dbconnection = new mysqli("server","user", "password", "database name");
	if(!$dbconnection) {
		die('Connection failed: ' . $dbconnection->error());
	}

/*$sql = "CREATE TABLE featureVector (" .
		 "tweetID VARCHAR(40) ," .
		 "tweetWord VARCHAR(50)," .
		 "tweetPolarity VARCHAR(20), " .
		 "INDEX (tweetWord))" ;
//$result = $dbconnection->query($sql); 
$result=$_SESSION['dbconnection']->query($sql) ;
*/

/*$sql = "CREATE TABLE tweetFeatureVector (" .
		 "tweetID VARCHAR(40) ," .
		 "tweetWord VARCHAR(50)," .
		 "INDEX (tweetWord))" ;
$result = $dbconnection->query($sql); */


//Import corpusTraing data
/*$sql = "CREATE TABLE corpusTraining (" .
		 "tweetID VARCHAR(40) ," .
		 "tweetText VARCHAR(140) ," .
		 "tweetSubject VARCHAR(60) , " .
		 "tweetPolarityInt TINYINT  , " .
		 "PRIMARY KEY(tweetID) )" ;
    $result = $dbconnection->query($sql);

*/	

/*$sql = "CREATE TABLE tempCorpus (" .
		 "tweetID VARCHAR(40) ," .
		 "tweetText VARCHAR(140) ," .
		 "tweetPolarityInt TINYINT  , " .
		 "PRIMARY KEY(tweetID) )" ;
    $result = $dbconnection->query($sql);
	$removeChars = array("[", "{", "~", "/", "<", ">") ;
	if (($handle = fopen("full_training_dataset.csv", "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
			$text=str_replace($removeChars,"",$data[1]);

		switch ($data[0]) {
			case "positive" :
				$polarity=4 ;
				break;
			case "negative" :
				$polarity=0;
				break;
			case "neutral" :
				$polarity=2;
				break;
		}
	
		$sql="INSERT INTO tempCorpus (tweetID, tweetText, tweetPolarityInt ) values (\"" .
		//replace double quotes with single quotes - causes formatting problems.
				
		uniqid() . "\",\"" . str_replace("\"","'",$text) . "\"," . $polarity .")" ;
//		 echo $sql . "<br>" ;
       $result = $dbconnection->query($sql); 
	   		if (!$result) {
				$error=$dbconnection->error ;
				echo $error . " " . $sql . "<br>" ;
			}
    }
    fclose($handle); 
} */

/*
if (($handle = fopen("trainingCorpus.csv", "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
		$sql="INSERT INTO corpusTraining (tweetID, tweetText, tweetSubject, tweetPolarityInt ) values (\"" .
		//replace double quotes with single quotes - causes formatting problems.
		$data[0] . "\",\"" . str_replace("\"","'",$data[1]) . "\",\"" . str_replace("\"","'",$data[2]) . "\"," . $data[3] .")" ;
		 echo $sql . "<br>" ;
       $result = $dbconnection->query($sql); 
    }
    fclose($handle); 
} 
//	$dbconnection->close();
*/
//import stopwords
/*$sql = "CREATE TABLE stopWords (" .
		 "stopWord VARCHAR(20),
		 PRIMARY KEY (stopWord))" ;
    $result = $dbconnection->query($sql);
*/
if (($handle = fopen("newStopWords.txt", "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
		$sql="INSERT INTO stopWords (stopWord) values (\"" .
		 $data[0] . "\")" ;
		 echo $sql . "<br>" ;
       $result = $dbconnection->query($sql); 
    }
    fclose($handle); 
} 
/*
$sql = "CREATE TABLE featureListSentiment (" .
		 "tweetWord VARCHAR(50) ," .
		 "tweetPolarityInt TINYINT  , " .
 		 "tweetProbability FLOAT)  " ;
    $result = $dbconnection->query($sql);
	
?>

/*	
	//Create lexicon table and get positive and negative words in there
$sql = "CREATE TABLE lexicon (" .
		 "lexiconWord VARCHAR(50) ," .
		 "polarityInt TINYINT  , " .
 		 "primary key (lexiconWord) ) " ;
		 echo $sql ;
$result = $dbconnection->query($sql);
*/
	
//open a text file with positive words and insert into lexicon table	
/*$handle = fopen("newPositiveWords.txt", "r");
if ($handle) {
    while (($line = fgets($handle)) !== false) {
       $sql="INSERT INTO lexicon (lexiconWord,polarityInt) values (\"" .
		trim(strtolower($line)) . "\", 4)" ;
		$result = $dbconnection->query($sql);
    }
	fclose($handle);
} else {
    echo "Error opening file" ;
} 
*/
/*
$handle = fopen("negativeWords.txt", "r");
if ($handle) {
    while (($line = fgets($handle)) !== false) {
        $sql="INSERT INTO lexicon (lexiconWord,polarityInt) values (\"" .
		trim(strtolower($line)) . "\", 0)" ;
		$result = $dbconnection->query($sql);
    }
	fclose($handle);
} else {
    echo "Error opening file" ;
}*/