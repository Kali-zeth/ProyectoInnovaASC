<?php
$barra = false;
$pg = "";
require_once "../../menu.php";
require_once "../../controlador/Procesos.php";
if (isset($_SESSION['entrada'])) {
    $entrada = $_SESSION['entrada'];
    $_SESSION['entrada'] = $entrada;
}
if (isset($_POST['pr'])) {
    $nit = $_POST['pr'];
    if ($dataCliente = mostrarDatos("proveedores", "", $nit, "")) {
        $tel = $dataCliente[0]['Telefono'];
        $dire = $dataCliente[0]['Direccion'];
        $nom = $dataCliente[0]['Nombre'];
        $ciu = $dataCliente[0]['Ciudad'];
    }
} else {
    $tel = $_POST['te'];
    $dire = $_POST['dire'];
    $nom = $_POST['nom'];
    $nit = $_POST['di'];
    $ciu = $_POST['ciu'];
}
$IDD = $_POST['IDD'];
$nroFac = $_POST['NroFac'];
$fech = $_POST['fech'];
$cant = array();
$prec = array();
$total = array();
$totalT = 0;

if (isset($_SESSION['entrada'])) {
    for ($i = 0; $i <= count($entrada) - 1; $i++) {
        $cant[] = $_POST['cant' . $i];
        $prec[] = $_POST['prc' . $i];
        $total[] = $prec[$i] * $cant[$i];
        $totalT = $totalT = $total[$i];
    }
}

// Comprobar si ya existe la factura 
$existenciaFactura = 0;
$Factura = mostrarDatosFactura(1, 4, $nroFac, "");
if ($Factura) {
    $existenciaFactura = $Factura;
    unset($_SESSION['entrada']);
    echo "
    <script>
        window.location.href = 'Entrada.php';
    </script>
    ";
}

if (empty($tel) || empty($dire) || empty($nom) || empty($nit) || empty($ciu) || empty($fech) || empty($_SESSION['entrada'])) {
    echo "
    <script>
        window.location.href = 'Entrada.php?d=0';
    </script>
    ";
    $datos = false;
} else {
    $datos = true;
}

if (empty($_POST['pr'])) {
    AgregarProveedorFactura($nit, $nom, $dire, $ciu, $tel);
}
?>
<div class="contenido" style="max-height: 100vh;margin-top:-5vh">
    <div class='bttn'>
        <form action="../productos/Productos.php">
            <button><i class='bi bi-arrow-return-left'></i></button>
        </form>
    </div>
    <div class="ventana" style="max-height: 90vh;color:var(--texto);">
        <!-- mostrar informacion  -->
        <div style="float:center;grid-column-start: 1;grid-row-start: 1;">
            <p style="font-size:38px;font-family:Brush Script MT"><?php echo $nom; ?></p>
        </div>
        <div style="grid-column-start: 1;grid-row-start: 2;">
            <i style="font-size:25px;color:gray;"><?php echo $nroFac ?></i>
        </div>
        <div style="grid-column-start: 1;grid-row-start: 3;">
            <ul style="float:left;text-align:left;font-size:18px">
                <li><b>Nombre Proveedor: </b> <?php echo $nom; ?> </li>
                <li><b>NIT: </b><?php echo $nit; ?> </li>
                <li><b>Telefono: </b><?php echo $tel; ?> </li>
                <li><b>Dirección: </b> <?php echo $dire; ?> </li>
                <li><b>Ciudad: </b> <?php echo $ciu; ?> </li>
            </ul>
        </div>
        <table style="width:92%;margin: 0 auto;height:20%;grid-column-start: 1;grid-row-start: 4;margin-bottom: 40px; ">
            <tr>
                <th><b>ID</b></th>
                <th><b>Nombre</b></th>
                <th><b>Cantidad</b></th>
                <th><b>Precio por caja</b></th>
                <th><b>Sub-total</b></th>
            </tr>
            <?php
            if (isset($_SESSION['entrada']) && $datos && $existenciaFactura == 0) {
                if (AgregarFacturaEntrada($nit, $nroFac, $totalT, $fech, $IDD)) {
                    for ($i = 0; $i <= count($entrada) - 1; $i++) {
                        if ($dataProducto = mostrarDatos("Productos", "", 1, $entrada[$i]['producto'])) {
            ?>
                            <tr>
                                <td><?php echo $dataProducto[0]['ID'] ?></td>
                                <td><?php echo $entrada[$i]['producto'] ?></td>
                                <td><?php echo $cant[$i] ?></td>
                                <td><?php echo $prec[$i] ?></td>
                                <td><?php echo $total[$i] ?></td>
                            </tr>
            <?php
                            $cant2 = $cant[$i];
                            $id = $dataProducto[0]['Producto'];
                            $prc = $prec[$i];
                            $subtotal = $total[$i];
                            $cantidadProducto = $dataProducto[0]['Cantidad'] + $cant[$i];
                            $precioProducto = $prec[$i];
                        }
                        AgregarDetalleEntrada($cant2, $id, $prc, $subtotal, $IDD, $cantidadProducto, $id, $precioProducto);
                    }
                    unset($_SESSION['entrada']);
                    HistorialFactura_entrada($nom, $IDD, $totalT);
                }
            }
            ?>
    </div>

</div>