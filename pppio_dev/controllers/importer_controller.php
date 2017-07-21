<?php

	class ImporterController
	{
		public function index()
		{
			$input = "Lesson: Example\nEx:\n{Create a variable named 'a' and set it equal to 7.\nCreate a function named 'func' and have it accept a numeric value. Then have it return a * (the integer).\n(Note: make sure to use the '*' symbol when multiplying these values.)\nCreate another function named 'pointless' and have it accept no values. It shouldn't return anything.\nCreate another function named 'buncha_params' and have it accept 2 strings and 3 ints. It shouldn't return anything.\nUsing the '#' symbol, write a comment (it can say anything).\nPrint 'Hello World!'.}\n{b = 5}\n{test_val('a', 7)\ntest_func('func', 42, 6)\ntest_func('pointless', None)\ntest_func('buncha_params', None, 'string 1', 'string 2', 1, 2, 3)\ntest_in('*')\ntest_in('#')\ntest_out('Hello World!')}";

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

