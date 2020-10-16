<?php
@ session_start();
$InfluenciadoresInstanciada = '';
if(empty($InfluenciadoresInstanciada)) {
	if(file_exists('Connection/conexao.php')) {
		require_once('Connection/con-pdo.php');
		require_once('funcoes.php');
	} else {
		require_once('../Connection/con-pdo.php');
		require_once('../funcoes.php');
	}
	
	class Influenciadores {
		
		private $pdo = null;  

		private static $Influenciadores = null; 

		private function __construct($conexao){  
			$this->pdo = $conexao;  
		}
	  
		public static function getInstance($conexao){   
			if (!isset(self::$Influenciadores)):    
				self::$Influenciadores = new Influenciadores($conexao);   
			endif;
			return self::$Influenciadores;    
		}
		
	
		function rsDados($id='', $orderBy='', $limite='') {
			
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
			
			if(isset($_POST['buscaNome']) && !empty($_POST['buscaNome'])) {
				$sql .= " and nome like '%{$_POST['buscaNome']}%'"; 
			}
			if(isset($_POST['buscaStatus']) && !empty($_POST['buscaStatus'])) {
				$sql .= " and status = '{$_POST['buscaStatus']}'"; 
			}

			/// ORDEM		
			if(!empty($orderBy)) {
				$sqlOrdem = " order by {$orderBy}";
			}
			
			if(!empty($limite)) {
				$sqlLimite = " limit 0,{$limite}";
			}
			
			try{   
				$sql = "SELECT * FROM tbl_influenciadores where 1=1 $sql $sqlOrdem $sqlLimite";
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

		function add($redireciona='') {
			if(isset($_POST['acao']) && $_POST['acao'] == 'addInfluenciador') {

				
				$nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_STRING);
				$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_STRING);
				$telefone = filter_input(INPUT_POST, 'telefone', FILTER_SANITIZE_STRING);
				$facebook = filter_input(INPUT_POST, 'facebook', FILTER_SANITIZE_STRING);
				$instagram = filter_input(INPUT_POST, 'instagram', FILTER_SANITIZE_STRING);
				$twitter = filter_input(INPUT_POST, 'twitter', FILTER_SANITIZE_STRING);
				$sexo = filter_input(INPUT_POST, 'sexo', FILTER_SANITIZE_STRING);
				
					try{
						if(file_exists('Connection/conexao.php')) {
							$pastaArquivos = 'img';
						} else {
							$pastaArquivos = '../img';
						}
						
						$sql = "INSERT INTO tbl_influenciadores (nome, email, telefone, facebook, instagram, twitter, foto, sexo) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";   
						$stm = $this->pdo->prepare($sql);   
						$stm->bindValue(1, $nome);   
						$stm->bindValue(2, $email);   
						$stm->bindValue(3, $telefone);   
						$stm->bindValue(4, $facebook);
						$stm->bindValue(5, $instagram);
						$stm->bindValue(6, $twitter);
						$stm->bindValue(7, upload('foto', $pastaArquivos, 'N'));
						$stm->bindValue(8, $sexo);
						$stm->execute(); 
						$idTratamento = $this->pdo->lastInsertId();
						
						if($redireciona == '') {
							$redireciona = '.';
						}
						
						echo "	<script>
								window.location='influenciadores.php';
								</script>";
								exit;
					} catch(PDOException $erro){
						echo $erro->getMessage(); 
					}
			}
		}
		
		function editar($redireciona='influenciadores.php') {
			if(isset($_POST['acao']) && $_POST['acao'] == 'editaInfluenciador') {

				$nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_STRING);
				$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_STRING);
				$telefone = filter_input(INPUT_POST, 'telefone', FILTER_SANITIZE_STRING);
				$facebook = filter_input(INPUT_POST, 'facebook', FILTER_SANITIZE_STRING);
				$instagram = filter_input(INPUT_POST, 'instagram', FILTER_SANITIZE_STRING);
				$twitter = filter_input(INPUT_POST, 'twitter', FILTER_SANITIZE_STRING);
				$sexo = filter_input(INPUT_POST, 'sexo', FILTER_SANITIZE_STRING);
				$id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_STRING);
			
				try { 
					if(file_exists('Connection/conexao.php')) {
						$pastaArquivos = 'img';
					} else {
						$pastaArquivos = '../img';
					}
				
					$sql = "UPDATE tbl_influenciadores SET nome=?, email=?, telefone=?, facebook=?, instagram=?, twitter=?, foto=?, sexo=? WHERE id=?";   
					$stm = $this->pdo->prepare($sql);   
					$stm->bindValue(1, $nome);   
					$stm->bindValue(2, $email);   
					$stm->bindValue(3, $telefone);   
					$stm->bindValue(4, $facebook);
					$stm->bindValue(5, $instagram);
					$stm->bindValue(6, $twitter);
					$stm->bindValue(7, upload('foto', $pastaArquivos, 'N'));
					$stm->bindValue(8, $sexo);
					$stm->bindValue(9, $id);   
					$stm->execute(); 
										
				} catch(PDOException $erro){
					echo $erro->getMessage(); 
				}

				echo "	<script>
							window.location='{$redireciona}';
							</script>";
							exit;
			}
		}
		
		function excluir() {
			if(isset($_GET['acao']) && $_GET['acao'] == 'excluirInfluenciador') {
				
				try{   
					$sql = "DELETE FROM tbl_influenciadores WHERE id=? ";   
					$stm = $this->pdo->prepare($sql);   
					$stm->bindValue(1, $_GET['id']);   
					$stm->execute();
				} catch(PDOException $erro){
					echo $erro->getMessage(); 
				}

			}
		}
	}
	
	$InfluenciadoresInstanciada = 'S';
}