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