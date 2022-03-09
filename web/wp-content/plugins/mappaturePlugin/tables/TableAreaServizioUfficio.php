<?php

namespace MappaturePlugin;

class TableAreaServizioUfficio
{

    public function selectAreaForTable()
    {
        $conn = new ConnectionSarala();
        $mysqli = $conn->connect();
        $sql = "SELECT DISTINCT meta_value FROM wp_gf_entry_meta WHERE form_id=76 AND meta_key=4 OR  form_id=105 AND meta_key=1";
        $result = mysqli_query($mysqli, $sql);
        $row = $result->fetch_all();

        mysqli_close($mysqli);
        return $row;
    }

    public function selectServizioForTable()
    {
        $conn = new ConnectionSarala();
        $mysqli = $conn->connect();
        $sql = "SELECT DISTINCT meta_value FROM wp_gf_entry_meta WHERE form_id=78 AND meta_key=1 OR  form_id=105 AND meta_key=2";
        $result = mysqli_query($mysqli, $sql);
        $row = $result->fetch_all();

        mysqli_close($mysqli);
        return $row;
    }

    public function selectUfficioForTable()
    {
        $conn = new ConnectionSarala();
        $mysqli = $conn->connect();
        $sql = "SELECT DISTINCT meta_value FROM wp_gf_entry_meta WHERE form_id=77 AND meta_key=4 OR  form_id=105 AND meta_key=3";
        $result = mysqli_query($mysqli, $sql);
        $row = $result->fetch_all();
        mysqli_close($mysqli);
        return $row;
    }

    public function getProcesso($area, $servizio, $ufficio)
    {
        $conn = new ConnectionSarala();
        $mysqli = $conn->connect();

        $sql = "SELECT m9.meta_value FROM wp_gf_entry_meta AS m1
                  JOIN wp_gf_entry_meta AS m2 ON m1.entry_id = m2.entry_id
                  JOIN wp_gf_entry_meta AS m3 ON m1.entry_id = m3.entry_id
                  JOIN wp_gf_entry_meta AS m9 ON m1.entry_id = m9.entry_id
WHERE (m1.meta_key = 2 AND m1.meta_value =?)
  AND (m2.meta_key = 3 AND m2.meta_value =?)
  AND (m3.meta_key = 4 AND m3.meta_value =?)
  AND (m9.meta_key LIKE '9.%')";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("sss", $area,$servizio,$ufficio);
        $res = $stmt->execute();
        $res = $stmt->get_result();
        $process = $res->fetch_all();

        $sql = "SELECT m9.meta_value FROM wp_gf_entry_meta AS m1
                  JOIN wp_gf_entry_meta AS m2 ON m1.entry_id = m2.entry_id
                  JOIN wp_gf_entry_meta AS m3 ON m1.entry_id = m3.entry_id
                  JOIN wp_gf_entry_meta AS m9 ON m1.entry_id = m9.entry_id
WHERE (m1.meta_key = 2 AND m1.meta_value =?)
  AND (m2.meta_key = 3 AND m2.meta_value =?)
  AND (m3.meta_key = 4 AND m3.meta_value =?)
  AND (m9.meta_key= 8)";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("sss", $area,$servizio,$ufficio);
        $res = $stmt->execute();
        $res = $stmt->get_result();
        $process_modified = $res->fetch_all();
        $process = [...$process,...$process_modified];
        $mysqli->close();
        return $process;
    }
    public function checkProcessoOnKanboard($array_processi){
        $conn = new Connection();
        $mysqli = $conn->connect();
        $process = array();
        $sql = "SELECT name FROM projects WHERE name=?";
        $stmt = $mysqli->prepare($sql);
        foreach ($array_processi as $proc){
            $stmt->bind_param("s", $proc[0]);
            $res = $stmt->execute();
            $res = $stmt->get_result();
            $rows = $res->fetch_all();
            if (!empty($rows)) {
                foreach ($rows as $row) {
                    array_push($process, $row[0]);
                }

            }
        }

        $mysqli->close();
        return $process;
    }

}
