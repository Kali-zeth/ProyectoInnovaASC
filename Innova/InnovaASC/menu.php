<?php
session_start();
if (empty($_SESSION['session'])) {
    header("location:../ajustes/sesion.php?pg=0");
} else {
    $session = $_SESSION['session'];
    if (isset($_SESSION['sms'])) {
        $mensajes = $_SESSION['sms'];
    }
}
require_once "../../controlador/Procesos.php";
$dataAjustes = mostrarDatosAjustes();
$dataUser = mostrarDatos("usuario", $session[0]['user'], "", "",);
$dataPermisos = mostrarDatos("permisos", $session[0]['user'], "", "");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="../../style.css?ts=<?= time() ?>" />
    <link rel="shortcut icon" href="../../imagenes/icon.png?ts=<?= time() ?>" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.0/chart.min.js" integrity="sha512-TW5s0IT/IppJtu76UbysrBH9Hy/5X41OTAbQuffZFU6lQ1rdcLHzpU5BzVvr/YFykoiMYZVWlr/PX1mDcfM9Qg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <title><?php echo $dataAjustes['Nombre'] ?></title>
</head>
<script type="text/javascript">
    $(window).on('load', function() {
        $('#pantalla_carga').css('display', 'none');
    })
    if (<?php echo $session[0]['modo'] ?> == 1) {
        document.documentElement.style.setProperty('--baseColor', '#35363a');
        document.documentElement.style.setProperty('--sombra', '#2d2e31');
        document.documentElement.style.setProperty('--titulo', '#B4B4B4');
        document.documentElement.style.setProperty('--texto', '#B4B4B4');
        document.documentElement.style.setProperty('--texto2', '#3F4040');
        document.documentElement.style.setProperty('--texto2', '#B4B4B4');
        document.documentElement.style.setProperty('--input', 'inset 5px 5px 44px #2d2e31, inset -5px -5px 4px #3d3e43');
    }
</script>

<div id="pantalla_carga">
    <div class="loader">
        <div></div>
        <div></div>
        <div></div>
    </div>
</div>

