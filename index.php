<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="css/style.css">
	<script src="js/main.js" defer></script>
	<title>Drømtorp VGS</title>
</head>

<body>
	<?php

	// Get data from json database 
	$db = json_decode(file_get_contents("db.json"), true);

	// Check if user with the id exists
	if (isset($_COOKIE['userid'])) {
		if ($db["users"][$_COOKIE['userid']]) $p = "manage";
		else $p = "login";
	} else $p = "login";

	// Show the appropriate page without redirecting
	echo "<section id='" . $p . "'>";
	include("php/" . $p . ".php");
	?>
	</section>
</body>

</html>

<?php
die();
// Kode som ikke trengs lenger:

// Loop through user list to see if any user 
// matches the encrypted credentials in the 
// userid-cookie or from the POST request.
$e = FALSE;
for ($i = 0; $i < count($db["users"]); $i++) {

	// If any user matches, stop looping
	if (
		password_verify($db["users"][$i]["username"] . $db["users"][$i]["username"], $_COOKIE["userid"]) ||
		($db['users'][$i]['username'] == trim($_POST['username']) &&
			password_verify($_POST['password'], $db['users'][$i]['password']))
	) {
		$e = TRUE;
		break;
	}
}
