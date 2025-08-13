<?php
$barra = false;
$pg = "";
require_once "../../menu.php";
require_once "../../controlador/Procesos.php";
if (isset($_SESSION['factura'])) {
    $factura = $_SESSION['factura'];
    $_SESSION['factura'] = $factura;
}
if (!$dataPermisos[0]["Facturar"]) {
    echo '
    <script type="text/javascript">
        window.history.go(-1);
    </script>
    ';
}
if (isset($_POST['cl'])) {
    $nit = $_POST['cl'];
    if ($dataCliente = mostrarDatos("clientes", "", $nit, "")) {
        $tel = $dataCliente[0]['Telefono'];
        $dire = $dataCliente[0]['Direccion'];
        $nom = $dataCliente[0]['Nombre'];
        $ciu = $dataCliente[0]['Ciudad'];
        $correo = $dataCliente[0]['Correo'];
    }
} else {
    $tel = $_POST['te'];
    $dire = $_POST['dire'];
    $nom = $_POST['nom'];
    $nit = $_POST['di'];
    $ciu = $_POST['ciu'];
    $correo = $_POST['correo'];
}
$nroFac = $_POST['NroFac'];
$fech = $_POST['fech'];
$total = $_POST['total'];

// Comprobar si ya existe la factura 
$existenciaFactura = 0;
$Factura = mostrarDatosFactura(0, 4, $nroFac, "");
if ($Factura) {
    $existenciaFactura = $Factura;
    unset($_SESSION['factura']);
    echo "
    <script>
    window.location.href = 'Salida.php';
    </script>
    ";
}

if (empty($tel) || empty($dire) || empty($nom) || empty($nit) || empty($ciu) || empty($fech) || empty($correo) || empty($_SESSION['factura'])) {
    echo "
        <script>
        window.location.href = 'Salida.php?d=0';
        </script>
        ";
    $datos = false;
} else {
    $datos = true;
}

if (empty($_POST['cl'])) {
    AgregarClienteFactura($nit, $nom, $tel, $dire, $ciu, $correo);
}
?>
<script>
    $('#pantalla_carga').css('display', 'flex');
</script>
<div class="contenido" style="max-height: 100vh;margin-top:-5vh">
    <div class='bttn'>
        <a class="print" style="padding: 1.8vh 2vh;width:3.5vh;" href="Salida.php"><i style="color:var(--texto)" class='bi bi-arrow-return-left'></i></a>
        <a class="print" style="padding: 1.8vh 2vh;width:3.5vh;" target='_blank' href="http://localhost/proyecto/facturas/Factura-<?php echo $fech . "-No-" . $nroFac ?>.pdf"><i style="font-size: 3.5vh;" class='bi bi-printer'></i></a>
    </div>
    <div class="ventana" style="max-height: 90vh;">
        <!-- mostrar informacion  -->
        <div style="float:center;grid-column-start: 1;grid-row-start: 1;">
            <p style="font-size:38px;font-family:Brush Script MT;color:var(--texto);"><?php echo $nom; ?></p>
        </div>
        <div style="grid-column-start: 1;grid-row-start: 2;">
            <i style="font-size:25px;color:gray;"><?php echo $nroFac ?></i>
        </div>
        <div style="grid-column-start: 1;grid-row-start: 3;">
            <ul style="float:left;text-align:left;font-size:18px;color:var(--texto);">
                <li><b>Nombre Proveedor: </b> <?php echo $nom; ?> </li>
                <li><b>NIT: </b><?php echo $nit; ?> </li>
                <li><b>Telefono: </b><?php echo $tel; ?> </li>
                <li><b>Dirección: </b> <?php echo $dire; ?> </li>
                <li><b>Ciudad: </b> <?php echo $ciu; ?> </li>
                <li><b>Correo: </b><?php echo $correo; ?> </li>
            </ul>
        </div>
        <table style="width:92%;margin: 0 auto;height:20%;grid-column-start: 1;grid-row-start: 4;margin-bottom: 40px;color:var(--texto) !important">
            <tr>
                <th><b>Nombre</b></th>
                <th><b>Cantidad</b></th>
                <th><b>Precio por caja</b></th>
                <th><b>Sub-total</b></th>
            </tr>
            <?php
            if (isset($_SESSION['factura']) && $datos && $existenciaFactura == 0) {
                if (AgregarFacturaSalida($nit, $nroFac, $total, $fech)) {
                    for ($i = 0; $i <= count($factura) - 1; $i++) {
                        if ($dataProducto = mostrarDatos("Productos", "", 1, $factura[$i]['producto'])) {
            ?>
                            <tr>
                                <td><?php echo $factura[$i]['producto'] ?></td>
                                <td><?php echo $factura[$i]['cantidad'] ?></td>
                                <td><?php echo $dataProducto[0]['PrecioC'] ?></td>
                                <td><?php echo $factura[$i]['subtotal'] ?></td>
                            </tr>
            <?php
                            $cant = $factura[$i]['cantidad'];
                            $id = $dataProducto[0]['Producto'];
                            $prc = $dataProducto[0]['PrecioC'];
                            $subtotal = $factura[$i]['subtotal'];
                            $cantidadProducto = $dataProducto[0]['Cantidad'] - $factura[$i]['cantidad'];
                        }
                        AgregarDetalleSalida($cant, $id, $prc, $subtotal, $nroFac, $cantidadProducto, $id);
                    }
                    require('facturaElectronica.php');
                    unset($_SESSION['factura']);
                    HistorialFactura_salida($nom, $nroFac, $total);
                }
            }
            ?>
    </div>

</div>