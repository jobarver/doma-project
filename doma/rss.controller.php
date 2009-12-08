<?php
  include_once(dirname(__FILE__) ."/include/main.php");
  
  class RSSController
  {
    public function Execute()
    {
      $viewData = array();  

      // check if user is not specified or hidden
      if(!getUser())
      {
        // all maps in archive
        $mode = "all";
        $count = $_GET["count"];
        if(!$count) $count = 30;
        $maps = DataAccess::GetMaps(0, 0, 0, 0, $count, "ID");
        $categories = DataAccess::GetCategoriesByUserID();
        $users = DataAccess::GetAllUsers(true);
        $viewData["Title"] = _SITE_TITLE;
        $viewData["LastCreatedTime"] = date("r", DataAccess::GetLastCreatedTime());
        $viewData["Description"] = _SITE_DESCRIPTION;
        $viewData["WebsiteUrl"] = Helper::GetWebsiteUrl() ."/index.php";
      }
      else
      {
        // specified archive
        $mode = "user";
        if(!getUser()->Visible) die();
        $users[getUser()->ID] = getUser();
        $maps = DataAccess::GetMaps(getUser()->ID);
        $categories = DataAccess::GetCategoriesByUserID(getUser()->ID);
        $viewData["Title"] = __("PAGE_TITLE");
        $viewData["LastCreatedTime"] = date("r", DataAccess::GetLastCreatedTime(getUser()->ID));
        $viewData["Description"] = __("RSS_DESCRIPTION");
        $viewData["WebsiteUrl"] = Helper::GetWebsiteUrl() ."/index.php?". Helper::CreateQuerystring(getUser());
      }

      foreach($maps as $map)
      {
        $item = array();
        $user = $users[$map->UserID];
        $item["Title"] = hsc(Helper::DateToLongString(Helper::StringToTime($map->Date, true)) .": ". $map->Name);
        $item["URL"] = ($map->MapImage ? Helper::GetWebsiteUrl() .'/show_map.php?user='. urlencode($user->Username) .'&amp;map='. $map->ID : "");
        
        $atoms = array();
        if(__("SHOW_MAP_AREA_NAME") && $map->MapName != "") $atoms[] = $map->MapName;
        if(__("SHOW_ORGANISER") && $map->Organiser != "") $atoms[] = $map->Organiser;
        if(__("SHOW_COUNTRY") && $map->Country != "") $atoms[] = $map->Country;

        $atoms2 = array();
        if(__("SHOW_DISCIPLINE") && $map->Discipline != "") $atoms2[] = hsc($map->Discipline);
        if(__("SHOW_RELAY_LEG") && $map->RelayLeg != "") $atoms2[] = __("RELAY_LEG_LOWERCASE") .' '. hsc($map->RelayLeg);
        
        $item["Description"] = 
          ($mode == "all" ? hsc($user->FirstName ." ". $user->LastName .'<br />') : '') .
          __("CATEGORY") .": ". hsc($categories[$map->CategoryID]->Name) . 
          hsc('<br />'). 
          hsc(join(", ", $atoms)) .
          hsc('<br />'). 
          join(", ", $atoms2);
        $item["PubDate"] = hsc(date("r", Helper::StringToTime($map->CreatedTime, true)));      
        $viewData["Items"][] = $item;      
      }
      
      return $viewData;
    }
  }
?>