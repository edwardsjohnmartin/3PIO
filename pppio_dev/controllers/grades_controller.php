<?php
require_once('controllers/base_controller.php');
class GradesController extends BaseController
{
	//Gives a list of all sections with at least 1 exam that the user access to
	public function index()
	{
		require_once('models/exam.php');

		//Show error if the user isn't the owner or ta of any sections
		if(!count($_SESSION['sections_owner']) > 0 and !count($_SESSION['sections_ta']) > 0){
		    add_alert("You do not have access to any sections.", Alert_Type::DANGER);
		    return call('pages', 'error');
		}
		else
		{
			$sections = array();
			foreach($_SESSION['sections_owner'] as $section_id => $section_name){
				$sections[$section_id] = $section_name;
			}
			foreach($_SESSION['sections_ta'] as $section_id => $section_name){
				$sections[$section_id] = $section_name;
			}

			$all_exams = array();
			foreach($sections as $section_id => $section_name){
				if(!array_key_exists($section_id, $all_exams)){
					$exams = Exam::get_pairs_for_section($section_id);
					if(count($exams) > 0){
						$all_exams[$section_id] = $exams;
					}
				}
			}

			if(!count($all_exams) > 0){
				add_alert("No sections you have access to has an exam.", Alert_Type::DANGER);
				return call('pages', 'error');
			}
			else{
				$view_to_show = 'views/grades/index.php';
				require_once('views/shared/layout.php');
			}
		}
	}

	public function get_section_grades(){
		if(!isset($_GET['id'])){
			add_alert("No section was selected to get grades for.", Alert_Type::DANGER);
			return call('pages', 'error');
		}

		require_once('models/section.php');
		require_once('models/exam.php');

		$user_id = $_SESSION['user']->get_id();
		$section_id = $_GET['id'];
		$section = Section::get($section_id);
		$is_owner = Section::is_owner($section_id, $user_id);
		$is_ta = Section::is_teaching_assistant($section_id, $user_id);

		if($is_ta or $is_owner){
			unset($user_id);
			unset($is_ta);
			unset($is_owner);

			$view_to_show = 'views/grades/section_grades.php';
			require_once('views/shared/layout.php');
		}
		else{
			add_alert("You do not have access to this section.", Alert_Type::DANGER);
			return call('pages', 'error');
		}
	}

	//Gets all scores for a specific exam
	public function get_exam_grades($exam_id){

	}

	public function get_exam_grade_for_student()
	{
		require_once('models/section.php');
		require_once('models/exam.php');

		if(!isset($_GET['exam_id'])){
			add_alert("No exam was selected to get grades for.", Alert_Type::DANGER);
			return call('pages', 'error');
		}

		$exam = Exam::get($_GET['exam_id']);
		if(empty(Exam::get($_GET['exam_id']))){
			add_alert("The exam you are trying to access doesn't exist.", Alert_Type::DANGER);
			return call('pages', 'error');
		}

		//User has to be the student whom the grades are for
		if(!Section::is_student($exam->get_section_id(), $_SESSION['user']->get_id())){
			add_alert("You do not have access to this section.", Alert_Type::DANGER);
			return call('pages', 'error');
		}

		$exam_id = $_GET['exam_id'];
		$exams = Exam::get_all_for_section($exam->get_section_id());
		$exam_props = $exam->get_properties();
		$exam_scores = Grades::get_exam_scores($exam_id);

		//TODO: Figure out a way to get the total weight here without this method so it can be deleted.
		$exam_weight = $exam->get_total_weight($exam_id);

		$view_to_show = 'views/grades/exam_grade_for_student.php';
		require_once('views/shared/layout.php');
	}
}
?>
