<?php
require_once('models/model.php');
class Section extends Model{
	protected static $types = array('id' => Type::INTEGER, 'name' => Type::STRING, 'course' => Type::COURSE,
		'teacher' => Type::USER, 'start_date' => Type::DATETIME, 'end_date' => Type::DATETIME,
		'students' => Type::LIST_USER, 'teaching_assistants' => Type::LIST_USER, 'concepts' => Type::LIST_CONCEPT);
	protected static $db_hidden_props = array('id' => true, 'hidden_props' => true, 'db_hidden_props' => true, 'types' => true, 'concepts' => true);
	protected $name;
	protected $course;
	protected $teacher;
	protected $start_date;
	protected $end_date;
	protected $students;
	protected $teaching_assistants;
	protected $concepts;

	//Returns all sections where a user is listed as a student
	public static function get_pairs_for_student($user_id){
		$db = Db::getReader();
		$user_id = intval($user_id);

		$function_name = 'sproc_read_section_get_pairs_for_student';
		$req = $db->prepare(static::build_query($function_name, array('user_id')));
		$req->execute(array('user_id' => $user_id));

		require_once('models/key_value_pair.php');
		return $req->fetchAll(PDO::FETCH_KEY_PAIR);
	}

	public static function get_pairs_for_teaching_assistant($user_id){
		$db = Db::getReader();
		$user_id = intval($user_id);

		$function_name = 'sproc_read_section_get_pairs_for_teaching_assistant';
		$req = $db->prepare(static::build_query($function_name, array('user_id')));
		$req->execute(array('user_id' => $user_id));

		require_once('models/key_value_pair.php');
		return $req->fetchAll(PDO::FETCH_KEY_PAIR);
	}

	//Return all sections a student is participating in the study for. Ignores the start and end time of the section.
	public static function get_study_pairs_for_student($user_id){
		$db = Db::getReader();
		$user_id = intval($user_id);

		$function_name = 'sproc_read_section_get_study_pairs_for_student';
		$req = $db->prepare(static::build_query($function_name, array('user_id')));
		$req->execute(array('user_id' => $user_id));

		require_once('models/key_value_pair.php');
		return $req->fetchAll(PDO::FETCH_KEY_PAIR);
	}

	//Returns all students who are participating in the study for a section
	public static function get_study_students($section_id){
		$db = Db::getReader();
		$section_id = intval($section_id);

		$function_name = 'sproc_read_section_get_study_students';
		$req = $db->prepare(static::build_query($function_name, array('section_id')));
		$req->execute(array('section_id' => $section_id));

		require_once('models/key_value_pair.php');
		return $req->fetchAll(PDO::FETCH_KEY_PAIR);
	}

	public static function get_students($owner_id){
		$db = Db::getReader();
		$owner_id = intval($owner_id);

		$function_name = 'sproc_read_section_get_students';
		$req = $db->prepare(static::build_query($function_name, array('owner_id')));
		$req->execute(array('owner_id' => $owner_id));
		$ret = $req->fetchAll(PDO::FETCH_ASSOC);
		foreach($ret as $key => $val){
			$ret[$key]['students'] = json_decode($val['students']);

		}
		return $ret;
	}

	public static function get_pairs_for_owner($owner_id){
		$db = Db::getReader();
		$owner_id = intval($owner_id);

		$function_name = 'sproc_read_section_get_pairs_for_owner';
		$req = $db->prepare(static::build_query($function_name, array('owner_id')));
		$req->execute(array('owner_id' => $owner_id));

		return $req->fetchAll(PDO::FETCH_KEY_PAIR);  // $req->fetchAll(PDO::FETCH_BOTH); //probably i should have a key/value model or something.. right now just using array. trust.
	}

	public static function is_owner($id, $user_id){
		$db = Db::getReader();
		$id = intval($id);
		$user_id = intval($user_id);

		$function_name = 'sproc_read_section_is_owner';
		$req = $db->prepare(static::build_query($function_name, array('id', 'user_id')));
		$req->execute(array('id' => $id, 'user_id' => $user_id));

		return $req->fetch(PDO::FETCH_COLUMN);
	}

	public static function is_teaching_assistant($id, $user_id){
		$db = Db::getReader();
		$id = intval($id);
		$user_id = intval($user_id);

		$function_name = 'sproc_read_section_is_teaching_assistant';
		$req = $db->prepare(static::build_query($function_name, array('id', 'user_id')));
		$req->execute(array('id' => $id, 'user_id' => $user_id));

		return $req->fetch(PDO::FETCH_COLUMN);
	}

