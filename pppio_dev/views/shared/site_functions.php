<?php

// This will create a view with a code editor based around the parameters passed in. Any parameter not passed in will be set to a default.
function create_code_editor_view($params = array())
{
	if(count($params['buttons']) > 0){
		$has_left_navbar = true;
	}
	else
	{
		$has_left_navbar = false;
		unset($params['left_title']);
	}

	// Default values
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
	<!--Left Nav Column (Only Shown When Buttons Are Passed In)-->
	<div class="col-xs-2 height-100 overflow-auto right-pad-7" style="display:<?php if($properties['has_left_navbar']){ echo 'block';}else{echo 'none';}?>">
		<div class="container-fluid right-pad-0">
			<!--Left Title Display-->
			<h1 class="text-center">
				<?php echo $properties['left_title'];?>
			</h1>

			<!--Buttons-->
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

	<!--Right Column, Contains All Base Elements-->
	<div class="<?php if($properties['has_left_navbar']){ echo 'col-xs-10';}else{echo 'col-xs-12 left-pad-30';}?> height-100 flex-columns">
		<!--Title Row-->
		<div class="row no-shrink">
			<h1 class="text-center">
                <?php echo $properties['title'];?>
			</h1>
		</div>

		<!--Dropdown Menu, Information Textbox, Text Output/Graphics Output Slider-->
		<div class="row no-shrink height-15 right-pad-15" style="display:<?php if(count($properties['dropdown_items'])){ echo 'block';}else{echo 'none';}?>">
			<!--Dropdown and Slider-->
			<div class="col-xs-2 right-pad-0 width-10 height-100">
				<!--Dropdown-->
				<div class="row dropdown centered-content top-bot-mar-7">
					<button id="btn_drop" class="dropbtn">
						Select Item
						<div>
							<span class="glyphicon glyphicon-chevron-down left-pad-7" aria-hidden="true"></span>
						</div>
					</button>

					<!--Links To Represent Dropdown Items-->
					<div class="dropdown-content">
						<?php
						foreach($properties['dropdown_items'] as $dd)
						{
							echo '<a id="' . $dd->get_id() . '" href="#" onclick="setInformationTextbox(\'' . $dd->get_text() . '\', this.id); return false;">' . $dd->get_property_name() . '</a>';
						}
						?>
					</div>
				</div>

				<!--Slider-->
				<div class="row centered-content top-bot-mar-7">
					<input type="range" min="0" max="98" onchange="resizeOutputAreas(this.value)"/>
				</div>				
			</div>

			<!--Information Textbox-->
			<div class="col-xs-10 height-100 width-90">
				<pre id="txtInfo" class="height-100"></pre>
			</div>
		</div>

		<!--Action Buttons, Alert Bar-->
        <div class="row no-shrink top-bot-mar-7 ">
			<!--Action Buttons-->
            <div class="col-xs-2 right-pad-0">
				<!--Run Button-->
                <button type="button" class="btn btn-default run-btn" id="runButton">
                    <span class="glyphicon glyphicon-play" aria-hidden="true"></span>
                </button>

				<!--Move Code-->
				<button type="button" class="btn btn-default run-btn" id="moveCodeButton" onclick="moveCode()">
					<span class="glyphicon glyphicon-arrow-down" aria-hidden="true"></span>
				</button>
            </div>

			<!--Alert Bar-->
			<div class="col-xs-10 width-80 left-pad-0">
				<div id="codeAlerts"></div>
			</div>
        </div>

		<!--Editor Code Area, Text/Graphics Output Area-->
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