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
$defaultClientID = isset($_SESSION["new_client_id"]) ? (int)$_SESSION["new_client_id"] : "";

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
	$repID = (int)$_SESSION["rep_id"];

	// The client info form was submitted
	if (isset($_POST["saveInfo"])){
		$clientIDRaw = $_POST["clientID"];
		$phoneRaw = $_POST["phone"];
		$phoneDigits = preg_replace("/[^0-9]/", "", $phoneRaw);
		$streetNum = mysqli_real_escape_string($con, $_POST["streetNumber"]);
		$streetName = mysqli_real_escape_string($con, $_POST["streetName"]);
		$city = mysqli_real_escape_string($con, $_POST["city"]);
		$state = mysqli_real_escape_string($con, strtoupper($_POST["state"]));
		$zip = mysqli_real_escape_string($con, $_POST["zip"]);

		if ($clientIDRaw == "" || $phoneRaw == "" || $streetNum == "" || $streetName == "" || $city == "" || $state == "" || $zip == ""){
			$script .= "alert('All fields are required.');";
		} elseif (strlen($phoneDigits) != 10){
			$script .= "alert('Phone number must have 10 digits.');";
		} else {
			$clientID = (int)$clientIDRaw;
			$phone = mysqli_real_escape_string($con, $phoneDigits);
			$clientCheck = mysqli_query($con, "SELECT ClientID FROM Client WHERE ClientID = $clientID");

			if (!$clientCheck || mysqli_num_rows($clientCheck) == 0){
				$script .= "alert('No client account found with that Client ID.');";
			} else {
				// REPLACE adds the client info record, or replaces the client's earlier one
				// Update the table name and phone column name to match your database schema
				$saved = mysqli_query($con, "REPLACE INTO ClientPersonalInfo (ClientID, ClientStreetNumber, ClientStreetName, ClientCity, ClientState, ClientZipCode, ClientPhonenumber)
				VALUES ($clientID, '$streetNum', '$streetName', '$city', '$state', '$zip', '$phone')");

				if (!$saved){
					$script .= "alert(" . json_encode("The Client Information Record could not be created: " . mysqli_error($con)) . ");";
				} else {
					unset($_SESSION["new_client_id"]);
					$script .= "alert('The Client Information Record was created.');";
					$script .= "window.location.href = 'ClientInfo.php';";
				}
			}
		}
	}
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>Client Information</title>
	<link rel="stylesheet" href="P1style.css">
	<style>
		.tableMain input[type="text"] { width: 40%; padding: 5px; font-size: 24px; }
	</style>
</head>

<body>

	<div class="tableMain">
	<form method="post" action="ClientInfo.php">
	<h1>Client Information</h1>

	<p>Client ID:</p>
	<input type="text" id="clientID" name="clientID" value="<?php echo htmlspecialchars($defaultClientID); ?>" placeholder="Example: 5001">
	<label for="clientID">Required</label>

	<p>Street Number:</p>
	<input type="text" id="streetNumber" name="streetNumber" placeholder="Example: 120">
	<label for="streetNumber">Required</label>

	<p>Street Name:</p>
	<input type="text" id="streetName" name="streetName" placeholder="Example: Broad Street">
	<label for="streetName">Required</label>

	<p>City:</p>
	<input type="text" id="city" name="city" placeholder="Example: Newark">
	<label for="city">Required</label>

	<p>State:</p>
	<input type="text" id="state" name="state" placeholder="Example: NJ">
	<label for="state">Required</label>

	<p>Zip Code:</p>
	<input type="text" id="zip" name="zip" placeholder="Example: 07102">
	<label for="zip">Required</label>

	<p>Phone Number:</p>
	<input type="text" id="phone" name="phone" placeholder="Example: 973-555-0201">
	<label for="phone">Required</label>

	<br><br>
	<button type="submit" id="sButton" name="saveInfo" value="1">Submit</button>
	</form>
	</div>

	<script><?php echo $script; ?></script>
</body>
</html>