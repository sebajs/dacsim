<?php

class SimulatorRunner
{
    public static function run($sHost, $iPort, $bSendResponse)
    {
        if ($sHost == '' || $iPort == '') {
            echo "Invalid IP or Port.\n";
            die();
        }

        if (($sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP)) === false) {
            echo "socket_create() falló: razón: " . socket_strerror(socket_last_error()) . "\n";
        }

        if (socket_bind($sock, $sHost, $iPort) === false) {
            echo "socket_bind() falló: razón: " . socket_strerror(socket_last_error($sock)) . "\n";
        }

        if (socket_listen($sock, 5) === false) {
            echo "socket_listen() falló: razón: " . socket_strerror(socket_last_error($sock)) . "\n";
        } 
        
        $iConnecitons = 0;
        echo "DACSIM Listening on {$sHost}:{$iPort}\n\n";   
            
        do {
            
            if (($msgsock = socket_accept($sock)) === false) {
                echo "socket_accept() falló: razón: " . socket_strerror(socket_last_error($sock)) . "\n";
                break;
            }
            $iConnecitons++;
            echo "Connection #{$iConnecitons} accepted!\n\n";
            
            $iMessages = 0;

            do {
                if (false === ($buf = socket_read($msgsock, 2048, PHP_NORMAL_READ))) {
                    echo "socket_read() falló: razón: " . socket_strerror(socket_last_error($msgsock)) . "\n";
                    break 2;
                }
                if (!$buf = trim($buf)) {
                    continue;
                }
                
                $iMessages++;
                if ($buf == 'quit') {
                    echo "Connection remotely closed!\n\n";
                    break;
                }
                if ($buf == 'shutdown') {
                    socket_close($msgsock);
                    break 2;
                }

                echo "\n";
                echo "=Received message #{$iMessages}: $buf\n";
                
                // Se pasa el contenido al procesador de mensajes
                $oMessage   = new Message($buf);
                $sRunResult = $oMessage->run();
                $oResponse  = $oMessage->getResponse();
                       
                if (is_object($oResponse) && $bSendResponse) {
                    echo "=Response to message #{$iMessages}: ";
                    
                    echo $oResponse->dump()."\n";
                    $oResponse->show();
                    
                    $sResponse = chr(2).$oResponse->dump().chr(13)."\n";
                    socket_write($msgsock, $sResponse, strlen($sResponse));     
                }
                  
                Message::showFooter($iMessages);
                
            } while (true);
            socket_close($msgsock);
        } while (true);

        socket_close($sock);          
    }
}