	public static function is_student($id, $user_id){
		$db = Db::getReader();
		$id = intval($id);
		$user_id = intval($user_id);

		$function_name = 'sproc_read_section_is_student';
		$req = $db->prepare(static::build_query($function_name, array('id', 'user_id')));
		$req->execute(array('id' => $id, 'user_id' => $user_id));

		return $req->fetch(PDO::FETCH_COLUMN);
	}

	//Overwrite the base create to allow for setting if a student will be participating in the study or not
	public function create(){
		$db = Db::getWriter();

		$props = $this->get_db_properties();
		unset($props['students']);

		if(isset($_POST['is_study_students'])){
			$props['is_study_students'] = static::php_array_to_pg_array($_POST['is_study_students']);
		} else {
			$props['is_study_students'] = static::php_array_to_pg_array(array());
		}

		if(isset($_POST['is_study_students'])){
			$props['not_study_students'] = static::php_array_to_pg_array($_POST['not_study_students']);
		} else {
			$props['not_study_students'] = static::php_array_to_pg_array(array());
		}

		$function_name = 'sproc_write_section_create';
		$req = $db->prepare(static::build_query($function_name, array_keys($props)));

		$req->execute($props);

		$this->set_id($req->fetchColumn());
	}

	/*This was created to be able to set if a student will participate in data collection or not
	The create function in the section controller had to be changed and this was the only way(that I know of) to get the order the array correct
	Also it doesn't return courses and teacher like it did before which weren't used for create*/
	public static function get_properties_for_create(){
		$ret_props = array();
		$ret_props['name'] = null;
		$ret_props['course'] = null;
		$ret_props['start_date'] = null;
		$ret_props['end_date'] = null;
		$ret_props['is_study_students'] = null;
		$ret_props['not_study_students'] = null;
		$ret_props['teaching_assistants'] = null;

		return $ret_props;
	}

	/*This was created to be able to set if a student will participate in data collection or not
	The create function in the section controller had to be changed and this was the only way(that I know of) to get the order the array correct
	Also it doesn't return courses and teacher like it did before which weren't used for create*/
	public static function get_types_for_create(){
		$ret_props = array();
		$ret_props['id'] = Type::INTEGER;
		$ret_props['name'] = Type::STRING;
		$ret_props['course'] = Type::COURSE;
		$ret_props['start_date'] = Type::DATETIME;
		$ret_props['end_date'] = Type::DATETIME;
		$ret_props['is_study_students'] = Type::LIST_USER;
		$ret_props['not_study_students'] = Type::LIST_USER;
		$ret_props['teaching_assistants'] = Type::LIST_USER;

		return $ret_props;
	}

	public static function get_types_for_read(){
		$ret_props = array();
		$ret_props['id'] = Type::INTEGER;
		$ret_props['name'] = Type::STRING;
		$ret_props['course'] = Type::COURSE;
		$ret_props['start_date'] = Type::DATETIME;
		$ret_props['end_date'] = Type::DATETIME;
		$ret_props['is_study_students'] = Type::LIST_USER;
		$ret_props['not_study_students'] = Type::LIST_USER;
		$ret_props['teaching_assistants'] = Type::LIST_USER;
		$ret_props['concepts'] = Type::LIST_CONCEPT;

		return $ret_props;
	}

	//Overwrite the base update to allow for updating study partipant students and non study participant students
	public function update(){
	    $db = Db::getWriter();

	    $props = $this->get_db_properties();
	    $props['id'] = $this->id;
		unset($props['students']);

		if(isset($_POST['is_study_students'])){
			$props['is_study_students'] = static::php_array_to_pg_array($_POST['is_study_students']);
		} else {
			$props['is_study_students'] = static::php_array_to_pg_array(array());
		}

		if(isset($_POST['is_study_students'])){
			$props['not_study_students'] = static::php_array_to_pg_array($_POST['not_study_students']);
		} else {
			$props['not_study_students'] = static::php_array_to_pg_array(array());
		}

	    $function_name = 'sproc_write_section_update';
	    $req = $db->prepare(static::build_query($function_name, array_keys($props)));
	    $req->execute($props);
	}

	public function get_properties_for_update(){
		$study_students = Section::get_study_students($this->id);

		$all_props = get_class_vars(static::class);
		$ret_props = array();
		foreach($all_props as $key => $value){
			if(!isset(static::$hidden_props[$key]) || !static::$hidden_props[$key]){
				if($key === 'students'){
					$ret_props['is_study_students'] = array();
					$ret_props['not_study_students'] = array();
					foreach($this->students as $s_key => $s_value){
						if(array_key_exists($s_key, $study_students)){
							$ret_props['is_study_students'][$s_key] = $s_value;}
						else {
							$ret_props['not_study_students'][$s_key] = $s_value;
						}
					}
				} else {
					$ret_props[$key] = $this->$key;
				}
			}
		}
		return $ret_props;
	}
}
?>
