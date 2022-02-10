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




    public static function edit_attivita()
    {
        $entry_gforms = GFAPI::get_entries(41);
        $attivita = new Attivita();
        if (!empty($entry_gforms)) {
            $old_title = $entry_gforms[0][16];
            $new_title = $entry_gforms[0][17];
            $old_title = $old_title. '- attivita';
            $new_title = $new_title. '- attivita';
            $attivita->update_attivita($old_title,$new_title);
        }

        return '';
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