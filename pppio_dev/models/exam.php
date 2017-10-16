<?php
require_once('models/model.php');
class Exam extends Model
{
	protected static $types = array('id' => Type::INTEGER, 'name' => Type::STRING,'instructions' => Type::STRING, 'owner' => Type::USER, 'section' => Type::SECTION, 'questions' => Type::LIST_QUESTION);
	protected static $hidden_props = array('id' => true, 'hidden_props' => true, 'db_hidden_props' => true, 'types' => true, 'owner' => true, 'questions' => true);
	protected static $db_hidden_props = array('id' => true, 'hidden_props' => true, 'db_hidden_props' => true, 'types' => true, 'owner' => true, 'questions' => true);
	protected $name = '';
	protected $instructions = '';
	protected $owner;
	protected $section;
	protected $questions;

	public static function get_pairs_for_owner($owner_id)
	{
		$db = Db::getReader();
		$owner_id = intval($owner_id);

		$function_name = 'sproc_read_exam_get_pairs_for_owner';
		$req = $db->prepare(static::build_query($function_name, array('owner_id')));
		$req->execute(array('owner_id' => $owner_id));

		return $req->fetchAll(PDO::FETCH_KEY_PAIR); // $req->fetchAll(PDO::FETCH_BOTH); //probably i should have a key/value model or something.. right now just using array. trust.
	}

	public static function get_all_for_section($section_id)
	{
		$db = Db::getReader();
		$section_id = intval($section_id);

		$function_name = 'sproc_read_exams_for_section';
		$req = $db->prepare(static::build_query($function_name, array('section_id')));
		$req->execute(array('section_id' => $section_id));
		$ret = $req->fetchAll(PDO::FETCH_ASSOC);
		foreach($ret as $key => $val)
		{
			$ret[$key]['questions'] = json_decode($val['questions']);

		}
		return $ret;
	}

	public static function get_all_for_section_and_student($section_id, $user_id)
	{
		$db = Db::getReader();
		$section_id = intval($section_id);
		$user_id = intval($user_id);

		$function_name = 'sproc_read_exam_get_all_for_section_and_student';
		$req = $db->prepare(static::build_query($function_name, array('section_id', 'user_id')));
		$req->execute(array('section_id' => $section_id, 'user_id' => $user_id));

		return $req->fetchAll(PDO::FETCH_NAMED);
	}

	public static function get_for_student($exam_id)
	{
		$db = Db::getReader();
		$exam_id = intval($exam_id);
		$user_id = $_SESSION['user']->get_id();

		$function_name = 'sproc_read_exam_get_for_student';
		$req = $db->prepare(static::build_query($function_name, array('exam_id', 'user_id')));
		$req->execute(array('exam_id' => $exam_id, 'user_id' => $user_id));

		$req->setFetchMode(PDO::FETCH_CLASS, Exam);
		return $req->fetch(PDO::FETCH_CLASS);
	}

	public static function get_times($exam_id)
	{
		$db = Db::getReader();
		$exam_id = intval($exam_id);

		$function_name = 'sproc_read_exam_get_times';
		$req = $db->prepare(static::build_query($function_name, array('exam_id')));
		$req->execute(array('exam_id' => $exam_id));
		return $req->fetchAll(PDO::FETCH_CLASS);
	}

	public static function get_times_for_student($exam_id, $user_id)
	{
		$db = Db::getReader();
		$exam_id = intval($exam_id);
		$user_id = intval($user_id);

		$function_name = 'sproc_read_exam_get_times_for_student';
		$req = $db->prepare(static::build_query($function_name, array('exam_id', 'user_id')));
		$req->execute(array('exam_id' => $exam_id, 'user_id' => $user_id));
		return $req->fetchAll(PDO::FETCH_CLASS);
	}

	public function set_owner($id)
	{
		$this->owner = intval($id);
	}

	public static function update_times($times)
	{
		$db = Db::getWriter();
		$function_name = 'sproc_write_exam_update_times';

		$times['students'] = parent::php_array_to_pg_array($times['students']);

		$req = $db->prepare(static::build_query($function_name, array_keys($times)));
		$req->execute($times);
	}

	public static function can_preview($id, $user_id)
	{
		return static::is_teaching_assistant($id, $user_id) || static::is_owner($id, $user_id);
	}

	public static function get_total_weight($exam_id)
	{
		$db = Db::getReader();
		$exam_id = intval($exam_id);

		$function_name = 'sproc_read_exam_get_total_weight';
		$req = $db->prepare(static::build_query($function_name, array('exam_id')));
		$req->execute(array('exam_id' => $exam_id));
		return $req->fetch(PDO::FETCH_COLUMN);
	}

	public function get_properties()
	{
		$all_props = get_class_vars(static::class);
		$ret_props = array();
		foreach($all_props as $key => $value)
		{
			if(!isset(static::$hidden_props[$key]) || !static::$hidden_props[$key])
			{
				$ret_props[$key] = $this->$key;
			}
		}
		$ret_props['questions'] = $this->questions;
		return $ret_props;
	}

	public function set_properties($args) //should be able to accept the return value of get_properties
	{
		foreach($args as $key => $value)
		{
			if ($key == "questions")
			{
				if(key_exists($key, static::$types) && static::$types[$key] == Type::BOOLEAN){
					$this->$key = true;
				}else{
					$this->$key = $value;
				}
			}
			else if ($key == "owner")
			{
				if(key_exists($key, static::$types) && static::$types[$key] == Type::BOOLEAN){
					$this->$key = true;
				}else{
					$this->$key = $value;
				}
			}
			else if(property_exists($this, $key) && (!isset(static::$hidden_props[$key]) || !static::$hidden_props[$key])) //i could also check if it's in the types
			{
				if(key_exists($key, static::$types) && static::$types[$key] == Type::BOOLEAN){
					$this->$key = true;
				}else{
					$this->$key = $value;
				}
			}

		}
	}

	public function create()
	{
		$model_name = static::class;
		$db = Db::getWriter();

		$props = $this->get_db_properties();
		$props['owner'] = $_SESSION['user']->get_id();

		$function_name = 'sproc_write_' . $model_name . '_create';
		$req = $db->prepare(static::build_query($function_name, array_keys($props)));

		$req->execute($props);

		$this->set_id($req->fetchColumn()); //something like that. i'm using the setter here but not the getter above, which should i do?
	}
}
?>
