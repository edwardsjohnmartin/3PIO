<?php
//i should use try/catches...
abstract class Model
{
	//hidden, db hidden, types. static.
	//or rather, shown and db allowed
	const MAX_BIGINT = 9223372036854775807; //this really should go somewhere else probably. should be global...
	//if these are overridden in child classes, they need to include these values, too. must be copied (sadly).
	protected static $hidden_props = array('id' => true, 'hidden_props' => true, 'db_hidden_props' => true, 'types' => true);
	protected static $db_hidden_props = array('id' => true, 'hidden_props' => true, 'db_hidden_props' => true, 'types' => true);
	protected static $types = array('id' => Type::INTEGER); //what will i do... the children need to set this one for sure. should this be static or constant? i'm just setting it over anyway in the class, for now.

	protected $id = null;

	public function get_id()
	{
		return $this->id;
	}

	public function set_id($id)
	{
		$this->id = intval($id);
	}

	public function __construct() //use optional parameters to allow to use constructor when getting from db
	{
		//this is probably very inefficient.. but it's convenient... what to do
		//i really should take care of this elsewhere. leave for now.
		//namely the db fill (set from json) and set properties (set int key)
		foreach(static::$types as $prop => $type) //actually just having an array would be much nicer
		{
			if(Type::is_list_model($type))
			{
				if(!is_array($this->$prop)) //this will either be an array of ints or a json
				{
					$temp = json_decode($this->$prop); //convert the json props to array. i'd like to not have to do this in these child classes. just do them in the base classes somehow. right now i have the model name as the type... but how do i know it's a model?
					$arr = array();
					foreach((array)$temp as $kvp) //why does this break on post if i don't tell it it's an array? they're the same. it knows it's an array. and it wasn't doing this before.
					{
						if($kvp->key != null) $arr[$kvp->key] = $kvp; //->value;
					}
					$this->$prop = $arr;
				}
			}
			else if(Type::is_model($type))
			{
				if(!is_int($this->$prop)) //well...
				{
					$this->$prop = json_decode($this->$prop); //convert the json props to array. i'd like to not have to do this in these child classes. just do them in the base classes somehow. right now i have the model name as the type... but how do i know it's a model?
				}
			}

		}
	}

	//i want another one just for property names?
	public function get_properties() //maybe just call "get" and "set"?
	{
		$all_props = get_class_vars(static::class);
		$ret_props = array();
		foreach($all_props as $key => $value)
		{
			if(!isset(static::$hidden_props[$key]) || !static::$hidden_props[$key]) //can't just do second. should i just do first?
			{
				$ret_props[$key] = $this->$key; //something like that...
				//what if i have a more complicated getter... maybe i should use individual getters/setters as well... not for now...
			}
		}
		return $ret_props;
	}

	public static function get_available_properties()
	{
		$all_props = get_class_vars(static::class);
		$ret_props = array();
		foreach($all_props as $key => $value)
		{
			if(!isset(static::$hidden_props[$key]) || !static::$hidden_props[$key]) //can't just do second. should i just do first?
			{
				$ret_props[$key] = null;
			}
		}
		return $ret_props;
	}

	//please go through this
	public function set_properties($args) //should be able to accept the return value of get_properties
	{
		foreach($args as $key => $value)
		{
			if(property_exists($this, $key) && (!isset(static::$hidden_props[$key]) || !static::$hidden_props[$key])) //i could also check if it's in the types
			{
				if(key_exists($key, static::$types) && static::$types[$key] == Type::BOOLEAN){
					$this->$key = true;
				}else{
					$this->$key = $value;
				}
			}

		}
	}

	//how to handle key of object instead of value itself?
	public function get_db_properties()
	{
		$all_props = get_class_vars(static::class); //don't want user added ones
		$ret_props = array();
		foreach($all_props as $key => $value) // i only want the key...
		{
			if(!isset(static::$db_hidden_props[$key]) || !static::$db_hidden_props[$key])
			{
				if(Type::is_list_model(static::$types[$key]))
				{
					$ret_props[$key] = static::php_array_to_pg_array($this->$key);
				}
				elseif( static::$types[$key] == Type::BOOLEAN){
					$ret_props[$key] = $this->$key == true ? "t": "f";
				}else{
					$ret_props[$key] = $this->$key;


				}
				//something like that...
				/*
				//if the type is a model...
				if(static::$types[$key] > Type::MODEL) //i should also check the type!!
				{
				$ret_props[$key] = $this->$key->key;
				}
				else
				{
				$ret_props[$key] = $this->$key; //something like that...
				}*/
			}
		}
		return $ret_props;
	}

	public static function get_types()
	{
		return static::$types;
	}

	public static function count()
	{
		$model_name = static::class;

		$db = Db::getReader();

		$function_name = 'sproc_read_' . $model_name . '_count';
		$req = $db->query(static::build_query($function_name));

		return $req->fetch_column();
	}

	//maybe rename these
	//i believe there is one case where this is used... in general, pairs should be used instead. some stored procedures may even be missing...
	public static function get_all()
	{
		$model_name = static::class;

		$db = Db::getReader();

		$function_name = 'sproc_read_' . $model_name . '_get_all';
		$req = $db->query(static::build_query($function_name));

		return $req->fetchAll(PDO::FETCH_CLASS, $model_name);
	}

