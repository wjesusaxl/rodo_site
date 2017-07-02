<?php

session_start();

date_default_timezone_set("America/Lima");

include ("clases/stock.php");

$id_centro = RetornarPOSTGET("id_centro", 0);
$id_producto = RetornarPOSTGET("id_producto", 0);
$id_almacen = RetornarPOSTGET("id_almacen", 0);
$id_almacen_origen = RetornarPOSTGET("id_almacen_origen", 0);
$id_almacen_destino = RetornarPOSTGET("id_almacen_destino", 0);
$operacion = RetornarPOSTGET("operacion", 0);

if($operacion == "query_stock_movimiento")
{
	if($id_producto > 0)
	{
		$stkBLO = new StockBLO();
		
		$stk = new Stock();
		$stk->id_producto = $id_producto;
		$stk->cantidad = 0.00;
		$stk->cantidad2 = 0.00;
		
		if($id_almacen_origen > 0)
			$stk_origen = $stkBLO->RetornarStockXIdProductoIdAlmacen($id_producto, $id_almacen_origen);
		
		if($id_almacen_destino > 0)
			$stk_destino = $stkBLO->RetornarStockXIdProductoIdAlmacen($id_producto, $id_almacen_destino);
		
		if(!is_null($stk_origen))
			$stk->cantidad = $stk_origen->cantidad;
		
		if(!is_null($stk_destino))
			$stk->cantidad2 = $stk_destino->cantidad;
						
		$stks = array();
		$stks[] = $stk;
		
		echo json_encode($stks);
		
	}
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