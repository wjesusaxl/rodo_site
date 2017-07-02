<?php

class Stock
{
	public $id;
	public $id_producto;
	public $descripcion_corta;
	public $producto_categoria;
	public $id_producto_categoria;
	public $marca;
	public $id_almacen;
	public $cod_almacen; 
	public $almacen;
	public $id_centro;
	public $cod_centro;
	public $centro;
	public $cantidad;
	public $cantidad_minima;
	public $cantidad2;
	public $flag_venta;
	public $nro_serie;
}
	
class StockDA
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
	
    public function Listar($filtro)
	{
	    if($filtro != "")
			$filtro = " WHERE $filtro";
		else
			$filtro = "";
		
		$query = "SELECT s.id, s.id_producto, p.descripcion_corta, p.id_producto_categoria, p.producto_categoria, p.marca, s.id_almacen, a.codigo cod_almacen, 
			a.descripcion almacen, a.id_centro, c.codigo cod_centro, c.descripcion centro, s.cantidad, s.cantidad_minima, p.flag_venta, p.nro_serie
			FROM stock s
			INNER JOIN v_producto p ON s.id_producto = p.id_producto
			INNER JOIN almacen a ON s.id_almacen = a.id
			INNER JOIN centro c ON a.id_centro = c.id $filtro";
		
		//echo "$query</br>";
		
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
					$obj = new Stock();
					$obj->id = $row["id"];
					$obj->id_producto = $row["id_producto"];
					$obj->descripcion_corta = $row["descripcion_corta"];
					$obj->id_producto_categoria = $row["id_producto_categoria"];
					$obj->producto_categoria = $row["producto_categoria"];
					$obj->marca = $row["marca"];
					$obj->id_almacen = $row["id_almacen"];
					$obj->cod_almacen = $row["cod_almacen"]; 
					$obj->almacen = $row["almacen"];
					$obj->id_centro = $row["id_centro"];
					$obj->cod_centro = $row["cod_centro"];
					$obj->centro = $row["centro"];
					$obj->cantidad = $row["cantidad"];
					$obj->cantidad_minima = $row["cantidad_minima"];
					$obj->flag_venta = $row["flag_venta"];
					$obj->nro_serie = $row["nro_serie"];
					$obj->cantidad2 = 0.00;
					$lista[] = $obj;
				}               
			
		}
            
		return $lista;
	}
	
	public function ActualizarStock()
	{
		$query = "CREATE TEMPORARY TABLE temp_producto_movimiento(
			id_producto int)";
			
		try
            {
                //echo "Beginning...";
                $result = $this->conn->prepare($query);
                $result->execute();    
                
            }catch(PDOException $e)
            {
                trigger_error('Wrong SQL: ' . $query . ' Error: ' . $e->getMessage());
            }
		
		$query = "CREATE TEMPORARY TABLE temp_stock (
			id_producto int,
			id_almacen int,
			cantidad decimal(10,2))";
		
		try
            {
                //echo "Beginning...";
                $result = $this->conn->prepare($query);
                $result->execute();    
                
            }catch(PDOException $e)
            {
                trigger_error('Wrong SQL: ' . $query . ' Error: ' . $e->getMessage());
            }

		$query = "CREATE TEMPORARY TABLE temp_stock_entrada (
			id_producto int,
			id_almacen int,
			cantidad decimal(10,2))";
			
		try
            {
                //echo "Beginning...";
                $result = $this->conn->prepare($query);
                $result->execute();    
                
            }catch(PDOException $e)
            {
                trigger_error('Wrong SQL: ' . $query . ' Error: ' . $e->getMessage());
            }
		
		$query = "CREATE TEMPORARY TABLE temp_stock_salida (
			id_producto int,
			id_almacen int,
			cantidad decimal(10,2))";

		try
            {
                //echo "Beginning...";
                $result = $this->conn->prepare($query);
                $result->execute();    
                
            }catch(PDOException $e)
            {
                trigger_error('Wrong SQL: ' . $query . ' Error: ' . $e->getMessage());
            }
		
		$query = "INSERT INTO temp_producto_movimiento(id_producto)
			SELECT DISTINCT id_producto
			FROM movimiento_detalle";
		
		if($this->conn->query($query) === false) {
			  trigger_error('Wrong SQL: ' . $query . ' Error: ' . $this->conn->error, E_USER_ERROR);
			}	
		
		$query = "INSERT INTO temp_stock_entrada(id_producto, id_almacen, cantidad)
			SELECT t.id_producto, a.id id_almacen, SUM(md.cantidad)
			FROM temp_producto_movimiento t
			INNER JOIN movimiento_detalle md ON t.id_producto = md.id_producto
			INNER JOIN movimiento m ON md.id_movimiento = m.id
			INNER JOIN almacen a ON m.id_almacen_destino = a.id
			WHERE md.flag_anulado = 0
			GROUP BY t.id_producto, a.id";

		try
            {
                //echo "Beginning...";
                $result = $this->conn->prepare($query);
                $result->execute();    
                
            }catch(PDOException $e)
            {
                trigger_error('Wrong SQL: ' . $query . ' Error: ' . $e->getMessage());
            }
		
		$query = "INSERT INTO temp_stock_salida(id_producto, id_almacen, cantidad)
			SELECT t.id_producto, a.id id_almacen, SUM(md.cantidad)
			FROM temp_producto_movimiento t
			INNER JOIN movimiento_detalle md ON t.id_producto = md.id_producto
			INNER JOIN movimiento m ON md.id_movimiento = m.id
			INNER JOIN almacen a ON m.id_almacen_origen = a.id
			WHERE md.flag_anulado = 0
			GROUP BY t.id_producto, a.id";
			
		try
            {
                //echo "Beginning...";
                $result = $this->conn->prepare($query);
                $result->execute();    
                
            }catch(PDOException $e)
            {
                trigger_error('Wrong SQL: ' . $query . ' Error: ' . $e->getMessage());
            }
			
		$query = "INSERT INTO temp_stock(id_producto, id_almacen, cantidad)
			SELECT id_producto, id_almacen, 0
			FROM temp_stock_entrada
			UNION
			SELECT id_producto, id_almacen, 0
			FROM temp_stock_salida";
			
		try
            {
                //echo "Beginning...";
                $result = $this->conn->prepare($query);
                $result->execute();    
                
            }catch(PDOException $e)
            {
                trigger_error('Wrong SQL: ' . $query . ' Error: ' . $e->getMessage());
            }
			
		$query = "UPDATE temp_stock s
			INNER JOIN temp_stock_entrada se ON s.id_producto = se.id_producto AND s.id_almacen = se.id_almacen
			SET s.cantidad = s.cantidad + se.cantidad";
			
		try
            {
                //echo "Beginning...";
                $result = $this->conn->prepare($query);
                $result->execute();    
                
            }catch(PDOException $e)
            {
                trigger_error('Wrong SQL: ' . $query . ' Error: ' . $e->getMessage());
            }
			
		$query = "UPDATE temp_stock s
			INNER JOIN temp_stock_salida ss ON s.id_producto = ss.id_producto AND s.id_almacen = ss.id_almacen
			SET s.cantidad = s.cantidad - ss.cantidad";
			
		try
            {
                //echo "Beginning...";
                $result = $this->conn->prepare($query);
                $result->execute();    
                
            }catch(PDOException $e)
            {
                trigger_error('Wrong SQL: ' . $query . ' Error: ' . $e->getMessage());
            }
			
		$query = "UPDATE stock s
			INNER JOIN temp_stock ts ON s.id_producto = ts.id_producto AND s.id_almacen = ts.id_almacen
			SET s.cantidad = ts.cantidad";
			
		try
            {
                //echo "Beginning...";
                $result = $this->conn->prepare($query);
                $result->execute();    
                
            }catch(PDOException $e)
            {
                trigger_error('Wrong SQL: ' . $query . ' Error: ' . $e->getMessage());
            }
			
		$query = "INSERT stock (id_producto, id_almacen, cantidad, cantidad_minima)
			SELECT ts.id_producto, ts.id_almacen, ts.cantidad, 0
			FROM temp_stock ts
			WHERE NOT EXISTS ( SELECT s.id_producto FROM stock s WHERE s.id_producto = ts.id_producto AND
			s.id_almacen = ts.id_almacen )";
			
		try
            {
                //echo "Beginning...";
                $result = $this->conn->prepare($query);
                $result->execute();    
                
            }catch(PDOException $e)
            {
                trigger_error('Wrong SQL: ' . $query . ' Error: ' . $e->getMessage());
            }
		
		$query = "DROP TABLE temp_stock";
		try
            {
                //echo "Beginning...";
                $result = $this->conn->prepare($query);
                $result->execute();    
                
            }catch(PDOException $e)
            {
                trigger_error('Wrong SQL: ' . $query . ' Error: ' . $e->getMessage());
            }
		
		$query = "DROP TABLE temp_stock_entrada";
		try
            {
                //echo "Beginning...";
                $result = $this->conn->prepare($query);
                $result->execute();    
                
            }catch(PDOException $e)
            {
                trigger_error('Wrong SQL: ' . $query . ' Error: ' . $e->getMessage());
            }
		
		$query = "DROP TABLE temp_stock_salida";
		try
            {
                //echo "Beginning...";
                $result = $this->conn->prepare($query);
                $result->execute();    
                
            }catch(PDOException $e)
            {
                trigger_error('Wrong SQL: ' . $query . ' Error: ' . $e->getMessage());
            }			
			
	}
		
}

class StockBLO
{
	private function Listar($filtro)
	{
		$objDA = new StockDA();
		return $objDA->Listar($filtro);
	}
	
	public function ListarXIdAlmacen($id_almacen)
	{
		$filtro = "s.id_almacen = $id_almacen ORDER BY p.descripcion_corta";
		return $this->Listar($filtro);
	}
	
	public function ActualizarStock()
	{
		$objDA = new StockDA();
		$objDA->ActualizarStock();
	}
	
	public function RetornarStockXIdProductoIdAlmacen($id_producto, $id_almacen)
	{
		$lista = $this->Listar("s.id_producto = $id_producto AND s.id_almacen = $id_almacen");
		
		if(!is_null($lista))
			if(count($lista) > 0)
				return $lista[0];
			else
				return null;
		else 
			return null;
	}
	
}
?>