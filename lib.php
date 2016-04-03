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

    $citfolder = $CFG->dirroot."/blocks/eexcess/citationStyles";
    $filearr = get_directory_list($citfolder);
    $citarr = array();
    $citstyles = array();
    $i = 0;
    foreach ($filearr as $value) {
        $filepath = $citfolder."/".$value;
        $filecontent = file_get_contents($filepath);
        $simplexml = simplexml_load_string($filecontent);
        $name = (string) $simplexml->info->title;
        $citarr[] = $filecontent;
        $citstyles[] = array("label" => $name, "val" => "$i", "content" => $filecontent);
        $i++;
    }
    $userid = $USER->id;
    $countlicense = 0;
    $adminlicenseimg = get_config('block_eexcess', 'img_license');
    if (strlen($adminlicenseimg) > 0) {
        $adminlicenseimgarr = explode(',', $adminlicenseimg);
        foreach ($adminlicenseimgarr as $value) {
            $adminlicensefinal[$countlicense] = trim($value);
            $countlicense++;
        }
    } else {
        $adminlicensefinal = false;
    }
    $userlicenseimg = $DB->get_records('block_eexcess_image_license', array("userid" => $userid));
    $countuserlicense = 0;
    if (count($userlicenseimg) > 0) {
        foreach ($userlicenseimg as $value) {
            $userlicenseimgfinal[$countuserlicense] = trim($value->license);
            $countuserlicense++;
        }
    } else {
        $userlicenseimgfinal = false;
    }
    if ($adminlicensefinal !== false && $userlicenseimgfinal !== false) {
        $alllicenses = array_merge ($adminlicensefinal, $userlicenseimgfinal);
    } else if ($adminlicensefinal !== false && $userlicenseimgfinal === false) {
        $alllicenses = $adminlicensefinal;
    } else if ($adminlicensefinal === false && $userlicenseimgfinal !== false) {
        $alllicenses = $userlicenseimgfinal;
    } else {
        $alllicenses = false;
    }
    $adminsettings = get_config('block_eexcess', 'citation');
    $tablename = "block_eexcess_citation";
    $usersettings = $DB->get_record($tablename, array("userid" => $userid), $fields = '*');
    if ($usersettings != false) {
        if (is_numeric($usersettings->citation)) {
            $styleid = $citarr[intval($usersettings->citation)];
        } else if ($usersettings->citation != false) {
            $styleid = $usersettings->citation;
        }
    } else {
        if (is_numeric($adminsettings)) {
            $styleid = $citarr[intval($adminsettings)];
        } else if ($adminsettings != false) {
            $styleid = $adminsettings;
        } else {
            $styleid = "lnk";
        }
    }
    $systemcontext = context_system::instance();
    if (isloggedin() && has_capability('block/eexcess:myaddinstance', $systemcontext)) {

        return array("defaultCitStyle" => $styleid, "citStyles" => $citstyles, "userId" => $userid, "imgLicense" => $alllicenses);

    } else {
        $resperror = get_string('noaccess', 'atto_eexcesseditor');
        return array("defaultCitStyle" => $styleid, "citStyles" => $citstyles, "userId" => $userid, "respError" => $resperror);
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

    // Make sure the user is logged in and has access to the module (plugins that are not course modules should leave
    // out the 'cm' part).
    require_login($course, true, $cm);

    // Extract the filename / filepath from the $args array.
    $filename = array_pop($args); // The last item in the $args array.
    if (!$args) {
        $filepath = '/'; // $args is empty => the path is '/'.
    } else {
        $filepath = '/'.implode('/', $args).'/'; // $args contains elements of the filepath.
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