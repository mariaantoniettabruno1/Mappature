<?php


namespace MappaturePlugin;


class ShortCodeTableUtente


{

    public static function visualize_table_utente()

    {

        ?>


        <!DOCTYPE html>

        <html lang="en">

        <head>


        </head>

        <body>


        <div>

            <form method='POST' action=''>

                <h4>Seleziona Utente</h4>

                <?php

                $user = new TableUtente();

                $results_user = $user->getAllUsers();

                ?>

                <select id='user' name='select_user' onchange='this.form.submit()'>

                    <?php foreach ($results_user as $res_user): ?>

                        <option <?= isset($_POST['select_user']) && $_POST['select_user'] === $res_user[0] ? 'selected' : '' ?>

                                value='<?= $res_user[0] ?>'><?= $res_user[0] ?></option>

                    <?php endforeach; ?>

                </select>

            </form>


        </div>

        <h2>TABELLA</h2>


        <table class="center">

            <thead>

            <tr>

                <th>Processo</th>

                <th>Procedimento di cui Dirigente</th>

                <th>Procedimento di cui PO</th>

                <th>Procedimento di cui Dipendente</th>

                <th>Fase/Attivita</th>

            </tr>

            </thead>

            <tbody>

            <?php

            if (isset($_POST['select_user'])) {


                $selected_user = $_POST["select_user"];

                $id_kanboard = $user->getIdKanboard($selected_user);

                $processi = $user->selectProcessoUtente($id_kanboard);

                $procedimenti_creator = $user->selectProcedimentoUtenteCreator($id_kanboard);

                $procedimenti_owner = $user->selectProcedimentoUtenteOwner($id_kanboard);

                $procedimenti_associati = $user->selectProcedimentoUtenteDipendente($id_kanboard);

                $fasi_attivita = $user->selectFaseAttivitaUtente($id_kanboard);


                $table = array("Processo" => $processi,

                    "Procedimento di cui Dirigente" => $procedimenti_creator,

                    "Procedimento di cui PO" => $procedimenti_owner,

                    "Procedimento di cui Dipendente" => $procedimenti_associati,

                    "Fase/Attivita" => $fasi_attivita);


                if (!empty($table)) {

                    array_unshift($table, null);

                    $transposedarr = call_user_func_array('array_map', $table);


                    foreach ($transposedarr as $r) {

                        echo '<tr>';

                        foreach ($r as $c) {

                            echo '<td>' . $c[0] . '</td>';

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