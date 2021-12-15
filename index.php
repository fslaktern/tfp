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

	// Get data from Database
	// Is formatted like: 
        // ["equipment"][n]["name"|"reserved"|"rented"|"amount"]
        // ["users"][n]
	$db = json_decode(file_get_contents("db.json"), true);

	// Check if user has logged in properly
	if (isset($_COOKIE['userid'])) {

		// Loop through user list to see if any user 
		// with matches the encrypted credentials
		// in the userid-cookie 
		$e = FALSE;
		for($i=0; $i<count($db["users"]); $i++) {
			// if($db["users"][$i]["username"] == trim($_POST["username"])) echo "<br><br>".$db["users"][$i]["username"] . " == " . $_POST["username"];
			// else echo "<br><br>".$db["users"][$i]["username"] . " != " . $_POST["username"];
			// if(password_verify($_POST["password"], $db["users"][$i]["password"])) echo "<br>" . $_POST["password"] . " == " . $db["users"][$i]["password"];
			// else echo "<br>" . $_POST["password"] . " != " . $db["users"][$i]["password"];
            if($db["users"][$i]["username"] == trim($_POST["username"]) && password_verify($_POST["password"], $db["users"][$i]["password"])) {
                $e = TRUE;
                break;
            }
        }
		if ($e) $p = "manage";
		else $p = "login";
	} else $p = "login";

	// Show the appropriate page without redirecting
	echo "<section id='" . $p . "'>";
	include("php/" . $p . ".php");
	?>
	</section>
</body>
</html>