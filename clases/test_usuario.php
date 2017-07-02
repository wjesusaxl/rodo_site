<?php
include('clases/general.php');
include('clases/enc_dec.php');
include('clases/usuario.php');
include('clases/opcion.php');
include('clases/centro.php');

if(isset($_POST['objeto']))
	$objeto = $_POST['objeto'];
else
	$objeto = 0;



if(isset($_POST['operacion']))
	$operacion = $_POST['operacion'];
else
	$operacion = "";

if($operacion == "crear_usuario")
{
	$usrBLO = new UsuarioBLO();
	$usuario = new Usuario();
	
	$key = random_string();
	
	$usuario->nombres = strtoupper($_POST['nombres']);
	$usuario->apellidos = strtoupper($_POST['apellidos']);
	$usuario->dni = strtoupper($_POST['dni']);
	$usuario->login = strtoupper($_POST['login']);
	$usuario->password_enc = encrypt($_POST['password'], $key);
	$usuario->password_key = $key;
	$usuario->flag_habilitado = $_POST['flag_habilitado'];
	$usuario->flag_cambiar_password = $_POST["flag_cambiar_password"];
	
	$resultado = $usrBLO->Registrar($usuario);
	if($resultado != null)
		echo $resultado->mensaje;	
}

if($operacion == "crear_opcion")
{
	$opcBLO = new OpcionBLO();
	$opcion = new Opcion();
	
	$opcion->opcion_key = $_POST['opc_key'];
	$opcion->codigo = $_POST['opc_codigo'];
	$opcion->estado = $_POST['opc_estado'];
	$opcion->descripcion = $_POST['opc_descripcion'];
	$opcion->enlace = $_POST['opc_enlace'];
	$opcion->id_centro = $_POST['id_centro'];
	$opcion->flag_menu = $_POST['flag_menu'] == "" ? 0 : $_POST['flag_menu'];  
	if($_POST['id_opcion_padre'] == 0)
		$opcion->id_opcion_padre = NULL;
	else
		$opcion->id_opcion_padre = $_POST['id_opcion_padre'];
	$opcion->flag_menu_principal = $_POST["flag_menu_principal"];
	$opcion->menu_posicion = $_POST["menu_posicion"];
	$opcion->flag_publica = $_POST['flag_publica'] == "" ? 0 : $_POST['flag_publica'];
	
	$resultado = $opcBLO->Registrar($opcion);
	
	if($resultado != null)
		echo $resultado->mensaje;
}

?>

<script language="JavaScript">

	function Enviar()
	{
		document.usuario.operacion.value = "crear_usuario";
		document.usuario.submit();
	}
	
	
</script>


<form name="usuario" method="POST" action="test_usuario.php">
	<input type="hidden" name="operacion"/>

<select name="objeto" onchange="submit()">	
	<option value="0" <?php echo ($objeto == 0) ? "selected = 'selected'": "";?>>Seleccione...</option>
	<option value="1" <?php echo ($objeto == 1) ? "selected = 'selected'": "";?>>Usuario</option>
	<option value="2" <?php echo ($objeto == 2) ? "selected = 'selected'": "";?>>Opcion</option>
	<option value="3" <?php echo ($objeto == 3) ? "selected = 'selected'": "";?>>Moneda</option>
</select>

