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

	public function get_exam_grade_for_student()
	{
		require_once('models/section.php');
		require_once('models/exam.php');

		if(!isset($_GET['exam_id']))
		{
			return call('pages', 'error');
		}

		if(!isset($_GET['section_id']))
		{
			return call('pages', 'error');
		}

		if(!isset($_SESSION['user']))
		{
			return call('pages', 'error');
		}

		if(!Section::is_student($_GET['section_id'], $_SESSION['user']->get_id()))
		{
			return call('pages', 'error');
		}

		if(empty(Exam::get($_GET['exam_id'])))
		{
			return call('pages', 'error');
		}

		$view_to_show = 'views/grades/exam_grade_for_student.php';
		require_once('views/shared/layout.php');
	}
}
?>
