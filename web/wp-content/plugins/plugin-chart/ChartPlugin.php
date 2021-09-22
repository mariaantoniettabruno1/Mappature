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
    $ente = new Ente();
    $ente->setIdFormEnte(22);
    $ente->returnIDEnte();
    $id = $ente->getIdEntryEnte();
    $entry = GFAPI::get_entry($id);
    echo '<pre>'; var_dump($entry); echo '</pre>';
    return $entry;

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
            if( $stmt = $mysqli->prepare($sql)){
                $stmt->bind_param("i", $this->id_form_ente);
                $res = $stmt->execute();
                $res = $stmt->get_result();
                $user = $res->fetch_assoc();
                $this->setIdEntryEnte($user['entry_id']);
            }
           else{
               printf("Errormessage: %s\n", $mysqli->error);
           }

            $mysqli->close();

    }
}