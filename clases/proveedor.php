<?php
	
	class ProveedorCategoria
	{
		public $id;
		public $codigo;
		public $descripcion;
	}
    
    class Proveedor
    {
        public $id;
        public $id_proveedor_categoria;
        public $cod_proveedor_categoria;
        public $proveedor_categoria;
        public $id_tipo_documento;
        public $cod_tipo_documento; 
        public $tipo_documento;
        public $nro_documento;
        public $razon_social; 
        public $nombre_comercial;
        public $direccion;
        public $telefonos;
        public $comentarios;
    }
    
	class ProveedorDA
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
        
		public function Registrar($obj)
		{
		    $obj->nro_documento = mysql_escape_string($obj->nro_documento);    
		    $obj->razon_social = mysql_escape_string($obj->razon_social);
            $obj->nombre_comercial = mysql_escape_string($obj->nombre_comercial);
            $obj->direccion = mysql_escape_string($obj->direccion);
            $obj->telefonos = mysql_escape_string($obj->telefonos);
            $obj->comentarios = mysql_escape_string($obj->comentarios);
            
			$query = "INSERT INTO proveedor (id_proveedor_categoria, id_tipo_documento, nro_documento, razon_social, nombre_comercial, direccion, comentarios,
			telefonos) VALUES($obj->id_proveedor_categoria, $obj->id_tipo_documento, '$obj->nro_documento', '$obj->razon_social',
            '$obj->nombre_comercial', '$obj->direccion', '$obj->comentarios', '$obj->telefonos');";
			
			//echo $query."</br>";
            
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
        
        public function RegistrarCategoria($obj)
        {
            $obj->codigo = mysql_escape_string($obj->codigo);
            $obj->descripcion = mysql_escape_string($obj->descripcion);
            
            $query = "INSERT INTO proveedor_categoria (codigo, descripcion) VALUES('$obj->codigo', '$obj->descripcion')";
            
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
		    $obj->nro_documento = mysql_escape_string($obj->nro_documento);
            $obj->razon_social = mysql_escape_string($obj->razon_social);
            $obj->nombre_comercial = mysql_escape_string($obj->nombre_comercial);
            $obj->direccion = mysql_escape_string($obj->direccion);
            $obj->comentarios = mysql_escape_string($obj->comentarios);
            $obj->telefonos = mysql_escape_string($obj->telefonos);
            			
			$query = "UPDATE proveedor
				SET 
                  id_proveedor_categoria = $obj->id_proveedor_categoria,
                  id_tipo_documento = $obj->id_tipo_documento,
                  nro_documento = '$obj->nro_documento',
                  razon_social = '$obj->razon_social',
                  nombre_comercial = '$obj->nombre_comercial',
                  direccion = '$obj->direccion',
                  comentarios = '$obj->comentarios',
                  telefonos = '$obj->telefonos'
				WHERE id = $obj->id";
			
			//echo $query."</br>";
                                
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
                      
           $query = "UPDATE proveedor_categoria
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
                
            $query = "SELECT id, codigo, descripcion FROM proveedor_categoria $filtro";
            
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
                        $obj = new ProveedorCategoria();
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
		
			$query = "SELECT p.id, p.id_proveedor_categoria, pc.codigo cod_proveedor_categoria, pc.descripcion proveedor_categoria,
                p.id_tipo_documento, td.codigo cod_tipo_documento, td.descripcion tipo_documento, p.nro_documento, p.razon_social, 
                p.nombre_comercial, p.direccion, p.comentarios, p.telefonos
                FROM proveedor p
                INNER JOIN proveedor_categoria pc ON p.id_proveedor_categoria = pc.id
                INNER JOIN tipo_documento td ON p.id_tipo_documento = td.id $filtro";
				
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
			
	    				$obj = new Proveedor();
						$obj->id = $row["id"];                        
						$obj->id_proveedor_categoria = $row["id_proveedor_categoria"];
	                    $obj->cod_proveedor_categoria = $row["cod_proveedor_categoria"];
	                    $obj->proveedor_categoria = strtoupper($row["proveedor_categoria"]);
	                    $obj->id_tipo_documento = $row["id_tipo_documento"];
	                    $obj->cod_tipo_documento = $row["cod_tipo_documento"];
	                    $obj->tipo_documento = strtoupper($row["tipo_documento"]);
	                    $obj->nro_documento = strtoupper($row["nro_documento"]);
	                    $obj->razon_social = strtoupper($row["razon_social"]);
	                    $obj->nombre_comercial = strtoupper($row["nombre_comercial"]);
	                    $obj->direccion = strtoupper($row["direccion"]);
	                    $obj->telefonos = strtoupper($row["telefonos"]);
	                    $obj->comentarios = strtoupper($row["comentarios"]);
						
						$lista[] = $obj;
	    			}               
                
            }
            
			return $lista;
		}
    }

	class ProveedorBLO
    {
        public function Listar($filtro)
		{
			$pDA = new ProveedorDA();
			return $pDA->Listar($filtro);
		}
        
        private function ListarCategoria($filtro)
        {
            $pDA = new ProveedorDA();
            return $pDA->ListarCategoria($filtro);
        }
		
		public function ListarCategoriaTodas()
		{
			return $this->ListarCategoria("");
		}
        		
		public function RetornarXId($id)
		{			
			$lista = $this->Listar("p.id = $id");
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
        
		public function Registrar($obj)
		{
			$pDA = new ProveedorDA();
			$pDA->Registrar($obj);	
		}
		
		public function Modificar($obj)
		{
			$pDA = new ProveedorDA();
			$pDA->Modificar($obj);	
		}
        
        public function RegistrarCategoria($obj)
        {
            $pDA = new ProveedorDA();
            $pDA->RegistrarCategoria($obj);  
        }
        
        public function ModificarCategoria($obj)
        {
            $pDA = new ProveedorDA();
            $pDA->ModificarCategoria($obj);  
        }
		
    }
    
    	


	
?>