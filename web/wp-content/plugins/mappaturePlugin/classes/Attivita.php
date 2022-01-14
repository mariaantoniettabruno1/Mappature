<?php

namespace MappaturePlugin;

class Attivita
{
    private $id_attivita;
    private $title_attivita;
    private $user_id;
    private $id_procedure_attivita;
    private $name_procedure_attivita;
    private $id_process_attivita;
    private $name_process_attivita;
    private $id_form_attivita;
    private $old_title_attivita;
    private $users;

    public function __construct()
    {
        $this->users = [];
    }

    public function getIdProcessAttivita()
    {
        return $this->id_process_attivita;
    }


    public function setIdProcessAttivita($id_process)
    {
        $this->id_process_attivita = $id_process;
    }

    public function getNameProcessAttivita()
    {
        return $this->name_process_attivita;
    }


    public function setNameProcessAttivita($name_process)
    {
        $this->name_process_attivita = $name_process;
    }


    public function getIdFormAttivita()
    {
        return $this->id_form_attivita;
    }


    public function setIdFormAttivita($id_form)
    {
        $this->id_form_attivita = $id_form;
    }


    public function getIdAttivita()
    {
        return $this->id_attivita;
    }


    public function setIdAttivita($id)
    {
        $this->id_attivita = $id;
    }


    public function getTitleAttivita()
    {
        return $this->title_attivita;
    }


    public function setTitleAttivita($title)
    {
        $this->title_attivita = $title;
    }


    public function getUserIdAttivita()
    {
        return $this->user_id;
    }


    public function setUserIdAttivita($user_id)
    {
        $this->user_id = $user_id;
    }

    public function getIdProcedureAttivita()
    {
        return $this->id_procedure_attivita;
    }


    public function setIdProcedureAttivita($id_procedure)
    {
        $this->id_procedure_attivita = $id_procedure;
    }


    public function getNameProcedureAttivita()
    {
        return $this->name_procedure_attivita;
    }


    public function setNameProcedureAttivita($name_procedure)
    {
        $this->name_procedure_attivita = $name_procedure;
    }

    public function insertDataAttivitaSarala()
    {
        $conn = new ConnectionSarala();
        $mysqli = $conn->connect();
        $sql = "INSERT INTO MAPP_attivita (id_attivita,id_form,id_processo,id_procedimento) VALUES(?,?,?,?)";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("iiii", $this->id_attivita, $this->id_form_attivita, $this->id_process_attivita, $this->id_procedure_attivita);
        $res = $stmt->execute();
        $mysqli->close();
    }

    public function createAttivita()
    {
        $conn = new Connection();
        $mysqli = $conn->connect();

        $sql = "SELECT id FROM tasks WHERE title=? ORDER BY id DESC LIMIT 1";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("s", $this->name_procedure_attivita);
        $res = $stmt->execute();
        $res = $stmt->get_result();
        $result = $res->fetch_assoc();
        $this->setIdProcedureAttivita($result['id']);


        $sql = "SELECT id FROM projects WHERE name=? ORDER BY id DESC LIMIT 1";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("s", $this->name_process_attivita);
        $res = $stmt->execute();
        $res = $stmt->get_result();
        $result = $res->fetch_assoc();
        if(!empty($result)){
            $this->setIdProcessAttivita($result['id']);
        }



        $a = " - attività";
        $this->title_attivita = $this->title_attivita . $a;

        $sql = "INSERT INTO subtasks (title,task_id) VALUES(?,?)";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("si", $this->title_attivita, $this->id_procedure_attivita);
        $res = $stmt->execute();
        $subtask_id = $mysqli->insert_id;
        $sql = "INSERT INTO MAPP_subtask_users (subtask_id,user_id) VALUES(?,?)";
        $stmt = $mysqli->prepare($sql);
        foreach ($this->users as $userId) {
            $stmt->bind_param("ii", $subtask_id, $userId);
            $res = $stmt->execute();
        }
        $mysqli->close();
        $this->insertDataAttivitaSarala();
    }

    public function update()
    {
        $conn = new Connection();
        $mysqli = $conn->connect();

        $dbTitle = $this->getDbTitle($this->title_attivita);
        $dbOldTitle = $this->getDbTitle($this->old_title_attivita);

        $sql = "UPDATE subtasks SET title=? WHERE title=? ORDER BY id DESC LIMIT 1";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("ss", $dbTitle, $dbOldTitle);
        $res = $stmt->execute();
        $mysqli->close();
    }

    public function delete()
    {
        $conn = new Connection();
        $mysqli = $conn->connect();
        $dbTitle = $this->getDbTitle($this->title_attivita);
        $sql = "DELETE FROM subtasks WHERE title=? ORDER BY id DESC LIMIT 1";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("s", $dbTitle);
        $res = $stmt->execute();
        $mysqli->close();
    }

