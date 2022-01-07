<?php
if (!isset($_SERVER['REQUEST_URI']) || str_contains($_SERVER['REQUEST_URI'], 'admin')) header('location:../');
?>
<div class="col">
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
                        <?= $user["creationDate"] ?>
                    </td>
                    <td>
                        <?= $user["elevated"] == 1 ? "Lærer" : "Elev" ?>
                    </td>
                    <td>
                        <?= $user["username"] ?>
                    </td>
                </tr>
            <?php } ?>
        </table>
    </div>
</div>
<?php
function addUser($errorMessage)
{
    $regexPassword = substr($GLOBALS["regex"]["password"], 1, -1);
    $regexUsername = substr($GLOBALS["regex"]["usernamenoid"], 1, -1);
    $d = $errorMessage == "" ? "none" : "block";
?>
    <div class="container pad">
        <form method="POST" action="">
            <h2>Legg til ny bruker</h2>
            <div class='error' style='display: <?= $d ?>;'><?= $errorMessage ?></div>
            <div class="input-container">
                <label for="nuType">Brukertype</label>
                <select name="nuType" id="nuType" required>
                    <option value="0" selected>Elev</option>
                    <option value="1">Lærer</option>
                </select>
            </div>
            <div class="input-container">
                <label for="nuUsername">Brukernavn</label>
                <input type="text" name="nuUsername" id="nuUsername" placeholder="Brukernavn (uten id)" pattern="<?= $regexUsername ?>" required>
            </div>
            <div class="input-container">
                <div class="bar">
                    <label for="nuPassword">Passord</label>
                    <a onclick="showPassword('nuPassword')">Vis/Skjul</a>
                </div>
                <input type="password" name="nuPassword[]" id="nuPassword" placeholder="Passord" pattern="<?= $regexPassword ?>" required>
                <input type='password' name='nuPassword[]' placeholder="Bekreft passord" required pattern='<?= $regexPassword ?>'>
            </div>
            <input type="submit" value="Legg til bruker">
        </form>
    </div>
<?php
}
function deleteUser($errorMessage)
{
    $db = $GLOBALS['db']; ?>
    <div class="container pad">
        <form method="POST" action="">
            <h2>Slett en eller flere brukere</h2>
            <?php
            $d = $errorMessage == "" ? "none" : "block";
            ?>
            <div class='error' style='display: <?= $d ?>;'><?= $errorMessage ?></div>
            <div class="input-container">
                <label for="deleteUser">Velg bruker</label>
                <select name="deleteUser" id="deleteUser">
                    <option value="" selected disabled>Velg fra listen</option>
                    <?php
                    for ($i = 0; $i < count($db["users"]); $i++)
                        if ($db["users"][$i]["elevated"] == 0) { ?>
                        <option value='<?= $i ?>'><?= $db['users'][$i]['username'] ?></option>
                    <?php } ?>
                </select>
            </div>
            <input type="submit" value="Fjern bruker">
        </form>
        <form action="" method="post">
            <div class="input-container">
                <label for="deleteGroupTime">Velg brukere opprettet mellom to datoer</label>
                <input type="date" name="deleteGroupTimeStart" id="deleteGroupTime">
                <input type="date" name="deleteGroupTimeEnd">
            </div>
            <input type="submit" value="Fjern bruker(e)">
        </form>
        <form action="" method="post">
            <div class="input-container">
                <label for="deleteGroupRegex">Velg brukere med Regex</label>
                <input type="text" name="deleteGroupRegex" id="deleteGroupRegex" placeholder="^test[123456789]{1}">
            </div>

            <input type="submit" value="Fjern bruker(e)">
        </form>
    </div>
<?php
}
function restoreUser()
{
    $db = $GLOBALS['db'];
?>
    <div class="container pad">
        <h2>Restaurer en slettet bruker</h2>
        <form action="" method="post">
            <div class="input-container">
                <label for="restoreUser">Velg bruker</label>
                <select name="restoreUser" id="restoreUser">
                    <option value="" selected disabled>Velg fra listen</option>
                    <?php
                    for ($i = 0; $i < count($db["deletedUsers"]); $i++) {
                    ?>
                        <option value="<?= $i ?>"><?= $db['deletedUsers'][$i]['username'] ?></option>
                    <?php
                    }
                    ?>
                </select>
            </div>
            <input type="submit" value="Restaurer bruker">
        </form>
    </div>
<?php
}
function filterLogs()
{
?>
    <div class="container pad">
        <h2>Filtrer loggen</h2>
        <form action="" method="POST">
            <div class="input-container">
                <label for="">Velg bruker</label>
                <select name="" id="" required>
                    <option value="" selected disabled>Velg fra listen</option>
                </select>
            </div>
            <input type="submit" value="Filtrer etter bruker">
        </form>
        <form action="" method="POST">
            <div class="input-container">
                <label for="">Velg nett</label>
                <select name="" id="">
                    <option value="" selected disabled>Velg fra listen</option>
                    <option value="0">Elevnett</option>
                    <option value="1">Lærernett</option>
                </select>
            </div>
            <input type="submit" value="Filtrer etter nett">
        </form>
    </div>
<?php
}
function showLogs($logs, $method)
{
?>
    <div class="container no-gap">
        <div class="bar pad">
            <h2>Logg</h2>
            <form action="" method="post" class="small input-container horizontal" oninput="this.submit()">
                <label for="logDate">Spesifiser en dato</label>
                <input type="date" name="logDate" id="logDate" max="<? echo date('Y-m-d'); ?>">
                <?php if ($method == "single") { ?>
                    <button name="logDateClear" class="remove icon" value="clear">&times;</button>
                <?php } ?>
            </form>
        </div>
        <table>
            <tr>
                <th>Tid</th>
                <th>Bruker</th>
                <th>IP-addresse</th>
                <th>Handling</th>
                <th>Data</th>
            </tr>
            <?php
            if ($method == "multiple") {
                foreach ($logs as $logYK => $logY)
                    foreach ($logY as $logMK => $logM)
                        foreach ($logM as $logDK => $logD)
                            for ($i = 0; $i < count($logD); $i++) {
            ?>
                    <tr>
                        <td><?= "$logYK/$logMK/$logDK " . $logD[$i]["time"] ?></td>
                        <td><?= $logD[$i]["user"] ?></td>
                        <td><?= $logD[$i]["addr"] ?></td>
                        <td><?= $logD[$i]["func"] ?></td>
                        <td><a target='_blank' href='php/raw.php?d=<?= urlencode($logD[$i]["data"]) ?>'>Åpne</a></td>
                    </tr>
            <?php
                            }
            }
            ?>
        </table>
    </div>
<?php
}
?>
<div class="col">

    <?php
    if (
        isset($_POST["nuUsername"]) &&
        isset($_POST["nuPassword"][0]) &&
        isset($_POST["nuPassword"][1]) &&
        isset($_POST["nuType"])
    ) {
        if ($_POST["nuPassword"][0] == $_POST["nuPassword"][1]) {
            if (
                preg_match($regex["usernamenoid"], trim($_POST['nuUsername'])) &&
                preg_match($regex["password"], $_POST['nuPassword'][0]) &&
                preg_match('/^[01]{1}$/', $_POST['nuType'])
            ) {
                $i = 1;
                $exists = true;
                while ($exists) {

                    // Pad id with a "0" if there are less than 2 digits.
                    if ($i < 10) $id = "0" . $i;
                    else $id = $i;

                    $exists = false;
                    $n = 0;
                    while ($n < count($db["users"]) && !$exists) {
                        if ($db['users'][$n]["username"] == $_POST['nuUsername'] . $id) $exists = true;
                        $n++;
                    }
                    if (!$exists) break;

                    $i++;
                }

                $user = [
                    "username" => $_POST["nuUsername"] . $id,
                    "password" => password_hash($_POST["nuPassword"][0], PASSWORD_BCRYPT),
                    "elevated" => $_POST["nuType"],
                    "creationDate" => date("Y.m.d"),
                    "reserved" => [],
                    "rented" => []
                ];
                $db["users"][] = $user;
                file_put_contents('db.json', json_encode($db));

                $logData = [
                    "time" => date("H:i:s"),
                    "user" => $db["users"][$_COOKIE["userid"]]["username"],
                    "func" => "add_user",
                    "addr" => $_SERVER["REMOTE_ADDR"],
                    "data" => base64_encode(json_encode($user))
                ];
                logThis($logData);
                header("Location:./");
            } else addUser("Et eller flere felter er formatert feil :(");
        } else addUser("Passordene samsvarer ikke :(");
    } else addUser("");
    ?>
