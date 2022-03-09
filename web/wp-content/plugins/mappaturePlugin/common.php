<?php

class MappatureCommon
{
    //Returns the url of the plugin's root folder
    public static function get_base_url()
    {
        return plugins_url('', __FILE__);
    }

    //Returns the physical path of the plugin's root folder
    public static function get_base_path()
    {
        return dirname(__FILE__);
    }
    public static function get_dir_path()
    {
        return plugin_dir_path(__FILE__);
    }


}