<?php

require_once __DIR__."/validate_local_info_xml.php";

$localInfoLocation = __DIR__."/../../../config/local_info.xml";
$localInfoXML = simplexml_load_file($localInfoLocation);
$webPortalURL = $localInfoXML->local_info->web_portal_url;
$piURL = $localInfoXML->local_info->pi_url; // e.g. https://localhost/gocdbpi
$baseURL = $localInfoXML->local_info->server_base_url;


define("PI_URL", $piURL."/public/?method=get_site_list"); //https://localhost/gocdbpi/public/?method=get_site_list
define("PORTAL_URL", $webPortalURL);
define("SERVER_BASE_URL", $baseURL);
//define("SERVER_SSLCERT", "/etc/grid-security/hostcert.pem");
//define("SERVER_SSLKEY", "/etc/pki/tls/private/hostkey.pem");

define("TEST_1", "GOCDB5 DB connection");
define("TEST_2", "GOCDBPI_v5 availability");
define("TEST_3", "GOCDB5 central portal availability");
define("TEST_4", "GOCDB5 server configuration validity");

$test_statuses =  array(
        TEST_1 	=> "unknown",
        TEST_2 	=> "unknown",
        TEST_3 	=> "unknown",
        TEST_4  => "unknown"
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

$test_messages =  array(
        TEST_1 => "no information",
        TEST_2 => "no information",
        TEST_3 => "no information",
        TEST_4 => "no information"
);

$disp = array(
        "unknown" => "<td align='center' bgcolor='#A0A0A0'>" .
                        "<font size='1'><i>unknown</i></font></td>",
        "warn" => "<td align='center' bgcolor='#FFAA00'>" .
                        "<font size='1'>WARNING</font></td>",
        "error" => "<td align='center' bgcolor='#F00000'>" .
                        "<font size='1'>ERROR</font></td>",
        "ok" => "<td align='center' bgcolor='#00D000'>" .
                        "<font size='1'>OK</font></td>",
);

// Test the connection to the database using Doctrine
function test_db_connection(){
    require_once __DIR__ . '/../../../lib/Gocdb_Services/Factory.php';

    try {
        $entityManager = Factory::getNewEntityManager();
        $entityManager->getConnection()->connect();
        $retval["status"] = "ok";
        $retval["message"] = "everything is well";
    } catch (\Exception $e) {
        $message = $e->getMessage();
        $retval["status"] = "error";
        $retval["message"] = "$message";
    }

    return $retval;
}

function test_url($url) {
    try{
        get_https2($url);
        $retval["status"] = "ok";
        $retval["message"] = "everything is well";
    } catch (Exception $exception){
        $message = $exception->getMessage();
        $retval["status"] = "error";
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

function test_config($localInfoLocation) {
    try{
        validate_local_info_xml($localInfoLocation);
        $retval["status"] = "ok";
        $retval["message"] = "everything is well";
    } catch (Exception $Exception){
        $retval["status"] = "error";
        $retval["message"] = $Exception->getMessage();
    }
    return $retval;
}

?>