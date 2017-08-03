<?php
	class PagesController
	{
		public function index()
		{
			//require_once('views/pages/home.php');
			$view_to_show = 'views/pages/home.php';
			require_once('views/shared/layout.php');
		}

		public function error()
		{
			//header("HTTP/1.0 404 Not Found");
//			require_once('views/pages/error.php');

			$view_to_show = 'views/pages/error.php';
			require_once('views/shared/layout.php');
		}

	}
?>
