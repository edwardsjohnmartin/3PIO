<?php
	require_once('controllers/base_controller.php');
	class LessonController extends BaseController
	{
		//now has the basic actions

		public function read_student() //the correct one should be called based on who is logged in. they shouldn't be different actions to the user.
		{
			//this needs to make sure student is in class

			if (!isset($_GET['id']) || !isset($_GET['concept_id']) || !lesson::can_access($_GET['id'], $_GET['concept_id'], 1)) //should show a permission error possibly
			{
				return call('pages', 'error');
			}
			//something like this
			$lesson = ($this->model_name)::get_for_concept_and_user($_GET['id'], $_GET['concept_id'], 1);
			//todo: should show error if there isn't one with that id!

			//where should all models be included? in the controller? right now the one that goes with the controller is included in routes.
			//i need to include the course model

			$view_to_show = 'views/lesson/read_student.php';
			require_once('views/shared/layout.php');

		}


	}
?>
