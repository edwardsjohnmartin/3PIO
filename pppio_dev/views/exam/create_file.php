<?php
require_once('views/shared/html_helper.php');
require_once('views/shared/MultiSelect.php');

if($success)
{
	echo '<h2>Exam and questions created:</h2>';
	foreach($exams as $exam)
	{
		$exam_props = $exam->get_properties();
		echo '<div class="panel panel-default">';
		echo '<div class="panel-heading"><a href="?controller=exam&action=update_times&id=' . $exam->get_id() . '">' . htmlspecialchars($exam_props['name']) . '</a></div>';
		//validate...
		foreach($exam_props['questions'] as $question)
		{
			echo '<a href="?controller=question&action=read&id=' . $question->get_id() . '" class="list-group-item">' . htmlspecialchars($question->get_properties()['instructions']) . '</a>';
		}
		echo '</div>';
	}
	echo '<a href="?controller=' . $this->model_name . '&action=index" class="btn btn-primary">Return to index</a>';
}
else
{
	echo $this->model_name;

	$sections_list = Section::get_pairs();

	$properties = array('file' => null, 'section' => $sections_list);
	$types = array('file' => TYPE::FILE, 'section' => Type::SECTION);
	echo HtmlHelper::form($types, $properties);
}
?>



