<?php

session_start();

date_default_timezone_set("America/Lima");

include("clases/caja_turno.php");
include("clases/enc_dec.php");
//$fecha_hora_str = date("d-m-Y h:i A.", strtotime(date('Y-m-d H:i:s', strtotime($fecha_hora))));

if(isset($_GET["opcion_original_key"]))
	$opcion_original_key = $_GET["opcion_original_key"];
else
	$opcion_original_key = "";

if(isset($_GET["usr_key"]))
	$usr_key = $_GET["usr_key"];
else 
	$usr_key = "";
   
if(isset($_POST["id_caja"]))
	$id_caja = $_POST["id_caja"];
else
	$id_caja = 0;

if(isset($_GET["id_centro"]))
	$id_centro = $_GET["id_centro"];
else
	$id_centro = 0;

if(isset($_POST["operacion"]))
	$operacion = $_POST["operacion"];
else
	$operacion = "";

if(isset($_POST["id_usuario"]))
	$id_usuario = $_POST["id_usuario"];
else 
	$id_usuario = 0;

if(isset($_POST["monto_inicial_mn"]))
	$monto_inicial_mn = $_POST["monto_inicial_mn"];
else 
	$monto_inicial_mn = 0;

if(isset($_POST["id_almacen"]))
	$id_almacen = $_POST["id_almacen"];
else
	$id_almacen = 0;

if(isset($_POST["id_turno"]))
	$id_turno = $_POST["id_turno"];
else
	$id_turno = 0;
	
if($operacion == "crear")
{
	$ctBLO = new CajaTurnoBLO();
	$ct = new CajaTurno();
	
	$ct->auto_key = random_string();
	$ct->id_caja = $id_caja;
	$ct->id_usuario = $id_usuario;
	$ct->id_centro = $id_centro;
	$ct->id_estado = 1;
	$ct->saldo_inicial_mn = $monto_inicial_mn;
	$ct->fecha_hora_inicio = date('Y-m-d H:i:s');
	
	$ctBLO->Registrar($ct);
	
	Redireccionar($opcion_original_key, $usr_key, $id_centro);
}

if($operacion == "modificar" || $operacion == "cerrar")
{
	$ctBLO = new CajaTurnoBLO();
	
	if($id_turno > 0)
	{
		$turno = $ctBLO->RetornarXId($id_turno);
		
		if(!is_null($turno))
		{
			$turno->id_almacen = $id_almacen;
			if($operacion == "cerrar")
				$turno->id_estado = 2; //Cerrando Estado
				
			$ctBLO->Actualizar($turno);
		}
	}
	
}

function Redireccionar($opcion_key, $usr_key, $id_centro)
{
    echo "Redireccionando...";
    ?>
    <script type="text/javascript">
        location.href = <?php echo "\"redirect.php?opcion_key=$opcion_key&usr_key=$usr_key&id_centro=$id_centro\"";?>;            
    </script>
    <?php
}
?>