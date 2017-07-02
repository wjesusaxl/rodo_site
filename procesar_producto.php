<?php

include("clases/producto.php");

//date_default_timezone_set("America/Lima");

$operacion = RetornarPOSTGET("operacion", "");
$opcion_original_key = RetornarPOSTGET("opcion_original_key", "");
$usr_key = RetornarPOSTGET("usr_key", "");
$id_centro = RetornarPOSTGET("id_centro", 0);
$tipo_codificacion_json = RetornarPOSTGET("tipo_codificacion_json", "");

$nro_serie = RetornarPOSTGET("nro_serie", "");
$id_producto = RetornarPOSTGET("id_producto", 0);
$id_producto_precio = RetornarPOSTGET("id_producto_precio", 0);
$id_pais = RetornarPOSTGET("id_pais", 0);
$id_usuario = RetornarPOSTGET("id_usuario", 0);
$id_producto_categoria = RetornarPOSTGET("id_producto_categoria", 0);
$id_producto_categoria_1 = RetornarPOSTGET("id_producto_categoria_1", 0);
$id_producto_categoria_2 = RetornarPOSTGET("id_producto_categoria_2", 0);
$id_producto_categoria_3 = RetornarPOSTGET("id_producto_categoria_3", 0);
$id_producto_categoria_padre  = RetornarPOSTGET("id_producto_categoria_padre", 0);
$id_producto_pack = RetornarPOSTGET("id_producto_pack", 0);
$id_producto_pack_item = RetornarPOSTGET("id_producto_pack_item", 0);

$id_marca = RetornarPOSTGET("id_marca", 0);
$descripcion_corta = RetornarPOSTGET("descripcion_corta", "");
$descripcion_larga = RetornarPOSTGET("descripcion_larga", "");
$codigo = RetornarPOSTGET("codigo", "");
$id_unidad_medida = RetornarPOSTGET("id_unidad_medida", "");
$dimension = RetornarPOSTGET("dimension", "");
$flag_venta = RetornarPOSTGET("flag_venta", "");
$flag_pack = RetornarPOSTGET("flag_pack", "");
$pack_contenido = RetornarPOSTGET("pack_contenido", "");
$opcion_cantidad = RetornarPOSTGET("opcion_cantidad_str", "");
$id_cantidad_default = RetornarPOSTGET("id_cantidad_default", "");

$id_producto_precio_tipo = RetornarPOSTGET("id_producto_precio_tipo", 0);
$fecha_inicio = RetornarPOSTGET("fecha_inicio", "");
$fecha_fin = RetornarPOSTGET("fecha_fin", "");
$precio_neto_mn = RetornarPOSTGET("precio_neto_mn", 0);
$impuesto_mn = RetornarPOSTGET("impuesto_mn", 0);
$precio_total_mn = RetornarPOSTGET("precio_total_mn", 0);
$flag_habilitado = RetornarPOSTGET("flag_habilitado", 0);


if($operacion == "query_categoria")
{
	$lista_cat = NULL;
	$proBLO = new ProductoBLO();
	$lista_cat = $proBLO->ListarCategoriaXCategoriaPadre($id_producto_categoria_padre);
	
	if($tipo_codificacion_json == "JS.JSON")
		echo json_encode2($lista_cat);
	else
		echo json_encode($lista_cat);
	
}

if($operacion == "buscar_nro_serie")
{
	if($nro_serie != "")
	{
		$proBLO = new ProductoBLO();
		
		$lista = $proBLO->Listar("nro_serie LIKE '%$nro_serie%'");
		
		echo json_encode($lista);		
	}
}	

