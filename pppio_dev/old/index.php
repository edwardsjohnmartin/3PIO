<?php
	//start the session here?
	//session_start();
	session_start();
	require_once('connection.php');
	require_once('type.php');

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

	require_once('views/layout.php');
	//end the session here?
	session_write_close();
?>
