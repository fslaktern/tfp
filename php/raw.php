<?php
header('Content-type: application/json');
if (isset($_GET["d"])) {
    $dataB64 = urldecode($_GET["d"]);
    $dataJSON = base64_decode($dataB64);
    echo $dataJSON;
} else header("Location:../");
