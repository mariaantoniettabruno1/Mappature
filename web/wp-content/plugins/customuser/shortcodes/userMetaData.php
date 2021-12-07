<?php
include_once "../classes/Area.php";
include_once "../classes/Servizio.php";
include_once "../classes/Ufficio.php";
include_once "../classes/Procedimento.php";
include_once "../classes/Processo.php";
include_once "../classes/IdProcessCreator.php";
function add_user_metadata()
{
    $entry_gforms = GFAPI::get_entries(52)[0];
    $area = new Area();
    $servizio = new Servizio();
    $ufficio = new Ufficio();
    $array_servizio = array();
    $array_ufficio = array();
    $area->setArea($entry_gforms[3]);
    $old_value_servizio = '';
    $old_value_ufficio = '';
    foreach ($entry_gforms as $key => $value) {
        $pattern = "[^1.]";
        if (preg_match($pattern, $key) && $value && $value != '') {
            $wp_userid = $value;
            update_user_meta($value, 'area', $area->getArea());
            $user_meta = array(get_user_meta($value));
            $area->setUserArea($user_meta[0]['id_kanboard'][0]);
            foreach ($entry_gforms as $key => $value) {
                $pattern = "[^6.]";
                if (preg_match($pattern, $key) && $value && $value != '') {
                    if ($old_value_servizio != $value)
                        array_push($array_servizio, $value);
                    $old_value_servizio = $value;
                }
            }

            foreach ($entry_gforms as $key => $value) {
                $pattern = "[^7.]";
                if (preg_match($pattern, $key) && $value && $value != '') {
                    if ($old_value_ufficio != $value) {
                        array_push($array_ufficio, $value);
                    }
                    $old_value_ufficio = $value;

                }
            }

            update_user_meta($wp_userid, 'servizio', implode(",", $array_servizio));
            $user_meta = array(get_user_meta($wp_userid));
            array_push($user_meta[0]['servizio'], $array_servizio);

            $servizio->setServizio(implode(",", $array_servizio));
            $servizio->setUserServizio($user_meta[0]['id_kanboard'][0]);

            update_user_meta($wp_userid, 'ufficio', implode(",", $array_ufficio));
            $user_meta = array(get_user_meta($wp_userid));
            array_push($user_meta[0]['ufficio'], $array_ufficio);
            $ufficio->setUfficio(implode(",", $array_ufficio));
            $ufficio->setUserUfficio($user_meta[0]['id_kanboard'][0]);
            echo "<pre>";
            print_r($user_meta);
            echo "</pre>";

        }

    }


}

add_shortcode('post_addusermetadata', 'add_user_metadata');

