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


class Ente
{
    private $id_entry_ente;
    private $id_form_ente;

    public function __construct()
    {

    }

    public function getIdEntryEnte()
    {
        return $this->id_entry_ente;
    }

    public function setIdEntryEnte($id_entry_ente)
    {
        $this->id_entry_ente = $id_entry_ente;
    }

    public function getIdFormEnte()
    {
        return $this->id_form_ente;
    }

    public function setIdFormEnte($id_form_ente)
    {
        $this->id_form_ente = $id_form_ente;
    }


    public function returnIDEnte()
    {
        $conn = new ConnectionSarala();
        $mysqli = $conn->connect();
        $sql = "SELECT entry_id FROM wp_gf_entry_meta WHERE form_id=?";
        if ($stmt = $mysqli->prepare($sql)) {
            $stmt->bind_param("i", $this->id_form_ente);
            $res = $stmt->execute();
            $res = $stmt->get_result();
            $user = $res->fetch_assoc();
            $this->setIdEntryEnte($user['entry_id']);
        } else {
            // printf("Errormessage: %s\n", $mysqli->error);
        }

        $mysqli->close();

    }
}
class Settore
{
    private $id_entry_settore;
    private $id_form_settore;

    public function __construct()
    {

    }

    public function getIdEntrySettore()
    {
        return $this->id_entry_settore;
    }

    public function setIdEntrySettore($id_entry_settore)
    {
        $this->id_entry_settore = $id_entry_settore;
    }

    public function getIdFormSettore()
    {
        return $this->id_form_settore;
    }

    public function setIdFormSettore($id_form_settore)
    {
        $this->id_form_settore = $id_form_settore;
    }


    public function returnIDSettore()
    {
        $conn = new ConnectionSarala();
        $mysqli = $conn->connect();
        $sql = "SELECT entry_id FROM wp_gf_entry_meta WHERE form_id=?";
        if ($stmt = $mysqli->prepare($sql)) {
            $stmt->bind_param("i", $this->id_form_settore);
            $res = $stmt->execute();
            $res = $stmt->get_result();
            $user = $res->fetch_assoc();
            $this->setIdEntrySettore($user['entry_id']);
        } else {
            // printf("Errormessage: %s\n", $mysqli->error);
        }

        $mysqli->close();

    }
}
class Servizio
{
    private $id_entry_servizio;
    private $id_form_servizio;

    public function __construct()
    {

    }

    public function getIdEntryServizio()
    {
        return $this->id_entry_servizio;
    }

    public function setIdEntryServizio($id_entry_servizio)
    {
        $this->id_entry_servizio = $id_entry_servizio;
    }

    public function getIdFormServizio()
    {
        return $this->id_form_servizio;
    }

    public function setIdFormServizio($id_form_servizio)
    {
        $this->id_form_servizio = $id_form_servizio;
    }


    public function returnIDServizio()
    {
        $conn = new ConnectionSarala();
        $mysqli = $conn->connect();
        $sql = "SELECT entry_id FROM wp_gf_entry_meta WHERE form_id=?";
        if ($stmt = $mysqli->prepare($sql)) {
            $stmt->bind_param("i", $this->id_form_servizio);
            $res = $stmt->execute();
            $res = $stmt->get_result();
            $user = $res->fetch_assoc();
            $this->setIdEntryServizio($user['entry_id']);
        } else {
            // printf("Errormessage: %s\n", $mysqli->error);
        }

        $mysqli->close();

    }
}
class Ufficio
{
    private $id_entry_ufficio;
    private $id_form_ufficio;

    public function __construct()
    {

    }

    public function getIdEntryUfficio()
    {
        return $this->id_entry_ufficio;
    }

    public function setIdEntryUfficio($id_entry_ufficio)
    {
        $this->id_entry_ufficio = $id_entry_ufficio;
    }

    public function getIdFormUfficio()
    {
        return $this->id_form_ufficio;
    }

    public function setIdFormUfficio($id_form_ufficio)
    {
        $this->id_form_ufficio = $id_form_ufficio;
    }


    public function returnIDUfficio()
    {
        $conn = new ConnectionSarala();
        $mysqli = $conn->connect();
        $sql = "SELECT entry_id FROM wp_gf_entry_meta WHERE form_id=?";
        if ($stmt = $mysqli->prepare($sql)) {
            $stmt->bind_param("i", $this->id_form_ufficio);
            $res = $stmt->execute();
            $res = $stmt->get_result();
            $user = $res->fetch_assoc();
            $this->setIdEntryUfficio($user['entry_id']);
        } else {
            // printf("Errormessage: %s\n", $mysqli->error);
        }

        $mysqli->close();

    }
}