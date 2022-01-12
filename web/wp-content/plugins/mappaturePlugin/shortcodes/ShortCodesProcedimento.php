<?php

namespace MappaturePlugin;

use GFAPI;

class ShortCodesProcedimento
{
    public static function create_procedimento()
    {
        $last_entry = GFAPI::get_entries(50);
        $procedure = new Procedimento();
        if (!empty($last_entry)) {
            $last_entry = GFAPI::get_entries(50)[0];
            foreach ($last_entry as $key => $value) {
                $pattern = "[^22.]";
                if (preg_match($pattern, $key) && $value) {

                    $procedure->setTitle($value);
                    $procedure->setIdForm($last_entry['form_id']);
                    $procedure->setNameProcess($last_entry[24]);
                    $settore = $last_entry[18];
                    $creator_id = GetterIdUsers::getProcessOwnerId($settore);
                    $procedure->setCreatorId($creator_id);
                    $servizio = $last_entry[19];
                    $ufficio = $last_entry[20];
                    $procedure->setOwnerId(GetterIdUsers::getProcedureOwnerId($settore, $servizio, $ufficio));
                    $procedure->setPosition(1);
                    $procedure->setDateCreated(strtotime($last_entry['date_created']));
                    $procedure->setDateUpdated(strtotime($last_entry['date_updated']));
                    $procedure->createProcedure();
                    $procedure->findTask();
                    $procedure->assignUsersOwner($procedure->getOwnerId());
                    $procedure->assignUsersCreator($procedure->getCreatorId());

                }
            }
        }


    }

    public static function create_procedimento_postuma()
    {
        $last_entry = GFAPI::get_entries(2);
        $procedure = new Procedimento();
        if (!empty($last_entry)) {
            $last_entry = GFAPI::get_entries(2)[0];
            $procedure->setTitle($last_entry[2]);
            $procedure->setIdForm($last_entry['form_id']);
            $procedure->setNameProcess($last_entry[17]);
            $settore = $last_entry[18];
            $creator_id = GetterIdUsers::getProcessOwnerId($settore);
            $procedure->setCreatorId($creator_id);
            $servizio = $last_entry[19];
            $ufficio = $last_entry[20];
            $procedure->setOwnerId(GetterIdUsers::getProcedureOwnerId($settore, $servizio, $ufficio));
            $procedure->setPosition(1);
            $procedure->setDateCreated(strtotime($last_entry['date_created']));
            $procedure->setDateUpdated(strtotime($last_entry['date_updated']));
            $procedure->createProcedure();
            $procedure->findTask();
            $procedure->assignUsersOwner($procedure->getOwnerId());
            $procedure->assignUsersCreator($procedure->getCreatorId());
        }


    }

    public static function delete_procedimento()
    {
        $entry_gforms = GFAPI::get_entries(2);
        if (!empty($entry_gforms)) {
            $id_current_form = $entry_gforms[0]['id'];
            $procedimento = new Procedimento();
            $procedimento->setTitle($entry_gforms[0][2]);
            $procedimento->deleteProcedure();
            $result = GFAPI::delete_entry($id_current_form);
        }


    }


}