<?php
	require_once('models/model.php');
	class Role extends Model
	{
		protected static $types = array('id' => Type::INTEGER, 'name' => Type::STRING);
		protected $name;

		// later, we will probably want the permissions on the model for easier editing. right now, we are getting permissions separately. no way to edit permissions on the role at the moment, but the tables in the database are set up to allow it.

		public static function get_permissions_for_role($id)
		{
			$db = Db::getReader(); 
			$id = intval($id);

			$function_name = 'sproc_read_role_get_permissions_for_role';
			$req = $db->prepare(static::build_query($function_name, array('id')));
			$req->execute(array('id' => $id));


			$permissions = $req->fetchAll(PDO::FETCH_COLUMN|PDO::FETCH_GROUP);
			foreach($permissions as $securable_id => $permission_types)
			{
				$permissions[$securable_id] = array();
				foreach($permission_types as $type)
				{
					$permissions[$securable_id][$type] = true;
				}
				//$temp[$securable_id] = array_fill_keys($temp[$securable_id], true);
			}
			return $permissions; // $req->fetchAll(PDO::FETCH_BOTH); //probably i should have a key/value model or something.. right now just using array. trust.
		}
	}
?>
