<?php

class NotificationModule implements IModule {
	public function spinnersize(){
		return Modules::SPINNER_LARGE;
	}

	public function getcode($params = array(), Head $h){
		?>

		<div id='notificationPanel'>
			
			<div id='notificationHeader'>
				<h2>Notifications</h2>
			</div>

			<div id='notifications'>

				<?php 
				Library::get('notifications');
				echo Notifications::get();
				?>

				
			</div>


			
		</div>


	<?php
	}

	public function getsurround($code, $params){
		Pages::$head->stylesheet("modules/notification");
		Pages::$head->script("modules/notification");

		return $code;
	}


}
