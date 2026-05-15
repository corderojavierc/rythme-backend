# RythMe — Backend - Hecho por Javier Cordero Martín

API REST y panel de administración de la plataforma social de música **RythMe**, construida con **Laravel 12** y **Filament 5**.

---

## Tecnologías principales

| Tecnología | Versión | Uso |
|---|---|---|
| PHP | 8.5+ | Lenguaje principal |
| Laravel | 12.x | Framework backend |
| Laravel Sanctum | 4.x | Autenticación por tokens |
| Filament | 5.x | Panel de administración |
| Pest | 4.x | Framework de tests |
| SQLite | — | Base de datos (por defecto) |
| Vite + Tailwind CSS | 4.x | Assets del panel admin |

---

## Requisitos previos

- PHP 8.5 o superior
- Composer
- Node.js y npm (para compilar assets del panel admin)
- [Laravel Herd](https://herd.laravel.com/) (recomendado en Windows/macOS)
- Configurar el .env, añadiendo las credenciales de spotify.

## La optener las credenciales de spotify

Cada desarrollador debería crear su propia aplicación gratuita en el [Spotify Developer Dashboard](https://developer.spotify.com/dashboard) y obtener sus propias credenciales. El proceso tarda menos de 5 minutos:

1. Inicia sesión con tu cuenta de Spotify.
2. Haz clic en **Create app**.
3. Rellena el nombre y la descripción (pueden ser cualquier cosa para desarrollo local).
4. En **Redirect URIs** añade `http://localhost:8000`.
5. Copia tu `Client ID` y `Client Secret` al archivo `.env` del backend.

Las credenciales nunca deben comprometerse en el repositorio. El archivo `.env` ya está incluido en `.gitignore` por defecto en Laravel; asegúrate de que así sea.
---

## Instalación

```bash
# Instala dependencias, genera clave, migra la BD y compila assets en un solo paso
composer setup
```

Si prefieres hacerlo paso a paso:

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
npm install
npm run build
```

---

## Configuración del entorno

Copia `.env.example` a `.env` y ajusta los valores necesarios:

```env
APP_NAME=RythMe
APP_URL=http://localhost:8000

DB_CONNECTION=sqlite          # Cambia a mysql si lo necesitas

# Credenciales de Spotify (ver credenciales-spotify.md en la raíz del proyecto)
SPOTIFY_CLIENT_ID=tu_client_id
SPOTIFY_CLIENT_SECRET=tu_client_secret

# Usuario administrador que se crea al ejecutar los seeders
APP_DEFAULT_USERNAME=admin
APP_DEFAULT_NAME="Nombre Admin"
APP_DEFAULT_EMAIL=admin@example.com
APP_DEFAULT_PASSWORD=password
APP_DEFAULT_IMAGE=https://api.dicebear.com/9.x/thumbs/svg?seed=admin
```

---

## Arranque en desarrollo

```bash
composer dev
```

Lanza en paralelo:
- Servidor PHP en `http://localhost:8000`
- Worker de colas de Laravel
- Servidor de Vite con HMR para los assets del panel admin

---

## Estructura del proyecto

```
app/
├── Http/
│   ├── Controllers/Api/   # Controladores de la API REST
│   └── Resources/         # Transformadores de respuestas JSON
├── Models/                # Modelos Eloquent (User, Post, Music, Comment, Like, Follow…)
├── Policies/              # Políticas de autorización por recurso
├── Enums/                 # UserTypeEnum, ArtistApplicationStatusEnum
└── Filament/Admin/        # Recursos y páginas del panel de administración

routes/
├── api.php                # Todos los endpoints de la API
└── web.php                # Redirección al panel admin

database/
├── migrations/            # Migraciones de base de datos
├── factories/             # Factories para tests y seeders
└── seeders/               # Datos iniciales (usuario admin)
```

---

## Endpoints de la API

Todos los endpoints (excepto `/api/login` y `/api/register`) requieren autenticación con el header:

```
Authorization: Bearer {token}
```

### Autenticación

| Método | Ruta | Descripción |
|---|---|---|
| POST | `/api/register` | Registro de usuario |
| POST | `/api/login` | Inicio de sesión |
| POST | `/api/logout` | Cierre de sesión |

### Posts

| Método | Ruta | Descripción |
|---|---|---|
| GET | `/api/posts` | Feed global |
| GET | `/api/posts/followed` | Feed de usuarios seguidos |
| POST | `/api/posts` | Crear post/valoración |
| GET | `/api/posts/{id}` | Detalle de un post |
| DELETE | `/api/posts/{id}` | Eliminar post |
| POST | `/api/posts/search` | Buscar posts |

### Usuarios

| Método | Ruta | Descripción |
|---|---|---|
| GET | `/api/users` | Listar usuarios |
| GET | `/api/users/search` | Buscar usuarios |
| GET | `/api/users/me` | Perfil del usuario autenticado |
| GET | `/api/users/{username}` | Perfil público de un usuario |

### Música

| Método | Ruta | Descripción |
|---|---|---|
| GET | `/api/music` | Listar música |
| POST | `/api/music` | Crear entrada de música |
| GET | `/api/music/{id}` | Detalle de una canción |
| DELETE | `/api/music/{id}` | Eliminar música |
| POST | `/api/music/search` | Buscar música |
| GET | `/api/music/{id}/posts` | Posts sobre una canción |

### Rankings

| Método | Ruta | Descripción |
|---|---|---|
| GET | `/api/musics/top-rated` | Mejor valoradas (histórico global) |
| GET | `/api/musics/most-rated` | Más valoradas (histórico global) |
| GET | `/api/musics/top-rated/actual` | Mejor valoradas (período actual) |
| GET | `/api/musics/most-rated/actual` | Más valoradas (período actual) |
| GET | `/api/musics/top-rated-history/{period}` | Top rated por período histórico |
| GET | `/api/musics/most-rated-history/{period}` | Más valoradas por período histórico |

### Social (Follows, Likes, Comments)

| Método | Ruta | Descripción |
|---|---|---|
| GET | `/api/follows/{id}` | Obtener seguidores |
| POST | `/api/follows` | Seguir usuario |
| DELETE | `/api/follows` | Dejar de seguir |
| GET | `/api/likes/{id}` | Obtener likes |
| POST | `/api/likes` | Dar like |
| DELETE | `/api/likes` | Quitar like |
| GET | `/api/comments` | Listar comentarios |
| POST | `/api/comments` | Crear comentario |
| GET | `/api/comments/{id}` | Detalle de un comentario |
| DELETE | `/api/comments/{id}` | Eliminar comentario |

### Solicitudes de artista

| Método | Ruta | Descripción |
|---|---|---|
| GET | `/api/artist-applications` | Listar solicitudes |
| POST | `/api/artist-applications` | Enviar solicitud |
| GET | `/api/artist-applications/has` | Comprobar si el usuario ya tiene solicitud activa |

---

## Panel de administración

Accesible en `http://localhost:8000/admin`. Requiere un usuario con rol **ADMIN**.

Permite gestionar con CRUD completo: usuarios, música, posts, comentarios, solicitudes de artista y eventos.

---

## Tests

```bash
# Ejecutar todos los tests (PHPStan, cobertura de tipos, unitarios y linting)
composer test

# Solo tests unitarios con informe de cobertura
composer test:unit

# Análisis estático con PHPStan
composer test:types

# Cobertura de tipos (mínimo exigido: 100%)
composer test:type-coverage
```

> **Con Herd en Windows:** usa `herd php artisan test` para asegurarte de que se usa el PHP de Herd.

---

## Linting y formato de código

```bash
composer lint
```

Ejecuta en orden: **Rector** (refactoring automático), **Pint** (estilo PSR-12) y el linter de **Vite**.
