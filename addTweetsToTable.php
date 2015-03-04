<?php
// This program deletes the existing tweets from the tweets SQL table.  Tweets are downloaded from twitter.  These tweets are then  parsed and either inserted in to the tweets table (if we are using our own classifier) or used to build a string to send to Sentiment 140 (if we choose to use the Sentiment 140 classifier) 
// twitter and inserts them in to the tweets table.  It also builds up a string that will be sent 
	include_once "functionFile.php" ;
	//delete existing tweets from tweets table
	$sql= "TRUNCATE TABLE tweets" ;
	$result=$dbconnection->query($sql) ;
	require_once('twitteroauth.php');
	$twitteruser = ""; //type your twitter user name here or ideally get from secure table
	$consumerkey = "";  //type your consumer key here or ideally get from secure table
	$consumersecret = "" ; // type your consumer secret here or ideally get from secure table
	$accesstoken = ""; // type your access token here or ideally get from secure table
	$accesstokensecret = ""; //type your accesstoken secret here or ideally get from secure table
	$connection = new TwitterOAuth($consumerkey, $consumersecret, $accesstoken, $accesstokensecret); 
	$countFromTwitter=0;
	$finish=0;
	$maxID=0;
	$_POST['searchPhrase'] = str_replace("\"", "'", $_POST['searchPhrase']) ;

	//Create a string to send tweets to Sentiment140 in json format
	$data_string = "{\"data\": [" ; // Sentiment 140 string
	// Keep looping until the required number of tweets is received or there are no more tweets available from twitter.
	while ($finish==0) {
	//get Tweets from twitter - As twitter only give a maximum of 100 per page, we have to do many calls.  We do this by storing the
	//id of the last tweet and subsequently calling only the tweets with a higher ID than the last.
		if ($maxID==0) {
			$tweets= $connection->get("https://api.twitter.com/1.1/search/tweets.json?q=" . 
			urlencode($_POST['searchPhrase']) 
			."&lang=en&count=200&include_rts=1"); 
		} else {
			$maxID=minusOneFromMaxID($maxID);
		  	$tweets= $connection->get("https://api.twitter.com/1.1/search/tweets.json?q=" . urlencode($_POST['searchPhrase']) 
			."&lang=en&count=200&include_rts=1&max_id=".$maxID); 
		} // if($maxID==0_
//		print_r($tweets) ;
		if (array_key_exists("errors",$tweets)) {
		  echo "The following error msg was returned from Twitter " . $tweets->errors[0]->message ;
    	}
		if (array_key_exists("statuses",$tweets)) {
	  		if (!array_key_exists("0",$tweets->statuses)) {
				if($maxID==0) {
					echo "No records found" ;
				}
				break;
			}
   		}
		
		$tweets=$tweets->statuses ;
		//Loop through tweets.  Remove characters that will cause problems. 
		$count=0;
		//clean up the data - remove curly brackets and square brackets
   		$removeChars = array("[", "{", "~", "/", "<", ">") ;
		//replace double quotes with single quotes - causes formatting problems.
   		$removeInvertedCommas = array('"') ;
  		foreach ($tweets as $items) {
			$countFromTwitter++ ;
			if ($countFromTwitter>$_POST['noOfTweets']) {
				$finish=1 ;
				break ;
			}
   			//get the date from the createdAt field
			$creationDate=$items->created_at ;
			//Remove all the items stored in array $removeChars
			$newText=str_replace($removeChars, "", $items->text);
			//Replace double quotes with single quotes
   			$newText=str_replace($removeInvertedCommas,"'",$newText) ;
   			$count++ ;
			$maxID=$items->id_str ;
			if ($_POST['typeAnalysis']=="sentiment140") {
			//Sentiment140 - Build the string that is sent to Sentiment 140
				if (($countFromTwitter)>1) {
   					$data_string=$data_string . "," ;
   				} 
 				$data_string=$data_string . "{\"text\":\"" . $newText . "\"" . ",\"id\":\"" . $items->id_str . "\",\"date\":\"" 
				. $items->created_at . "\"}" ;
   			} else {
				// If using our own classifier insert the tweets into an SQL table.
				/*if (($count)>1) {
					$sql=$sql."," ;
   				} else {  		
					$sql="INSERT INTO tweets (tweetSubject, tweetID, tweetText, createdAt) values " ;
				}
				$sql=$sql . "(\"" . $_POST['searchPhrase'] . "\",\""  . $items->id_str . "\",\"" . $newText .  "\",\"" 
				. $items->created_at . "\")"  ; */
				$sql="INSERT INTO tweets (tweetSubject, tweetID, tweetText, createdAt) values (\"" . $_POST['searchPhrase'] . "\",\"" ;
				$sql=$sql . $items->id_str . "\",\""  .
				$newText . "\",\"" .
				$items->created_at . "\")" ;
				$result=$dbconnection->query($sql) ;
			} //if ($_POST['typeAnalysis']=="sentiment140")
	
			if($count==0) {
				$finish=1 ;
			} // if($count==0)
		} // for each
	} // while ($finish==0)
?>
</body>
</html>
