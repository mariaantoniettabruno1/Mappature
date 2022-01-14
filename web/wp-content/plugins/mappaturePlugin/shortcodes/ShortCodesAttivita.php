<?php

namespace MappaturePlugin;

use GFAPI;

class ShortCodesAttivita
{
    public static function create_attivita()
    {
        $entry_gforms = GFAPI::get_entries(24);
        $attivita = new Attivita();
        if (!empty($entry_gforms)) {
            $entry_gforms = GFAPI::get_entries(24)[0];
            $attivita->setTitleAttivita($entry_gforms[1]);
            $attivita->setNameProcedureAttivita($entry_gforms[11]);
            $attivita->setNameProcessAttivita($entry_gforms[10]);
            $attivita->setIdFormAttivita($entry_gforms['form_id']);
            $attivita->setIdAttivita($entry_gforms['id']);

            foreach ($entry_gforms as $key => $value) {
                $pattern = "[^9.]";
                if (preg_match($pattern, $key) && $value) {
                    $attivita->addUser($value);
                }
            }
            $attivita->createAttivita();
        }

        return '';
    }

    public static function create_attivita_postuma()
    {
        $entry_gforms = GFAPI::get_entries(59);
        $attivita = new Attivita();
        if (!empty($entry_gforms)) {
            $entry_gforms = GFAPI::get_entries(59)[0];
            $attivita->setTitleAttivita($entry_gforms[1]);
            $attivita->setNameProcedureAttivita($entry_gforms[3]);
            $attivita->setNameProcessAttivita($entry_gforms[2]);
            $attivita->setIdFormAttivita($entry_gforms['form_id']);
            $attivita->setIdAttivita($entry_gforms['id']);

            foreach ($entry_gforms as $key => $value) {
                $pattern = "[^7.]";
                if (preg_match($pattern, $key) && $value) {
                    $attivita->addUser($value);
                }
            }
            $attivita->createAttivita();
        }

        return ' ';
    }


    public static function update_attivita()
    {
        $entry_gforms = GFAPI::get_entries(41);
        $atto = new Attivita();
        if (!empty($entry_gforms)) {
            $id_current_form = $entry_gforms[0]['id'];
            $results_atto = GForm::getForm($id_current_form);
            $atto->setTitleAttivita($results_atto[1]);

            $entry = array('1' => $results_atto[1], '2' => $results_atto[2], '3' => $results_atto[3], '4' => $results_atto[4], '5' => $results_atto[5], '6' => $results_atto[6]);
            $entry_gforms = GFAPI::get_entries(24);
            $id_current_form = $entry_gforms[0]['id'];
            $atto->setOldTitleAttivita($entry_gforms[0][1]);
            $atto->update();
            $result = GFAPI::update_entry($entry, $id_current_form);
        }

        return ' ';
    }

    public static function delete_attivita()
    {
        $entry_gforms = GFAPI::get_entries(24);
        if (!empty($entry_gforms)) {
            $id_current_form = $entry_gforms[0]['id'];
            $atto = new Attivita();
            $atto->setTitleAttivita($entry_gforms[0][1]);
            $atto->delete();
            $result = GFAPI::delete_entry($id_current_form);
        }
        return ' ';
    }


}