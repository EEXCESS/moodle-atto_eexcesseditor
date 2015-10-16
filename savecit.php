<?php
define('AJAX_SCRIPT', true);

require_once(dirname(__FILE__) . '/../../../../../config.php');
$tablename = "local_eexcess_citation";
$userid=$USER->id;
$citstyle = required_param('citstyle', PARAM_INT);
$user_setting = $DB->get_record($tablename, array("userid"=>$userid), $fields='*', $strictness=IGNORE_MISSING);
if($user_setting==false){
		//insert
		$s = new stdClass();
		$s->id = null;
		$s->userid = $userid;
		$s->citation = $citstyle;
		$r = $DB->insert_record($tablename,$s);
	}else{
		//update
		$user_setting->citation = $citstyle;
		$r = $DB->update_record($tablename,$user_setting);
		
	}
//$user_setting->citation = $citstyle;
//$DB->update_record($tablename,$user_setting);
echo json_encode(array("res",$r));