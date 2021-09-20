<?php
/*
Plugin Name: OrgChart Plugin
Plugin URI:
Description:
Version: 0.1
Author: MG3
Author URI:

*/


add_action('gform_after_submission_6', 'after_submission', 10, 2);

function after_submission($entry, $form)
{
    error_log(print_r($entry, true));
    if ($entry['form_id'] == 6) {
        $info = array();


        if ($entry[11] != NULL) {
            $info = [
                'ente' => $entry[4],
                'settore' => $entry[5],
                'apicale' => $entry[8],
                'servizio' => $entry[9],
                'responsabile processo' => $entry[11],
                'ufficio' => $entry[17],
                'dipendente' => $entry[25],
            ];


        } else {
            $info = [
                'ente' => $entry[4],
                'settore' => $entry[5],
                'apicale' => $entry[8],
                'servizio' => $entry[9],
                'ufficio' => $entry[17],
                'dipendente' => $entry[26],
            ];

        }

         }
        /* $url = 'https://sarala.it/organigramma-citta-di-savona/';
          $args = [
              'method' => 'POST',
              'body' => $info,
          ];
          wp_remote_post($url, $args);*/

    print_r($info);
    throw new Exception();
    $url = 'https://sarala.it/organigramma-citta-di-savona/';
    $args = [
        'method' => 'POST',
        'body' => $info,
    ];
    wp_remote_post($url, $args);
}

?>
