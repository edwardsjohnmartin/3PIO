var methods; 		  //This will be used to store the string containing the methods the TEST code will use. It will be appended to the student's code.
var test;             //This stores the TEST code that will be appended to the student's code.

    var editor = CodeMirror.fromTextArea(document.getElementById("code"), {
        mode: {name: "python",
               version: 2,
               singleLineStringErrors: false},
        lineNumbers: true,
        indentUnit: 4,
        matchBrackets: true,
		theme: "solarized dark"
    });

	document.getElementById("runButton").onclick = run;
	
  window.onload = function() {
        var fileInput = document.getElementById('fileInput');
        var doc = document.getElementById('output');
        fileInput.addEventListener('change', function(e) {
            var file = fileInput.files[0];
            var textType = /text.*/;

            if (file.type.match(textType)) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    test = reader.result;
					mod = Sk.importMainWithBody("<stdin>", false, test, true);
                    var runMethod = mod.tp$getattr('GET_PROMPT');
                    var ret = Sk.misceval.callsim(runMethod);
                    document.getElementById('prompt').innerHTML = ret.v;

                }
                reader.readAsText(file);
            } else {
                alert("File not supported!")
            }
        });
  }
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
  var mod;
  var program = editor.getValue() + "\n" + test;
  var outputArea = document.getElementById("output")
  outputArea.innerHTML = '';
  Sk.pre = "output";
    Sk.configure({output:outf, read:builtinRead});
  (Sk.TurtleGraphics || (Sk.TurtleGraphics = {})).target = 'mycanvas';
   var myPromise = Sk.misceval.asyncToPromise(function() {
      mod = Sk.importMainWithBody("<stdin>", false, program, true);
      return mod;
   });

    myPromise.then(function(mod) {
        console.log('success');
        var runMethod = mod.tp$getattr('TEST');
        var ret = Sk.misceval.callsim(runMethod, Sk.builtin.str(editor.getValue()), Sk.builtin.str(outputArea.innerHTML));
        alert(ret.v);
    },
        function(err) {
          var line_num = Number(err.toString().split("on line", 2)[1]);
		if (err.args != undefined)
		{
			if (err.args.v[0].v === "EOF in multi-line string") {
          alert("ERROR: It looks like you have an open multi-line string.")
			}
			else {
          alert(err.toString());
			}
		}
		else
		{
			alert(err.toString());
		}
    });
  }
