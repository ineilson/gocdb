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
  echo 'Too few arguments. Usage: php overwriteSite.php targetId sourceId count stopfile dn'."\n";
  exit (1);
}

//require_once __DIR__."/../../../lib/Doctrine/bootstrap.php";
require_once __DIR__."/bootstrap.php";
// require "./overwriteSiteUtilsBasic.php";

$targetSite = $entityManager->find("Site", $argv[1]);
if (!$targetSite) {
  echo 'Target site '.$targetSite.' not found.'."\n";
  return;
}

$overwriteCount = $argv[2];

$stopfile = $argv[3];
if (file_exists($stopfile)) {
  echo $stopfile.' already exists. Terminating.'."\n";
  exit (1);
}

$targetShortName = $targetSite->getShortName();
$domain = rand(1,1000);
$targetSite->setDomain($domain); // constant for each loop

echo 'target '.$targetShortName."\n";

while ($overwriteCount > 0 and !file_exists($stopfile)) {
  $entityManager->getConnection()->beginTransaction();
  try {
    $location = $overwriteCount;
    $targetSite->setLocation($location); //varies with each loop
    $targetSite->setDomain($domain); // constant for each loop
//    $hashValues = $domain.$location.$targetShortName;
    $hashValues = $domain.$location;
    $targetSite->setDescription(hash('md5',$hashValues));
  } catch(\Exception $ex){
    $entityManager->getConnection()->rollback();
    $entityManager->close();
    throw $ex;
  }
  
  $entityManager->merge($targetSite);
  $entityManager->flush();
  $entityManager->getConnection()->commit();
  
  $overwriteCount -= 1;
  echo ".";
  usleep(rand(1000,100000));
}

echo "\n";

echo 'Domain    '.$targetSite->getDomain()."\n";
echo 'Location  '.$targetSite->getLocation()."\n";
echo 'Shortname '.$targetSite->getShortName()."\n";
echo $targetSite->getLocation()."\n";

return;
// ------------------------------------------------------------------------- //
?>