<?php
require_once('models/model.php');
class Lesson extends Model{
	protected static $types = array('id' => Type::INTEGER, 'name' => Type::STRING, 'description' => Type::STRING, 'owner' => Type::USER, 'exercises' => Type::LIST_EXERCISE);
	protected static $db_hidden_props = array('id' => true, 'hidden_props' => true, 'db_hidden_props' => true, 'types' => true, 'exercises' => true);
	protected $name = '';
	protected $description = '';
	protected $owner;
	protected $exercises;

	public static function get_pairs_for_owner($owner_id){
		$db = Db::getReader();
		$owner_id = intval($owner_id);

		$function_name = 'sproc_read_lesson_get_pairs_for_owner';
		$req = $db->prepare(static::build_query($function_name, array('owner_id')));
		$req->execute(array('owner_id' => $owner_id));

		return $req->fetchAll(PDO::FETCH_KEY_PAIR); // $req->fetchAll(PDO::FETCH_BOTH); //probably i should have a key/value model or something.. right now just using array. trust.
	}

	//the statuses will just be not completed if the user doesn't have permission to access
	public static function get_for_concept_and_student($id, $concept_id, $user_id){
		$db = Db::getReader();
		$id = intval($id);
		$concept_id = intval($concept_id);
		$user_id = intval($user_id);

		$function_name = 'sproc_read_lesson_get_for_concept_and_student';
		$req = $db->prepare(static::build_query($function_name, array('id', 'concept_id', 'user_id')));
		$req->execute(array('id' => $id, 'concept_id' => $concept_id, 'user_id' => $user_id));

		$req->setFetchMode(PDO::FETCH_CLASS, Lesson);
		return $req->fetch(PDO::FETCH_CLASS);
	}

	//the statuses will just be not completed if the user doesn't have permission to access
	public static function get_all_for_concept_and_student($concept_id, $user_id){
		$db = Db::getReader();
		$concept_id = intval($concept_id);
		$user_id = intval($user_id);

		$function_name = 'sproc_read_lesson_get_all_for_concept_and_student';
		$req = $db->prepare(static::build_query($function_name, array('concept_id', 'user_id')));
		$req->execute(array('concept_id' => $concept_id, 'user_id' => $user_id));

		return $req->fetchAll(PDO::FETCH_CLASS, 'Lesson');
	}

	public static function get_by_concept($owner_id){
		$db = Db::getReader();
		$owner_id = intval($owner_id);

		$function_name = 'sproc_read_lesson_get_by_concept';
		$req = $db->prepare(static::build_query($function_name, array('owner_id')));
		$req->execute(array('owner_id' => $owner_id));

		$ret = $req->fetchAll(\PDO::FETCH_ASSOC|\PDO::FETCH_UNIQUE);
		foreach($ret as $key => $val){
			$ret[$key]['lessons'] = json_decode($val['lessons']);
		}
		return $ret;
	}

	public static function can_access($id, $concept_id, $user_id){
		$db = Db::getReader();
		$id = intval($id);
		$concept_id = intval($concept_id);
		$user_id = intval($user_id);

		$function_name = 'sproc_read_lesson_can_access';
		$req = $db->prepare(static::build_query($function_name, array('id', 'concept_id', 'user_id')));
		$req->execute(array('id' => $id, 'concept_id' => $concept_id, 'user_id' => $user_id));

		return $req->fetch(PDO::FETCH_COLUMN);
	}

	public static function can_access_for_concept($concept_id, $user_id){
		$db = Db::getReader();
		$concept_id = intval($concept_id);
		$user_id = intval($user_id);

		$function_name = 'sproc_read_lesson_can_access_for_concept';
		$req = $db->prepare(static::build_query($function_name, array('concept_id', 'user_id')));
		$req->execute(array('concept_id' => $concept_id, 'user_id' => $user_id));

		return $req->fetch(PDO::FETCH_COLUMN);
	}

	public static function is_owner($id, $user_id){
		$db = Db::getReader();
		$id = intval($id);
		$user_id = intval($user_id);

		$function_name = 'sproc_read_lesson_is_owner';
		$req = $db->prepare(static::build_query($function_name, array('id', 'user_id')));
		$req->execute(array('id' => $id, 'user_id' => $user_id));

		return $req->fetch(PDO::FETCH_COLUMN);
	}
}
?>
