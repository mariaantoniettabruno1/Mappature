<?php

namespace MappaturePlugin;
/**
 * Classe USer contentente function di default come getter e setter e custom function
 */
class User
{
    private $username;
    private $email;
    private $name;
    private $idKanboard;
    private $password;


    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password): void
    {
        $this->password = $password;
    }


    public function setIdKanboard($idKanboard)
    {
        $this->idKanboard = (int)$idKanboard;
    }

    public function getIdKanboard()
    {
        return $this->idKanboard;
    }

    public function setUsername($username)
    {
        $this->username = $username;
    }

    public function getUsername()
    {

        return $this->username;

    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }


    public function getEmail()
    {
        return $this->email;

    }

    /**
     * Function per aggiornare nome, email e password di un utente nella table 'users' del db di Kanboard
     * input:
     * output:
     */

    public function updateUser()
    {
        $conn = new Connection();
        $mysqli = $conn->connect();
        $sql = "UPDATE users SET name=?, email=?, password=? WHERE id=?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("sssi", $this->name, $this->email, $this->password, $this->idKanboard);
        $res = $stmt->execute();

        $mysqli->close();
    }

    /**
     * Function per creare un nuovo utente su kanboard, dopo averlo creato su wordpress, salvando i dati nel db di Kanboard
     * in particolare nella table 'users'
     * Successiva select per ottenere l'id dell'utente in Kanboard
     * Set della variabile $idKanboard
     * input:
     * output:
     */

    public function createUser()
    {
        $conn = new Connection();
        $mysqli = $conn->connect();
        $sql = "INSERT INTO users (username,name,email,password) VALUES(?,?,?,?)";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("ssss", $this->username, $this->name, $this->email, $this->password);
        $res = $stmt->execute();
        $sql = "SELECT id FROM users WHERE username=? ORDER BY id DESC LIMIT 1";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("s", $this->username);
        $res = $stmt->execute();
        $res = $stmt->get_result();
        $result = $res->fetch_assoc();
        $this->setIdKanboard($result['id']);
        $mysqli->close();
    }

    /**
     * Function per cancellare un utente da Kanboard, dopo averlo cancellato su WordPress, attuando una delete sulla table 'users'
     * input: id kanboard dell'utente
     * output:
     */

    public function deleteUser($id_kan)
    {
        $conn = new Connection();
        $mysqli = $conn->connect();
        $this->setIdKanboard($id_kan);
        $sql = "DELETE FROM users WHERE id=?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("i", $this->idKanboard);
        $res = $stmt->execute();
        $mysqli->close();
    }

    /**
     * Function che seleziona i dirigenti assegnati ad una specifica area dal db di WordPress, in particolare dalla
     * tabella di meta utente di WordPress.
     * input: area collegata al dirigente
     * output: array contenente tutti i nomi dei dirigenti assegnati ad una specifica area
     */

    public function selectDirigente($area)
    {
        $conn = new ConnectionSarala();
        $mysqli = $conn->connect();
        $sql = "SELECT meta_value FROM wp_usermeta WHERE meta_key ='nickname'
                                      AND user_id IN (SELECT user_id FROM wp_usermeta WHERE meta_value='Dirigente')
                                      AND user_id IN (SELECT user_id FROM wp_usermeta WHERE meta_key='area' AND meta_value=?)";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("s", $area);
        $res = $stmt->execute();
        $res = $stmt->get_result();
        $rows = $res->fetch_all();
        $mysqli->close();
        $vettore = array();
        foreach ($rows as $row) {
            array_push($vettore, $row[0]);
        }
        return $vettore;
    }

    /**
     * Function che seleziona i dirigenti assegnati ad uno specifico processo dal db di Kanboard, in particolare dalla tabella
     * 'users' del db di Kanboard, prendendo l'id dell'user dalla tabella custom 'MAPP_project_users_owner' e il project_id
     * dalla tabella projects
     * input: nome del processo (project)
     * output: array contenente tutti i nomi dei dirigenti di uno specifico processo
     */

    public function findDirigenteByProcesso($processo)
    {
        $conn = new Connection();
        $mysqli = $conn->connect();
        $sql = "SELECT username FROM users WHERE id IN(SELECT user_id FROM MAPP_project_users_owner WHERE project_id IN (SELECT id FROM projects WHERE name=?))";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("s", $processo[0]);
        $res = $stmt->execute();
        $res = $stmt->get_result();
        $rows = $res->fetch_all();
        $mysqli->close();
        return $rows;

    }

    /**
     * Function che seleziona i PO assegnati ad uno specifico procedimento dal db di Kanboard, in particolare dalla tabella
     * 'users' del db di Kanboard, prendendo l'id dell'user dalla tabella custom 'MAPP_task_users_owner' e il task_id
     * dalla tabella tasks
     * input: nome del procedimento (task)
     * output: array contenente tutti i nomi dei PO di uno specifico procedimento
     */
    public function findPOByProcedimento($procedimento)
    {
        $conn = new Connection();
        $mysqli = $conn->connect();
        $sql = "SELECT username FROM users WHERE id IN(SELECT user_id FROM MAPP_task_users_owner WHERE task_id IN (SELECT id FROM tasks WHERE title=?))";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("s", $procedimento[0]);
        $res = $stmt->execute();
        $res = $stmt->get_result();
        $rows = $res->fetch_all();

        $mysqli->close();
        return $rows;
    }

    /**
     * Function che seleziona i Dipendenti assegnati ad uno specifico procedimento dal db di Kanboard, in particolare dalla tabella
     * 'users' del db di Kanboard, prendendo l'id dell'user dalla tabella custom 'MAPP_task_users' e il task_id
     * dalla tabella tasks
     * input: nome del procedimento (task)
     * output: array contenente tutti i nomi dei Dipendenti assegnati ad uno specifico procedimento
     */

    public function findDipendentiAssegnatiAProcedimento($procedimento)
    {
        $conn = new Connection();
        $mysqli = $conn->connect();
        $sql = "SELECT username FROM users WHERE id IN(SELECT user_id FROM MAPP_task_users WHERE task_id IN (SELECT id FROM tasks WHERE title=?))";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("s", $procedimento[0]);
        $res = $stmt->execute();
        $res = $stmt->get_result();
        $rows = $res->fetch_all();

        $mysqli->close();
        return $rows;

    }

    /**
     * Function che seleziona i PO assegnati ad uno specifico servizio collegato ad una specifica area dal db di WordPress,
     * in particolare dalla table di usermeta di WordPRess
     * input: nome dell'area e nome del servizio associati
     * output: array contenente tutti i nomi dei PO assegnati ad uno specifico servizio collegato ad una specifica area
     */

    public function selectPO($area, $servizio)
    {
        $conn = new ConnectionSarala();
        $mysqli = $conn->connect();
        $po_names = array();
        $sql = "SELECT meta_value FROM wp_usermeta WHERE meta_key ='nickname'
                                      AND user_id IN (SELECT user_id FROM wp_usermeta WHERE meta_value='PO')
                                      AND user_id IN (SELECT user_id FROM wp_usermeta WHERE meta_key= 'area' AND meta_value=?)
                                      AND user_id IN (SELECT user_id FROM wp_usermeta WHERE meta_key='servizio' AND meta_value LIKE ?)";
        $stmt = $mysqli->prepare($sql);
        $temp = "%$servizio%";
        $stmt->bind_param("ss", $area[0], $temp);
        $res = $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_all();
        foreach ($row as $item) {
            array_push($po_names, $item[0]);
        }
        $mysqli->close();
        return $po_names;
    }

    /**
     * Function che seleziona i Dipendenti assegnati ad uno specifico ufficio collegato ad una specifica area e servizio dal db di WordPress,
     * in particolare dalla table di usermeta di WordPRess
     * input: nome dell'area, nome del servizio e nome dell'ufficio associati
     * output: array contenente tutti i nomi dei PO assegnati ad uno specifico ufficio collegato ad una specifico servizio e area
     */

    public function selectDipendente($area, $servizio, $ufficio)
    {
        $servizio_user = "%$servizio%";
        $ufficio_user = "%$ufficio%";
        $conn = new ConnectionSarala();
        $mysqli = $conn->connect();
        $sql = "SELECT meta_value FROM wp_usermeta WHERE meta_key ='nickname'
                                      AND user_id IN (SELECT user_id FROM wp_usermeta WHERE meta_value='Dipendente')
                                      AND user_id IN (SELECT user_id FROM wp_usermeta WHERE meta_value=?)
                                      AND user_id IN (SELECT user_id FROM wp_usermeta WHERE meta_value LIKE ?)
                                      AND user_id IN (SELECT user_id FROM wp_usermeta WHERE meta_value LIKE ?)";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("sss", $area, $servizio_user, $ufficio_user);
        $res = $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_all();
        $mysqli->close();
        return $row;
    }

    /**
     * Function che seleziona i Dipendenti assegnati ad uno specifica fase/attività in particolare dalla tabella
     * 'users' del db di Kanboard, prendendo l'id dell'user dalla tabella custom 'MAPP_subtask_users' e il subtask_id
     * dalla tabella subtasks
     * input: nome della fase/attività (subtask)
     * output: array contenente tutti i nomi dei Dipendenti assegnati ad una specifica fase/attività
     */

    public function selectDipendenteFaseAttivita($fasi_attivita)
    {

        $conn = new Connection();
        $mysqli = $conn->connect();
        $sql = "SELECT username FROM users WHERE id IN(SELECT user_id FROM MAPP_subtask_users WHERE subtask_id IN (SELECT id FROM subtasks WHERE title=?))";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("s", $fasi_attivita[0]);
        $res = $stmt->execute();
        $res = $stmt->get_result();
        $rows = $res->fetch_all();

        $mysqli->close();
        return $rows;
    }
}