<?php
//Formatando data que vem do banco mysql
function formataData($data){
    return date("d/m/Y", strtotime($data));
}

//Mostrar opções do para ligar
function exibe_status_ligar($exibe_status_ligar) {
    switch ($exibe_status_ligar) {
      case "O": return "Ocupado"; break;
      case "NA": return "Esperando Atendimento"; break;
      case "A": return "Atendido"; break;
      case "LN": return "Ligar Novamente"; break;
      default: return "";
    }
  }

  //Mostrar status contato
function exibe_status_contato($exibe_status_contato) {
  switch ($exibe_status_contato) {
    case "NQ": return "<span class='label label-inline label-danger font-weight-bold'>Não Qualificado</span>"; break;
    case "EA": return "<span class='label label-inline label-primary font-weight-bold'>Esperando Atendimento</span>"; break;
    case "NA": return "<span class='label label-inline label-warning font-weight-bold'>Não Atendeu</span>"; break;
    case "LN": return "<span class='label label-inline label-info font-weight-bold'>Ligar Novamente</span>"; break;
    case "PE": return "<span class='label label-inline label-light-primary font-weight-bold'>Proposta Enviada</span>"; break;
    case "I": return "<span class='label label-inline label-success font-weight-bold'>Interesse</span>"; break;
    case "NF": return "<span class='label label-inline label-secondary font-weight-bold'>Negócio Fechado</span>"; break;
    case "NFO": return "<span class='label label-inline label-secondary font-weight-bold'>Negócio Fechado Online</span>"; break;
    default: return "";
  }
}

function statusAgendamento($status_agendamento) {
  switch ($status_agendamento) {
    case "RL": return "<span class='label label-inline label-info font-weight-bold'>Retornar Ligação</span>"; break;
    case "AE": return "<span class='label label-inline label-secondary font-weight-bold'>Aula Experimental</span>"; break;
    default: return "";
  }
}
//Opçao de escolha do aluno
  function exibe_opcao_aluno($exibe_opcao_aluno) {
    switch ($exibe_opcao_aluno) {
      case "1": return "Aceite"; break;
      case "2": return "Trancamento"; break;
      default: return "";
    }
  }

  //Opçao de escolha do aluno
  function exibe_rede_social($exibe_rede_social) {
    switch ($exibe_rede_social) {
      case "I": return "Instagram"; break;
      case "F": return "Facebook"; break;
      case "G": return "Google"; break;
      case "NI": return "Não Informado"; break;
      default: return "";
    }
  }

  //Opçao de escolha do aluno
  function icone_genero($genero) {
    switch ($genero) {
      case "M": return "assets/media/svg/avatars/001-boy.svg"; break;
      case "F": return "assets/media/svg/avatars/014-girl-7.svg"; break;
      default: return "";
    }
  }

  function upload($campo, $pasta, $array) {
    list($usec, $sec) = explode(" ", microtime());
    $tmp = (float)$usec + (float)$sec;	
    
    if($array == 'N' and $array != '0') {
      if(isset($_FILES[$campo]['name']) && !empty($_FILES[$campo]['name'])) {
        $file = $_FILES[$campo];
        $file['name'];
        $ext = substr($file['name'],strrpos($file['name'],"."));
        copy($file['tmp_name'],$pasta."/$tmp-$campo-$array$ext");
        
        return("$tmp-$campo-$array$ext"); 
      } else { 
        if(isset($_POST[$campo.'_Atual'])){
          return($_POST[$campo.'_Atual']); 
        }
        
      } // hidden com a foto _Atual.
    } else {
      if(isset($_FILES[$campo]['name'][$array]) && !empty($_FILES[$campo]['name'][$array])) {
        $file = $_FILES[$campo];
        $file['name'][$array];
        $ext = substr($file['name'][$array],strrpos($file['name'][$array],"."));
        copy($file['tmp_name'][$array],$pasta."/$tmp-$campo-$array$ext");
        return("$tmp-$campo-$array$ext"); 
      } else { 
        return($_POST[$campo.'_Atual'][$array]); 
      } // hidden com a foto _Atual.
    }
  }

  function mudaCor() {
    global $cor;
    if($cor/2 == 0) { 
      $cor=1; 
      return('background-color:#FFF4DE !important;');
    } else { 
      $cor=0; 
      return('background-color:#FFE2E5 !important;');
    }
  }
  function corAleatoriaClasse() {
    static $corAnterior = 0;
    static $cor = array( 'bg-light-warning', 'bg-light-success', 'bg-light-danger', 'bg-light-info' );

    $aleatorio = rand( $corAnterior?1:0, count( $cor ) - 1 );
    if( $aleatorio >= $corAnterior ) $aleatorio++;
    $corAnterior = $aleatorio;
    return $cor[$aleatorio - 1];
 }
?>