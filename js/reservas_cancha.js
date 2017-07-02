/**
 * Created by wjesusaxl on 6/24/17.
 */
function Redireccionar($opcion_key)
{
    var $usr_key = document.getElementById("op_usr_key").value;
    var $id_centro = document.getElementById("op_id_centro").value;
    location.href = "../redirect.php?opcion_key=" + $opcion_key + "&usr_key=" + $usr_key + "&id_centro=" + $id_centro;
}

function Left(str, n)
{
    if (n <= 0)
        return "";
    else if (n > String(str).length)
        return str;
    else
        return String(str).substring(0,n);
}

function Right(str, n)
{
    if (n <= 0)
        return "";
    else
    if (n > String(str).length)
        return str;
    else
    {
        var iLen = String(str).length;
        return String(str).substring(iLen, iLen - n);
    }
}

function TransformarHoraAMPM(nro)
{
    var min = Right(nro.toString(), 2);
    var hora = Left(nro.toString(), 2 - (4 - nro.toString().length));
    var ampm;

    if(hora >= 12 && hora != 24)
    {
        hora = hora - 12;
        ampm = "PM.";
    }
    else
        ampm = "AM.";

    if(hora == 0)
        hora = 12;

    if(hora == 24)
    {
        hora = 12;
        ampm = "AM.";
    }

    hora_valor = "" + hora;
    var pad = "00";
    hora_valor = pad.substring(0, pad.length - hora_valor.length) + hora_valor;
    hora_valor = hora_valor + ":" + min + " " + ampm;
    return hora_valor;
}

function TransformarHoraANro(hora_texto)
{
    var hora = hora_texto.substring(0, 2);
    var min = hora_texto.substring(3, 5);

    if(hora.substring(0,1) == "0")
        hora = hora.substring(1,2);

    return hora + "" + min;
}

function TransformarHoraADB(nro)
{
    var min_str = Right(nro.toString(), 2);
    var hora = parseInt(Left(nro.toString(), 2 - (4 - nro.toString().length)));

    var hora_str;

    if(hora < 10)
        hora_str = "0" + hora.toString();
    else
        hora_str = hora.toString();

    hora_str = hora_str + ":" + ((min_str == "00") ? "00" : min_str) + ":00";

    return hora_str;
}

function DiferenciaEnFormato($tiempo_ini, $tiempo_fin)
{
    var $tiempo_ini_nro = TransformarHoraANro($tiempo_ini);
    var $tiempo_fin_nro = TransformarHoraANro($tiempo_fin);
    var $diferencia_hora = "";

    $diferencia = ($tiempo_fin_nro - $tiempo_ini_nro).toString();

    if(Right($diferencia, 2) == "70")
        $diferencia = $diferencia.replace("70", "30");

    switch($diferencia.length){
        case 2:
            $diferencia_hora = $diferencia + " min(s)"; break;
        case 3:
            $diferencia_hora = $diferencia.substring(0, 1) + " hora(s)";
            if($diferencia.substring(1, 3) != "00")
                $diferencia_hora += " " + $diferencia.substring(1, 3) + " min(s)";
            break;
        case 4:
            $diferencia_hora = $diferencia.substring(0, 2) + " hora(s)";
            if($diferencia.substring(2, 4) != "00")
                $diferencia_hora += " " + $diferencia.substring(2, 4) + " min(s)";
            break;
    }
    //$diferencia_hora = $diferencia;
    return $diferencia_hora;


}


