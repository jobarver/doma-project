<?php
  include_once(dirname(__FILE__) ."/config.php");
  include_once(dirname(__FILE__) ."/include/definitions.php");
  include_once(dirname(__FILE__) ."/include/db_scripts.php");
  
  class CreateController
  {
    public function Execute()
    {
      $viewData = array();  

      // create new session
      session_start();

      $errors = array();

      // load strings
      Session::SetLanguageStrings(Helper::GetLanguageStrings());

      // check php version  
      if(version_compare(phpversion(), "5.0.0") < 0) $errors[] = sprintf(__("TOO_OLD_PHP_VERSION"), phpversion()); 

      if(count($errors) == 0)
      {
        $previousDatabaseVersion = DataAccess::GetSetting("DATABASE_VERSION", "0.0");
        
        if(Helper::DatabaseVersionIsValid()) $errors[] = __("SITE_ALREADY_CREATED");
        
        if(count($errors) == 0)
        {
          // create database
          $result = executeDatabaseScripts();
          $errors = $result["errors"];
          // chmod only has effext on linux/unix systems
          @mkdir(Helper::LocalPath(MAP_IMAGE_PATH));
          @chmod(Helper::LocalPath(MAP_IMAGE_PATH), 0777);
          @mkdir(Helper::LocalPath(TEMP_FILE_PATH));
          @chmod(Helper::LocalPath(TEMP_FILE_PATH), 0777);

		      /* processed by external page _upgrade_301.php - avoiding memory limit errors and php timeouts
          if (version_compare(DataAccess::GetSetting("DATABASE_VERSION", "0.0"),"2.1")>0)
		      {
			      $mp = DataAccess::GetAllMaps(0);
			      if(count($mp) > 0)
			      {
			        foreach($mp as $map)
 			        {
                $map->AddGeocoding();
                DataAccess::SaveMapWaypoints($map);
				        $map->Save();
			        }
			      }
          }
          */

          if($previousDatabaseVersion == "0.0")
          {
            Helper::LogUsage("createSite", "version=". DOMA_VERSION);
            Helper::LoginAdmin(ADMIN_USERNAME, ADMIN_PASSWORD);
          }
          else
          {
            Helper::LogUsage("updateSite", "version=". DOMA_VERSION);
            Helper::Redirect("users.php");
          }
        }
      }
      $viewData["Errors"] = $errors;
  
      return $viewData;
    }
  }
?>