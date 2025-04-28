# Rayito de Sol - README

## Descripción del proyecto
**"Rayito de Sol"** es una plataforma web destinada al alquiler del apartamento de mi madre, ubicado en primera línea de playa en Calahonda. En esta segunda versión, la página evoluciona hacia un **portal de propietarios**, permitiendo que los usuarios no solo reserven el apartamento, sino también gestionen sus propios inmuebles de forma autónoma.

El sistema permite a los propietarios:
- Crear una cuenta y gestionar su perfil.
- Añadir propiedades, visualizar su disponibilidad y alquilarlas a terceros.
- Consultar las fechas libres mediante un calendario interactivo.
- Comunicarse directamente con los interesados mediante un sistema de mensajería interno.
- Configurar su información personal y preferencias.
- Concordar fechas, precios y otros detalles a través del chat.
- Realizar pagos a través de **Stripe** de manera segura.
- Acceder a documentos legales como la política de privacidad.
- Consultar la sección de ayuda en la nueva página de Help & Support.

Esta nueva versión está desarrollada con **Vue.js** en el front-end y **Laravel** en el back-end, utilizando **MariaDB** como sistema de bases de datos.

## Documentación del proyecto

- [Anteproyecto](https://www.notion.so/Anteproyecto-Plantilla-1c5279a519238093af6ae105e277e424?pvs=4)
- [Código de Conducta](CODE_OF_CONDUCT.md)
- [Licencia](LICENSE)
- [Contribuciones](CONTRIBUTING.md)
- [Seguridad](SECURITY.md)

## Características principales

### 1. Portal de Propietarios
- Registro y gestión de cuentas de propietarios.
- Acceso seguro mediante inicio de sesión.

### 2. Gestión de Propiedades
- Alta de nuevas propiedades (con fotos, descripciones y tarifas).
- Visualización de calendarios de disponibilidad.
- Edición y eliminación de propiedades.

### 3. Alquiler y Reservas
- Consulta de fechas disponibles.
- Reservas en línea desde el portal.
- Confirmaciones automáticas de reservas por correo electrónico.

### 4. Configuración de Usuario
- Modificación de datos personales y de inicio de sesión.
- Gestión de la privacidad y notificaciones.

### 5. Políticas Legales
- Acceso a la política de privacidad y términos de uso desde la web.

### 6. Pagos Integrados
- Sistema de cobro a través de **Stripe** para mayor comodidad y seguridad.

## 7. Página de soporte
- Documentación de ayuda y soporte para resolver dudas comunes.
- Canal de contacto directo para recibir soporte adicional.

## 8. Sistema de mensajería interno
- Comunicación directa entre propietarios e interesados.
Acordar precios, fechas y condiciones específicas mediante chat.

## Tecnologías utilizadas

- **Front-end**: [Vue.js](https://vuejs.org/)
- **Back-end**: [Laravel](https://laravel.com/)
- **Base de datos**: [MariaDB](https://mariadb.org/)
- **Otros**:
  - Autenticación y control de accesos seguros.
  - Integración de pagos con **Stripe API**.
  - Gestión de calendarios dinámicos para disponibilidad de propiedades.

## Uso de la aplicación

- **Registro**: Cualquier propietario puede crear una cuenta desde la página de inicio.
- **Inicio de sesión**: Acceso a las funcionalidades de gestión tras autenticación.
- **Añadir propiedades**: Panel de control intuitivo para registrar apartamentos o viviendas disponibles para alquiler.
- **Consultar reservas**: Visualización y gestión de reservas recibidas.
- **Realizar pagos**: Stripe facilita los pagos de manera rápida y segura.
- **Actualizar configuración**: Gestión personalizada de la cuenta desde el área de usuario.

## Consideraciones de seguridad

- Autenticación robusta con tokens y hashing de contraseñas.
- Comunicación segura mediante HTTPS.
- Validaciones estrictas en front-end y back-end.
- Protección frente a ataques comunes como XSS, CSRF y SQL Injection.
- Integración segura del sistema de pagos (PCI DSS compliance con Stripe).

## Licencia
Este proyecto está licenciado bajo la **RayitodeSol License**. Puedes usar, modificar y distribuir el código bajo los términos especificados en el archivo de licencia.

## Contacto
Para consultas, problemas o sugerencias, puedes comunicarte mediante el formulario de contacto de la página o directamente al correo electrónico del administrador.

## Autores
Para conocer a los contribuyentes de este proyecto, visita el archivo [AUTHORS.md](./AUTHORS.md).

--------------------------------------------------------------------------------------------------


# Rayito de Sol (PHP) - README - BETA

## Descripción del proyecto
"Rayito de Sol" es una página web dedicada al alquiler del apartamento de mi madre, situado a primera línea de playa en Calahonda. Esta plataforma ha sido diseñada para ofrecer a los visitantes una experiencia completa y cómoda para alquilar el apartamento y gestionar sus reservas. Los usuarios pueden registrarse, iniciar sesión y enviar formularios de contacto al administrador. Además, se incluyen funcionalidades para la gestión de cuenta o la modificación de contraseñas.

## Documentación del proyecto

- [Código de Conducta](CODE_OF_CONDUCT.md)
- [Licencia](LICENSE)
- [Contribuciones](CONTRIBUTING.md)
- [Seguridad](SECURITY.md)
- [Anteproyecto](https://www.notion.so/Anteproyecto-Plantilla-1c5279a519238093af6ae105e277e424?pvs=4)

## Características de la página web

### 1. Página de inicio
- Visibilidad de la presentación general del apartamento y detalles destacados.
- Información sobre la ubicación y características atractivas de la zona.

### 2. Servicios
- Descripción de los servicios y comodidades ofrecidos por el apartamento.
- Información sobre servicios adicionales, como limpieza y otras facilidades.

### 3. Galería de imágenes
- Visualización de imágenes de alta calidad del apartamento y su entorno.
- Fotografías de las habitaciones, sala, cocina y vistas al mar.

### 4. Cuenta de usuario
- Registro de nuevos usuarios.
- Inicio de sesión para usuarios registrados.
- Cierre de sesión para asegurar la privacidad de los usuarios.

### 5. Gestión de reservas
- Función de reserva en línea disponible para los usuarios que han iniciado sesión.
- Calendario de disponibilidad y selección de fechas.
- Confirmación de reservas y detalles de la reserva por correo electrónico.

### 6. Formulario de contacto
- Opción para enviar mensajes al administrador para consultas, preguntas o solicitudes específicas.
- Respuesta al usuario por parte del administrador, facilitando una comunicación efectiva.

### 7. Gestión de cuenta y seguridad
- Modificación de contraseña disponible para los usuarios que han iniciado sesión, garantizando una mayor seguridad en la cuenta.

## Tecnologías utilizadas
- **Front-end**: HTML, CSS, JavaScript
- **Back-end**: PHP
- **Base de datos**: MariaDB
- **Otros**: Validación de formularios tanto con PHP como con JavaScript para garantizar la seguridad y la integridad de los datos.

## Uso de la aplicación
- **Registro de usuario**: Los nuevos usuarios pueden registrarse en la página de inicio.
- **Inicio de sesión**: Los usuarios existentes pueden iniciar sesión desde la sección de "Cuenta".
- **Reservas**: Una vez iniciada la sesión, los usuarios pueden hacer reservas desde la página de reservas.
- **Contacto**: Los usuarios pueden enviar mensajes directamente al administrador desde la sección de contacto.
- **Modificación de contraseña**: Los usuarios pueden actualizar su contraseña desde el panel de configuración de su cuenta.

## Consideraciones de seguridad
- Se implementa autenticación de usuario con almacenamiento de contraseñas de manera segura (hashing y salting).
- Uso de HTTPS para la protección de la información en tránsito.
- Verificación y validación de formularios tanto del lado del cliente (JavaScript) como del servidor (PHP) para evitar inyecciones y otros ataques comunes.

## Licencia
Este proyecto está licenciado bajo la **RayitodeSol License**. Puedes usar, modificar y distribuir el código, sujeto a los términos de la licencia.

## Contacto
Si tienes alguna pregunta o necesitas más información, por favor contacta con el administrador de la página a través del formulario de contacto o por correo electrónico.

## Autores

Para ver más detalles de los contribuyentes, revisa el archivo [AUTHORS.md](./AUTHORS.md).
