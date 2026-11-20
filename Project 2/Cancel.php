<?php
session_start();
$servername = "mysql01.arcs.njit.edu";
$username = "bef";
$password = "_{jCu*=h0>Zikyi{;Q++";
$db = "bef";

$con = new mysqli($servername, $username, $password, $db);
if (mysqli_connect_error()){
	echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

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


if (isset($_POST["cancel"])){
		$repID = isset($_SESSION["rep_id"]) ? (int)$_SESSION["rep_id"] : 0;
		$clientText = trim($_POST["clientID"]);

		if ($clientText == "" || !is_numeric($clientText)){
			$script .= "alert('Client ID is required and must be a number.');";
		} else {
			$clientID = (int)$clientText;

			// The client must have a booking in the database
			$info = mysqli_query($con, "SELECT ClientID FROM ClientMovingInfo WHERE ClientID = $clientID");
			$booked = $info && mysqli_num_rows($info) > 0;

			if ($booked){
				$movingID = isset($_SESSION["moving_id_" . $clientID]) ? $_SESSION["moving_id_" . $clientID] : "N/A";

				// Moving event found: ONE confirmation. OK = cancelled, Cancel = nothing happens
				$question = "Are you sure you want to cancel Moving ID " . $movingID . " for Client ID " . $clientID . "?\n\nIf Special Instructions were given for this moving event, they will also be deleted.\n\nClick OK to cancel the moving event, or Cancel to keep it.";
				$done = "The moving event has been cancelled.\n\nMoving Representative ID: " . $repID . "\nClient ID: " . $clientID . "\nMoving ID: " . $movingID;
				$script .= "if (confirm(" . json_encode($question) . ")) { alert(" . json_encode($done) . "); }";
				$script .= "window.location.href = 'Cancel.php';";
			} else {
				// Moving event not found
				$script .= "alert('No moving booking was found for this Client ID. Please check the information and re-enter valid data.');";
			}
		}
	}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>Cancel A Client's Moving Event</title>
	<link rel="stylesheet" href="P1style.css">
	<style>
		.tableMain input[type="text"] { width: 40%; padding: 5px; font-size: 24px; }
	</style>
</head>

<body>

	<div class="tableMain">
	<form method="post" action="Cancel.php">
	<h1>Cancel A Client's Moving Event</h1>

	<p>Client's ID:</p>
	<input type="text" id="clientID" name="clientID" placeholder="Example: 5001">
	<label for="clientID">Required</label>

	<br><br>
	<button type="submit" id="sButton" name="cancel" value="1">Submit</button>
	</form>
	</div>

	<script><?php echo $script; ?></script>
</body>
</html>