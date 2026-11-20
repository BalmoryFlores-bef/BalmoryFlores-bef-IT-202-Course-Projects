<?php
session_start();
error_reporting(E_ALL);
ini_set("display_errors", 1);

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

	if ($login && mysqli_num_rows($login) > 0){
		$_SESSION["rep_id"] = $repID;
	} else {
		unset($_SESSION["rep_id"]);
	}
}

if (isset($_SESSION["rep_id"]) && isset($_POST["createClient"])){
	$firstNameRaw = trim($_POST["clientFirstName"]);
	$lastNameRaw = trim($_POST["clientLastName"]);
	$clientIDRaw = trim($_POST["clientID"]);

	$firstName = mysqli_real_escape_string($con, $firstNameRaw);
	$lastName = mysqli_real_escape_string($con, $lastNameRaw);

	if ($firstNameRaw == "" || $lastNameRaw == "" || $clientIDRaw == ""){
		$script .= "alert('Client First Name, Client Last Name, and Client ID are all required.');";
	} elseif (!is_numeric($clientIDRaw) || (int)$clientIDRaw <= 0){
		$script .= "alert('Client ID must be a valid positive number.');";
	} elseif (!preg_match("/^[a-zA-Z\s'-]+$/", $firstNameRaw) || !preg_match("/^[a-zA-Z\s'-]+$/", $lastNameRaw)){
		$script .= "alert('Client First Name and Last Name may only contain letters, spaces, apostrophes, or hyphens.');";
	} else {
		$clientID = (int)$clientIDRaw;
		$existing = mysqli_query($con, "SELECT ClientID FROM Client WHERE ClientID = $clientID");

		if ($existing && mysqli_num_rows($existing) > 0){
			$msg = "An account already exists for Client ID " . $clientID . ". Please choose another option from the Navigation Bar.";
			$script .= "alert(" . json_encode($msg) . "); window.location.href = 'CreateClient.php';";
		} else {
			$inserted = mysqli_query($con, "INSERT INTO Client (ClientID, ClientFirstName, ClientLastName) VALUES ($clientID, '$firstName', '$lastName')");
			if (!$inserted){
				$script .= "alert(" . json_encode("Could not create client account: " . mysqli_error($con)) . ");";
			} else {
				$_SESSION["new_client_id"] = $clientID;
				$msg = "An account was created for " . $firstNameRaw . " " . $lastNameRaw . " (Client ID: " . $clientID . ").";
				$script .= "alert(" . json_encode($msg) . "); window.location.href = 'ClientInfo.php';";
			}
		}
	}
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>Create A New Client Account</title>
	<link rel="stylesheet" href="P1style.css">
	<style>
		.tableMain input[type="text"] { width: 40%; padding: 5px; font-size: 24px; }
	</style>
</head>

<body>

	<div class="tableMain">
	<form method="post" action="CreateClient.php">
	<h1>Create A New Client Account</h1>

	<p>Client First Name:</p>
	<input type="text" id="clientFirstName" name="clientFirstName" placeholder="Example: Jane">
	<label for="clientFirstName">Required</label>

	<p>Client Last Name:</p>
	<input type="text" id="clientLastName" name="clientLastName" placeholder="Example: Doe">
	<label for="clientLastName">Required</label>

	<p>Client ID:</p>
	<input type="text" id="clientID" name="clientID" placeholder="Example: 5001">
	<label for="clientID">Required</label>

	<br><br>
	<button type="submit" id="sButton" name="createClient" value="1">Submit</button>
	</form>
	</div>

	<script><?php echo $script; ?></script>
</body>
</html>
