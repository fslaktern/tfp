<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="css/style.css">
	<link rel="stylesheet" href="css/fonts.css">
	<script src="js/main.js" defer></script>
	<title>Drømtorp VGS</title>
</head>

<body>
	<?php
	function importLogs()
	{
		// Get data from json log
		return json_decode(file_get_contents("log.json"), true);
	}
	function logThis($logData)
	{
		$log = importLogs();

		// Get date
		$d = date("d");
		$m = date("m");
		$y = date("Y");

		if (is_null($log[$y])) $log[] = [$y => [$m => [$d => $logData]]];
		elseif (is_null($log[$y][$m])) $log[$y][] = [$m => [$d => $logData]];
		elseif (is_null($log[$y][$m][$d])) $log[$y][$m][] = [$d => $logData];
		else $log[$y][$m][$d][] = $logData;
		file_put_contents('log.json', json_encode($log));
	}

	// Get data from json database
	$db = json_decode(file_get_contents("db.json"), true);

	// Assigning variables which are required in more than one page

	// Minimum requirements for a password: 
	// * 8 characters long, 
	// * 1 capital letter, 
	// * 1 lower case letter,
	// * 1 number,
	$regex = [
		"password" => '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,}$/',
		"username" => '/^[a-zæøå]{2,6}[0-9]{2}$/',
		"usernamenoid" => '/^[a-zæøå]{2,6}$/'
	];

	// Check if user with the id exists
	if (isset($_COOKIE['userid'])) {
		if (isset($db["users"][$_COOKIE['userid']])) $p = "main";
		else $p = "login";
	} else $p = "login";

	// Show the appropriate page without redirecting
	echo "<section id='" . $p . "'>";
	include("php/" . $p . ".php");
	?>
	</section>
</body>

</html>