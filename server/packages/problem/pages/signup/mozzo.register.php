<?php
class RegisterPage implements IPage {
    public function __construct(PageInfo &$info) {
    }
    
    public function template() {
        return Templates::findtemplate("default")->small();
    }
    public function permission() {
        Library::get("users");
        return !Users::loggedin();
    }
    
    public function subpages() { return true; }
    
    public function head(Head &$head) { }
    
    public function body() {
        $ret = array();
        if (isset($_POST['token'])) {
            Library::get("validator");
            
            $ret = Validator::batchvalidate(array(
                array(
                    "value" => $_POST['username'],
                    "type" => "complex",
                    "parameters" => Validator::COMPLEX_USERNAME,
                    "message" => "Your username should be longer than 4 characters and not contain spaces"
                ),
                array(
                    "value" => $_POST['firstname'],
                    "type" => "string",
                    "message" => "Please enter a first name"
                ),
                array(
                    "value" => $_POST['lastname'],
                    "type" => "string",
                    "message" => "Please enter a last name"
                ),
                array(
                    "value" => $_POST['password'],
                    "type" => "complex",
                    "parameters" => Validator::COMPLEX_PASSWORD,
                    "message" => "Your password should be longer than 5 characters, shorter than 21, and contain at least one number, uppercase, and lowercase character"
                ),
                array(
                    "value" => $_POST["password2"],
                    "type" => "same",
                    "parameters" => array("other" => $_POST['password']),
                    "message" => "Passwords are not the same"
                ),
                array(
                    "value" => $_POST["email"],
                    "type" => "email",
                    "message" => "Please enter a valid email"
                )
            ));
            
            if (Users::getuser($_POST['username'], Users::TYPE_USERNAME)) {
                array_push($ret, "A person with that username already exists");
            } else if (Users::getuser($_POST['email'], Users::TYPE_EMAIL)) {
                array_push($ret, "A person with that email already exists");
            }
            
            if (!count($ret)) {                
                $newuser = new User();
                $newuser->username($_POST['username']);
                $newuser->firstname($_POST['firstname']);
                $newuser->lastname($_POST['lastname']);
                $newuser->password($_POST['password']);
                $newuser->email($_POST['email']);
                
                Users::login($_POST['username'], $_POST['password']);
                
                //Path::redirect(Path::addget(Path::getclientfolder(), "notice", "Welcome, " . $_POST['firstname'] . "! You're now a member of mozzo."));
                Cookies::prop("go", true);
                Path::redirect(Path::getclientfolder(array("register", "step2")));
            }
        }
        
        ?>
<form action="<?php echo Path::getclientfolder("register"); ?>" method="post">
    <div class="section">
        <h1 class="title">Register</h1>
        <?php if (count($ret)) { ?>
        <div class="error">
            <?php if (count($ret) > 1) { ?>
            <ul>
                <li><?php echo implode("</li><li>", $ret); ?></li>
            </ul>
            <?php } else echo $ret[0]; ?>
        </div>
        <?php } ?>
        <p>Registering on mozzo is very easy.</p>
            <table>
                <tr>
                    <td>Username:</td>
                    <td colspan="2"><input type="text" name="username" value="<?php if (array_key_exists("username", $_POST)) echo htmlentities($_POST['username']); ?>" /></td>
                </tr>
                <tr>
                    <td>Email:</td>
                    <td colspan="2"><input type="email" name="email" /></td>
                </tr>
                <tr>
                    <td>Name:</td>
                    <td><input type="text" name="firstname" placeholder="First" value="<?php if (array_key_exists("firstname", $_POST)) echo htmlentities($_POST['firstname']); ?>" /></td>
                    <td><input type="text" name="lastname" placeholder="Last" value="<?php if (array_key_exists("lastname", $_POST)) echo htmlentities($_POST['lastname']); ?>" /></td>
                </tr>
                <tr>
                    <td>Password:</td>
                    <td><input type="password" name="password" /></td>
                    <td><input type="password" name="password2" placeholder="Re-enter your password" /></td>
                </tr>
            </table>
            
            <input type="hidden" name="token" value="NOT_IMPLEMENTED" />
    </div>
    <div class="button-set light-bg">
        <button type="submit" class="highlight button-right">Continue</button>
        <div class="clear"></div>
    </div>
</form>
<?php } }