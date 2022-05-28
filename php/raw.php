<?php

if (isset($_GET["d"])) {
    header('Content-type: application/json');
    $dataB64 = urldecode($_GET["d"]);
    $dataJSON = base64_decode($dataB64);

    // copilot: Sanitize $dataJSON to prevent XSS
    $dataJSON = preg_replace('/[^a-zA-Z0-9_,:{}\[\]\.\s\n\r\t-#&;]/', '', $dataJSON);
    echo $dataJSON;
} else header("Location:../");
