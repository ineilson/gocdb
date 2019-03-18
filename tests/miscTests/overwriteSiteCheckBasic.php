<?php
// -------------------------------------------------------------------------- //
// Repeatedly overwrite a specified (target) GOCDB site details with those taken
// another site (source) with two exceptions: 
// - the target site name is preserved;
// - the target site DESCRIPTION field is set to a hash derived from the
//   source site's other fields.
// - to force the above hash to change the LOCATION field is set to a counter 
//   which changes for each repeat write.
// A small element of random timing is introduced to avoid tight looping.
// At the start of each loop a check is made to see is a named file, specified
// by the 'stopfile' argument, exists. If it does, looping is halted.
// Proper error handling is very limited!
//
// Usage: php overwriteSite.php targetId sourceId count dn
// Example: > php overwriteSite.php 4 5 2 '/C=UK/O=eScience/OU=CLRC/L=RAL/CN=ian neilson'
//
// Author: Ian Neilson. Feb-2019
// -------------------------------------------------------------------------- //

if ($argc < 4) {
  echo 'Too few arguments. Usage: php overwriteSiteCheckBasic.php targetId count stopfile'."\n";
  exit (1);
}

require __DIR__."/../../lib/Doctrine/bootstrap_doctrine.php";
require __DIR__."/../../lib/Doctrine/bootstrap.php";
require "./overwriteSiteUtilsBasic.php";

// require_once __DIR__ . '/overwriteSiteUtils.php';

// $entityManager defined above;
$targetSite = $argv[1];

$count = $argv[2];

$stopfile = $argv[3];

$site = $entityManager->find("Site", $targetSite);

if (!$site) {
  echo 'Site '.$targetSite.' not found.'."\n";
  return;
}

$siteValues = NULL;

while ($count > 0 and !file_exists($stopfile)) {
  $siteValues = getSiteValues($site);
  $hash = hashSiteValues($siteValues);
  if ($site->getDescription() != $hash) {
    fopen($stopfile, 'w') or die('Cannot open file:  '.$stopfile);
    echo '##### Inconsistency found #####';
    break;
  }
  echo '.';
  $count -= 1;
  usleep(rand(1000,10000));
}

echo "\n";
if (is_array($siteValues)) {
  dumpSiteValues($siteValues);
}

return;
// ------------------------------------------------------------------------- //
?>
