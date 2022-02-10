<?php

namespace MappaturePlugin;

use GFAPI;

class ShortCodesFase
{
    public static function create_fase()
    {
        $entry_gforms = GFAPI::get_entries(23);
        $fase = new Fase();
        if (!empty($entry_gforms)) {
            $entry_gforms = GFAPI::get_entries(23)[0];
            $fase->setTitle($entry_gforms[1]);
            $fase->setNameProcedure($entry_gforms[11]);
            $fase->setNameProcess($entry_gforms[10]);
            $fase->setIdForm($entry_gforms['form_id']);
            $fase->setId($entry_gforms['id']);
            foreach ($entry_gforms as $key => $value) {
                $pattern = "[^9.]";
                if (preg_match($pattern, $key) && $value) {
                    $fase->addUser($value);
                }
            }
            $fase->createFase();
        }
        return '';

    }


    public static function edit_fase()
    {
        $entry_gforms = GFAPI::get_entries(43);
        $fase = new Fase();
        if (!empty($entry_gforms)) {
            $old_title = $entry_gforms[0][14];
            $new_title = $entry_gforms[0][17];
            $old_title = $old_title. '- fase';
            $new_title = $new_title. '- fase';
            $fase->update($old_title,$new_title);
        }

        return '';
    }


    public
    static function delete_fase()
    {
        $entry_gforms = GFAPI::get_entries(23);
        if (!empty($entry_gforms)) {
            $id_current_form = $entry_gforms[0]['id'];
            $fase = new Fase();
            $fase->setTitle($entry_gforms[0][1]);
            $fase->delete();
            $result = GFAPI::delete_entry($id_current_form);
        }
        return '';
    }


}