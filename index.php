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
	function logThis($data)
	{
		// Get current data from json log
		$log = json_decode(file_get_contents("log.json"), true);
		$d = date("d");
		$m = date("m");
		$y = date("Y");
		if (!isset($log[$y]))
			$log[] = [$y => [$m => [$d]]];
		elseif (!isset($log[$y][$m]))
			$log[$y] = [$m => [$d]];
		elseif (!isset($log[$y][$m][$d]))
			$log[$y][$m] = [$d];
		array_push($log[date("Y")][date("m")][date("d")], $data);
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