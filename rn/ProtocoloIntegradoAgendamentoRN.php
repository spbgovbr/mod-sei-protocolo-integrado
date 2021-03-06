<?php
	
require_once dirname(__FILE__).'/../../../../SEI.php';

class ProtocoloIntegradoAgendamentoRN extends InfraRN {

    public function __construct() {
        parent::__construct();
    }
		
    protected function inicializarObjInfraIBanco() {
        return BancoSEI::getInstance();
    }
		
	public function publicarProtocoloIntegrado() {
        
	    try {
  
            ini_set('max_execution_time','0');
            ini_set('memory_limit','-1');
            
            InfraDebug::getInstance()->setBolLigado(true);
            InfraDebug::getInstance()->setBolDebugInfra(false);
            InfraDebug::getInstance()->setBolEcho(false);
            InfraDebug::getInstance()->limpar();
            
            SessaoSEI::getInstance(false)->simularLogin(SessaoSEI::$USUARIO_SEI, SessaoSEI::$UNIDADE_TESTE);
            $numSeg = InfraUtil::verificarTempoProcessamento();
            
            InfraDebug::getInstance()->gravar('Inicializando Publica��es no Protocolo Integrado');
            $objProtocoloIntegradoMonitoramento = new ProtocoloIntegradoMonitoramentoProcessosDTO();	
            $objProtocoloRN = new ProtocoloIntegradoMonitoramentoProcessosRN();
            try {		  	
                $objProtocoloRN->publicarProcessos($objProtocoloIntegradoMonitoramento);
            } catch (Exception $e) {
                throw new InfraException('Erro ao executar publica��o de protocolos.',$e);
            }
            
            $numSeg = InfraUtil::verificarTempoProcessamento($numSeg);
            InfraDebug::getInstance()->gravar('TEMPO TOTAL DE EXECUCAO: '.$numSeg.' s');
            InfraDebug::getInstance()->gravar('FIM');
            
            LogSEI::getInstance()->gravar(InfraDebug::getInstance()->getStrDebug());
            InfraDebug::getInstance()->limpar();
		  
        } catch(Exception $e) {
            InfraDebug::getInstance()->setBolLigado(false);
            InfraDebug::getInstance()->setBolDebugInfra(false);
            InfraDebug::getInstance()->setBolEcho(false);
            
            InfraDebug::getInstance()->limpar();
            throw new InfraException('Erro ao publicar Metadados e Opera��es dos Processos no Protocolo Integrado.',$e);
        }
    
    }

    public function notificarNovosPacotesNaoSendoGerados() {
        
        try {
        
            ini_set('max_execution_time','0');
            ini_set('memory_limit','-1');
            
            InfraDebug::getInstance()->setBolLigado(true);
            InfraDebug::getInstance()->setBolDebugInfra(false);
            InfraDebug::getInstance()->setBolEcho(false);
            InfraDebug::getInstance()->limpar();
            $numSeg = InfraUtil::verificarTempoProcessamento();
            InfraDebug::getInstance()->gravar('Inicializando Notifica��es de Novos Pacotes N�o sendo gerados para enviar para oo Protocolo Integrado');
            
            $objProtocoloRN = new ProtocoloIntegradoMonitoramentoProcessosRN();
            $objProtocoloRN->notificarPacotesSemEnvio(); 
            
            $numSeg = InfraUtil::verificarTempoProcessamento($numSeg);
            InfraDebug::getInstance()->gravar('TEMPO TOTAL DE EXECUCAO: '.$numSeg.' s');
            InfraDebug::getInstance()->gravar('FIM');
            LogSEI::getInstance()->gravar(InfraDebug::getInstance()->getStrDebug());
            InfraDebug::getInstance()->limpar();
        
        } catch(Exception $e) {
            InfraDebug::getInstance()->setBolLigado(false);
            InfraDebug::getInstance()->setBolDebugInfra(false);
            InfraDebug::getInstance()->setBolEcho(false);
              
            InfraDebug::getInstance()->limpar();
            throw new InfraException('Erro ao publicar Metadados e Opera��es dos Processos no Protocolo Integrado.',$e);
        }
    	
    }

    public function notificarProcessosComFalhaPublicacaoProtocoloIntegrado() {
    
        try {
        
            ini_set('max_execution_time','0');
            ini_set('memory_limit','-1');
            
            InfraDebug::getInstance()->setBolLigado(true);
            InfraDebug::getInstance()->setBolDebugInfra(false);
            InfraDebug::getInstance()->setBolEcho(false);
            InfraDebug::getInstance()->limpar();
            
            //SessaoSEI::getInstance(false)->simularLogin(SessaoSEI::$USUARIO_SEI, SessaoSEI::$UNIDADE_TESTE);
            $numSeg = InfraUtil::verificarTempoProcessamento();
            
            InfraDebug::getInstance()->gravar('Inicializando Notifica��es de Processos N�o Publicados no Protocolo Integrado');
            $objProtocoloIntegradoMonitoramento = new ProtocoloIntegradoMonitoramentoProcessosDTO();	
            $objProtocoloRN = new ProtocoloIntegradoMonitoramentoProcessosRN();
            $objProtocoloRN->notificarProcessosComFalha($objProtocoloIntegradoMonitoramento);
            $numSeg = InfraUtil::verificarTempoProcessamento($numSeg);
            InfraDebug::getInstance()->gravar('TEMPO TOTAL DE EXECUCAO: '.$numSeg.' s');
            InfraDebug::getInstance()->gravar('FIM');
            
            LogSEI::getInstance()->gravar(InfraDebug::getInstance()->getStrDebug());
            InfraDebug::getInstance()->limpar();
        
        } catch(Exception $e) {
            InfraDebug::getInstance()->setBolLigado(false);
            InfraDebug::getInstance()->setBolDebugInfra(false);
            InfraDebug::getInstance()->setBolEcho(false);
            
            InfraDebug::getInstance()->limpar();
            throw new InfraException('Erro ao publicar Metadados e Opera��es dos Processos no Protocolo Integrado.',$e);
        }
        
    }
  
}
 
?>