if($operacion == "query")
{
	$lista = NULL;
	
	$proBLO = new ProductoBLO();
	
	$filtro = "";
	/*
	echo "C1: $id_producto_categoria_1</br>";
	echo "C2: $id_producto_categoria_2</br>";
	echo "C3: $id_producto_categoria_3</br>";
	*/
	
	//echo "Flag Venta: ".$_GET["flag_venta"]."</br>";
	if($id_producto_categoria_3 > 0 && $id_producto_categoria_2 == 0 && $id_producto_categoria_1 == 0)
	{
		$id_producto_categoria_1 = $id_producto_categoria_3;
		$id_producto_categoria_3 = 0;
	}
	
	if($id_producto_categoria_3 > 0 && $id_producto_categoria_2 > 0 && $id_producto_categoria_1 == 0)
	{
		$id_producto_categoria_1 = $id_producto_categoria_2;
		$id_producto_categoria_2 = $id_producto_categoria_3;
		$id_producto_categoria_3 = 0;
	} 
	
	if($nro_serie != "")
		$filtro = "$filtro AND nro_serie LIKE '%$nro_serie%'";
	
	if($descripcion_corta != "")
		$filtro = "$filtro AND descripcion_corta LIKE '%$descripcion_corta%'";
	
	if($descripcion_larga != "")
		$filtro = "$filtro AND descripcion_larga LIKE '%$descripcion_larga%'";
		
	if($id_marca > 0)
		$filtro = "$filtro AND id_marca = $id_marca";
	
	if($codigo != "")
		$filtro = "$filtro AND codigo LIKE '%$codigo%'";
	
	if($dimension != "")
		$filtro = "$filtro AND dimension = $dimension";
	
	if($flag_venta != "")
		$filtro = "$filtro AND flag_venta = $flag_venta";
	
	if($flag_pack != "")
		$filtro = "$filtro AND flag_pack = $flag_pack";
	
	if($id_unidad_medida > 0)
		$filtro = "$filtro AND id_unidad_medida = $id_unidad_medida";
	
	if($id_producto_categoria_1 > 0)
		$filtro = "$filtro AND id_producto_categoria = $id_producto_categoria_1";
	
	if($id_producto_categoria_2 > 0)
		$filtro = "$filtro AND id_producto_categoria2 = $id_producto_categoria_2";
	
	if($id_producto_categoria_3 > 0)
		$filtro = "$filtro AND id_producto_categoria3 = $id_producto_categoria_3";	
	
	$filtro = substr($filtro, 5);
	$lista = $proBLO->Listar($filtro);
	
	if(count($lista) == 0) 
		$lista = NULL;
	
	//$lista = NULL;
	
	echo json_encode($lista);
	//echo "{\"productos\":".json_encode($lista)."}";
}

if($operacion == "query_producto")
{
	$lista = NULL;
	
	$proBLO = new ProductoBLO();
	
	$filtro = "";
	
	if($descripcion_corta != "")
		$filtro = "(descripcion_corta LIKE '%$descripcion_corta%' OR marca LIKE '%$descripcion_corta%')";
	
	if($flag_venta != "")
		$filtro = "$filtro AND flag_venta = $flag_venta";
	
	//$filtro = substr($filtro, 5);
	$lista = $proBLO->Listar($filtro);
	
	if(count($lista) == 0) 
		$lista = NULL;
	
	//$lista = NULL;
	
	echo json_encode($lista);
	//echo "{\"productos\":".json_encode($lista)."}";
}

if($operacion == "query_producto_no_en_pack")
{
	$lista = NULL;
	$proBLO = new ProductoBLO();
	
	if($id_producto_pack > 0 && $id_marca > 0)
	{
		$filtro = " AND NOT EXISTS ( SELECT PP.id_producto FROM producto_pack PP WHERE v_producto.id_producto = PP.id_producto AND PP.id_producto_pack = $id_producto_pack)";
		$filtro = $filtro . " AND id_marca = $id_marca AND flag_pack = 0 AND flag_venta = 1";
		
		$filtro = substr($filtro, 5);
		$lista = $proBLO->Listar($filtro);
		
	}
	
	if(!is_null($lista))
		if(count($lista) == 0)
			$lista = NULL;
	
	if($tipo_codificacion_json == "JS.JSON")
		echo json_encode2($lista);
	else
		echo json_encode($lista);
	
}

