<?php
$barra = false;
if (isset($_GET['pg'])) {
    $p = $_GET['pg'];
    $pg = "Panel de control - Gráficas";
} else {
    $p = 0;
    $pg = "Panel de control";
}
require_once "../../menu.php";
require_once "../../controlador/Procesos.php";
if (isset($_SESSION['sms'])) {
    $mensajes = $_SESSION['sms'];
} else {
    $mensajes = null;
}
$dataPanel =  panelDeControl();
if ($p == 0) {
?>
    <div class="contenido" style="margin-bottom:-1vh">
        <div class="box" style="max-width: 40%;min-width: 50%;min-height:42vh;margin-right: 2vh;padding:20px;">
            <a href="?pg=1&f=W" title="Ver graficas"><i style="float:right" class="bi bi-chevron-right"></i></a>
            <?php include "grafica.php"; ?>
        </div>
        <div class="box" style="max-width: 39%;min-height:39.4vh;max-height:39.4vh;animation: inicial 1s">
            <?php
            $sms = ($mensajes != null) ? count($mensajes) : 0;
            if ($sms != 0) {
                for ($i = 0; $i != $sms; $i++) {
                    $producto = $mensajes[$i]['Producto'];
                    echo '
                    <div class="alert"> El producto ' . $producto . ' se esta acabando <i style="font-size:2.5vh" class="bi bi-exclamation-triangle"></i></div>
                    ';
                }
            } else {
                echo '
                <div style="color:gray">
                    <br>
                    <i style="font-size: 17vh" class="bi bi-envelope-open"></i>
                    <br>
                    <h3>No hay notificaciones nuevas</h3>
                </div>
                ';
            }
            ?>
        </div>
    </div>
    <div class="contenido" style="animation: inicial 1.5s;">
        <div class="box panel">
            <label><i class="bi bi-truck"></i></label><br><br>
            <label style="color:gray">Entradas</label><br><br>
            <input class="input_panel" style="color:rgba(54, 162, 235, 1) !important;" type="text" value="<?php echo $dataPanel['entrada'] ?>" disabled>
        </div>
        <div class="box panel">
            <label><i class="bi bi-cart3"></i></label><br><br>
            <label style="color:gray">Ventas</label><br><br>
            <input class="input_panel" type="text" value="<?php echo $dataPanel['salida'] ?>" disabled>
        </div>
        <div class="box panel">
            <label><i class="bi bi-exclamation-triangle"></i></label><br><br>
            <label style="color:gray">Productos con stock bajo</label><br><br>
            <input class="input_panel" style="color:rgba(255, 99, 132, 1) !important;" type="text" value="<?php echo $sms ?>" disabled>
        </div>
        <div class="box panel" style="margin-right:0;">
            <label><i class="bi bi-cash"></i></label><br><br>
            <label style="color:gray">Ganancias de hoy</label><br><br>
            <input class="input_panel" style="max-width: 23vh;" type="text" value="$<?php echo  number_format($dataPanel['ganancias']) ?>" disabled>
        </div>
    </div>
<?php
} else {
    include "grafica.php";
}
?>