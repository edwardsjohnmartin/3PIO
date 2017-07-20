<?php
	require_once('controllers/base_controller.php');
	class ExerciseController extends BaseController
	{
		//now has the basic actions

		public function try_it() //check if can access!!
		{
			//requires exercise id, lesson id, concept id in query sting
			if (!isset($_GET['id']) || !isset($_GET['lesson_id']) || !isset($_GET['concept_id']) || !exercise::can_access($_GET['id'], $_GET['lesson_id'], $_GET['concept_id'], 1))
			{
				return call('pages', 'error'); //or even call a blank editor for playing around in
			}
			$exercise = exercise::get($_GET['id']); //what if it's null? don't want that.. need to be careful of that in base, too

			require_once('models/lesson.php');
			$lesson = lesson::get_for_concept_and_user($_GET['lesson_id'], $_GET['concept_id'], 1);


			require_once('models/concept.php');
			$concept = concept::get($_GET['concept_id']); //all i really want is the section id for links...

			//require_once('views/shared/editor.php');

			$view_to_show = 'views/exercise/editor.php';
			require_once('views/shared/layout.php');
		}

		public function mark_as_completed() //
		{
			require_once('completion_status.php');

			/*
			to mark completed
				-must be allowed to access (check in stored procedure)
				-don't mark again if already completed.
			*/
			$success = false;

			
			//return success true/false
			if (isset($_POST["id"]) && isset($_POST["lesson_id"]) && isset($_POST["concept_id"]) && exercise::can_access($_POST["id"], $_POST["lesson_id"], $_POST["concept_id"], 1))
			{
				//if it accidentally gets marked twice somehow, it's not a problem, but let's try to avoid
				if(exercise::get_completion_status($_POST["id"], $_POST["lesson_id"], $_POST["concept_id"], 1) != Completion_Status::COMPLETED)
				{
					exercise::set_completion_status($_POST['id'], $_POST['lesson_id'], $_POST['concept_id'], 1, Completion_Status::COMPLETED);
				}
				$success = true;
			}
			
			
			$json_data = array('success' => $success);
			require_once('views/shared/json_wrapper.php');

			//should i check the referrer? $_SERVER['HTTP_REFERER'];
			//echo $_SERVER['HTTP_REFERER'];
			//it's still really easy to cheat, but it should help a little...
		}

	}
?>
