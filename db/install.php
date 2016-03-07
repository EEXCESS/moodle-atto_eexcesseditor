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
 * Install utility.
 *
 * @package    atto_eexcesseditor
 * @copyright  bit media e-solutions GmbH <gerhard.doppler@bitmedia.cc>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Enable ATTO EEXCESSEDITOR plugin buttons on installation.
 */
function xmldb_atto_eexcesseditor_install() {
    $toolbar = get_config('editor_atto', 'toolbar');
    if (strpos($toolbar, 'eexcesseditor') === false) {
        $groups = explode("\n", $toolbar);
        // Try to put wiris in other group
        $found = false;
        foreach ($groups as $i => $group) {
            $parts = explode('=', $group);
            if (trim($parts[0]) == 'other') {
                $groups[$i] = 'other = ' . trim($parts[1]) . ', eexcesseditor';
                $found = true;
            }
        }
        // Otherwise create a math group in the second position starting from 
        // the end.
        if (!$found) {
            do {
                $last = array_pop($groups);
            } while(empty($last) && !empty($groups));
            $groups[] = 'other = eexcesseditor';
            $groups[] = $last;
        }
        // Update config variable.
        $toolbar = implode("\n", $groups);
        set_config('toolbar', $toolbar, 'editor_atto');
    }
}