<?php

session_start();

date_default_timezone_set("America/Lima");

$global_login_url = "../login.php";
$global_logout_url = "../logout.php";
$global_images_folder = "../images/";

$hora_inicial = 9;
$hora_final = 23;
$fraction_in_hour = 2;
$lap = 60 / $fraction_in_hour;

$opcion_key = "ZA7O075A";
$opcion_query_cliente = "24QZZ19Q";
$opcion_ver_detalle_reserva_cancha = "D01BO62A";
$opcion_crear_reserva_cancha = "EXT53D14";
$opcion_modificar_reserva_cancha = "EO5JZ294";
$opcion_cancelar_reserva_cancha = "YY9M6M15";
$opcion_transaccion_sin_turno = "J3FMA841";
$opcion_ingresar_ventas_otro_turno = "84I3F3HS";

$cliente_buscar_enlace_post = "../procesar_cliente.php";
$cliente_buscar_query_cliente = "../procesar_cliente.php";
$cliente_ruta_imagenes = "../images";
$cliente_tipo_fuente_externa = "reservas_cancha_cliente_buscar";


include ('../clases/enc_dec.php');
include ('../clases/usuario.php');
include ('../clases/opcion.php');
include ('../clases/general.php');
include ('../clases/security.php');
include ('../clases/reserva_cancha.php');
include ('../clases/centro.php');
include ("../clases/anuncio.php");
include ("../clases/caja.php");
include ("../clases/turno_atencion.php");
include ("../clases/cliente.php");

$id_usuario = $usuario->id;

$cenBLO = new CentroBLO();
$opcBLO = new OpcionBLO();
$caBLO = new CajaBLO();
$resBLO = new ReservaCanchaBLO();
$taBLO = new TurnoAtencionBLO();

$permiso_ver_detalle_reserva_cancha = $opcBLO -> ValidarOpcionXIdUsuario($opcion_ver_detalle_reserva_cancha, $usuario->id, $id_centro);

$permiso_crear_reserva_cancha = $opcBLO -> ValidarOpcionXIdUsuario($opcion_crear_reserva_cancha, $usuario->id, $id_centro);
$permiso_modificar_reserva_cancha = $opcBLO -> ValidarOpcionXIdUsuario($opcion_modificar_reserva_cancha, $usuario->id, $id_centro);
$permiso_cancelar_reserva_cancha = $opcBLO -> ValidarOpcionXIdUsuario($opcion_cancelar_reserva_cancha, $usuario->id, $id_centro);

$permiso_registrar_transaccion_sin_turno = $opcBLO -> ValidarOpcionXIdUsuario($opcion_transaccion_sin_turno, $usuario->id, $id_centro);
$permiso_ingresar_ventas_otro_turno = $opcBLO -> ValidarOpcionXIdUsuario($permiso_ingresar_ventas_otro_turno, $usuario->id, $id_centro);

function GetDays($sStartDate, $sEndDate) {

	$sStartDate = gmdate("Y-m-d", strtotime($sStartDate));
	$sEndDate = gmdate("Y-m-d", strtotime($sEndDate));
	$aDays[] = $sStartDate;
	$sCurrentDate = $sStartDate;

	while ($sCurrentDate < $sEndDate) {
		$sCurrentDate = gmdate("Y-m-d", strtotime("+1 day", strtotime($sCurrentDate)));
		$aDays[] = $sCurrentDate;
	}

	return $aDays;
}

function TimeIntToString($hour, $min = 0, $db_format = FALSE)
{
	if($min == 0)
		$min_str = "00";
	else
		$min_str = strlen($min) < 2 ? "0".(string)$min : (string)$min;		
	$ampm = $hour < 12 ? "AM" : "PM";
	$hour_str = $hour <= 12 ? $hour : $hour-12;
	if(!$db_format)
		return ((strlen($hour_str) < 2) ? "0".(string)$hour_str : (string)$hour_str).":$min_str $ampm";
	else
		return (strlen($hour) < 2 ? "0".(string)$hour : $hour).":".$min_str.":00";
}

if (isset($_GET["fecha"]))
	$fecha = $_GET["fecha"];
else
	$fecha = date("Y-m-d");

$fecha_mostrar = date("d/m/Y", strtotime(date('Y-m-d', strtotime($fecha))));

