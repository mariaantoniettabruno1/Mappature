<?php

namespace MappaturePlugin;


class Processo
{
    private $nome_processo;
    private $id_processo;
    private $id_user;
    private $ruolo_user;
    private $id_form;
    private $prima_posizione = 1;
    private $seconda_posizione = 2;
    private $terza_posizione = 3;
    private $quarta_posizione = 4;
    private $primo_titolo = "In attesa";
    private $secondo_titolo = "Pronto";
    private $terzo_titolo = "In corso";
    private $quarto_titolo = "Fatto";
    private $token = '';
    private $swimlanes_name = "Corsia predefinita";
    private $vecchio_nome_processo;
    private $area_processo;
    private $servizio_processo;
    private $ufficio_processo;
    private $users;



    /**
     * @return mixed
     */
    public function getNomeProcesso()
    {
        return $this->nome_processo;
    }

    /**
     * @param mixed $nome_processo
     */
    public function setNomeProcesso($nome_processo): void
    {
        $this->nome_processo = $nome_processo;
    }

    /**
     * @return mixed
     */
    public function getIdProcesso()
    {
        return $this->id_processo;
    }

    /**
     * @param mixed $id_processo
     */
    public function setIdProcesso($id_processo): void
    {
        $this->id_processo = $id_processo;
    }

    /**
     * @return mixed
     */
    public function getIdUser()
    {
        return $this->id_user;
    }

    /**
     * @param mixed $id_user
     */
    public function setIdUser($id_user): void
    {
        $this->id_user = $id_user;
    }

    /**
     * @return mixed
     */
    public function getRuoloUser()
    {
        return $this->ruolo_user;
    }

    /**
     * @param mixed $ruolo_user
     */
    public function setRuoloUser($ruolo_user): void
    {
        $this->ruolo_user = $ruolo_user;
    }

    /**
     * @return mixed
     */
    public function getIdForm()
    {
        return $this->id_form;
    }

    /**
     * @param mixed $id_form
     */
    public function setIdForm($id_form): void
    {
        $this->id_form = $id_form;
    }

    /**
     * @return int
     */
    public function getPrimaPosizione(): int
    {
        return $this->prima_posizione;
    }

    /**
     * @param int $prima_posizione
     */
    public function setPrimaPosizione(int $prima_posizione): void
    {
        $this->prima_posizione = $prima_posizione;
    }

    /**
     * @return int
     */
    public function getSecondaPosizione(): int
    {
        return $this->seconda_posizione;
    }

    /**
     * @param int $seconda_posizione
     */
    public function setSecondaPosizione(int $seconda_posizione): void
    {
        $this->seconda_posizione = $seconda_posizione;
    }

    /**
     * @return int
     */
    public function getTerzaPosizione(): int
    {
        return $this->terza_posizione;
    }

    /**
     * @param int $terza_posizione
     */
    public function setTerzaPosizione(int $terza_posizione): void
    {
        $this->terza_posizione = $terza_posizione;
    }

    /**
     * @return int
     */
    public function getQuartaPosizione(): int
    {
        return $this->quarta_posizione;
    }

    /**
     * @param int $quarta_posizione
     */
    public function setQuartaPosizione(int $quarta_posizione): void
    {
        $this->quarta_posizione = $quarta_posizione;
    }

    /**
     * @return string
     */
    public function getPrimoTitolo(): string
    {
        return $this->primo_titolo;
    }

    /**
     * @param string $primo_titolo
     */
    public function setPrimoTitolo(string $primo_titolo): void
    {
        $this->primo_titolo = $primo_titolo;
    }

    /**
     * @return string
     */
    public function getSecondoTitolo(): string
    {
        return $this->secondo_titolo;
    }

    /**
     * @param string $secondo_titolo
     */
    public function setSecondoTitolo(string $secondo_titolo): void
    {
        $this->secondo_titolo = $secondo_titolo;
    }

    /**
     * @return string
     */
    public function getTerzoTitolo(): string
    {
        return $this->terzo_titolo;
    }

    /**
     * @param string $terzo_titolo
     */
    public function setTerzoTitolo(string $terzo_titolo): void
    {
        $this->terzo_titolo = $terzo_titolo;
    }

    /**
     * @return string
     */
    public function getQuartoTitolo(): string
    {
        return $this->quarto_titolo;
    }

    /**
     * @param string $quarto_titolo
     */
    public function setQuartoTitolo(string $quarto_titolo): void
    {
        $this->quarto_titolo = $quarto_titolo;
    }


    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * @param string $token
     */
    public function setToken(string $token): void
    {
        $this->token = $token;
    }

    /**
     * @return string
     */
    public function getSwimlanesName(): string
    {
        return $this->swimlanes_name;
    }

