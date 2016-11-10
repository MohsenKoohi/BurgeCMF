<?php
class Burge_CMF_Security extends CI_Security
{

    public function __construct()
    {
        parent::__construct();
    }

    public function csrf_show_error()
    {
        //unfortunately we will have a language change
        //some works for future versions
        $address=$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
        
        list($no,$yes)=explode(MAIN_ADDRESS."/", $address);

        header('Location: '.HOME_URL.'/retry?prev='.$yes, TRUE, 302);
        exit();
    }
}