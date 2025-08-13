<!-- Aquí agrega el formato de la pagina-->
<?php
$barra = false;
$pg = "Editar";
require_once "../../menu.php";
require_once "../../controlador/Procesos.php";
if (isset($_REQUEST['id'])) {
    $id = $_REQUEST['id'];
} else {
    $id = $_POST['ID'];
}
if ($data = mostrarDatos("Productos", $id, "", "")) {
?>
    <div class="contenido">
        <div class="box" style="padding-bottom:40px">
            <form style="padding-top:40px" method="post" enctype="multipart/form-data">
                <div class="column1">
                    <p>ID del producto</p>
                    <p><?php echo $id ?></p><input autocomplete="off" type="hidden" name="ID" value="<?php echo $id ?>">
                    <p>Nombre del producto</p><input autocomplete="off" type="text" name="nom" required value="<?php echo $data[0]['Producto'] ?>">
                    <p>Cantidad de cajas en el almacen</p>
                    <p> <?php echo $data[0]['Cantidad'] ?></p>
                    <p>Precio de compra del producto</p><input autocomplete="off" type="number" name="prc" required value="<?php echo $data[0]['PrecioC'] ?>"><br>
                    <p style="margin-top:30px;"> <a href="Productos.php?AC=2" class="false">Atras</a> <input type="submit" value="Guardar" class="true" name="guardar"></p>
                </div>
                <div class="column2" style="margin-top: -20px;">
                    <img style="width:55%;max-height:20vh;padding-bottom:30px" src="../../imagenes/<?php echo $data[0]['Imagen']; ?>" />
                    <input type="hidden" name="imga" value="<?php echo $data[0]['Imagen']; ?>">
                    <input type="hidden" name="nom2" value="<?php echo $data[0]['Producto'] ?>">
                    <input type="hidden" name="prc2" value="<?php echo $data[0]['PrecioC'] ?>">
                    <input type="hidden" name="cat2" value="<?php echo $data[0]['Categoria'] ?>">
                    <label class="custom-file-upload" style="width:50%;margin-left:23%;padding:1.3vh;margin-top:2vh">
                        <i style="padding-right: 10px;padding-left: 10px;font-size:3vh" class="bi bi-cloud-plus"></i>
                        <input type="file" name="img" accept=".png">
                        <span>Seleccione una imagen</span>
                    </label>
                    <p>Categoria</p>
                    <select id="categoria" name="cat" class="select_column2">
                        <?php
                        if ($categoria = mostrarDatos("Categoria", "", "", "")) {
                            $longitud = count($categoria);
                            for ($i = 0; $i < $longitud; $i++) {
                        ?>
                                <option value="<?php echo $categoria[$i]['Nombre'] ?>"><?php echo $categoria[$i]['Nombre'] ?></option>
                        <?php
                            }
                        }
                        ?>
                    </select>
                    <script>
                        $(function() {
                            $('input[type=file]').change(function() {
                                let nombre_fichero = $(this).val().split('\\').pop();
                                if (nombre_fichero === "") {
                                    $('.custom-file-upload').children('span').html("Seleccione una imagen");
                                    $('.custom-file-upload').children('i').show();
                                } else {
                                    $('.custom-file-upload').children('span').html(nombre_fichero);
                                }
                                $('.custom-file-upload').children('i').hide();
                            });
                        });
                        $("#categoria > option[value='<?php echo $data[0]["Categoria"] ?>']").attr("selected", true);
                    </script>
                </div>
            </form>
        </div>
    </div>
    </body>
<?php
}
if (isset($_POST['guardar'])) {
    EditarProducto();
}
?>

</html>