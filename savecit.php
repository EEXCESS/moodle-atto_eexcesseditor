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