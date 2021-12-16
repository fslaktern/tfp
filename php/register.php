<table>
    <tr>
        <th>Brukertype</th>
        <th>Brukernavn</th>
    </tr>
    <?php
    foreach ($db["users"] as $user) {
    ?>
        <tr>
            <td><?php echo $user["elevated"] == 1 ? "Lærer" : "Elev"; ?></td>
            <td><?php echo $user["username"]; ?></td>
        </tr>
    <?php
    }
    ?>
</table>

<?php
function addUser($errorMessage)
{
?>
    <form method="POST" action="">
        <h2>Legg til ny bruker</h2>

        <?php
        $d = $errorMessage == "" ? "none" : "block";
        echo "<div id='errorNu' class='error' style='display: $d;'>$errorMessage</div>";
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
            <input type="text" name="nuUsername" id="nuUsername" placeholder="Brukernavn" pattern="^[a-zæøå]{2,6}[0-9]{2}$" required>
        </div>
        <div class="input-container">
            <label for="nuPassword">Passord</label>
            <input type="password" name="nuPassword" id="nuPassword" placeholder="Passord" pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,}$" required>
        </div>
        <input type="submit" value="Legg til">
    </form>
<?php
}
// "nu" = new user
if ($_POST["nuUsername"] && $_POST["nuPassword"] && $_POST["nuType"]) {
    if (
        preg_match('/^[a-zæøå]{2,6}[0-9]{2}$/', trim($_POST['nuUsername'])) &&
        preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,}$/', $_POST['nuPassword']) &&
        preg_match('/^[01]{1}$/', $_POST['nuType'])
    ) {
        $user = [
            "username" => $_POST["nuUsername"],
            "password" => password_hash($_POST["nuPassword"], PASSWORD_BCRYPT),
            "elevated" => $_POST["nuType"],
            "reserved" => [],
            "rented" => []
        ];
        print_r($db);
        $users = $db->users();
        print_r($users);
        array_push($db["users"], $user);
        print_r($db);
        file_put_contents('db2.json', json_encode($db));
    } else addUser("Et eller flere felter er formatert feil :(");
} else addUser("");
