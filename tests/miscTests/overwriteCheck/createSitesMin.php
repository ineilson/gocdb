<?php 
  
  require_once __DIR__.'/bootstrap.php';
  
  $i = 0;
  
  while ($i < 3) {
    $site = new Site();
    $sitename = 'Site'.$i;
    $site->setShortname($sitename);
    $site->setLocation($sitename.' Location');
    $site->setDescription($sitename.' Description');
    $site->setDomain($sitename.' Domain');
    $i++;
    $entityManager->persist($site);
    $entityManager->flush();
  };
  
 ?>