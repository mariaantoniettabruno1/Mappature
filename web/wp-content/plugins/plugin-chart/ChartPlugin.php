<?php

/*
Plugin Name: Chart Plugin
Plugin URI:
Description:
Version: 0.1
Author: MG3
Author URI:

*/


function visualize_orgchart()
{
    $entry_gforms = GFAPI::get_entries(20);
    $data = array();

    for ($i = 0; $i < sizeof($entry_gforms); $i++) {
        if (!is_array($data[$entry_gforms[$i]['7']][$entry_gforms[$i]['8']])) {
            $data[$entry_gforms[$i]['7']][$entry_gforms[$i]['8']] = array();
        }
        array_push($data[$entry_gforms[$i]['7']][$entry_gforms[$i]['8']], $entry_gforms[$i]['4']);
    }
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <style>
            ul, #myUL {
                list-style-type: none;
            }

            li {
                list-style-type: none;
            }

            #myUL {
                margin: 0;
                margin-left: auto;
                margin-right: auto;
                padding: 0;
            }

            .caret::before {
                content: "\25B6";
                color: darkslategray;
                display: inline-block;
                margin-right: 6px;

            }

            .caret-down::before {
                transform: rotate(90deg);

            }

            .nested {
                display: none;
            }

            .active {
                display: block;
            }

            .settore{
                color:#483D8B ;
            }

            .servizio{
                color: green;
            }

            .ufficio{
                color: #e36d11;
            }
        </style>
    </head>
    <body>
    <ul id="myUL">

    <?php
    foreach ($data as $settore => $listaServizi) {
        echo "
        <li>
            <span class='caret settore'>  $settore </span>
            <ul class='nested'>";
        foreach ($listaServizi as $servizio => $uffici) {
            echo"
                <li>
                   <span class='caret servizio' > $servizio </span>
                   <ul class='nested'>
            ";
            foreach ($uffici as $key => $ufficio){
                echo"
                        <li>
                           <span class='caret ufficio' > $ufficio </span>
                        </li> ";
            }
            echo " </ul>
                </li>";
        }
        echo "
            </ul>
        </li>";
    } ?>

    </ul>
    </body>
    <script>
        let toggler = document.getElementsByClassName("caret");
        console.log(toggler)

        for (let i = 0; i < toggler.length; i++) {
            toggler[i].addEventListener("click", function () {
                this.parentElement.querySelector(" .nested").classList.toggle("active");
                this.classList.toggle("caret-down");
            });
        }
    </script>

    </body>
    </html>

    <?php
}

add_shortcode('post_orgchart', 'visualize_orgchart');
