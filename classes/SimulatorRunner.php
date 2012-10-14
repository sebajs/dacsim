<?php

class SimulatorRunner
{
    public static function run($sHost, $iPort, $bSendResponse)
    {
        if ($sHost == '' || $iPort == '') {
            Output::log("Invalid IP or Port!");
            die();
        }

        if (($sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP)) === false) {
            Output::log("socket_create() falló: razón: " . socket_strerror(socket_last_error()));
        }

        if (socket_bind($sock, $sHost, $iPort) === false) {
            Output::log("socket_bind() falló: razón: " . socket_strerror(socket_last_error($sock)));
        }

        if (socket_listen($sock, 5) === false) {
            Output::log("socket_listen() falló: razón: " . socket_strerror(socket_last_error($sock)));
        } 
        
        $iConnecitons = 0;
        Output::log("DACSIM Listening on {$sHost}:{$iPort}");
            
        do {
            
            if (($msgsock = socket_accept($sock)) === false) {
                Output::log("socket_accept() falló: razón: " . socket_strerror(socket_last_error($sock)));
                break;
            }
            $iConnecitons++;
            Output::log("Connection #{$iConnecitons} accepted!");
            
            $iMessages = 0;

            do {
                if (false === ($buf = socket_read($msgsock, 2048, PHP_NORMAL_READ))) {
                    Output::log("socket_read() falló: razón: " . socket_strerror(socket_last_error($msgsock)));
                    break 2;
                }
                if (!$buf = trim($buf)) {
                    continue;
                }
                
                $iMessages++;
                if ($buf == 'quit') {
                    Output::log();
                    Output::log("=Connection remotely closed!");
                    break;
                }
                if ($buf == 'shutdown' || $buf == 'sd') {
                    Output::log();
                    Output::log("=Received shutdown command!");
                    socket_close($msgsock);
                    break 2;
                }
                if (substr($buf, 0, 2) == '--') {
                    Output::log();
                    Output::log("=Received management command: $buf");
                    
                    Result_Register::activate();
                    $aArgs = explode(' ', $buf);
                    Bootstrap::run($aArgs);
                    Result_Register::deactivate();
                    
                    $sResponse = implode('', Result_Register::getLines());
                    $sResponse = chr(2).$sResponse.chr(13)."\n";
                    socket_write($msgsock, $sResponse, strlen($sResponse));   
                    
                    Result_Register::clean();
                    continue;
                }

                Output::log();
                Output::log("=Received message #{$iMessages}: $buf");
                
                // Se pasa el contenido al procesador de mensajes
                $oMessage   = new Message($buf);
                $sRunResult = $oMessage->run();
                $oResponse  = $oMessage->getResponse();
                       
                if (is_object($oResponse) && $bSendResponse) {
                    Output::log("=Response to message #{$iMessages}: ", false);          
                    Output::line($oResponse->dump());
                    
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
