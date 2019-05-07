<?
    
$pasta = ROOT."/";
$resultadoProcura["numArquivos"] = 0;

			
			
//	VARRE TODO OS SUBDIRETORIOS
function subDiretorioVarrer($diretorioArray,$subPasta = ""){
		
	global $pasta;
	global $arquivo;
	global $resultadoProcura;
	global $palavraChave;
	global $resultadoProcura;
	global $caseSensitive;
	global $opcaoProcura;
	$subPasta = (!empty($subPasta))?$subPasta."/":"";
	
	foreach( $diretorioArray as $val ){
		
		if( strripos($val,".php") !== false or strripos($val,".js") !== false  or strripos($val,".css") !== false or strripos($val,".ofx") !== false ){ // SE FOR UM ARQUIVO, FAZ O PROCESSO DE BUSCA
			
			chmod(ROOT."/__root_utilitarios/procurar.txt", 0644);
			
			//if(!copy($pasta.$arquivo."/".$subPasta.$val,$_SERVER['DOCUMENT_ROOT']."/__root_utilitarios/procurar.txt")){
			
			copy($pasta.$arquivo."/".$subPasta.$val, ROOT."/__root_utilitarios/procurar.txt");
			$conteudo = file_get_contents(ROOT."/__root_utilitarios/procurar.txt");
			//$conteudo = file_get_contents($_SERVER['DOCUMENT_ROOT']."/__root_utilitarios/procurar.txt");
			
			$rr .= $pasta.$arquivo."/".$subPasta.$val.'<br>';
			
			$resultadoProcura["numArquivos"]+= 1;
			if( $caseSensitive and strrpos($val,$palavraChave) !== false and in_array($opcaoProcura,array("nc","n")) ){
				
				$resultadoProcura["encontrados"][] = "$val - (Nome do arquivo)";
				
			}else if( !$caseSensitive and strripos($val,$palavraChave) !== false and in_array($opcaoProcura,array("nc","n")) ){
				
				$resultadoProcura["encontrados"][] = "<i style='color:gray'>".substr($pasta, 14)."</i><i style='color:blue'>".$arquivo."/".$subPasta.$val." - (Nome do arquivo)</i>";
				
			}

			if( $caseSensitive and strrpos($conteudo,$palavraChave) !== false and in_array($opcaoProcura,array("nc","c")) ){
				
				$resultadoProcura["encontrados"][] = "<i style='color:gray'>".substr($pasta, 14)."</i><i style='color:blue'>".$arquivo."/".$subPasta.$val."</i><br/>";
				
			}else if( !$caseSensitive and strripos($conteudo,$palavraChave) !== false and in_array($opcaoProcura,array("nc","c")) ){
				
				$resultadoProcura["encontrados"][] = "<i style='color:gray'>".substr($pasta, 14)."</i><i style='color:blue'>".$arquivo."/".$subPasta.$val."</i>";
				
			}else{
				
				$resultadoProcura['nEncontrados'][] = "<i style='color:gray'>".substr($pasta, 14).$arquivo."/".$subPasta.$val."</i><br/>";
				
			}
 
		}else if( $val != ".." and $val != "." ){// SE FOR UM DIRETORIO, VARRE SEU DIRETORIO E CHAMA PARA A PROPRIA FUNCAO
			
			$pasta1 = scandir($pasta.$arquivo."/".$subPasta.$val);
			subDiretorioVarrer($pasta1,$subPasta.$val);
			
		}
		
	}
	
	return $rr;
	
}
			
?>

