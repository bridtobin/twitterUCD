<?php
/*Program to control the adding, edit and removing of records from corpusTraining, lexicon and stop words*/
session_start();
$dbconnection = new mysqli("csserver.ucd.ie","bdelap", "uryafql1", "bdelap");
if(!$dbconnection) {
	die('Connection failed: ' . $dbconnection->error());
}
include_once "functionFile.php" ;

?>
<!DOCTYPE html>
<html>
<head>
	<link href="twittercss.css" rel="stylesheet" type="text/css"/>
	<title>Train Twitter Machine</title>
	<!--Had to use the following character set to display certain characters -->
	<meta http-equiv="Content-Type" content="text/html; charset=windows-1252" />
	<meta name="description" content="Twitter Sentiment Analysis">

<script>
//function to confirm that user wishes to delete
function ConfirmDelete()
    {
     var ID = document.getElementById('selectID');
	 if (ID.selectedIndex==-1) {
	 	confirm("You must select an item before it can be deleted!");
		return false;
	 }
	 else {
      	var x = confirm("Are you sure you want to delete " + ID.value + "?");
      	if (x)
        	  return true;
      	else
        	return false;
	 }
}
//Function to prompt user if they press edit but have not selected an item on the list
function validateEdit() 
    {
     var ID = document.getElementById('selectID');
	 if (ID.selectedIndex==-1) {
	 	confirm("You must select an item before it can be edited!");
		return false;
	 }
	 else {
    	  return true;
	 }
}

//function to ensure that all required fields are filled before adding or editing.
function ValidateAddSave(whichTable)
	{
		if(whichTable=="stopWords") {
			x=document.getElementById('tableText').value ;
			if (x == null || x == "") {
				confirm("You must enter a word before saving") ;
				return false;
			}
		}
		if(whichTable=="lexicon" || whichTable=="corpusTraining") {
			if(whichTable=="corpusTraining") {
				var msgWord = "Tweet" ;
			} else {
				var msgWord = "Word" ;
			}
			var x=document.getElementById('tableText').value ;
			var y=document.getElementById('polarity').selectedIndex ;
			if (y==-1 || x==null || x=="") {
				confirm("You must enter a " + msgWord + " and Polarity" ) ;
				return false;
			}
		}
	}
//function to display an error message.
function errorHandling(errorMsg)
	{
		alert(errorMsg)  ;
	}
</script> 
</head>
<body>
<div id="wrapper">
<div id="header">
<?php
//Because I have one <select> element handling all 3 tables that have different field names, I firstly store the names of the fields for
//each table in variables.
	switch ($_POST['whichTable']) {
		case "corpusTraining" :
			$primaryField="tweetID";
			$polarityField="tweetPolarityInt" ;
			$textField="tweetText";
			$listHeading="Tweet" ;
			$tableHeading="Training Corpus";
  			break;
		case "lexicon" :
			$primaryField="lexiconWord" ;
			$polarityField="polarityInt" ;
			$textField="lexiconWord" ;
			$listHeading="Word" ;
			$tableHeading="Dictionary" ;

        	break;
    	case "stopWords":
			$primaryField="stopWord";
			$polarityField="null";
			$textField="stopWord";
			$listHeading="stop Word" ;
			$tableHeading="Stop Word Table";

        	break;
	} //switch
echo "<div class=\"pagename\">" .  $tableHeading . "</div>" ;
?>
</div>
<div id="listDiv">
</form>
    <form method="GET" action="trainFrontScreen.php" >
    <input type="submit" class="submitSmallButton" value="< Back">
