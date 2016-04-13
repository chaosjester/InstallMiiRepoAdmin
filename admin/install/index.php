<?php
session_start();

class MyDB extends SQLite3
{
   function __construct()
   {
      $this->open('../includes/repo.db');
   }
}
$db = new MyDB();
if(!$db){
   $error = $db->lastErrorMsg();
}

if(isset($_POST['repoconfig'])) {

  $reponame = $_POST['reponame'];
  $repoowner = $_POST['repoowner'];
  $repourl = $_POST['repourl'];
  $repoblurb = $_POST['repoblurb'];

  $repofile = "../../reposettings.php";
  $repofilecontent = '<?php $reponame = "'.$reponame.'"; $repoowner = "'.$repoowner.'"; $repourl = "'.$repourl.'"; $repoblurb = "'.$repoblurb.'";?>';
  file_put_contents($repofile, $repofilecontent);

  if (file_exists($repofile)){

  	 if (!file_exists('../includes/repo.db')) {
  	   fopen('../includes/repo.db', w);
  	}

  	    $result = $db->query('CREATE TABLE users (id INT(20) PRIMARY KEY NOT NULL, name VARCHAR(200) NOT NULL, email VARCHAR(200) NOT NULL, password VARCHAR(200) NOT NULL)');
  	       if(!$result){
  	          $error = $error."Table 'users' was not created<br>".$db->lastErrorMsg()."<br>";
  	       } else {
  	          $message = $message."Table 'users' created successfully<br>";
  	       }


  	    $result = $db->query('CREATE TABLE packages (id INT(20) PRIMARY KEY NOT NULL, name VARCHAR(30) NOT NULL, short_description VARCHAR(200), author VARCHAR(30) NOT NULL, category VARCHAR(50), website VARCHAR(200), type VARCHAR(50), version VARCHAR(50), dl_path VARCHAR(200), info_path VARCHAR(200))');
  	       if(!$result){
  	          $error = $error."Table 'packages' was not created<br>".$db->lastErrorMsg()."<br>";
  	       } else {
  	          $message = $message."Table 'packages' created successfully<br>";
  	       }


  	    $result = $db->query('CREATE TABLE repos (id INT(20) PRIMARY KEY NOT NULL, name VARCHAR(100) NOT NULL, url VARCHAR(200))');
  	       if(!$result){
  	          $error = $error."Table 'repos' not created<br>".$db->lastErrorMsg()."<br><br>This may not be a problem, if the error states that the tables already exist, you should be right.";
  	       } else {
  	          $message = $message."Table 'repos' created successfully<br>";
  	       }

  	  $reponame = SQLite3::escapeString($reponame);
  	  $repourl = SQLite3::escapeString($repourl); 	  


  	  $result = $db->query("INSERT INTO repos (`id`,`name`, `url`) VALUES ('".hexdec(uniqid())."','$reponame','$repourl')");
  	     if(!$result){
  	        $error = $error.'Repo entry not created<br>'.$db->lastErrorMsg().'<br>';
  	     } else {
  	        $message = $message.'Repo settings and database have been created<br>You may now create a user account';
  	     }
  	     $db->close();

  } else if (!file_exists($repofile)) {
    $error = $error."reposettings.php and/or admin/includes/repo.db failed to create<br>Check your webserver logs for errors";
  }



} //repoconfig


if(isset($_POST['register'])){


  $_SESSION['name'] = $_POST['name'];
  $_SESSION['email'] = $_POST['email'];
  $_SESSION['password'] = $_POST['password'];
  $_SESSION['confirm_password'] = $_POST['confirm_password'];

  if(strlen($_POST['name'])<3){
    $error ="Name must be at least 3 chatacters long";
  }
  else if(strlen($_POST['password'])<8){
    $error ="Password should be at least 8 characters";
  }
  else if(strlen($_POST['confirm_password'])<8){
    $error ="Confirm Password should be at least 8 characters";
  }
  else if($_POST['password'] != $_POST['confirm_password']){
    $error ="Password and Confirm Password do not match";
  } else {
    $name = SQLite3::escapeString($_POST['name']);
    $email = SQLite3::escapeString($_POST['email']);
    $password = SQLite3::escapeString(password_hash($_POST['password'], PASSWORD_BCRYPT));

    $checkdupe = $db->query("SELECT * FROM users WHERE email='$email'");
    $dupe = ($checkdupe->fetchArray());

    if(!$dupe){
    	$result = $db->query("INSERT INTO users (id,name,email,password) VALUES('".hexdec(uniqid())."','$name','$email','$password')");
    	if(!$result){
    		$error = 'User not created<br>'.$db->lastErrorMsg()."<br>";
    	} else {
    		$message = 'Account Created<br>If you do not require any more users, you should delete the install directory now<br><br><a href ="../" class="btn waves-effect waves-light">Proceed to Login</a><br>';
    	}

    } else {
    	$error = "Email in use, please use another";
    }


  }
} //register

