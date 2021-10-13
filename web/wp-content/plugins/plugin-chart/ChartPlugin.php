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
        $temparray = array("settore" => $entry_gforms[$i]['7'],
            "servizio" => $entry_gforms[$i]['8'],
            "ufficio" => $entry_gforms[$i]['4']);
        array_push($data, $temparray);
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
        </style>
    </head>
    <body>
    <ul id="myUL">

            <?php for ($i = 0; $i < sizeof($data); $i++) {
                echo "<div>";
                echo "<li>";
                echo "<span class='caret'>";
                echo "<font color ='green'>";
                echo $data[$i]["settore"];
                echo "</font>";
                echo "</span>";
                echo "<ul class='nested'>";
                echo "<span class='caret'>";
                echo "<font color ='#ff7d1a'>";
                echo $data[$i]["servizio"];
                echo "</font>";
                echo "</span>";
                echo "<ul class='nested'>";
                echo "<span class='caret'>";
                echo "<font color ='#483d8b'>";
                echo $data[$i]["ufficio"];
                echo "</font>";
                echo "</span>";
                echo "</li>";
                echo "</div>";
            }; ?>

    </ul>
    </body>
    <script>
        var toggler = document.getElementsByClassName("caret");
        var i;

        for (i = 0; i < toggler.length; i++) {
            toggler[i].addEventListener("click", function () {
                this.parentElement.querySelector(".nested").classList.toggle("active");
                this.classList.toggle("caret-down");
            });
        }
    </script>

    </body>
    </html>

    <?php
}

add_shortcode('post_orgchart', 'visualize_orgchart');