$(function()
{
    jQuery.validator.addMethod(
        "resIdCliente",
        function(value, element){
            $rn_id_cliente = $("#rn_id_cliente").val();
            if($rn_id_cliente != "0")
                resp = true;
            else
                resp = false;
            return resp;
        },
        "Seleccione Cliente"
    );

    jQuery.validator.addMethod(
        "resHoraFin",
        function(value, element){
            $rn_hora_fin = $("#rn_hora_fin").val();
            if($rn_hora_fin != "0")
                resp = true;
            else
                resp = false;
            return resp;
        },
        "Seleccione Hora"
    );

    jQuery.validator.addMethod(
        "resExIdCliente",
        function(value, element){
            $re_id_cliente = $("#re_id_cliente").val();
            if($re_id_cliente != "0")
                resp = true;
            else
                resp = false;
            return resp;
        },
        "Seleccione Cliente"
    );

    jQuery.validator.addMethod(
        "resExHoraFin",
        function(value, element){
            $re_hora_fin = $("#re_fecha_hora_fin").val();
            if($re_hora_fin != "0")
                resp = true;
            else
                resp = false;
            return resp;
        },
        "Selecciona Hora"
    );

    jQuery.validator.addMethod(
        "resExIdOperacion",
        function(value, element){
            $re_operacion = $("#re_operacion").val();
            if($re_operacion != "-")
                resp = true;
            else
                resp = false;
            return resp;
        },
        "Selecciona una Operación"
    );

    $op_fecha_mostrar = $("#op_fecha_mostrar").val();
    $('#fecha_mostrar').datepicker({dateFormat: 'dd/mm/yy', altField: '#op_fecha', altFormat: 'yy-mm-dd'});
    $("#fecha_mostrar").datepicker("setDate",$op_fecha_mostrar);


    $("#fecha_mostrar").change(function()
    {
        var $fecha = $("#op_fecha").val();
        var $usr_key = $("#op_usr_key").val();
        var $id_centro = $("#op_id_centro").val();
        var $opcion_key = $("#op_opcion_key").val();

        location.href = "reservar.php?usr_key=" + $usr_key + "&id_centro=" + $id_centro + "&opcion_key=" + $opcion_key + "&fecha="+$fecha;
    });

    //$url_query_cliente = "<?php echo $cliente_buscar_query_cliente;?>" + "?operacion=query2&nombres=";
    $url_query_cliente = $("#op_cliente_buscar_query_cliente").val() + "?operacion=query2&nombres=";


    $('#rn_nombre_cliente').autocomplete({
        source: function (request, response) {
            $.getJSON($url_query_cliente + request.term, function (data) {
                response($.map(data, function (item) {
                    return {
                        value: item.id,
                        label: item.nombres+ ' '+item.apellidos+'['+item.tipo_documento.substring(0,3)+':'+item.nro_documento+']',
                        nombres: item.nombres,
                        apellidos: item.apellidos,
                    };
                }));
            });
        },
        select: function(event, ui) {
            event.preventDefault();
            $("#rn_id_cliente").val(ui.item.value);
            $("#rn_nombre_cliente").val(ui.item.nombres+" "+ui.item.apellidos);
            $("#rn_nombre_cliente_aux").val(ui.item.nombres+" "+ui.item.apellidos);
        },
        change: function(event, ui) {
            if($("#rn_nombre_cliente").val() != $("#rn_nombre_cliente_aux").val())
                $("#rn_id_cliente").val(0);
        },
        minLength: 3
    });

    $('#rn_nombre_cliente').autocomplete("option","appendTo","#div_reserva_nueva");

    $('#div_reserva_nueva').dialog({
        autoOpen: false,
        height: 460,
        width: 470,
        modal: true,
        resizable: false,
        title: "Detalle de la Reserva",
        buttons: {
            'Crear Reserva': function() {
                var $id_cliente = $("#rn_id_cliente").val();
                var $cliente_nombre = $("#rn_nombre_cliente").val();
                if ($('#frm_reserva_nueva').valid()) {

                    if($id_cliente > 0)
                    {
                        $("#rn_operacion").val("crear");
                        $fecha_hora_fin = $("#rn_fecha").val() + " " + $("#rn_hora_fin").val();
                        $("#rn_fecha_hora_fin").val($fecha_hora_fin);
                        $("#frm_reserva_nueva").submit();
                    }
                    else
                    {
                        if($cliente_nombre != "")
                        {
                            if(confirm("Cliente " + $cliente_nombre + " No Encontrado. Desea Crearlo?"))
                            {

                                $("#cliente_id_tipo_documento").val(1);
                                $("#cliente_nro_documento").val("");
                                $cliente_nombre_arr = $cliente_nombre.split(" ");
                                $("#cliente_nombres").val($cliente_nombre_arr[0]);
                                $("#cliente_telefono1").val("");
                                $("#cliente_telefono2").val("");
                                if($cliente_nombre_arr.length > 1)
                                    $("#cliente_apellidos").val($cliente_nombre_arr[1]);

                                $("#div_cliente_nuevo").dialog("open");

                            }
                        }
                        else
                            alert("Ingrese Valor Valido de Cliente");
                    }
                }

            }
        }
    });


    $("#div_reserva_existente").dialog({
        autoOpen: false,
        height: 'auto',
        width: 'auto',
        modal: true,
        resizable: false,
        title: "Detalle de la Reserva",
        buttons: {
            'Guardar Cambios': function() {
                var $cliente_nombre = $("#re_nombre_cliente").val();
                var $fecha_hora_fin = $("#re_fecha_hora_fin").val();
                if ($('#frm_reserva_existente').valid()) {
                    console.log("Entrando");
                    var $id_cliente = $("#re_id_cliente").val();

                    if($id_cliente > 0)
                    {
                        $("#re_operacion").val("modificar");
                        $("#frm_reserva_existente").submit();
                    }
                    else
                    {
                        if($cliente_nombre != "")
                        {
                            if(confirm("Cliente " + $cliente_nombre + " No Encontrado. Deseas Crearlo?"))
                            {

                                $("#cliente_id_tipo_documento").val(1);
                                $("#cliente_nro_documento").val("");
                                $cliente_nombre_arr = $cliente_nombre.split(" ");
                                $("#cliente_nombres").val($cliente_nombre_arr[0]);
                                $("#cliente_telefono1").val("");
                                $("#cliente_telefono2").val("");
                                if($cliente_nombre_arr.length > 1)
                                    $("#cliente_apellidos").val($cliente_nombre_arr[1]);

                                $("#div_cliente_nuevo").dialog("open");

                            }

                        }
                        else
                            alert("Ingrese Valor Valido de Cliente");
                    }
                }

            }
        }
    });

    $(".div_celda_reserva").click(function(){
        var $reserva_fecha_hora_inicio = $(this).find(".fecha_hora_inicio").val();
        var $reserva_fecha_hora_fin = $(this).find(".fecha_hora_fin").val();
        var $reserva_cliente_nombres_apellidos = $(this).find(".cliente_nombres_apellidos").val();
        var $reserva_id_cliente = $(this).find(".id_cliente").val();
        var $reserva_id_reserva = $(this).find(".id_reserva").val();
        var $reserva_usuario_creacion = $(this).find(".usuario_creacion").val();
        var $reserva_cliente_telefonos = $(this).find(".cliente_telefonos").val();
        var $reserva_pago_adelantado = $(this).find(".pago_adelantado").val();
        var $reserva_comentarios = $(this).find(".comentarios").val();

        var $reserva_fecha = $reserva_fecha_hora_inicio.substr(0,10);
        var $reserva_hora_inicio = $reserva_fecha_hora_inicio.substr(11,19);
        var $reserva_hora_fin = $reserva_fecha_hora_fin.substr(11,19);

        var $hora_inicial = parseInt($("#op_hora_inicial").val());
        var $hora_final = parseInt($("#op_hora_final").val());
        var $fraction_in_hour = $("#op_fraction_in_hour").val();
        var $min, i, j, $valor, $nro, $etiqueta_inicio, $etiqueta_fin, $option_inicio, $option_fin,
            $selected_inicio, $selected_fin, $valor_full;

        $("#re_fecha_hora_inicio").empty();//.append("<option value='0'>Selecciona...</option>");
        $("#re_fecha_hora_fin").empty();
        $("#re_operacion").val("-");

        var $reserva_hora_fin_nro = TransformarHoraANro($reserva_hora_fin);

        for(i = $hora_inicial; i <= $hora_final; i++)
        {
            for(j = 0; j < $fraction_in_hour; j++)
            {
                $min = (j == 0) ? "00" : "30";
                $nro = parseInt(i.toString() + $min);
                $valor = TransformarHoraADB($nro);
                $selected_inicio = ($reserva_hora_inicio == $valor) ? "selected=selected" : "";
                $valor_full = $reserva_fecha + " " + $valor;

                $etiqueta_inicio = TransformarHoraAMPM($nro);
                $option_inicio = "<option value='" + $valor_full + "' "+ $selected_inicio + ">" + $etiqueta_inicio + "</option>";

                $("#re_fecha_hora_inicio").append($option_inicio);
                if($nro >= $reserva_hora_fin_nro)
                {
                    $etiqueta_fin = TransformarHoraAMPM($nro) + " [" + DiferenciaEnFormato($reserva_hora_inicio, $valor) + "]";
                    $selected_fin = ($reserva_hora_fin == $valor) ? "selected=selected" : "";
                    $option_fin = "<option value='" + $valor_full + "' "+ $selected_fin + ">" + $etiqueta_fin + "</option>";
                    $("#re_fecha_hora_fin").append($option_fin);
                }
            }
        }

        $option_fin = $reserva_fecha + " 23:59:59";

        $("#re_fecha_hora_fin").append("<option value='" + $option_fin +"'>11:59 PM. [" + DiferenciaEnFormato($reserva_hora_inicio, "23:59:59") + "]</option>");
        $("#re_fecha_hora_inicio").prop("disabled","disabled");
        $("#re_fecha_hora_fin").prop("disabled","disabled");

        $("#re_nombre_cliente").val($reserva_cliente_nombres_apellidos);
        $("#re_nombre_cliente").prop("disabled", "disabled");
        $("#re_id_cliente").val($reserva_id_cliente);
        $("#re_id_reserva").val($reserva_id_reserva);
        $("#re_cliente_telefonos").val($reserva_cliente_telefonos);
        $("#re_cliente_telefonos").prop("disabled", "disabled");

        $("#re_usuario_creacion").val($reserva_usuario_creacion);
        $("#re_usuario_creacion").prop("disabled", "disabled");

        $("#re_pago_adelantado").val(parseFloat($reserva_pago_adelantado).toFixed(2));
        $("#re_pago_adelantado").prop("disabled", "disabled");

        $("#re_comentarios").val($reserva_comentarios);
        $("#re_comentarios").prop("disabled", "disabled");

        $("#div_reserva_existente").dialog("open");
    });

    $("#re_fecha_hora_inicio").change(function(){
        var $reserva_fecha_hora_inicio = $("#re_fecha_hora_inicio").val();
        var $hora_inicio = $reserva_fecha_hora_inicio.substr(11,19);
        var $reserva_fecha = $reserva_fecha_hora_inicio.substr(0,10);

        var $hora_inicial = parseInt($("#op_hora_inicial").val());
        var $hora_final = parseInt($("#op_hora_final").val());
        var $fraction_in_hour = $("#op_fraction_in_hour").val();

        var $min, i, j, $valor, $nro, $etiqueta, $option, $valor_full;

        $("#re_fecha_hora_fin").empty();

        var $reserva_hora_inicio_nro = TransformarHoraANro($hora_inicio);

        for(i = $hora_inicial; i <= $hora_final; i++)
        {
            for(j = 0; j < $fraction_in_hour; j++)
            {
                $min = (j == 0) ? "00" : "30";
                $nro = parseInt(i.toString() + $min);
                $valor = TransformarHoraADB($nro);
                $valor_full = $reserva_fecha + " " + $valor;

                $etiqueta = TransformarHoraAMPM($nro) + " [" + DiferenciaEnFormato($hora_inicio, $valor) + "]";
                $option = "<option value='" + $valor_full + "'>" + $etiqueta + "</option>";

                if($nro > $reserva_hora_inicio_nro)
                    $("#re_fecha_hora_fin").append($option);

            }
        }

        $option_fin = $reserva_fecha + " 23:59:59";

        $("#re_fecha_hora_fin").append("<option value='" + $option_fin +"'>11:59 PM. [" + DiferenciaEnFormato($hora_inicio, "23:59:59") + "]</option>");



    });

    $("#re_reserva_operacion").change(function(){
       var $operacion = $(this).val();

       switch($operacion)
       {
           case "modificar":
               $("#re_fecha_hora_inicio").prop("disabled", false);
               $("#re_fecha_hora_fin").prop("disabled", false);
               $("#re_nombre_cliente").prop("disabled", false);
               $("#re_pago_adelantado").prop("disabled", false);
               $("#re_comentarios").prop('disabled', false);
               $("#re_operacion").val('modificar');
               break;
           case "cancelar":
               $("#re_operacion").val("cancelar");
               if(confirm("Seguro que deseas cancelar esta reserva?"))
                   $("#frm_reserva_existente").submit();
               break;
       }
    });

    $('#re_nombre_cliente').autocomplete({
        source: function (request, response) {
            $.getJSON($url_query_cliente + request.term, function (data) {
                response($.map(data, function (item) {
                    return {
                        value: item.id,
                        label: item.nombres+ ' '+item.apellidos+'['+item.tipo_documento.substring(0,3)+':'+item.nro_documento+']',
                        nombres: item.nombres,
                        apellidos: item.apellidos,
                    };
                }));
            });
        },
        select: function(event, ui) {
            event.preventDefault();
            $("#re_id_cliente").val(ui.item.value);
            $("#re_nombre_cliente").val(ui.item.nombres+" "+ui.item.apellidos);
            $("#re_nombre_cliente_aux").val(ui.item.nombres+" "+ui.item.apellidos);
        },
        change: function(event, ui) {
            if($("#re_nombre_cliente").val() != $("#re_nombre_cliente_aux").val())
                $("#re_id_cliente").val(0);
            $("#re_cliente_telefonos").val("");
        },
        minLength: 3
    });

    $('#re_nombre_cliente').autocomplete("option","appendTo","#div_reserva_existente");



    $('#frm_reserva_nueva').validate({
        rules: {
            rn_hora_fin: { resHoraFin: true }/*,
            rn_nombre_cliente: { resIdCliente: true }*/
        },
        errorClass: "my-error-class"
    });

    $("#frm_reserva_existente").validate({
        rules: {
            operacion : { resExIdOperacion: true }
        },
        errorClass: "my-error-class"
    });

    $(".div_celda_libre").click(function()
    {
        $("#rn_fecha_hora_str").empty();
        $("#rn_hora_fin").val("0");
        $("#rn_id_cliente").val("0");
        $("#rn_pago_adelantado").val("0.00");
        $("#rn_nombre_cliente").val("");
        $("#rn_nombre_cliente_aux").val("");
        $("#rn_comentarios").val("");

        $fecha_hora_str = $(this).find(".fecha_str").val();
        $fecha_hora = $(this).find(".fecha_hora").val();
        $fecha_hora_str += " - " + $(this).find(".hora_str").val();
        $fecha_db = $(this).find(".fecha_db").val();
        $hora_inicio = $(this).find(".hora_db").val();

        $hora_inicio_nro = parseInt($hora_inicio.substr(0,5).replace(":",""));

        $("#rn_fecha_hora_str").append($fecha_hora_str);
        $("#rn_fecha").val($fecha_db);
        $("#rn_fecha_hora_inicio").val($fecha_hora);

        $("#rn_hora_fin option").show();

        $("#rn_hora_fin option").each(function()
        {
            $hora_fin = $(this).val();
            $hora_fin_nro = TransformarHoraANro($hora_fin);
            $hora_fin_str_am_pm = TransformarHoraAMPM($hora_fin_nro);


            if(($hora_fin_nro != 0 && $hora_fin_nro <= $hora_inicio_nro))
                $(this).hide();
            else
            {
                if($hora_fin_nro > 0)
                {
                    $opcion = $hora_fin_str_am_pm;
                    $diferencia_hora = DiferenciaEnFormato($hora_inicio, $hora_fin);
                    if($(this).val() != "0")
                        $(this).html($opcion + " [" + $diferencia_hora + "]");
                }
            }

        });

        $('#div_reserva_nueva').dialog('open');

    });

    /*$(".div_celda_reserva").click(function(){
        $id_reserva = $(this).find(".id_reserva").val();
        $("#operacion_cancelar").val("cancelar");
        $("#id_reserva_cancelar").val($id_reserva);
        $title = $(this).attr("title");
        if(confirm("Deseas eliminar la reserva con " + $title +"?"))
            $("#frm_reservas_semana").submit();

    });*/



});


