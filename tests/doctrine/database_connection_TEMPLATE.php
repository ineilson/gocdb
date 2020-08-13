<?php
	// Copy this template to /etc/gocdb/database_connection.php
	// edit as appropriate.

	// error_log will appear in Apache (ssl_)error_log
	/* --------------------------------------------------------------
	   Oracle

	   - specify dbname as 'XE' in dbMappings
	$conn = array(
		'driver' => 'oci8',
		'port' => 1521,
		'charset' => 'AL32UTF8'
		);
	*/
	/* --------------------------------------------------------------
	   Mysql

    $conn = array(
		'driver' => 'pdo_mysql',
		'host' => 'HOSTNAME_HERE',
		);
	*/
	// --------------------------------------------------------------

	// Edit dbMappings to include alternative databases.
	// Always provide a DEFAULT key and values.

	$dbMappings = array( 'DEFAULT' =>
						  array ('dbname' 	=> 'DATABASE_NAME_HERE',
								 'user'   	=> 'USER_NAME_HERE',
								 'password' => 'PASSWORD_HERE'),
	//					   'GIT_BRANCH_NAME_HERE' =>
	//					  array ('dbname' 	=> 'DATABASE_NAME_HERE',
	//							 'user'   	=> 'USER_NAME_HERE',
	//							 'password' => 'PASSWORD_HERE')
						  );

	$branch = 'DEFAULT';
	$mappings = $dbMappings['DEFAULT'];

	exec("cd /usr/share/GOCDB5;git rev-parse --abbrev-ref HEAD", $gitBranch, $x);

	if (!$x and array_key_exists($gitBranch[0], $dbMappings)) {
		$branch = $gitBranch[0];
		$mappings = $dbMappings[$branch];
		// error_log('On git branch - ' . $branch);
	}

	foreach ($mappings as $key => $values) {
		$conn[$key] = $values;
	}
	// Look for the environment variable 'phpunitdb' and, if it is there
	// append it's value to the databasename.
	// This aims to avoid potentially damaging a 'production' database by running
	// phpunit tests against it.
	// TODO: make this logic work for Oracle by changing the User instead.

	if (array_key_exists('phpunitdb', $_SERVER)) {
		$conn['dbname'] .= $_SERVER['phpunitdb'];
	}

	// error_log('Configured for database - ' . $conn['dbname']);
?>
