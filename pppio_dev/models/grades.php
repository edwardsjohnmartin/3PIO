<?php
require_once('models/model.php');
class Grades extends Model
{
	public static function get_exam_scores($exam_id)
	{
		$db = Db::getReader();
		$exam_id = intval($exam_id);

		$function_name = 'sproc_read_exam_scores';
		$req = $db->prepare(static::build_query($function_name, array('exam_id')));
		$req->execute(array('exam_id' => $exam_id));
		$ret = $req->fetchAll(PDO::FETCH_ASSOC);

		if(!empty($ret))
		{
			foreach($ret as $key => $val)
			{
				$ret[$key]['scores'] = json_decode($val['scores'], false);
			}
		}

		return $ret;
	}

	public static function get_exam_grades($exam_id)
	{
		$db = Db::getReader();
		$exam_id = intval($exam_id);

		$function_name = 'sproc_read_exam_grades';
		$req = $db->prepare(static::build_query($function_name, array('exam_id')));
		$req->execute(array('exam_id' => $exam_id));
		$ret = $req->fetchAll(\PDO::FETCH_ASSOC|\PDO::FETCH_UNIQUE);

		if(!empty($ret))
		{
			foreach($ret as $ret_key => $ret_value)
			{
				$q_arr = trim($ret_value['questions'], "{}");
				$q_arr = explode(",",$q_arr);
				$s_arr = trim($ret_value['scores'], "{}");
				$s_arr = explode(",", $s_arr);

				$new_arr = array_combine($q_arr, $s_arr);

				$ret[$ret_key] = $new_arr;
			}
		}
		return $ret;
	}
}
?>
