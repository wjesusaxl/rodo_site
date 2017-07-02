<?php


$cenBLO = new CentroBLO();
$opcBLO = new OpcionBLO();
$anBLO = new AnuncioBLO();
if($id_centro > 0 && $id_usuario > 0)			
	$centro = $cenBLO->RetornarXId($id_centro);

$url_query_anuncio = "procesar_anuncio.php?id_centro=$id_centro&operacion=query_activos";

$lista_anuncios_menu_header = $anBLO->ListarActivos($id_centro);
$nro_anuncios = count($lista_anuncios_menu_header);

function CreateAnimationHTML($id, $nro_anuncios)
{
	$duracion = 15000;
	
	$html = "$(\"#message_bar_msg_$id\").css(\"display\", \"block\");\n";
	$html = $html."$(\"#message_bar_msg_$id\").animate({\n";
	$html = $html."	'margin-left': '+=1100px'}, $duracion, function()\n";
	$html = $html."	{\n";
	$html = $html."		$(\"#message_bar_msg_$id\").css(\"marginLeft\", \"0\");\n";
	$html = $html."		$(\"#message_bar_msg_$id\").css(\"display\", \"none\");\n";
	
	if($id <= $nro_anuncios - 1)
		$html = $html." ".CreateAnimationHTML($id + 1, $nro_anuncios);
	else
		$html = $html." AnimarAnuncios()";
		
	$html = $html."	}\n";
	$html = $html."	);\n";
	
	return $html;
}
		
?>

<!--link rel="stylesheet" href="style.css" type="text/css" /-->


<script type="text/javascript">

	function Desconectarse()
	{
		window.location.href = "<?php echo $global_login_url; ?>";
	}
	
	function AnimarAnuncios()
	{
	<?php
		echo CreateAnimationHTML(1, $nro_anuncios);
	?>
	}
	
	$(function()
	{
		
		
		$(".header_menu_top").live('mouseover',function()
		{
			$top = $(this);
			
			$top.find(".header_menu_item_1").each(function()
			{
				$(this).css("display","block");

			});
			
			$top.find(".header_menu_item_2").each(function()
			{
				$(this).css("display","block");
			});
			 
		});
		
		$(".header_menu_top").live('mouseout',function()
		{
			$top = $(this);
			
			$top.find(".header_menu_item_1").each(function()
			{
				$(this).css("display","none");
			});
			
			$top.find(".header_menu_item_2").each(function()
			{
				$(this).css("display","none");
			});
		});
		
		AnimarAnuncios();
				
		});
			
</script>

<?php

/*if(isset($_SESSION['usuario_nombres']))
	$usuario_nombres = $_SESSION['usuario_nombres'];
else
	$usuario_nombres = '';*/

?>

