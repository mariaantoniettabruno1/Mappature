<?php

namespace MappaturePlugin;

use GFAPI;

class ShortCodesFase
{
    public static function create_fase()
    {
        $entry_gforms = GFAPI::get_entries(79);
        $fase = new Fase();
        if (!empty($entry_gforms)) {
            $entry_gforms = GFAPI::get_entries(79)[0];
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
        $entry_gforms = GFAPI::get_entries(93);
        $fase = new Fase();
        if (!empty($entry_gforms)) {
            $old_title = $entry_gforms[0][14];
            $new_title = $entry_gforms[0][17];
            $old_title = $old_title . ' - fase';
            $new_title = $new_title . ' - fase';
            $fase->update($old_title, $new_title);
        }

        return '';
    }

    public
    static function associa_fase()
    {
        $entry_gforms = GFAPI::get_entries(106);
        $idKanboard_array = array();
        if (!empty($entry_gforms)) {
            foreach ($entry_gforms[0] as $key => $value) {
                $pattern = "[^7.]";
                if (preg_match($pattern, $key) && $value) {
                    array_push($idKanboard_array, $value);
                }
            }
        }
        $fase_title = $entry_gforms[0][2];
        $fase = new Fase();
        $fase->associa_fase($fase_title,$idKanboard_array);

        return '';
    }


    public
    static function delete_fase()
    {
        $entry_gforms = GFAPI::get_entries(96);
        if (!empty($entry_gforms)) {
            $fase = new Fase();
            $title_fase = $entry_gforms[0][9].' - fase';
            $fase->setTitle($title_fase);
            $fase->deleteFase();

        }
        return '';
    }


}