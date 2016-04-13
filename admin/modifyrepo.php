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

if(isset($_POST['modifyrepo'])) {


  $id = SQLite3::escapeString($packageid);
  $name = SQLite3::escapeString($_POST['name']);
  $url = SQLite3::escapeString($_POST['url']);
 

  $query = "UPDATE repos SET name='$name', url='$url'
  WHERE `id`='$id'";

$result = $db->query($query);

if(!$result){
    $error =  "Repo not updated<br>".$db->lastErrorMsg();
} else {

  $message = "Repo Modified successfully<br>Redirecting back to Repo list in 3 seconds";
  $db->close();
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
  <title>Modigy Repo List</title>
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

          <?php if(isset($_POST['modifyrepo'])){ ?>
          <div class="row">
          <div class="col s12 m6 offset-m3">
              <div class="card-panel green">
                <span class="white-text"><?php echo $message; ?>
                </span>
              </div>
            </div>
          </div>
          <?php header( "refresh:3;url=repolist.php" ); } ?>
          <form method="post">
        <?php 
        

              $query="SELECT * FROM repos WHERE `id`='$packageid'";
              $results = $db->query($query);

              while ($row = ($results->fetchArray(SQLITE3_ASSOC))) { ?>

              <div class="input-field col s12 m6">
                <input name="name" type="text" value="<?php echo $row['name']; ?>" onfocus="if(this.value=='<?php echo $row['name']; ?>') this.value='<?php echo $row['name']; ?>';">
                <label for="name">Repo Name</label>
              </div>
              <div class="input-field col s12 m6">
                <input name="url" type="text" value="<?php echo $row['url']; ?>" onfocus="if(this.value=='<?php echo $row['url']; ?>') this.value='<?php echo $row['url']; ?>';">
                <label for="url">Repo URL</label>
              </div>
              <button class="btn waves-effect waves-light" type="submit" name="modifyrepo">Save Changes
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