<?php


class OrgChartProcess
{
    private $data;

    public function __construct() {
        $processi = GFAPI::get_entries(1);
        $procedimenti = GFAPI::get_entries(2);
        $fasi = GFAPI::get_entries(23);
        $atti = GFAPI::get_entries(24);

        $this->data = array();
        foreach ($processi as $key => $value){
            $this->data[$value[1]] = array();
        }
        foreach ($procedimenti as $key => $value){
            //$this->data[$value[2]][$value[1]] = array();
            $this->data[$value[11]][$value[2]]["fasi"] = array();
            $this->data[$value[11]][$value[2]]["atti"] = array();
        }
        foreach ($fasi as $key => $value){
            array_push($this->data[$value[2]][$value[3]]["fasi"], $value[1]);
        }
        foreach ($atti as $key => $value){
            array_push($this->data[$value[2]][$value[3]]["atti"], $value[1]);
        }

    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

}