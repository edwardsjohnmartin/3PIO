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

    //Save where the cursor is and where the code editor is scrolled to
    var curPos = editor.getDoc().getCursor();
    var scrollPos = editor.getScrollInfo();

    //Clear whatever is currently drawn
    if (Sk.TurtleGraphics !== undefined && Sk.TurtleGraphics.reset !== undefined)
    {
        Sk.TurtleGraphics.reset();
    }

    //Replace tabs with 4 spaces 
    editor.setValue(editor.getValue().replace(/\t/g, '    '));

    //Set the cursor and scrollbar position to where they were before the run button was pressed
    editor.getDoc().setCursor(curPos);
    editor.scrollTo(0, scrollPos.top);
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
    outputArea.innerHTML = '';

    Sk.configure({
        output: outf, read: builtinRead,
        inputfun: inf, inputfunTakesPrompt: true
    });

    (Sk.TurtleGraphics || (Sk.TurtleGraphics = {})).target = "mycanvas";
    Sk.TurtleGraphics.width = document.getElementById("mycanvas").clientWidth;
    Sk.TurtleGraphics.height = document.getElementById("mycanvas").clientHeight;

    var myPromise = Sk.misceval.asyncToPromise(function () {
        mod = Sk.importMainWithBody("<stdin>", false, program, true);
        return mod;
    });

    myPromise.then(
        function (mod) {
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
        }
    );  
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

// Called when a dropdown item is clicked. Sets contents of the <pre> area to the text attribute of the dropdown item.
function setInformationTextbox(dd_text, dd_item_id) {
    document.getElementById('txtInfo').innerHTML = dd_text;
    document.getElementById('btn_drop').innerHTML = document.getElementById(dd_item_id).text + '<div><span class="glyphicon glyphicon-chevron-down left-pad-7" aria-hidden="true"></span></div>';
}

function setInformationTextboxOnly(dd_text) {
    document.getElementById('txtInfo').innerHTML = dd_text;
}

// Moves the code from txtInfo into the editor.
function moveCode() {
    editor.setValue(txtInfo.innerHTML);
}

// Called as the page loads. Sets the editor to the default code. If none was given, will be blank.
function setDefaultCode(def_code)
{
    editor.setValue(def_code);
}

// Called in the slider.onchange event. Changes the sizes of where text is printed out and where turtle graphics are drawn. 
function resizeOutputAreas(graphicSize)
{
    textOutputSize = 99 - graphicSize;
    document.getElementById('mycanvas').style.height = graphicSize + "%";
    document.getElementById('output').style.height = textOutputSize + "%";

    //Clear whatever is currently drawn
    if (Sk.TurtleGraphics !== undefined && Sk.TurtleGraphics.reset !== undefined) {
        Sk.TurtleGraphics.reset();
    }
}
