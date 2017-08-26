<?php

echo '<link rel="stylesheet" href="css/editor.css">';
require_once('views/shared/CodeMirror.php');
require_once('views/shared/Skulpt.php');

echo '<div class="col-xs-12 height-100 flex-columns">';

echo '<div class="row no-shrink">
		<div class="col-xs-12">
			<h3>Sandbox Mode</h3>'; 
			echo '<p id="prompt">Try out code here to see what it will do. Don\'t be afraid to try new things. This code won\'t be saved.</p>
		</div>
	</div>';

echo '<div class="row no-shrink navbar-default navbar-form navbar-left">
					<button type="button" class="btn btn-default" id="runButton"><span class="glyphicon glyphicon-play" aria-hidden="true"></span><span class="sr-only">Run</span></button>';
			//<span>Choose a test file:</span><input type="file"  class="form-control" id="fileInput">
            echo '</div>
            <div class="row no-shrink"> <!--this alert needs to be filled with the error, or the next button-->
            <div class="col-xs-12 pad-0">
                <div id="codeAlerts"></div>
            </div>
        </div>
			<div class="row overflow-hidden height-100">
				<div class="col-xs-6 height-100 overflow-hidden pad-0">
					<textarea id="code" name="code"></textarea>
				</div>
				<div class="col-xs-6 height-100">

					<div id="mycanvas" class="graphicalOutput"></div>
                    <div class="textOutput">
      				  <pre id="output"></pre>
                    </div>

				</div>
			</div>
			';

echo '</div></div>';

echo '<script src="js/run_only.js"></script>';
echo '<script src="js/key-handler.js"></script>';
?>
