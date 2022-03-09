<?php

namespace MappaturePlugin;

use mysqli;


class Fase
{
    private $id;
    private $title;
    private $user_id;
    private $users;
    private $id_procedure;
    private $name_procedure;
    private $name_process;
    private $id_process;
    private $id_form;
    private $old_title;

    public function __construct()
    {
        $this->users = [];
    }


    public function getIdForm()
    {
        return $this->id_form;
    }


    public function setIdForm($id_form)
    {
        $this->id_form = $id_form;
    }


    public function getIdProcess()
    {
        return $this->id_process;
    }


    public function setIdProcess($id_process)
    {
        $this->id_process = $id_process;
    }

    public function getId()
    {
        return $this->id;
    }


    public function setId($id)
    {
        $this->id = $id;
    }


    public function getTitle()
    {
        return $this->title;
    }


    public function setTitle($title)
    {
        $this->title = $title;
    }


    public function getUserId()
    {
        return $this->user_id;
    }


    public function setUserId($user_id)
    {
        $this->user_id = $user_id;
    }

    public function getIdProcedure()
    {
        return $this->id_procedure;
    }


    public function setIdProcedure($id_procedure)
    {
        $this->id_procedure = $id_procedure;
    }


    public function getNameProcedure()
    {
        return $this->name_procedure;
    }


    public function getNameProcess()
    {
        return $this->name_process;
    }

    public function setNameProcess($name_process)
    {
        $this->name_process = $name_process;
    }


    public function setNameProcedure($name_procedure)
    {
        $this->name_procedure = $name_procedure;
    }

    public function insertDataFaseSarala()
    {
        $conn = new ConnectionSarala();
        $mysqli = $conn->connect();
        $sql = "INSERT INTO MAPP_fasi (id_fase,id_form,id_processo,id_procedimento) VALUES(?,?,?,?)";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("iiii", $this->id, $this->id_form, $this->id_process, $this->id_procedure);
        $res = $stmt->execute();
        $mysqli->close();
    }

    public function createFase()
    {
        $conn = new Connection();
        $mysqli = $conn->connect();
        $sql = "SELECT id FROM tasks WHERE title=? ORDER BY id DESC LIMIT 1";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("s", $this->name_procedure);
        $res = $stmt->execute();
        $res = $stmt->get_result();
        $result = $res->fetch_assoc();
        if (!empty($result)) {
            $this->setIdProcedure($result['id']);
        }

        $sql = "SELECT id FROM projects WHERE name=? ORDER BY id DESC LIMIT 1";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("s", $this->name_process);
        $res = $stmt->execute();
        $res = $stmt->get_result();
        $result = $res->fetch_assoc();
        if (!empty($result)) {
            $this->setIdProcess($result['id']);
        }


        $a = " - fase";
        $this->title = $this->title . $a;
        $sql = "INSERT INTO subtasks (title,task_id) VALUES(?,?)";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("si", $this->title, $this->id_procedure);
        $res = $stmt->execute();
        $subtask_id = $mysqli->insert_id;
        $sql = "INSERT INTO MAPP_subtask_users (subtask_id,user_id) VALUES(?,?)";
        $stmt = $mysqli->prepare($sql);
        foreach ($this->users as $userId) {
            $stmt->bind_param("ii", $subtask_id, $userId);
            $res = $stmt->execute();
        }
        $mysqli->close();
        $this->insertDataFaseSarala();


    }

    public function update($old_title, $new_title)
    {
        $conn = new Connection();
        $mysqli = $conn->connect();
        $sql = "UPDATE subtasks SET title=? WHERE title=?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("ss", $new_title, $old_title);
        $res = $stmt->execute();
        $mysqli->close();
    }

