	<?php
	//i should use try/catches...
	abstract class Model
	{
		//hidden, db hidden, types. static.
		//or rather, shown and db allowed
		const MAX_BIGINT = 9223372036854775807; //this really should go somewhere else probably. should be global...
		protected static $hidden_props = array('id' => true, 'hidden_props' => true, 'db_hidden_props' => true, 'types' => true);//protected?
		protected static $db_hidden_props = array('id' => true, 'hidden_props' => true, 'db_hidden_props' => true, 'types' => true); //ooo... this will be a lot to copy if that stuff doesn't work out...
		protected static $types = array('id' => Type::INTEGER); //what will i do... the children need to set this one for sure. should this be static or constant? i'm just setting it over anyway in the class, for now.

		//hm maybe i should force types in my get/set, idk
		protected $id = null;

		public function get_id()
		{
			return $this->id;
		}

		public function set_id($id)
		{
			$this->id = intval($id); //intval?
		}
		
		public function __construct() //use optional parameters to allow to use constructor when getting from db
		{
			//this is probably very inefficient.. but it's convenient... what to do
			//i really should take care of this elsewhere. leave for now.
			//namely the db fill (set from json) and set properties (set int key)
			foreach(static::$types as $prop => $type)
			{
				if($type > Type::MODEL)
				{
					if(!is_int($this->$prop)) //well...
					{
						$this->$prop = json_decode($this->$prop); //convert the json props to array. i'd like to not have to do this in these child classes. just do them in the base classes somehow. right now i have the model name as the type... but how do i know it's a model?
					}
					else
					{
						$value = $this->$prop;
						$this->$prop = new key_value_pair();
						$this->$prop->key = $value;
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
			//each arg...
			foreach($args as $key => $value)
			{
				if(property_exists($this, $key) && (!isset(static::$hidden_props[$key]) || !static::$hidden_props[$key])) //i could also check if it's in the types
				{
					$this->$key = $value;
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
					$ret_props[$key] = $this->$key; //something like that...
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
		public static function all()
		{
			$model_name = static::class;

			$db = Db::getReader(); 

			$function_name = 'sproc_read_' . $model_name . '_get_all';
			$req = $db->query(static::build_query($function_name));

			return $req->fetchAll(PDO::FETCH_CLASS, $model_name);

			//$result = $req->fetchAll(PDO::FETCH_CLASS, $model_name);
			//print_r($result);
			//return $result;

		}

		//this seems like abuse
		public static function pairs()
		{
			$model_name = static::class;

			$db = Db::getReader(); 

			$function_name = 'sproc_read_' . $model_name . '_get_pairs';
			$req = $db->query(static::build_query($function_name));

			require_once('models/key_value_pair.php');
			return $req->fetchAll(PDO::FETCH_CLASS, 'key_value_pair'); // $req->fetchAll(PDO::FETCH_BOTH); //probably i should have a key/value model or something.. right now just using array. trust.

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
			$model_name = static::class;

			$db = Db::getWriter();

			$props = $this->get_db_properties();
			$props['id'] = $this->id; //use id getter? probably should for consistency, probably shouldn't for speed, and i can trust my own self (class)
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
		private static function build_query($function_name, $keys = array()) //pass in array_keys of params //i feel like i should pass in not just the keys... why go through another time?
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

		public function is_valid() //or should i make abstract and force user to define(?) and where should the check happen? before trying to go to db? or outside?
		{
			//ooh i should check if the types are correct here?
			return true;
		}
	}
?>