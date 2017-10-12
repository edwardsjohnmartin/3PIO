<?php
require_once('controllers/base_controller.php');
class GradesController extends BaseController
{
	public function index()
	{
		$model = grades::get();
		$view_to_show = 'views/shared/index.php';
		require_once('views/shared/layout.php');
	}
}
?>
