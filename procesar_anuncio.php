<?php

include ("clases/anuncio.php");

$id_centro = RetornarPOSTGET("id_centro", 0);
$id_usuario = RetornarPOSTGET("id_usuario", 0);
$operacion = RetornarPOSTGET("operacion", "");
$op_original_key = RetornarPOSTGET("op_original_key", "");
$usr_key = RetornarPOSTGET("usr_key", "");
$fecha_inicio = RetornarPOSTGET("fecha_inicio", "");
$fecha_fin = RetornarPOSTGET("fecha_fin", "");
$mensaje = RetornarPOSTGET("mensaje", "");
$usr_key = RetornarPOSTGET("usr_key", "");
$id_anuncio = RetornarPOSTGET("id_anuncio", 0);
$flag_anulado = RetornarPOSTGET("flag_anulado", 0);

$fecha_inicio = substr($fecha_inicio, 0, 10);
$fecha_fin = substr($fecha_fin, 0, 10);

if($operacion == "query_activos")
{
	$lista = NULL;
	$anBLO = new AnuncioBLO();
	
	if($id_centro > 0)
	{
		
		$lista = $anBLO->ListarActivos($id_centro);
		
		if(count($lista) == 0)
			$lista = NULL;
	}
	
	echo json_encode($lista);
}

	/*$tBLO = new TestBLO();
	
	$t = new Test();
	$t->msg = $mensaje;
	$t->tiempo = date('Y-m-d H:i:s');
	$tBLO->Registrar($t);*/

	

if($operacion == "crear" || $operacion == "modificar")
{
	$anBLO = new AnuncioBLO();

	echo "Operacion: $operacion</br>";

	if($operacion == "crear")
		$obj = new Anuncio();
	if($operacion == "modificar")
		$obj = $anBLO->RetornarXId($id_anuncio);
		
	$obj->id_usuario = $id_usuario;
	$obj->id_centro = $id_centro;
	$obj->fecha_hora_inicio = $fecha_inicio." 00:00:00";
	$obj->fecha_hora_fin = $fecha_fin." 23:59:59";
	$obj->flag_anulado = $flag_anulado;
	$obj->mensaje = mysql_escape_string(strtoupper($mensaje));
	
	if($operacion == "crear")
		$anBLO->Registrar($obj);
	if($operacion == "modificar")
		$anBLO->Modificar($obj);
	
	?>
	<script type="text/javascript">
		alert('Anuncio Guardado!');             
	</script>
	<?php
	Redireccionar($op_original_key, $usr_key, $id_centro);
	
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

function Redireccionar($opcion_key, $usr_key, $id_centro)
{
    echo "Redireccionando..";
    ?>
    <script type="text/javascript">
        location.href = <?php echo "\"redirect.php?opcion_key=$opcion_key&usr_key=$usr_key&id_centro=$id_centro\"";?>;            
    </script>
    <?php
}

?>