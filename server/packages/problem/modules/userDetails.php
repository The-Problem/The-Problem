<?php
//module for changing user details which appears on user pages
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
		$gravatarLink = "https://public-api.wordpress.com/oauth2/authorize?client_id=1854&response_type=code&blog_id=0&state=5c8d5106ab20c9a043551ec56525bb0d10242058b320ce6881cd7ca99381be7a&redirect_uri=https%3A%2F%2Fen.gravatar.com%2Fconnect%2F%3Faction%3Drequest_access_token";

		?>
		
		<div id='detailsPanel'>
			<h2>Your Details</h2>
			<div id='detailsWrap'>
				<div id='left'>
					<a href='<?php echo $gravatarLink; ?>'><img src='<?php echo $profilePicLink ?>' title='You can change your profile picture through Gravatar' /></a>
					<h2><?php echo $currentUser->username?></h2>
				</div>

				<div id='right'>
					<form name='userDetailsForm' method='post' action='<?php echo Path::getclientfolder("ajax", "users", "changeDetails"); ?>' id='userDetailsForm'>
					<label>Prefered Name</label><br><input id='nameField' type='text' value='<?php echo $currentUser->name;?>'><i id='nameIcon' class='verifyIcon'></i><br>
					<label>Email</label><br><input id='emailField' type='text' value="<?php echo $currentUser->email; ?>"><i id='emailIcon' class='verifyIcon'></i><br>
					<label>Your Bio</label><br><textarea id='bioField' form='userDetailsForm'><?php echo $currentUser->bio; ?></textarea>
					<i id='passwordIcon' class='verifyIcon'></i>
					<button id='passwordButton' class='formButton'>Change Password</button>
					<button class='formButton'>Upload Cover Photo</button>

					</form>
					<p id='formMessage'></p>
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