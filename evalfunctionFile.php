<?php
//This file holds all functions that are called in the system

//function that is called when there is problem with an mysqli query
function errorFunction($dbconnection) {
	$text=$dbconnection->error ;
	$removeChars = array("[", "]","{","}", "~", "/", "\"","\\","'","(",")",) ;
	$text=str_replace($removeChars,"|",$text) ;
	echo "Error Msg" . $text ;
	echo "<script type='text/javascript'>alert('". $text . "');</script>";
}

//General message display
function msgHandling($errorMsg) {
	echo "<script type='text/javascript'>alert('". $errorMsg . "');</script>";
}

//This function is used by both trainClassifier and classifyTweets.
function preProcessTweet($tweet) {
    // Pre-process the tweets
    //Convert to lower case
    $tweet = strtolower($tweet) ;
    //Convert @username to AT_USER
	$tweet = preg_replace('/(^|\s)@([a-z0-9_]+)/i',' atuser',$tweet) ;

    /*Remove all punctuation is done in addToFeatureVector function
	/Remove URL is done in addToFeatureVector function */
	
	//Remove greater than 1 space
	$tweet = preg_replace('!\s+!', ' ', $tweet);
	return $tweet ;
}

//This function is used by trainClassifier and classifyTweets
function addStopWordsToArray($dbconnection) {
	//add stop words from the Stop Words table to the stopWords array - remove apostrophe from stop words
	$stopWords = array();
	$sql="select stopWord from stopWords" ;
	$result=$dbconnection->query($sql) ;

	if($result) {
		while ($row = $result->fetch_assoc()) {
			$stopWords[] = str_replace("'","",$row['stopWord']) ;
		}
	//Also add the AT_USER used in the replacement above and URL to stop words and RT for retweet
	$stopWords[]='atuser' ;
	$stopWords[]='url' ;
	$stopWords[]='rt' ;
	$result->close();
	return $stopWords ;
    }
}

//This function is used by trainClassifier and classifyTweets
function addToFeatureVector($tweet, $stopWords, $subject=null) {
	$featureVector = array();
	$words = explode(" ", $tweet);
	//array to hold punctuation to be deleted
//   $removeChars = array("[", "{", "(","~", "+", "=", "*") ;
   foreach ($words as $w) {
	    //do not add words that contain links
		if (preg_match("/http*/",$w)) {
			continue ;
		}
	    //remove non-alpha or numeric from the word
//		$w=str_replace($removeChars, "", $w);
		$w=preg_replace('/[^a-z0-9]+/i', '', $w);
		//remove repeating characters in word and replace with only 2 characters
		$w=preg_replace('/(.)\1{1,}/', '\1\1', $w);
		//remove the # from hashtag.  This is done in removing punctuation
//		$w= preg_replace('/#([^\s]*)/', '$1', $w) ;
		//if the word is the same as the subject of the training tweet then disregard that word.  Also used when adding word from tweets to be 	
		//analysed.  If a person has searched on a particular subject, then that subject is not added to the feature vector
		if($w==$subject) {
				continue;
		}
		//Do not add words that contain numbers
		if (preg_match('/[0-9]+/', $w)) {
			continue;
		}
		//Do not add words that are stop words
		if (in_array($w, $stopWords, true)) {
   			continue ;
		}
		
		//add the word to the feature vector
		$featureVector[]= $w;
	}
	return $featureVector ;
}

//Used by trainClassier.php.  This is where our classifier is created
//train the machine
function trainClassifier($dbconnection) {
	//once all the words are written to the feature vector, now add a word to featureListSentiment table for each group of the same sentiment and 
	//assign a probability to that word and sentiment using the naive bayes formula.  
	$sql="DELETE FROM featureListSentiment" ;
	$result=$dbconnection->query($sql) ;

	$sql="INSERT INTO featureListSentiment( tweetWord, tweetPolarityInt, tweetProbability ) 
	      SELECT fv.tweetWord, fv.tweetPolarity, (
          COUNT( fv.tweetWord ) / ( 
          SELECT COUNT( fv1.tweetWord ) 
          FROM featureVector fv1
          WHERE fv1.tweetWord = fv.tweetWord )
          ) AS probability
          FROM featureVector fv
          GROUP BY fv.tweetWord, fv.tweetPolarity" ;	
 	$result=$dbconnection->query($sql) ;
	 echo "Machine has been trained<br><br>" ;
}

//Used by the program classifyTweets.php.  Used to classify all tweets downloaded from twitter
function classifyTweets($dbconnection) {
	//update tweets by joining the tweet table to the result of the query below which finds the max of the sum of polarity assigned 
	//to each tweet.
	$sql="UPDATE tweets AS t1 INNER JOIN (
		  SELECT tweetID, tweetPolarityInt, MAX( sumProb ) 
		  FROM (
				SELECT tweetID, tweetPolarityInt, SUM( tweetProbability ) AS sumProb
				FROM tweetFeatureVector
				JOIN featureListSentiment ON tweetFeatureVector.tweetWord = featureListSentiment.tweetWord
				GROUP BY tweetID, tweetPolarityInt
				ORDER BY sumProb DESC
				)foo
				GROUP BY tweetID
				) AS nw ON nw.tweetID = t1.tweetID
		SET t1.tweetPolarity = nw.tweetPolarityInt" ;
		$result=$dbconnection->query($sql) ;
}

//Used when downloading tweets.  We need to send a parameter of $maxID which is the ID of the last tweet downloaded minus 1.
//The $maxID is a string -If we store it a number, we get problems with the length of the number.  We must
//therefore examine each element in the string in order to minus 1.
function minusOneFromMaxID($maxID) {
	//Tweet ID is stored a string.  We need to subtract 1 from tweet ID in order to get all tweets lower than the last tweet.  Because of the
	//length of the tweetID, this causes maths problems.  I used this string function to subtract 1.
	 for ($i=strlen($maxID)-1;$i>=0;$i--) {
		 $currentNum=substr($maxID,$i,1) ;
		 if($currentNum==0) {
	 		$maxID=substr_replace($maxID,9,$i,1);
	 	} else {
		 	$maxID=substr_replace($maxID,$currentNum-1,$i,1) ;
		 	break ;
	 	}
 	}
	return $maxID ;
}

//Called from showList.php
//function to select the primary field, the polarity field and the text field from the appropriate table.  All the correct values were
//stored to the variables at the beginning of the program based on the choice in the previous screen.
function selectFunction($primaryField,$polarityField,$textField,$tableName) {
	$sql="SELECT " . $primaryField . " AS ID, " . $polarityField . " AS field1," . $textField . " AS field2 FROM " . $tableName ;
	return $sql ; 
}
?>