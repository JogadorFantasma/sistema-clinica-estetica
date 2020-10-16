<?php
@session_start();
$LogsInstanciada = '';
if(empty($LogsInstanciada)) {
	if(file_exists('Connection/conexao.php')) {
		require_once('Connection/con-pdo.php');
		require_once('funcoes.php');
	} else {
		require_once('../Connection/con-pdo.php');
		require_once('../funcoes.php');
	}
	
	class Logs {

		private $pdo = null;  
	
		private static $Logs = null; 

		private function __construct($conexao){  
			$this->pdo = $conexao;  
		}
	  
		public static function getInstance($conexao){   
			if (!isset(self::$Logs)):    
				self::$Logs = new Logs($conexao);   
			endif;
			return self::$Logs;    
		}
		
		function rsDados($id='', $idContato='', $orderBy='', $limite='', $idUsuario='', $groupBy='') {
			$sqlLimite = '';
			$sqlOrdem = '';
			$sql = '';
			/// FILTROS
			$nCampos = 0;
			
			if(!empty($id)) {
				$sql .= " and id = ?"; 
				$nCampos++;
				$campo[$nCampos] = $id;
			}

			if(!empty($idContato)) {
				$sql .= " and id_contato = ?"; 
				$nCampos++;
				$campo[$nCampos] = $idContato;
			}

			if(!empty($idUsuario)) {
				$sql .= " and id_usuario = ?"; 
				$nCampos++;
				$campo[$nCampos] = $idUsuario;
			}

			if(isset($_POST['id_colaborador']) && !empty($_POST['id_colaborador'])) {
				$sql .= " and id_usuario = {$_POST['id_colaborador']}"; 
			}
			if(isset($_POST['dataDeCampanha']) && !empty($_POST['dataDeCampanha'])) {
				$sql .= " and data >= '{$_POST['dataDeCampanha']}'"; 
			}
			if(isset($_POST['dataAteCampanha']) && !empty($_POST['dataAteCampanha'])) {
				$sql .= " and data <= '{$_POST['dataAteCampanha']}'"; 
			}

			if(isset($_GET['dias']) && $_GET['dias'] == 1) {
				$sql .= " and data = CURDATE()"; 
			}
			if(isset($_GET['dias']) && $_GET['dias'] == 7) {
				$sql .= " and data >= NOW() + INTERVAL -7 DAY
				AND data <  NOW() + INTERVAL  0 DAY"; 
			}
			if(isset($_GET['dias']) && $_GET['dias'] == 15) {
				$sql .= " and data >= NOW() + INTERVAL -15 DAY
				AND data <  NOW() + INTERVAL  0 DAY"; 
			}
			if(isset($_GET['dias']) && $_GET['dias'] == 30) {
				$sql .= " and data >= NOW() + INTERVAL -30 DAY
				AND data <  NOW() + INTERVAL  0 DAY"; 
			}
			/// ORDEM		
			if(!empty($orderBy)) {
				$sqlOrdem = " order by {$orderBy}";
			}
			
			if(!empty($limite)) {
				$sqlLimite = " limit 0,{$limite}";
			}
			
			try{   
				$sql = "SELECT * FROM tbl_historicos where 1=1 $sql $groupBy $sqlOrdem $sqlLimite";
				$stm = $this->pdo->prepare($sql);
				
				for($i=1; $i<=$nCampos; $i++) {
					$stm->bindValue($i, $campo[$i]);
				}
				
				$stm->execute();
				$rsDados = $stm->fetchAll(PDO::FETCH_OBJ);
				//print_r($rsDados);
				if($id <> '' or $limite == 1) {
					return($rsDados[0]);
				} else {
					return($rsDados);
				}
			} catch(PDOException $erro){   
				echo $erro->getMessage(); 
			}
		}
		
	}
	
	$LogsInstanciada = 'S';
}