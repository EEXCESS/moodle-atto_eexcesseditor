<?php

function atto_eexcesseditor_params_for_js(){
	global $CFG;
	global $DB;
	global $USER;
	$citFolder = $CFG->dirroot."/local/eexcess/citationStyles";
	
	$fileArr = get_directory_list($citFolder);
	$citArr = array();
	
	foreach($fileArr as $value){
		$file_path = $citFolder."/".$value;
		$file_content = file_get_contents($file_path);
		$simpleXML = simplexml_load_string($file_content);
		$name = (string) $simpleXML->info->title;
		$citArr[] = $file_content;
		
	}
	$citArr["lnk"] = "lnk";
	$adminSettings = get_config('local_eexcess','citation');
	
	$tablename = "local_eexcess_citation";
	$userid=$USER->id;
	$userSettings = $DB->get_record($tablename, array("userid"=>$userid), $fields='*', $strictness=IGNORE_MISSING);
	
	
	if(is_numeric($userSettings->citation)){
		$styleid = $citArr[intval($userSettings->citation)];
	}elseif($userSettings->citation!=false){
		$styleid = $userSettings->citation;
	}elseif(is_numeric($adminSettings)){
		$styleid = $citArr[intval($adminSettings)];
	}elseif($adminSettings != false){
		$styleid = $adminSettings;
	}else{
		$styleid = $citArr["lnk"];
	}
	
	return array("citStyles"=>$styleid,"userId"=>$userid);
}
