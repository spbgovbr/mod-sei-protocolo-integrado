<?php
/**
 * TRIBUNAL REGIONAL FEDERAL DA 4� REGI�O
 *
 * 02/05/2011 - criado por mga
 *
 * Vers�o do Gerador de C�digo: 1.31.0
 *
 * Vers�o no CVS: $Id$
 */

require_once dirname ( __FILE__ ) . '/../../../../SEI.php';

class ProtocoloIntegradoParametrosBD extends InfraBD {
    
    public function __construct(InfraIBanco $objInfraIBanco) {
        parent::__construct( $objInfraIBanco );
    }
    
}

?>