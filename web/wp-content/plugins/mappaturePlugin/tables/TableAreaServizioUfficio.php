<?php

namespace MappaturePlugin;

class TableAreaServizioUfficio
{

    public function selectAreaForTable()
    {
        $conn = new ConnectionSarala();
        $mysqli = $conn->connect();
        $sql = "SELECT DISTINCT meta_value FROM wp_gf_entry_meta WHERE form_id=17 AND meta_key=4 OR  form_id=66 AND meta_key=1";
        $result = mysqli_query($mysqli, $sql);
        $row = $result->fetch_all();

        mysqli_close($mysqli);
        return $row;
    }

    public function selectServizioForTable()
    {
        $conn = new ConnectionSarala();
        $mysqli = $conn->connect();
        $sql = "SELECT DISTINCT meta_value FROM wp_gf_entry_meta WHERE form_id=21 AND meta_key=1 OR  form_id=66 AND meta_key=2";
        $result = mysqli_query($mysqli, $sql);
        $row = $result->fetch_all();

        mysqli_close($mysqli);
        return $row;
    }

    public function selectUfficioForTable()
    {
        $conn = new ConnectionSarala();
        $mysqli = $conn->connect();
        $sql = "SELECT DISTINCT meta_value FROM wp_gf_entry_meta WHERE form_id=20 AND meta_key=4 OR  form_id=66 AND meta_key=3";
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
        return $process;
    }
}
