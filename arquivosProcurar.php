<?

//	/www/v/N.NN/__essenciais/utilitarios/arquivosProcurar.php / perm=735370

// 	PROCURAR DE PALAVRAS CHAVES EM ARQUIVOS DE DETERMINADO DIRETORIO

// 	========================================v=============================================================== //

$permissao_raiz 														= 735300;
$permissao_desta_pag 												= 735370;

			
if( !permVerificar($permissao_desta_pag, false, false, true) ){
	
	// nao tem permissao.
	require_once($var_SESSION['pathHost']			. "/__root_essenciais/redirect_403.php");
	exit();

}

// 	========================================v=============================================================== //

?>

<meta http-equiv="content-type" content="text/html;charset=utf-8" />

<? $atualizarLinks = (!empty($_COOKIE['atz'])) ? "?atz=".$_COOKIE['atz'] : ""; ?>

<link href="<?= $var_SESSION["ste_Link"] ?>_css/multipleSelect/multiple-select.css<?= $atualizarLinks ?>" type="text/css" rel="stylesheet" />

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js" type="text/javascript"></script>
<script src="<?= $var_SESSION["urlVersao"] 	. "_js/multipleSelect/multiple-select.js$atualizarLinks" ?>" type="text/javascript"></script>

<? 
	
echo codigoFonteCaminho($permissao_desta_pag, dirname(__FILE__)."/".basename( __FILE__ ),"<br>" );

//include_once($_SERVER['DOCUMENT_ROOT'] . "/config.php");	// aqui tambem chama o ipsLiberados
 
// 	========================================v=============================================================== //
			
$pasta 																			= ROOT."/";
			
$resultadoProcura["numArquivos"] 						= 0;
			
// 	========================================v=============================================================== //			

//	BUSCAR A LINHA E O TRECHO DA PALAVRA BUSCADA
function buscarLinhaTrecho($conteudo, $palavraChave, $caseSensitive)
{
	$retorno = [];
	$conteudo_exp = explode("\n", str_replace(["?php"], " ", $conteudo));

	foreach ($conteudo_exp as $lin_num => $lin_codigo) 
	{
		$lin_codigo = htmlentities(strval($lin_codigo));
		if( ($caseSensitive and strrpos($lin_codigo,$palavraChave) !== false and !empty($lin_codigo)) OR
		 	(!$caseSensitive and strripos($lin_codigo,$palavraChave) !== false and !empty($lin_codigo)) )
		{
			$retorno[] = [
				'numero' => $lin_num,
				'codigo' => $lin_codigo
			];
		}
	}

	return $retorno;
}

//	VARRE TODO OS SUBDIRETORIOS
function subDiretorioVarrer($diretorioArray, $subPasta = "", $ignorar_Versao = false){
		
	global $pasta;
	global $arquivo;
	global $resultadoProcura;
	global $palavraChave;
	global $resultadoProcura;
	global $caseSensitive;
	global $opcaoProcura;

	$subPasta 	= ( !empty($subPasta) )? $subPasta."/" : "";
	
	foreach( $diretorioArray as $val ){

		if( strripos($val, ".php") !== false or strripos($val, ".js") !== false or strripos($val, ".css") !== false or strripos($val, ".ofx") !== false ){ // SE FOR UM ARQUIVO, FAZ O PROCESSO DE BUSCA
			
			copy(ROOT."/_temp/index.php", ROOT."/_temp/procurar.txt");
			chmod(ROOT."/_temp/procurar.txt", 0644);
			
			copy($pasta.$arquivo."/".$subPasta.$val, ROOT."/_temp/procurar.txt");
			
			$conteudo 														= file_get_contents(ROOT."/_temp/procurar.txt");
			
			$rr 															.= $pasta.$arquivo."/".$subPasta.$val.'<br>';
			
			$resultadoProcura["numArquivos"]			+= 1;
			
			if( $caseSensitive and strrpos($val,$palavraChave) !== false and in_array($opcaoProcura,array("nc","n")) ){
				
				$resultadoProcura["encontrados"][] 	= [
					"$val - (Nome do arquivo)"
				];
				
			}else if( !$caseSensitive and strripos($val,$palavraChave) !== false and in_array($opcaoProcura,array("nc","n")) ){
				
				$resultadoProcura["encontrados"][] 	= [
					"<i style='color:gray'>". $pasta ."</i><i style='color:blue'>".$arquivo."/".$subPasta.$val." - (Nome do arquivo)</i>"
				];
				
			}

			if( $caseSensitive and strrpos($conteudo,$palavraChave) !== false and in_array($opcaoProcura,array("nc","c")) ){
				
				$resultadoProcura["encontrados"][] 	= [
					"<i style='color:gray'>". $pasta ."</i><i style='color:blue'>".$arquivo."/".$subPasta.$val."</i><br/>",
					buscarLinhaTrecho($conteudo, $palavraChave, $caseSensitive)
				];
				
			}else if( !$caseSensitive and strripos($conteudo,$palavraChave) !== false and in_array($opcaoProcura,array("nc","c")) ){

				$resultadoProcura["encontrados"][] 	= [
					"<i style='color:gray'>". $pasta ."</i><i style='color:blue'>".$arquivo."/".$subPasta.$val."</i>",
					buscarLinhaTrecho($conteudo, $palavraChave, $caseSensitive)
				];
				
			}else{
				
				$resultadoProcura['nEncontrados'][] = "<i style='color:gray'>".substr($pasta, 14).$arquivo."/".$subPasta.$val."</i><br/>";
				
			}
 
		}else if( $val != ".." and $val != "." ){// SE FOR UM DIRETORIO, VARRE SEU DIRETORIO E CHAMA PARA A PROPRIA FUNCAO
			
			$pasta1 															= scandir($pasta.$arquivo."/".$subPasta.$val);
			
			subDiretorioVarrer($pasta1, $subPasta.$val);
			
		}
		
	}
	
	return $rr;
	
}
			
