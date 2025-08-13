<?php
$barra = false;
$pg = "Agregar";
require_once "../../menu.php";
require_once "../../controlador/Procesos.php";
$data = mostrarDatos("Productos", "", "", "");
$id = count($data) + 1;
?>
<div class="contenido">
    <div class="box">
        <form style="padding-top:40px" method="post" enctype="multipart/form-data">
            <div class="column1">
                <p>ID del producto</p>
                <p><?php echo $id ?></p><input type="hidden" name="ID" value="<?php echo $id ?>">
                <p>Nombre del producto</p><input autocomplete="off" type="text" name="nom" required>
                <p>La cantidad de cajas en el almacen</p><input autocomplete="off" type="number" min="0" name="can" required>
                <p>Precio por caja del producto</p><input autocomplete="off" type="number" min="0" name="prc" required><br>
                <p style="margin-top:30px"><a href="Productos.php?pg=0" class="false">Atras</a><input type="submit" value="Agregar" class="true" name="agregar"></p>
            </div>
            <div class="column2">
                <p>Imagen del producto</p>
                <label class="custom-file-upload" style="width:50%;margin-left:23%;padding:1.3vh;margin-top:2vh">
                    <i style="padding-right: 10px;padding-left: 10px;font-size:3vh" class="bi bi-cloud-plus"></i>
                    <input type="file" name="img" accept=".png">
                    <span>Seleccione una imagen</span>
                </label>
                <p>Categoria del producto</p>
                <select name="cat" class="select_column2">
                    <?php
                    if ($data = mostrarDatos("Categoria", "", "", "")) {
                        $longitud = count($data);
                        for ($i = 0; $i < $longitud; $i++) {
                    ?>
                            <option value="<?php echo $data[$i]['ID'] ?>"><?php echo $data[$i]['Nombre'] ?></option>
                    <?php
                        }
                    }
                    ?>
                </select>
            </div>
        </form>
    </div>
</div>
</body>
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
</script>

</html>
<!--Aqui recibe la informacion para llevarla a SQL-->
<?php
if (isset($_POST['agregar'])) {
    AgregarProducto();
}
?>