<?php

namespace MappaturePlugin;

use GFAPI;

class ShortCodesProcesso
{

    public static function create_processo()
    {
        $lastEntry = GFAPI::get_entries(1);
        $processo = new Processo();
        if (!empty($lastEntry)) {
            $lastEntry = GFAPI::get_entries(1)[0];
            foreach ($lastEntry as $key => $value) {
                $pattern = "[^9.]";
                if (preg_match($pattern, $key) && $value) {

                    $processo->setNomeProcesso($value);
                    $processo->setIdForm($lastEntry['form_id']);
                    $processo->setAreaProcesso($lastEntry[2]);
                    $processo->setServizioProcesso($lastEntry[3]);
                    $processo->setUfficioProcesso($lastEntry[4]);
                    $id_owner = GetterIdUsers::getProcessOwnerId($processo->getAreaProcesso());
                    if ($id_owner == NULL || $id_owner == '') {
                        $id_owner = GetterIdUsers::getProcedureOwnerId($processo->getAreaProcesso(), $processo->getServizioProcesso(), $processo->getUfficioProcesso());
                    }
                    $processo->setIdUser($id_owner);
                    $processo->setRuoloUser('project-manager');
                    $processo->creaProcesso();
                    $processo->insertProcessoMetaData($processo->getNomeProcesso(), $processo->getAreaProcesso(), $processo->getServizioProcesso(), $processo->getUfficioProcesso());
                }
            }
            $processo->findProject();
            $processo->assignUsers($id_owner);

        }
        return '';

    }

    public static function create_processo_postuma()
    {
        $lastEntry = GFAPI::get_entries(65);

        $processo = new Processo();
        if (!empty($lastEntry)) {
            $lastEntry = GFAPI::get_entries(65)[0];

            $processo->setNomeProcesso($lastEntry[1]);
            $processo->setIdForm($lastEntry['form_id']);
            $processo->setAreaProcesso($lastEntry[2]);
            $processo->setServizioProcesso($lastEntry[3]);
            $processo->setUfficioProcesso($lastEntry[4]);
            $id_owner = GetterIdUsers::getProcessOwnerId($processo->getAreaProcesso());
            if ($id_owner == NULL || $id_owner == '') {
                $id_owner = GetterIdUsers::getProcedureOwnerId($processo->getAreaProcesso(), $processo->getServizioProcesso(), $processo->getUfficioProcesso());
            }
            $processo->setIdUser($id_owner);
            $processo->setRuoloUser('project-manager');
            $processo->creaProcesso();
            $processo->insertProcessoMetaData($processo->getNomeProcesso(), $processo->getAreaProcesso(), $processo->getServizioProcesso(), $processo->getUfficioProcesso());

            $processo->findProject();
            $processo->assignUsers($id_owner);

        }
        return '';

    }

    public static function edit_processo()
    {
        $lastEntry = GFAPI::get_entries(34);
        $processo = new Processo();


        if (!empty($lastEntry)) {
            $old_title = $lastEntry[0][11];
            $new_title = $lastEntry[0][8];
            $processo->editProcesso($old_title, $new_title);
        }

        return '';
    }

    public
    static function delete_processo()
    {
        $entry_gforms = GFAPI::get_entries(36);
        if (!empty($entry_gforms)) {
            $process = new Processo();
            $process->setNomeProcesso($entry_gforms[0][5]);
            $process->cancellaProcesso();
        }

        return '';
    }

}