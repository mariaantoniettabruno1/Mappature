<?php

namespace MappaturePlugin;

class Procedimento
{
    private $id_procedure;
    private $title;
    private $description;
    private $date_completed;
    private $id_form;
    private $id_process;
    private $column_id;
    private $swimlane_id;
    private $name_process;
    private $creator_id;
    private $date_created;
    private $date_updated;
    private $position;
    private $old_title;
    private $owner_id;
    private $users;
    private $id_procedure_for_users;

    public function __construct()
    {
        $this->users = [];
        $this->id_procedure_for_users = [];
    }

    /**
     * @return mixed
     */
    public function getOwnerId()
    {
        return $this->owner_id;
    }

    /**
     * @param mixed $owner_id
     */
    public function setOwnerId($owner_id): void
    {
        $this->owner_id = $owner_id;
    }

    /**
     * @return array
     */
    public function getIdProcedureForUsers(): array
    {
        return $this->id_procedure_for_users;
    }

    /**
     * @param array $id_procedure_for_users
     */
    public function setIdProcedureForUsers(array $id_procedure_for_users): void
    {
        $this->id_procedure_for_users = $id_procedure_for_users;
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


    public function setIdProcedure($id_procedure)
    {
        $this->id_procedure = $id_procedure;
    }


    public function getIdProcedure()
    {
        return $this->id_procedure;
    }


    public function setIdTask($id_procedure)
    {
        $this->id_procedure = $id_procedure;
    }


    public function getTitle()
    {
        return $this->title;
    }


    public function setTitle($title)
    {
        $this->title = $title;
    }


    public function getDescription()
    {
        return $this->description;
    }


    public function setDescription($description)
    {
        $this->description = $description;
    }


    public function getDateCompleted()
    {
        return $this->date_completed;
    }


    public function setDateCompleted($date_completed)
    {
        $this->date_completed = $date_completed;
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

    public function getNameProcess()
    {
        return $this->name_process;
    }

    public function setNameProcess($name_process)
    {
        $this->name_process = $name_process;
    }

    public function getSwimlaneId()
    {
        return $this->swimlane_id;
    }

    public function setSwimlaneId($swimlane_id)
    {
        $this->swimlane_id = $swimlane_id;
    }


    public function getColumnId()
    {
        return $this->column_id;
    }


    public function setColumnId($column_id)
    {
        $this->column_id = $column_id;
    }


    public function getCreatorId()
    {
        return $this->creator_id;
    }


    public function setCreatorId($creator_id)
    {
        $this->creator_id = $creator_id;
    }

    public function getDateCreated()
    {
        return $this->date_created;
    }


    public function setDateCreated($date_created)
    {
        $this->date_created = $date_created;
    }


    public function getDateUpdated()
    {
        return $this->date_updated;
    }


    public function setDateUpdated($date_updated)
    {
        $this->date_updated = $date_updated;
    }


    public function getPosition()
    {
        return $this->position;
    }


    public function setPosition($position)
    {
        $this->position = $position;
    }


    public function insertDataProcedureSarala()
    {
        $conn = new ConnectionSarala();
        $mysqli = $conn->connect();
        $sql = "INSERT INTO MAPP_procedimenti (id_form,id_procedimento,id_processo) VALUES(?,?,?)";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("iii", $this->id_form, $this->id_procedure, $this->id_process);
        $res = $stmt->execute();
        $mysqli->close();
    }

    public function createProcedure()
    {
        $conn = new Connection();
        $mysqli = $conn->connect();
        $sql = "SELECT id FROM projects WHERE name=? ORDER BY id DESC LIMIT 1";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("s", $this->name_process);
        $res = $stmt->execute();
        $res = $stmt->get_result();
        $result = $res->fetch_assoc();
        $this->setIdProcess($result['id']);


        $sql = "SELECT id FROM swimlanes WHERE project_id=?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("i", $this->id_process);
        $res = $stmt->execute();
        $res = $stmt->get_result();
        $result = $res->fetch_assoc();
        $this->setSwimlaneId($result['id']);

        $sql = "SELECT id FROM columns WHERE project_id=?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("i", $this->id_process);
        $res = $stmt->execute();
        $res = $stmt->get_result();
        $result = $res->fetch_assoc();
        $this->setColumnId($result['id']);

        $sql = "INSERT INTO tasks (title,project_id,column_id,swimlane_id,position,date_creation,date_modification) VALUES(?,?,?,?,?,?,?)";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("siiiiii", $this->title, $this->id_process, $this->column_id, $this->swimlane_id, $this->position, $this->date_created, $this->date_updated);
        $res = $stmt->execute();

        $sql = "SELECT id FROM tasks WHERE title=?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("s", $this->title);
        $res = $stmt->execute();
        $res = $stmt->get_result();
        $procedimento = $res->fetch_assoc();
        $this->setIdProcedure($procedimento['id']);

        $sql = "INSERT INTO project_daily_column_stats (project_id,column_id) VALUES(?,?)";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("ii", $this->id_process, $this->column_id);
        $res = $stmt->execute();

        $sql = "INSERT INTO project_daily_stats (project_id) VALUES(?)";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("i", $this->id_process);
        $res = $stmt->execute();

        $this->insertDataProcedureSarala();
        $mysqli->close();

    }


    public function deleteProcedure()
    {
        $conn = new Connection();
        $mysqli = $conn->connect();
        $sql = "DELETE  FROM tasks WHERE title=? ORDER BY id DESC LIMIT 1";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("s", $this->title);
        $res = $stmt->execute();
        $mysqli->close();
    }

    public function findTask()
    {
        $conn = new Connection();
        $mysqli = $conn->connect();
        $sql = "SELECT id FROM tasks WHERE title=?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("s", $this->title);
        $res = $stmt->execute();
        $res = $stmt->get_result();
        $result = $res->fetch_assoc();
        $this->setIdProcedure($result['id']);
        $mysqli->close();
    }


    public function assegnaDipendenti()
    {
        $conn = new Connection();
        $mysqli = $conn->connect();
        $sql = "INSERT INTO MAPP_task_users (task_id,user_id) VALUES(?,?)";
        $stmt = $mysqli->prepare($sql);
        foreach ($this->users as $userId) {
            $stmt->bind_param("ii", $this->id_procedure, $userId);
            $res = $stmt->execute();
        }
        $mysqli->close();
    }

    public function assignUsersOwner($users)
    {
        $conn = new Connection();
        $mysqli = $conn->connect();
        $sql = "INSERT INTO MAPP_task_users_owner (task_id,user_id) VALUES(?,?)";
        $stmt = $mysqli->prepare($sql);
        for ($i = 0; $i < sizeof($users); $i++) {
            foreach ($users[$i] as $userId) {
                $stmt->bind_param("ii", $this->id_procedure, $userId);
                $res = $stmt->execute();

            }
        }
        $mysqli->close();
    }

    public function assignUsersCreator($users)
    {
        $conn = new Connection();
        $mysqli = $conn->connect();

        $sql = "INSERT INTO MAPP_task_users_creator (task_id,user_id) VALUES(?,?)";
        $stmt = $mysqli->prepare($sql);
        for ($i = 0; $i < sizeof($users); $i++) {
            foreach ($users[$i] as $userId) {
                $stmt->bind_param("ii", $this->id_procedure, $userId);
                $res = $stmt->execute();

            }
        }
        $mysqli->close();
    }

    public function modificaAssegnazioneDipendenti()
    {
        $conn = new Connection();
        $mysqli = $conn->connect();
        $sql = "DELETE FROM MAPP_task_users WHERE task_id=?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("i", $this->id_procedure);
        $res = $stmt->execute();
        $sql = "INSERT INTO MAPP_task_users  (user_id,task_id) VALUES (?,?)";
        $stmt = $mysqli->prepare($sql);
        foreach ($this->users as $userId) {
            $stmt->bind_param("ii", $userId, $this->id_procedure);
            $res = $stmt->execute();
        }
        $mysqli->close();
    }

    public function findTaskOnWordpress($area, $servizio)
    {
        $conn = new ConnectionSarala();
        $mysqli = $conn->connect();
        $id_field_creazione_procedimento = 2;
        $id_form_creazione_procedimento = 2;
        $id_form_procedimento_csv = 50;
        $id_field_procedimento_csv = "%22.%";
        $id_area_form = 18;
        $id_servizio_form = 19;
        $res_task = array();
        $string_servizio = '';
        $temp_servizio = array();


        $sql = "SELECT meta_value FROM wp_gf_entry_meta WHERE form_id=? AND meta_key=? AND
                                              entry_id IN ( SELECT  entry_id FROM wp_gf_entry_meta WHERE meta_key=? AND meta_value=?) AND 
                                              entry_id IN (SELECT  entry_id FROM wp_gf_entry_meta WHERE meta_key=? AND meta_value=? )
                                           OR 
                                              form_id=? AND meta_key LIKE ? AND
                                                  entry_id IN (SELECT  entry_id FROM wp_gf_entry_meta WHERE meta_key=? AND meta_value=?) AND 
                                              entry_id IN ( SELECT  entry_id FROM wp_gf_entry_meta WHERE meta_key=? AND meta_value=?)";
        $stmt = $mysqli->prepare($sql);
        if (!empty($servizio) && $servizio != null && $servizio != '') {

            if (gettype($servizio) == 'string') {
                if (strpos($servizio, '"') == true)
                    $string_servizio = (explode('"', $servizio)[1]);
                else
                    $string_servizio = $servizio;


            } elseif (is_array($servizio)) {

                $temp_servizio = $servizio;
            }
        }

        if (is_array($temp_servizio) && !empty($temp_servizio)) {
            foreach ($temp_servizio as $item) {
                $stmt->bind_param("iiisisisisis", $id_form_creazione_procedimento, $id_field_creazione_procedimento, $id_area_form, $area, $id_servizio_form, $item,
                    $id_form_procedimento_csv, $id_field_procedimento_csv, $id_area_form, $area, $id_servizio_form, $item);
                $stmt->execute();
                $result = $stmt->get_result();
                $row = $result->fetch_all();
                if ($row != null)
                    array_push($res_task, $row);
            }
        } else {
            $stmt->bind_param("iiisisisisis", $id_form_creazione_procedimento, $id_field_creazione_procedimento, $id_area_form, $area, $id_servizio_form, $string_servizio,
                $id_form_procedimento_csv, $id_field_procedimento_csv, $id_area_form, $area, $id_servizio_form, $string_servizio);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_all();
            if ($row != null)
                array_push($res_task, $row);
        }

        $mysqli->close();
        if (empty($res_task))
            return $res_task = array();
        else
            return $res_task[0];

    }

    public function findTaskOnWordpressForDipendenti($area, $servizio, $ufficio)
    {

        $conn = new ConnectionSarala();
        $mysqli = $conn->connect();
        $id_field_creazione_procedimento = 2;
        $id_form_creazione_procedimento = 2;
        $id_form_procedimento_csv = 50;
        $id_field_procedimento_csv = "%22.%";
        $id_area_form = 18;
        $id_servizio_form = 19;
        $id_ufficio_form = 20;
        $res_task = array();
        $string_servizio = '';
        $temp_servizio = array();


        $sql = "SELECT meta_value FROM wp_gf_entry_meta WHERE form_id=? AND meta_key=? AND
                                              entry_id IN ( SELECT  entry_id FROM wp_gf_entry_meta WHERE meta_key=? AND meta_value=?) AND 
                                              entry_id IN (SELECT  entry_id FROM wp_gf_entry_meta WHERE meta_key=? AND meta_value=? ) AND 
                                              entry_id IN (SELECT  entry_id FROM wp_gf_entry_meta WHERE meta_key=? AND meta_value=? )
                                           OR 
                                              form_id=? AND meta_key LIKE ? AND
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

                    $stmt->bind_param("iiisisisisisisis", $id_form_creazione_procedimento, $id_field_creazione_procedimento, $id_area_form, $area, $id_servizio_form, $item_servizio, $id_ufficio_form, $item_ufficio,
                        $id_form_procedimento_csv, $id_field_procedimento_csv, $id_area_form, $area, $id_servizio_form, $item_servizio, $id_ufficio_form, $item_ufficio);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $row = $result->fetch_all();
                    if ($row != null)
                        array_push($res_task, $row);

                }

            }
        } else {

            $stmt->bind_param("iiisisisisisisis", $id_form_creazione_procedimento, $id_field_creazione_procedimento, $id_area_form, $area, $id_servizio_form, $string_servizio, $id_ufficio_form, $string_ufficio,
                $id_form_procedimento_csv, $id_field_procedimento_csv, $id_area_form, $area, $id_servizio_form, $string_servizio, $id_ufficio_form, $string_ufficio);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_all();

            if ($row != null)
                array_push($res_task, $row);
        }


