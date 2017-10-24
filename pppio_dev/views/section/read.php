
<?php
require_once('views/shared/html_helper.php');
echo '<h2>' . $this->model_name . '</h2>';

$is_teacher = Section::is_owner($_GET['id'], $_SESSION['user']->get_id());
$is_ta = Section::is_teaching_assistant($_GET['id'], $_SESSION['user']->get_id());

//if ta or teacher
//   show exams at the bottom
//      each exam will be a link to update_times

if($is_teacher or $is_ta)
{
	require_once('models/exam.php');
	$exams = Exam::get_all_for_section($_GET['id']);

	if(!empty($exams))
	{
		$exam_ret = array();
		foreach($exams as $exam)
		{
			$generic = new stdClass;
			$generic->key = $exam['id'];
			$generic->value = $exam['name'];
			$exam_ret[$generic->key] = $generic;
		}
		$types['exams'] = Type::LIST_EXAM;
		$properties['exams'] = $exam_ret;
	}
}

echo HtmlHelper::view($types, $properties);

if(has_permission(new Permission(constant('Securable::' . strtoupper($this->model_name)), Permission_Type::EDIT)))
{
	echo '<a href="?controller=' . $this->model_name . '&action=update&id=' . $model->get_id() . '" class="btn btn-primary">Update</a><br>';
}
?>
