# E.A.P.E.C. Aguas - Sistema de Gestión

Este proyecto es una plataforma integral para la gestión de servicios de agua potable, desarrollada originalmente para Aguas El Colorado. Su foco principal es la facturación del consumo de agua, pero incluye módulos completos para la gestión de bodegas, clientes, análisis de calidad del agua y administración del sistema.

> **Nota:** Este proyecto fue descontinuado en 2017.

## Tabla de Contenidos
1. [Tecnologías Utilizadas](#tecnologías-utilizadas)
2. [Características](#características)
3. [Requisitos](#requisitos)
4. [Instalación](#instalación)
5. [Configuración](#configuración)
6. [Cómo ejecutar el proyecto](#cómo-ejecutar-el-proyecto)
7. [Ejemplos de uso](#ejemplos-de-uso)
8. [Estructura de carpetas](#estructura-de-carpetas)
9. [Módulos del Sistema](#módulos-del-sistema)
10. [Solución de Problemas](#solución-de-problemas)
11. [Notas Adicionales](#notas-adicionales)

## Tecnologías Utilizadas
*   **Lenguaje Backend:** PHP (Compatible con versiones 5.6 - 7.x, usa `mysqli`)
*   **Base de Datos:** MySQL
*   **Frontend:** HTML5, CSS3, Bootstrap (v3), jQuery
*   **Librerías Adicionales:**
    *   `PHPMailer`: Envío de correos electrónicos.
    *   `PHPExcel`: Generación y manipulación de archivos Excel.
    *   `TCPDF`: Generación de documentos PDF.
    *   `PHP2Word`: Generación de documentos Word.
    *   `LibreDTE`: Facturación electrónica (probable integración).

## Características
*   **Gestión de Usuarios:** Sistema de login seguro con roles y permisos (ACL).
*   **Facturación de Agua:** Cálculo de consumo, generación de facturas y control de pagos.
*   **Gestión de Bodegas:** Control de stock, ingresos, egresos y traspasos de materiales.
*   **Gestión de Clientes:** Base de datos de clientes, medidores asignados y datos de contacto.
*   **Análisis de Aguas:** Registro de muestreos y resultados de calidad del agua.
*   **Notificaciones:** Sistema interno de alertas para usuarios.
*   **Seguridad:** Protección contra XSS y validación de entradas.

## Requisitos
*   **Servidor Web:** Apache o Nginx.
*   **PHP:** Versión recomendada 7.4 (compatible con 5.6+). Extensiones necesarias: `mysqli`, `gd`, `mbstring`.
*   **Base de Datos:** MySQL 5.7 o superior / MariaDB.
*   **Navegador Web:** Chrome, Firefox, Edge (Soporte IE legacy incluido).

## Instalación
1.  **Clonar el repositorio:**
    Descarga el código fuente en tu directorio web (ej. `/var/www/html/eapec_aguas`).
2.  **Base de Datos:**
    *   Crea una base de datos llamada `eapec_aguas` en tu servidor MySQL.
    *   Importa el esquema de la base de datos `eapec_aguas.sql` guardada en la carpeta `database`.
3.  **Permisos:**
    Asegúrate de que el servidor web tenga permisos de escritura en las carpetas `upload/`.

## Configuración
La configuración principal se encuentra en `AA2D2CFFDJFDJX1/xrxs_configuracion/config.php`.

Edita este archivo para configurar las credenciales de la base de datos:

```php
// Ejemplo en config.php
if( in_array( $_SERVER['REMOTE_ADDR'], $whitelist) ){
    define( 'DB_SERVER', 'localhost' );
    define( 'DB_NAME', 'eapec_aguas');
    define( 'DB_USER', 'root');
    define( 'DB_PASS', 'tu_contraseña');
}
```

Asegúrate de configurar correctamente las credenciales tanto para el entorno de desarrollo (localhost) como para producción si es necesario.

## Cómo ejecutar el proyecto
1.  Inicia tu servidor web (Apache/Nginx) y servicio MySQL.
2.  Abre tu navegador web.
3.  Navega a la ruta donde instalaste el proyecto, específicamente a la carpeta `administracion`:
    `http://localhost/eapec_aguas/administracion/`
4.  Verás la pantalla de inicio de sesión. Ingresa tus credenciales de usuario.

## Ejemplos de uso
*   **Ingresar al sistema:** Usa el formulario de login en `index.php`.
*   **Ver Stock de Bodega:** Navega al módulo de Bodegas -> Stock Simple para ver los materiales disponibles.
*   **Registrar Lectura:** Ve al módulo de Clientes -> Mediciones e ingresa el consumo del mes.
*   **Generar Informe:** En la sección de Informes, selecciona "Informe de Facturación" para descargar un Excel con los cobros del periodo.

## Estructura de carpetas

```text
eapec_aguas/
├── AA2D2CFFDJFDJX1/         # Core y librerías del sistema
│   ├── PHPMailer/           # Librería de correo
│   ├── xrxs_configuracion/  # Archivos de configuración y conexión DB
│   ├── xrxs_form/           # Formularios reutilizables
│   ├── xrxs_funciones/      # Funciones globales
│   └── xrxs_seguridad/      # Scripts de seguridad (AntiXSS)
├── administracion/          # Aplicación principal (Frontend/Backend)
│   ├── assets/              # CSS, JS, Fuentes (Bootstrap, Metis)
│   ├── core/                # Lógica de negocio core y permisos
│   ├── img/                 # Imágenes estáticas
│   ├── lib_*/               # Librerías PHP (PDF, Excel, Word)
│   ├── upload/              # Archivos subidos por usuarios
│   ├── *.php                # Controladores y Vistas por módulo
│   │   ├── admin_*.php      # Módulo Administración
│   │   ├── bodegas_*.php    # Módulo Bodegas
│   │   ├── clientes_*.php   # Módulo Clientes
│   │   ├── facturacion*.php # Módulo Facturación
│   │   └── principal*.php   # Dashboard y vistas principales
│   └── index.php            # Punto de entrada (Login)
├── database/                # Base de datos
│   └── eapec_aguas.sql      # Respaldo de la base de datos
└── README.md                # Documentación del proyecto
```

## Módulos del Sistema
*   **Administración (`admin_*`)**: Configuración global, datos de la empresa, proveedores.
*   **Bodegas (`bodegas_*`)**: Control de inventario. Permite ingresos, egresos, transformaciones y visualización de stock.
*   **Clientes (`clientes_*`)**: Gestión de fichas de clientes, medidores, historial de pagos y observaciones.
*   **Facturación (`facturacion*`)**: Emisión de documentos tributarios, control de SII (posiblemente), y reportes de ventas.
*   **Análisis (`analisis_*`)**: Gestión de parámetros de calidad del agua, sectores y laboratorios.
*   **Usuarios (`usuarios_*` / `trabajadores_*`)**: Gestión del personal y usuarios del sistema con sus respectivos roles.

## Solución de Problemas
*   **Error de conexión a la Base de Datos:** Verifica `AA2D2CFFDJFDJX1/xrxs_configuracion/conexion.php` y asegúrate de que MySQL esté corriendo y las credenciales sean correctas.
*   **Página en blanco:** Revisa los logs de error de PHP. Puede deberse a una versión de PHP incompatible o falta de permisos en alguna carpeta.
*   **Problemas con librerías (PDF/Excel):** Asegúrate de que las extensiones `gd`, `zip` y `xml` de PHP estén habilitadas.
*   **Acceso denegado:** El sistema utiliza una constante `XMBCXRXSKGC` para seguridad. Asegúrate de siempre entrar por `index.php` o archivos que definan esta constante antes de incluir otros.

## Notas Adicionales
Este sistema utiliza una estructura personalizada donde la lógica y la vista a menudo están en el mismo archivo o separadas por prefijos (`view_`). Se recomienda precaución al actualizar librerías core debido a la antigüedad del código (2017).
