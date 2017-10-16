<?php
	require_once('models/model.php');
	class User extends Model
	{
		protected static $types = array('id' => Type::INTEGER, 'name' => Type::STRING, 'email' => Type::EMAIL, 'password' => Type::PASSWORD, 'role' => Type::ROLE); //use the enum
		protected $email;
		protected $name;
		protected $role;
		protected $password; //the return ones will never get this. but it's needed for the create... hmm.

		public function get_grades_for_exam($user_id, $exam_id)
		{
			$db = Db::getReader();
			$user_id = intval($user_id);
			$exam_id = intval($exam_id);

			$function_name = 'sproc_read_user_get_grades_for_exam';
			$req = $db->prepare(static::build_query($function_name, array('user_id', 'exam_id')));
			$req->execute(array('user_id' => $user_id, 'exam_id' => $exam_id));
			return $req->fetchAll(PDO::FETCH_CLASS);
		}

		public function get_for_login($email, $password)
		{
			$db = Db::getReader();
			$function_name = 'sproc_read_user_get_for_login';
			$req = $db->prepare(static::build_query($function_name, array('email', 'password')));
			$req->execute(array('email' => $email, 'password' => $password));

			$req->setFetchMode(PDO::FETCH_CLASS, 'user');
			return $req->fetch(PDO::FETCH_CLASS);
		}

		public function email_is_available($email, $id = null)
		{
			$db = Db::getReader();
			$function_name = 'sproc_read_user_email_is_available';
			$keys = array('email');
			$params = array('email' => $email);
			if($id != null)
			{
				$keys[] = 'id';
				$params['id'] = $id;
			}

			$req = $db->prepare(static::build_query($function_name, $keys));
			$req->execute($params);

			return $req->fetch(PDO::FETCH_COLUMN);
		}

		public function create() //sproc_write_user_create(email text, name text, password text, role_id int)
		{
			$model_name = static::class;
			$db = Db::getWriter();

			$props = $this->get_db_properties();

			$function_name = 'sproc_write_user_create';
			$req = $db->prepare(static::build_query($function_name, array_keys($props)));
			$req->execute($props);

			$this->set_id($req->fetchColumn());
			$this->password = null;
		}

		public function update() //sproc_write_user_create(email text, name text, password text, role_id int)
		{
			$model_name = static::class;
			$db = Db::getWriter();

			$props = $this->get_db_properties();
			$props['id'] = $this->id;
			unset($props['password']);

			$function_name = 'sproc_write_user_update';
			$req = $db->prepare(static::build_query($function_name, array_keys($props)));
			$req->execute($props);

			$this->set_id($req->fetchColumn());
			$this->password = null;
		}
	}
?>
