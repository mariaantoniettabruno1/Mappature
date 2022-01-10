<?php
include_once "../classes/Area.php";
include_once "../classes/Servizio.php";
include_once "../classes/Ufficio.php";
include_once "../classes/Procedimento.php";
include_once "../classes/Processo.php";
include_once "../classes/IdProcessCreator.php";
include_once "../classes/User.php";
//function add_user_metadata()
//{
//    $entry_gforms = GFAPI::get_entries(52)[0];
//    $area = new Area();
//    $servizio = new Servizio();
//    $ufficio = new Ufficio();
//    $array_servizio = array();
//    $array_ufficio = array();
//    $area->setArea($entry_gforms[3]);
//    $old_value_servizio = '';
//    $old_value_ufficio = '';
//    foreach ($entry_gforms as $key => $value) {
//        $pattern = "[^1.]";
//        if (preg_match($pattern, $key) && $value && $value != '') {
//            $wp_userid = $value;
//            update_user_meta($value, 'area', $area->getArea());
//            $user_meta = array(get_user_meta($value));
//            $area->setUserArea($user_meta[0]['id_kanboard'][0]);
//            foreach ($entry_gforms as $key => $value) {
//                $pattern = "[^6.]";
//                if (preg_match($pattern, $key) && $value && $value != '') {
//                    if ($old_value_servizio != $value)
//                        array_push($array_servizio, $value);
//                    $old_value_servizio = $value;
//                }
//            }
//
//            foreach ($entry_gforms as $key => $value) {
//                $pattern = "[^7.]";
//                if (preg_match($pattern, $key) && $value && $value != '') {
//                    if ($old_value_ufficio != $value) {
//                        array_push($array_ufficio, $value);
//                    }
//                    $old_value_ufficio = $value;
//
//                }
//            }
//
//            update_user_meta($wp_userid, 'servizio', implode(",", $array_servizio));
//            $user_meta = array(get_user_meta($wp_userid));
//            array_push($user_meta[0]['servizio'], $array_servizio);
//
//            $servizio->setServizio(implode(",", $array_servizio));
//            $servizio->setUserServizio($user_meta[0]['id_kanboard'][0]);
//
//            update_user_meta($wp_userid, 'ufficio', implode(",", $array_ufficio));
//            $user_meta = array(get_user_meta($wp_userid));
//            array_push($user_meta[0]['ufficio'], $array_ufficio);
//            $ufficio->setUfficio(implode(",", $array_ufficio));
//            $ufficio->setUserUfficio($user_meta[0]['id_kanboard'][0]);
//
//
//        }
//
//    }
//
//
//}
//
//add_shortcode('post_addusermetadata', 'add_user_metadata');
//
//function edit_user_metadata()
//{
//    $entry_gforms = GFAPI::get_entries(55)[0];
//    $area = new Area();
//    $servizio = new Servizio();
//    $ufficio = new Ufficio();
//    $array_servizio = array();
//    $array_ufficio = array();
//    $array_users_dirigente = array();
//    $array_users_po = array();
//    $array_users_dipendente = array();
//    $area->setArea($entry_gforms[3]);
//    $old_value_servizio = '';
//    $old_value_ufficio = '';
//    $old_user_area = '';
//    $old_user_servizio = array();
//
//
//    //foreach per leggere tutti gli utenti dalle entries del form
//    foreach ($entry_gforms as $key => $value) {
//        $pattern = "[^1.]";
//        if (preg_match($pattern, $key) && $value && $value != '') {
//
//            $wp_userid = $value;
//            $user_meta = get_user_meta($value);
//            $old_user_servizio = $user_meta['servizio'];
//            $old_user_ufficio = $user_meta['ufficio'];
//            $old_user_area = $user_meta['area'][0];
//            update_user_meta($value, 'area', $area->getArea());
//            $area->editUserArea($user_meta['id_kanboard'][0]);
//
//            if ($user_meta['Ruolo'][0] == 'Dirigente' && $user_meta['Ruolo'][0] != '')
//                array_push($array_users_dirigente, $user_meta['id_kanboard'][0]);
//            elseif ($user_meta['Ruolo'][0] == 'PO' && $user_meta['Ruolo'][0] != '')
//                array_push($array_users_po, $user_meta['id_kanboard'][0]);
//            elseif ($user_meta['Ruolo'][0] == 'Dipendente' && $user_meta['Ruolo'][0] != '')
//                array_push($array_users_dipendente, $user_meta['id_kanboard'][0]);
//
//
//            //foreach per leggere tutti i servizi selezionati da associare agli user
//            foreach ($entry_gforms as $key => $value) {
//                $pattern = "[^4.]";
//                if (preg_match($pattern, $key) && $value && $value != '') {
//                    if ($old_value_servizio != $value) {
//                        array_push($array_servizio, $value);
//                    }
//                    $old_value_servizio = $value;
//
//
//                }
//            }
//            //foreach per leggere tutti gli uffici selezionati da associare agli user
//            foreach ($entry_gforms as $key => $value) {
//                $pattern = "[^6.]";
//                if (preg_match($pattern, $key) && $value && $value != '') {
//                    if ($old_value_ufficio != $value) {
//                        array_push($array_ufficio, $value);
//                    }
//                    $old_value_ufficio = $value;
//
//                }
//            }
//
//            //aggiornamento meta utente wp per servizio
//
//            update_user_meta($wp_userid, 'servizio', $array_servizio);
//            $user_meta = get_user_meta($wp_userid);
//            array_push($user_meta['servizio'], $array_servizio);
//            $servizio->setServizio(implode(",", $array_servizio));
//            $servizio->editUserServizio($user_meta['id_kanboard'][0]);
//
//            //aggiornamento meta utente wp per ufficio
//
//            update_user_meta($wp_userid, 'ufficio', $array_ufficio);
//            $user_meta = get_user_meta($wp_userid);
//            array_push($user_meta['ufficio'], $array_ufficio);
//            $ufficio->setUfficio(implode(",", $array_ufficio));
//            $ufficio->editUserUfficio($user_meta['id_kanboard'][0]);
//
//        }
//
//    }
//
//
////aggioranmento di processi e procedimenti che hanno il dirigente collegato
//    if (!empty(array_filter($array_users_dirigente))) {
//        $processi_wp = Processo::findProjectsOnWordpress($old_user_area);
//        $array_ids = Processo::findProjectsOnKanboard($processi_wp);
//        Processo::deleteDismatchProject($array_ids, $array_users_dirigente);
//        $nuovi_processi_wp = Processo::findProjectsOnWordpress($area->getArea());
//        $array_ids = Processo::findProjectsOnKanboard($nuovi_processi_wp);
//        Processo::insertMatchProject($array_ids, $array_users_dirigente);
//        $procedimenti_wp = Procedimento::findTaskOnWordpress($old_user_area, implode(",", $old_user_servizio));
//        $array_ids_procedimento = Procedimento::findTasksOnKanboard($procedimenti_wp);
//        Procedimento::deleteDismatchTasksCreator($array_ids_procedimento, $array_users_dirigente);
//        $nuovi_procedimenti_wp = Procedimento::findTaskOnWordpress($area->getArea(), $array_servizio);
//        $array_ids_procedimento = Procedimento::findTasksOnKanboard($nuovi_procedimenti_wp);
//        Procedimento::insertMatchTasksCreator($array_ids_procedimento, $array_users_dirigente);
//
//    } elseif (!empty(array_filter($array_users_po))) { //aggiornamenti di procedimenti che hanno il PO collegato
//        //se ci sono dei processi collegati ai po, aggiorno i dati nel db
//        $processi_wp = Processo::findProjectsOnWordpress($old_user_area);
//        $array_ids = Processo::findProjectsOnKanboard($processi_wp);
//        Processo::deleteDismatchProject($array_ids, $array_users_po);
//        $nuovi_processi_wp = Processo::findProjectsOnWordpress($area->getArea());
//        $array_ids = Processo::findProjectsOnKanboard($nuovi_processi_wp);
//        Processo::insertMatchProject($array_ids, $array_users_po);
//
//        //se ci sono dei procedimenti associati ai po selezionati, aggiorno i dati
//        $procedimenti_wp = Procedimento::findTaskOnWordpress($old_user_area, implode(",", $old_user_servizio));
//        $array_ids_procedimento = Procedimento::findTasksOnKanboard($procedimenti_wp);
//        Procedimento::deleteDismatchTasksOwner($array_ids_procedimento, $array_users_po);
//        $nuovi_procedimenti_wp = Procedimento::findTaskOnWordpress($area->getArea(), $array_servizio);
//        $array_ids_procedimento = Procedimento::findTasksOnKanboard($nuovi_procedimenti_wp);
//        Procedimento::insertMatchTasksOwner($array_ids_procedimento, $array_users_po);
//    }
//
//    elseif (!empty((array_filter($array_users_dipendente)))) {//aggiornamento dei dipendenti che hanno fase e attività collegata
//        $fase_wp = Fase::findFaseOnWordpress($old_user_area, implode(",", $old_user_servizio), implode(",", $old_user_ufficio));
//        $attivita_wp = SubtaskAttivita::findAttivitaOnWordpress($old_user_area, implode(",", $old_user_servizio), implode(",", $old_user_ufficio));
//        print_r("Sono dentro il terzo if");
//        if (!empty((array_filter($fase_wp))) && empty(array_filter($attivita_wp))) { //se ci sono delle fasi collegate ma non delle attività lavoro solo sulle fasi
//            $array_ids_fase = Fase::findFaseOnKanboard($fase_wp);
//            Fase::deleteDismatchSubtaskUsers($array_ids_fase, $array_users_dipendente);
//            $nuova_fase_wp = Fase::findFaseOnWordpress($area->getArea(), $array_servizio, $array_ufficio);
//            $array_ids_fase = Fase::findFaseOnKanboard($nuova_fase_wp);
//            Fase::insertMatchSubtaskUsers($array_ids_fase, $array_users_dipendente);
//        }
//        elseif (empty((array_filter($fase_wp))) && !empty(array_filter($attivita_wp))) { //se ci sono delle attivita collegate ma non delle fasi lavoro solo sulle attività
//            $array_ids_attivita = SubtaskAttivita::findAttivitaOnKanboard($attivita_wp);
//            SubtaskAttivita::deleteDismatchAttivitaUsers($array_ids_attivita, $array_users_dipendente);
//            $attivita_wp = SubtaskAttivita::findAttivitaOnWordpress($area->getArea(), $array_servizio, $array_ufficio);
//            $array_ids_attivita = SubtaskAttivita::findAttivitaOnKanboard($attivita_wp);
//            SubtaskAttivita::insertMatchAttivitaUsers($array_ids_attivita, $array_users_dipendente);
//        }
//        elseif (!empty((array_filter($fase_wp))) && !empty(array_filter($attivita_wp))) { //altrimenti lavoro su entrambe
//            $array_ids_fase = Fase::findFaseOnKanboard($fase_wp);
//            Fase::deleteDismatchSubtaskUsers($array_ids_fase, $array_users_dipendente);
//            $nuova_fase_wp = Fase::findFaseOnWordpress($area->getArea(), $array_servizio, $array_ufficio);
//            $array_ids_fase = Fase::findFaseOnKanboard($nuova_fase_wp);
//            Fase::insertMatchSubtaskUsers($array_ids_fase, $array_users_dipendente);
//            $array_ids_attivita = SubtaskAttivita::findAttivitaOnKanboard($attivita_wp);
//            SubtaskAttivita::deleteDismatchAttivitaUsers($array_ids_attivita, $array_users_dipendente);
//            $attivita_wp = SubtaskAttivita::findAttivitaOnWordpress($area->getArea(), $array_servizio, $array_ufficio);
//            $array_ids_attivita = SubtaskAttivita::findAttivitaOnKanboard($attivita_wp);
//            SubtaskAttivita::insertMatchAttivitaUsers($array_ids_attivita, $array_users_dipendente);
//        }
//
//    }
//
//
//}
//
//add_shortcode('post_editusermetadata', 'edit_user_metadata');