<form action="" method="post" style="margin-top: 30px; margin-left: 30px;">
	
	<div>
		
		<?
	
			$pathV = ROOT."/v";
			$arrVersao = array();
			$diretorio2 = dir($pathV);
		
			while($arquivo2 = $diretorio2->read()){
				
				if( is_numeric($arquivo2) ){
					
					$arrVersao[] = $arquivo2;
				
				}
				
			}
			   
			$pathV = ROOT."/painel";
			$arrVersaoPainel = array();
			$diretorio2 = dir($pathV);
		
			while($arquivo2 = $diretorio2->read()){
				
				if( is_numeric($arquivo2) ){

					$arrVersaoPainel[] = $arquivo2;

				}

			}

		?>

		Procurar: <input type="text" name="palavraChave" value="<? echo $_REQUEST['palavraChave']  ?>" />&nbsp;
		
		<select name="opcaoProcura">
			
			<option value="c" <? echo ($_REQUEST['opcaoProcura']=="c")?"selected='selected'":""; ?>>Somente no Conteúdo do Arquivo</option>
			<option value="n" <? echo ($_REQUEST['opcaoProcura']=="n")?"selected='selected'":""; ?>>Somente no Nome do Arquivo</option>
			<option value="nc" <? echo ($_REQUEST['opcaoProcura']=="nc")?"selected='selected'":""; ?>>Nome + Conteúdo do Arquivo</option>
			
		</select>
		
		&nbsp;<input type="checkbox" name="case-sensitive" <? echo ($_REQUEST['case-sensitive'])?"checked='checked'":""; ?> /> Case Sensitive
		<br/><br/>
		Caminho: 	
