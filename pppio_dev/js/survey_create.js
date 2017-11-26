var questionCount = 0;
var choiceCount = 0;

window.onload = function() {
	document.getElementById("sel_lesson").disabled = true;
	document.getElementById("sel_survey_type").addEventListener("change", doSomething);
	document.getElementById("btn_add_question").addEventListener("click", addQuestion);
};

function doSomething(sel)
{
	//Lesson selection dropdown
	var sel_lesson = document.getElementById("sel_lesson");

	//Hard coded to be the options Pre Lesson and Post Lesson
	//If either lesson option is selected, enable the dropdown
	//Else disable it and select the default option
	if(sel.target.selectedIndex == 4 || sel.target.selectedIndex == 5){
		sel_lesson.disabled = false;
		sel_lesson.selectedIndex = 0;
	}
	else{
		sel_lesson.disabled = true;
	}
};

function addQuestion(){
	questionCount++;

	var qDiv = document.createElement("div");
	qDiv.id = "div_question_" + questionCount;
	qDiv.setAttribute('class', 'question');

	var divContainer = document.getElementsByClassName("container")[0];
	divContainer.appendChild(qDiv);

	var qHead = document.createElement("h3");
	qHead.innerText = "Question " + questionCount;
	qDiv.appendChild(qHead);

	var qDelete = document.createElement("button");
	qDelete.id = "btn_del_question_" + questionCount;
	qDelete.setAttribute('class', 'del-btn');
	qDelete.innerText = "X";
	qDelete.title = "Delete Question";
	qDelete.addEventListener("click", deleteQuestion);
	qDiv.appendChild(qDelete);

	var question = document.createElement("input");
	question.type = "text";
	question.value = "Question instructions go here.";
	question.setAttribute('class', 'form-control');
	qDiv.appendChild(question);

	var qAddChoice = document.createElement("button");
	qAddChoice.id = "btn_add_choice_" + questionCount;
	qAddChoice.setAttribute('class', 'add-btn');
	qAddChoice.innerText = "Add Choice";
	qAddChoice.addEventListener("click", addChoice);
	qDiv.appendChild(qAddChoice);
};

function deleteQuestion(){
	//get the index of the delete question button that was pressed
	var question_id = this.id.match(/\d+/)[0];

	document.getElementById("div_question_" + question_id).remove();
};

function addChoice(){
	choiceCount++;	

	//get the index of the add choice button that was pressed
	var question_id = this.id.match(/\d+/)[0];
	
	var cDiv = document.createElement("div");
	cDiv.setAttribute('class', 'row');
	document.getElementById("div_question_" + question_id).appendChild(cDiv);

	var choice = document.createElement("input");
	choice.id = "sel_choice_" + choiceCount;
	choice.type = "text";
	choice.value = "Choice text here.";
	choice.setAttribute('class', 'form-control choice-inp');
	cDiv.appendChild(choice);

	var cDelete = document.createElement("button");
	cDelete.id = "btn_del_choice_" + choiceCount;
	cDelete.setAttribute('class', 'del-btn');
	cDelete.innerText = "X";
	cDelete.title = "Delete Choice";
	cDelete.addEventListener("click", deleteChoice);
	cDiv.appendChild(cDelete);
};

function deleteChoice(){
	//get the index of the delete choice button that was pressed
	var choice_id = this.id.match(/\d+/)[0];

	document.getElementById("btn_del_choice_" + choice_id).remove();
	document.getElementById("sel_choice_" + choice_id).remove();
};
