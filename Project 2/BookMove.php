<?php
session_start();
$servername = "mysql01.arcs.njit.edu";
$username = "bef";
$password = "password";
$db = "bef";

$con = new mysqli($servername, $username, $password, $db);
if (mysqli_connect_error()){
	echo "Failed to connect to MySQL: " . mysqli_connect_error();
}



if (isset($_POST["identity"]) && isset($_POST["pwd"])){
	$repID = mysqli_real_escape_string($con, $_POST["identity"]);
	$pwd = mysqli_real_escape_string($con, $_POST["pwd"]);
	$login = mysqli_query($con, "SELECT RepID FROM movingRepresentatives WHERE RepID = '$repID' AND RepPassword = 	'$pwd'");

if (!$login) { echo "Login query failed: " . mysqli_error($con); }
	// Check if at least one match was found, if so save the repID, if not delete the entered ID
	if (mysqli_num_rows($login) > 0){
	$_SESSION["rep_id"] = $repID;
	} else {
	unset($_SESSION["rep_id"]);
	$invalid = true;
	}
}
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Book a Client's Moving Event</title>
<script src="MovingValidate.js"></script>
<style>
	body {
		background-image: url("images/movingTruck.jpeg");
		background-repeat: repeat;
		background-size: cover;
		background-attachment: fixed;
	}
	h1 {
		text-align: center;
		font-size: 24px;
		border: 5px solid black;
		padding: 20px;
		background-color: rgba(255, 255, 255, 0.8);
	}
	table {
		text-align: center;
		border: 5px solid black;
		padding: 20px;
		background-color: rgba(255, 255, 255, 0.8);
		font-size: 16px;
		margin: 15px auto;
	}
	th, td {
		border: 1px solid black;
		padding: 8px 10px;
		text-align: left;
	}
	.nav {
		text-align: center;
		border: 5px solid black;
		padding: 10px;
		background-color: rgba(255, 255, 255, 0.8);
		margin-bottom: 15px;
	}
	.nav a {
		margin: 0 15px;
		font-size: 18px;
		font-weight: bold;
		color: black;
	}
	.formBox {
		text-align: center;
		border: 5px solid black;
		padding: 20px;
		background-color: rgba(255, 255, 255, 0.8);
		font-size: 18px;
		width: 60%;
		margin: 15px auto;
	}
	.formBox input {
		font-size: 18px;
		padding: 5px;
		width: 80%;
	}
	button {
		font-size: 20px;
		padding: 6px 16px;
		background-color: lightblue;
	}
</style>
</head>
<body>
	<div class = "ClientAccount">
	<form id = "loginForm" method="post">
	<h1>Moving On Up</h1>
	
	<p>Moving Representative's First Name:</p>
 	<input type ="text" id = "fName" name = "firstName" placeholder = "Example: John">
	<label for = "fName">Required</label>

	<p>Moving Representative's Last Name:</p>
 	<input type ="text" id = "lName" name = "lastName" placeholder = "Example: Johnson">
	<label for = "lName">Required</label>

	<p>Moving Representative's Password:</p>

 	<input type ="password" id = "pwd" name = "pwd" placeholder = "Example: $m0vinG">
	<span class = "eye" id="eyeIcon" onclick="toggle()">👁</span>
	<label for = "pwd">Required</label>

	<p>Moving Representative's ID:</p>
 	<input type ="text" id = "identity" name = "identity" placeholder = "Example: 1020">
	<label for = "identity">Required</label>

	<p>Moving Representative's Phone Number:</p>
 	<input type ="text" id = "phoNum" name = "phoneNumber" placeholder = "Example: 555-555-5555 ext 555">
	<label for = "phoNum">Required</label>

	<p>Moving Representative's Email:</p>
 	<input type ="text" id = "Email" name = "Email" placeholder = "Example: john@MC.com">
	<label for="Email" id="emailReq" style="display:none;">Required</label>



	<button type="button" id = "rButton" name = "resetButton" onclick = "resetForm()">Reset</button>
	<button type="button" id = "sButton" name = "submitButton" onclick = "validate()">Submit</button>
	</form>
	</div>

</body>
</html>