	public static function get_pairs()
	{
		$model_name = static::class;

		$db = Db::getReader();

		$function_name = 'sproc_read_' . $model_name . '_get_pairs';
		$req = $db->query(static::build_query($function_name));

		return $req->fetchAll(PDO::FETCH_KEY_PAIR);
	}

	//should have pairs subset (too? only?)
	public static function subset($offset, $limit)
	{
		$model_name = static::class;

		//db will be happy
		//page may not be
		//¯\_(ツ)_/¯
		//model doesn't care!
		$offset = max(0, min($offset, MAX_BIGINT));
		$limit = max(0, min($limit, MAX_BIGINT));

		$params = array('off' => $offset, 'lim' => $limit);

		$db = Db::getReader();

		$function_name = 'sproc_read_' . $model_name . '_get_subset';
		$req = $db->query(static::build_query($function_name), $params);

		return $req->fetchAll(PDO::FETCH_CLASS, $model_name);
	}

	public static function get($id)
	{
		//make sure this is in the allowed range of ids.
		//my ids are ints right now, so that limit is 2147483647.
		//if i make them bigints it will be 9223372036854775807
		$model_name = static::class;

		$db = Db::getReader();
		$id = intval($id);

		$function_name = 'sproc_read_' . $model_name . '_get';
		$req = $db->prepare(static::build_query($function_name, array('id')));
		$req->execute(array('id' => $id));

		$req->setFetchMode(PDO::FETCH_CLASS,  $model_name);
		return $req->fetch(PDO::FETCH_CLASS);
	}

	public function create() //from self
	{
		$model_name = static::class;
		$db = Db::getWriter();

		$props = $this->get_db_properties();

		$function_name = 'sproc_write_' . $model_name . '_create';
		$req = $db->prepare(static::build_query($function_name, array_keys($props)));

		$req->execute($props);

		$this->set_id($req->fetchColumn()); //something like that. i'm using the setter here but not the getter above, which should i do?
	}

	public function update() //use self. don't return. except maybe boolean?
	{
		//print_r($this);
		$model_name = static::class;
		$db = Db::getWriter();

		$props = $this->get_db_properties();
		$props['id'] = $this->id; //use id getter? probably should for consistency, probably shouldn't for speed, and i can trust my own self (class)

		//print_r($this->get_properties());

		$function_name = 'sproc_write_' . $model_name . '_update';
		$req = $db->prepare(static::build_query($function_name, array_keys($props))); //something like that
		$req->execute($props);
	}

	public function delete($id) //same comment as update.
	{
		$model_name = static::class;

		$db = Db::getWriter();

		$function_name = 'sproc_write_' . $model_name . '_delete';
		$req = $db->prepare(static::build_query($function_name, array('id')));
		$req->execute(array('id' => $id));
	}

	//i could even pass in values for limit, offset, and order by
	//but it wouldn't be in the stored function.
	//it's a possibility to keep in mind.
	protected static function build_query($function_name, $keys = array()) //pass in array_keys of params //i feel like i should pass in not just the keys... why go through another time?
	{
		//loop through to get preparation string
		//named notation
		//https://www.postgresql.org/docs/current/static/sql-syntax-calling-funcs.html
		$str = 'SELECT * FROM ' . $function_name . '(';
		$l = count($keys);
		if($l > 0)
		{
			for($i = 0; $i < $l - 1; $i++)
			{
				$str .= $keys[$i] . ' => :' . $keys[$i] . ', ';
				//hmm... will i have to be careful about strings...?
			}
			$str .= $keys[$i] . ' => :' . $keys[$i];
		}
		$str .= ');';
		return $str;
	}

	public function is_valid() //todo: make sure types are correct
	{
		foreach (static::$types as $key => $value) {
			if(!array_key_exists($key, static::$hidden_props) && !array_key_exists($key, static::$db_hidden_props))
			{
				switch ($value) {
					case (Type::INTEGER):
						if (!is_int((int)$this->$key)) {
							return false;
						}
						break;
					case (Type::DOUBLE):
						if (!is_numeric($this->$key)) {
							return false;
						}
						break;
					case (Type::BOOLEAN):
						if (!is_bool($this->$key)) {
							return false;
						}
						break;
					case (Type::DATETIME):
						if (!validate_date($this->$key)) {
							return false;
						}
						break;
					case (Type::STRING):
					case (Type::CODE):
					case (Type::PASSWORD):
						if (!is_string($this->$key)) {
							echo 'failed here ' . $key;
							return false;
						}
						break;
					case (Type::EMAIL):
						if(!is_string($this->$key) || !filter_var($this->$key, FILTER_VALIDATE_EMAIL)) {
							return false;
						}
						break;
					default:
						if (Type::is_model($value)) {
							if (!(is_numeric($this->$key) && is_int($this->$key + 0))) {
								return false;
							}
						}
						elseif (Type::is_list_model($value)) {
							if (!(is_array($this->$key) || $this->$key == null)) //todo: also need to check if each key is an int
							{
								return false;
							}
						}
						else {
							return false;
						}
				}
			}
		}
		return true;
	}

	protected static function php_array_to_pg_array($t)
	{
		//$t is array to be escaped. $u will be string literal.
		$tv=array();
		foreach($t as $key=>$val){
			$tv[$key]="\"" .
			  str_replace("\"",'\\"', str_replace('\\','\\\\',$val)) . "\"
		";
		}
		$u= implode(",",$tv) ;
		$u="{" . pg_escape_string($u) . "}";
		return $u;
	}
}
?>
