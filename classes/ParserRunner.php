<?php

class ParserRunner
{
    public static function run($sStream)
    {
        Output::line("DACSIM Message Parser");
        Output::line();
        
        Output::line("=Message: {$sStream}");
        
        // Se pasa el contenido al procesador de mensajes
        $oMessage = new Message($sStream);
            
        Message::showFooter();
    }
    
    public static function checksum($sStream)
    {
        $sFinalStream = $sStream;
        Output::line("DACSIM Checksum Generator");
        Output::line();
        
        $sStream = str_replace(' ', '', $sStream);
        $sStream = str_replace('.', '', $sStream);
        
        $iSize   = (strlen($sStream)+4)/2;  // Adds 4 nibbles for the size header length and 2 for the checksum
        $sStream = strtoupper(str_pad(dechex($iSize), 4, '0', STR_PAD_LEFT).$sStream);
        
        $sChecksum = MessageHeader::generateChecksum($sStream);
            
        Output::line("=Size:     ".substr($sStream, 0, 4)." ({$iSize})");
        Output::line("=Checksum: {$sChecksum}");
        Output::line("=Message:  {$sStream}{$sChecksum}");
    }    
}
