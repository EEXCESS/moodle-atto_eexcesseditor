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
 * Atto text editor eexcesseditor plugin lib.
 *
 * @package    atto_eexcesseditor
 * @copyright  bit media e-solutions GmbH <gerhard.doppler@bitmedia.cc>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Initialise the strings required for JS.
 */
function atto_eexcesseditor_strings_for_js() {
    global $PAGE;

    $PAGE->requires->string_for_js('citationstyle', 'atto_eexcesseditor');
    $PAGE->requires->string_for_js('add_license', 'atto_eexcesseditor');
}
/**
 * Initialise the parameters required for JS.
 */
function atto_eexcesseditor_params_for_js() {
    global $CFG;
    global $DB;
    global $USER;

    $cit_folder = $CFG->dirroot."/blocks/eexcess/citationStyles";
    $fileArr = get_directory_list($cit_folder);
    $cit_arr = array();
    $cit_styles = array();
    $i = 0;
    foreach($fileArr as $value) {
        $file_path = $cit_folder."/".$value;
        $file_content = file_get_contents($file_path);
        $simple_xml = simplexml_load_string($file_content);
        $name = (string) $simple_xml->info->title;
        $cit_arr[] = $file_content;
        $cit_styles[] = array("label"=>$name,"val"=>"$i","content"=>$file_content);
        $i++;
    }
    $userid = $USER->id;
    $countlicense = 0;
    $admin_license_img = get_config('block_eexcess','img_license');
    if(strlen($admin_license_img) > 0) {
        $admin_license_img_arr = explode(',', $admin_license_img);
        foreach($admin_license_img_arr as $value){
            $admin_license_final[$countlicense] = trim($value);
            $countlicense++;
        }
    } else {
        $admin_license_final = false;
    }
    $user_license_img = $DB->get_records('block_eexcess_image_license', array("userid"=>$userid));
    $count_user_license = 0;
    if(count($user_license_img) > 0){
        foreach($user_license_img as $value){
            $user_license_img_final[$count_user_license] = trim($value->license);
            $count_user_license++;
        }
    } else {
        $user_license_img_final = false;
    }
    if($admin_license_final !== false && $user_license_img_final !== false){
        $all_licenses = array_merge ($admin_license_final, $user_license_img_final);
    } else if($admin_license_final !== false && $user_license_img_final === false){
        $all_licenses = $admin_license_final;
    } else if($admin_license_final === false && $user_license_img_final !== false){
        $all_licenses = $user_license_img_final;
    } else {
        $all_licenses = false;
    }
    $admin_settings = get_config('block_eexcess','citation');
    $tablename = "block_eexcess_citation";
    $user_settings = $DB->get_record($tablename, array("userid"=>$userid), $fields='*');
    if($user_settings != false) {
        if(is_numeric($user_settings->citation)){
            $styleid = $cit_arr[intval($user_settings->citation)];
        } elseif($user_settings->citation!=false) {
            $styleid = $user_settings->citation;
        }
    } else {
        if(is_numeric($admin_settings)) {
            $styleid = $cit_arr[intval($admin_settings)];
        } elseif($admin_settings != false) {
            $styleid = $admin_settings;
        } else {
            $styleid = "lnk";
        }
    }
    $systemcontext = context_system::instance();
    if (isloggedin() && has_capability('block/eexcess:myaddinstance', $systemcontext)) {

        return array("defaultCitStyle"=>$styleid, "citStyles"=>$cit_styles, "userId"=>$userid, "imgLicense"=>$all_licenses);

    } else {
        $resp_error = get_string('noaccess', 'atto_eexcesseditor');
        return array("defaultCitStyle"=>$styleid,"citStyles"=>$cit_styles, "userId"=>$userid, "respError"=>$resp_error);
    }
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
    send_file($file, 86400, 0, $forcedownload, $options);
}