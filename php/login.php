<?php
// Redirect to homepage if user tries to open this page directly
if (str_contains($_SERVER['REQUEST_URI'], 'login')) header('location:../');
function getName($errorMessage)
{
?>
    <div class="col col-left">Venstre</div>
    <div class='col col-center'>
        <form action='' method='POST'>
            <h1>Hvem er du?</h1>
            <div id="error" class='error' onchange="changeVisibility(this)"><?php echo $errorMessage; ?></div>
            <div class='input-container'>
                <label for='username'>Brukernavn&nbsp;</label>
                <input type='text' id='username' name='username' placeholder="Brukernavn" required pattern='^[a-zæøå0-9]{1,32}$' oninput="checkInput(this.value, /^[a-zæøå0-9]{4,8}$/, 'brukernavn')">
            </div>
            <div class='input-container'>
                <label for='pass'>Passord&nbsp;</label>
                <input type='password' id='password' name='password' placeholder="Passord" required pattern='^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,}$'>
            </div>
            <input type='submit' value='Logg inn'>
        </form>
    </div>
    <div class="col col-right">Høyre</div>
<?php
}

// Cookie varer i 1 år, men gjelder kun på denne siden
$cookieOptions = ['expires' => time() + 3600 * 24 * 365, 'path' => $_SERVER['REQUEST_URI'], 'samesite' => 'Lax'];
if (
    isset($_POST['username']) &&
    isset($_POST['password'])
) {
    // Check if the inputted username matches the expected pattern
    if (
        preg_match('/^[a-zæøå0-9]{4,8}$/', trim($_POST['username'])) &&
        preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,}$/', $_POST['password'])
    ) {
        // Check if username exists
        $exists = FALSE;
        $i = 0;
        while ($i < count($db['users'])) {
            if (
                $db['users'][$i]['username'] == trim($_POST['username']) &&
                password_verify($_POST['password'], $db['users'][$i]['password'])
            ) {
                $exists = TRUE;
                break;
            }
            $i++;
        }
        if ($exists) {
            setcookie('userid', password_hash($_POST['username'] . $_POST['password'], PASSWORD_BCRYPT), $cookieOptions);



            // Redirect to proper page
            header('location: ../');
        } else getName('Brukernavn eller passord er feil :(');
    } else getName('Et eller fler av feltene inneholder ulovlige symboler :(');
} else getName('');
