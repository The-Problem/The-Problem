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
				
				<section class='notificationCell'>
					<p class='message'>
							Patrick replied to your comment:<br>
							“I’ll leave my matter till later...”
					</p>

					<p class='stats'>
						Just then - <u>Carbine</u>
					</p>
				</section>

				<section class='notificationCell'>
						
						<span class='message'>
							Tom +1’d your bug, “Needs More Dragons”
						</span>

						<span class='stats'>
							Just then - <u>General Feedback</u>
						</span>
					
				</section>

				<ul class='notificationCell'></ul>

				<?php 
				Library::get(notifications);
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
