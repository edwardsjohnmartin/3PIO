<a href="<?php echo '/?controller=' . $this->model_name . '&action=create';?>" class="btn btn-primary">Create</a><br>

<table class="table table-striped table-bordered">
<?php
//i am expecting $models to be defined!!!!!
//where do i store things like how many pages there should be?
//
foreach($models as $model)
{
?>
<tr>
	<td>
		<?php echo htmlspecialchars($model->value); ?>
	</td>
	<td>
		<a href="<?php echo '/?controller=' . $this->model_name . '&action=read&id=' . $model->key;?>">View</a><br>
	</td>
	<td>
		<a href="<?php echo '/?controller=' . $this->model_name . '&action=update&id=' . $model->key;?>">Update</a><br>
	</td>
</tr>
<?php
}
?>
</table>
