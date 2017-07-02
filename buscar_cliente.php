<?php

include ('clases/general.php');
include ('clases/cliente.php');
include ('clases/usuario.php');
include ('clases/cuenta_venta.php');
//include ('security.php');
$enlace = "buscar_cliente.php?key=$usr_key";

/*if(isset($_POST['opcion']))
	$opcion = $_POST['opcion'];
else 
	$opcion = 0;
	
*/	

if(isset($_POST['operacion']))
	$operacion = $_POST['operacion'];
else 
	$operacion = "";

if(isset($_POST['tipo_documento']))
	$tipo_documento = $_POST['tipo_documento'];
else
	$tipo_documento = 0;

if(isset($_POST['nro_documento']))
	$nro_documento = $_POST['nro_documento'];
else
	$nro_documento = "";

if(isset($_POST['apellidos']))
	$apellidos = $_POST['apellidos'];
else
	$apellidos = "";

if(isset($_POST['cliente']))
	$cliente = $_POST['cliente'];
else
	$cliente = 0;

setcookie("cliente",0);

if($operacion == "asignar" && $cliente > 0)
{
	setcookie("cliente", $cliente); 
	$operacion = "cerrar";
}

?>


<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN"
"http://www.w3.org/TR/html4/strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>RODO</title>
		<meta name="author" content="Jesus Rodriguez" />
		<!-- Date: 2011-11-28 -->
		<link rel="stylesheet" href="style.css" type="text/css" />
		<script type="text/javascript">
		
			function IsNumeric(expression)
			{
			    return (String(expression).search(/^\d+$/) != -1);
			}
			
			function terminate()
			{
			  	<?php
		        if($operacion == "cerrar")				
					echo "self.close();";
				?>
			}
			
			function Buscar()
			{
				var tipo_documento = document.cliente.tipo_documento.value;
				var nro_documento = document.cliente.nro_documento.value;
				var apellidos = document.cliente.apellidos.value;				
				
				var resultado = 0;
				var mensaje = "";
				
				if(tipo_documento != 0 && tipo_documento != "Seleccione...")
					resultado = 1;
				
				if(nro_documento != "")
					resultado = 2;
				
				if(apellidos != "")
					resultado = 3;
													
				if(resultado  == 0)
					alert('Al menos debe ingresar un valor.');
				else
				{
					document.cliente.operacion.value = "buscar";
					document.cliente.submit();
				}
				
			}
		
			function Seleccionar(cliente)
			{
				document.cliente.cliente.value = cliente;
				document.cliente.operacion.value = "asignar";				 
				document.cliente.submit();
				
			}	

		</script>
		<style type="text/css">
			table.resultado {
				border-left: dotted 1px #3399FF;
				border-right: dotted 1px #3399FF;
				
			}
			table.resultado td {
				border-bottom: dotted 1px #3399FF;
				
				border-spacing: 0px;								
			}
