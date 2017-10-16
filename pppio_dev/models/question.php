<?php
	require_once('models/model.php');
	class Question extends Model
	{
		protected static $types = array('id' => Type::INTEGER, 'name' => Type::STRING, 'instructions' => Type::STRING, 'start_code' => Type::CODE, 'test_code' => Type::CODE, 'language' => Type::LANGUAGE, 'exam' => Type::EXAM, 'weight' => TYPE::INTEGER); //use the enum
		protected $name = '';
		protected $instructions = '';
		protected $start_code = '';
		protected $test_code = '';
		protected $language;
		protected $exam;
		protected $weight = '';

		public static function get_pairs_for_owner($owner_id)
		{
			$db = Db::getReader();
			$owner_id = intval($owner_id);

			$function_name = 'sproc_read_question_get_pairs_for_owner';
			$req = $db->prepare(static::build_query($function_name, array('owner_id')));
			$req->execute(array('owner_id' => $owner_id));

			return $req->fetchAll(PDO::FETCH_KEY_PAIR); // $req->fetchAll(PDO::FETCH_BOTH); //probably i should have a key/value model or something.. right now just using array. trust.
		}

		public static function get_pairs_for_exam($exam_id){
			$db = Db::getReader();
			$exam_id = intval($exam_id);

			$function_name = 'sproc_read_question_get_pairs_for_exam';
			$req = $db->prepare(static::build_query($function_name, array('exam_id')));
			$req->execute(array('exam_id' => $exam_id));

			return $req->fetchAll(PDO::FETCH_KEY_PAIR);
		}

		public static function update_code_file($question_id, $exam_id, $user_id, $contents, $completion_status_id)
		{
			$db = Db::getWriter();
			$question_id = intval($question_id);
			$exam_id = intval($exam_id);
			$user_id = intval($user_id);
			$completion_status_id = intval($completion_status_id);

			$function_name = 'sproc_write_question_update_student_answer';
			$req = $db->prepare(static::build_query($function_name, array('question_id', 'exam_id', 'user_id', 'contents', 'completion_status_id')));
			$req->execute(array('question_id' => $question_id, 'exam_id' => $exam_id, 'user_id' => $user_id, 'contents' => $contents, 'completion_status_id' => $completion_status_id));
		}

		public static function get_code_file($question_id, $exam_id)
		{
			$db = Db::getReader();
			$question_id = intval($question_id);
			$exam_id = intval($exam_id);
			$user_id = intval($_SESSION['user']->get_id());

			$function_name = 'sproc_read_question_get_student_answer';
			$req = $db->prepare(static::build_query($function_name, array('question_id', 'exam_id', 'user_id')));
			$req->execute(array('question_id' => $question_id, 'exam_id' => $exam_id, 'user_id' => $user_id));

			return $req->fetch(PDO::FETCH_COLUMN); //returns only the contents
		}

		public static function set_completion_status($question_id, $exam_id, $user_id, $completion_status_id)
		{
			require_once('enums/completion_status.php');

			$db = Db::getReader();
			$question_id = intval($question_id);
			$exam_id = intval($exam_id);
			$user_id = intval($user_id);
			$completion_status_id = intval($completion_status_id); //please be valid

			$function_name = 'sproc_write_completion_status_to_question';
			$req = $db->prepare(static::build_query($function_name, array('question_id', 'exam_id', 'user_id', 'completion_status_id')));
			$req->execute(array('question_id' => $question_id, 'exam_id' => $exam_id, 'user_id' => $user_id, 'completion_status_id' => $completion_status_id));
		}

		public static function get_completion_status($question_id, $exam_id, $user_id)
		{
			$db = Db::getReader();
			$question_id = intval($question_id);
			$exam_id = intval($exam_id);
			$user_id = intval($user_id);

			$function_name = 'sproc_read_completion_status_to_question';
			$req = $db->prepare(static::build_query($function_name, array('question_id', 'exam_id', 'user_id')));
			$req->execute(array('question_id' => $question_id, 'exam_id' => $exam_id, 'user_id' => $user_id));

			return $req->fetch(PDO::FETCH_COLUMN);
		}
	}
?>
