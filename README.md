# Blog API - Documentación Completa

## Descripción

Esta API proporciona las funcionalidades necesarias para gestionar un sistema de blogs, permitiendo a los usuarios registrar cuentas, autenticar, crear y administrar publicaciones, categorías, etiquetas, comentarios y respuestas. Además, incluye un sistema de roles y permisos para la administración de usuarios.

## Tecnologías Utilizadas

- **PHP** ^8.2
- **Laravel Framework** ^12.0
- **Laravel Sanctum** (Autenticación)
- **Algolia Search** (Búsqueda)
- **Laravel Scout**
- **Laravel Tinker**
- **Predis** (Redis para caché)
- **Spatie MediaLibrary** (Gestor de medios)
- **Spatie Permission** (Gestor de permisos y roles)
- **Spatie ResponseCache** (Caché de respuestas)
- **Spatie Sluggable** (URLs amigables)

## Autenticación

La API utiliza **Laravel Sanctum** para la autenticación de usuarios, incluyendo verificación de correo electrónico y restablecimiento de contraseña.

### Rutas de Autenticación

- `POST /register` - Registrar un nuevo usuario
- `POST /login` - Iniciar sesión
- `POST /logout` - Cerrar sesión (requiere autenticación)
- `POST /email/verify/{id}/{hash}` - Verificar correo electrónico
- `POST /email/resend` - Reenviar verificación de correo
- `POST /forgot-password` - Solicitar restablecimiento de contraseña
- `POST /reset-password` - Restablecer contraseña

## Endpoints de la API

### Categorías

- `GET /categories` - Obtener todas las categorías
- `GET /categories/{id}` - Obtener una categoría por ID
- `POST /categories` - Crear una nueva categoría
- `PUT /categories/{id}` - Actualizar una categoría
- `DELETE /categories/{id}` - Eliminar una categoría

### Etiquetas (Tags)

- `GET /tags` - Obtener todas las etiquetas
- `GET /tags/{id}` - Obtener una etiqueta por ID
- `POST /tags` - Crear una nueva etiqueta
- `PUT /tags/{id}` - Actualizar una etiqueta
- `DELETE /tags/{id}` - Eliminar una etiqueta

### Publicaciones (Posts)

- `GET /posts` - Obtener todas las publicaciones
- `GET /posts/{id}` - Obtener una publicación por ID
- `POST /posts` - Crear una nueva publicación
- `PUT /posts/{id}` - Actualizar una publicación
- `DELETE /posts/{id}` - Eliminar una publicación
- `GET /user/{user}/posts` - Obtener publicaciones de un usuario
- `GET /posts/search` - Buscar publicaciones

### Comentarios

- `GET /comments` - Obtener todos los comentarios
- `GET /comments/post/{post}` - Obtener comentarios de un post
- `POST /comments/post/{post}` - Agregar un comentario
- `PUT /comments/{comment}` - Actualizar un comentario
- `DELETE /comments/{comment}` - Eliminar un comentario

### Respuestas a Comentarios

- `GET /replies` - Obtener todas las respuestas
- `GET /replies/comments/{comment}` - Obtener respuestas de un comentario
- `POST /replies/comment/{comment}/reply/{parent_reply?}` - Responder a un comentario
- `PUT /replies/{reply}/update` - Actualizar una respuesta
- `DELETE /replies/{reply}/destroy` - Eliminar una respuesta

### Perfil de Usuario

- `GET /profile/users/index` - Ver perfil del usuario autenticado
- `PUT /profile/update` - Actualizar perfil
- `PUT /profile/password` - Cambiar contraseña

### Administración

#### Usuarios

- `GET /admin/users` - Obtener todos los usuarios
- `POST /admin/users` - Crear usuario
- `PUT /admin/users/{id}` - Actualizar usuario
- `DELETE /admin/users/{id}` - Eliminar usuario

#### Roles y Permisos

- `GET /admin/roles` - Obtener roles
- `GET /admin/permissions` - Obtener permisos
- `POST /admin/permissions/{user}/give-permission` - Asignar permiso
- `DELETE /admin/permissions/{user}/revoke-permission` - Revocar permiso

#### Dashboard (Estadísticas)

- `GET /admin/dashboard/totals` - Totales generales
- `GET /admin/dashboard/recent-users` - Usuarios recientes
- `GET /admin/dashboard/recent-posts` - Publicaciones recientes
- `GET /admin/dashboard/top-authors` - Mejores autores
- `GET /admin/dashboard/top-categories` - Categorías populares
- `GET /admin/dashboard/top-posts` - Publicaciones populares

## Políticas de Seguridad

Se han definido las siguientes **policies** para garantizar la seguridad y control de acceso:

- **CategoryPolicy**
- **CommentPolicy**
- **CommentReplyPolicy**
- **PermissionPolicy**
- **PostPolicy**
- **ProfilePolicy**
- **RolePolicy**
- **TagPolicy**
- **UserPolicy**

## Instalación y Configuración

1. Clonar el repositorio:
   ```sh
   https://github.com/StevenU21/blog_post-api-backend.git
   cd blog_post-api-backend
   ```

   ```sh
   cd blog_post-api-backend
   ```
2. Instalar dependencias:
   ```sh
   composer install
   ```
3. Configurar variables de entorno:
   ```sh
   cp .env.example .env
   php artisan key:generate
   ```
4. Migrar base de datos:
   ```sh
   php artisan migrate --seed
   ```
5. Levantar el servidor:
   ```sh
   php artisan serve
   ```