<style type="text/css">
	#header_main { }
	#header_usuario { float:right; font-family: Impact; }
	#header_usuario_lbl { background-color: #0099CC; color: #585858; font-size:14px; padding-left: 5px; padding-right: 5px; }
	#header_desconectarse_div { font-family: Helvetica; font-size: 12px; padding-left: 20px; color: red; border: }
	#header_desconectarse_div:hover { cursor: pointer; }
	#header_centro { color: #0099CC; font-size: 32px; margin-bottom:10px; border: dotted 1px #585858; padding: 2px 2px 2px 2px; border-radius: 5px 5px 5px 5px; font-family: Impact; }
	
	#header_body { margin-top: 10px;}
	#header_logos_div { height: 100px; margin-bottom: 20px;}
	
	#header_titulo { color: #585858; font-size:18px; font-family: Impact; }
	
	#header_menu {  padding-top: 20px; padding-bottom: 20px; color:#585858; height: 50px; }
	
	.header_menu_top_1 {  border-right: dotted 1px #3399FF; border-top: dotted 1px #3399FF; border-bottom: dotted 1px #3399FF; }
	.header_menu_top_2 {  border-right: dotted 1px #3399FF; border-top: dotted 1px #3399FF; border-bottom: dotted 1px #3399FF; }
	
	.header_menu_top { float: left;}													
	
	.header_menu_top_1:hover { background-color: #0099CC; color: #FFFFFF; cursor: pointer; }
	.header_menu_top_2:hover { background-color: #99CC00; color: #FFFFFF; cursor: pointer; }
	
	.header_menu_item_1 { border-right: dotted 1px #3399FF; border-bottom: dotted 1px #3399FF; border-left: dotted 1px #3399FF; display: none; font-family: Helvetica; font-size:11px; }
	.header_menu_item_2 { border-right: dotted 1px #3399FF; border-bottom: dotted 1px #3399FF; border-left: dotted 1px #3399FF; display: none; font-family: Helvetica; font-size:11px; }
	
	.header_menu_item_1:hover { background-color: #0099CC; color: #FFFFFF; cursor: pointer; }
	.header_menu_item_2:hover { background-color: #99CC00; color: #FFFFFF; cursor: pointer; }
	
	.header_menu_top_span { font-family: Impact; font-size: 16px; }
	.header_menu_item_span { font-family: Helvetica; font-size:11px; }
	
	#header_cuadrado_izquierdo { width:20px; height:40px; float: left; background-color: #0099CC; }
	#header_cuadrado_derecho { width:20px; height:40px; float: left; background-color: #99CC00; }
	
	#header_menu_body {  }
	
	#floated { float:right; position:relative; right:50%; }
	#floated-inner { float:left; position:relative; left:50%; }
	#div_logo { float: left;}
	#div_header_centro { float: left; }
	#message_bar { border-radius: 5px 5px 5px 5px; background-color: #383838; margin: 0 auto; overflow: hidden; width: 1100px; height: 15px; }
	.message_bar_msg { color: #FFFF00; font-family: Helvetica; font-size: 12px; display: none; float: left; }
	.anuncio_usuario { color: #0099CC; font-weight: bold; font-size: 12px; }
	
</style>


<?php
	
	
if(count($lista_anuncios_menu_header) > 0)
{
	echo "<div id=\"message_bar\">";
	$i = 1;
	foreach($lista_anuncios_menu_header as $a)
	{
		$cuenta = "($i/$nro_anuncios)";
		echo "<div class=\"message_bar_msg\" id=\"message_bar_msg_$i\"><span class=\"anuncio_usuario\">$cuenta $a->usuario_nombres_apellidos: </span>$a->mensaje</div>\n";
		$i++;
	}
	echo "</div>";
}		
			
?>



<div id="header_main">
	
	
	
	<div id="header_usuario" align="right">
		<div id="div_logo">
			<?php
			
			if(!is_null($centro))
			{
				if($centro->id == 1)
				{?>
					<img src="<?php echo $global_images_folder;?>logo-delocal.png" style="width:84px; height:93px;"/>
				<?php
				}
				if($centro->id == 2)
				{?>
					<img src="<?php echo $global_images_folder;?>/neritos.png" style="width:160px; height:64px;"/>
			<?php
				}
			}
			?>
		</div>
		<div id="div_header_centro">
			<div id="header_centro"><span style="font-size:14px; color: #585858 ">Centro: </span><?php echo !is_null($centro) ? $centro->descripcion : ""; ?></div>	
			<div><span id="header_usuario_lbl"><?php echo 'Bienvenid@ '.$usuario->nombres.' '.$usuario->apellidos?></span></div>
			<div id="header_desconectarse_div">
				<u><span onclick="Desconectarse()"> Desconectarse</span></u>
			</div>
		</div>
	</div>
	
	<div id="header_body" align="center">
		
		<div id="header_logos_div">
			
		</div>
		<div id="header_titulo">
			<span>RODO: Sistema de Registro de información para los locales de RODISNESS S.A.C.</span>				
		</div>
		<div id="header_menu" align="center">
			<div id="floated">
				<div id="floated-inner">
					<div id="header_menu_body" align="center">
						<div id="header_cuadrado_izquierdo">					
						</div>				
						
						<?php
						$lista_opciones_top = $opcBLO->ListarMenuTopItemsXIdUsuarioIdCentro($id_usuario, $id_centro);
						$lista_opciones_item = $opcBLO->ListarMenuSubItemsXIdUsuarioIdCentro($id_usuario, $id_centro);
						
						if(!is_null($lista_opciones_top))
						{
							$i = 1;
							$j = 1;
							foreach($lista_opciones_top as $ot)
							{
								$ancho = strlen($ot->descripcion)*10 + 50;
								if($ancho < 140)
									$ancho = 140;
								$ancho = $ancho."px";
								
								$onclick_top_item = "";
								if($ot->enlace != "")
									$onclick_top_item = "onclick=\"Redireccionar('$ot->opcion_key')\"";
								
								echo "<div id=\"item_$i\" class=\"header_menu_top\" align=\"center\">\n";
								echo "	<div class=\"header_menu_top_$j\" style=\"width: $ancho\" $onclick_top_item>\n\n";
								echo "		<span class=\"header_menu_top_span\">$ot->descripcion</span>";
								echo "	</div>\n";
								
								$k = 1;
								$l = 1;
								foreach($lista_opciones_item as $oi)
								{
									if($oi->id_opcion_padre == $ot->id)
									{
										$onclick_subitem = "";
										if($oi->enlace != "")
											$onclick_subitem = "onclick=\"Redireccionar('$oi->opcion_key')\"";
										
										echo "			<div id=\"item_".$i."_".$l."\" class=\"header_menu_item_$k\" $onclick_subitem>\n"; 
										echo "				<span class=\"header_menu_item_span\">$oi->descripcion</span>\n";
										echo "			</div>";
										
										$l++;
										if($k == 1)
											$k = 2;
										else 
											$k = 1;
									}
								}
								
								echo "</div>\n\n";
								
								$i++;
								if($j == 1)
									$j = 2;
								else 
									$j = 1;
							}
							
						}
						
						?>
						
						<div id="header_cuadrado_derecho">
										
						</div>
					</div>
				
				</div>
			</div>
			
		</div>

	</div>		
</div>

	