<?php

namespace MappaturePlugin;

use GFAPI;


class UserMetaDataOrgchart
{
    /**
     * Function che prende tutti i vecchi metadati collegati ad un utente ed inseriti in un form di gravity forms
     * e li salva in un array.
     * Viene utilizzato per ufficio e per servizio
     * input: array di gravity forms contenente le entries, $pattern che contraddistingue il field che contiene il metadato
     * output: array contenente tutti i metadati correlati all'utente
     */

    private static function get_old_metavalue(array $entry_gforms, string $pattern)
    {
        $old_value_array = '';
        $array = array();

        foreach ($entry_gforms as $key => $value) {

            if (preg_match($pattern, $key) && $value && $value != '') {
                if ($old_value_array != $value)
                    array_push($array, $value);
                $old_value_array = $value;
            }
        }
        return $array;
    }

    private static function update_processi_by_user(string $old_user_area, array $array_users, string $area)
    {

        $processi_wp = (new Processo)->findProjectsOnWordpress($old_user_area);
        $array_ids = (new Processo)->findProjectsOnKanboard($processi_wp);
        (new Processo)->deleteDismatchProject($array_ids, $array_users);
        $nuovi_processi_wp = (new Processo)->findProjectsOnWordpress($area);
        $array_ids = (new Processo)->findProjectsOnKanboard($nuovi_processi_wp);
        (new Processo)->insertMatchProject($array_ids, $array_users);
    }

    private static function update_procedimenti_by_dirigente(string $area, string $old_user_area, $old_user_servizio, array $array_servizio, array $array_users_dirigente)
    {

        $procedimenti_wp = (new Procedimento)->findTaskOnWordpress($old_user_area, implode(",", $old_user_servizio));
        $array_ids_procedimento = (new Procedimento)->findTasksOnKanboard($procedimenti_wp);
        (new Procedimento)->deleteDismatchTasksCreator($array_ids_procedimento, $array_users_dirigente);
        $nuovi_procedimenti_wp = (new Procedimento)->findTaskOnWordpress($area, $array_servizio);
        $array_ids_procedimento = (new Procedimento)->findTasksOnKanboard($nuovi_procedimenti_wp);
        (new Procedimento)->insertMatchTasksCreator($array_ids_procedimento, $array_users_dirigente);

    }

    private static function update_procedimenti_by_po(string $area, string $old_user_area, array $array_users_po, $old_user_servizio, array $array_servizio)
    {


        //se ci sono dei procedimenti associati ai po selezionati, aggiorno i dati
        $procedimenti_wp = (new Procedimento)->findTaskOnWordpress($old_user_area, implode(",", $old_user_servizio));
        $array_ids_procedimento = (new Procedimento)->findTasksOnKanboard($procedimenti_wp);
        (new Procedimento)->deleteDismatchTasksOwner($array_ids_procedimento, $array_users_po);
        $nuovi_procedimenti_wp = (new Procedimento)->findTaskOnWordpress($area, $array_servizio);
        $array_ids_procedimento = (new Procedimento)->findTasksOnKanboard($nuovi_procedimenti_wp);
        (new Procedimento)->insertMatchTasksOwner($array_ids_procedimento, $array_users_po);
    }

    private static function update_fase_attivita_by_dipendenti(string $old_user_area, $old_user_servizio, $old_user_ufficio, array $array_users_dipendente, string $area, array $array_servizio, array $array_ufficio)
    {

        $fase_wp = (new Fase)->findFaseOnWordpress($old_user_area, implode(",", $old_user_servizio), implode(",", $old_user_ufficio));
        $attivita_wp = (new Attivita)->findAttivitaOnWordpress($old_user_area, implode(",", $old_user_servizio), implode(",", $old_user_ufficio));

            $array_ids_fase = (new Fase)->findFaseOnKanboard($fase_wp);
            (new Fase)->deleteDismatchSubtaskUsers($array_ids_fase, $array_users_dipendente);
            $nuova_fase_wp = (new Fase)->findFaseOnWordpress($area, $array_servizio, $array_ufficio);
            $array_ids_fase = (new Fase)->findFaseOnKanboard($nuova_fase_wp);
            (new Fase)->insertMatchSubtaskUsers($array_ids_fase, $array_users_dipendente);
            $array_ids_attivita = (new Attivita)->findAttivitaOnKanboard($attivita_wp);
            (new Attivita)->deleteDismatchAttivitaUsers($array_ids_attivita, $array_users_dipendente);
            $attivita_wp = (new Attivita)->findAttivitaOnWordpress($area, $array_servizio, $array_ufficio);
            $array_ids_attivita = (new Attivita)->findAttivitaOnKanboard($attivita_wp);
            (new Attivita)->insertMatchAttivitaUsers($array_ids_attivita, $array_users_dipendente);

    }

