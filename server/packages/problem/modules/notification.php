<?php

class NotificationModule implements IModule {
	public function spinnersize(){
		return Modules::SPINNER_LARGE;
	}

	public function getcode($params = array(), Head $h){
		$h->stylesheet("modules/notification");
		$h->script("");
		$h->script("modules/notification");
		

		?>

		<div id='notificationPanel'>
			
			<div id='notificationHeader'>
				<h2>Notifications</h2>
				<div id='refreshButton' class='fa fa-refresh sideButton'></div>
			</div>

			<div id='notifications'></div>
			
		</div>

		<div id='screen'></div>


	<?php
	}

	public function getsurround($code, $params){
		return $code;
	}


}
