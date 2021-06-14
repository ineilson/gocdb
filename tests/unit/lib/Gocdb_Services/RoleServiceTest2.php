<?php
/*
 * Copyright (C) 2015 STFC
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * http://www.apache.org/licenses/LICENSE-2.0
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
require_once __DIR__ . '/../../../doctrine/TestUtil.php';
//require_once __DIR__ . '/../../../../lib/Doctrine/entities/User.php';
//require_once __DIR__ . '/../../../../lib/Gocdb_Services/User.php';
//require_once __DIR__ . '/../../../../lib/Gocdb_Services/Config.php';
require_once __DIR__ . '/../../../../lib/Gocdb_Services/Factory.php';

use Doctrine\ORM\EntityManager;
use org\gocdb\services\RoleActionAuthorisationService;

//use org\gocdb\services\User;
//use User;

/**
 * DBUnit test class for the {@see \org\gocdb\services\Role} service.
 *
 * @author Ian Neilson (after David Meredith)
 */
class RoleServiceTest2 extends PHPUnit_Extensions_Database_TestCase {
  private $entityManager;
  private $dbOpsFactory;
  private $user;
  private $site;

  function __construct() {
    parent::__construct();
    // Use a local instance to avoid Mess Detector's whinging about avoiding
    // static access.
    $this->dbOpsFactory = new PHPUnit_Extensions_Database_Operation_Factory();
  }
  /**
  * Overridden.
  */
  public static function setUpBeforeClass() {
    parent::setUpBeforeClass();
    echo "\n\n-------------------------------------------------\n";
    echo "Executing RoleServiceTest. . .\n";
  }

  /**
  * Overridden. Returns the test database connection.
  * @return PHPUnit_Extensions_Database_DB_IDatabaseConnection
  */
  protected function getConnection() {
    require_once __DIR__ . '/../../../doctrine/bootstrap_pdo.php';
    return getConnectionToTestDB();
  }

  /**
  * Overridden. Returns the test dataset.
  * Defines how the initial state of the database should look before each test is executed.
  * @return PHPUnit_Extensions_Database_DataSet_IDataSet
  */
  protected function getDataSet() {
    return $this->createFlatXMLDataSet(__DIR__ . '/../../../doctrine/truncateDataTables.xml');
    // Use below to return an empty data set if we don't want to truncate and seed
    //return new PHPUnit_Extensions_Database_DataSet_DefaultDataSet();
  }

  /**
  * Overridden.
  */
  protected function getSetUpOperation() {
    // CLEAN_INSERT is default
    //return PHPUnit_Extensions_Database_Operation_Factory::CLEAN_INSERT();
    //return PHPUnit_Extensions_Database_Operation_Factory::UPDATE();
    //return PHPUnit_Extensions_Database_Operation_Factory::NONE();
    //
    // Issue a DELETE from <table> which is more portable than a
    // TRUNCATE table <table> (some DBs require high privileges for truncate statements
    // and also do not allow truncates across tables with FK contstraints e.g. Oracle)
    return $this->dbOpsFactory->DELETE_ALL();
  }

  /**
  * Overridden.
  */
  protected function getTearDownOperation() {
    // NONE is default
    return $this->dbOpsFactory->NONE();
  }

  /**
  * Sets up the fixture, e.g create a new entityManager for each test run
  * This method is called before each test method is executed.
  */
  protected function setUp() {
    parent::setUp();
    $this->entityManager = $this->createEntityManager();
    $this->util = new TestUtil();
    $this->user = $this->util->createSampleUser("Alpha", "Beta", "/CN=Alpha Beta");
    $this->user->setAdmin(true);
    $this->entityManager->persist($this->user);

    $this->site = $this->util->createSampleSite("Site1");
    $this->entityManager->persist($this->site);

    $this->roleServ = new \org\gocdb\services\Role();
    $this->roleServ->setEntityManager($this->entityManager);
  }

  /**
  * @return EntityManager
  */
  private function createEntityManager(){
    $entityManager = null; // Initialise in local scope to avoid unused variable warnings
    require __DIR__ . '/../../../doctrine/bootstrap_doctrine.php';
    return $entityManager;
  }

  /**
  * Called after setUp() and before each test. Used for common assertions
  * across all tests.
  */
  protected function assertPreConditions() {
    $con = $this->getConnection();
    $fixture = __DIR__ . '/../../../doctrine/truncateDataTables.xml';
    $tables = simplexml_load_file($fixture);

    foreach($tables as $tableName) {
      $sql = "SELECT * FROM ".$tableName->getName();
      $result = $con->createQueryTable('results_table', $sql);
      if($result->getRowCount() != 0){
        throw new RuntimeException("Invalid fixture. Table has rows: ".$tableName->getName());
      }
    }
  }
  /**
   * Create some roles
   */
  private function createTestRoles (array $roleNames) {

    foreach ($roleNames as $type) {
      $roleType = $this->util->createSampleRoleType($type);
      $role = $this->util->createSampleRole($this->user, $roleType, $this->site, \RoleStatus::GRANTED);
      $this->entityManager->persist($roleType);
      $this->entityManager->persist($role);
      $roles[] = $role;
    };

    $this->entityManager->flush();

    return $roles;
  }
  /**
   * Attach an APIAuthentication credential to user and site
   */
  private function addAPIAuthEntity() {
    // The only way to add an APIAuthentication credential is by creating
    // a site service ...

    $siteServ = new \org\gocdb\services\Site();
    $siteServ->setEntityManager($this->entityManager);

    // No role mappings are created or tested - we rely in user being an admin
    $ram = new \org\gocdb\services\RoleActionMappingService();
    $ras = new \org\gocdb\services\RoleActionAuthorisationService($ram);

    $ras->setEntityManager($this->entityManager);

    $siteServ->setRoleActionAuthorisationService($ras);

    $siteServ->addAPIAuthEntity(
      $this->site,
      $this->user,
      array(
        "IDENTIFIER" => $this->user->getCertificateDn(),
        "TYPE" => "X509",
        "ALLOW_WRITE" => false
      )
    );
    return;
  }
  /**
   * Tests begin here
   */
  public function testNullCheckOrphanAPIAuth () {
    print __METHOD__ . "\n";

    $roles = $this->createTestRoles(array("Manager","Administrator"));

    // No APIAuthentication credentials are yet assigned so the
    // check should pass.
    $this->assertNull($this->roleServ->checkOrphanAPIAuth($roles[0]));

    $this->addAPIAuthEntity();

    // User has two roles so the check should pass.
    $this->assertNull($this->roleServ->checkOrphanAPIAuth($roles[0]));

  }
  /**
   * @depends testNullCheckOrphanAPIAuth
   * @expectedException Exception
   */
  public function testFailCheckOrphanAPIAuth () {
    print __METHOD__ . "\n";

    $roles = $this->createTestRoles(array("Manager"));

    $this->addAPIAuthEntity();

    // Should fail because there is only one role.
    $this->roleServ->checkOrphanAPIAuth($roles[0]);
  }
}
