<?php

define('DATA_PATH', 'data/');
define('STBS_PATH', 'stb/');
define('EQUIPTYPE_PATH', 'equiptype/');
define('HEADEND_PATH', 'headend/');
define('USPLANT_PATH', 'usplant/');
define('DSPLANT_PATH', 'dsplant/');
define('CHANNELMAP_PATH', 'channelmap/');
define('SERVICE_PATH', 'service/');
define('DATA_EXTENSION', '.dat');

function dacsimAutoloader($sClassName)
{
    //class directories
    $aClassPath = array('classes/');

    //for each directory
    foreach($aClassPath as $sDir) {
        //see if the file exsists
        if(file_exists($sDir.$sClassName.'.php')) {
            require_once($sDir.$sClassName.'.php');
            return;
        }
    }
}

spl_autoload_register('dacsimAutoloader');
