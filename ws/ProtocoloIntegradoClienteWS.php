<?
/*
* TRIBUNAL REGIONAL FEDERAL DA 4� REGI�O
*
* 27/11/2006 - criado por mga
*
*
*/
ini_set('soap.wsdl_cache_enabled',0);
ini_set('soap.wsdl_cache_ttl',0);
require_once dirname(__FILE__).'/../../../SEI.php';
require_once dirname(__FILE__).'/Enconding.php';



class ProtocoloIntegradoClienteWS extends SoapClient {
		
	 
	 private $context;
	 private $acao;
	 private $login;
	 private $senha;
	 private $url;
	 private $listaDocumentosFormatada;
	 private $certificado;
	  
	 public function __construct($url,$login,$senha,$opcoes) {
	 	
		try{
			if(strpos($url,'homologa.protocolointegrado.gov.br')!== FALSE ){
				
				$this->certificado    = "certificado_homologacao.cer";	
				
			}else if(strpos($url,'protocolointegrado.gov.br')!== FALSE ){
				
				$this->certificado    = "certificado_producao.cer";	
				
			}else{
				
				$this->certificado    = "certificado.cer";
			}
			
			$this->login = $login;
			$this->senha = $senha;
			$this->url  = $url;
	        // Create the stream_context and add it to the options
	        $this->context = stream_context_create();
			$this->soap_defencoding='utf-8';
	        $opcoes = array_merge($opcoes, array('stream_context' => $this->context,'local_cert'=>$this->certificado));
			
			$this->validarConexaoWebService();
			parent::SoapClient($url, $opcoes );
			
			
		}catch (Exception $e){
	      	throw new InfraException('Erro ao se conectar ao Webservice',$e);
	    }
    }
	private function validarConexaoWebService(){
		
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_HEADER, true) ;  
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt ($ch, CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; //Windows NT 5.1; en-US; rv:1.7.5) Gecko/20041107 Firefox/1.0');
		curl_setopt($ch, CURLOPT_URL,$this->url );
		
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 1);
		curl_setopt($ch, CURLOPT_CAINFO, dirname(__FILE__).'/'.$this->certificado);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
		
		$retorno = curl_exec($ch);
		$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
		$header = substr($retorno, 0, $header_size);
		
		if(stripos( $this->url,"?wsdl")===false){
			throw new InfraException("Endere�o do servi�o inv�lido ou servi�o fora do ar.
							Verifique se este endere�o est� corretamente informado nos par�metros de integra��o ao Protocolo Integrado.",$e);
	
		}	
		if(curl_errno($ch)) {
			if (curl_errno($ch)==60){
				throw new InfraException("Certificado inv�lido ou ausente.",$e);
			}
			else{
				throw new InfraException("Endere�o do servi�o inv�lido ou servi�o fora do ar.
					Verifique se este endere�o est� corretamente informado nos par�metros de integra��o ao Protocolo Integrado.",$e);
			}			
		}else{
			if(stripos( $header,"200 OK")===false){
				throw new InfraException("Endere�o do servi�o inv�lido ou servi�o fora do ar.
							Verifique se este endere�o est� corretamente informado nos par�metros de integra��o ao Protocolo Integrado.",$e);
	
			}
			
		}
		
		
	}
    // Override doRequest to calculate the authentication hash from the $request. 
	
    function __doRequest($request, $location, $action, $version, $one_way = 0) {
        // Grab all the text from the request.
       
        $codSiorg  = $this->login ;
	    $senha     = $this->senha; 
		if($this->acao=='enviarListaDocumentosServidor'){
			
			$request = $this->listaDocumentosFormatada;
		
		}
        // Set the HTTP headers.
        $autorizacao = "Basic ".base64_encode($codSiorg.':'.$senha);
        stream_context_set_option($this->context, array('http' => array('header' => 'Authorization:'. $autorizacao)));
		 
         $response = parent::__doRequest($request, $location, $action, $version, $one_way);
		 
		 return $response;
		
    }  
   
	public function getQuantidadeMaximaDocumentosPorRequisicaoServidor(){
	  		
	  	try{
	  		 	
			$numMaxDocumentos = $this->getQuantidadeMaximaDocumentosPorRequisicao();	
			
	  	  	return $numMaxDocumentos;
	  	} catch(Exception $e){
	  	
	      return $e->getMessage();
	    }
		return null;
	}
	public function enviarListaDocumentosServidor($param){
	  		
	  	try{
			
			$this->acao = 'enviarListaDocumentosServidor';
			$retorno = $this->formatarEnvioListaDocumentosPI($param);
			
			return $retorno;
	  	} catch(Exception $e){
	  	
	  	  	error_log('Exce��o:'.$e->getMessage());
	      	return $e;
	    }
		return null;
	}
	public function formatarEnvioListaDocumentosPI($param){
		
		$elementos = array(0=>'Assunto',1=>'NomeInteressado',2=>'Operacao',3=>'UnidadeOperacao');
		
		for($it=0;$it<count($elementos);$it++){
			
			$this->formatarElementoXML($param,$elementos[$it]);
		}
	    $sax = xml_parser_create();
		
		$xml = $param->saveXML();
		$pos = strpos($xml, '<ListaDocumentos>');
		$xml = substr($xml, $pos,strlen($xml));
		for($control = 0; $control < 32; $control++) {
				    $xml = str_replace(chr($control), "", $xml);	
		}
		$this->listaDocumentosFormatada = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:end="http://endpoint.ws.protocolo.gov.br/">
				   		 <soapenv:Header/>
				  		 <soapenv:Body> <end:enviarListaDocumentos>'.($xml).' </end:enviarListaDocumentos></soapenv:Body>
					</soapenv:Envelope>';
				       
		
		return $this->__soapCall('EnviarListaDocumentos',array());	
	}
	//Converte elementos(tags) do XML com caracteres especiais (acentos,pontua��o,etc.) para formato de enconding aceito pelo PI
	private function formatarElementoXML($xml,$elemento){
		
		$objetos = $xml->getElementsByTagName($elemento);
		
		if($objetos!=null){
		    for($ite=0;$ite<$objetos->length;$ite++){
		   		
				$objetos->item($ite)->nodeValue = InfraString::formatarXML(Encoding::fixUTF8($objetos->item($ite)->nodeValue));
		    }
		}	
	}
}	
	