<body>
    <?php
    if ($pg != "Facturar" && $pg != "Entrada") {
    ?>
        <div id="menu">
            <ul>
                <li style="border-bottom: 2px solid var(--sombra);">
                    <div class="icono"><i class="bi bi-box-seam"></i></div>
                    <h2 id="marca"><?php echo $dataAjustes['Nombre'] ?></h2>
                    <div class="user">
                        <i style="font-size: 5vh;" class="bi bi-person"></i>
                        <label style="font-size:1.9vh;padding-left:10px;"><?php echo $dataUser[0]["Usuario"] ?></label>
                        <div id="submenu">
                            <a href="../ajustes/ajustes.php">Ajustes</a>
                            <a href="../../controlador/Procesos.php?bs=0">Cerrar sesion</a>
                        </div>
                    </div>
                </li>
                <li style="padding-top: 15px;"><a id="panel" href="../ajustes/panel.php"><i class="bi bi-clipboard-data"></i> Panel de control</a></li>
                <li>
                    <a id="Productos" href="../productos/Productos.php?"> <i class="bi bi-card-list"></i> Productos</a>
                    <ul>
                        <?php
                        $dataCategoria = mostrarDatos("categoria", "", "", "");
                        for ($i = 0; $i < count($dataCategoria); $i++) {
                        ?>
                            <li><a href="../productos/Productos.php?pg=<?php echo $dataCategoria[$i]['ID'] ?>"><?php echo $dataCategoria[$i]['Nombre'] ?></a></li>
                        <?php
                        }
                        ?>
                    </ul>
                </li>
                <?php
                if ($dataPermisos[0]["Clientes"]) {
                    echo '
                    <li><a id="Clientes" href="../clienteProveedor/Clientes.php"><i class="bi bi-person"></i> Clientes</a></li>
                    ';
                }
                if ($dataPermisos[0]['Proveedores']) {
                    echo '
                    <li><a id="Proveedores" href="../clienteProveedor/Proveedores.php"><i class="bi bi-truck"></i> Proveedores</a></li>
                    ';
                }
                ?>
                <li>
                    <a id="facturas" href="../facturar/facturas.php?p=0"><i class="bi bi-receipt"></i> Facturas</a>
                    <ul>
                        <li><a href="../facturar/facturas.php?p=0">Salida</a></li>
                        <li><a href="../facturar/facturas.php?p=1">Entrada</a></li>
                    </ul>
                </li>
                <li><a id="historial" href="../historial/historial.php"><i class="bi bi-clock-history"></i> Historial</a></li>
                <li><a id="" target="_blank" href="http://localhost:8082/proyecto/vista/ajustes/manual.pdf" style="color:peru">Acerca de</a></li>
            </ul>
            <?php
            if ($dataPermisos[0]['Facturar']) {
                echo '
                <form action="../facturar/Salida.php">
                <button class="facturar">Facturar</button>
                </form>
                ';
            }
            ?>
        </div>
    <?php
    } else {
    ?>
        <div id="menu" class="drop">
            <ul>
                <li>
                    <div class="icono" style="margin-left:-18px;width:80%;margin-bottom:3vh"><i style="font-size:5vh;" class="bi bi-box-seam"></i></div>
                </li>
                <li style="border-bottom: 2px solid var(--sombra);margin-left:-5vh">
                    <div class="user" style="margin-left:2.5vh;width:35%;margin-bottom:2vh;height:5vh">
                        <i style="font-size: 4vh;" class="bi bi-person"></i>
                        <div id="submenu" style="margin-left:7vh;padding-left:5vh;">
                            <a style="margin-bottom:0vh" href="../ajustes/ajustes.php">Ajustes</a>
                            <a style="margin-bottom:0vh" href="../../controlador/Procesos.php?bs=0">Cerrar sesion</a>
                        </div>
                    </div>
                </li>
                <li style="padding-top: 20px;"><a id="panel" href="../ajustes/panel.php"><i class="bi bi-clipboard-data"></i></a></li>
                <li>
                    <a title="Productos" href="../productos/Productos.php?pg=0"> <i class="bi bi-card-list"></i> </a>
                    <ul>
                        <?php
                        $dataCategoria = mostrarDatos("categoria", "", "", "");
                        for ($i = 0; $i < count($dataCategoria); $i++) {
                        ?>
                            <li><a href="../productos/Productos.php?pg=<?php echo $dataCategoria[$i]['ID'] ?>"><?php echo $dataCategoria[$i]['Nombre'] ?></a></li>
                        <?php
                        }
                        ?>
                    </ul>
                </li>
                <?php
                if ($dataPermisos[0]["Clientes"]) {
                    echo '
                    <li><a title="Clientes" href="../clienteProveedor/Clientes.php"><i class="bi bi-person-fill"></i> </a></li>
                    ';
                }
                if ($dataPermisos[0]['Proveedores']) {
                    echo '
                    <li><a title="Proveedores" href="../clienteProveedor/Proveedores.php"><i class="bi bi-truck"></i> </a></li>
                    ';
                }
                ?>
                <li>
                    <a title="Facturas" href="../facturar/facturas.php?p=0"><i class="bi bi-receipt"></i> </a>
                    <ul>
                        <li><a href="../facturar/facturas.php?p=0">Salida</a></li>
                        <li><a href="../facturar/facturas.php?p=1">Entrada</a></li>
                    </ul>
                </li>
                <li><a title="Historial" href="../historial/historial.php"><i class="bi bi-clock-history"></i> </a></li>
                <li><a id="" href="" style="color:peru"><i class="bi bi-info-circle"></i></a></li>
                <?php
                if ($dataPermisos[0]['Facturar']) {
                    echo '
                    <li><a title="Facturar" href="../facturar/Salida.php"><i class="bi bi-receipt-cutoff"></i></a></li>
                    ';
                }
                ?>
            </ul>

        </div>
    <?php
    }
    ?>
    <div class="BrArriba">
        <h2 class='titulo'><?php echo $pg ?></h2>
        <?php
        if ($barra) {
        ?>
            <form method="post">
                <?php
                if (isset($_POST['caja'])) {
                ?>
                    <a class="false reset" href="">Reset</a>
                <?php
                } elseif (isset($_GET['hs'])) {
                    $p = $_GET['p'];
                ?>
                    <a class="false reset" href="facturas.php?p=<?php echo $p ?>">Reset</a>
                <?php
                }
                ?>
                <div class="barra" id="barra">
                    <i style="font-size:2.5vh;" class='bi bi-search'></i>
                    <input autocomplete="off" id="buscar" type="text" name="caja" placeholder="Buscar">
                </div>
            </form>
        <?php
        } elseif ($pg == "Historial") {
        ?>
            <form method="post">
                <?php
                if (isset($_POST['caja'])) {
                ?>
                    <a class="false reset" href="">Reset</a>
                <?php
                }
                ?>
                <div class="barra">
                    <i style="font-size:2.5vh;" class='bi bi-search'></i>
                    <input autocomplete="off" id="buscar" type="text" name="caja" placeholder="dd-mm-aaaa">
                </div>
            </form>
        <?php
        }
        ?>
    </div>
    <script>
        function sms() {
            let element = document.getElementById('sms');
            let envelope = document.getElementById('envelope');
            if (envelope.className == 'bi bi-envelope') {
                envelope.className = "bi bi-envelope-open";
                $('#sms').css('display', 'block');
            } else {
                envelope.className = "bi bi-envelope";
                $('#sms').css('display', 'none');
            }
        }
        var pagina1 = window.location.pathname.split('/');
        var pagina = pagina1[4].split('.');
        $('#' + pagina[0]).addClass('active');
    </script>
    </div>

    <?php
    // conexion
    date_default_timezone_set("America/Bogota");
    ?>