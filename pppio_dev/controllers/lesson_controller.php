<?php
	require_once('controllers/base_controller.php');
	class LessonController extends BaseController
	{
		//now has the basic actions

		public function read_student() //the correct one should be called based on who is logged in. they shouldn't be different actions to the user.
		{
			//this needs to make sure student is in class

			if (!isset($_GET['id']))
			{
				return call('pages', 'error');
			}
			//something like this
			$lesson = ($this->model_name)::get($_GET['id']);
			//todo: should show error if there isn't one with that id!

			//where should all models be included? in the controller? right now the one that goes with the controller is included in routes.
			//i need to include the course model

			require_once('views/lesson/read_student.php');
		}


	}
?>
