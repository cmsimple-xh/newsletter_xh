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


function nl_fieldHoneypotDisplay() {

    global $plugin_tx;

    $o = '<br>'
       . '<span class="newsletter_label newsleter_noeyes">'
       . $plugin_tx['newsletter']['field_leave_blank']
       . '</span>'
       . "\n"
       . '<input placeholder="'
       . $plugin_tx['newsletter']['field_leave_blank']
       . '" class="newsleter_noeyes" name="hp-subject" type="text" class="newsletter_inputfield" value="">'
       . "\n";

    return $o;
}

function nl_fieldHoneypotCheck() {

    global $plugin_tx;

    $o = false;

    if (!empty($_POST['hp-subject'])) {
        XH_logMessage('warning',
                      'Newsletter_XH',
                      'registration - ' . $_POST['subscribermail'],
                      'Spam suspicion: Honeypot');
        $o = '<p class="xh_fail">'
           . $plugin_tx['newsletter']['info_spam_suspicious']
           . '</p>';
    }

    return $o;
}

function nl_generateTimeHash() {

    global $cf;

    // the key for encrypt
    $key = $cf['security']['password'];
    // second unique key
    $iv = md5($cf['security']['password'], true);
    // the encryption method
    $cipher = 'aes-128-cbc';
    $time = time();

    // test whether encryption method is available
    if(in_array($cipher, openssl_get_cipher_methods())) {
        // returns encrypted timestamp value
        $o = openssl_encrypt($time, $cipher, $key, 0, $iv);
    } else {
        $o = $time;
  }

  return $o;
}

function nl_fieldSpamTimeDisplay() {

    global $plugin_tx;

    $o = '<br>'
       . '<span class="newsletter_label newsleter_noeyes">'
       . $plugin_tx['newsletter']['field_do_not_change']
       . '</span>'
       . "\n"
       . '<input class="newsleter_noeyes" name="sp_time" type="text" class="newsletter_inputfield" value="'
       . nl_generateTimeHash()
       . '">'
       . "\n";

    return $o;
}

function nl_fieldSpamTimeCheck() {

    global $cf, $plugin_cf, $plugin_tx;

    $failmessage = false;
    $o = false;
    // the key for encrypt
    $key = $cf['security']['password'];
    // second unique key
    $iv = md5($cf['security']['password'], true);
    // the encryption method
    $cipher = 'aes-128-cbc';
    $minTime = (int)$plugin_cf['newsletter']['spam_protection_min_time'];
    $maxTime = (int)$plugin_cf['newsletter']['spam_protection_max_time'];

    $givenTime = isset($_POST['sp_time']) ? $_POST['sp_time'] : '';

    // decode passed value
    $decryptedTime = openssl_decrypt($givenTime, $cipher, $key, 0, $iv);
    // check whether the transferred value could be checked
    if ($decryptedTime) {
        $currentTime = time();
        if(($currentTime - $decryptedTime) <= $minTime
        || ($currentTime - $decryptedTime) >= $maxTime ) {
            $failmessage = 'The submission is outside from the specified time frame.';
        }
    } else {
      // supplied value could not be decypted -> input field was edited
      $failmessage = 'The Time field has been manipulated.';
    }
    if ($failmessage) {
        XH_logMessage('warning',
                      'Newsletter_XH',
                      'registration - ' . $_POST['subscribermail'],
                      'Spam suspicion: ' . $failmessage);
        $o = '<p class="xh_fail">'
           . $plugin_tx['newsletter']['info_spam_suspicious']
           . '</p>';
    }

    return $o;
}