if($operacion == "query_categoria_producto")
{
	$lista = NULL;
	
	$proBLO = new ProductoBLO();
	
	$filtro = "";
	
	if($id_producto_categoria > 0)
		$filtro = " AND id_producto_categoria = $id_producto_categoria OR id_producto_categoria2 = $id_producto_categoria OR id_producto_categoria3 = $id_producto_categoria";
	
	if($id_marca > 0)
		$filtro = "$filtro AND id_marca = $id_marca";
		
	if($flag_pack > 0)
		$filtro = "$filtro AND flag_pack = $flag_pack";
	
	//echo $filtro;
	
	$filtro = substr($filtro, 5);
	$lista = $proBLO->Listar($filtro);
	
	if(!is_null($lista))
		if(count($lista) == 0)
			$lista = NULL;
		
	if($tipo_codificacion_json == "JS.JSON")
		echo json_encode2($lista);
	else
		echo json_encode($lista);
}

if($operacion == "query_producto_pack")
{
	$proBLO = new ProductoBLO();
	$filtro = "";
	
	if($id_producto_pack >= 0)
		$filtro = " AND id_producto_pack = $id_producto_pack";
	
	if($id_producto_pack_item > 0)
		$filtro = " AND id_producto_pack = $id_producto_pack_item";
	
	$filtro = substr($filtro, 5);
	
	$lista = $proBLO->ListarProductoPackItem($filtro);
	
	if(!is_null($lista))
		if(count($lista) == 0)
			$lista = NULL;
		
	if($tipo_codificacion_json == "JS.JSON")
		echo json_encode2($lista);
	else
		echo json_encode($lista);

}

if($operacion == "crear" || $operacion == "modificar")
{
	$proBLO = new ProductoBLO();
	$pro = new Producto();
	
	//echo "Id Usuario: $id_usuario</br>";
	
	if($id_producto > 0 && $operacion == "modificar")	
		$pro = $proBLO->RetornarProductoXId($id_producto);	
	
	$pro->id = $id_producto;
	$pro->id_producto_categoria = $id_producto_categoria;
	$pro->codigo = $codigo;
	$pro->descripcion_corta = $descripcion_corta;
	$pro->descripcion_larga = $descripcion_larga;
	$pro->id_pais_origen = $id_pais;
	$pro->nro_serie = $nro_serie;
	$pro->dimension = $dimension;
	$pro->id_unidad_medida = $id_unidad_medida;
	$pro->opcion_cantidad = $opcion_cantidad;
	$pro->id_usuario = $id_usuario;
	$pro->id_marca = $id_marca;
	$pro->id_cantidad_default = $id_cantidad_default;
	$pro->flag_venta = $flag_venta;
	$pro->flag_pack = $flag_pack;
	
	$msg = "Informacion de Producto Almacenada!";
	
	if($operacion == "crear")
		$proBLO->Registrar($pro);
	
	if($id_producto > 0 && $operacion == "modificar")
		$proBLO->Modificar($pro);
	
	
	?>
	<script type="text/javascript">
            alert('<?php echo "Operacion Exitosa";?>');
	</script>
	<?php
	Redireccionar($opcion_original_key, $usr_key, $id_centro);

}

if($operacion == "query_precio")
{
	$lista = NULL;
	
	$filtro = " AND p.flag_venta = 1";
	
	if($id_producto_categoria > 0)
		$filtro = "$filtro AND p.id_producto_categoria = $id_producto_categoria";
	if($id_producto > 0)
		$filtro = "$filtro AND p.id_producto = $id_producto";
	if($id_marca > 0)
		$filtro = "$filtro AND p.id_marca = $id_marca";
	if($descripcion_corta != "")
		$filtro = "$filtro AND p.descripcion_corta LIKE '%$descripcion_corta%'";
	if($codigo != "")
		$filtro = "$filtro AND pp.codigo LIKE '%$codigo%'";
	if($fecha_inicio != "")
		$filtro = "$filtro AND pp.fecha_inicio >= '%$fecha_inicio%'";
	if($fecha_fin != "")
		$filtro = "$filtro AND pp.fecha_fin <= '%$fecha_fin%'";

	$filtro = substr($filtro, 5);
	
	//	echo "Filtro: $filtro</br>";
	
	$proBLO = new ProductoBLO();
	$lista = $proBLO->ListarPrecio($filtro);
	//echo "Cuenta: ".count($lista)."</br>";
	
	if(!is_null($lista))
		if(count($lista) == 0)
			$lista = NULL;
		
	echo json_encode($lista);
}

