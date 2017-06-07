<!DOCTYPE html>
<html>
	<head>
		<title>Sign-On Manager</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width">
		
		<link href="bootstrap.min.css" rel="stylesheet">
		<script src="jquery-2.1.4.min.js"></script>
		
		<script src="bootstrap.min.js"></script>
		<script src="typeahead.js"></script>
		
		<link href="newStyle.css" type=text/css rel=StyleSheet>
		
		<script type="text/javascript">
		// Send sign off time to database.
		function setendtime(timeEntryID, start){
			var endTime = new XMLHttpRequest();
			endTime.open("POST", "getdata.php", true);
			endTime.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			endTime.send('timeEntryID=' + timeEntryID + '&start=' + start);
			window.location.href = "signon.php";
			//window.location = window.location.pathname;
			return false;
		}
		
		function signin(){
			var username = document.getElementById("search_keyword_id").value
			if (username == ''){
				return;
			} 
			var newTime = new XMLHttpRequest(); 
			newTime.open("POST", "senddata.php", true);
			newTime.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			newTime.send('name=' + username);
			window.location.href = "signon.php";
			//window.location = window.location.pathname;
			//window.location = window.location.href.split("#")[0];
			return false;
		}
			
		function debugs(debug){
			var endTime = new XMLHttpRequest();
			endTime.open("POST", "senddata.php", true);
			endTime.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			endTime.send('timeEntryID=0&start=0&debug=1');
			//window.location = window.location.pathname;
			window.location.href = "signon.php";
			return false;
		}		
		</script>
		<script type="text/javascript">
		$(function(){
		$(".search_keyword").keyup(function() 
		{ 
			var search_keyword_value = $(this).val();
			var dataString = 'search_keyword='+ search_keyword_value;
			if(search_keyword_value!='')
			{
				$.ajax({
					type: "POST",
					url: "search.php",
					data: dataString,
					cache: false,
					success: function(html)
						{
							$("#result").html(html).show();
						}
				});
			}
			return false;    
		});
		 
		$("#result").on("click", null, function(e){
			var $clicked = $(e.target);
			var el = $clicked[0].tagName.toLowerCase();
			if (el == 'strong'){
				$clicked = $clicked.parent();
			}
			var el = $clicked[0].tagName.toLowerCase();
			if (el == 'span'){
				$clicked = $clicked.parent();
			}
			var $name = $clicked.find('fullname').html();
			var decoded = $("").html($name).text();
			$('#search_keyword_id').val(decoded);
		});
		 
		$(document).on("click", null, function(e) { 
			var $clicked = $(e.target);
			if (! $clicked.hasClass("search_keyword")){
				$("#result").fadeOut(); 
			}
		});
		 
		$('#search_keyword_id').click(function(){
			$("#result").fadeIn();
		});
		});
		</script>
    </head>
    <body>		
		<div class = "contentDiv">
			<ul>
				<li>
					<ul id="topRow">
						<li>Name</li>
						<li>In</li>
						<li>Out</li>
						<li>Date</li>
					</ul>
				</li>
				
	<?php
	include("database.php");
	// GMT/UTC mod doesn't seem to work
	date_default_timezone_set('Australia/Adelaide');
	
	// We only want to see today's sign-ons.
	// Time offset hard coded for Adelaide.
	$today = time() + 34200;
	$humanDate = new DateTime("@$today");
	$convertToDayMonth = $humanDate->format('Y-m-d');
	//echo ($convertToDayMonth);
	$epochToday = strtotime($convertToDayMonth);
	
	
	// Hard coded test case
	$customerID = 2;
	$projectID = 2;
	$activityID = 2;
	
	//$dp_today = date("d/m/Y", time());
	
	$query = "SELECT timeEntryID, userID, start, end "
			. "FROM kimai_timesheet "
			. "WHERE activityID = '" . $activityID . "' "
			. "AND projectID = '" . $projectID . "' "
			. "AND start >= '" . $epochToday . "';";
	//echo ($query);
	$exe = $conn->query($query);
	if ($exe->num_rows > 0){
		while ($row = $exe->fetch_assoc()){
			$nameQuery = "SELECT name FROM kimai_users WHERE userID = '" . $row["userID"] . "';";
			$name = $conn->query($nameQuery);
			$nameResult = $name->fetch_assoc();
			// Time offset hard coded for Adelaide.
			$epochStart = $row["start"] + 34200;
			$epochEnd = $row["end"] + 34200;
			$start = new DateTime("@$epochStart");
			$stop = new DateTime("@$epochEnd");
			
			echo ("<li><ul><li>" . $nameResult["name"] . "</li>"
			. "<li>" . $start->format('H:i') . "</li>");
			
			if ($row["end"] > 0){
				echo ("<li>" . $stop->format('H:i') . "</li>");
			} else {
				echo ("<li><form><input name=\"" . $row["timeEntryID"] . "\" type=submit class=\"button\" value=\"Sign Out\" style=\"width:100%\" onclick=\"setendtime(" . $row["timeEntryID"] . ", " . $row["start"] . ")\"></form></li>");
			}
			echo ("<li>" . $start->format('d-m') . "</li></ul></li>");
		}
	}
	?>
				
			<div>
				<a name="signin" id="button2" style="width:100%" href="#popup1"><p>Sign In</p></a>
			</div>					
			
			<!--<form><input name="debug" type=submit value="Debug" id="button1" style="width:100%" onclick="debugs(1)"></form></td> -->
			
			<div id="popup1" class="overlay">
				<div class="popup">
					<h2>Please sign in</h2>
					<a class="close" href="#">&times;</a>
					<div class="content">
						<form>							
							<input type="text" class="search_keyword" id="search_keyword_id" placeholder="Enter your Full Name" />
							<input type=submit value ="Sign In" id="button1" onclick="signin()">
							<div id="result"></div>
						</form>
					</div>
				</div>		
			</div>
	</body>
</html>
		
			
					