<?php

session_start();

date_default_timezone_set("America/Lima");	

include ('clases/opcion.php');
include ('clases/general.php');
include ('clases/enc_dec.php');
include ("clases/usuario.php");
include ('clases/almacen.php');
include ("clases/movimiento.php");
include ("clases/stock.php");


$opcion_key = RetornarPOSTGET("opcion_key", "");
$op_original_key = RetornarPOSTGET("op_original_key", "");
$usr_key = RetornarPOSTGET("usr_key", "");
$operacion = RetornarPOSTGET("operacion", "");
$id_centro = RetornarPOSTGET("id_centro", 0);

$id_usuario_conf = RetornarPOSTGET("id_usuario_conf", 0);
$id_centro_conf = RetornarPOSTGET("id_centro_conf", 0);
$id_usuario = RetornarPOSTGET("id_usuario", 0);
$almacen_habilitado = RetornarPOSTGET("almacen_habilitado", "");
$almacen_entrada = RetornarPOSTGET("almacen_entrada", NULL);
$almacen_salida = RetornarPOSTGET("almacen_salida", NULL);

$id_almacen_origen = RetornarPOSTGET("id_almacen_origen", NULL);
$id_almacen_destino = RetornarPOSTGET("id_almacen_destino", NULL);
$id_compra = RetornarPOSTGET("id_compra", 0);
$nro_items = RetornarPOSTGET("nro_items", 0);
$id_movimiento_motivo = RetornarPOSTGET("id_movimiento_motivo", 0);

$comentarios = RetornarPOSTGET("comentarios", "");


if($operacion == "crear_movimiento")
{
	if($id_movimiento_motivo > 0)
	{
		$movBLO = new MovimientoBLO();
		$stkBLO = new StockBLO();
		
		$mov = new Movimiento();
		
		$mov->id_centro = $id_centro;
		$mov->movimiento_key = random_string();
		$mov->fecha_hora = date('Y-m-d H:i:s');
		$mov->id_usuario = $id_usuario;
		$mov->id_motivo = $id_movimiento_motivo;
		$mov->id_almacen_origen = $id_almacen_origen;
		$mov->id_almacen_destino = $id_almacen_destino;
		$mov->comentarios = $comentarios;
		$mov->id_compra = NULL;
		
		$resultado = $movBLO->Registrar($mov);
		
		if($resultado->isOK)
		{
			$mov->id = $resultado->id;
			
			for($i = 1; $i <= $nro_items; $i ++)
			{
				$mi = new MovimientoItem();
				$mi->id_movimiento = $mov->id;
				$mi->id_producto = RetornarPOSTGET("id_producto_$i", 0);
				$mi->cantidad = RetornarPOSTGET("cantidad_$i", 0);
				$mi->auto_key = random_string();
				$mi->flag_anulado = 0;
				
				$movBLO->RegistrarItem($mi);
			}
			
			$stkBLO->ActualizarStock();
			
			?>
	        <script type="text/javascript">
	            alert('<?php echo $resultado->mensaje;?>');             
	        </script>
	        <?php  
			Redireccionar($op_original_key, $usr_key, $id_centro);
		}
	}
}

if($operacion == "asignar_permisos")
{
    if($id_usuario_conf > 0)
    {
    	
        $almBLO = new AlmacenBLO();
                
        $lista_almacenes = $almBLO->ListarAlmacenXIdCentro($id_centro);
		
		foreach($lista_almacenes as $a)
		{
			$au = $almBLO->RetornarAlmacenUsuarioXIdUsuarioIdAlmacen($id_usuario_conf, $a->id);
			
			$au_n = new AlmacenUsuario();
			$au_n->id_almacen = $a->id;
			$au_n->id_usuario = $id_usuario_conf;
			$au_n->flag_habilitado = 0;
			$au_n->flag_entrada = 0;
			$au_n->flag_salida = 0;
			
			if(!is_null($almacen_habilitado))
				foreach($almacen_habilitado as $ah)
					if($ah == $a->id)
						$au_n->flag_habilitado = 1;
			
			if(!is_null($almacen_entrada))
				foreach($almacen_entrada as $ae)
					if($ae == $a->id)
						$au_n->flag_entrada = 1;
			
			if(!is_null($almacen_salida))
				foreach($almacen_salida as $as)
					if($as == $a->id)
						$au_n->flag_salida = 1;
			
			if(!is_null($au))
			{
				$au_n->id = $au->id;
				$almBLO->ModificarAlmacenUsuario($au_n);
			}
			else
			{
				if($au_n->flag_habilitado == 1 || $au_n->flag_entrada == 1 || $au_n->flag_salida == 1) 
					$almBLO->RegistrarAlmacenUsuario($au_n);
			}
						
		}
        
        ?>
        <script type="text/javascript">
            alert('Permisos Actualizados para el Usuario!');             
        </script>
        <?php        
    }
    Redireccionar($op_original_key, $usr_key, $id_centro);
    
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