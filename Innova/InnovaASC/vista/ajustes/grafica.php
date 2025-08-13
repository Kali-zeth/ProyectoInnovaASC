    <?php
    date_default_timezone_set("America/Bogota");
    $contadorTotal = 0;
    $ventasTotales = 0;
    $Meses = array(
        'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
        'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
    );
    $tipoFactura = (isset($_GET['fc'])) ? $_GET['fc'] : 0;
    // RECIBIR FECHAS PERSONALIZADAS//////////////////////////////////////////////////
    if (isset($_GET['f'])) {
        switch ($_GET['f']) {
            case "Y":
                $tipo = 0;
                $year = (isset($_GET['year'])) ? $_GET['year'] : date("Y");
                $data = mostrarDatosFactura($tipoFactura, 8, $year, "");
                break;
            case "p":
                $tipo = 1;
                if (isset($_GET['fch1'])) {
                    $data = mostrarDatosFactura($tipoFactura, 1, $_GET['fch1'], $_GET['fch2']);
                } else {
                    $data = mostrarDatosFactura($tipoFactura, 0, "", "");
                }
                break;
            case "W":
                $tipo = 2;
                $year = date('Y-m');
                $data = mostrarDatosFactura($tipoFactura, 2, $year, "");
                break;
            case "Y-m":
                $tipo = 4;
                $year = (isset($_GET['month'])) ? date("Y") . "-" . $_GET['month'] : date("Y-m");
                $data = mostrarDatosFactura($tipoFactura, 2, $year, "");
                break;
            default:
                $tipo = 1;
                $year = date($_GET['f']);
                $data = mostrarDatosFactura($tipoFactura, 2, $year, "");
                break;
        }
    } else {
        $tipo = 2;
        $year = date('Y-m');
        $data = mostrarDatosFactura($tipoFactura, 2, $year, "");
    }
    //LLENAR LAS ARRAYS DE FECHAS///////////////////////////////////////////////
    if ($data) {
        $count = count($data);
        switch ($tipo) {
            case 0:
                for ($i = 0; $i < $count; $i++) {
                    $fecha = $data[$i]['Fecha'];
                    $seleccionarFecha = explode('-', trim($fecha));
                    $mes[] = $seleccionarFecha[0] . "-" . $seleccionarFecha[1];
                }
                for ($i = 0; $i < $count; $i++) {
                    if (empty($mes[$i + 1])) {
                        $fech[] = $mes[$i];
                        $contadorTotal++;
                        break;
                    } else {
                        if ($mes[$i] != $mes[$i + 1]) {
                            $fech[] = $mes[$i];
                            $contadorTotal++;
                        }
                    }
                }
                break;
            case 1:
                for ($i = 0; $i < $count; $i++) {
                    $mes[] = $data[$i]['Fecha'];
                }
                for ($i = 0; $i < $count; $i++) {
                    if (empty($mes[$i + 1])) {
                        $fech[] = $mes[$i];
                        $contadorTotal++;
                        break;
                    } else {
                        if ($mes[$i] != $mes[$i + 1]) {
                            $fech[] = $mes[$i];
                            $contadorTotal++;
                        }
                    }
                }
                break;
            case 2:
                if (isset($_GET['week'])) {
                    $week = $_GET["week"];
                } else {
                    $week = date("W");
                }
                $dias = array("Domingo", "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado");
                for ($i = 0; $i < $count; $i++) {
                    if (date("W", strtotime($data[$i]['Fecha'])) == $week) {
                        $mes[] = $data[$i]['Fecha'];
                    }
                    $nroFac = $data[$i]["Nro_factura"];
                    $dataDetalles = mostrarDatosFactura($tipoFactura, 5, $nroFac, "");
                    $product[] = $dataDetalles[0]['Producto'];
                }
                for ($i = 0; $i < $count; $i++) {
                    if (empty($mes[$i + 1])) {
                        if ($i != 0) {
                            $fech[] = $mes[$i];
                            $producto[] = $product[$i];
                            $contadorTotal++;
                        }
                        break;
                    } else {
                        if ($mes[$i] != $mes[$i + 1]) {
                            $fech[] = $mes[$i];
                            $contadorTotal++;
                        }
                        if ($product[$i] != $product[$i + 1]) {
                            $producto[] = $product[$i];
                        }
                    }
                }
                break;
            case 3:
                for ($i = 0; $i < $count; $i++) {
                    $mes[] = $data[$i]['Fecha'];
                }
                for ($i = 0; $i < $count; $i++) {
                    if (empty($mes[$i + 1])) {
                        $fech[] = $mes[$i];
                        $contadorTotal++;
                        break;
                    } else {
                        if ($mes[$i] != $mes[$i + 1]) {
                            $fech[] = $mes[$i];
                            $contadorTotal++;
                        }
                    }
                }
                break;
            case 4:
                $dias = array("Semana1", "Semana2", "Semana3", "Semana4");
                for ($i = 0; $i < $count; $i++) {
                    $mes[] = $data[$i]['Fecha'];
                }
                $mesEnNumero = date("n", strtotime($data[0]['Fecha']));
                $primeraSemana = ($mesEnNumero * 4) - 3;
                for ($i = 0; $i < count($mes); $i++) {
                    if (empty($mes[$i + 1])) {
                        $fech[] = $mes[$i];
                        break;
                    } else {
                        if ($mes[$i] != $mes[$i + 1]) {
                            $fech[] = $mes[$i];
                        }
                    }
                }
                for ($i = 0; $i < 4; $i++) {
                    for ($j = 0; $j < count($fech); $j++) {
                        if (date("W", strtotime($fech[$j])) == ($primeraSemana + $i)) {
                            $totalsemanas[$i][] = $fech[$j];
                        }
                    }
                }
        }
        // LLENAR LAS ARRAYS DE PRECIOS/////////////////////////////////////////
        switch ($tipo) {
            case 2:
                $vent = array(0, 0, 0, 0, 0, 0, 0);
                if ($contadorTotal != 0) {
                    for ($i = 0; $i < $contadorTotal; $i++) {
                        $data = mostrarDatosFactura($tipoFactura, 3, $fech[$i], "");
                        $day = date("w", strtotime($data[0]['Fecha']));
                        $count = count($data);
                        for ($j = 0; $j < $count; $j++) {
                            $vent[$day] = $vent[$day] + $data[$j]['Total'];
                        }
                    }
                }
                break;
            case 4:
                $vent = array(0, 0, 0, 0);
                for ($i = 0; $i <= count($totalsemanas); $i++) {
                    $contadorTotal++;
                    if (isset($totalsemanas[$i])) {
                        for ($j = 0; $j < count($totalsemanas[$i]); $j++) {
                            $dataVenta = mostrarDatosFactura($tipoFactura, 3, $totalsemanas[$i][$j], "");
                            $countVentas = count($dataVenta);
                            for ($k = 0; $k < $countVentas; $k++) {
                                $ventasTotales = $ventasTotales + $dataVenta[$k]["Total"];
                            }
                        }
                        $vent[$i] = $ventasTotales;
                    } else {
                        $vent[$i] = 0;
                    }
                }
                break;
            default:
                for ($i = 0; $i < $contadorTotal; $i++) {
                    $vent[$i] = 0;
                    if (empty($_GET['f'])) {
                        $data = mostrarDatosFactura($tipoFactura, 2, $fech[$i], "");
                    } else {
                        $dias[] = date("l", strtotime($fech[$i]));
                        if ($_GET['f'] != "Y" & $_GET['f'] != 0) {
                            $data = mostrarDatosFactura($tipoFactura, 3, $fech[$i], "");
                        } else {
                            $data = mostrarDatosFactura($tipoFactura, 2, $fech[$i], "");
                        }
                    }
                    $count = count($data);
                    for ($j = 0; $j < $count; $j++) {
                        $vent[$i] = $vent[$i] + $data[$j]['Total'];
                    }
                }
                break;
        }
    } else {
        $tipo = 1;
        $vent = array(0);
        $fech = array(0);
    }
    if ($p == 1) {
    ?>
        <div class="contenido" style="max-height:13vh;min-height:11vh;animation:drop 0.4s">
            <select class="select_grafica" onchange="factura()" id="factura" style="position: fixed;right:0;margin-right:5vh;margin-top:-5vh;text-align:center">
                <option value="0">Salida</option>
                <option value="1">Entrada</option>
            </select>
            <div class="box" style="min-height:6vh;max-height:9vh;width:91.6%;margin-bottom:5px;padding:10px;border-radius:5px;">
                <a style="float:left;padding: 2vh 0.5vh;margin-right:-2vh" href="?"><i class="bi bi-chevron-left"></i></a>
                <label for="diagrama">Tipo de grafica: </label>
                <select class="rol" style="width: 90px;text-align:center;margin-right:30px" id="diagrama" onchange="grafica()">
                    <option value="bar">Barras</option>
                    <option value="line">Lineal</option>
                </select>
                <label for="fecha">Tiempo: </label>
                <select class="rol" style="min-width: 90px;max-width:150px;text-align:center;margin-right:20px" id="fecha" onchange="fecha()">
                    <option value="Y">Año</option>
                    <option value="W">Semana</option>
                    <option value="Y-m">Mes</option>
                    <option value="p">Personalizado</option>
                </select>
                <?php
                if (isset($_GET['f'])) {
                    if ($_GET['f'] == "Y") {
                        echo '
                        <label for="fecha">Año: </label>
                        <select onchange="elegirYear()" class="rol" style="min-width: 100px;max-width:150px;text-align:center;margin-right:20px" id="NoYear">
                        ';
                        for ($i = 2021; $i <= date("Y"); $i++) {
                            echo '<option value="' . $i . '">' . $i . '</option>';
                        }
                        echo '
                        </select>
                        <script>
                        $("#NoYear > option[value=' . $year . ']").attr("selected", true);
                        </script>
                        ';
                    }
                    if ($_GET['f'] == "W") {
                        $semana = (isset($_GET['week'])) ? $_GET['week'] : date("W");
                        echo '
                        <label for="fecha">Semana No: </label>
                        <select onchange="elegirSemana()" class="rol" style="min-width: 100px;max-width:150px;text-align:center;margin-right:20px" id="NoSemana">
                        ';
                        for ($i = 1; $i <= date("W"); $i++) {
                            echo '<option value="' . $i . '">' . $i . '</option>';
                        }
                        echo '
                        </select>
                        <script>
                        $("#NoSemana > option[value=' . $semana . ']").attr("selected", true);
                        </script>
                        ';
                    }
                    if ($_GET['f'] == "Y-m") {
                        $mesEleccion = (isset($_GET['month'])) ? $_GET['month'] : date("n");
                        echo '
                        <label for="fecha">Mes No: </label>
                        <select onchange="elegirMes()" class="rol" style="min-width: 100px;max-width:150px;text-align:center;margin-right:20px" id="NoMes">
                        ';
                        for ($i = 1; $i <= date("n"); $i++) {
                            echo '<option value="' . str_pad($i, 2, "0", STR_PAD_LEFT) . '">' . $Meses[$i - 1] . '</option>';
                        }
                        echo '
                        </select>
                        <script>
                        $("#NoMes > option[value=' . str_pad($mesEleccion, 2, "0", STR_PAD_LEFT) . ']").attr("selected", true);
                        </script>
                        ';
                    }
                    if ($_GET['f'] == "p") {
                        echo '
                        <label for="fecha">Fecha #1: </label>
                        <select class="rol" style="min-width: 100px;max-width:150px;text-align:center;margin-right:20px" id="fch1">
                        ';
                        $data = mostrarDatosFactura($tipoFactura, 0, "", "");
                        $count =  count($data);
                        for ($i = 0; $i < $count; $i++) {
                            $fecha1[] = $data[$i]['Fecha'];
                        }
                        for ($i = 0; $i < $count; $i++) {
                            if (empty($fecha1[$i + 1])) {
                                echo '<option value="' . $fecha1[$i] . '">' . $fecha1[$i] . '</option>';
                                break;
                            } else {
                                if ($fecha1[$i] != $fecha1[$i + 1]) {
                                    echo '<option value="' . $fecha1[$i] . '">' . $fecha1[$i] . '</option>';
                                }
                            }
                        }
                        echo '
                        </select>
                        <label for="fecha">Fecha #2: </label>
                        <select class="rol" style="min-width: 100px;max-width:150px;text-align:center;margin-right:-10px" id="fch2">
                        ';
                        $data = mostrarDatosFactura($tipoFactura, 0, "", "");
                        $count =  count($data);
                        for ($i = 0; $i < $count; $i++) {
                            $fecha2[] = $data[$i]['Fecha'];
                        }
                        for ($i = 0; $i < $count; $i++) {
                            if (empty($fecha2[$i + 1])) {
                                echo '<option value="' . $fecha2[$i] . '">' . $fecha2[$i] . '</option>';
                                break;
                            } else {
                                if ($fecha2[$i] != $fecha2[$i + 1]) {
                                    echo '<option value="' . $fecha2[$i] . '">' . $fecha2[$i] . '</option>';
                                }
                            }
                        }
                        echo '
                        </select>
                        <a class="boton" onclick="personalizada()" style="width:7vh;float:right;margin-right:10px">Generar</a>
                        ';
                        if (isset($_GET['fch1'])) {
                            echo '
                            <script>
                                $("#fch1 > option[value=' . $_GET['fch1'] . ']").attr("selected", true);
                                $("#fch2 > option[value=' . $_GET['fch2'] . ']").attr("selected", true);
                            </script>
                            ';
                        }
                    }
                    echo '
                    <script>
                        $("#fecha > option[value=' . $_GET['f'] . ']").attr("selected", true);
                    </script>
                    ';
                }
                if ($dataPermisos[0]["Reportes"]) {
                    echo '
                    <a title="Crear Reporte" target="_blank" href="reporte.php" class="boton" style="width:2vh;float:right;padding: 2.1vh 2vh;"><i class="bi bi-clipboard-fill"></i></a>
                    ';
                }
                ?>
            </div>
        </div>
        <div class="contenido" style="min-height: 75vh;animation: inicial 0.5s;">
            <div class="box" id="box" style="padding:20px;max-height:70vh;margin-top:5px;border-radius:5px 5px 20px 20px">
            <?php
            echo '
            <canvas id="myChart" style="color:var(--texto)" width="100%" height="45%"></canvas>
            ';
        } else {
            echo '
            <canvas id="myChart" style="color:var(--texto)" width="100%" height="48%"></canvas>
            ';
        }
            ?>
            <script>
                var variables = window.location.search;
                var tipo = "bar";
                var label = '<?php echo ($tipoFactura == 0) ? "Ingresos" : "Compras" ?>';
                $("#factura > option[value='<?php echo $tipoFactura ?>']").attr("selected", true);
                <?php
                if (isset($_GET['t'])) {
                    if ($_GET['t'] == "bar") {
                        echo 'var tipo = "bar";';
                    } else {
                        echo 'var tipo = "line";';
                    }
                }
                ?>
                $("#diagrama > option[value=" + tipo + "]").attr("selected", true);
                let mes = [];
                let venta = [];

                function personalizada() {
                    var fecha1 = document.getElementById("fch1").value;
                    var fecha2 = document.getElementById("fch2").value;
                    <?php
                    if (isset($_GET["fch1"])) {
                    ?>
                        var pagina1 = window.location.href.split('&fch1=<?php echo $_GET['fch1'] ?>&fch2=<?php echo $_GET['fch2'] ?>');
                        if (typeof pagina1[1] !== 'undefined') {
                            variables = pagina1[0] + pagina1[1];
                        } else {
                            variables = pagina1[0];
                        }
                    <?php
                    }
                    ?>
                    window.location.href = variables + "&fch1=" + fecha1 + "&fch2=" + fecha2;
                }

                function factura() {
                    var factura = document.getElementById('factura').value;
                    <?php
                    if (isset($_GET['f'])) {
                    ?>
                        window.location.href = "?pg=1&f=<?php echo $_GET['f'] ?>&fc=" + factura;
                    <?php
                    }
                    ?>
                }

                function elegirYear() {
                    var NoSemana = document.getElementById("NoYear").value;
                    <?php
                    if (isset($_GET['year'])) {
                    ?>
                        var pagina1 = window.location.href.split('&year=<?php echo $_GET['year'] ?>');
                        if (typeof pagina1[1] !== 'undefined') {
                            variables = pagina1[0] + pagina1[1];
                        } else {
                            variables = pagina1[0];
                        }
                    <?php
                    }
                    ?>
                    window.location.href = variables + "&year=" + NoSemana;
                }
                
                function elegirSemana() {
                    var NoSemana = document.getElementById("NoSemana").value;
                    <?php
                    if (isset($_GET['week'])) {
                    ?>
                        var pagina1 = window.location.href.split('&week=<?php echo $_GET['week'] ?>');
                        if (typeof pagina1[1] !== 'undefined') {
                            variables = pagina1[0] + pagina1[1];
                        } else {
                            variables = pagina1[0];
                        }
                    <?php
                    }
                    ?>
                    window.location.href = variables + "&week=" + NoSemana;
                }

                function elegirMes() {
                    var NoMes = document.getElementById("NoMes").value;
                    <?php
                    if (isset($_GET['month'])) {
                    ?>
                        var pagina1 = window.location.href.split('&month=<?php echo $_GET['month'] ?>');
                        if (typeof pagina1[1] !== 'undefined') {
                            variables = pagina1[0] + pagina1[1];
                        } else {
                            variables = pagina1[0];
                        }
                    <?php
                    }
                    ?>
                    window.location.href = variables + "&month=" + NoMes;
                }

                function fecha() {
                    var tipo = document.getElementById("diagrama").value;
                    var fecha = document.getElementById("fecha").value;
                    var factura = document.getElementById('factura').value;
                    window.location.href = "?pg=1&t=" + tipo + "&f=" + fecha + '&fc=' + factura;
                }

                function grafica() {
                    var tipo = document.getElementById("diagrama").value;
                    if (tipo == "line") {
                        var pagina1 = window.location.href.split('&t=bar');
                    } else {
                        var pagina1 = window.location.href.split('&t=line');
                    }
                    if (typeof pagina1[1] !== 'undefined') {
                        variables = pagina1[0] + pagina1[1];
                    } else {
                        variables = pagina1[0];
                    }
                    window.location.href = variables + "&t=" + tipo;
                }
                <?php
                if ($tipo != 2 && $tipo != 4) {
                    for ($i = 0; $i < $contadorTotal; $i++) {
                        echo "
                            mes[" . $i . "] = ['" . $fech[$i] . "'];
                            venta[" . $i . "] = $vent[$i];
                        ";
                    }
                } else {
                    for ($i = 0; $i < count($dias); $i++) {
                        echo "
                            mes[" . $i . "] = ['" . $dias[$i] . "'];
                            venta[" . $i . "] = $vent[$i];
                        ";
                    }
                }
                ?>
                const ctx = document.getElementById('myChart').getContext('2d');
                const myChart = new Chart(ctx, {
                    type: tipo,
                    data: {
                        labels: mes,
                        datasets: [{
                            label: label,
                            data: venta,
                            backgroundColor: [
                                'rgba(255, 99, 132, 0.2)',
                                'rgba(54, 162, 235, 0.2)',
                                'rgba(255, 206, 86, 0.2)',
                                'rgba(75, 192, 192, 0.2)',
                                'rgba(153, 102, 255, 0.2)',
                                'rgba(255, 159, 64, 0.2)'
                            ],
                            borderColor: [
                                'rgba(255, 99, 132, 1)',
                                'rgba(54, 162, 235, 1)',
                                'rgba(255, 206, 86, 1)',
                                'rgba(75, 192, 192, 1)',
                                'rgba(153, 102, 255, 1)',
                                'rgba(255, 159, 64, 1)'
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            </script>
            <?php
            if ($p == 1) {
            ?>
            </div>
        </div>
    <?php
            }
    ?>