<?php

function create_code_editor_view($params = array())
{
	if(count($params['buttons']) > 0)
	{
		$has_left_navbar = true;
	}
	else
	{
		$has_left_navbar = false;
		unset($params['left_title']);
	}

	$defaults = array(
		'title' => 'Default Title',
		'default_code' => '',
		'text_output_height_percent' => 50,
		'graphic_output_height_percent' => 50,
		'buttons' => null,
		'left_title' => '',
		'has_left_navbar' => $has_left_navbar,
		'dropdown_items' => null
	);
	$properties = array_merge($defaults, $params);

	echo '<link rel="stylesheet" href="css/editor.css">';
	require_once('views/shared/CodeMirror.php');
	require_once('views/shared/Skulpt.php');

?>
<div class="row height-100 overflow-hidden">
	<div class="col-xs-2 height-100 overflow-auto right-pad-7" style="display:<?php if($properties['has_left_navbar']){ echo 'block';}else{echo 'none';}?>">
		<div class="container-fluid right-pad-0">
			<h1 class="text-center">
				<?php echo $properties['left_title'];?>
			</h1>
			<div class="row">
				<?php
				foreach($properties['buttons'] as $b)
				{
					echo '<div class="col-xs-4 text-center left-pad-7 right-pad-7">';
						echo '<button type="button" id="' . $b->get_id() . '" class="tile btn btn-primary" onclick=" ' . $b->get_link() . ' ">';
							echo '<span class="tile-number">' . $b->get_text() . '</span>';
						echo '</button>';
					echo '</div>';
				}
				?>
			</div>
		</div>
	</div>

	<div class="<?php if($properties['has_left_navbar']){ echo 'col-xs-10';}else{echo 'col-xs-12 left-pad-30';}?> height-100 flex-columns">
		<div class="row no-shrink">
			<h1 class="text-center">
                <?php echo $properties['title'];?>
			</h1>
		</div>

		<div class="row no-shrink height-15 right-pad-15" style="display:<?php if(count($properties['dropdown_items'])){ echo 'block';}else{echo 'none';}?>">
			<div class="col-xs-2 right-pad-0 width-10 height-100">
				<div class="row dropdown centered-content top-bot-mar-7">
					<button id="btn_drop" class="dropbtn">
						Select Item
						<div>
							<span class="glyphicon glyphicon-chevron-down left-pad-7" aria-hidden="true"></span>
						</div>
					</button>
					<div class="dropdown-content">
						<?php
						foreach($properties['dropdown_items'] as $dd)
						{
							echo '<a id="' . $dd->get_id() . '" href="#" onclick="populate_dd_text(\'' . $dd->get_text() . '\', this); return false;">' . $dd->get_property_name() . '</a>';
						}
						?>
					</div>
				</div>
				<div class="row centered-content top-bot-mar-7">
					<input type="range" />
				</div>				
			</div>
			<div class="col-xs-10 height-100 width-90">
				<pre id="dd_pre" class="height-100"></pre>
			</div>
		</div>

        <div class="row no-shrink top-bot-mar-7 ">
            <div class="col-xs-2 right-pad-0">
                <button type="button" class="btn btn-default run-btn" id="runButton">
                    <span class="glyphicon glyphicon-play" aria-hidden="true"></span>
                </button>
				<button type="button" class="btn btn-default run-btn" id="moveCodeButton" onclick="moveCode()">
					<span class="glyphicon glyphicon-arrow-down" aria-hidden="true"></span>
				</button>
            </div>
			<div class="col-xs-10 width-80 left-pad-0">
				<div id="codeAlerts"></div>
			</div>
        </div>

		<div class="row overflow-hidden height-100">
			<div class="col-xs-6 height-100 overflow-hidden right-pad-7">
				<textarea id="code" name="code"></textarea>
			</div>
			<div class="col-xs-6 height-100 left-pad-7 right-pad-30">
				<div id="mycanvas" class="graphicalOutput height-50"></div>
				<div class="textOutput height-100">
					<pre id="output" class="height-50"></pre>
				</div>
			</div>
		</div>
	</div>
</div>

<?php
    echo '<script>var default_code = "' . $properties['default_code'] . '";</script>';
	echo '<script type="text/x-python" id="test_code_to_run">';
		require('py_test/METHODS.py');
	echo '</script>';
	echo '<script src="js/code_editor_view.js"></script>';
	echo '<script>setDefaultCode();</script>';
	echo '</div>';
}
?>