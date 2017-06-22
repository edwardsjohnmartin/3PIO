<?php
	class PagesController
	{
		public function home()
		{
			require_once('views/pages/home.php');
		}

		public function error()
		{
			header("HTTP/1.0 404 Not Found");
			require_once('views/pages/error.php');
		}

	}
?>
