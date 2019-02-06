<?php
// -------------------------------------------------------------------------- //
// Supporting functions for overwriteSite.php and overWriteSiteCheck.php
//
// Author: Ian Neilson. Feb-2019
// -------------------------------------------------------------------------- //
require_once __DIR__ . '/../../lib/Gocdb_Services/Factory.php';
require_once __DIR__ . '/../../lib/Gocdb_Services/User.php';

$SITE_KEYS = array('HOME_URL','EMAIL','CONTACTTEL','GIIS_URL','LATITUDE',
'LONGITUDE','CSIRTEMAIL','IP_RANGE','IP_V6_RANGE',
'DOMAIN','LOCATION','CSIRTTEL','EMERGENCYTEL',
'HELPDESKEMAIL','TIMEZONE');

function getSiteValues (Site $site) {
  
  $newValues = array();
  
  $newValues['Scope_ids'] = $site->getScopes();
  $newValues['ReservedScope_ids'] = array();
  $newValues['childServiceScopeAction'] = 'noModify';
  
  $newValues['NGI'] = $site->getNGI()->getName();
  $newValues['Country'] = $site->getCountry()->getName();
  $newValues['Timezone'] = $site->getTimezone();
  $newValues['ProductionStatus'] = $site->getInfrastructure()->getName();
  
  $newValues['Site']['OFFICIAL_NAME'] = $site->getOfficialName();
  $newValues['Site']['SHORT_NAME'] = $site->getShortName();
  $newValues['Site']['DESCRIPTION'] = $site->getDescription();
  $newValues['Site']['HOME_URL'] = $site->getHomeUrl();
  $newValues['Site']['EMAIL'] = $site->getEmail();
  $newValues['Site']['CONTACTTEL'] = $site->getTelephone();
  $newValues['Site']['GIIS_URL'] = $site->getGiisUrl();
  $newValues['Site']['LATITUDE'] = $site->getLatitude();
  $newValues['Site']['LONGITUDE'] = $site->getLongitude();
  $newValues['Site']['CSIRTEMAIL'] = $site->getCsirtEmail();
  $newValues['Site']['IP_RANGE'] = $site->getIpRange(); // 0.0.0.0/255.000.255.255
  $newValues['Site']['IP_RANGE'] = '0.0.0.0/255.000.255.000';
  $newValues['Site']['IP_V6_RANGE'] = $site->getIpV6Range();
  $newValues['Site']['DOMAIN'] = $site->getDomain();
  $newValues['Site']['LOCATION'] = $site->getLocation();
  $newValues['Site']['CSIRTTEL'] = $site->getCsirtTel();
  $newValues['Site']['EMERGENCYTEL'] = $site->getEmergencyTel();
  $newValues['Site']['EMERGENCYEMAIL'] = $site->getEmergencyEmail();
  $newValues['Site']['EMERGENCYEMAIL'] = $site->getAlarmEmail();
  $newValues['Site']['HELPDESKEMAIL'] = $site->getHelpdeskEmail();
  $newValues['Site']['TIMEZONE'] = $site->getTimezoneId();
  
  return $newValues;
}
// ------------------------------------------------------------------------- //
function hashSiteValues (array $values) {
  
  $stringToHash = '';
  
  foreach ($GLOBALS['SITE_KEYS'] as $key) {    
    $stringToHash .= $values['Site'][$key];
  }
  
  return hash('md5',$stringToHash);
}
// ------------------------------------------------------------------------- //
function dumpSiteValues (array $values) {
  
  $stringToHash = '';
  
  echo "\n";
  
  foreach ($GLOBALS['SITE_KEYS'] as $key) {
    $stringToHash .= ($value = $values['Site'][$key]);    
    echo str_pad($key,15).$value."\n";
  }
  
  $hash = hash('md5',$stringToHash);
  echo str_pad('HASH',15).$hash."\n";
  
  return $hash;
}
// ------------------------------------------------------------------------- //
function getUser ($dn) {
  
  $user = \Factory::getUserService()->getUserByPrinciple($dn);
  
  if (!$user) {
    echo 'Error: Most likely you didn\'t specify a valid or known DN.'."\n"; 
    exit (1);
  }
  return $user;
}
// ------------------------------------------------------------------------- //
?>
