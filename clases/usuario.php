<?php	
	class Usuario
    {
    	public $id;
    	public $nombres;
		public $apellidos;
		public $dni;
		public $login;
		public $flag_habilitado;
        public $flag_cambiar_password;
		public $password_key;
		public $password_enc;
    }
	

	class UsuarioDA
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
			set_time_limit(0);
			
		    if($filtro != "")
                $filtro = " WHERE $filtro";
            else
                $filtro = "";
            
            $query = "SELECT id, dni, nombres, apellidos, login, flag_habilitado, flag_cambiar_password, password_enc, password_key 
                FROM usuario $filtro";
            
			try
            {
                //echo "Beginning...$query";
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
                        $obj = new Usuario();
                    
                        $obj->id = $row['id'];
                        $obj->nombres = $row['nombres'];
                        $obj->apellidos = $row['apellidos'];
                        $obj->dni = $row['dni'];
                        $obj->login = $row['login'];
                        $obj->flag_habilitado = $row['flag_habilitado'];
                        $obj->flag_cambiar_password = $row['flag_cambiar_password'];
                        $obj->password_enc = $row['password_enc'];
                        $obj->password_key = $row['password_key'];
                        
                        $lista[] = $obj; 
                    }
                             
            }
            			
			return $lista;
		}
		
		public function Registrar($usuario)
		{
			$query = "INSERT INTO usuario(login, nombres, apellidos, dni, password_key, password_enc, flag_habilitado, flag_cambiar_password)
			VALUES('$usuario->login', '$usuario->nombres', '$usuario->apellidos', '$usuario->dni', '$usuario->password_key',
			'$usuario->password_enc', $usuario->flag_habilitado, $usuario->flag_cambiar_password);";
			
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
        
        public function Modificar($usuario)
        {
            $query = "UPDATE usuario
            SET login = '$usuario->login', 
            nombres = '$usuario->nombres', 
            apellidos = '$usuario->apellidos', 
            dni = '$usuario->dni', 
            password_key = '$usuario->password_key', 
            password_enc = '$usuario->password_enc', 
            flag_habilitado = $usuario->flag_habilitado, 
            flag_cambiar_password = $usuario->flag_cambiar_password
            WHERE id = $usuario->id";
            
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

    }

	class UsuarioBLO
    {
        private function Listar($filtro)
        {
            $usDA = new UsuarioDA();
            $lista = NULL;
            $lista = $usDA->Listar($filtro);
                        
            return $lista;
        }
		
		public function ListarTodos()
		{
			return $this->Listar("id > 0 ORDER BY apellidos");
		}
        
    	public function RetornarUsuarioXLogin($login)
		{
			$usuarios = $this->Listar("login = '$login'");
			
            $usuario = $usuarios[0];
			return $usuario;
		}
        
        public function ValidarUsuarioXLogin($login)
        {
            $usuarios = $this->Listar("login = '$login'");
            $resultado = OperacionResultado();
            if(count($usuarios) > 0)
            {
                $usuario = $usuarios[0];
                
                $resultado->id = $usuario->id;
                $resutlado->codigo = "02";
                $resultado->mensaje = "Usuario Validado!";
                $resultado->isOK = TRUE;                    
            }
            else
            {
                $resultado->id = 0;
                $resutlado->codigo = "03";
                $resultado->mensaje = "Usuario No existe!";
                $resultado->isOK = TRUE;    
            }
        }
		
		public function RetornarUsuarioXId($id)
		{
			if($id == 0)
				$filtro = "";
			else
				$filtro = "id = '$id'";

			$lista = $this->Listar($filtro);

			return $lista[0];

		}

		public function Validar($login, $password)
		{
			$usuario = $this->RetornarUsuarioXLogin($login);
			
			$resultado = new OperacionResultado();
			
			if(!is_null($usuario))
			{
				$password_real = decrypt($usuario->password_enc, $usuario->password_key);
				if($password == $password_real)
				{
					$resultado->id = $usuario->id;
    				$resultado->codigo = "02";
					$resultado->isOK = true;
					$resultado->mensaje = "OK!";
				}
				else
				{	
					$resultado->id = 0;
    				$resultado->codigo = "03";
					$resultado->isOK = false;
					$resultado->mensaje = "Usuario/Password incorrecto(s)!";
					//$resultado->mensaje = $login;	
				}
			}
			else
			{
				$resultado->id = 0;
    			$resultado->codigo = "01";
				$resultado->isOK = false;
				$resultado->mensaje = "Usuario No Existe!";		
			}
			
			return $resultado;
		}
		
        public function Modificar($obj)
        {
            $resultado = new OperacionResultado();
            
            $usDA = new UsuarioDA();
            $usDA->Modificar($obj);
            
            
            $resultado->id = $obj->id;
            $resultado->codigo = "02";
            $resultado->isOK = TRUE;
            $resultado->mensaje = "Usuario Modificado Correctamente!";
            
            return $resultado;
        }
		
		public function Registrar($usuario)
		{
			$resultado = new OperacionResultado();
			
			$usr = $this->RetornarUsuarioXLogin($login);
			
			if(!is_null($usr))
			{
				$resultado->id = $usuario->id;
    			$resultado->codigo = "01";
				$resultado->isOK = false;
				$resultado->mensaje = "Login de Usuario ya existe!";				
			}
			else
			{
				$usrDA = new UsuarioDA();
				$usrDA->Registrar($usuario);
				
				$usr = $this->RetornarUsuarioXLogin($usuario->login);
				if(is_null($usr))
				{
					$resultado->id = 0;
	    			$resultado->codigo = "03";
					$resultado->isOK = false;
					$resultado->mensaje = "No se ha creado el Usuario. Error en la BD!";
				}
				else
				{
					$resultado->id = $usuario->id;
	    			$resultado->codigo = "02";
					$resultado->isOK = true;
					$resultado->mensaje = "Usuario creado Exitosamente!";	
				}						
			}
			
			return $resultado;
		}
    }

	
?>