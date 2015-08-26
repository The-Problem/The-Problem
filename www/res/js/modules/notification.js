LimePHP.register("modules.notification", function() {
	document.getElementById('notificationButton').addEventListener('click', toggleNotifications, false);

	var notificationsOpen = false;

	function toggleNotifications(event){
		if (notificationsOpen){
			document.getElementById('notificationPanel').style.right = "-500px";
			notificationsOpen = false;
		}else{
			document.getElementById('notificationPanel').style.right = "0";
			notificationsOpen = true;
		}
	}
});

