<?php

session_start();

date_default_timezone_set("America/Lima");

include ('clases/opcion.php');
include ('clases/general.php');
include ('clases/enc_dec.php');
include ('clases/usuario.php');
include ("clases/centro.php");

$enlace_original = "administracion/usuario.php";
$enlace_original = "$enlace_original?opcion_key=$opcion_key&usr_key=$usr_key&id_centro=$id_centro";


$operacion = RetornarPOSTGET("operacion", "");
$opcion_key = RetornarPOSTGET("opcion_key", "");
$op_original_key = RetornarPOSTGET("op_original_key", "");
$usr_key = RetornarPOSTGET("usr_key", "");

$id_usuario = RetornarPOSTGET("id_usuario", 0);
$id_centro = RetornarPOSTGET("id_centro", 0);
$id_usuario_conf = RetornarPOSTGET("id_usuario_conf", 0);
$id_centro_conf = RetornarPOSTGET("id_centro_conf", 0);
$login = RetornarPOSTGET("login", "");
$dni = RetornarPOSTGET("dni", "");
$password = RetornarPOSTGET("password", "");
$nombres = RetornarPOSTGET("nombres", "");
$apellidos = RetornarPOSTGET("apellidos", "");
$flag_habilitado = RetornarPOSTGET("flag_habilitado", "");
$flag_cambiar_password = RetornarPOSTGET("flag_cambiar_password", "");
$password_nuevo = RetornarPOSTGET("password_nuevo", "");
$usuario_opciones = RetornarPOSTGET("usuario_opciones", NULL);

	
if($operacion == "crear")
{
	if($login != "")
	{
		$objBLO = new UsuarioBLO();
		$obj = new Usuario();
		
		$obj->login = $login;
		$obj->nombres = strtoupper($nombres);
        $obj->apellidos = strtoupper($apellidos);
        $obj->dni = strtoupper($dni);
        $obj->password_key = random_string();
        $obj->password_enc = encrypt($password, $obj->password_key);
        $obj->flag_habilitado = $flag_habilitado;
        $obj->flag_cambiar_password = $flag_cambiar_password;
        
		$resultado = $objBLO->Registrar($obj);
		
		?>
		<script type="text/javascript">
			alert('<?php echo $resultado->mensaje;?>');
			location.href = '<?php echo $enlace_original; ?>'; 
		</script>	
		
	<?php
	}		
}

if($operacion == "modificar")
{
	if($id_usuario > 0)
	{
		$objBLO = new UsuarioBLO();
		$obj = $objBLO->RetornarUsuarioXId($id_usuario);
        if(!is_null($obj))
        {
            $obj->id = $id_usuario;
            $obj->login = $login; 
            $obj->nombres = strtoupper($nombres);
            $obj->apellidos = strtoupper($apellidos);
            $obj->dni = strtoupper($dni);
            if($cambio_password == 1)
            {
                $obj->password_key = random_string();
                $obj->password_enc = encrypt($password, $obj->password_key);            
            }
            
            $obj->flag_habilitado = $flag_habilitado;
            $obj->flag_cambiar_password = $flag_cambiar_password;
            
            $resultado = $objBLO->Modificar($obj);
            
        }
        else 
        {?>
            <script type="text/javascript">
                alert('Error Modificando el Usuario. Favor Revisar!');
                location.href = '<?php echo $enlace_original; ?>'; 
            </script>            
        <?php    
        }
       
		?>
		<script type="text/javascript">
			alert('<?php echo $resultado->mensaje;?>');
			location.href = '<?php echo $enlace_original; ?>'; 
		</script>			
	<?php
	}
}

if($operacion == "cambiar_password")
{
	
    if($id_usuario > 0)
    {
        $objBLO = new UsuarioBLO();
        $obj = $objBLO->RetornarUsuarioXId($id_usuario);
	
        
        if(!is_null($obj))
        {
        	echo "aca vamos";
            $obj->password_key = random_string();
            $obj->password_enc = encrypt($password_nuevo, $obj->password_key);
            $obj->flag_cambiar_password = 0;
            $resultado = $objBLO->Modificar($obj);            
        }
	
        ?>
        <script type="text/javascript">
            alert('<?php echo $resultado->mensaje;?>');             
        </script>
        <?php
        
        //echo "Opcion Original: $op_original_key - Usr Key: $usr_key - Id Empresa: $id_centro ";        
        Redireccionar($op_original_key, $usr_key, $id_centro);
    }
}



