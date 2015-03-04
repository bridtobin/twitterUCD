<html>
<!--Most of this code was taken from Google Charts except the php code.  I have also added the functions selectHandler() and showBreakdown() to allow users to click on the chart to view the tweets related to that particular slice of the chart-->
<?php
	//Count the number of tweets for each sentiment in the "tweets" table
	$sql = "SELECT count(tweetID) as sumTotal, if(isnull(tweetPolarity),5,tweetPolarity) as tweetPolarity from tweets group by tweetPolarity" ;
	$result=$dbconnection->query($sql) ;
	$neg=0;
	$pos=0;
	$neu=0;
	$notAnalysed=0;
	$x= $_POST['searchPhrase'] ;
	if($result) {
		while ($row = $result->fetch_assoc()) {
			switch ($row['tweetPolarity']) {
    			case 0 :
					$neg= $row['sumTotal'] ;
        			break;
    			case 2 :
					$neu=$row['sumTotal'] ;
        			break;
    			case 4 :
					$pos=$row['sumTotal'] ;
        			break;
				case 5 :
					$notAnalysed=$row['sumTotal'] ;
					break ;
			}
		}
		// add the $notAnalysed to the neutral tweets
		$neu=$neu+$notAnalysed;
//		$totalTweets=$neg + $neu + $pos + $notAnalysed;
		$totalTweets=$neg + $neu + $pos;

	}
	?>
	
   <head>
    <!--Load the AJAX API-->
    <script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script type="text/javascript">
      // Load the Visualization API and the piechart package.
    google.load('visualization', '1.0', {'packages':['corechart']});
      
      // Set a callback to run when the Google Visualization API is loaded.
      google.setOnLoadCallback(drawChart);

      // Callback that creates and populates a data table,
      // instantiates the pie chart, passes in the data and
      // draws it.
      
	  function drawChart() {
	      // Create the data table.
     	var pos = <?php echo $pos ; ?> ;
    	var neg = <?php echo $neg ; ?> ;
    	var neu = <?php echo $neu ; ?> ;
   		//var notAnalysed = <?php echo $notAnalysed ; ?> ;
		var totalTweets  = <?php echo $totalTweets ; ?> ;
		var x = <?php echo "\"" . $_POST['searchPhrase'] . "\"" ; ?> ;
 		eval(pos) ;
		eval(neg) ;
		eval(neu) ;
		//eval(notAnalysed) ;
		eval(totalTweets);
	    var data = new google.visualization.DataTable();
	    data.addColumn('string', 'Sentiment');
        data.addColumn('number', 'Percentage of Tweets');
        data.addRows([
          ['Neutral', neu],
          ['Positive', pos],
          ['Negative', neg]
     //     ['Not Analysed', notAnalysed]

        ]);


        // Set chart options
       var options = {"title": totalTweets + " tweets analysed for  \" " + x + "\"\n\n Double click slice to view related tweets",
                       "width":600,
                       "height":270};

  
	// this function allows the user to double click on a slice of the pie chart and view the related tweets. It does this by calling
	// the program displaySliceTweets.php and placing the result in the DIV called "mydiv".
	function selectHandler() {
  		  var changediv = document.getElementById("tweetbreakdown");
		  if(changediv) {
		  	changediv.innerHTML = " ";
		  }
          var selectedItem = chart.getSelection()[0];
          if (selectedItem) {
            var polarityChoice = data.getValue(selectedItem.row, 0);
			  if (window.XMLHttpRequest) {
    			// code for IE7+, Firefox, Chrome, Opera, Safari
    			xmlhttp=new XMLHttpRequest();
  			  } else { // code for IE6, IE5
    			 xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  			  }
	  		  xmlhttp.onreadystatechange=function() {
   			  	if (xmlhttp.readyState==4 && xmlhttp.status==200) {
      					document.getElementById("mydiv").innerHTML=xmlhttp.responseText;
    		  	}
  			  }  
			  xmlhttp.open("GET","displaySliceTweets.php?q="+polarityChoice,true);
              xmlhttp.send();
		  }
		}	
       var chart = new google.visualization.PieChart(document.getElementById('chart_div'));
		google.visualization.events.addListener(chart, 'select', selectHandler); 
        chart.draw(data, options);
	}
	
	//this function allows the user to double click on a tweet and see how that sentiment was calculated.  Information from the 
	//tweetFeatureVector table is used.  The program listBreakdown.php is called and the result is placed in the DIV called
	//"tweetbreakdown"
    function showBreakdown(tweetID)
	{
	//you can get the value from arguments itself
	    if (window.XMLHttpRequest) {
    			// code for IE7+, Firefox, Chrome, Opera, Safari
    		xmlhttp=new XMLHttpRequest();
  	    } else { // code for IE6, IE5
    	    xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  		}
  		xmlhttp.onreadystatechange=function() {
   			if (xmlhttp.readyState==4 && xmlhttp.status==200) {
      			document.getElementById("tweetbreakdown").innerHTML=xmlhttp.responseText;
    		}
  		 }  
  		 xmlhttp.open("GET","listBreakdown.php?q="+tweetID,true);
         xmlhttp.send();
	}
  
    </script>
  </head>
  
  <body>
    <!--Div that will hold the pie chart-->
	<div id="chart_div"></div>
	<div class="wrapResult">
    <div id="mydiv"></div>
	<div id="tweetbreakdown"></div>
    </div>

  </body>
	<?php
    mysqli_close($dbconnection) ;
    ?>
</html>
