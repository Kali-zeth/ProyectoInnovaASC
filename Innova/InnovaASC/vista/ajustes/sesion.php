<?php
if (isset($_GET['pg'])) {
    $pg = $_GET['pg'];
} else {
    $pg = 0;
}
require_once "../../controlador/Procesos.php";
session_start();
if (isset($_SESSION['bloqueo'])) {
    $intento = $_SESSION['bloqueo']['intento'];
    if ($intento > 2) {
        header('location:bloqueo.php');
    }
}
$dataAjustes = mostrarDatosAjustes();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.1/font/bootstrap-icons.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Icono de la pagina-->
    <link rel="stylesheet" type="text/css" href="../../style.css?ts=<?= time() ?>" />
    <link rel="shortcut icon" href="../../imagenes/icon.png?ts=<?= time() ?>" />
    <title><?php echo $dataAjustes['Nombre'] ?></title>
</head>
<div id="pantalla_carga">
    <div class="loader">
        <div></div>
        <div></div>
        <div></div>
    </div>
</div>

<body style="background: #e0e0e0;">
    <div class="contenido contenido_session">
        <div class="box box_session">
            <div>
                <div class="icon">
                    <img style="width: 70%;" src="../../imagenes/icon.png?ts=<?= time() ?>" alt="">
                </div>
                <br>
                <h2 id="empresa"><?php echo $dataAjustes['Nombre'] ?></h2>
                <?php
                // FORMULARIOS 
                switch ($pg) {
                    case 0:
                        if ($dataUser = mostrarDatos("usuario", "Rol", 0, "",)) {
                ?>
                            <div id="informacion" style="position:absolute;right:0;margin-right:20vh;text-align:right;top:0;margin-top:15vh;animation:drop 1s">
                                <h1 style="font-size: 9vh;color:#333">InnovaASC</h1>
                                <p style="margin-top:-5vh">Innovacion de hoy para el exito de mañana</p>
                                <br>
                                <h2>La mejor manera de manejar el inventario de tu empresa <br> de forma segura y organizada.<br></h2>
                                <h2 style="color:#333"><br> Sin complicaciones ;)</h2>
                            </div>
                            <form method="post" id="formulario">
                                <div class="formulario_session" style="margin-left:9vh;width:41vh;">
                                    <b>Email</b><br>
                                    <input type="email" name="email" required><br>
                                    <b>Contraseña</b><br>
                                    <input type="password" name="pass" required>
                                </div>
                                <button type="submit" class="true" name="guardar">Ingresar</button>
                            </form>
                            <p style="font-size:2vh;padding-top:6vh"><a style="color:gray;border-bottom: solid 1px gray;padding: 2px 1px" href="?pg=2">Olvidaste tu contraseña?</a></p>
                            <script>
                                function info() {
                                    $("#informacion").hide();
                                    $(".contenido_session").css("padding-left", "65vh");
                                }
                                $('.box_session input').hover(function() {
                                    $("#informacion").hide();
                                    $(".contenido_session").css("padding-left", "65vh");
                                });
                                $("#empresa").css("margin-bottom", "-4.5vh");
                                $(".icon").css("margin-left", "16vh");
                            </script>
                            <?php
                        } else {
                            header("location:sesion.php?pg=5");
                        }
                        break;
                    case 1:
                        if (isset($_SESSION['session'])) {
                            $idUser = $_SESSION['session'][0]['user'];
                            $dataUser = mostrarDatos("usuario", $idUser, "", "",);
                            if ($dataUser[0]['Rol'] == 0) {
                            ?>
                                <div style="height:2vh;margin-top:3vh">
                                    <h2 class="titulo1_session" style="padding-left: 7vh;"><a class="titulo_session_seleccionado" href="?pg=0">Registrar usuario</a></h2>
                                    <h2 class="titulo2_session" style="padding-right:7vh"><a class="titulo_session_noSeleccionado" href="ajustes.php?p=3">volver</a></h2>
                                </div>
                                <form id="registro" method="post">
                                    <div class="formulario_session" style="margin-left:13vh">
                                        <b>Nombre completo</b><br>
                                        <input type="text" name="nombre" required><br>
                                        <b>Cédula</b><br>
                                        <input type="text" name="cedula" placeholder="La cedula sera la contraseña" required><br>
                                        <b>Email</b><br>
                                        <input type="email" name="email" required><br>
                                        <select name="rol" class="rol" style="width:16vh;text-align:center;height:5vh;border-radius:5px;margin-left:20.3%">
                                            <?php
                                            $dataRol = mostrarDatos("Roles", "", "", "",);
                                            if (count($dataRol) == 0) {
                                                echo '
                                                <option value="0" id="Administrador">Administrador</option>
                                                ';
                                            } else {
                                                for ($i = 1; $i < count($dataRol); $i++) {
                                                    echo '
                                                <option value="' . $dataRol[$i]['ID'] . '" id="' . $dataRol[$i]['Nombre'] . '">' . $dataRol[$i]['Nombre'] . '</option>
                                                ';
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <button type="submit" class="true" name="registrar">Registrar</button>
                                </form>
                                <script>
                                    $(".contenido_session").css({
                                        "padding-left": "65vh",
                                        "padding-top": "2vh",
                                    });
                                </script>
                            <?php
                            } else {
                                header("location:ajustes.php");
                            }
                        } else {
                            header("location:ajustes.php");
                        }
                        break;
                    case 2:
                        if (empty($_SESSION['session'])) {
                            ?>
                            <div style="height:6vh;margin-top:3vh">
                                <h2 class="titulo1_session" style="padding-left: 7vh;"><a class="titulo_session_seleccionado" href="?pg=0">Recuperar contraseña</a></h2>
                                <h2 class="titulo2_session" style="padding-right:7vh"><a class="titulo_session_noSeleccionado" href="?pg=0">volver</a></h2>
                            </div>
                        <?php
                        } else {
                        ?>
                            <div style="height:6vh;margin-top:3vh">
                                <h2 class="titulo1_session" style="padding-left: 7vh;"><a class="titulo_session_seleccionado">Cambiar contraseña</a></h2>
                                <h2 class="titulo2_session" style="padding-right:7vh"><a class="titulo_session_noSeleccionado" href="ajustes.php">volver</a></h2>
                            </div>
                        <?php
                        }
                        ?>
                        <form method="post">
                            <div style="padding-top: 8vh;text-align:left;margin-left:15vh">
                                <b>Email</b><br>
                                <input type="email" name="email" required>
                            </div>
                            <button type="submit" class="true" name="guardar">Enviar</button>
                        </form>
                        <script>
                            $(".contenido_session").css("padding-left", "65vh");
                        </script>
                        <?php
                        break;
                    case 3:
                        if (empty($_SESSION['session'])) {
                        ?>
                            <div style="height:6vh;margin-top:3vh">
                                <h2 class="titulo1_session" style="padding-left: 7vh;"><a class="titulo_session_seleccionado" href="?pg=0">Recuperar contraseña</a></h2>
                                <h2 class="titulo2_session" style="padding-right:7vh"><a class="titulo_session_noSeleccionado" href="?pg=0">volver</a></h2>
                            </div>
                        <?php
                        } else {
                        ?>
                            <div style="height:6vh;margin-top:3vh">
                                <h2 class="titulo1_session" style="padding-left: 7vh;"><a class="titulo_session_seleccionado">Cambiar contraseña</a></h2>
                                <h2 class="titulo2_session" style="padding-right:7vh"><a class="titulo_session_noSeleccionado" href="ajustes.php">volver</a></h2>
                            </div>
                        <?php
                        }
                        ?>
                        <form method="post" id="codigo">
                            <div style="padding-top: 3vh;text-align:left;margin-left:13vh">
                                <p style="width: 300px;">Se ha enviado un codigo de confirmacion al correo registrado</p>
                                <br>
                                <input class="codigo" style="margin-left:8.5vh" type="number" name="codigo1" id="codigo1" max="9" required>
                                <input class="codigo" type="number" name="codigo2" id="codigo2" max="9" required>
                                <input class="codigo" type="number" name="codigo3" id="codigo3" max="9" required>
                                <input class="codigo" type="number" name="codigo4" id="codigo4" max="9" required>
                            </div>
                            <button type="submit" class="true" name="guardar">Validar</button>
                            <p style="margin-bottom:50px;font-size:13px;font-family:Avenir"><a href="?pg=3&rn=1" style="color:gray;border-bottom: solid 1px gray;padding: 2px 8px">Reenviar codigo</a></p>
                        </form>
                        <script>
                            $(".contenido_session").css("padding-left", "65vh");
                        </script>
                    <?php
                        break;
                    case 4:
                    ?>
                        <div style="height:10%;margin-top:3vh">
                            <h2><a class="titulo_session_seleccionado" href="?pg=0">Cambiar contraseña</a></h2>
                        </div>
                        <?php
                        if (empty($_SESSION['session'])) {
                            echo '<form id="formulario" action="?pg=0" method="post">';
                        } else {
                            echo '<form id="formulario" action="ajustes.php" method="post">';
                        }
                        ?>
                        <div style="padding-top: 10px;text-align:left;margin-left:2vh">
                            <b>Nueva contraseña</b><br>
                            <input type="password" style="width: 90% !important;" name="pass" id="pass" required>
                            <!-- mensajes  -->
                            <i id="error1" style="color:#f43;font-size:15px;margin-left:-30px" class="bi bi-exclamation-circle-fill error1" hidden></i>
                            <i id="check1" style="color:#5b5;font-size:15px;margin-left:-30px" class="bi bi-check-circle-fill " hidden></i><br>
                            <p id="alert1" style="width:240px;color:#f43;margin-top:-10px" hidden>La contraseña debe tener de 8 a 20 digitos</p>
                            <!-- fin mensajes  -->
                            <!-- // -->
                            <br>
                            <b>Confirmar contraseña</b><br>
                            <input type="password" style="width: 90% !important;" name="pass2" id="pass2" required>
                            <!-- mensajes  -->
                            <i id="error" style="color:#f43;font-size:15px;margin-left:-30px" class="bi bi-exclamation-circle-fill" hidden></i>
                            <i id="check" style="color:#5b5;font-size:15px;margin-left:-30px" class="bi bi-check-circle-fill" hidden></i>
                            <p id="alert" style="width:210px;color:#f43;margin-top:-1vh;margin-left:10px" hidden>Ambas contraseñas deben ser iguales</p>
                            <!-- fin mensajes  -->
                        </div>
                        <br>
                        <button id="boton" type="submit" class="true" name="cambiarContraseña" disabled>Guardar</button>
                        </form>
                        <script>
                            $(".contenido_session").css("padding-left", "65vh");
                            $(".box_session").css("min-height", "75vh");
                            $(".icon").css("margin-left", "22.5%");
                            const formulario = document.getElementById('formulario');
                            const inputs = document.querySelectorAll('#formulario input');
                            let vd = 0;

                            const expresiones = {
                                password: /^.{8,20}$/, // 4 a 12 digitos.
                            }

                            const validarFormulario = (e) => {
                                switch (e.target.name) {
                                    case "pass":
                                        const error1 = document.getElementById('error1');
                                        const alert1 = document.getElementById('alert1');
                                        const check1 = document.getElementById('check1');
                                        if (expresiones.password.test(e.target.value)) {
                                            $('#pass').removeClass('mal');
                                            error1.setAttribute('hidden', 'true');
                                            alert1.setAttribute('hidden', 'true');
                                            check1.removeAttribute('hidden');
                                            vd = 0;
                                        } else {
                                            $('#pass').addClass('mal');
                                            error1.removeAttribute('hidden');
                                            alert1.removeAttribute('hidden');
                                            check1.setAttribute('hidden', 'true');
                                            vd = 1;
                                        }
                                        validarPassword2();
                                        break;
                                    case "pass2":
                                        validarPassword2();
                                        break;
                                }
                            }

                            const validarPassword2 = () => {
                                const check = document.getElementById('check');
                                const error = document.getElementById('error');
                                const alert = document.getElementById('alert');
                                const boton = document.getElementById('boton');
                                const inputPassword1 = document.getElementById('pass');
                                const inputPassword2 = document.getElementById('pass2');

                                if (inputPassword1.value !== inputPassword2.value && vd == 0) {
                                    $('#pass2').addClass('mal');
                                    error.removeAttribute('hidden');
                                    alert.removeAttribute('hidden');
                                    check.setAttribute('hidden', 'true');
                                    boton.setAttribute('disabled', 'true');
                                } else {
                                    if (vd == 0) {
                                        $('#pass2').removeClass('mal');
                                        error.setAttribute('hidden', 'true');
                                        alert.setAttribute('hidden', 'true');
                                        check.removeAttribute('hidden');
                                        boton.removeAttribute('disabled');
                                    }
                                }
                            }

                            inputs.forEach((input) => {
                                input.addEventListener('keyup', validarFormulario);
                                input.addEventListener('blur', validarFormulario);
                            });
                        </script>
                        <?php
                        break;
                    case 5;
                        if (!$dataUser = mostrarDatos("usuario", "Rol", 0, "",)) {
                        ?>
                            <form id="registro" method="post">
                                <br>
                                <div style="padding-top: 10px;text-align:left;margin-left:11vh;width:44vh">
                                    <b>Nombre completo</b><br>
                                    <input type="text" name="nombre" id="nombre" required><br>
                                    <b>Email</b><br>
                                    <input type="email" name="email" required><br>
                                    <b>Cédula</b><br>
                                    <input type="number" name="cedula" autocomplete="off" required><br>
                                    <b>Contraseña</b><br>
                                    <input type="password" style="width: 72% !important;" name="pass" id="password" required>
                                    <!-- mensajes  -->
                                    <i id="error2" style="color:#f43;font-size:15px;margin-left:-30px" class="bi bi-exclamation-circle-fill error1" hidden></i>
                                    <i id="check2" style="color:#5b5;font-size:15px;margin-left:-30px" class="bi bi-check-circle-fill " hidden></i><br>
                                    <p id="alert2" style="width:240px;color:#f43;margin-top:-10px" hidden>La contraseña debe tener de 8 a 20 digitos</p>
                                    <!-- fin mensajes  -->
                                    <!-- // -->
                                    <b>Confirmar contraseña</b><br>
                                    <input type="password" style="width: 72% !important;" name="pass2" id="password2" required>
                                    <!-- mensajes  -->
                                    <i id="error3" style="color:#f43;font-size:15px;margin-left:-30px" class="bi bi-exclamation-circle-fill" hidden></i>
                                    <i id="check3" style="color:#5b5;font-size:15px;margin-left:-30px" class="bi bi-check-circle-fill" hidden></i>
                                    <p id="alert3" style="width:210px;color:#f43;margin-top:-10px;margin-left:10px" hidden>Ambas contraseñas deben ser iguales</p>
                                    <!-- fin mensajes  -->
                                    <input type="hidden" name="rol" value="0">
                                </div>
                                <button type="submit" class="true" name="registrarAdministrador" id="boton2" disabled>Registrar</button>
                            </form>
                            <p id="mensajeRA">Registre una cuenta Administrador para<br>el correcto control del sistema</p>
                            <script>
                                $(".contenido_session").css("padding-left", "65vh");
                                $(".box_session").css("min-height", "75vh");
                                $("#empresa").hide();
                                const formulario = document.getElementById('registro');
                                const inputs = document.querySelectorAll('#registro input');
                                const boton = document.getElementById('boton2');
                                let vd = 0;
                                const expresiones = {
                                    password: /^.{8,20}$/,
                                }

                                const campos = {
                                    password: false,
                                }

                                const validarFormulario = (e) => {
                                    switch (e.target.name) {
                                        case "pass":
                                            validarCampo(expresiones.password, e.target, 'password');
                                            validarPassword2();
                                            break;
                                        case "pass2":
                                            validarPassword2();
                                            break;
                                        default:
                                            $("#mensajeRA").hide();
                                            break;
                                    }
                                }

                                const validarCampo = (expresion, input, campo) => {
                                    if (expresion.test(input.value)) {
                                        if (campo == "password") {
                                            const check1 = document.getElementById('check2');
                                            const error1 = document.getElementById('error2');
                                            const alert1 = document.getElementById('alert2');
                                            error1.setAttribute('hidden', 'true');
                                            alert1.setAttribute('hidden', 'true');
                                            check1.removeAttribute('hidden');
                                            vd = 0;
                                        }
                                        if (campos.password) {
                                            boton.removeAttribute('disabled');
                                        } else {
                                            boton.setAttribute('disabled', 'true');
                                        }
                                        document.querySelector('#' + campo).classList.remove('mal');
                                        campos[campo] = true;
                                    } else {
                                        if (campo == "password") {
                                            const check1 = document.getElementById('check2');
                                            const error1 = document.getElementById('error2');
                                            const alert1 = document.getElementById('alert2');
                                            error1.removeAttribute('hidden');
                                            alert1.removeAttribute('hidden');
                                            check1.setAttribute('hidden', 'true');
                                            vd = 1;
                                        }
                                        boton.setAttribute('disabled', 'true');
                                        document.querySelector('#' + campo).classList.add('mal');
                                        campos[campo] = false;
                                    }
                                }

                                const validarPassword2 = () => {
                                    const check = document.getElementById('check3');
                                    const error = document.getElementById('error3');
                                    const alert = document.getElementById('alert3');
                                    const inputPassword1 = document.getElementById('password');
                                    const inputPassword2 = document.getElementById('password2');

                                    if (inputPassword1.value !== inputPassword2.value && vd == 0) {
                                        $('#password2').addClass('mal');
                                        error.removeAttribute('hidden');
                                        alert.removeAttribute('hidden');
                                        check.setAttribute('hidden', 'true');
                                        boton.setAttribute('disabled', 'true');
                                        campos['password'] = false;
                                    } else if (vd == 0) {
                                        $('#password2').removeClass('mal');
                                        error.setAttribute('hidden', 'true');
                                        alert.setAttribute('hidden', 'true');
                                        check.removeAttribute('hidden');
                                        campos['password'] = true;
                                        if (campos.password) {
                                            boton.removeAttribute('disabled');
                                        } else {
                                            boton.setAttribute('disabled', 'true');
                                        }
                                    }
                                }

                                inputs.forEach((input) => {
                                    input.addEventListener('keyup', validarFormulario);
                                    input.addEventListener('blur', validarFormulario);
                                });
                            </script>
                <?php
                        } else {
                            header("location:sesion.php?pg=0");
                        }
                        break;
                }
                ?>
            </div>
        </div>
    </div>
</body>

</html>
<?php
if (isset($_POST['email']) && $pg == 0) {
    iniciarSesion();
}
if (isset($_POST['email']) && $pg == 2) {
    recuperarContraseña();
}
if (isset($_POST['codigo1'])) {
    validarCodigo();
}
if (isset($_POST['cambiarContraseña']) && $pg == 0) {
    cambiarContraseña();
}
if (isset($_POST['registrar']) && $pg == 1) {
    registrar();
}
if (isset($_POST['registrarAdministrador']) && $pg == 5) {
    registrarAdministrador();
}
if(isset($_GET['rn']) && $pg== 3){
    recuperarContraseña();
}
?>