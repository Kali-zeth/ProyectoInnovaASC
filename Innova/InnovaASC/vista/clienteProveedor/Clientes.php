<?php
$barra = true;
$accion = 2;
$pg = "Clientes";
require_once "../../menu.php";
require_once "../../controlador/Procesos.php";
if (!$dataPermisos[0]["Clientes"]) {
  echo '
  <script type="text/javascript">
    window.history.go(-1);
  </script>
  ';
}
if (isset($_POST['IDE']) || isset($_POST['editar']) || isset($_POST['AC']) || isset($_POST['agregar'])) {
  if (isset($_POST['IDE']) || isset($_POST['editar'])) {
    $accion = 1;
  } elseif (isset($_POST['AC']) || isset($_POST['agregar'])) {
    $accion = 0;
  }
  echo "
  <script>
    $('#tabla').css('max-height', '75vh');
  </script>
  ";
  if ($accion == 1) {
    if (isset($_POST['editar'])) {
      EditarCliente();
    } else {
      $id = $_POST['IDE'];
      if ($data = mostrarDatos("clientes", "", $id, "")) {
?>
        <div class="contenido" style="float:right;width:30%;margin-left:-30px;animation:drop 0.7s">
          <div class="box" style="padding-top:20px;max-height:76vh;">
            <form action="Clientes.php" method="post" enctype="multipart/form-data">
              <div>
                <p>CC/NIT cliente</p>
                <p><input autocomplete="off" type="text" name="ID" value="<?php echo $id ?>"></p><input type="hidden" name="ID2" value="<?php echo $id ?>">
                <p>Nombre </p><input autocomplete="off" type="text" name="NOM" required value="<?php echo $data[0]['Nombre'] ?>">
                <p>Telefono </p><input autocomplete="off" type="text" name="TEL" required value="<?php echo $data[0]['Telefono'] ?>">
                <p>Direccion</p><input autocomplete="off" type="text" name="DIREC" required value="<?php echo $data[0]['Direccion'] ?>">
                <p>Ciudad </p><input autocomplete="off" type="text" name="CIUDAD" required value="<?php echo $data[0]['Ciudad'] ?>">
                <p>Correo </p><input autocomplete="off" type="email" pattern=".+@gmail\.com" name="CORREO" required value="<?php echo $data[0]['Correo'] ?>">
                <p style="padding-top:10px;"><input type="submit" value="Editar" class="true" name="editar"> <a href="Clientes.php" class="false">Atras</a> </p>
              </div>
            <?php
          }
            ?>
          </div>
          </form>
        </div>
      <?php
    }
  } elseif ($accion == 0) {
      ?>
      <div class="contenido" style="float:right;width:30%;margin-left:-30px;animation:drop 0.7s">
        <div class="box" style="padding-top:20px;max-height:76vh;">
          <form action="Clientes.php" method="post" enctype="multipart/form-data">
            <div>
              <p>CC/NIT cliente</p><input autocomplete="off" type="text" name="ID" required>
              <p>Nombre </p><input autocomplete="off" type="text" name="NOM" required>
              <p>Telefono</p><input autocomplete="off" type="text" name="TEL" required>
              <p>Direccion</p><input autocomplete="off" type="text" name="DIREC" required><br>
              <p>Ciudad</p><input type="text" name="CIUDAD" required><br>
              <p>Correo</p><input autocomplete="off" type="email" name="CORREO" required><br>
              <p style="padding-top:10px;"><input type="submit" value="Agregar" class="true" name="agregar"> <a href="Clientes.php" class="false">Atras</a> </p>
            </div>
          </form>
        </div>
      </div>
    <?php
    if (isset($_POST['agregar'])) {
      AgregarCliente();
    }
  }
} else {
    ?>
    <div class="botones">
      <form method="post">
        <button class="boton" name="AC"><i class="bi bi-plus-circle"></i><label for="">Agregar</label></button>
      </form>
    </div>
  <?php
}
  ?>
  <div class="contenido">
    <div class="box" id="tabla" style="width:auto;min-width:90%;max-width:95%">
      <table style="width:95%;margin: 0 auto;" color="black">
        <tr>
          <td>
            <h3>CC/NIT</h3>
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
          <td>
            <h3>Correo</h3>
          </td>
        </tr>
        <?php
        if (isset($_POST['caja'])) {
          $codigo = $_POST['caja'];
          if (mostrarDatos("clientes", "", "", $codigo)) {
            $datos = true;
            $data = mostrarDatos("clientes",  "", "", $codigo);
          } else {
            $datos = false;
          }
        } else {
          if (mostrarDatos("clientes", "", "", "")) {
            $datos = true;
            $data = mostrarDatos("clientes", "", "", "");
          } else {
            $datos = false;
          }
        }
        if ($datos) {
          $longitud = count($data);
          for ($i = 0; $i < $longitud; $i++) {
        ?>
            <tr>
              <td><?php echo $data[$i]['ID'] ?></td>
              <td><?php echo $data[$i]['Nombre'] ?></td>
              <td><?php echo $data[$i]['Telefono'] ?></td>
              <td><?php echo $data[$i]['Direccion'] ?></td>
              <td><?php echo $data[$i]['Ciudad'] ?></td>
              <td><?php echo $data[$i]['Correo'] ?>
                <?php
                if ($accion == 2) {
                ?>
              <td class="cn">
                <form name="eliminar" action="Modificar.php" method="post">
                  <button type="submit" class="eliminar" onclick="return Eliminar('<?php echo $data[$i]['ID'] ?>')"><i class="bi bi-x"></i></button>
                </form>
                <form name="editar" method="post">
                  <input type="hidden" name="IDE" value="<?php echo $data[$i]['ID'] ?>">
                  <button type="submit" class="modificar" style="float:left"><i class="bi bi-pencil-square"></i></button>
                </form>
              </td>
            <?php
                }
            ?>
            </td>
            </tr>
        <?php
          }
        }
        if (isset($_GET['id'])) {
          $id = $_GET['id'];
          EliminarCliente($id);
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
        title: 'Seguro que quiere eliminiar este cliente?',
        icon: 'warning',
        confirmButtonText: 'Si, eliminar',
        buttonsStyling: false,
        background: 'var(--baseColor)',
        customClass: {
          confirmButton: 'boton true',
        }
      }).then((result) => {
        if (result.isConfirmed) {
          window.location.href = "Clientes.php?id=" + id;
        }
      })
    }
  </script>

  </html>