<?php
@ session_start();
$MateriaisInstanciada = '';
if(empty($MateriaisInstanciada)) {
	if(file_exists('Connection/conexao.php')) {
		require_once('Connection/con-pdo.php');
		require_once('funcoes.php');
	} else {
		require_once('../Connection/con-pdo.php');
		require_once('../funcoes.php');
	}
	
	class Materiais {
		
		private $pdo = null;  

		private static $Materiais = null; 

		private function __construct($conexao){  
			$this->pdo = $conexao;  
		}
	  
		public static function getInstance($conexao){   
			if (!isset(self::$Materiais)):    
				self::$Materiais = new Materiais($conexao);   
			endif;
			return self::$Materiais;    
		}
		
	
		function rsDados($id='', $tipoMaterial='', $orderBy='', $limite='', $ativo='', $email='') {
			
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
			
			
			
			if($ativo == 'S') {
				$sql .= " and ativo = 'S'"; 
			}
			
			if($ativo == 'N') {
				$sql .= " and (ativo = 'N' or ativo is null)"; 
			}
			
			
			/// ORDEM		
			if(!empty($orderBy)) {
				$sqlOrdem = " order by {$orderBy}";
			}
			
			if(!empty($limite)) {
				$sqlLimite = " limit 0,{$limite}";
			}
			
			try{   
				$sql = "SELECT * FROM tbl_materiais where 1=1 $sql $sqlOrdem $sqlLimite";
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

		function relacionaPerdas($id='', $orderBy='', $limite='', $foiQuebra='S') {
			
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

			if(!empty($foiQuebra) && $foiQuebra == 'S') {
				$sql .= " and foi_quebra = ?"; 
				$nCampos++;
				$campo[$nCampos] = $foiQuebra;
			}

			if(!empty($_GET['dataDe']) && !empty($_GET['dataDe'])) {
				$sql .= " and data_aplicacao >= {$_GET['dataDe']}"; 
			}
			if(!empty($_GET['dataAte']) && !empty($_GET['dataAte'])) {
				$sql .= " and data_aplicacao <= {$_GET['dataAte']}"; 
			}
			
			/// ORDEM		
			if(!empty($orderBy)) {
				$sqlOrdem = " order by {$orderBy}";
			}
			
			if(!empty($limite)) {
				$sqlLimite = " limit 0,{$limite}";
			}
			
			try{   
				$sql = "SELECT * FROM tbl_relaciona_itens_utilizados where 1=1 $sql $sqlOrdem $sqlLimite";
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


		function relatorioMateriais($id='', $orderBy='', $limite='', $group='', $dataVazia='') {
			
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


			if(!empty($_GET['dataDe']) && !empty($_GET['dataDe'])) {
				$sql .= " and data_aplicacao >= {$_GET['dataDe']}"; 
			}
			if(!empty($_GET['dataAte']) && !empty($_GET['dataAte'])) {
				$sql .= " and data_aplicacao <= {$_GET['dataAte']}"; 
			}
			
			if(!empty($dataVazia)) {
				$sql .= " and id_medicamento is not NULL"; 
			
			}
			
			if(!empty($group)) {
				$sql .= " GROUP BY id_medicamento"; 
			}
			
			/// ORDEM		
			if(!empty($orderBy)) {
				$sqlOrdem = " order by {$orderBy}";
			}
			
			if(!empty($limite)) {
				$sqlLimite = " limit 0,{$limite}";
			}
			
			try{   
				$sql = "SELECT SUM(quantidade) as total, id_medicamento FROM tbl_relaciona_itens_utilizados where 1=1 $sql $sqlOrdem $sqlLimite";
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

		function TipoMaterial($id='') {
			$sql = '';
			/// FILTROS
			$nCampos = 0;
			
			if(!empty($id)) {
				$sql .= " and id = ?"; 
				$nCampos++;
				$campo[$nCampos] = $id;
			}
			try{   
				$sql = "SELECT * FROM tbl_tipo_material where 1=1 $sql";
				$stm = $this->pdo->prepare($sql);
				
				for($i=1; $i<=$nCampos; $i++) {
					$stm->bindValue($i, $campo[$i]);
				}
				
				$stm->execute();
				$rsDados = $stm->fetchAll(PDO::FETCH_OBJ);
				//print_r($rsDados);
				if($id <> '') {
					return($rsDados[0]);
				} else {
					return($rsDados);
				}
			} catch(PDOException $erro){   
				echo $erro->getMessage(); 
			}
		}

		function UnidadeMedida($id='') {
			$sql = '';
			/// FILTROS
			$nCampos = 0;
			
			if(!empty($id)) {
				$sql .= " and id = ?"; 
				$nCampos++;
				$campo[$nCampos] = $id;
			}
			try{   
				$sql = "SELECT * FROM tbl_unidade_medida where 1=1 $sql";
				$stm = $this->pdo->prepare($sql);
				
				for($i=1; $i<=$nCampos; $i++) {
					$stm->bindValue($i, $campo[$i]);
				}
				
				$stm->execute();
				$rsDados = $stm->fetchAll(PDO::FETCH_OBJ);
				//print_r($rsDados);
				if($id <> '') {
					return($rsDados[0]);
				} else {
					return($rsDados);
				}
			} catch(PDOException $erro){   
				echo $erro->getMessage(); 
			}
		}
		
		function add($redireciona='') {
			if(isset($_POST['acao']) && $_POST['acao'] == 'addMaterial') {
				
				$nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_STRING);
				$id_tipo_material = filter_input(INPUT_POST, 'id_tipo_material', FILTER_SANITIZE_STRING);
				$id_unidade_medida = filter_input(INPUT_POST, 'id_unidade_medida', FILTER_SANITIZE_STRING);
				$quantidade = filter_input(INPUT_POST, 'quantidade', FILTER_SANITIZE_STRING);
				$avise = filter_input(INPUT_POST, 'avise', FILTER_SANITIZE_STRING);
				$descricao = filter_input(INPUT_POST, 'descricao', FILTER_SANITIZE_STRING);
				$data_registro = date('Y-m-d H:i:s');
				

				// Verificar se já existe:
				$sql = "SELECT tbl_Materiais.* FROM tbl_materiais where nome = ?";
				$stm = $this->pdo->prepare($sql);
				$stm->bindValue(1, $nome);			
				$stm->execute();  
				
				if(count($stm->fetchAll(PDO::FETCH_OBJ)) > 0) {
					echo "	<script>
							alert('Material já cadastrado.');
							history.back();
							</script>";
							exit;
				} else {
					try{
						
						$sql = "INSERT INTO tbl_materiais (nome, id_tipo_material, id_unidade_medida, quantidade, avise, descricao, data_registro) VALUES (?, ?, ?, ?, ?, ?, ?)";   
						$stm = $this->pdo->prepare($sql);   
						$stm->bindValue(1, $nome);   
						$stm->bindValue(2, $id_tipo_material);   
						$stm->bindValue(3, $id_unidade_medida);   
						$stm->bindValue(4, $quantidade);   
						$stm->bindValue(5, $avise);   
						$stm->bindValue(6, $descricao);   
						$stm->bindValue(7, $data_registro);   
						$stm->execute(); 
						$idConteudo = $this->pdo->lastInsertId();
						
						
						/// FIM CAMPO PERSONALIZADO
						
						if($redireciona == '') {
							$redireciona = 'estoque.php';
						}
						
						echo "	<script>
								window.location='{$redireciona}';
								</script>";
								exit;
					} catch(PDOException $erro){
						echo $erro->getMessage(); 
					}
				}
			}
		}
		
		function editar($redireciona='estoque.php') {
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
							window.location='{$redireciona}';
							</script>";
							exit;
				} catch(PDOException $erro){
					echo $erro->getMessage(); 
				}
			}
		}
		
		function excluir() {
			if(isset($_GET['acao']) && $_GET['acao'] == 'excluirMaterial') {
				
				try{   
					$sql = "DELETE FROM tbl_materiais WHERE id=? ";   
					$stm = $this->pdo->prepare($sql);   
					$stm->bindValue(1, $_GET['id']);   
					$stm->execute();
				} catch(PDOException $erro){
					echo $erro->getMessage(); 
				}
				
			}
		}
	}
	
	$MateriaisInstanciada = 'S';
}