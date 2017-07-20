<?php

	class ImporterController
	{
		public function index()
		{
			$input = '';
			if ($_SERVER['REQUEST_METHOD'] === 'POST') {
				if (isset($_POST['input'])) {
					$lessons = Importer::get_lessons($_POST['input']);
					//$lessons = 'Hello';
					$input = $_POST['input'];
				}
				
			}
			//require_once('views/pages/home.php');
			$view_to_show = 'views/importer/index.php';
			require_once('views/shared/layout.php');
			
			
		}
	}
?>

