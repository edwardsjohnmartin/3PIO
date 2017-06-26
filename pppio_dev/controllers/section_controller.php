<?php
	require_once('controllers/base_controller.php');
	class SectionController extends BaseController
	{
		//now has the basic actions
		
		public function read_student() //the correct one should be called based on who is logged in. they shouldn't be different actions to the user.
		{
			//this needs to make sure student is in class

			if (!isset($_GET['id']))
			{
				return call('pages', 'error');
			}
			else
			{
			//something like this
				$section = ($this->model_name)::get($_GET['id']);
				if ($section == null)
				{
					return call('pages', 'error');
				}
				else
				{
					//todo: should show error if there isn't one with that id!

					//where should all models be included? in the controller? right now the one that goes with the controller is included in routes.
					//i need to include the course model
					require_once('models/concept.php');

					$concepts = concept::get_all_for_section($_GET['id']);
					require_once('views/section/read_student.php');
				}
			}
		}
	}
?>
