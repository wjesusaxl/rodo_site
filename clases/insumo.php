<?php
	
	class InsumoCategoria
	{
		public $id;
		public $codigo;
		public $descripcion;
	}
	
	class Insumo
    {
    	public $id;
    	public $descripcion_corta;
    	public $descripcion_larga;
    	public $origen;
    	public $porcentaje_util;
    	public $caracteristicas;
    	public $id_marca;
        public $cod_marca;
        public $marca;
    	public $stock_minimo;
        public $id_insumo_categoria;
        public $cod_insumo_categoria;
        public $insumo_categoria;        
        public $id_unidad_medida;
        public $cod_unidad_medida;
        public $unidad_medida;
    }
	class InsumoDA
    {
		private $conn;
		public function __construct()
		{
			
			$file = "config.xml";
			$xml = simplexml_load_file($file);
			 
			#coneccion con el MDB en MySql
			//$link = mysql_connect("localhost","peruingc","banner2010", false, 65536)
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
        
		public function Crear($obj)
		{
			$pro = $producto;
            
            $query = "INSERT INTO insumo (descripcion_corta, descripcion_larga, origen, porcentaje_util, caracteristicas, id_marca, stock_minimo,
            id_insumo_categoria, id_unidad_medida) VALUES('$obj->descripcion_corta', '$obj->descripcion_larga', '$obj->origen', '$obj->porcentaje_util',
            '$obj->caracteristicas',$obj->id_marca, $obj->stock_minimo, $obj->id_insumo_categoria, $obj->id_unidad_medida);";
            
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
        
        public function CrearCategoria($obj)
        {
            $obj->codigo = mysql_escape_string($obj->codigo);
            $obj->descripcion = mysql_escape_string($obj->descripcion);
            
            $query = "INSERT INTO insumo_categoria (codigo, descripcion) VALUES('$obj->codigo', '$obj->descripcion')";
            
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
			$query = "UPDATE insumo
				SET descripcion_corta = '$obj->descripcion_corta',
                    descripcion_larga = '$obj->descripcion_larga',
                    origen = '$obj->origen',
                    porcentaje_util = '$obj->porcentaje_util',
                    caracteristicas = '$obj->caracteristicas',
                    id_marca = $obj->id_marca,
                    stock_minimo = $obj->stock_minimo,
                    id_insumo_categoria = $obj->id_insumo_categoria,
                    id_unidad_medida = $obj->id_unidad_medida
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
		
	   public function ModificarCategoria($obj)
       {           
           $obj->codigo = mysql_escape_string($obj->codigo);
           $obj->descripcion = mysql_escape_string($obj->descripcion);
                      
           $query = "UPDATE insumo_categoria
                SET codigo = '$obj->codigo',
                    descripcion = '$obj->descripcion'
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
	
		
		public function ListarCategoria($filtro)
		{
		    if($filtro != "")
                $filtro = "WHERE $filtro";
            else
                $filtro = "";
                
            $query = "SELECT id, codigo, descripcion FROM insumo_categoria $filtro";
            
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
	                    $obj = new InsumoCategoria();
	                    $obj->id = $row["id"];
	                    $obj->codigo = $row["codigo"];
	                    $obj->descripcion = $row["descripcion"];
	                    
	                    $lista[] = $obj;
	                }               
                
            }
            
			return $lista;
		}
		
		public function Listar($filtro)
		{
		    if($filtro != "")
                $filtro = " WHERE $filtro";
            else
                $filtro = "";		    
		
			$query = "SELECT i.id, i.descripcion_corta, i.descripcion_larga, i.origen, i.porcentaje_util, i.caracteristicas, i.id_marca, m.codigo cod_marca,
                m.nombre marca, i.stock_minimo, ic.codigo cod_insumo_categoria, um.codigo cod_unidad_medida,  i.id_unidad_medida, um.descripcion unidad_medida,
                ic.id id_insumo_categoria, ic.descripcion insumo_categoria
                FROM insumo i
                INNER JOIN marca m ON i.id_marca = m.id
                INNER JOIN insumo_categoria ic ON i.id_insumo_categoria = ic.id
                INNER JOIN unidad_medida um ON i.id_unidad_medida = um.id $filtro";
			
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
    					$obj = new Insumo();
    					$obj->id = $row["id"];
                        $obj->descripcion_corta = $row["descripcion_corta"];
                        $obj->descripcion_larga = $row["descripcion_larga"];
                        $obj->origen = $row["origen"];
                        $obj->porcentaje_util = $row["porcentaje_util"];
                        $obj->caracteristicas = $row["caracteristicas"];
                        $obj->id_marca = $row["id_marca"];
                        $obj->cod_marca = $row["cod_marca"];
                        $obj->marca = $row["marca"];
                        $obj->stock_minimo = $row["stock_minimo"];
                        $obj->id_insumo_categoria = $row["id_insumo_categoria"];
                        $obj->cod_insumo_categoria = $row["cod_insumo_categoria"];
                        $obj->insumo_categoria = $row["cod_insumo_categoria"];
                        $obj->id_unidad_medida = $row["id_unidad_medida"];
                        $obj->cod_unidad_medida = $row["cod_unidad_medida"];
                        $obj->unidad_medida = $row["unidad_medida"];
    					$lista[] = $obj;
    				}               
                
            }
            
			return $lista;
		}
    }

	class InsumoBLO
    {
        public function Listar($filtro)
		{
			$pDA = new InsumoDA();
			return $pDA->Listar($filtro);
		}
        
        public function ListarCategoria($filtro)
        {
            $pDA = new InsumoDA();
            return $pDA->ListarCategoria($filtro);
        }
        		
		public function RetornarXId($id)
		{			
			$lista = $this->Listar("i.id = $id");
			if(!is_null($lista))                           
                if(count($lista) == 1)
                    return $lista[0];
                else
                    return null;
            else
                return null;
		}
        
        public function RetornarCategoriaXId($id)
        {           
            $lista = $this->ListarCategoria("id = $id");
            if(!is_null($lista))
                if(count($lista) == 1)
                    return $lista[0];
                else
                    return null;
            else
                return null;
        }
        
		public function Crear($obj)
		{
			$pDA = new InsumoDA();
			$pDA->Crear($obj);	
		}
		
		public function Modificar($obj)
		{
			$pDA = new InsumoDA();
			$pDA->Modificar($obj);	
		}
        
        public function CrearCategoria($obj)
        {
            $pDA = new InsumoDA();
            $pDA->CrearCategoria($obj);  
        }
        
        public function ModificarCategoria($obj)
        {
            $pDA = new InsumoDA();
            $pDA->ModificarCategoria($obj);  
        }
		
    }
    
    	


	
?>