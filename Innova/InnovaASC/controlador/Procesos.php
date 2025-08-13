<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (isset($pg)) {
    require_once "../../modelo/sql.php";
} else {
    require_once "../modelo/sql.php";
}

function mensaje($tipo, $icon, $title, $text, $botonConfirm, $pagina)
{
    if ($tipo == 1) {
        $alerta = "
            <script>
                Swal.fire({
                    icon: '$icon',
                    title: '$title',
                    ";
        if ($text != null) {
            $alerta .= "text: '$text',";
        }
        $alerta .= "
                    showConfirmButton: $botonConfirm,
                    position: 'top-start',
                    background: 'var(--baseColor)',
                    timer: 2000,
                    color: 'var(--texto)',
                    buttonsStyling: false,
                    timerProgressBar: true,
                }).then(() => {
                    window.location.href = '$pagina';
                });
            </script>
            ";
    } else {
        $alerta = "
            <script>
                Swal.fire({
                    icon: '$icon',
                    title: '$title',
                    ";
        if ($text != null) {
            $alerta .= "text: '$text',";
        }
        $alerta .= "
                    showConfirmButton: $botonConfirm,
                    background: 'var(--baseColor)',
                    timer: 2500,
                    buttonsStyling: false,
                    customClass: {
                        confirmButton: 'true', 
                    }
                }).then(() => {
                    ";
        if ($pagina != null) {
            $alerta .= "window.location.href = '$pagina';";
        }
        $alerta .= "
                });
            </script>
            ";
    }
    echo $alerta;
}
///USUARIO///////////////////////////////////////////////////////////////////////
function iniciarSesion()
{
    $email = $_POST['email'];
    $pass = $_POST['pass'];
    $sesion = new usuario($email, $pass);
    if ($sesion->validacionDeUser()) {
        if ($sesion->validacionDePass()) {
            if (isset($_SESSION['recuperar'])) {
                unset($_SESSION['recuperar']);
            }
            echo "
                <script>
                    $('#pantalla_carga').css('display', 'flex');
                    window.location.href = '../ajustes/panel.php';
                </script>
                ";
        } else {
            echo '
            <script>
                $("#informacion").hide();
                $(".contenido_session").css("padding-left", "65vh");
            </script>';
            if (isset($_SESSION['bloqueo'])) {
                $intento = $_SESSION['bloqueo']['intento'];
                if ($intento > 2) {
                    mensaje(0, "error", "Alerta!", "Esta apunto de ser bloqueado por superar el numero de intentos", "true", null);
                } else {
                    mensaje(0, "error", "error", "Contraseña incorrecta", "true", null);
                }
            } else {
                mensaje(0, "error", "error", "Contraseña incorrecta", "true", null);
            }
        }
    } else {
        echo '
            <script>
                $("#informacion").hide();
                $(".contenido_session").css("padding-left", "65vh");
            </script>';
        mensaje(0, "error", "error", "Usuario no encontrado", "true", null);
    }
}
function enviarCorreo($email, $asunto, $mensaje, $alt, $path)
{
    require '../../librerias/PHPMailer/src/Exception.php';
    require '../../librerias/PHPMailer/src/PHPMailer.php';
    require '../../librerias/PHPMailer/src/SMTP.php';

    //Create an instance; passing `true` enables exceptions
    $mail = new PHPMailer(true);
    $dataAjustes = mostrarDatosAjustes();
    try {
        //Server settings
        $mail->SMTPDebug = 0;
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = $dataAjustes['Correo'];
        $mail->Password   = $dataAjustes['Pass'];
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = 465;
        //Recipients
        $mail->setFrom($dataAjustes['Correo'], $dataAjustes['Nombre']);
        $mail->addAddress($email, '');
        //Content
        $mail->isHTML(true);
        if ($path != null) {
            $mail->AddAttachment($path);
        }
        $mail->Subject =  $asunto;
        $mail->Body    =  $mensaje;
        $mail->AltBody =  $alt;
        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}
function registrarAdministrador()
{
    $nombre = $_POST['nombre'];
    $nombreUsuario = explode(' ', trim($nombre));
    $user = $nombreUsuario[0] . " " . $nombreUsuario[2];
    $pass = $_POST['pass'];
    $pass2 = $_POST['pass2'];
    $cedula = $_POST['cedula'];
    $email = $_POST['email'];
    if ($pass != $pass2) {
        mensaje(0, "error", "error", "Las contraseñas no coinciden!", "true", null);
    } else {
        $sesion = new usuario($user, $pass);
        if ($sesion->registrarAdministrador($nombre, $email, $cedula)) {
            mensaje(0, "success", "Registro exitoso!", "", "true", "sesion.php?pg=0");
        } else {
            mensaje(0, "error", "No se ha podido completar el registro", "Compruebe que sus datos son correctos", "true", null);
        }
    }
}
function registrar()
{
    $nombre = $_POST['nombre'];
    $nombreUsuario = explode(' ', trim($nombre));
    $user = $nombreUsuario[0] . " " . $nombreUsuario[2];
    $cedula = $_POST['cedula'];
    $pass = $_POST['cedula'];
    $email = $_POST['email'];
    $rol = $_POST['rol'];
    $sesion = new usuario($user, $pass);
    if ($sesion->registrar($nombre, $email, $rol, $cedula)) {
        mensaje(0, "success", "Registro exitoso!", "", "false", "ajustes.php?p=3");
    } else {
        mensaje(0, "error", "No se ha podido completar el registro", "Compruebe que sus datos son correctos", "true", null);
    }
}
function recuperarContraseña()
{
    if(isset($_POST['email'])){
        $email = $_POST['email'];
    }else{
        $email = $_SESSION['recuperar'][0]['email'];
    }
    $user = "n/a";
    $pass = 0;
    $sesion = new usuario($user, $pass);
    if ($sesion->recuperarContraseña($email)) {
        $asunto = 'Correo de verificacion';
        $codigo = rand(0000, 9999);
        $_SESSION['recuperar']['Codigo'] = $codigo;
        $mensaje = '
        <body>
            <h1>Codigo de verificacion</h1>
            <p>Introduzca el codigo en la pagina para poder confirmar su identidad</p>
            <div class="codigo">
                <b>' . $codigo . '</b>
            </div>
        </body>
        ';
        $alt = "Su codigo de verificacion es: $codigo";
        if (enviarCorreo($email, $asunto, $mensaje, $alt, "")) {
            mensaje(1, "success", "Correo de confirmacion enviado!", "", "false", "?pg=3");
        } else {
            mensaje(0, "error", "No se ha podido enviar el correo", "Asegurese de que los datos son correctos", "true", null);
        }
    } else {
        mensaje(0, "error", "No se ha encontrado el Email en la base de datos", "Asegurese de que lo ha escrito bien", "true", null);
    }
}
function validarCodigo()
{
    $codigo1 = $_POST['codigo1'];
    $codigo2 = $_POST['codigo2'];
    $codigo3 = $_POST['codigo3'];
    $codigo4 = $_POST['codigo4'];
    $codigo = $codigo1 . $codigo2 . $codigo3 . $codigo4;
    if ($codigo == $_SESSION['recuperar']['Codigo']) {
        header('location:sesion.php?pg=4');
    } else {
        if (empty($_SESSION['recuperar']['intento'])) {
            $_SESSION['recuperar']['intento'] = 1;
        } else {
            if ($_SESSION['recuperar']['intento'] != 3) {
                $_SESSION['recuperar']['intento']++;
            } else {
                unset($_SESSION['recuperar']);
                header('location:sesion.php?pg=0');
            }
        }
        mensaje(0, "error", "Error!", "Codigo invalido", "false", "");
    }
}
function cambiarContraseña()
{
    $recuperar = $_SESSION['recuperar'];
    $email = $recuperar[0]['email'];
    $pass1 = $_POST['pass'];
    $pass2 = $_POST['pass2'];
    if ($pass1 == $pass2) {
        $sesion = new usuario($email, $pass1);
        if ($sesion->cambiarContraseña()) {
            unset($_SESSION['recuperar']);
            mensaje(1, "success", "Cambio de contraseña exitoso!", "", "false", "");
        } else {
            mensaje(0, "error", "Error!", "No se ha podido cambiar la contraseña", "false", "");
        }
    } else {
        mensaje(0, "error", "Error!", "Las contraseñas no son iguales", "false", "");
    }
}
function modificarInformacion()
{
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $id = $_POST['id'];
    $sesion = new usuario($id, "N/A");
    if ($sesion->modificarInformacion($nombre, $email)) {
        mensaje(1, "success", "Informacion editada correctamente!", "", "false", "?");
    } else {
        mensaje(0, "error", "No se ha podido editar la informacion!", "Intente nuevamente", "true", "");
    }
}
function eliminarUsuario($id)
{
    if (usuario::eliminarUsuario($id)) {
        mensaje(1, "success", "Usuario eliminado!", "", "false", "?pg=3");
    } else {
        mensaje(0, "error", "No se ha podido eliminar el usuario!", "Intente nuevamente", "true", "");
    }
}

///PRODUCTOS///////////////////////////////////////////////////////////////////////
function EliminarProducto($id)
{
    $producto = new Productos($id, "", "", "", "", "");
    if ($producto->EliminarProducto()) {
        mensaje(0, "success", "Eliminado!", "El producto fue eliminado con exito", "true", "Productos.php");
    } else {
        mensaje(0, "error", "Error!", "No se pudo eliminar el producto", "true", "");
    }
}
function EditarProducto()
{
    $conectar = new Conectar();
    $conexion = $conectar->conexion();
    $id = $_POST['ID'];
    $nom = $_POST['nom'];
    $Prc = $_POST['prc'];
    $nom2 = $_POST['nom2'];
    $Prc2 = $_POST['prc2'];
    $idCategoria2 = $_POST['cat2'];
    $idCategoria = $_POST['cat'];
    $imga = $_POST['imga'];
    $cont = 0;
    if (isset($_FILES['img']['name']) && $_FILES['img']['name'] != null) {
        $sqlimg = "SELECT * from productos WHERE Imagen = '$imga'";
        $resultimg = mysqli_query($conexion, $sqlimg);
        while ($mostrar = mysqli_fetch_array($resultimg)) {
            $cont = $cont + 1;
        }
        $nombreImg = $_FILES['img']['name'];
        $ruta      = $_FILES['img']['tmp_name'];
        $destino   = "../../imagenes/" . $nombreImg;
        if (move_uploaded_file($ruta, $destino)) {
            $producto = new Productos($id, $nom, 0, $Prc, $idCategoria, $nombreImg);
        }
    } else {
        $producto = new Productos($id, $nom, 0, $Prc, $idCategoria, $imga);
    }
    if ($producto->EditarProducto($Prc2, $idCategoria2, $nom2)) {
        if (file_exists("../../imagenes/" . $imga) && $cont == 1) {
            unlink("../../imagenes/" . $imga);
        }
        mensaje(0, "success", "Echo!", "El producto fue modificado con exito", "true", "editar.php?id=$id");
    } else {
        mensaje(0, "error", "Error!", "No se pudo modificar el producto", "true", "");
    }
}
function AgregarProducto()
{
    $id = $_POST['ID'];
    $nom = $_POST['nom'];
    $CAN = $_POST['can'];
    $Prc = $_POST['prc'];
    $idCategoria = $_POST['cat'];
    if (isset($_FILES['img']['name']) && $_FILES['img']['name'] != null) {
        $nombreImg = $_FILES['img']['name'];
        $ruta      = $_FILES['img']['tmp_name'];
        $destino   = "../../imagenes/" . $nombreImg;
        if (move_uploaded_file($ruta, $destino)) {
            $producto = new Productos($id, $nom, $CAN, $Prc, $idCategoria, $nombreImg);
        } else {
            mensaje(0, "error", "Error!", "No se pudo guardar la imagen", "true", "");
        }
    } else {
        $producto = new Productos($id, $nom, $CAN, $Prc, $idCategoria, 'N/A');
    }
    if ($producto->AgregarProducto()) {
        mensaje(0, "success", "Echo!", "El producto fue agregado con exito", "true", "agregar.php");
    } else {
        mensaje(0, "error", "Error!", "No se pudo agregar el producto", "true", "");
    }
}

///CATEGORIA///////////////////////////////////////////////////////////////////////
function AgregarCategoria()
{
    $id = $_POST['id'];
    $nombre = $_POST['categoriaNueva'];
    if ($nombre == null) {
        mensaje(0, "error", "No se puede crear categorias vacias!", "Intente nuevamente", "true", "");
    } else {
        $categoria = new Categoria($nombre, $id);
        if ($categoria->agregarCategoria()) {
            echo "
            <script>
            window.location.href = '?p=2';
            </script>
            ";
        } else {
            mensaje(0, "error", "No se ha podido agregar la categoria!", "Intente nuevamente", "true", "");
        }
    }
}
function EditarCategoria()
{
    $id = $_POST['id'];
    $nombre = $_POST['nuevoNombre'];
    if ($nombre == null) {
        mensaje(0, "error", "No se puede crear categorias vacias!", "Intente nuevamente", "true", "");
    } else {
        $categoria = new Categoria($nombre, $id);
        if ($categoria->editarCategoria()) {
            echo "
            <script>
            window.location.href = '?p=2';
            </script>
            ";
        } else {
            mensaje(0, "error", "No se ha podido editar la categoria!", "Intente nuevamente", "true", "");
        }
    }
}
function EliminarCategoria($id)
{
    if (categoria::eliminarCategoria($id)) {
        echo "
            <script>
            window.location.href = '?p=2';
            </script>
            ";
    } else {
        mensaje(0, "error", "No se puede eliminar esta categoria!", "Hay productos que la estan utilizando", "true", "");
    }
}

///ROLES///////////////////////////////////////////////////////////////////////
function AgregarRoles()
{
    $id = $_POST['id'];
    $nombre = $_POST['rolNuevo'];
    if ($nombre == null) {
        mensaje(0, "error", "No se puede crear roles vacios!", "Intente nuevamente", "true", "");
    } else {
        $categoria = new Roles($nombre, $id);
        if ($categoria->agregarRoles()) {
            echo "
            <script>
            window.location.href = '?p=2&a=2';
            </script>
            ";
        } else {
            mensaje(0, "error", "No se ha podido agregar el rol!", "Intente nuevamente", "true", "");
        }
    }
}
function EditarRoles()
{
    $id = $_POST['id'];
    $nombre = $_POST['nuevoNombreRol'];
    if ($nombre == null) {
        mensaje(0, "error", "No se puede crear roles vacios!", "Intente nuevamente", "true", "");
    } else {
        $categoria = new Roles($nombre, $id);
        if ($categoria->editarRoles()) {
            echo "
            <script>
            window.location.href = '?p=2&a=2';
            </script>
            ";
        } else {
            mensaje(0, "error", "No se ha podido editar el rol!", "Intente nuevamente", "true", "");
        }
    }
}
function EliminarRoles($id)
{
    if (Roles::eliminarRoles($id)) {
        echo "
            <script>
            window.location.href = '?p=2&a=2';
            </script>
            ";
    } else {
        mensaje(0, "error", "No se puede eliminar este rol!", "Hay usuarios que lo estan utilizando", "true", "");
    }
}

///CLIENTES///////////////////////////////////////////////////////////////////////
function EliminarCliente($id)
{
    if (clientes::EliminarCliente($id)) {
        mensaje(0, "success", "Eliminado!", "El cliente fue eliminado con exito", "true", "Clientes.php");
    } else {
        mensaje(0, "error", "Error!", "No se puede eliminar este cliente", "true", "");
    }
}
function EditarCliente()
{
    $ID = $_POST['ID'];
    $ID2 = $_POST['ID2'];
    $NOM = $_POST['NOM'];
    $TEL = $_POST['TEL'];
    $DIREC = $_POST['DIREC'];
    $CIUDAD = $_POST['CIUDAD'];
    $CORREO = $_POST['CORREO'];
    $cliente = new clientes($ID, $NOM, $TEL, $DIREC, $CIUDAD, $CORREO);
    if ($cliente->EditarCliente($ID2)) {
        mensaje(0, "success", "Echo!", "La informacion fue modificada con exito", "true", "Clientes.php");
    } else {
        mensaje(0, "error", "Error!", "No se pudo modificar la informacion", "true", "");
    }
}
function AgregarCliente()
{
    $ID = $_POST['ID'];
    $NOM = $_POST['NOM'];
    $TEL = $_POST['TEL'];
    $DIREC = $_POST['DIREC'];
    $CIUDAD = $_POST['CIUDAD'];
    $CORREO = $_POST['CORREO'];
    $cliente = new clientes($ID, $NOM, $TEL, $DIREC, $CIUDAD, $CORREO);
    if ($cliente->AgregarCliente()) {
        mensaje(0, "success", "Echo!", "El cliente fue agregado con exito", "true", "Clientes.php");
    } else {
        mensaje(0, "error", "Error!", "No se agrego el cliente", "true", "");
    }
}
function AgregarClienteFactura($nit, $nom, $tel, $dire, $ciu, $correo)
{
    $cliente = new clientes($nit, $nom, $tel, $dire, $ciu, $correo);
    if ($cliente->AgregarCliente()) {
        return true;
    } else {
        return false;
    }
}

///PROVEEDORES///////////////////////////////////////////////////////////////////////
function EliminarProveedor($id)
{
    if (proveedores::EliminarProveedor($id)) {
        mensaje(0, "success", "Eliminado!", "El proveedor fue eliminado con exito", "true", "Proveedores.php");
    } else {
        mensaje(0, "error", "Error!", "No se puede eliminar este proveedor", "true", "");
    }
}
function EditarProveedor()
{
    $ID = $_POST['ID'];
    $ID2 = $_POST['ID2'];
    $NOM = $_POST['NOM'];
    $TEL = $_POST['TEL'];
    $DIREC = $_POST['DIREC'];
    $CIUDAD = $_POST['CIUDAD'];
    $proveedor = new proveedores($ID, $NOM, $TEL, $DIREC, $CIUDAD);
    if ($proveedor->EditarProveedor($ID2)) {
        mensaje(0, "success", "Echo!", "La informacion fue modificada con exito", "true", "Proveedores.php");
    } else {
        mensaje(0, "error", "Error!", "No se ha podido modificar la informacion", "true", "");
    }
}
function AgregarProveedor()
{
    $ID = $_POST['ID'];
    $NOM = $_POST['NOM'];
    $TEL = $_POST['TEL'];
    $DIREC = $_POST['DIREC'];
    $CIUDAD = $_POST['CIUDAD'];
    $proveedor =  new proveedores($ID, $NOM, $TEL, $DIREC, $CIUDAD);
    if ($proveedor->AgregarProveedor()) {
        mensaje(0, "success", "Echo!", "El proveedor fue agregado con exito", "true", "Proveedores.php");
    } else {
        mensaje(0, "error", "Error!", "No se pudo agregar el cliente", "true", "");
    }
}
function AgregarProveedorFactura($nit, $nom, $dire, $ciu, $tel)
{
    $cliente = new proveedores($nit, $nom, $dire, $ciu, $tel);
    if ($cliente->AgregarProveedor()) {
        return true;
    } else {
        return false;
    }
}

///FACTURA SALIDA///////////////////////////////////////////////////////////////////////
function HistorialFactura_salida($nom, $nroFac, $total)
{
    $factura = new facturas_salida($nroFac, $nom, $total);
    if ($factura->AgregarHistorial()) {
        mensaje(0, "success", "Echo!", "Factura generada con exito", "false", "");
        echo "
            <script>
            $('#pantalla_carga').css('display', 'none');
            </script>
            ";
    } else {
        mensaje(0, "error", "Error!", "Error al generar la factura", "true", "");
    }
}
function AgregarFacturaSalida($nom, $nroFac, $total, $fech)
{
    $factura = new facturas_salida($nroFac, $nom, $total);
    if ($factura->AgregarFactura($fech)) {
        return true;
    } else {
        return false;
    }
}
function AgregarDetalleSalida($cant, $id, $prc, $subtotal, $nroFac, $cantidad, $producto)
{
    if (facturas_salida::AgregarDetalle($cant, $id, $prc, $subtotal, $nroFac, $cantidad, $producto)) {
        return true;
    } else {
        return false;
    }
}
if (isset($_POST['factura'])) {
    session_start();
    $factura = $_SESSION['factura'];
    $id = $_POST['id'];
    $cant = $_POST['cant'];
    $cant2 = $_POST['cant2'];
    $prc = $_POST['prc'];
    $cantT = $cant2 - $cant;
    $total = $cant * $prc;
    if ($factura != null) {
        for ($i = 0; $i <= count($factura) - 1; $i++) {
            if ($id == $factura[$i]['producto']) {
                $factura[$i] = array("producto" => $id, "cantidad" => $cant, "subtotal" => $total);
                $_SESSION['factura'] = $factura;
                break;
            } elseif ($i == count($factura) - 1) {
                $factura[] = array("producto" => $id, "cantidad" => $cant, "subtotal" => $total);
                $_SESSION['factura'] = $factura;
            }
        }
        header("location:" . $_SERVER['HTTP_REFERER'] . "");
    } else {
        $factura[] = array("producto" => $id, "cantidad" => $cant, "subtotal" => $total);
        $_SESSION['factura'] = $factura;
        header("location:" . $_SERVER['HTTP_REFERER'] . "");
    }
}
if (isset($_GET['bru'])) {
    session_start();
    $bru = $_GET['bru'];
    $factura = $_SESSION['factura'];
    if (count($factura) != 1) {
        for ($i = 0; $i <= count($factura) - 1; $i++) {
            if ($factura[$i]['producto'] != $bru) {
                $array[] = $factura[$i];
            }
        }
        $_SESSION['factura'] = $array;
    } else {
        unset($_SESSION['factura']);
    }
    header("location:../vista/Facturar/Salida.php");
}
if (isset($_POST['borrar'])) {
    session_start();
    unset($_SESSION['factura']);
    header("location:../vista/Facturar/Salida.php");
}

///FACTURA ENTRADA///////////////////////////////////////////////////////////////////////
function Historialfactura_entrada($nom, $nroFac, $total)
{
    $factura = new facturas_entrada($nroFac, $nom, $total);
    if ($factura->AgregarHistorial()) {
        mensaje(0, "success", "Echo!", "Factura guardada con exito", "false", "");
    } else {
        mensaje(0, "error", "Error!", "Error al guardar la factura", "true", "");
    }
}
function AgregarFacturaEntrada($nom, $nroFac, $total, $fech, $IDD)
{
    $factura = new facturas_entrada($nroFac, $nom, $total);
    if ($factura->AgregarFactura($fech, $IDD)) {
        return true;
    } else {
        return false;
    }
}
function AgregarDetalleEntrada($cant2, $id, $prc, $subtotal, $IDD, $cantidadProducto, $nombre, $precioProducto)
{
    if (facturas_entrada::AgregarDetalle($cant2, $id, $prc, $subtotal, $IDD, $cantidadProducto, $nombre, $precioProducto)) {
        return true;
    } else {
        return false;
    }
}
if (isset($_POST['entrada'])) {
    session_start();
    $entrada = $_SESSION['entrada'];
    $id = $_POST['id'];
    if ($entrada != null) {
        for ($i = 0; $i <= count($entrada) - 1; $i++) {
            if ($id == $entrada[$i]['producto']) {
                $entrada[$i] = array("producto" => $id);
                $_SESSION['entrada'] = $entrada;
                break;
            } elseif ($i == count($entrada) - 1) {
                $entrada[] = array("producto" => $id);
                $_SESSION['entrada'] = $entrada;
            }
        }
        header("location:" . $_SERVER['HTTP_REFERER'] . "");
    } else {
        $entrada[] = array("producto" => $id);
        $_SESSION['entrada'] = $entrada;
        header("location:" . $_SERVER['HTTP_REFERER'] . "");
    }
}
if (isset($_GET['bruE'])) {
    session_start();
    $bru = $_GET['bruE'];
    $factura = $_SESSION['entrada'];
    if (count($factura) != 1) {
        for ($i = 0; $i <= count($factura) - 1; $i++) {
            if ($factura[$i]['producto'] != $bru) {
                $array[] = $factura[$i];
            }
        }
        $_SESSION['entrada'] = $array;
    } else {
        unset($_SESSION['entrada']);
    }
    header("location:../vista/Facturar/Entrada.php");
}
if (isset($_POST['borrarE'])) {
    session_start();
    unset($_SESSION['entrada']);
    header("location:../vista/Facturar/Entrada.php");
}

///AJUSTES///////////////////////////////////////////////////////////////////////
function guardarAjustes()
{
    $session = $_SESSION['session'];
    $user = $session[0]['user'];
    $ganancia = $_POST['ganancia'];
    $auto = $_POST['auto'];
    $stock = $_POST['stock'];
    $ajuste = new ajustes($user, $ganancia, $auto, $stock);
    if ($ajuste->guardarAjustes()) {
        mensaje(1, "success", "Ajustes guardados exitosamente!", "", "false", "?");
    } else {
        mensaje(0, "error", "Error!", "No se han podido guardar los ajustes", "true", "");
    }
}
function panelDeControl()
{
    $consulta = ajustes::panelDeControl();
    if ($consulta) {
        return $consulta;
    } else {
        return false;
    }
}
function guardarPermisos()
{
    $id = $_POST['id'];
    $editar = $_POST['editar'];
    $agregar = $_POST['agregar'];
    $reportes = $_POST['reportes'];
    $entrada = $_POST['entradaProductos'];
    $salida = $_POST['salida'];
    $clientes = $_POST['clientes'];
    $proveedores = $_POST['proveedores'];
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $rol = $_POST['rol'];
    $cedula = $_POST['cedula'];
    $ajustes = new permisos($editar, $agregar, $reportes, $entrada, $salida, $clientes, $proveedores);
    if ($ajustes->guardarPermisos($id, $nombre, $email, $rol, $cedula)) {
        mensaje(1, "success", "Ajustes guardados exitosamente!", "", "false", "?p=3");
        echo "
                <script>
                $('.BrArriba').css('animation','none');
                </script>
                ";
    } else {
        mensaje(0, "error", "Error!", "No se han podido guardar los ajustes!", "true", "");
        echo "
                <script>
                $('.BrArriba').css('animation','none');
                </script>
                ";
    }
}
function guardarDatosEmpresa()
{
    $nombre = $_POST['nombre'];
    $nit = $_POST['nit'];
    $ciudad = $_POST['ciudad'];
    $direccion = $_POST['direccion'];
    if (isset($_FILES['img']['name']) && $_FILES['img']['name'] != null) {
        $ruta      = $_FILES['img']['tmp_name'];
        $destino   = "../../imagenes/icon.png";
        if (file_exists("../../imagenes/icon.png")) {
            unlink("../../imagenes/icon.png");
        }
        move_uploaded_file($ruta, $destino);
    }
    if (ajustes::guardarDatosEmpresa($nombre, $nit, $direccion, $ciudad)) {
        mensaje(1, "success", "Datos guardados exitosamente!", "", "false", "?p=2&a=3");
    } else {
        mensaje(0, "error", "No se han podido guardar los datos!", "Intente nuevamente", "true", "?p=2&a=3");
    }
}
if (isset($_GET['m'])) {
    session_start();
    $session = $_SESSION['session'];
    $hora = $session[0]['hora'];
    $fecha = $session[0]['fecha'];
    $user = $session[0]['user'];
    if ($_GET['m'] == 1) {
        $session[0] = array("fecha" => $fecha, "hora" => $hora, "user" => $user, "modo" => 1);
        $_SESSION['session'] = $session;
        header("location:../vista/ajustes/ajustes.php");
    } else {
        $session[0] = array("fecha" => $fecha, "hora" => $hora, "user" => $user, "modo" => 0);
        $_SESSION['session'] = $session;
        header("location:../vista/ajustes/ajustes.php");
    }
}
if (isset($_GET['bs'])) {
    session_start();
    unset($_SESSION['sms']);
    unset($_SESSION['session']);
    header("location:../vista/ajustes/sesion.php?pg=0");
}

///DATOS///////////////////////////////////////////////////////////////////////
function mostrarDatos($tabla, $id, $filtro, $codigo)
{
    $consulta = $tabla::mostrarDatos($id, $filtro, $codigo);
    if ($consulta) {
        return $consulta;
    } else {
        return false;
    }
}
function mostrarDatosAjustes()
{
    $consulta = ajustes::mostrarDatosAjustes();
    if ($consulta) {
        return $consulta;
    } else {
        return false;
    }
}
function mostrarDatosFactura($factura, $tipo, $fecha1, $fecha2)
{
    if ($factura == 0) {
        $consulta = facturas_salida::mostrarDatosSalidas($tipo, $fecha1, $fecha2);
    } else {
        $consulta = facturas_entrada::mostrarDatosSalidas($tipo, $fecha1, $fecha2);
    }
    if ($consulta) {
        return $consulta;
    } else {
        echo $consulta;
    }
}
if (isset($_GET["Reboot"])) {
    $pass = $_POST['passValidacion2'];
    echo $pass;
    if (usuario::validarPassAjustes($pass)) {
        if (ajustes::Reboot()) {
            session_start();
            unset($_SESSION['sms']);
            unset($_SESSION['session']);
            $files = glob('../imagenes/*');
            foreach ($files as $file) {
                if (is_file($file))
                    unlink($file);
            }
            header("location:../vista/ajustes/sesion.php?pg=0");
        } else {
            header("location:../vista/ajustes/ajustes.php?p=2&a=4&re=1");
        }
    } else {
        header("location:../vista/ajustes/ajustes.php?p=2&a=4&re=0");
    }
}
