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
 * Save citation style.
 *
 * @package    atto_eexcesseditor
 * @copyright  bit media e-solutions GmbH <gerhard.doppler@bitmedia.cc>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('AJAX_SCRIPT', true);

require_once(dirname(__FILE__) . '/../../../../../config.php');
$tablename = "block_eexcess_citation";
$userid = $USER->id;

$systemcontext = context_system::instance();
if (isloggedin() && has_capability('block/eexcess:myaddinstance', $systemcontext)) {
    $citstyle = optional_param('citstyle', false, PARAM_TEXT);
    $usersetting = $DB->get_record($tablename, array("userid" => $userid), $fields = '*', $strictness = IGNORE_MISSING);
    if ($usersetting === false) {
        // Insert.
        $s = new stdClass();
        $s->id = null;
        $s->userid = $userid;
        $s->citation = $citstyle;
        $r = $DB->insert_record($tablename, $s);
    } else {
        // Update.
        $usersetting->citation = $citstyle;
        $r = $DB->update_record($tablename, $usersetting);
    }
    echo json_encode(array("res", $r));
}
