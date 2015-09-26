LimePHP.register("modules.userDetails", function() {
	document.getElementById('userDetailsButton').addEventListener('click', showPanel, false);

	function showPanel(event){
		document.getElementById('userDetailsScreen').style.display = "block";
		document.getElementById('userDetailsScreen').style.backgroundColor = "rgba(0,0,0,0.4)";
		document.getElementById('detailsPanel').style.top = "50px";

		document.getElementById('saveButton').addEventListener('click', validateForm, false);
		document.getElementById('cancelButton').addEventListener('click', closeUserPanel, false);
		document.getElementById('userDetailsScreen').addEventListener('click', closeUserPanel, false);
		document.getElementById('passwordButton').addEventListener('click', changePassword, false);
	}

	function closeUserPanel(event){
		document.getElementById('cancelButton').removeEventListener('click', closeUserPanel, false);
		document.getElementById('userDetailsScreen').removeEventListener('click', closeUserPanel, false);
		document.getElementById('saveButton').removeEventListener('click', validateForm, false);
		document.getElementById('userDetailsScreen').style.backgroundColor = "rgba(0,0,0,0)";
		document.getElementById('detailsPanel').style.top = "calc(-100%)";
		setTimeout(function(){
			document.getElementById('userDetailsScreen').style.display = "none";
		}, 500);
	}

	function changePassword(event){
		event.preventDefault();
		var request = LimePHP.request("post", LimePHP.path("ajax/user/changePassword"), { "username": username}, "json");
		changeIconState('password', 2);
		request.success = passwordSuccess;
		request.error = passwordError;
	}

	function passwordSuccess(success){
		document.getElementById('formMessage').innerHTML = "A link has been emailed to you."
		changeIconState('password', 1);
	}

	function passwordError(error){
		console.log('error:');
		console.log(error);
	}



	//form validation
	window.formElements = {
		"name": 0,
		"email": 0
	};

	var formTests = {
		"name": testName,
		"email": testEmail,
	};

	for (var element in formElements){
		document.getElementById(element + "Field").addEventListener("blur", fieldChangeHandler, false);
	}

	function submitToAjax(){
		var name = document.getElementById('nameField').value;
		var email = document.getElementById('emailField').value;
		var bio = document.getElementById('bioField').value;

		console.log(name + email + bio);
		var request = LimePHP.request("post", LimePHP.path("ajax/user/changeDetails"), { "name": name, "email": email, "bio": bio }, "json");
		request.success = formSaved;
		request.error = formError;
	}

	function formSaved(success){
		console.log('success');
		closeUserPanel();
	}

	function formError(error){
		console.log('Error:');
		console.log(error);

	}

	function validateForm(event){
		var canSubmit = true;
		
		for (element in formElements){
			validateField(element);
		}

		setTimeout(function(){
			for (element in formElements){
				if (formElements[element] != 1){
					console.log(element);
					canSubmit = false;
					break;
				}
			}
			
			if (!canSubmit){
				document.getElementById('invalidMessage').innerHTML = "There were some issues with the details you entered.";
				document.getElementById('messageDiv').style.height = "35px";
			}else{
				document.getElementById('invalidMessage').innerHTML = "";
				submitToAjax();
			}

		}, 800);

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

	/*Form Test Functions*/
	function testName(name){
		if (name.length > 1){
			return 1;
		}

		return 0;
	}

	function testEmail(address){
		var request = LimePHP.request("get", LimePHP.path("ajax/signup/checkEmail"), { "address": address}, "json");
		request.success = setEmailSuccess;
		request.error = setEmailFail;
		return 2;
	}


	function setEmailSuccess(success){

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

});
