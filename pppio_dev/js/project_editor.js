function betterTab(cm) {
    if (cm.somethingSelected()) {
        cm.indentSelection("add");
    } else {
        cm.replaceSelection(cm.getOption("indentWithTabs") ? "\t" :
            Array(cm.getOption("indentUnit") + 1).join(" "),
            "end", "+input");
    }
}

var editor = CodeMirror.fromTextArea(document.getElementById("code"), {
    mode: {
        name: "python",
        version: 2,
        singleLineStringErrors: false
    },
    lineNumbers: true,
    indentUnit: 4,
    matchBrackets: true,
    theme: "default",
    extraKeys: { Tab: betterTab }
});

document.getElementById("runButton").onclick = function () {
    clearAlerts();
	/*Replaces any tab characters in the code area as 4 spaces. If code was pasted in from a 
    source where tabs are a different amount of spaces, it will cause indentation 
    errors. This prevents the issue.*/
	editor.setValue(editor.getValue().replace(/\t/g, '    '));
    if (!readonly) {
        save(concept_id, editor.getValue());
    } 
	run();
};

/*
uncomment to disable copy/paste for projects
editor.on('copy', function(a, e) {e.preventDefault();});
editor.on('cut', function(a, e) {e.preventDefault();});
editor.on('paste', function(a, e) {e.preventDefault();});
*/

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

function run() {
    var program = editor.getValue() + '\n';
    var outputArea = document.getElementById("output");
    outputArea.innerHTML = '';
    Sk.pre = "output";
    Sk.configure({
        output: outf,
        read: builtinRead,
        inputfun: inf,
        inputfunTakesPrompt: true
    });
    (Sk.TurtleGraphics || (Sk.TurtleGraphics = {})).target = 'mycanvas';
    var myPromise = Sk.misceval.asyncToPromise(function () {
        return Sk.importMainWithBody("<stdin>", false, program, true);
    });
    myPromise.then(function (mod) { },
        function (err) {
            markError(err.toString());
        });

    //Delays the function that creates the drawing area by 750ms to allow the other elements to be created by the time this code runs
    //This will get the calculate the size the text output area needs to be by getting the total height of the right column and subtracting
    //the size of the drawing area
    setTimeout(function () {
        var canvas = document.getElementById("mycanvas");
        var canHeight = canvas.offsetHeight;
        var canParHeight = canvas.parentNode.clientHeight;
        outputArea.style.height = canParHeight - canHeight + "px";
    }, 750);
}

function save(concept_id, contents) {
    $.ajax({
        method: "POST",
        url: "?controller=project&action=save_code",
        data: { concept_id: concept_id, contents: contents },
        success: function (data) {
            if (data.success) {
                markSuccess('Code saved.');
            }
            else {
                markError('Unable to save code.');
            }
        },
        error: function () { markError('Unable to save code.'); 　}
    });
}

var codeAlerts = document.getElementById('codeAlerts');

function clearAlerts() {
    codeAlerts.innerHTML = '';
}

function markError(errorMessage) {
    codeAlerts.innerHTML += '<div class="alert alert-danger alert-dismissible mar-0" role="alert" id="infoAlert">' + errorMessage + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
}

function markSuccess(successMessage) {
    codeAlerts.innerHTML += '<div class="alert alert-success alert-dismissible mar-0" role="alert" id="infoAlert">' + successMessage + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
}

