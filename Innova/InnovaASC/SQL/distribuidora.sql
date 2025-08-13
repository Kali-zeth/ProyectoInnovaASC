-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 31-03-2022 a las 18:19:43
-- Versión del servidor: 10.4.22-MariaDB
-- Versión de PHP: 8.1.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `distribuidora`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ajustes`
--

CREATE TABLE `ajustes` (
  `ID` int(255) NOT NULL,
  `Ganancia` int(255) NOT NULL,
  `Porcentaje` tinyint(1) NOT NULL,
  `Stock` int(255) NOT NULL,
  `Nombre_Empresa` varchar(255) COLLATE utf8mb4_spanish_ci NOT NULL,
  `NIT` varchar(15) COLLATE utf8mb4_spanish_ci NOT NULL,
  `Ciudad` varchar(255) COLLATE utf8mb4_spanish_ci NOT NULL,
  `Direccion` varchar(255) COLLATE utf8mb4_spanish_ci NOT NULL,
  `Correo_Empresa` varchar(255) COLLATE utf8mb4_spanish_ci NOT NULL,
  `Pass_Empresa` varchar(255) COLLATE utf8mb4_spanish_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `ajustes`
--

INSERT INTO `ajustes` (`ID`, `Ganancia`, `Porcentaje`, `Stock`, `Nombre_Empresa`, `NIT`, `Ciudad`, `Direccion`, `Correo_Empresa`, `Pass_Empresa`) VALUES
(1, 0, 0, 0, 'Nombre empresa', '0', 'N/A', 'N/A', 'innovaASC@gmail.com', 'innova123456789');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categoria`
--

