<?php
@ session_start();
$PacientesInstanciada = '';
if(empty($PacientesInstanciada)) {
	if(file_exists('Connection/conexao.php')) {
		require_once('Connection/con-pdo.php');
		require_once('funcoes.php');
	} else {
		require_once('../Connection/con-pdo.php');
		require_once('../funcoes.php');
	}
	
	class Pacientes {
		
		private $pdo = null;  

		private static $Pacientes = null; 

		private function __construct($conexao){  
			$this->pdo = $conexao;  
		}
	  
		public static function getInstance($conexao){   
			if (!isset(self::$Pacientes)):    
				self::$Pacientes = new Pacientes($conexao);   
			endif;
			return self::$Pacientes;    
		}
		
	
		function rsDados($id='', $orderBy='', $limite='', $ativo='', $email='') {
			
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
			
			if(!empty($email)) {
				$sql .= " and email = ?"; 
				$nCampos++;
				$campo[$nCampos] = $email;
			}

			if(isset($_POST['buscaNome']) && !empty($_POST['buscaNome'])) {
				$sql .= " and nome like '%{$_POST['buscaNome']}%'"; 
			}

			if(isset($_POST['buscaCPF']) && !empty($_POST['buscaCPF'])) {
				$sql .= " and cpf = '{$_POST['buscaCPF']}'"; 
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
				$sql = "SELECT * FROM tbl_pacientes where 1=1 $sql $sqlOrdem $sqlLimite";
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

		function PuxaRelacionaTratamentos($idPaciente = '', $id='', $orderBy='', $limite='') {
			
			/// FILTROS
			$nCampos = 0;
			$sql = '';
			$sqlOrdem = ''; 
			$sqlLimite = '';
			if(!empty($idPaciente)) {
				$sql .= " and id_paciente = ?"; 
				$nCampos++;
				$campo[$nCampos] = $idPaciente;
			}

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
				$sql = "SELECT * FROM tbl_relaciona_tratamento where 1=1 $sql $sqlOrdem $sqlLimite";
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

		function PuxaRelacionaMedicos($idPaciente = '', $id='', $orderBy='', $limite='') {
			
			/// FILTROS
			$nCampos = 0;
			$sql = '';
			$sqlOrdem = ''; 
			$sqlLimite = '';
			if(!empty($idPaciente)) {
				$sql .= " and id_paciente = ?"; 
				$nCampos++;
				$campo[$nCampos] = $idPaciente;
			}

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
				$sql = "SELECT * FROM tbl_relaciona_medico where 1=1 $sql $sqlOrdem $sqlLimite";
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

		function PuxaRelacionaAplicacoes($idPaciente = '', $idTratamento='', $id='', $orderBy='', $limite='', $dataHoje= '', $data7dias='', $dataVazia='', $idRelacionaTratamento='') {
			
			/// FILTROS
			$nCampos = 0;
			$sql = '';
			$sqlOrdem = ''; 
			$sqlLimite = '';
			if(!empty($idPaciente)) {
				$sql .= " and id_paciente = ?"; 
				$nCampos++;
				$campo[$nCampos] = $idPaciente;
			}

			if(!empty($idTratamento)) {
				$sql .= " and id_tratamento = ?"; 
				$nCampos++;
				$campo[$nCampos] = $idTratamento;
			}

			if(!empty($id)) {
				$sql .= " and id = ?"; 
				$nCampos++;
				$campo[$nCampos] = $id;
			}

			if(!empty($idRelacionaTratamento)) {
				$sql .= " and id_relaciona_tratamento = ?"; 
				$nCampos++;
				$campo[$nCampos] = $idRelacionaTratamento;
			}

			if(!empty($dataHoje)) {
				$sql .= " and data_aplicacao = ?"; 
				$nCampos++;
				$campo[$nCampos] = $dataHoje;
			}

			if(!empty($_GET['dataDe']) && !empty($_GET['dataDe'])) {
				$sql .= " and data_aplicacao >= {$_GET['dataDe']}"; 
			}
			if(!empty($_GET['dataAte']) && !empty($_GET['dataAte'])) {
				$sql .= " and data_aplicacao <= {$_GET['dataAte']}"; 
			}

			if($data7dias == 'S') {
				$sql .= " and data_aplicacao >= DATE_SUB(CURRENT_DATE(), INTERVAL
				7 DAY)"; 
				$nCampos++;
				$campo[$nCampos] = $data7dias;
			}
			if(!empty($dataVazia)) {
				$sql .= " and id_enfermeiro is not NULL"; 
			
			}

			/// ORDEM		
			if(!empty($orderBy)) {
				$sqlOrdem = " order by {$orderBy}";
			}
			
			if(!empty($limite)) {
				$sqlLimite = " limit 0,{$limite}";
			}
			
			try{   
				$sql = "SELECT * FROM tbl_relaciona_aplicacoes where 1=1 $sql $sqlOrdem $sqlLimite";
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


		function PuxaRelatorioTratamento($id='', $dataVazia='', $group='', $limite='') {
			
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
				$sql .= " and id_enfermeiro is not NULL"; 
			
			}

			/// ORDEM		
			if(!empty($group)) {
				$sql .= " GROUP BY id_tratamento"; 
			}

			if(!empty($orderBy)) {
				$sqlOrdem = " order by {$orderBy}";
			}
			
			if(!empty($limite)) {
				$sqlLimite = " limit 0,{$limite}";
			}
			
			try{   
				$sql = "SELECT count(id_tratamento) as total, id_tratamento FROM tbl_relaciona_aplicacoes where 1=1 $sql $sqlOrdem $sqlLimite";
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
			if(isset($_POST['acao']) && $_POST['acao'] == 'addPaciente') {
				
				$nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_STRING);
				$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_STRING);
				$telefone = filter_input(INPUT_POST, 'telefone', FILTER_SANITIZE_STRING);
				$endereco = filter_input(INPUT_POST, 'endereco', FILTER_SANITIZE_STRING);
				$cpf = filter_input(INPUT_POST, 'cpf', FILTER_SANITIZE_STRING);
				$senha = filter_input(INPUT_POST, 'senha', FILTER_SANITIZE_STRING);
				$id_usuario = filter_input(INPUT_POST, 'id_usuario', FILTER_SANITIZE_STRING);
				$nome_usuario = filter_input(INPUT_POST, 'nome_usuario', FILTER_SANITIZE_STRING);
				$sexo = filter_input(INPUT_POST, 'sexo', FILTER_SANITIZE_STRING);
				$data_nascimento = filter_input(INPUT_POST, 'data_nascimento', FILTER_SANITIZE_STRING);
				$data_registro = date('Y-m-d H:i:s');
				

				// Verificar se já existe:
				$sql = "SELECT tbl_pacientes.* FROM tbl_pacientes where email = ? or cpf = ? ";
				$stm = $this->pdo->prepare($sql);
				$stm->bindValue(1, $_POST['email']);			
				$stm->bindValue(2, $_POST['cpf']);			
				$stm->execute();  
				
				if(count($stm->fetchAll(PDO::FETCH_OBJ)) > 0) {
					echo "	<script>
							alert('E-mail ou CPF já cadastrado.');
							history.back();
							</script>";
							exit;
				} else {
					try{
						
						if(file_exists('Connection/conexao.php')) {
							$pastaArquivos = 'img';
						} else {
							$pastaArquivos = '../img';
						}
						
						$sql = "INSERT INTO tbl_pacientes (nome, email, telefone, endereco, cpf, foto, data_registro, senha, id_usuario, sexo, data_nascimento) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";   
						$stm = $this->pdo->prepare($sql);   
						$stm->bindValue(1, $nome);   
						$stm->bindValue(2, $email);   
						$stm->bindValue(3, $telefone);   
						$stm->bindValue(4, $endereco);   
						$stm->bindValue(5, $cpf);   
						$stm->bindValue(6, upload('foto', $pastaArquivos, 'N'));   
						$stm->bindValue(7, $data_registro);
						$stm->bindValue(8, $senha);
						$stm->bindValue(9, $id_usuario);
						$stm->bindValue(10, $sexo);
						$stm->bindValue(11, $data_nascimento);   
						$stm->execute(); 
						$idPaciente = $this->pdo->lastInsertId();
						
						
						/// FIM CAMPO PERSONALIZADO
						
						if($redireciona == '') {
							$redireciona = 'paciente-cadastrado.php?id='.$idPaciente;
						}
						
						
					} catch(PDOException $erro){
						echo $erro->getMessage(); 
					}


					////LOG
					try{
						$data_log = date('Y-m-d');
						$hora_log = date('H:i:s');
						$mensagem = "O usuário '".$nome_usuario."' cadastrou esse paciente no dia ".formataData($data_log)." as ".$hora_log;

						$sql = "INSERT INTO tbl_historicos (id_paciente, id_usuario, ocorrencia, data, hora) VALUES (?, ?, ?, ?, ?)";   
						$stm = $this->pdo->prepare($sql);   
						$stm->bindValue(1, $idPaciente);   
						$stm->bindValue(2, $id_usuario);   
						$stm->bindValue(3, $mensagem);   
						$stm->bindValue(4, $data_log);   
						$stm->bindValue(5, $hora_log);   
						$stm->execute(); 
						$idConteudo = $this->pdo->lastInsertId();
						
					} catch(PDOException $erro){
						echo $erro->getMessage(); 
					}
					/// FIM LOG


					echo "	<script>
							window.location='{$redireciona}';
							</script>";
							exit;
				}
			}
		}
		
		function editar($redireciona='pacientes.php') {
			if(isset($_POST['acao']) && $_POST['acao'] == 'editaPaciente') {
				$nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_STRING);
				$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_STRING);
				$telefone = filter_input(INPUT_POST, 'telefone', FILTER_SANITIZE_STRING);
				$endereco = filter_input(INPUT_POST, 'endereco', FILTER_SANITIZE_STRING);
				$cpf = filter_input(INPUT_POST, 'cpf', FILTER_SANITIZE_STRING);
				$data_registro = filter_input(INPUT_POST, 'data_registro', FILTER_SANITIZE_STRING);
				$senha = filter_input(INPUT_POST, 'senha', FILTER_SANITIZE_STRING);
				$id_usuario = filter_input(INPUT_POST, 'id_usuario', FILTER_SANITIZE_STRING);
				$nome_usuario = filter_input(INPUT_POST, 'nome_usuario', FILTER_SANITIZE_STRING);
				$sexo = filter_input(INPUT_POST, 'sexo', FILTER_SANITIZE_STRING);
				$data_nascimento = filter_input(INPUT_POST, 'data_nascimento', FILTER_SANITIZE_STRING);
				$id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_STRING);

				try { 
				
					if(file_exists('Connection/conexao.php')) {
						$pastaArquivos = 'img';
					} else {
						$pastaArquivos = '../img';
					}
						 
					$sql = "UPDATE tbl_pacientes SET nome=?, email=?, telefone=?, endereco=?, cpf=?, foto=?, data_registro=?, senha=?, sexo=?, data_nascimento=? WHERE id=?";   
					$stm = $this->pdo->prepare($sql);   
					$stm->bindValue(1, $nome);   
					$stm->bindValue(2, $email);   
					$stm->bindValue(3, $telefone);   
					$stm->bindValue(4, $endereco);   
					$stm->bindValue(5, $cpf);   
					$stm->bindValue(6, upload('foto', $pastaArquivos, 'N'));   
					$stm->bindValue(7, $data_registro);
					$stm->bindValue(8, $senha); 
					$stm->bindValue(9, $sexo);
					$stm->bindValue(10, $data_nascimento); 
					$stm->bindValue(11, $id);   
					$stm->execute(); 
					
					
					
					
				} catch(PDOException $erro){
					echo $erro->getMessage(); 
				}

					///INSERE TRATAMENTO
					if(!empty($_POST['id_tratamento']) && $_POST['id_tratamento'] != 0) {

						////DELETA TRATAMENTO
				try{   
					$sql = "DELETE FROM tbl_relaciona_tratamento WHERE id=?";   
					$stm = $this->pdo->prepare($sql);   
					$stm->bindValue(1, $id);   
					$stm->execute();
				} catch(PDOException $erro){
					echo $erro->getMessage(); 
				}


						$camposTratamentos = $_POST['id_tratamento'];
						for($i=0; $i<count($camposTratamentos); $i++) {
							try{   
								$sql = "INSERT INTO tbl_relaciona_tratamento (id_paciente, id_tratamento, quantidade) VALUES (?, ?, ?)";   
								$stm = $this->pdo->prepare($sql);   
								$stm->bindValue(1, $id);   
								$stm->bindValue(2, $_POST['id_tratamento'][$i]);   
								$stm->bindValue(3, $_POST['quantidade_tratamento'][$i]);   
								$stm->execute();
							} catch(PDOException $erro){
								echo $erro->getMessage(); 
							}
						}
					}

						///INSERE MEDICO
						if(!empty($_POST['id_medico']) && $_POST['id_medico'] != 0) {

							////DELETA MEDICO
					try{   
						$sql = "DELETE FROM tbl_relaciona_medico WHERE id=?";   
						$stm = $this->pdo->prepare($sql);   
						$stm->bindValue(1, $id);   
						$stm->execute();
					} catch(PDOException $erro){
						echo $erro->getMessage(); 
					}
	
	
							$camposMedicos = $_POST['id_medico'];
							for($i=0; $i<count($camposMedicos); $i++) {
								try{   
									$sql = "INSERT INTO tbl_relaciona_medico (id_paciente, id_medico) VALUES (?, ?)";   
									$stm = $this->pdo->prepare($sql);   
									$stm->bindValue(1, $id);   
									$stm->bindValue(2, $_POST['id_medico'][$i]);   
									$stm->execute();
								} catch(PDOException $erro){
									echo $erro->getMessage(); 
								}
							}
						}

					echo "	<script>
							window.location='{$redireciona}';
							</script>";
							exit;

			}
		}

		function editarTratamento($redireciona='') {
			if(isset($_POST['acao']) && $_POST['acao'] == 'editaTratamento') {
				$id_usuario = filter_input(INPUT_POST, 'id_usuario', FILTER_SANITIZE_STRING);
				$nome_usuario = filter_input(INPUT_POST, 'nome_usuario', FILTER_SANITIZE_STRING);
				$id_paciente = filter_input(INPUT_POST, 'id_paciente', FILTER_SANITIZE_STRING);

				/// REMOVENDO TRATAMENTOS EXISTENTES DO PACIENTE
				if(!empty($_POST['id_tratamento_removido'])) {

					$campoTratamentosRemovidos = $_POST['id_tratamento_removido'];
					for($y=0;$y<count($campoTratamentosRemovidos); $y++){

						////LOG
					try{
						$data_log = date('Y-m-d');
						$hora_log = date('H:i:s');
						$mensagem = "O usuário '".$nome_usuario."' excluiu o tratamento ".$_POST['nome_tratamento_removido'][$y]." no dia ".formataData($data_log)." as ".$hora_log;

						$sql = "INSERT INTO tbl_historicos (id_paciente, id_usuario, ocorrencia, data, hora) VALUES (?, ?, ?, ?, ?)";   
						$stm = $this->pdo->prepare($sql);   
						$stm->bindValue(1, $id_paciente);   
						$stm->bindValue(2, $id_usuario);   
						$stm->bindValue(3, $mensagem);   
						$stm->bindValue(4, $data_log);   
						$stm->bindValue(5, $hora_log);   
						$stm->execute(); 
						$idConteudo = $this->pdo->lastInsertId();
						
					} catch(PDOException $erro){
						echo $erro->getMessage(); 
					}
					/// FIM LOG
					///DELETA DO BANCO O TRATAMENTO
					try{   
						$sql = "DELETE FROM tbl_relaciona_tratamento WHERE id=? ";   
						$stm = $this->pdo->prepare($sql);   
						$stm->bindValue(1, $_POST['id_tratamento_removido'][$y]);   
						$stm->execute();
					} catch(PDOException $erro){
						echo $erro->getMessage(); 
					}

					///DELETA DO BANCO AS APLICACOES DESSE TRATAMENTO
					try{   
						$sql = "DELETE FROM tbl_relaciona_aplicacoes WHERE id_relaciona_tratamento=? ";   
						$stm = $this->pdo->prepare($sql);   
						$stm->bindValue(1, $_POST['id_tratamento_removido'][$y]);   
						$stm->execute();
					} catch(PDOException $erro){
						echo $erro->getMessage(); 
					}

					}
				}
				

					///INSERE TRATAMENTO
					if(!empty($_POST['id_tratamento']) && $_POST['id_tratamento'] != 0) {

						$camposTratamentos = $_POST['id_tratamento'];
						for($i=0; $i<count($camposTratamentos); $i++) {

							////LOG
					try{
						$data_log = date('Y-m-d');
						$hora_log = date('H:i:s');
						$mensagem = "O usuário '".$nome_usuario."' cadastrou o tratamento '".$_POST['nometratamento'][$i]."' no dia ".formataData($data_log)." as ".$hora_log;

						$sql = "INSERT INTO tbl_historicos (id_paciente, id_usuario, ocorrencia, data, hora) VALUES (?, ?, ?, ?, ?)";   
						$stm = $this->pdo->prepare($sql);   
						$stm->bindValue(1, $id_paciente);   
						$stm->bindValue(2, $id_usuario);   
						$stm->bindValue(3, $mensagem);   
						$stm->bindValue(4, $data_log);   
						$stm->bindValue(5, $hora_log);   
						$stm->execute(); 
						$idConteudo = $this->pdo->lastInsertId();
						
					} catch(PDOException $erro){
						echo $erro->getMessage(); 
					}
					/// FIM LOG

							try{   
								$sql = "INSERT INTO tbl_relaciona_tratamento (id_paciente, id_tratamento, quantidade, id_medico) VALUES (?, ?, ?, ?)";   
								$stm = $this->pdo->prepare($sql);   
								$stm->bindValue(1, $id_paciente);   
								$stm->bindValue(2, $_POST['id_tratamento'][$i]);   
								$stm->bindValue(3, $_POST['quantidade_tratamento'][$i]);
								$stm->bindValue(4, $id_usuario);      
								$stm->execute();
								$idRelacionaTratamento = $this->pdo->lastInsertId();
							} catch(PDOException $erro){
								echo $erro->getMessage(); 
							}
							///INSERE QUANTIDADE DE APLICACOES
							
							$idTratamentos = $_POST['id_tratamento'][$i];
							for($y=0; $y<($_POST['quantidade_tratamento'][$i]); $y++) {

								try{   
									$sql = "INSERT INTO tbl_relaciona_aplicacoes (id_paciente, id_tratamento, id_relaciona_tratamento, id_medico) VALUES (?, ?, ?, ?)";   
									$stm = $this->pdo->prepare($sql);   
									$stm->bindValue(1, $id_paciente);   
									$stm->bindValue(2, $idTratamentos);   
									$stm->bindValue(3, $idRelacionaTratamento);
									$stm->bindValue(4, $id_usuario);      
									$stm->execute();
								} catch(PDOException $erro){
									echo $erro->getMessage(); 
								}
								/* if($y == $quantidadeTratamentos){
										  break;
										} */
									
							}
						}
					}
					if($redireciona == '') {
						$redireciona = 'editar-paciente.php?id='.$id_paciente.'#tratamento';
					}
				
					echo "	<script>
							window.location='{$redireciona}';
							</script>";
							exit;

			}
		}

		function addAplicacao($redireciona='') {
			if(isset($_POST['acao']) && $_POST['acao'] == 'addAplicacao') {
				
				$id_tratamento = filter_input(INPUT_POST, 'id_tratamento', FILTER_SANITIZE_STRING);
				$id_paciente = filter_input(INPUT_POST, 'id_paciente', FILTER_SANITIZE_STRING);
				$data_aplicacao = filter_input(INPUT_POST, 'data_aplicacao', FILTER_SANITIZE_STRING);
				$hora_aplicacao = filter_input(INPUT_POST, 'hora_aplicacao', FILTER_SANITIZE_STRING);
				$id_enfermeiro = filter_input(INPUT_POST, 'id_enfermeiro', FILTER_SANITIZE_STRING);
				$observacao = filter_input(INPUT_POST, 'observacao', FILTER_SANITIZE_STRING);
				$nome_tratamento = filter_input(INPUT_POST, 'nome_tratamento', FILTER_SANITIZE_STRING);
				$nome_enfermeiro = filter_input(INPUT_POST, 'nome_enfermeiro', FILTER_SANITIZE_STRING);
				$id = filter_input(INPUT_POST, 'id_aplicacao', FILTER_SANITIZE_STRING);
				
					try{
						
						$sql = "UPDATE tbl_relaciona_aplicacoes SET id_tratamento=?, id_paciente=?, data_aplicacao=?, hora_aplicacao=?, id_enfermeiro=?, observacao=? WHERE id=?";   
						$stm = $this->pdo->prepare($sql);   
						$stm->bindValue(1, $id_tratamento);   
						$stm->bindValue(2, $id_paciente);   
						$stm->bindValue(3, $data_aplicacao);   
						$stm->bindValue(4, $hora_aplicacao);   
						$stm->bindValue(5, $id_enfermeiro);   
						$stm->bindValue(6, $observacao);   
						$stm->bindValue(7, $id);
						$stm->execute(); 
						$idConteudo = $this->pdo->lastInsertId();
						
					} catch(PDOException $erro){
						echo $erro->getMessage(); 
					}

						///INSERE MATERIAIS NOVOS E DÁ BAIXA NO ESTOQUE
						if(!empty($_POST['id_material'])) {
							$camposMateriais = $_POST['id_material'];
							for($i=0; $i<count($camposMateriais); $i++) {

								// puxa material
							$sql = "SELECT tbl_materiais.* FROM tbl_materiais where id = ? ";
							$stm = $this->pdo->prepare($sql);
							$stm->bindValue(1, $_POST['id_material'][$i]);			
							$stm->execute();  
							$rsDados = $stm->fetchAll(PDO::FETCH_OBJ);

							$nova_quantidade = ($rsDados[0]->quantidade -  $_POST['quantidade_material'][$i]);
								///UPDATE NA QUANTIDADE DO ESTOQUE
							try { 
									 
								$sql = "UPDATE tbl_materiais SET quantidade=? WHERE id=?";   
								$stm = $this->pdo->prepare($sql);   
								$stm->bindValue(1, $nova_quantidade);   
								$stm->bindValue(2, $_POST['id_material'][$i]);   
								$stm->execute(); 
								
							} catch(PDOException $erro){
								echo $erro->getMessage(); 
							}


								try{   
									$sql = "INSERT INTO tbl_relaciona_itens_utilizados (id_tratamento, id_material, quantidade, id_paciente, data_registro, hora_registro, id_usuario, foi_quebra) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";   
									$stm = $this->pdo->prepare($sql);   
									$stm->bindValue(1, $id_tratamento);   
									$stm->bindValue(2, $_POST['id_material'][$i]);   
									$stm->bindValue(3, $_POST['quantidade_material'][$i]);
									$stm->bindValue(4, $id_paciente);
									$stm->bindValue(5, $data_aplicacao);
									$stm->bindValue(6, $hora_aplicacao);
									$stm->bindValue(7, $id_enfermeiro);
									$stm->bindValue(8, 'S');   
									$stm->execute();
								} catch(PDOException $erro){
									echo $erro->getMessage(); 
								}
							}
						}

						///INSERE MATERIAIS JÁ LISTADOS E DÁ BAIXA NO ESTOQUE
						if(!empty($_POST['id_material_db'])) {
							$camposMateriais = $_POST['id_material_db'];
							for($i=0; $i<count($camposMateriais); $i++) {

								// puxa material
							$sql = "SELECT tbl_materiais.* FROM tbl_materiais where id = ? ";
							$stm = $this->pdo->prepare($sql);
							$stm->bindValue(1, $_POST['id_material_db'][$i]);			
							$stm->execute();  
							$rsDados = $stm->fetchAll(PDO::FETCH_OBJ);

							$nova_quantidade = ($rsDados[0]->quantidade -  $_POST['quantidade_material_db'][$i]);
								///UPDATE NA QUANTIDADE DO ESTOQUE
							try { 
									 
								$sql = "UPDATE tbl_materiais SET quantidade=? WHERE id=?";   
								$stm = $this->pdo->prepare($sql);   
								$stm->bindValue(1, $nova_quantidade);   
								$stm->bindValue(2, $_POST['id_material_db'][$i]);   
								$stm->execute(); 
								
							} catch(PDOException $erro){
								echo $erro->getMessage(); 
							}


								try{   
									$sql = "INSERT INTO tbl_relaciona_itens_utilizados (id_tratamento, id_material, quantidade, id_paciente, data_registro, hora_registro, id_usuario) VALUES (?, ?, ?, ?, ?, ?, ?)";   
									$stm = $this->pdo->prepare($sql);   
									$stm->bindValue(1, $id_tratamento);   
									$stm->bindValue(2, $_POST['id_material_db'][$i]);   
									$stm->bindValue(3, $_POST['quantidade_material_db'][$i]);
									$stm->bindValue(4, $id_paciente);
									$stm->bindValue(5, $data_aplicacao);
									$stm->bindValue(6, $hora_aplicacao);
									$stm->bindValue(7, $id_enfermeiro);
									$stm->execute();
								} catch(PDOException $erro){
									echo $erro->getMessage(); 
								}
							}
						}



						///INSERE MEDICAMENTOS NOVOS E DÁ BAIXA NO ESTOQUE
						if(!empty($_POST['id_medicamento'])) {
							$camposMedicamentos = $_POST['id_medicamento'];
							for($i=0; $i<count($camposMedicamentos); $i++) {

									// puxa medicamento
							$sql = "SELECT tbl_materiais.* FROM tbl_materiais where id = ? ";
							$stm = $this->pdo->prepare($sql);
							$stm->bindValue(1, $_POST['id_medicamento'][$i]);			
							$stm->execute();  
							$rsDados = $stm->fetchAll(PDO::FETCH_OBJ);

							$nova_quantidade = ($rsDados[0]->quantidade -  $_POST['quantidade_medicamento'][$i]);
								///UPDATE NA QUANTIDADE DO ESTOQUE
							try { 
									 
								$sql = "UPDATE tbl_materiais SET quantidade=? WHERE id=?";   
								$stm = $this->pdo->prepare($sql);   
								$stm->bindValue(1, $nova_quantidade);   
								$stm->bindValue(2, $_POST['id_medicamento'][$i]);   
								$stm->execute(); 
								
							} catch(PDOException $erro){
								echo $erro->getMessage(); 
							}


								try{   
									$sql = "INSERT INTO tbl_relaciona_itens_utilizados (id_tratamento, id_medicamento, quantidade, id_paciente, data_registro, hora_registro, id_usuario, foi_quebra) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";   
									$stm = $this->pdo->prepare($sql);   
									$stm->bindValue(1, $id_tratamento);   
									$stm->bindValue(2, $_POST['id_medicamento'][$i]);   
									$stm->bindValue(3, $_POST['quantidade_medicamento'][$i]);
									$stm->bindValue(4, $id_paciente);
									$stm->bindValue(5, $data_aplicacao);
									$stm->bindValue(6, $hora_aplicacao);
									$stm->bindValue(7, $id_enfermeiro);
									$stm->bindValue(8, 'S');   
									$stm->execute();
								} catch(PDOException $erro){
									echo $erro->getMessage(); 
								}
							}
						}

						///INSERE MEDICAMENTOS JÁ LISTADOS E DÁ BAIXA NO ESTOQUE
						if(!empty($_POST['id_medicamento_db'])) {
							$camposMedicamentos = $_POST['id_medicamento_db'];
							for($i=0; $i<count($camposMedicamentos); $i++) {

									// puxa medicamento
							$sql = "SELECT tbl_materiais.* FROM tbl_materiais where id = ? ";
							$stm = $this->pdo->prepare($sql);
							$stm->bindValue(1, $_POST['id_medicamento_db'][$i]);			
							$stm->execute();  
							$rsDados = $stm->fetchAll(PDO::FETCH_OBJ);

							$nova_quantidade = ($rsDados[0]->quantidade -  $_POST['quantidade_medicamento_db'][$i]);
								///UPDATE NA QUANTIDADE DO ESTOQUE
							try { 
									 
								$sql = "UPDATE tbl_materiais SET quantidade=? WHERE id=?";   
								$stm = $this->pdo->prepare($sql);   
								$stm->bindValue(1, $nova_quantidade);   
								$stm->bindValue(2, $_POST['id_medicamento_db'][$i]);   
								$stm->execute(); 
								
							} catch(PDOException $erro){
								echo $erro->getMessage(); 
							}


								try{   
									$sql = "INSERT INTO tbl_relaciona_itens_utilizados (id_tratamento, id_medicamento, quantidade, id_paciente, data_registro, hora_registro, id_usuario) VALUES (?, ?, ?, ?, ?, ?, ?)";   
									$stm = $this->pdo->prepare($sql);   
									$stm->bindValue(1, $id_tratamento);   
									$stm->bindValue(2, $_POST['id_medicamento'][$i]);   
									$stm->bindValue(3, $_POST['quantidade_medicamento'][$i]);
									$stm->bindValue(4, $id_paciente);
									$stm->bindValue(5, $data_aplicacao);
									$stm->bindValue(6, $hora_aplicacao);
									$stm->bindValue(7, $id_enfermeiro);
									$stm->execute();
								} catch(PDOException $erro){
									echo $erro->getMessage(); 
								}
							}
						}
						
						////LOG
						try{
							$data_log = date('Y-m-d');
							$hora_log = date('H:i:s');
							$mensagem = "O usuário '".$nome_enfermeiro."' aplicou o tratamento '".$nome_tratamento."' no dia ".formataData($data_log)." as ".$hora_log;
	
							$sql = "INSERT INTO tbl_historicos (id_paciente, id_usuario, ocorrencia, data, hora) VALUES (?, ?, ?, ?, ?)";   
							$stm = $this->pdo->prepare($sql);   
							$stm->bindValue(1, $id_paciente);   
							$stm->bindValue(2, $id_enfermeiro);   
							$stm->bindValue(3, $mensagem);   
							$stm->bindValue(4, $data_log);   
							$stm->bindValue(5, $hora_log);   
							$stm->execute(); 
							$idConteudo = $this->pdo->lastInsertId();
							
						} catch(PDOException $erro){
							echo $erro->getMessage(); 
						}
						/// FIM LOG
					
					if($redireciona == '') {
						$redireciona = 'aplicacoes.php?id='.$id_paciente;
					}
					
					echo "	<script>
							window.location='{$redireciona}';
							</script>";
							exit;
			
			}
		}
		
		function excluir() {
			if(isset($_GET['acao']) && $_GET['acao'] == 'excluirPaciente') {
				
				try{   
					$sql = "DELETE FROM tbl_pacientes WHERE id=? ";   
					$stm = $this->pdo->prepare($sql);   
					$stm->bindValue(1, $_GET['id']);   
					$stm->execute();
				} catch(PDOException $erro){
					echo $erro->getMessage(); 
				}

				header('Location: pacientes.php');
				
			}
		}
	}
	
	$PacientesInstanciada = 'S';
}