if($operacion == "crear_precio" || $operacion == "modificar_precio")
{
		
	$proBLO = new ProductoBLO();
	$precio = new ProductoPrecio();
	
	$precio->id = $id_producto_precio;
	$precio->id_usuario = $id_usuario;
	$precio->id_centro = $id_centro;
	$precio->id_producto = $id_producto;
	$precio->id_producto_precio_tipo = $id_producto_precio_tipo;
	$precio->codigo = strtoupper($codigo);
	$precio->fecha_inicio = $fecha_inicio;
	$precio->fecha_fin = $fecha_fin;
	$precio->flag_habilitado = $flag_habilitado;
	$precio->precio_neto_mn = $precio_neto_mn;
	$precio->impuesto_mn = $impuesto_mn;
	$precio->precio_total_mn = $precio_total_mn;
	
	$msg = "Informacion de Precio Almacenada!";
	
	
	if($operacion == "crear_precio")
		$proBLO->RegistrarPrecio($precio);
	if($operacion == "modificar_precio")
		if($id_producto > 0)
			$proBLO->ModificarPrecio($precio);
		else
			$msg = "Operacion NO Existosa!";
	
	?>
	<script type="text/javascript">
            alert('<?php echo $msg;?>');
	</script>
	<?php
	Redireccionar($opcion_original_key, $usr_key, $id_centro);
}

