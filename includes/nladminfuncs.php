<?php
/*
============================================================
    Newsletter for CMSimple_XH
============================================================
 *
 * @copyright 2025 The CMSimple_XH developers <http://cmsimple-xh.org/?The_Team>
 * @author    The CMSimple_XH developers <devs@cmsimple-xh.org>
 * @license    GNU GPLv3 - http://www.gnu.org/licenses/gpl-3.0.en.html
*/

/**
 * Checks system requirements
 *
 * @return html.
 */
function newsletter_Systemcheck($nl_cmsVersionArray, $nl_phpVersion) {

    global $pth;

    $o = '<h2>System Check</h2>' . "\n";
// CMSimple_XH Version
    $op_cms_version_tmp = CMSIMPLE_XH_VERSION;
    $op_cms_version_tmp = str_replace(array('CMSimple_XH '), '', $op_cms_version_tmp);
    if (version_compare($op_cms_version_tmp, $nl_cmsVersionArray[0], '<')) {
        $o .= '<p class="xh_warning">'
            . CMSIMPLE_XH_VERSION
            . ' &#x2192 I hope it is still supported. It was designed for '
            . $nl_cmsVersionArray[0]
            . ' - '
            . end($nl_cmsVersionArray)
            . '.</p>'
            . "\n";
    } else {
        $o .= '<p class="xh_success">'
            . CMSIMPLE_XH_VERSION
            . ' &#x2192 supported</p>'
            . "\n";
    }
// PHP Version
    if(version_compare(phpversion(), $nl_phpVersion, '<')) {
        $o .= '<p class="xh_fail">PHP: '
           . phpversion()
           . ' &#x2192 not supported</p>'
           . "\n";
    } else {
        $o .= '<p class="xh_success">PHP: '
           . phpversion()
           . ' &#x2192 supported</p>'
           . "\n";
    }
// check if Phpmailer_XH installed
    if (function_exists('phpmailer_create')) {
        $o .= '<p class="xh_success">Phpmailer_XH  is installed, (PHPMailer: '
            . PHPMailer\PHPMailer\PHPMailer::VERSION
            . ')</p>';
    } else {
        $o .= '<p class="xh_fail">Phpmailer_XH is not installed</p>';
    }
// write permissions
    $op_filename_arr = array($pth['file']['plugin_config'],
                             $pth['file']['plugin_stylesheet'],
                             $pth['folder']['plugin_languages'],
                             $pth['file']['plugin_language'],
                             $pth['folder']['plugin'] . 'data/');
    foreach($op_filename_arr as $op_filename) {
        if(is_writable($op_filename)) {
            $o .= '<p class="xh_success">'
               . $op_filename
               . ' &#x2192 writable</p>'
               . "\n";
        } else {
            $o .= '<p class="xh_fail">'
               . $op_filename
               . ' &#x2192 not writable</p>'
               . "\n";
        }
    }
// access protection
    $nlDataFolder = $pth['folder']['plugin'] . 'data/';
    $nlCheckDataFolder = XH_isAccessProtected($nlDataFolder);
    if($nlCheckDataFolder) {
        $o .= '<p class="xh_success"><a target="_blank" href="'
            . $nlDataFolder
            . '">'
            . $nlDataFolder
            . '&#x2192 access-protected</a></p>'
            . "\n";
    } else {
        $o .= '<p class="xh_fail"><a target="_blank" href="'
            . $nlDataFolder
            . '">'
            . $nlDataFolder
            . '&#x2192 not access-protected</a></p>'
            . "\n";
    }

    return $o;
}