    public function deleteFase()
    {

        $old_title = $this->title;
        $conn = new Connection();
        $mysqli = $conn->connect();
        $sql = "SELECT id FROM subtasks WHERE title=?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("s", $this->title);
        $res = $stmt->execute();
        $res = $stmt->get_result();
        $fase = $res->fetch_assoc();

        if ($fase != NULL && $fase['id'] != NULL) {
            $this->setId($fase['id']);
            $sql = "DELETE FROM subtasks WHERE id=?";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("i", $this->id);
            $res = $stmt->execute();
            $mysqli->close();
            $conn = new ConnectionSarala();
            $mysqli = $conn->connect();
            $sql = "DELETE FROM MAPP_fasi WHERE id_fase=?";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("i", $this->id);
            $res = $stmt->execute();

        }
        $mysqli->close();

        $conn = new ConnectionSarala();
        $mysqli = $conn->connect();
        $this->setTitle(explode(' - fase', $this->title)[0]);
        while ($this->title != NULL) {
            
            $sql = "SELECT m2.meta_value FROM wp_gf_entry_meta AS m1
JOIN wp_gf_entry_meta AS m2 ON m1.entry_id = m2.entry_id
WHERE (m1.meta_value=?) AND m2.meta_key=14";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("s", $this->title);
            $res = $stmt->execute();
            $res = $stmt->get_result();
            $old_title = $res->fetch_assoc();

            $sql = "SELECT entry_id FROM wp_gf_entry_meta WHERE meta_value=? AND form_id=93";
            if ($old_title != NULL && $old_title['meta_value'] != NULL) {
                $stmt = $mysqli->prepare($sql);
                $stmt->bind_param("s", $this->title);
                $res = $stmt->execute();
                $res = $stmt->get_result();
                $id_entry = $res->fetch_assoc();
                if($id_entry!=NULL && $id_entry['entry_id']!=NULL) {
                    $sql = "DELETE FROM wp_gf_entry_meta WHERE entry_id=?";
                    $stmt = $mysqli->prepare($sql);
                    $stmt->bind_param("i", $id_entry['entry_id']);
                    $res = $stmt->execute();
                    if ($old_title != NULL && $old_title['meta_value'] != NULL) {
                        $this->setTitle($old_title['meta_value']);
                    }
                }
                    else {
                        $sql = "DELETE FROM wp_gf_entry_meta WHERE form_id=79 AND meta_value=? ";
                        $stmt = $mysqli->prepare($sql);
                        $stmt->bind_param("s", $this->title);
                        $res = $stmt->execute();
                        $this->setTitle(NULL);
                    }
                }



            else {
                $sql = "DELETE FROM wp_gf_entry_meta WHERE form_id=79 AND meta_value=? ";
                $stmt = $mysqli->prepare($sql);
                print_r("Sono nell'else");
                print_r($old_title);
                $stmt->bind_param("s", $this->title);
                $res = $stmt->execute();
                $this->setTitle(NULL);
            }
        }


        $mysqli->close();
    }

    public function associa_fase($title_fase, $array_users)
    {
        $conn = new Connection();
        $mysqli = $conn->connect();
        $title_fase = $title_fase . " - fase";

        $sql = "SELECT id FROM subtasks WHERE title LIKE ? ";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("s", $title_fase);
        $res = $stmt->execute();
        $res = $stmt->get_result();
        $result = $res->fetch_assoc();


        $sql = "INSERT INTO MAPP_subtask_users (subtask_id,user_id) VALUES(?,?)";
        $stmt = $mysqli->prepare($sql);
        foreach ($array_users as $userId) {
            $stmt->bind_param("ii", $result['id'], $userId);
            $res = $stmt->execute();
        }


        $mysqli->close();
    }

    public function findFaseOnWordpress($area, $servizio, $ufficio)
    {
        $conn = new ConnectionSarala();
        $mysqli = $conn->connect();
        $id_field_creazione_fase = 1;
        $id_form_creazione_fase = 23;
        $id_form_fase_postuma = 60;
        $id_field_fase_postuma = 1;
        $id_area_form_postuma = 4;
        $id_servizio_form_postuma = 5;
        $id_ufficio_form_postuma = 6;
        $id_area_form = 12;
        $id_servizio_form = 13;
        $id_ufficio_form = 14;
        $servizi = array();
        $temp_ufficio = array();
        $temp_servizio = array();
        $string_servizio = '';
        $string_ufficio = '';

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

        if (!empty($servizio) && !empty($ufficio) && $servizio != null && $ufficio != null && $servizio != '' && $ufficio != '') {

            if (gettype($servizio) == 'string' && (gettype($ufficio) == 'string')) {
                if (strpos($servizio, '"') == true && (strpos($ufficio, '"') == true)) {
                    $string_servizio = (explode('"', $servizio)[1]);
                    $string_ufficio = (explode('"', $ufficio)[1]);
                } else {
                    $string_servizio = $servizio;
                    $string_ufficio = $ufficio;
                }


            } elseif (is_array($servizio) && is_array($ufficio)) {

                $temp_servizio = $servizio;
                $temp_ufficio = $ufficio;
            }
        }

        if (!empty($temp_servizio) && !empty($temp_ufficio) && is_array($servizio) && is_array($ufficio)) {
            foreach ($temp_servizio as $item_servizio) {
                foreach ($temp_ufficio as $item_ufficio) {

                    $stmt->bind_param("iiisisisiiisisis", $id_form_creazione_fase, $id_field_creazione_fase, $id_area_form, $area, $id_servizio_form, $item_servizio, $id_ufficio_form, $item_ufficio,
                        $id_form_fase_postuma, $id_field_fase_postuma, $id_area_form_postuma, $area, $id_servizio_form_postuma, $item_servizio, $id_ufficio_form_postuma, $item_ufficio);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $row = $result->fetch_all();
                    if ($row != null)
                        array_push($servizi, $row);

                }

            }
        } else {

            $stmt->bind_param("iiisisisiiisisis", $id_form_creazione_fase, $id_field_creazione_fase, $id_area_form, $area, $id_servizio_form, $string_servizio, $id_ufficio_form, $string_ufficio,
                $id_form_fase_postuma, $id_field_fase_postuma, $id_area_form_postuma, $area, $id_servizio_form_postuma, $string_servizio, $id_ufficio_form_postuma, $string_ufficio);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_all();

            if ($row != null)
                array_push($servizi, $row);
        }


        $mysqli->close();
        if (empty($servizi))
            return $servizi = array();
        else
            return $servizi[0];

    }


