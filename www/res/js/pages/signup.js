window.addEventListener("load", init, false);

function init(){
	window.formElements = {
		"username": 0,
		"password": 0,
		"rpassword": 0,
		"name": 0,
		"email": 0
	}

	window.formTests = {
		"username": testUsername,
		"password": testPassword,
		"rpassword": testRPassword,
		"name": testName,
		"email": testEmail
	};

	for (element in formElements){
		document.getElementById(element + "Field").addEventListener("keyup", fieldChangeHandler, false);
	}

	document.getElementById('joinButton').addEventListener('click', validateForm, false);
}

function validateForm(event){
	var canSubmit = true;
	
	for (element in formElements){
		if (formElements[element] != 1){
			canSubmit = false;
			break;
		}
	}

	if (!canSubmit){
		event.preventDefault();
		document.getElementById('invalidMessage').innerHTML = "There were some issues with the details you entered. Please fix them to continue.";
		document.getElementById('messageDiv').style.height = "35px";

		setTimeout(function(){
			for (element in formElements){
			validateField(element);
			}

		}, 200);
	}
}

function fieldChangeHandler(event){
	var currentFieldID = event.target.getAttribute('id');
	var currentFieldName = currentFieldID.substring(0, currentFieldID.length - 5);

	validateField(currentFieldName);
}

function validateField(fieldName){
	var fieldValue = document.getElementById(fieldName + "Field").value;

	var newState = formTests[fieldName](fieldValue);
	formElements[fieldName] = newState;
	changeIconState(fieldName, newState);
}

function changeIconState(fieldName, state){
	var icon = document.getElementById(fieldName + "Icon");
	var stateIcons = ['fa fa-exclamation', 'fa fa-check', 'fa fa-refresh fa-spin'];
	var newClass = 'verifyIcon ' + stateIcons[state];

	if (icon.className == newClass){
		return false;	
	}

	icon.style.transform = "scale(0)";
	
	setTimeout(function(){
		icon.className = newClass;
		icon.style.transform = "scale(1)";
	}, 150);

	return true;
}

/*Password Test Functions*/

function testUsername(){
	return 2;
}

function testPassword(password){
	if (password.length > 8 && /\d/.test(password) && /[A-Z]/i.test(password)){
		return 1;
	}

	return 0;
}

function testRPassword(rPassword){
	var password = document.getElementById("passwordField").value;
	if (formElements['password'] && rPassword == password){
		return 1;
	}

	return 0;
}

function testName(name){
	if (name.length > 1){
		return 1;
	}

	return 0;
}

function testEmail(address){
	return 2; 
}