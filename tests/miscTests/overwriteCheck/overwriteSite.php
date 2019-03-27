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

if ($argc < 6) {
  echo 'Too few arguments. Usage: php overwriteSite.php targetId sourceId count stopfile dn'."\n";
  exit (1);
}

require_once __DIR__ . '/../../../lib/Gocdb_Services/Factory.php';
require_once __DIR__ . '/../../../lib/Gocdb_Services/Site.php';
require_once __DIR__ . '/../../../lib/Gocdb_Services/User.php';
require_once __DIR__ . '/overwriteSiteUtils.php';

$service = \Factory::getSiteService();
$targetSite = $service->getSite($argv[1]);
$sourceSite = $service->getSite($argv[2]);
$overwriteCount = $argv[3];
$stopfile = $argv[4];
$user = getUser($argv[5]);

if (file_exists($stopfile)) {
  echo $stopfile.' already exists. Terminating.'."\n";
  exit (1);
}
echo 'source->target '.$sourceSite->getShortName().'->'.$targetSite->getShortName()."\n";

$newValues = getSiteValues ($sourceSite);
// set the shortname (unique constraint)
$newValues['Site']['SHORT_NAME']  = $targetSite->getShortName();

while ($overwriteCount > 0 and !file_exists($stopfile)) {
  // set the description to the md5 hash of the new values
  $newValues['Site']['LOCATION'] = $sourceSite->getShortName().' '.$overwriteCount;
  $newValues['Site']['DESCRIPTION'] = hashSiteValues($newValues);
  $site = $service->editSite($targetSite, $newValues, $user);
  $overwriteCount -= 1;
  echo ".";
  usleep(rand(1000,100000));
}

dumpSiteValues($newValues);

echo "\n";

return;
// ------------------------------------------------------------------------- //
?>