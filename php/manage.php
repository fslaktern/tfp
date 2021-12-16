<!-- 
    TODO:
    - Låne-/reserverknapp som funker.
    - Registrere nye brukere (kun for lærere).
    - Se reserverte/utlånte produkter.
        - Elever skal kun se sitt eget.
        - Lærere skal kunne se alle sitt, evt. overskrive elevers reservasjoner.
    - Automatisk sletting av reservasjoner om det ikke er hentet innen dato/tid.
    - Knapp for å logge ut
 -->
<?php
// Prevent users from opening this page directly (or without login in first)
if (!isset($_SERVER['REQUEST_URI']) || str_contains($_SERVER['REQUEST_URI'], 'manage')) header('location:../');
?>

<div class="col col-left">
    <table>
        <tr>
            <th>Produkt</th>
            <th>Ledige</th>
            <th>Lån</th>
            <th>Reserver</th>
        </tr>
        <?php
        for ($i = 0; $i < count($db["equipment"]); $i++) {
            $product = $db["equipment"][$i];
        ?>
            <tr>
                <td><?php echo $product["name"]; ?></td>
                <td class="center"><?php echo $product["amount"] - ($product["reserved"] + $product["rented"]); ?></td>
            <?php
            if ($product["amount"] - ($product["reserved"] + $product["rented"]) > 0) echo "
                <td><button>Lån</button></td>
                <td><button>Reserver</button></td>
            </tr>";
            else echo "
                <td></td>
                <td><button>Reserver</button></td>
            </tr>";
        } ?>
    </table>
</div>
<div class='col col-center'>
    <a href='/php/logout.php'">Logg ut</a>
    <?php
    echo $db["users"][$_COOKIE['userid']]["username"] . ", " . $db["users"][$_COOKIE['userid']]["password"] . ", " . $db["users"][$_COOKIE['userid']]["elevated"];
    ?>
</div>
<div class=" col col-right">
</div>
</section>

<?php
if ($db["users"][$_COOKIE['userid']]["elevated"] == 1) {
    echo "<section id='register'>";
    include("php/register.php");
}
?>