<?php 
//	this expects either a problem or a project.
//id, name, description, starter code
 $props = $exercise->get_properties();
?>
<link rel="stylesheet" href="css/editor.css">
<?php include_once('views/shared/CodeMirror.php'); ?>
<script src="js/skulpt/skulpt.min.js"></script>
<script src="js/skulpt/skulpt-stdlib.js"></script>

<div class="editor">
	<div class="c25">
		<h1>
			<?php echo $props['name'];?>
		</h1>
		<p>
			<?php echo $props['description'];?>
		</p>
	</div>
	<div class="c50"><!-- class="col-md-6"-->

		<textarea id="code" name="code"><?php echo $props['starter_code'];?></textarea>

	</div>
	<div class="c25">

		<button type="button" class="btn btn-default" id="runButton"><span class="glyphicon glyphicon-play" aria-hidden="true"></span><span class="sr-only">Run</span></button>


		<div id="mycanvas" class="graphicalOutput"></div>
		<pre id="output" ></pre>


	</div>
</div>

<script src="js/editor.js"></script>
