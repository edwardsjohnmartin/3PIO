<?php
	require_once('models/model.php');
	class Lesson extends Model
	{
		protected static $types = array('id' => Type::INTEGER, 'name' => Type::STRING, 'description' => Type::STRING, 'owner' => Type::USER, 'exercises' => Type::LIST_EXERCISE); //use the enum
		protected $name;
		protected $description;
		protected $owner;
		protected $exercises;

		public static function get_for_concept_and_user($id, $concept_id, $user_id) //the statuses will just be not completed if the user doesn't have permission to access
		{
			$db = Db::getReader();
			$id = intval($id);
			$concept_id = intval($concept_id);
			$user_id = intval($user_id);

			$function_name = 'sproc_read_lesson_get_for_concept_and_user';
			$req = $db->prepare(static::build_query($function_name, array('id', 'concept_id', 'user_id')));
			$req->execute(array('id' => $id, 'concept_id' => $concept_id, 'user_id' => $user_id));

			$req->setFetchMode(PDO::FETCH_CLASS,  'Lesson');
			return $req->fetch(PDO::FETCH_CLASS);
		}

		public static function can_access($id, $concept_id, $user_id)
		{
			$db = Db::getReader();
			$id = intval($id);
			$concept_id = intval($concept_id);
			$user_id = intval($user_id);

			$function_name = 'sproc_read_lesson_can_access';
			$req = $db->prepare(static::build_query($function_name, array('id', 'concept_id', 'user_id')));
			$req->execute(array('id' => $id, 'concept_id' => $concept_id, 'user_id' => $user_id));

			return $req->fetch(PDO::FETCH_COLUMN);
		}
	}
?>
