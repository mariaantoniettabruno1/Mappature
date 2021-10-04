<?php

/*
Plugin Name: Chart Plugin
Plugin URI:
Description:
Version: 0.1
Author: MG3
Author URI:

*/
require_once 'ConnectionSarala.php';

function visualize_orgchart()
{
    $entry_gforms = GFAPI::get_entries(20);

    $data = array(
        "settore" => $entry_gforms[0]['7'],
        "servizio" => $entry_gforms[0]['8'],
        "ufficio" => $entry_gforms[0]['4']

    );


    //echo '<pre>';
    // print_r($entry_gforms);
    // echo '</pre>';

    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <style>
            ul, #myUL {
                list-style-type: none;
                margin: 0 auto;
            }

            #myUL {
                margin: 0;
                padding: 0;
            }

            .caret::before {
                content: "\25B6";
                color: black;
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
        <div style="text-align: center;">
            <li><span class="caret"><?php echo $data["settore"]; ?></span>
                <ul class="nested">
                    <li><span class="caret"><?php echo $data["servizio"]; ?></span>
                        <ul class="nested">
                            <li><span class="caret"><?php echo $data["ufficio"]; ?></span>
                            </li>
                        </ul>
                    </li>
                </ul>
            </li>
    </ul>
    </li>
    </ul>
    </body>
    </div>
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
