<?php
$barra = false;
$pg = "Facturar";
require_once "../../menu.php";
if (isset($_SESSION['factura'])) {
    $factura = $_SESSION['factura'];
    $_SESSION['factura'] = $factura;
}
if (isset($_GET['d'])) {
    echo "
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'No puede crear una factura vacia',
            confirmButtonColor: 'gray',
            confirmButtonText: 'Volver',
        })
    </script>
    ";
}
?>
<style>
    .titulo {
        padding-left: 0 !important;
    }
</style>
<div class="contenido contenido_facturas">
    <div class="box box_facturas">
        <form action="facturaSalida.php" id="factura" method="post" style="min-width:92%">
            <?php
            $num = '';
            $i = '';
            if ($data = mostrarDatosFactura(0, 6, "", "")) {
                $count = $data[0]['Nro_factura'] + 1;
            }
            ?>
            <input type="hidden" name="NroFac" value="<?php echo $count; ?>">
            <h3 style="float:center">Nro Factura<p style="color:gray"><?php echo $count; ?></p>
            </h3>
            <div class="cliente">
                <i style="font-size: 17px;float:left;padding-left:20px" class="bi bi-person-circle">
                    <?php
                    if (isset($_GET['cl'])) {
                        echo "Cliente nuevo";
                    } else {
                    ?>
                        <input type="hidden" name="ciu" value="Bogota D.C">
                        <input type="hidden" value="<?php echo date('Y-m-j'); ?>" name="fech">
                        <select name="cl" class="select_persona">
                            <?php
                            $data = mostrarDatos("clientes", "", "", "");
                            $longitud = count($data);
                            for ($i = 0; $i < $longitud; $i++) {
                                echo "<option value='" . $data[$i]['ID'] . "'>" . $data[$i]['Nombre'] . "</option>";
                            }
                            ?>
                        </select>
                    <?php
                    }
                    ?>
                </i>
            </div>
            <?php
            if (isset($_GET['cl']) && $dataPermisos[0]["Clientes"]) {
            ?>
                <a href="Salida.php?"><i class="bi bi-caret-up-fill"></i></a>
                <ul align="left" style="margin-top:-5px;font-size: 1.5vh;">
                    <div style="float:left;margin-right:10px;">
                        <li>
                            <h3>Nombre Cliente:<input autocomplete="off" style="width:60%" type="text" name="nom" required></h3>
                        </li>
                        <li>
                            <h3>Cedula-NIT:<input autocomplete="off" style="width:65%" type="text" name="di" required></h3>
                        </li>
                        <li>
                            <h3>Telefono:<input autocomplete="off" style="width:75%" type="text" name="te" required></h3>
                        </li>
                    </div>
                    <div style="float:right;margin-right:10px;margin-bottom:25px">
                        <li>
                            <h3>Direccion:<input autocomplete="off" style="width:60%" type="text" name="dire" required></h3>
                        </li>
                        <li>
                            <h3>Ciudad:<input autocomplete="off" style="width:67%" type="text" name="ciu" value="Bogota D.C" required></h3>
                        </li>
                        <li>
                            <h3>Correo:<input autocomplete="off" pattern=".+@gmail\.com" style="width:67%;text-align:center" type="email" name="correo"></h3>
                        </li>
                    </div>
                    <input type="hidden" value="<?php echo date('Y-m-d'); ?>" name="fech">
                </ul>
            <?php
            } else {
                if ($dataPermisos[0]["Clientes"]) {
                    echo '<a href="Salida.php?cl=0"><i class="bi bi-caret-down-fill"></i></a>';
                }
            }
            ?>
            <!-- ////////////////////////////////////////////////////////////////////////Factura -->
            <table id="tabla" style="width:90%;margin: 0 auto;">
                <tr>
                    <th>ID</th>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Sub-total</th>
                </tr>
                <?php
                $total = 0;
                if (isset($_SESSION['factura'])) {
                    for ($i = 0; $i <= count($factura) - 1; $i++) {
                        $data = mostrarDatos("Productos", $factura[$i]['producto'], "", "");
                ?>
                        <tr>
                            <td><?php echo $factura[$i]['producto'] ?></td>
                            <td><?php echo $data[0]['Producto'] ?></td>
                            <input type="hidden" name="nom<?php echo $i ?>" value="<?php echo $data[0]['Producto'] ?>">
                            <td><?php echo $factura[$i]['cantidad'] ?></td>
                            <input type="hidden" name="cant<?php echo $i ?>" value="<?php echo $factura[$i]['cantidad'] ?>">
                            <td>$<?php echo number_format($factura[$i]['subtotal']) ?></td>
                            <td><a class="boton_factura borrar" href="../../controlador/Procesos.php?bru=<?php echo $factura[$i]['producto'] ?>"><i class="bi bi-x"></i></a></td>
                        </tr>
                    <?php
                        $total = $total + $factura[$i]['subtotal'];
                    }
                    ?>
                    <input type="hidden" name="cont" value="<?php echo count($factura) ?>">
                <?php
                } else {
                ?>
                    <tr>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                    </tr>
                <?php
                }
                ?>
                <input name="total" value="<?php echo ($total + ($total * 0.19)) ?>" type="hidden"></h3>
            </table>
            <br>
            <br>
        </form>
    </div>
    <div class="barra_factura">
        <h3 class="total">Total: <input style="width:20vh;height:30px;font-size:17px;color:rgb(75, 192, 114)" value="$<?php echo number_format(($total + ($total * 0.19))) ?>" type="text" readonly="readonly"></h3>
        <form action="../../controlador/Procesos.php" method="post">
            <input style="padding:20px 28px;margin-left:5px" class="boton false" type="submit" name="borrar" value="Borrar">
        </form>
        <button class="boton true" type="submit" form="factura" name="facturar">Facturar</button>
    </div>
