<!-- Aquí agrega el formato de la pagina-->
<?php
$barra = false;
$pg = "Entrada";
require_once "../../menu.php";
if (isset($_SESSION['entrada'])) {
    $entrada = $_SESSION['entrada'];
    $_SESSION['entrada'] = $entrada;
}
if (!$dataPermisos[0]["Entrada"]) {
    echo '
    <script type="text/javascript">
        window.history.go(-1);
    </script>
    ';
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
        }).then(() => {
            window.location.href = 'Entrada.php';
        });
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
        <form action="facturaEntrada.php" id="factura" method="post" style="min-width:92%">
            <?php
            if ($data = mostrarDatosFactura(1, 6, "", "")) {
                $count = $data[0]['ID'] + 1;
            }
            ?>
            <input type="hidden" name="IDD" value="<?php echo $count; ?>">
            <h3 style="padding-right: 40%;">Nro Factura: <i style="color:gray"><input type="number" name="NroFac" autocomplete="off" required></i></h3>
            <div class="cliente">
                <i style="font-size: 17px;float:left;padding-left:20px" class="bi bi-person-circle">
                    <?php
                    if (isset($_GET['pr'])) {
                        echo "Proveedor nuevo";
                    } else {
                    ?>
                        <input type="hidden" name="ciu" value="Bogota D.C">
                        <input type="hidden" value="<?php echo date('Y-m-j'); ?>" name="fech">
                        <select name="pr" class="select_persona">
                            <?php
                            $data = mostrarDatos("proveedores", "", "", "");
                            $longitud = count($data);
                            for ($i = 0; $i < $longitud; $i++) {
                                echo "<option value='" . $data[$i]['NIT'] . "'>" . $data[$i]['Nombre'] . "</option>";
                            }
                            ?>
                        </select>
                    <?php
                    }
                    ?>
                </i>
            </div>
            <?php
            if (isset($_GET['pr']) && $dataPermisos[0]['Proveedores']) {
            ?>
                <a href="Entrada.php?"><i class="bi bi-caret-up-fill"></i></a>
                <ul align="left" style="margin-top:-5px;font-size: 1.5vh;">
                    <div style="float:left;margin-right:5px">
                        <li>
                            <h3>Nombre Proveedor: <input style="width:55%" type="text" name="nom" required></h3>
                        </li>
                        <li>
                            <h3>NIT: <input style="width:86%" type="text" name="di" required></h3>
                        </li>
                        <li>
                            <h3>Telefono: <input style="width:75%" type="text" name="te" required></h3>
                        </li>
                    </div>
                    <div style="float:right;margin-right:30px;margin-bottom:25px">
                        <li>
                            <h3>Direccion: <input style="width:62%" type="text" name="dire" required></h3>
                        </li>
                        <li>
                            <h3>Ciudad: <input style="width:69%" type="text" name="ciu" value="Bogota D.C" required></h3>
                        </li>
                        <li>
                            <h3>Fecha: <input style="width:72%;text-align:right" value="<?php echo date('Y-m-j'); ?>" type="date" readonly></h3>
                        </li>
                    </div>
                    <input type="hidden" value="<?php echo date('Y-m-d'); ?>" name="fech">
                </ul>
            <?php
            } else {
                if ($dataPermisos[0]["Proveedores"]) {
                    echo '<a href="Entrada.php?pr=0"><i class="bi bi-caret-down-fill"></i></a>';
                }
            }
            ?>
            <table id="tabla" style="width:90%;margin: 0 auto;">
                <tr>
                    <th>ID</th>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Precio</th>
                </tr>
                <?php
                $total = 0;
                if (isset($_SESSION['entrada'])) {
                    for ($i = 0; $i <= count($entrada) - 1; $i++) {
                        $data = mostrarDatos("Productos", $entrada[$i]['producto'], "", "");
                ?>
                        <tr>
                            <td><?php echo $entrada[$i]['producto'] ?></td>
                            <td><?php echo $data[0]['Producto'] ?></td>
                            <input type="hidden" name="nom<?php echo $i ?>" value="<?php echo $entrada[$i]['producto'] ?>">
                            <td><input type="number" name="cant<?php echo $i ?>"></td>
                            <td><input type="number" name="prc<?php echo $i ?>"></td>
                            <td><a class="boton_factura borrar" href="../../controlador/Procesos.php?bruE=<?php echo $entrada[$i]['producto'] ?>"><i class="bi bi-x"></i></a></td>
                        </tr>
                    <?php
                    }
                    ?>
                    <input type="hidden" name="cont" value="<?php echo count($entrada) ?>">
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
            </table>
            <br>
            <br>
        </form>
    </div>
    <div class="barra_factura">
        <a href="../productos/Productos.php?pg=0" class="boton" style="width:20px;margin-right:5px;font-size:20px" name="AC" Value="0"><i class='bi bi-arrow-return-left'></i></a>
        <form action="../../controlador/Procesos.php" method="post">
            <input style="padding:20px 28px;margin-left:5px" class="boton false" type="submit" name="borrarE" value="Borrar">
        </form>
        <button style="padding:0px 28px" class="boton true" type="submit" form="factura" name="entradas">Facturar</button>
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
        <table style="width:90%;margin: 0 auto;margin-top:-15px" color="black">
            <tr>
                <td>
                    <h3>ID</h3>
                </td>
                <td>
                    <h3>Nombre</h3>
                </td>
            </tr>
            <tr style="background:var(--sombra);">
                <td></td>
                <td>Nuevo Producto</td>
                <td>
                    <form action="../productos/agregar.php">
                        <button class="boton_factura" id="agregar_producto" name="entrada"><i class="bi bi-plus-square-fill"></i></button>
                    </form>
                </td>
            </tr>
            <?php
            if (empty($_GET['caja'])) {
                $data = mostrarDatos("Productos", "", 0, "");
            } else {
                $data = mostrarDatos("Productos", "", 0, $_GET['caja']);
            }
            $longitud = count($data);
            for ($i = 0; $i < $longitud; $i++) {
            ?>
                <form id="productos" action="../../controlador/Procesos.php" method="post">
                    <tr>
                        <td><?php echo $data[$i]['ID'] ?></td>
                        <td><?php echo $data[$i]['Producto'] ?></td>
                        <input id="id" type="hidden" name="id" value="<?php echo $data[$i]['ID'] ?>">
                        <td>
                            <button id="agregar_producto" class="boton_factura" name="entrada"><i class="bi bi-plus-square-fill"></i></button>
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