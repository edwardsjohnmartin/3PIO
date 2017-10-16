
<?php
require_once('views/shared/html_helper.php');
echo '<h2>' . $this->model_name . '</h2>';
echo HtmlHelper::view($types, $properties);

if(has_permission(new Permission(constant('Securable::' . strtoupper($this->model_name)), Permission_Type::EDIT)))
{
	echo '<a href="?controller=' . $this->model_name . '&action=update&id=' . $model->get_id() . '" class="btn btn-primary">Update</a><br>';
}
?>

