<?php
require_once('models/model.php');
class Session extends Model{
	protected static $types = array('securable_id' => Type::INTEGER, 'activity_id' => Type::INTEGER, 'start_time' => Type::DATETIME, 'end_time' => Type::DATETIME, 'mouse_clicks' => Type::INTEGER, 'key_presses' => Type::INTEGER, 'times_ran' => Type::INTEGER);
	protected $securable_id;
	protected $activity_id;
	protected $start_time;
	protected $end_time;
	protected $mouse_clicks;
	protected $key_presses;
	protected $times_ran;
	protected $error_count;

	//Get all exercise, project, and question sessions for a single student
	public static function get_all_for_student($user_id){
		$db = Db::getReader();
		$user_id = intval($user_id);

		$function_name = 'sproc_read_session_for_student';
		$req = $db->prepare(static::build_query($function_name, array('user_id')));
		$req->execute(array('user_id' => $user_id));

		return $req->fetchALL(PDO::FETCH_CLASS, 'session');
	}

	//Get all sessions of a single type for a single student
	public static function get_all_of_type_for_student($securable_id, $user_id){
		$db = Db::getReader();
		$securable_id = intval($securable_id);
		$user_id = intval($user_id);

		$function_name = 'sproc_read_session_type_for_student';
		$req = $db->prepare(static::build_query($function_name, array('securable_id', 'user_id')));
		$req->execute(array('securable_id' => $securable_id, 'user_id' => $user_id));

		return $req->fetchALL(PDO::FETCH_CLASS, 'session');
	}

	//Save a session to the database
	public static function write_session($user_id, $securable_id, $activity_id, $start_time, $end_time, $mouse_clicks, $key_presses, $times_ran, $error_count, $partnered_session){
		$db = Db::getWriter();
		$function_name = 'sproc_write_session';

		if($partnered_session == false){
			$partnered_session = "f";
		}

		$req = $db->prepare(static::build_query($function_name,
			array('user_id', 'securable_id', 'activity_id', 'start_time',
			'end_time', 'mouse_clicks', 'key_presses', 'times_ran', 'error_count', 'partnered_session')));
		$req->execute
			(array('user_id' => $user_id, 'securable_id' => $securable_id,
			'activity_id' => $activity_id, 'start_time' => $start_time,
			'end_time' => $end_time, 'mouse_clicks' => $mouse_clicks,
			'key_presses' => $key_presses, 'times_ran' => $times_ran, 'error_count' => $error_count, 'partnered_session' => $partnered_session));
	}

	//Get the elapsed time of the session object in seconds (end - start)
	public function get_elapsed(){
		$start = strtotime($this->start_time);
		$end = strtotime($this->end_time);

		if($end - $start < 0){
			return 0;
		}
		else{
			return $end - $start;
		}
	}

	//Acts as a getter for any property name passed in
	public function get_prop($prop_name){
		if($prop_name == "session_length"){
			return $this->get_length($this->get_elapsed());
		}
		else if($prop_name == "elapsed"){
			return $this->get_elapsed();
		}
		else if($prop_name == "error_count"){
			if($this->error_count == -1){
				return 0;
			}
			else{
				return $this->$prop_name;
			}
		}
		else{
			return $this->$prop_name;
		}
	}

	//Returns a string of how long a session lasted (H:M:S)
	public static function get_length($elapsed){
		//$elapsed = $this->get_elapsed();

		$hours = floor($elapsed / 3600);
		$mins = floor($elapsed / 60 % 60);
		$secs = floor($elapsed % 60);

		return sprintf('%02d:%02d:%02d', $hours, $mins, $secs);
	}
}
?>