//************ ASIGNACION DE FECHA DE INICIO Y FECHA FIN******************

$w = date("w", strtotime(date('Y-m-d', strtotime($fecha))));
$dia_1 = date('Y-m-d', strtotime(($w == 0 ? -6 : $w - ((2*$w)-1))." days", strtotime($fecha)));
$dia_7 = date('Y-m-d', strtotime(($w == 0 ? 0 : 6 - $w + 1)." days", strtotime($fecha)));

$lista_reservas_activas = $resBLO->ListarReservaActivaXFechaIniyFechaFin(
	$id_centro, 
	$dia_1." 00:00:00", 
	$dia_7." 23:59:00"
	);
	
function BuscarReservaActiva($fecha_hora, $lista_reservas_activas)
{
	$res = NULL;
    if(!is_null($lista_reservas_activas))
    	foreach($lista_reservas_activas as $r)
    		if($r->fecha_hora_inicio <= $fecha_hora && $r->fecha_hora_fin > $fecha_hora )
    			$res = $r;
	return $res;
}

$dia_1_str = date("d/m/Y", strtotime(date('Y-m-d', strtotime($dia_1))));
$dia_7_str = date("d/m/Y", strtotime(date('Y-m-d', strtotime($dia_7))));

function NombreDia($nro_dia)
{
	switch($nro_dia)
	{
		case 0: $nombre_dia = "Domingo"; break;
		case 1: $nombre_dia = "Lunes"; break;
		case 2: $nombre_dia = "Martes"; break;
		case 3: $nombre_dia = "Miércoles"; break;
		case 4: $nombre_dia = "Jueves"; break;
		case 5: $nombre_dia = "Viernes"; break;
		case 6: $nombre_dia = "Sábado"; break;
	}
	return $nombre_dia;
}

//************************************************************************

$dias = GetDays($dia_1, $dia_7);

$enlace_procesar = "../procesar_reserva_cancha.php?opcion_key=$opcion_key&usr_key=$usr_key&id_centro=$id_centro&op_original_key=$opcion_key";
$enlace_query_turno = "../procesar_turno_atencion.php?opcion_key=$opcion_key&usr_key=$usr_key&id_centro=$id_centro&op_original_key=$opcion_key";

if($permiso_registrar_transaccion_sin_turno->isOK)
	$enlace_query_turno = "$enlace_query_turno&operacion=query_turnos_activos_usuario";