</style>


	</head>
	<body onload="terminate()">		
		<div style="float: left;">
			<form name="cliente" action="<?php echo $enlace; ?>" method="POST">
				<input type="hidden" name="operacion" />
				<input type="hidden" name="cliente" />
				<table style="border: dotted 1px #3399FF; width:750px; background-color: #E6F2FF; color: #585858; font-family: Helvetica; margin-left: 10px;" class="clase12">
					<tr>
						<td colspan="2">
							<div style="float: left; width: 400px;">
								<img src="images/logo-delocal.png" style="width:63px; height:72px;"/></br>
								<span style="font-size:11px; font-weight: bold">Ingrese al menos un valor para la búsqueda de clientes:</span>	
							</div>						
						</td>					
					</tr>
					<tr height="5px;">
						
					</tr>
					<tr>
						<td width="150px;">
							<span style="font-weight: bold; font-size:11px;">Tipo de Documento:</span>
						</td>
						<td >
							<select name="tipo_documento" style="font-size: 11px; width: 200px;" onchange="submit();" class="clase12">
								<option style="color: #585858;">Seleccione...</option>
								<?php
																
									$tipodocBLO = new TipoDocumentoBLO();
									
									$tipos = $tipodocBLO->Listar('');
									if(count($tipos) > 0)
									{
										foreach ($tipos as $t)
										{
											if($t->id == $tipo_documento)
												$selected = "selected = 'selected'";
											else
												$selected = "";
											echo "<option value='$t->id' $selected>$t->descripcion</option>";
										}
									}	
								?>
							</select>
						</td>
					</tr>
					<tr>
						<td>
							<span style="font-weight: bold; font-size:11px;">Nro de Documento:</span>
						</td>
						<td>
							<input name="nro_documento" value="<?php echo $nro_documento; ?>" class="clase12" maxlength="20" style="font-size:11px; width: 200px;"/>
						</td>
					</tr>
					<tr>
						<td><span style="font-weight: bold; font-size:11px;">Raz. Social o Apellidos:</span></td>
						<td><input name="apellidos" value="<?php echo $apellidos; ?>" class="clase12" maxlength="50" style="font-size:11px; width: 200px;"/></td>
					</tr>
					<tr>
						<td colspan="2">
							<input type="button" class="clase12" style="font-size: 11px;" value="Buscar" onclick="Buscar()"/>
							<input type="button" class="clase12" style="font-size: 11px;" value="Cerrar" onclick="self.close()"/>
						</td>
					</tr>
					<?php
					if($operacion == "buscar")
					{
						$cliBLO = new ClienteBLO();
						$clientes = $cliBLO->ListarXCondiciones($tipo_documento, $nro_documento, $apellidos);
						if($clientes != null)
						{?>
							<tr>
								<td colspan="2">
									<span style="font-weight: bold; font-size:11px;"><?php echo count($clientes)." Registro(s) encontrado(s)."?></span>
								</td>
							</tr>
							<tr>
								<td colspan="2">
									<table class="resultado" style="width:750px; font-family:Helvetica; font-size:11px;">
										<tr>
											<td align="center" style="font-weight: bold; background-color: #3399FF; color: #FFFFFF;">Tipo Doc.</td>
											<td align="center" style="font-weight: bold; background-color: #3399FF; color: #FFFFFF;">Nro Doc.</td>
											<td align="center" style="font-weight: bold; background-color: #3399FF; color: #FFFFFF;">Nombres y Apellidos</td>											
											<td align="center" style="font-weight: bold; background-color: #3399FF; color: #FFFFFF;">Teléfono(s)</td>
											<td align="center" style="font-weight: bold; background-color: #3399FF; color: #FFFFFF;">Email</td>
											<td align="center" style="font-weight: bold; background-color: #3399FF; color: #FFFFFF;">Keyword</td>
											<td align="center" style="font-weight: bold; background-color: #3399FF; color: #FFFFFF;">Creado por</td>
										</tr>
										<?php
											foreach ($clientes as $cli) 
											{
												$tels = "";
												if($cli->telefonos != null)
												{
													
													if(count($cli->telefonos) > 0)
														foreach($cli->telefonos as $t)
															$tels = $tels . " - ".$t->telefono;														
												}												
												$tels = substr($tels, 3);?>
												<tr onmouseover="this.style.backgroundColor = '#FFFCCC'; this.style.cursor='pointer'" 
												onmouseout="this.style.backgroundColor = '#E6F2FF';" onclick="Seleccionar(<?php echo $cli->id?>)">
													<td><?php echo $cli->tipo_documento;?></td>
													<td><?php echo $cli->nro_documento;?></td>									
													<td><?php echo $cli->nombres. " ". $cli->apellidos;?></td>
													<td><?php echo $tels;?></td>
													<td><?php echo $cli->email;?></td>
													<td><?php echo $cli->keyword;?></td>
													<td><?php echo $cli->usuario_creacion;?></td>
												</tr>
											<?php 
											}?>
									</table>
								</td>
							</tr>									
							
						<?php
						}
						else
						{?>
						<tr>
							<td colspan="2">
								<span style="font-weight: bold; font-size:11px; color:red;">No se encontraron registros para la búsqueda.</span>
							</td>
						</tr>
					<?php
						}
					}
					?>
				</table>
			</div>
		</form>
	</body>
</html>