</form>
<?php
//If a button has been pressed
if($_SERVER['REQUEST_METHOD']=='POST') {
	//If delete button pressed
	if (!empty($_POST['removeButton'])) {
		if(isset($_POST['selectID'])) { // This if will only be used if javascript is disabled
			$sql="DELETE FROM " . $_POST['whichTable'] . " WHERE " . $primaryField . "=\""
			. ($_POST['selectID']) . "\"" ;
			$result2=$dbconnection->query($sql) ;
		} else { // This else will only be used if javascript is disabled
			echo "You must select an item before you can delete it." ;
		}
	}

	
	//If save button pressed
	if(!empty($_POST['saveButton'])) {
		//replace double quotes with single quotes
		$_POST['tableText']=str_replace("\"","'",$_POST['tableText']);
		//Save can be pressed for either add or edit.  Different calls for each.
		if(($_POST['addOrEdit'])=="add") {
 			$sql="INSERT INTO " . $_POST['whichTable'] . " SET " . $primaryField . " = \"" ; 
			if($_POST['whichTable']=="corpusTraining") {
				$sql=$sql . uniqid()  . "\", " . $textField . "= \"" . ($_POST['tableText']) . "\"";
			} else {
				$sql=$sql . preg_replace('/[^a-z0-9]+/i', '', ($_POST['tableText'])) . "\"" ;
			//	$sql=$sql . ($_POST['tableText']) . "\"" ;
			}
			if($polarityField<>"null") {
				$sql=$sql . ", " . $polarityField . " = " . ($_POST['polarity']) ;
			}
		} else {
			if($_POST['whichTable']=="corpusTraining") {
				$sql="UPDATE " . $_POST['whichTable'] . " SET " . $textField . " = \"" . ($_POST['tableText']) . "\"" ;
			} else {
				$sql="UPDATE " . $_POST['whichTable'] . " SET " . $textField . " = \"" . 
				preg_replace('/[^a-z0-9]+/i', '', ($_POST['tableText'])) . "\"" ;
			}
			if($polarityField<>"null") {
				$sql=$sql . ", " . $polarityField . " = " .  ($_POST['polarity']) ;
			}
			$sql=$sql . " WHERE " . $primaryField . " = \"" . ($_POST['selectID']) . "\"" ;
		}
		$result3=$dbconnection->query($sql) ;
		if (!$result3) {
				$text=$dbconnection->error ;
				$removeChars = array("[", "]","{","}", "~", "/", "\"","\\","'","(",")",) ;
				$text=str_replace($removeChars,"|",$text) ;
			    echo "<script type='text/javascript'>alert('". $text . "');</script>";
		} // if(!$result3)
	} //if(!empty($_POST['saveButton']))
} //if($_SERVER['REQUEST_METHOD']=='POST')



