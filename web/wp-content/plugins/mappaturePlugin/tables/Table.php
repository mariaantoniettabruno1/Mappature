<?php

namespace MappaturePlugin;
class Table
{
    public static function visualize_table()
    {
        ?>


        <!DOCTYPE html>
        <html lang="en">
        <head>
            <title>Bootstrap Example</title>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
            <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
            <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
        </head>
        <body>

        <div>
            <th>Seleziona Processo</th>
            <br class="container" id="dropdownlist">
            <td>
                <?php
                $processo = new Processo();
                $results_processi = $processo->selectProcesso();
                echo "<form method='POST' action=''>";
                echo "<select id='processo' name='select_processo'   onchange='this.form.submit()'>";
                foreach ($results_processi as $result) {
                    echo "<option selected='selected' value='$result[0]'> $result[0]</option>";
                    echo "<br>";
                }
                echo "</select>";
                echo "</form>";

                ?>
            </td>

        </div>
        <h2>TABELLA PROCESSI</h2>

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
            if (isset($_POST["select_processo"])) {
                $selected_processo = $_POST["select_processo"];
                $table_processi = new TableProcessi();
                $table = $table_processi->getDataOfProcesso($selected_processo);
                $array = array($table);

                foreach ($array as $item) {
                    if (!empty($item['Dipendenti'])) {
                        foreach ($item['Dipendenti'] as $value) {
                            echo '<tr>';
                            echo '<td>' . $item['Processo'] . '</td>';
                            echo '<td>' . implode("", $item['Dirigente']) . '</td>';
                            echo '<td>' . implode("", $item['Procedimento']) . '</td>';
                            echo '<td>' . implode("", $item['PO']) . '</td>';
                            echo '<td>' . implode("", $item['Dipendenti associati']) . '</td>';
                            echo '<td>' . $value[0] . '</td>';
                            unset($value[0]);
                            echo '<td>' . implode(" ", $value) . '</td>';
                            echo '</tr>';

                        }
                    }
                    else{
                        echo '<tr>';
                        echo '<td>' . $item['Processo'] . '</td>';
                        echo '<td>' . implode("", $item['Dirigente']) . '</td>';
                        echo '<td>' . implode("", $item['Procedimento']) . '</td>';
                        echo '<td>' . implode("", $item['PO']) . '</td>';
                        echo '<td>' . implode("", $item['Dipendenti associati']) . '</td>';
                        echo '<td>' . implode("", $item['Fase/Attivita']). '</td>';
                        echo '<td>' . '</td>';
                        echo '</tr>';
                    }


                }
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
