LimePHP.register("modules.notification", function() {
	//get latest 10 notifications

		fetchNotifications();
		var longTimeAgo = new Date(0);
		window.lastRefreshTime = longTimeAgo.toISOString();
		window.refreshRate = 5;

		//add button event listeners
		document.getElementById('notificationButton').addEventListener('click', toggleNotifications, false);
		document.getElementById('refreshButton').addEventListener('click', refreshNotifications, false);
		window.notificationsOpen = false;

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

		setTimeout(refreshNotifications, 5000);
		
		function notificationsArrived(package){

			if (package.length >= 1){
				lastRefreshTime = package[0]['time'];
			}

			for (var i = 0; i < package.length; i++){
				addNotification(package[i], false);
			}
		}
		
		function notificationsLost(error){
			refreshDone();
			console.log('Notifications lost on initial fetch:');
			console.log(error);
		}
	}

	function refreshNotifications(){
		showRefreshing();

		var notificationSettings = {
			"time": lastRefreshTime, 
			"before": 0,
		}

		var notificationRequest = LimePHP.request("get", LimePHP.path("ajax/notifications/load"), notificationSettings, "json");
		notificationRequest.success = notificationsArrived;
		notificationRequest.error = notificationsLost;

		setTimeout(refreshNotifications, refreshRate * 1000);

		function notificationsArrived(package){
			refreshDone();
			var notificationList = document.getElementById('notifications');

			if (package.length >= 1){
				lastRefreshTime = package[0]['time'];
			}

			for (var i = package.length-1; i >= 0; i--){
				addNotification(package[i], true);
			}
		}
		
		function notificationsLost(error){
			refreshDone();
			console.log('Notifications lost on refresh:');
			console.log(error);
		}
	}


	function addNotification(currentNotification, addToTop){
		
		var statHTML = "<span class='timeago' title='" + currentNotification['time'] + "'></span>" + ' - ' + "<a href='" + LimePHP.path('bugs/' + currentNotification['sectionSlug']) + "'>" + currentNotification['sectionName'] + "</a>";
		var notificationList = document.getElementById('notifications');
		
		var notificationCell = document.createElement('a');
		notificationCell.setAttribute('href', currentNotification['link']);
		notificationCell.innerHTML = "<section class='notificationCell'><p class='message'>" + currentNotification['message'] + "</p><p class='stats'>" + statHTML + "</p></section>";
		
		if (addToTop){
			notificationList.insertBefore(notificationCell, notificationList.children[0]);
		}else{
			notificationList.appendChild(notificationCell);
		}

		$(notificationCell.children[0].children[1].children[0]).timeago();
	}

});
