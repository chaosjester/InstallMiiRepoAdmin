<?php 
if (!file_exists("./includes/repo.db")){
  header('Location:./install');
  } else if(!file_exists("../reposettings.php")) {
    header('Location:./install');
  } else {

require ("../reposettings.php");
}
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
?>
