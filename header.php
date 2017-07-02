<?php

date_default_timezone_set("America/Lima");

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
		/*if ($.browser.msie && $.browser.version.substr(0,1)<7)
          {
			$('li').has('ul').mouseover(function(){
				$(this).children('ul').show();
				}).mouseout(function(){
				$(this).children('ul').hide();
				})
          }
		
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
		});*/
		
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
	#header_menu{ margin: 40px auto; width: 900px; }
	
	#menu{
	width: 100%;
	margin: 0;
	padding: 10px 0 0 0;
	list-style: none;  
	background: #111;
	background: -moz-linear-gradient(#444, #111); 
    background: -webkit-gradient(linear,left bottom,left top,color-stop(0, #111),color-stop(1, #444));	
	background: -webkit-linear-gradient(#444, #111);	
	background: -o-linear-gradient(#444, #111);
	background: -ms-linear-gradient(#444, #111);
	background: linear-gradient(#444, #111);
	-moz-border-radius: 50px;
	border-radius: 50px;
	-moz-box-shadow: 0 2px 1px #9c9c9c;
	-webkit-box-shadow: 0 2px 1px #9c9c9c;
	box-shadow: 0 2px 1px #9c9c9c;
}

#menu li
{
	float: left;
	padding: 0 0 10px 0;
	position: relative;
	line-height: 0;
}

#menu a 
{
	float: left;
	height: 25px;
	padding: 0 25px;
	color: #999;
	text-transform: uppercase;
	font: bold 12px/25px Helvetica, Arial;
	text-decoration: none;
	text-shadow: 0 1px 0 #000;
}

#menu li:hover > a
{
	color: #fafafa;
}

*html #menu li a:hover /* IE6 */
{
	color: #fafafa;
}

#menu li:hover > ul
{
	display: block;
}

/* Sub-menu */

#menu ul
{
    list-style: none;
    margin: 0;
    padding: 0;    
    display: none;
    position: absolute;
    top: 35px;
    left: 0;
    z-index: 99999;    
    background: #444;
    background: -moz-linear-gradient(#444, #111);
    background: -webkit-gradient(linear,left bottom,left top,color-stop(0, #111),color-stop(1, #444));
    background: -webkit-linear-gradient(#444, #111);    
    background: -o-linear-gradient(#444, #111);	
    background: -ms-linear-gradient(#444, #111);	
    background: linear-gradient(#444, #111);
    -moz-box-shadow: 0 0 2px rgba(255,255,255,.5);
    -webkit-box-shadow: 0 0 2px rgba(255,255,255,.5);
    box-shadow: 0 0 2px rgba(255,255,255,.5);	
    -moz-border-radius: 5px;
    border-radius: 5px;
}

#menu ul ul
{
  top: 0;
  left: 150px;
}

#menu ul li
{
    float: none;
    margin: 0;
    padding: 0;
    display: block;  
    -moz-box-shadow: 0 1px 0 #111111, 0 2px 0 #777777;
    -webkit-box-shadow: 0 1px 0 #111111, 0 2px 0 #777777;
    box-shadow: 0 1px 0 #111111, 0 2px 0 #777777;
}

#menu ul li:last-child
{   
    -moz-box-shadow: none;
    -webkit-box-shadow: none;
    box-shadow: none;    
}

#menu ul a
{    
    padding: 10px;
	height: 10px;
	width: 200px;
	height: auto;
    line-height: 1;
    display: block;
    white-space: nowrap;
    float: none;
	text-transform: none;
}

*html #menu ul a /* IE6 */
{    
	height: 10px;
}

*:first-child+html #menu ul a /* IE7 */
{    
	height: 10px;
}

#menu ul a:hover
{
    background: #0186ba;
	background: -moz-linear-gradient(#04acec,  #0186ba);	
	background: -webkit-gradient(linear, left top, left bottom, from(#04acec), to(#0186ba));
	background: -webkit-linear-gradient(#04acec,  #0186ba);
	background: -o-linear-gradient(#04acec,  #0186ba);
	background: -ms-linear-gradient(#04acec,  #0186ba);
	background: linear-gradient(#04acec,  #0186ba);
}

#menu ul li:first-child > a
{
    -moz-border-radius: 5px 5px 0 0;
    border-radius: 5px 5px 0 0;
}

#menu ul li:first-child > a:after
{
    content: '';
    position: absolute;
    left: 30px;
    top: -8px;
    width: 0;
    height: 0;
    border-left: 5px solid transparent;
    border-right: 5px solid transparent;
    border-bottom: 8px solid #444;
}

#menu ul ul li:first-child a:after
{
    left: -8px;
    top: 12px;
    width: 0;
    height: 0;
    border-left: 0;	
    border-bottom: 5px solid transparent;
    border-top: 5px solid transparent;
    border-right: 8px solid #444;
}

#menu ul li:first-child a:hover:after
{
    border-bottom-color: #04acec; 
}

#menu ul ul li:first-child a:hover:after
{
    border-right-color: #04acec; 
    border-bottom-color: transparent; 	
}


#menu ul li:last-child > a
{
    -moz-border-radius: 0 0 5px 5px;
    border-radius: 0 0 5px 5px;
}

/* Clear floated elements */
#menu:after 
{
	visibility: hidden;
	display: block;
	font-size: 0;
	content: " ";
	clear: both;
	height: 0;
}

* html #menu             { zoom: 1; } /* IE6 */
*:first-child+html #menu { zoom: 1; } /* IE7 */
	
	
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
			<ul id="menu">		
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
								
					echo "<li><a href=\"#\" $onclick_top_item>$ot->descripcion</a>\n";					
					
					$k = 1;
					$l = 1;
					
					$lista_subopciones = array();
					
					foreach($lista_opciones_item as $oi)					
						if($oi->id_opcion_padre == $ot->id)
							$lista_subopciones[]=$oi;
						
					if(count($lista_subopciones) > 0)
					{
						echo "<ul>\n";
						foreach($lista_subopciones as $oi)
						{
							$onclick_subitem = "";
							if($oi->enlace != "")
								$onclick_subitem = "onclick=\"Redireccionar('$oi->opcion_key')\"";
							echo "<li><a href=\"#\" $onclick_subitem>$oi->descripcion</a></li>";
							
							
						}
						echo "</ul>\n";
					}
								
					echo "</li>\n\n";
							
				}
			}?>
			</ul>
				
		</div>
	</div>
			
</div>


	