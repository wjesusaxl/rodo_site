<?php

session_start();

date_default_timezone_set("America/Lima");

include("clases/cliente.php");
include("clases/general.php");
include("clases/enc_dec.php");
	
$enlace_original = "clientes/crear.php";


$operacion = RetornarPOSTGET("operacion", "");
$operacion2 = RetornarPOSTGET("operacion2", "");
$opcion_key = RetornarPOSTGET("opcion_key", "");
$usr_key = RetornarPOSTGET("usr_key", "");
$id_centro = RetornarPOSTGET("id_centro", 0);
$id_tipo_documento = RetornarPOSTGET("id_tipo_documento", 0);
$nombres = RetornarPOSTGET("nombres", "");
$nro_documento = RetornarPOSTGET("nro_documento", "");
$apellidos = RetornarPOSTGET("apellidos", "");
$razon_social = RetornarPOSTGET("razon_social", "");
$id_usuario =  RetornarPOSTGET("id_usuario", 0);
$keyword = RetornarPOSTGET("keyword", "");
$email = RetornarPOSTGET("email", "");
$telefonos = RetornarPOSTGET("telefonos", "");
$id_cliente = RetornarPOSTGET("id_cliente", 0);

$enlace_original = "$enlace_original?opcion_key=$opcion_key&usr_key=$usr_key&id_centro=$id_centro"; 

//$telefonos_arr = array();

if($telefonos != "")
	$telefonos_arr = explode("&*", $telefonos);

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
	$id_tipo_documento = $cliente->id_tipo_documento;
	$nro_documento = $cliente->nro_documento;
	$nombres = $cliente->nombres;
	$apellidos = $cliente->apellidos;
	$keyword = $cliente->keyword;
	$email = $cliente->email;
	$telefonos_arr = array();
	
	
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

if($operacion == "crear" || $operacion == "modificar")
{
	
	$cliBLO = new ClienteBLO();
	$cliente = new Cliente();
	//$resultado = new OperacionResultado();
	
	if($operacion == "modificar")
		$cliente = $cliBLO->RetornarClienteXId($id_cliente);
	$cliente->id = $id_cliente;
	$cliente->id_tipo_documento = $id_tipo_documento;
	$cliente->nro_documento = $nro_documento;
	$cliente->nombres = strtoupper($nombres);
	
	if($id_tipo_documento == 1)
		$cliente->apellidos = strtoupper($apellidos);
	if($id_tipo_documento == 2)
		$cliente->apellidos = strtoupper($razon_social);
	$cliente->keyword = strtoupper($keyword);
	$cliente->email = $email;
	$cliente->id_usuario_creacion = $id_usuario;	
	
	echo $cliente->apellidos;
	
	
	if($operacion == "crear")
	{		
		$cli = $cliBLO->RetornarClienteXNroDocumento($id_tipo_documento, $nro_documento);
		
		if(is_null($cli))
		{			
			$resultado = $cliBLO->Registrar($cliente);
			
			$cli = $cliBLO->RetornarClienteXNroDocumento($id_tipo_documento, $nro_documento);
			
			$tels = array();
			if($telefonos_arr != null)
			{
				foreach($telefonos_arr as $t)
				{
					$tel = new ClienteTelefono();
					$tel->id_cliente = $cli->id;
					$tel->telefono = $t;
					$tel->habilitado = true;
					$tels[] = $tel;
				}
				
				$cli->telefonos = $tels;
			}
			
			if(!is_null($cli->telefonos))			
				foreach($cli->telefonos as $t)				
					$cliBLO->RegistrarTelefono($t);
		}
		else 
		{
			$resultado = new OperacionResultado();
			$resultado->id = 0;
			$resultado->codigo = "03";
			$resultado->isOK = FALSE;
			$resultado->mensaje = "Cliente ya se encuentra registrado(a)!";	
		}
	}
	
	if($operacion == "modificar" && $id_cliente > 0)
	{	
		
		$tels = array();
		if($telefonos_arr != null)
		{
			foreach($telefonos_arr as $t)
			{
				$tel = new ClienteTelefono();
				$tel->id_cliente = $cli->id;
				$tel->telefono = $t;
				$tel->habilitado = true;
				$tels[] = $tel;
			}
				
			$cliente->telefonos = $tels;
		}
		
		$resultado = $cliBLO->Actualizar($cliente);
		
	}
	
	
	?>
		<script type="text/javascript">
			alert('<?php echo $resultado->mensaje;?>');
			location.href = '<?php echo $enlace_original; ?>'; 
		</script>	
		
	<?php
	
}



