<?php

    class Transaccion
    {
     	public $id;
		public $auto_key;
     	public $fecha_hora_registro;
     	public $id_centro;
		public $id_transaccion_grupo;
     	public $cod_centro;
     	public $centro;
     	public $id_usuario;		
     	public $usuario;
		public $id_caja;
		public $cod_caja;
		public $caja;
		public $monto_neto_mn;
		public $monto_impuesto_mn;
		public $monto_otros_impuestos_mn;
		public $monto_total_mn;
		public $flag_aprobado;
		public $flag_anulado;
		public $comentarios;
		public $id_transaccion_motivo; 
		public $cod_transaccion_motivo;
		public $transaccion_motivo;
		public $id_turno_atencion;
		public $turno_key;
		public $transaccion_factor;
		public $transaccion_grupo;
    }
	
	class TransaccionGrupo
	{
		public $id;
		public $codigo;
		public $descripcion;
		public $factor;
	}
	
	class TransaccionMotivo
	{
		public $id;
		public $id_transaccion_grupo;
		public $cod_transaccion_grupo;
		public $transaccion_grupo;
		public $codigo;
		public $descripcion;
	}
	
	class UsuarioGrupoTransaccion
	{
		public $id;
		public $codigo;
		public $descripcion;
		public $id_transaccion_grupo;
		public $cod_transaccion_grupo;
        public $transaccion_grupo;
		public $transaccion_factor;
	}
	
	class UsuarioMotivoTransaccion
	{
		public $id;
		public $codigo;
		public $descripcion;
		public $id_transaccion_motivo;
        public $cod_transaccion_grupo;
        public $transaccion_grupo;
		public $transaccion_factor;

	}
    
	class TransaccionDA
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
        
		public function ListarGrupo($filtro)
        {
            if($filtro != "")
                $filtro = "WHERE $filtro";
            
            $query = "SELECT id, codigo, descripcion, factor FROM transaccion_grupo $filtro";
				
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
	                    $obj = new TransaccionGrupo();
	                        
	                    $obj->id = $row["id"];
	                    $obj->codigo = $row["codigo"];
						$obj->descripcion = $row["descripcion"];
						$obj->factor = $row["factor"];
	                    
	                    $lista[] = $obj;
	                }               
                }
            
            
			return $lista;
		}
		
		public function ListarMotivo($filtro)
        {
            if($filtro != "")
                $filtro = "WHERE $filtro";
            
            $query = "SELECT tm.id, tm.codigo, tm.descripcion, tm.id_transaccion_grupo, tg.codigo cod_transaccion_grupo,
            tg.descripcion transaccion_grupo
            FROM transaccion_motivo tm
			INNER JOIN transaccion_grupo tg ON tm.id_transaccion_grupo = tg.id $filtro";
				
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
	                    $obj = new TransaccionMotivo();
	                        
	                    $obj->id = $row["id"];
						$obj->id_transaccion_grupo = $row["id_transaccion_grupo"];
						$obj->cod_transaccion_grupo = $row["cod_transaccion_grupo"];
						$obj->transaccion_grupo = $row["transaccion_grupo"];
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
                $filtro = "WHERE $filtro";
            
            $query = "SELECT t.id, t.auto_key, t.fecha_hora_registro, t.id_centro, ce.codigo cod_centro, ce.descripcion centro, t.id_usuario, u.login usuario,
				t.id_caja, ca.codigo cod_caja, ca.descripcion caja, t.monto_neto_mn, t.monto_impuesto_mn, t.monto_otros_impuestos_mn,
				t.monto_total_mn, t.flag_aprobado, t.flag_anulado, t.id_transaccion_grupo, t.comentarios, t.id_transaccion_motivo, tm.codigo cod_transaccion_motivo,
				tm.descripcion transaccion_motivo, t.id_turno_atencion, ta.auto_key turno_key, tg.factor transaccion_factor, tg.descripcion transaccion_grupo
				FROM transaccion t
				INNER JOIN centro ce ON t.id_centro = ce.id
				INNER JOIN usuario u ON t.id_usuario = u.id
				INNER JOIN transaccion_grupo tg ON t.id_transaccion_grupo = tg.id
				INNER JOIN caja ca ON t.id_caja = ca.id
				INNER JOIN transaccion_motivo tm ON t.id_transaccion_motivo = tm.id 
				LEFT JOIN turno_atencion ta ON t.id_turno_atencion = ta.id $filtro";
				
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
	                    $obj = new Transaccion();
	                        
	                    $obj->id = $row["id"];
	                    $obj->auto_key = $row["auto_key"];
				     	$obj->fecha_hora_registro = $row["fecha_hora_registro"];
				     	$obj->id_centro = $row["id_centro"];
				     	$obj->cod_centro = $row["cod_centro"];
				     	$obj->centro = $row["centro"];
				     	$obj->id_usuario = $row["id_usuario"];
				     	$obj->usuario = $row["usuario"];
						$obj->id_caja = $row["id_caja"];
						$obj->cod_caja = $row["cod_caja"];
						$obj->caja = $row["caja"];
						$obj->monto_neto_mn = $row["monto_neto_mn"];
						$obj->monto_impuesto_mn = $row["monto_impuesto_mn"];
						$obj->monto_otros_impuestos_mn = $row["monto_otros_impuestos_mn"];
						$obj->monto_total_mn = $row["monto_total_mn"];
						$obj->flag_aprobado = $row["flag_aprobado"];
						$obj->flag_anulado = $row["flag_anulado"];
						$obj->id_transaccion_grupo = $row["id_transaccion_grupo"];
	                    $obj->comentarios = $row["comentarios"];
						$obj->id_transaccion_motivo = $row["id_transaccion_motivo"];
						$obj->cod_transaccion_motivo = $row["cod_transaccion_motivo"];
						$obj->transaccion_motivo = $row["transaccion_motivo"];
						$obj->id_turno_atencion = $row["id_turno_atencion"];
						$obj->turno_key = $row["turno_key"];
						$obj->transaccion_factor = $row["transaccion_factor"];
						$obj->transaccion_grupo = $row["transaccion_grupo"];
	                    $lista[] = $obj;
	                }
	                       
	            	
            	
            }
            
            return $lista;
		}

		public function Registrar($obj)
		{
			$comentarios = mysql_escape_string($obj->comentarios);
			
			$monto_neto_mn = is_null($obj->monto_neto_mn) ? 0 : $obj->monto_neto_mn;
			$monto_impuesto_mn = is_null($obj->monto_impuesto_mn) ? 0 : $obj->monto_impuesto_mn;
			$monto_total_mn = is_null($obj->monto_total_mn) ? 0 : $obj->monto_total_mn;
			$monto_otros_impuestos_mn = is_null($obj->monto_otros_impuestos_mn) ? 0 : $obj->monto_otros_impuestos_mn;
			
			if(!is_null($obj->id_turno_atencion))
				$id_turno_atencion = $obj->id_turno_atencion;
			
			if($obj->id_turno_atencion == "")
				$id_turno_atencion = "NULL";
			
			$query = "INSERT INTO transaccion (auto_key, fecha_hora_registro, id_centro, id_usuario, id_transaccion_grupo, id_caja, monto_neto_mn,
				monto_impuesto_mn, monto_otros_impuestos_mn, monto_total_mn, flag_aprobado, flag_anulado, comentarios, id_transaccion_motivo, 
				id_turno_atencion) 
				VALUES ('$obj->auto_key', '$obj->fecha_hora_registro', $obj->id_centro, $obj->id_usuario, $obj->id_transaccion_grupo, $obj->id_caja, 
				$monto_neto_mn, $monto_impuesto_mn, $monto_otros_impuestos_mn, $monto_total_mn, $obj->flag_aprobado, $obj->flag_anulado,
				'$comentarios', $obj->id_transaccion_motivo, $id_turno_atencion)" ;
				
			//echo "Query: $query</br>";			
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
		
		public function Modificar($obj)
		{
			$comentarios = mysql_escape_string($obj->comentarios);
			
			$id_turno_atencion = "NULL";
			
			if(!is_null($obj->id_turno_atencion))
				$id_turno_atencion = $obj->id_turno_atencion;
			
			$query = "UPDATE transaccion
				SET auto_key = '$obj->auto_key',
				fecha_hora_registro = $obj->fecha_hora_registro,
				id_centro = $obj->id_centro,
				id_usuario = $obj->id_usuario,
				id_transaccion_grupo = $obj->id_transaccion_grupo,
				id_caja = $obj->id_caja,
				monto_neto_mn = $obj->monto_neto_mn,
				monto_impuesto_mn = $obj->monto_impuesto_mn,
				monto_otros_impuestos_mn = $obj->monto_otros_impuestos_mn,
				monto_total_mn = $obj->monto_total_mn,
				flag_aprobado = $obj->flag_aprobado,
				flag_anulado = $obj->flag_anulado,
				comentarios = '$comentarios',
				id_transaccion_motivo = $obj->id_transaccion_motivo,
				id_turno_atencion = $id_turno_atencion
				WHERE id = $obj->id";
			
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
		
		public function RegistrarUsuarioGrupoTransaccion($obj)
		{
			$query = "INSERT usuario_transaccion_grupo (id_centro, id_usuario, id_transaccion_grupo, flag_habilitado) VALUES ($obj->id_centro,
			$obj->id_usuario, $obj->id_transaccion_grupo, $obj->flag_habilitado)";
			
			//echo $query;
			
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
		
		public function RegistrarUsuarioMotivoTransaccion($obj)
		{
			$query = "INSERT usuario_transaccion_motivo (id_centro, id_usuario, id_transaccion_motivo, flag_habilitado) VALUES ($obj->id_centro,
			$obj->id_usuario, $obj->id_transaccion_motivo, $obj->flag_habilitado)";
			
			//echo $query;
			
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
		
		public function ModificarUsuarioGrupoTransaccion($obj)
		{
			$query = "UPDATE usuario_transaccion_grupo
			SET flag_habilitado = $obj->flag_habilitado
			WHERE id = $obj->id";
			
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
		
		public function ModificarUsuarioMotivoTransaccion($obj)
		{
			$query = "UPDATE usuario_transaccion_motivo
			SET flag_habilitado = $obj->flag_habilitado
			WHERE id = $obj->id";
			
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
		
		public function ListarUsuarioGrupoTransaccion($filtro)
        {
            if($filtro != "")
                $filtro = "WHERE $filtro";
            
            $query = "SELECT utg.id, utg.id_centro, utg.id_usuario, utg.id_transaccion_grupo, utg.flag_habilitado, tg.descripcion transaccion_grupo, tg.codigo cod_transaccion_grupo,
            tg.factor transaccion_factor
            FROM usuario_transaccion_grupo utg
            INNER JOIN transaccion_grupo tg ON utg.id_transaccion_grupo = tg.id $filtro";
				
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
	                    $obj = new UsuarioGrupoTransaccion();
	                        
	                    $obj->id = $row["id"];
	                    $obj->id_centro = $row["id_centro"];
	                    $obj->id_usuario = $row["id_usuario"];
	                    $obj->id_transaccion_grupo = $row["id_transaccion_grupo"];
						$obj->flag_habilitado = $row["flag_habilitado"];
						$obj->cod_transaccion_grupo = $row["cod_transaccion_grupo"];
						$obj->transaccion_grupo = $row["transaccion_grupo"];
						$obj->transaccion_factor = $row["transaccion_factor"];
	                    
	                    $lista[] = $obj;
	                }
	                       
	            
			}
			                
            
            return $lista;
		}
		
		public function ListarUsuarioMotivoTransaccion($filtro)
        {
            if($filtro != "")
                $filtro = "WHERE $filtro";
            
            $query = "SELECT utm.id, utm.id_centro, utm.id_usuario, utm.id_transaccion_motivo, utm.flag_habilitado, tm.descripcion transaccion_motivo, 
				tm.codigo cod_transaccion_motivo, tg.factor transaccion_factor, tg.codigo cod_transaccion_grupo
            	FROM usuario_transaccion_motivo utm
            	INNER JOIN transaccion_motivo tm ON utm.id_transaccion_motivo = tm.id
            	INNER JOIN transaccion_grupo tg On tm.id_transaccion_grupo = tg.id $filtro";
				
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
	                    $obj = new UsuarioMotivoTransaccion();
	                        
	                    $obj->id = $row["id"];
	                    $obj->id_centro = $row["id_centro"];
	                    $obj->id_usuario = $row["id_usuario"];
	                    $obj->id_transaccion_motivo = $row["id_transaccion_motivo"];
						$obj->flag_habilitado = $row["flag_habilitado"];
						$obj->cod_transaccion_motivo = $row["id_transaccion_motivo"];
						$obj->transaccion_motivo = $row["transaccion_motivo"];
						$obj->cod_transaccion_grupo = $row["cod_transaccion_grupo"]; 
						$obj->transaccion_factor = $row["transaccion_factor"];
	                    
	                    $lista[] = $obj;
	                }
	                       
	            
			}
			                
            
            return $lista;
		}
	}
    
    
    class TransaccionBLO
    {
    	private function Listar($filtro)
    	{
    		$objDA = new TransaccionDA();
    		return $objDA->Listar($filtro);
    	}
		
		private function ListarGrupoTransaccion($filtro)
		{
			$objDA = new TransaccionDA();
			return $objDA->ListarGrupo($filtro);
		}
		private function ListarUsuarioGrupoTransaccion($filtro)
		{
			$objDA = new TransaccionDA();
			return $objDA->ListarUsuarioGrupoTransaccion($filtro);
		}
		
		public function ListarGruposTransaccionHabilitadosXIdUsuarioIdCentro($id_usuario, $id_centro)
		{
			return $this->ListarUsuarioGrupoTransaccion("utg.id_usuario = $id_usuario AND utg.id_centro = $id_centro AND utg.flag_habilitado = 1");
		}
		
		public function RetornarUsuarioGrupoTransaccionXIdUsuarioIdGrupoTransaccionIdCentro($id_usuario, $id_transaccion_grupo, $id_centro)
		{
			$lista = $this->ListarUsuarioGrupoTransaccion("utg.id_usuario = $id_usuario AND utg.id_transaccion_grupo = $id_transaccion_grupo AND utg.id_centro = $id_centro");
			
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
		
		public function RetornarMotivoTransaccionXId($id)
		{
			$lista = $this->ListarMotivo("tm.id = $id");
			if(!is_null($lista))
				if(count($lista) > 0)
					return $lista[0];
				else 
					return NULL;
			else
				return NULL;
		}
		
		public function RetornarGrupoXCodTransaccionGrupo($cod_transaccion_grupo)
		{
			$lista = $this->ListarGrupoTransaccion("codigo = '$cod_transaccion_grupo'");
			
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
		public function ListarGrupoTransaccionTodos()
		{
			return $this->ListarGrupoTransaccion("");
		}
		
		public function ListarMotivoTransaccionTodos()
		{
			return $this->ListarMotivo("");	
		}
		
		public function RegistrarUsuarioGrupoTransaccion($obj)
		{
			$objDA = new TransaccionDA();
			$objDA->RegistrarUsuarioGrupoTransaccion($obj);
		}
		
		public function RegistrarUsuarioMotivoTransaccion($obj)
		{
			$objDA = new TransaccionDA();
			$objDA->RegistrarUsuarioMotivoTransaccion($obj);
		}
		
		public function ModificarUsuarioGrupoTransaccion($obj)
		{
			$objDA = new TransaccionDA();
			$objDA->ModificarUsuarioGrupoTransaccion($obj);
		}
		
		public function ModificarUsuarioMotivoTransaccion($obj)
		{
			$objDA = new TransaccionDA();
			$objDA->ModificarUsuarioMotivoTransaccion($obj);
		}
		
		public function RetornarXId($id)
		{
			$lista = $this->Listar("$t.id = $id");
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
		
		public function ListarXIdTurnoAtencion($id_turno_atencion)
		{
			return $this->Listar("t.id_turno_atencion = $id_turno_atencion AND t.flag_anulado = 0 ORDER BY t.id DESC");
		}
		
		public function RetornarXKey($key)
		{
			$lista = $this->Listar("t.auto_key = '$key'");
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
		
		public function Registrar($obj)
		{
			$objDA = new TransaccionDA();
			
			$resultado = $this->ValidarTransaccion($obj);
			
			if($resultado->isOK)
			{
				$objDA->Registrar($obj);
			
				$t = $this->RetornarXKey($obj->auto_key);
				
				$resultado = new OperacionResultado();
				
				if(!is_null($t))
				{
					$resultado->id = $t->id;
					$resultado->isOK = TRUE;
					$resultado->codigo = "02";
					$resultado->mensaje = "Transaccion ID:$t->id Creada!";
				}
				else
				{
					$resultado->id = 0;
					$resultado->isOK = FALSE;
					$resultado->codigo = "03";
					$resultado->mensaje = "Transaccion NO Creada!";	
				}	
			}
			
			return $resultado;
		}
		
		private function ListarUsuarioMotivoTransaccion($filtro)
		{
			$objDA = new TransaccionDA();
			return $objDA->ListarUsuarioMotivoTransaccion($filtro);			
		}
		
		private function ListarMotivo($filtro)
		{
			$objDA = new TransaccionDA();
			return $objDA->ListarMotivo($filtro);
		}
		
		public function ListarMotivosXIdTransaccionGrupo($id_transaccion_grupo)
		{
			return $this->ListarMotivo("tm.id_transaccion_grupo = $id_transaccion_grupo ORDER BY tm.descripcion");
		}
		
		public function ListarMotivoTransaccionesXIdUsuario($id_usuario)
		{
			return $this->ListarUsuarioMotivoTransaccion("utm.id_usuario = $id_usuario");
		}
		
		public function RetornarUsuarioMotivoXIdUsuarioIdUsuarioIdMotivo($id_usuario, $id_transaccion_motivo, $id_centro)
		{
			$filtro = "utm.id_usuario = $id_usuario AND utm.id_transaccion_motivo = $id_transaccion_motivo AND
				utm.id_centro = $id_centro";
			
			$lista = $this->ListarUsuarioMotivoTransaccion($filtro);
				
			/*ListarUsuarioMotivoTransaccion("utm.id_usuario = $id_usuario AND utm.id_transaccion_motivo = $id_transaccion_motivo AND
				utm.id_centro = $id_centro");*/
				
			if(!is_null($lista))
				if(count($lista) > 0)
					return $lista[0];
				else
					return NULL;
			else
				return NULL;
					
		}
		
		public function ValidarTransaccion($obj)
		{
			$caBLO = new CajaBLO();
			
			$resultado = new OperacionResultado();
			
			$utm = $this->RetornarUsuarioMotivoXIdUsuarioIdUsuarioIdMotivo($obj->id_usuario, $obj->id_transaccion_motivo, $obj->id_centro);
			$cu = $caBLO->RetornarCajaUsuarioXIdCajaIdUsuario($obj->id_caja, $obj->id_usuario);
			
			if(!is_null($cu))
			{
				$factor = $utm->transaccion_factor;
				
				/*echo "Flag Salida: $cu->flag_salida</br>";
				echo "Flag Ingreso: $cu->flag_ingreso</br>";
				echo "Flag Responsable: $cu->flag_responsable</br>";
				echo "Factor: $factor</br>";*/
				
				
				if($cu->habilitado)
				{
					if(($cu->flag_salida && $factor < 0) || ($cu->flag_ingreso && $factor > 0) || $cu->flag_responsable)
					{
						$resultado->id = $utm->id;
						$resultado->codigo = "02";
						$resultado->isOK = true;
						$resultado->mensaje = "Transaccion Habilitada";							
					}
					else
					{
						$resultado->id = $utm->id;
						$resultado->codigo = "03";
						$resultado->isOK = false;
						$resultado->mensaje = "Transaccion NO Habilitada a Usuario.";
					}
				}
				else
				{
					$resultado->id = $utm->id;
					$resultado->codigo = "03";
					$resultado->isOK = false;
					$resultado->mensaje = "Caja NO Habilitada a Usuario.";
				}
			}
			else
			{
				$resultado->id = 0;
				$resultado->codigo = "05";
				$resultado->isOK = false;
				$resultado->mensaje = "Caja NO Asignada a Usuario.";
			}
			
			if($resultado->isOK)
			{
				if(!is_null($utm))
				{				
					if($utm->flag_habilitado)
					{
						$resultado->id = $utm->id;
						$resultado->codigo = "02";
						$resultado->isOK = true;
						$resultado->mensaje = "Transaccion Habilitada";
					}
					else
					{
						$resultado->id = $utm->id;
						$resultado->codigo = "03";
						$resultado->isOK = false;
						$resultado->mensaje = "Motivo de Transaccion NO Habilitada a Usuario.";
					}
				}
				else
				{
					$resultado->id = 0;
					$resultado->codigo = "05";
					$resultado->isOK = false;
					$resultado->mensaje = "Motivo de Transaccion NO Asignada a Usuario.";
				}	
			}
			
			
			return $resultado;
		}
		
		
    }
    
?>