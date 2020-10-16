<?php
@session_start();
$AcessoInstanciada = '';
if(empty($AcessoInstanciada)) {
	if(file_exists('Connection/conexao.php')) {
		require_once('Connection/con-pdo.php');
		require_once('funcoes.php');
	} else {
		require_once('../Connection/con-pdo.php');
		require_once('../funcoes.php');
	}
	
	class Acesso {

		private $pdo = null;  
	
		private static $Acesso = null; 

		private function __construct($conexao){  
			$this->pdo = $conexao;  
		}
	  
		public static function getInstance($conexao){   
			if (!isset(self::$Acesso)):    
				self::$Acesso = new Acesso($conexao);   
			endif;
			return self::$Acesso;    
		}
		
		function restritoAdmin() {
			if(empty($_SESSION['admLogado'])) {
				echo "	<script>
						window.location='login.php'
						</script>";
						exit;
			}
		}
		
		function logout() {
			if(isset($_GET['acao']) && $_GET['acao'] == 'logout') {
				unset($_SESSION['admLogado']);
				unset($_SESSION['dadosLogado']);
				unset($_SESSION['id_campanha']);
			}
		}
			
		function login($login, $senha) {
			
			if($login <> '' and $senha <> '') {
				
				try{   

					$login_cpf = $login;
				$login_cpf = str_replace('.', '', $login_cpf);
				$login_cpf = str_replace('-', '', $login_cpf);

				if(is_numeric($login_cpf)){
					$sql = "SELECT * FROM tbl_usuarios where REPLACE(REPLACE(cpf,'.', ''), '-', '') = :login and senha = :senha";
					$stm = $this->pdo->prepare($sql);
					$stm->bindValue(':login', $login_cpf, PDO::PARAM_STR);
					$stm->bindValue(':senha', $senha, PDO::PARAM_STR);
					$stm->execute();   
					$rsDados = $stm->fetchAll(PDO::FETCH_OBJ);
				}else{
					$login_email = $login;
					$sql = "SELECT * FROM tbl_usuarios where email = :login and senha = :senha";
					$stm = $this->pdo->prepare($sql);
					$stm->bindValue(':login', $login_email, PDO::PARAM_STR);
					$stm->bindValue(':senha', $senha, PDO::PARAM_STR);
					$stm->execute();   
					$rsDados = $stm->fetchAll(PDO::FETCH_OBJ);

				}

					
					
					if(!empty($rsDados[0]->id)) {
						if($rsDados[0]->data_frase != date('Y-m-d')){
							$sql = "SELECT * FROM tbl_frases ORDER BY rand() limit 1";
							$stm = $this->pdo->prepare($sql);
							$stm->execute();   
							$rsFrases = $stm->fetchAll(PDO::FETCH_OBJ);

							try{   
								$sql = "UPDATE tbl_usuarios SET data_frase=?, id_frase=?, frase_lida=? WHERE id=?";   
								$stm = $this->pdo->prepare($sql);   
								$stm->bindValue(1, date('Y-m-d'));   
								$stm->bindValue(2, $rsFrases[0]->id);   
								$stm->bindValue(3, 'N');   
								$stm->bindValue(4, $rsDados[0]->id);   
								$stm->execute();  
							} catch(PDOException $erro){   
								echo $erro->getMessage();
							}
						}

						$_SESSION['admLogado'] = 'S';
						$_SESSION['dadosLogado'] = $rsDados[0];
						
						echo "	<script>
								window.location='.';
								</script>";
								exit;
					} else {
						echo "	<script>
								alert('Dados inválidos. Por favor, verifique os dados digitados.');
								window.location='login.php';
								</script>";
								exit;
					}
					
				} catch(PDOException $erro){   
					echo $erro->getMessage(); 
				}
			}
		}
		
		
		
		function rsDados($id='', $idCargo='', $orderBy='', $limite='') {
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

			if(!empty($idCargo)) {
				$sql .= " and id_cargo = ?"; 
				$nCampos++;
				$campo[$nCampos] = $idCargo;
			}
			/// ORDEM		
			if(!empty($orderBy)) {
				$sqlOrdem = " order by {$orderBy}";
			}
			
			if(!empty($limite)) {
				$sqlLimite = " limit 0,{$limite}";
			}
			
			try{   
				$sql = "SELECT * FROM tbl_usuarios where 1=1 $sql $sqlOrdem $sqlLimite";
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


		function Cargo($id='') {
			$sql = '';
			/// FILTROS
			$nCampos = 0;
			
			if(!empty($id)) {
				$sql .= " and id = ?"; 
				$nCampos++;
				$campo[$nCampos] = $id;
			}
			try{   
				$sql = "SELECT * FROM tbl_cargo where 1=1 $sql";
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
			if(isset($_POST['acao']) && $_POST['acao'] == 'addUsuario') {

				/* echo $_FILES['foto']['name'];
				exit; */

				$nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_STRING);
				$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_STRING);
				$telefone = filter_input(INPUT_POST, 'telefone', FILTER_SANITIZE_STRING);
				$endereco = filter_input(INPUT_POST, 'endereco', FILTER_SANITIZE_STRING);
				$cpf = filter_input(INPUT_POST, 'cpf', FILTER_SANITIZE_STRING);
				$login = filter_input(INPUT_POST, 'login', FILTER_SANITIZE_STRING);
				$senha = filter_input(INPUT_POST, 'senha', FILTER_SANITIZE_STRING);
				$id_cargo = filter_input(INPUT_POST, 'id_cargo', FILTER_SANITIZE_STRING);
				$perm_cad_usuario = filter_input(INPUT_POST, 'perm_cad_usuario', FILTER_SANITIZE_STRING);
				$perm_cad_contato = filter_input(INPUT_POST, 'perm_cad_contato', FILTER_SANITIZE_STRING);
				$perm_relatorio = filter_input(INPUT_POST, 'perm_relatorio', FILTER_SANITIZE_STRING);
				$perm_add_usuario = filter_input(INPUT_POST, 'perm_add_usuario', FILTER_SANITIZE_STRING);
				$perm_edit_usuario = filter_input(INPUT_POST, 'perm_edit_usuario', FILTER_SANITIZE_STRING);
				$perm_del_usuario = filter_input(INPUT_POST, 'perm_del_usuario', FILTER_SANITIZE_STRING);
				$perm_edit_contato = filter_input(INPUT_POST, 'perm_edit_contato', FILTER_SANITIZE_STRING);
				$perm_edit_contato_nf = filter_input(INPUT_POST, 'perm_edit_contato_nf', FILTER_SANITIZE_STRING);
				$perm_del_contato = filter_input(INPUT_POST, 'perm_del_contato', FILTER_SANITIZE_STRING);
				$perm_pag_principal_rm = filter_input(INPUT_POST, 'perm_pag_principal_rm', FILTER_SANITIZE_STRING);
				$perm_pag_principal_uc = filter_input(INPUT_POST, 'perm_pag_principal_uc', FILTER_SANITIZE_STRING);
				$sexo = filter_input(INPUT_POST, 'sexo', FILTER_SANITIZE_STRING);
				
				
				// Verificar se já existe:
				// $sql = "SELECT tbl_usuarios.* FROM tbl_usuarios where email = ? or cpf = ? or login = ? ";
				// $stm = $this->pdo->prepare($sql);
				// $stm->bindValue(1, $email);			
				// $stm->bindValue(2, $cpf);
				// $stm->bindValue(3, $login);			
				// $stm->execute();  
				
				// if(count($stm->fetchAll(PDO::FETCH_OBJ)) > 0) {
				// 	echo "	<script>
				// 			alert('E-mail, Login ou CPF já cadastrado. Por favor. Tente cadastrar um inxesistente.');
				// 			history.back();
				// 			</script>";
				// 			exit;
				// } else {
					try{
						
						if(file_exists('Connection/conexao.php')) {
							$pastaArquivos = 'img';
						} else {
							$pastaArquivos = '../img';
						}
						
						$sql = "INSERT INTO tbl_usuarios (nome, email, telefone, endereco, cpf, login, senha, foto, id_cargo, perm_cad_usuario, perm_cad_contato, perm_relatorio, perm_add_usuario, perm_edit_usuario, perm_del_usuario, perm_edit_contato, perm_del_contato, perm_edit_contato_nf, perm_pag_principal_rm, perm_pag_principal_uc, sexo) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";   
						$stm = $this->pdo->prepare($sql);   
						$stm->bindValue(1, $nome);   
						$stm->bindValue(2, $email);   
						$stm->bindValue(3, $telefone);   
						$stm->bindValue(4, $endereco);   
						$stm->bindValue(5, $cpf);   
						$stm->bindValue(6, $login);   
						$stm->bindValue(7, $senha);   
						$stm->bindValue(8, upload('foto', $pastaArquivos, 'N'));   
						$stm->bindValue(9, $id_cargo);
						$stm->bindValue(10, $perm_cad_usuario);
						$stm->bindValue(11, $perm_cad_contato);
						$stm->bindValue(12, $perm_relatorio);
						$stm->bindValue(13, $perm_add_usuario);
						$stm->bindValue(14, $perm_edit_usuario);
						$stm->bindValue(15, $perm_del_usuario);
						$stm->bindValue(16, $perm_edit_contato);
						$stm->bindValue(17, $perm_del_contato);
						$stm->bindValue(18, $perm_edit_contato_nf);
						$stm->bindValue(19, $perm_pag_principal_rm);
						$stm->bindValue(20, $perm_pag_principal_uc);
						$stm->bindValue(21, $sexo);   
						$stm->execute(); 
						$idConteudo = $this->pdo->lastInsertId();
						
						if($redireciona == '') {
							$redireciona = 'usuarios.php';
						}
						
						echo "	<script>
								window.location='{$redireciona}';
								</script>";
								exit;
					} catch(PDOException $erro){
						echo $erro->getMessage(); 
				///	}
				}
			}
		}
	
		function editar() {
			if(isset($_POST['acao']) && $_POST['acao'] == 'editaUsuario') {

				$nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_STRING);
				$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_STRING);
				$telefone = filter_input(INPUT_POST, 'telefone', FILTER_SANITIZE_STRING);
				$endereco = filter_input(INPUT_POST, 'endereco', FILTER_SANITIZE_STRING);
				$cpf = filter_input(INPUT_POST, 'cpf', FILTER_SANITIZE_STRING);
				$login = filter_input(INPUT_POST, 'login', FILTER_SANITIZE_STRING);
				$senha = filter_input(INPUT_POST, 'senha', FILTER_SANITIZE_STRING);
				$id_cargo = filter_input(INPUT_POST, 'id_cargo', FILTER_SANITIZE_STRING);
				$perm_cad_usuario = filter_input(INPUT_POST, 'perm_cad_usuario', FILTER_SANITIZE_STRING);
				$perm_cad_contato = filter_input(INPUT_POST, 'perm_cad_contato', FILTER_SANITIZE_STRING);
				$perm_relatorio = filter_input(INPUT_POST, 'perm_relatorio', FILTER_SANITIZE_STRING);
				$perm_add_usuario = filter_input(INPUT_POST, 'perm_add_usuario', FILTER_SANITIZE_STRING);
				$perm_edit_usuario = filter_input(INPUT_POST, 'perm_edit_usuario', FILTER_SANITIZE_STRING);
				$perm_del_usuario = filter_input(INPUT_POST, 'perm_del_usuario', FILTER_SANITIZE_STRING);
				$perm_edit_contato = filter_input(INPUT_POST, 'perm_edit_contato', FILTER_SANITIZE_STRING);
				$perm_edit_contato_nf = filter_input(INPUT_POST, 'perm_edit_contato_nf', FILTER_SANITIZE_STRING);
				$perm_del_contato = filter_input(INPUT_POST, 'perm_del_contato', FILTER_SANITIZE_STRING);
				$sexo = filter_input(INPUT_POST, 'sexo', FILTER_SANITIZE_STRING);
				$perm_pag_principal_rm = filter_input(INPUT_POST, 'perm_pag_principal_rm', FILTER_SANITIZE_STRING);
				$perm_pag_principal_uc = filter_input(INPUT_POST, 'perm_pag_principal_uc', FILTER_SANITIZE_STRING);
				$id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_STRING);

				if(file_exists('Connection/conexao.php')) {
					$pastaArquivos = 'img';
				} else {
					$pastaArquivos = '../img';
				}

				try{   
					$sql = "UPDATE tbl_usuarios SET nome=?, email=?, telefone=?, endereco=?, cpf=?, login=?, senha=?, id_cargo=?, foto=?, perm_cad_usuario=?, perm_cad_contato=?, perm_relatorio=?, perm_add_usuario=?, perm_edit_usuario=?, perm_del_usuario=?, perm_edit_contato=?, perm_del_contato=?, sexo=?, perm_edit_contato_nf=?, perm_pag_principal_rm=?, perm_pag_principal_uc=? WHERE id=?";   
					$stm = $this->pdo->prepare($sql);   
					$stm->bindValue(1, $nome);   
					$stm->bindValue(2, $email);   
					$stm->bindValue(3, $telefone);   
					$stm->bindValue(4, $endereco);   
					$stm->bindValue(5, $cpf);   
					$stm->bindValue(6, $login);   
					$stm->bindValue(7, $senha);   
					$stm->bindValue(8, $id_cargo);   
					$stm->bindValue(9, upload('foto', $pastaArquivos, 'N'));
					$stm->bindValue(10, $perm_cad_usuario);
					$stm->bindValue(11, $perm_cad_contato);
					$stm->bindValue(12, $perm_relatorio);
					$stm->bindValue(13, $perm_add_usuario);
					$stm->bindValue(14, $perm_edit_usuario);
					$stm->bindValue(15, $perm_del_usuario);
					$stm->bindValue(16, $perm_edit_contato);
					$stm->bindValue(17, $perm_del_contato);
					$stm->bindValue(18, $sexo);
					$stm->bindValue(19, $perm_edit_contato_nf);
					$stm->bindValue(20, $perm_pag_principal_rm);
					$stm->bindValue(21, $perm_pag_principal_uc);
					$stm->bindValue(22, $id);   
					$stm->execute();  
					
					echo "	<script>
							window.location='usuarios.php';
							</script>";
							exit;
				} catch(PDOException $erro){   
					echo $erro->getMessage();
				}
			}
		}

		function excluir() {
			if(isset($_GET['acao']) && $_GET['acao'] == 'excluirUsuarios') {
				
				try{   
					$sql = "DELETE FROM tbl_usuarios WHERE id=? ";   
					$stm = $this->pdo->prepare($sql);   
					$stm->bindValue(1, $_GET['id']);   
					$stm->execute();
				} catch(PDOException $erro){
					echo $erro->getMessage(); 
				}
				/* echo "	<script>
							window.location='usuarios.php';
							</script>";
							exit; */
				
			}
		}
		
	}
	
	$AcessoInstanciada = 'S';
}