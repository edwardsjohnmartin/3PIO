<?php
	require_once('connection.php');
	require_once('type.php');

	//these have to be before the session start
	require_once('models/user.php');
	require_once('models/key_value_pair.php');

	//start the session here?
	session_start();

	if (isset($_GET['controller']) && isset($_GET['action'])) //would like to default action to 'index' or something
	{
		$controller = strtolower($_GET['controller']);
		$action = strtolower($_GET['action']);
	}
	else
	{
		$controller = 'pages';
		$action = 'home';
	}

	require_once('routes.php'); //changed to go index->routes->controller->layout->view
	//end the session here?
	session_write_close();
?>
