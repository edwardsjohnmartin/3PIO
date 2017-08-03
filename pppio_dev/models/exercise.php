<?php
	require_once('models/model.php');
	class Exercise extends Model
	{
		protected static $types = array('id' => Type::INTEGER, 'name' => Type::STRING, 'description' => Type::STRING, 'starter_code' => Type::CODE, 'test_code' => Type::CODE, 'language' => Type::LANGUAGE, 'lesson' => Type::LESSON, 'tags' => TYPE::LIST_TAG); //use the enum
		protected $name;
		protected $description;
		protected $starter_code;
		protected $test_code;
		protected $language;
		protected $lesson;
		protected $tags;

		public static function can_access($id, $lesson_id, $concept_id, $user_id)
		{
			$db = Db::getReader();
			$id = intval($id);
			$lesson_id = intval($lesson_id);
			$concept_id = intval($concept_id);
			$user_id = intval($user_id);

			$function_name = 'sproc_read_exercise_can_access';
			$req = $db->prepare(static::build_query($function_name, array('id', 'lesson_id', 'concept_id', 'user_id')));
			$req->execute(array('id' => $id, 'lesson_id' => $lesson_id, 'concept_id' => $concept_id, 'user_id' => $user_id));

			return $req->fetch(PDO::FETCH_COLUMN);
		}

		public static function set_completion_status($id, $lesson_id, $concept_id, $user_id, $completion_status_id)
		{
			require_once('completion_status.php');

			$db = Db::getReader();
			$id = intval($id);
			$lesson_id = intval($lesson_id);
			$concept_id = intval($concept_id);
			$user_id = intval($user_id);
			$completion_status_id = intval($completion_status_id); //please be valid

			$function_name = 'sproc_write_completion_status_to_exercise_create';
			$req = $db->prepare(static::build_query($function_name, array('exercise_id', 'lesson_id', 'concept_id', 'user_id', 'completion_status_id')));
			$req->execute(array('exercise_id' => $id, 'lesson_id' => $lesson_id, 'concept_id' => $concept_id, 'user_id' => $user_id, 'completion_status_id' => $completion_status_id));
		}

		public static function get_completion_status($id, $lesson_id, $concept_id, $user_id)
		{
			$db = Db::getReader();
			$id = intval($id);
			$lesson_id = intval($lesson_id);
			$concept_id = intval($concept_id);
			$user_id = intval($user_id);

			$function_name = 'sproc_read_completion_status_to_exercise_get';
			$req = $db->prepare(static::build_query($function_name, array('exercise_id', 'lesson_id', 'concept_id', 'user_id')));
			$req->execute(array('exercise_id' => $id, 'lesson_id' => $lesson_id, 'concept_id' => $concept_id, 'user_id' => $user_id));

			return $req->fetch(PDO::FETCH_COLUMN);
		}

	}
?>