    /**
     * @param string $swimlanes_name
     */
    public function setSwimlanesName(string $swimlanes_name): void
    {
        $this->swimlanes_name = $swimlanes_name;
    }

    /**
     * @return mixed
     */
    public function getVecchioNomeProcesso()
    {
        return $this->vecchio_nome_processo;
    }

    /**
     * @param mixed $vecchio_nome_processo
     */
    public function setVecchioNomeProcesso($vecchio_nome_processo): void
    {
        $this->vecchio_nome_processo = $vecchio_nome_processo;
    }

    /**
     * @return mixed
     */
    public function getAreaProcesso()
    {
        return $this->area_processo;
    }

    /**
     * @param mixed $area_processo
     */
    public function setAreaProcesso($area_processo): void
    {
        $this->area_processo = $area_processo;
    }

    /**
     * @return mixed
     */
    public function getServizioProcesso()
    {
        return $this->servizio_processo;
    }

    /**
     * @param mixed $servizio_processo
     */
    public function setServizioProcesso($servizio_processo): void
    {
        $this->servizio_processo = $servizio_processo;
    }

    /**
     * @return mixed
     */
    public function getUfficioProcesso()
    {
        return $this->ufficio_processo;
    }

    /**
     * @param mixed $ufficio_processo
     */
    public function setUfficioProcesso($ufficio_processo): void
    {
        $this->ufficio_processo = $ufficio_processo;
    }


    public function inserisciDataProcessoSarala()
    {
        $conn = new ConnectionSarala();
        $mysqli = $conn->connect();
        $sql = "INSERT INTO MAPP_processi (id_form,id_processo) VALUES(?,?)";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("ii", $this->id_form, $this->id_processo);
        $res = $stmt->execute();
        $mysqli->close();
    }


    public function creaProcesso()
    {
        $conn = new Connection();
        $mysqli = $conn->connect();

        $sql = "INSERT INTO projects (name,token) VALUES(?,?)";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("ss", $this->nome_processo, $this->token);
        $res = $stmt->execute();

        $sql = "SELECT id FROM projects WHERE name=? ORDER BY id DESC LIMIT 1";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("s", $this->nome_processo);
        $res = $stmt->execute();
        $res = $stmt->get_result();
        $process = $res->fetch_assoc();
        $this->setIdProcesso($process['id']);

        $sql = "INSERT INTO columns (project_id,position,title) VALUES(?,?,?)";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("iis", $this->id_processo, $this->prima_posizione, $this->primo_titolo);
        $res = $stmt->execute();
        $sql = "INSERT INTO columns (project_id,position,title) VALUES(?,?,?)";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("iis", $this->id_processo, $this->seconda_posizione, $this->secondo_titolo);
        $res = $stmt->execute();
        $sql = "INSERT INTO columns (project_id,position,title) VALUES(?,?,?)";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("iis", $this->id_processo, $this->terza_posizione, $this->terzo_titolo);
        $res = $stmt->execute();
        $sql = "INSERT INTO columns (project_id,position,title) VALUES(?,?,?)";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("iis", $this->id_processo, $this->quarta_posizione, $this->quarto_titolo);
        $res = $stmt->execute();

        $sql = "INSERT INTO swimlanes (name,project_id) VALUES(?,?)";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("si", $this->swimlanes_name, $this->id_processo);
        $res = $stmt->execute();
        $mysqli->close();
        $this->inserisciDataProcessoSarala();
    }


    public function cancellaProcesso()
    {
        $conn = new Connection();
        $mysqli = $conn->connect();
        $sql = "SELECT id FROM projects WHERE name=? ORDER BY id DESC LIMIT 1";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("s", $this->nome_processo);
        $res = $stmt->execute();
        $res = $stmt->get_result();
        $process = $res->fetch_assoc();
        $this->setIdProcesso($process['id']);
        $sql = "DELETE FROM projects WHERE id=? ORDER BY id DESC LIMIT 1";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("i", $this->id_processo);
        $res = $stmt->execute();
        $mysqli->close();
        $conn = new ConnectionSarala();
        $mysqli = $conn->connect();
        $sql = "DELETE  FROM MAPP_processi WHERE id_processo=?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("i", $this->id_processo);
        $res = $stmt->execute();
        $mysqli->close();

    }

    public function selectProcesso()
    {
        $conn = new Connection();
        $mysqli = $conn->connect();

        $sql = "SELECT name FROM projects";
        $result = mysqli_query($mysqli, $sql);
        $row = $result->fetch_all();
        mysqli_close($mysqli);
        return $row;

    }


    public function findProject()
    {
        $conn = new Connection();
        $mysqli = $conn->connect();
        $sql = "SELECT id FROM projects WHERE name=?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("s", $this->nome_processo);
        $res = $stmt->execute();
        $res = $stmt->get_result();
        $result = $res->fetch_assoc();
        $this->setIdProcesso($result['id']);
        $mysqli->close();
    }

