<?php
ob_start();
if (isset($_POST['id'])) {
    $Nro_fac = $_POST['id'];
} else {
    $Nro_fac = $nroFac;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo "Factura-Nro:" . $Nro_fac; ?></title>
</head>
<style>
    table,
    th,
    td {
        color: black;
        padding: 4px;
        border: 1px solid black;
        border-collapse: collapse;
    }

    li {
        list-style-type: none;
    }
</style>
<?php
date_default_timezone_set("America/Bogota");
$dataUser = mostrarDatos("usuario", $session[0]['user'], "", "",);
$responsable = $dataUser[0]["Nombre"];
$dataAjustes = mostrarDatosAjustes();
$dataFactura = mostrarDatosFactura(0, 4, $Nro_fac, "");
$dataCliente = mostrarDatos("clientes",  "", $dataFactura[0]['NIT'], "");
?>

<body style="width: 100%;">
    <div style="margin-left:5px;color:#333">Factura_Nro: <?php echo $Nro_fac ?></div>
    <table style="margin-top:5px;width: 100%;">
        <tr>
            <td>
                <h1 style="padding-left: 15px;"><?php echo $dataAjustes['Nombre'] ?></h1>
                <p style="font-size:14px">
                    <b style="padding-left: 15px;">NIT: </b><i><?php echo $dataAjustes['NIT'] ?></i>
                    <b style="padding-left: 15px;">Ciudad: </b><i><?php echo $dataAjustes['Ciudad'] ?></i>
                    <b style="padding-left: 15px;">Direccion: </b><i><?php echo $dataAjustes['Direccion'] ?></i><br><br>
                    <b style="padding-left: 15px;">Responsable: </b><i><?php echo $responsable ?></i>
                </p>
            </td>
        </tr>
    </table>
    <table style="width: 100%;">
        <td style="padding:8px 20px">
            <p><b>Fecha: </b><?php echo $dataFactura[0]['Fecha']; ?><b style="padding-left: 15px">Hora: </b><?php echo date('H:i:s'); ?></p>
        </td>
    </table>
    <table style="width:100%;" color="black">
        <tr>
            <td style="padding:8px 20px">
                <li style="padding-bottom:5px"><b>Nombre Cliente: </b> <?php echo $dataCliente[0]['Nombre']; ?> </li>
                <li style="padding-bottom:5px"><b>NIT: </b><?php echo $dataCliente[0]['ID']; ?> </li>
                <li style="padding-bottom:5px"><b>Correo: </b><?php echo $dataCliente[0]['Correo']; ?> </li>
                <li style="padding-bottom:5px"><b>Telefono: </b><?php echo $dataCliente[0]['Telefono']; ?> </li>
                <li style="padding-bottom:5px"><b>Dirección: </b> <?php echo $dataCliente[0]['Direccion']; ?> </li>
                <li style="padding-bottom:5px"><b>Ciudad: </b> <?php echo $dataCliente[0]['Ciudad']; ?> </li>
            </td>
        </tr>
    </table>
    <table style="width:100%" color="black">
        <tr>
            <th>
                <h4>Producto</h4>
            </th>
            <th>
                <h4>Precio por caja</h4>
            </th>
            <th>
                <h4>Cantidad</h4>
            </th>
            <th>
                <h4>Sub-total</h4>
            </th>
        </tr>
        <?php
        $totalSNIVA = 0;
        $dataDetalle = mostrarDatosFactura(0, 5, $Nro_fac, "");
        for ($i = 0; $i < count($dataDetalle); $i++) {
        ?>
            <tr>
                <td><?php echo $dataDetalle[$i]['Producto'] ?></td>
                <td><?php echo $dataDetalle[$i]['Precio_caja'] ?></td>
                <td><?php echo $dataDetalle[$i]['Cantidad'] ?></td>
                <td>$<?php echo number_format($dataDetalle[$i]['Sub_total']) ?></td>
            </tr>
        <?php
            $totalSNIVA = $totalSNIVA + $dataDetalle[$i]['Sub_total'];
        }
        ?>
    </table>
    <table style="width:250px;text-align:left;margin-top:20px;float:right" color="black">
        <tr>
            <td style="padding:8px 20px">
                <b>Total sin IVA:</b>
                <div style="float:right">$<?php echo number_format($totalSNIVA) ?></div>
            </td>
        </tr>
        <tr>
            <td style="padding:8px 20px">
                <b>IVA %19:</b>
                <div style="float:right">$<?php echo number_format($totalSNIVA * 0.19) ?></div>
            </td>
        </tr>
        <tr>
            <td style="padding:8px 20px">
                <b>Total:</b>
                <div style="float:right">$<?php echo number_format($totalSNIVA + ($totalSNIVA * 0.19)) ?></div>
            </td>
        </tr>
    </table>
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
$dompdf->render();
//Donde guardar el documento
$nombreArchivo = "../../facturas/Factura-" . $dataFactura[0]['Fecha'] . "-No-" . $Nro_fac . ".pdf";
$output = $dompdf->output();
$mensaje = "
<body>
    <h1>" . $dataAjustes['Nombre'] . "</h1>
    <p>Factura de compra No: $Nro_fac</p>
    <div>
        <h3>Gracias por su compra!</h3>
    </div>
</body>
";
if (file_exists($nombreArchivo)) {
} else {
    file_put_contents($nombreArchivo, $output);
}
// enviarCorreo($dataCliente[0]['Correo'], $dataAjustes['Nombre'] . "-Factura virtual", $mensaje, "Factura de compra No: " . $Nro_fac, $nombreArchivo);
?>