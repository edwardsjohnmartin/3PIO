var mouseclicks = 0;
var keypresses = 0;
var timesran = 0;
var start;
var sessionIdleTime = 0;
var activityOccurred = false;
var sessionExpireTime = 5; //number of minutes of inactivity to kill a session after

//when the window loads, initialize the start time and update the onclick function for the run button
window.onload = function () {
    //divide by 1000 because js uses milliseconds and php uses seconds
    start = new Date().getTime() / 1000;

    var minuteInterval = setInterval(timerIncrement, 60000); //Time in ms for each counter - 60000 is one minute
    
    //add tracking of timesran to the existing functionality of the run button if it exists
    var runbtn = document.getElementById("runButton");
    if (runbtn !== null) {
        var old = runbtn["onclick"] || function () { };
        runbtn["onclick"] = function () {
            old();
            timesran += 1;
        };
    }
};

//handle updating the session idle time
//save and then reset the session of the idle time reached 5 minutes
function timerIncrement() {
    sessionIdleTime += 1;

    //console.log("timer incremented to " + sessionIdleTime);

    if (sessionIdleTime === sessionExpireTime) {
        //console.log("sessionIdleTime is equal to sessionExpireTime so save was called");
        saveSession();
        resetSession();
    }
}

//track when a key was pressed even if the code editor is not focused
//only tracks enter key when the code editor does not have focus
//does not track windows key, ctrl, alt, or shift at all
window.onkeypress = function () {
    //console.log("keypress occurred");
    keypresses += 1;
    resetSessionIdleTime();
};

//called when a tab is closed or when the browser closes
//need to test if it happens when the browswer is force closed
//this will make the ajax call to save the session to the database
//i have read that sometimes there isnt enough time to finish the ajax call and data could be lost
//will need to test this
window.onbeforeunload = function () {
    saveSession();
    resetSession();
};

//handles key shortcuts
//tracks when the run button was clicked using the shortcut
$(window).keydown(function (event) {
    //console.log("keydown occurred");
    resetSessionIdleTime();

    //adds ctrl+enter shortcut for the run button
    if (event.ctrlKey && event.keyCode === 13) {
        var runbtn = document.getElementById("runButton");
        if (runbtn !== null) {
            runbtn.click();
        }
        event.preventDefault();
    }

    //adds ctrl+/ shortcut for the next button
    if (event.ctrlKey && event.keyCode === 191) {       
        var closebtn = document.getElementById("infoAlert");
        if (closebtn !== null) {
            if (closebtn.children[0].href !== undefined) {
                window.location.href = closebtn.children[0].href;
            }
        }
    }

    //adds ctrl+. shortcut to save the session (used for testing)
    //if (event.ctrlKey && event.keyCode === 190) {
    //    saveSession();
    //    resetSession();
    //}
});

//currently captures any mouse button; left, right, or middle
window.onmousedown = function () {
    //console.log("mousedown occurred");
    mouseclicks += 1;
    resetSessionIdleTime();
};

//ajax call to save the session to the database
function saveSession() {
    if (mouseclicks === 0 && keypresses === 0 && timesran === 0) {
        //console.log("no activity was detected so not saved");
    }
    else {

        //console.log("activity was detected and save was called");

        if (typeof exercise_id !== "undefined") {
            var activity_name = "exercise";
            var activity_id = exercise_id;
        }
        else if (typeof project_id !== "undefined") {
            var activity_name = "project";
            var activity_id = project_id;
        }
        else if (typeof current_question_id !== "undefined") {
            var activity_name = "question";
            var activity_id = current_question_id;
        }

        //Used for debugging
        //console.log("activity_name: " + activity_name);
        //console.log("activity_id: " + activity_id);
        //console.log("mouseclicks: " + mouseclicks);
        //console.log("keypresses: " + keypresses);
        //console.log("timesran: " + timesran);

        $.ajax({
            method: "POST",
            url: "?controller=session&action=save",
            async:false,
            data: { start: start, mouseclicks: mouseclicks, keypresses: keypresses, timesran: timesran, activity_name: activity_name, activity_id: activity_id }
        });
    }
}

//reset sessionIdleTime since there was activity
function resetSessionIdleTime() {
    //console.log("session idle time was reset");
    sessionIdleTime = 0;
    activityOccurred = true;
}

//zero out any currently tracked metrics
function resetSession() {
    mouseclicks = 0;
    keypresses = 0;
    timesran = 0;
    activityOccurred = false;
    sessionIdleTime = 0;
    start = new Date().getTime() / 1000;

    //console.log("resetSession occurred and the new start time is " + start);
}
