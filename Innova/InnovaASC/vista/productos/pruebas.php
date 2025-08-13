<?php
$pg = 0;
$contadorTotal = 0;
$ventasTotales = 0;
require_once "../../controlador/Procesos.php";
$data = mostrarDatosFactura(0, 2, "2022-01", "");
$count = ($data != null) ? count($data) : 0;
echo $count;

