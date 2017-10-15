<?php
if(count($exams) > 0)
{
	echo '<h1>Exams</h1>';
	echo '<div class="force-x-scroll">';
	echo '<table class="table table-striped table-bordered">';
	echo '<thead>';
	echo '<tr>';
	echo '<th>Exam Name</th>';
	echo '<th>Start Time</th>';
	echo '<th>Close Time</th>';
	echo '</tr>';
	echo '</thead>';
	echo '<tbody>';
	$now = intval(date_format(new DateTime(), 'U'));
	foreach($exams as $key => $value)
	{
		$exam = exam::get_for_student($value[id]);
		$exam_props = $exam->get_properties();
		$first_question_id = reset($exam_props['questions'])->key;
		$start = date_create_from_format('Y-m-d H:i:s', $value['start_time']);
		$start_seconds = intval(date_format($start, 'U'));
		$close = date_create_from_format('Y-m-d H:i:s', $value['close_time']);
		$close_seconds = intval(date_format($close, 'U'));
		if($start_seconds < $now && $now < $close_seconds)
		{

			$class = ' class="success">';
			$link = '<a href="?controller=question&action=read_for_student&id=' . $first_question_id . '&exam_id=' . $exam->get_id() . '">' . htmlspecialchars($value['name']).'</a>';
		}
		else
		{
			$class = ' class="warning">';
			$link = $value['name'];
		}
		echo '<tr>';
		echo '<td ' . $class . $link . '</td>';
		echo '<td ' . $class . $value['start_time'] . '</td>';
		echo '<td ' . $class . $value['close_time'] . '</td>';
		echo '</tr>';
	}
	echo '</tbody>';
	echo '</table>';
	echo '</div>';
}
?>
