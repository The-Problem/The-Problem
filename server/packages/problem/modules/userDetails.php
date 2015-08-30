<?php

class UserDetailsModule implements IModule {
	public function spinnersize(){
		return Modules::SPINNER_LARGE;
	}

	public function getcode($params = array(), Head $h){
		$h->stylesheet("modules/userDetails");
		$h->script("");
		$h->script("modules/userDetails");
		$script = "var username = '" . $params['username'] . "'";
		Pages::$head->tag("script", $script);
		Library::get('users');
		$currentUser = Users::getUser($params['username']);
		$profilePicLink = $currentUser->getAvatarLink(175);

		?>

		<div id='detailsPanel'>
			<h2>Your Details</h2>
			<div id='detailsWrap'>
				<div id='left'>
					<img src='<?php echo $profilePicLink ?>' />
					<h2><?php echo $currentUser->username?></h2>
				</div>

				<div id='right'>
					<form name='userDetailsForm' method='post' action='<?php echo Path::getclientfolder("ajax", "users", "changeDetails"); ?>' id='userDetailsForm'>
					<label>Prefered Name</label><br><input id='nameField' type='text' value='<?php echo $currentUser->name;?>'><i id='nameIcon' class='verifyIcon'></i><br>
					<label>Email</label><br><input id='emailField' type='text' value="<?php echo $currentUser->email; ?>"><i id='emailIcon' class='verifyIcon'></i><br>
					<label>Your Bio</label><br><textarea id='bioField'><?php echo $currentUser->bio; ?></textarea>
					<button class='formButton'>Change Password</button>
					<button class='formButton'>Upload Cover Photo</button>

					</form>
				</div>

				<div id='messageDiv'>
					<p id='invalidMessage'></p>
				</div>
			
				<div id='buttonWrap'>
					<button id='cancelButton' class='formButton'>CANCEL</button>
					<button id='saveButton' class='formButton'>SAVE CHANGES</button>
				</div>
			</div>
			
		</div>

		<div id='userDetailsScreen'></div>



	<?php
	}

	public function getsurround($code, $params){
		return $code;
	}


}