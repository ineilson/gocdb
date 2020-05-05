<?php
require_once "tests.php";

$counts = get_test_counts($localInfoLocation);

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
