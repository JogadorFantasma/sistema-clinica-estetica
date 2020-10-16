<?php
@ session_start();
$ContatosInstanciada = '';
if(empty($ContatosInstanciada)) {
	if(file_exists('Connection/conexao.php')) {
		require_once('Connection/con-pdo.php');
		require_once('funcoes.php');
	} else {
		require_once('../Connection/con-pdo.php');
		require_once('../funcoes.php');
	}
	
	class Contatos {
		
		private $pdo = null;  

		private static $Contatos = null; 

		private function __construct($conexao){  
			$this->pdo = $conexao;  
		}
	  
		public static function getInstance($conexao){   
			if (!isset(self::$Contatos)):    
				self::$Contatos = new Contatos($conexao);   
			endif;
			return self::$Contatos;    
		}
		
	
		function rsDados($id='', $orderBy='', $limite='', $veioDeOnde='', $idCampanha='', $idColaborador='', $status='') {
			
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
			if(!empty($veioDeOnde)) {
				$sql .= " and veio_de_onde = ?"; 
				$nCampos++;
				$campo[$nCampos] = $veioDeOnde;
			}

			if(!empty($idCampanha)) {
				$sql .= " and id_campanha = ?"; 
				$nCampos++;
				$campo[$nCampos] = $idCampanha;
			}
			if(!empty($status)) {
				$sql .= " and status = ?"; 
				$nCampos++;
				$campo[$nCampos] = $status;
			}

			if(!empty($idColaborador)) {
				if($idColaborador == 'NA'){
					$sql .= " and id_usuario is NULL"; 	
				}else{
					$sql .= " and id_usuario = ?"; 
					$nCampos++;
					$campo[$nCampos] = $idColaborador;
				}


			}

			if(isset($_POST['buscaNome']) && !empty($_POST['buscaNome'])) {
				$sql .= " and nome like '%{$_POST['buscaNome']}%'"; 
			}

			if(isset($_POST['buscaTelefone']) && !empty($_POST['buscaTelefone'])) {
				$sql .= " and telefone like '%{$_POST['buscaTelefone']}%'"; 
			}
			if(isset($_POST['buscaStatus']) && !empty($_POST['buscaStatus'])) {
				$sql .= " and status = '{$_POST['buscaStatus']}'"; 
			}

			// if(isset($_POST['buscaCampanha']) && !empty($_POST['buscaCampanha'])) {
			// 	$sql .= " and id_campanha = '{$_POST['buscaCampanha']}'"; 
			// }

			if(isset($_POST['dataDeContato']) && !empty($_POST['dataDeContato'])) {
				$sql .= " and data_registro >= '{$_POST['dataDeContato']}'"; 
			}
			if(isset($_POST['dataAteContato']) && !empty($_POST['dataAteContato'])) {
				$sql .= " and data_registro <= '{$_POST['dataAteContato']}'"; 
			}

			if(isset($_POST['dataDeCampanha']) && !empty($_POST['dataDeCampanha'])) {
				$sql .= " and data_registro >= '{$_POST['dataDeCampanha']}'"; 
			}
			if(isset($_POST['dataAteCampanha']) && !empty($_POST['dataAteCampanha'])) {
				$sql .= " and data_registro <= '{$_POST['dataAteCampanha']}'"; 
			}

			if(isset($_GET['dias']) && $_GET['dias'] == 1) {
				$sql .= " and data_registro = CURDATE()"; 
			}

			if(isset($_GET['dias']) && $_GET['dias'] == 7) {
				$sql .= " and data_registro >= NOW() + INTERVAL -7 DAY
				AND data_registro <  NOW() + INTERVAL  0 DAY"; 
			}
			if(isset($_GET['dias']) && $_GET['dias'] == 15) {
				$sql .= " and data_registro >= NOW() + INTERVAL -15 DAY
				AND data_registro <  NOW() + INTERVAL  0 DAY"; 
			}
			if(isset($_GET['dias']) && $_GET['dias'] == 30) {
				$sql .= " and data_registro >= NOW() + INTERVAL -30 DAY
				AND data_registro <  NOW() + INTERVAL  0 DAY"; 
			}

			/// ORDEM		
			if(!empty($orderBy)) {
				$sqlOrdem = " order by {$orderBy}";
			}
			
			if(!empty($limite)) {
				$sqlLimite = " limit 0,{$limite}";
			}
			
			try{   
				$sql = "SELECT * FROM tbl_contato where 1=1 $sql $sqlOrdem $sqlLimite";
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

		function rsRankingFuncionario($idCampanha='', $id='') {
			
			/// FILTROS
			$nCampos = 0;
			$sql = '';
			$sqlLimite = '';
			if(!empty($id)) {
				$sql .= " and id = ?"; 
				$nCampos++;
				$campo[$nCampos] = $id;
			}
			
			if(!empty($idCampanha)) {
				$sql .= " and tbl_contato.id_campanha = ?"; 
				$nCampos++;
				$campo[$nCampos] = $idCampanha;
			}

			if(isset($_POST['dataDeCampanha']) && !empty($_POST['dataDeCampanha'])) {
				$sql .= " and tbl_contato.data_registro >= '{$_POST['dataDeCampanha']}'"; 
			}
			if(isset($_POST['dataAteCampanha']) && !empty($_POST['dataAteCampanha'])) {
				$sql .= " and tbl_contato.data_registro <= '{$_POST['dataAteCampanha']}'"; 
			}

			if(isset($_GET['dias']) && $_GET['dias'] == 1) {
				$sql .= " and tbl_contato.data_registro = CURDATE()"; 
			}

			if(isset($_GET['dias']) && $_GET['dias'] == 7) {
				$sql .= " and tbl_contato.data_registro >= NOW() + INTERVAL -7 DAY
				AND tbl_contato.data_registro <  NOW() + INTERVAL  0 DAY"; 
			}
			if(isset($_GET['dias']) && $_GET['dias'] == 15) {
				$sql .= " and tbl_contato.data_registro >= NOW() + INTERVAL -15 DAY
				AND tbl_contato.data_registro <  NOW() + INTERVAL  0 DAY"; 
			}
			if(isset($_GET['dias']) && $_GET['dias'] == 30) {
				$sql .= " and tbl_contato.data_registro >= NOW() + INTERVAL -30 DAY
				AND tbl_contato.data_registro <  NOW() + INTERVAL  0 DAY"; 
			}

			if(!empty($limite)) {
				$sqlLimite = " limit 0,{$limite}";
			}
		
			try{   
				$sql = "SELECT tbl_usuarios.nome as nomeUsuario, tbl_usuarios.id as idUsuario, tbl_usuarios.foto as fotoUsuario, tbl_usuarios.sexo as sexoUsuario, count(tbl_contato.id_usuario) as quantidade_fechado FROM tbl_contato left join tbl_usuarios on tbl_contato.id_usuario = tbl_usuarios.id WHERE tbl_contato.status = 'NF' $sql GROUP BY (tbl_contato.id_usuario) ORDER BY count(tbl_contato.id_usuario) DESC";
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

		function rsPlano($id='', $orderBy='', $limite='') {
			
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
				$sql = "SELECT * FROM tbl_planos where 1=1 $sql $sqlOrdem $sqlLimite";
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
			if(isset($_POST['acao']) && $_POST['acao'] == 'addContato') {

				
				$nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_STRING);
				$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_STRING);
				$telefone = filter_input(INPUT_POST, 'telefone', FILTER_SANITIZE_STRING);
				$data_registro = date('Y-m-d');
				$hora_registro = date('H:i:s');
				$veio_de_onde1 = filter_input(INPUT_POST, 'veio_de_onde', FILTER_SANITIZE_STRING);
				$status = "EA";
				$id_campanha = filter_input(INPUT_POST, 'id_campanha', FILTER_SANITIZE_STRING);
				$cod_colaborador = filter_input(INPUT_POST, 'cod_colaborador', FILTER_SANITIZE_STRING);
				

				if($veio_de_onde1 == 'NI'){
					$veio_de_onde = 'NI';
				}else{
					$veio_de_onde = strtoupper(substr($veio_de_onde1,0,1));
				}
				
				if(isset($nome) && empty($nome)) {

					echo "	<script>
							alert('Favor inserir seu nome.');
							window.location='.';
							</script>";
							exit;
				}
				if(isset($telefone) && empty($telefone)) {

					echo "	<script>
							alert('Favor inserir seu telefone.');
							window.location='.';
							</script>";
							exit;
				}

				// Verificar se já existe:
				$sql = "SELECT * FROM tbl_contato where telefone = ?";
				$stm = $this->pdo->prepare($sql);
				$stm->bindValue(1, $telefone);
				$stm->execute();  
				
				if(count($stm->fetchAll(PDO::FETCH_OBJ)) > 0) {
					echo "	<script>
							alert('Seu telefone ja esta cadastrado na nossa base de dados. Ligaremos assim que possivel.');
							window.location='.';
							</script>";
							exit;
				} else {
					try{
						
						$sql = "INSERT INTO tbl_contato (nome, email, telefone, data_registro, hora_registro, veio_de_onde, status, id_campanha, id_influenciador) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";   
						$stm = $this->pdo->prepare($sql);   
						$stm->bindValue(1, $nome);   
						$stm->bindValue(2, $email);   
						$stm->bindValue(3, $telefone);   
						$stm->bindValue(4, $data_registro);
						$stm->bindValue(5, $hora_registro);
						$stm->bindValue(6, $veio_de_onde);
						$stm->bindValue(7, $status);
						$stm->bindValue(8, $id_campanha);
						$stm->bindValue(9, $cod_colaborador);   
						$stm->execute(); 
						$idTratamento = $this->pdo->lastInsertId();
						
						
					} catch(PDOException $erro){
						echo $erro->getMessage(); 
					}
					echo "	<script>
								window.location='msg_enviado.html';
								</script>";
								exit;
				}
			}
		}
		
		function editar($redireciona='contatos.php') {
			if(isset($_POST['acao']) && $_POST['acao'] == 'editaContato') {

				

				$nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_STRING);
				$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_STRING);
				$cpf = filter_input(INPUT_POST, 'cpf', FILTER_SANITIZE_STRING);
				$telefone = filter_input(INPUT_POST, 'telefone', FILTER_SANITIZE_STRING);
				$endereco = filter_input(INPUT_POST, 'endereco', FILTER_SANITIZE_STRING);
				$sexo = filter_input(INPUT_POST, 'sexo', FILTER_SANITIZE_STRING);
				$veio_de_onde = filter_input(INPUT_POST, 'veio_de_onde', FILTER_SANITIZE_STRING);
				$status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_STRING);
				$observacao = filter_input(INPUT_POST, 'observacao', FILTER_SANITIZE_STRING);
				$data_registro = filter_input(INPUT_POST, 'data_registro', FILTER_SANITIZE_STRING);
				$hora_registro = filter_input(INPUT_POST, 'hora_registro', FILTER_SANITIZE_STRING);
				$id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_STRING);
				$id_campanha = filter_input(INPUT_POST, 'id_campanha', FILTER_SANITIZE_STRING);
				$id_usuario = filter_input(INPUT_POST, 'id_usuario', FILTER_SANITIZE_STRING);
				$data_venda = filter_input(INPUT_POST, 'data_venda', FILTER_SANITIZE_STRING);
				$cod_aluno = filter_input(INPUT_POST, 'cod_aluno', FILTER_SANITIZE_STRING);
				$data_atualizacao = date('Y-m-d');
				$hora_atualizacao = date('H:i:s');
				if(isset($status) && $status == 'NF' OR $status == 'NFO') {
				if(isset($cpf) && empty($cpf)) {

					echo "	<script>
							alert('Favor inserir cpf.');
							history.back();
							</script>";
							exit;
				}
				if(isset($email) && empty($email)) {

					echo "	<script>
							alert('Favor inserir e-mail.');
							history.back();
							</script>";
							exit;
				}
				if(isset($data_venda) && empty($data_venda)) {

					echo "	<script>
							alert('Favor inserir data da venda.');
							history.back();
							</script>";
							exit;
				}
			}

				// 	//// LOG
				try{   
					$sql = "SELECT * FROM tbl_usuarios where 1=1 and id = $id_usuario ";
					$stm = $this->pdo->prepare($sql);
					$stm->execute();   
					$rsBox1 = $stm->fetchAll(PDO::FETCH_OBJ);

				} catch(PDOException $erro){   
					echo $erro->getLine(); 
				}

				try{   
					$sql = "SELECT * FROM tbl_contato where 1=1 and id = $id ";
					$stm = $this->pdo->prepare($sql);
					$stm->execute();   
					$rsPesquisa = $stm->fetchAll(PDO::FETCH_OBJ);

				} catch(PDOException $erro){   
					echo $erro->getLine(); 
				}
				$mensagem = '';
				if($rsPesquisa[0]->status <> $status){
					$mensagem = "O usuário ".$rsBox1[0]->nome." mudou o status para ".exibe_status_contato($status)." - ".formataData($data_atualizacao)."-".$hora_atualizacao;

					try{   
						$sql = "INSERT INTO tbl_historicos (id_contato, id_usuario, ocorrencia, data, hora) VALUES (?, ?, ?, ?, ?)";   
						$stm = $this->pdo->prepare($sql);   
						$stm->bindValue(1, $id);   
						$stm->bindValue(2, $id_usuario);   
						$stm->bindValue(3, $mensagem);
						$stm->bindValue(4, $data_atualizacao);   
						$stm->bindValue(5, $hora_atualizacao);   
						$stm->execute(); 
						$idProduto = $this->pdo->lastInsertId();
					} catch(PDOException $erro){
						echo $erro->getMessage(); 
					}	
				}
				if($rsPesquisa[0]->nome <> $nome){
					$mensagem = "O usuário ".$rsBox1[0]->nome." mudou o nome para ".$nome." - ".formataData($data_atualizacao)."-".$hora_atualizacao;

					try{   
						$sql = "INSERT INTO tbl_historicos (id_contato, id_usuario, ocorrencia, data, hora) VALUES (?, ?, ?, ?, ?)";   
						$stm = $this->pdo->prepare($sql);   
						$stm->bindValue(1, $id);   
						$stm->bindValue(2, $id_usuario);   
						$stm->bindValue(3, $mensagem);
						$stm->bindValue(4, $data_atualizacao);   
						$stm->bindValue(5, $hora_atualizacao);   
						$stm->execute(); 
						$idProduto = $this->pdo->lastInsertId();
					} catch(PDOException $erro){
						echo $erro->getMessage(); 
					}	
				}
				if($rsPesquisa[0]->email <> $email){
					$mensagem = "O usuário ".$rsBox1[0]->nome." mudou o e-mail para ".$email." - ".formataData($data_atualizacao)."-".$hora_atualizacao;

					try{   
						$sql = "INSERT INTO tbl_historicos (id_contato, id_usuario, ocorrencia, data, hora) VALUES (?, ?, ?, ?, ?)";   
						$stm = $this->pdo->prepare($sql);   
						$stm->bindValue(1, $id);   
						$stm->bindValue(2, $id_usuario);   
						$stm->bindValue(3, $mensagem);
						$stm->bindValue(4, $data_atualizacao);   
						$stm->bindValue(5, $hora_atualizacao);   
						$stm->execute(); 
						$idProduto = $this->pdo->lastInsertId();
					} catch(PDOException $erro){
						echo $erro->getMessage(); 
					}	
				}

				if($rsPesquisa[0]->endereco <> $endereco){
					$mensagem = "O usuário ".$rsBox1[0]->nome." mudou o endereço para ".$endereco." - ".formataData($data_atualizacao)."-".$hora_atualizacao;

					try{   
						$sql = "INSERT INTO tbl_historicos (id_contato, id_usuario, ocorrencia, data, hora) VALUES (?, ?, ?, ?, ?)";   
						$stm = $this->pdo->prepare($sql);   
						$stm->bindValue(1, $id);   
						$stm->bindValue(2, $id_usuario);   
						$stm->bindValue(3, $mensagem);
						$stm->bindValue(4, $data_atualizacao);   
						$stm->bindValue(5, $hora_atualizacao);   
						$stm->execute(); 
						$idProduto = $this->pdo->lastInsertId();
					} catch(PDOException $erro){
						echo $erro->getMessage(); 
					}	
				}
				if($rsPesquisa[0]->sexo <> $sexo){
					$mensagem = "O usuário ".$rsBox1[0]->nome." mudou o sexo para ".$sexo." - ".formataData($data_atualizacao)."-".$hora_atualizacao;

					try{   
						$sql = "INSERT INTO tbl_historicos (id_contato, id_usuario, ocorrencia, data, hora) VALUES (?, ?, ?, ?, ?)";   
						$stm = $this->pdo->prepare($sql);   
						$stm->bindValue(1, $id);   
						$stm->bindValue(2, $id_usuario);   
						$stm->bindValue(3, $mensagem);
						$stm->bindValue(4, $data_atualizacao);   
						$stm->bindValue(5, $hora_atualizacao);   
						$stm->execute(); 
						$idProduto = $this->pdo->lastInsertId();
					} catch(PDOException $erro){
						echo $erro->getMessage(); 
					}	
				}
				if($rsPesquisa[0]->cpf <> $cpf){
					$mensagem = "O usuário ".$rsBox1[0]->nome." mudou o cpf para ".$cpf." - ".formataData($data_atualizacao)."-".$hora_atualizacao;

					try{   
						$sql = "INSERT INTO tbl_historicos (id_contato, id_usuario, ocorrencia, data, hora) VALUES (?, ?, ?, ?, ?)";   
						$stm = $this->pdo->prepare($sql);   
						$stm->bindValue(1, $id);   
						$stm->bindValue(2, $id_usuario);   
						$stm->bindValue(3, $mensagem);
						$stm->bindValue(4, $data_atualizacao);   
						$stm->bindValue(5, $hora_atualizacao);   
						$stm->execute(); 
						$idProduto = $this->pdo->lastInsertId();
					} catch(PDOException $erro){
						echo $erro->getMessage(); 
					}	
				}
				if($rsPesquisa[0]->telefone <> $telefone){
					$mensagem = "O usuário ".$rsBox1[0]->nome." mudou o telefone para ".$telefone." - ".formataData($data_atualizacao)."-".$hora_atualizacao;

					try{   
						$sql = "INSERT INTO tbl_historicos (id_contato, id_usuario, ocorrencia, data, hora) VALUES (?, ?, ?, ?, ?)";   
						$stm = $this->pdo->prepare($sql);   
						$stm->bindValue(1, $id);   
						$stm->bindValue(2, $id_usuario);   
						$stm->bindValue(3, $mensagem);
						$stm->bindValue(4, $data_atualizacao);   
						$stm->bindValue(5, $hora_atualizacao);   
						$stm->execute(); 
						$idProduto = $this->pdo->lastInsertId();
					} catch(PDOException $erro){
						echo $erro->getMessage(); 
					}	
				}
				if($rsPesquisa[0]->observacao <> $observacao){
					$mensagem = "O usuário ".$rsBox1[0]->nome." mudou a observação para ".$observacao." - ".formataData($data_atualizacao)."-".$hora_atualizacao;

					try{   
						$sql = "INSERT INTO tbl_historicos (id_contato, id_usuario, ocorrencia, data, hora) VALUES (?, ?, ?, ?, ?)";   
						$stm = $this->pdo->prepare($sql);   
						$stm->bindValue(1, $id);   
						$stm->bindValue(2, $id_usuario);   
						$stm->bindValue(3, $mensagem);
						$stm->bindValue(4, $data_atualizacao);   
						$stm->bindValue(5, $hora_atualizacao);   
						$stm->execute(); 
						$idProduto = $this->pdo->lastInsertId();
					} catch(PDOException $erro){
						echo $erro->getMessage(); 
					}	
				}

			
				try { 
				
					$sql = "UPDATE tbl_contato SET nome=?, email=?, cpf=?, telefone=?, endereco=?, sexo=?, veio_de_onde=?, status=?, observacao=?, data_registro=?, hora_registro=?, id_campanha=?, id_usuario=?, data_venda=?, cod_aluno=? WHERE id=?";   
					$stm = $this->pdo->prepare($sql);   
					$stm->bindValue(1, $nome);   
					$stm->bindValue(2, $email);   
					$stm->bindValue(3, $cpf);   
					$stm->bindValue(4, $telefone); 
					$stm->bindValue(5, $endereco);
					$stm->bindValue(6, $sexo);   
					$stm->bindValue(7, $veio_de_onde);   
					$stm->bindValue(8, $status);   
					$stm->bindValue(9, $observacao); 
					$stm->bindValue(10, $data_registro);
					$stm->bindValue(11, $hora_registro);   
					$stm->bindValue(12, $id_campanha);   
					$stm->bindValue(13, $id_usuario);
					$stm->bindValue(14, $data_venda);
					$stm->bindValue(15, $cod_aluno);
					$stm->bindValue(16, $id);   
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
			if(isset($_GET['acao']) && $_GET['acao'] == 'excluirContato') {
				
				try{   
					$sql = "DELETE FROM tbl_contato WHERE id=? ";   
					$stm = $this->pdo->prepare($sql);   
					$stm->bindValue(1, $_GET['id']);   
					$stm->execute();
				} catch(PDOException $erro){
					echo $erro->getMessage(); 
				}

			}
		}
	}
	
	$ContatosInstanciada = 'S';
}