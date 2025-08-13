<?php
require_once 'conexion.php';

///HISTORIAL///////////////////////////////////////////////////////////////////////////
class historial
{
    public $conexion;
    public $countIdHistorial;
    public $fecha;
    public $hora;
    public $countIdHistorial2;

    public function __construct()
    {
        $conectar = new Conectar();
        $this->conexion = $conectar->conexion();

        // fecha y hora
        date_default_timezone_set("America/Bogota");
        $this->fecha = date('d-m-Y');
        $this->hora = date('H:i:s');

        // Conocer cuantos registros hay
        $MAX = "SELECT Max(ID) As ID FROM historial";
        $resultMAX = mysqli_query($this->conexion, $MAX);
        if ($mostrarMAX = mysqli_fetch_array($resultMAX)) {
            $this->countIdHistorial = $mostrarMAX['ID'] + 1;
        }

        $MAX2 = "SELECT Max(ID) As ID FROM detalle_historial";
        $resultMAX2 = mysqli_query($this->conexion, $MAX2);
        if ($mostrarMAX2 = mysqli_fetch_array($resultMAX2)) {
            $this->countIdHistorial2 = $mostrarMAX2['ID'] + 1;
        }
    }

    public static function mostrarDatos($id, $filtro)
    {
        $conectar = new Conectar();
        $conexion = $conectar->conexion();

        $data = array(); // Inicializamos el array para evitar errores

        if ($id == null) {
            $query = mysqli_query($conexion, "SELECT * FROM historial ORDER BY ID DESC");
        } else {
            if ($filtro == 0) {
                $id = mysqli_real_escape_string($conexion, $id); // Previene inyección SQL
                $query = mysqli_query($conexion, "SELECT * FROM historial WHERE Fecha LIKE '$id%'");
            } else {
                $id = mysqli_real_escape_string($conexion, $id);
                $query = mysqli_query($conexion, "SELECT * FROM detalle_historial WHERE ID_historial = '$id'");
            }
        }

        if ($query && mysqli_num_rows($query) != 0) {
            while ($mostrar = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
                if ($filtro == 0) {
                    // Tabla 'historial'
                    $data[] = array(
                        "ID" => isset($mostrar['ID']) ? $mostrar['ID'] : 'No disponible',
                        "Fecha" => isset($mostrar['Fecha']) ? $mostrar['Fecha'] : 'No disponible',
                        "Hora" => isset($mostrar['Hora']) ? $mostrar['Hora'] : 'No disponible',
                        "Accion" => isset($mostrar['Accion']) ? $mostrar['Accion'] : 'No disponible',
                    );
                } else {
                    // Tabla 'detalle_historial'
                    $data[] = array(
                        "Dato1" => array_key_exists('Dato1', $mostrar) ? $mostrar['Dato1'] : null,
                        "Dato2" => array_key_exists('Dato2', $mostrar) ? $mostrar['Dato2'] : null,
                    );

                }
            }
        }

        return $data; // Retorna array vacío si no hay resultados
    }


    public function AgregarHistorial($historial, $detalle)
    {
        $queryHistorial = mysqli_query($this->conexion, $historial);
        $queryDetalleHistorial = mysqli_query($this->conexion, $detalle);

        if ($queryHistorial && $queryDetalleHistorial) {
            return true;
        } else {
            return false;
        }
    }
}


///USUARIO/////////////////////////////////////////////////////////////////////////////
class usuario
{
    public $conexion;
    public $user;
    public $pass;

