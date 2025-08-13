<?php
$barra = false;
$pg = "Ajustes";
if (isset($_GET['p'])) {
    $p = $_GET['p'];
} else {
    $p = 1;
}
require_once "../../menu.php";
require_once "../../controlador/Procesos.php";
$idUser = $session[0]['user'];
$dataUser = mostrarDatos("usuario", $idUser, "", "",);
$modo = $session[0]['modo'];
$user = $dataUser[0]["Usuario"];
$rol = $dataUser[0]['Rol'];
$maxUser = mostrarDatos("usuario", "", "", "", "");
$dataUser2 = mostrarDatos("usuario", $us, "", "", "");
$dataPermisos = mostrarDatos("permisos", $us, "", "", "");
if ($p > 1) {
    if ($dataUser[0]['Rol'] != 0) {
        echo "
        <script>
        window.location.href='ajustes.php';
        </script>
        ";
    }
}
// PAGINAS //////////////////////////////////////////////////////////////
switch ($p) {
    case 1:
        // AJUSTES GENARELES ///////////
?>
        <!-- USUARIO -->
        <div class="contenido" style="width:30%;float:right;animation:none">
            <div class="box mostrar_informacion" style="margin-right:0vh;animation:inicial 0.5s">
                <div class="box interior_informacion">
                    <a onclick="form()" style="Float:left;margin-left:15px;margin-top:5px;cursor:pointer"><i id="gear" class="bi bi-gear-fill"></i></a>
                    <div class="icon" style="position:static;margin-top:5vh;margin-bottom:5vh;margin-left:33%;box-shadow:0px 0px 20px var(--sombra)">
                        <i style="font-size:65px;" class="bi bi-person-fill"></i>
                    </div>
                    <h2 style="border-bottom:2px solid var(--sombra);padding-bottom:5vh;"><?php echo $user ?></h2>
                    <div id="form_user" hidden>
                        <form method="post" action="?">
                            <input type="hidden" name="id" value="<?php echo $dataUser[0]['ID'] ?>">
                            <b>Nombre</b>
                            <p><input autocomplete="off" style="height:5vh;min-width:68%" name="nombre" type="text" value="<?php echo $dataUser[0]['Nombre'] ?>"></p>
                            <b>Email</b>
                            <p><input autocomplete="off" type="text" style="height:5vh" name="email" value="<?php echo $dataUser[0]['Email'] ?>"></p>
                            <br>
                            <p><a class="boton" style="width:31.5%;margin-left:29%" href="sesion.php?pg=2">Cambiar contraseña</a></p>
                            <br>
                            <button name="DatosUser" style="margin-top: 3vh;" type="submit" class="true">Guardar</button>
                        </form>
                        <script>
                            const option = document.getElementById(<?php echo $dataUser[0]['Rol'] ?>)
                            option.setAttribute('selected', 'true');
                        </script>
                    </div>
                    <div id="mostrar_usuario">
                        <h3 style="color:gray">Nombre</h3>
                        <p><?php echo $dataUser[0]['Nombre'] ?></p>
                        <h3 style="color:gray">Cedula</h3>
                        <p><?php echo $dataUser[0]['Cedula'] ?></p>
                        <h3 style="color:gray">Email</h3>
                        <p><?php echo $dataUser[0]['Email'] ?></p>
                        <h3 style="color:gray">Rol</h3>
                        <p>
                            <?php
                            $nombreRol = mostrarDatos("Roles", $dataUser[0]['Rol'], "", "",);
                            echo $nombreRol[0]['Nombre'];
                            ?>
                        </p>
                        <h3 style="color:gray">Inicio de sesion</h3>
                        <p><?php echo $session[0]['fecha'] . " / " . $session[0]['hora'] ?></p>
                    </div>
                </div>
                <?php
                $dataAjustes = mostrarDatosAjustes();
                ?>
                <script>
                    function form() {
                        let info = document.getElementById('mostrar_usuario');
                        let form = document.getElementById('form_user');
                        let feh = document.getElementById('gear');
                        if (feh.className == 'bi bi-gear-fill') {
                            feh.className = "bi bi-chevron-left";
                            info.setAttribute('hidden', 'true');
                            form.removeAttribute('hidden');
                        } else {
                            feh.className = "bi bi-gear-fill";
                            form.setAttribute('hidden', 'true');
                            info.removeAttribute('hidden');
                        }
                    }
                </script>
            </div>
        </div>
        <!-- AJUSTES -->
        <div class="contenido" style="width:51%;">
            <div class="box" style="color:gray;min-height: 78vh;margin-right:-5vh;padding-top:4vh">
                <form id="form" action="" method="post" style="display: grid;justify-content:left;margin-bottom:5vh">
                    <?php
                    if ($rol == 0) {
                    ?>
                        <!--  AJUSTES GENERALES  -->
                        <div class="ajuste">
                            <i style="font-size: 5vh" class="bi bi-gear-wide-connected"></i>
                            <div>
                                <b>General</b><br>
                                <i style="font-size: 1.8vh;">Aspectos generales de la pagina</i>
                            </div>
                            <a href="?p=2" class="asignar"><i style="font-size: 2vh;" class="bi bi-chevron-right"></i></a>
                        </div>
                    <?php
                    }
                    ?>
                    <!--  MODO NOCTURNO -->
                    <div class="ajuste">
                        <i style="font-size: 5vh;" class="bi bi-moon"></i>
                        <div>
                            <b>Modo nocturno</b><br>
                            <i style="font-size: 1.8vh;">Cambiar la paleta de colores a un tono oscuro</i>
                        </div>
                        <select class="select_column2" style="width:7vh" id="modo" onchange="modoNocturno()">
                            <option value="0">No</option>
                            <option value="1">Si</option>
                        </select>
                    </div>
                    <!--  STOCK MINIMO  -->
                    <div class="ajuste">
                        <i style="font-size: 5vh" class="bi bi-inbox"></i>
                        <div>
                            <b>Stock minimo</b><br>
                            <i style="font-size: 1.8vh;">Minima cantidad de cajas de un producto</i>
                        </div>
                        <input autocomplete="off" style="width:6vh;text-align:center;" type="number" id="stock" name="stock" value="<?php echo $dataAjustes['stock'] ?>">
                    </div>
                    <!--  PORCENTAJE DE GANANCIA  -->
                    <div class="ajuste">
                        <i style="font-size: 5vh;" class="bi bi-percent"></i>
                        <div>
                            <b>Porcentaje de ganancia</b><br>
                            <i style="font-size: 1.8vh;">Asigne el porcentaje que obtendra de beneficio por producto</i>
                        </div>
                        <input autocomplete="off" style="width:6vh;text-align:center" type="number" id="ganancia" name="ganancia" value="<?php echo $dataAjustes['ganancia'] ?>">
                    </div>
                    <!--  PRECIO AUTOMATICO  -->
                    <div class="ajuste">
                        <i style="font-size: 5vh;" class="bi bi-tags"></i>
                        <div>
                            <b>Precio automatico</b><br>
                            <i style="font-size: 1.8vh;">Asignar precio de los productos automaticamente</i>
                        </div>
                        <select class="select_column2" style="width:7vh" name="auto" id="select_auto" onchange="por()">
                            <option value="0">No</option>
                            <option value="1">Si</option>
                        </select>
                    </div>
                </form>
                <button form="form" id="boton" name="ajustes" class="true" hidden>Guardar</button>
            </div>
        </div>
        <!-- JAVASCRIPT -->
        <script>
            $("#select_auto > option[value=<?php echo $dataAjustes['porcentaje'] ?>]").attr("selected", true);
            $("#modo > option[value='<?php echo $modo ?>']").attr("selected", true);
            const inputs = document.querySelectorAll('#ganancia');
            const inputs2 = document.querySelectorAll('#stock');
            const precioAutomatico = "<?php echo $dataAjustes['porcentaje'] ?>";
            const botons = document.getElementById('boton');
            const validarFormulario = (e) => {
                if (e.target.value != <?php echo $dataAjustes['ganancia'] ?>) {
                    botons.removeAttribute('hidden');
                } else {
                    botons.setAttribute('hidden', 'true');
                }
            }
            const validarFormulario2 = (e) => {
                if (e.target.value != <?php echo $dataAjustes['stock'] ?>) {
                    botons.removeAttribute('hidden');
                } else {
                    botons.setAttribute('hidden', 'true');
                }
            }

            function modoNocturno() {
                var modo = document.getElementById('modo').value;
                if (modo == 1) {
                    window.location.href = "../../controlador/Procesos.php?m=1";
                } else {
                    window.location.href = "../../controlador/Procesos.php?m=0";
                }
            }

            function por() {
                var select = document.getElementById('select_auto').value;
                if (select != precioAutomatico) {
                    botons.removeAttribute('hidden');
                } else {
                    botons.setAttribute('hidden', 'true');
                }
            }
            inputs.forEach((input) => {
                input.addEventListener('keyup', validarFormulario);
                input.addEventListener('blur', validarFormulario);
            });
            inputs2.forEach((input) => {
                input.addEventListener('keyup', validarFormulario2);
                input.addEventListener('blur', validarFormulario2);
            });
            if ("<?php echo $rol ?>" != 0) {
                $("#select_auto").attr("disabled", true);
                $("#ganancia").attr("disabled", true);
                $("#stock").attr("disabled", true);
                $("#boton").attr("disabled", true);
            }
        </script>
    <?php
        break;
        // AJUSTES ADMINISTRADOR ////////////
    case 2:
    ?>
        <!--  MENSAJE CONFIRMACION  -->
        <div id="validarPass2" class="contenido visualizacion_factura" style="z-index:99;display:none;animation:none">
            <div class="ventana" style="padding:8vh 18vh;grid-template-columns: auto;box-shadow:none;grid-template-rows: auto auto auto;color:var(--texto);animation: inicial 0.1s;">
                <a onclick="$('#validarPass2').css('display', 'none');" style="Float:left;margin-left:-16.5vh;margin-top:-6.5vh;cursor:pointer;position:fixed"><i style="font-size:3vh" class="bi bi-x"></i></a>
                <form action="../../controlador/Procesos.php?Reboot" method="post">
                    <i style="font-size: 25vh;grid-column-start: 1;grid-row-start: 1;" class="bi bi-exclamation-triangle-fill"></i>
                    <h1 style="grid-column-start: 1;grid-row-start: 2;">Verificacion</h1>
                    <b style="grid-column-start: 1;grid-row-start: 3;">Introduzca su contraseña para validar el rol de administrador</b>
                    <p><input autocomplete="off" type="password" style="height:5vh" id="passValidacion2" name="passValidacion2"></p>
                    <button id="validacion2" type="submit" style="grid-column-start: 1;grid-row-start: 5;width:13vh;margin-top:3vh" class="true">Validar</button>
                </form>
            </div>
        </div>
        <!--  AJUSTES  -->
        <div class="contenido" style="width:78%;float:right;animation:inicial 0.4s;">
            <div class="box" style="color:gray;min-height: 78vh;margin-left:2vh;margin-right:-5vh;padding-top:4vh">
                <form id="form" action="" method="post" style="display: grid;justify-content:left;margin-bottom:10%;grid-template-columns: 58%;padding-left:2vh">
                    <!-- //// USUARIOS /////////////////////////////////////////////////////// -->
                    <div class="ajuste">
                        <i style="font-size: 5vh;min-width:14vh" class="bi bi-people-fill"></i>
                        <div>
                            <b>Usuarios</b><br>
                            <i style="font-size: 12px;">Administrar las cuentas de usuarios</i>
                        </div>
                        <a href="?p=3" class="asignar"><i style="font-size: 2vh;" class="bi bi-chevron-right"></i></a>
                    </div>
                    <!-- //// CATEGORIAS /////////////////////////////////////////////////////// -->
                    <div class="ajuste" id="ajuste1">
                        <i style="font-size: 5vh;min-width:14vh" class="bi bi-inboxes"></i>
                        <div>
                            <b>Categorias</b><br>
                            <i style="font-size: 12px;">Asignar categorias</i>
                        </div>
                        <a href="?p=2&a=1" class="asignar"><i style="font-size: 2vh;" class="bi bi-chevron-right"></i></a>
                    </div>
                    <!-- //// ROLES /////////////////////////////////////////////////////// -->
                    <div class="ajuste" id="ajuste2">
                        <i style="font-size: 5vh;min-width:14vh" class="bi bi-person-workspace"></i>
                        <div>
                            <b>Roles</b><br>
                            <i style="font-size: 12px;">Asignar roles</i>
                        </div>
                        <a href="?p=2&a=2" class="asignar"><i style="font-size: 2vh;" class="bi bi-chevron-right"></i></a>
                    </div>
                    <!-- //// DATOS EMPRESA /////////////////////////////////////////////////////// -->
                    <div class="ajuste" id="ajuste3">
                        <i style="font-size: 5vh;min-width:14vh" class="bi bi-journal-text"></i>
                        <div>
                            <b>Datos de la empresa</b><br>
                            <i style="font-size: 12px;">Establecer los datos de la empresa</i>
                        </div>
                        <a href="?p=2&a=3" class="asignar"><i style="font-size: 2vh;" class="bi bi-chevron-right"></i></a>
                    </div>
                    <!-- //// RESTABLECER /////////////////////////////////////////////////////// -->
                    <div class="ajuste" id="ajuste4">
                        <i style="font-size: 5vh;min-width:14vh" class="bi bi-bootstrap-reboot"></i>
                        <div>
                            <b>Restablecer sistema</b><br>
                            <i style="font-size: 12px;">Borrar todos los datos del sistema</i>
                        </div>
                        <a href="?p=2&a=4" class="asignar"><i style="font-size: 2vh;" class="bi bi-chevron-right"></i></a>
                    </div>
                </form>
                <a href="?" style="width: 5vh;margin-left:37.5%;margin-top:-3vh" class="boton false">Atras</a>
            </div>
            <!-- FORMULARIOS -->
            <div class="box" style="color:gray;min-height:78vh;margin-right:10vh;padding-top:4vh;border-radius:0;animation: inicial 0.4s">
                <?php
                $ajuste = (isset($_GET['a'])) ? $_GET['a'] : 1;
                switch ($ajuste) {
                        ////CATEGORIA//////////////////////////////////////////////////7
                    case 1:
                ?>
                        <h1>Categorias</h1>
                        <br>
                        <table id="tableCategoria" style="width:92%;margin: 0 auto">
                            <div style="display: grid;justify-content:center;">
                                <tbody>
                                    <tr>
                                        <th>
                                            <h3>ID</h3>
                                        </th>
                                        <th>
                                            <h3>Nombre</h3>
                                        </th>
                                        <th>
                                            <h3>Productos</h3>
                                        </th>
                                        <th></th>
                                        <th><button id="agregar_producto" class="boton_factura" onclick="agregar()" style="box-shadow: 0 0 10px var(--sombra);" name="factura"><i style="font-size:2vh;color:gray">New</i></button></th>
                                    </tr>
                                    <?php
                                    $dataCategoria = mostrarDatos("categoria", "", "", "");
                                    for ($i = 0; $i < count($dataCategoria); $i++) {
                                        $ProductosCategoria = mostrarDatos("productos", $dataCategoria[$i]['ID'], "", "categoria");
                                    ?>
                                        <form action="" method="post">
                                            <tr>
                                                <!-- Id  -->
                                                <input type="hidden" name="id" value="<?php echo $dataCategoria[$i]['ID'] ?>">
                                                <td><?php echo $dataCategoria[$i]['ID'] ?></td>
                                                <!-- Nombre  -->
                                                <td><input autocomplete="off" type="text" style="height:4vh;width:95%;" id="input<?php echo $dataCategoria[$i]['ID'] ?>" name="nuevoNombre" value="<?php echo $dataCategoria[$i]['Nombre'] ?>" required></td>
                                                <td><?php echo ($ProductosCategoria != null) ? count($ProductosCategoria) : 0; ?></td>
                                                <!-- Enviar  -->
                                                <td><button type="submit" name="editarCategoria" class="boton_factura borrar"><i style="color:#4ac800" class="bi bi-check"></i></button></td>
                                                <!-- Borrar  -->
                                                <td><a href="?p=2&a=1&cat=<?php echo $dataCategoria[$i]['ID']  ?>" class="boton_factura borrar"><i class="bi bi-x"></i></a></td>
                                            </tr>
                                        </form>
                                    <?php
                                    }
                                    ?>
                                </tbody>
                            </div>
                            <!-- AGREGAR CATEGORIA  -->
                            <form name="newCategoria" action="" method="post" style="display: grid;justify-content:center;">
                                <tr id="agregar_form_categoria" hidden>
                                    <input type="hidden" name="id" value="<?php echo $dataCategoria[count($dataCategoria) - 1]['ID'] + 1 ?>">
                                    <td><?php echo $dataCategoria[count($dataCategoria) - 1]['ID'] + 1 ?></td>
                                    <td>
                                        <input autocomplete="off" type="text" style="height:4vh;width:95%;" name="categoriaNueva" required>
                                    </td>
                                    <td><a class="boton_factura borrar" onclick="enviar()"><i style="color:#4ac800" class="bi bi-check"></i></a></td>
                                    <td><a class="boton_factura borrar" onclick="cerrar()"><i class="bi bi-x"></i></a></td>
                                    <td></td>
                                </tr>
                            </form>
                            <script>
                                var form = document.getElementById("agregar_form_categoria");

                                function agregar() {
                                    document.getElementById("agregar_producto").setAttribute('hidden', "true");
                                    form.removeAttribute('hidden');
                                }

                                function cerrar() {
                                    document.getElementById("agregar_producto").removeAttribute('hidden');
                                    form.setAttribute('hidden', "true");
                                }

                                function editar(id) {
                                    var input = document.getElementById('input' + id).value;
                                    window.location.href = '?p=3';
                                }

                                function enviar() {
                                    document.newCategoria.submit()
                                }
                            </script>
                        </table>
                    <?php
                        break;
                        ////ROLES///////////////////////////////////////////////////////
                    case 2:
                    ?>
                        <h1>Roles</h1>
                        <br>
                        <table id="tableRol" style="width:92%;margin: 0 auto">
                            <div style="display: grid;justify-content:center;">
                                <tbody>
                                    <tr>
                                        <th>
                                            <h3>ID</h3>
                                        </th>
                                        <th>
                                            <h3>Cargo</h3>
                                        </th>
                                        <th>
                                            <h3>Usuarios</h3>
                                        </th>
                                        <th></th>
                                        <th><button id="agregar_rol" class="boton_factura" style="box-shadow: 0 0 10px var(--sombra);" onclick="agregarRol()"><i style="font-size:2vh;color:gray">New</i></button></th>
                                    </tr>
                                    <?php
                                    $dataRoles = mostrarDatos("Roles", "", "", "");
                                    for ($i = 0; $i < count($dataRoles); $i++) {
                                        $RolUsuario = mostrarDatos("usuario", "Rol", $dataRoles[$i]['ID'], "");
                                    ?>
                                        <form action="" method="post">
                                            <tr>
                                                <!-- Id  -->
                                                <input type="hidden" name="id" value="<?php echo $dataRoles[$i]['ID'] ?>">
                                                <td><?php echo $dataRoles[$i]['ID'] ?></td>
                                                <!-- Nombre  -->
                                                <td><input autocomplete="off" type="text" style="height:4vh;width:95%;" id="input<?php echo $dataRoles[$i]['ID'] ?>" name="nuevoNombreRol" value="<?php echo $dataRoles[$i]['Nombre'] ?>" required></td>
                                                <td><?php echo ($RolUsuario != null) ? count($RolUsuario) : 0; ?></td>
                                                <!-- Enviar  -->
                                                <td><button type="submit" name="editarRol" class="boton_factura borrar"><i style="color:#4ac800" class="bi bi-check"></i></button></td>
                                                <!-- Borrar  -->
                                                <td><a href="?p=2&a=2&rol=<?php echo $dataRoles[$i]['ID']  ?>" class="boton_factura borrar"><i class="bi bi-x"></i></a></td>
                                            </tr>
                                        </form>
                                    <?php
                                    }
                                    ?>
                                </tbody>
                            </div>
                            <!-- AGREGAR Usuario  -->
                            <form name="newRol" action="" method="post" style="display: grid;justify-content:center;">
                                <tr id="agregar_form_rol" hidden>
                                    <input type="hidden" name="id" value="<?php echo $dataRoles[count($dataRoles) - 1]['ID'] + 1 ?>">
                                    <td><?php echo $dataRoles[count($dataRoles) - 1]['ID'] + 1 ?></td>
                                    <td>
                                        <input autocomplete="off" type="text" style="height:4vh;width:95%;" name="rolNuevo" required>
                                    </td>
                                    <td><a class="boton_factura borrar" onclick="enviarRol()"><i style="color:#4ac800" class="bi bi-check"></i></a></td>
                                    <td><a class="boton_factura borrar" onclick="cerrarRol()"><i class="bi bi-x"></i></a></td>
                                    <td></td>
                                </tr>
                            </form>
                            <script>
                                var form = document.getElementById("agregar_form_rol");

                                function agregarRol() {
                                    document.getElementById("agregar_rol").setAttribute('hidden', "true");
                                    form.removeAttribute('hidden');
                                }

                                function cerrarRol() {
                                    document.getElementById("agregar_rol").removeAttribute('hidden');
                                    form.setAttribute('hidden', "true");
                                }

                                function editarRol(id) {
                                    var input = document.getElementById('input' + id).value;
                                    window.location.href = '?p=3';
                                }

                                function enviarRol() {
                                    document.newRol.submit()
                                }
                            </script>
                        </table>
                    <?php
                        break;
                        ////DATOS DE EMPRESA///////////////////////////////////////////////////////
                    case 3:
                        $dataAjustes = mostrarDatosAjustes();
                    ?>
                        <h1>Datos de empresa</h1><br>
                        <form id="formEmpresa" method="post" enctype="multipart/form-data">
                            <div style="text-align:left;margin-left:16vh">
                                <b>Logo: </b>
                                <label class="custom-file-upload" style="width:47%;margin-left:15vh;padding:1.3vh;margin-top:-4vh">
                                    <i style="padding-right: 10px;padding-left: 10px;font-size:3vh" class="bi bi-cloud-plus"></i>
                                    <input type="file" name="img" accept=".png">
                                    <span>Seleccione una imagen</span>
                                </label>
                                <b>Nombre: </b>
                                <input required autocomplete="off" type="text" class="input_datos" style="margin-left:6vh" name="nombre" value="<?php echo $dataAjustes['Nombre'] ?>"><br>
                                <b>NIT: </b>
                                <input required autocomplete="off" type="text" class="input_datos" style="margin-left:10.5vh" name="nit" value="<?php echo $dataAjustes['NIT'] ?>"><br>
                                <b>Ciudad: </b>
                                <input required autocomplete="off" type="text" class="input_datos" style="margin-left:6.8vh" name="ciudad" value="<?php echo $dataAjustes['Ciudad'] ?>"><br>
                                <b>Dirección: </b>
                                <input required autocomplete="off" type="text" class="input_datos" style="margin-left:4.4vh" name="direccion" value="<?php echo $dataAjustes['Direccion'] ?>"><br>
                            </div>
                        </form>
                        <br>
                        <br>
                        <button form="formEmpresa" class="true" name="datosEmpresa">Guardar</button>
                    <?php
                        break;
                        ////RESTABLECER///////////////////////////////////////////////////////
                    case 4:
                        if (isset($_GET['re'])) {
                            echo "
                        <script>
                            Swal.fire({
                                icon: 'error',
                                title: 'No se ha podido restablecer el sistema!',
                                text: 'Intente nuevamente',
                                showConfirmButton: true,
                                buttonsStyling: false,
                                background: 'var(--sombra)',
                                customClass: {
                                    confirmButton: 'boton true',
                                }
                            }).then(() => {
                                window.location.href = '?p=2&a=4';
                            });
                        </script>
                        ";
                        }
                    ?>
                        <h3 style="padding: 2vh 8vh">
                            Esta apunto de borrar todos los datos del sistema esto incluye productos, facturas, clientes, proveedores, usuarios, etc.<br><br>
                            ¿Desea continuar con esta operacion?.
                        </h3>
                        <button onclick="validarPass()" name="reboot" style="margin-top:5%" class="true">Si, Restablecer</button>
                <?php
                        break;
                }
                ?>
            </div>
            <script>
                function validarPass() {
                    Swal.fire({
                        title: 'Seguro que quiere continuar?',
                        icon: 'warning',
                        confirmButtonText: 'Si, restablecer',
                        buttonsStyling: false,
                        background: 'var(--baseColor)',
                        color: "var(--texto)",
                        customClass: {
                            confirmButton: 'boton true',
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $('#validarPass2').css('display', 'grid');
                        }
                    })
                }
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
                $('#ajuste<?php echo $ajuste ?>').addClass("ajuste_seleccionado");
            </script>
        </div>
        <?php
        break;
        // PERMISOS 
    case 3:
        if (isset($_GET['us'])) {
            $us = $_GET['us'];
        } else {
            $us = 1;
        }
        $maxUser = mostrarDatos("usuario", "", "", "",);
        $dataUser2 = mostrarDatos("usuario", $us, "", "",);
        $dataPermisos = mostrarDatos("permisos", $us, "", "",);
        if (count($maxUser) <= 1) {
            echo '
            <div class="contenido">
                <div class="box" style="min-height: 75vh;color:gray">
                    <div class="botones">
                        <a href="?" class="boton" style="width:3vh;padding: 2.1vh 2.8vh;"><i class="bi bi-arrow-return-left"></i></a>
                    </div>
                    <i style="font-size: 30vh" class="bi bi-cloud-slash"></i>
                    <br>
                    <br>
                    <h1>No se han encontrado datos</h1>
                </div>
            </div>
            ';
        } else {
        ?>
            <!-- //// PERMISOS /////////////////////////////////////////////////////// -->
                        <div class="contenido" style="width:51%;animation:none">
                            <div class="box" style="margin-right:-4vh;color:gray;min-height: 80vh;padding-top:5px;">
                                <form id="formPermisos" name="formPermisos" method="post" style="display: grid;justify-content:left;padding-left:5vh">
                                    <br>
                                    <!--  PERMISO EDITAR  -->
                                    <div class="ajuste permisos">
                                        <i style="font-size: 4vh;" class="bi bi-pencil-square"></i>
                                        <div>
                                            <b>Editar los productos</b><br>
                                            <i style="font-size: 1.7vh;">Poder cambiar aspectos de los productos</i>
                                        </div>
                                        <select class="select_column2" style="width:7vh" id="editar" name="editar">
                                            <option value="0">No</option>
                                            <?php if(count($maxUser) < 200): ?>
                                                <option value="1">Si</option>
                                            <?php else: ?>
                                                <option value="1" disabled>Si (Máximo alcanzado)</option>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                    <br>
                                </form>
                            </div>
                        </div>
                                <br>
                        <!--  PERMISO AGREGAR  -->
                        <div class="ajuste permisos">
                            <i style="font-size: 4vh;" class="bi bi-box-seam"></i>
                            <div>
                                <b>Agregar productos</b><br>
                                <i style="font-size: 1.7vh;">Poder agregar productos a la base de datos</i>
                            </div>
                            <select class="select_column2" style="width:7vh" id="agregar" name="agregar">
                                <option value="0">No</option>
                                <option value="1">Si</option>
                            </select>
                        </div>
                        <br>
                        <!--  PERMISO ENTRADA  -->
                        <div class="ajuste permisos">
                            <i style="font-size: 4vh;" class="bi bi-box-arrow-in-left"></i>
                            <div>
                                <b>Entrada de productos</b><br>
                                <i style="font-size: 1.7vh;">Poder crear facturas de entrada</i>
                            </div>
                            <select class="select_column2" style="width:7vh" id="entrada" name="entradaProductos">
                                <option value="0">No</option>
                                <option value="1">Si</option>
                            </select>
                        </div>
                        <br>
                        <!--  PERMISO SALIDA  -->
                        <div class="ajuste permisos">
                            <i style="font-size: 4vh;" class="bi bi-box-arrow-in-right"></i>
                            <div>
                                <b>Salida de productos</b><br>
                                <i style="font-size: 1.7vh;">Poder crear facturas de salida</i>
                            </div>
                            <select class="select_column2" style="width:7vh" id="salida" name="salida">
                                <option value="0">No</option>
                                <option value="1">Si</option>
                            </select>
                        </div>
                        <br>
                        <!--  PERMISO CLIENTES  -->
                        <div class="ajuste permisos">
                            <i style="font-size: 4vh;" class="bi bi-person-video2"></i>
                            <div>
                                <b>Manejar informacion de clientes</b><br>
                                <i style="font-size: 1.7vh;">Poder editar y agregar clientes a la base de datos</i>
                            </div>
                            <select class="select_column2" style="width:7vh" id="clientes" name="clientes">
                                <option value="0">No</option>
                                <option value="1">Si</option>
                            </select>
                        </div>
                        <br>
                        <!--  PERMISO PROOVEDORES  -->
                        <div class="ajuste permisos">
                            <i style="font-size: 4vh;" class="bi bi-truck"></i>
                            <div>
                                <b>Manejar informacion de proveedores</b><br>
                                <i style="font-size: 1.7vh;">Poder editar y agregar proveedores a la base de datos</i>
                            </div>
                            <select class="select_column2" style="width:7vh" id="proveedores" name="proveedores">
                                <option value="0">No</option>
                                <option value="1">Si</option>
                            </select>
                        </div>
                        <br>
                        <!--  PERMISO REPORTES  -->
                        <div class="ajuste permisos">
                            <i style="font-size: 4vh;" class="bi bi-clipboard-minus"></i>
                            <div>
                                <b>Reportes</b><br>
                                <i style="font-size: 1.7vh;padding-right:5px">Poder generar reportes de entrada y salida, ademas de imprimir facturas</i>
                            </div>
                            <select class="select_column2" style="width:7vh" id="reportes" name="reportes">
                                <option value="0">No</option>
                                <option value="1">Si</option>
                            </select>
                        </div>
                        <!--  INFORMACION USUARIOS  -->
                        <div class="box mostrar_informacion" style="max-width: 55vh;margin-right: -60vh;animation:inicial 0.4s">
                            <div class="box interior_informacion" style="margin-top: -7.3vh;padding-top:5vh">
                                <a title="Eliminar" onclick="borrarUser(<?php echo $dataUser2[0]['ID'] ?>)" class="asignar" style="position: absolute;left:0;top:0;padding:2vh 1.5vh;cursor:pointer"><i style="font-size: 2.5vh;" class="bi bi-trash3"></i></a>
                                <a title="Agregar" href="sesion.php?pg=1" class="asignar" style="position: absolute;right:1vh;top:0;padding:2vh 1.5vh;cursor:pointer"><i style="font-size: 2.5vh;" class="bi bi-plus-circle"></i></a>
                                <?php
                                if ($us > 1) {
                                    echo '<a href="?p=3&us=' . ($us - 1) . '" style="margin-top:8vh;float:left;margin-left:14vh"><i style="color:gray;font-size:3vh" class="bi bi-chevron-left"></i></a>';
                                } elseif ($us < count($maxUser) - 1) {
                                    echo '<a href="?p=3&us=' . ($us + 1) . '" style="margin-top:8vh;float:right;margin-right:14vh"><i style="color:gray;font-size:3vh" class="bi bi-chevron-right"></i></a>';
                                }
                                ?>
                                <div class="icon" style="margin-top:1px;position:static;margin-left:33%;box-shadow:0px 0px 20px var(--sombra)">
                                    <i style="font-size:65px" class="bi bi-person-fill"></i>
                                </div>
                                <br>
                                <input type="hidden" name="id" value="<?php echo $dataUser2[0]['ID'] ?>">
                                <h2 style="border-bottom:2px solid var(--sombra);padding-bottom:5vh;"><?php echo $dataUser2[0]['Usuario'] ?></h2>
                                <b>Nombre</b>
                                <p><input autocomplete="off" style="width:250px;" name="nombre" type="text" value="<?php echo $dataUser2[0]['Nombre'] ?>"></p>
                                <b>Cedula</b>
                                <p><input autocomplete="off" name="cedula" type="number" value="<?php echo $dataUser2[0]['Cedula'] ?>"></p>
                                <b>Email</b>
                                <p><input autocomplete="off" type="text" name="email" value="<?php echo $dataUser2[0]['Email'] ?>"></p>
                                <b>Rol</b><br>
                                <select name="rol" class="rol" id="cargo" style="width:20vh;text-align:center;height:30px;border-radius:5px;margin-left:2%;margin-top:10px">
                                    <?php
                                    $dataRol = mostrarDatos("Roles", "", "", "",);
                                    for ($i = 0; $i < count($dataRol); $i++) {
                                        echo '
                                <option value="' . $dataRol[$i]['ID'] . '">' . $dataRol[$i]['Nombre'] . '</option>
                                ';
                                    }
                                    ?>
                                </select>
                                <script>
                                    $("#cargo > option[value=<?php echo $dataUser2[0]['Rol'] ?>]").attr("selected", true);
                                </script>
                                <br>
                                <a href="?p=2" class="false" style="margin-top:10vh;float:right;margin-right:13.3vh;width:7vh">Atras</a>
                                <button type="submit" id="boton" name="Guardarpermisos" class="true" style="margin-top:10vh;float:right;margin-right:0.8vh;width:14vh;padding:1.7vh">Guardar</button>
                            </div>
                        </div>
                    </form>
                </div>
                <script>
                    function borrarUser(id) {
                        Swal.fire({
                            title: 'Seguro que quiere eliminiar este usuario?',
                            icon: 'warning',
                            confirmButtonText: 'Si, eliminar',
                            buttonsStyling: false,
                            background: 'var(--baseColor)',
                            customClass: {
                                confirmButton: 'boton true',
                            }
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = "?p=3&id=" + id;
                            }
                        })
                    }
                    $("#editar > option[value='<?php echo $dataPermisos[0]['Editar'] ?>']").attr("selected", true);
                    $("#agregar > option[value='<?php echo $dataPermisos[0]['Agregar'] ?>']").attr("selected", true);
                    $("#clientes > option[value='<?php echo $dataPermisos[0]['Clientes'] ?>']").attr("selected", true);
                    $("#proveedores > option[value='<?php echo $dataPermisos[0]['Proveedores'] ?>']").attr("selected", true);
                    $("#entrada > option[value='<?php echo $dataPermisos[0]['Entrada'] ?>']").attr("selected", true);
                    $("#salida > option[value='<?php echo $dataPermisos[0]['Facturar'] ?>']").attr("selected", true);
                    $("#reportes > option[value='<?php echo $dataPermisos[0]['Reportes'] ?>']").attr("selected", true);
                </script>
            </div>
<?php
        }
        break;
}

// FUNCIONES ////////////////////////////////////////////////////////////
///AJUSTES
if (isset($_POST['DatosUser'])) {
    modificarInformacion();
}
if(isset($_POST['cambiarContraseña'])){
    cambiarContraseña();
}
if ($dataUser[0]['Rol'] == 0) {
    if (isset($_POST['ajustes'])) {
        guardarAjustes();
    }
    if (isset($_POST['Guardarpermisos'])) {
        guardarPermisos();
    }
    if (isset($_POST['datosEmpresa'])) {
        guardarDatosEmpresa();
    }
    ///CATEGORIA
    if (isset($_POST['categoriaNueva'])) {
        AgregarCategoria();
    }
    if (isset($_POST['editarCategoria'])) {
        EditarCategoria();
    }
    if (isset($_GET['cat'])) {
        $id = $_GET['cat'];
        EliminarCategoria($id);
    }
    ///ROLES
    if (isset($_POST['rolNuevo'])) {
        AgregarRoles();
    }
    if (isset($_POST['editarRol'])) {
        EditarRoles();
    }
    if (isset($_GET['rol'])) {
        $id = $_GET['rol'];
        EliminarRoles($id);
    }
    ///USUARIO
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        eliminarUsuario($id);
    }
}
?>