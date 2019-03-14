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

require __DIR__."/../../lib/Doctrine/bootstrap_doctrine.php";
require __DIR__."/../../lib/Doctrine/bootstrap.php";

// require_once __DIR__ . '/overwriteSiteUtils.php';

// $entityManager defined above;

$overwriteCount = $argv[3];

$stopfile = $argv[4];
if (file_exists($stopfile)) {
  echo $stopfile.' already exists. Terminating.'."\n";
  exit (1);
}
//$user = getUser($argv[5]);

$targetSite = $entityManager->find("Site", $argv[1]);
if (!$targetSite) {
  echo 'Target site '.$targetSite.' not found.'."\n";
  return;
}
$sourceSite = $entityManager->find("Site", $argv[2]);
if (!$sourceSite) {
  echo 'Source site '.$sourceSite.' not found.'."\n";
  return;
}

$sourceShortName = $sourceSite->getShortName();
$targetShortName = $targetSite->getShortName();

echo 'Source before write - '.$sourceSite->getId().';';
echo $sourceShortName."\n";
echo 'Target before write - '.$targetSite->getId().';';
echo $targetSite->getLocation()."\n";


while ($overwriteCount > 0 and !file_exists($stopfile)) {
  $entityManager->getConnection()->beginTransaction();
  try {
    $location = $sourceSite->getShortName().' '.$overwriteCount;
    $hash = hash('md5',$targetShortName.$location);
    $targetSite->setLocation($location);
    $targetSite->setDescription($hash);
  } catch(\Exception $ex){
    $entityManager->getConnection()->rollback();
    $entityManager->close();
    throw $ex;
  }
  
  $entityManager->merge($targetSite);
  $entityManager->flush();
  $entityManager->getConnection()->commit();
  
  // set the description to the md5 hash of the new values
  //$newValues['Site']['LOCATION'] = $sourceSite->getShortName().' '.$overwriteCount;
  //$newValues['Site']['DESCRIPTION'] = hashSiteValues($newValues);
  //$site = $service->editSite($targetSite, $newValues, $user);
  $overwriteCount -= 1;
  echo ".";
  usleep(rand(1000,100000));
}

echo "\n";

echo 'Target after  write - '.$targetSite->getId().';';
echo $targetSite->getLocation()."\n";

return;
// ------------------------------------------------------------------------- //
?>