</div>

<!-- ////////////////////////////////////////////////////////////////////////Tabla Productos -->

<div class="contenido" style="float:rigth;max-height:60%;min-height:60%">
    <div class="box" style="width:84%;border-radius:10px;overflow-y: auto;min-height:76vh;max-height: 77.5vh;">
        <?php
        if (isset($_GET['caja'])) {
            echo '
        <a class="reset" style="float:left;margin-left:30px;padding:1vh 1.5vh" href="?"><i style="font-size:3vh" class="bi bi-arrow-clockwise"></i></a>
        ';
        }
        ?>
        <form action="">
            <div class="barra" style="margin-bottom:5%;float:right;margin-right:5%">
                <i style="font-size:2.5vh;" class='bi bi-search'></i>
                <input style="box-shadow:none;width:70%" type="text" name="caja" autocomplete="off">
            </div>
        </form>
        <table style="width:90%;margin: 0 auto;margin-top:-15px" id="tablaSalida">
            <tr>
                <td>
                    <h3>ID</h3>
                </td>
                <td>
                    <h3>Nombre</h3>
                </td>
                <td>
                    <h3>Existencias</h3>
                </td>
                <td>
                    <h3>Cantidad</h3>
                </td>
            </tr>
            <?php
            if (empty($_GET['caja'])) {
                $data = mostrarDatos("Productos", "", 0, "");
            } else {
                $data = mostrarDatos("Productos", "", 0, $_GET['caja']);
            }
            $dataAjustes = mostrarDatosAjustes();
            $longitud = count($data);
            for ($i = 0; $i < $longitud; $i++) {
            ?>
                <form id="productos<?php echo $i ?>" action="../../controlador/Procesos.php" method="post">
                    <tr>
                        <td><?php echo $data[$i]['ID'] ?></td>
                        <td><?php echo $data[$i]['Producto'] ?></td>
                        <td><?php echo $data[$i]['Cantidad']; ?>
                        </td>
                        <td>
                            <?php
                            if ($data[$i]['Cantidad'] == 0) {
                            ?>
                                <input title="No hay existencias" type="number" name="cant" min="0" max="<?php echo $data[$i]['Cantidad'] ?>" style="width:60px" disabled>
                            <?php
                            } else {
                            ?>
                                <input id="cant" type="number" name="cant" min="0" max="<?php echo $data[$i]['Cantidad'] ?>" style="width:60px" autocomplete="off" required>
                            <?php
                            }
                            ?>
                        </td>
                        <input id="cant2" type="hidden" name="cant2" value="<?php echo $data[$i]['Cantidad'] ?>">
                        <input id="id" type="hidden" name="id" value="<?php echo $data[$i]['ID'] ?>">
                        <input id="prc" type="hidden" name="prc" value="<?php echo $data[$i]['PrecioC'] + ($data[$i]['PrecioC'] * ($dataAjustes['ganancia'] / 100)) ?>">
                        <td>
                            <?php
                            if ($data[$i]['Cantidad'] == 0) {
                            ?>
                                <button style="color:var(--basecolor)" id="agregar_producto" disabled>-</button>
                            <?php
                            } else {
                            ?>
                                <button class="boton_factura" id="agregar_producto" type="submit" name="factura"><i class="bi bi-plus-square-fill"></i></button>
                            <?php
                            }
                            ?>
                        </td>
                    </tr>
                </form>
            <?php
            }
            ?>
        </table>
    </div>
</div>
</body>

</html>