<?php
$pg = "";
include "../../controlador/Procesos.php";
ob_start();
date_default_timezone_set("America/Bogota");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo "Reporte-Semanal-" . date("Y-m-j"); ?></title>
</head>
<style>
    table,
    th,
    td {
        color: black;
        padding: 4px;
        border: 1px solid black;
        border-collapse: collapse;
        text-align: center;
    }

    th {
        font-size: 1em;
    }

    td {
        padding: 6px;
    }
</style>
<?php
//USUARIO
session_start();
if (isset($_SESSION['session'])) {
    $session = $_SESSION['session'];
}
$dataUser = mostrarDatos("usuario", $session[0]['user'], "", "",);
//EMPRESA
$dataAjustes = mostrarDatosAjustes();
//RESULTADOS
$totalVentas = 0;
$totalCompras = 0;
$cantidadVentas = 0;
$cantidadCompras = 0;
//FECHAS
$year = date('Y-m');
$data = mostrarDatosFactura(0, 2, $year, "");
$count = count($data);
$tiempo = array("Domingo", "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado");
$mes = array(0, 0, 0, 0, 0, 0, 0);
if (isset($_GET['week'])) {
    $week = $_GET["week"];
} else {
    $week = date("W");
}
for ($i = 0; $i < $count; $i++) {
    if (date("W", strtotime($data[$i]['Fecha'])) == $week) {
        if ($i != 0) {
            if ($data[$i - 1]['Fecha'] != $data[$i]['Fecha']) {
                $posicion = date("w", strtotime($data[$i]['Fecha']));
                $mes[$posicion] = $data[$i]['Fecha'];
            }
        } else {
            $posicion = date("w", strtotime($data[$i]['Fecha']));
            $mes[$posicion] = $data[$i]['Fecha'];
        }
    }
}
for ($i = 0; $i < count($mes); $i++) {
    if ($mes[$i] != 0) {
        $dataSalida = mostrarDatosFactura(0, 3, $mes[$i], "");
        for ($a = 0; $a < count($dataSalida); $a++) {
            $nross[$i][] = $dataSalida[$a]['Nro_factura'];
        }
        $dataGastos = mostrarDatosFactura(1, 3, $mes[$i], "");
        if ($dataGastos != null) {
            $cantidadCompras = $cantidadCompras + count($dataGastos);
            for ($a = 0; $a < count($dataGastos); $a++) {
                $totalCompras = $totalCompras + $dataGastos[$a]['Total'];
            }
        }
    } else {
        $nross[$i][] = 0;
    }
}
?>

<body style="width: 100%;">
    <h3 style="color:gray">Reporte-<?php echo date("Y-m-j") ?></h3>
    <h1 style=""><?php echo $dataAjustes['Nombre'] ?></h1>
    <p><b>Generado por: </b><?php echo $dataUser[0]["Nombre"]; ?></p>
    <p><b>fecha: </b><?php echo date("Y-m-d") ?></p>
    <p><b>Hora: </b><?php echo date('H:i:s') ?></p>
    <br>
    <?php
    for ($k = 1; $k != (count($tiempo) + 1); $k++) {
        if ($k == 7) {
            $k = 0;
        }
    ?>
        <div style="border:1px solid black;margin-left:5px;margin-right:5px;padding:10px 5px;padding-left:10px;background: lightgray">
            <b style="font-size: 1.3em;"><?php echo $tiempo[$k] ?></b>
        </div>
        <table style="width:100%">
            <tr style="background: gray">
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Total venta</th>
            </tr>
            <?php
            $total = 0;
            $totalCantidad = 0;
            if ($nross[$k][0] != 0) {
                $detalle = mostrarDatosFactura(0, 7, $nross[$k], "");
                $cantidadVentas = $cantidadVentas + count($detalle);
                for ($b = 0; $b < count($detalle); $b++) {
                    $total = $total + $detalle[$b]["Sub_total"];
                    $totalCantidad = $totalCantidad + $detalle[$b]["Cantidad"];
            ?>
                    <tr>
                        <td><?php echo $detalle[$b]["Producto"] ?></td>
                        <td><?php echo $detalle[$b]["Cantidad"] ?></td>
                        <td>$<?php echo number_format($detalle[$b]["Sub_total"]) ?></td>
                    </tr>
                <?php
                    $totalVentas = $totalVentas + $detalle[$b]["Sub_total"];
                }
            } else {
                ?>
                <tr>
                    <td>0</td>
                    <td>0</td>
                    <td>0</td>
                </tr>
            <?php
            }
            if ($k == 0) {
                $k = 7;
            }
            ?>
            <tr style="background: lightgray">
                <th style="font-size: 1em;">Total:</th>
                <th><?php echo $totalCantidad ?></th>
                <th>$<?php echo number_format($total) ?></th>
            </tr>
        </table>
        <br>
    <?php
    }
    ?>
    <div style="border:1px solid black;margin-left:5px;margin-right:5px;padding:10px 5px;padding-left:10px;background: lightgray">
        <b style="font-size: 1.3em;">Resultados</b>
    </div>
    <table style="width:100%">
        <tr style="background: lightgray">
            <th>Cantidad ventas</th>
            <th>Total ventas</th>
            <th>Cantidad compras</th>
            <th>Total compras</th>
            <?php
            if (($totalVentas - $totalCompras) < 0) {
                echo "
                <th>Perdidas</th>
                <th>Porcentaje de perdida</th>
                ";
            } else {
                echo "
                <th>Ganancia</th>
                <th>Porcentaje de ganancia</th>
                ";
            }
            ?>
        </tr>
        <tr>
            <td><?php echo $cantidadVentas ?></td>
            <td>$<?php echo number_format($totalVentas) ?></td>
            <td><?php echo $cantidadCompras ?></td>
            <td>$<?php echo number_format($totalCompras) ?></td>
            <?php
            if (($totalVentas - $totalCompras) < 0) {
                echo "<td style='color:tomato'>";
            } else {
                echo "<td style='color:green'>";
            }
            ?>
            $<?php echo number_format($totalVentas - $totalCompras) ?>
            </td>
            <?php
            if (($totalVentas - $totalCompras) < 0) {
                echo "<td style='color:tomato'>";
            } else {
                echo "<td style='color:green'>";
            }
            ?>
            <?php echo round((($totalVentas - $totalCompras) * 100) / $totalVentas) ?>%
            </td>
        </tr>
    </table>
    <br>
</body>

</html>
<?php
$html = ob_get_clean();

// include autoloader
require_once '../../librerias/dompdf/autoload.inc.php';

use Dompdf\Dompdf;

$dompdf = new Dompdf();

$options = $dompdf->getOptions();
$options->set(array('isRemoteEnabled' => true));
$dompdf->setOptions($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('letter');
$dompdf->render(); //este comando renderiza el PDF
$nombreArchivo = "Reporte-" . date("Y-m-j") . ".pdf";
$dompdf->stream("Reporte.pdf", ['Attachment' => false]);
?>