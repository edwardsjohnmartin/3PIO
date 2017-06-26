<?php
	require_once('controllers/base_controller.php');
	class ProblemController extends BaseController
	{
		public function try_it()
		{
			//this should eventually require the lesson, class, etc. ids.
			if (!isset($_GET['id']))
			{
				return call('pages', 'error'); //or even call a blank editor for playing around in
			}
			$model = ($this->model_name)::get($_GET['id']); //what if it's null? don't want that.. need to be careful of that in base, too
			require_once('views/shared/editor.php');
		}
	}
?>
