<?php
namespace MappaturePlugin;

use GFAPI;
class ShortCodesProcesso{

    public static function create_processo()
    {
        $lastEntry = GFAPI::get_entries(1)[0];
        $processo = new Processo();
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
            }
        }
        $processo->findProject();
        $processo->assignUsers($id_owner);



    }
    public static function delete_processo()
    {
        $entry_gforms = GFAPI::get_entries(1);
        $id_current_form = $entry_gforms[0]['id'];
        $process = new Processo();
        $process->setNomeProcesso($entry_gforms[0][1]);
        $process->cancellaProcesso();
        $result = GFAPI::delete_entry($id_current_form);

    }

}