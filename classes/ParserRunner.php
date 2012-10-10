<?php

class ParserRunner
{
    public static function run($sStream)
    {
        echo "DACSIM Message Parser\n\n";
        
        echo "\n";
        echo "=Message: {$sStream}\n";
        
        // Se pasa el contenido al procesador de mensajes
        $oMessage = new Message($sStream);
            
        Message::showFooter();
    }
    
    public static function checksum($sStream)
    {
        $sFinalStream = $sStream;
        echo "DACSIM Checksum Generator\n\n";
        
        $sStream = str_replace(' ', '', $sStream);
        $sStream = str_replace('.', '', $sStream);
        
        $iSize   = (strlen($sStream)+4)/2;  // Adds 4 nibbles for the size header length and 2 for the checksum
        $sStream = strtoupper(str_pad(dechex($iSize), 4, '0', STR_PAD_LEFT).$sStream);
        
        $sChecksum = MessageHeader::generateChecksum($sStream);
            
        echo "=Size:     ".substr($sStream, 0, 4)." ({$iSize})\n";
        echo "=Checksum: {$sChecksum}\n";
        echo "=Message:  {$sStream}{$sChecksum}\n";
    }    
}
