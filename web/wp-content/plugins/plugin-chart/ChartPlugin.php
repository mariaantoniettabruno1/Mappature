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
    $settore = new Settore();
    $settore->setIdFormSettore(20);
    $settore->returnIDSettore();
    $id_settore = $settore->getIdEntrySettore();
   $entry_settore = GFAPI::get_entries(20);

   echo '<pre>';
    print_r($entry_settore);
    echo '</pre>';

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
        <li><span class="caret"><?php echo $entry_settore['4'] ?></span>
            <ul class="nested">
                        <li><span class="caret"><?php ?></span>
                            <ul class="nested">
                                <li></li>
                                <li></li>
                                <li></li>
                                <li></li>
                            </ul>
                        </li>
                    </ul>
                </li>
            </ul>
        </li>
    </ul>

    <script>
        var toggler = document.getElementsByClassName("caret");
        var i;

        for (i = 0; i < toggler.length; i++) {
            toggler[i].addEventListener("click", function() {
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
