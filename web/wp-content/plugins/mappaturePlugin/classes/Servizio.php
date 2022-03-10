<?php
namespace MappaturePlugin;

/**
 * Classe servizio contentente function di default come getter e setter e custom function
 */

class Servizio
{
    private $meta_key = 'servizio';
    private $servizio;


    public function __construct()
    {

    }

    /**
     * @return mixed
     */
    public function getServizio()
    {
        return $this->servizio;
    }

    /**
     * @param mixed $servizio
     */
    public function setServizio($servizio): void
    {
        $this->servizio = $servizio;
    }


    /**
     * Function per l'insert del servizio nel db di WordPress, in particolare nella tabella dei metadati dell'utente
     * input: user id
     * output:
     */

    public function setUserServizio($userid)
    {
        $conn = new Connection();
        $mysqli = $conn->connect();
        $sql = "INSERT INTO user_has_metadata (user_id,name,value) VALUES(?,?,?)";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("iss", $userid, $this->meta_key, $this->servizio);
        $res = $stmt->execute();
        $mysqli->close();
    }
    /**
     * Function per l'edit del servizio nel db di WordPress, in particolare nella tabella dei metadati dell'utente
     * input: user id
     * output:
     */

    public function editUserServizio($userid){
        $conn = new Connection();
        $mysqli = $conn->connect();
        $sql = "UPDATE user_has_metadata SET  value=? WHERE user_id=? AND name=?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("sis", $this->servizio, $userid, $this->meta_key);
        $res = $stmt->execute();
        $mysqli->close();
    }
    /**
     * Function per la select del servizio nel db di WordPress, in particolare dalla tabella delle entries
     * di uno specifico form
     * input: string dell'area correlata al servizio
     * output: array contentente i servizi correlati all'area
     */
    public function selectServizio($area)
    {
        $servizi = array();
        $conn = new ConnectionSarala();
        $mysqli = $conn->connect();
        $sql = "SELECT meta_value FROM wp_gf_entry_meta WHERE (form_id=105 AND meta_key=2
                                          AND entry_id IN (SELECT entry_id FROM wp_gf_entry_meta WHERE meta_key=1 AND meta_value=?)) OR
                                            (form_id=78 AND meta_key=1 
                                                  AND entry_id IN(SELECT entry_id FROM wp_gf_entry_meta WHERE meta_key=3 AND meta_value=?))   
                                       ";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("ss", $area,$area);
        $res = $stmt->execute();
        $res = $stmt->get_result();
        $result = $res->fetch_all();
        foreach ($result as $item) {
            array_push($servizi, $item[0]);
        }
        $mysqli->close();
        return $servizi;
    }
    /**
     * Function per trovare il servizio assegnato ad un PO comunale
     * input: array di PO ai quali è assegnato uno specifico servizio
     * output: array contentente tutti i servizi assegnati ai PO
     */
    public function findServizioByPO($po){

        $conn = new Connection();
        $mysqli = $conn->connect();
        $sql = "SELECT value FROM user_has_metadata WHERE name='servizio' AND user_id IN (SELECT id FROM users WHERE name=?)";
        foreach ($po as $single_po) {
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("s", $single_po);
            $res = $stmt->execute();
            $res = $stmt->get_result();
            $result = $res->fetch_all();
        }

        $mysqli->close();
        return $result[0];

    }
}