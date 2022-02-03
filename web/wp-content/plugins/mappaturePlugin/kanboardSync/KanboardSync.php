<?php
namespace MappaturePlugin;

use Dompdf\Exception;

/**
* classe per la modifica o la creazione di uno user su kanboard, prelevando i dati e i metadati utente da WordPress
 *e richiamando la funzione updateuser() o createuser() che inseriscono o aggiornano l'utente anche nel db di kanboard
 */

class KBSync
{
    public static function updateUser($user_id)
    {
        $user_data = array(get_userdata($user_id));
        $user_meta = array(get_user_meta($user_id));


        if (isset($user_data[0]->data) && isset($user_meta[0])) {
            $user = new User();
            $user->setEmail($user_data[0]->data->user_email);
            $first_and_second_name = $user_meta[0]['first_name'][0] . ' ' . $user_meta[0]['last_name'][0];
            $user->setName($first_and_second_name);
            $user->setUsername($user_data[0]->data->user_login);
            $user->setPassword($user_data[0]->data->user_pass);
            $user->setIdKanboard($user_meta[0]['id_kanboard'][0]);
            $idKanboard = $user->getIdKanboard();
            if ($idKanboard != NULL) {
                $user->updateUser();

            } else {
                $user->createUser();
                $idKanboard = $user->getIdKanboard();
                update_user_meta($user_id, 'id_kanboard', $idKanboard);
            }

        }
        $area = new Area();
        $servizio = new Servizio();
        $ufficio = new Ufficio();
        $area->setArea($user_meta[0]['area'][0]);
        $area->setUserArea($idKanboard);
        $servizio->setServizio($user_meta[0]['servizio'][0]);
        $servizio->setUserServizio($idKanboard);
        $ufficio->setUfficio($user_meta[0]['ufficio'][0]);
        $ufficio->setUserUfficio($idKanboard);
        $user->setUserRole($idKanboard,$user_meta[0]['Ruolo'][0]);
        $user->editUserRole($idKanboard,$user_meta[0]['Ruolo'][0]);

        //TODO capire come salvare il precedente ruolo in una temp_variable, altrimenti non posso fare dei controlli funzionali
        /*if($user_meta[0]['Ruolo'][0] == 'PO'){
            (new UpdateThingsByRuolo)->delete_dipendente_from_customtable($idKanboard);
            $array_procedimenti = (new Procedimento)->findTaskOnWordpress($area->getArea(),$servizio->getServizio());
            $array_ids = (new Procedimento)->findTasksOnKanboard($array_procedimenti);
            (new UpdateThingsByRuolo)->insert_po_in_customtable($idKanboard,$array_ids);
            (new UpdateThingsByRuolo)->find_dirigente_for_procedimenti($array_ids);
            $array_processi = (new Processo)->findProjectsOnWordpress($area->getArea());
            $array_ids_processi = (new Processo)->findProjectsOnKanboard($array_processi);
            (new UpdateThingsByRuolo)->find_dirigente_for_processi($array_ids_processi);


        }
        elseif ($user_meta[0]['Ruolo'][0] == 'Dirigente'){
            (new UpdateThingsByRuolo)->delete_po_from_customtable($idKanboard);
            $array_processi = (new Processo)->findProjectsOnWordpress($area->getArea());
            $array_ids_processi = (new Processo)->findProjectsOnKanboard($array_processi);
            (new UpdateThingsByRuolo)->insert_dirigente($array_ids_processi,$idKanboard);
        }
        elseif($user_meta[0]['Ruolo'][0] == 'Dipendente'){
            //con fase vengono incluse anche le attività, dato che sono la stessa cosa i metodi sono presenti solo nella classe fase
            (new UpdateThingsByRuolo)->delete_po_dipendente_from_customtable($idKanboard);
            $array_fase = (new Fase)->findFaseOnWordpress($area->getArea(),$servizio->getServizio(),$ufficio->getUfficio());
            $array_ids_fase = (new Fase)->findFaseOnKanboard($array_fase);
            (new UpdateThingsByRuolo)->link_dipendente_to_fase($array_ids_fase,$idKanboard);
        }*/
    }

    public static function importUser($post_id){
        $user_data = array(get_userdata($post_id));
        $user_meta = array(get_user_meta($post_id));

        if (isset($user_data[0]->data) && isset($user_meta[0])) {
            $user = new User();
            $user->setEmail($user_data[0]->data->user_email);
            $first_and_second_name = $user_meta[0]['first_name'][0] . ' ' . $user_meta[0]['last_name'][0];
            $user->setName($first_and_second_name);
            $user->setUsername($user_data[0]->data->user_login);
            $user->setPassword($user_data[0]->data->user_pass);
            $user->setIdKanboard($user_meta[0]['id_kanboard'][0]);
            $idKanboard = $user->getIdKanboard();
            if ($idKanboard != NULL) {
                $user->updateUser();

            } else {
                $user->createUser();
                $idKanboard = $user->getIdKanboard();
                update_user_meta($post_id, 'id_kanboard', $idKanboard);

            }

        }
        $area = new Area();
        $servizio = new Servizio();
        $ufficio = new Ufficio();
        $area->setArea($user_meta[0]['area'][0]);
        $area->setUserArea($idKanboard);
        $servizio->setServizio($user_meta[0]['servizio'][0]);
        $servizio->setUserServizio($idKanboard);
        $ufficio->setUfficio($user_meta[0]['ufficio'][0]);
        $ufficio->setUserUfficio($idKanboard);
        $user->setUserRole($idKanboard,$user_meta[0]['Ruolo'][0]);
    }

    public static function deleteUser($user_id){
        $user_meta = array(get_user_meta($user_id));
        $user = new User();
        $id_kan = $user_meta[0]['id_kanboard'][0];
        $user->deleteUser($id_kan);
    }
}