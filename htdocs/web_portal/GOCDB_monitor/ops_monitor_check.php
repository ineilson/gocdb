<?php
require_once "tests.php";

// function returns associative array
$res[1] = test_db_connection();
$res[2] = test_url(PI_URL);
//$res[3] = test_url(PORTAL_URL);
$res[3] = test_url(SERVER_BASE_URL);
$res[4] = test_config($localInfoLocation);

$counts=array(
        "ok" => 0,
        "warn" => 0,
        "error" => 0
);

foreach ($res as $r){
    $counts[$r["status"]]++;
}
$ok  = "All GOCDB tests are looking good\n";
$nok = "GOCDB Web Portal is unable to connect to the GOCDB back end database\n";

/* If someone wants to test the failure, they can fake one using
 * the fake_failure parameter */
if(isset($_REQUEST['fake_failure'])) {
    header("HTTP/1.0 500");
    echo($nok);
    die();
}

if ($counts["error"] != 0) {
    header("HTTP/1.0 500");
    echo($nok);
    die();
}
else {
    echo($ok);
    die();
}

?>