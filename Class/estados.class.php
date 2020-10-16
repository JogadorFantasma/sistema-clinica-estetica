<?php
$EstadosInstanciada = '';
if(isset($EstadosInstanciada)) {
	if(file_exists('Connection/conexao.php')) {
		require_once('Connection/con-pdo.php');
		require_once('funcoes.php');
	} else {
		require_once('../Connection/con-pdo.php');
		require_once('../funcoes.php');
	}
	
	class Estados {
		/*  
		* Atributo para conex�o com o banco de dados   
		*/  
		private $pdo = null;  
	
		/*  
		* Atributo est�tico para inst�ncia da pr�pria classe    
		*/  
		private static $Estados = null; 
	
		/*  
		* Construtor da classe como private  
		* @param $conexao - Conex�o com o banco de dados  
		*/  
		private function __construct($conexao){  
			$this->pdo = $conexao;  
		}
	  
		/*
		* M�todo est�tico para retornar um objeto crudBlog    
		* Verifica se j� existe uma inst�ncia desse objeto, caso n�o, inst�ncia um novo    
		* @param $conexao - Conex�o com o banco de dados   
		* @return $AtualizaProcessos - Instancia do objeto AtualizaProcessos    
		*/   
		
		public static function getInstance($conexao){   
			if (!isset(self::$Estados)):    
				self::$Estados = new Estados($conexao);   
			endif;
			return self::$Estados;    
		}
		
		function rsDados() {
			
			try{   
				$sql = "SELECT * FROM dados_estados where 1=1 ";
				$stm = $this->pdo->prepare($sql);
				$stm->execute();   
				$rsDados = $stm->fetchAll(PDO::FETCH_OBJ);
				return($rsDados);
			} catch(PDOException $erro){   
				echo $erro->getLine(); 
			}
		}
		
		function selectEstados($nomeCampo='id_estado', $style="padding:5px;", $selecionado='', $campoLabel='uf', $chamaCidades='', $class='form-control', $disabled='') {
			$estados = $this->rsDados();
			?>
			<select name="<?=$nomeCampo;?>" id="<?=$nomeCampo;?>" class="<?php echo $class;?>" style=" <?=$style;?>" <?php if($chamaCidades == 'S') { ?>onChange="AtualizaJanela('cidades.php?id_estado='+this.value, 'Cidades');"<?php } ?> <?php if($disabled == 'S') { echo "disabled"; } ?> >
				<option value=""></option>
					<?php 
					foreach($estados as $estado) {
					?>
						<option value="<?php echo $estado->id?>" <?php if(isset($selecionado) && !empty($selecionado) && $estado->id == $selecionado) { echo "selected"; } ?>><?php if(isset($estado->uf) && !empty($estado->uf)){echo $estado->uf;}?></option>
					<?php 
					}
					?>
			</select>
        <?php
		}
	}
	
	$EstadosInstanciada = 'S';
}
?>