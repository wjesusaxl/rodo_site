<?php 
	
session_start();
	
$main_opcion_key = "8O9MQ9B4";
	
include('clases/enc_dec.php');	
include('clases/usuario.php');
include('clases/centro.php');
include('clases/opcion.php');
include('clases/general.php');
	
if(isset($_SESSION["session_key"]))
	unset($_SESSION["session_key"]);
if(isset($_SESSION["rodo_timeout"]))
	unset($_SESSION["rodo_timeout"]);
	
	//setcookie("timeout");
    
if(isset($_GET['q']))    
	$resultado = $_GET['q'];
else 
	$resultado = '';
    	
if(isset($_POST['usuario']))
	$usuario = $_POST['usuario'];
else 
	$usuario = '';	
    
if(isset($_POST['password']))
	$password = $_POST['password'];
else
	$password = '';
    
if(isset($_POST["operacion"]))
	$operacion = $_POST["operacion"];
else
	$operacion = "";
	
if(isset($_POST["id_centro"]))
	$id_centro = $_POST["id_centro"];
else
	$id_centro = 0;
	
if($operacion == "validar")
{
	$cUsrBLO = new UsuarioBLO();
		
	$resultado = $cUsrBLO->Validar($usuario, $password);
        
	if(!is_null($resultado))
	{				
		if($resultado->isOK)				
		{
		    $key = random_string();
                
			$usuario_key = encrypt($usuario, $key);
			$_SESSION["session_key"] = $key;
			$_SESSION["rodo_timeout"] = time();
			
			session_write_close();
					
			header("Location: redirect.php?opcion_key=$main_opcion_key&usr_key=$usuario_key&id_centro=$id_centro");
			
			
			/*?>
				<script type="text/javascript">
					location.href = "<?php echo "redirect.php?opcion_key=$main_opcion_key&usr_key=$usuario_key&id_centro=$id_centro";?>"
				</script>
			<?php*/
			
		}
                
		$msg_resultado = $resultado->mensaje;
	}
	else 
		$msg_resultado = "Error de Base de Datos!";
		
}
	
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN"
"http://www.w3.org/TR/html4/strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>RODO</title>
		<meta name="author" content="Jesus Rodriguez" />
		<!-- Date: 2011-11-28 -->
		<link rel="stylesheet" href="styles/login.css?v=0.025" type="text/css" />
		<script type="text/javascript">
			function Ingresar()
			{
				usuario = document.login.usuario.value ;
				password = document.login.password.value;
				id_centro = document.login.id_centro.value;
				
				mensaje = "";
				
				res = true;
				
				if(usuario == "")
				{
					res = false;
					mensaje += "* No ha Ingresado Usuario!\n";
				}
				
				if(password == "")
				{
					res = false;
					mensaje += "* No ha Ingresado Password!\n";					
				}
				
				if(id_centro == 0)
				{
					res = false;
					mensaje += "* No ha seleccionado Centro!"
				}
				
				if(res)
				{
					document.login.operacion.value = "validar";
					document.login.submit();					
				}
				else
				{
					mensaje = "Se han encontrado los siguientes errores: \n\n" + mensaje;
					alert(mensaje);
				}
				
			}
			
			function Entrar(e)
			{				
			    if (e.keyCode == 13) 
			    	Ingresar();

			}
		</script>
		<!--script type="text/javascript" src="http://use.typekit.com/xiy3cef.js"></script-->
        <!--script type="text/javascript">try{Typekit.load();}catch(e){}</script-->
		<style>
            body { background-color: #F1F1F1; }  
    		#main {  width:1100px; color: #585858; padding-bottom: 20px; padding-top: 20px; overflow: hidden; margin: 0 auto; }
    		#main input { font-size: 12px; }
    		#main select { font-size: 12px; }
    
            .clase12 { font-family:Helvetica; font-size:32px; color: #333333; }
            .etiqueta { font-family: Helvetica; font-size: 12px; } 
            /*color: rgba(127, 0, 0, 0.7); }*/
            #ingresar { font-family: Helvetica; font-size: 12px; }
            
            #etiqueta { font-size: 14px; }
            
            #div_titulo { height: 20px; 
                /*background-image: -webkit-gradient(linear, left top, left bottom, color-stop(0, #252525), color-stop(1, #292929));*/ 
                color: #3399FF; 
                padding-left: 10px; padding-right: 10px; border-radius: 10px 10px 10px 10px; padding-top: 5px; padding-bottom: 5px; }
            #titulo { font-size: 40px; font-weight:800; letter-spacing:-1px; text-shadow:1px 1px 1px #333; font-family: Helvetica; }
            
            #div_tabla_formulario { margin-top: 100px; margin-left: 20px; }
            #tabla_formulario { border: dotted 1px #3399FF; width:300px; border-radius: 10px 10px 10px 10px; background-color: #FFFFFF; 
                font-family: Helvetica; font-size: 12px; }
		    		          		          
		    #div_img_logo { /*border: dotted 1px #3399FF; float: left; border-radius: 10px 10px 10px 10px; background-color: rgba(35, 35, 35, 0.6); 
                padding-right: 7px; padding-top: 3px;*/ }
            
            #img_logo_de_local { height: 112px; width:100px; }
            #img_logo_neritos { height: 74px; width:166px; padding-bottom: 15px; }
		    
		    #id_centro { width: 140px; }		    
		</style>
		
	</head>
	<body>
		<div id="main" align="center">
		    <div id="div_img_logo">
                <img id="img_logo_de_local" src="images/logo-delocal.png"/>
                <img id="img_logo_neritos" src="images/neritos_3.png"/>
            </div>
            <div id="div_titulo">
                <span id="titulo">RODO: Sistema de Gestión y Registro para RODISNESS S.A.C.</span>
            </div>				
            <div id="div_tabla_formulario">
                <span><?php echo $query_text; ?></span>
                <form action="login.php" method="post" name="login">
                    <input type="hidden" name="operacion">
                    <table id="tabla_formulario">
                        <tr height="5px;"></tr>
                        <tr>
                            <td width="5px;"></td>
                            <td><span class="etiqueta">Usuario:</span></td>
                            <td><input name="usuario" type="input"/></td>
                        </tr>
                        <tr>
                            <td width="5px;"></td>
                            <td><span class="etiqueta">Password:</span></td>
                            <td><input name="password" type="password" onkeypress="Entrar(event)"/></td>
                        </tr>
                        <tr>
                            <td width="5px;"></td>
                            <td><span class="etiqueta">Centro:</span></td>
                            <td>
                                <select id="id_centro" name="id_centro" onkeypress="Entrar(event)"/>
                                    <option value=0>Seleccione...</option>
                                    <?php
                                        $cenBLO = new CentroBLO();
                                        $lista = $cenBLO->ListarTodos();
                                        foreach($lista as $o)
                                            echo "<option value='$o->id'>$o->descripcion</option>";
                                    ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3" align="center"><span style="color: red;"><?php echo $msg_resultado; ?></span></td>
                        </tr>
                        <tr align="center">						
                            <td colspan=3><input type="button" id="ingresar" name="ingresar" value="Ingresar" class="clase12" onclick="Ingresar()"/></td>
                        </tr>    						
                    </table>    					
                </form>
            </div>
		</div>
	</body>
</html>