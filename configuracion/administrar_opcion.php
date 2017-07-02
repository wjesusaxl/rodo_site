<?php

session_start();

$global_login_url = "../login.php";
$global_logout_url = "../logout.php";
$global_images_folder = "../images/";

include ('../clases/enc_dec.php');
include ('../clases/usuario.php');
include ('../clases/centro.php');
include ('../clases/opcion.php');
include ('../clases/general.php');
include ('../clases/security.php');
include ("../clases/anuncio.php");

$enlace_procesar = "../procesar_opcion.php?opcion_key=$opcion_key&usr_key=$usr_key&id_centro=$id_centro&op_original_key=$opcion_key";

$opcBLO = new OpcionBLO();
$cenBLO = new CentroBLO();

$id_usuario = $usuario->id;

$cenBLO = new CentroBLO();
$lista_centros = $cenBLO->ListarTodos();

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN"
"http://www.w3.org/TR/html4/strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
	<head>		
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<!--link rel="stylesheet" href="../styles/administracion.css?v=0.18" type="text/css" /-->
		<script language="JavaScript" src="../js/jquery-1.7.2.min.js"></script>
		<script language="JavaScript" src="../js/jquery.livequery.js"></script>
		<title>.:RODO:.</title>
		<meta name="author" content="Jesus Rodriguez" />
		<!-- Date: 2012-07-01 -->
		<style type="text/css">
			body { background-color: #F1F1F1;}
			#div_volver { width: 400px; float: left; }
	        #div_label_volver { font-family: 'Segoe UI'; font-size: 14px; color: #558EDB; float: left; margin-left: 15px; }
	        #div_label_volver:hover { border-bottom: dotted 1px #558EDB; cursor: pointer; }		
	        #div_formulario {  }
	        #contenido { margin-top: 35px; font-family: 'Helvetica';  }
	        #titulo { border-radius: 5px 5px 5px 5px; font-size: 14px; font-family: 'Helvetica'; font-weight: bold; height: 20px; padding-bottom: 0px; width: 350px; 
	        	vertical-align: middle; color: #585858; }
	        
	        .div_titulo_1 { border-radius: 10px 10px 0px 0px; background-color: #0099CC; padding-bottom: 2px; }
	        .titulo_1 { font-family: Helvetica; font-weight: bold; font-size: 12px; color: #FFFFFF}
	        #tabla_formulario_top { margin-top: 10px; margin-left: 20px; }
	        #tabla_formulario_cuerpo { margin-top: 10px; font-family: 'Helvetica'; }
	        .etiqueta { font-family: 'Helvetica'; font-size: 12px; color: #585858; }
	        .texto { font-family: 'Helvetica'; font-size: 12px; color: #585858; }
	        .texto1 { font-family: 'Helvetica'; font-size: 11px; color: #585858; width: 60px; }
	        .texto2 { font-family: 'Helvetica'; font-size: 12px; color: #585858; width: 80px; }
	        .texto3 { font-family: 'Helvetica'; font-size: 12px; color: #585858; width: 150px; }
	        .texto4 { font-family: 'Helvetica'; font-size: 12px; color: #585858; width: 200px; }
	        .texto5 { font-family: 'Helvetica'; font-size: 12px; color: #585858; width: 250px; }
	        .btn_1 { font-family: 'Helvetica'; font-size: 12px; color: #FFFFFF; background-color: #999999; border-radius: 5px 5px 5px 5px; width: 60px; 
	            padding-top: 2px; padding-bottom: 2px; cursor: pointer; font-weight: bold; }
	        .btn_1:hover { background-color: #585858; }
	        .div_botones_operacion { margin-top: 10px; }
	        
	        #div_lista_opciones { width: 1120px; margin: 0 auto; overflow: hidden; border: dotted 1px #0099CC; border-radius: 10px 10px 10px 10px; 
	        background-color: #FFFFFF; margin-top: 20px; padding-bottom: 20px; }
	        
	        .div_tabla_opcion { margin-top: 15px; border: dotted 1px #0099CC; border-radius: 10px 10px 10px 10px; }
	        .tabla_opcion { color: #585858; border-collapse:collapse; }
	        .tabla_opcion thead { font-size: 12px; padding-left: 5px; padding-right: 5px; color: #0099CC; }
	        .tabla_opcion td { border-top: dotted 1px #0099CC; }	        	        
	        
	        #div_tabla_opciones { width: 970px;  }
	        #div_tabla_caja_usuario { width: 620px; }
	        #div_tabla_usuario_grupo_transaccion { width: 260px;}

	        .opcion_padre { font-weight: bold; font-size: 14px; padding-left: 5px; padding-right: 5px; color: #0099CC; }
	        
	        .opcion_hija{ font-size: 12px; padding-left: 5px; padding-right: 5px; }
	        
	        .fila_opcion_hija:hover { background-color: #F8FEA9; }
	        
	        .opc_centro { background-color: #F0F0F0; }
	        
	        .opcion_hija_th { font-size: 12px; color: #0099CC; font-family: Helvetica; font-weight: bold;  }
				
		</style>
		
		<script type="text/javascript">
		
		function Redireccionar(opcion_key)
		{
			location.href = "../redirect.php?opcion_key=" + opcion_key + "&usr_key=<?php echo $usr_key. "&id_centro=$id_centro"; ?>";
		}
		
		
		function Cancelar()
		{
			location.href = '<?php echo $enlace_original; ?>';			
		}
		
		function Guardar()
		{
		    var id_usuario = document.formulario.id_usuario_conf.value;
		    var id_centro = document.formulario.id_centro_conf.value;
		    		    
		    if(id_usuario > 0 && id_centro)
		    {
		        document.formulario.action = "<?php echo $enlace_procesar;?>";
		        document.formulario.operacion.value = "asignar_opciones";
                document.formulario.submit();                		       
		    }
		    else
                alert("No ha seleccionado un usuario");		    
		}
		
		function GuardarCajaUsuario()
		{
				
		}
		
		function CambiarUsuario()
		{
		    var id_usuario = document.formulario.id_usuario_conf.value;
                        
            if(id_usuario > 0)
            {
                document.formulario.action = "<?php echo $enlace_usuario_opcion;?>";
                document.formulario.submit();                              
            }            
		}
		
		function CambiarCentro()
		{
		    var id_centro = document.formulario.id_centro_conf.value;
                        
            if(id_centro > 0)
            {
                document.formulario.action = "<?php echo $enlace_usuario_opcion;?>";
                document.formulario.submit();                              
            }            
		}
		
		
		
			
		$(function() 
		{
			$("#btn_guardar").click(function()
			{
				$("#operacion").val("modificar");
				if(confirm("¿Seguro de guardar los cambios?"))				
					$("#formulario").submit();
			});
			
			
			
			$(".opcion_general").live("click", function()
			{
				$fila = $(this).parent().parent();
				
				if($(this).is(":checked"))
				{
					$fila.find(".opcion_centro").attr("disabled", "disabled");					
				}
				else
				{
					$fila.find(".opcion_centro").removeAttr("disabled");
				}
			});
		  
		});
		
		</script>
	</head>
	<body>
		<div id="div_main">
			<?php include ('../header.php');?>
			<div id = "contenido" align="center">
			    
				<form id="formulario" name="formulario" action="<?php echo $enlace_procesar; ?>" method="POST">
				    <input type="hidden" id="operacion" name="operacion"/>
				    
				    
					<div id = "div_formulario">					
						<div id="tabla_formulario_cuerpo">
							
							<div id="div_lista_opciones" >
								<div id="div_tabla_opciones" class="div_tabla_opcion" >
									<div class="div_titulo_1"><span class="titulo_1">Opciones del Sistema</span></div>
									<table id="tabla_opciones" class="tabla_opcion">
										<thead>
											<th width=50px></th>
											<th width=300px>Opción</th>
											<th width=80px></th>
											<th width=80px></th>
											<th width=120px></th>
											<th width=80px></th>
											<th width=80px></th>
											<?php
											
											$span = 7;
											if(!is_null($lista_centros))
											{
												foreach($lista_centros as $c)
												{
													$largo = 8 * strlen($c->descripcion);
													echo "<th width=".$largo."px class=\"opc_centro\"></th>\n";
													$span = 7 + count($lista_centros);
												}												
											}
											?>
										</thead>
										<tbody>
										<?php
										$opBLO = new OpcionBLO();
										$lista_padres = $opBLO->ListarOpcionesPadre();
										if(!is_null($lista_padres))
										{
											foreach($lista_padres as $p)
											{
												$lista_hijos = $opBLO->ListarOpcionesHijasXOpcionPadreId($p->id);
												if(!is_null($lista_hijos))
												{
													if(count($lista_hijos) > 0)
													{
														
														echo "<tr class=\"fila_opcion_padre\"><td colspan=\"2\"><span class=\"opcion_padre\">".strtoupper($p->descripcion)."</span></td>\n";
														echo "<td class=\"opcion_hija_th\" align='center'>Estado</td>";
														echo "<td class=\"opcion_hija_th\" align='center'>Menú</td>";
														echo "<td class=\"opcion_hija_th\" align='center'>Menú Principal</td>";
														echo "<td class=\"opcion_hija_th\" align='center'>Pública</td>";
														echo "<td class=\"opcion_hija_th\" align='center'>General</td>";
														if(!is_null($lista_centros))
														{
															foreach($lista_centros as $c)
															{
																$largo = 8 * strlen($c->descripcion);
																echo "<td width=".$largo."px class=\"opc_centro opcion_hija_th\">".strtoupper($c->descripcion)."</td>";
															}																										
														}
														echo "</tr>";
														
														foreach($lista_hijos as $h)
														{
															$check_estado = $h->estado == 0 ? "" : "checked='checked'"; 
															$check_flag_menu = $h->flag_menu == 0 ? "" : "checked='checked'";
															$check_flag_menu_principal = $h->flag_menu_principal == 0 ? "" : "checked='checked'";
															$check_flag_publica = $h->flag_publica == 0 ? "" : "checked='checked'";
															$check_flag_general = $h->flag_general == 0 ? "" : "checked='checked'";
															$disabled_opcion_general = $h->flag_general == 0 ? "" : "disabled='disabled'";
															
															$tr = "<tr class=\"fila_opcion_hija\">";
															$tr = $tr."<td align=\"center\"><span class=\"opcion_hija\"><b>$h->opcion_key</b></span></td>\n";
															$tr = $tr."<td><span class=\"opcion_hija \">[$h->id]$h->descripcion</span></td>\n";
															$tr = $tr."<td align=\"center\"><input type=\"checkbox\" class=\"opcion_estado\" name=\"opcion_estado[]\" value=\"$h->id\" $check_estado/></td>\n";
															$tr = $tr."<td align=\"center\"><input type=\"checkbox\" class=\"opcion_menu\" name=\"opcion_menu[]\" value=\"$h->id\" $check_flag_menu/></td>\n";
															$tr = $tr."<td align=\"center\"><input type=\"checkbox\" class=\"opcion_menu_principal\" name=\"opcion_menu_principal[]\" value=\"$h->id\" $check_flag_menu_principal/></td>\n";
															$tr = $tr."<td align=\"center\"><input type=\"checkbox\" class=\"opcion_publica\" name=\"opcion_publica[]\" value=\"$h->id\" $check_flag_publica/></td>\n";
															$tr = $tr."<td align=\"center\"><input type=\"checkbox\" class=\"opcion_general\" name=\"opcion_general[]\" value=\"$h->id\" $check_flag_general/></td>\n";
															
															$fila_opciones = "";
															
															foreach($lista_centros as $c)
															{
																$check_centro = "";
																	
																$co = $opcBLO->RetornarCentroOpcionXIdCentroIdOpcion($c->id, $h->id);
																if(!is_null($co))
																	if($co->flag_habilitado == 1)
																		$check_centro = "checked='checked'";
																	
																$fila_opciones = $fila_opciones."<td align=\"center\" class=\"opc_centro\" align='center'><input $check_centro class=\"opcion_centro\" type=\"checkbox\" name=\"centro_opcion_$c->id"."[]\" value=\"$h->id\" $disabled_opcion_general /></td>\n";
															}
																	
															
															$tr = $tr.$fila_opciones;
															$tr = $tr."</tr>\n";
															echo $tr;
														}
													}
												}
												
											}
										}
										
										?>
										<tr height=10px></tr>
										<tr>
											<td colspan="<?php echo $span;?>" align=center>
												<input type="button" class="texto1" value="Guardar" id="btn_guardar"/>
											</td>
										</tr>
										</tbody>
									</table>
									    
								</div>
							</div>
															
						</div>					
					</div>
				</form>
			</div>
		</div>
		
		
		
	</body>
</html>

