<?php
if (!isset($_SERVER['REQUEST_URI']) || str_contains($_SERVER['REQUEST_URI'], 'admin')) header('location:../');
?><div class="container">
    <table>
        <tr>
            <th>Opprettelsesdato</th>
            <th>Brukertype</th>
            <th>Brukernavn</th>
        </tr>
        <?php
        foreach ($db["users"] as $user) {
        ?>
            <tr>
                <td><?php echo $user["creationDate"]; ?></td>
                <td><?php echo $user["elevated"] == 1 ? "Lærer" : "Elev"; ?></td>
                <td><?php echo $user["username"]; ?></td>
            </tr>
        <?php
        }
        ?>
    </table>
</div>
<?php
function addUser($errorMessage)
{
?>
    <div class="container pad">
        <form method="POST" action="">
            <h2>Legg til ny bruker</h2>
            <?php
            $d = $errorMessage == "" ? "none" : "block";
            echo "<div class='error' style='display: $d;'>$errorMessage</div>";
            ?>
            <div class="input-container">
                <label for="nuType">Brukertype</label>
                <select name="nuType" id="nuType" required>
                    <option value="0" selected>Elev</option>
                    <option value="1">Lærer</option>
                </select>
            </div>
            <div class="input-container">
                <label for="nuUsername">Brukernavn</label>
                <input type="text" name="nuUsername" id="nuUsername" placeholder="Brukernavn (uten id)" pattern="^[a-zæøå]{2,6}$" required>
            </div>
            <div class="input-container">
                <div class="above-input">
                    <label for="nuPassword">Passord</label>
                    <a onclick="showPassword('nuPassword')">Vis/Skjul</a>
                </div>
                <input type="password" name="nuPassword" id="nuPassword" placeholder="Passord" pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,}$" required>
                <input type='password' name='nuPasswordConfirm' id='nuPasswordConfirm' placeholder="Bekreft passord" required pattern='^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,}$'>
            </div>
            <input type="submit" value="Legg til">
        </form>
    </div>
<?php
}
if (isset($_POST["nuUsername"]) && isset($_POST["nuPassword"]) && isset($_POST["nuPasswordConfirm"]) && isset($_POST["nuType"])) {
    if (
        preg_match('/^[a-zæøå]{2,6}$/', trim($_POST['nuUsername'])) &&
        preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,}$/', $_POST['nuPassword']) &&
        preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,}$/', $_POST['nuPasswordConfirm']) &&
        preg_match('/^[01]{1}$/', $_POST['nuType'])
    ) {
        if ($_POST["nuPassword"] == $_POST["nuPasswordConfirm"]) {
            $matchingUsernames = 1;
            foreach ($db['users'] as $user) if (substr($user['username'], 0, strlen(trim($_POST['nuUsername']))) == trim($_POST['nuUsername'])) {
                $matchingUsernames++;
            }

            // Pad id with a "0" if there are less than 2 digits.
            if ($matchingUsernames < 10) $id = "0" . $matchingUsernames;
            else $id = $matchingUsernames;


            $user = [
                "username" => $_POST["nuUsername"] . $id,
                "password" => password_hash($_POST["nuPassword"], PASSWORD_BCRYPT),
                "elevated" => $_POST["nuType"],
                "creationDate" => date("Y.m.d"),
                "reserved" => [],
                "rented" => []
            ];

            array_push($db["users"], $user);
            file_put_contents('db.json', json_encode($db));

            $logData = [
                "time"=>date("Hms"),
                "user"=>$db["users"][$_COOKIE["userid"]]["username"],
                "func"=>"add_user",
                "addr"=>$_SERVER["REMOTE_ADDR"],
                "data"=>base64_encode(json_encode($user))
            ];
            logData($logData);

            die("Redirect cancelled");
            header("Location:./");
        } else addUser("Passordene samsvarer ikke :(");
    } else addUser("Et eller flere felter er formatert feil :(");
} else addUser("");
