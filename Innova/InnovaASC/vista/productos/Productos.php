<!-- Aquí agrega el formato de la pagina-->
<?php
$barra = true;
$pg = "Productos";
require_once "../../controlador/Procesos.php";
if (isset($_REQUEST['pg'])) {
  $pg2 = $_REQUEST['pg'];
  if ($pg2 != 0) {
    $dataCategoria = mostrarDatos("categoria", $pg2, "", "");
    $pg = $dataCategoria[0]['Nombre'];
  } else {
    $pg = "Productos";
    $pg2 = 0;
  }
} else {
  $pg = "Productos";
  $pg2 = 0;
}
require_once "../../menu.php";
$dataAjustes = mostrarDatosAjustes();
?>
<div class="botones" id="botones">
  <?php
  if ($dataPermisos[0]['Agregar']) {
    echo '<a href="agregar.php" class="boton"><i class="bi bi-plus-circle"></i><label for="">Agregar</label></a>';
  }
  if ($dataPermisos[0]['Entrada']) {
    echo '
      <a href="../Facturar/Entrada.php" class="boton"><i class="bi bi-truck"></i><label for="">Entrada</label></a>
      ';
  }
  if ($dataPermisos[0]['Editar']) {
  ?>
    <button class="boton" onclick="TablaEditar()" id="editar"><i class="bi bi-pencil-square"></i><label for="">Editar</label></button>
    <button class="boton" onclick="TablaEditarVer()" style="width:5.5%;padding: 2.1vh 2.8vh;display:none" id="atras"><i class="bi bi-arrow-return-left"></i></button>
  <?php
  }
  ?>
</div>
<?php
if (isset($_REQUEST['pro'])) {
  $id = $_REQUEST['pro'];
  if ($data = mostrarDatos("Productos", $id, $pg2, "")) {
?>
    <script>
      document.getElementById('botones').style.setProperty('justify-content', 'right');
      document.getElementById('botones').style.setProperty('padding-right', '6vh');
    </script>
    <div class="contenido" style="float:left;width:30%;margin-top:-7.8vh;margin-left:5px;min-height:80vh;margin-right:-50px;animation:none">
      <div class="box" style="min-height:65vh;width:85%;text-align:center;margin-right:20px">
        <a id="this" style="float:right;position:fixed;padding-left:12.7%" href="?pg=<?php echo $pg2 ?>"><i style="font-size:18px;color:gray" class="bi bi-arrow-bar-right"></i></a>
        <div style="width:100%">
          <img style="width:70%" src="../../imagenes/<?php echo $data[0]['Imagen']; ?>" />
        </div>
        <div class="box" style="min-height: 40%;max-width:25.5%;padding-top:20px;position:absolute">
          <b>ID del producto</b>
          <p><?php echo $id ?></p>
          <b>Nombre del producto</b>
          <p><?php echo $data[0]['Producto'] ?></p>
          <b>Cantidad de cajas en el almacen</b>
          <p> <?php echo $data[0]['Cantidad'] ?></p>
          <b>Precio de compra</b>
          <p><?php echo number_format($data[0]['PrecioC']) ?></p>
          <b>Precio de venta</b>
          <p><?php echo number_format($data[0]['PrecioC'] + ($data[0]['PrecioC'] * ($dataAjustes['ganancia'] / 100))) ?></p>
          <b>Categoria</b>
          <p><?php echo $data[0]['Categoria'] ?></p>
        </div>
      </div>
    </div>
<?php
  }
}
?>
<div class="contenido">
  <div class="box">
    <table id="tabla2" style="width:92%;margin: 0 auto;">
      <tr>
        <th>
          <h3>ID</h3>
        </th>
        <th>
          <h3>Nombre</h3>
        </th>
        <th>
          <h3>Cantidad en almacen</h3>
        </th>
        <th>
          <h3>Precio de venta</h3>
        </th>
      </tr>
      <?php
      if (isset($_POST['caja'])) {
        $codigo = $_POST['caja'];
        if (mostrarDatos("Productos", "", $pg2, $codigo)) {
          $datos = true;
          $data = mostrarDatos("Productos",  "", $pg2, $codigo);
        } else {
          $datos = false;
        }
      } else {
        if (mostrarDatos("Productos", "", $pg2, "")) {
          $datos = true;
          $data = mostrarDatos("Productos", "", $pg2, "");
        } else {
          $datos = false;
        }
      }
      if ($datos) {
        $longitud = count($data);
        for ($i = 0; $i < $longitud; $i++) {
      ?>
          <td><?php echo $data[$i]['ID'] ?></td>
          <td><?php echo $data[$i]['Producto'] ?></td>
          <?php
          if ($data[$i]['Cantidad'] < $dataAjustes['stock']) {
            echo "<td style='color:tomato' title='Existencias bajas'>";
          } else {
            echo "<td>";
          }
          echo $data[$i]['Cantidad']
          ?>
          </td>
          <td><?php echo number_format($data[$i]['PrecioC'] + ($data[$i]['PrecioC'] * ($dataAjustes['ganancia'] / 100))) ?></td>
          <?php
          if ($dataPermisos[0]['Editar']) {
            echo '
            <td class="cn" hidden>
            <button type="submit" class="eliminar" onclick="Eliminar(' . $data[$i]['ID'] . ')"><i class="bi bi-x"></i></button>
            <form name="editar" action="editar.php" method="post">
              <input type="hidden" name="id" value="' . $data[$i]['ID'] . '">
              <button type="submit" class="modificar" style="float:left"><i class="bi bi-pencil-square"></i></button>
            </form>
            </td>
            ';
          }
          ?>
          <td class="informacion"><a title="Informacion" href="Productos.php?pg=<?php echo $pg2 ?>&pro=<?php echo $data[$i]['ID'] ?>"><i class="bi bi-info-circle"></i></a></td>
          </tr>
        <?php
        }
      } else {
        ?>
        <td>-</td>
        <td>-</td>
        <td>-</td>
        <td>-</td>
      <?php
      }
      if (isset($_GET['El'])) {
        $id = $_GET['El'];
        EliminarProducto($id);
      }
      ?>
    </table>
  </div>
</div>
<script>
  function TablaEditar() {
    $('.cn').show();
    $('#atras').show();
    $('.informacion').hide();
    $('#editar').hide();
  }

  function TablaEditarVer() {
    $('.cn').hide();
    $('#atras').hide();
    $('.informacion').show();
    $('#editar').show();
  }

  function Eliminar(id) {
    event.preventDefault();
    Swal.fire({
      title: 'Seguro que quiere eliminiar este producto?',
      icon: 'warning',
      confirmButtonText: 'Si, eliminar',
      buttonsStyling: false,
      background: 'var(--baseColor)',
      customClass: {
        confirmButton: 'boton true',
      }
    }).then((result) => {
      if (result.isConfirmed) {
        window.location.href = "?AC=2&El=" + id;
      }
    })
  }
</script>

</html>