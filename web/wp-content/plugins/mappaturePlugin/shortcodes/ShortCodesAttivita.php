<?php

namespace MappaturePlugin;

use GFAPI;

class ShortCodesAttivita
{
    public static function create_attivita()
    {
        $entry_gforms = GFAPI::get_entries(80);
        $attivita = new Attivita();
        if (!empty($entry_gforms)) {
            $entry_gforms = GFAPI::get_entries(80)[0];
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
        $entry_gforms = GFAPI::get_entries(91);
        $attivita = new Attivita();
        if (!empty($entry_gforms)) {
            $old_title = $entry_gforms[0][16];
            $new_title = $entry_gforms[0][17];
            $old_title = $old_title . ' - attivita';
            $new_title = $new_title . ' - attivita';
            $attivita->update_attivita($old_title, $new_title);
        }

        return '';
    }

    static function associa_attivita()
    {
        $entry_gforms = GFAPI::get_entries(107);

        $idKanboard_array = array();
        if (!empty($entry_gforms)) {
            foreach ($entry_gforms[0] as $key => $value) {
                $pattern = "[^7.]";
                if (preg_match($pattern, $key) && $value) {
                    array_push($idKanboard_array, $value);
                }
            }
        }
        $attivita_title = $entry_gforms[0][2];
        $attivita = new Attivita();
        $attivita->associa_attivita($attivita_title, $idKanboard_array);

        return '';
    }

    public static function delete_attivita()
    {
        $entry_gforms = GFAPI::get_entries(95);
        if (!empty($entry_gforms)) {
            $att = new Attivita();
            $title_attivita = $entry_gforms[0][7] . ' - attivita';
            $att->setTitleAttivita($title_attivita);
            $att->deleteAttivita();
        }
        return ' ';
    }


}