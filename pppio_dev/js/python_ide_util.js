function outf(text) {
	if (text && typeof (text) != "undefined") {
		text = text.replace("<", "&lt;").replace(">", "&gt;");
		// console.log(text);
		var mypre = document.getElementById("output");
		// console.log(mypre);
		mypre.innerHTML = mypre.innerHTML + text;
	}
}
function inf(prompt) {
	// Must copy the prompt string for some reason
	return window.prompt(String(prompt));
}

var idleTime = 0;
var hadActivity = false;
$(document).ready(function () {
	//Increment the idle time counter every minute.
	var idleInterval = setInterval(idleTimerIncrement, 60000); //Time in ms for each counter - 60000 is one minute
	var saveInterval = setInterval(saveTimerIncrement, 600000); //Time for autosave so server doesn't do auto logff

	//Zero the idle timer on mouse movement or keypress
	$(this).mousemove(function (e) {
		idleTime = 0;
		hadActivity = true;
	});
	$(this).keypress(function (e) {
		idleTime = 0;
		hadActivity = true;
	});
	$(this).keydown(function (e) {
		idleTime = 0;
		hadActivity = true;
	});
});

function idleTimerIncrement() {
	//Set counter limit until auto logoff
	expireTime = 10;

	//Increment counters
	idleTime = idleTime + 1;

	//Show an alert 1 counter before a auto logoff happens that they will be logged out
	//Only occurs if there is a user logged in
	var codeAlertsCopy = codeAlerts;
	if (idleTime == (expireTime - 1)) {
		$(".alert").alert('close')
		codeAlertsCopy.innerHTML += '<div class="alert alert-danger alert-dismissible mar-0" role="alert" id="inactiveAlert">You will be logged out for inactivity in 1 minute. Please save your code to avoid losing progress.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
	}

	if (idleTime >= expireTime) {
		window.location.href = "?controller=user&action=log_out";
	}
}

function saveTimerIncrement() {
	//Do an autosave to ping the server only if there was activity in the last 10 minutes
	if(hadActivity == true){
		if (document.getElementById("runButton") != null) {
			document.getElementById("runButton").click();
		}
		hadActivity = false;
	} 
}