if($operacion == "modificar_pack_producto")
{
	$nro_elementos = 5;
	$pack_arr = explode("&", $pack_contenido);
	
	$cuenta = count($pack_arr) / $nro_elementos;
	
	$proBLO = new ProductoBLO();
		
	for($i = 0; $i < $cuenta; $i ++)
	{
		$id = explode("=", $pack_arr[$i * $nro_elementos]);
		//echo "ID: ".$id[1];echo "</br>";
		$id_producto = explode("=", $pack_arr[$i * $nro_elementos + 1]);
		//echo "ID Producto: ".$id_producto[1];echo "</br>";
		$pack_item_status = explode("=", $pack_arr[$i * $nro_elementos + 2]);
		//echo "Status: ".$pack_item_status[1];echo "</br>";
		$item_cantidad = explode("=", $pack_arr[$i * $nro_elementos + 3]);
		//echo "Cantidad: ".$item_cantidad[1];echo "</br>";
		$item_flag_habilitado = explode("=", $pack_arr[$i * $nro_elementos + 4]);
		//echo "item_flag_habilitado: ".$item_flag_habilitado[1];echo "</br>";
		
		if($pack_item_status[1] > 0)
		{
			$pack_item = new ProductoPackItem();
			$pack_item->id = $id[1];
			$pack_item->id_producto_pack = $id_producto_pack;
			$pack_item->id_producto = $id_producto[1];
			$pack_item->cantidad = $item_cantidad[1];
			$pack_item->flag_habilitado = $item_flag_habilitado[1];
			
			echo json_encode($pack_item);
			
			if($pack_item_status[1] == 1)
				$proBLO->RegistrarPackItem($pack_item);
			if($pack_item_status[1] ==  2)
				$proBLO->ModificarPackItem($pack_item);
			
		}
	}
	
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



function json_encode2($list)
{
	$cadena = "{\"data\":[]}";
	if(!is_null($list))
		if(count($list) > 0)
		{
			$cadena = "";
			
			switch($list[0]->tipo_objeto)
			{
				case "producto":
					$i = 0;
					$separator = "";
					$cadena = "{\"data\":[";
					foreach($list as $o)
					{
						if($i > 0)							
							$separator = ",";
						$objeto = $separator."["."\"".$o->id."\",";	
						$objeto = $objeto."\"".$o->producto_categoria."\",";
						$objeto = $objeto."\"".$o->id_producto_categoria."\",";
						$objeto = $objeto."\"".$o->id_producto_categoria2."\",";
						$objeto = $objeto."\"".$o->id_producto_categoria3."\",";
						$objeto = $objeto."\"".$o->id_producto_categoria4."\",";
						$objeto = $objeto."\"".$o->codigo."\",";
						$objeto = $objeto."\"".$o->descripcion_corta."\",";
						$objeto = $objeto."\"".$o->descripcion_larga."\",";
						$objeto = $objeto."\"".$o->pais_origen."\",";
						$objeto = $objeto."\"".$o->id_pais_origen."\",";
						$objeto = $objeto."\"".$o->nro_serie."\",";
						$objeto = $objeto."\"".$o->dimension."\",";
						$objeto = $objeto."\"".$o->unidad_medida."\",";
						$objeto = $objeto."\"".$o->id_unidad_medida."\",";
						$objeto = $objeto."\"".$o->codigo_unidad_medida."\",";
						$objeto = $objeto."\"".$o->opcion_cantidad."\",";
						$objeto = $objeto."\"".$o->usuario."\",";
						$objeto = $objeto."\"".$o->id_usuario."\",";
						$objeto = $objeto."\"".$o->id_marca."\",";
						$objeto = $objeto."\"".$o->marca."\",";
						$objeto = $objeto."\"".$o->id_cantidad_default."\",";
						$objeto = $objeto."\"".$o->flag_venta."\",";
						$objeto = $objeto."\"".$o->flag_pack."\",";
						$objeto = $objeto."\"".$o->tipo_objeto."\"]";
						$cadena = $cadena.$objeto;
						$i ++;
					}	
					$cadena = $cadena."]}";
					break;
				case "producto_categoria":
					$i = 0;
					$separator = "";
					$cadena = "{\"data\":[";
					foreach($list as $o)
					{
						if($i > 0)							
							$separator = ",";
						$objeto = $separator."["."\"".$o->id."\",";	
						$objeto = $objeto."\"".$o->descripcion."\",";
						$objeto = $objeto."\"".$o->id_categoria_padre."\"]";
						$cadena = $cadena.$objeto;
						$i ++;
					}	
					$cadena = $cadena."]}";
					break;
				case "producto_pack_item":
					$i = 0;
					$separator = "";
					$cadena = "{\"data\":[";
					foreach($list as $o)
					{
						if($i > 0)							
							$separator = ",";
						$objeto = $separator."["."\"".$o->id."\",";
						$objeto = $objeto."\"".$o->id_producto_pack."\",";
						$objeto = $objeto."\"".$o->pack_descripcion_corta."\",";
						$objeto = $objeto."\"".$o->pack_descripcion_larga."\",";
						$objeto = $objeto."\"".$o->pack_nro_serie."\",";
						$objeto = $objeto."\"".$o->pack_opcion_cantidad."\",";
						$objeto = $objeto."\"".$o->pack_marca."\",";
						$objeto = $objeto."\"".$o->pack_cantidad_default."\",";
						$objeto = $objeto."\"".$o->pack_flag_venta."\",";
						$objeto = $objeto."\"".$o->id_producto."\",";
						$objeto = $objeto."\"".$o->producto_descripcion_corta."\",";
						$objeto = $objeto."\"".$o->producto_descripcion_larga."\",";
						$objeto = $objeto."\"".$o->producto_marca."\",";
						$objeto = $objeto."\"".$o->producto_nro_serie."\",";
						$objeto = $objeto."\"".$o->cantidad."\",";
						$objeto = $objeto."\"".$o->flag_habilitado."\",";
						$objeto = $objeto."\"".$o->tipo_objeto."\"]";
						$cadena = $cadena.$objeto;
						$i ++;
					}	
					$cadena = $cadena."]}";
				break;
			}	
		}
	echo $cadena;
}


?>