// 	========================================v=============================================================== //
			
?>

<form action="" method="post" style="margin-top: 30px; margin-left: 30px;">
	
	<div>
		
		<?
		
		// 	====================================v=============================================================== //
	
		$pathV 																= ROOT."/v";
		$arrVersao 															= array();
		$diretorio2 														= dir($pathV);

		while($arquivo2 = $diretorio2->read()){

			if( is_numeric($arquivo2) ){

				$arrVersao[] 												= $arquivo2;

			}

		}
		
		rsort($arrVersao);

		// 	====================================v=============================================================== //
		
		$arr_caminhosCookie = ($_COOKIE['caminhosSelecionados'])??("['v/".$arrVersao[0]."']");
		
		?>

		Procurar: <input type="text" name="palavraChave" value="<? echo $_REQUEST['palavraChave']  ?>" />&nbsp;
		
		<select name="opcaoProcura">
			
			<option value="c" <? echo ($_REQUEST['opcaoProcura']=="c")?"selected='selected'":""; ?>>Somente no Conteúdo do Arquivo</option>
			<option value="n" <? echo ($_REQUEST['opcaoProcura']=="n")?"selected='selected'":""; ?>>Somente no Nome do Arquivo</option>
			<option value="nc" <? echo ($_REQUEST['opcaoProcura']=="nc")?"selected='selected'":""; ?>>Nome + Conteúdo do Arquivo</option>
			
		</select>
		
		&nbsp;<input type="checkbox" name="case-sensitive" <? echo ($_REQUEST['case-sensitive']) ? "checked='checked'" : ""; ?> /> Case Sensitive
		<br/><br/>
		
		Caminho: 	
	
		<select id="multiselect" name="caminhoInicio[]" class="multiselect campo_filtro " tyle="width: 315px; display: none;" onchange="ultimaVersaoJS(this.value,'<? echo $arrVersao[0] ?>')">

			<option value="v/" 
							<? echo (in_array("v/", $arr_caminhosCookie)) ? "selected='selected'" : ""; ?>>/www/v/[n.nn] [digite a versao desejada] (opcional)	
			</option>
			
			<option value="v/<? echo $arrVersao[0] ?>" 
							<? echo (in_array("v/".$arrVersao[0], $arr_caminhosCookie) or empty($arr_caminhosCookie)) ? "selected='selected'" : ""; ?>>/www/v/<? echo $arrVersao[0] ?>/ -R (última versão do site, incluindo subpastas)
			</option>
		
			<option value="v/<? echo $arrVersao[0] ?>/_templates/" 
							<? echo (in_array("v/".$arrVersao[0]."/_templates/", $arr_caminhosCookie)) ? "selected='selected'" : ""; ?>>/www/v/<? echo $arrVersao[0] ?>/_templates/ -R (template específico, incluindo subpastas)
			</option>
		
			<option value="/" <? echo (in_array("/", $arr_caminhosCookie)) ? "selected='selected'" : ""; ?>>/www/* (somente a raiz, excluindo subpastas)</option>
			
			<option value="" <? echo (in_array("", $arr_caminhosCookie)) ? "selected='selected'" : ""; ?>>/www/ -R (tudo, desde a raiz - incluindo versões /v/)</option>
			<option value="-v" <? echo (in_array("-v", $arr_caminhosCookie)) ? "selected='selected'" : ""; ?>>/www/ -R (tudo, desde a raiz - exceto versões /v/)</option>
			
			<option value="__root_" <? echo (in_array("__root_", $arr_caminhosCookie)) ? "selected='selected'" : ""; ?>>/___root_[digite a subpasta] (opcional)</option>
			<option value="___sites/" <? echo (in_array("___sites/", $arr_caminhosCookie)) ? "selected='selected'" : ""; ?>>/___site_/[digite a subpasta] (opcional)</option>
			<option value="___projetos/" <? echo (in_array("___projetos/", $arr_caminhosCookie)) ? "selected='selected'" : ""; ?>>/___projetos_/[digite a subpasta] (opcional)</option>

		</select>

		<input type="text" name="caminho_input" value="<? echo $_REQUEST['caminho_input']  ?>" />/<br/>
		
		<? echo "<br>Versões disponíveis: Site (".implode(", ", $arrVersao).")";  ?>
		
		<br/>
		<br/>
		
		<input type="submit" value="Procurar" />

	</div>
	
</form>

<script>
	
	const urlVersao = "<?= $var_SESSION['urlVersao']; ?>";
	$.getScript(urlVersao+"painel/_js/cookie.js");

	// 	======================================v=============================================================== //
	// Cria o multiselect
	$("#multiselect").multipleSelect({		
		name		: 'teste',
		placeholder	: "Selecionar",
		filter		: 1,
		selectAll	: 0,
		
		onClick: function() 
		{
			let selecionados = $("#multiselect").multipleSelect("getSelects");
			setCookie('caminhosSelecionados',JSON.stringify(selecionados),3);
		},		
	})
	.multipleSelect('setSelects', <?=$arr_caminhosCookie?>);
	
	$('[data-name="selectItemcaminhoInicio[]"]').attr("name", "selectItemcaminhoInicio[]");
	
	// 	======================================v=============================================================== //
	//	Autopreenche o caminho com a ultima versao do site ou painel
	
	function ultimaVersaoJS(val, versaoS, versaoP){
		
		if(val == "v/"){
			
			document.getElementsByName("caminho_input")[0].value 			= versaoS;
			
		}else if(val == "painel/"){
			
			document.getElementsByName("caminho_input")[0].value 			= versaoP;
			
		}else if(val == "/"){
			
			document.getElementsByName("caminho_input")[0].value 			= "";
				
		}else{
			
			document.getElementsByName("caminho_input")[0].value 			= "";
			
		}
		
	};
	
	// 	======================================v=============================================================== //
	
</script>

<?

// 	========================================v=============================================================== //

if( !empty($_REQUEST['palavraChave']) ){
	
	// 	======================================v=============================================================== //
	
	$resultadoProcura 												= array();
	
	$palavraChave 														= $_REQUEST['palavraChave'];
	$caminhoInicio 														= $_REQUEST['caminhoInicio'];
	$caseSensitive 														= $_REQUEST['case-sensitive'];
	$opcaoProcura 														= $_REQUEST['opcaoProcura'];
	
	$resultadoProcura["numArquivos"] 					= 0;
		
	echo "<br>Buscar por <i><strong style='color:#FF0000'>'".$palavraChave."'</strong></i></br>";
	
	// 	======================================v=============================================================== //
	// Tratamento para as pastas que comecao com "__root_"
	
	if(in_array("__root_", $_REQUEST['selectItemcaminhoInicio']) && empty($_REQUEST['caminho_input'])){
		
		$rootDir 																= glob(ROOT.'/__root_*' , GLOB_ONLYDIR);
		
		unset($_REQUEST['selectItemcaminhoInicio'][array_search('__root_', $_REQUEST['selectItemcaminhoInicio'])]);
		
		$_REQUEST['selectItemcaminhoInicio'] 		= array_merge($_REQUEST['selectItemcaminhoInicio'], $rootDir);
		
	}
	
	
	// 	======================================v=============================================================== //
	
	foreach( $_REQUEST['selectItemcaminhoInicio'] as $c ){
		
		// 	====================================v=============================================================== //
		//	VERIFICAR SE É A OPCAO 'IGNORAR VERSAO'
		$ignorar_Versao														= false;
		if( $c == "-v" )
		{
			$c 																= "";					
			$ignorar_Versao													= true;			
		}

		// 	======================================v=============================================================== //
		//	Tratamento para as pastas que comecao com "__root_"
		$c 																	= str_replace("/mnt/hd-codigo/www/", "", $c);
		
		$separador 															= ( strripos($_REQUEST['caminho_input'], "__root_") !== false ) 
																								? "/" 
																								: "";
		
		$pasta 																= ROOT;
		
		$pasta																.= ( $c ) 
																								 ? "/".$c 
																								 : "";
		
		$pasta																.= ( trim($_REQUEST['caminho_input'], "/") ) 
																								 ? $separador.trim($_REQUEST['caminho_input'], "/") 
																								 : "";
		
		$pasta																.= "/";

		// 	====================================v=============================================================== //
		
		echo " Local: '<strong style='color:#FF0000'>".$pasta."</strong>'<br />"; 
		
		// 	====================================v=============================================================== //
		
		if(is_dir($pasta)){ // SE O CAMINHO DIGITADO FOR UMA PASTA
			
			// 	==================================v=============================================================== //
			
			$diretorio 														= dir($pasta);
			
			// 	==================================v=============================================================== //
			
			while($arquivo = $diretorio->read()){
				
				$sub 																= $pasta.$arquivo;

				// 	================================v=============================================================== //
				
				if($caminhoInicio == "/"){
					
					if(is_dir($sub)){
						
						continue;
						
					}
					
				}
				
				// 	================================v=============================================================== //
				
				if( ($ignorar_Versao and strripos($sub, "/mnt/hd-codigo/www/v") !== false) or strripos($sub, "/mnt/hd-codigo/www/divulgacao.com") !== false || strripos($sub, "divulgacao.com/v") !== false || strripos($sub, "divulgacao.com//painel") !== false || strripos($sub, "/mnt/hd-codigo/www/___projetos") !== false || strripos($sub, "/mnt/hd-codigo/www/___sites") !== false ){
					
					echo "<br/> Ignorei a pasta: ".$sub;
					continue;
					
				}

				// 	================================v=============================================================== //
				
				if( is_dir($pasta.$arquivo) and $arquivo != ".." and $arquivo != "." ){ 			// SE FOR UM DIRETORIO
					
					$dir 														= $pasta.$arquivo;
					$file 														= scandir($dir);
					
					subDiretorioVarrer($file, "", $ignorar_Versao);

				}

				// 	================================v=============================================================== //
				
			}

			// 	==================================v=============================================================== //
			///	FECHA O ARQUIVO
			
			$diretorio->close();

		}
		else{ // CASO NÃO ENCONTRE O DIRETORIO
			
			echo 'A pasta não existe: '.$pasta;

		}
		
		// 	====================================v=============================================================== //
		
	}

	// 	======================================v=============================================================== //
	
	echo "<hr/>Total de Arquivos Pesquisados: <b><i style='color:#FF0000'>".$resultadoProcura["numArquivos"]."</i></b>";
	echo "<br/><hr/>";

	// 	======================================v=============================================================== //
	///	EXIBE O RESULTADO DA BUSCA

	if( count($resultadoProcura["encontrados"]) == 0 ){

		echo "<br/><b style='color:#FF0000'>NÃO encontrei: ".$palavraChave."</b><br/>";

	}else{

		echo "<br/><b style='color:blue'>Encontrei <i style='color:#FF0000'>'".$palavraChave."'</i> nos arquivos:</b> <br/><br/>";

		foreach( $resultadoProcura["encontrados"] as $e ){	

			list($e_arquivo, $e_linhas) = $e;

			echo $e_arquivo; echo "</br>";
			
			foreach ($e_linhas as $ln) {
				echo "<span style='color:mediumvioletred'>" . $ln['numero'] . ":</span><span style='color:gray'> " . $ln['codigo'] . "</span><br/>";
			}
			echo "<br/>";			
			
		}

	}

	// 	======================================v=============================================================== //
	
	echo "<br/><hr/>";

	if( count($resultadoProcura["nEncontrados"]) > 0 ){

		echo "<br/><b>Pesquisei nestes também, mas não encontrei: </b><br/><br/>";
		$contador 															= 0;

		foreach( $resultadoProcura["nEncontrados"] as $e ){

			echo $e;
			if( $contador > 10 ) break; else $contador+= 1;

		}

		if( $resultadoProcura["nEncontrados"] > 10 ){

			echo "<br/>";
			echo "Insisti em mais ".( count($resultadoProcura["nEncontrados"]) - 10 )." arquivos e não encontrei '".$palavraChave."'";

		}

	}

	echo "<br/><hr/>";

	// 	======================================v=============================================================== //
	
}
else if( isset($_REQUEST['caminho_input']) ){ // PALAVRA CHAVE OU CAMINHO VAZIO
	
	echo "Erro: Palavra chave vazia.";

}

// 	========================================v=============================================================== //

?>