        $mysqli->close();
        if (empty($res_task))
            return $res_task = array();
        else
            return $res_task[0];

    }

    public function findTasksOnKanboard($arrayNameTasks)
    {

        $conn = new Connection;
        $mysqli = $conn->connect();
        $array_ids = array();
        $sql = "SELECT id FROM tasks WHERE title=? ";
        $stmt = $mysqli->prepare($sql);

        for ($i = 0; $i < sizeof($arrayNameTasks); $i++) {
            foreach ($arrayNameTasks[$i] as $nameTask) {
                $stmt->bind_param("s", $nameTask);
                $res = $stmt->execute();
                $result = $stmt->get_result();
                $row = $result->fetch_all();
                if ($row != null) array_push($array_ids, $row[0][0]);
            }
        }

        $mysqli->close();

        return $array_ids;
    }

    public function deleteDismatchTasksOwner($array_ids, $userId)
    {
        $conn = new Connection;
        $mysqli = $conn->connect();
        $sql = "DELETE  FROM MAPP_task_users_owner WHERE task_id=? AND user_id=?";
        $stmt = $mysqli->prepare($sql);
        foreach ($array_ids as $id) {
            foreach ($userId as $user) {
                $stmt->bind_param("ii", $id, $user);
                $res = $stmt->execute();
            }
        }
        $mysqli->close();
    }

    public function insertMatchTasksOwner($array_ids, $userId)
    {
        $conn = new Connection;
        $mysqli = $conn->connect();

        $sql = "INSERT INTO MAPP_task_users_owner (task_id,user_id) VALUES (?,?)";
        $stmt = $mysqli->prepare($sql);
        foreach ($array_ids as $id) {
            foreach ($userId as $user) {
                $stmt->bind_param("ii", $id, $user);
                $res = $stmt->execute();
            }

        }

        $mysqli->close();
    }

    public function deleteDismatchTasksCreator($array_ids, $userId)
    {
        $conn = new Connection;
        $mysqli = $conn->connect();
        $sql = "DELETE  FROM MAPP_task_users_creator WHERE task_id=? AND user_id=?";
        $stmt = $mysqli->prepare($sql);
        foreach ($array_ids as $id) {
            foreach ($userId as $user) {
                $stmt->bind_param("ii", $id, $user);
                $res = $stmt->execute();

            }
        }
        $mysqli->close();
    }

    public function insertMatchTasksCreator($array_ids, $userId)
    {
        $conn = new Connection;
        $mysqli = $conn->connect();

        $sql = "INSERT INTO MAPP_task_users_creator (task_id,user_id) VALUES (?,?)";
        $stmt = $mysqli->prepare($sql);
        foreach ($array_ids as $id) {
            foreach ($userId as $user) {

                $stmt->bind_param("ii", $id, $user);
                $res = $stmt->execute();
            }

        }
        $mysqli->close();
    }

    public function deleteDismatchTasksUsers($array_ids, $userId)
    {

        $conn = new Connection;
        $mysqli = $conn->connect();
        $sql = "DELETE  FROM MAPP_task_users WHERE task_id=? AND user_id=?";
        $stmt = $mysqli->prepare($sql);
        foreach ($array_ids as $id) {
            foreach ($userId as $user) {
                $stmt->bind_param("ii", $id, $user);
                $res = $stmt->execute();
            }
        }
        $mysqli->close();
    }

    public function insertMatchTasksUsers($array_ids, $userId)
    {

        $conn = new Connection;
        $mysqli = $conn->connect();

        $sql = "INSERT INTO MAPP_task_users (task_id,user_id) VALUES (?,?)";
        $stmt = $mysqli->prepare($sql);
        foreach ($array_ids as $id) {
            foreach ($userId as $user) {

                $stmt->bind_param("ii", $id, $user);
                $res = $stmt->execute();
            }

        }
        $mysqli->close();
    }

    public function findTaskByProcesso($processo)
    {
        $conn = new Connection();
        $mysqli = $conn->connect();
        $sql = "SELECT title FROM tasks WHERE project_id IN (SELECT id FROM projects WHERE name=?)";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("s", $processo[0]);
        $res = $stmt->execute();
        $res = $stmt->get_result();
        $result = $res->fetch_all();

        $mysqli->close();
        return $result;
    }

    /**
     * @return mixed
     */
    public function getOldTitle()
    {
        return $this->old_title;
    }

    /**
     * @param mixed $oldTitle
     */
    public function setOldTitle($oldTitle)
    {
        $this->old_title = $oldTitle;
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