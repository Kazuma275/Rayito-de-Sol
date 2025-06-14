# Rayito de Sol - README

## Descripción del proyecto
**"Rayito de Sol"** es una plataforma web destinada al alquiler del apartamento de mi madre, ubicado en primera línea de playa en Calahonda. En esta segunda versión, la página evoluciona hacia un **portal de propietarios**, permitiendo que los usuarios no solo reserven el apartamento, sino también gestionen sus propios inmuebles de forma autónoma.

## Índice

- [Objetivos del Proyecto](#objetivos-del-proyecto)
- [Descripción del Proyecto](#descripción-del-proyecto)
- [Histórico de cambios](#histórico-de-cambios)
- [Bibliografía y recursos utilizados](#bibliografía-y-recursos-utilizados)
- [Vídeo explicativo](#vídeo-explicativo)
- [Licencia](#licencia)
- [Contacto](#contacto)

## Objetivos del Proyecto

Los objetivos principales de **Rayito de Sol** son:

- Ofrecer a mi madre una plataforma propia para patrocinar y alquilar su apartamento vacacional, sin depender de plataformas externas.
- Reducir las comisiones habituales de intermediarios, mejorando así los beneficios para el propietario.
- Permitir a los usuarios alquilar el apartamento a un coste más bajo.
- Proporcionar un sistema sencillo y seguro para gestionar reservas, comunicaciones y pagos online.
- Ofrecer una experiencia de usuario cómoda, con acceso a ayuda y soporte técnico.

## Funcionalidades

La plataforma permite a los propietarios:

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
- [Documentación Diseño Interfaces Web](https://www.notion.so/212279a5192380f3b0ede1c2ec52e5cf?pvs=4)
- [Documentación Programación Web en Entorno Cliente](https://www.notion.so/Programaci-n-Web-en-Entorno-Cliente-212279a5192380009670eb3b49dbfce2?pvs=4)
- [Documentación Desarrollo web en Entorno Servidor](https://www.notion.so/212279a51923806b93dbfd44d29e90bd?pvs=4)
- [Código de Conducta](CODE_OF_CONDUCT.md)
- [Licencia](LICENSE)
- [Contribuciones](CONTRIBUTING.md)
- [Seguridad](SECURITY.md)
- [Vídeo explicativo](https://youtu.be/qilXgt90NZE)

## Histórico de cambios

| Fecha       | Cambio realizado                              | Estado |
|-------------|------------------------------------------------|:------:|
| 01/04/2025  | Inicio del proyecto en Laravel + Vue.js        | ✅ |
| 08/04/2025  | Sistema de registro e inicio de sesión         | ✅ |
| 15/04/2025  | Módulo de gestión de propiedades               | ✅ |
| 20/04/2025  | Integración de Stripe para pagos               | ✅ |
| 24/04/2025  | Sistema de mensajería interna                  | ✅ |
| 27/04/2025  | Página de Help & Support                       | ✅ |
| 28/04/2025  | Grabación del vídeo explicativo para checkpoint | ✅ |

## Bibliografía y recursos utilizados

- [Documentación oficial de Vue.js](https://vuejs.org/)
- [Documentación oficial de Laravel](https://laravel.com/docs)
- [Documentación oficial de MariaDB](https://mariadb.org/)
- [Stripe API Documentation](https://stripe.com/docs/api)
- Cursos de Udemy sobre Laravel y Vue.js
- Guías de seguridad web de OWASP

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
