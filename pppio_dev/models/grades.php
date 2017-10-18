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
		foreach($ret as $key => $val)
		{
			$ret[$key]['scores'] = json_decode($val['scores']);

		}
		return $ret;
	}
}
?>
