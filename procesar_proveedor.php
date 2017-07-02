<?php

session_start();

date_default_timezone_set("America/Lima");

include ("clases/proveedor.php");

if(isset($_GET["opcion_original_key"]))
	$opcion_original_key = $_GET["opcion_original_key"];
else
	$opcion_original_key = "";

if(isset($_GET["usr_key"]))
	$usr_key = $_GET["usr_key"];
else
	$usr_key = "";

if(isset($_GET["id_centro"]))
	$id_centro = $_GET["id_centro"];
else
	$id_centro = "";

if(isset($_GET["id_proveedor"]))
	$id_proveedor = $_GET["id_proveedor"];
else 
	if(isset($_POST["id_proveedor"]))
		$id_proveedor = $_POST["id_proveedor"];
	else 
		$id_proveedor = 0;

if(isset($_GET["id_proveedor_categoria"]))
	$id_proveedor_categoria = $_GET["id_proveedor_categoria"];
else 
	if(isset($_POST["id_proveedor_categoria"]))
		$id_proveedor_categoria = $_POST["id_proveedor_categoria"];
	else 
		$id_proveedor_categoria = 0;
	
if(isset($_GET["razon_social"]))
	$razon_social = $_GET["razon_social"];
else
	if(isset($_POST["razon_social"]))
		$razon_social = $_POST["razon_social"];
	else
		$razon_social = "";

if(isset($_GET["id_tipo_documento"]))
	$id_tipo_documento = $_GET["id_tipo_documento"];
else
	if(isset($_POST["id_tipo_documento"]))
		$id_tipo_documento = $_POST["id_tipo_documento"];
	else
		$id_tipo_documento = "";

if(isset($_GET["nombre_comercial"]))
	$nombre_comercial = $_GET["nombre_comercial"];
else
	if(isset($_POST["nombre_comercial"]))
		$nombre_comercial = $_POST["nombre_comercial"];
	else
		$nombre_comercial = "";

if(isset($_GET["nro_documento"]))
	$nro_documento = $_GET["nro_documento"];
else
	if(isset($_POST["nro_documento"]))
		$nro_documento = $_POST["nro_documento"];
	else
		$nro_documento = "";

if(isset($_GET["direccion"]))
	$direccion = $_GET["direccion"];
else
	if(isset($_POST["direccion"]))
		$direccion = $_POST["direccion"];
	else
		$direccion = "";
	
if(isset($_GET["telefonos"]))
	$telefonos = $_GET["telefonos"];
else
	if(isset($_POST["telefonos"]))
		$telefonos = $_POST["telefonos"];
	else
		$telefonos = "";
	
if(isset($_GET["comentarios"]))
	$comentarios = $_GET["comentarios"];
else
	if(isset($_POST["comentarios"]))
		$comentarios = $_POST["comentarios"];
	else
		$comentarios = "";
		
$nombres = RetornarPOSTGET("nombres", "");
$operacion = RetornarPOSTGET("operacion", "");
$id_tipo_documento = RetornarPOSTGET("id_tipo_documento", 0);
	
if($operacion == "query")
{
	$proBLO = new ProveedorBLO();
	$lalista = NULL;
	$filtro = "";
	
	if($razon_social != "")
		$filtro .= " AND p.razon_social LIKE '%$razon_social%'";
	if($nombre_comercial != "")
		$filtro .= " AND p.nombre_comercial LIKE '%$nombre_comercial%'";
	if($id_proveedor_categoria > 0)
		$filtro .= " AND p.id_proveedor_categoria = $id_proveedor_categoria";
	if($id_tipo_documento > 0)
		$filtro .= " AND p.id_tipo_documento = $id_tipo_documento";
	if($nro_documento != "")
		$filtro .= " AND p.nro_documento LIKE '%$nro_documento%'";
	if($direccion != "")
		$filtro .= " AND p.direccion LIKE '%$direccion%'";
	if($telefonos != "")
		$filtro .= " AND p.telefonos LIKE '%$telefonos%'";
	if($comentarios != "")
		$filtro .= " AND p.comentarios LIKE '%$comentarios%'";
		
	
	if(strlen($filtro) > 0)
		$filtro = substr($filtro, 5);
	else
		$filtro = "";
	
	$lalista = $proBLO->Listar($filtro);
	
	if(!is_null($lalista))
		if(count($lalista) == 0)
			$lalista = NULL;
		
	echo json_encode($lalista);
}

if($operacion == "query2")
{
	$proBLO = new ProveedorBLO();
	$lalista = NULL;
	$filtro = "";
	
	if($nombres != "" and $id_tipo_documento > 0)
	{
		$filtro .= "p.id_tipo_documento = $id_tipo_documento AND (p.razon_social LIKE '%$nombres%' OR p.nombre_comercial LIKE '%$nombres%' OR p.nro_documento LIKE '%$nombres%')";
		$lalista = $proBLO->Listar($filtro);
	}
		
	if(!is_null($lalista))
		if(count($lalista) == 0)
			$lalista = NULL;
		
	echo json_encode($lalista);
	
}

if( $operacion == "crear" || $operacion == "modificar")
{
	$provBLO = new ProveedorBLO();
	$pro = new Proveedor();
	
	$pro->id = $id_proveedor;
	$pro->id_proveedor_categoria = $id_proveedor_categoria;
	$pro->id_tipo_documento = $id_tipo_documento;
	$pro->nro_documento = strtoupper($nro_documento);
	$pro->nombre_comercial = strtoupper($nombre_comercial);
	$pro->razon_social = strtoupper($razon_social);
	$pro->direccion = strtoupper($direccion);
	$pro->telefonos = strtoupper($telefonos);
	$pro->comentarios = strtoupper($comentarios);
	
	if($operacion == "crear")
		$provBLO->Registrar($pro);
	if($operacion == "modificar")
		$provBLO->Modificar($pro);
	?>
	<script type="text/javascript">
		alert('Informacion de Proveedor Almacenada!');             
	</script>
	<?php
	Redireccionar($opcion_original_key, $usr_key, $id_centro);
}

function Redireccionar($opcion_key, $usr_key, $id_centro)
{
    echo "Redireccionando..";
    ?>
    <script type="text/javascript">
        location.href = <?php echo "\"redirect.php?opcion_key=$opcion_key&usr_key=$usr_key&id_centro=$id_centro\"";?>;            
    </script>
    <?php
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


?>