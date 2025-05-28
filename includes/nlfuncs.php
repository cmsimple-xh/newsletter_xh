<?php
/*
============================================================
    Newsletter for CMSimple
============================================================
 * @copyright  © simpleSolutions 2010-2012
 * @author    simplesolutions.dk
 * @license Released under Remove Link License and Commercial License. Please read file TermsOfUse.txt included in distribution package. 
 * @license Visit simplesolutions.dk for user manual and to buy a licence.
 *
 * Message from Jerry Jakobsfeld (2025-01-09)
 * I stoped development of CMSimple plug-ins som time ago. Your welcome to use my development as you wish, it's free for use, changes and further development.
 *
 * @copyright 2025 The CMSimple_XH developers <http://cmsimple-xh.org/?The_Team>
 * @author    The CMSimple_XH developers <devs@cmsimple-xh.org>
 * @license    GNU GPLv3 - http://www.gnu.org/licenses/gpl-3.0.en.html
*/

function newsletterConfirmSubscription()
{
    global $mail_confirm_subscribtion, $plugin_cf, $plugin_tx;

    $newspageArray = array();
    //decrypt;
    $key = trim($plugin_cf['newsletter']['encrypt_key']);
    if ($key != '') {
        $getCnf = newsletterConvert($_GET['cnf'], 0);
    } else {
        $getCnf = $_GET['cnf'];
    }
    if ($getCnf == '') {
        return false;
    }
    $getCnfArray = explode('¤¤¤', $getCnf);
    $o = '<h1>' . $plugin_tx['newsletter']['subscribe'] . '</h1>';
    $newspageArray[] = $getCnfArray[1];
    $cnfArray = explode('@@@', $getCnfArray[0]);
    $subscriberMail = $cnfArray[0];
    array_splice($cnfArray, 0, 1);
    // overwrite
    $mail_confirm_subscribtion = 'thx';
    $o .= newsletterAddSubscriberToList($newspageArray, $subscriberMail, $cnfArray);

    return $o;
}

function newsletterConvert(string $string, bool $encrypt): string
{

    global $plugin_cf;

    // the key for encrypt
    $key = hash('sha256', $plugin_cf['newsletter']['encrypt_key'], true);
    // the encryption method
    $cipher = 'aes-256-cbc';

    if (!in_array($cipher, openssl_get_cipher_methods())) {
        return string;
    }

    if ($encrypt) {
        $iv = openssl_random_pseudo_bytes(16);
        $encryptData = openssl_encrypt($string, $cipher, $key, OPENSSL_RAW_DATA, $iv);
        $hash = hash_hmac('sha256', $encryptData . $iv, $key, true);
        $encodeData = base64_encode($iv . $hash . $encryptData);
        $data = str_replace(['+','/','='], ['-','_',''], $encodeData);
        return $data;
    } else {
        $encodeData = strtr($string, '-_', '+/');
        $decodeData = base64_decode($encodeData);
        $iv = substr($decodeData, 0, 16);
        $hash = substr($decodeData, 16, 32);
        $encryptData = substr($decodeData, 48);
        if (hash_equals(hash_hmac('sha256', $encryptData . $iv, $key, true), $hash)) {
            $data = openssl_decrypt($encryptData, $cipher, $key, OPENSSL_RAW_DATA, $iv);
            return $data;
        }
    }

    return false;
}

function newsletterUnderline4Spaces($fname)
{
    if ($fname) {
        return str_replace(' ', '_', $fname);
    }
    return false;
}
