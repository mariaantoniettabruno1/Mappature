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


    }


    public static function create_fase_postuma()
    {
        $entry_gforms = GFAPI::get_entries(60);
        $fase = new Fase();
        if (!empty($entry_gforms)) {
            $entry_gforms = GFAPI::get_entries(60)[0];
            $fase->setTitle($entry_gforms[1]);
            $fase->setNameProcedure($entry_gforms[3]);
            $fase->setNameProcess($entry_gforms[2]);
            $fase->setIdForm($entry_gforms['form_id']);
            $fase->setId($entry_gforms['id']);
            foreach ($entry_gforms as $key => $value) {
                $pattern = "[^7.]";
                if (preg_match($pattern, $key) && $value) {
                    $fase->addUser($value);
                }
            }
            $fase->createFase();
        }


    }


    public static function update_fase()
    {
        $entry_gforms = GFAPI::get_entries(43);
        $fase = new Fase();
        if (!empty($entry_gforms)) {
            $id_current_form = $entry_gforms[0]['id'];
            $results_fase = GForm::getForm($id_current_form);
            $fase->setTitle($results_fase[1]);

            $entry = array('1' => $results_fase[1], '2' => $results_fase[2], '3' => $results_fase[3], '4' => $results_fase[4], '5' => $results_fase[5], '6' => $results_fase[6]);
            $entry_gforms = GFAPI::get_entries(23);
            $id_current_form = $entry_gforms[0]['id'];
            $fase->setOldTitle($entry_gforms[0][1]);
            $fase->update();
            $result = GFAPI::update_entry($entry, $id_current_form);
        }


    }


    public static function delete_fase()
    {
        $entry_gforms = GFAPI::get_entries(23);
        if (!empty($entry_gforms)) {
            $id_current_form = $entry_gforms[0]['id'];
            $fase = new Fase();
            $fase->setTitle($entry_gforms[0][1]);
            $fase->delete();
            $result = GFAPI::delete_entry($id_current_form);
        }

    }


}