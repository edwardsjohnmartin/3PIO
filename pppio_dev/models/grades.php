<?php
require_once('models/model.php');
class Grades extends Model
{
	public static function get()
	{
		require_once('models/section.php');
		require_once('models/exam.php');
		$owner_id = $_SESSION['user']->get_id();

		$sections = Section::get_students($owner_id);
		$exams = Exam::get_pairs_for_owner($owner_id);
		$section_id = $sections['0']['id'];
		$exams_2 = Exam::get_all_for_section($section_id);

		$user_id = $sections['0']['students']['2']->key;
		$exam_id = $exams_2['2']['id'];
		$x = 0;
	}

	public static function get_exam_scores($exam_id)
	{
		$db = Db::getReader();
		$exam_id = intval($exam_id);

		$function_name = 'sproc_read_exam_scores';
		$req = $db->prepare(static::build_query($function_name, array('exam_id')));
		$req->execute(array('exam_id' => $exam_id));
		$ret = $req->fetchAll(PDO::FETCH_ASSOC);
		foreach($ret as $key => $val)
		{
			$ret[$key]['scores'] = json_decode($val['scores']);

		}
		return $ret;
	}
}
?>
