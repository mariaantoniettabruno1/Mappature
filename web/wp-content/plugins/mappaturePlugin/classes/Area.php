<?php

namespace MappaturePlugin;

/**
 * Classe area contentente function di default come getter e setter e custom function
 */


class Area
{
    private $meta_key = 'area';
    private $area;

    /**
     * @return mixed
     */
    public function getArea()
    {
        return $this->area;
    }

    /**
     * @param mixed $area
     */
    public function setArea($area): void
    {
        $this->area = $area;
    }

    /**
     * Function per l'insert dell'area nel db di WordPress, in particolare nella tabella dei metadati dell'utente
     * input: user id
     * output:
     */

    public function setUserArea($userid)
    {
        $conn = new Connection();
        $mysqli = $conn->connect();
        $sql = "INSERT INTO user_has_metadata (user_id,name,value) VALUES(?,?,?)";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("iss", $userid, $this->meta_key, $this->area);
        $res = $stmt->execute();
        $mysqli->close();
    }
    /**
     * Function per l'update dell'area nel db di WordPress, in particolare nella tabella dei metadati dell'utente
     * input: user id
     * output:
     */

    public function editUserArea($userid)
    {
        $conn = new Connection();
        $mysqli = $conn->connect();
        $sql = "UPDATE user_has_metadata SET  value=? WHERE user_id=? AND name=?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("sis", $this->area, $userid, $this->meta_key);
        $res = $stmt->execute();
        $mysqli->close();
    }
    /**
     * Function per il select dell'area dal custom form costruito tramite gforms
     * per l'inserimento manuale di una nuova area
     *     * input:
     * output: array contentente tutte le aree inserite tramite il form
     */
    public function selectArea()
    {
        $form_id = 17;
        $conn = new ConnectionSarala();
        $mysqli = $conn->connect();
        $sql = "SELECT meta_value FROM wp_gf_entry_meta WHERE form_id=?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("i", $form_id);
        $res = $stmt->execute();
        $res = $stmt->get_result();
        $result = $res->fetch_all();
        $mysqli->close();
        return $result;
    }

    /**
     * Function per trovare l'area assegnata ad un dirigente comunale
     * input: nome del dirigente
     * output: array contentente tutte le aree assegnate al dirigente
     */
    public function findAreaByDirigente($dirigenti)
    {

        $conn = new Connection();
        $mysqli = $conn->connect();
        $sql = "SELECT value FROM user_has_metadata WHERE name='area' AND user_id IN (SELECT id FROM users WHERE username=?)";
        foreach ($dirigenti as $dirigente) {

            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("s", $dirigente);
            $res = $stmt->execute();
            $res = $stmt->get_result();
            $result = $res->fetch_all();
        }

        $mysqli->close();
        return $result[0];
    }
}
