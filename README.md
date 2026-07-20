# Radio RSN — sitio web

Sitio en PHP + Bootstrap 5 con slider, noticias (con panel de administración),
enlaces a redes sociales y un reproductor de radio que permanece disponible
en toda la navegación del sitio (no se detiene al cambiar de página).

## Estructura

```
index.php              Página de inicio (slider + últimas noticias + redes)
noticias.php            Listado de noticias
noticia.php              Detalle de una noticia (?slug=...)
includes/                Config, conexión BD, funciones, header/footer, reproductor
assets/css, assets/js    Estilos y JS (reproductor + navegación AJAX)
admin/                    Panel de administración de noticias
uploads/noticias/         Imágenes subidas desde el panel
sql/schema.sql            Script para crear las tablas en MySQL
```

## Cómo funciona el reproductor "siempre disponible"

El `<audio>` del reproductor vive en `includes/player.php`, fuera del bloque
`#content`. La navegación entre `index.php` y `noticias.php` se hace por AJAX
(`assets/js/main.js`): solo se reemplaza el contenido interno de la página,
así que el reproductor nunca se recarga ni se corta el audio al navegar por
el sitio. Si el usuario recarga la página completa (F5) el reproductor
recuerda si estaba sonando (localStorage) e intenta reanudar automáticamente.

## Despliegue en cPanel (Namecheap u otro hosting)

Namecheap usa cPanel estándar, así que estos pasos aplican igual ahí. Hay un
archivo `radiorsn-cpanel.zip` (junto a esta carpeta) con todo el sitio listo
para subir, incluyendo las imágenes y el contenido tal como está funcionando
ahora mismo.

1. **Subir los archivos**
   - Entra a cPanel → *Administrador de archivos* → abre `public_html`
     (o crea/entra a una subcarpeta si vas a usar un subdominio).
   - Sube `radiorsn-cpanel.zip` con el botón *Cargar/Upload*.
   - Cuando termine de subir, selecciónalo y usa *Extraer/Extract* — esto
     descomprime todos los archivos directamente ahí.
   - Puedes borrar el .zip después de extraerlo (ya no hace falta).

2. **Base de datos MySQL**
   - En cPanel → *Bases de datos MySQL* (MySQL Database Wizard):
     - Crea una base de datos (ej. `radiorsn`) — cPanel le pondrá un prefijo
       automático, tipo `usuario_radiorsn`.
     - Crea un usuario con contraseña y agrégalo a esa base de datos con
       **todos los privilegios (All Privileges)**.
   - Anota los 3 datos que te da cPanel: nombre completo de la base,
     usuario completo, y la contraseña que elegiste.
   - Entra a *phpMyAdmin*, selecciona esa base de datos, pestaña *Importar*,
     y sube el archivo `sql/schema.sql` (ya extraído en el paso 1, dentro de
     la carpeta `sql/`). Esto crea las tablas y carga todo el contenido
     actual (noticias, categorías, programación, slider).

3. **Configuración**
   - En el Administrador de archivos, edita `includes/config.php`
     (clic derecho → *Editar*) y coloca:
     - `DB_HOST` (normalmente `localhost`), `DB_NAME`, `DB_USER`, `DB_PASS`
       con los datos reales que anotaste en el paso 2.
     - `STREAM_URL` con la URL real de tu stream Shoutcast/Icecast, cuando
       la tengas (por ahora tiene una de ejemplo).
     - `SITE_URL` con tu dominio real.
     - Los enlaces de `SOCIAL_*` con tus redes reales.

4. **Permisos de las carpetas de subida**
   - Verifica que `uploads/noticias/` y `uploads/sliders/` tengan permisos
     de escritura (755 suele ser suficiente en cPanel; si la subida de
     imágenes falla desde el panel admin, prueba con 775).

5. **Primer ingreso al panel admin**
   - Ve a `https://tudominio.com/admin/`
   - Usuario: `admin` — Contraseña: `admin123` (o la que hayas puesto tú
     si ya la cambiaste antes de generar este paquete).
   - **Cambia la contraseña de inmediato** desde *Mi perfil* dentro del panel.

6. **Listo**
   - El sitio público está en `https://tudominio.com/`
   - El panel de administración está en `https://tudominio.com/admin/`

## Notas de seguridad ya incluidas

- Contraseñas de administrador guardadas con `password_hash` (bcrypt).
- Protección CSRF en los formularios del panel admin.
- Las carpetas `includes/` y `sql/` bloquean el acceso directo vía navegador.
- Las imágenes subidas se guardan con nombre aleatorio y no pueden
  ejecutarse como PHP aunque alguien intente subir un script disfrazado.
- Consultas a la base de datos siempre parametrizadas (PDO), sin
  concatenar datos del usuario en el SQL.

## Personalización rápida

- **Slider**: se administra desde `/admin` -> Slider (no requiere tocar código).
- **Colores**: variables CSS al inicio de `assets/css/style.css`
  (`--brand`, `--dark-bg`, etc.).
- **Noticias**: se administran desde `/admin` (no requiere tocar código).
