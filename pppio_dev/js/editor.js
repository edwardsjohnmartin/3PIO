var editor = CodeMirror.fromTextArea(document.getElementById("code"), {
    mode: {name: "python",
           version: 3,
           singleLineStringErrors: false},
    lineNumbers: true,
    indentUnit: 4,
    matchBrackets: true,
	theme: "solarized dark"
});

document.getElementById("runButton").onclick = run;

function outf(text) { 
    var mypre = document.getElementById("output"); 
    mypre.innerHTML = mypre.innerHTML + text; 
} 
function builtinRead(x) {
    if (Sk.builtinFiles === undefined || Sk.builtinFiles["files"][x] === undefined)
            throw "File not found: '" + x + "'";
    return Sk.builtinFiles["files"][x];
}

function run() {
	var program = editor.getValue();
	var outputArea = document.getElementById("output");
	outputArea.innerHTML = '';
	Sk.pre = "output";
    Sk.configure({output:outf, read:builtinRead});
	(Sk.TurtleGraphics || (Sk.TurtleGraphics = {})).target = 'mycanvas';
   var myPromise = Sk.misceval.asyncToPromise(function() {
       return Sk.importMainWithBody("<stdin>", false, program, true);
   });
   myPromise.then(function(mod) {
       console.log('success');
		if(trying_latest)
		{
			markAsComplete(exercise_id, lesson_id, concept_id);
		}
		else
		{
			completeExercise();
		}
   },
       function(err) {
       console.log(err.toString());
   });
}

function markAsComplete(exercise_id, lesson_id, concept_id) //i should only do this if i'm trying the latest!!
{
	$.ajax({
		method: "POST",
		url: "/?controller=exercise&action=mark_as_completed",
		data: { id: exercise_id, lesson_id: lesson_id, concept_id: concept_id },
		success: function(data) {
			if(data.success)
			{
				completeExercise();
			}
			else
			{
				markError();
			}
		}
	});
}

function completeExercise()
{
	infoAlert.classList.remove('alert-danger');
	infoAlert.classList.add('alert-success');
	infoAlert.innerHTML = 'Good job! ';
	if(trying_last)
	{
		infoAlert.innerHTML += '<a href="' + link + '" class="btn btn-success btn-sm"><span class="">Continue</span></a>';
	}
	else
	{
		infoAlert.innerHTML += '<a href="' + link + '" class="btn btn-success btn-sm"><span class="">Next exercise</span></a>';

	}
	if(trying_latest)
	{
		updateTiles();
	}
}

function markError()
{
	infoAlert.classList.remove('alert-success');
	infoAlert.classList.add('alert-danger');
	infoAlert.innerHTML = 'Something went wrong.';
}

function updateTiles()
{
			var current_tile = document.getElementById('exercise-' + exercise_id);
			current_tile.classList.remove('btn-default');
			current_tile.classList.add('btn-success');

			if(!trying_last)
			{
				var next_tile = document.getElementById('exercise-' + next_exercise_id);
				next_tile.innerHTML = '<span>' + next_index + '</span>';
				next_tile.classList.remove('disabled');
				next_tile.href = link;
			}
}

