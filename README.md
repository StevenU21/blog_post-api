# Blog Post API en Laravel 11

Una API para gestionar un sistema de blog personal que incluye funcionalidades de autenticación, gestión de categorías, etiquetas, publicaciones, comentarios y administración de usuarios, con control de roles y permisos.

## Tabla de Contenidos

- [Descripción](#descripción)
- [Requisitos](#requisitos)
- [Instalación](#instalación)
- [Configuración](#configuración)
- [Rutas de la API](#rutas-de-la-api)
  - [Autenticación](#autenticación)
  - [Categorías](#categorías)
  - [Etiquetas](#etiquetas)
  - [Publicaciones](#publicaciones)
  - [Comentarios](#comentarios)
  - [Usuarios](#usuarios)
  - [Administración (Admin)](#administración-admin)
- [Roles y Permisos](#roles-y-permisos)
- [Ejemplos de Uso](#ejemplos-de-uso)
- [Seguridad](#seguridad)
- [Licencia](#licencia)
- [Contacto](#contacto)

## Descripción

Esta API permite gestionar un blog personal mediante Laravel 11. Utiliza Laravel Sanctum para la autenticación por tokens y maneja la autorización a través de roles y permisos, definiendo tres roles principales:

- **admin:** Acceso total a la aplicación.
- **writer:** Acceso a la creación y edición de publicaciones, comentarios y gestión parcial de categorías y etiquetas.
- **reader:** Acceso limitado a la lectura de contenidos y gestión de comentarios.

## Requisitos

- PHP 8.2 >
- Composer
- Laravel 11
- BD (MySQL, SQLite)

## Instalación

1. **Clonar el repositorio:**
   ```bash
   git clone [https://github.com/tu_usuario/tu_repositorio.git](https://github.com/StevenU21/blog-post-api.git)
   ```

   ```bash
   cd blog-post-api
   ```
2. **Instalar dependencias:**
   ```bash
   composer install
   ```
3. **Configurar el archivo `.env`:**
   ```bash
   cp .env.example .env
   ```
5. **Ejecutar migraciones y seeders:**
   ```bash
   php artisan migrate
   ```

    ```bash
   php artisan db:seed
   ```
6. **Instalar y configurar Laravel Sanctum:**
   ```bash
   php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
   ```
7. **Generar la clave de la aplicación:**
   ```bash
   php artisan key:generate
   ```

## Configuración

La aplicación utiliza un sistema de roles y permisos para restringir el acceso a ciertas rutas. En el método `assignPermissionsToRoles` se definen las asignaciones de permisos para cada rol.  
Por ejemplo:

```php
protected function assignPermissionsToRoles(): void
{
    $adminRole = Role::firstOrCreate(['name' => 'admin']);
    $writerRole = Role::firstOrCreate(['name' => 'writer']);
    $readerRole = Role::firstOrCreate(['name' => 'reader']);

    $adminRole->givePermissionTo(Permission::all());

    $writerPermissions = array_merge(
        $this->filterPermissions('categories')->only(['read categories'])->get(),
        $this->filterPermissions('labels')->remove(['destroy labels'])->get(),
        $this->filterPermissions('posts')->get(),
        $this->filterPermissions('comments')->get()
    );

    $writerRole->givePermissionTo($writerPermissions);

    $readerPermissions = array_merge(
        $this->filterPermissions('categories')->only(['read categories'])->get(),
        $this->filterPermissions('labels')->only(['read labels'])->get(),
        $this->filterPermissions('posts')->only(['read posts'])->get(),
        $this->filterPermissions('comments')->get()
    );

    $readerRole->givePermissionTo($readerPermissions);
}
```

## Rutas de la API

### Autenticación

- **Registro de usuario:**  
  `POST /register`  
  Ruta: `Route::post('/register', [RegisterController::class, 'register'])->name('register');`

- **Login:**  
  `POST /login`  
  Ruta: `Route::post('/login', [LoginController::class, 'login'])->name('login');`

- **Logout:**  
  `POST /logout`  
  Ruta: `Route::post('/logout', [LoginController::class, 'logout'])->name('logout');`  
  *Protegida por middleware `auth:sanctum`.*

### Categorías

- **CRUD completo de categorías:**  
  `Route::apiResource('categories', CategoryController::class);`

### Etiquetas

- **Listado de etiquetas y CRUD:**  
  `Route::apiResource('labels', LabelController::class);`
- **Obtener publicaciones por etiqueta:**  
  `GET /labels/{label}/posts`  
  Ruta: `Route::get('/labels/{label}/posts', [LabelController::class, 'label_posts'])->name('labels.post');`

### Publicaciones

- **Listado de publicaciones del usuario autenticado:**  
  `GET /posts/user`  
  Ruta: `Route::get('/posts/user', [PostController::class, 'own_posts'])->name('posts.user');`
- **CRUD completo de publicaciones:**  
  `Route::apiResource('posts', PostController::class);`

### Comentarios

Las rutas de comentarios se agrupan bajo el prefijo `/comments`:

- **Listar comentarios:**  
  `GET /comments`  
  Ruta: `Route::get('/', [CommentController::class, 'index'])->name('comments.index');`
- **Obtener comentarios de una publicación:**  
  `GET /comments/post/{post}`  
  Ruta: `Route::get('/post/{post}', [CommentController::class, 'post_comments'])->name('comments.post');`
- **Crear un comentario en una publicación:**  
  `POST /comments/post/{post}`  
  Ruta: `Route::post('/post/{post}', [CommentController::class, 'store'])->name('comments.post.store');`
- **Actualizar un comentario:**  
  `PUT /comments/{comment}`  
  Ruta: `Route::put('/{comment}', [CommentController::class, 'update'])->name('comments.update');`
- **Eliminar un comentario:**  
  `DELETE /comments/{comment}`  
  Ruta: `Route::delete('/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');`

### Usuarios

- **Listar usuarios:**  
  `GET /users`  
  Ruta: `Route::get('/users', [UserController::class, 'index'])->name('users.index');`

### Administración (Solo para Admin)

Rutas agrupadas bajo `/admin` y protegidas con el middleware `role:admin`:

- **Roles:**
  - Listar roles:  
    `GET /admin/roles`  
    Ruta: `Route::get('/roles', [RoleController::class, 'index'])->name('admin.index');`
  - Asignar rol a un usuario:  
    `PUT /admin/roles/{user}/assign-role`  
    Ruta: `Route::put('/roles/{user}/assign-role', [RoleController::class, 'assignRole'])->name('admin.assign-role');`
  
- **Permisos:**
  - Listar permisos:  
    `GET /admin/permissions`  
    Ruta: `Route::get('/permissions', [PermissionController::class, 'index'])->name('admin.permissions.index');`
  - Obtener permisos de un usuario:  
    `GET /admin/permissions/{user}/list-permission`  
    Ruta: `Route::get('/permissions/{user}/list-permission', [PermissionController::class, 'getUserPermissions'])->name('admin.permissions.list-permission');`
  - Asignar permiso a un usuario:  
    `POST /admin/permissions/{user}/give-permission`  
    Ruta: `Route::post('/permissions/{user}/give-permission', [PermissionController::class, 'assignPermission'])->name('admin.permissions.give-permission');`
  - Revocar permiso a un usuario:  
    `DELETE /admin/permissions/{user}/revoke-permission`  
    Ruta: `Route::delete('/permissions/{user}/revoke-permission', [PermissionController::class, 'revokePermission'])->name('admin.permissions.revoke-permission');`

## Roles y Permisos

El sistema de roles y permisos se gestiona mediante la asignación de permisos específicos a cada rol:

- **Admin:** Permisos totales.
- **Writer:** Permisos para crear, leer, actualizar y eliminar (con excepciones, como no poder eliminar etiquetas).
- **Reader:** Permisos limitados a la lectura y a gestionar comentarios.

La asignación se realiza a través del método `assignPermissionsToRoles()` que se encarga de filtrar y asignar los permisos necesarios.

## Ejemplos de Uso

### Registro y Login

1. **Registro:**  
   Envía una solicitud `POST` a `/register` con los datos del usuario (nombre, email, password, etc.).
2. **Login:**  
   Envía una solicitud `POST` a `/login` con `email` y `password`. Al autenticarse, recibirás un token de autenticación que debes enviar en los encabezados en solicitudes posteriores:
   ```http
   Authorization: Bearer {token}
   ```

### Gestión de Publicaciones

- **Crear publicación:**  
  Envía una solicitud `POST` a `/posts` con el título, contenido, y otros campos requeridos.
- **Obtener publicaciones propias:**  
  Envía una solicitud `GET` a `/posts/user` para ver las publicaciones del usuario autenticado.

## Seguridad

- **Autenticación:** Utiliza Laravel Sanctum para la gestión de tokens.
- **Protección de rutas:**  
  Las rutas críticas se protegen con el middleware `auth:sanctum` y, en caso de rutas administrativas, con `role:admin`.
- **Roles y permisos:**  
  Revisa y ajusta las asignaciones de permisos según los requerimientos de tu aplicación para garantizar un acceso controlado.