//Initial form that is shown listing the data in <select> element
	$sql=selectFunction($primaryField,$polarityField,$textField,$_POST['whichTable']) . " ORDER BY " . $textField ;
	$result=$dbconnection->query($sql) ;
	?>
	<form method="post" name="form2" class="formInLine">
    <input type="hidden" name="whichTable" value="<?= $_POST['whichTable'] ?>" />

    <select name="selectID" id="selectID" size=15 id="listTable">	
        
     <?php
      while ($row = $result->fetch_assoc()) {
		  if(isset($_POST['selectID'])) {
			  if(($_POST['selectID'])==$row['ID']) {
		  	     echo "<option value=\"" . $row['ID'] . "\" selected=\"selected\">" ;
			  } else {
		     	echo "<option value=\"" . $row['ID'] . "\">" ;
			  }
		  } else	{
		  	     echo "<option value=\"" . $row['ID'] . "\">" ;
		  }
//			echo $$field1 . "&nbsp;&nbsp;&nbsp;" ;
			//echo $row['tweetPolarityInt'] . "&nbsp;&nbsp;&nbsp;";
		 echo $row['field1'] .  "&nbsp;&nbsp;&nbsp;" ;
		 echo $row['field2']  ;
		 echo "</option>" ;
	 }	
	 ?>
     </select>
     <br><br>
     <input type='submit' class='submitSmallButton' name='addButton' value='Add' />
     <input type='submit' class='submitSmallButton' id="editButton" name='editButton' value='Edit' Onclick="return validateEdit();"/>
     <input type="submit" class='submitSmallButton' id="removeButton" name="removeButton" value="Remove" Onclick="return ConfirmDelete();"  />
 
     <?php
	 // If the Add button or edit button has been pressed, include this code in the form.  This is where we add a tweet
	 if (!empty($_POST['addButton']) or !empty($_POST['editButton']) ) {
			$tableText = "<input type=\"text\" name=\"tableText\"  id=\"tableText\" >" ; 
			if(!empty($_POST['addButton'])) {
				echo "<br><br>" ;
				echo "<h4>Add " . $listHeading . " to " . $tableHeading . "</h4>" ;
				echo $tableText ;
				//echo $listHeading .  "&nbsp;&nbsp;" . $tableText ;
       			echo "<input type=\"hidden\" name=\"addOrEdit\" value=\"add\" />" ;
				if($polarityField<>"null") {
 					$polarity = "<br>Polarity &nbsp;  <select name='polarity' id='polarity' size=2  >" .
				    "<option value=0>0 - Negative</option>" .
				    "<option value=2>2 - Neutral</option>" .
				    "<option value=4>4 - Positive</option>" .
				    "</select>" ;
					echo $polarity;
				} 
			} else {
				if (isset($_POST['selectID'])) { // This if will only be used if javascript is disabled 
					?>
				    <input type="hidden" name="selectID" value="<?= $_POST['selectID'] ?>" />
	    	        <input type="hidden" name="addOrEdit" value="edit" />
					<?php
					$sql=selectFunction($primaryField,$polarityField,$textField,$_POST['whichTable']) . " WHERE " . $primaryField .
			    	"= \"" .($_POST['selectID']) . "\"" ;
					$result3=$dbconnection->query($sql) ;
	 				if (!$result3) {
						$text=$dbconnection->error ;
						$removeChars = array("[", "]","{","}", "~", "/", "\"","\\","'","(",")",) ;
						$text=str_replace($removeChars,"|",$text) ;
				    	echo "<script type='text/javascript'>alert('". $text . "');</script>";
					} else {
		         		while ($row = $result3->fetch_assoc()) {
							echo "<br><br>" ;
				    		echo "<h4>Edit " . $listHeading . " in " . $tableHeading . "</h4>" ;
							$tableText = "<input type=\"text\" name=\"tableText\"  id=\"tableText\" value=\"" . $row['field2'] . "\"" . "/>" ;
				    		echo $tableText;
							if($polarityField<>"null") {
								echo "<div class=\"styledSelect\">" ;
								$polarity = "<br>Polarity:  <select name='polarity' id='polarity' size=2  >" .
			    				"<option value=0 ". ($row['field1']==0?"selected='selected'":"") . ">0 - Negative</option>" .
			    				"<option value=2 ". ($row['field1']==2?"selected='selected'":"") . ">2 - Neutral</option>" .
			    				"<option value=4 ". ($row['field1']==4?"selected='selected'":"") . ">4 - Positive</option>" .
			    				"</select>" ;
								echo $polarity ;
								//echo "</div>" ;
							} // if ($polarityField<>"null")
				 		} // while ($row=$result3->fetch_assoc())
					} // if(!result3)
				} else { // if(isset($_POST['selectID'] - // This else will only be used if javascript is disabled
					echo "You must select an item before it can be edited" ;
				}
			}  // if(!empty($_POST['addButton']))

			if(!empty($_POST['addButton']) or (!empty($_POST['editButton']) and isset($_POST['selectID']))) {
    			echo "<input type=\"submit\" class=\"submitSmallButton\" name=\"saveButton\" value=\"Save\" onClick=\"return ValidateAddSave('" .
				 ($_POST['whichTable']) . "');\"/>" ;
				echo "<input type='submit' class='submitSmallButton' name='cancelButton'  value='Cancel'/>" ;
			}
			
		} // if(!empty($_POST['addButton']) or !empty($_POST['editButton']) )
	   ?>
 
        </div>
        </div>
 </body>
<?php
 ?>
</html>