    public function findFaseOnKanboard($arrayNameSubtasks)
    {
        $conn = new Connection;
        $mysqli = $conn->connect();
        $array_ids = array();
        $sql = "SELECT  id FROM subtasks WHERE title LIKE ? ";
        $stmt = $mysqli->prepare($sql);
        for ($i = 0; $i < sizeof($arrayNameSubtasks); $i++) {

            foreach ($arrayNameSubtasks[$i] as $nameSubtask) {
                //$nameSubtask = "%$nameSubtask%";
                $nameSubtask = $nameSubtask . " - fase";
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

    public function deleteDismatchSubtaskUsers($array_ids, $userId)
    {
        $conn = new Connection;
        $mysqli = $conn->connect();
        $sql = "DELETE FROM MAPP_subtask_users WHERE subtask_id=? AND user_id=?";
        $stmt = $mysqli->prepare($sql);
        foreach ($array_ids as $id) {
            foreach ($userId as $user) {
                $stmt->bind_param("ii", $id, $user);
                $res = $stmt->execute();

            }
        }
        $mysqli->close();
    }

    public function insertMatchSubtaskUsers($array_ids, $userId)
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
        $mysqli->close();
    }

    public function findFaseByUser($username, $procedimento)
    {
        $subtask_names = array();
        $id_subtask = array();
        $conn = new Connection();
        $mysqli = $conn->connect();
        $sql = "SELECT subtask_id FROM MAPP_subtask_users WHERE user_id IN (SELECT id FROM users WHERE username=?)";
        $stmt = $mysqli->prepare($sql);
        foreach ($username as $nickname) {
            $stmt->bind_param("s", $nickname);
            $res = $stmt->execute();
            $res = $stmt->get_result();
            $result = $res->fetch_all();

            if (!empty($result))
                array_push($id_subtask, $result);

        }

        $sql = "SELECT title FROM subtasks WHERE id=? AND task_id IN (SELECT id FROM tasks WHERE title=?)";
        $stmt = $mysqli->prepare($sql);
        foreach ($id_subtask[0] as $item) {
            foreach ($item as $id) {
                $stmt->bind_param("is", $id, $procedimento);
                $res = $stmt->execute();
                $res = $stmt->get_result();
                $result = $res->fetch_all();
                if (!empty($result))
                    array_push($subtask_names, $result[0]);

            }
        }

        $mysqli->close();
        return $subtask_names;
    }

    public function findSubtaskByProcedimento($procedimento)
    {
        $conn = new Connection();
        $mysqli = $conn->connect();
        $sql = "SELECT title FROM subtasks WHERE task_id IN (SELECT id FROM tasks WHERE title=?)";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("s", $procedimento[0]);
        $res = $stmt->execute();
        $res = $stmt->get_result();
        $result = $res->fetch_all();

        $mysqli->close();
        return $result;

    }

    private function getDbTitle($title)
    {
        return $title . " - fase";
    }

    /**
     * @return mixed
     */
    public function getOldTitle()
    {
        return $this->old_title;
    }

    /**
     * @param mixed $old_title
     */
    public function setOldTitle($old_title)
    {
        $this->old_title = $old_title;
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