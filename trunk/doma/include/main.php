<?php
  include_once(dirname(__FILE__) ."/../config.php");
  include_once(dirname(__FILE__) ."/definitions.php");

  // set character encoding
  header('Content-Type: text/html; charset=utf-8');

  // load session
  session_start();
  
  // create database if it does not exist
  if(!Helper::DatabaseVersionIsValid()) Helper::Redirect("create.php?redirectUrl=". urlencode($_SERVER["REQUEST_URI"]));
  
  // extract current user from querystring
  if($_GET["user"])
  {
    $currentUser = getUser();
    if(!$currentUser || $currentUser->Username != $_GET["user"] || !Session::GetLanguageStrings() || Session::GetLanguageCode() != LANGUAGE_CODE)
    {
      Helper::SetUser(DataAccess::GetUserByUsername($_GET["user"]));
    }
  }
  else
  {
    Helper::SetUser(null);
  }
?>