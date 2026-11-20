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
$defaultClientID = isset($_SESSION["special_client"]) ? (int)$_SESSION["special_client"] : "";

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

	// Step 1: Check booking & existing special instructions when checking a client
	if (isset($_POST["checkClient"])){
		$clientIDRaw = $_POST["clientID"];

		if ($clientIDRaw == "" || !is_numeric($clientIDRaw) || (int)$clientIDRaw <= 0){
			$script .= "alert('There is an issue with the data. Please enter a valid numeric Client ID.');";
			$script .= "window.location.href = 'SpecialInstructions.php';";
		} else {
			$clientID = (int)$clientIDRaw;
			$defaultClientID = $clientID;

			// Check if a moving event booking exists for this client
			$moveCheck = mysqli_query($con, "SELECT ClientID FROM ClientMovingInfo WHERE ClientID = $clientID");

			if (!$moveCheck || mysqli_num_rows($moveCheck) == 0){
				$msg = "A moving event booking does not exist for Client ID " . $clientID . ". Please check the information and re-enter valid data.";
				$script .= "alert(" . json_encode($msg) . ");";
				$script .= "window.location.href = 'SpecialInstructions.php';";
			} else {
				// Booking exists: check if special instructions already exist
				// Update SpecialInstructions table/column names below if your schema uses different names
				$instCheck = mysqli_query($con, "SELECT ClientID FROM SpecialInstructions WHERE ClientID = $clientID");

				if ($instCheck && mysqli_num_rows($instCheck) > 0){
					$_SESSION["update_special_client"] = $clientID;
					$msg = "Special instructions already exist for Client ID " . $clientID . ". Redirecting to Update Special Instructions.";
					$script .= "alert(" . json_encode($msg) . ");";
					$script .= "window.location.href = 'UpdateSpecialInstructions.php';";
				} else {
					$_SESSION["special_client"] = $clientID;
					$ask = "No special instructions exist yet for Client ID " . $clientID . ". Do you wish to request special instructions for the move?";
					$script .= "if (!confirm(" . json_encode($ask) . ")) { window.location.href = 'SpecialInstructions.php'; } else { document.getElementById('instructions').focus(); }";
				}
			}
		}
	}

	// Step 2: Validate and save new special instructions
	if (isset($_POST["saveInstructions"])){
		$clientIDRaw = $_POST["clientID"];
		$instructionsRaw = $_POST["instructions"];

		if ($clientIDRaw == "" || !is_numeric($clientIDRaw) || (int)$clientIDRaw <= 0 || $instructionsRaw == "" || strlen($instructionsRaw) > 500){
			$script .= "alert('There is an issue with the data entered. Please enter a valid Client ID and special instructions (1 to 500 characters).');";
		} else {
			$clientID = (int)$clientIDRaw;
			$defaultClientID = $clientID;
			$movingID = isset($_SESSION["moving_id_" . $clientID]) ? $_SESSION["moving_id_" . $clientID] : "N/A";

			// Ensure a moving event booking exists before inserting
			$moveCheck = mysqli_query($con, "SELECT ClientID FROM ClientMovingInfo WHERE ClientID = $clientID");

			if (!$moveCheck || mysqli_num_rows($moveCheck) == 0){
				$msg = "A moving event booking does not exist for Client ID " . $clientID . ". Please check the information and re-enter valid data.";
				$script .= "alert(" . json_encode($msg) . ");";
				$script .= "window.location.href = 'SpecialInstructions.php';";
			} else {
				$instCheck = mysqli_query($con, "SELECT ClientID FROM SpecialInstructions WHERE ClientID = $clientID");
				if ($instCheck && mysqli_num_rows($instCheck) > 0){
					$_SESSION["update_special_client"] = $clientID;
					$msg = "Special instructions already exist for Client ID " . $clientID . ". Redirecting to Update Special Instructions.";
					$script .= "alert(" . json_encode($msg) . ");";
					$script .= "window.location.href = 'UpdateSpecialInstructions.php';";
				} else {
					$instructions = mysqli_real_escape_string($con, $instructionsRaw);

					// Update SpecialInstructions table/column names below if your schema uses different names
					$saved = mysqli_query($con, "INSERT INTO SpecialInstructions (ClientID, SpecialInstructions)
					VALUES ($clientID, '$instructions')");

					if (!$saved){
						$script .= "alert(" . json_encode("There is an issue saving the data: " . mysqli_error($con)) . ");";
					} else {
						unset($_SESSION["special_client"]);
						$done = "The special instructions request has taken place.\n\nMoving Representative ID: " . $repID . "\nClient ID: " . $clientID . "\nMoving ID: " . $movingID;
						$script .= "alert(" . json_encode($done) . ");";
						$script .= "window.location.href = 'SpecialInstructions.php';";
					}
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
	<title>Special Instructions</title>
	<link rel="stylesheet" href="P1style.css">
	<style>
		.tableMain input[type="text"] { width: 40%; padding: 5px; font-size: 24px; }
		.tableMain textarea { width: 40%; padding: 8px; font-size: 18px; }
	</style>
</head>

<body>

	<div class="tableMain">
	<form method="post" action="SpecialInstructions.php">
	<h1>Special Instructions for the Move</h1>

	<p>Client ID:</p>
	<input type="text" id="clientID" name="clientID" value="<?php echo htmlspecialchars($defaultClientID); ?>" placeholder="Example: 5001">
	<label for="clientID">Required</label>
	<button type="submit" name="checkClient" value="1">Check Booking</button>

	<p>Special Instructions for the Move:</p>
	<textarea id="instructions" name="instructions" rows="4" placeholder="Example: Fragile piano, use freight elevator, call 30 minutes prior."></textarea>
	<label for="instructions">Required</label>

	<br><br>
	<button type="submit" id="sButton" name="saveInstructions" value="1">Submit</button>
	</form>
	</div>

	<script><?php echo $script; ?></script>
</body>
</html>