</div>
<div class="col">
    <?php
    // && !is_null($_POST["deleteUser"])
    if (isset($_POST["deleteUser"])) {
        if (isset($db["users"][$_POST["deleteUser"]])) {
            $userid = $_POST["deleteUser"];
            if ($db["users"][$userid]["elevated"] == "0") {

                // Save the deleted user in a separate array if it needs to be recovered later
                $deletedUser = $db["users"][$userid];
                $db["deletedUsers"][] = $deletedUser;

                // Delete user from userlist
                unset($db["users"][$userid]);
                $db["users"] = array_values($db["users"]);
                file_put_contents("db.json", json_encode($db));

                // Log the action
                $logData = [
                    "time" => date("H:i:s"),
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
    ?>
</div>
<div class="col">
    <?php
    if (isset($_POST["restoreUser"]) && isset($db["deletedUsers"][$_POST["restoreUser"]])) {
        $db["users"][] = $db["deletedUsers"][$_POST["restoreUser"]];
        $logData = [
            "time" => date("H:i:s"),
            "user" => $db["users"][$_COOKIE["userid"]]["username"],
            "func" => "restore_user",
            "addr" => $_SERVER["REMOTE_ADDR"],
            "data" => base64_encode(json_encode($db["deletedUsers"][$_POST["restoreUser"]]))
        ];
        logThis($logData);
        unset($db["deletedUsers"][$_POST["restoreUser"]]);
        $db["deletedUsers"] = array_values($db["deletedUsers"]);
        file_put_contents("db.json", json_encode($db));
        header("Location:./");
    } else restoreUser();
    ?>
</div>
</section>
<section id="logs">
    <div class="col">
        <?php
        if (isset($_POST["logFilter"])) {
            // Filtrer etter:
            // * Tid,
            // * Bruker,
            // * Elev-/Lærernett,
            // * IP-addresse,
            // * Hendelse
        } else filterLogs();
        ?>
    </div>
    <div class="col large">
        <?php
        $log = importLogs();
        if (isset($_POST["logDate"])) {
            $logDate = explode("-", $_POST["logDate"]);
            $logList = $log[$logDate[0]][$logDate[1]][$logDate[2]];
            showLogs($logList, "single");
        } else {
            $logList = $log;
            showLogs($logList, "multiple");
        }
        ?>
    </div>
</section>