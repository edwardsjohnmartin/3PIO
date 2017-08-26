var editor = CodeMirror.fromTextArea(document.getElementById("code"), {
    mode: {name: "python",
           version: 2,
           singleLineStringErrors: false},
    lineNumbers: true,
    indentUnit: 4,
    matchBrackets: true,
	theme: "solarized dark"
});

document.getElementById("runButton").onclick = function() { run(); };

/*
uncomment to disable copy/paste for projects
editor.on('copy', function(a, e) {e.preventDefault();});
editor.on('cut', function(a, e) {e.preventDefault();});
editor.on('paste', function(a, e) {e.preventDefault();});
*/

function outf(text) { 
    var mypre = document.getElementById("output"); 
    mypre.innerHTML = mypre.innerHTML + text; 
} 
function inf(prompt) {
	// Must copy the prompt string for some reason
  return window.prompt(String(prompt));
}
function builtinRead(x) {
    if (Sk.builtinFiles === undefined || Sk.builtinFiles["files"][x] === undefined)
            throw "File not found: '" + x + "'";
    return Sk.builtinFiles["files"][x];
}

function run() {
	var program = editor.getValue() + '\n';
	var outputArea = document.getElementById("output");
	outputArea.innerHTML = '';
	Sk.pre = "output";
    // Sk.configure({output:outf, read:builtinRead});
  Sk.configure({output:outf, read:builtinRead,
								inputfun:inf, inputfunTakesPrompt:true});
	(Sk.TurtleGraphics || (Sk.TurtleGraphics = {})).target = 'mycanvas';
   var myPromise = Sk.misceval.asyncToPromise(function() {
       return Sk.importMainWithBody("<stdin>", false, program, true);
   });
   myPromise.then(function(mod) {},
       function(err) {
       markError(err.toString());
   });
}
function markError(errorMessage)
{
	infoAlert.classList.remove('alert-success');
	infoAlert.classList.add('alert-danger');
	infoAlert.innerHTML = errorMessage;
}

function markSuccess(successMessage)
{
	infoAlert.classList.remove('alert-danger');
	infoAlert.classList.add('alert-success');
	infoAlert.innerHTML = successMessage;
}