function edit_user_metadata()
{
    $entry_gforms = GFAPI::get_entries(55)[0];
    $area = new Area();
    $servizio = new Servizio();
    $ufficio = new Ufficio();
    $array_servizio = array();
    $array_ufficio = array();
    $array_users_dirigente = array();
    $array_users_po = array();
    $area->setArea($entry_gforms[3]);
    $old_value_servizio = '';
    $old_value_ufficio = '';
    $old_user_area = '';
    $old_user_servizio = array();


    //foreach per leggere tutti gli utenti dalle entries del form
    foreach ($entry_gforms as $key => $value) {
        $pattern = "[^1.]";
        if (preg_match($pattern, $key) && $value && $value != '') {

            $wp_userid = $value;
            $user_meta = get_user_meta($value);
            $old_user_servizio = $user_meta['servizio'];
            $old_user_area = $user_meta['area'][0];
            update_user_meta($value, 'area', $area->getArea());
            $area->editUserArea($user_meta['id_kanboard'][0]);

            if ($user_meta['Ruolo'][0] == 'Dirigente' && $user_meta['Ruolo'][0] != '')
                array_push($array_users_dirigente, $user_meta['id_kanboard'][0]);
            elseif ($user_meta['Ruolo'][0] == 'PO' && $user_meta['Ruolo'][0] != '')
                array_push($array_users_po, $user_meta['id_kanboard'][0]);
            print_r($array_users_po);

            //foreach per leggere tutti i servizi selezionati da associare agli user
            foreach ($entry_gforms as $key => $value) {
                $pattern = "[^4.]";
                if (preg_match($pattern, $key) && $value && $value != '') {
                    if ($old_value_servizio != $value) {
                        array_push($array_servizio, $value);
                    }
                    $old_value_servizio = $value;


                }
            }
            //foreach per leggere tutti gli uffici selezionati da associare agli user
            foreach ($entry_gforms as $key => $value) {
                $pattern = "[^6.]";
                if (preg_match($pattern, $key) && $value && $value != '') {
                    if ($old_value_ufficio != $value) {
                        array_push($array_ufficio, $value);
                    }
                    $old_value_ufficio = $value;

                }
            }

            //aggiornamento meta utente wp per servizio

            update_user_meta($wp_userid, 'servizio', $array_servizio);
            $user_meta = get_user_meta($wp_userid);
            array_push($user_meta['servizio'], $array_servizio);
            $servizio->setServizio(implode(",", $array_servizio));
            $servizio->editUserServizio($user_meta['id_kanboard'][0]);

            //aggiornamento meta utente wp per ufficio

            update_user_meta($wp_userid, 'ufficio', $array_ufficio);
            $user_meta = get_user_meta($wp_userid);
            array_push($user_meta['ufficio'], $array_ufficio);
            $ufficio->setUfficio(implode(",", $array_ufficio));
            $ufficio->editUserUfficio($user_meta['id_kanboard'][0]);

        }

    }


    /* else {

          $dipendenteId = $user_meta[0]['id_kanboard'][0];
          $entry_gforms_fase = GFAPI::get_entries(23);
         $entry_gforms_procedimento = GFAPI::get_entries(50);
         echo "<pre>";
         print_r($user_meta);
         echo "<pre>";
         for ($i = 0; $i < sizeof($entry_gforms_fase); $i++) {
             foreach ($entry_gforms_fase[$i] as $key => $value) {
                 $pattern = "[^9.]";
                 if ($entry_gforms_fase[$i][12] == $area->getArea()) {

                     if (preg_match($pattern, $key) && $value && $value != '') {

                         Fase::aggiornaFase($dipendenteId);
                     }
                 }
                 else{
                     Fase::aggiornaFase(null);
                 }
             }
         }
     }*/


    /*$processi_wp= Processo::findProjectsOnWordpress($old_user_area);
    $array_ids = Processo::findProjectsOnKanboard($processi_wp);
    Processo::deleteDismatchProject($array_ids, $array_users_dirigente);
    $nuovi_processi_wp = Processo::findProjectsOnSarala($area->getArea());
    $array_ids = Processo::findProjectsOnKanboard($nuovi_processi_wp);
    Processo::insertMatchProject($array_ids, $array_users_dirigente);*/

    $procedimenti_wp = Procedimento::findTaskOnWordpress($old_user_area, implode(",",$old_user_servizio));
    $array_ids_procedimento = Procedimento::findTasksOnKanboard($procedimenti_wp);
    print_r($array_ids_procedimento);
    Procedimento::deleteDismatchTasks($array_ids_procedimento, $array_users_po);
    $nuovi_procedimenti_wp = Procedimento::findTaskOnWordpress($area->getArea(),$array_servizio);
    $array_ids_procedimento = Procedimento::findTasksOnKanboard($nuovi_procedimenti_wp);
    Procedimento::insertMatchTasks($array_ids_procedimento, $array_users_po);

    /* $entry_gforms_procedimento = GFAPI::get_entries(50);
     for ($i = 0; $i < sizeof($entry_gforms_procedimento); $i++) {
         foreach ($entry_gforms_procedimento[$i] as $key => $value) {
             $pattern = "[^22.]";
             if ($entry_gforms_procedimento[$i][18] == $area->getArea() && $entry_gforms_procedimento[$i][19] == $area->getArea()) {

                 if (preg_match($pattern, $key) && $value && $value != '') {

                     Procedimento::aggiornaProcedimento($creatorId, $value);
                 }
             } else {
                 Procedimento::aggiornaProcedimento(null, $value);
             }
       }*/


}

add_shortcode('post_editusermetadata', 'edit_user_metadata');