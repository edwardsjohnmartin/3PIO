function betterTab(cm) {
  if (cm.somethingSelected()) {
    cm.indentSelection("add");
  } else {
    cm.replaceSelection(cm.getOption("indentWithTabs")? "\t":
												Array(cm.getOption("indentUnit") + 1).join(" "),
												"end", "+input");
  }
}

var editor = CodeMirror.fromTextArea(document.getElementById("code"), {
  mode: {name: "python",
         version: 2,
         singleLineStringErrors: false},
  lineNumbers: true,
  indentUnit: 4,
  matchBrackets: true,
	//theme: "solarized dark"
	theme: "default",
	extraKeys: { Tab: betterTab }
});

document.getElementById("runButton").onclick = function () {
    clearAlerts();
    run();
};

//editor.on('copy', function(a, e) {e.preventDefault();});
editor.on('cut', function(a, e) {e.preventDefault();});
editor.on('paste', function(a, e) {e.preventDefault();});

// function outf(text) { 
//     var mypre = document.getElementById("output"); 
//     mypre.innerHTML = mypre.innerHTML + text; 
// } 
// function inf(prompt) {
// 	// Must copy the prompt string for some reason
//   return window.prompt(String(prompt));
// }

function builtinRead(x) {
    if (Sk.builtinFiles === undefined || Sk.builtinFiles["files"][x] === undefined)
            throw "File not found: '" + x + "'";
    return Sk.builtinFiles["files"][x];
}

function getTestCode() {
	// var pre = "";
	// var post = "";
	var pre = "\nimport sys\nclass Buffer:\n    def __init__(self):\n        self.str = \"\"\n    def write(self, txt):\n        self.str += str(txt)\nold_stdout = sys.stdout\nsys.stdout = test_std_out = Buffer()\n";
	var post = "sys.stdout = old_stdout";
	// We can examine test_std_out.str
	var code = pre + "\n" + document.getElementById('test_code_to_run').innerText + "\n" + post + "\n";
	// var code = document.getElementById('test_code_to_run').innerText;
	code = code.replace(/\n/g, "\n    ");
	code = "try:\n" + code + "\nexcept NameError as e:\n    __returns.append(str(e).split(\"'\")[1] + \" not defined.\")";
	return code;
}

function run() {
  var mod;
  // var program = editor.getValue() + "\n" + document.getElementById('test_code_to_run').innerText;
  var program = editor.getValue() + "\n" + getTestCode();
	// console.log(program);
  var outputArea = document.getElementById("output");
  
  outputArea.innerHTML = '';
  Sk.pre = "output";
  Sk.configure({output:outf, read:builtinRead,
								inputfun:inf, inputfunTakesPrompt:true});
  (Sk.TurtleGraphics || (Sk.TurtleGraphics = {})).target = 'mycanvas';
	var myPromise = Sk.misceval.asyncToPromise(function() {
        mod = Sk.importMainWithBody("<stdin>", false, program, true);
      return mod;

	});

	myPromise.then(function(mod) {
        var runMethod = mod.tp$getattr('__TEST');
		// console.log(String(Sk.builtin.str(outputArea.innerHTML)));
        var ret = Sk.misceval.callsim(runMethod, Sk.builtin.str(editor.getValue()), Sk.builtin.str(outputArea.innerHTML));
        //ret.v is an array of problems
		if(ret.v.length === 0 || ret.v[0].v === null)
		{
		//success
			if(trying_latest && !can_preview)
			{
				markAsComplete(exercise_id, lesson_id, concept_id);
			}
			else
			{
				completeExercise();
			}
		}
		else
		{
			//print errors
			for(var i = 0, l = ret.v.length; i<l; i++)
			{
				markError(ret.v[i].v);

			}
		}
    },
        function(err) {
          var line_num = Number(err.toString().split("on line", 2)[1]);
		if (err.args !== undefined) {
            if (err.args.v[0].v === "EOF in multi-line string") {
                markError("ERROR: It looks like you have an open multi-line comment.");
			}
			else {
                markError(err.toString());
			}
		}
		else {
			markError(err.toString());
		}
    });
  }

/*
		if(trying_latest)
		{
			markAsComplete(exercise_id, lesson_id, concept_id);
		}
		else
		{
			completeExercise();
		}
*/

function markAsComplete(exercise_id, lesson_id, concept_id) //i should only do this if i'm trying the latest!!
{
	$.ajax({
		method: "POST",
		url: "?controller=exercise&action=mark_as_completed",
		data: { id: exercise_id, lesson_id: lesson_id, concept_id: concept_id },
		success: function(data) {
			if(data.success)
			{
				completeExercise();
			}
			else
			{
				markError('Something went wrong.');
			}
		},
		error: function() { markError('Something went wrong.');　}
	});
}

function completeExercise()
{
	var successMessage = 'Good job! ';
	if(trying_last)
	{
		successMessage += '<a href="' + link + '" class="btn btn-success btn-sm"><span class="">Continue</span></a>';
	}
	else
	{
		successMessage += '<a href="' + link + '" class="btn btn-success btn-sm"><span class="">Next exercise</span></a>';

	}
	markSuccess(successMessage);
	if(trying_latest)
	{
		updateTiles();
	}
}

var codeAlerts = document.getElementById('codeAlerts');

function clearAlerts()
{
	codeAlerts.innerHTML = '';
}

function markError(errorMessage)
{
    if (typeof errorCount !== "undefined") {
        errorCount += 1;
    }
	codeAlerts.innerHTML += '<div class="alert alert-danger alert-dismissible mar-0" role="alert" id="infoAlert">' + errorMessage + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
}

function markSuccess(successMessage)
{
	//infoAlert.classList.remove('alert-danger');
	//infoAlert.classList.add('alert-success');
	//infoAlert.innerHTML = successMessage;
	codeAlerts.innerHTML += '<div class="alert alert-success alert-dismissible mar-0" role="alert" id="infoAlert">' + successMessage + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
}

function updateTiles()
{
			var current_tile = document.getElementById(current_tile_id);
			current_tile.classList.remove('btn-default');
			current_tile.classList.add('btn-success');

			if(!trying_last)
			{
				var next_tile = document.getElementById(next_tile_id);
				next_tile.innerHTML = '<span class="tile-number">' + next_index + '</span>';
				next_tile.classList.remove('disabled');
				next_tile.href = link;
			}
}
