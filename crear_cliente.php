<?php

include ('usuario.php');
include ('general.php');
include ('cliente.php');
include ('cuenta_venta.php');
include ('security.php');
$enlace = "crear_cliente.php?key=$usr_key";

if(isset($_POST['opcion']))
	$opcion = $_POST['opcion'];
else 
	if(isset($_GET['opcion']))
		$opcion = $_GET['opcion'];
	else 
		$opcion = 0;
	
if($opcion != 2 && $opcion != 3)
	setcookie("cliente");

if($opcion == 2)
	$operacion = "mostrar";

$id_cliente = 0;
	
if(isset($_POST['operacion']))
	$operacion = $_POST['operacion'];
else 
	$operacion = "";
	
if(isset($_POST['hid_tipo_documento']))
	$tipo_documento = $_POST['hid_tipo_documento'];
else
	$tipo_documento = 0;

if(isset($_POST['nro_documento']))
	$nro_documento = $_POST['nro_documento'];
else
	$nro_documento = "";

if(isset($_POST['nombres']))
	$nombres = $_POST['nombres'];
else
	$nombres = "";

if(isset($_POST['apellidos']))
	$apellidos = $_POST['apellidos'];
else
	$apellidos = "";

if(isset($_POST['keyword']))
	$keyword = $_POST['keyword'];
else
	$keyword = "";

if(isset($_POST['email']))
	$email = $_POST['email'];
else
	$email = "";

if(isset($_POST['telefonos']))
	$telefonos= $_POST['telefonos'];
else
	$telefonos = "";

//$telefonos_arr = array();

if($telefonos != "")
	$telefonos_arr = split(";", $telefonos);

if(isset($_GET['id']))
	$id_cliente = $_GET['id'];
else 
{
	if(isset($_POST['id_cliente']))
		$id_cliente = $_POST['id_cliente'];
	else 
		$id_cliente = 0;	
}

if($operacion == "mostrar")
{
	if(isset($_COOKIE['cliente']))
		$id_cliente = $_COOKIE['cliente'];
	else
		$id_cliente = 0;
	
	if($id_cliente == 0)
		header("Location:$enlace");
}
if(($operacion == "mostrar" || $operacion == "editar") && $id_cliente > 0)
{
	
	$cliBLO = new ClienteBLO();
	
	$cliente = $cliBLO->RetornarClienteXId($id_cliente);
	$tipo_documento = $cliente->id_tipo_documento;
	$nro_documento = $cliente->nro_documento;
	$nombres = $cliente->nombres;
	$apellidos = $cliente->apellidos;
	$keyword = $cliente->keyword;
	$email = $cliente->email;
	$telefonos_arr = array();
	$telefonos = "";	
	
	if($cliente->telefonos != null)
		if(count($cliente->telefonos) > 0)
		{
			foreach($cliente->telefonos as $t)
			{
				if($t->habilitado)	
					$telefonos_arr[] = $t->telefono;
				$telefonos = $telefonos.";".$t->telefono; 		
			}
			$telefonos = substr($telefonos,1);
		}	
}

$resultado = NULL;

