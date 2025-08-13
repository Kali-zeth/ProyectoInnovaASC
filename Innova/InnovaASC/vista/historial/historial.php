<?php
$barra = false;
$pg = "Historial";
require_once "../../menu.php";
require_once "../../controlador/Procesos.php";
?>
<?php
if (isset($_GET['hs'])) {
    $ID = $_GET['hs'];
    if ($dataDetalle2 = mostrarDatos("historial", $ID, 1, "")) {
?>
        <div class="contenido" style="float:left;width:32%;min-height:70vh;margin-right:-75px">
            <div style="min-height:25%;font-size:15px;color:#333;align-items:stretch;height:80vh;width:80%;text-align:center;margin-right:30px;" class="box">
                <a style="float:right;margin-right:10px;margin-top:-25px" href="historial.php?"><i style="font-size:18px;" class="bi bi-x"></i></a>
                <div style="width:100%;padding-bottom:10px;padding-top:20px">
                    <?php
                    if (file_exists('../../imagenes/' . $dataDetalle2[0]["Dato1"])) {
                    ?>
                        <img style="width:80%" src="../../imagenes/<?php echo $dataDetalle2[0]['Dato1']; ?>" />
                    <?php
                    } else {
                    ?>
                        <img style="width:60%" src="../../imagenes/nohay.png" />
                    <?php
                    }
                    ?>
                </div>
                <div style="min-height:17%;min-width:100%;text-align:left;">
                    <div style="font-size:15px;margin-left:50px;color:var(--texto)"><?php echo $dataDetalle2[0]['Dato2'] ?></div>
                </div>
            </div>
        </div>
<?php
    }
}
?>
<div class="contenido">
    <div class="box" style="min-width:90%;max-height: 76vh;">
        <table style="width:90%;margin: 0 auto;">
            <tr>
                <td>
                    <h3>Fecha</h3>
                </td>
                <td>
                    <h3>Hora</h3>
                </td>
                <td>
                    <h3>Accion</h3>
                </td>
            </tr>
            <?php
            if (isset($_POST['caja']) && $_POST['caja'] != null) {
                $fecha = $_POST['caja'];
                $data = mostrarDatos("historial", $fecha, "", "");
            } else {
                $data = mostrarDatos("historial", "", "", "");
            }
            $count = count($data);
            for ($i = 1; $i < $count; $i++) {
            ?>
                <tr>
                    <td style="padding-bottom: 5px;"><?php echo $data[$i]['Fecha'] ?></td>
                    <td><?php echo $data[$i]['Hora'] ?></td>
                    <td style="text-align:left;padding-left:4%"><?php echo $data[$i]['Accion']; ?></td>
                    <td>
                        <?php
                        $dataDetalle = mostrarDatos("historial", $data[$i]['ID'], 1, "");
                        $NroFac = $dataDetalle[0]['Dato1'];
                        $dato2 = $dataDetalle[0]['Dato2'];
                        if ($dato2 == "factura salida") {
                        ?>
                            <a title="Ver factura" href="../Facturar/facturas.php?p=0&hs=<?php echo $NroFac ?>&nro=<?php echo $NroFac ?>"><i class="bi bi-info-circle"></i></a>
                        <?php
                        } elseif ($dato2 == "factura entrada") {
                        ?>
                            <a title="Ver factura" href="../Facturar/facturas.php?p=1&hs=<?php echo $NroFac ?>"><i class="bi bi-info-circle"></i></a>
                        <?php
                        } elseif ($dato2 == "Inicio de sesion") {
                        ?>
                            <a title="Ver factura" href="../ajustes/ajustes.php"><i class="bi bi-info-circle"></i></a>
                        <?php
                        } else {
                        ?>
                            <a title="Informacion" href="historial.php?hs=<?php echo $data[$i]['ID'] ?>"><i class="bi bi-info-circle"></i></a>
                        <?php
                        }
                        ?>
                    </td>
                </tr>
            <?php
            }
             
            ?>
        </table>
    </div>