if($operacion == "asignar_opciones")
{
    if($id_usuario_conf > 0)
    {
    	
        $objBLO = new UsuarioBLO();                
        $opBLO = new OpcionBLO();
		$cenBLO = new CentroBLO();
		
        //$opBLO->DesahibilitarOpcionesXIdUsuario($id_usuario_conf);
        
        $lista_opciones = $opBLO->ListarActivas();
		$lista_centros = $cenBLO->ListarTodos();
        
		if(!is_null($lista_opciones))
		{
			foreach($lista_opciones as $o)
			{
				$uo_n = new UsuarioOpcion();
				$uo_n->id_usuario = $id_usuario_conf;
				$uo_n->id_opcion = $o->id;
				$uo_n->flag_habilitado = 0;
				
				$uo_a = $opBLO->RetornarUsuarioOpcionXIdUsuarioIdOpcion($id_usuario_conf, $o->id);
				
				if(!is_null($usuario_opciones))
					foreach($usuario_opciones as $uo)
		        		if($uo == $o->id)
							$uo_n->flag_habilitado = 1;
						
				if(!is_null($uo_a))
				{
					$uo_n->id = $uo_a->id;
					$opBLO->ModificarUsuarioOpcion($uo_n);
				}
				else 
					if($uo_n->flag_habilitado == 1)
						$opBLO->RegistrarUsuarioOpcion($uo_n);
				
				
				foreach($lista_centros as $c)
				{
					$uco_n = new UsuarioCentroOpcion();
					$uco_n->id_usuario = $id_usuario_conf;
					$uco_n->id_opcion = $o->id;
					$uco_n->id_centro = $c->id;
					$uco_n->flag_habilitado = 0;
					
					$uco_a = $opBLO->RetornarUsuarioCentroOpcion($id_usuario_conf, $o->id, $c->id);
					
					if(isset($_POST["usuario_opciones_centro_$c->id"]))
					{
						$usuario_opciones_centro = $_POST["usuario_opciones_centro_$c->id"];
						if(!is_null($usuario_opciones_centro))
						{
							foreach($usuario_opciones_centro as $uco)
								if($uco == $o->id)
									$uco_n->flag_habilitado = 1;	
						}						
					}
						
					if(!is_null($uco_a))
					{
						$uco_n->id = $uco_a->id;
						$opBLO->ModificarUsuarioCentroOpcion($uco_n);
					}
					else
						if($uco_n->flag_habilitado == 1)
							$opBLO->RegistrarUsuarioCentroOpcion($uco_n);						
				}
				
				
			}
		}
        
	/*
        ?>
        <script type="text/javascript">
            alert('Opciones Actualizadas para el Usuario!');             
        </script>
        <?php*/        
    }
    //Redireccionar($op_original_key, $usr_key, $id_centro);
    
}

if($operacion == "query")
{
	if(isset($_GET['login']))
		$login = $_GET['login'];
	else 
		$login = "";
	
	if(isset($_GET['nombres']))
		$nombres = $_GET['nombres'];
	else 
		$nombres = "";
    
    if(isset($_GET['apellidos']))
        $apellidos = $_GET['apellidos'];
    else 
        $apellidos = "";
    
    if(isset($_GET['dni']))
        $dni = $_GET['dni'];
    else 
        $dni = "";
    
    $objBLO = new UsuarioBLO();
	$obj = new Usuario();
	
	if($login != "")
		$filtro .= " AND login like '%$login%'";
		
	if($nombres != "")
		$filtro .= " AND nombres like '%$nombres%'";
    
    if($apellidos != "")
        $filtro .= " AND apellidos like '%$apellidos%'";
    
    if($dni != "")
        $filtro .= " AND dni like '%$dni%'";
    
    if(strlen($filtro) > 0)
		$filtro = substr($filtro, 5);
	else
		$filtro = "";
    
    $lista = $objBLO->Listar($filtro);
	echo json_encode($lista);
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