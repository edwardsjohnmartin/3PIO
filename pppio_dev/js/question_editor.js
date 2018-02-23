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

var isSuccess = false;

document.getElementById("runButton").onclick = function () {
    clearAlerts();
    var curPos = editor.getDoc().getCursor();
    editor.setValue(editor.getValue().replace(/\t/g, '    '));
    editor.focus();
    editor.getDoc().setCursor(curPos);
    run();
};

editor.on('copy', function (a, e) { e.preventDefault(); });
editor.on('cut', function (a, e) { e.preventDefault(); });
editor.on('paste', function (a, e) { e.preventDefault(); });

function builtinRead(x) {
    if (Sk.builtinFiles === undefined || Sk.builtinFiles["files"][x] === undefined)
        throw "File not found: '" + x + "'";
    return Sk.builtinFiles["files"][x];
}

function run() {
    var mod;
    var program = editor.getValue() + "\n" + document.getElementById('test_code_to_run').innerText;

    var test_code = document.getElementById('test_code_for_score').innerText;

    var numTests = 0;
    numTests += (test_code.split("test_val").length - 1);
    numTests += (test_code.split("test_type").length - 1);
    numTests += (test_code.split("test_func").length - 1);
    numTests += (test_code.split("test_in").length - 1);
    numTests += (test_code.split("test_out").length - 1);
    numTests += (test_code.split("test_equal").length - 1);

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

    //This runs if the code was able to compile
    myPromise.then(function (mod) {
        //var runMethod = mod.tp$getattr('__TEST');
        var runMethod = mod.tp$getattr('__TEST_EXAM');

        var ret = Sk.misceval.callsim(runMethod, Sk.builtin.str(editor.getValue()), Sk.builtin.str(outputArea.innerHTML));

        var score_mult = 1.0;

        //ret.v is an array of problems
        if (ret.v.length === 0 || ret.v[0].v === null) {
            //success
            completion_status_id = 1;
            completeExercise();
        }
        else {
            score_mult = (numTests - ret.v.length) / parseFloat(numTests);
            //print errors
            for (var i = 0, l = ret.v.length; i < l; i++) {
                markError(ret.v[i].v); 
                break;
            }
        }
        updateTiles(completion_status_id);
        save(current_question_id, exam_id, editor.getValue(), completion_status_id, score_mult);
    },
    //This runs if the code couldn't compile
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
        save(current_question_id, exam_id, editor.getValue(), completion_status_id, 0);
        updateTiles(completion_status_id);
    });
}

function save(question_id, exam_id, contents, completion_status_id, score_multiplier) {
    $.ajax({
        type: "POST",
        url: "?controller=question&action=save_code",
        data: { question_id: question_id, exam_id: exam_id, contents: contents, completion_status_id: completion_status_id, score_multiplier: score_multiplier },
        success: function (data) {
            if (data.success) {
                //markSuccess('Code saved.');
            }
            else {
                markError('Unable to save code.');
            }
        },
        error: function () { markError('Unable to save code.'); }
    });

    var points = parseFloat((q_point_val * score_multiplier).toFixed(2).toString());
    document.getElementById("h_points").innerHTML = "Q" + q_pos + " - " + points.toString() + "/" + q_point_val + "pts";  
}

function completeExercise() {
    var successMessage = 'Good job! ';
    if (trying_last) {
        successMessage += '<a href="' + link + '" class="btn btn-success btn-sm"><span class="">Continue</span></a>';
    }
    else {
        successMessage += '<a href="' + link + '" class="btn btn-success btn-sm"><span class="">Next question</span></a>';
    }
    markSuccess(successMessage);
}

var codeAlerts = document.getElementById('codeAlerts');

function clearAlerts() {
    codeAlerts.innerHTML = '';
}

function markError(errorMessage) {
    if (typeof errorCount !== "undefined") {
        errorCount += 1;
    }
    codeAlerts.innerHTML += '<div class="alert alert-danger alert-dismissible mar-0" role="alert" id="infoAlert">' + errorMessage + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
}

function markSuccess(successMessage) {
    codeAlerts.innerHTML += '<div class="alert alert-success alert-dismissible mar-0" role="alert" id="infoAlert">' + successMessage + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
}

function updateTiles(completion_status_id) {
    var current_tile = document.getElementById(current_tile_id);
    if (completion_status_id === 1) {
        current_tile.classList.remove('btn-default');
        current_tile.classList.remove('btn-primary');
        current_tile.classList.remove('btn-started');
        current_tile.classList.add('btn-success');
    }
    else if (completion_status_id === 2)
    {
        current_tile.classList.remove('btn-default');
        current_tile.classList.remove('btn-success');
        current_tile.classList.remove('btn-primary');
        current_tile.classList.add('btn-started');
    }
}

window.onblur = function () {
    $.ajax({
        type: "POST",
        url: "?controller=question&action=create_occurrence",
        data: { user_id: user_id, question_id: current_question_id, exam_id: exam_id },
        success: function (data) {
            if (data.success) {
                //console.log("Occurrence create success");
            }
            else {
                //console.log("Occurrence create fail");
            }
        }
    });
};

function addEvent(obj, evt, fn) {
    if (obj.addEventListener) {
        obj.addEventListener(evt, fn, false);
    }
    else if (obj.attachEvent) {
        obj.attachEvent("on" + evt, fn);
    }
}

addEvent(document, "mouseout", function (e) {
    e = e ? e : window.event;
    var from = e.relatedTarget || e.toElement;
    if (!from || from.nodeName === "HTML") {
        //console.log("cursor left");
    }
});