CREATE TABLE `categoria` (
  `ID_Categoria` int(255) NOT NULL COMMENT 'Se utiliza para  asignar a los productos una categoria',
  `Nombre` varchar(45) CHARACTER SET latin1 NOT NULL COMMENT 'nombre de la categoria'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

CREATE TABLE `clientes` (
  `ID` varchar(255) CHARACTER SET latin1 NOT NULL COMMENT 'Se utiliza para indentificar a los clientes',
  `Nombre` varchar(100) CHARACTER SET latin1 NOT NULL COMMENT 'Campo para el nombre del cliente',
  `Telefono` varchar(45) CHARACTER SET latin1 NOT NULL COMMENT 'Campo para el telefono del cliente',
  `Direccion` varchar(100) CHARACTER SET latin1 NOT NULL COMMENT 'Campo para la direccion del cliente',
  `Ciudad` varchar(100) CHARACTER SET latin1 NOT NULL COMMENT 'Campo para la ciudad del cliente',
  `Correo` varchar(255) COLLATE utf8mb4_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_factura_entrada`
--

CREATE TABLE `detalle_factura_entrada` (
  `ID` int(255) NOT NULL COMMENT 'Campo para identificar los detalles de cada factura',
  `Cantidad` int(11) NOT NULL COMMENT 'Campo para colocar la cantidad del producto ingresado',
  `Producto` varchar(255) COLLATE utf8mb4_spanish_ci NOT NULL COMMENT 'Campo para colocar el identificador del producto ingresado',
  `Precio_caja` int(255) NOT NULL COMMENT 'Campo para colocar el precio por el que se compro cada caja',
  `Sub_total` int(255) NOT NULL COMMENT 'Campo donde se coloca la suma de todas las cajas compradas',
  `ID_Factura` int(255) NOT NULL COMMENT 'Campo para identificar a que factura pertenece este registro'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_factura_salida`
--

CREATE TABLE `detalle_factura_salida` (
  `ID` int(255) NOT NULL COMMENT '	Campo para identificar los detalles de cada factura	',
  `Cantidad` int(255) NOT NULL COMMENT 'Campo para colocar la cantidad del producto ingresado',
  `Producto` varchar(255) COLLATE utf8mb4_spanish_ci NOT NULL COMMENT 'Campo para colocar el identificador del producto vendido',
  `Precio_caja` int(255) NOT NULL COMMENT 'Campo para colocar el precio por el que se vendio cada caja',
  `Sub_total` int(255) NOT NULL COMMENT 'Campo donde se coloca la suma de todas las cajas vendidas',
  `Nro_factura` int(255) NOT NULL COMMENT '	Campo para identificar a que factura pertenece este registro'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_historial`
--

CREATE TABLE `detalle_historial` (
  `ID` int(255) NOT NULL COMMENT 'Identificador del detalle 	',
  `Dato1` varchar(255) CHARACTER SET latin1 NOT NULL COMMENT 'Campo para ingresar la ID y el nombre del producto en el que se realizo la accion',
  `Dato2` text CHARACTER SET latin1 NOT NULL COMMENT 'Campo para detallar las acciones realizadas',
  `ID_historial` int(255) NOT NULL COMMENT 'Campo para identificar a que registro de historial pertenece'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `detalle_historial`
--

INSERT INTO `detalle_historial` (`ID`, `Dato1`, `Dato2`, `ID_historial`) VALUES
(1, 'Andres Gonzalez', 'Inicio de sesion', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `facturas_entrada`
--

CREATE TABLE `facturas_entrada` (
  `ID` int(255) NOT NULL COMMENT 'Campo donde se asigna el numero de la factura ',
  `Nro_factura` int(255) NOT NULL,
  `NIT` varchar(13) CHARACTER SET latin1 NOT NULL COMMENT 'Campo para ingresar el NIT del proveedor ',
  `Fecha` varchar(10) CHARACTER SET latin1 NOT NULL COMMENT 'Campo para ingresar la fecha en la que se realizo la factura ',
  `Total` int(45) NOT NULL COMMENT 'Campo donde se ingresa el valor total de la factura '
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `facturas_salida`
--

CREATE TABLE `facturas_salida` (
  `Nro_factura` int(11) NOT NULL COMMENT '	Campo donde se asigna el numero de la factura',
  `CC/NIT` varchar(45) CHARACTER SET latin1 NOT NULL COMMENT 'Campo para ingresar ael NIT o Cedula del  cliente',
  `Fecha` varchar(45) CHARACTER SET latin1 NOT NULL COMMENT '	Campo para ingresar la fecha en la que se realizo la factura',
  `Total` int(20) NOT NULL COMMENT 'Campo donde se ingresa el valor total de la factura'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `historial`
--

CREATE TABLE `historial` (
  `ID` int(255) NOT NULL COMMENT 'Campo para identificar cada registro ',
  `Fecha` varchar(45) CHARACTER SET latin1 NOT NULL COMMENT 'Campo para ingresar la fecha en la que se realizo la accion',
  `Hora` varchar(45) CHARACTER SET latin1 NOT NULL COMMENT 'Campo para ingresar la hora en la que se realizo la accion',
  `Accion` varchar(255) CHARACTER SET latin1 NOT NULL COMMENT 'Campo en el que se ingresa la accion realizada'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `historial`
--

INSERT INTO `historial` (`ID`, `Fecha`, `Hora`, `Accion`) VALUES
(1, '31-03-2022', '10:14:43', 'Se inicio sesion con el usuario <b>Andres Gonzalez</b>');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permisos`
--

CREATE TABLE `permisos` (
  `ID` int(255) NOT NULL,
  `Editar` tinyint(1) NOT NULL,
  `Agregar` tinyint(1) NOT NULL,
  `Entrada` tinyint(1) NOT NULL,
  `Facturar` tinyint(1) NOT NULL,
  `Clientes` tinyint(1) NOT NULL,
  `Proveedores` tinyint(1) NOT NULL,
  `Reportes` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `permisos`
--

INSERT INTO `permisos` (`ID`, `Editar`, `Agregar`, `Entrada`, `Facturar`, `Clientes`, `Proveedores`, `Reportes`) VALUES
(0, 1, 1, 1, 1, 1, 1, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `ID_Producto` int(255) NOT NULL COMMENT 'Campo donde se asigna una ID a cada producto ',
  `Producto` varchar(45) CHARACTER SET latin1 NOT NULL COMMENT 'Campo para ingresar el nombre del producto',
  `Cantidad` int(255) NOT NULL COMMENT 'Campo donde se muestra la cantidad de cajas que se tienen del producto',
  `PrecioC` int(255) NOT NULL COMMENT 'Campo donde se ingresa el precio por caja del producto ',
  `Imagen` varchar(255) COLLATE utf8mb4_spanish_ci DEFAULT NULL,
  `ID_Categoria` int(255) NOT NULL COMMENT 'Campo donde atravez de un identificador se le asigna a un producto una categoria'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proveedores`
--

CREATE TABLE `proveedores` (
  `NIT` varchar(15) CHARACTER SET latin1 NOT NULL COMMENT 'Campo que utiliza el nit del proveedor como identificador ',
  `Nombre` varchar(45) CHARACTER SET latin1 NOT NULL COMMENT 'Campo donde se ingresa el nombre del proveedor',
  `Direccion` varchar(45) CHARACTER SET latin1 NOT NULL COMMENT 'Campo donde se encuentra la direccion del proveedor ',
  `Ciudad` varchar(45) CHARACTER SET latin1 NOT NULL COMMENT 'Campo donde se ingresa la ciudad en la que se ubica el proveedor ',
  `Telefono` varchar(10) CHARACTER SET latin1 NOT NULL COMMENT 'Campo donde se ingresa el numero de contacto del proveedor '
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rol`
--

CREATE TABLE `rol` (
  `ID` int(255) NOT NULL,
  `Cargo` varchar(255) COLLATE utf8mb4_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `rol`
--

INSERT INTO `rol` (`ID`, `Cargo`) VALUES
(0, 'Administrador');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `ID` int(255) NOT NULL,
  `Cedula` int(11) NOT NULL,
  `Usuario` varchar(255) COLLATE utf8mb4_spanish_ci NOT NULL,
  `Nombre` varchar(255) COLLATE utf8mb4_spanish_ci NOT NULL,
  `Email` varchar(255) COLLATE utf8mb4_spanish_ci NOT NULL,
  `Pass` varchar(255) COLLATE utf8mb4_spanish_ci NOT NULL,
  `ID_Rol` int(255) NOT NULL,
  `ID_Permisos` int(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`ID`, `Cedula`, `Usuario`, `Nombre`, `Email`, `Pass`, `ID_Rol`, `ID_Permisos`) VALUES
(0, 12345678, 'Andres Gonzalez', 'Andres Felipe Gonzalez Roldan ', 'andterol1@gmail.com', '12345678', 0, 0);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `ajustes`
--
ALTER TABLE `ajustes`
  ADD PRIMARY KEY (`ID`);

--
-- Indices de la tabla `categoria`
--
ALTER TABLE `categoria`
  ADD PRIMARY KEY (`ID_Categoria`) USING BTREE;

--
-- Indices de la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`ID`);

--
-- Indices de la tabla `detalle_factura_entrada`
--
ALTER TABLE `detalle_factura_entrada`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `ID_Factura` (`ID_Factura`);

--
-- Indices de la tabla `detalle_factura_salida`
--
ALTER TABLE `detalle_factura_salida`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `Nro_factura` (`Nro_factura`);

--
-- Indices de la tabla `detalle_historial`
--
ALTER TABLE `detalle_historial`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `ID_historial` (`ID_historial`);

--
-- Indices de la tabla `facturas_entrada`
--
ALTER TABLE `facturas_entrada`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `NIT` (`NIT`);

--
-- Indices de la tabla `facturas_salida`
--
ALTER TABLE `facturas_salida`
  ADD PRIMARY KEY (`Nro_factura`),
  ADD KEY `CC/NIT` (`CC/NIT`);

--
-- Indices de la tabla `historial`
--
ALTER TABLE `historial`
  ADD PRIMARY KEY (`ID`);

--
-- Indices de la tabla `permisos`
--
ALTER TABLE `permisos`
  ADD PRIMARY KEY (`ID`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`ID_Producto`) USING BTREE,
  ADD KEY `ID_Categoria` (`ID_Categoria`) USING BTREE;

--
-- Indices de la tabla `proveedores`
--
ALTER TABLE `proveedores`
  ADD PRIMARY KEY (`NIT`) USING BTREE;

--
-- Indices de la tabla `rol`
--
ALTER TABLE `rol`
  ADD PRIMARY KEY (`ID`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `ID_Permisos` (`ID_Permisos`),
  ADD KEY `ID_Rol` (`ID_Rol`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `ajustes`
--
ALTER TABLE `ajustes`
  MODIFY `ID` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `detalle_historial`
--
ALTER TABLE `detalle_historial`
  ADD CONSTRAINT `detalle_historial_ibfk_1` FOREIGN KEY (`ID_historial`) REFERENCES `historial` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `productos`
--
ALTER TABLE `productos`
  ADD CONSTRAINT `productos_ibfk_1` FOREIGN KEY (`ID_Categoria`) REFERENCES `categoria` (`ID_Categoria`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`ID_Permisos`) REFERENCES `permisos` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `usuarios_ibfk_2` FOREIGN KEY (`ID_Rol`) REFERENCES `rol` (`ID`) ON DELETE NO ACTION ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
