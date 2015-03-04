<?php
//This program handles the call to sentiment140.  We finish building the string that we started in addTweetsToTable.php.  We then send this string
//($data_string) as a curl request to sentiment 140.
$data_string= $data_string . "]}"; 
$ch = curl_init() ;
curl_setopt($ch, CURLOPT_URL, "http://www.sentiment140.com/api/bulkClassifyJson?appid=briddelap@gmail.com/");
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                       
curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);                                                                    
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                        
curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                            
    'Content-Type: application/json',                                                                                  
    'Content-Length: ' . strlen($data_string))                                                                         
);                                                                                                                     
//send tweets to Sentiment140 using curl and store result in variable $result
$result = curl_exec($ch) . "";
echo "<br>" ;
//write results to array - Had to use utf8-encode as getting spurious results from result.
$json=json_decode(utf8_encode($result)) ;
$count=0 ;
//loop through each tweet and build an insert statement
foreach ($json->data as $polItems) {
  if ($count>0) {
		$sql=$sql."," ;
   }
   else {
  		$sql="INSERT INTO tweets (tweetID, tweetText, createdAt, tweetPolarity) values " ;
	}
    $sql=$sql . "(\"" . $polItems->id . "\",\"" . $polItems->text .  "\",\"" . $polItems->date  . "\"," .  $polItems->polarity . ")"  ;
	$count++ ;
}
//submit the insert statement to MySQL
$result=$dbconnection->query($sql) ;
			if (!$result) {
				$text=$dbconnection->error ;
				$removeChars = array("[", "]","{","}", "~", "/", "\"","\\","'","(",")",) ;
				$text=str_replace($removeChars,"|",$text) ;
				echo $text ;

			}
?>
