<?php
	require_once('models/model.php');
	class Project extends Model
	{
		protected static $types = array('id' => Type::INTEGER, 'name' => Type::STRING, 'description' => Type::STRING, 'starter_code'=> Type::CODE, 'language' => Type::LANGUAGE, 'max_grade' => Type::DOUBLE);
		protected $name;
		protected $description;
		protected $starter_code;
		protected $language;
		protected $max_grade;

	//this is incomplete. will need to take into account teams.
	//may also need to check if the concept has a project at all
		public static function can_access($concept_id, $user_id)
		{
			$db = Db::getReader();
			$concept_id = intval($concept_id);
			$user_id = intval($user_id);

			$function_name = 'sproc_read_project_can_access';
			$req = $db->prepare(static::build_query($function_name, array('concept_id', 'user_id')));
			$req->execute(array('concept_id' => $concept_id, 'user_id' => $user_id));

			return $req->fetch(PDO::FETCH_COLUMN);
		}


	}
?>
