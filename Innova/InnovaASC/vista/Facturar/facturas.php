<?php
$p = $_GET['p'];
if ($p == 1) {
    $pg = "Facturas - Entrada";
} else {
    $pg = "Facturas - Salida";
}
$barra = true;
require_once "../../menu.php";

?>

<?php
if (isset($_GET['id']) || isset($_GET['nro'])) {
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $data = mostrarDatosFactura(1, 4, $id, "");
    } else {
        $id = $_GET['nro'];
        $data = mostrarDatosFactura(0, 4, $id, "");
    }
    if ($data) {
        $nroFac = $data[0]['Nro_factura'];
        if (isset($_GET['id'])) {
            $filtro = $data[0]['NIT'];
            $dataUser = mostrarDatos("proveedores",  "", $filtro, "");
        } else {
            $filtro = $data[0]['NIT'];
            $dataUser = mostrarDatos("clientes",  "", $filtro, "");
        }
        $dataDetalle = mostrarDatosFactura($p, 5, $id, "");
        if ($dataUser && $dataDetalle) {
            $count = count($dataDetalle);
?>
            <div class="contenido visualizacion_factura">
                <div class="ventana" style="width:110vh;box-shadow:none;overflow-y:none;color:var(--texto);animation: inicial 0.4s">
                    <div class='bttn' style="position:static;grid-column-start: 2;grid-row-start: 1;">
                        <a title="Cerrar" href="facturas.php?p=<?php echo $p ?>"><i style="font-size: 27px;" class="bi bi-x"></i></a>
                    </div>
                    <div style="float:center;grid-column-start: 1;grid-row-start: 1;">
                        <p style="font-size:38px;font-family:Brush Script MT"><?php echo $dataUser[0]['Nombre']; ?></p>
                    </div>
                    <div style="grid-column-start: 1;grid-row-start: 2;">
                        <i style="font-size:25px;color:gray;"><?php echo $nroFac ?></i>
                    </div>
                    <div style="grid-column-start: 1;grid-row-start: 3">
                        <ul style="float:left;text-align:left;font-size:2.6vh;">
                            <li><b>Nombre Cliente: </b> <?php echo $dataUser[0]['Nombre']; ?> </li>
                            <li><b>NIT: </b><?php echo $filtro; ?> </li>
                            <li><b>Telefono: </b><?php echo $dataUser[0]['Telefono']; ?> </li>
                            <li><b>Dirección: </b> <?php echo $dataUser[0]['Direccion']; ?> </li>
                            <li><b>Ciudad: </b> <?php echo $dataUser[0]['Ciudad']; ?> </li>
                        </ul>
                    </div>
                    <table style="width:92%;margin: 0 auto;height:20%;grid-column-start: 1;grid-row-start: 4;margin-bottom: 40px;font-size:2vh;color:var(--texto)">
                        <tr>
                            <th><b>Producto</b></th>
                            <th><b>Cantidad</b></th>
                            <th><b>Precio por caja</b></th>
                            <th><b>Sub-total</b></th>
                        </tr>
                        <?php
                        for ($i = 0; $i < $count; $i++) {
                        ?>
                            <tr>
                                <td><?php echo $dataDetalle[$i]['Producto'] ?></td>
                                <td><?php echo $dataDetalle[$i]['Cantidad'] ?></td>
                                <td><?php echo $dataDetalle[$i]['Precio_caja'] ?></td>
                                <td><?php echo $dataDetalle[$i]['Sub_total'] ?></td>
                            </tr>
                <?php
                        }
                    }
                }
                ?>
                    </table>
                </div>
            </div>
        <?php
    }
        ?>
        <div class="contenido">
            <div class="box" style="max-height: 76vh;">
                <table style="width:90%;margin: 0 auto;">
                    <tr>
                        <th>
                            <h3>Nro_factura</h3>
                        </th>
                        <th>
                            <h3>Nombre</h3>
                        </th>
                        <th>
                            <h3>CC/NIT</h3>
                        </th>
                        <th>
                            <h3>Fecha</h3>
                        </th>
                        <th>
                            <h3>Total</h3>
                        </th>
                    </tr>
                    <?php
                    $NIT = '';
                    if (isset($_POST['caja']) != null || isset($_GET['hs'])) {
                        if (isset($_GET['hs'])) {
                            $codigo = $_GET['hs'];
                        } else {
                            $codigo = $_POST['caja'];
                        }
                        $data = mostrarDatosFactura($p, 4, $codigo, "");
                    } else {
                        $data = mostrarDatosFactura($p, 0, 0, "");
                    }
                    if ($data) {
                        $count = count($data);
                        for ($i = 0; $i < $count; $i++) {
                    ?>
                            <tr>
                                <td><?php echo $data[$i]['Nro_factura'] ?></td>
                                <td><?php echo $data[$i]['Nombre'] ?></td>
                                <td><?php echo $data[$i]['NIT'] ?></td>
                                <td><?php echo $data[$i]['Fecha']  ?></td>
                                <td><?php echo $data[$i]['Total'] ?></td>
                                <?php
                                if ($p == 0) {
                                    echo '
                                    <td><a title="Informacion" href="facturas.php?p=' . $p . '&nro=' . $data[$i]['Nro_factura'] . '"><i class="bi bi-info-circle"></i></a></td>
                                    ';
                                    if ($dataPermisos[0]["Reportes"]) {
                                        echo '
                                        <td><a class="print" target="_blank" href="http://localhost:8082/proyecto/facturas/Factura-' . $data[$i]['Fecha'] . "-No-" . $data[$i]['Nro_factura'] . '.pdf" title="Ver Factura"><i class="bi bi-printer"></i></a></td>
                                        ';
                                    }
                                } else {
                                    echo '
                                    <td><a title="Informacion" href="facturas.php?p=' . $p . '&id=' . $data[$i]['ID'] . '"><i class="bi bi-info-circle"></i></a></td>
                                    ';
                                }
                                ?>
                            </tr>
                    <?php
                        }
                    }
                    ?>
                </table>
            </div>
        </div>