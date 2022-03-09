<?php

namespace MappaturePlugin;

class ShortCodeTableArea

{
    public static function visualize_table_area()
    {
        ?>


        <!DOCTYPE html>
        <html lang="en">
        <head>

        </head>
        <body>

        <div>
            <form method='POST' action=''>
                <h4>Seleziona Area</h4>
                <?php
                $area = new TableAreaServizioUfficio();
                $results_area = $area->selectAreaForTable();
                ?>
                <select id='area' name='select_area' onchange='this.form.submit()'>
                    <?php foreach ($results_area as $res_area): ?>
                        <option <?= isset($_POST['select_area']) && $_POST['select_area'] === $res_area[0] ? 'selected' : '' ?>
                                value='<?= $res_area[0] ?>'><?= $res_area[0] ?></option>
                    <?php endforeach; ?>
                </select>


                <h4>Seleziona Servizio</h4>

                <?php
                $servizio = new TableAreaServizioUfficio();
                $results_servizio = $servizio->selectServizioForTable();
                ?>

                <select id='servizio' name='select_servizio' onchange='this.form.submit()'>
                    <?php foreach ($results_servizio as $res_serv): ?>
                        <option <?= isset($_POST['select_servizio']) && $_POST['select_servizio'] === $res_serv[0] ? 'selected' : '' ?>
                                value='<?= $res_serv[0] ?>'><?= $res_serv[0] ?></option>
                    <?php endforeach; ?>
                </select>


                <h4>Seleziona Ufficio</h4>
                <?php
                $ufficio = new TableAreaServizioUfficio();
                $results_ufficio = $ufficio->selectUfficioForTable();
                ?>


                <select id='ufficio' name='select_ufficio' onchange='this.form.submit()'>
                    <?php foreach ($results_ufficio as $res_uff): ?>
                        <option <?= isset($_POST['select_ufficio']) && $_POST['select_ufficio'] === $res_uff[0] ? 'selected' : '' ?>
                                value='<?= $res_uff[0] ?>'><?= $res_uff[0] ?></option>
                    <?php endforeach; ?>
                </select>
            </form>


        </div>
        <h2>TABELLA</h2>

        <table class="center">
            <thead>
            <tr>
                <th>Processo</th>
                <th>Dirigente</th>
                <th>Procedimento</th>
                <th>PO</th>
                <th>Dipendenti Associati a Procedimento</th>
                <th>Fase/Attivita</th>
                <th>Dipendenti</th>
            </tr>
            </thead>
            <tbody>
            <?php
            if (isset($_POST['select_area']) && isset($_POST['select_servizio']) && isset($_POST['select_ufficio'])) {

                $selected_area = $_POST["select_area"];

                $selected_servizio = $_POST['select_servizio'];

                $selected_ufficio = $_POST['select_ufficio'];


                $processi = new TableAreaServizioUfficio();

                $array_processi = $processi->getProcesso($selected_area, $selected_servizio, $selected_ufficio);
                $array_processi_kanboard = $processi->checkProcessoOnKanboard($array_processi);

                $data = new TableProcessi();

                foreach ($array_processi_kanboard as $proc) {
                    $array_data = $data->getDataOfProcesso($proc);
                    foreach ($array_data as $item) {
                        echo '<tr>';
                        echo '<td>' . $item['Processo'] . '</td>';
                        echo '<td>' . implode(" , ", $item['Dirigente']) . '</td>';
                        echo '<td>' . $item['Procedimento'] . '</td>';
                        echo '<td>' . implode(" , ", $item['PO']) . '</td>';
                        echo '<td>' . implode(" , ", $item['Dipendenti associati']) . '</td>';
                        if (!empty($item['Fase/Attività'])) {
                            foreach ($item['Fase/Attività'] as $value) {

                                if (is_array($value)) {

                                    echo '<td>' . implode(" , ", $value) . '</td>';


                                } else {
                                    echo '<td>' . $value . '</td>';
                                }

                            }

                        } else {
                            echo '<td>' . '</td>';
                            echo '<td>' . '</td>';

                        }


                        echo '</tr>';
                    }


                }
            } elseif (isset($_POST['select_area']) && isset($_POST['select_servizio'])) {
                $selected_area = $_POST["select_area"];

                $selected_servizio = $_POST['select_servizio'];

                $selected_ufficio = '';
                $processi = new TableAreaServizioUfficio();

                $array_processi = $processi->getProcesso($selected_area, $selected_servizio, $selected_ufficio);
                $array_processi_kanboard = $processi->checkProcessoOnKanboard($array_processi);

                $data = new TableProcessi();
                foreach ($array_processi_kanboard as $proc) {
                    $array_data = $data->getDataOfProcesso($proc);
                    foreach ($array_data as $item) {
                        echo '<tr>';
                        echo '<td>' . $item['Processo'] . '</td>';
                        echo '<td>' . implode(" , ", $item['Dirigente']) . '</td>';
                        echo '<td>' . $item['Procedimento'] . '</td>';
                        echo '<td>' . implode(" , ", $item['PO']) . '</td>';
                        echo '<td>' . implode(" , ", $item['Dipendenti associati']) . '</td>';
                        if (!empty($item['Fase/Attività'])) {
                            foreach ($item['Fase/Attività'] as $value) {

                                if (is_array($value)) {

                                    echo '<td>' . implode(" , ", $value) . '</td>';


                                } else {
                                    echo '<td>' . $value . '</td>';
                                }

                            }

                        } else {
                            echo '<td>' . '</td>';
                            echo '<td>' . '</td>';

                        }


                        echo '</tr>';
                    }


                }
            } else {
                echo '<tr>';
                echo '<td>' . '</td>';
                echo '<td>' . '</td>';
                echo '<td>' . '</td>';
                echo '<td>' . '</td>';
                echo '<td>' . '</td>';
                echo '<td>' . '</td>';

                echo '<td>' . '</td>';
                echo '</tr>';
            }

            ?>
            </tbody>
        </table>

        </div>

        </body>
        </html>
        <?php


    }


}