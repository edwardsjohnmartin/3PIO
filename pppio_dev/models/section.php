<?php
	require_once('models/model.php');
	class Section extends Model
	{
		protected static $types = array('id' => Type::INTEGER, 'name' => Type::STRING, 'course' => Type::COURSE, 'teacher' => Type::USER, 'start_date' => Type::DATETIME, 'end_date' => Type::DATETIME, /*'concepts' => Type::LIST_CONCEPT, */'users' => Type::LIST_USER); //use the enum
		//protected static $db_hidden_props = array('id' => true, 'hidden_props' => true, 'db_hidden_props' => true, 'types' => true, 'concepts' => true);
		protected $name;
		protected $course;
		protected $teacher;
		protected $start_date;
		protected $end_date;
		//protected $concepts;
		protected $users;


		public static function get_pairs_for_student($user_id) //expecting multiple
		{
			$db = Db::getReader();
			$user_id = intval($user_id);

			$function_name = 'sproc_read_section_get_pairs_for_student';
			$req = $db->prepare(static::build_query($function_name, array('user_id')));
			$req->execute(array('user_id' => $user_id));

			require_once('models/key_value_pair.php');
			return $req->fetchAll(PDO::FETCH_CLASS, 'key_value_pair');
		}

		public static function get_pairs_for_owner($owner_id)
		{
			$db = Db::getReader(); 
			$owner_id = intval($owner_id);

			$function_name = 'sproc_read_section_get_pairs_for_owner';
			$req = $db->prepare(static::build_query($function_name, array('owner_id')));
			$req->execute(array('owner_id' => $owner_id));

			return $req->fetchAll(PDO::FETCH_KEY_PAIR);  // $req->fetchAll(PDO::FETCH_BOTH); //probably i should have a key/value model or something.. right now just using array. trust.
		}

	}
?>
