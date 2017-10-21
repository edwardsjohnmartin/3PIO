var codeAlerts = document.getElementById('codeAlerts');

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
    var curPos = editor.getDoc().getCursor();
    editor.setValue(editor.getValue().replace(/\t/g, '    '));
    editor.focus();
    editor.getDoc().setCursor(curPos);
    run();
};

function builtinRead(x) {
    if (Sk.builtinFiles === undefined || Sk.builtinFiles["files"][x] === undefined)
        throw "File not found: '" + x + "'";
    return Sk.builtinFiles["files"][x];
}

function run() {
    var mod;
    var program = editor.getValue() + "\n" + document.getElementById('test_code_to_run').innerText;
    var outputArea = document.getElementById("output");
    var completion_status_id = 2;
    outputArea.innerHTML = '';
    Sk.pre = "output";
    Sk.configure({
        output: outf, read: builtinRead,
        inputfun: inf, inputfunTakesPrompt: true
    });
    (Sk.TurtleGraphics || (Sk.TurtleGraphics = {})).target = 'mycanvas';
    var myPromise = Sk.misceval.asyncToPromise(function () {
        mod = Sk.importMainWithBody("<stdin>", false, program, true);
        return mod;
    });

    myPromise.then(function (mod) {
        var runMethod = mod.tp$getattr('__TEST');
        var ret = Sk.misceval.callsim(runMethod, Sk.builtin.str(editor.getValue()), Sk.builtin.str(outputArea.innerHTML));
        //ret.v is an array of problems
        if (ret.v.length === 0 || ret.v[0].v === null) {
            //success
        }
        else {
            //print errors
            for (var i = 0, l = ret.v.length; i < l; i++) {
                markError(ret.v[i].v);
            }
        }
    },
        function (err) {
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

function betterTab(cm) {
    if (cm.somethingSelected()) {
        cm.indentSelection("add");
    } else {
        cm.replaceSelection(cm.getOption("indentWithTabs") ? "\t" :
            Array(cm.getOption("indentUnit") + 1).join(" "),
            "end", "+input");
    }
}

function clearAlerts() {
    codeAlerts.innerHTML = '';
}

function markError(errorMessage) {
    codeAlerts.innerHTML += '<div class="alert alert-danger alert-dismissible mar-0 alert-pad-7" role="alert" id="infoAlert">' + errorMessage + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
}

function populate_dd_text(dd_text, dd_item) {
    document.getElementById('dd_pre').innerHTML = dd_text;
    document.getElementById('btn_drop').innerHTML = document.getElementById(dd_item.id).text + '<div><span class="glyphicon glyphicon-chevron-down left-pad-7" aria-hidden="true"></span></div>';
}

function moveCode() {
    editor.setValue(dd_pre.innerHTML);
}

function getContents(index)
{
    populate_dd_text(contents_arr[index], document.getElementById('drp_contents'));
}

function setDefaultCode()
{
    editor.setValue(default_code);
}