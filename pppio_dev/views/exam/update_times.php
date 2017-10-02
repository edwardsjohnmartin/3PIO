<?php
require_once('views/shared/html_helper.php');
require_once('models/section.php');

echo '<h2>' . $properties['name'] . '</h2>';
echo HtmlHelper::view($types, $properties);

$is_owner = section::is_owner($properties['section']->key, $_SESSION['user']->get_id());
$is_ta = section::is_teaching_assistant($properties['section']->key, $_SESSION['user']->get_id());

if($is_owner || $is_ta)
{
	$section = $properties['section']->key;
	$options = array('students' => Section::get($section)->get_properties()['students']);
	foreach($options['students'] as $k => $v)
	{
		$options['students'][$k] = $v->value;
	}
	$types = array('students' => Type::LIST_USER, 'start_time' => Type::DATETIME, 'close_time' => Type::DATETIME);
	$properties = array('students' => null, 'start_time' => null, 'close_time' => null);
	$student_times = exam::get_times($_GET['id']);

	echo '<h2>Exam Times</h2>';
	echo '<div class="force-x-scroll">';
	echo '<table class="table table-striped table-bordered">';
	echo '<thead>';
	echo '<tr>';
	echo '<th>Student</th>';
	echo '<th>Start Time</th>';
	echo '<th>Close Time</th>';
	echo '</tr>';
	echo '</thead>';
	echo '<tbody>';
	$now = intval(date_format(new DateTime(), 'U'));
	foreach($student_times as $key => $value)
	{
		$start = date_create_from_format('Y-m-d H:i:s', $value->start_time);
		$start_seconds = intval(date_format($start, 'U'));
		$close = date_create_from_format('Y-m-d H:i:s', $value->close_time);
		$close_seconds = intval(date_format($close, 'U'));
		$in_time_range = $start_seconds < $now && $now < $close_seconds;
		echo '<tr>';
		echo '<th>' . htmlspecialchars($value->name) . '</th>';
		echo '<td';
		if($in_time_range)
		{
			echo ' class="success"';
		}
		else
		{
			echo ' class="warning"';
		}
		echo '>' . date_format($start, 'm/d/Y g:i A') . '</td>';
		echo '<td';
		if($in_time_range)
		{
			echo ' class="success"';
		}
		else
		{
			echo ' class="warning"';
		}
		echo '>' . date_format($close, 'm/d/Y g:i A') . '</td>';
		echo '</tr>';
	}
	echo '</tbody>';
	echo '</table>';
	echo '</div>';

	echo '<h2>Update Exam Times</h2>';
	if(!isset($options)) $options = null;

	echo HtmlHelper::form($types, $properties, null, $options);

	//this should only be true after the submit button was pressed to update times
	if($_SERVER['REQUEST_METHOD'] === 'POST')
	{
		$postedToken = filter_input(INPUT_POST, 'token');
		if(!empty($postedToken) && isTokenValid($postedToken))
		{
			$times = array('students' => $_POST['students'], 'exam_id' => $_GET['id'] , 'start_time' => $_POST['start_time'], 'close_time' => $_POST['close_time']);
			if(!isset($times['students']) || !is_valid_date($times['start_time']) || !is_valid_date($times['close_time']))
			//if(!isset($times['students']) || $times['start_time'] == "" || $times['close_time'] == "")
			{
				redirect('exam', 'update_times');
				add_alert('Please try again.', Alert_Type::DANGER);
			}
			else
			{
				$model->update_times($times);
			}
		}
	}
}
//a user who isn't the owner of the exam or a ta for the section is trying to access this exam
else
{
	return call('pages', 'error');
}

function is_valid_date($date, $format = 'm/d/Y g:i A')
{
	return date($format, strtotime($date)) == $date;
}
?>