    public function findProjectByUser($username)
    {

        $project_names = array();
        $id_projects = array();
        $conn = new Connection();
        $mysqli = $conn->connect();
        $sql = "SELECT project_id FROM MAPP_project_users_owner WHERE user_id IN (SELECT id FROM users WHERE username=?)";
        $stmt = $mysqli->prepare($sql);
        $id_projects = array();
        $stmt->bind_param("s", $username);
        $res = $stmt->execute();
        $res = $stmt->get_result();
        $rows = $res->fetch_all();
        foreach ($rows as $row) {
            array_push($id_projects, $row[0]);
        }

        $project_names = array();
        $sql = "SELECT name FROM projects WHERE id=?";
        $stmt = $mysqli->prepare($sql);
        foreach ($id_projects as $item) {
            $stmt->bind_param("i", $item);
            $res = $stmt->execute();
            $res = $stmt->get_result();
            $result = $res->fetch_all();

            foreach ($result as $row) {
                array_push($project_names, $row[0]);
            }
        }
        $mysqli->close();

        return $project_names;
    }

    public function findProjectsOnWordpress($area)
    {
        $conn = new ConnectionSarala();
        $mysqli = $conn->connect();
        $id_field_processo = 1;
        $id_form_creazione_processo = 65;
        $id_form_processo_csv = 1;
        $id_field_processo_csv = "%9.%";
        $id_area_form = 2;
        $sql = "SELECT ALL meta_value FROM wp_gf_entry_meta WHERE form_id=? AND meta_key=? AND
                                                  entry_id IN (SELECT  entry_id FROM wp_gf_entry_meta WHERE meta_key=? AND meta_value=?) 
                                               OR form_id=? AND meta_key LIKE ? AND
                                                  entry_id IN (SELECT  entry_id FROM wp_gf_entry_meta WHERE meta_key=? AND meta_value=?)";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("iiisisis", $id_form_creazione_processo, $id_field_processo, $id_area_form, $area,
            $id_form_processo_csv, $id_field_processo_csv, $id_area_form, $area);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_all();
        $mysqli->close();

        return $row;

    }

    public function findProjectsOnKanboard($arrayNameProjects)
    {
        $conn = new Connection;
        $mysqli = $conn->connect();
        $array_ids = array();
        $sql = "SELECT  id FROM projects WHERE name=?";
        $stmt = $mysqli->prepare($sql);
        foreach ($arrayNameProjects as $nameProject) {
            foreach ($nameProject as $item) {
                $stmt->bind_param("s", $item);
                $stmt->execute();
                $result = $stmt->get_result();
                $row = $result->fetch_all();
            }
            if (!empty($row)) {
                array_push($array_ids, $row[0][0]);
            }
        }

        $mysqli->close();
        return $array_ids;
    }

    public function deleteDismatchProject($array_ids, $userId)
    {
        $conn = new Connection;
        $mysqli = $conn->connect();
        $sql = "DELETE  FROM MAPP_project_users_owner WHERE project_id=? AND user_id=?";
        $stmt = $mysqli->prepare($sql);
        foreach ($array_ids as $id) {
            foreach ($userId as $user) {
                $stmt->bind_param("ii", $id, $user);
                $res = $stmt->execute();
            }

        }
        $mysqli->close();
    }

    public function insertMatchProject($array_ids, $userId)
    {
        $conn = new Connection;
        $mysqli = $conn->connect();


        $sql = "INSERT INTO MAPP_project_users_owner (project_id,user_id) VALUES (?,?)";
        $stmt = $mysqli->prepare($sql);
        foreach ($array_ids as $id) {
            foreach ($userId as $user) {
                $stmt->bind_param("ii", $id, $user);
                $res = $stmt->execute();
            }
        }

        $mysqli->close();
    }


    public function assignUsers($usersArray)
    {
        $conn = new Connection();
        $mysqli = $conn->connect();

        $sql = "INSERT INTO MAPP_project_users_owner (project_id,user_id) VALUES(?,?)";
        $stmt = $mysqli->prepare($sql);
        for ($i = 0; $i < sizeof($usersArray); $i++) {
            foreach ($usersArray[$i] as $userId) {
                $stmt->bind_param("ii", $this->id_processo, $userId);
                $res = $stmt->execute();

            }
        }
        $sql = "INSERT INTO project_has_users (project_id,user_id,role) VALUES(?,?,?)";
        $stmt = $mysqli->prepare($sql);
        for ($i = 0; $i < sizeof($usersArray); $i++) {
            foreach ($usersArray[$i] as $userId) {
                $stmt->bind_param("iis", $this->id_processo, $userId, $this->ruolo_user);
                $res = $stmt->execute();

            }
        }

        $mysqli->close();
    }

}