if($operacion == "crear" || $operacion == "guardar")
{
	
	$cliBLO = new ClienteBLO();
	$cliente = new Cliente();
	//$resultado = new OperacionResultado();
	
	$cliente->id = $id_cliente;
	$cliente->id_tipo_documento = $tipo_documento;
	$cliente->nro_documento = $nro_documento;
	$cliente->nombres = strtoupper($nombres);
	$cliente->apellidos = strtoupper($apellidos);
	$cliente->keyword = strtoupper($keyword);
	$cliente->email = $email;
	$cliente->id_usuario_creacion = $cUsr->id;	
	$tels = array();	
	if($telefonos_arr != null)
	{
		foreach($telefonos_arr as $t)
		{
			$tel = new ClienteTelefono();
			$tel->telefono = $t;
			$tel->habilitado = true;
			$tels[] = $tel;
		}
		
		$cliente->telefonos = $tels;	
	}
	
	if($operacion == "crear")
		$resultado = $cliBLO->Registrar($cliente);
	if($operacion == "guardar" && $id_cliente > 0)	
		$resultado = $cliBLO->Actualizar($cliente);
	
	if($resultado != NULL)
	{
		if($resultado->isOK)
		{
			$opcion = 0;
			$operacion = "";
			$tipo_documento = 0;
			$nro_documento = "";
			$nombres = "";
			$apellidos = "";
			$keyword = "";
			$telefonos = "";
			$email = "";
			setcookie("cliente");
		}		
	}
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
			
			function LimpiarValores(opcion)
			{
				location.href = '<?php echo $enlace."&opcion="?>' + opcion;
			}
			
			function Desconectarse()
			{
				location.href = "login.php";
			}
			
			function EliminarTelefono(idx)
			{
				var telefonos = document.cliente.telefonos.value;
				var telefonos_arr = telefonos.split(";");
				
				if(telefonos_arr.length > 1)
				{					
					telefonos_arr.splice(idx,1);
					
					for(i = 0; i < telefonos_arr.length; i++)
					{
						
						if(i == 0)
							telefonos = telefonos_arr[i];
						else
							telefonos = telefonos + ";" + telefonos_arr[i]; 
					}
					
					document.cliente.telefonos.value = telefonos;					
				}
				else
					document.cliente.telefonos.value = "";
				
				document.cliente.submit();
			}
			
			function AgregarTelefono()
			{
				var telefonos = document.cliente.telefonos.value;
				var telefono = document.cliente.telefono.value;
				if(telefonos == '')				
					document.cliente.telefonos.value = telefono;
				else
					document.cliente.telefonos.value = telefonos + ';' + telefono;
				
				document.cliente.submit();
			}
			
			function CambiarTipoDocumento()
			{
				document.cliente.hid_tipo_documento.value = document.cliente.tipo_documento.value;
				document.cliente.submit();
			}
			
			function Editar()
			{				
				//location.href = '//php echo $enlace."&opcion=2&id=$id_cliente"; ?>';
				document.cliente.tipo_documento.value = document.cliente.hid_tipo_documento.value;				
				document.cliente.opcion.value = 2;
				document.cliente.submit();	
			}
			
			function CrearCliente()
			{
				var tipo_documento = document.cliente.tipo_documento.value;
				var nro_documento = document.cliente.nro_documento.value;

				var telefonos = document.cliente.telefonos.value;
				
				var resultado = true;
				var mensaje = "";
				
				if(tipo_documento == 0 || tipo_documento == "Seleccione...")
				{
					mensaje += ", Tipo de Documento";					
					resultado = false;
				}
				
				if(nro_documento == "")
				{
					mensaje += ", Nro de Documento";
					resultado = false;
				}
				
				if(tipo_documento > 0)
				{
					if(tipo_documento != 2)
					{
						var nombres = document.cliente.nombres.value;
						if(nombres == "")
						{	
							mensaje += ", Nombres"
							resultado = false;
						}
					}
					
					var apellidos = document.cliente.apellidos.value;
					
					if(apellidos == "")
					{
						if(tipo_documento == 2)
							mensaje += ", Raz. Social";
						else
							mensaje += ", Apellidos";
							
						resultado = false;
					}						
				}
				
				if(telefonos == "")
				{
					mensaje += ", Teléfonos";
					resultado = false; 
				}				
				if(!resultado)
				{
					mensaje = "Hay valores errados en: " + mensaje.substring(1);
					alert(mensaje);
				}								
				else
				{
					document.cliente.operacion.value = "crear";
					document.cliente.submit();
				}
			}
			
			function GuardarCambios()
			{
				var tipo_documento = document.cliente.tipo_documento.value;
				var nro_documento = document.cliente.nro_documento.value;

				var telefonos = document.cliente.telefonos.value;
				
				var resultado = true;
				var mensaje = "";
				
				if(tipo_documento == 0 || tipo_documento == "Seleccione...")
				{
					mensaje += ", Tipo de Documento";					
					resultado = false;
				}
				
				if(nro_documento == "")
				{
					mensaje += ", Nro de Documento";
					resultado = false;
				}
				
				if(tipo_documento > 0)
				{
					if(tipo_documento != 2)
					{
						var nombres = document.cliente.nombres.value;
						if(nombres == "")
						{	
							mensaje += ", Nombres"
							resultado = false;
						}
					}
					
					var apellidos = document.cliente.apellidos.value;
					
					if(apellidos == "")
					{
						if(tipo_documento == 2)
							mensaje += ", Raz. Social";
						else
							mensaje += ", Apellidos";
							
						resultado = false;
					}						
				}
				
				if(telefonos == "")
				{
					mensaje += ", Teléfonos";
					resultado = false; 
				}				
				if(!resultado)
				{
					mensaje = "Hay valores errados en: " + mensaje.substring(1);
					alert(mensaje);
				}								
				else
				{
					
					document.cliente.operacion.value = "guardar";
					document.cliente.submit();
				}
			}
			
			function CargarResultados()
			{
				<?php
				if($resultado != null)
				{
					if($resultado->isOK)
					{?>
						alert('<?php echo $resultado->mensaje;?>');	
					<?php
						$resultado = NULL;
					}
				}
				?>
			}
			
			function CambiarOpcion()
			{
				opcion = document.cliente.opcion.value;
				if(opcion == 1)
					LimpiarValores(opcion);
				if(opcion == 2 || opcion == 3)
				{
					mywindow = showModalDialog("buscar_cliente.php?key=<?php echo $usr_key;?>", "", "dialogHeight:600px; dialogWidth:1300px; center:yes");
					document.cliente.operacion.value = "mostrar";
    				document.cliente.submit();
				}
			}

		</script>
	</head>
	<body onload="CargarResultados()">
		
		<?php 
			include("header.php");
		?>
		<div id="producto" style="float: left; padding-left: 20px; padding-top: 20px; width: 1000px; font-family: Helvetica;">
			
			<form name="cliente" action="<?php echo $enlace; ?>" method="POST" >
				<input type="hidden" name="operacion"/>				
				<input type="hidden" name="telefonos" value="<?php echo $telefonos;?>"/>
				<input type="hidden" name="id_cliente" value="<?php echo $id_cliente;?>"/>
				
				<table id="tb_cliente" style="border: dotted 1px #3399FF; width:1000px; background-color: #E6F2FF; color: #585858;" class="clase12">
					<tr style="height: 40px;">
						<td colspan="4" align="center">
							<span style="font-weight: bold; font-size:14px;">CREACION Y MODIFICACION DE CLIENTES</span>	
						</td>						
					</tr>
					<tr>
						<td width="200px;">
							<span style="font-weight: bold;">Opción General:</span>
						</td>
						<td>
							<select name="opcion" style="width:200px; font-size: 11px;" onchange="CambiarOpcion()">
								<option value="0" <? if($opcion == 0) echo "selected = 'selected'";?>>Seleccione...</option>
								<option value="1" <? if($opcion == 1) echo "selected = 'selected'";?>>Crear Cliente</option> 
								<option value="2" <? if($opcion == 2) echo "selected = 'selected'";?>>Editar Cliente</option>
								<option value="3" <? if($opcion == 3) echo "selected = 'selected'";?>>Buscar Cliente</option>								
							</select>
						</td>
						<td colspan="2">							
						</td>
					</tr>
					<tr><td colspan="4"><hr></td></tr>
					<?php
					if($opcion > 0)
					{?>
					<tr>
						<td colspan="4">
							<span style="font-weight: bold; font-size:12px;">Datos del Cliente:</span>
						</td>
					</tr>
					<tr>
						<td>
							<span style="font-weight: bold; font-size:11px;">Tipo de Documento:</span>
						</td>
						<td style="width: 200px;">
							<input type="hidden" name="hid_tipo_documento" value="<?php echo $tipo_documento; ?>"/>
							<select name="tipo_documento" style="font-size: 11px; width: 200px;" onchange="CambiarTipoDocumento();" class="clase12" 
							<?php if($opcion == 3) echo "disabled = 'disabled'";?> >
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
						<td>
							<span style="font-weight: bold; font-size:11px;">Nro de Documento:</span>
						</td>
						<td>
							<input name="nro_documento" value="<?php echo $nro_documento; ?>" class="clase12" maxlength="20" style="font-size:11px; width: 200px;"
							<?php if($opcion == 3) echo "readonly='readonly';"?>/>
						</td>
					</tr>
					<tr>
						<?php
						if($tipo_documento > 0)
						{
							if($tipo_documento == 2)
							{?>
							<td><span style="font-weight: bold; font-size:11px;">Razon Social:</span></td>
							<td><input name="apellidos" value="<?php echo $apellidos; ?>" class="clase12" maxlength="80" style="font-size:11px; width: 200px;"
								<?php if($opcion == 3) echo "readonly='readonly';"?>/></td>
							<td colspan = 2></td>
							<?php
							}
							else
							{							
							?>
							<td><span style="font-weight: bold; font-size:11px;">Nombres:</span></td>
							<td><input name="nombres" value="<?php echo $nombres; ?>" class="clase12" maxlength="40" style="font-size:11px; width: 200px;" <?php if($opcion == 3) echo "readonly='readonly';"?>/></td>
							<td><span style="font-weight: bold; font-size:11px;">Apellidos:</span></td>
							<td><input name="apellidos" value="<?php echo $apellidos; ?>" class="clase12" maxlength="50" style="font-size:11px; width: 200px;" <?php if($opcion == 3) echo "readonly='readonly';"?>/></td>
							<?php
							}
						}?>						
					</tr>
					<tr>
						<td><span style="font-weight: bold; font-size:11px;">Palabra Clave:</span></td>
						<td><input name="keyword" class="clase12" value="<?php echo $keyword; ?>" maxlength="80" style="font-size:11px; width: 200px;" <?php if($opcion == 3) echo "readonly='readonly';"?>/></td>
						<td colspan = 2></td>
					</tr>
					<tr><td colspan="4"><hr></td></tr>
					<tr>
						<td colspan="4">
							<span style="font-weight: bold; font-size:12px;">Datos de Contacto:</span>
						</td>
					</tr>
					<tr>
						<td><span style="font-weight: bold; font-size:11px;">Email:</span></td>
						<td><input name="email" value="<?php echo $email; ?>" class="clase12" maxlength="80" style="font-size:11px; width: 200px;" <?php if($opcion == 3) echo "readonly='readonly';"?>/></td>
						<td colspan = 2></td>
					</tr>
					<?php
					if(count($telefonos_arr) > 0)
					{
						for($i = 0; $i < count($telefonos_arr); $i++)
						{?>
						<tr>
							<td><span style="font-weight: bold; font-size:11px;">Teléfono <?php echo " ".$i+1;?>:</span></td>
							<td><input readonly=readonly name="telefono<?php echo $i+1;?>" value="<?php echo $telefonos_arr[$i]; ?>" 
								class="clase12" maxlength="20" style="font-size:11px; width: 100px;"/>
								<?php
								if($opcion != 3)
								{?>
								<input type="button" class="clase12" style="font-size: 11px;" value="Eliminar" onclick="EliminarTelefono(<?php echo $i;?>)"/>
								<?php
								}?>
							</td>
							<td colspan = 2></td>
						</tr>
						<?php
						}
					}
					?>
					<?php
					if($opcion > 0 && $opcion != 3)
					{?>
					<tr>
						<td><span style="font-weight: bold; font-size:11px;">Nuevo Teléfono:</span></td>
						<td><input name="telefono" class="clase12" maxlength="20" style="font-size:11px; width: 100px;"/>
							<input type="button" class="clase12" style="font-size: 11px;" value="Agregar" onclick="AgregarTelefono()"/>
						</td>
						<td colspan = 2></td>
					</tr>
					<tr><td colspan="4"><hr></td></tr>
					<?php
					}
					if($opcion == 1)
					{
					?>
					<tr>
						<td>
							<input type="button" class="clase12" style="font-size: 11px;" value="Crear Cliente" onclick="CrearCliente()"/>							
						</td>
						<td colspan="3">
							<span style="font-size:11px; color: red;"><?php echo $resultado->mensaje;?></span>
						</td>						
					</tr>
					<?php
					}
					if($opcion == 2)
					{
					?>
					<tr>
						<td>
							<input type="button" class="clase12" style="font-size: 11px;" value="Guardar Cambios" onclick="GuardarCambios()"/>							
						</td>
						<td colspan="3">						
						</td>
					</tr>
					<?php
					}
					if($opcion == 3)
					{?>
					<tr>
						<td>
							<input type="button" class="clase12" style="font-size: 11px;" value="OK" onclick="location.href = '<?php echo $enlace?>'"/>							
						</td>
						<td>
							<input type="button" class="clase12" style="font-size: 11px;" value="Editar" onclick="Editar()"/>							
						</td>
						<td colspan="2">						
						</td>
					</tr>
					<?php
					}
					
				}?>					
				</table>
			</form>
		</div>
	</body>
</html>