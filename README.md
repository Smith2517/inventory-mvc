# Inventory MVC (PHP + JS) for XAMPP

Este proyecto es un sistema web MVC en PHP para gestionar inventario, con login, oficinas, registro de productos, descuentos de stock con bitácora de movimientos, etiquetas imprimibles (50mm x 30mm) y reportes imprimibles (puedes Guardar como PDF desde el diálogo de impresión del navegador).

## Requisitos
- XAMPP (Apache + PHP >= 7.4 + MariaDB)
- Habilitar mod_rewrite (Apache) si no lo está
- Colocar la carpeta `inventory-mvc` dentro de `htdocs`

## Instalación
1. Importa `sql/schema.sql` en MariaDB (phpMyAdmin).
   - Usuario por defecto: **admin**
   - Contraseña: **admin123**
2. Edita `app/config/config.php` con tus credenciales de DB si usas otras.
3. Navega a: `http://localhost/inventory-mvc/public/`
4. Inicia sesión con el usuario por defecto y empieza a usar el sistema.

## Notas
- Los reportes se abren como páginas imprimibles. Haz clic en **Imprimir** y elige **Guardar como PDF**.
- La etiqueta se genera en 50mm x 30mm (3cm x 5cm aprox.) lista para pegar.
- Los descuentos de stock registran un movimiento (SALIDA) con motivo y oficina de uso. Cuando la cantidad llega a 0, el estado pasa a **AGOTADO**.