<?php
if($objeto == 1)
{?>


<table style="border: dotted 1px #585858;">	
	<tr><td>Nombres: <td><td><input name="nombres" type="text"/></td></tr>
	<tr><td>Apellidos: <td><td><input name="apellidos" type="text"/></td></tr>	
	<tr><td>DNI:<td><td><input name="dni" type="text"/></td></tr>
	<tr><td>Login:<td><td><input name="login" type="text"/></td></tr>
	<tr><td>Password:<td><td><input name="password" type="text"/></td></tr>
	<tr><td>Habilitado:</td>
		<td>
			<select name="flag_habilitado">
				<option value="0">No</option>	
				<option value="1">Si</option>
			</select>
		</td>
	</tr>
	<tr><td>Cambiar Password:</td>
		<td>
			<select name="flag_cambiar_password">
				<option value="0">No</option>	
				<option value="1">Si</option>
			</select>
		</td>
	</tr>
	
	<tr><td colspan="2" align="center"><input type="button" value="Crear" onclick="Enviar();" /></td></tr>
</table>	
<table style="border: dotted 1px #585858;">
	<tr>
		<td colspan="6"><?php echo random_string();?></td>
	</tr>
	<tr>
		<td>id</td><td>nombres</td><td>apellidos</td><td>dni</td><td>login</td><td>habilitado</td><td>flag_cambiar_password</td><td>password</td>		
	</tr>
	<?php
	
	$usrBLO = new UsuarioBLO();
	$usuarios = $usrBLO->Listar("");
	
	echo "<tr><td colspan=8>".count($usuarios)."</td></tr>";
	if($usuarios != null)
	{
		foreach($usuarios as $u)
		{
			echo"<tr>";	
			echo "<td>$u->id</td>";
			echo "<td>$u->nombres</td>";
			echo "<td>$u->apellidos</td>";
			echo "<td>$u->dni</td>";
			echo "<td>$u->login</td>";
			echo "<td>$u->flag_habilitado</td>";
			echo "<td>$u->flag_cambiar_password</td>";
			echo "<td>".decrypt($u->password_enc, $u->password_key)."</td>";			
			echo "</tr>";
		}	
	}

	?>
</table>
<?php
}
if($objeto == 2)
{?>
<table style="border: solid 1px;">
	<tr><td colspan="2" align="center">OPCION:</td></tr>
	<tr><td>Key:</td><td><input name="opc_key" type="text" value="<?php echo random_string(); ?>"/></td></tr>
	<tr><td>Centro:</td>
		<td>
			<select name="id_centro">
				<option value="0">...</option>	
				<?php
					$cenBLO = new CentroBLO();
					$centros = $cenBLO->Listar("");
					foreach($centros as $c)
						echo "<option value=\"$c->id\">$c->descripcion</option>/n";  
				?>
			</select>
		</td>
	</tr>
	<tr><td>Codigo:</td><td><input name="opc_codigo" type="text"/></td></tr>	
	<tr><td>Descripcion:</td><td><input name="opc_descripcion" type="text"/></td></tr>
	<tr><td>Estado:</td><td><input name="opc_estado" type="text"/></td></tr>
	<tr><td>Enlace:</td><td><input name="opc_enlace" type="text"/></td></tr>
	<tr><td>Flag Menu:</td>
		<td>
			<select name="flag_menu">
				<option value="0">No</option>	
				<option value="1">Si</option>
			</select>
		</td>
	</tr>	
	<tr>
		<td>Opc Padre:</td>
		<td>
			<select name="id_opcion_padre">
				<option value="0">...</option>	
				<?php
					$opBLO = new OpcionBLO();
					$opciones = $opBLO->ListarOpcionesPadre();
					foreach($opciones as $o)
						echo "<option value=\"$o->id\">$o->descripcion</option>/n";  
				?>
			</select>	
		</td>
	</tr>
	<tr><td>Flag Menu Principal:</td>
		<td>
			<select name="flag_menu_principal">
				<option value="0">No</option>	
				<option value="1">Si</option>
			</select>
		</td>
	</tr>
	<tr><td>Posicion Menu:</td><td><input name="menu_posicion" type="text"/></td></tr>	
	<tr><td>Flag Publica:</td>
		<td>
			<select name="flag_publica">
				<option value="0">No</option>	
				<option value="1">Si</option>
			</select>
		</td>
	</tr>
		
	<tr><td colspan="2" align="center"><input type="button" value="Crear" onclick="document.usuario.operacion.value = 'crear_opcion'; submit();" /></td></tr>
</table>
<table>
	<tr><td>Id</td><td>Centro</td><td>Key</td><td>Codigo</td></td><td>Descripcion</td><td>Estado</td><td>Opcion Padre</td><td>Enlace</td><td>Flag Publica</td></tr>
	<?php 
	
	$opcBLO = new OpcionBLO();
	$opciones = $opcBLO->Listar("");
	
	if($opciones != null)
	{
		
		foreach ($opciones as $o) 
		{
			//echo "<tr><td colspan=6>Opcion Padre: $o->id_opcion_padre</td></tr>";
			echo "<tr>";
			echo "<td>$o->id</td>";
			echo "<td>$o->centro</td>";
			echo "<td>$o->opcion_key</td>";
			echo "<td>$o->codigo</td>";
			echo "<td>$o->descripcion</td>";
			echo "<td>$o->estado</td>";
			echo "<td>$o->id_opcion_padre</td>";
			echo "<td>$o->enlace</td>";
			echo "<td>$o->flag_publica</td>";
			echo "</tr>";	
		}
	}
	else
		echo "<tr><td colspan=6> NO HAY NI MIERDA K MOSTRAR</td></tr>";	
	?>
</table>
<?php
}
?>
		
</form>
	