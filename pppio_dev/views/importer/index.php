<?php
require_once('views/shared/html_helper.php');

$properties = array('input' => $input);
$types = array('input' => TYPE::CODE);

echo HtmlHelper::form($types, $properties);

if (isset($lessons)) {
	echo '<pre>';
	print_r($lessons);
	echo '</pre>';
}


?>