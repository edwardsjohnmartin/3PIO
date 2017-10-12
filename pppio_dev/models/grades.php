<?php
require_once('models/model.php');
class Grades extends Model
{
	protected static $types = array('id' => Type::INTEGER, 'name' => Type::STRING, 'sections' => Type::LIST_SECTION, 'exams' => Type::LIST_EXAM);

	protected $id = null;
	protected $name;
	protected $sections;
	protected $exams;

	public static function get()
	{
		require_once('models/section.php');
		require_once('models/exam.php');
		$owner_id = $_SESSION['user']->get_id();

		$sections_2 = Section::get_students($owner_id);
		$exams = Exam::get_pairs_for_owner($owner_id);
		$section_id = $sections_2['0']['id'];
		$exams_2 = Exam::get_all_for_section($section_id);

		$user_id = $sections_2['0']['students']['2']->key;
		$exam_id = $exams_2['2']['id'];
		$grades = User::get_grades_for_exam($user_id, $exam_id);
		$x = 0;
	}
}
?>
