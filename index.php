<!DOCTYPE htmly
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
	function logData($data) {

		// Get data from json log
		$log = json_decode(file_get_contents("log.json"), true);

		if(!isset($log[date("Y")][date("m")][date("d")])) {
			if (!isset($log[date("Y")])) $log[] = date("Y");
			if (!isset($log[date("Y")][date("m")])) $log[date("y")] = date("m");
			if (!isset($log[date("Y")][date("m")][date("d")])) $log[date("Y")][date("m")] = [date("d")=>[]];
		}
		#print_r($log);
		#array_push($log[date("Y")][date("m")][date("d")], [$data]);
		var_dump($log);
		file_put_contents('log.json', json_encode($log));
	}

	// Get data from json database
	$db = json_decode(file_get_contents("db.json"), true);

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