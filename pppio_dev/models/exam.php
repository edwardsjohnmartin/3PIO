<?php
	require_once('models/model.php');
	class Exam extends Model
	{
        protected static $types = array('id' => Type::INTEGER, 'name' => Type::STRING ,'instructions' => Type::STRING, 'owner' => Type::USER, 'section' => Type::SECTION);
        protected $name = '';
        protected $instructions = '';
        protected $owner;
        protected $section;

		public static function get_pairs_for_owner($owner_id)
		{
			$db = Db::getReader();
			$owner_id = intval($owner_id);

			$function_name = 'sproc_read_exam_get_pairs_for_owner';
			$req = $db->prepare(static::build_query($function_name, array('owner_id')));
			$req->execute(array('owner_id' => $owner_id));

			return $req->fetchAll(PDO::FETCH_KEY_PAIR); // $req->fetchAll(PDO::FETCH_BOTH); //probably i should have a key/value model or something.. right now just using array. trust.
		}
    }
?>