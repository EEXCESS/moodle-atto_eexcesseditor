<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Atto text editor integration version file.
 *
 * @package    atto_eexcesseditor
 * @copyright  bit media e-solutions GmbH <gerhard.doppler@bitmedia.cc>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
function atto_eexcesseditor_strings_for_js() {
    global $PAGE;

    $PAGE->requires->strings_for_js(array('opendashboard', 'citationstyle'), 'atto_eexcesseditor');
}
function atto_eexcesseditor_params_for_js(){
	global $CFG;
	global $DB;
	global $USER;
	$citFolder = $CFG->dirroot."/local/eexcess/citationStyles";

	$fileArr = get_directory_list($citFolder);
	$citArr = array();
	$citStyles = array();
	$i = 0;
	foreach($fileArr as $value){
		$file_path = $citFolder."/".$value;
		$file_content = file_get_contents($file_path);
		$simpleXML = simplexml_load_string($file_content);
		$name = (string) $simpleXML->info->title;
		$citArr[] = $file_content;
		$simpleXML = simplexml_load_string($file_content);
		$name = (string) $simpleXML->info->title;
		$citStyles[] = array("label"=>$name,"val"=>"$i","content"=>$file_content);
		$i++;
	}
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
		$styleid = "lnk";
	}

	return array("defaultCitStyle"=>$styleid,"citStyles"=>$citStyles,"userId"=>$userid);
}

function atto_eexcesseditor_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options=array()) {
    // Check the contextlevel is as expected - if your plugin is a block, this becomes CONTEXT_BLOCK, etc.
    if ($context->contextlevel != CONTEXT_MODULE) {
        return false;
    }

    // Make sure the filearea is one of those used by the plugin.
    if ($filearea !== 'screenshot') {
        return false;
    }

    // Make sure the user is logged in and has access to the module (plugins that are not course modules should leave out the 'cm' part).
    require_login($course, true, $cm);
 	/*
    // Check the relevant capabilities - these may vary depending on the filearea being accessed.
    if (!has_capability('mod/MYPLUGIN:view', $context)) {
        return false;
    }
 	*/
    // Leave this line out if you set the itemid to null in make_pluginfile_url (set $itemid to 0 instead).
    //$itemid = array_shift($args); // The first item in the $args array.
 
    // Use the itemid to retrieve any relevant data records and perform any security checks to see if the
    // user really does have access to the file in question.
 
    // Extract the filename / filepath from the $args array.
    $filename = array_pop($args); // The last item in the $args array.
    if (!$args) {
        $filepath = '/'; // $args is empty => the path is '/'
    } else {
        $filepath = '/'.implode('/', $args).'/'; // $args contains elements of the filepath
    }
 
    // Retrieve the file from the Files API.
    $fs = get_file_storage();
    $file = $fs->get_file($context->id, 'atto_eexcesseditor', $filearea, $itemid, $filepath, $filename);
    if (!$file) {
        return false; // The file does not exist.
    }
 	echo var_dump($file);
    // We can now send the file back to the browser - in this case with a cache lifetime of 1 day and no filtering. 
    // From Moodle 2.3, use send_stored_file instead.
    send_file($file, 86400, 0, $forcedownload, $options);
}