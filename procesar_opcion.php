<?php

include ("clases/opcion.php");
include ("clases/centro.php");

$operacion = RetornarPOSTGET("operacion", "");
$id_centro = RetornarPOSTGET("id_centro", 0);
$usr_key = RetornarPOSTGET("usr_key", "");
$op_original_key = RetornarPOSTGET("op_original_key", "");
$opcion_estado = RetornarPOSTGET("opcion_estado", null);
$opcion_menu = RetornarPOSTGET("opcion_menu", null);
$opcion_menu_principal = RetornarPOSTGET("opcion_menu_principal", null);
$opcion_publica = RetornarPOSTGET("opcion_publica", null);
$opcion_general = RetornarPOSTGET("opcion_general", null);

if($operacion == "modificar")
{
	echo "Redireccionando...";
	
	$opcBLO = new OpcionBLO();
	$cenBLO = new CentroBLO();
	$lista_opciones = $opcBLO->ListarTodas();
	$lista_centros = $cenBLO->ListarTodos();
	
	if(!is_null($lista_opciones))
	{
		foreach($lista_opciones as $o)
		{
			
			$o->estado = 0;
			$o->flag_menu = 0;
			$o->flag_menu_principal = 0;
			$o->flag_publica = 0;
			$o->flag_general = 0;
			
			if(!is_null($opcion_estado))			
				foreach($opcion_estado as $oe)
					if($o->id == $oe)
						$o->estado = 1;
					
			if(!is_null($opcion_menu))			
				foreach($opcion_menu as $om)
					if($o->id == $om)
						$o->flag_menu = 1;
			
			if(!is_null($opcion_menu_principal))			
				foreach($opcion_menu_principal as $omp)
					if($o->id == $omp)
						$o->flag_menu_principal = 1;
						
			if(!is_null($opcion_publica))			
				foreach($opcion_publica as $op)
					if($o->id == $op)
						$o->flag_publica = 1;
					
			if(!is_null($opcion_general))			
				foreach($opcion_general as $og)
					if($o->id == $og)
						$o->flag_general = 1;
			
			if($o->estado == 1 || $o->flag_menu == 1 || $o->flag_menu_principal == 1 || $o->flag_publica == 1 || $o->flag_general == 1)
				$opcBLO->Modificar($o);
			
			if($o->general == 0)
			{
				
				if(!is_null($lista_centros))
				{
					
					foreach($lista_centros as $c)
					{
						$oc_n = new CentroOpcion();
						$oc_n->id_opcion = $o->id;
						$oc_n->id_centro = $c->id;
						$oc_n->flag_habilitado = 0;
						
						$oc_a = $opcBLO->RetornarCentroOpcionXIdCentroIdOpcion($c->id, $o->id);
						
						$centro_opciones = RetornarPOSTGET("centro_opcion_$c->id", null);
						
						if(!is_null($centro_opciones))
							foreach($centro_opciones as $co)
								if($o->id == $co)
									$oc_n->flag_habilitado = 1;								
								
						if(!is_null($oc_a))
						{
							$oc_n->id = $oc_a->id;
							$opcBLO->ModificarCentroOpcion($oc_n);
						}
						else
							if($oc_n->flag_habilitado == 1)
								$opcBLO->RegistrarCentroOpcion($oc_n);
									
							
					}
					
				}
			}
		}
		
		
	}
	
	?>
      	<script type="text/javascript">
			alert('Informacion Guardada!');             
        </script>
	<?php
	Redireccionar($op_original_key, $usr_key, $id_centro);   
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