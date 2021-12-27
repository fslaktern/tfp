<!--
    TODO:
    - [ ] Få alle dato-inputtene til å funke.
    - [ ] Låning og Reservering som fungerer.
    - [ ] Se reserverte/utlånte produkter.
        - Elever skal kun se sitt eget.
        - Lærere skal kunne se alle sitt + mulighet til å overskrive elevers reservasjoner.
    - [ ] Passordkrav som er synlige for brukeren.
    - [ ] Automatisk sletting av reservasjoner om det ikke er hentet innen dato/tid.
    - [X] Knapp for å logge ut.
    - [/] Logging av alle hendelser på nettsiden.
    - [X] La lærere se loggen.
    - [ ] Mobilt layout.
    - [X] Mulighet til å bytte passord.
    - [X] Registrere nye brukere (kun for lærere).
    - [ ] Filterfunksjoner i loggen.

    EGET FORM-ELEMENT FOR HVER FUNKSJON, ALT SKAL VÆRE REQUIRED.
 -->
<?php
// Prevent users from opening this page directly (or without logging in first)
if (!isset($_SERVER['REQUEST_URI']) || str_contains($_SERVER['REQUEST_URI'], 'main')) header('location:../');
ob_start();
function changePassword($errorMessage)
{
    $regexPassword = substr($GLOBALS['regex']['password'], 1, -1);
?>
    <form method="POST" action="">
        <h2>Bytt passord</h2>
        <?php $d = $errorMessage == "" ? "none" : "block"; ?>
        <div class='error' style='display: <?= $d ?>;'><?= $errorMessage ?></div>
        <div class="input-container">
            <div class="bar">
                <label for="oldPassword">Nåværende passord</label>
                <a onclick="showPassword('oldPassword')">Vis/Skjul</a>
            </div>
            <input type='password' id='oldPassword' name='oldPassword' placeholder="Nåværende passord" required pattern='<?= $regexPassword ?>'>
        </div>
        <div class="input-container">
            <div class="bar">
                <label for="newPassword">Nytt passord</label>
                <a onclick="showPassword('newPassword')">Vis/Skjul</a>
            </div>
            <input type='password' id='newPassword' name='newPassword[]' placeholder="Nytt passord" required pattern='<?= $regexPassword ?>'>
            <input type='password' name='newPassword[]' placeholder="Bekreft passord" required pattern='<?= $regexPassword ?>'>
        </div>
        <input type="submit" value="Bytt passord">
    </form>
<?php } ?>

<div class="col">
    <div class="container">
        <table>
            <tr>
                <th>Produkt</th>
                <th class="num">Ledige</th>
                <th class="num">Reservert</th>
                <th class="num">Utlån</th>
            </tr>
            <?php
            foreach ($db["equipment"] as $item) {
            ?>
                <tr>
                    <td><?php echo $item["name"]; ?></td>
                    <td class="num"><?php echo $item["amount"] - ($item["reserved"] + $item["rented"]); ?></td>
                    <td class='num'><?php echo $item["reserved"]; ?></td>
                    <td class='num'><?php echo $item["rented"]; ?></td>
                </tr>
            <?php } ?>
        </table>
    </div>