    public function findAttivitaOnWordpress($area, $servizio, $ufficio)
    {
        $conn = new ConnectionSarala();
        $mysqli = $conn->connect();
        $id_field_creazione_attivita = 1;
        $id_form_creazione_attivita = 24;
        $id_form_attivita_postuma = 59;
        $id_field_attivita_postuma = 1;
        $id_area_form_postuma = 4;
        $id_servizio_form_postuma = 5;
        $id_ufficio_form_postuma = 6;
        $id_area_form = 12;
        $id_servizio_form = 14;
        $id_ufficio_form = 15;
        $servizi = array();

        $sql = "SELECT meta_value FROM wp_gf_entry_meta WHERE form_id=? AND meta_key=? AND
                                              entry_id IN ( SELECT  entry_id FROM wp_gf_entry_meta WHERE meta_key=? AND meta_value=?) AND 
                                              entry_id IN (SELECT  entry_id FROM wp_gf_entry_meta WHERE meta_key=? AND meta_value=? ) AND 
                                              entry_id IN (SELECT  entry_id FROM wp_gf_entry_meta WHERE meta_key=? AND meta_value=? ) 
                                           OR 
                                              form_id=? AND meta_key=? AND
                                              entry_id IN (SELECT  entry_id FROM wp_gf_entry_meta WHERE meta_key=? AND meta_value=?) AND 
                                              entry_id IN ( SELECT  entry_id FROM wp_gf_entry_meta WHERE meta_key=? AND meta_value=?) AND
                                              entry_id IN ( SELECT  entry_id FROM wp_gf_entry_meta WHERE meta_key=? AND meta_value=?)";
        $stmt = $mysqli->prepare($sql);
        if (gettype($servizio) == 'string' && gettype($ufficio) == 'string') {
            $temp_servizio = unserialize($servizio);
            $temp_ufficio = unserialize($ufficio);
        } elseif (gettype($servizio) == 'array' && gettype($ufficio) == 'array') {
            $temp_servizio = $servizio;
            $temp_ufficio = $ufficio;
        }

        foreach ($temp_servizio as $item_servizio) {

            foreach ($temp_ufficio as $item_ufficio) {

                $stmt->bind_param("iiisisisiiisisis", $id_form_creazione_attivita, $id_field_creazione_attivita, $id_area_form, $area, $id_servizio_form, $item_servizio, $id_ufficio_form, $item_ufficio,
                    $id_form_attivita_postuma, $id_field_attivita_postuma, $id_area_form_postuma, $area, $id_servizio_form_postuma, $item_servizio, $id_ufficio_form_postuma, $item_ufficio);
                $stmt->execute();
                $result = $stmt->get_result();
                $row = $result->fetch_all();
                if ($row != null)
                    array_push($servizi, $row);

            }

        }

        $mysqli->close();
        if (empty($servizi))
            return $servizi = array();
        else
            return $servizi[0];

    }

    public function findAttivitaOnKanboard($arrayNameSubtasks)
    {
        $conn = new Connection;
        $mysqli = $conn->connect();
        $array_ids = array();
        $sql = "SELECT  id FROM subtasks WHERE title LIKE ? ";
        $stmt = $mysqli->prepare($sql);
        for ($i = 0; $i < sizeof($arrayNameSubtasks); $i++) {
            foreach ($arrayNameSubtasks[$i] as $nameSubtask) {
                $nameSubtask = "%$nameSubtask%";
                $stmt->bind_param("s", $nameSubtask);
                $res = $stmt->execute();
                $result = $stmt->get_result();
                $row = $result->fetch_all();
                if ($row != null) array_push($array_ids, $row[0][0]);
            }
        }

        $mysqli->close();
        return $array_ids;
    }

    public function deleteDismatchAttivitaUsers($array_ids, $userId)
    {
        $conn = new Connection;
        $mysqli = $conn->connect();
        $sql = "DELETE  FROM MAPP_subtask_users WHERE subtask_id=? AND user_id=?";
        $stmt = $mysqli->prepare($sql);
        foreach ($array_ids as $id) {
            foreach ($userId as $user) {
                $stmt->bind_param("ii", $id, $user);
                $res = $stmt->execute();
            }
        }
        $mysqli->close();
    }

    public function insertMatchAttivitaUsers($array_ids, $userId)
    {
        $conn = new Connection;
        $mysqli = $conn->connect();
        $sql = "INSERT INTO MAPP_subtask_users (subtask_id,user_id) VALUES (?,?)";
        $stmt = $mysqli->prepare($sql);
        foreach ($array_ids as $id) {
            foreach ($userId as $user) {
                $stmt->bind_param("ii", $id, $user);
                $res = $stmt->execute();
            }

        }
    }

    private function getDbTitle($title)
    {
        return $title . " - attività";
    }

    /**
     * @return mixed
     */
    public function getOldTitleAttivita()
    {
        return $this->old_title_attivita;
    }

    /**
     * @param mixed $old_title_attivita
     */
    public function setOldTitleAttivita($old_title_attivita)
    {
        $this->old_title_attivita = $old_title_attivita;
    }

    /**
     * @return array
     */
    public function getUsers(): array
    {
        return $this->users;
    }

    /**
     * @param array $users
     */
    public function setUsers(array $users)
    {
        $this->users = $users;
    }

    public function addUser($value)
    {
        $conn = new ConnectionSarala();
        $mysqli = $conn->connect();
        $sql = "SELECT meta_value FROM wp_usermeta WHERE meta_key='id_kanboard' AND user_id=?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("i", $value);
        $res = $stmt->execute();
        $res = $stmt->get_result();
        $result = $res->fetch_assoc();
        array_push($this->users, $result['meta_value']);
        $mysqli->close();

    }
}