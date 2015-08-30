var formElements = {
	"username": 0,
	"password": 0,
	"rpassword": 0,
	"name": 0,
	"email": 0
};

var formTests = {
	"username": testUsername,
	"password": testPassword,
	"rpassword": testRPassword,
	"name": testName,
	"email": testEmail
};

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

	if (icon.className != newClass){
		icon.style.transform = "scale(0)";
	}

	
	setTimeout(function(){
		icon.className = newClass;
		icon.style.transform = "scale(1)";
	}, 150);

	return true;
}

/*Password Test Functions*/

function testUsername(username){
	var request = LimePHP.request("get", LimePHP.path("ajax/signup/checkUsername"), { "username": username }, "json");
	request.success = setUsernameSuccess;
	request.error = setUsernameFail;
	return 2;
}

function setUsernameSuccess(success){
	console.log(success.result);
	if (success.result){
		console.log("this username can be used");
		formElements['username'] = 1;
		changeIconState('username', 1);
	}else{
		console.log("username taken");
		formElements['username'] = 0;
		changeIconState('username', 0);
	}

}

function setUsernameFail(error){
	console.log("There was an error in verifying your username: " + error);
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
	var request = LimePHP.request("get", LimePHP.path("ajax/signup/checkEmail"), { "address": address, "new": true }, "json");
	request.success = setEmailSuccess;
	request.error = setEmailFail;
	return 2;
}

function setEmailSuccess(success){
	console.log(success);

	if (!success.result){
		formElements['email'] = 0;
		changeIconState('email', 0);
	}else{
		formElements['email'] = 1;
		changeIconState('email', 1);
	}
}

function setEmailFail(error){
	console.log("Your email address could not be validated: " + error);
}

LimePHP.register("page.signup", function() {


	for (var element in formElements){
		document.getElementById(element + "Field").addEventListener("keyup", fieldChangeHandler, false);
	}

	document.getElementById('joinButton').addEventListener('click', validateForm, false);

});
