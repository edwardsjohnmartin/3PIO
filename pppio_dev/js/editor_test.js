var editor = CodeMirror.fromTextArea(document.getElementById("code"), {
    mode: {name: "python",
           version: 3,
           singleLineStringErrors: false},
    lineNumbers: true,
    indentUnit: 4,
    matchBrackets: true,
	//theme: "solarized dark"
	theme: "default"
});

document.getElementById("runButton").onclick = function() { clearAlerts(); run(); };

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
  var mod;
  var program = editor.getValue() + "\n" + document.getElementById('test_code_to_run').innerText;
  var outputArea = document.getElementById("output")
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
        var ret = Sk.misceval.callsim(runMethod, Sk.builtin.str(editor.getValue()), Sk.builtin.str(outputArea.innerHTML));
        //ret.v is an array of problems
		if(ret.v.length == 0 || ret.v[0].v == null)
		{
		//success
			markSuccess('Success!');
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
		if (err.args != undefined) {
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

function clearAlerts()
{
	codeAlerts.innerHTML = '';
}
function markError(errorMessage)
{
	//infoAlert.classList.remove('alert-success');
	//infoAlert.classList.add('alert-danger');
	//infoAlert.innerHTML = errorMessage;
	codeAlerts.innerHTML += '<div class="alert alert-danger alert-dismissible mar-0" role="alert" id="infoAlert">' + errorMessage + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
}

function markSuccess(successMessage)
{
	//infoAlert.classList.remove('alert-danger');
	//infoAlert.classList.add('alert-success');
	//infoAlert.innerHTML = successMessage;
	codeAlerts.innerHTML += '<div class="alert alert-success alert-dismissible mar-0" role="alert" id="infoAlert">' + successMessage + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
}
