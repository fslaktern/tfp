<!--
    TODO:
    - [ ] Låning og Reservering som fungerer.
    - [ ] Logging av alle hendelser på nettsiden.
    - [ ] La lærere se loggen.
    - [ ] Se reserverte/utlånte produkter.
        - Elever skal kun se sitt eget.
        - Lærere skal kunne se alle sitt + mulighet til å overskrive elevers reservasjoner.
    - [ ] Automatisk sletting av reservasjoner om det ikke er hentet innen dato/tid.
    - [x] Registrere nye brukere (kun for lærere).
    - [x] Knapp for å logge ut
    - [x] Mulighet til å bytte passord
 -->
<?php
// Prevent users from opening this page directly (or without logging in first)
if (!isset($_SERVER['REQUEST_URI']) || str_contains($_SERVER['REQUEST_URI'], 'main')) header('location:../');
ob_start();
function changePassword($errorMessage)
{
?>
    <form method="POST" action="">
        <h2>Bytt passord</h2>
        <?php
        $d = $errorMessage == "" ? "none" : "block";
        echo "<div class='error' style='display: $d;'>$errorMessage</div>";
        ?>
        <div class="input-container">
            <div class="above-input">
                <label for="oldPassword">Nåværende passord</label>
                <a onclick="showPassword('oldPassword')">Vis/Skjul</a>
            </div>
            <input type='password' id='oldPassword' name='oldPassword' placeholder="Nåværende passord" required pattern='^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,}$'>
        </div>
        <div class="input-container">
            <div class="above-input">
                <label for="newPassword">Nytt passord</label>
                <a onclick="showPassword('newPassword')">Vis/Skjul</a>
            </div>
            <input type='password' id='newPassword' name='newPassword' placeholder="Nytt passord" required pattern='^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,}$'>
            <input type='password' id='confirmPassword' name='confirmPassword' placeholder="Bekreft passord" required pattern='^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,}$'>
        </div>
        <input type="submit" value="Bytt passord">
    </form>
<?php
}
?>

<div class="col">
    <div class="container">
        <table>
            <tr>
                <th>Produkt</th>
                <th>Ledige</th>
                <th>Reservert</th>
                <th>Utlån</th>
            </tr>
            <?php
            foreach ($db["equipment"] as $item) {
            ?>
                <tr>
                    <td><?php echo $item["name"]; ?></td>
                    <td class="num"><?php echo $item["amount"] - ($item["reserved"] + $item["rented"]); ?></td>
                    <td class='num'><?php echo $item["reserved"]; ?> </td>
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
            <input type="submit" value="Lån">
        </form>
    </div>
</div>
<div class="col">
    <div class="container pad">
        <form method="POST" action="" id="reserve" class="mainForm">
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
                <input type="datetime-local" name="reserveStartDate" id="reserveStartDate" placeholder="Starttid (dd/MM hh:mm)" required>
            </div>
            <div class="input-container">
                <label for="reserveEndDate">Sluttid</label>
                <input type="datetime-local" name="reserveStartDate" id="reserveStartDate" placeholder="Sluttid (dd/MM hh:mm)" required>
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
        if (isset($_POST["oldPassword"]) && isset($_POST["newPassword"]) && $_POST["confirmPassword"])
            if (
                preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,}$/', $_POST['oldPassword'])
                && preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,}$/', $_POST['newPassword'])
                && preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,}$/', $_POST['confirmPassword'])
            )
                if (password_verify($_POST['oldPassword'], $db["users"][$_COOKIE['userid']]['password']))
                    if ($_POST['newPassword'] == $_POST['confirmPassword']) {
                        $db["users"][$_COOKIE['userid']]['password'] = password_hash($_POST['newPassword'], PASSWORD_BCRYPT);
                        file_put_contents("db.json", json_encode($db));
                        header("Location:./");
                    } else changePassword("Passordene er ikke like :(");
                else changePassword("Det nåværende passordet er feil :(");
            else changePassword("Et eller flere av feltene er formatert feil :(");
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