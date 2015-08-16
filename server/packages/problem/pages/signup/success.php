<?php
class SignupSuccessPage implements IPage{
	public function __construct(PageInfo &$page){
	}

	public function template(){
		return Templates::findtemplate("default");
	}

	public function permission(){
		return true;
		
		/*Library::get('cookies');
		if(Cookies::prop('signedup')){
			return true;
		}

		return false;*/
	}

	public function subpages(){
		return true;
	}

	public function head(Head &$head){
		//$head->stylesheet("pages/signupsuccess");
		$head->title = "Account Created";
		$head->stylesheet("pages/success");
	}

	public function body(){
		Cookies::prop('signedup', false);
		Library::get('users');
		?>

		<h1>You are now part of The Problem</h1>
		<p>You'll need to verify your account through an email we've sent you before you begin working. But before you do that, check your avatar and put in a bio to get your profile going!</p>
		<h2>Your Avatar</h2>
		<p>The Problem uses avatars which are connected to your Gravatar email address. If you don't have a Gravatar, we've generated for you your own identicon.</p>

		<?php

		$currentUser = Users::getUser("current");
		$emailHash = md5(strtolower($currentUser->email));
		$imageLink = "http://www.gravatar.com/avatar/" . $emailHash . "?s=200";

		?>

		<img id='profilePic' src= <?php echo $imageLink?> />
		<h2>Your Bio</h2>
		<p>Write something about yourself to help others know you.</p>
		<textarea></textarea>

		<a href='.'><button class='highlight'>GO HOME</button></a>


		<?php
		
	}
}
?>