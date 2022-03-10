<?php


namespace MappaturePlugin;

class ShortCodeTableProcessi

{

    public static function visualize_table_processi()

    {

        ?>


        <!DOCTYPE html>

        <html lang="en">

        <head>

        </head>

        <body>


        <div>

            <form method='POST' action=''>

                <h4>Seleziona Processo</h4>


                <?php

                $processo = new Processo();

                $results_processi = $processo->selectProcesso();

                echo "<select id='processo' name='select_processo'   onchange='this.form.submit()'>";
                echo "<option disabled selected value='Seleziona Processo'> Seleziona Processo </option>";

                foreach ($results_processi as $result) {

                    if ($_POST['select_processo'] === $result[0]) {

                        echo "<option selected value='$result[0]'> $result[0]</option>";

                    } else {

                        echo "<option  value='$result[0]'> $result[0]</option>";

                    }


                }


                echo "</select>";


                ?>

            </form>

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

                foreach ($table as $item) {
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

            ?>

            </tbody>

        </table>

        </div>


        </body>

        </html>

        <?php


    }


}

