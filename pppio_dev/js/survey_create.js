var questionCount = 0;
var choiceCount = 0;

window.onload = function() {
	document.getElementById("sel_lesson").disabled = true;
	document.getElementById("sel_survey_type").addEventListener("change", doSomething);
	document.getElementById("btn_add_question").addEventListener("click", addQuestion);
	document.getElementById("btn_frm_submit").disabled = true;
};

function doSomething(sel)
{
	//Lesson selection dropdown
	var sel_lesson = document.getElementById("sel_lesson");

	//Hard coded to be the options Pre Lesson and Post Lesson
	//If either lesson option is selected, enable the dropdown
	//Else disable it and select the default option
	if(sel.target.selectedIndex === 4 || sel.target.selectedIndex === 5){
		sel_lesson.disabled = false;
		sel_lesson.selectedIndex = 0;
	}
	else{
		sel_lesson.disabled = true;
	}
}

function addQuestion(){
	questionCount++;

	var qDiv = document.createElement("div");
	qDiv.id = "div_question_" + questionCount;
	qDiv.setAttribute('class', 'question');

	var divContainer = document.getElementById("question_container");
	divContainer.appendChild(qDiv);

	var qHead = document.createElement("h3");
	qHead.style.color = "#F00";
	qHead.innerText = "Question " + questionCount;
	qDiv.appendChild(qHead);

	var qDelete = document.createElement("button");
	qDelete.id = "btn_del_question_" + questionCount;
	qDelete.type = "button";
	qDelete.setAttribute('class', 'del-btn');
	qDelete.innerText = "X";
	qDelete.title = "Delete Question";
	qDelete.addEventListener("click", deleteQuestion);
	qDiv.appendChild(qDelete);

	var question = document.createElement("input");
	question.type = "text";
	question.name = "questions[" + questionCount + "]";
	question.value = "Question instructions go here.";
	question.setAttribute('class', 'form-control');
	qDiv.appendChild(question);

	var qAddChoice = document.createElement("button");
	qAddChoice.id = "btn_add_choice_" + questionCount;
	qAddChoice.type = "button";
	qAddChoice.setAttribute('class', 'add-btn');
	qAddChoice.innerText = "Add Choice";
	qAddChoice.addEventListener("click", addChoice);
	qDiv.appendChild(qAddChoice);
    checkSurveyValidity();
}

function deleteQuestion(){
	//get the index of the delete question button that was pressed
	var question_id = this.id.match(/\d+/)[0];

	document.getElementById("div_question_" + question_id).remove();
    checkSurveyValidity();
}

function addChoice(){
	choiceCount++;	

	//get the index of the add choice button that was pressed
	var question_id = this.id.match(/\d+/)[0];
	
	var cDiv = document.createElement("div");
	cDiv.id = "div_row_" + question_id + "_" + choiceCount;
	cDiv.setAttribute('class', 'row');
	document.getElementById("div_question_" + question_id).appendChild(cDiv);

	var choice = document.createElement("input");
	choice.id = "sel_choice_" + choiceCount;
	choice.name = "choices[" + question_id + "][" + choiceCount + "]";
	choice.type = "text";
	choice.value = "Choice text here.";
	choice.setAttribute('class', 'form-control choice-inp');
	cDiv.appendChild(choice);

	var cDelete = document.createElement("button");
	cDelete.id = "btn_del_choice_" + choiceCount;
	cDelete.type = "button";
	cDelete.setAttribute('class', 'del-btn');
	cDelete.innerText = "X";
	cDelete.title = "Delete Choice";
	cDelete.addEventListener("click", deleteChoice);
	cDiv.appendChild(cDelete);

	countChoices(document.getElementById("div_question_" + question_id));
    checkSurveyValidity();
}

function deleteChoice(){
	//get the index of the delete choice button that was pressed
	var choice_id = this.id.match(/\d+/)[0];

	var div_row = document.getElementById("btn_del_choice_" + choice_id).parentNode;
	
	var q_id = div_row.id.split('_')[2];
	div_row.remove();

	countChoices(document.getElementById("div_question_" + q_id));
	
    checkSurveyValidity();
}

function countChoices(qDiv){
	var choiceCount = qDiv.getElementsByClassName("row").length;

	if(choiceCount >= 2){
		qDiv.getElementsByTagName("h3")[0].style.color = "#000";
	}
	else{
		qDiv.getElementsByTagName("h3")[0].style.color = "#F00";
	}
}

function checkSurveyValidity(){
	var qDivs = document.getElementsByClassName("question");
	var isValid = true;
	
	//There has to be at least 1 question
	if(qDivs.length < 1){
		isValid = false;
	}
	else{
		//Each question has to have at least 2 choices
		for(var i = 0; i < qDivs.length; i++){
			if(qDivs[i].getElementsByClassName("row").length < 2){
				isValid = false;
			} 
		}
	}
	
	document.getElementById("btn_frm_submit").disabled = !isValid;
}

$('#frm_create_survey').submit(function () {

});
