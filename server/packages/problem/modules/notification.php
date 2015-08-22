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
				<ul class='notificationCell'>
					<div class='notificationTextWrap'>
						<span class='notificationText'>
							Patrick replied to your comment:<br>
							“I’ll leave my matter till later...”
						</span>
					</div>
				</ul>

				<ul class='notificationCell'>
					<div class='notificationTextWrap'>
						<span class='notificationText'>
							Tom +1’d your bug, “Needs More <br>Dragons”
						</span>
					</div>
				</ul>

				<ul class='notificationCell'>
					<div class='notificationTextWrap'>
						<span class='notificationText'>
							Patrick replied to your comment:<br>
							“I’ll leave my matter till later...”
						</span>
					</div>
				</ul>

				<ul class='notificationCell'>
					<div class='notificationTextWrap'>
						<span class='notificationText'>
							Tom +1’d your bug, “Needs More <br>Dragons”
						</span>
					</div>
				</ul>

				<ul class='notificationCell'></ul>
				
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
