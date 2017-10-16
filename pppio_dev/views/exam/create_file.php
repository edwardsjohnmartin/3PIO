<?php
require_once('views/shared/html_helper.php');
if($success)
{
	echo '<h2>Exam and questions created:</h2>';
	foreach($exams as $exam)
	{
		$exam_props = $exam->get_properties();
		echo '<div class="panel panel-default">';
		echo '<div class="panel-heading"><a href="?controller=exam&action=read&id=' . $exam->get_id() .'" target="_blank">' . htmlspecialchars($exam_props['name']) . '</a></div>';
		//validate...
		foreach($exam_props['questions'] as $question)
		{
			echo '<a href="?controller=question&action=read&id=' . $question->get_id() .'" class="list-group-item" target="_blank">' . htmlspecialchars($question->get_properties()['instructions']) . '</a>';
		}
		echo '</div>';
	}
	echo '<a href="?controller=' . $this->model_name . '&action=index" class="btn btn-primary">Return to index</a>';
}
else
{
	echo $this->model_name;
	$properties = array('file' => null);
	$types = array('file' => TYPE::FILE);
	echo HtmlHelper::form($types, $properties);
}
?>



