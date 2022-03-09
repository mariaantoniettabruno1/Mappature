<?php

namespace MappaturePlugin;

use GFAPI;

class ShortCodesDipendenteProcedimento
{
    public static function assign_dipendente()
    {

        $entry_gforms = GFAPI::get_entries(102);
        echo "<pre>";
        print_r($entry_gforms[0]);
        echo "</pre>";
        $procedimento = new Procedimento();

        $old_value = '';
        if (!empty($entry_gforms)) {
            foreach ($entry_gforms[0] as $key => $value) {
                $pattern = "[^1.]";

                if (preg_match($pattern, $key) && $value) {

                    $procedimento->setTitle($value);

                    foreach ($entry_gforms[0] as $key => $value) {
                        $pattern = "[^3.]";

                        if (preg_match($pattern, $key) && $value) {

                                $procedimento->addUser($value);

                        }

                    }


                }
                $procedimento->findTask();
                $procedimento->assegnaDipendenti();
            }
        }

        return '';

    }


    public static function edit_assign_dipendente()
    {
        $entry_gforms = GFAPI::get_entries(103);

        $procedimento = new Procedimento();
        $old_value = '';
        if (!empty($entry_gforms)) {
            foreach ($entry_gforms[0] as $key => $value) {
                $pattern = "[^1.]";
                if (preg_match($pattern, $key) && $value) {

                    $procedimento->setTitle($value);
                    foreach ($entry_gforms[0] as $key => $value) {
                        $pattern = "[^3.]";
                        if (preg_match($pattern, $key) && $value) {
                            if ($old_value != $value) {
                                $procedimento->addUser($value);
                                $old_value = $value;
                            }


                        }
                    }


                }
                $procedimento->findTask();
                $procedimento->modificaAssegnazioneDipendenti();
            }
        }


        return '';
    }


}