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
 * Save screenshot.
 *
 * @package    atto_eexcesseditor
 * @copyright  bit media e-solutions GmbH <gerhard.doppler@bitmedia.cc>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('AJAX_SCRIPT', true);
require_once(dirname(__FILE__) . '/../../../../../config.php');
$screensfolder = $CFG->dirroot."/lib/editor/atto/plugins/eexcesseditor/pix/screenshots/";
$imgurl = $CFG->wwwroot."/lib/editor/atto/plugins/eexcesseditor/pix/screenshots/";
$guid = uniqid();
$filename = $guid."_file.png";
$tmppath = $screensfolder.$filename;
$data = optional_param("imgdata", false, PARAM_TEXT);
$fdata = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $data));
file_put_contents($tmppath, $fdata);
$context = context_system::instance();
$fs = get_file_storage();
$filerecord = array(
    'contextid' => $context->id,
    'component' => 'block_eexcess',
    'filearea' => 'screenshot',
    'itemid' => 0,
    'filepath' => '/',
    'filename' => $filename,
    'timecreated' => time(),
    'timemodified' => time());

$file = $fs->create_file_from_pathname($filerecord, $tmppath);
unlink($tmppath);
$fullpath = "{$CFG->wwwroot}/pluginfile.php/{$file->get_contextid()}/block_eexcess/screenshot/{$file->get_itemid()}/{$file->get_filename()}";
echo $fullpath;