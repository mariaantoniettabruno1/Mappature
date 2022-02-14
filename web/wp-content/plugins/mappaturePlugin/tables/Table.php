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
            <?php
            if(isset($_POST["select_processo"])){
                $selected_processo = $_POST["select_processo"];
                $processo = new Processo();
                $table = $processo->getDataOfProcesso($selected_processo);
                echo "<pre>";
                print_r($table);
                echo "</pre>";

            }
            ?>
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
            <tr>
                <td>John</td>
                <td>Doe</td>
                <td>john@example.com</td>
                <td>john@example.com</td>
                <td>john@example.com</td>
                <td>john@example.com</td>
                <td>john@example.com</td>
            </tr>
            <tr>
                <td>Mary</td>
                <td>Moe</td>
                <td>mary@example.com</td>
                <td>john@example.com</td>
                <td>john@example.com</td>
                <td>john@example.com</td>
                <td>john@example.com</td>
            </tr>
            <tr>
                <td>July</td>
                <td>Dooley</td>
                <td>july@example.com</td>
                <td>john@example.com</td>
                <td>john@example.com</td>
                <td>john@example.com</td>
                <td>john@example.com</td>

            </tr>


            </tbody>
        </table>
        </div>

        </body>
        </html>
        <?php


    }

    public function select_processo()
    {
        $conn = new Connection();
        $mysqli = $conn->connect();

        $sql = "SELECT name FROM projects";
        $result = mysqli_query($mysqli, $sql);
        $array_projects = array();
        $rows = $result->fetch_all();
        foreach ($rows as $row) {
            array_push($array_projects, $row[0]);
        }
        return $array_projects;
    }
}
