<?php
    
    //include ('enc_dec.php');
	    
    class ReservaCancha
    {
    	public $id;
    	public $id_cliente;
		public $auto_key;
		public $cliente_nombres_apellidos;
		public $cliente_telefonos;
		public $id_centro;
		public $fecha_hora_registro;
		public $fecha_hora_inicio;
		public $fecha_hora_fin;
		public $fecha_hora_inicio_str;
		public $fecha_hora_fin_str;
		public $id_usuario_creacion;
		public $usuario_creacion;
		public $usuario_nombres_apellidos;
		public $estado;
		public $estado_descripcion;
		public $colorweb;
		public $pago_adelantado;
		public $comentarios;
		public $flag_libre;
    }
	
	class ReservaCanchaTransaccion 
	{
		public $id;
		public $id_reserva_cancha;
		public $id_transaccion;
		public $id_usuario;
		public $usuario;
		public $auto_key;
		public $monto_neto_mn;
		public $monto_impuesto_mn;
		public $monto_otros_impuestos_mn;
		public $monto_total_mn;
		public $flag_anulado;
		public $comentarios;
		public $id_transaccion_motivo;
		public $cod_transaccion_motivo;
		public $transaccion_motivo;
		public $id_turno_atencion;
		public $turno_key;
		public $fecha_hora_registro;
	}
	
	class ReservaCanchaEstado
	{
		public $id;
		public $descripcion;
		public $colorweb;
		public $flag_libre; 
	}
	
	class ReservaCanchaHistoria
	{
			
		public $id;
		public $id_reserva_cancha;
		public $d_centro;
		public $id_cliente;
		public $cliente_nombres_apellidos;
		public $fecha_hora_registro;
		public $fecha_hora_inicio;
		public $fecha_hora_fin;
		public $id_estado;
		public $estado;
		public $id_usuario;
		public $usuario;
		public $pago_adelantado;
		public $comentarios;
		
	}
	
	
	
    class ReservaCanchaDA
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
                echo "[ERROR: ". $e->getMessage();
            }
		}
		
		public function ListarReservaCanchaTransaccion($filtro)
		{
			if($filtro != "")
				$filtro = "WHERE $filtro";
			
			$query = "SELECT rct.id, rct.id_reserva_cancha, rct.id_transaccion, t.auto_key, t.monto_neto_mn, t.monto_impuesto_mn, t.monto_otros_impuestos_mn, t.monto_total_mn, t.flag_anulado, t.comentarios,
				t.id_transaccion_motivo, tm.codigo cod_transaccion_motivo, tm.descripcion transaccion_motivo, t.id_turno_atencion, ta.auto_key turno_key, t.fecha_hora_registro,
				t.id_usuario, u.login usuario
				FROM reserva_cancha_transaccion rct	
				INNER JOIN reserva_cancha rc ON rct.id_reserva_cancha = rc.id
				INNER JOIN transaccion t ON rct.id_transaccion = t.id
				INNER JOIN transaccion_motivo tm ON t.id_transaccion_motivo = tm.id
				INNER JOIN usuario u ON t.id_usuario = u.id
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
						$obj = new ReservaCanchaTransaccion();
						$obj->id = $row["id"];
						$obj->id_usuario = $row["id_usuario"];
						$obj->usuario = $row["usuario"];
						$obj->id_reserva_cancha = $row["id_reserva_cancha"];
						$obj->id_transaccion = $row["id_transaccion"];
						$obj->auto_key = $row["auto_key"];
						$obj->monto_neto_mn = $row["monto_neto_mn"];
						$obj->monto_impuesto_mn = $row["monto_impuesto_mn"];
						$obj->monto_otros_impuestos_mn = $row["monto_otros_impuestos_mn"];
						$obj->monto_total_mn = $row["monto_total_mn"];
						$obj->flag_anulado = $row["flag_anulado"];
						$obj->comentarios = $row["comentarios"];
						$obj->id_transaccion_motivo = $row["id_transaccion_motivo"];
						$obj->cod_transaccion_motivo = $row["cod_transaccion_motivo"];
						$obj->transaccion_motivo = $row["transaccion_motivo"];
						$obj->id_turno_atencion = $row["id_turno_atencion"];
						$obj->turno_key = $row["turno_key"];
						$obj->fecha_hora_registro = $row["fecha_hora_registro"];
						
						$lista[] = $obj; 
					}               
                
            }
            
			return $lista;
		}
		
		public function Modificar($reserva)
		{
			$query = "UPDATE reserva_cancha
			SET	fecha_hora_inicio = '$reserva->fecha_hora_inicio',
				fecha_hora_fin = '$reserva->fecha_hora_fin',
				pago_adelantado = $reserva->pago_adelantado,
				id_cliente = $reserva->id_cliente,
				comentarios = '$reserva->comentarios',
				estado = $reserva->estado
			WHERE id = $reserva->id";
			
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
		
	
    	public function Registrar($obj)
		{
			$obj->comentarios = mysql_escape_string($obj->comentarios);
						
			$query = "INSERT INTO reserva_cancha(id_centro, auto_key, id_cliente, fecha_hora_registro, fecha_hora_inicio, fecha_hora_fin, estado, id_usuario_creacion,
			pago_adelantado, comentarios)
			VALUES('$obj->id_centro', '$obj->auto_key', $obj->id_cliente, '$obj->fecha_hora_registro', '$obj->fecha_hora_inicio',
			'$obj->fecha_hora_fin', $obj->estado, $obj->id_usuario_creacion, $obj->pago_adelantado, '$obj->comentarios')";
			
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
		
		public function RegistrarHistoria($obj)
		{
			$obj->comentarios = mysql_escape_string($obj->comentarios);
			
			$query = "INSERT INTO reserva_cancha_historia (id_reserva_cancha, id_centro, id_cliente, fecha_hora_registro, fecha_hora_inicio, fecha_hora_fin, estado, 
				id_usuario, pago_adelantado, comentarios)
				VALUES ($obj->id_reserva_cancha, $obj->id_centro, $obj->id_cliente, '$obj->fecha_hora_registro', '$obj->fecha_hora_inicio', '$obj->fecha_hora_fin', $obj->estado,
				$obj->id_usuario, $obj->pago_adelantado, '$obj->comentarios')";
				
			//echo "Historia: $query</br>";
			
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
		
		public function RegistrarReservaCanchaTransaccion($obj)
		{
			$query = "INSERT INTO reserva_cancha_transaccion (id_reserva_cancha, id_transaccion) VALUES ($obj->id_reserva_cancha, $obj->id_transaccion)";
			
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
		
		public function ListarHistoria($filtro)
		{
			if($filtro != "")
				$filtro = " WHERE $filtro";
			
			$query = "SELECT rch.id, rch.id_reserva_cancha, rch.id_centro, rch.id_cliente, CONCAT(c.nombres, CONCAT(' ', c.apellidos)) cliente_nombres_apellidos, rch.fecha_hora_registro,
				rch.fecha_hora_inicio, rch.fecha_hora_fin, rch.estado id_estado, rce.descripcion estado, rch.id_usuario, u.login usuario, rch.pago_adelantado, rch.comentarios
				FROM reserva_cancha_historia rch
				INNER JOIN cliente c ON rch.id_cliente = c.id
				INNER JOIN reserva_cancha_estado rce ON rch.estado = rce.id
				INNER JOIN usuario u ON rch.id_usuario = u.id $filtro";
				
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
						$obj = new ReservaCanchaHistoria();
						$obj->id = $row["id"];
						$obj->id_reserva_cancha = $row["id_reserva_cancha"];
						$obj->id_centro = $row["id_centro"];
						$obj->id_cliente = $row["id_cliente"];
						$obj->cliente_nombres_apellidos = $row["cliente_nombres_apellidos"];
						$obj->fecha_hora_registro = $row["fecha_hora_registro"];
						$obj->fecha_hora_inicio = $row["fecha_hora_inicio"];
						$obj->fecha_hora_fin = $row["fecha_hora_fin"];
						$obj->id_estado = $row["id_estado"];
						$obj->estado = $row["estado"];
						$obj->id_usuario = $row["id_usuario"];
						$obj->usuario = $row["usuario"];
						$obj->pago_adelantado = $row["pago_adelantado"];
						$obj->comentarios = $row["comentarios"];
						
						$lista[] = $obj; 
					}
	
				}	
				
			

			//echo "Lista: ".json_encode($lista)."</br></br>"; 
			

			return $lista;
				
		}
		
		public function Listar($filtro)
		{
			if($filtro != "")
				$filtro = "WHERE $filtro";
			else
				$filtro = "";
				
			$query = "SELECT rc.id, rc.auto_key, rc.id_centro, rc.id_cliente, rc.fecha_hora_registro, rc.fecha_hora_inicio, 
			rc.fecha_hora_fin, rc.estado, rc.id_usuario_creacion, u.login usuario, rce.descripcion estado_descripcion, 
			CONCAT(CONCAT(c.nombres, ' '), c.apellidos) cliente_nombres_apellidos, rce.colorweb,
			rc.comentarios, rc.pago_adelantado, rce.flag_libre, CONCAT(CONCAT(u.nombres, ' '), u.apellidos) usuario_nombres_apellidos
			FROM reserva_cancha rc
			INNER JOIN cliente c ON rc.id_cliente = c.id
			INNER JOIN usuario u ON rc.id_usuario_creacion = u.id
			INNER JOIN reserva_cancha_estado rce ON rc.estado = rce.id ". $filtro;
			
			//echo $query."</br></br>";
			
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
                    {
						$obj = new ReservaCancha();
						$obj->cliente_nombres_apellidos = $row['cliente_nombres_apellidos'];
						$obj->id = $row['id'];
						$obj->auto_key = $row["auto_key"];
						$obj->id_centro = $row['id_centro'];
						$obj->id_cliente = $row['id_cliente'];
						$obj->fecha_hora_registro = $row['fecha_hora_registro'];
						$obj->fecha_hora_inicio = $row['fecha_hora_inicio'];
						$obj->fecha_hora_fin = $row['fecha_hora_fin'];
						$obj->estado = $row['estado'];
						$obj->id_usuario_creacion = $row['id_usuario_creacion'];
						$obj->usuario_nombres_apellidos = $row["usuario_nombres_apellidos"];
						$obj->usuario_creacion = $row['usuario'];
						$obj->estado_descripcion = $row['estado_descripcion'];
						$obj->colorweb = $row['colorweb'];
						$obj->comentarios = $row['comentarios'];
						$obj->pago_adelantado = $row['pago_adelantado'];
						$obj->flag_libre = $row['flag_libre'];


						$query = "SELECT id_cliente, telefono FROM cliente_telefono WHERE id_cliente = $obj->id_cliente 
                            AND habilitado = 1";

						try
                        {
                            $result2 = $this->conn->query($query);
                        }catch(PDOException $e)
                        {
                            trigger_error('Wrong SQL: ' . $query . ' Error: ' . $e->getMessage());
                        }

                        $lista2 = NULL;

                        if($result2->rowCount()>0)
                        {
                            $cliente_telefonos = "";
                            foreach($result2 as $row2)
                            {
                                if($cliente_telefonos == "")
                                    $cliente_telefonos = $row2["telefono"];
                                else
                                    $cliente_telefonos = $cliente_telefonos."-".$row2["telefono"];
                            }

                        }

                        $obj->cliente_telefonos = $cliente_telefonos;
						$lista[] = $obj; 
					}
	
				
				
			}
			//echo "Lista: ".json_encode($lista)."</br></br>"; 

			return $lista;
		}

		public function ListarEstado($filtro)
		{
			if($filtro != "")
				$filtro = "WHERE $filtro";
			else
				$filtro = "";
				
			
			$query = "SELECT id, descripcion, colorweb, flag_libre
			FROM reserva_cancha_estado
			$filtro ";
			
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
						$obj = new ReservaCanchaEstado();
						$obj->id = $row["id"];
						$obj->descripcion = $row["descripcion"];
						$obj->colorweb = $row["colorweb"];
						$obj->flag_libre = $row["flag_libre"];
						
						$lista[] = $obj; 
					 }               
                
            }
            
			return $lista;
		}

			
    }

	class ReservaCanchaBLO
	{
		public function RegistrarHistoria($obj)
		{
			$objDA = new ReservaCanchaDA();
			
			$obj_h = new ReservaCanchaHistoria();
			
			$obj_h->id_cliente = $obj->id_cliente;
			$obj_h->id_reserva_cancha = $obj->id;
			$obj_h->id_centro = $obj->id_centro;
			$obj_h->fecha_hora_registro = date('Y-m-d H:i:s');
			$obj_h->fecha_hora_inicio = $obj->fecha_hora_inicio;
			$obj_h->fecha_hora_fin = $obj->fecha_hora_fin;
			$obj_h->estado = $obj->estado;
			$obj_h->id_usuario = $obj->id_usuario_creacion;
			$obj_h->pago_adelantado = $obj->pago_adelantado;
			$obj_h->comentarios = $obj->comentarios; 
			
			//echo "OBJ Historia: ".json_encode($obj)."</br>";
			
			$objDA->RegistrarHistoria($obj_h);
			
		}
		
		public function Registrar($obj)
		{
			$resultado = new OperacionResultado();
			$objDA = new ReservaCanchaDA();
			
			$lista = $this->ListarReservaActivaXFechaIniyFechaFin($reserva_cancha->id_centro, 
				$reserva_cancha->fecha_hora_inicio, $reserva_cancha->fecha_hora_fin);
				
			if(is_null($lista))
			{
				$reservas = count($lista);
				if($reservas == 0)
				{
					$objDA->Registrar($obj);
					
					$obj = $this->RetornarXKey($obj->auto_key);
					if(!is_null($obj))
					{
						$resultado->id = $obj->id;
						$resultado->codigo = "02";
						$resultado->isOK = TRUE;
						$resultado->mensaje = "Reserva [$obj->id] creada exitosamente!";
						
						$this->RegistrarHistoria($obj);
					}
					else
					{
						$resultado->id = 0;
						$resultado->codigo = "03";
						$resultado->isOK = FALSE;
						$resultado->mensaje = "No se creó la Reserva. Revisar.!";
					}
				}
				else 
				{
					
					$resultado->id = 0;
					$resultado->isOK = FALSE;
					$resultado->codigo = "03";
					$resultado->mensaje = "Existe(n) $reservas reserva(s) registrada(s) en este horario!";	
				}							
			}
			else 
			{
				$resultado->id = 0;
				$resultado->isOK = FALSE;
				$resultado->codigo = "03";
				$resultado->mensaje = "Erro verificando Reservas Existentes!";	
			}
			
			return $resultado;
		}
		
		private function Listar($filtro)
		{
			$cReservaDA = new ReservaCanchaDA();
			
			return $cReservaDA->Listar($filtro);
		}
		
		public function RetornarXId($id)
		{
			$lista = $this->Listar("rc.id = $id");
			if(!is_null($lista))
				if(count($lista) > 0)
					return $lista[0];
				else
					return NULL;
			else
				return NULL;
		}
		
		public function RetornarXKey($key)
		{
			$lista = $this->Listar("rc.auto_key = '$key'");
			if(!is_null($lista))
				if(count($lista) > 0)
					return $lista[0];
				else
					return NULL;
			else
				return NULL;
		}
		
		public function RetornarReservaActivaXFechaHoraInicio($fecha_hora_inicio)
		{
			$filtro= "rc.fecha_hora_inicio = '$fecha_hora_inicio' AND rce.flag_libre = 0 ORDER BY rc.id";
			
			$lista = $this->Listar($filtro); 
			
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
		
		public function ListarReservaXFechaIniyFechaFin($id_centro, $fecha_hora_inicio, $fecha_hora_fin)
		{
			
			if($fecha_hora_inicio != "" && $fecha_hora_fin != "")
			{
				
				$objDA = new ReservaCanchaDA();
				
				$filtro = "rc.id_centro = $id_centro AND rc.fecha_hora_inicio >= '$fecha_hora_inicio' AND rc.fecha_hora_inicio <= '$fecha_hora_fin' ORDER BY rc.fecha_hora_inicio ASC";
			
				return $objDA->Listar($filtro);					
			}			 
		}
		
		public function RetornarListaReservasCanchaXEstado($estado)
		{
						
			$filtro = " id_centro = $id_centro";
			
			if($estado > 0)
				$filtro = " AND rc.estado = $estado";
								
			$cReservaDA = new ReservaCanchaDA();
			
			return $cReservaDA->ListarReservasCancha($filtro);						
		}
		
		public function RetornarReservasCanchaXId($id)
		{
						
			//$filtro = "WHERE rc.id = $id";
			
			if($id > 0)
				$filtro = "rc.id = $id";
								
			$cReservaDA = new ReservaCanchaDA();
			
			$reservas = $cReservaDA->ListarReservasCancha($filtro);
			if($reservas != null)
				if(count($reservas) > 0)
					return $reservas[0];
				else 
					return null;
			else
				return null;
										
		}
		
		public function RetornarListaReservasCanchaXVariosEstados($id_centro, $estados)
		{
			
			$filtro = "id_centro = $id_centro";
			
			if($estados != "")	
				$filtro += " AND rc.estado in ($estados)";
								
			$cReservaDA = new ReservaCanchaDA();
			
			return $cReservaDA->ListarReservasCancha($filtro);
		}
		
		public function RetornarReservaXFechaIniyFechaFin($id_centro, $fecha_hora_inicio, $fecha_hora_fin)
		{
			
			if($fecha_hora_inicio != "" && $fecha_hora_fin != "")
			{
				$cReservaDA = new ReservaCanchaDA();
				
				$filtro = "(
							(	rc.fecha_hora_inicio <= '$fecha_hora_inicio' AND rc.fecha_hora_fin > '$fecha_hora_inicio' ) OR 
							(	rc.fecha_hora_inicio > '$fecha_hora_inicio' AND rc.fecha_hora_inicio < '$fecha_hora_fin' )) AND rc.id_centro = $id_centro ORDER BY rc.id";
			
				return $cReservaDA->ListarReservasCancha($filtro);					
			}			 
		}
		
		public function ListarReservaActivaXFechaIniyFechaFin($id_centro, $fecha_hora_inicio, $fecha_hora_fin)
		{
			
			if($fecha_hora_inicio != "" && $fecha_hora_fin != "")
			{
				$objDA = new ReservaCanchaDA();
				
				$filtro = "( rce.flag_libre = 0 AND
							((	rc.fecha_hora_inicio <= '$fecha_hora_inicio' AND rc.fecha_hora_fin > '$fecha_hora_inicio' ) OR 
							(	rc.fecha_hora_inicio > '$fecha_hora_inicio' AND rc.fecha_hora_inicio < '$fecha_hora_fin' ))) AND rc.id_centro = $id_centro ORDER BY rc.id";
			
				return $objDA->Listar($filtro);					
			}			 
		}
		
		public function ListarReservaActivaXFechaIniyFechaFinIdCliente($id_cliente, $id_centro, $fecha_hora_inicio, $fecha_hora_fin)
		{
			
			if($fecha_hora_inicio != "" && $fecha_hora_fin != "")
			{
				$cReservaDA = new ReservaCanchaDA();
				
				$filtro = "( rce.flag_libre = 0 AND rc.id_cliente = $id_cliente AND
							((	rc.fecha_hora_inicio <= '$fecha_hora_inicio' AND rc.fecha_hora_fin > '$fecha_hora_inicio' ) OR 
							(	rc.fecha_hora_inicio > '$fecha_hora_inicio' AND rc.fecha_hora_inicio < '$fecha_hora_fin' ))) AND rc.id_centro = $id_centro ORDER BY rc.id";
			
				return $cReservaDA->Listar($filtro);					
			}
			else
				return NULL;		 
		}
		
		public function ListararReservaActivaXFechaIniyFechaFinDif($id_centro, $fecha_hora_inicio, $fecha_hora_fin, $idx)
		{
			
			if($fecha_hora_inicio != "" && $fecha_hora_fin != "")
			{
				$cReservaDA = new ReservaCanchaDA();
				
				$filtro = "(
								(	rc.fecha_hora_inicio <= '$fecha_hora_inicio' AND rc.fecha_hora_fin > '$fecha_hora_inicio' ) OR 
								(	rc.fecha_hora_inicio > '$fecha_hora_inicio' AND rc.fecha_hora_inicio < '$fecha_hora_fin' )) AND (rc.id_centro = $id_centro AND
								 	rc.id <> $idx AND rce.flag_libre = 0) ORDER BY rc.id";
			
				return $cReservaDA->Listar($filtro);					
			}			 
		}
		
		public function Actualizar($obj)
		{
			$resultado = new OperacionResultado();
			$objDA = new ReservaCanchaDA();
			$lista = $this->ListararReservaActivaXFechaIniyFechaFinDif($obj->id_centro, $obj->fecha_hora_inicio, $obj->fecha_hora_fin, $obj->id);
			
			//echo "Lista: ". json_encode($lista);
				
			if(is_null($lista))
                $reservas = 0;
            else
				$reservas = count($lista);
				
			if($reservas == 0)
			{
				$resultado->id = 0;
				$resultado->codigo = "02";
				$resultado->isOK = TRUE;
				$resultado->mensaje = "Reserva Modificada exitosamente!";
				$objDA->Modificar($obj);
				$this->RegistrarHistoria($obj);
			}
			else 
			{
				$resultado->id = 0;
				$resultado->isOK = FALSE;
				$resultado->codigo = "03";
				$resultado->mensaje = "Existe(n) $reservas reserva(s) registrada(s) en este horario!";	
			}							
						
			echo $resultado->mensaje."</br>";
			
			return $resultado;
			
		}
		
		private function ListarEstado($filtro)
		{
			$objDA = new ReservaCanchaDA();
			return $objDA->ListarEstado($filtro);
		}
		
		public function ListarEstadoTodos()
		{
			return $this->ListarEstado("");
		}
		
		public function RegistrarReservaCanchaTransaccion($obj)
		{
			$objDA = new ReservaCanchaDA();
			$objDA->RegistrarReservaCanchaTransaccion($obj);
		}
		
		private function ListarTransaccion($filtro)
		{
			$objDA = new ReservaCanchaDA();
			return $objDA->ListarReservaCanchaTransaccion($filtro);
		}
		
		public function ListarTransaccionesXIdReserva($id_reserva)
		{
			return $this->ListarTransaccion("rct.id_reserva_cancha = $id_reserva ORDER BY rct.id");
		}
		
		private function ListarHistoria($filtro)
		{
			$objDA = new ReservaCanchaDA();
			return $objDA->ListarHistoria($filtro);
		}
		
		public function ListarHistoriaXIdReserva($id_reserva)
		{
			return $this->ListarHistoria("rch.id_reserva_cancha = $id_reserva ORDER BY rch.id ASC");	
		}
		
		public function ListarHistoriaReservaModificada($id_reserva)
		{
			return $this->ListarHistoria("rch.id_reserva_cancha = $id_reserva AND rch.estado <> 3 ORDER BY rch.id ASC");
		}
		
	}
	
?>