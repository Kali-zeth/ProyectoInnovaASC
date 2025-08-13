<?php
$barra = true;
$accion = 2;
$pg = "Proveedores";
require_once "../../menu.php";
require_once "../../controlador/Procesos.php";
if (!$dataPermisos[0]["Proveedores"]) {
  echo '
  <script type="text/javascript">
    window.history.go(-1);
  </script>
  ';
}
if (isset($_POST['NIT']) || isset($_POST['editar']) || isset($_POST['AC']) || isset($_POST['agregar'])) {
  if (isset($_POST['NIT']) || isset($_POST['editar'])) {
    $accion = 1;
  } elseif (isset($_POST['AC']) || isset($_POST['agregar'])) {
    $accion = 0;
  }
  if ($accion == 1) {
    if (isset($_POST['editar'])) {
      EditarProveedor();
    } else {
      $ID = $_POST['NIT'];
      if ($data = mostrarDatos("proveedores", "", $ID, "")) {
        $ccat = $data[0]['NIT'];
?>
        <div class="contenido" style="float:right;width:30%;margin-left:-30px">
          <div class="box" style="padding-top:20px;max-height:76vh;">
            <form action="Proveedores.php" method="post" enctype="multipart/form-data">
              <div>
                <p>NIT</p>
                <p><input autocomplete="off" type="text" name="ID" value="<?php echo $ID ?>"></p><input type="hidden" name="ID2" value="<?php echo $ID ?>">
                <p>Nombre </p><input autocomplete="off" type="text" name="NOM" required value="<?php echo $data[0]['Nombre'] ?>">
                <p>Telefono </p><input autocomplete="off" type="text" name="TEL" required value="<?php echo $data[0]['Telefono'] ?>">
                <p>Direccion</p><input autocomplete="off" type="text" name="DIREC" required value="<?php echo $data[0]['Direccion'] ?>">
                <p>Ciudad </p><input autocomplete="off" type="text" name="CIUDAD" required value="<?php echo $data[0]['Ciudad'] ?>">
                <p style="margin-top:30px;margin-bottom:25px"> <input type="submit" value="Editar" class="true" name="editar"> <a href="proveedores.php" class="false">Atras</a> </p>
              </div>
          </div>
          </form>
        </div>
    <?php
      }
    }
  } elseif ($accion == 0) {
    ?>
    <div class="contenido" style="float:right;width:30%;margin-left:-30px">
      <div class="box" style="padding-top:20px;max-height:76vh;">
        <form action="Proveedores.php" method="post" enctype="multipart/form-data">
          <div>
            <p>NIT</p><input autocomplete="off" type="text" name="ID" required>
            <p>Nombre </p><input autocomplete="off" type="text" name="NOM" required>
            <p>Telefono</p><input autocomplete="off" type="text" name="TEL" required>
            <p>Direccion</p><input autocomplete="off" type="text" name="DIREC" required><br>
            <p>Ciudad</p><input type="text" name="CIUDAD" required><br>
            <p style="margin-top:30px;margin-bottom:25px"> <input type="submit" value="Agregar" class="true" name="agregar"> <a href="proveedores.php" class="false">Atras</a> </p>
          </div>
        </form>
      </div>
    </div>
  <?php
    if (isset($_POST['agregar'])) {
      AgregarProveedor();
    }
  }
} else {
  ?>
  <form method="post">
    <div class="botones">
      <button class="boton" name="AC"><i class="bi bi-plus-circle"></i><label for="">Agregar</label></button>
    </div>
  </form>
<?php
}
?>
<div class="contenido">
  <div class="box" id="tabla">
    <table style="width:90%;margin: 0 auto;" color="black">
      <tr>
        <td>
          <h3>NIT</h3>
        </td>
        <td>
          <h3>Nombre</h3>
        </td>
        <td>
          <h3>Telefono</h3>
        </td>
        <td>
          <h3>Direccion</h3>
        </td>
        <td>
          <h3>Ciudad</h3>
        </td>
      </tr>
      <?php
      if (isset($_POST['caja'])) {
        $codigo = $_POST['caja'];
        if (mostrarDatos("Proveedores", "", "", $codigo)) {
          $datos = true;
          $data = mostrarDatos("Proveedores",  "", "", $codigo);
        } else {
          $datos = false;
        }
      } else {
        if (mostrarDatos("Proveedores", "", "", "")) {
          $datos = true;
          $data = mostrarDatos("Proveedores", "", "", "");
        } else {
          $datos = false;
        }
      }
      if ($datos) {
        $longitud = count($data);
        for ($i = 0; $i < $longitud; $i++) {
      ?>
          <tr>
            <td><?php echo $data[$i]['NIT'] ?></td>
            <td><?php echo $data[$i]['Nombre'] ?></td>
            <td><?php echo $data[$i]['Telefono'] ?></td>
            <td><?php echo $data[$i]['Direccion'] ?></td>
            <td><?php echo $data[$i]['Ciudad'] ?></td>
            <?php
            if ($accion == 2) {
            ?>
              <td class="cn">
                <form name="eliminar" action="Modificar.php" method="post">
                  <button type="submit" class="eliminar" onclick="return Eliminar('<?php echo $data[$i]['NIT'] ?>')"><i class="bi bi-x"></i></button>
                </form>
                <form name="editar" method="post">
                  <input type="hidden" name="NIT" value="<?php echo $data[$i]['NIT'] ?>">
                  <button type="submit" class="modificar" style="float:left"><i class="bi bi-pencil-square"></i></button>
                </form>
              </td>
            <?php
            }
            ?>
          </tr>
      <?php
        }
      }
      if (isset($_GET['id'])) {
        $id = $_GET['id'];
        EliminarProveedor($id);
      }
      if (isset($_POST['IDE']) || isset($_POST['editar']) || isset($_POST['AC']) || isset($_POST['agregar'])) {
        echo "
        <script>
          $('#tabla').css({
            'max-height':'75vh',
            'min-height':'75vh',
          });
        </script>
        ";
      }
      ?>
    </table>
  </div>

</div>
</body>
<script>
  function Eliminar(id) {
    event.preventDefault();
    Swal.fire({
      title: 'Seguro que quiere eliminiar este proveedor?',
      icon: 'warning',
      confirmButtonText: 'Si, eliminar',
      buttonsStyling: false,
      background: 'var(--baseColor)',
      customClass: {
        confirmButton: 'boton true',
      }
    }).then((result) => {
      if (result.isConfirmed) {
        window.location.href = "proveedores.php?id=" + id;
      }
    })
  }
</script>

</html>