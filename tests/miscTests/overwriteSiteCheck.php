<?php 
// -------------------------------------------------------------------------- //
// Repeatedly check a specified (target) GOCDB site details whose details
// have been overwritten by use of overwriteSite.php (see there for more details) 
// If an inconsistency is found, a file, specified by the 'stopfile' argument
// is created.
// Proper error handling is very limited!
//
// Usage: php overwriteSiteCheck.php siteId count dn
// Example: > php overwriteSiteCheck.php 4 5000 '/C=UK/O=eScience/OU=CLRC/L=RAL/CN=ian neilson'
//
// Author: Ian Neilson. Feb-2019
// -------------------------------------------------------------------------- //
require_once __DIR__ . '/../../lib/Gocdb_Services/Factory.php';
require_once __DIR__ . '/../../lib/Gocdb_Services/Site.php';
require_once __DIR__ . '/overwriteSiteUtils.php';

if ($argc < 5) {
  echo 'Too few arguments. Usage: php overwriteSiteCheck.php siteId count stopfile dn'."\n"; 
  return;
}

$user = getUser($argv[4]);

$siteId = $argv[1];

$service = \Factory::getSiteService();

echo 'Checking  '.$service->getSite($siteId)->getShortName() ."\n";

$count = $argv[2];
$stopfile = $argv[3];

$lastLocation = '';

while ($count > 0) {
  
  unset($targetSite);
  
  $targetSite = $service->getSite($siteId);
  
  $newValues = getSiteValues ($targetSite);
  $hashValue = hashSiteValues($newValues);
  
  $description = $newValues['Site']['DESCRIPTION'];
  $location    = $newValues['Site']['LOCATION'];
  
  if ($description != $hashValue) {
    fopen($stopfile, 'w') or die('Cannot open file:  '.$stopfile);
    echo '##### Inconsistency found #####';
    break;
  } 
  
  if ($location != $lastLocation) {
    echo $location.' ';
  }

  $lastLocation = $location;
  
  echo '.';
  
  $count -= 1;
  usleep(rand(1000,10000));
}

dumpSiteValues($newValues, TRUE);

echo "\n";

return;
?>