<?php
	require_once('models/model.php');
	class Project extends Model
	{
		protected static $types = array('id' => Type::INTEGER, 'name' => Type::STRING, 'description' => Type::STRING, 'starter_code'=> Type::CODE, 'language' => Type::LANGUAGE, 'owner' => Type::USER, 'max_grade' => Type::DOUBLE);
		protected $name;
		protected $description;
		protected $starter_code;
		protected $language;
		protected $owner;
		protected $max_grade;

	//this is incomplete. will need to take into account teams.
	//may also need to check if the concept has a project at all
		public static function can_access($concept_id, $user_id)
		{
			if(is_array($user_id))
			{
				$db = Db::getReader();
				$concept_id = intval($concept_id);
				$user_id = static::php_array_to_pg_array($user_id); 

				$function_name = 'sproc_read_project_can_access_for_multiple_users';
				$req = $db->prepare(static::build_query($function_name, array('concept_id', 'user_id')));
				$req->execute(array('concept_id' => $concept_id, 'user_id' => $user_id));

				return $req->fetchAll(PDO::FETCH_KEY_PAIR);
			}
			else
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

		public static function update_code_file($concept_id, $user_ids, $contents) //well.. it doesn't make sense to add it if the user is not in the concept. i probably should check that somewhere
		{
			$db = Db::getReader();
			$user_ids = static::php_array_to_pg_array($user_ids); //i should check if it's an int, and if it is, make it an array containing just that int
			$concept_id = intval($concept_id);

			$function_name = 'sproc_write_project_update_code_file_for_users';
			$req = $db->prepare(static::build_query($function_name, array('concept_id', 'user_ids', 'contents')));
			$req->execute(array('concept_id' => $concept_id, 'user_ids' => $user_ids, 'contents' => $contents));
		}

		public static function get_code_file($concept_id, $user_id)
		{
			$db = Db::getReader();
			$concept_id = intval($concept_id);
			$user_id = intval($user_id);

			$function_name = 'sproc_read_project_get_code_file_for_user';
			$req = $db->prepare(static::build_query($function_name, array('concept_id', 'user_id')));
			$req->execute(array('concept_id' => $concept_id, 'user_id' => $user_id));

			return $req->fetch(PDO::FETCH_COLUMN); //returns only the contents
		}

		public static function is_owner($id, $user_id)
		{
			$db = Db::getReader();
			$id = intval($id);
			$user_id = intval($user_id);

			$function_name = 'sproc_read_project_is_owner';
			$req = $db->prepare(static::build_query($function_name, array('id', 'user_id')));
			$req->execute(array('id' => $id, 'user_id' => $user_id));

			return $req->fetch(PDO::FETCH_COLUMN);
		}
	}
?>