    public static function add_user_metadata()
    {
        $entry_gforms = GFAPI::get_entries(52)[0];
        $area = new Area();
        $servizio = new Servizio();
        $ufficio = new Ufficio();
        $area->setArea($entry_gforms[3]);

        foreach ($entry_gforms as $key => $value) {
            $pattern = "[^1.]";
            if (preg_match($pattern, $key) && $value && $value != '') {
                $wp_userid = $value;
                update_user_meta($value, 'area', $area->getArea());
                $user_meta = array(get_user_meta($value));
                $area->setUserArea($user_meta[0]['id_kanboard'][0]);

                $pattern_servizio = "[^6.]";
                $array_servizio = self::get_old_metavalue($entry_gforms, $pattern_servizio);
                $pattern_ufficio = "[^7.]";
                $array_ufficio = self::get_old_metavalue($entry_gforms, $pattern_ufficio);


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


            }

        }

        return '';

    }

    public static function edit_user_metadata()
    {
        $entry_gforms = GFAPI::get_entries(55)[0];
        $area = new Area();
        $servizio = new Servizio();
        $ufficio = new Ufficio();
        $array_servizio = array();
        $array_ufficio = array();
        $array_users_dirigente = array();
        $array_users_po = array();
        $array_users_dipendente = array();
        $area->setArea($entry_gforms[3]);
        $old_user_area = '';
        $old_user_servizio = array();


        //foreach per leggere tutti gli utenti dalle entries del form
        foreach ($entry_gforms as $key => $value) {
            $pattern = "[^1.]";
            if (preg_match($pattern, $key) && $value && $value != '') {

                $wp_userid = $value;
                $user_meta = get_user_meta($value);
                $old_user_servizio = $user_meta['servizio'];
                $old_user_ufficio = $user_meta['ufficio'];
                $old_user_area = $user_meta['area'][0];
                update_user_meta($value, 'area', $area->getArea());
                $area->editUserArea($user_meta['id_kanboard'][0]);

                if ($user_meta['Ruolo'][0] == 'Dirigente' && $user_meta['Ruolo'][0] != '')
                    array_push($array_users_dirigente, $user_meta['id_kanboard'][0]);
                elseif ($user_meta['Ruolo'][0] == 'PO' && $user_meta['Ruolo'][0] != '')
                    array_push($array_users_po, $user_meta['id_kanboard'][0]);
                elseif ($user_meta['Ruolo'][0] == 'Dipendente' && $user_meta['Ruolo'][0] != '')
                    array_push($array_users_dipendente, $user_meta['id_kanboard'][0]);

                $pattern_servizio = "[^4.]";
                $array_servizio = self::get_old_metavalue($entry_gforms, $pattern_servizio);
                if(empty($array_servizio)){
                    array_push($array_servizio,$user_meta['servizio'][0]);
                    echo "<pre>";
                    print_r("Sono dentro l'if se servizio precedente è vuoto");
                    print_r($array_servizio);
                    echo "</pre>";
                }
                $pattern_ufficio = "[^6.]";
                $array_ufficio = self::get_old_metavalue($entry_gforms, $pattern_ufficio);
                if(empty($array_ufficio)){
                    array_push($array_ufficio,$user_meta['ufficio'][0]);
                    echo "<pre>";
                    print_r("Sono dentro l'if se ufficio precedente è vuoto");
                    print_r($array_ufficio);
                    echo "</pre>";
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


//aggioranmento di processi e procedimenti che hanno il dirigente collegato
        $temp_area = $area->getArea();
        if (!empty(array_filter($array_users_dirigente))) {
            //se ci sono dei processi collegati ai dirigenti, aggiorno i dati nel db
            self::update_processi_by_user($old_user_area, $array_users_dirigente, $temp_area);
            self::update_procedimenti_by_dirigente($temp_area, $old_user_area, $old_user_servizio, $array_servizio, $array_users_dirigente);

        } elseif (!empty(array_filter($array_users_po))) { //aggiornamenti di procedimenti che hanno il PO collegato
            //se ci sono dei processi collegati ai po, aggiorno i dati nel db
            self::update_processi_by_user($old_user_area, $array_users_po, $temp_area);
            self::update_procedimenti_by_po($temp_area, $old_user_area, $array_users_po, $old_user_servizio, $array_servizio);
        } elseif (!empty((array_filter($array_users_dipendente)))) {//aggiornamento dei dipendenti che hanno fase e attività collegata

            self::update_fase_attivita_by_dipendenti($old_user_area, $old_user_servizio, $old_user_ufficio, $array_users_dipendente, $temp_area, $array_servizio, $array_ufficio);
        }

        return '';
    }


}