else 
	$enlace_query_turno = "$enlace_query_turno&operacion=query_turnos_activos";
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN"
"http://www.w3.org/TR/html4/strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		
		<title>RODO </title>
		<meta name="author" content="Jesus Rodriguez" />
		
      
		
		<script language="JavaScript" src="../js/jquery-1.12.3.js"></script>
		<script language="JavaScript" src="../js/jquery-ui-1.11.4.min.js"></script>
		<link rel="stylesheet" type="text/css" href="../css/jquery-ui-1.11.4.min.css" />
        <!--script language="JavaScript" src="../js/jquery.autocomplete-min.js"></script-->
        <link rel="stylesheet" type="text/css" href="../css/jquery-ui.css"/>
		<script src="../js/jquery.validate.min.js"></script>
        <script src="../js/reservas_cancha.js?q=EW34"></script>
		
		<style media="screen" type="text/css">
			body {
				background-color: #F1F1F1;
			}


			#div_main {
				width: 1250px; border: dotted 1px #0099CC; border-radius: 10px 10px 10px 10px; margin: 10px auto; overflow: hidden; background-color: #FFFFFF; font-family: Helvetica; }

			#div_reservas_titulo {}
			#reservas_titulo { font-family: Helvetica; font-size: 18px; font-weight: bold; color: #0099CC; }
			#ir_a_fecha { font-family: Helvetica; font-size: 12px; float: right; font-weight: bold; margin-right: 10px; }
			#ir_a_fecha:hover { cursor: pointer; }

			#fecha_mostrar { font-family: Helvetica; font-size: 11px; width: 60px; }

			#div_reservas_semana { color: #585858; margin-bottom: 20px; width: 1230px; border-radius: 10px 10px 10px 10px; }
			#div_tabla_reservas {
				width: 1225px; overflow-x: auto; overflow-y: auto; display: inline; float: left; border: solid 1px #3399FF; margin-left: 7px; border-radius: 8px 8px 8px 8px; margin-bottom: 10px;
				margin-top: 3px; }
				
			#tabla_reservas { border-collapse: collapse;}
			
			#tabla_reservas tbody tr:nth-child(even) { background-color: #EEE; }
			#tabla_reservas tbody tr:nth-child(odd) {  }
				
			.div_fecha_titulo { float: left; width: 159px; background-color: #333333; color: #FFFFFF; border-radius: 8px 8px 8px 8px; padding-top: 3px; padding-bottom: 3px; }
			.div_fecha_titulo .div_label { height: 16px; }
			.span_fecha_titulo_nombre_dia { font-family: Helvetica; font-size: 14px; font-weight: bold; }
			.span_fecha_titulo_fecha { font-family: Helvetica; font-size: 11px; }
		

			.div_cliente { width: 158px; border-radius: 5px 5px 5px 5px; height: 95%; display: inline-block; font-family: Helvetica; font-size: 10px; width: 158px; margin-left: 1px; }
			.div_cliente:hover { background-color: #585858; color: #FFFFFF; font-weight: bold; }
			.div_cliente p { display: table-cell; vertical-align: middle; text-align: center; }

			#aux_comentarios { resize: none; }
			.ui-dialog { border: dotted 1px #0099CC; }
			.ui-dialog-titlebar { border: dotted 1px #0099CC; }
			.ui-dialog-title { font-family: Helvetica; font-size: 14px; font-weight: bold; color: #0099CC; }

			#nombre_cliente { text-transform: uppercase; font-size: 11px; width: 240px; text-align: center;  }
			.boton_operacion {  font-size: 11px; }
			#btn_agregar_cliente { width: 22px; height; 20px; font-weight: bold; font-size: 20px; font-family: Helvetica;}			
			.etiqueta { font-size: 11px; font-family: Helvetica; font-weight: bold;  }

			.ui-widget input { font-family: Helvetica; }
			.ui-widget select { font-family: Helvetica; }

			#div_leyenda_estados { border: dotted 1px #0099CC; margin-top: 10px; padding: 10px 10px 10px 10px; width: 980px; height: 18px; border-radius: 10px 10px 10px 10px; background-color: #FFFFFF; margin: 0 auto 10px; overflow: hidden; }
			.div_reserva_estado { float: left; margin-left: 10px; border: dotted 1px #0099CC; border-radius: 5px 5px 5px 5px; background-color: #FFFFFF; padding-left: 5px; font-size: 10px; }
			.leyenda_estado { width: 40px; height: 15px; float: left; margin-left: 10px; border-radius: 5px 5px 5px 5px; }
			.etiqueta_leyenda { float: left; }
			
			.ui-datepicker table {
			    width: 100%;
			    font-size: 11px;
			    border-collapse: collapse;
			    margin: 0 0 .4em;
			}
			
			.ui-widget {
			    font-family: Helvetica;
			    font-size: 12px;
			}
			
			.div_celda_libre { width: 100%; min-height: 20px; border-radius: 5px; box-sizing: border-box; padding-top: 3px; font-size: 4px; }
			.div_celda_libre_00 { color: #FFFFFF; }
			.div_celda_libre_30 { color: #EEE; }
			.div_celda_libre_00:hover { cursor: pointer; background-color: #333333; font-weight: bold; font-size: 12px; }
			.div_celda_libre_30:hover { cursor: pointer; background-color: #333333; font-weight: bold; font-size: 12px; }
			
			.etiqueta_celda_libre { }
			
			
			.td_celda_hora { }
			.td_celda_fecha_hora {  }
			.div_span_etiqueta { }
			.div_celda_hora { box-sizing:border-box; width: 60px; height: 42px; background-color: #333333; border-radius: 5px; vertical-align: middle; text-align: center;
				padding-left: 11px; padding-top: 5px; }
				
			.div_celda_reserva { box-sizing:border-box; border-radius: 5px; height: 100%; overflow: auto; }
			.div_celda_reserva:hover { cursor: pointer; color: #FFFFFF; font-weight: bold; }
			.etiqueta_reserva { font-size: 10px; display: block; }
			
			.td_celda_hora {  }
			.div_etiqueta_hora_am_pm {  }
			.div_etiqueta_hora { float: left; }
			.div_etiqueta_am_pm { float: left; }
			.etiqueta_hora { color: #eee; font-size: 24px; font-weight: bold; font-family: Impact; }
			.etiqueta_am_pm { color: #eee; font-size: 8px; }
			
			.div_hora_min_00, .div_hora_min_30 { border-style: solid; border-width: 1px; border-radius: 2px; }
			.div_hora_min_00 { background-color: #333333; color: #FFFFFF; border-color: #333333; }
			.div_hora_min_30 { border-color: #333333; color: #333333; }			
			.etiqueta_hora_min { font-weight: bold; font-size: 13px; text-decoration: underline; }
			
			#reserva_fecha_hora_str { /*font-weight: bold;*/ }

            .fs_reserva { border: none;}
            .fs_reserva ul{ margin:5px auto; max-width: 500px; font: 12px "Helvetica", "Lucida Grande"; -webkit-padding-start: 20px; }

            .fs_reserva li { padding: 0; display: block; list-style: none; margin: 10px 0 0 0; }
            .fs_reserva label{ margin:0 0 3px 0; padding:0px; display:block; font-weight: bold; }

            .fs_reserva input[type=text],
            .fs_reserva input[type=date],
            .fs_reserva input[type=datetime],
            .fs_reserva input[type=number],
            .fs_reserva input[type=search],
            .fs_reserva input[type=time],
            .fs_reserva input[type=url],
            .fs_reserva input[type=email],
            textarea,
            select{
                box-sizing: border-box;
                -webkit-box-sizing: border-box;
                -moz-box-sizing: border-box;
                border:1px solid #BEBEBE;
                padding: 7px;
                margin:0px;
                -webkit-transition: all 0.30s ease-in-out;
                -moz-transition: all 0.30s ease-in-out;
                -ms-transition: all 0.30s ease-in-out;
                -o-transition: all 0.30s ease-in-out;
                outline: none;
            }
            .fs_reserva input[type=text]:focus,
            .fs_reserva input[type=date]:focus,
            .fs_reserva input[type=datetime]:focus,
            .fs_reserva input[type=number]:focus,
            .fs_reserva input[type=search]:focus,
            .fs_reserva input[type=time]:focus,
            .fs_reserva input[type=url]:focus,
            .fs_reserva input[type=email]:focus,
            .fs_reserva textarea:focus,
            .fs_reserva select:focus{
                -moz-box-shadow: 0 0 8px #88D5E9;
                -webkit-box-shadow: 0 0 8px #88D5E9;
                box-shadow: 0 0 8px #88D5E9;
                border: 1px solid #88D5E9;
            }
            .fs_reserva .field-divided{
                width: 49%;
            }

            .fs_reserva .field-long{
                width: 100%;
            }
            .fs_reserva .field-select{
                width: 100%;
            }
            .fs_reserva .field-textarea{
                height: 100px;
            }
            .fs_reserva input[type=submit], #fs_reserva input[type=button]{
                background: #4B99AD;
                padding: 8px 15px 8px 15px;
                border: none;
                color: #fff;
            }
            .fs_reserva input[type=submit]:hover, #fs_reserva input[type=button]:hover{
                background: #4691A4; box-shadow:none; -moz-box-shadow:none; -webkit-box-shadow:none;
            }
            .fs_reserva .required{
                color:red;
            }

            .fs_reserva textarea{
                resize: none;
            }
            
            #rn_nombre_cliente, #re_nombre_cliente { width: 285px; font-size: 12px; }
            .my-error-class {
                color:#FF0000;  /* red */
                font-weight: bold;
            }



            #tabla_reservas{ height: 705px;}

		</style>


	</head>
    <?php

function RetornarContenidoCelda($str)
{
    $aux = "";
    if($str == "div_celda_hora")
    {
        $aux = "<div class=\"div_celda_hora\" align=\"center\">";
        $aux = $aux."<div class=\"div_etiqueta_hora_am_pm\">";
        $aux = $aux."<div class=\"div_etiqueta_hora\"><span class=\"etiqueta_hora\">REP_etiqueta_hora</span></div>";
        $aux = $aux."<div class=\"div_etiqueta_am_pm\"><span class=\"etiqueta_am_pm\">REP_etiqueta_am_pm</span></div>";
        $aux = $aux."</div>";
        $aux = $aux."</div>";
        
        return $aux;
    }
    
    if($str == "div_celda_libre")
    {
        $aux = "<div class=\"div_celda_libre div_celda_libre_REP_div_celda_libre\" align=\"center\">";
        $aux = $aux."<input type=\"hidden\" class=\"fecha_hora\" value=\"REP_fecha_hora\" />";
        $aux = $aux."<input type=\"hidden\" class=\"fecha_db\" value=\"REP_fecha_db\" />";
        $aux = $aux."<input type=\"hidden\" class=\"fecha_str\" value=\"REP_str_fecha\" />";
        $aux = $aux."<input type=\"hidden\" class=\"hora_str\" value=\"REP_str_hora\" />";
        $aux = $aux."<input type=\"hidden\" class=\"hora_db\" value=\"REP_hora_db\" />";
        $aux = $aux."<span class=\"etiqueta_celda_libre\">REP_etiqueta_celda_libre</span>";
        $aux = $aux."</div>";
        
        return $aux;
    }
    
    if($str == "div_reserva")
    {
        $aux = "<div class=\"div_celda_reserva\" align=\"center\" title=\"REP_div_titulo\" style=\"background-color: REP_color_estado_reserva;\" >";
        $aux = $aux."<input type=\"hidden\" class=\"id_reserva\" value=\"REP_id_reserva\" />";
        $aux = $aux."<input type=\"hidden\" class=\"id_cliente\" value=\"REP_id_cliente\"/>";
        $aux = $aux."<input type=\"hidden\" class=\"usuario_creacion\" value=\"REP_usuario_creacion\"/>";
        $aux = $aux."<input type=\"hidden\" class=\"cliente_telefonos\" value=\"REP_cliente_telefonos\"/>";

        $aux = $aux."<input type=\"hidden\" class=\"cliente_nombres_apellidos\" value=\"REP_cliente_nombres_apellidos\" />";
        $aux = $aux."<input type=\"hidden\" class=\"fecha_hora_inicio\" value=\"REP_fecha_hora_inicio_db\" />";
        $aux = $aux."<input type=\"hidden\" class=\"fecha_hora_fin\" value=\"REP_fecha_hora_fin_db\" />";
        $aux = $aux."<input type=\"hidden\" class=\"pago_adelantado\" value=\"REP_pago_adelantado\" />";
        $aux = $aux."<input type=\"hidden\" class=\"comentarios\" value=\"REP_comentarios\" />";

        $aux = $aux."<div style=\"padding-top: REP_etiqueta_padding_top_px\">";
        $aux = $aux."<span class=\"etiqueta_reserva\" >REP_etiqueta_cliente</span></div>";
        $aux = $aux."<span class=\"etiqueta_reserva\" >REP_etiqueta_pago_adelantado</span></div>";
        $aux = $aux."</div>";
        
        return $aux;
    }

}   

    
    
    ?>
	<body>
	<?php
		include ("../header.php");
	?>
	<div id="div_main">
	    <div id = "main" align="center" >
            <input type="hidden" id="op_fecha_mostrar" value="<?php echo $fecha_mostrar;?>"/>
            <input type="hidden" id="op_hora_inicial" value="<?php echo $hora_inicial;?>"/>
            <input type="hidden" id="op_hora_final" value="<?php echo $hora_final;?>"/>
            <input type="hidden" id="op_fraction_in_hour" value="<?php echo $fraction_in_hour;?>" />
            <input type="hidden" id="op_usr_key" value="<?php echo $usr_key;?>">
            <input type="hidden" id="op_id_centro" value="<?php echo $id_centro;?>">
            <input type="hidden" id="op_opcion_key" value="<?php echo $opcion_key;?>">
            <input type="hidden" id="op_cliente_buscar_query_cliente" value="<?php echo $cliente_buscar_query_cliente;?>"/>
            <input type="hidden" id="op_fecha" value=""/>
            <input type="hidden" id="op_id_cliente"/>
            <form id="frm_reservas_semana" name="frm_reservas_semana" action="<?php echo $enlace_procesar;?>" method="POST">
                <input type="hidden" id="id_reserva_cancelar" name="id_reserva"/>
                <input type="hidden" id="operacion_cancelar" name="operacion"/>
                <div id="div_reservas_titulo" align="center">
                    <span id="reservas_titulo"><?php echo "RESERVAS DE CANCHA ENTRE: $dia_1_str y $dia_7_str"; ?></span>
                    <div id="ir_a_fecha">
                        <span ><u>Ir a fecha:</u></span>
                        <input id="fecha_mostrar" type="text" readonly="readonly"/>
                    </div>
                </div>
                <div id="div_reservas_semana" >
                    <div id="div_tabla_reservas">

                            <table id="tabla_reservas">
                                <thead>
                                    <tr>
                                        <th width=80px colspan=2></th>
                                        <?php
                                        foreach($dias as $d)
                                        {
                                            $nro_dia= date("w", strtotime( date('Y-m-d', strtotime($d)) ));
                                            $fecha_str = date("d-m-Y", strtotime( date('Y-m-d', strtotime($d)) ));
                                            $nombre_dia = NombreDia($nro_dia);
                                        ?>
                                        <th align="center">
                                            <div class="div_fecha_titulo">
                                            <div align="center" ><span class="span_fecha_titulo_nombre_dia"><?php echo $nombre_dia; ?></span></div>
                                                <div align="center" class="div_label"><span class="span_fecha_titulo_fecha"><?php echo $fecha_str; ?></span></div>
                                            </div>
                                        </th>
                                        <?php
                                        }
                                    ?>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php

                                for($i = $hora_inicial; $i<= $hora_final; $i++)
                                {?>
                                    <?php
                                    for($j = 0; $j < $fraction_in_hour; $j++)
                                    {
                                        echo "<tr>";
                                        if($j == 0)
                                        {
                                            $hora = date("h", strtotime($d." ".TimeIntToString($i, $j * $lap, TRUE)));
                                            $am_pm = date("A", strtotime($d." ".TimeIntToString($i, $j * $lap, TRUE)));
                                            $aux = RetornarContenidoCelda("div_celda_hora");
                                            $aux = str_replace("REP_etiqueta_hora",$hora, $aux);
                                            $aux = str_replace("REP_etiqueta_am_pm",$am_pm, $aux);
                                            echo "<td class=\"td_celda_hora\" rowspan=$fraction_in_hour>$aux</td>";
                                        }
                                        if($j % 2 == 0)
                                            $class_td_hora_min = "div_hora_min_00";
                                        else
                                            $class_td_hora_min = "div_hora_min_30";
                                        echo "<td><div class=\"$class_td_hora_min\" align=\"center\"><span class=\"etiqueta_hora_min\">".substr($class_td_hora_min, -2)."</span></div></td>";

                                        foreach($dias as $d)
                                        {
                                            $nro_dia= date("w", strtotime( date('Y-m-d', strtotime($d)) ));
                                            $nombre_dia = NombreDia($nro_dia);

                                            $fecha_hora = date("Y-m-d H:i:s", strtotime($d." ".TimeIntToString($i, $j * $lap, TRUE)));
                                            $fecha_db = date('Y-m-d', strtotime($d));
                                            $fecha_str = date("d/m/Y", strtotime($d));
                                            $hora_str = date("h:i A", strtotime($d." ".TimeIntToString($i, $j * $lap, TRUE)));
                                            $hora_db = date("H:i:s", strtotime($d." ".TimeIntToString($i, $j * $lap, TRUE)));
                                            $reserva = BuscarReservaActiva($fecha_hora, $lista_reservas_activas);
                                            $rowspan = 1;
                                            if(!is_null($reserva))
                                            {
                                                if($reserva->fecha_hora_inicio == $fecha_hora)
                                                {
                                                    $rowspan = (round(abs( strtotime($reserva->fecha_hora_fin) - strtotime($reserva->fecha_hora_inicio)) / 60,2)) / $lap;
                                                    $title = "Fecha: $nombre_dia $fecha_str [Hora Inicio: ";
                                                    $title = $title . date("h:i A.", strtotime( date('Y-m-d H:i:s', strtotime($reserva->fecha_hora_inicio))));
                                                    $title = $title . " - Hora Fin: ".date("h:i A.", strtotime( date("Y-m-d H:i:s", strtotime($reserva->fecha_hora_fin))));
                                                    $title = $title . "] - A Cta.: S/".number_format($reserva->pago_adelantado, 2);

                                                    $aux = RetornarContenidoCelda("div_reserva");
                                                    $aux = str_replace("REP_div_titulo", $title, $aux);
                                                    $aux = str_replace("REP_color_estado_reserva", $reserva->colorweb, $aux);
                                                    $aux = str_replace("REP_etiqueta_padding_top_", $rowspan == 1 ? 0 : round((($rowspan * 20) - 24) / 2, 0), $aux);
                                                    $aux = str_replace("REP_id_reserva",$reserva->id, $aux);
                                                    $aux = str_replace("REP_etiqueta_cliente",substr($reserva->cliente_nombres_apellidos, 0, 25), $aux);
                                                    $aux = str_replace("REP_etiqueta_pago_adelantado","[A Cta. S/.".number_format($reserva->pago_adelantado, 2)."]", $aux);

                                                    $aux = str_replace("REP_cliente_nombres_apellidos",$reserva->cliente_nombres_apellidos, $aux);
                                                    $aux = str_replace("REP_id_cliente", $reserva->id_cliente, $aux);
                                                    $aux = str_replace("REP_usuario_creacion", $reserva->usuario_creacion, $aux);
                                                    $aux = str_replace("REP_cliente_telefonos", $reserva->cliente_telefonos, $aux);
                                                    $aux = str_replace("REP_fecha_hora_inicio_db",$reserva->fecha_hora_inicio, $aux);
                                                    $aux = str_replace("REP_fecha_hora_fin_db",$reserva->fecha_hora_fin, $aux);
                                                    $aux = str_replace("REP_pago_adelantado",$reserva->pago_adelantado, $aux);
                                                    $aux = str_replace("REP_comentarios",$reserva->comentarios, $aux);

                                                    echo "<td class=\"td_celda_fecha_hora\" rowspan=$rowspan>$aux</td>";
                                                }
                                            }
                                            else // Celda Libre
                                            {
                                                $aux = RetornarContenidoCelda("div_celda_libre");
                                                $aux = str_replace("REP_fecha_hora", $fecha_hora, $aux);
                                                $aux = str_replace("REP_str_hora", $hora_str, $aux);
                                                $aux = str_replace("REP_fecha_db", $fecha_db, $aux);
                                                $aux = str_replace("REP_str_fecha", $nombre_dia." ".$fecha_str, $aux);
                                                $aux = str_replace("REP_hora_db", $hora_db, $aux);
                                                $aux = str_replace("REP_div_celda_libre", substr($class_td_hora_min, -2), $aux);
                                                $aux = str_replace("REP_etiqueta_celda_libre", $hora_str, $aux);
                                                echo "<td class=\"td_celda_fecha_hora\">".$aux."</td>";
                                            }

                                        }
                                        echo "</tr>";
                                    }
                                }
                                ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
            </form>
        
        </div>  
        <div id="div_leyenda_estados">
        <?php
        $lista_estados = $resBLO->ListarEstadoTodos();
        if(!is_null($lista_estados))
        {
            foreach($lista_estados as $e)
            {?>
                <div class="div_reserva_estado">
                    <div class="etiqueta_leyenda"><span><?php echo $e -> descripcion; ?></span></div>
                    <div class="leyenda_estado" style="background-color: <?php echo $e -> colorweb; ?>"></div>
                </div>
            <?php
            }
        }
        ?>
        </div>
	</div>
	
    <div id="div_reserva_nueva">
        <form action="<?php echo $enlace_procesar;?>" method="POST" id="frm_reserva_nueva" class="frm_reserva">

            <input type="hidden" id="rn_operacion" name="operacion" value=""/>
            <input type="hidden" id="rn_id_usuario" name="id_usuario" value="<?php echo $usuario->id;?>"/>
            <input type="hidden" id="rn_fecha_hora_fin" name="fecha_hora_fin" />
            <fieldset id="fs_reserva_nueva" class="fs_reserva">
                <ul >
                    <li>
                        <label for="rn_fecha_str">Hora Inicio</label>
                        <span id="rn_fecha_hora_str"></span>
                        <input id="rn_fecha" type="hidden" />
                        <input id="rn_fecha_hora_inicio" name="fecha_hora_inicio" type="hidden"/>
                    </li>               
                    <li>
                        <label for="rn_hora_fin">Fin</label>
                        <select id="rn_hora_fin" name="rn_hora_fin">
                            <option value="0">Seleccione...</option>
                            <?php
                            for($i = $hora_inicial; $i <= $hora_final; $i++)
                            {
                                for($j = 0; $j < $fraction_in_hour; $j++)
                                {
                                    $min = $j==0 ? "00" : "30";
                                    $valor = TimeIntToString($i, $j * $lap, TRUE);
                                    $etiqueta = TimeIntToString($i, $j * $lap, FALSE);
                                    echo "<option value=\"$valor\">$etiqueta</option>";
                                }
                            }
                            echo "<option value=\"23:59:59\">11:59 PM.</option>";
                            ?>
                        </select>
                    </li>
                    <li>
                        <label for="rn_nombre_cliente">Cliente</label>
                        <input type="text" id="rn_nombre_cliente" name="rn_nombre_cliente"/>
                        <input type="hidden" id="rn_id_cliente" name="id_cliente"/>
                        <input type="hidden" id="rn_nombre_cliente_aux"/>
                    </li>
                    <li>
                        <label for="rn_pago_adelantado">Pago Adelantado (S/.)</label>
                        <input type="number" id="rn_pago_adelantado" name="pago_adelantado_mn" value="0.00" />
                    </li>
                    <li>
                        <label for="rn_comentarios">Comentarios</label>
                        <textarea id="rn_comentarios" cols=60 rows=3 maxlength="100" name="comentarios"></textarea>
                    </li>
                            
                </ul>
                        
                        
            </fieldset>
        </form>
    </div>

    <div id="div_reserva_existente">
        <form action="<?php echo $enlace_procesar;?>" method="POST" id="frm_reserva_existente">
            <input type="hidden" id="re_operacion" name="operacion" value=""/>
            <input type="hidden" id="re_id_usuario" name="id_usuario" value="<?php echo $usuario->id;?>"/>
            <input type="hidden" id="re_id_reserva" name="id_reserva" />
            <fieldset id="fs_reserva_existente" class="fs_reserva">
                <ul>
                    <li>
                        <label for="re_fecha_hora_inicio">Hora Inicio</label>
                        <select id="re_fecha_hora_inicio" name="fecha_hora_inicio" class="hora_seleccion">

                        </select>
                    </li>
                    <li>
                        <label for="re_fecha_hora_fin">Hora Fin</label>
                        <select id="re_fecha_hora_fin" name="fecha_hora_fin" class="hora_seleccion">

                        </select>
                    </li>
                    <li>
                        <label for="re_nombre_cliente">Cliente (Teléfonos)</label>
                        <input type="text" id="re_nombre_cliente" name="re_nombre_cliente"/>
                        <input type="hidden" id="re_id_cliente" name="id_cliente"/>
                        <input type="hidden" id="re_nombre_cliente_aux"/>
                        <input type="text" id="re_cliente_telefonos"/>
                    </li>
                    <li>
                        <label for="re_usuario_creacion">Reserva Creada Por</label>
                        <input type="text" id="re_usuario_creacion"/>
                    </li>
                    <li>
                        <label for="re_pago_adelantado">Pago Adelantado (S/.)</label>
                        <input type="number" id="re_pago_adelantado" name="pago_adelantado_mn" value="0.00" disabled="disabled"/>
                    </li>
                    <li>
                        <label for="re_comentarios">Comentarios</label>
                        <textarea id="re_comentarios" cols=60 rows=3 maxlength="100" name="comentarios"></textarea>
                    </li>
                    <li>
                        <label for="re_reserva_operacion">Operación</label>
                        <select id="re_reserva_operacion" name="operacion">
                            <option value="-">Seleccione...</option>
                            <option value="modificar">Modificar Reserva</option>
                            <option value="cancelar">Cancelar Reserva</option>
                            <!--option value="completar">Completar Reserva con Pago</option-->
                            <!--option value="anular">Reserva No Completada (Sin Pago)</option-->
                        </select>
                    </li>
                </ul>
            </fieldset>


        </form>
    </div>
	
	
	<?php		
		include ('../clientes/crear_simple.php');
	?>
	</body>
</html>
	
