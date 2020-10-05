<?php
/**
 * Common definition of constants and functions for various tests.
 */
require_once __DIR__."/validate_local_info_xml.php";
require_once __DIR__."/../../../lib/Gocdb_Services/Factory.php";
require_once __DIR__."/../../../lib/Gocdb_Services/Config.php";

define("TEST_1", "GOCDB5 DB connection");
define("TEST_2", "GOCDBPI_v5 availability");
define("TEST_3", "GOCDB5 central portal availability");
define("TEST_4", "GOCDB5 server configuration validity");

define("OK","ok");
define("NOK", "error");
define("UKN", "unknown");
define("OKMSG", "everything is well");
define("UKNMSG", "no information");

$localInfoLocation = __DIR__."/../../../config/local_info.xml";

// Initialise configuration for target URL

$config = \Factory::getConfigService();
$config->setLocalInfoOverride($_SERVER['SERVER_NAME']);
$config->setLocalInfoFileLocation($localInfoLocation);

$test_statuses =  array(
    TEST_1 	=> UKN,
    TEST_2 	=> UKN,
    TEST_3 	=> UKN,
    TEST_4  => UKN
);

$test_messages =  array(
    TEST_1 => UKNMSG,
    TEST_2 => UKNMSG,
    TEST_3 => UKNMSG,
    TEST_4 => UKNMSG
);

$test_desc =  array(
        TEST_1 =>
            "Connect to GOCDB5 (RAL/master instance) from this " .
            "machine using EntityManager->getConnection()->connect()",
        TEST_2 =>
            "Retrieve https://goc.egi.eu/gocdbpi/?" .
            "method=get_site_list&sitename=RAL-LCG2 using PHP CURL",
        TEST_3 =>
            "N/A",
        TEST_4 =>
            "Server XML configuration validation."
);

$test_doc =  array(
        TEST_1 =>
            "<a href='https://svn.esc.rl.ac.uk/repos/sct-docs/SCT " .
            "Documents/Servers and Services/GOCDB/Cookbook and " .
            "recipes/database_is_down.txt' target='_blank'>" .
            "documentation/recipe</a>",
        TEST_2 =>
            "<a href='https://svn.esc.rl.ac.uk/repos/sct-docs/SCT " .
            "Documents/Servers and Services/GOCDB/Cookbook and " .
            "recipes/failover_cookbook.txt' target='_blank'>" .
            "documentation/recipe</a>",
        TEST_3 =>
            "<a href='https://svn.esc.rl.ac.uk/repos/sct-docs/SCT " .
            "Documents/Servers and Services/GOCDB/Cookbook and " .
            "recipes/failover_cookbook.txt' target='_blank'>" .
            "documentation/recipe</a>",
        TEST_4 =>
            "<p>Contact GOCDB service managers." .
            "<br>Other tests have dependencies on the server configuration ".
            "<br>so may show errors if the configuration is invalid.</p>"
);


$disp = array(
        UKN => "<td align='center' bgcolor='#A0A0A0'>" .
                        "<font size='1'>UNKNOWN</font></td>",
        NOK => "<td align='center' bgcolor='#F00000'>" .
                        "<font size='1'>ERROR</font></td>",
        OK => "<td align='center' bgcolor='#00D000'>" .
                        "<font size='1'>OK</font></td>",
);

// Run the tests but return nothing but a count of passes and failures
function get_test_counts($config) {

    $res[1] = test_db_connection();
    $res[4] = test_config($config);

    if ($res[4]["status"] != "error") {
        // Only define test URLs if the config is valid
        define_test_urls($config);

        $res[2] = test_url(PI_URL);
        $res[3] = test_url(SERVER_BASE_URL);
    }

    $counts = array("ok" => 0,
                    "warn" => 0,
                    "error" => 0
            );

    foreach ($res as $r){
        $counts[$r["status"]]++;
    }

    return $counts;
}

// Define url constants for testing.
// Note: Should only be called if test_config is successful
function define_test_urls ($config) {

    list($serverBaseURL, $webPortalURL, $piURL) = $config->getURLs();

    // ??pi_url not used anywhere else??
    define("PI_URL",            $piURL."/public/?method=get_site_list");
    define("PORTAL_URL",        $webPortalURL);
    define("SERVER_BASE_URL",   $serverBaseURL  );

    //define("SERVER_SSLCERT", "/etc/grid-security/hostcert.pem");
    //define("SERVER_SSLKEY", "/etc/pki/tls/private/hostkey.pem");
}

// Test the connection to the database using Doctrine
function test_db_connection(){

    try {
        $entityManager = Factory::getNewEntityManager();
        $entityManager->getConnection()->connect();
        $retval["status"] = OK;
        $retval["message"] = OKMSG;
    } catch (\Exception $e) {
        $message = $e->getMessage();
        $retval["status"] = NOK;
        $retval["message"] = "$message";
    }

    return $retval;
}

function test_url($url) {

    try{
        get_https2($url);
        $retval["status"] = OK;
        $retval["message"] = OKMSG;
    } catch (Exception $e){
        $message = $e->getMessage();
        $retval["status"] = NOK;
        $retval["message"] = "$message";
    }
    return $retval;
}

function get_https2($url){

    $curloptions = array (
            CURLOPT_RETURNTRANSFER => false,
            CURLOPT_HEADER         => false,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_MAXREDIRS      => 1,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_USERAGENT      => 'GOCDB monitor',
            CURLOPT_VERBOSE        => false,
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CAPATH => '/etc/grid-security/certificates/'
    );
    if( defined('SERVER_SSLCERT') && defined('SERVER_SSLKEY') ){
      $curloptions[CURLOPT_SSLCERT] = SERVER_SSLCERT;
      $curloptions[CURLOPT_SSLKEY] = SERVER_SSLKEY;
    }

    $handle = curl_init();
    curl_setopt_array($handle, $curloptions);

    $return = curl_exec($handle);
    if (curl_errno($handle)) {
        throw new Exception("curl error:".curl_error($handle));
    }
    curl_close($handle);

    if ($return == false) {
        throw new Exception("no result returned. curl says: ".curl_getinfo($handle));
    }

    return $return;
}

function test_config($config) {

    try{
        validate_local_info_xml($config->getLocalInfoFileLocation());
        $retval["status"] = OK;
        $retval["message"] = OKMSG;
    } catch (Exception $Exception){
        $retval["status"] = NOK;
        $retval["message"] = $Exception->getMessage();
    }
    return $retval;
}

?>
