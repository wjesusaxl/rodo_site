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
include ("../clases/caja.php");
include ("../clases/transaccion.php");
include ("../clases/almacen.php");
include ("../clases/movimiento.php");
include ("../clases/anuncio.php");

$enlace_procesar = "../procesar_usuario.php?opcion_key=$opcion_key&usr_key=$usr_key&id_centro=$id_centro&op_original_key=$opcion_key";
$enlace_procesar_caja = "../procesar_caja.php?opcion_key=$opcion_key&usr_key=$usr_key&id_centro=$id_centro&op_original_key=$opcion_key";
$enlace_procesar_almacen = "../procesar_almacen.php?opcion_key=$opcion_key&usr_key=$usr_key&id_centro=$id_centro&op_original_key=$opcion_key";
$enlace_usuario_opcion = "usuario_opcion.php?opcion_key=$opcion_key&usr_key=$usr_key&id_centro=$id_centro&op_original_key=$opcion_key";
$enlace_procesar_transaccion = "../procesar_transaccion.php?opcion_key=$opcion_key&usr_key=$usr_key&id_centro=$id_centro&op_original_key=$opcion_key";
$enlace_procesar_motivo = "../procesar_movimiento.php?opcion_key=$opcion_key&usr_key=$usr_key&id_centro=$id_centro&op_original_key=$opcion_key";

$opcBLO = new OpcionBLO();
$cenBLO = new CentroBLO();

$id_usuario = $usuario->id;

if(isset($_POST["id_usuario_conf"]))
	$id_usuario_conf = $_POST["id_usuario_conf"];
else
	$id_usuario_conf = 0;

if($id_usuario_conf > 0)
{
	$lista_opciones_usuario = $opcBLO->ListarUsuarioOpcionXIdUsuario($id_usuario_conf);	
	$lista_opciones_centro_usuario = $opcBLO->ListarUsuarioCentroOpcionXIdUsuario($id_usuario_conf);
}
	
	
$lista_centros = $cenBLO->ListarTodos();
	

