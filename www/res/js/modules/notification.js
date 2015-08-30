LimePHP.register("modules.notification", function() {;
	//get latest 10 notifications
	fetchNotifications();
	var lastRefreshTime = timeNow();

	setInterval(recalculateTimes, 1000);

	//add button event listeners
	document.getElementById('notificationButton').addEventListener('click', toggleNotifications, false);
	document.getElementById('refreshButton').addEventListener('click', refreshNotifications, false);
	var notificationsOpen = false;

	//turn notification panel on or off
	function toggleNotifications(event){
		if (!notificationsOpen){
			//if screen is small, make a screen
			if (document.body.clientWidth < (960 + 800)){
				document.getElementById('screen').style.display = 'block';	
				document.getElementById('screen').addEventListener('click', toggleNotifications, false);
				document.removeEventListener('scroll', preventScrolling, false);
			}
			

			//open notifications
			document.getElementById('notificationPanel').style.right = "0";
			notificationsOpen = true;

			document.addEventListener('scroll', preventScrolling, false);


		}else{
			//if screen is small, close the screen
			if (document.body.clientWidth < (960 + 800)){
				document.removeEventListener('scroll', preventScrolling, false);
				document.getElementById('screen').removeEventListener('click', toggleNotifications, false);
				document.getElementById('screen').style.display = 'none';
				
			}
			//close notifications
			document.getElementById('notificationPanel').style.right = "-500px";
			notificationsOpen = false;
		}
	}

	function preventScrolling(event){
		event.preventDefault();
	}

	//button functions
	function showRefreshing(){
		document.getElementById('refreshButton').removeEventListener('click', refreshNotifications, false);
		var refreshButton = document.getElementById('refreshButton');
		refreshButton.style.color = "#4d2224";

		refreshButton.className = 'sideButton fa fa-refresh fa-spin';
	}

	function refreshDone(){
		var refreshButton = document.getElementById('refreshButton');
		refreshButton.style.color = 'white';
		refreshButton.className = 'sideButton fa fa-refresh';
		document.getElementById('refreshButton').addEventListener('click', refreshNotifications, false);
	}

	//refresh functions

	function fetchNotifications(){
		var notificationRequest = LimePHP.request("get", LimePHP.path("ajax/notifications/load"), {"limit": 10}, "json");
		notificationRequest.success = notificationsArrived;
		notificationRequest.error = notificationsLost;

		//setTimeout(refreshNotifications, 30000);
	}

	function refreshNotifications(){
		showRefreshing();

		var notificationSettings = {
			"time": Math.round(lastRefreshTime), 
			"before": 0,
		}

		var notificationRequest = LimePHP.request("get", LimePHP.path("ajax/notifications/load"), notificationSettings, "json");
		notificationRequest.success = notificationsArrived;
		notificationRequest.error = notificationsLost;

		console.log("Refreshing with: ");
		console.log(notificationSettings);
		setTimeout(refreshNotifications, 30000);
	}

	function notificationsArrived(package){

		refreshDone();


		var notificationList = document.getElementById('notifications');

		for (notification in package){
			var currentNotification = package[notification];
			console.log(currentNotification)
			var statHTML = "<span class='time' time='" + currentNotification['time'] + "'>" + timeago(currentNotification['time']) + "</span>" + ' - ' + currentNotification['section'];
			notificationList.innerHTML = "<section class='notificationCell'><p class='message'>" + currentNotification['message'] + "</p><p class='stats'>" + statHTML + "</p></section>" + notificationList.innerHTML;
			lastRefreshTime = currentNotification['time'];
		}
	}

	function notificationsLost(error){
		refreshDone();
		console.log('Notifications lost:');
		console.log(error);
	}


	function timeNow(){
		var date = new Date();
		return date.getTime();
	}

    function timeago(pastTime){
	    var second = 1000;
	    var minute = 60 * second;
	    var hour = minute * 60;
	    var day = hour * 24;
	    var week = day * 7;
	    var month = week * 4;
	    var year = month * 12;

	    var timeDifference = timeNow() - (pastTime * 1000);

	    output = timeDifference / 1000;

	    if (timeDifference < minute){
	        output = "Just now";
	    }else if (timeDifference < hour){
	        output = Math.round(timeDifference/minute) + " minute";
	    }else if (timeDifference < day){
	        output = Math.round(timeDifference/hour) + " hour";
	    }else if (timeDifference < week){
	        output = Math.round(timeDifference/day) + " day";
	    }else if (timeDifference < month){
	        output = Math.round(timeDifference / week) + " week";
	    }else if (timeDifference < year){
	        output = Math.round(timeDifference / month) + " month";
	    }else{
	        output = Math.round(timeDifference / year) + " year";
	    }

	    if (output.substring(0, 2) != "1 " && output != "Just now"){
	        output += "s ago";
	    }else if (output.substring(0, 2) == "1 " && output != "Just now"){
	        output += " ago";
	    }

	    return output;
	}

	function recalculateTimes(){
		var timeSpans = document.getElementsByClassName('time');

		for (var i = 0; i < timeSpans.length; i++){
			var fuzzyTime = timeago(timeSpans[i].getAttribute('time'));
			timeSpans[i].innerHTML = fuzzyTime;
		}
	}

});

