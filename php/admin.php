<?php
if (!isset($_SERVER['REQUEST_URI']) || str_contains($_SERVER['REQUEST_URI'], 'admin')) header('location:../');
?>
<div class="container">
    <table>
        <tr>
            <th>Opprettet</th>
            <th>Type</th>
            <th>Brukernavn</th>
        </tr>
        <?php foreach ($db["users"] as $user) { ?>
            <tr>
                <td>
                    <?php echo $user["creationDate"]; ?>
                </td>
                <td>
                    <?php echo $user["elevated"] == 1 ? "Lærer" : "Elev"; ?>
                </td>
                <td>
                    <?php echo $user["username"]; ?>
                </td>
            </tr>
        <?php } ?>
    </table>
</div>
<?php
function addUser($errorMessage)
{ ?>
    <div class="container pad">
        <form method="POST" action="">
            <h2>Legg til ny bruker</h2>
            <?php $d = $errorMessage == "" ? "none" : "block";
            echo "<div class='error' style='display: $d;'>$errorMessage</div>"; ?>
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
                <div class="bar">
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
function deleteUser($errorMessage)
{
    $db = $GLOBALS['db']; ?>
    <div class="container pad">
        <form method="POST" action="">
            <h2>Slett en eller flere bruker</h2>
            <?php $d = $errorMessage == "" ? "none" : "block";
            echo "<div class='error' style='display: $d;'>$errorMessage</div>"; ?>
            <div class="input-container">
                <label for="deleteUser">Velg bruker</label>
                <select name="deleteUser" id="deleteUser" required>
                    <option value="" selected disabled>Velg fra listen</option>
                    <?php for ($i = 0; $i < count($db["users"]); $i++) {
                        if ($db["users"][$i]["elevated"] == 0) {
                            echo "<option value='" . $i . "'>" . $db['users'][$i]['username'] . "</option>";
                        }
                    } ?>
                </select>
            </div>
            <div class="input-container">
                <label for="deleteGroup">Velg gruppe</label>
            </div>
            <input type="submit" value="Fjern bruker">
        </form>
    </div>
<?php
}
if (isset($_POST["nuUsername"]) && isset($_POST["nuPassword"]) && isset($_POST["nuPasswordConfirm"]) && isset($_POST["nuType"])) {
    if (
        preg_match('/^[a-zæøå]{2,6}$/', trim($_POST['nuUsername'])) &&
        preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,}$/', $_POST['nuPassword']) &&
        preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,}$/', $_POST['nuPasswordConfirm']) &&
        preg_match('/^[01]{1}$/', $_POST['nuType']) &&
        $_POST['nuUsername'] &&
        $_POST['nuPassword']
    ) {
        if ($_POST["nuPassword"] === $_POST["nuPasswordConfirm"]) {
            $i = 1;
            $exists = true;
            while ($exists == true) {

                // Pad id with a "0" if there are less than 2 digits.
                if ($i < 10) $id = "0" . $i;
                else $id = $i;

                $n = $i % count($db["users"]);
                if ($db['users'][$n]['username'] == $_POST['nuUsername'] . $id) $i++;

                $exists = false;
                for ($d = 0; $d < count($db["users"]); $d++) {
                    if ($db['users'][$d]["username"] == $_POST['nuUsername'] . $id) {
                        $exists = true;
                        break;
                    }
                }
                if (!$exists) break;
                echo "i=$i,d=$d,n=$n,id=$id,exists=$exists";
            }

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
                "time" => date("H:m:s"),
                "user" => $db["users"][$_COOKIE["userid"]]["username"],
                "func" => "add_user",
                "addr" => $_SERVER["REMOTE_ADDR"],
                "data" => base64_encode(json_encode($user))
            ];
            logThis($logData);
            header("Location:./");
        } else addUser("Passordene samsvarer ikke :(");
    } else addUser("Et eller flere felter er formatert feil :(");
} else addUser("");

if (isset($_POST["deleteUser"]) && !is_null($_POST["deleteUser"])) {
    if (isset($db["users"][$_POST["deleteUser"]])) {
        $userid = $_POST["deleteUser"];
        if ($db["users"][$userid]["elevated"] == "0") {

            // Save the deleted user in a separate array if it needs to be recovered later
            $deletedUser = $db["users"][$userid];
            array_push($db["deletedUsers"], $deletedUser);

            // Delete user from userlist
            unset($db["users"][$userid]);
            file_put_contents("db.json", json_encode($db));

            // Log the action
            $logData = [
                "time" => date("H:m:s"),
                "user" => $db["users"][$_COOKIE["userid"]]["username"],
                "func" => "delete_user",
                "addr" => $_SERVER["REMOTE_ADDR"],
                "data" => base64_encode(json_encode($deletedUser))
            ];
            logThis($logData);

            // Update page with new changes
            header("Location:./");
        } else deleteUser("Du kan dessverre ikke fjerne denne brukeren :(");
    } else deleteUser("Denne brukeren finnes ikke :(");
} else deleteUser("");

if (isset($_POST["restoreUser"])) {
}
?>
</section>
<section id="logs">
    <?php
    $logs = json_decode(file_get_contents("log.json"), true);
    $logsToday = $logs[date("Y")][date("m")][date("d")];
    ?>
    <div class="container container-large">
        <div class="bar">
            <h2>Logg</h2>
            <input type="date" name="logDate" value="<?php echo date("Y/m/d"); ?>">
        </div>
        <table>
            <tr>
                <th>Klokkeslett</th>
                <th>Bruker</th>
                <th>IP-addresse</th>
                <th>Handling</th>
                <th>Data</th>
            </tr>
            <?php
            for ($i = 0; $i < count($logsToday); $i++) {
                echo "
                <tr>
                    <td>" . $logsToday[$i]["time"] . "</td>
                    <td>" . $logsToday[$i]["user"] . "</td>
                    <td>" . $logsToday[$i]["addr"] . "</td>
                    <td>" . $logsToday[$i]["func"] . "</td>
                    <td>
                    <a target='_blank' href='php/raw.php?d=" . urlencode($logsToday[$i]["data"]) . "'>Åpne</td>
                </tr>
                ";
            }
            ?>
        </table>
    </div>
</section>