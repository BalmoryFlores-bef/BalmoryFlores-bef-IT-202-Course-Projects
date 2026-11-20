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

$script = "";

$script = "";

if (isset($_POST["identity"]) && isset($_POST["pwd"])){
	$repID = mysqli_real_escape_string($con, $_POST["identity"]);
	$pwd = mysqli_real_escape_string($con, $_POST["pwd"]);
	$login = mysqli_query($con, "SELECT RepID FROM movingRepresentatives WHERE RepID = '$repID' AND RepPassword = '$pwd'");

	// Check if at least one match was found, if so save the repID, if not delete the entered ID
	if ($login && mysqli_num_rows($login) > 0){
		$_SESSION["rep_id"] = $repID;
	} else {
		unset($_SESSION["rep_id"]);
	}
}

if (isset($_SESSION["rep_id"])){
	// The form was submitted: does this client have an account?
	if (isset($_POST["verify"])){
		$clientID = mysqli_real_escape_string($con, $_POST["clientID"]);
		$firstName = mysqli_real_escape_string($con, $_POST["firstName"]);
		$lastName = mysqli_real_escape_string($con, $_POST["lastName"]);
		$info = mysqli_query($con, "SELECT ClientID FROM Client WHERE ClientID = '$clientID' AND ClientFirstName = '$firstName' AND ClientLastName = '$lastName'");

		if ($info && mysqli_num_rows($info) > 0){
			// Client exists: no alert, simply continue to booking the moving event
			$_SESSION["verified_client"] = (int)$clientID;
			header("Location: BookMove.php");
			exit();
		} else {
			// Client does not exist: ONE alert. OK = re-enter the data, Cancel = create an account
			$message = "The client cannot be found. Press OK to re-enter data or Cancel to be redirected to create an account for a new client.";
			$script .= "if (confirm(" . json_encode($message) . ")) { window.location.href = 'VerifyClient.php'; } else { window.location.href = 'CreateClient.php?from=book'; }";
		}
	}
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>Verify Client</title>
	<link rel="stylesheet" href="P1style.css">
	<style>
	body {
		background-image: url("images/movingTruck.jpeg");
		background-repeat: repeat;
		background-size: cover;
	
	.clientTable {
		text-align: center;
		border: 5px solid black;
		padding: 20px;
		background-color: rgba(255, 255, 255, 0.8);
		font-size: 32px;
	}
	</style>
</head>

<body>

	<div class="clientTable">
	<form method="post" action="VerifyClient.php" onsubmit="return validateVerify();">
	<h1>Moving On Up: Verify Client</h1>

	<p>Client's First Name:</p>
	<input type="text" id="fName" name="firstName" placeholder="Example: James">
	<label for="fName">Required</label>

	<p>Client's Last Name:</p>
	<input type="text" id="lName" name="lastName" placeholder="Example: Jameson">
	<label for="lName">Required</label>

	<p>Client's ID:</p>
	<input type="text" id="identity" name="clientID" placeholder="Example: 5001">
	<label for="identity">Required</label>

	<br><br>
	<button type="submit" id="sButton" name="verify" value="1">Submit</button>
	</form>
	</div>

	<script><?php echo $script; ?></script>
</body>
</html>
