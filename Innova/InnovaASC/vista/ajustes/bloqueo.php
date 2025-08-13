<?php
date_default_timezone_set("America/Bogota");
session_start();
$Final = $_SESSION['bloqueo']['final'];
$tiempoFinal = strtotime($Final);
$tiempoActual = strtotime(date("Y-m-d H:i:s",time()));
$contador =(($tiempoFinal - $tiempoActual) * 1000) - 3;
if ($tiempoActual >= $tiempoFinal) {
    unset($_SESSION['bloqueo']);
    header('location:sesion.php?pg=0');
}
?>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../../style.css?ts=<?= time() ?>" />
    <link rel="shortcut icon" href="../../imagenes/icon.png?ts=<?= time() ?>" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <title>Bloqueo</title>
</head>
<div class="contenido visualizacion_factura" style="animation:none">
    <div class="ventana" style="padding:10vh 18vh;grid-template-columns: auto;box-shadow:none;grid-template-rows: auto auto auto;">
        <i style="font-size: 30vh;grid-column-start: 1;grid-row-start: 1;" class="bi bi-exclamation-triangle-fill"></i>
        <h1 style="grid-column-start: 1;grid-row-start: 2;">¡Bloqueado!</h1>
        <p style="grid-column-start: 1;grid-row-start: 3;">Ha sido bloqueado por superar el número maximo de intentos</p>
        <section>
            <p>
                <b><span id="minutes"></span> minutos / <span id="seconds"></span> segundos</b>
            </p>
        </section>
        <!-- Fin contador regresivo -->
        <script>
            setTimeout('document.location.reload()',<?php echo $contador ?>);
            document.addEventListener('DOMContentLoaded', () => {
                //===
                // VARIABLES
                //===
                const DATE_TARGET = new Date('<?php echo $Final ?>');
                // DOM for render
                const SPAN_DAYS = document.querySelector('span#days');
                const SPAN_HOURS = document.querySelector('span#hours');
                const SPAN_MINUTES = document.querySelector('span#minutes');
                const SPAN_SECONDS = document.querySelector('span#seconds');
                // Milliseconds for the calculations
                const MILLISECONDS_OF_A_SECOND = 1000;
                const MILLISECONDS_OF_A_MINUTE = MILLISECONDS_OF_A_SECOND * 60;
                const MILLISECONDS_OF_A_HOUR = MILLISECONDS_OF_A_MINUTE * 60;
                const MILLISECONDS_OF_A_DAY = MILLISECONDS_OF_A_HOUR * 24
                function updateCountdown() {
                    // Calcs
                    const NOW = new Date()
                    const DURATION = DATE_TARGET - NOW;
                    const REMAINING_DAYS = Math.floor(DURATION / MILLISECONDS_OF_A_DAY);
                    const REMAINING_HOURS = Math.floor((DURATION % MILLISECONDS_OF_A_DAY) / MILLISECONDS_OF_A_HOUR);
                    const REMAINING_MINUTES = Math.floor((DURATION % MILLISECONDS_OF_A_HOUR) / MILLISECONDS_OF_A_MINUTE);
                    const REMAINING_SECONDS = Math.floor((DURATION % MILLISECONDS_OF_A_MINUTE) / MILLISECONDS_OF_A_SECOND);
                    // Render
                    SPAN_MINUTES.textContent = REMAINING_MINUTES;
                    SPAN_SECONDS.textContent = REMAINING_SECONDS;
                }
                //===
                // INIT
                //===
                updateCountdown();
                // Refresh every second
                setInterval(updateCountdown, MILLISECONDS_OF_A_SECOND);
            });
        </script>
    </div>
</div>