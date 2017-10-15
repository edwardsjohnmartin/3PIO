<?php
require_once('controllers/base_controller.php');
class GradesController extends BaseController
{
	public function index()
	{
		require_once('models/section.php');
		require_once('models/exam.php');
		$owner_id = $_SESSION['user']->get_id();

		$sections = Section::get_students($owner_id);

		$view_to_show = 'views/grades/index.php';
		require_once('views/shared/layout.php');
	}

	public function get_section_grades()
	{
		if(!isset($_GET['id']))
		{
			return call('pages', 'error');
		}

		require_once('models/section.php');
		require_once('models/exam.php');

		$section = Section::get($_GET['id']);

		$view_to_show = 'views/grades/section_grades.php';
		require_once('views/shared/layout.php');
	}
}
?>
