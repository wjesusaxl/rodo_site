<?php
    
    class CuentaVenta
    {
    	public $id;
    	public $auto_key;
		public $id_centro;
		public $fecha_hora;
		public $id_usuario_creacion;
		public $usuario_creacion;
		public $id_usuario_cierre;
		public $fecha_hora_cierre;
		public $usuario_cierre;
		public $estado;
		public $total;
		public $id_lugar_atencion;
		public $id_centro_ubicacion;
		public $id_cliente;
		public $cliente;
		public $lugar_atencion_codigo;
		public $lugar_atencion;
		public $id_turno_atencion;
		public $turno_key;
		public $id_caja;
		public $cod_caja; 
		public $caja;
		public $id_almacen;
		public $cod_almacen;
		public $almacen;
		public $id_movimiento;
		public $movimiento_key;
		public $id_transaccion;
		public $transaccion_key;
		public $comentarios;
		public $desc_estado;
    }
	
	class CuentaVentaItem
    {
		public $id;
		public $id_cuenta_venta;
		public $id_producto;
		public $descripcion_corta;
		public $marca;
		public $nro_serie;
		public $fecha_hora;
		public $id_usuario;
		public $usuario;
		public $id_producto_precio;
		public $id_producto_precio_tipo;
		public $cod_precio;
		public $cantidad;
		public $precio_neto_mn;
		public $impuesto_mn;
		public $precio_total_mn;
		public $comentarios;
		public $flag_anulado;
		public $id_usuario_anulacion;
		public $usuario_anulacion;
		public $id_movimiento_detalle;
		public $opcion_cantidad;
		public $cantidad_default;
		public $estado;
		public $auto_key;
		public $array_precios;
    }
	
	
    class CuentaVentaDA
    {
    	private $conn;
    	public function __construct()
		{
			
			$file = "config.xml";
			$xml = simplexml_load_file($file);
			 
			try
            {
                $connection_string = "mysql:host=".$xml->database_configuration[0]->host[0].";";
                $connection_string = $connection_string."dbname=".$xml->database_configuration[0]->database[0];
                    
                $this->conn = new PDO($connection_string, 
                    $xml->database_configuration[0]->username[0], 
                    $xml->database_configuration[0]->password[0]
                );
                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);    
            }catch (PDOException $e) {
                echo 'ERROR: ' . $e->getMessage();
            }
		}
		
		public function ActualizarEstado($obj)
		{
			$estado = $obj->estado;
			
			$id_usuario = $obj->id_usuario_cierre;
			
			$fecha_hora_cierre = $obj->fecha_hora_cierre;
			
			$query = "UPDATE cuenta_venta set estado = $estado, id_usuario_cierre = $id_usuario, fecha_hora_cierre = '$fecha_hora_cierre' where id = $id_cuenta_venta";
			
			try
            {
                //echo "Beginning...";
                $result = $this->conn->prepare($query);
                $result->execute();    
                
                $last_inserted_id = $this->conn->lastInsertId();
                $affected_rows = $result->rowCount();
                
            }catch(PDOException $e)
            {
                trigger_error('Wrong SQL: ' . $query . ' Error: ' . $e->getMessage());
            }		
		}
		
		public function CancelarCuenta($id_cuenta_venta)
		{
			$query = "UPDATE cuenta_venta_detalle cvd
				INNER JOIN cuenta_venta cv ON cvd.id_cuenta_venta = cv.id
				SET cvd.flag_anulado = 1
				WHERE cv.id = $id_cuenta_venta";
			
			//echo "Query 1: $query</br>";
			
			try
            {
                //echo "Beginning...";
                $result = $this->conn->prepare($query);
                $result->execute();    
                
                $last_inserted_id = $this->conn->lastInsertId();
                $affected_rows = $result->rowCount();
                
            }catch(PDOException $e)
            {
                trigger_error('Wrong SQL: ' . $query . ' Error: ' . $e->getMessage());
            }
			
			$query = "UPDATE movimiento_detalle md
				INNER JOIN cuenta_venta_detalle cvd ON cvd.id_movimiento_detalle = md.id
				INNER JOIN cuenta_venta cv ON cvd.id_cuenta_venta = cv.id
				SET md.flag_anulado = 1
				WHERE cv.id = $id_cuenta_venta";
				
			//echo "Query 1: $query</br>";
		
			try
            {
                //echo "Beginning...";
                $result = $this->conn->prepare($query);
                $result->execute();    
                
                $last_inserted_id = $this->conn->lastInsertId();
                $affected_rows = $result->rowCount();
                
            }catch(PDOException $e)
            {
                trigger_error('Wrong SQL: ' . $query . ' Error: ' . $e->getMessage());
            }

		}
		
    	public function Registrar($cuenta_venta)
		{
			$resultado = new OperacionResultado();
			
			$query = "INSERT INTO cuenta_venta(auto_key, id_centro, id_usuario_creacion, estado, id_lugar_atencion, total, id_cliente, id_turno_atencion, id_almacen, id_movimiento)
			VALUES('$cuenta_venta->auto_key', $cuenta_venta->id_centro, $cuenta_venta->id_usuario_creacion, $cuenta_venta->estado, $cuenta_venta->id_lugar_atencion, 
			0.0, $cuenta_venta->id_cliente, $cuenta_venta->id_turno_atencion, $cuenta_venta->id_almacen, $cuenta_venta->id_movimiento)";
			
			
			//echo "Query: $query</br>";
			
			try
            {
                //echo "Beginning...";
                $result = $this->conn->prepare($query);
                $result->execute();    
                
                $last_inserted_id = $this->conn->lastInsertId();
                $affected_rows = $result->rowCount();
                
            }catch(PDOException $e)
            {
                trigger_error('Wrong SQL: ' . $query . ' Error: ' . $e->getMessage());
            }
			
			$query = "SELECT auto_key FROM cuenta_venta WHERE auto_key = '$cuenta_venta->auto_key'";
			$rs = $this->conn->query($query);
			$rs->data_seek(0);
			$row = $rs->fetch_array(MYSQLI_ASSOC);
				
			$row = mysql_fetch_array($result) or die(mysql_error());
			
			try
            {
                //echo "Beginning...";
                $result = $this->conn->query($query);    
            }catch(PDOException $e)
            {
                trigger_error('Wrong SQL: ' . $query . ' Error: ' . $e->getMessage());
            }
            
            $lista = NULL;
            
            if($result->rowCount()>0)
            {
                $row = $result->fetch();
                
                if($cuenta_venta->auto_key == $row['auto_key'])
                {
                    $resultado->id = $row["id"];
                    $resultado->codigo = "02";
                    $resultado->isOK = TRUE;
                    $resultado->mensaje = "Cuenta Venta creada exitosamente!";
                }
                
                else
                {
                    $resultado->id = 0;
                    $resultado->codigo = "03";
                    $resultado->isOK = FALSE;
                    $resultado->mensaje = "Error creando Cuenta Venta!";
                    
                }
			}
			
			
			
			return $resultado;
		}
		
		public function Listar($filtro)
		{
			if($filtro != "")
				$filtro = "WHERE $filtro";
			else
				$filtro = "";
				
			$query = "SELECT cv.id, cv.auto_key, cv.fecha_hora, cv.id_centro, cv.id_cliente, CONCAT(c.nombres, ' ', c.apellidos) cliente, cv.fecha_hora_cierre, 
				u.login usuario_creacion, uc.login usuario_cierre, cv.estado, cv.total, la.codigo lugar_atencion_codigo,  
				cv.id_usuario_creacion, IFNULL(cv.id_lugar_atencion,0) id_lugar_atencion, la.id_centro_ubicacion, ta.id id_turno_atencion, ta.auto_key turno_key, 
				ca.id id_caja, ca.codigo cod_caja, ca.descripcion caja, cv.id_almacen, a.codigo cod_almacen, a.descripcion almacen,
				cv.id_movimiento, mo.auto_key movimiento_key, cv.id_transaccion, t.auto_key transaccion_key, cv.id_usuario_cierre, la.descripcion lugar_atencion,
				cve.descripcion desc_estado
				FROM cuenta_venta cv
				INNER JOIN almacen a ON cv.id_almacen = a.id
				INNER JOIN turno_atencion ta ON cv.id_turno_atencion = ta.id
				INNER JOIN caja ca ON ta.id_caja = ca.id
				INNER JOIN usuario u ON cv.id_usuario_creacion = u.id												
				INNER JOIN cliente c ON cv.id_cliente = c.id
				INNER JOIN cuenta_venta_estado cve ON cv.estado = cve.id
				LEFT JOIN movimiento mo ON cv.id_movimiento = mo.id
				LEFT JOIN transaccion t ON cv.id_transaccion = t.id
				LEFT JOIN lugar_atencion la ON cv.id_lugar_atencion = la.id
				LEFT JOIN usuario uc ON cv.id_usuario_cierre = uc.id $filtro";
		
			//echo "Query: $query</br>";
				
			
			try
            {
                //echo "Beginning...";
                $result = $this->conn->query($query);    
            }catch(PDOException $e)
            {
                trigger_error('Wrong SQL: ' . $query . ' Error: ' . $e->getMessage());
            }
            
            $lista = NULL;
            
            if($result->rowCount()>0)
            {
                	$lista = array();
					foreach($result as $row)                    
                    //while($row = $result->fetch_assoc())
                    {
						$obj = new CuentaVenta();
						$obj->id = $row['id'];
						$obj->auto_key = $row['auto_key'];
						$obj->fecha_hora = $row['fecha_hora'];
						$obj->id_centro = $row['id_centro'];
						$obj->id_cliente = $row['id_cliente'];
						$obj->cliente = $row['cliente'];
						$obj->fecha_hora = $row['fecha_hora'];
						$obj->id_usuario_creacion = $row['id_usuario_creacion'];
						$obj->usuario_creacion = strtoupper($row['usuario_creacion']);
						$obj->estado = $row['estado'];
						$obj->id_usuario_cierre = $row["id_usuario_cierre"];
						$obj->fecha_hora_cierre = $row['fecha_hora_cierre'];
						$obj->usuario_cierre = strtoupper($row['usuario_cierre']);
						$obj->total = $row['total'];
						$obj->id_lugar_atencion = $row['id_lugar_atencion'];
						$obj->id_centro_ubicacion = $row['id_centro_ubicacion'];
						$obj->lugar_atencion_codigo = strtoupper($row['lugar_atencion_codigo']);
						$obj->lugar_atencion = strtoupper($row["lugar_atencion"]);
						$obj->id_caja = $row["id_caja"];
						$obj->cod_caja = $row["cod_caja"];
						$obj->caja = strtoupper($row["caja"]);
						$obj->id_turno_atencion = $row["id_turno_atencion"];
						$obj->turno_key = $row["turno_key"];
						$obj->id_almacen = $row["id_almacen"];
						$obj->cod_almacen = $row["cod_almacen"];
						$obj->almacen = strtoupper($row["almacen"]);
						$obj->id_movimiento = $row["id_movimiento"];
						$obj->movimiento_key = $row["movimiento_key"];
						$obj->id_transaccion = $row["id_transaccion"];
						$obj->transaccion_key = $row["transaccion_key"];
						$obj->desc_estado = $row["desc_estado"];
						
						$lista[] = $obj; 
					}               
                
            }
            
			return $lista;
		}

		public function EliminarItem($id_cuenta_venta, $id_cuenta_venta_item, $id_usuario_anulacion)
		{
			$query = "UPDATE cuenta_venta_detalle SET flag_anulado = 1, id_usuario_anulacion = $id_usuario_anulacion  
				WHERE id_cuenta_venta = $id_cuenta_venta AND id =  $id_cuenta_venta_item";
			
			try
            {
                //echo "Beginning...";
                $result = $this->conn->prepare($query);
                $result->execute();    
                
                $last_inserted_id = $this->conn->lastInsertId();
                $affected_rows = $result->rowCount();
                
            }catch(PDOException $e)
            {
                trigger_error('Wrong SQL: ' . $query . ' Error: ' . $e->getMessage());
            }
		}
		
		
		
		public function ListarCuentaVentaItem($filtro)
		{
			if($filtro != "")
				$filtro = " WHERE $filtro";
			else
				$filtro = " ";
			
			$query = "SELECT cvd.id, cvd.id_cuenta_venta, cvd.id_producto, p.descripcion_corta, p.marca, p.nro_serie, cvd.fecha_hora, cvd.id_usuario,
				u.login usuario, cvd.id_producto_precio, pp.codigo cod_precio, cvd.cantidad, cvd.precio_neto_mn, cvd.impuesto_mn, cvd.precio_total_mn, 
				cvd.comentarios, cvd.flag_anulado, cvd.id_usuario_anulacion, ua.login usuario_anulacion, cvd.id_movimiento_detalle, p.opcion_cantidad,
				p.cantidad_default, 1 estado, cvd.auto_key, pp.id_producto_precio_tipo
				FROM cuenta_venta_detalle cvd
				INNER JOIN v_producto p ON cvd.id_producto = p.id_producto
				INNER JOIN producto_precio pp ON cvd.id_producto_precio = pp.id
				INNER JOIN usuario u ON cvd.id_usuario = u.id
				LEFT JOIN usuario ua ON cvd.id_usuario_anulacion = ua.id $filtro
				ORDER BY cvd.id ASC";
				
			try
            {
                //echo "Beginning...";
                $result = $this->conn->query($query);    
            }catch(PDOException $e)
            {
                trigger_error('Wrong SQL: ' . $query . ' Error: ' . $e->getMessage());
            }
            
            $lista = NULL;
            
            if($result->rowCount()>0)
            {
                	$lista = array();                	
					foreach($result as $row)                    
                    //while($row = $result->fetch_assoc())
                    {
						$obj = new CuentaVentaItem();
						
						$obj->id = $row["id"];
						$obj->id_cuenta_venta = $row["id_cuenta_venta"];
						$obj->id_producto = $row["id_producto"];
						$obj->descripcion_corta = strtoupper($row["descripcion_corta"]);
						$obj->marca = strtoupper($row["marca"]);
						$obj->nro_serie = strtoupper($row["nro_serie"]);
						$obj->fecha_hora = $row["fecha_hora"];
						$obj->id_usuario = $row["id_usuario"];
						$obj->usuario = $row["usuario"];
						$obj->id_producto_precio = $row["id_producto_precio"];
						$obj->cod_precio = strtoupper($row["cod_precio"]);
						$obj->cantidad = $row["cantidad"];
						$obj->precio_neto_mn = $row["precio_neto_mn"];
						$obj->impuesto_mn = $row["impuesto_mn"];
						$obj->precio_total_mn = $row["precio_total_mn"];
						$obj->comentarios = $row["comentarios"];
						$obj->flag_anulado = $row["flag_anulado"];
						$obj->id_usuario_anulacion = $row["id_usuario_anulacion"];
						$obj->usuario_anulacion = $row["usuario_anulacion"];
						$obj->id_movimiento_detalle = $row["id_movimiento_detalle"];
						$obj->opcion_cantidad = $row["opcion_cantidad"];
						$obj->cantidad_default = $row["cantidad_default"];
						$obj->estado = $row["estado"];
						$obj->auto_key = $row["auto_key"];
						$obj->id_producto_precio_tipo = $row["id_producto_precio_tipo"];
						$lista[] = $obj;
						
					}               
                
            }
            
			return $lista;
		}
		
		
		public function RegistrarItem($obj)
		{
			$flag_anulado = 0;
			$id_usuario_anulacion = "NULL";
			$id_movimiento_detalle = "NULL";
						
			if(!is_null($obj->flag_anulacion))
				$flag_anulado = $obj->flag_anulado;
				
			if(!is_null($obj->id_usuario_anulacion))
				$id_usuario_anulacion = $obj->id_usuario_anulacion;
		
			if(!is_null($obj->id_movimiento_detalle))
				$id_movimiento_detalle = $obj->id_movimiento_detalle;
			
			
			$query = "INSERT cuenta_venta_detalle(id_cuenta_venta, id_producto, fecha_hora, id_usuario,  cantidad, precio_total_mn,
				impuesto_mn, precio_neto_mn, comentarios, flag_anulado, id_usuario_anulacion,  id_movimiento_detalle, id_producto_precio, auto_key)
				VALUES($obj->id_cuenta_venta, $obj->id_producto, '$obj->fecha_hora', $obj->id_usuario, $obj->cantidad, 
				$obj->precio_total_mn, $obj->impuesto_mn, $obj->precio_neto_mn, '$obj->comentarios', $flag_anulado, $id_usuario_anulacion,
				$id_movimiento_detalle, $obj->id_producto_precio, '$obj->auto_key')";
				
			try
            {
                //echo "Beginning...";
                $result = $this->conn->prepare($query);
                $result->execute();    
                
                $last_inserted_id = $this->conn->lastInsertId();
                $affected_rows = $result->rowCount();
                
            }catch(PDOException $e)
            {
                trigger_error('Wrong SQL: ' . $query . ' Error: ' . $e->getMessage());
            }
		}
		
		public function Modificar($obj)
		{
			$id_usuario_cierre = "NULL";
			$fecha_hora_cierre = "NULL";
			$id_transaccion = "NULL";
			
			if(!is_null($obj->id_usuario_cierre))
				$id_usuario_cierre = $obj->id_usuario_cierre;
			
			if(!is_null($obj->fecha_hora_cierre))
				$fecha_hora_cierre = "'$obj->fecha_hora_cierre'";
			
			if(!is_null($obj->id_transaccion))
				$id_transaccion = $obj->id_transaccion;
				
			$query = "UPDATE cuenta_venta
				SET fecha_hora_cierre = $fecha_hora_cierre,
				id_usuario_cierre = $id_usuario_cierre,
				estado = $obj->estado,
				total = $obj->total,
				id_lugar_atencion = $obj->id_lugar_atencion,
				id_cliente = $obj->id_cliente,
				id_turno_atencion = $obj->id_turno_atencion,
				id_almacen = $obj->id_almacen,
				id_transaccion = $id_transaccion
				WHERE id = $obj->id";
				
			try
            {
                //echo "Beginning...";
                $result = $this->conn->prepare($query);
                $result->execute();    
                
                $last_inserted_id = $this->conn->lastInsertId();
                $affected_rows = $result->rowCount();
                
            }catch(PDOException $e)
            {
                trigger_error('Wrong SQL: ' . $query . ' Error: ' . $e->getMessage());
            }
		}
		
		public function ModificarItem($obj)
		{
			$obj->comentarios = mysql_escape_string($obj->comentarios);
			$id_usuario_anulacion = "NULL";
			
			if(!is_null($obj->id_usuario_anulacion))
				$id_usuario_anulacion = $obj->id_usuario_anulacion;
			
			$query = "UPDATE cuenta_venta_detalle
				SET id_producto_precio = $obj->id_producto_precio,
				cantidad = $obj->cantidad,
				precio_neto_mn = $obj->precio_neto_mn,
				impuesto_mn = $obj->impuesto_mn,
				precio_total_mn = $obj->precio_total_mn,
				comentarios = '$obj->comentarios',
				flag_anulado = $obj->flag_anulado,
				id_usuario_anulacion = $id_usuario_anulacion
				WHERE id = $obj->id";
				
			//echo "Query: $query</br>";
			
			try
            {
                //echo "Beginning...";
                $result = $this->conn->prepare($query);
                $result->execute();    
                
                $last_inserted_id = $this->conn->lastInsertId();
                $affected_rows = $result->rowCount();
                
            }catch(PDOException $e)
            {
                trigger_error('Wrong SQL: ' . $query . ' Error: ' . $e->getMessage());
            }
			
		}
	
		public function RetornarMontoTotalXIdCuenta($id_cuenta_venta)
		{
		    $monto_total_mn = null;
				
			$query = "SELECT IFNULL(SUM(cvd.precio_total_mn * cvd.cantidad),0) monto_total_mn
				FROM cuenta_venta_detalle cvd
				WHERE cvd.flag_anulado = 0 AND cvd.id_cuenta_venta = $id_cuenta_venta";
				
			//echo "Query: $query</br>";
			
			try
            {
                //echo "Beginning...";
                $result = $this->conn->query($query);    
            }catch(PDOException $e)
            {
                trigger_error('Wrong SQL: ' . $query . ' Error: ' . $e->getMessage());
            }
            
            if($result->rowCount()>0)
            {
                $row = $result->fetch();
			    $monto_total_mn = $row["monto_total_mn"];
            }
		
            return $monto_total_mn;	
		}
        
        
    }

	class CuentaVentaBLO
	{
		private function Listar($filtro)
		{
			$objDA = new CuentaVentaDA();
			return $objDA->Listar($filtro);
		}
		
		public function Registrar($cuenta_venta)
		{
			$objDA = new CuentaVentaDA();
			if (!$objDA->Registrar($cuenta_venta))
				throw new Exception('No se registro la Cuenta!');
		}
		
		public function RegistrarItem($cuenta_venta_item)
		{
			$cCtaVtaDA = new CuentaVentaDA();
			$cCtaVtaDA->RegistrarItem($cuenta_venta_item);				
						
		}
		
		private function ListarItem($filtro)
		{
			$objDA = new CuentaVentaDA();
			return $objDA->ListarCuentaVentaItem($filtro);
		}
		
		public function RetornarCuentaVentaXKey($key)
		{
			//$filtro = " WHERE CV.auto_key = '$key' AND CVD.flag_anulacion = 0 ";
			$filtro = " WHERE cv.auto_key = '$key'";
			
			$objDA = new CuentaVentaDA();
			$lista = $objDA->Listar($filtro);
			
			if(!is_null($lista))
			{
				if(count($lista) > 0)
					return $lista[0];
				else
					return NULL;
			}
			else
				return NULL;			
		}
		
		public function RetornarXId($id)
		{
			$filtro = "cv.id = $id";
			
			$objDA = new CuentaVentaDA();
			$lista = $objDA->Listar($filtro);
			
			if(!is_null($lista))
			{
				if(count($lista) > 0)
					return $lista[0];
				else
					return NULL;
			}
			else
				return NULL;
			
		}
		
		public function RetornarCuentaVentaItemXId($id)
		{
			$filtro = "cvd.id = $id";
			
			$objDA = new CuentaVentaDA();
			$lista = $objDA->ListarCuentaVentaItem($filtro);
			
			if(!is_null($lista))
			{
				if(count($lista) > 0)
					return $lista[0];
				else
					return NULL;
			}
			else
				return NULL;
		}
		
		public function ListarItemsXCuentaVentaKey($key)
		{
			$filtro = " CV.auto_key = '$key' AND IFNULL(CVD.flag_anulado,0) = 0";
			$objDA = new CuentaVentaDA();
			return $objDA->ListarCuentaVentaItem($filtro);
		}
		
		public function RetornarItemXItemKey($auto_key)
		{
			$lista = $this->ListarItem("cvd.auto_key = '$auto_key'");
			
			if(!is_null($lista))
			{
				if(count($lista) > 0)
					return $lista[0];
				else
					return NULL;
			}
			else
				return NULL;
			
		}
		
		public function ListarTodosItemsXIdCuentaVenta($id)
		{
			$filtro = " cvd.id_cuenta_venta = $id";
			$objDA = new CuentaVentaDA();
			
			return $objDA->ListarCuentaVentaItem($filtro);
			
		}
		public function ListarItemsXIdCuentaVenta($id)
		{
			$filtro = " cvd.id_cuenta_venta = $id AND IFNULL(cvd.flag_anulado, 0) = 0";
			$objDA = new CuentaVentaDA();
			
			return $objDA->ListarCuentaVentaItem($filtro);
			
		}
		
		public function EliminarItem($id_cuenta_venta, $id_cuenta_venta_item, $id_usuario_anulacion)
		{
			$objDA = new CuentaVentaDA();
			$objDA->EliminarItem($id_cuenta_venta, $id_cuenta_venta_item, $id_usuario_anulacion);
		}
		
		public function CerrarCuenta($auto_key, $id_usuario)
		{
			$objDA = new CuentaVentaDA();
		 	$objDA->ActualizarEstado($auto_key, 2, $id_usuario);	
		}
		
		public function CancelarCuenta($id_cuenta_venta)
		{
			$objDA = new CuentaVentaDA();		 				
			$objDA->CancelarCuenta($id_cuenta_venta);
		}
		
		public function RetornarMontoTotalXIdCuenta($id_cuenta_venta)
		{
			$objDA = new CuentaVentaDA();
			return $objDA->RetornarMontoTotalXIdCuenta($id_cuenta_venta);
		}
		
		public function ListarCuentaVentaActivaXIdTurnoAtencion($id_turno_atencion)
		{
			$objDA = new CuentaVentaDA();
			return $objDA->Listar("cv.id_turno_atencion = $id_turno_atencion AND cv.estado = 1 ORDER BY cv.id DESC");			
		}
		
		public function Modificar($obj)
		{
			$objDA = new CuentaVentaDA();
			$objDA->Modificar($obj);
		}
		
		public function ModificarItem($obj)
		{
			$objDA = new CuentaVentaDA();
			$objDA->ModificarItem($obj);
		}
		
		public function ListarCuentasAbiertasXIdTurnoAtencion($id_turno_atencion)
		{
			$filtro = "cv.id_turno_atencion = $id_turno_atencion AND cv.estado = 1";
			return $this->Listar($filtro);
		}
		
		public function ListarCuentasXIdTurnoAtencion($id_turno_atencion)
		{
			$filtro = "cv.id_turno_atencion = $id_turno_atencion AND cv.estado <> 3";
			return $this->Listar($filtro);
		}
		
		
	}
	
	
	
?>