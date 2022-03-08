<?php

namespace MappaturePlugin;

use GFAPI;

class ShortCodesProcedimento
{
    public static function create_procedimento()
    {
        $last_entry = GFAPI::get_entries(98);
        $procedure = new Procedimento();
        if (!empty($last_entry)) {
            $last_entry = GFAPI::get_entries(98)[0];
            foreach ($last_entry as $key => $value) {
                $pattern = "[^22.]";
                if (preg_match($pattern, $key) && $value) {

                    $procedure->setTitle($value);
                    $procedure->setIdForm($last_entry['form_id']);
                    $procedure->setNameProcess($last_entry[24]);
                    $area = $last_entry[18];
                    $creator_id = GetterIdUsers::getProcessOwnerId($area);
                    $procedure->setCreatorId($creator_id);
                    $servizio = $last_entry[19];
                    $ufficio = $last_entry[20];
                    $procedure->setOwnerId(GetterIdUsers::getProcedureOwnerId($area, $servizio, $ufficio));
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

        return '';
    }

    public static function create_procedimento_postuma()
    {
        $last_entry = GFAPI::get_entries(75);
        $procedure = new Procedimento();

        if (!empty($last_entry)) {
            $last_entry = GFAPI::get_entries(75)[0];
            $procedure->setTitle($last_entry[2]);
            $procedure->setIdForm($last_entry['form_id']);
            $procedure->setNameProcess($last_entry[17]);
            $area = $last_entry[18];
            $creator_id = GetterIdUsers::getProcessOwnerId($area);
            $procedure->setCreatorId($creator_id);
            $servizio = $last_entry[19];
            $ufficio = $last_entry[20];
            $procedure->setOwnerId(GetterIdUsers::getProcedureOwnerId($area, $servizio, $ufficio));
            $procedure->setPosition(1);
            $procedure->setDateCreated(strtotime($last_entry['date_created']));
            $procedure->setDateUpdated(strtotime($last_entry['date_updated']));
            $procedure->createProcedure();
            $procedure->findTask();
            $procedure->assignUsersOwner($procedure->getOwnerId());
            $procedure->assignUsersCreator($procedure->getCreatorId());
        }

        return '';
    }

    public static function delete_procedimento()
    {
        $entry_gforms = GFAPI::get_entries(90);
        if (!empty($entry_gforms)) {
            $procedimento = new Procedimento();
            $procedimento->setTitle($entry_gforms[0][7]);
            $procedimento->deleteProcedure();
        }

        return '';
    }

    public static function edit_procedimento()
    {
        $entry_gforms = GFAPI::get_entries(88);
        if (!empty($entry_gforms)) {
            $procedimento = new Procedimento();
            $old_title = $entry_gforms[0][12];
            $new_title = $entry_gforms[0][13];
            $procedimento->editProcedure($old_title,$new_title);
        }

        return '';


    }

}