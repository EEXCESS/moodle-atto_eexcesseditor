<?php
function atto_eexcesseditor_params_for_js(){
	global $CFG;
	$citFolder = $CFG->dirroot."/local/eexcess/citationStyles";
	$fileArr = get_directory_list($citFolder);
	$citArr = array();
	
	foreach($fileArr as $value){
		$file_path = $citFolder."/".$value;
		$file_content = file_get_contents($file_path);
		$simpleXML = simplexml_load_string($file_content);
		$name = (string) $simpleXML->info->title;
		$citArr[] = array("name"=>$name,"style"=>$file_content);
		
	}
	return array("citStyles"=>$citArr);
}