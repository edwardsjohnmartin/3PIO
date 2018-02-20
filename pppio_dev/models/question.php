<?php
	require_once('models/model.php');
	class Question extends Model{
		protected static $types = array('id' => Type::INTEGER, 'name' => Type::STRING, 'instructions' => Type::STRING, 'start_code' => Type::CODE, 'test_code' => Type::CODE, 'language' => Type::LANGUAGE, 'exam' => Type::EXAM, 'weight' => TYPE::INTEGER); //use the enum
		protected $name = '';
		protected $instructions = '';
		protected $start_code = '';
		protected $test_code = '';
		protected $language;
		protected $exam;
		protected $weight = '';

		public static function get_pairs_for_owner($owner_id){
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

		public static function update_code_file($question_id, $exam_id, $user_id, $contents, $completion_status_id, $score){
			$db = Db::getWriter();
			$question_id = intval($question_id);
			$exam_id = intval($exam_id);
			$user_id = intval($user_id);
			$score = floatval($score);
			$completion_status_id = intval($completion_status_id);

			$function_name = 'sproc_write_question_update_student_answer';
			$req = $db->prepare(static::build_query($function_name, array('question_id', 'exam_id', 'user_id', 'contents', 'completion_status_id', 'score')));
			$req->execute(array('question_id' => $question_id, 'exam_id' => $exam_id, 'user_id' => $user_id, 'contents' => $contents, 'completion_status_id' => $completion_status_id, 'score' => $score));
		}

		public static function get_question_with_answer_for_student($question_id, $exam_id, $student_id){
			$db = Db::getReader();
			$question_id = intval($question_id);
			$exam_id = intval($exam_id);
			$student_id = intval($student_id);

			$function_name = 'sproc_read_question_with_answer_for_student';
			$req = $db->prepare(static::build_query($function_name, array('question_id', 'exam_id', 'student_id')));
			$req->execute(array('question_id' => $question_id, 'exam_id' => $exam_id, 'student_id' => $student_id));

			$req->setFetchMode(PDO::FETCH_CLASS, 'Question');
			return $req->fetch(PDO::FETCH_CLASS);
		}

		public static function get_code_file($question_id, $exam_id){
			$db = Db::getReader();
			$question_id = intval($question_id);
			$exam_id = intval($exam_id);
			$user_id = intval($_SESSION['user']->get_id());

			$function_name = 'sproc_read_question_get_student_answer';
			$req = $db->prepare(static::build_query($function_name, array('question_id', 'exam_id', 'user_id')));
			$req->execute(array('question_id' => $question_id, 'exam_id' => $exam_id, 'user_id' => $user_id));

			return $req->fetch(PDO::FETCH_COLUMN); //returns only the contents
		}

		public static function read_occurrences($user_id, $exam_id){
			$db = Db::getReader();
			$user_id = intval($user_id);
			$exam_id = intval($exam_id);

			$function_name = 'sproc_read_get_left_page_occurrences_for_user_and_exam';
			$req = $db->prepare(static::build_query($function_name, array('user_id', 'exam_id')));
			$req->execute(array('user_id' => $user_id, 'exam_id' => $exam_id));

			return $req->fetchAll(PDO::FETCH_ASSOC);
		}

		public static function create_occurrence($user_id, $question_id, $exam_id, $date_of_occurrence){
			$db = Db::getReader();
			$function_name = 'sproc_write_user_left_page_occurrence_create';
			$req = $db->prepare(static::build_query($function_name, array('user_id', 'question_id', 'exam_id', 'date_of_occurrence')));
			$req->execute(array('user_id' => $user_id, 'question_id' => $question_id, 'exam_id' => $exam_id, 'date_of_occurrence' => $date_of_occurrence));
		}

		public function get_full_properties(){
			$ret_props = array();
			$ret_props['id'] = $this->id;
			$ret_props['instructions'] = $this->instructions;
			$ret_props['weight'] = $this->weight;
			$ret_props['start_code'] = $this->start_code;
			$ret_props['test_code'] = $this->test_code;
			return $ret_props;
		}
	}
?>
