<?php
include_once 'Connection.php';
include_once 'ConnectionSarala.php';

include_once "OrgChartProcess.php";
include_once "Form.php";
include_once "IdProcessCreator.php";

function create_processo()
{
    $lastEntry = GFAPI::get_entries(1)[0];
    $processo = new Processo();
    foreach ($lastEntry as $key => $value) {
        $pattern = "[^9.]";
        if (preg_match($pattern, $key) && $value) {

            $processo->setNomeProcesso($value);
            $processo->setIdForm($lastEntry['form_id']);
            $processo->setAreaProcesso($lastEntry[2]);
            $processo->setServizioProcesso($lastEntry[3]);
            $processo->setUfficioProcesso($lastEntry[4]);
            $id_owner = idProcessCreator::getProcessOwnerId($processo->getAreaProcesso());
            if ($id_owner == NULL || $id_owner == '') {
                $id_owner = idProcessCreator::getProcedureOwnerId($processo->getAreaProcesso(), $processo->getServizioProcesso(), $processo->getUfficioProcesso());
            }
            $processo->setIdUser($id_owner);
            $processo->setRuoloUser('project manager');
            $processo->creaProcesso();
        }
    }

}

add_shortcode('post_processo', 'create_processo');


function delete_processo()
{
    $entry_gforms = GFAPI::get_entries(1);
    $id_current_form = $entry_gforms[0]['id'];
    $process = new Processo();
    $process->setNomeProcesso($entry_gforms[0][1]);
    $process->cancellaProcesso();
    $result = GFAPI::delete_entry($id_current_form);

}

add_shortcode('post_deleteprocesso', 'delete_processo');


function update_processo()
{
//    $entry_gforms = GFAPI::get_entries(34);
//    $process = new Process();
//    $id_current_form = $entry_gforms[0]['id'];
//    $results_processo = Form::getForm($id_current_form);
//    $process->setProcessName($results_processo[1]);
//    $entry = array('1' => $results_processo[1], '2' => $results_processo[2], '3' => $results_processo[3], '4' => $results_processo[4]);
//    $entry_gforms = GFAPI::get_entries(1);
//    $id_current_form = $entry_gforms[0]['id'];
//    $process->setOldProcessName($entry_gforms[0][1]);
//    $process->updateProcess();
//    $result = GFAPI::update_entry($entry, $id_current_form);
    $entry_gforms = GFAPI::get_entries(2);

    /* $procedure = new Procedimento();
     $procedure->setTitle($entry_gforms[0][2]);
     $procedure->setIdForm($entry_gforms[0]['form_id']);
     $procedure->setNameProcess($entry_gforms[0][17]);
     $settore = $entry_gforms[0][18];
     $procedure->setCreatorId(idProcessCreator::getProcessOwnerId($settore));
     $servizio = $entry_gforms[0][19];
     $ufficio = $entry_gforms[0][20];
     $procedure->setOwnerId(idProcessCreator::getProcedureOwnerId($settore, $servizio, $ufficio));

     //$procedure->setDateCreated($entry_gforms[0]['date_created']);
     //$procedure->setDateUpdated($entry_gforms[0]['date_updated']);
     $procedure->setPosition(1);
     $procedure->createProcedure();*/

}

add_shortcode('post_updateprocesso', 'update_processo');

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

        $sql = "INSERT INTO projects (name,owner_id,token) VALUES(?,?,?)";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("sis", $this->nome_processo, $this->id_user, $this->token);
        $res = $stmt->execute();

        $sql = "SELECT id FROM projects WHERE name=? ORDER BY id DESC LIMIT 1";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("s", $this->nome_processo);
        $res = $stmt->execute();
        $res = $stmt->get_result();
        $process = $res->fetch_assoc();
        $this->setIdProcesso($process['id']);
        $sql = "INSERT INTO project_has_users (project_id,user_id,role) VALUES(?,?,?)";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("iis", $this->id_processo, $this->id_user, $this->ruolo_user);
        $res = $stmt->execute();

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
        $sql = "DELETE  FROM projects WHERE id=? ORDER BY id DESC LIMIT 1";
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

    public function aggiornaProcesso($ownerId, $nome_processo)
    {

        $conn = new Connection();
        $mysqli = $conn->connect();
//        $sql = "SELECT id FROM projects WHERE name=? ORDER BY id DESC LIMIT 1";
//        $stmt = $mysqli->prepare($sql);
//        $stmt->bind_param("s", $this->old_process_name);
//        $res = $stmt->execute();
//        $res = $stmt->get_result();
//        $process = $res->fetch_assoc();
//        $this->setIdProcess($process['id']);
        $sql = "UPDATE projects SET owner_id=? WHERE name=? ";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("is", $ownerId, $nome_processo);
        $res = $stmt->execute();

        $sql = "SELECT id FROM projects WHERE name=?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("s", $nome_processo);
        $res = $stmt->execute();
        $res = $stmt->get_result();
        $processo = $res->fetch_assoc();
        $id_processo = $processo['id'];

        $mysqli->close();

        return $id_processo;

    }

}
