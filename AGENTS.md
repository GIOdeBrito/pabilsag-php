# AGENTS.md

## Project Overview

"Pabilsag" is a custom PHP 8+ micro-framework (no Composer). All framework code lives under `src/` with the `Pabilsag` namespace. Application-level code lives under `public/src/`.

## Running the App

```
docker compose up
```

The app serves on `http://localhost:3000`. Document root is `public/`. Apache handles rewrites via `.htaccess` with `mod_rewrite` enabled.

## Architecture

- **Entrypoint**: `public/index.php` - bootstraps the `Application`, loads env vars from `public/.env.public`, registers controllers and middlewares, then calls `$app->run()`.
- **Autoloader**: `src/Core/Autoloader.php` maps `Pabilsag\...` namespaces to `src/...` paths. No `composer.json` exists.
- **DI Container**: Services are bound in `src/Core/Application.php`. Controllers are instantiated through the container.
- **Routing**: Attribute-based. Controller methods use `#[Route(method, path, middlewares, ...)]` (see `src/Attributes/Route.php`). Controllers are manually `require`'d then registered via `$app->router()->addController(ClassName::class)` in `index.php`.
- **Middleware pipeline**: Middlewares implement `MiddlewareInterface` with `handle($request, $response, $next)`. Global middlewares added via `$app->middleware()->add()`. Per-route middlewares specified in the `#[Route]` attribute.
- **Database**: SQLite via PDO. Connection config in `public/src/Config/Connections.php`. Database file at `public/database.db`.

## Key Response Patterns

All controller methods receive `($req, $res)` and return a `Response`:

```php
$res->status(200)->json($data)
$res->status(200)->render('ViewName', '_layout', $viewData)
$res->status(200)->html($html)
$res->status(200)->plain($text)
$res->redirect('/path')
```

Views are plain PHP files in `public/src/Views/`. Layouts use `src/Template/_layout.php`. Partials go in `public/src/Partials/`.

## Directory Boundaries

| Directory | Purpose |
|---|---|
| `src/Core/` | Application class, autoloader |
| `src/Routing/` | Router, route matching |
| `src/Http/` | Request, Response, response types |
| `src/Services/` | DI container, middleware pipeline, loader, logger, asset manager |
| `src/Infrastructure/` | Connection factory, JSON serialization |
| `src/Database/` | PDO database wrapper |
| `src/Attributes/` | `#[Route]` attribute |
| `src/Enums/` | HttpMethod, HttpCode, ContentType |
| `src/Helpers/` | Utility functions (Env, String, Json, Http, DateTime, etc.) |
| `src/Interfaces/` | MiddlewareInterface, ResponseInterface, DatabaseInterface |
| `src/Middlewares/` | Framework-level middlewares |
| `public/src/Controllers/` | Application controllers |
| `public/src/Views/` | Application view templates |
| `public/src/Partials/` | View partials |
| `public/src/Middlewares/` | Application middlewares |
| `public/src/DependencyInjection/` | App-level DI bindings |

## Conventions

- Timezone is set to `America/Fortaleza` in `public/index.php`.
- Error output goes to both browser and log file (`log/demo-{YYYY-MM-DD}.log`).
- Allman brace style. Use tabs for indentation.
- No test suite exists.