/*if($id_centro > 0 && $id_usuario > 0)
{
	$centro = $cenBLO->RetornarXId($id_centro);		
}

if(isset($_POST["id_usuario_conf"]))
    $id_usuario_conf = $_POST["id_usuario_conf"];
else
    $id_usuario_conf = 0;

if(isset($_POST["id_centro_conf"]))
	$id_centro_conf = $_POST["id_centro_conf"];
else
	$id_centro_conf = 0;

if($id_usuario_conf > 0 && $id_centro_conf > 0 )
{
    
    $lista_opciones_usuario = $opcBLO->ListarOpcionesXIdUsuario($id_usuario_conf, $id_centro_conf);
	
        
    $enlace_post = $enlace_usuario_opcion;    
}
*/


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
	        .texto1 { font-family: 'Helvetica'; font-size: 12px; color: #585858; width: 50px; }
	        .texto2 { font-family: 'Helvetica'; font-size: 12px; color: #585858; width: 80px; }
	        .texto3 { font-family: 'Helvetica'; font-size: 12px; color: #585858; width: 150px; }
	        .texto4 { font-family: 'Helvetica'; font-size: 12px; color: #585858; width: 200px; }
	        .texto5 { font-family: 'Helvetica'; font-size: 12px; color: #585858; width: 250px; }
	        .btn_1 { font-family: 'Helvetica'; font-size: 12px; color: #FFFFFF; background-color: #999999; border-radius: 5px 5px 5px 5px; width: 60px; 
	            padding-top: 2px; padding-bottom: 2px; cursor: pointer; font-weight: bold; }
	        .btn_1:hover { background-color: #585858; }
	        .div_botones_operacion { margin-top: 10px; }
	        
	        #div_lista_opciones { width: 1220px; margin: 0 auto; overflow: hidden; border: dotted 1px #0099CC; border-radius: 10px 10px 10px 10px; 
	        background-color: #FFFFFF; margin-top: 20px; padding-bottom: 20px; }
	        
	        .div_tabla_opcion { margin-top: 15px; border: dotted 1px #0099CC; border-radius: 10px 10px 10px 10px; float: left; margin-left: 15px;  }
	        .tabla_opcion { color: #585858; border-collapse:collapse; }
	        .tabla_opcion thead { font-size: 12px; padding-left: 5px; padding-right: 5px; color: #0099CC; }
	        .tabla_opcion td { border-top: dotted 1px #0099CC; border-bottom: dotted 1px #0099CC; }
	        
	        #div_tabla_opciones { width: 550px; }
	        #div_tabla_caja_usuario { width: 620px; }
	        #div_tabla_usuario_grupo_transaccion { width: 260px;}

	        .opcion_padre { font-weight: bold; font-size: 12px; padding-left: 5px; padding-right: 5px; }
	        .opcion_hija{ font-size: 12px; padding-left: 5px; padding-right: 5px; }
	        
			.titulo_centro { background-color: #F0F0F0; font-family: Helvetica; font-size: 12px; font-weight: bold; }
			.opcion_padre { color: #0099CC;}
				
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
			$("#btn_guardar_caja").click(function()
			{
				var id_usuario = $("#id_usuario_conf").val();
			    
			    if(id_usuario > 0)
			    {
			    	$("#formulario").attr("action", "<?php echo $enlace_procesar_caja;?>");
			        $("#operacion").val("asignar_permisos");
			        $.ajax({
						type: "POST",
						url: $("#formulario").attr( 'action' ),
						data: $("#formulario").serialize(),
						success: function( response ) {
					       	$("#operacion").val("");
					       	alert("Cambios Guardados!");
					     }
					});	
	                                		       
			    }
			    else
	                alert("No ha seleccionado un Usuario o Centro");
			});
			
			$("#btn_guardar_almacen").click(function()
			{
				var id_usuario = $("#id_usuario_conf").val();
			    
			    if(id_usuario > 0)
			    {
			    	$("#formulario").attr("action", "<?php echo $enlace_procesar_almacen;?>");
			        $("#operacion").val("asignar_permisos");
			        $.ajax({
						type: "POST",
						url: $("#formulario").attr( 'action' ),
						data: $("#formulario").serialize(),
						success: function( response ) {
					       	$("#operacion").val("");
					       	alert("Cambios Guardados!");
					     }
					});	
	                                		       
			    }
			    else
	                alert("No ha seleccionado un Usuario o Centro");
			});
			
			$("#btn_guardar_motivo").click(function()
			{
				var id_usuario = $("#id_usuario_conf").val();
			    
			    if(id_usuario > 0)
			    {
			    	$("#formulario").attr("action", "<?php echo $enlace_procesar_motivo;?>");
			        $("#operacion").val("asignar_permisos");
			        $.ajax({
						type: "POST",
						url: $("#formulario").attr( 'action' ),
						data: $("#formulario").serialize(),
						success: function( response ) {
					       	$("#operacion").val("");
					       	alert("Cambios Guardados!");
					     }
					});
	                                		       
			    }
			    else
	                alert("No ha seleccionado un Usuario o Centro");
			});
			
			$("#btn_guardar_usuario_transaccion_grupo").click(function()
			{
				var id_usuario = $("#id_usuario_conf").val();
			    
			    if(id_usuario > 0)
			    {
			    	$("#formulario").attr("action", "<?php echo $enlace_procesar_transaccion;?>");
			        $("#operacion").val("asignar_permisos");
			        $.ajax({
						type: "POST",
						url: $("#formulario").attr( 'action' ),
						data: $("#formulario").serialize(),
						success: function( response ) {
					       	$("#operacion").val("");
					       	alert("Cambios Guardados!");
					     }
					});
	                                		       
			    }
			    else
	                alert("No ha seleccionado un Usuario o Centro");
			});
			
			$("#btn_guardar_usuario_transaccion_motivo").click(function()
			{
				var id_usuario = $("#id_usuario_conf").val();
			    
			    if(id_usuario > 0)
			    {
			    	$("#formulario").attr("action", "<?php echo $enlace_procesar_transaccion;?>");
					$("#operacion").val("asignar_permisos_motivo_transaccion");
					$.ajax({
						type: "POST",
						url: $("#formulario").attr( 'action' ),
						data: $("#formulario").serialize(),
						success: function( response ) {
					       	$("#operacion").val("");
					       	alert("Cambios Guardados!");
					     }
					});
				}
				
			});
			
			$("#btn_guardar_opcion").click(function()
			{
				$id_usuario_conf = $("#id_usuario_conf").val();
				
				if($id_usuario_conf > 0)
				{
					$("#formulario").attr("action", "<?php echo $enlace_procesar;?>");
					$("#operacion").val("asignar_opciones");
					$.ajax({
						type: "POST",
						url: $("#formulario").attr( 'action' ),
						data: $("#formulario").serialize(),
						success: function( response ) {
					       	$("#operacion").val("");
					       	alert("Cambios Guardados!");
					     }
					});	
					
				}
				
			});
		  
		});
		
		</script>
	</head>
	<body>
		<div id="div_main">
			<?php include ('../header.php');?>
			<div id = "contenido" align="center">
			    
				<form id="formulario" name="formulario" action="<?php echo $enlace_post; ?>" method="POST">
				    <input type="hidden" id="operacion" name="operacion"/>
				    <input type="hidden" id="id_usuario" name="id_usuario" value="<?php echo $id_usuario; ?>"/>
				    <input type="hidden" id="id_centro" name="id_centro" value="<?php echo $id_centro; ?>"/>
					<div id = "div_formulario">					
						<div id = "div_tabla_formulario_top">							
							<table>
								<tr>
									<td colspan="2" width="600px" align="center">
										<div id = "titulo">
											<span>Permiso de Usuarios</span>
										</div>
									</td>																											
								</tr>
							</table>							
						</div>						
						<div id="tabla_formulario_cuerpo">
							<div id="formulario_operacion">
								<table>
									<tr>
										<td width="150px"><span class = "etiqueta">Usuario</span></td>
										<td width="50px"><span class = "etiqueta" style="font-weight: bold;">:</span></td>
										<td>
										    <select id="id_usuario_conf" name="id_usuario_conf" class="texto5" onchange="CambiarUsuario()">
										        <option value="0">Seleccione...</option>
										        <?php
										        $objBLO = new UsuarioBLO();
                                                $lista = $objBLO->ListarTodos();
                                                    
                                                if(!is_null($lista))
                                                {
                                                    foreach($lista as $o)
                                                    {
                                                        if($id_usuario_conf == $o->id)
                                                            $selected = "selected='selected'";
                                                        else
                                                            $selected = "";
                                                        echo "<option value=\"$o->id\" $selected>$o->nombres $o->apellidos [$o->login]</option>";
                                                    }
                                                }  
										        ?>
										    </select>
										</td>
										<td></td>
									</tr>
									
									                                                                        
								</table>
							</div>
							<div id="div_lista_opciones" >
								<div id="div_tabla_opciones" class="div_tabla_opcion" >
									<div class="div_titulo_1"><span class="titulo_1">Opciones de Aplicación</span></div>
									<table id="tabla_opciones" class="tabla_opcion">
										<thead>
											<th width=50px></th>
											<th width=350px>Opción</th>
											<th width=90px"></th>
											<?php
											$span = 3;
											if(!is_null($lista_centros))
											{
												foreach($lista_centros as $c)
												{
													$largo = 10 * strlen($c->descripcion);
													echo "<th width=".$largo."px class=\"opc_centro\"></th>\n";
													$span = 3 + count($lista_centros);
												}												
											}
											?>
										</thead>
										<tbody>
										<?php
										$opBLO = new OpcionBLO();
										$lista_padres = $opBLO->ListarOpcionesPadre();										
										//echo "Opciones Centro Usuario: ".json_encode($lista_opciones_centro_usuario)."</br></br>";										
										if(!is_null($lista_padres))
										{
											foreach($lista_padres as $p)
											{
												$lista_hijos = $opBLO->ListarOpcionesHijasXOpcionPadreId($p->id);
												
												if(count($lista_hijos) > 0)
												{
													echo "<tr class=\"opcion_padre\"><td colspan=2><span class=\"titulo_centro\"><b>$p->descripcion</b></span></td>";
													echo "<td><span>Habilitado</span></td>";
													foreach($lista_centros as $c)
														echo "<td width=".$largo."px class=\"titulo_centro\" align='center'>".strtoupper($c->descripcion)."</th>\n";
													echo "</tr>\n";
													
													foreach($lista_hijos as $h)
													{
														if(!$h->flag_publica)
														{
															$checked = "";
																
															$tr = "<tr>";
															$tr = $tr."<td align=\"center\"><span class=\"opcion_hija\"><b>$h->opcion_key</b></span></td>";
															$tr = $tr."<td><span class=\"opcion_hija\">[$h->id]$h->descripcion</span></td>";
															
															if($h->flag_general)
															{
																if(!is_null($lista_opciones_usuario))
																	if(count($lista_opciones_usuario) > 0)
																	{
																		foreach($lista_opciones_usuario as $ou)
																		{
																			if($ou->id_opcion == $h->id)
																				if($ou->flag_habilitado)
																					$checked = "checked='checked'";
																		}
																	}
																
																$tr = $tr."<td align=\"center\"><input type=\"checkbox\" name=\"usuario_opciones[]\" value=\"$h->id\" $checked/></td>\n";
																$tr = $tr."<td colspan=\"".count($lista_centros)."\" class=\"titulo_centro\"></td>";
															}
															else
															{
																$tr = $tr."<td></td>";
																$centro_opciones = $opcBLO->ListarOpcionCentrosHabilitadosXIdOpcion($h->id);
																
																foreach($lista_centros as $c)
																		{
																			$checked = "";
																			
																			$check_centro_opcion = "<td class=\"titulo_centro\"></td>";
																			
																			if(!is_null($centro_opciones))
																				if(count($centro_opciones) > 0)
																				{
																					foreach($centro_opciones as $co)
																					{
																						if($co->id_centro == $c->id)
																						{
																							
																							if(!is_null($lista_opciones_centro_usuario))
																								if(count($lista_opciones_centro_usuario) > 0)
																								{
																									foreach($lista_opciones_centro_usuario as $ocu)
																									{
																										if($ocu->id_opcion == $h->id  && $ocu->id_centro == $c->id)
																											if($ocu->flag_habilitado)
																												$checked = "checked='checked'";
																									}
																								}
																							
																							$check_centro_opcion = "<td align=\"center\" class=\"titulo_centro\"><input type=\"checkbox\" name=\"usuario_opciones_centro_$c->id[]]\" value=\"$h->id\" $checked/></td>\n";
																						}
																					}
																								
																				}
																			$tr = $tr.$check_centro_opcion;
																		}


																
															}
															
															$tr = $tr."</tr>";
															echo $tr;	
														}	
														
													}
													
													
												}
												
											}
										}
										
										?>
										</tbody>
									</table>
									    
									
									<div class="div_botones_operacion">
										<table>
											<tr height="30px;">
												<td align="left" colspan="2" width="154px;">
													<div class="btn_1" id="btn_guardar_opcion" align="middle" title="Cambiar Contraseña" onclick="Guardar();" >
													    <span>Cambiar</span>												
													</div>
												</td>
												<td colspan="2" align="left">
													<div class="btn_1" id="btn_cancelar" align="middle" title="Cancelar Operación" onclick="Cancelar();">
														<span>Cancelar</span>
													</div>
												</td>
											</tr>
										</table>	
									</div>
								</div>
								
								
								
								
								
								<div id="div_tabla_caja_usuario" class="div_tabla_opcion">
									<div class="div_titulo_1"><span class="titulo_1">Cajas por Usuario</span></div>
									<table id="tabla_caja_usuario" class="tabla_opcion">
										<thead>
											<th width=240px>Caja</th>
											<th width=80px>Habilitado</th>
											<th width=80px>Responsable</th>
											<th width=80px>Ingreso</th>
											<th width=80px>Salida</th>
										</thead>
										<tbody>
										<?php
										$caBLO = new CajaBLO();
										$lista = $caBLO->ListarCajaXIdCentro($id_centro);
										$lista_caja_usuario = $caBLO->ListarCajaXIdUsuario($id_usuario_conf, $id_centro);
										
										if(!is_null($lista))
										{
											if(count($lista) > 0)
											{
												foreach($lista as $ca)
												{
													$check_habilitado = "";
													$check_responsable = "";
													$check_ingreso = "";
													$check_salida = "";
													
													foreach ($lista_caja_usuario as $cu)
													{
														if($cu->id_caja == $ca->id)
														{
															if($cu->habilitado == 1)
																$check_habilitado = "checked=\"yes\"";
															if($cu->flag_responsable == 1)
																$check_responsable = "checked=\"yes\"";
															if($cu->flag_ingreso == 1)
																$check_ingreso = "checked=\"yes\"";
															if($cu->flag_salida == 1)
																$check_salida = "checked=\"yes\"";
														}
													}
													
													?>
												<tr>
													<td><span class="opcion_hija"><?php echo "[$ca->id]$ca->descripcion";?></span></td>
													<td align="center"><input type="checkbox" name="caja_habilitado[]" value="<?php echo "$ca->id\" $check_habilitado";?> /></td>
													<td align="center"><input type="checkbox" name="caja_responsable[]" value="<?php echo "$ca->id\" $check_responsable";?> /></td>
													<td align="center"><input type="checkbox" name="caja_ingreso[]" value="<?php echo "$ca->id\" $check_ingreso";?> /></td>
													<td align="center"><input type="checkbox" name="caja_salida[]" value="<?php echo "$ca->id\" $check_salida";?> /></td>
												</tr>
												<?php
												}
											}
										}
										?>
										</tbody>
									</table>
									<div class="div_botones_operacion">
										<table>
											<tr height="30px;">
												<td align="left" colspan="2" width="154px;">
													<div class="btn_1" id="btn_guardar_caja" align="middle" title="Guardar Permisos" >
													    <span>Cambiar</span>												
													</div>
												</td>
												<td colspan="2" align="left">
													<div class="btn_1" id="btn_cancelar" align="middle" title="Cancelar Operación" onclick="Cancelar();">
														<span>Cancelar</span>
													</div>
												</td>
											</tr>
										</table>	
									</div>
								</div>
								
								
								
								<div id="div_tabla_almacen_usuario" class="div_tabla_opcion">
									<div class="div_titulo_1"><span class="titulo_1">Almacenes por Usuario</span></div>
									<table id="tabla_almacen_usuario" class="tabla_opcion">
										<thead>
											<th width=200px>Almacen</th>
											<th width=100px>Habilitado</th>
											<th width=100px>Ingreso</th>
											<th width=100px>Salida</th>
										</thead>
										<tbody>
										<?php
										$almBLO = new AlmacenBLO();
										$lista = $almBLO->ListarAlmacenXIdCentro($id_centro);
										$lista_almacen_usuario = $almBLO->ListarAlmacenXIdUsuarioIdCentro($id_usuario_conf, $id_centro);
										
										if(!is_null($lista))
										{
											if(count($lista) > 0)
											{
												foreach($lista as $a)
												{
													$check_habilitado = "";
													$check_entrada = "";
													$check_salida = "";
													
													foreach ($lista_almacen_usuario as $au)
													{
														if($au->id_almacen == $a->id)
														{
															if($au->flag_habilitado == 1)
																$check_habilitado = "checked=\"yes\"";
															if($au->flag_entrada == 1)
																$check_entrada = "checked=\"yes\"";
															if($au->flag_salida == 1)
																$check_salida = "checked=\"yes\"";
														}
													}
													
													?>
												<tr>
													<td><span class="opcion_hija"><?php echo "[$a->id]$a->descripcion";?></span></td>
													<td align="center"><input type="checkbox" name="almacen_habilitado[]" value="<?php echo "$a->id\" $check_habilitado";?> /></td>
													<td align="center"><input type="checkbox" name="almacen_entrada[]" value="<?php echo "$a->id\" $check_entrada";?> /></td>
													<td align="center"><input type="checkbox" name="almacen_salida[]" value="<?php echo "$a->id\" $check_salida";?> /></td>
												</tr>
												<?php
												}
											}
										}
										?>
										</tbody>
									</table>
									<div class="div_botones_operacion">
										<table>
											<tr height="30px;">
												<td align="left" colspan="2" width="154px;">
													<div class="btn_1" id="btn_guardar_almacen" align="middle" title="Guardar Permisos" >
													    <span>Cambiar</span>												
													</div>
												</td>
												<td colspan="2" align="left">
													<div class="btn_1" id="btn_cancelar" align="middle" title="Cancelar Operación" onclick="Cancelar();">
														<span>Cancelar</span>
													</div>
												</td>
											</tr>
										</table>	
									</div>
								</div>
								
								
								
								<div id="div_tabla_movimiento_usuario" class="div_tabla_opcion">
									<div class="div_titulo_1"><span class="titulo_1">Motivos de Movimiento por Usuario</span></div>
									<table id="tabla_movimiento_usuario" class="tabla_opcion">
										<thead>
											<th width=200px>Motivo</th>
											<th width=100px>Habilitado</th>
										</thead>
										<tbody>
										<?php
										$movBLO = new MovimientoBLO();
										$lista = $movBLO->ListarMotivoTodos();
										$lista_movimiento_usuario = $movBLO->ListarMotivoXIdUsuario($id_usuario_conf, $id_centro);
										
										if(!is_null($lista))
										{
											if(count($lista) > 0)
											{
												foreach($lista as $m)
												{
													$check_habilitado = "";
													
													foreach ($lista_movimiento_usuario as $mu)
													{
														if($mu->id_movimiento_motivo == $m->id)
														{
															if($mu->flag_habilitado == 1)
																$check_habilitado = "checked=\"yes\"";
														}
													}
													
													?>
												<tr>
													<td><span class="opcion_hija"><?php echo "[$m->id]".strtoupper($m->descripcion);?></span></td>
													<td align="center"><input type="checkbox" name="motivo_habilitado[]" value="<?php echo "$m->id\" $check_habilitado";?> /></td>
												</tr>
												<?php
												}
											}
										}
										?>
										</tbody>
									</table>
									<div class="div_botones_operacion">
										<table>
											<tr height="30px;">
												<td align="left" colspan="2" width="154px;">
													<div class="btn_1" id="btn_guardar_motivo" align="middle" title="Guardar Permisos" >
													    <span>Cambiar</span>												
													</div>
												</td>
												<td colspan="2" align="left">
													<div class="btn_1" id="btn_cancelar" align="middle" title="Cancelar Operación" onclick="Cancelar();">
														<span>Cancelar</span>
													</div>
												</td>
											</tr>
										</table>	
									</div>
								</div>
								
								
								<div id="div_tabla_usuario_grupo_transaccion" class="div_tabla_opcion">
									<div class="div_titulo_1"><span class="titulo_1">Grupo de Transacciones por Usuario</span></div>
									<table id="tabla_usuario_grupo_transaccion" class="tabla_opcion">
										<thead>
											<th width=180px>Grupo Transacción</th>
											<th width=100px>Habilitado</th>
										</thead>
										<tbody>
										<?php
										$traBLO = new TransaccionBLO();
										$lista = $traBLO->ListarGrupoTransaccionTodos();
										$lista_grupo_transacciones = $traBLO->ListarGruposTransaccionHabilitadosXIdUsuarioIdCentro($id_usuario_conf, $id_centro);
										if(!is_null($lista))
										{
											if(count($lista) > 0)
											{
												foreach($lista as $tg)
												{
													$check_habilitado = "";
													
													foreach ($lista_grupo_transacciones as $utg)
													{
														if($tg->id == $utg->id_transaccion_grupo)
														{
															if($utg->flag_habilitado == 1)
																$check_habilitado = "checked=\"yes\"";
														}
													}
													
													?>
												<tr>
													<td><span class="opcion_hija"><?php echo "[$tg->id]".strtoupper($tg->descripcion);?></span></td>
													<td align="center"><input type="checkbox" name="usuario_transaccion_grupo_habilitado[]" value="<?php echo "$tg->id\" $check_habilitado";?> /></td>
												</tr>
												<?php
												}
											}
										}
										?>
										</tbody>
									</table>
									<div class="div_botones_operacion">
										<table>
											<tr height="30px;">
												<td align="left" colspan="2" width="154px;">
													<div class="btn_1" id="btn_guardar_usuario_transaccion_grupo" align="middle" title="Guardar Permisos" >
													    <span>Cambiar</span>												
													</div>
												</td>
												<td colspan="2" align="left">
													<div class="btn_1" id="btn_cancelar" align="middle" title="Cancelar Operación" onclick="Cancelar();">
														<span>Cancelar</span>
													</div>
												</td>
											</tr>
										</table>	
									</div>									
								</div>
								
								
								<div id="div_tabla_usuario_transaccion_motivo" class="div_tabla_opcion">
									<div class="div_titulo_1"><span class="titulo_1">Motivos de Transacción por Usuario</span></div>
									<table id="tabla_usuario_transaccion_motivo" class="tabla_opcion">
										<thead>
											<th width=200px>Motivo Transacción</th>
											<th width=100px>Habilitado</th>
										</thead>
										<tbody>
										<?php
										$traBLO = new TransaccionBLO();
										$lista = $traBLO->ListarMotivoTransaccionTodos();
										$lista_motivo_transacciones = $traBLO->ListarMotivoTransaccionesXIdUsuario($id_usuario_conf);
										if(!is_null($lista))
										{
											if(count($lista) > 0)
											{
												foreach($lista as $tm)
												{
													$check_habilitado = "";
													
													foreach ($lista_motivo_transacciones as $utm)
													{
														if($tm->id == $utm->id_transaccion_motivo)
														{
															if($utm->flag_habilitado == 1)
																$check_habilitado = "checked=\"checked\"";
														}
													}
													
													?>
												<tr>
													<td><span class="opcion_hija"><?php echo "[$tm->id]".strtoupper($tm->descripcion);?></span></td>
													<td align="center"><input type="checkbox" name="usuario_transaccion_motivo_habilitado[]" value="<?php echo "$tm->id\" $check_habilitado";?> /></td>
												</tr>
												<?php
												}
											}
										}
										?>
										</tbody>
									</table>
									<div class="div_botones_operacion">
										<table>
											<tr height="30px;">
												<td align="left" colspan="2" width="154px;">
													<div class="btn_1" id="btn_guardar_usuario_transaccion_motivo" align="middle" title="Guardar Permisos" >
													    <span>Cambiar</span>												
													</div>
												</td>
												<td colspan="2" align="left">
													<div class="btn_1" id="btn_cancelar" align="middle" title="Cancelar Operación" onclick="Cancelar();">
														<span>Cancelar</span>
													</div>
												</td>
											</tr>
										</table>	
									</div>									
								</div>
								
								
								
							</div>
															
						</div>					
					</div>
				</form>
			</div>
		</div>
		
		
		
	</body>
</html>

