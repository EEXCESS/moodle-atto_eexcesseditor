<?php
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