?>

<!DOCTYPE html>
<html>
<head>
  <!--Import Google Icon Font-->
  <link href="http://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <!--Import materialize.css-->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.5/css/materialize.min.css">
  <link rel="stylesheet" type="text/css" href="../index.css">
  <!--Let browser know website is optimized for mobile-->
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Repo Admin Install</title>
</head>

<body>
  <header>
    <nav>
      <div class="nav-wrapper blue-grey darken-4">
        <a class="brand-logo center">PHPInstallMiiRepoAdmin Installer</a>
      </div>
    </nav>
  </header>     
  <main>
    <br>
    <div class="container" id="top">
      <?php if(isset($error)) { ?>
      <div class="row">
        <div class="col s12 m6 offset-m3 center-align">
          <div class="card-panel red">
            <p class="white-text"><?php echo $error; ?></p>
          </div>       
        </div>
      </div>
      <?php } ?>
      <?php if(isset($message)) { ?>
      <div class="row">
        <div class="col s12 m6 offset-m3 center-align">
          <div class="card-panel green">
            <p class="white-text"><?php echo $message; ?></p>
          </div>       
        </div>
      </div>
      <?php } ?>
      <div class="row">
        <div class="col s12 m10 offset-m1 center-align">
          <h3>Step 1: Repo Settings and Database Creation</h3>
          <p>These details are used on the index page of your repo<br>The SQLite Database will also be created</p>
          <form method="post">
            <div class="input-field col s12 m6">
              <input required name="reponame" type="text">
              <label for="reponame">Repo Name</label>
            </div>
            <div class="input-field col s12 m6">
              <input required name="repoowner" type="text">
              <label for="repoowner">Repo Owner</label>
            </div>
            <div class="input-field col s12 m6">
              <input required name="repourl" type="text">
              <label for="repourl">Repo URL</label>
            </div>
            <div class="input-field col s12 m6">
              <input required name="repoblurb" type="text">
              <label for="repoblurb">Repo Blurb Text</label>
            </div>
            <button class="btn waves-effect waves-light" type="submit" name="repoconfig">Create Repo Settings/Database
              <i class="material-icons left">done</i>
            </button>
          </form>
        </div>
      </div>
      <div class="row">
        <div class="col s12 m10 offset-m1 center-align">
          <h3>Step 2: Admin User Creation</h3>
          <p>Fill out the form with the deisred details<br>Multiple Admins can be added if needed</p>
          <form method="post">
           <div class="row">
             <div class="input-field col s12 m6">
               <input type="text" name="name" required value="<?php echo @$_SESSION['name']; ?>">
               <label for="name">Name</label>
             </div>
             <div class="input-field col s12 m6">
               <input name="email" type="email" class="validate" required value="<?php echo @$_SESSION['email']; ?>">
               <label for="email">Email</label>
             </div>
             <div class="input-field col s12 m6">
               <input name="password" type="password" class="validate" required value="<?php echo @$_SESSION['password']; ?>">
               <label for="password">Password</label>
             </div>
             <div class="input-field col s12 m6">
               <input name="confirm_password" type="password" class="validate" required value="<?php echo @$_SESSION['confirm_password']; ?>">
               <label for="confirm_password">Confirm Password</label>
             </div>
             <div class="col s12 input-field">
               <button class="btn waves-effect waves-light" type="submit" name="register">Register
                 <i class="material-icons right">send</i>
               </button>                      
             </div>
           </div>
         </form>
       </div>
     </div>
   </div>
 </div>
</div>
</main>
<footer class="page-footer blue-grey darken-3">
  <div class="container ">
    <div class="row ">
      <div class="col l6 s12 ">
        <p class="grey-text text-lighten-3">PHPInstallMiiRepo by ChaosJester and LiquidFenrir</p>
      </div>
    </div>
  </div>
  <div class="footer-copyright blue-grey darken-2">
    <div class="container">
      Created with PHP InstallMii Repo creator.
      <a class="grey-text text-lighten-4 right" href="https://github.com/chaosjester/PHPInstallMiiRepo" target="_blank">Project GitHub page</a>
    </div>
  </div>
</footer>



<!--Import jQuery before materialize.js-->
<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.1.min.js"></script>
<!-- Compiled and minified JavaScript -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.5/js/materialize.min.js"></script>
<script src="custom.js"></script>
</body>
</html>