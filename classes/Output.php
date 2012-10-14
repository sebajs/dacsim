<?php

class Output
{
    public static function log($sMsg='', $bNewLine=true)
    {
        $sLine = date('M d H:i:s :: ');
        $sLine .= $sMsg;        
        
        if ($bNewLine) {
            $sLine .= "\n";
        }
        
        echo $sLine;
        if (Result_Register::isActive()) {
            Result_Register::add($sLine);
        }
    }
    
    public static function line($sMsg='')
    {
        $sLine = $sMsg."\n";
        
        echo $sLine;
        if (Result_Register::isActive()) {
            Result_Register::add($sLine);
        }
    }
}
