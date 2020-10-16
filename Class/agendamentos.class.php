<?php
@ session_start();
$AgendamentosInstanciada = '';
if(empty($AgendamentosInstanciada)) {
	if(file_exists('Connection/conexao.php')) {
		require_once('Connection/con-pdo.php');
		require_once('funcoes.php');
	} else {
		require_once('../Connection/con-pdo.php');
		require_once('../funcoes.php');
	}
	
	class Agendamentos {
		
		private $pdo = null;  

		private static $Agendamentos = null; 

		private function __construct($conexao){  
			$this->pdo = $conexao;  
		}
	  
		public static function getInstance($conexao){   
			if (!isset(self::$Agendamentos)):    
				self::$Agendamentos = new Agendamentos($conexao);   
			endif;
			return self::$Agendamentos;    
		}
		
	
		function rsDados($id='', $orderBy='', $limite='', $idUsuario='', $id_contato='', $data_agendamento='') {
			
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

			if(!empty($tipoMaterial)) {
				$sql .= " and id_tipo_material = ?"; 
				$nCampos++;
				$campo[$nCampos] = $tipoMaterial;
			}
			
			if(!empty($email)) {
				$sql .= " and email = ?"; 
				$nCampos++;
				$campo[$nCampos] = $email;
			}
			if(!empty($idUsuario)) {
				$sql .= " and id_usuario = ?"; 
				$nCampos++;
				$campo[$nCampos] = $idUsuario;
			}
			if(!empty($id_contato)) {
				$sql .= " and id_contato = ?"; 
				$nCampos++;
				$campo[$nCampos] = $id_contato;
			}
			if(!empty($data_agendamento)) {
				$sql .= " and data_agendamento = ?"; 
				$nCampos++;
				$campo[$nCampos] = $data_agendamento;
			}
			
			
			/// ORDEM		
			if(!empty($orderBy)) {
				$sqlOrdem = " order by {$orderBy}";
			}
			
			if(!empty($limite)) {
				$sqlLimite = " limit 0,{$limite}";
			}
			
			try{   
				$sql = "SELECT * FROM tbl_agendamentos where 1=1 $sql $sqlOrdem $sqlLimite";
				$stm = $this->pdo->prepare($sql);
				
				for($i=1; $i<=$nCampos; $i++) {
					$stm->bindValue($i, $campo[$i]);
				}
				
				$stm->execute();
				$rsDados = $stm->fetchAll(PDO::FETCH_OBJ);
				//print_r($rsDados);
				if(isset($id) && !empty($id) or $limite == 1) {
					return($rsDados[0]);
				} else {
					return($rsDados);
				}
			} catch(PDOException $erro){   
				echo $erro->getMessage(); 
			}
		}

		function add($redireciona='') {
			if(isset($_POST['acao']) && $_POST['acao'] == 'addAgendamento') {
				
				$status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_STRING);
				$data_agendamento = filter_input(INPUT_POST, 'data_agendamento', FILTER_SANITIZE_STRING);
				$hora_agendamento = filter_input(INPUT_POST, 'hora_agendamento', FILTER_SANITIZE_STRING);
				$id_contato = filter_input(INPUT_POST, 'id_contato', FILTER_SANITIZE_STRING);
				$id_usuario = filter_input(INPUT_POST, 'id_usuario', FILTER_SANITIZE_STRING);
				
					try{
						
						$sql = "INSERT INTO tbl_agendamentos (status, data_agendamento, hora_agendamento, id_contato, id_usuario) VALUES (?, ?, ?, ?, ?)";   
						$stm = $this->pdo->prepare($sql);   
						$stm->bindValue(1, $status);   
						$stm->bindValue(2, $data_agendamento);   
						$stm->bindValue(3, $hora_agendamento);   
						$stm->bindValue(4, $id_contato);   
						$stm->bindValue(5, $id_usuario);   
						$stm->execute(); 
						$idConteudo = $this->pdo->lastInsertId();
						
					} catch(PDOException $erro){
						echo $erro->getMessage(); 
					}
				
					echo "	<script>
							window.location='editar-contato.php?id=$id_contato';
							</script>";
							exit;
				
			}
		}
		
		function editar() {
			if(isset($_POST['acao']) && $_POST['acao'] == 'editaMaterial') {
				$nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_STRING);
				$id_tipo_material = filter_input(INPUT_POST, 'id_tipo_material', FILTER_SANITIZE_STRING);
				$id_unidade_medida = filter_input(INPUT_POST, 'id_unidade_medida', FILTER_SANITIZE_STRING);
				$quantidade = filter_input(INPUT_POST, 'quantidade', FILTER_SANITIZE_STRING);
				$avise = filter_input(INPUT_POST, 'avise', FILTER_SANITIZE_STRING);
				$descricao = filter_input(INPUT_POST, 'descricao', FILTER_SANITIZE_STRING);
				$data_registro = filter_input(INPUT_POST, 'data_registro', FILTER_SANITIZE_STRING);
				$id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_STRING);

				try { 
				
					$sql = "UPDATE tbl_materiais SET nome=?, id_tipo_material=?, id_unidade_medida=?, quantidade=?, avise=?, descricao=?, data_registro=? WHERE id=?";   
					$stm = $this->pdo->prepare($sql);   
					$stm->bindValue(1, $nome);   
					$stm->bindValue(2, $id_tipo_material);   
					$stm->bindValue(3, $id_unidade_medida);   
					$stm->bindValue(4, $quantidade);   
					$stm->bindValue(5, $avise);   
					$stm->bindValue(6, $descricao);   
					$stm->bindValue(7, $data_registro); 
					$stm->bindValue(8, $id);   
					$stm->execute(); 
					$id = $_POST['id'];
					
					
					echo "	<script>
							window.location='';
							</script>";
							exit;
				} catch(PDOException $erro){
					echo $erro->getMessage(); 
				}
			}
		}
		
		function excluirAgendamento() {
			if(isset($_GET['acao']) && $_GET['acao'] == 'excluirAgendamento') {
				
				try{   
					$sql = "DELETE FROM tbl_agendamentos WHERE id=? ";   
					$stm = $this->pdo->prepare($sql);   
					$stm->bindValue(1, $_GET['id']);   
					$stm->execute();
				} catch(PDOException $erro){
					echo $erro->getMessage(); 
				}

				echo "	<script>
							window.location='editar-contato.php?id={$_GET['id_contato']}';
							</script>";
							exit;
				
			}
		}
	}
	
	$AgendamentosInstanciada = 'S';
}