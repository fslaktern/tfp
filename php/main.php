<!-- 
    TODO:
    - [ ] Låning og Reservering som fungerer.
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
                    <td class="center"><?php echo $item["amount"] - ($item["reserved"] + $item["rented"]); ?></td>
                    <td class='center'><?php echo $item["reserved"]; ?> </td>
                    <td class='center'><?php echo $item["rented"]; ?></td>
                </tr>
            <?php } ?>
        </table>
    </div>
</div>
<div class='col'>
    <div class="container pad">
        <form method="POST" action="" id="rent">
            <!-- style="display: none;" -->
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
        </form>
    </div>
</div>
<div class="col">
    <div class="container pad">
        <form method="POST" action="" id="reserve" class="mainForm">
            <!-- style="display: none;" -->
            <h2>Reserver fra lageret</h2>
            <div class="input-container">
                <label for="reserveItem">Velg produkt</label>
                <select name="item" id="reserveItem">
                    <?php
                    foreach ($db["equipment"] as $item) {
                        if ($item["amount"] - ($item["reserved"] + $item["rented"])) {
                            echo "<option value='" . $item['name'] . "'>" . $item['name'] . "</option>";
                        }
                    }
                    ?>
                </select>
            </div>

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
        <form method="POST" action="">
            <h2>Bytt passord</h2>
            <div class="input-container">
                <div class="above-input">
                    <label for="oldPassword">Nåværende passord</label>
                    <a onclick="showPassword('oldPassword',this)">Show/Hide</a>
                </div>
                <input type='password' id='oldPassword' name='oldPassword' placeholder="Nåværende passord" required pattern='^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,}$'>
            </div>
            <div class="input-container">
                <div class="above-input">
                    <label for="newPassword">Nytt passord</label>
                    <a onclick="showPassword('newPassword',this)">Show/Hide</a>
                </div>
                <input type='password' id='newPassword' name='newPassword' placeholder="Nytt passord" required pattern='^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,}$'>
                <input type='password' id='confirmPassword' name='confirmPassword' placeholder="Gjenta passord" required pattern='^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,}$'>
            </div>
        </form>
    </div>
</div>
</section>

<?php
if ($db["users"][$_COOKIE['userid']]["elevated"] == 1) {
    echo "<section id='admin'>";
    include("php/admin.php");
}
?>