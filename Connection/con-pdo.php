<?php
$conInstanciada = '';
if(empty($conInstanciada)) {
	/*   
	* Constantes de parâmetros para configuração da conexão   
	*/ 
	include('conexao.php');
	
	define('HOST', $hostname_conexao);   
	define('DBNAME', $database_conexao);   
	define('CHARSET', 'utf8');   
	define('USER', $username_conexao);   
	define('PASSWORD', $password_conexao);   
	
	class Conexao {  
		/*   
		* Atributo estático para instância do PDO   
		*/   
		private static $pdo;  
		
		/*   
		* Escondendo o construtor da classe   
		*/   
		private function __construct() {   
		//   
		}  
		
		/*   
		* Método estático para retornar uma conexão válida   
		* Verifica se já existe uma instância da conexão, caso não, configura uma nova conexão   
		*/   
		public static function getInstance() {   
		if (!isset(self::$pdo)) {   
		 try {   
		  $opcoes = array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8', PDO::ATTR_PERSISTENT => TRUE);   
		  self::$pdo = new PDO("mysql:host=" . HOST . "; dbname=" . DBNAME . "; charset=" . CHARSET . ";", USER, PASSWORD, $opcoes);
		  self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);   
		 } catch (PDOException $e) {   
		  print "Erro: " . $e->getMessage();   
		 }   
		}   
		return self::$pdo;   
		}   
	}
	$conInstanciada = 'S';
}

if(!function_exists("trataAtaqueXSS")) {
	function trataAtaqueXSS($data) {
		
		// Fix &entity\n;
		$data = str_replace(array('&amp;','&lt;','&gt;'), array('&amp;amp;','&amp;lt;','&amp;gt;'), $data);
		$data = preg_replace('/(&#*\w+)[\x00-\x20]+;/u', '$1;', $data);
		$data = preg_replace('/(&#x*[0-9A-F]+);*/iu', '$1;', $data);
		$data = html_entity_decode($data, ENT_COMPAT, 'UTF-8');
		
		// Remove any attribute starting with "on" or xmlns
		$data = preg_replace('#(<[^>]+?[\x00-\x20"\'])(?:on|xmlns)[^>]*+>#iu', '$1>', $data);
		
		// Remove javascript: and vbscript: protocols
		$data = preg_replace('#([a-z]*)[\x00-\x20]*=[\x00-\x20]*([`\'"]*)[\x00-\x20]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2nojavascript...', $data);
		$data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2novbscript...', $data);
		$data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*-moz-binding[\x00-\x20]*:#u', '$1=$2nomozbinding...', $data);
		
		// Only works in IE: <span style="width: expression(alert('Ping!'));"></span>
		$data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?expression[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
		$data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?behaviour[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
		$data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:*[^>]*+>#iu', '$1>', $data);
		
		// Remove namespaced elements (we do not need them)
		$data = preg_replace('#</*\w+:\w[^>]*+>#i', '', $data);
		
		do {
			// Remove really unwanted tags
			$old_data = $data;
			$data = preg_replace('#</*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|i(?:frame|layer)|l(?:ayer|ink)|meta|object|s(?:cript|tyle)|title|xml)[^>]*+>#i', '', $data);
		} while ($old_data !== $data);
		
		// we are done...
		return $data;
	}
}

foreach($_GET as $key => $value){
	if(!is_array($_GET[$key])) {
		$_GET[$key] = trataAtaqueXSS($value);
	}
}

foreach($_POST as $key => $value){
	if(!is_array($_POST[$key])) {
		$_POST[$key] = trataAtaqueXSS($value);
	}
}