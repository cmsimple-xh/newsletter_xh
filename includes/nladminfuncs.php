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
function newsletterSystemcheck($cmsVersionArray, $phpVersion) {

    global $pth;

    $o = '<h2>System Check</h2>' . "\n";
// CMSimple_XH Version
    $cmsVersionTmp = CMSIMPLE_XH_VERSION;
    $cmsVersionTmp = str_replace(array('CMSimple_XH '), '', $cmsVersionTmp);
    if (version_compare($cmsVersionTmp, $cmsVersionArray[0], '<')) {
        $o .= '<p class="xh_warning">'
            . CMSIMPLE_XH_VERSION
            . ' &#x2192 I hope it is still supported. It was designed for '
            . $cmsVersionArray[0]
            . ' - '
            . end($cmsVersionArray)
            . '.</p>'
            . "\n";
    } else {
        $o .= '<p class="xh_success">'
            . CMSIMPLE_XH_VERSION
            . ' &#x2192 supported</p>'
            . "\n";
    }
// PHP Version
    if(version_compare(phpversion(), $phpVersion, '<')) {
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
    $pathArray = array($pth['file']['plugin_config'],
                       $pth['file']['plugin_stylesheet'],
                       $pth['folder']['plugin_languages'],
                       $pth['file']['plugin_language'],
                       $pth['folder']['plugin'] . 'data/');
    foreach($pathArray as $path) {
        if(is_writable($path)) {
            $o .= '<p class="xh_success">'
               . $path
               . ' &#x2192 writable</p>'
               . "\n";
        } else {
            $o .= '<p class="xh_fail">'
               . $path
               . ' &#x2192 not writable</p>'
               . "\n";
        }
    }
// access protection
    $dataFolder = $pth['folder']['plugin'] . 'data/';
    $checkDataFolder = XH_isAccessProtected($dataFolder);
    if($checkDataFolder) {
        $o .= '<p class="xh_success"><a target="_blank" href="'
            . $dataFolder
            . '">'
            . $dataFolder
            . '&#x2192 access-protected</a></p>'
            . "\n";
    } else {
        $o .= '<p class="xh_fail"><a target="_blank" href="'
            . $dataFolder
            . '">'
            . $dataFolder
            . '&#x2192 not access-protected</a></p>'
            . "\n";
    }

    return $o;
}