<!-- 		<select name="caminhoInicio" onchange="ultimaVersao(this.value,<? echo end($arrVersao).",".end($arrVersaoPainel) ?>)">
			<option value="" <? echo ($_REQUEST['caminhoInicio']=="")?"selected='selected'":""; ?>>/www/ -R (tudo, desde a raiz) </option>
			<option value="/" <? echo ($_REQUEST['caminhoInicio']=="/")?"selected='selected'":""; ?>>/www/* (somente a raiz, excluindo subpastas)</option>
			<option value="__root_" <? echo ($_REQUEST['caminhoInicio']=="__root_")?"selected='selected'":""; ?>>/www/__root_[digite a pasta desejada]</option>
			<option value="v/" <? echo ($_REQUEST['caminhoInicio']=="v/")?"selected='selected'":""; ?>>/www/v/[n.nn] (digite a versao desejada)</option>
			<option value="v/<? echo end($arrVersao) ?>/_templates/" <? echo ($_REQUEST['caminhoInicio']=="v/".end($arrVersao)."/_templates/")?"selected='selected'":""; ?>>/www/v/<? echo end($arrVersao) ?>/_templates/[versao do template]</option>
			<option value="painel/" <? echo ($_REQUEST['caminhoInicio']=="painel/")?"selected='selected'":""; ?>>/www/painel/[n.nn] (digite a versao desejada)</option>
			<option value="___sites/" <? echo ($_REQUEST['caminhoInicio']=="___sites/")?"selected='selected'":""; ?>>/www/___sites/[nnn] (opcional - digite o site)</option>
			<option value="___projetos/" <? echo ($_REQUEST['caminhoInicio']=="___projetos/")?"selected='selected'":""; ?>>/www/_projetos/ [opcional - digite o projeto]/</option>
		</select> -->
		
		<select id="multiselect" name="caminhoInicio[]" class="multiselect campo_filtro " tyle="width: 315px; display: none;" onchange="ultimaVersao(this.value,'<? echo end($arrVersao)."','".end($arrVersaoPainel) ?>')">

			<option value="v/" <? echo (in_array("v/",$_REQUEST['selectItemcaminhoInicio']))?"selected='selected'":""; ?>>/www/v/[n.nn] [digite a versao desejada] (opcional)
		</option>
			<option value="v/<? echo end($arrVersao) ?>" <? echo (in_array("v/".end($arrVersao),$_REQUEST['selectItemcaminhoInicio']))?"selected='selected'":""; ?>>/www/v/<? echo end($arrVersao) ?>/ -R (última versão do site, incluindo subpastas)</option>
			<option value="v/<? echo end($arrVersao) ?>/_templates/" <? echo (in_array("v/".end($arrVersao)."/_templates/",$_REQUEST['selectItemcaminhoInicio']))?"selected='selected'":""; ?>>/www/v/<? echo end($arrVersao) ?>/_templates/ -R (template específico, incluindo subpastas)</option>
			<option value="painel/" <? echo (in_array("painel/",$_REQUEST['selectItemcaminhoInicio']))?"selected='selected'":""; ?>>/www/painel/[n.nn] [digite a versao desejada] (opcional)
		</option>
			<option value="painel/<? echo end($arrVersaoPainel) ?>" <? echo (in_array("painel/".end($arrVersaoPainel),$_REQUEST['selectItemcaminhoInicio']))?"selected='selected'":""; ?>>/www/painel/<? echo end($arrVersaoPainel) ?>/ -R (última versão do painel, incluindo subpastas) </option>
			<option value="/" <? echo (in_array("/",$_REQUEST['selectItemcaminhoInicio']))?"selected='selected'":""; ?>>/www/* (somente a raiz, excluindo subpastas)</option>
			<option value="" <? echo (in_array("",$_REQUEST['selectItemcaminhoInicio']))?"selected='selected'":""; ?>>/www/ -R (tudo, desde a raiz)</option>
			<option value="__root_" <? echo (in_array("__root_",$_REQUEST['selectItemcaminhoInicio']))?"selected='selected'":""; ?>>/___root_[digite a subpasta] (opcional)</option>
			<option value="___sites/" <? echo (in_array("___sites/",$_REQUEST['selectItemcaminhoInicio']))?"selected='selected'":""; ?>>/___site_/[digite a subpasta] (opcional)</option>
			<option value="___projetos/" <? echo (in_array("___projetos/",$_REQUEST['selectItemcaminhoInicio']))?"selected='selected'":""; ?>>/___projetos_/[digite a subpasta] (opcional)</option>

		</select>

		<input type="text" name="caminho" value="<? echo $_REQUEST['caminho']  ?>" />/<br/>
		
		<? echo "Versões disponíveis: Site (".implode(", ",$arrVersao).") / Painel (".implode(", ",$arrVersaoPainel).")";  ?>
		<br/>
		<br/>
		<input type="submit" value="Procurar" />

	</div>
	
</form>

<script>
	
	//Cria o multiselect
	$("#multiselect").multipleSelect({
		name: 'teste',
		placeholder: "Selecionar",
		filter: 1,
		selectAll: 0,
		onClick: function() {
			console.log( $("#multiselect").multipleSelect("getSelects") );
		},
	});
	
	$('[data-name="selectItemcaminhoInicio[]"]').attr("name", "selectItemcaminhoInicio[]");
	
	//Autopreenche o caminho com a ultima versao do site ou painel
	function ultimaVersao(val,versaoS, versaoP){
		
		if(val == "v/"){
			
			
			document.getElementsByName("caminho")[0].value = versaoS;
			
		}else if(val == "painel/"){
			
			document.getElementsByName("caminho")[0].value = versaoP;
			
		}else if(val == "/"){
			
			document.getElementsByName("caminho")[0].value = "";
				
		}else{
			
			document.getElementsByName("caminho")[0].value = "";
			
		}
		
	};
	
</script>

<?

if( !empty($_REQUEST['palavraChave']) ){
	
	$palavraChave = $_REQUEST['palavraChave'];
	$resultadoProcura = array();
	$caminhoInicio = $_REQUEST['caminhoInicio'];
	$caseSensitive = $_REQUEST['case-sensitive'];
	$opcaoProcura = $_REQUEST['opcaoProcura'];
	$resultadoProcura["numArquivos"] = 0;
		
	echo "Buscar por <i><strong style='color:#FF0000'>'".$palavraChave."'</strong></i></br>";
	
	//Tratamento para as pastas que comecao com "__root_"
	if(in_array("__root_",$_REQUEST['selectItemcaminhoInicio']) && empty($_REQUEST['caminho'])){
		
		$rootDir = glob(ROOT.'/__root_*' , GLOB_ONLYDIR);
		unset($_REQUEST['selectItemcaminhoInicio'][array_search('__root_', $_REQUEST['selectItemcaminhoInicio'])]);
		$_REQUEST['selectItemcaminhoInicio'] = array_merge($_REQUEST['selectItemcaminhoInicio'],$rootDir);
		
	}
	
	foreach($_REQUEST['selectItemcaminhoInicio'] as $c){
		
		//Tratamento para as pastas que comecao com "__root_"
		$c = str_replace("/mnt/hd-codigo/www/", "", $c);
		$separador = (strripos($_REQUEST['caminho'],"__root_") !== false)?"/":"";
		$pasta = ROOT;
		$pasta.= ($c)?"/".$c:"";
		$pasta.= (trim($_REQUEST['caminho'],"/"))?$separador.trim($_REQUEST['caminho'],"/"):"";
		$pasta.= "/";

		echo " Local: '<strong style='color:#FF0000'>".$pasta."</strong>'<br />"; 
		
		if(is_dir($pasta)){ // SE O CAMINHO DIGITADO FOR UMA PASTA
			
			$diretorio = dir($pasta);
			
			while($arquivo = $diretorio->read()){
				
				$sub = $pasta.$arquivo;

				if($caminhoInicio == "/"){
					if(is_dir($sub)){
						continue;
					}
				}
				
				if( strripos($sub,"/media/hd-codigo/www//divulgacao.com") !== false || strripos($sub,"divulgacao.com//v") !== false || strripos($sub,"divulgacao.com//painel") !== false || strripos($sub,"/media/hd-codigo/www/___projetos") !== false || strripos($sub,"/media/hd-codigo/www/___sites") !== false ){
					echo "<br/> Ignorei a pasta: ".$sub;
					continue;
				}

				if( is_dir($pasta.$arquivo) and $arquivo != ".." and $arquivo != "." ){ // SE FOR UM DIRETORIO
					
					$dir = $pasta.$arquivo;
					$file = scandir($dir);
					
					subDiretorioVarrer($file);

				}else if( strripos($arquivo,".php") !== false or strripos($arquivo,".js") !== false or strripos($arquivo,".ofx") !== false ){ //	SE FOR UM ARQUIVO PHP

					if($arquivo == "config.php"){continue;}
					copy($pasta.$arquivo,"procurar.txt");
					$conteudo = file_get_contents("procurar.txt");

					$resultadoProcura["numArquivos"]+= 1;

					if( $caseSensitive and strrpos($arquivo,$palavraChave) !== false and in_array($opcaoProcura,array("nc","n")) ){

						$resultadoProcura["encontrados"][] = "$arquivo - (Nome do arquivo)";

					}else if( !$caseSensitive and strripos($arquivo,$palavraChave) !== false and in_array($opcaoProcura,array("nc","n")) ){

						$resultadoProcura["encontrados"][] = "$arquivo - (Nome do arquivo)";
						
					}

					if( $caseSensitive and strrpos($conteudo,$palavraChave) !== false and in_array($opcaoProcura,array("nc","c")) ){

						$resultadoProcura["encontrados"][] = "<i style='color:gray'>".substr($pasta, 0, 14)."</i><i style='color:blue'>".$arquivo."</i><br/>";

					}else if( !$caseSensitive and strripos($conteudo,$palavraChave) !== false and in_array($opcaoProcura,array("nc","c")) ){

						$resultadoProcura["encontrados"][] = "<i style='color:gray'>".substr($pasta, 0, 14)."</i><i style='color:blue'>".$arquivo."</i><br/>";

					}else{

						$resultadoProcura['nEncontrados'][] = "<i style='color:gray'>".substr($pasta, 14).$arquivo."</i><br/>";
					}

				}

			}

			///	FECHA O ARQUIVO
			$diretorio->close();

		}else{ // CASO NÃO ENCONTRE O DIRETORIO
			
			echo 'A pasta não existe: '.$pasta;

		}
		
	}

	echo "<hr/>Total de Arquivos Pesquisados: <b><i style='color:#FF0000'>".$resultadoProcura["numArquivos"]."</i></b>";
	echo "<br/><hr/>";


	///	EXIBE O RESULTADO DA BUSCA
	if( count($resultadoProcura["encontrados"]) == 0 ){

		echo "<br/><b style='color:#FF0000'>NÃO encontrei: ".$palavraChave."</b><br/>";

	}else{

		echo "<br/><b style='color:blue'>Encontrei <i style='color:#FF0000'>'".$palavraChave."'</i> nos arquivos:</b> <br/><br/>";

		foreach( $resultadoProcura["encontrados"] as $e ){	
			echo $e;echo "</br>";
		}

	}

	echo "<br/><hr/>";

	if( count($resultadoProcura["nEncontrados"]) > 0 ){

		echo "<br/><b>Pesquisei nestes também, mas não encontrei: </b><br/><br/>";
		$contador = 0;

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
	
}else if( isset($_REQUEST['caminho']) ){ // PALAVRA CHAVE OU CAMINHO VAZIO
	
	echo "Erro: Palavra chave vazio";

}

?>