<?php
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