</div>
<div class='col'>
    <div class="container pad">
        <form method="POST" action="" id="rent">
            <h2>Lån fra lageret</h2>
            <div class="input-container">
                <label for="item">Velg produkt</label>
                <select name="item" id="rentItem">
                    <?php
                    foreach ($db["equipment"] as $item) {
                        if ($item["amount"] - ($item["reserved"] + $item["rented"])) {
                            echo "<option value='" . $item['name'] . "'>" . $item['name'] . "</option>";
                        }
                    }
                    ?>
                </select>
            </div>
            <div class="input-container">
                <label for="reserveAmount">Antall</label>
                <input type="number" name="reserveAmount" id="reserveAmount" min="1" max="1" placeholder="Antall" required>
            </div>
            <input type="submit" value="Lån produkt">
        </form>
    </div>
    <div class="container pad">
        <form method="POST" action="">
            <h2>Reserver fra lageret</h2>
            <div class="input-container">
                <label for="reserveItem">Velg produkt</label>
                <select name="item" id="reserveItem" required>
                    <?php
                    foreach ($db["equipment"] as $item) {
                        if ($item["amount"] - ($item["reserved"] + $item["rented"])) {
                            echo "<option value='" . $item['name'] . "'>" . $item['name'] . "</option>";
                        }
                    }
                    ?>
                </select>
            </div>

            <!-- Since we use php there is no way to
                actually check what time a product is
                available without sending the entire
                db.json to the client potentially leaking
                user data. We could segregate our
                database into tables to work around this
                particular issue -->
            <input type="hidden" id="timezone" name="timezone" value="-01:00">
            <div class="input-container">
                <label for="reserveStartDate">Starttid</label>
                <input type="datetime-local" name="reserveStartDate" id="reserveStartDate" placeholder="Starttid (dd/MM/yy hh:mm)" min="<?php echo date('Y-m-d H:m:s'); ?>" required>
            </div>
            <div class="input-container">
                <label for="reserveEndDate">Sluttid</label>
                <input type="datetime-local" name="reserveStartDate" id="reserveStartDate" placeholder="Sluttid (dd/MM/yy hh:mm)" min="<?php echo date('Y-m-d H:m:s'); ?>" required>
            </div>

            <!--When item name is selected above, a
                js function will change the maximum
                value respective to the item chosen. -->
            <div class="input-container">
                <label for="reserveAmount">Antall</label>
                <input type="number" name="reserveAmount" id="reserveAmount" min="1" max="1" placeholder="Antall" required>
            </div>

            <input type="submit" value="Reserver produkt">
        </form>
    </div>
</div>
<div class="col">
    <div class="container pad">
        <h2>Lånte produkter</h2>
    </div>
    <div class="container pad">
        <h2>Reserverte Produkter</h2>
    </div>
</div>
<div class="col">
    <div class="container pad">
        <div class="top-bar">
            <span><?php
                    echo $db["users"][$_COOKIE['userid']]["elevated"] == 1 ? "Lærer" : "Elev";
                    echo ", " . $db["users"][$_COOKIE['userid']]["username"];
                    ?></span>
            <button onclick="window.location.href='php/logout.php'">Logg&nbsp;ut</button>
        </div>
    </div>
    <div class="container pad">
        <?php
        $regexPassword = $GLOBALS['regex']['password'];
        if (isset($_POST["oldPassword"]) && isset($_POST["newPassword"][0]) && $_POST["newPassword"][1])
            if ($_POST['newPassword'][0] == $_POST['newPassword'][1])
                if (
                    preg_match($regexPassword, $_POST['oldPassword'])
                    && preg_match($regexPassword, $_POST['newPassword'][0])
                )
                    if (password_verify($_POST['oldPassword'], $db["users"][$_COOKIE['userid']]['password'])) {
                        $newPassword = password_hash($_POST['newPassword'][0], PASSWORD_BCRYPT);
                        $passwords = [
                            "old" => $db["users"][$_COOKIE['userid']]['password'],
                            "new" => $newPassword,
                        ];
                        $db["users"][$_COOKIE['userid']]['password'] = $newPassword;
                        $logData = [
                            "time" => date('H:m:s'),
                            "user" => $db["users"][$_COOKIE['userid']]["username"],
                            "func" => "change_password",
                            "addr" => $_SERVER["REMOTE_ADDR"],
                            "data" => base64_encode(json_encode($passwords))
                        ];
                        logThis($logData);
                        file_put_contents("db.json", json_encode($db));
                        header("Location:./");
                    } else changePassword("Det nåværende passordet er feil :(");
                else changePassword("Et eller flere av feltene er formatert feil :(");
            else changePassword("Passordene er ikke like :(");
        else changePassword("");
        ?>
    </div>
</div>
</section>

<?php
if ($db["users"][$_COOKIE['userid']]["elevated"] == 1) {
    echo "<section id='admin'>";
    include("php/admin.php");
}
?>