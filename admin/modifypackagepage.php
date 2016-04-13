<?php
session_start();

require ("../reposettings.php");

class MyDB extends SQLite3
{
   function __construct()
   {
      $this->open('./includes/repo.db');
   }
}
$db = new MyDB();
if(!$db){
   $error = $db->lastErrorMsg();
}


if(!isset($_SESSION['name']))
{
  $_SESSION['error'] = "Either you are not logged in or your username and/or password are incorrect. Please try again.";
 header("Location: index.php");
 exit();
}

if(isset($_GET['id'])){
  $packageid = $_GET['id'];
}

if(isset($_POST['modifypackage'])) {


  $id = SQLite3::escapeString($packageid);
  $name = SQLite3::escapeString($_POST['name']);
  $desc = SQLite3::escapeString($_POST['desc']);
  $author = SQLite3::escapeString($_POST['author']);
  $category = SQLite3::escapeString($_POST['category']);
  $website = SQLite3::escapeString($_POST['website']);
  $type = SQLite3::escapeString("3ds"); // This always needs to be 3ds for InstallMii to install it correctly
  $version = SQLite3::escapeString($_POST['version']);
  $dl_path = SQLite3::escapeString($_POST['dl_path']);
  $info_path = SQLite3::escapeString($_POST['info_path']);

  $query = "UPDATE packages SET name='$name', short_description='$desc', author='$author', category='$category', website='$website', type='$type', version='$version', dl_path='$dl_path', info_path='$info_path'
  WHERE `id`='$id'";

  $result = $db->query($query);
  if ($result) {
    $message = "Package Modified successfully<br>Redirecting back to package list in 3 seconds";
  $db->close();
} else {
  $error = 'Error updating package<br>'.$db->lastErrorMsg().'<br><br>';
  
}

} 


?>

<!DOCTYPE html>
<html>
<head>
  <!--Import Google Icon Font-->
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <!--Import materialize.css-->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.5/css/materialize.min.css">
  <link rel="stylesheet" type="text/css" href="custom.css">
  <!--Let browser know website is optimized for mobile-->
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <link rel='shortcut icon' type='image/x-icon' href='../favicon.ico' />
  <title>Delete Packages</title>
</head>

<body>
<?php  if ($_SESSION['devtype'] == TRUE){
  include('3dshead.php'); 
} else {

  include('header.php'); } ?>     
  <main>
    <br>
    <div class="container">
      <div class="row">
        <div class="col s12 m12 center-align">

          <?php if(isset($_POST['modifypackage'])){ ?>
          <div class="row">
          <div class="col s12 m6 offset-m3">
              <div class="card-panel green">
                <span class="white-text"><?php echo $message; ?>
                </span>
              </div>
            </div>
          </div>
          <?php header( "refresh:3;url=viewpackage.php" ); } ?>
          <?php if(isset($error)){ ?>
          <div class="row">
          <div class="col s12 m6 offset-m3">
              <div class="card-panel red">
                <span class="white-text"><?php echo $error; ?>
                </span>
              </div>
            </div>
          </div>
          <?php } ?>
          <form method="post">
        <?php 
        

              $query="SELECT * FROM packages WHERE `id`='$packageid'";
              $results = $db->query($query);

              while ($row = ($results->fetchArray(SQLITE3_ASSOC))) { ?>

              <div class="input-field col s12 m6">
                <input name="name" type="text" value="<?php echo $row['name']; ?>" onfocus="if(this.value=='<?php echo $row['name']; ?>') this.value='<?php echo $row['name']; ?>';">
                <label for="name">Package Name</label>
              </div>
              <div class="input-field col s12 m6">
                <input name="desc" type="text" value="<?php echo $row['short_description']; ?>" onfocus="if(this.value=='<?php echo $row['short_description']; ?>') this.value='<?php echo $row['short_description']; ?>';">
                <label for="desc">*Package Description</label>
              </div>
              <div class="input-field col s12 m6">
                <input name="author" type="text" value="<?php echo $row['author']; ?>" onfocus="if(this.value=='<?php echo $row['author']; ?>') this.value='<?php echo $row['author']; ?>';">
                <label for="author">Package Author</label>
              </div>
              <div class="input-field col s12 m6">
                <select name="category">
                  <option value="Games">Games</option>
                  <option value="Application">Applications</option>
                  <option value="Tools">Tools</option>
                </select>
                <label for="category">Package Category</label>
              </div>
              <div class="input-field col s12 m6">
                <input name="website" type="text" value="<?php echo $row['website']; ?>" onfocus="if(this.value=='<?php echo $row['website']; ?>') this.value='<?php echo $row['website']; ?>';">
                <label for="website">Website</label>
              </div>
              <div class="input-field col s12 m6">
                <input name="version" type="text" value="<?php echo $row['version']; ?>" onfocus="if(this.value=='<?php echo $row['version']; ?>') this.value='<?php echo $row['version']; ?>';">
                <label for="version">Package Version</label>
              </div>
              <div class="input-field col s12 m6">
                <input name="dl_path" type="text" value="<?php echo $row['dl_path']; ?>" onfocus="if(this.value=='<?php echo $row['dl_path']; ?>') this.value='<?php echo $row['dl_path']; ?>';">
                <label class="tooltipped" for="dl_path" data-position="top" data-delay="50" data-tooltip="Usually 3ds/packagename/">Download Path <i class="tiny material-icons">info_outline</i></label>
              </div>
              <div class="input-field col s12 m6">
                <input name="info_path" type="text" value="<?php echo $row['info_path']; ?>" onfocus="if(this.value=='<?php echo $row['info_path']; ?>') this.value='<?php echo $row['info_path']; ?>';">
                <label class="tooltipped" for="info_path" data-position="top" data-delay="50" data-tooltip="Usually 3ds/packagename/packagefile.smdh">SMDH Path <i class="tiny material-icons">info_outline</i></label>
              </div>
              <button class="btn waves-effect waves-light" type="submit" name="modifypackage">Save Changes
                <i class="material-icons left">done</i>
              </button>
              <?php } ?>
          </form>

        </div>
      </div>
    </div>
  </main>
<?php include("footer.php");?>



  <!--Import jQuery before materialize.js-->
  <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.1.min.js"></script>
  <!-- Compiled and minified JavaScript -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.5/js/materialize.min.js"></script>
  <script src="custom.js"></script>
</body>
</html>