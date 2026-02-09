# pkremer.de (Frontend)

Kleines PHP/Twig-Frontend mit Webpack Asset-Pipeline und einem eigenen DSL für Content (`*.page`).

## Doku

Die komplette technische Dokumentation (Architektur, DSL, Templates, Build, CI/CD) liegt auf der Website:

- https://www.pkremer.de/apps/website *(derzeit noch nicht public)*

## Lokal starten (kurz)

- PHP App: DocumentRoot ist `html/` (Entry: `html/index.php`)
- Assets: `npm run build` (prod) oder `npm run dev` (dev)

## Projektstruktur (wo ist was)

- `content/` Seiten + Routing (`*.page`, `routes.yaml`)
- `templates/` Twig Layouts/Pages/Elemente
- `src/` PHP Parser/Renderer/Elemente
- `assets/` SCSS/Build-Sources
- `html/` DocumentRoot (ausgelieferte Assets/Media)

## Requirements

- Node.js + npm (nur für Asset-Build)
- Docker (optional, für Dev-System)
- PHP Extension: `ext-yaml`

## Dev/Test System (Docker)

```bash
docker compose up --build
```

- App ist danach unter `http://localhost:8080` erreichbar
- Dev-ENV ist gesetzt via `APP_ENV=dev` und `dev-php.ini` wird gemountet

## Assets (Watch + Manifest)

```bash
npm run watch
```

- `npm run build` schreibt `html/assets/manifest.json` (wichtig für korrekte Asset-URLs in Twig)
