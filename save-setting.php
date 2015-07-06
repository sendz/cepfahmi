<?php


error_reporting(E_ALL);
ini_set('display_errors',1);
  $username = $_POST['username'];
  $password = $_POST['password'];

  if($username && $password)
  {
    $f = fopen('config.php','r+') or die ("can not open file");
    fwrite($f,'<?php $hostname = "{imap.gmail.com:993/imap/ssl}INBOX"; $username="'.$username.'"; $password="'.addslashes($password).'"; ?>');
    fclose($f);
    header('location:setting.php');
  }
