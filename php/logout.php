<?php

// Remove all cookie associated with this site
if (isset($_SERVER['HTTP_COOKIE'])) {
    $cookies = explode(';', $_SERVER['HTTP_COOKIE']);
    foreach ($cookies as $cookie) {
        $parts = explode('=', $cookie);
        $name = trim($parts[0]);
        // setcookie($name, '', time() - 1000);
        // setcookie($name, '', time() - 1000, '/');
        setcookie($name, '', time() - 1000, str_replace("php/logout.php", "", $_SERVER['REQUEST_URI']));
    }
}
// Go back, and let index.php show login.php
header("location:../");
