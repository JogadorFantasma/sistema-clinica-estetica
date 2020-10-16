<?php
$ConfigSistemaInstanciada = '';
if(empty($ConfigSistemaInstanciada)) {
	if(file_exists('Connection/conexao.php')) {
		require_once('Connection/con-pdo.php');
		require_once('funcoes.php');
	} else {
		require_once('../Connection/con-pdo.php');
		require_once('../funcoes.php');
	}
	
	class ConfigSistema {

		private $pdo = null;  

		private static $ConfigSistema = null; 
	
		private function __construct($conexao){  
			$this->pdo = $conexao;  
		}
		
		public static function getInstance($conexao){   
			if (!isset(self::$ConfigSistema)):    
				self::$ConfigSistema = new ConfigSistema($conexao);   
			endif;
			return self::$ConfigSistema;    
		}
		
		var $id_campanha;
				
		function rsDados() {
			
			try{   
				$sql = "SELECT * FROM tbl_config ";
				$stm = $this->pdo->prepare($sql);
				$stm->execute();   
				$rsDados = $stm->fetchAll(PDO::FETCH_OBJ);
											
				$this->id_campanha = $rsDados[0]->id_campanha;
				
			} catch(PDOException $erro){   
				echo $erro->getLine(); 
			}
			
			return($this);
		}

		

		function acessosSite($id='', $orderBy='', $limite='', $idCampanha='', $veioDeOnde='') {
			
			/// FILTROS
			$nCampos = 0;
			$sql = '';
			$sqlOrdem = ''; 
			$sqlLimite = '';
			if(!empty($id)) {
				$sql .= " and id = ?"; 
				$nCampos++;
				$campo[$nCampos] = $id;
			}
			
			if(!empty($idCampanha)) {
				$sql .= " and id_campanha = ?"; 
				$nCampos++;
				$campo[$nCampos] = $idCampanha;
			}
			if(!empty($veioDeOnde)) {
				$sql .= " and veio_de_onde = ?"; 
				$nCampos++;
				$campo[$nCampos] = $veioDeOnde;
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
				$sql = "SELECT * FROM contadores_paginas where 1=1 $sql $sqlOrdem $sqlLimite";
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

		
		function statusFrase($id_usuario, $status) {
		

				try{   
					$sql = "UPDATE tbl_usuarios SET frase_lida=? WHERE id=?";   
					$stm = $this->pdo->prepare($sql);   
					$stm->bindValue(1, $status);   
					$stm->bindValue(2, $id_usuario);   
					$stm->execute();  
					
					
				} catch(PDOException $erro){   
					echo $erro->getMessage();
				}
				echo "	<script>
							window.location='.';
							</script>";
							exit;
			
		}
		
		
		function editar() {
			if(isset($_POST['acao']) && $_POST['acao'] == 'editarConfig') {
				$id_campanha = filter_input(INPUT_POST, 'id_campanha', FILTER_SANITIZE_STRING);
				try{   
					$sql = "UPDATE tbl_config SET id_campanha=? WHERE id=? ";   
					$stm = $this->pdo->prepare($sql);  
					$stm->bindValue(1, $id_campanha);
					$stm->bindValue(2, 1);
					$stm->execute();  
					
					echo "	<script>
							alert('Modificado com sucesso!');
							window.location='configuracoes.php';
							</script>";
							exit;
				} catch(PDOException $erro){   
					echo $erro->getMessage();
				}
			}
		}

		function rsFrases($id='', $orderBy='', $limite='') {
			
			/// FILTROS
			$nCampos = 0;
			$sql = '';
			$sqlOrdem = ''; 
			$sqlLimite = '';
			if(!empty($id)) {
				$sql .= " and id = ?"; 
				$nCampos++;
				$campo[$nCampos] = $id;
			}
			
			/// ORDEM		
			if(!empty($orderBy)) {
				$sqlOrdem = " order by {$orderBy}";
			}
			if(!empty($limite)) {
				$sqlLimite = " limit 0,{$limite}";
			}
			try{   
				$sql = "SELECT * FROM tbl_frases where 1=1 $sql $sqlOrdem $sqlLimite";
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

		function addFrase($redireciona='') {
			if(isset($_POST['acao']) && $_POST['acao'] == 'addFrase') {
				$frase = filter_input(INPUT_POST, 'frase', FILTER_SANITIZE_STRING);
			
					try{
						
						$sql = "INSERT INTO tbl_frases (frase) VALUES (?)";   
						$stm = $this->pdo->prepare($sql);   
						$stm->bindValue(1, $frase);   
						$stm->execute(); 
						//$idTratamento = $this->pdo->lastInsertId();
						
						
					} catch(PDOException $erro){
						echo $erro->getMessage(); 
					}
					if($redireciona == '') {
						$redireciona = '.';
					}
					
					echo "	<script>
							window.location='frases.php';
							</script>";
							exit;
				
			}
		}

		function editarFrase() {
			if(isset($_POST['acao']) && $_POST['acao'] == 'editaFrase') {
				$frase = filter_input(INPUT_POST, 'frase', FILTER_SANITIZE_STRING);
				$id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_STRING);
				try{   
					$sql = "UPDATE tbl_frases SET frase=? WHERE id=? ";   
					$stm = $this->pdo->prepare($sql);  
					$stm->bindValue(1, $frase);
					$stm->bindValue(2, $id);
					$stm->execute();  
					
				} catch(PDOException $erro){   
					echo $erro->getMessage();
				}
				echo "	<script>
				window.location='frases.php';
				</script>";
				exit;
			}
		}

		function excluirFrase() {
			if(isset($_GET['acao']) && $_GET['acao'] == 'excluirFrase') {
				
				try{   
					$sql = "DELETE FROM tbl_frases WHERE id=? ";   
					$stm = $this->pdo->prepare($sql);   
					$stm->bindValue(1, $_GET['id']);   
					$stm->execute();
				} catch(PDOException $erro){
					echo $erro->getMessage(); 
				}

			}
		}

		function rsCampanha($id='', $orderBy='', $limite='') {
			
			/// FILTROS
			$nCampos = 0;
			$sql = '';
			$sqlOrdem = ''; 
			$sqlLimite = '';
			if(!empty($id)) {
				$sql .= " and id = ?"; 
				$nCampos++;
				$campo[$nCampos] = $id;
			}
			
			/// ORDEM		
			if(!empty($orderBy)) {
				$sqlOrdem = " order by {$orderBy}";
			}
			if(!empty($limite)) {
				$sqlLimite = " limit 0,{$limite}";
			}
			try{   
				$sql = "SELECT * FROM tbl_campanha where 1=1 $sql $sqlOrdem $sqlLimite";
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

		function addCampanha($redireciona='') {
			if(isset($_POST['acao']) && $_POST['acao'] == 'addCampanha') {
				$nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_STRING);
			
					try{
						
						$sql = "INSERT INTO tbl_campanha (nome) VALUES (?)";   
						$stm = $this->pdo->prepare($sql);   
						$stm->bindValue(1, $nome);   
						$stm->execute(); 
						//$idTratamento = $this->pdo->lastInsertId();
						
						
					} catch(PDOException $erro){
						echo $erro->getMessage(); 
					}
					if($redireciona == '') {
						$redireciona = '.';
					}
					
					echo "	<script>
							window.location='campanhas.php';
							</script>";
							exit;
				
			}
		}

		function editarCampanha() {
			if(isset($_POST['acao']) && $_POST['acao'] == 'editaCampanha') {
				$nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_STRING);
				$id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_STRING);
				try{   
					$sql = "UPDATE tbl_campanha SET nome=? WHERE id=? ";   
					$stm = $this->pdo->prepare($sql);  
					$stm->bindValue(1, $nome);
					$stm->bindValue(2, $id);
					$stm->execute();  
					
				} catch(PDOException $erro){   
					echo $erro->getMessage();
				}
				echo "	<script>
				window.location='campanhas.php';
				</script>";
				exit;
			}
		}

		function excluirCampanha() {
			if(isset($_GET['acao']) && $_GET['acao'] == 'excluirCampanha') {
				
				try{   
					$sql = "DELETE FROM tbl_campanha WHERE id=? ";   
					$stm = $this->pdo->prepare($sql);   
					$stm->bindValue(1, $_GET['id']);   
					$stm->execute();
				} catch(PDOException $erro){
					echo $erro->getMessage(); 
				}

			}
		}
	
	}
	
	$ConfigSistemaInstanciada = 'S';
}