<?php
include_once 'Connection.php';
include_once 'ConnectionSarala.php';

function visualize_atto()
{
    $entry_gforms = GFAPI::get_entries(24);
    $atto = new Atto();
    $atto->setTitleAtto($entry_gforms[0][1]);
    $atto->setNameProcedureAtto($entry_gforms[0][3]);
    $atto->setNameProcessAtto($entry_gforms[0][2]);
    $atto->setIdFormAtto($entry_gforms[0]['form_id']);
    $atto->setIdAtto($entry_gforms[0]['id']);

   /*foreach ($entry_gforms[0] as $key => $value){
        $pattern = "[^9.]";
        if (preg_match($pattern, $key) && $value) {
           $atto->setUserIdAtto(idProcessCreator::getAttoFaseOwnerId($value));

        }
        }
*/
    $atto->createAtto();

}

add_shortcode('post_atto', 'visualize_atto');

function update_atto()
{
    $entry_gforms = GFAPI::get_entries(41);
    $atto = new Atto();
    $id_current_form = $entry_gforms[0]['id'];
    $results_atto = Form::getForm($id_current_form);
    $atto->setTitleAtto($results_atto[1]);

    $entry = array('1'=>$results_atto[1],'2'=>$results_atto[2],'3'=>$results_atto[3], '4'=>$results_atto[4], '5'=>$results_atto[5], '6'=>$results_atto[6]);
    $entry_gforms = GFAPI::get_entries(24);
    $id_current_form = $entry_gforms[0]['id'];
    $atto->setOldTitleAtto($entry_gforms[0][1]);
    $atto->update();
    $result = GFAPI::update_entry($entry, $id_current_form);

}

add_shortcode('post_updateatto', 'update_atto');

function delete_atto(){
    $entry_gforms = GFAPI::get_entries(24);
    $id_current_form = $entry_gforms[0]['id'];
    $atto = new Atto();
    $atto->setTitleAtto($entry_gforms[0][1]);
    $atto->delete();
    $result = GFAPI::delete_entry($id_current_form);
}

add_shortcode('post_deleteatto', 'delete_atto');

class Atto{
    private $id_atto;
    private $title_atto;
    private $user_id;
    private $id_procedure_atto;
    private $name_procedure_atto;
    private $id_process_atto;
    private $name_process_atto;
    private $id_form_atto;
    private $old_title_atto;


    public function getIdProcessAtto()
    {
        return $this->id_process_atto;
    }


    public function setIdProcessAtto($id_process)
    {
        $this->id_process_atto = $id_process;
    }

    public function getNameProcessAtto()
    {
        return $this->name_process_atto;
    }


    public function setNameProcessAtto($name_process)
    {
        $this->name_process_atto = $name_process;
    }


    public function getIdFormAtto()
    {
        return $this->id_form_atto;
    }


    public function setIdFormAtto($id_form)
    {
        $this->id_form_atto = $id_form;
    }



    public function getIdAtto()
    {
        return $this->id_atto;
    }


    public function setIdAtto($id)
    {
        $this->id_atto = $id;
    }


    public function getTitleAtto()
    {
        return $this->title_atto;
    }


    public function setTitleAtto($title)
    {
        $this->title_atto = $title;
    }



    public function getUserIdAtto()
    {
        return $this->user_id;
    }


    public function setUserIdAtto($user_id)
    {
        $this->user_id = $user_id;
    }

    public function getIdProcedureAtto()
    {
        return $this->id_procedure_attp;
    }


    public function setIdProcedureAtto($id_procedure)
    {
        $this->id_procedure_atto = $id_procedure;
    }


    public function getNameProcedureAtto()
    {
        return $this->name_procedure_atto;
    }


    public function setNameProcedureAtto($name_procedure)
    {
        $this->name_procedure_atto = $name_procedure;
    }

    public function insertDataAttoSarala(){
        $conn = new ConnectionSarala();
        $mysqli = $conn->connect();
        $sql = "INSERT INTO MAPP_atti (id_atto,id_form,id_processo,id_procedimento) VALUES(?,?,?,?)";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("iiii", $this->id_atto, $this->id_form_atto, $this->id_process_atto,$this->id_procedure_atto);
        $res = $stmt->execute();
        $mysqli->close();
    }

    public function createAtto(){
        $conn = new Connection();
        $mysqli = $conn->connect();
        $sql = "SELECT id FROM tasks WHERE title=? ORDER BY id DESC LIMIT 1";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("s", $this->name_procedure_atto);
        $res = $stmt->execute();
        $res = $stmt->get_result();
        $result = $res->fetch_assoc();
        $this->setIdProcedureAtto($result['id']);

        $sql = "SELECT id FROM projects WHERE name=? ORDER BY id DESC LIMIT 1";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("s", $this->name_process_atto);
        $res = $stmt->execute();
        $res = $stmt->get_result();
        $result = $res->fetch_assoc();
        $this->setIdProcessAtto($result['id']);

        $a = " - atto";
        $this->title_atto = $this->title_atto.$a;
        $sql = "INSERT INTO subtasks (title,task_id,user_id) VALUES(?,?,?)";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("sii", $this->title_atto, $this->id_procedure_atto, $this->user_id);
        $res = $stmt->execute();
        $mysqli->close();
        $this->insertDataAttoSarala();
    }

    public function update(){
        $conn = new Connection();
        $mysqli = $conn->connect();

        $dbTitle = $this->getDbTitle($this->title_atto);
        $dbOldTitle = $this->getDbTitle($this->old_title_atto);

        $sql = "UPDATE subtasks SET title=? WHERE title=? ORDER BY id DESC LIMIT 1";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("ss", $dbTitle,  $dbOldTitle);
        $res = $stmt->execute();
        $mysqli->close();
    }

    public function delete(){
        $conn = new Connection();
        $mysqli = $conn->connect();
        $dbTitle = $this->getDbTitle($this->title_atto);
        $sql = "DELETE FROM subtasks WHERE title=? ORDER BY id DESC LIMIT 1";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("s", $dbTitle);
        $res = $stmt->execute();
        $mysqli->close();
    }

    private function getDbTitle($title){
        return $title." - atto";
    }

    /**
     * @return mixed
     */
    public function getOldTitleAtto()
    {
        return $this->old_title_atto;
    }

    /**
     * @param mixed $old_title_atto
     */
    public function setOldTitleAtto($old_title_atto)
    {
        $this->old_title_atto = $old_title_atto;
    }


}