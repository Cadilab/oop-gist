<?php

	// * Requiring configuration file so we can connect to database in login class

	require_once('config/config.php');


	// * Requiring login class in which we determine wether user is logged in or not and than include files based on that!

	require_once('classes/AccountController.php');

	// * Now we are checking wether user is logged in or not, based on returned information we're including different files

	$login = new Login();

	if($login->loggedIn() == true)
	{
		$login->doLogout();
	}
	else
	{
        header("location: index.php");
        exit();			
	}

?>