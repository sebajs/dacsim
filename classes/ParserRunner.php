<?php

class ParserRunner
{
    public static function run($sStream)
    {
        echo "DACSIM Message Parser\n\n";
        
        echo "\n";
        echo "=Message: $sStream\n";
        
        // Se pasa el contenido al procesador de mensajes
        $oMessage = new Message($sStream);
            
        Message::showFooter();
    }
}
