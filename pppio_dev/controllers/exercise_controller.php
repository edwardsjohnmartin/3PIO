<?php
	require_once('controllers/base_controller.php');
	class ExerciseController extends BaseController
	{
		//now has the basic actions

		public function try_it()
		{
			//this should eventually require the lesson, class, etc. ids.
			if (!isset($_GET['id']) || !isset($_GET['lesson_id']))
			{
				return call('pages', 'error'); //or even call a blank editor for playing around in
			}
			$exercise = exercise::get($_GET['id']); //what if it's null? don't want that.. need to be careful of that in base, too

			require_once('models/lesson.php');
			$lesson = lesson::get($_GET['lesson_id']);

			require_once('views/shared/editor.php');
		}
	}
?>
