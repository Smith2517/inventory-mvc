# Documentación del Sistema de Inventario MVC

## Índice
1. [Descripción General](#descripción-general)
2. [Arquitectura del Sistema](#arquitectura-del-sistema)
3. [Estructura de Directorios](#estructura-de-directorios)
4. [Componentes del Sistema](#componentes-del-sistema)
5. [Controladores](#controladores)
6. [Modelos](#modelos)
7. [Vistas](#vistas)
8. [Configuración](#configuración)
9. [Base de Datos](#base-de-datos)
10. [Flujo de la Aplicación](#flujo-de-la-aplicación)
11. [Funcionalidades Principales](#funcionalidades-principales)
12. [Estilos y Recursos Estáticos](#estilos-y-recursos-estáticos)

## Descripción General

El sistema de Inventario MVC es una aplicación web construida en PHP que sigue el patrón de arquitectura Modelo-Vista-Controlador (MVC). La aplicación permite gestionar inventarios, oficinas, cargos, movimientos de productos y generar reportes.

### Características principales:
- Sistema de autenticación de usuarios
- Gestión de oficinas
- Gestión de cargos
- Registro de productos con códigos únicos
- Control de stock con bitácora de movimientos
- Generación de etiquetas imprimibles (50mm x 30mm)
- Reportes imprimibles (guardable como PDF)
- Interfaz responsive con Bootstrap 5

## Arquitectura del Sistema

El sistema sigue el patrón MVC clásico:

```
public/ (punto de entrada)
├── index.php (Router Frontal)
├── assets/ (CSS, JS, Imágenes)
│   ├── css/
│   ├── js/
│   └── img/
└── otros archivos (reset_admin_dynamic.php, test_db.php)

app/ (código de la aplicación)
├── config/ (archivos de configuración)
├── controllers/ (controladores)
├── models/ (modelos de datos)
├── views/ (vistas)
│   ├── layout/ (plantillas comunes)
│   ├── auth/ (vistas de autenticación)
│   ├── cargo/ (vistas de cargos)
│   ├── inventory/ (vistas de inventario)
│   ├── office/ (vistas de oficinas)
│   └── report/ (vistas de reportes)
└── core/ (clases base del sistema)
```

## Estructura de Directorios

```
inventory-mvc/
├── 16-10-25.sql
├── 29-09-2025.sql
├── basededatosfinal.sql
├── bd_inventario.sql
├── README.md
├── .git/
├── app/
│   ├── config/
│   │   └── config.php
│   ├── controllers/
│   │   ├── AuthController.php
│   │   ├── CargoController.php
│   │   ├── InventoryController.php
│   │   ├── OfficeController.php
│   │   └── ReportController.php
│   ├── core/
│   │   ├── App.php
│   │   └── Controller.php
│   │   └── Database.php
│   ├── models/
│   │   ├── Cargo.php
│   │   ├── Inventory.php
│   │   ├── Movement.php
│   │   ├── Office.php
│   │   └── User.php
│   └── views/
│       ├── layout/
│       │   ├── header.php
│       │   └── footer.php
│       ├── auth/
│       │   ├── login.php
│       │   └── reset_password.php
│       ├── cargo/
│       │   ├── create.php
│       │   ├── edit.php
│       │   └── index.php
│       ├── inventory/
│       │   ├── create.php
│       │   ├── index.php
│       │   ├── label.php
│       │   └── show.php
│       ├── office/
│       │   ├── create.php
│       │   ├── edit.php
│       │   └── index.php
│       └── report/
│           ├── inventory.php
│           └── movements.php
├── public/
│   ├── index.php
│   ├── reset_admin_dynamic.php
│   ├── test_db.php
│   └── assets/
│       ├── css/
│       │   └── styles.css
│       ├── js/
│       │   └── app.js
│       └── img/
│           └── logo.png
└── sql/
    ├── cargos.sql
    └── schema.sql
```

## Componentes del Sistema

### 1. Router Frontal (public/index.php)
- Punto de entrada único para todas las solicitudes
- Carga las clases base del sistema
- Instancia y ejecuta la aplicación

### 2. Clase App (app/core/App.php)
- Gestiona la lógica principal de enrutamiento
- Interpreta los parámetros GET para determinar el controlador y acción
- Muestra errores 404 si el controlador o acción no existen

### 3. Clase Controller (app/core/Controller.php)
- Clase base para todos los controladores
- Proporciona métodos comunes de renderizado, redirección y autenticación
- Implementa protección CSRF

### 4. Clase Database (app/core/Database.php)
- Implementa el patrón singleton para la conexión a la base de datos
- Proporciona acceso a la instancia PDO

## Controladores

### AuthController.php
- Maneja autenticación y cierre de sesión
- Gestiona el restablecimiento de contraseñas
- Controla la seguridad de la aplicación

### CargoController.php
- Gestión de cargos/roles de usuario
- Operaciones CRUD para la entidad Cargo

### InventoryController.php
- Gestión principal del inventario
- Registro de productos con códigos únicos
- Control de stock y movimientos
- Generación de etiquetas
- Manejo de descuentos de stock

### OfficeController.php
- Gestión de oficinas
- Operaciones CRUD para la entidad Oficina

### ReportController.php
- Generación de reportes de inventario y movimientos
- Manejo de parámetros de fechas para reportes

## Modelos

### Cargo.php
- Gestión de la tabla de cargos
- Operaciones CRUD básicas

### Inventory.php
- Gestión de la tabla de inventario
- Generación de códigos únicos con prefijos
- Paginación y búsqueda
- Actualización de cantidades y estados

### Movement.php
- Gestión de la tabla de movimientos
- Registro de entradas y salidas
- Consultas por rango de fechas e inventario

### Office.php
- Gestión de la tabla de oficinas
- Operaciones CRUD básicas

### User.php
- Gestión de la tabla de usuarios
- Autenticación y gestión de usuarios

## Vistas

### Layout Común
- header.php: Cabecera principal con menú lateral
- footer.php: Pie de página con recursos JavaScript

### Vistas de Autenticación
- login.php: Formulario de inicio de sesión
- reset_password.php: Formulario de restablecimiento de contraseña

### Vistas de Inventario
- index.php: Listado principal del inventario
- create.php: Formulario para crear nuevos artículos
- show.php: Detalles de un artículo con movimientos
- label.php: Vista de etiqueta imprimible

### Vistas de Reportes
- inventory.php: Reporte de inventario
- movements.php: Reporte de movimientos

## Configuración

### app/config/config.php
Archivo de configuración principal que define:
- Conexión a la base de datos (host, puerto, nombre, usuario, contraseña)
- URL base de la aplicación
- Información de la empresa (nombre, logo)

### Valores de ejemplo:
```php
return [
  'db' => [
    'host' => 'localhost',
    'port' => '3306',
    'name' => 'inventory_mvc',
    'user' => 'root',
    'pass' => '',
    'charset' => 'utf8mb4',
  ],
  'app' => [
    'base_url' => '/inventory-mvc/public',
  ],
  'company' => [
    'name' => 'IESTP - RIOJA',
    'logo' => '/inventory-mvc/public/assets/img/logo.png',
  ]
];
```

## Base de Datos

El sistema utiliza MySQL/MariaDB con las siguientes tablas principales:

### Tabla `inventario`
- id: Identificador único
- codigo: Código único del artículo (generado automáticamente)
- nombre: Nombre del artículo
- serie: Número de serie
- descripcion: Descripción del artículo
- cantidad: Cantidad disponible
- oficina_id: Referencia a la oficina
- estado: Estado del artículo (DISPONIBLE/AGOTADO)
- estado_2: Estado detallado (BUENO/MALO/REGULAR/BAJA/NUEVO)
- estante: Ubicación en estante
- created_at: Fecha de creación
- updated_at: Fecha de actualización

### Tabla `movimientos` (movimientos)
- id: Identificador único
- inventario_id: Referencia al artículo
- tipo: Tipo de movimiento (ENTRADA/SALIDA)
- cantidad: Cantidad movida
- motivo: Motivo del movimiento
- oficina_id: Referencia a la oficina
- user_id: Referencia al usuario que realizó el movimiento
- created_at: Fecha del movimiento

### Tabla `oficinas`
- id: Identificador único
- nombre: Nombre de la oficina
- created_at: Fecha de creación
- updated_at: Fecha de actualización

### Tabla `cargos`
- id: Identificador único
- nombre: Nombre del cargo
- created_at: Fecha de creación
- updated_at: Fecha de actualización

### Tabla `usuarios`
- id: Identificador único
- username: Nombre de usuario
- password: Contraseña (hash)
- nombre: Nombre completo
- cargo_id: Referencia al cargo
- created_at: Fecha de creación
- updated_at: Fecha de actualización

## Flujo de la Aplicación

1. **Solicitud HTTP**: Toda solicitud pasa por `public/index.php`
2. **Enrutamiento**: La clase `App` interpreta los parámetros GET
3. **Instanciación**: Se carga el controlador solicitado
4. **Ejecución**: Se ejecuta la acción especificada
5. **Modelo**: El controlador interactúa con los modelos según sea necesario
6. **Vista**: Se renderiza la vista correspondiente
7. **Respuesta**: Se envía la respuesta HTML al navegador

## Funcionalidades Principales

### 1. Gestión de Inventario
- Registro de artículos con códigos únicos generados automáticamente
- Categorización por oficina y estante
- Control de cantidades y estado (BUENO/MALO/REGULAR/BAJA/NUEVO)
- Visualización en tabla con paginación

### 2. Movimientos de Inventario
- Registro de entradas y salidas de artículos
- Bitácora completa de movimientos con motivos
- Asociación a usuarios y oficinas

### 3. Generación de Etiquetas
- Diseño de etiquetas específicas para impresión (50mm x 30mm)
- Visualización en modal con iframe
- Funcionalidad de impresión directa

### 4. Reportes
- Reporte de inventario completo
- Reporte de movimientos por rango de fechas
- Interfaz de impresión optimizada

### 5. Autenticación
- Sistema de login/logout con validación
- Protección CSRF
- Sesiones de usuario

### 6. Seguridad
- Validación de formularios
- Filtrado de datos de entrada
- Protección contra inyección SQL
- Control de acceso basado en sesiones

## Estilos y Recursos Estáticos

### CSS
- Bootstrap 5 para componentes y maquetación
- Estilos personalizados en `styles.css`
- Estilos responsive para diferentes dispositivos
- Uso de sistema de colores personalizado con CSS variables

### JavaScript
- Bootstrap Bundle para componentes interactivos
- jQuery y DataTables para tablas dinámicas
- Extensiones de DataTables para exportación
- SweetAlert2 para notificaciones
- Funcionalidad personalizada en `app.js`

### Recursos
- Iconos de Bootstrap Icons
- Tipografía Inter desde Google Fonts
- Funcionalidades de sidebar, modales y DataTables

## Consideraciones de Seguridad

1. **Protección CSRF**: Todos los formularios incluyen tokens CSRF
2. **Validación de entrada**: Los datos son validados tanto en cliente como en servidor
3. **Escapado de salida**: Los datos se escapan antes de mostrarlos en vistas
4. **Hash de contraseñas**: Las contraseñas se almacenan como hashes
5. **Control de sesión**: Verificación de sesiones en operaciones protegidas

## Características Técnicas

- **PHP 7.4+**: Requisito mínimo del sistema
- **MySQL/MariaDB**: Sistema de base de datos
- **Bootstrap 5**: Framework CSS para el diseño
- **Patrón MVC**: Arquitectura organizada en modelos, vistas y controladores
- **Responsive Design**: Compatible con dispositivos móviles y de escritorio