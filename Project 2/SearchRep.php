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
$invalid = false;
$result = false;

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

if (isset($_SESSION["rep_id"])){
	$repID = (int)$_SESSION["rep_id"];
	$result = mysqli_query($con, "SELECT r.RepFirstName, r.RepLastName, r.RepID,
	r.RepPhoneNumber, r.RepEmail,
	c.ClientFirstName, c.ClientLastName, c.ClientID,
	p.ClientStreetNumber, p.ClientStreetName, p.ClientCity, p.ClientState, p.ClientZipCode, p.ClientPhoneNumber,
	m.MoveToStreetNumber, m.MoveToStreetName, m.MoveToCity, m.MoveToState, m.MoveToZipCode,
	m.MovingDate, m.TotalWeightLbs, m.TotalCost, s.SpecialInstructions
	FROM movingRepresentatives r
	JOIN ClientManyReps rc ON r.RepID = rc.RepID
	JOIN Client c ON rc.ClientID = c.ClientID
	JOIN ClientPersonalInfo p ON c.ClientID = p.ClientID
	JOIN ClientMovingInfo m ON c.ClientID = m.ClientID
	LEFT JOIN ClientSpecialInstructions s ON c.ClientID = s.ClientID
	WHERE r.RepID = $repID
	ORDER BY c.ClientLastName");
	}else{
	$invalid = true;
	}
if (!$result) { echo "Table query failed: " . mysqli_error($con); }
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Moving Company Representative Account</title>
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

	}

	th, td {
		border: 1px solid black;
		padding: 8px 10px;
		text-align: left;
	}
</style>
</head>
<body>
	<h1>Moving On Up - Representative Accounts</h1>

	<div class="RepClientInfo">
	<table>
	<tr>
		<th>Representative Name</th>
		<th>Representative ID</th>
		<th>Representative Phone</th>
		<th>Representative Email</th>
		<th>Client Name</th>
		<th>Client ID</th>
		<th>Client Street Number</th>
		<th>Client Street Name</th>
		<th>Client City</th>
		<th>Client State</th>
		<th>Client Zip Code</th>
		<th>Client Phone</th>
		<th>Client New Street Number</th>
		<th>Client New Street Name</th>
		<th>Client New City</th>
		<th>Client New State</th>
		<th>Client New Zip Code</th>
		<th>Date of Move</th>
		<th>Weight of Items (lbs)</th>
		<th>Cost of Move</th>
		<th>Special Instructions</th>
	</tr>
	
	<?php
	while ($result && $row = mysqli_fetch_assoc($result)) {
		echo "<tr>";
		echo "<td>" . $row["RepFirstName"] . " " . $row["RepLastName"] . "</td>";
		echo "<td>" . $row["RepID"] . "</td>";
		echo "<td>" . $row["RepPhoneNumber"] . "</td>";
		echo "<td>" . $row["RepEmail"] . "</td>";
		echo "<td>" . $row["ClientFirstName"] . " " . $row["ClientLastName"] . "</td>";
		echo "<td>" . $row["ClientID"] . "</td>";
		echo "<td>" . $row["ClientStreetNumber"] . "</td>";
		echo "<td>" . $row["ClientStreetName"] . "</td>";
		echo "<td>" . $row["ClientCity"] . "</td>";
		echo "<td>" . $row["ClientState"] . "</td>";
		echo "<td>" . $row["ClientZipCode"] . "</td>";
		echo "<td>" . $row["ClientPhoneNumber"] . "</td>";
		echo "<td>" . $row["MoveToStreetNumber"] . "</td>";
		echo "<td>" . $row["MoveToStreetName"] . "</td>";
		echo "<td>" . $row["MoveToCity"] . "</td>";
		echo "<td>" . $row["MoveToState"] . "</td>";
		echo "<td>" . $row["MoveToZipCode"] . "</td>";
		echo "<td>" . $row["MovingDate"] . "</td>";
		echo "<td>" . $row["TotalWeightLbs"] . "</td>";
		echo "<td>$" . number_format($row["TotalCost"], 2) . "</td>";
		echo "<td>" . $row["SpecialInstructions"] . "</td>";
		echo "</tr>";
	}
	?>
	</table>
	</div>

	<?php if ($invalid): ?>
	<script>
        alert("Representative account cannot be found.");
    	</script>
    	<?php endif; ?>
</body>
</html>
