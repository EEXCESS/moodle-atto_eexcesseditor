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
 
define('AJAX_SCRIPT', true);
require_once(dirname(__FILE__) . '/../../../../../config.php');
$screensFolder = $CFG->dirroot."/lib/editor/atto/plugins/eexcesseditor/pix/screenshots/";
$imgUrl = $CFG->wwwroot."/lib/editor/atto/plugins/eexcesseditor/pix/screenshots/";
$guid = uniqid();
$filename = $guid."_file.png";
$tmppath = $screensFolder.$filename;
$data = $_POST["imgdata"];
$fdata = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $data));
file_put_contents($tmppath, $fdata);
$context = context_system::instance();
$fs = get_file_storage();
$file_record = array(
    'contextid'=>$context->id,
    'component'=>'local_eexcess',
    'filearea'=>'screenshot',
    'itemid'=>0,
    'filepath'=>'/',
    'filename'=>$filename,
    'timecreated'=>time(),
    'timemodified'=>time());

$file = $fs->create_file_from_pathname($file_record, $tmppath);
unlink($tmppath);
$fullpath = "{$CFG->wwwroot}/pluginfile.php/{$file->get_contextid()}/local_eexcess/screenshot/{$file->get_itemid()}/{$file->get_filename()}";
echo $fullpath;