if($operacion == "crear_simple")
{
	if($keyword == "")
		$keyword = "simple";
	$cliBLO = new ClienteBLO();
	$cliente = new Cliente();
	$resultado = new OperacionResultado();
	$err_msg;
	
	//$resultado = new OperacionResultado();
	
	if($operacion2 == "")
	{
		$clientes = $cliBLO->RetornarClienteXNombresYApellidos($nombres, $apellidos);
		
		if(!is_null($clientes))
		{
			$i=0;
			foreach($clientes as $c)
			{
				if($i == 0)
					$err_msg = "* $c->nombres $c->apellidos";
				else
					$err_msg = $err_msg."\n"."* $c->nombres $c->apellidos";
				$i++;

			}
			
			$resultado->id = 0;
			$resultado->codigo = "03";
			$resultado->isOK = FALSE;
			$resultado->mensaje = "Se encuentra(n) registrado(s) el/los siguiente(s) cliente(s):\n".$err_msg."\n\n";
		}
		else
		{
			$cliente->id_tipo_documento = $id_tipo_documento;		
			
			if($nro_documento == "")
				$nro_documento = random_string();
			
			$cliente->nro_documento = $nro_documento;
			
			if($id_tipo_documento == 2)
			{
				if($apellidos == "")				
					$cliente->apellidos = $nombres;
				else 
				{
					if(strtoupper($nombres) == strtoupper($apellidos))
					{
						$cliente->apellidos = $nombres;
						$cliente->nombres = "";							
					}
					else
					{
						$cliente->nombres = strtoupper($nombres);
						$cliente->apellidos = strtoupper($apellidos);		
					}	
				}
			}
			else
			{
				$cliente->nombres = strtoupper($nombres);
				$cliente->apellidos = strtoupper($apellidos);		
			}	
					
			$cliente->keyword = strtoupper($keyword);
			$cliente->email = "";
			$cliente->id_usuario_creacion = $id_usuario;
			
			$resultado = $cliBLO->Registrar($cliente);

			$cli = $cliBLO->RetornarClienteXNroDocumento($id_tipo_documento, $nro_documento);

			$resultado->id = $cli->id;
			
			$tels = array();
						
			if(!is_null($telefonos_arr))
			{
				foreach($telefonos_arr as $t)
				{
					$tel = new ClienteTelefono();
					$tel->id_cliente = $cli->id;
					$tel->telefono = $t;
					$tel->habilitado = true;
					$tels[] = $tel;
				}
				
				$cli->telefonos = $tels;
			}
			
			if(!is_null($cli->telefonos))			
				foreach($cli->telefonos as $t)				
					$cliBLO->RegistrarTelefono($t);	
				
		}
	}

	if($operacion2 == "crear")
	{
		$cliente->id_tipo_documento = $id_tipo_documento;		
			
		if($nro_documento == "")
			$nro_documento = random_string();
			
		$cliente->nro_documento = $nro_documento;
			
		if($id_tipo_documento == 2)
		{
			if($apellidos == "")				
				$cliente->apellidos = $nombres;
			else 
			{
				if(strtoupper($nombres) == strtoupper($apellidos))
				{
					$cliente->apellidos = $nombres;
					$cliente->nombres = "";							
				}
				else
				{
					$cliente->nombres = strtoupper($nombres);
					$cliente->apellidos = strtoupper($apellidos);		
				}	
			}
		}
		else
		{
			$cliente->nombres = strtoupper($nombres);
			$cliente->apellidos = strtoupper($apellidos);		
		}	
					
		$cliente->keyword = strtoupper($keyword);
		$cliente->email = "";
		$cliente->id_usuario_creacion = $id_usuario;
			
		$resultado = $cliBLO->Registrar($cliente);
		
		$cli = $cliBLO->RetornarClienteXNroDocumento($id_tipo_documento, $nro_documento);
		$resultado->id = $cli->id;
			
		$tels = array();
		if($telefonos_arr != null)
		{
			foreach($telefonos_arr as $t)
			{
				$tel = new ClienteTelefono();
				$tel->id_cliente = $cli->id;
				$tel->telefono = $t;
				$tel->habilitado = true;
				$tels[] = $tel;
			}
				
			$cli->telefonos = $tels;
		}
			
		if(!is_null($cli->telefonos))			
			foreach($cli->telefonos as $t)				
				$cliBLO->RegistrarTelefono($t);	
	}
	
	echo json_encode($resultado);	
}

if($operacion == "query")
{
	$cliBLO = new ClienteBLO();
	$lista = array();
	$filtro = "";
	
	if($nombres != "")
		$filtro .= " AND c.nombres LIKE '%$nombres%'";
	if($apellidos != "")
		$filtro .= " AND c.apellidos LIKE '%$apellidos%'";
	if($id_tipo_documento > 0)
		$filtro .= " AND c.id_tipo_documento = $id_tipo_documento";
	if($nro_documento != "")
		$filtro .= " AND c.nro_documento LIKE '%$nro_documento%'";
	
	if(strlen($filtro) > 0)
		$filtro = substr($filtro, 5);
	else
		$filtro = "";
	
	if($filtro != "")	
		$lista = $cliBLO->Listar($filtro);
	else 
		$lista = null;
	
	echo json_encode($lista);	
}

if($operacion == "query2")
{
	$cliBLO = new ClienteBLO();
	$lista = array();
	$filtro = "";
	
	if($nombres != "")
		$filtro .= " AND (c.nombres LIKE '%$nombres%' OR c.apellidos LIKE '%$nombres%')";
	
	if(strlen($filtro) > 0)
		$filtro = substr($filtro, 5);
	else
		$filtro = "";
	
	if($filtro != "")	
		$lista = $cliBLO->Listar($filtro);
	else 
		$lista = array();
	
	echo json_encode($lista);	
}

function RetornarPOSTGET($value, $default)
{
	if(isset($_GET[$value]))
		$q = $_GET[$value];
	else
		if(isset($_POST[$value]))
			$q = $_POST[$value];
		else
			$q = $default;
	
	return $q;
}

function Crear($cliente)
{
	
	
}

?>

