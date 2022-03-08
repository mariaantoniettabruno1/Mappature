<?php
namespace MappaturePlugin;
/**
 * Classe Ufficio contentente function di default come getter e setter e custom function
 */


class Ufficio
{
    private $meta_key = 'ufficio';
    private $ufficio;

    public function __construct()
    {

    }

    /**
     * @return mixed
     */
    public function getUfficio()
    {
        return $this->ufficio;
    }

    /**
     * @param mixed $ufficio
     */
    public function setUfficio($ufficio): void
    {
        $this->ufficio = $ufficio;
    }
    /**
     * Function per l'insert dell'ufficio nel db di WordPress, in particolare nella tabella dei metadati dell'utente
     * input: user id
     * output:
     */

    public function setUserUfficio($userid)
    {
        $conn = new Connection();
        $mysqli = $conn->connect();
        $sql = "INSERT INTO user_has_metadata (user_id,name,value) VALUES(?,?,?)";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("iss", $userid, $this->meta_key, $this->ufficio);
        $res = $stmt->execute();
        $mysqli->close();
    }
    /**
     * Function per l'update dell'ufficio nel db di WordPress, in particolare nella tabella dei metadati dell'utente
     * input: user id
     * output:
     */
    public function editUserUfficio($userid){
        $conn = new Connection();
        $mysqli = $conn->connect();
        $sql = "UPDATE user_has_metadata SET  value=? WHERE user_id=? AND name=?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("sis", $this->ufficio, $userid, $this->meta_key);
        $res = $stmt->execute();
        $mysqli->close();
    }
    /**
     * Function per il select dell'ufficio dal custom form costruito tramite gforms
     * per l'inserimento manuale di una nuovo ufficio, collegato ad un area e ad un servizio specifico
     * input: string di area, string di servizio
     * output: array contentente tutti gli uffici inseriti tramite il form
     */
    public function selectUfficio($area,$servizio)
    {

        $uffici = array();
        $conn = new ConnectionSarala();
        $mysqli = $conn->connect();
        $sql = "SELECT meta_value FROM wp_gf_entry_meta WHERE form_id=66 AND meta_key=3 
                                          AND entry_id IN (SELECT entry_id FROM wp_gf_entry_meta WHERE meta_key=1 AND meta_value=?)
                                        AND entry_id IN (SELECT entry_id FROM wp_gf_entry_meta WHERE meta_key=2 AND meta_value=?)";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("ss", $area,$servizio);
        $res = $stmt->execute();
        $res = $stmt->get_result();
        $result = $res->fetch_all();
        foreach ($result as $item) {
            array_push($uffici, $item[0]);
        }

        $mysqli->close();
        return $uffici;
    }
    /**
     * Function per trovare l'ufficio assegnato a dipendenti comunali
     * input: array di dipendenti comunali a cui è stato assegnato un Ufficio
     * output: array contentente tutti gli uffici assegnati ai dipendenti
     */
    public function findUfficioByDipendente($dipendenti){

        $conn = new Connection();
        $mysqli = $conn->connect();
        $sql = "SELECT value FROM user_has_metadata WHERE name='ufficio' AND user_id IN (SELECT id FROM users WHERE name=?)";
        foreach ($dipendenti as $dipendente) {

            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("s", $dipendente);
            $res = $stmt->execute();
            $res = $stmt->get_result();
            $result = $res->fetch_all();
        }

        $mysqli->close();
        return $result[0];
    }
}