    public function  __construct($user, $pass)
    {
        $conectar = new Conectar();
        $this->conexion = $conectar->conexion();
        $this->user = $user;
        $this->pass = $pass;
    }
    public static function mostrarDatos($user, $id)
    {
        $conectar = new Conectar();
        $conexion = $conectar->conexion();
        if ($user == null) {
            $query = mysqli_query($conexion, "SELECT * from usuarios");
        } else {
            if ($user == "Rol") {
                $query = mysqli_query($conexion, "SELECT * from usuarios WhERE ID_Rol = $id");
            } else {
                if (is_numeric($user)) {
                    $query = mysqli_query($conexion, "SELECT * from usuarios WHERE ID = '$user'");
                } else {
                    $query = mysqli_query($conexion, "SELECT * from usuarios WHERE Usuario = '$user'");
                }
            }
        }
        if ($query) {
            if (mysqli_num_rows($query) != 0) {
                while ($mostrar = mysqli_fetch_array($query)) {
                    $data[] = array(
                        "Permisos" => $mostrar['ID_Permisos'],
                        "Usuario" => $mostrar['Usuario'],
                        "Nombre" => $mostrar['Nombre'],
                        "Cedula" => $mostrar['Cedula'],
                        "Email" => $mostrar['Email'],
                        "Rol" => $mostrar['ID_Rol'],
                        "ID" => $mostrar['ID'],
                    );
                }
                return $data;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
    public function validacionDeUser()
    {
        $comprobar = mysqli_query($this->conexion, "SELECT * FROM usuarios WHERE Email = '$this->user'");
        if ($mostrar = mysqli_fetch_array($comprobar)) {
            return true;
        } else {
            return false;
        }
    }
    public function validacionDePass()
    {
        $comprobar = mysqli_query($this->conexion, "SELECT * FROM usuarios WHERE Email = '$this->user'");
        if ($mostrar = mysqli_fetch_array($comprobar)) {
            $user = $mostrar['Usuario'];
            $pass2 = $mostrar['Pass'];
            $id = $mostrar['ID'];
            $sms  = null;
            if ($this->pass == $pass2) {
                if (isset($_SESSION['bloqueo'])) {
                    unset($_SESSION['bloqueo']);
                }
                date_default_timezone_set("America/Bogota");
                $session[0] = array("fecha" => date('d-m-Y'), "hora" => date('H:i:s'), "user" => $id, "modo" => 0);
                $result = mysqli_query($this->conexion, "SELECT * from Productos ORDER BY ID_Producto");
                $ajustes = mysqli_query($this->conexion, "SELECT * from ajustes");
                if ($comprobar = mysqli_fetch_array($ajustes)) {
                    $stock = $comprobar['Stock'];
                    while ($mostrar = mysqli_fetch_array($result)) {
                        if ($mostrar['Cantidad'] < $stock) {
                            $sms[] = array("Producto" => $mostrar['Producto'], "id" => $mostrar['ID_Producto']);
                        }
                    }
                }
                if ($sms != null) {
                    $_SESSION['sms'] = $sms;
                }
                $_SESSION['session'] = $session;
                // Historial
                $H = new historial();
                $historial = "INSERT INTO historial VALUES('$H->countIdHistorial','$H->fecha', '$H->hora', 'Se inicio sesion con el usuario <b>$user</b>')";
                $detalle = "INSERT INTO detalle_historial VALUES('$H->countIdHistorial2','$user','Inicio de sesion','$H->countIdHistorial')";
                if ($H->AgregarHistorial($historial, $detalle)) {
                    return true;
                } else {
                    return false;
                }
                //Fin Historial
            } else {
                date_default_timezone_set("America/Bogota");
                if (isset($_SESSION['bloqueo'])) {
                    $intento = $_SESSION['bloqueo']['intento'];
                    $intento++;
                    $fechaEntrada = date('Y-m-d H:i:s');
                    $fechaAuxiliar    = strtotime("5 minutes", strtotime($fechaEntrada));
                    $final = date('Y-m-d H:i:s', $fechaAuxiliar);
                    $date = array('inicio' => $fechaEntrada, "intento" => $intento, "final" => $final);
                    $_SESSION['bloqueo'] = $date;
                } else {
                    $date = array("intento" => 1);
                    $_SESSION['bloqueo'] = $date;
                }
                return false;
            }
        }
    }
    public function registrarAdministrador($nombre, $email, $cedula)
    {
        $asignarRol = mysqli_query($this->conexion, "INSERT INTO rol VALUES(0,'Administrador')");
        $permisos = mysqli_query($this->conexion, "INSERT INTO permisos VALUES(0,1,1,1,1,1,1,1);");
        $ejecutar = mysqli_query($this->conexion, "INSERT INTO usuarios VALUES(0,$cedula,'$this->user','$nombre','$email','$this->pass',0,0)");
        if ($ejecutar) {
            // Historial
            $H = new historial();
            $historial = "INSERT INTO historial VALUES('$H->countIdHistorial','$H->fecha', '$H->hora', 'Se registro un nuevo usuario 'Administrador' con el nombre <b>$this->user</b>')";
            $detalle = "INSERT INTO detalle_historial VALUES('$H->countIdHistorial2','$this->user','<b>- ID: </b>0<br><b>- Nombre: </b>$this->user<br><b>- Email: </b>$email','$H->countIdHistorial')";
            $H->AgregarHistorial($historial, $detalle);
            //Fin Historial
            return true;
        } else {
            return false;
        }
    }
    public function registrar($nombre, $email, $rol, $cedula)
    {
        $resultMaxId = mysqli_query($this->conexion, "SELECT Max(ID) As ID FROM usuarios");
        if ($mostrarMaxId = mysqli_fetch_array($resultMaxId)) {
            $id = $mostrarMaxId['ID'] + 1;
            $comprobarEmail = mysqli_query($this->conexion, "SELECT * FROM usuarios WHERE Email = '$email'");
            if ($mostrarEmail = mysqli_fetch_array($comprobarEmail)) {
                return false;
            } else {
                $result = mysqli_query($this->conexion, "SELECT Max(ID) As ID FROM permisos");
                if ($mostrar = mysqli_fetch_array($result)) {
                    $idPermisos = $mostrar['ID'] + 1;
                    $permisos = mysqli_query($this->conexion, "INSERT INTO permisos VALUES($idPermisos,1,1,1,1,1,1,1)");
                }
            }
        }
        if ($permisos) {
            $ejecutar = mysqli_query($this->conexion, "INSERT INTO usuarios VALUES('$id','$cedula','$this->user','$nombre','$email','$this->pass',$rol,$idPermisos)");
            if ($ejecutar) {
                // Historial
                $H = new historial();
                $historial = "INSERT INTO historial VALUES('$H->countIdHistorial','$H->fecha', '$H->hora', 'Se registro un nuevo usuario con el usuario <b>$this->user</b>')";
                $detalle = "INSERT INTO detalle_historial VALUES('$H->countIdHistorial2','$this->user','<b>- ID: </b>$id<br><b>- Nombre: </b>$this->user<br><b>- Email: </b>$email','$H->countIdHistorial')";
                $H->AgregarHistorial($historial, $detalle);
                //Fin Historial
                return true;
            } else {
                $BorrarPermisos = mysqli_query($this->conexion, "DELETE FROM permisos WHERE ID = $idPermisos");
                return false;
            }
        }
    }
    public function recuperarContraseña($email)
    {
        $comprobarEmail = mysqli_query($this->conexion, "SELECT * FROM usuarios WHERE Email = '$email'");
        if ($mostrarEmail = mysqli_fetch_array($comprobarEmail)) {
            if (isset($_SESSION['recuperar'])) {
                $recuperar = $_SESSION['recuperar'];
            }
            $recuperar[0] = array("email" => $email);
            $_SESSION['recuperar'] = $recuperar;
            return true;
        } else {
            return false;
        }
    }
    public function cambiarContraseña()
    {
        $idUsuario = mysqli_query($this->conexion, "SELECT * FROM usuarios WHERE Email = '$this->user'");
        if ($mostrarID = mysqli_fetch_array($idUsuario)) {
            $id = $mostrarID['ID'];
            $actualizarContraseña = mysqli_query($this->conexion, "UPDATE `usuarios` SET `Pass` = '$this->pass' WHERE `usuarios`.`ID` = '$id';");
            if ($actualizarContraseña) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
    public function modificarInformacion($nombre, $email)
    {
        $nombreUsuario = explode(' ', trim($nombre));
        $user = $nombreUsuario[0] . " " . $nombreUsuario[2];
        $modificar = mysqli_query($this->conexion, "UPDATE usuarios SET Usuario = '$user' , Nombre = '$nombre' , Email = '$email' WHERE ID = $this->user");
        if ($modificar) {
            if (isset($_SESSION['session'])) {
                $session = $_SESSION['session'];
                $hora = $session[0]['hora'];
                $fecha = $session[0]['fecha'];
                $modo = $session[0]['modo'];
            }
            $session[0] = array("fecha" => $fecha, "hora" => $hora, "user" => $user, "modo" => $modo);
            $_SESSION['session'] = $session;
            return true;
        } else {
            return false;
        }
    }
    public static function eliminarUsuario($id)
    {
        $conectar = new Conectar();
        $conexion = $conectar->conexion();
        $resultID = mysqli_query($conexion, 'SELECT * FROM usuarios');
        $IDD = mysqli_num_rows($resultID);
        $query = mysqli_query($conexion, "DELETE FROM permisos WHERE ID = $id");
        $id2 = $IDD - $id;
        $id3 = $id + 1;
        while ($id2 != 0) {
            $actualizarID = mysqli_query($conexion, "UPDATE usuarios SET ID = '$id' WHERE ID = '$id3'");
            $id++;
            $id3++;
            $id2 = $id2 - 1;
        }
        if ($query) {
            return true;
        } else {
            return false;
        }
    }
    public static function validarPassAjustes($pass)
    {
        $conectar = new Conectar();
        $conexion = $conectar->conexion();
        $comprobar = mysqli_query($conexion, "SELECT * FROM usuarios WHERE ID_Rol = 0");
        if ($mostrar = mysqli_fetch_array($comprobar)) {
            $pass2 = $mostrar['Pass'];
        }
        if ($pass2 == $pass) {
            return true;
        } else {
            return false;
        }
    }
}
///CATEGORIA///////////////////////////////////////////////////////////////////////////
class Categoria
{
    public $conexion;
    public $nombre;
    public $id;

    public function  __construct($nombre, $id)
    {
        $conectar = new Conectar();
        $this->conexion = $conectar->conexion();
        $this->nombre = $nombre;
        $this->id = $id;
    }
    public static function mostrarDatos($id)
    {
        $conectar = new Conectar();
        $conexion = $conectar->conexion();
        if ($id == null) {
            $query = mysqli_query($conexion, "SELECT * from categoria");
        } else {
            if (is_numeric($id)) {
                $query = mysqli_query($conexion, "SELECT * from categoria WhERE ID_Categoria = '$id'");
            } else {
                $query = mysqli_query($conexion, "SELECT * from categoria WhERE Nombre = '$id'");
            }
        }
        if ($query) {
            if (mysqli_num_rows($query) != 0) {
                while ($mostrar = mysqli_fetch_array($query)) {
                    $data[] = array(
                        "ID" => $mostrar['ID_Categoria'],
                        "Nombre" => $mostrar['Nombre']
                    );
                }
                return $data;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
    public function agregarCategoria()
    {
        $query = mysqli_query($this->conexion, "INSERT INTO categoria VALUES('$this->id','$this->nombre')");
        if ($query) {
            return true;
        } else {
            return false;
        }
    }
    public static function eliminarCategoria($id)
    {
        $conectar = new Conectar();
        $conexion = $conectar->conexion();
        $query = mysqli_query($conexion, "DELETE FROM categoria WHERE ID_Categoria = '$id'");
        if ($query) {
            return true;
        } else {
            return false;
        }
    }
    public function editarCategoria()
    {
        $query = mysqli_query($this->conexion, "UPDATE categoria SET `Nombre` = '$this->nombre' WHERE `categoria`.`ID_Categoria` = $this->id");
        if ($query) {
            return true;
        } else {
            return false;
        }
    }
}
///ROLES///////////////////////////////////////////////////////////////////////////////
class Roles
{
    public $conexion;
    public $id;
    public $nombre;

    public function  __construct($nombre, $id)
    {
        $conectar = new Conectar();
        $this->conexion = $conectar->conexion();
        $this->id = $id;
        $this->nombre = $nombre;
    }
    public static function mostrarDatos($id)
    {
        $conectar = new Conectar();
        $conexion = $conectar->conexion();
        if ($id == null) {
            $query = mysqli_query($conexion, "SELECT * from rol");
        } else {
            if (is_numeric($id)) {
                $query = mysqli_query($conexion, "SELECT * from rol WhERE ID = '$id'");
            } else {
                $query = mysqli_query($conexion, "SELECT * from rol WhERE Cargo = '$id'");
            }
        }
        if ($query) {
            if (mysqli_num_rows($query) != 0) {
                while ($mostrar = mysqli_fetch_array($query)) {
                    $data[] = array(
                        "ID" => $mostrar['ID'],
                        "Nombre" => $mostrar['Cargo']
                    );
                }
                return $data;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
    public function agregarRoles()
    {
        $query = mysqli_query($this->conexion, "INSERT INTO rol VALUES('$this->id','$this->nombre')");
        if ($query) {
            return true;
        } else {
            return false;
        }
    }
    public static function eliminarRoles($id)
    {
        $conectar = new Conectar();
        $conexion = $conectar->conexion();
        $query = mysqli_query($conexion, "DELETE FROM rol WHERE ID = '$id'");
        if ($query) {
            return true;
        } else {
            return false;
        }
    }
    public function editarRoles()
    {
        $query = mysqli_query($this->conexion, "UPDATE rol SET `Cargo` = '$this->nombre' WHERE `rol`.`ID` = $this->id");
        if ($query) {
            return true;
        } else {
            return false;
        }
    }
}
///PRODUCTOS///////////////////////////////////////////////////////////////////////////
class Productos
{
    public $conexion;
    public $id;
    public $nom;
    public $cant;
    public $prc;
    public $cate;
    public $img;

    public function  __construct($id, $nom, $cant, $prc, $cate, $img)
    {
        $conectar = new Conectar();
        $this->conexion = $conectar->conexion();
        $this->id = $id;
        $this->producto = $nom;
        $this->cantidad = $cant;
        $this->precioCaja = $prc;
        $this->categoria = $cate;
        $this->imagen = $img;
    }
    public static function mostrarDatos($id, $filtro, $codigo)
    {
        $conectar = new Conectar();
        $conexion = $conectar->conexion();
        if ($codigo != null) {
            if (is_numeric($codigo)) {
                $query = mysqli_query($conexion, "SELECT * from Productos WHERE ID_Producto LIKE '$codigo%'");
            } else {
                if ($codigo == "categoria") {
                    $query = mysqli_query($conexion, "SELECT * from Productos WhERE ID_Categoria = '$id'");
                } else {
                    if ($filtro == null) {
                        $query = mysqli_query($conexion, "SELECT * from Productos WHERE Producto LIKE '$codigo%'");
                    } else {
                        $query = mysqli_query($conexion, "SELECT * from productos WHERE Producto = '$codigo'");
                    }
                }
            }
        } else {
            if ($id != null) {
                $query = mysqli_query($conexion, "SELECT * from Productos WHERE ID_Producto = '$id'");
            } else {
                if ($filtro != 0) {
                    $query = mysqli_query($conexion, "SELECT * from Productos WHERE ID_Categoria = '$filtro' ORDER BY ID_Producto");
                } else {
                    $query = mysqli_query($conexion, "SELECT * from Productos ORDER BY ID_Producto");
                }
            }
        }
        if ($query) {
            if (mysqli_num_rows($query) != 0) {
                while ($mostrar = mysqli_fetch_array($query)) {
                    $consulta = Categoria::mostrarDatos($mostrar['ID_Categoria']);
                    $categoria = $consulta[0]['Nombre'];
                    $data[] = array(
                        "ID" => $mostrar['ID_Producto'],
                        "Producto" => $mostrar['Producto'],
                        "Cantidad" => $mostrar['Cantidad'],
                        "PrecioC" => $mostrar['PrecioC'],
                        "Categoria" => $categoria,
                        "Imagen" => $mostrar['Imagen'],
                    );
                }
                return $data;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
    public function EliminarProducto()
    {
        $resultID = mysqli_query($this->conexion, 'SELECT * FROM productos');
        $cantidadTotalDeProductos = mysqli_num_rows($resultID);
        $ejecutarDelete = mysqli_query($this->conexion, "DELETE FROM productos WHERE ID_Producto = '$this->id'");
        if ($ejecutarDelete) {
            $id = $this->id;
            $id2 = $cantidadTotalDeProductos - $id;
            $id3 = $id + 1;
            while ($id2 >= 0) {
                $actualizarID = mysqli_query($this->conexion, "UPDATE productos SET ID_Producto = '$id' WHERE ID_Producto = '$id3'");
                $id++;
                $id3++;
                $id2--;
            }
            // Historial
            $H = new historial();
            $historial = "INSERT INTO historial VALUES('$H->countIdHistorial','$H->fecha', '$H->hora', 'Se elimino el producto <b>$this->producto</b> con id <b>$this->id</b>')";
            $detalle = "INSERT INTO detalle_historial VALUES('$H->countIdHistorial2','$this->id - $this->producto','<b>- ID: </b>$this->id<br><b>- Nombre: </b>$this->producto<br><b>- Cantidad: </b>$this->cantidad<br><b>- Precio: </b>$this->precioCaja<br><b>- Categoria: </b>$this->categoria', '$H->countIdHistorial')";
            $H->AgregarHistorial($historial, $detalle);
            //Fin Historial
            return true;
        } else {
            return false;
        }
    }
    public function EditarProducto($Prc2, $idCategoria2, $nom2)
    {
        $categoriaSql = Categoria::mostrarDatos($this->categoria);
        $categoria = $categoriaSql[0]['ID'];
        $actualizar = "UPDATE productos SET Producto = '$this->producto' , PrecioC = '$this->precioCaja', ID_Categoria = '$categoria', Imagen = '$this->imagen' WHERE ID_Producto = '$this->id'";
        $ejecutar = mysqli_query($this->conexion, $actualizar);
        if ($ejecutar) {
            // Historial
            $H = new historial();
            $historial = "INSERT INTO historial VALUES('$H->countIdHistorial','$H->fecha', '$H->hora', 'Se modifico el producto <b>$this->producto</b> con id <b>$this->id</b>')";
            $detalle = "INSERT INTO detalle_historial VALUES('$H->countIdHistorial2','$this->imagen','<b>- ID: </b>$this->id<br><b>- Nombre: </b>$nom2<br><b>- Precio: </b>$Prc2<br><b>- Categoria: </b>$idCategoria2','$H->countIdHistorial')";
            $H->AgregarHistorial($historial, $detalle);
            //Fin Historial            
            return true;
        } else {
            return false;
        }
    }
    public function AgregarProducto()
    {
        $insert = "INSERT INTO productos VALUES('$this->id','$this->producto','$this->cantidad', '$this->precioCaja','$this->imagen', '$this->categoria')";
        $ejecutar = mysqli_query($this->conexion, $insert);
        if ($ejecutar) {
            // Historial
            $H = new historial();
            $historial = "INSERT INTO historial VALUES('$H->countIdHistorial','$H->fecha', '$H->hora', 'Se agrego un nuevo producto con el nombre de <b>$this->producto</b> y id <b>$this->id</b>')";
            $detalle = "INSERT INTO detalle_historial VALUES('$H->countIdHistorial2','$this->imagen','<b>- ID: </b>$this->id<br><b>- Nombre: </b>$this->producto<br><b>- Precio: </b>$this->precioCaja<br><b>- Categoria: </b>$this->categoria','$H->countIdHistorial')";
            $H->AgregarHistorial($historial, $detalle);
            //Fin Historial
            return true;
        } else {
            echo mysqli_error($this->conexion);
            return false;
        }
    }
}
///CLIENTES////////////////////////////////////////////////////////////////////////////
class clientes
{
    public $conexion;
    public $ID;
    public $NOM;
    public $TEL;
    public $DIREC;
    public $CIUDAD;
    public $CORREO;

    public function  __construct($ID, $NOM, $TEL, $DIREC, $CIUDAD, $CORREO)
    {
        $conectar = new Conectar();
        $this->conexion = $conectar->conexion();
        $this->ID = $ID;
        $this->Nombre = $NOM;
        $this->Telefono = $TEL;
        $this->Direccion = $DIREC;
        $this->Ciudad = $CIUDAD;
        $this->Correo = $CORREO;
    }
    public static function mostrarDatos($id, $filtro, $codigo)
    {
        $conectar = new Conectar();
        $conexion = $conectar->conexion();
        if ($codigo != null) {
            if (is_numeric($codigo)) {
                $query = mysqli_query($conexion, "SELECT * from Clientes WHERE ID LIKE '$codigo%'");
            } else {
                $query = mysqli_query($conexion, "SELECT * from Clientes WHERE Nombre LIKE '$codigo%'");
            }
        } else {
            if ($filtro == null) {
                $query = mysqli_query($conexion, "SELECT * from Clientes ORDER BY Nombre DESC");
            } else {
                $query = mysqli_query($conexion, "SELECT * FROM clientes Where ID = '$filtro'");
            }
        }
        if ($query) {
            if (mysqli_num_rows($query) != 0) {
                while ($mostrar = mysqli_fetch_array($query)) {
                    $data[] = array(
                        "ID" => $mostrar['ID'],
                        "Nombre" => $mostrar['Nombre'],
                        "Telefono" => $mostrar['Telefono'],
                        "Direccion" => $mostrar['Direccion'],
                        "Ciudad" => $mostrar['Ciudad'],
                        "Correo" => $mostrar['Correo']
                    );
                }
                return $data;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
    public static function EliminarCliente($id)
    {
        $conectar = new Conectar();
        $conexion = $conectar->conexion();
        $Delete = mysqli_query($conexion, "DELETE FROM clientes WHERE ID = '$id'");
        if ($Delete) {
            return true;
        } else {
            return false;
        }
    }
    public function EditarCliente($ID2)
    {
        $actualizar = "UPDATE clientes SET ID = '$this->ID' , nombre = '$this->Nombre', telefono = '$this->Telefono', direccion = '$this->Direccion', ciudad = '$this->Ciudad', Correo = '$this->Correo' WHERE ID = '$ID2'";
        $ejecutar = mysqli_query($this->conexion, $actualizar);
        if ($ejecutar) {
            return true;
        } else {
            return false;
        }
    }
    public function AgregarCliente()
    {
        $insert = "INSERT INTO clientes VALUES('$this->ID','$this->Nombre','$this->Telefono', '$this->Direccion','$this->Ciudad','$this->Correo')";
        $ejecutar = mysqli_query($this->conexion, $insert);
        if ($ejecutar) {
            return true;
        } else {
            return false;
        }
    }
}
///PROVEEDORES/////////////////////////////////////////////////////////////////////////
class proveedores
{
    public $conexion;
    public $ID;
    public $NOM;
    public $TEL;
    public $DIREC;
    public $CIUDAD;

    public function  __construct($ID, $NOM, $TEL, $DIREC, $CIUDAD)
    {
        $conectar = new Conectar();
        $this->conexion = $conectar->conexion();
        $this->ID = $ID;
        $this->Nombre = $NOM;
        $this->Telefono = $TEL;
        $this->Direccion = $DIREC;
        $this->Ciudad = $CIUDAD;
    }
    public static function mostrarDatos($id, $filtro, $codigo)
    {
        $conectar = new Conectar();
        $conexion = $conectar->conexion();
        if ($codigo != null) {
            if (is_numeric($codigo)) {
                $query = mysqli_query($conexion, "SELECT * from proveedores WHERE NIT LIKE '$codigo%'");
            } else {
                $query = mysqli_query($conexion, "SELECT * from proveedores WHERE Nombre LIKE '$codigo%'");
            }
        } else {
            if ($filtro == null) {
                $query = mysqli_query($conexion, "SELECT * from proveedores ORDER BY Nombre DESC");
            } else {
                $query = mysqli_query($conexion, "SELECT * FROM proveedores WHERE NIT = '$filtro'");
            }
        }
        if ($query) {
            if (mysqli_num_rows($query) != 0) {
                while ($mostrar = mysqli_fetch_array($query)) {
                    $data[] = array(
                        "NIT" => $mostrar['NIT'],
                        "Nombre" => $mostrar['Nombre'],
                        "Telefono" => $mostrar['Telefono'],
                        "Direccion" => $mostrar['Direccion'],
                        "Ciudad" => $mostrar['Ciudad'],
                    );
                }
                return $data;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
    public static function EliminarProveedor($id)
    {
        $conectar = new Conectar();
        $conexion = $conectar->conexion();
        $ejecutarDelete = mysqli_query($conexion, "DELETE FROM proveedores WHERE NIT = '$id'");
        if ($ejecutarDelete) {
            return true;
        } else {
            return false;
        }
    }
    public function EditarProveedor($ID2)
    {
        $actualizar = "UPDATE proveedores SET NIT = '$this->ID' , nombre = '$this->Nombre', telefono = '$this->Telefono', direccion = '$this->Direccion', ciudad = '$this->Ciudad' WHERE NIT = '$ID2'";
        $ejecutar = mysqli_query($this->conexion, $actualizar);
        if ($ejecutar) {
            return true;
        } else {
            return false;
        }
    }
    public function AgregarProveedor()
    {
        $insert = "INSERT INTO proveedores VALUES('$this->ID','$this->Nombre', '$this->Direccion','$this->Ciudad','$this->Telefono')";
        $ejecutar = mysqli_query($this->conexion, $insert);
        if ($ejecutar) {
            return true;
        } else {
            return false;
        }
    }
}
///FACTURAS SALIDA/////////////////////////////////////////////////////////////////////
class facturas_salida
{
    public $conexion;
    public $Nro_factura;
    public $cliente;
    public $total;

    public function  __construct($Nro_factura, $cliente, $total)
    {
        $conectar = new Conectar();
        $this->conexion = $conectar->conexion();
        $this->total = $total;
        $this->nombre = $cliente;
        $this->nro_factura = $Nro_factura;
    }
    public static function mostrarDatosSalidas($tipo, $data1, $data2)
    {
        $conectar = new Conectar();
        $conexion = $conectar->conexion();
        switch ($tipo) {
            case 0:
                $query = mysqli_query($conexion, "SELECT * FROM facturas_salida ORDER BY Nro_factura DESC");
                break;
            case 1:
                $comprobar1 = explode('-', trim($data1));
                $comprobar2 = explode('-', trim($data2));
                $fechaEnNumero1 = $comprobar1[0] . $comprobar1[1] . $comprobar1[2];
                $fechaEnNumero2 = $comprobar2[0] . $comprobar2[1] . $comprobar2[2];
                if ($fechaEnNumero1 < $fechaEnNumero2) {
                    $query = mysqli_query($conexion, "SELECT * FROM facturas_salida WHERE Fecha BETWEEN '$data1' AND '$data2'");
                } else {
                    $query = mysqli_query($conexion, "SELECT * FROM facturas_salida WHERE Fecha BETWEEN '$data2' AND '$data1'");
                }
                break;
            case 2:
                $query = mysqli_query($conexion, "SELECT * FROM facturas_salida WHERE Fecha LIKE '$data1%'");
                break;
            case 3:
                $query = mysqli_query($conexion, "SELECT * FROM facturas_salida WHERE Fecha = '$data1'");
                break;
            case 4:
                $query = mysqli_query($conexion, "SELECT * FROM facturas_salida WHERE Nro_factura like '$data1%'");
                break;
            case 5:
                $query = mysqli_query($conexion, "SELECT * from detalle_factura_salida WHERE Nro_factura = '$data1'");
                break;
            case 6:
                $query = mysqli_query($conexion, "SELECT Max(Nro_factura) As Nro_factura FROM facturas_salida");
                break;
            case 7:
                $count = count($data1) - 1;
                $var =  "SELECT Producto,sum(Cantidad),sum(Sub_total) from detalle_factura_salida where";
                for ($i = 0; $i <= $count; $i++) {
                    if ($i == $count) {
                        $var .= " Nro_factura =" . $data1[$i] . " ";
                    } else {
                        $var .= " Nro_factura =" . $data1[$i] . " OR ";
                    }
                }
                $var .= "GROUP BY Producto";
                $query = mysqli_query($conexion, $var);
                break;
            case 8:
                $query = mysqli_query($conexion, "SELECT * FROM facturas_salida WHERE Fecha LIKE '$data1%' GROUP BY Fecha");
                break;
        }
        if ($query) {
            if (mysqli_num_rows($query) != 0) {
                if ($tipo == 5) {
                    while ($mostrar = mysqli_fetch_array($query)) {
                        $data[] = array(
                            "ID" => $mostrar['ID'],
                            "Cantidad" => $mostrar['Cantidad'],
                            "Producto" => $mostrar['Producto'],
                            "Precio_caja" => $mostrar['Precio_caja'],
                            "Sub_total" => $mostrar['Sub_total'],
                            "Nro_factura" => $mostrar['Nro_factura'],
                        );
                    }
                } elseif ($tipo == 4 || $tipo == 0 && $data1 == 0) {
                    while ($mostrar = mysqli_fetch_array($query)) {
                        $nit = $mostrar['CC/NIT'];
                        $user = mysqli_query($conexion, "SELECT * FROM clientes WHERE ID = '$nit'");
                        if ($mostrarUser = mysqli_fetch_array($user)) {
                            $nombre = $mostrarUser['Nombre'];
                        }
                        $data[] = array(
                            "Nro_factura" => $mostrar['Nro_factura'],
                            "NIT" => $mostrar['CC/NIT'],
                            "Nombre" => $nombre,
                            "Fecha" => $mostrar['Fecha'],
                            "Total" => $mostrar['Total'],
                        );
                    }
                } elseif ($tipo == 6) {
                    while ($mostrar = mysqli_fetch_array($query)) {
                        $data[] = array(
                            "Nro_factura" => $mostrar['Nro_factura'],
                        );
                    }
                } elseif ($tipo == 7) {
                    while ($mostrar = mysqli_fetch_array($query)) {
                        $data[] = array(
                            "Producto" => $mostrar['Producto'],
                            "Cantidad" => $mostrar['sum(Cantidad)'],
                            "Sub_total" => $mostrar['sum(Sub_total)'],
                        );
                    }
                } else {
                    while ($mostrar = mysqli_fetch_array($query)) {
                        $data[] = array(
                            "Nro_factura" => $mostrar['Nro_factura'],
                            "CC/NIT" => $mostrar['CC/NIT'],
                            "Fecha" => $mostrar['Fecha'],
                            "Total" => $mostrar['Total'],
                        );
                    }
                }
                return $data;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
    public function AgregarFactura($fecha)
    {
        $query = mysqli_query($this->conexion, "INSERT INTO facturas_salida VALUES('$this->nro_factura','$this->nombre', '$fecha', $this->total)");
        if ($query) {
            return true;
        } else {
            return false;
        }
    }
    public static function AgregarDetalle($cant, $id, $prc, $subtotal, $nroFac, $cantidad, $producto)
    {
        $conectar = new Conectar();
        $conexion = $conectar->conexion();
        $session = null;
        $resultDT = mysqli_query($conexion, "SELECT Max(ID) As ID FROM detalle_factura_salida ");
        if ($mostrarDT = mysqli_fetch_array($resultDT)) {
            $countDT = $mostrarDT['ID'] + 1;
        }
        $query = mysqli_query($conexion, "INSERT INTO detalle_factura_salida VALUES('$countDT','$cant','$id', '$prc', '$subtotal', '$nroFac')");
        if ($query) {
            if (isset($_SESSION['sms'])) {
                unset($_SESSION['sms']);
            }
            $result = mysqli_query($conexion, "SELECT * from Productos ORDER BY ID_Producto");
            $ajustes = mysqli_query($conexion, "SELECT * from ajustes");
            if ($comprobar = mysqli_fetch_array($ajustes)) {
                $stock = $comprobar['Stock'];
                while ($mostrar = mysqli_fetch_array($result)) {
                    if ($mostrar['Cantidad'] < $stock) {
                        $session[] = array("Producto" => $mostrar['Producto'], "id" => $mostrar['ID_Producto']);
                    }
                }
            }
            if ($session != null) {
                $_SESSION['sms'] = $session;
            }
            $actualizarCantidadProducto = mysqli_query($conexion, "UPDATE productos SET Cantidad = '$cantidad' WHERE Producto = '$producto'");
            if ($actualizarCantidadProducto) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
    public function AgregarHistorial()
    {
        $H = new historial();
        $historial = "INSERT INTO historial VALUES('$H->countIdHistorial','$H->fecha', '$H->hora', 'Se genero una factura salida para <b>$this->nombre</b> por un total de <b>$this->total</b>')";
        $detalle = "INSERT INTO detalle_historial VALUES('$H->countIdHistorial2','$this->nro_factura','factura salida','$H->countIdHistorial')";
        if ($H->AgregarHistorial($historial, $detalle)) {
            return true;
        } else {
            return false;
        }
    }
}
///FACTURAS ENTRADA///////////////////////////////////////////////////////////////////
class facturas_entrada
{
    public $conexion;
    public $Nro_factura;
    public $cliente;
    public $total;

    public function  __construct($Nro_factura, $cliente, $total)
    {
        $conectar = new Conectar();
        $this->conexion = $conectar->conexion();
        $this->total = $total;
        $this->nombre = $cliente;
        $this->nro_factura = $Nro_factura;
    }

    public static function mostrarDatosSalidas($tipo, $data1, $data2)
    {
        $conectar = new Conectar();
        $conexion = $conectar->conexion();
        switch ($tipo) {
            case 0:
                $query = mysqli_query($conexion, "SELECT * FROM facturas_entrada ORDER BY ID DESC");
                break;
            case 1:
                $comprobar1 = explode('-', trim($data1));
                $comprobar2 = explode('-', trim($data2));
                $fechaEnNumero1 = $comprobar1[0] . $comprobar1[1] . $comprobar1[2];
                $fechaEnNumero2 = $comprobar2[0] . $comprobar2[1] . $comprobar2[2];
                if ($fechaEnNumero1 < $fechaEnNumero2) {
                    $query = mysqli_query($conexion, "SELECT * FROM facturas_entrada WHERE Fecha BETWEEN '$data1' AND '$data2'");
                } else {
                    $query = mysqli_query($conexion, "SELECT * FROM facturas_entrada WHERE Fecha BETWEEN '$data2' AND '$data1'");
                }
                break;
            case 2:
                $query = mysqli_query($conexion, "SELECT * FROM facturas_entrada WHERE Fecha LIKE '$data1%'");
                break;
            case 3:
                $query = mysqli_query($conexion, "SELECT * FROM facturas_entrada WHERE Fecha = '$data1'");
                break;
            case 4:
                $query = mysqli_query($conexion, "SELECT * FROM facturas_entrada WHERE ID like '$data1%'");
                break;
            case 5:
                $query = mysqli_query($conexion, "SELECT * from detalle_factura_entrada WHERE ID_Factura = '$data1'");
                break;
            case 6:
                $query = mysqli_query($conexion, "SELECT Max(ID) As ID FROM facturas_entrada");
                break;
            case 7:
                $count = count($data1) - 1;
                $var =  "SELECT Producto,sum(Cantidad),sum(Sub_total) from detalle_factura_entrada where";
                for ($i = 0; $i <= $count; $i++) {
                    if ($i == $count) {
                        $var .= " Nro_factura =" . $data1[$i] . " ";
                    } else {
                        $var .= " Nro_factura =" . $data1[$i] . " OR ";
                    }
                }
                $var .= "GROUP BY Producto";
                $query = mysqli_query($conexion, $var);
                break;
            case 8:
                $query = mysqli_query($conexion, "SELECT * FROM facturas_entrada WHERE Fecha LIKE '$data1%' GROUP BY Fecha");
                break;
        }
        if ($query) {
            if (mysqli_num_rows($query) != 0) {
                if ($tipo == 5) {
                    while ($mostrar = mysqli_fetch_array($query)) {
                        $data[] = array(
                            "ID" => $mostrar['ID'],
                            "Cantidad" => $mostrar['Cantidad'],
                            "Producto" => $mostrar['Producto'],
                            "Precio_caja" => $mostrar['Precio_caja'],
                            "Sub_total" => $mostrar['Sub_total'],
                            "ID_Factura" => $mostrar['ID_Factura'],
                        );
                    }
                } elseif ($tipo == 4 || $tipo == 0 && $data1 == 0) {
                    while ($mostrar = mysqli_fetch_array($query)) {
                        $nit = $mostrar['NIT'];
                        $user = mysqli_query($conexion, "SELECT * FROM proveedores WHERE NIT = '$nit'");
                        if ($mostrarUser = mysqli_fetch_array($user)) {
                            $nombre = $mostrarUser['Nombre'];
                        }
                        $data[] = array(
                            "ID" => $mostrar['ID'],
                            "Nro_factura" => $mostrar['Nro_factura'],
                            "NIT" => $mostrar['NIT'],
                            "Nombre" => $nombre,
                            "Fecha" => $mostrar['Fecha'],
                            "Total" => $mostrar['Total'],
                        );
                    }
                } elseif ($tipo == 6) {
                    while ($mostrar = mysqli_fetch_array($query)) {
                        $data[] = array(
                            "ID" => $mostrar['ID'],
                        );
                    }
                } elseif ($tipo == 7) {
                    while ($mostrar = mysqli_fetch_array($query)) {
                        $data[] = array(
                            "Producto" => $mostrar['Producto'],
                            "Cantidad" => $mostrar['sum(Cantidad)'],
                            "Sub_total" => $mostrar['sum(Sub_total)'],
                        );
                    }
                } else {
                    while ($mostrar = mysqli_fetch_array($query)) {
                        $data[] = array(
                            "ID" => $mostrar['ID'],
                            "Nro_factura" => $mostrar['Nro_factura'],
                            "NIT" => $mostrar['NIT'],
                            "Fecha" => $mostrar['Fecha'],
                            "Total" => $mostrar['Total'],
                        );
                    }
                }
                return $data;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
    public function AgregarFactura($fecha, $ID)
    {
        $query = mysqli_query($this->conexion, "INSERT INTO facturas_entrada VALUES('$ID','$this->nro_factura','$this->nombre', '$fecha', $this->total)");
        if ($query) {
            return true;
        } else {
            return false;
        }
    }
    public static function AgregarDetalle($cant2, $id, $prc, $subtotal, $IDD, $cantidadProducto, $nombre, $precioProducto)
    {
        $conectar = new Conectar();
        $conexion = $conectar->conexion();
        $resultDT = mysqli_query($conexion, "SELECT Max(ID) As ID FROM detalle_factura_entrada");
        if ($mostrarDT = mysqli_fetch_array($resultDT)) {
            $countDT = $mostrarDT['ID'] + 1;
        }
        $query = mysqli_query($conexion, "INSERT INTO detalle_factura_entrada VALUES('$countDT','$cant2','$id', '$prc', '$subtotal', '$IDD')");
        if ($query) {
            $actualizarCantidadProducto = mysqli_query($conexion, "UPDATE productos SET Cantidad = '$cantidadProducto' WHERE Producto = '$nombre'");
            if ($actualizarCantidadProducto) {
                if (isset($_SESSION['sms'])) {
                    unset($_SESSION['sms']);
                }
                $result = mysqli_query($conexion, "SELECT * from Productos ORDER BY ID_Producto");
                $ajustes = mysqli_query($conexion, "SELECT * from ajustes");
                if ($comprobar = mysqli_fetch_array($ajustes)) {
                    $stock = $comprobar['Stock'];
                    while ($mostrar = mysqli_fetch_array($result)) {
                        if ($mostrar['Cantidad'] < $stock) {
                            $session[] = array("Producto" => $mostrar['Producto'], "id" => $mostrar['ID_Producto']);
                        }
                    }
                }
                if ($session != null) {
                    $_SESSION['sms'] = $session;
                }
                $seleccionarAjustes = mysqli_query($conexion, "SELECT * FROM ajustes");
                if ($ajust = mysqli_fetch_array($seleccionarAjustes)) {
                    $ajustes = $ajust['Porcentaje'];
                }
                if ($ajustes == 1) {
                    $actualizarPrecioProducto = mysqli_query($conexion, "UPDATE productos SET PrecioC = '$precioProducto' WHERE Producto = '$nombre'");
                    if ($actualizarPrecioProducto) {
                        return true;
                    } else {
                        return false;
                    }
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
    public function AgregarHistorial()
    {
        $H = new historial();
        $historial = "INSERT INTO historial VALUES('$H->countIdHistorial','$H->fecha', '$H->hora', 'Se genero una factura entrada de <b>$this->nombre</b> por un total de <b>$this->total</b>')";
        $detalle = "INSERT INTO detalle_historial VALUES('$H->countIdHistorial2','$this->nro_factura','factura entrada','$H->countIdHistorial')";
        if ($H->AgregarHistorial($historial, $detalle)) {
            return true;
        } else {
            return false;
        }
    }
}
///AJUSTES////////////////////////////////////////////////////////////////////////////
class ajustes
{
    public $conexion;
    public $user;
    public $porcentaje;
    public $auto;
    public $stock;

    public function  __construct($user, $porcentaje, $auto, $stock)
    {
        $conectar = new Conectar();
        $this->conexion = $conectar->conexion();
        $this->user = $user;
        $this->porcentaje = $porcentaje;
        $this->auto = $auto;
        $this->stock = $stock;
    }
    public static function mostrarDatosAjustes()
    {
        $conectar = new Conectar();
        $conexion = $conectar->conexion();
        $seleccionarAjustes = mysqli_query($conexion, "SELECT * FROM ajustes");
        if ($mostrar = mysqli_fetch_array($seleccionarAjustes)) {
            $dataAjustes['porcentaje'] = $mostrar['Porcentaje'];
            $dataAjustes['Nombre'] = $mostrar['Nombre_Empresa'];
            $dataAjustes['Correo'] = $mostrar['Correo_Empresa'];
            $dataAjustes['Pass'] = $mostrar['Pass_Empresa'];
            $dataAjustes['Direccion'] = $mostrar['Direccion'];
            $dataAjustes['ganancia'] = $mostrar['Ganancia'];
            $dataAjustes['Ciudad'] = $mostrar['Ciudad'];
            $dataAjustes['stock'] = $mostrar['Stock'];
            $dataAjustes['NIT'] = $mostrar['NIT'];
            return $dataAjustes;
        }
    }
    public function guardarAjustes()
    {
        $session = null;
        $informacionUser = mysqli_query($this->conexion, "SELECT * FROM usuarios WHERE ID = '$this->user'");
        if ($mostrar = mysqli_fetch_array($informacionUser)) {
            if ($mostrar['ID_Rol'] == 0) {
                $actualizar = "UPDATE ajustes SET Ganancia = '$this->porcentaje' ,Porcentaje = $this->auto ,Stock = $this->stock  WHERE ID = 1";
                $query = mysqli_query($this->conexion, $actualizar);
                if ($query) {
                    if (isset($_SESSION['sms'])) {
                        unset($_SESSION['sms']);
                    }
                    $result = mysqli_query($this->conexion, "SELECT * from Productos ORDER BY ID_Producto");
                    $ajustes = mysqli_query($this->conexion, "SELECT * from ajustes");
                    if ($comprobar = mysqli_fetch_array($ajustes)) {
                        $stock = $comprobar['Stock'];
                        while ($mostrar = mysqli_fetch_array($result)) {
                            if ($mostrar['Cantidad'] < $stock) {
                                $session[] = array("Producto" => $mostrar['Producto'], "id" => $mostrar['ID_Producto']);
                            }
                        }
                    }
                    if ($session != null) {
                        $_SESSION['sms'] = $session;
                    }
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
    public static function panelDeControl()
    {
        $conectar = new Conectar();
        $conexion = $conectar->conexion();
        date_default_timezone_set("America/Bogota");
        $fecha = date('Y-m-j');
        // entradas
        $consultarEntradas = mysqli_query($conexion, "SELECT * from facturas_entrada WHERE Fecha = '$fecha'");
        if ($consultarEntradas) {
            $data['entrada'] = mysqli_num_rows($consultarEntradas);
        } else {
            $data['entrada'] = 0;
        }
        // salidas
        $consultarSalidas = mysqli_query($conexion, "SELECT * from facturas_salida WHERE Fecha = '$fecha'");
        if ($consultarSalidas) {
            $data['salida'] = mysqli_num_rows($consultarSalidas);
        } else {
            $data['salida'] = 0;
        }
        // ganancias
        $consultarGanancias = mysqli_query($conexion, "SELECT * from facturas_salida WHERE Fecha = '$fecha'");
        if ($consultarGanancias) {
            $total = 0;
            while ($mostrar = mysqli_fetch_array($consultarGanancias)) {
                $total = $total + $mostrar['Total'];
            }
            $data['ganancias'] = $total;
        } else {
            $data['ganancias'] = 0;
        }
        return $data;
    }
    public static function guardarDatosEmpresa($nombre, $nit, $direccion, $ciudad)
    {
        $conectar = new Conectar();
        $conexion = $conectar->conexion();
        $actualizar = "UPDATE ajustes SET Nombre_Empresa = '$nombre' ,NIT = '$nit' ,Ciudad = '$ciudad', Direccion = '$direccion'  WHERE ID = 1";
        $query = mysqli_query($conexion, $actualizar);
        if ($query) {
            return true;
        } else {
            return false;
        }
    }
    public static function Reboot()
    {
        $conectar = new Conectar();
        $conexion = $conectar->conexion();
        $delate = mysqli_query($conexion, "UPDATE ajustes SET Ganancia = 0 ,Porcentaje = 0 ,Stock = 0, Nombre_Empresa = 'Nombre empresa' ,NIT = '0' ,Ciudad = 'N/A', Direccion = 'N/A'  WHERE ID = 1");
        $delate = mysqli_query($conexion, "DELETE FROM 'categoria';");
        $delate = mysqli_query($conexion, "DELETE FROM 'clientes';");
        $delate = mysqli_query($conexion, "DELETE FROM 'detalle_factura_entrada';");
        $delate = mysqli_query($conexion, "DELETE FROM 'detalle_factura_salida';");
        $delate = mysqli_query($conexion, "DELETE FROM 'detalle_historial';");
        $delate = mysqli_query($conexion, "DELETE FROM 'facturas_entrada';");
        $delate = mysqli_query($conexion, "DELETE FROM 'facturas_salida';");
        $delate = mysqli_query($conexion, "DELETE FROM 'historial';");
        $delate = mysqli_query($conexion, "DELETE FROM 'permisos';");
        $delate = mysqli_query($conexion, "DELETE FROM 'productos';");
        $delate = mysqli_query($conexion, "DELETE FROM 'proveedores';");
        $delate = mysqli_query($conexion, "DELETE FROM 'rol';");
        $delate = mysqli_query($conexion, "DELETE FROM 'usuarios';");
        if ($delate) {
            return true;
        } else {
            return false;
        }
    }
}
///PERMISOS///////////////////////////////////////////////////////////////////////////
class permisos
{
    public $editar;
    public $agregar;
    public $facturar;
    public $entrada;
    public $salida;
    public $clientes;
    public $proveedores;

    public function  __construct($editar, $agregar, $reportes, $entrada, $salida, $clientes, $proveedores)
    {
        $conectar = new Conectar();
        $this->conexion = $conectar->conexion();
        $this->editar = $editar;
        $this->agregar = $agregar;
        $this->reportes = $reportes;
        $this->entrada = $entrada;
        $this->salida = $salida;
        $this->cliente = $clientes;
        $this->proveedores = $proveedores;
    }
    public static function mostrarDatos($id)
    {
        $conectar = new Conectar();
        $conexion = $conectar->conexion();
        if ($id == null) {
            $query = mysqli_query($conexion, "SELECT * from permisos");
        } else {
            $query = mysqli_query($conexion, "SELECT * from permisos WHERE ID = '$id'");
        }
        if ($query) {
            if (mysqli_num_rows($query) != 0) {
                while ($mostrar = mysqli_fetch_array($query)) {
                    $data[] = array(
                        "Editar" => $mostrar['Editar'],
                        "Agregar" => $mostrar['Agregar'],
                        "Entrada" => $mostrar['Entrada'],
                        "Facturar" => $mostrar['Facturar'],
                        "Clientes" => $mostrar['Clientes'],
                        "Proveedores" => $mostrar['Proveedores'],
                        "Reportes" => $mostrar['Reportes'],
                    );
                }
                return $data;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
    public function guardarPermisos($id, $nombre, $email, $rol, $cedula)
    {
        $permisos = "UPDATE permisos SET Editar = $this->editar , Agregar = $this->agregar , Entrada = $this->entrada , Facturar = $this->salida , Clientes = $this->cliente , Proveedores = $this->proveedores , Reportes = $this->reportes WHERE ID = '$id'";
        $query = mysqli_query($this->conexion, $permisos);
        if ($query) {
            $nombreUsuario = explode(' ', trim($nombre));
            if(empty($nombreUsuario[2])){
                $user = $nombreUsuario[0] . " " . $nombreUsuario[1];
            }else{
                $user = $nombreUsuario[0] . " " . $nombreUsuario[2];
            }
            $modificar = mysqli_query($this->conexion, "UPDATE usuarios SET Usuario = '$user' , Nombre = '$nombre' , Cedula = $cedula , Email = '$email' , ID_Rol = '$rol' WHERE ID = '$id'");
            if ($modificar) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
}
