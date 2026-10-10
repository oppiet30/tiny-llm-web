# Tiny LLM Web

[![PHP tests](https://github.com/oppiet30/tiny-llm-web/actions/workflows/tests.yml/badge.svg?branch=main)](https://github.com/oppiet30/tiny-llm-web/actions/workflows/tests.yml)

A lightweight PHP MVC dashboard for viewing and comparing CPU-based TinyGPT training benchmarks stored in MariaDB.

The application is part of the Tiny LLM project and provides a web interface and versioned JSON API for benchmark runs, machines, models, and datasets.

## Features

- **Dashboard** — overview of benchmark results.
- **Machines** — list machines and inspect individual machine details and runs.
- **Models and datasets** — browse model and dataset records.
- **Benchmark run details** — inspect an individual run.
- **Benchmark comparison** — filter runs by dataset, model, and total training-step count; compare runtime, throughput, and loss values.
- **Versioned JSON API** — read machines, models, datasets, and benchmark runs under `/api/v1`.
- **Responsive navigation and styling** — shared navigation and CSS-based layout.

The comparison page deliberately lists individual matching runs. Repeated measurements are not silently averaged, so results from the same machine remain visible.

## Technology

- PHP with a small custom MVC structure and router
- MariaDB/MySQL
- PHP's `mysqli` extension
- Apache-compatible front-controller routing

The application does not require a full-stack PHP framework.

## Requirements

- PHP 8.2 or later (the production development environment has used PHP 8.5)
- PHP `mysqli` extension
- MariaDB or MySQL
- Apache with PHP support, or another PHP-capable web server configured to route requests to `index.php`

The SQL schema and benchmark data are maintained separately in the [Tiny LLM training repository](https://github.com/oppiet30/tiny-llm).

## Installation

### 1. Get the repository

```bash
git clone https://github.com/oppiet30/tiny-llm-web.git
cd tiny-llm-web
```

If the repository is already cloned, update it with:

```bash
git pull --ff-only
```

### 2. Create the database

Create or use the `tiny_llm_benchmarks` database and load the schema maintained in the [Tiny LLM repository](https://github.com/oppiet30/tiny-llm).

The application expects the database to contain the machines, models, datasets, and benchmark-runs tables used by the dashboard.

### 3. Configure the database connection

The front controller loads `config.php`, which in turn loads `config.local.php`. Create your local configuration by copying the example:

```bash
cp config.example.php config.local.php
```

Edit `config.local.php` and set the database host, username, password, and database name for your environment. For example:

```php
<?php

$db = new mysqli(
    'localhost',
    'your_database_user',
    'your_database_password',
    'tiny_llm_benchmarks'
);

if ($db->connect_errno) {
    die('Database connection failed: ' . $db->connect_error);
}

$db->set_charset('utf8mb4');
```

Use a database account with only the permissions the application needs. Do not commit `config.local.php`, real passwords, or other credentials to Git.

### 4. Configure the web server

Configure Apache to serve the project directory and allow the repository's `.htaccess` front-controller rules to work. Ensure the required PHP extensions are enabled.

For a per-user Apache installation, the URL may look like:

```text
http://localhost/~USERNAME/tiny-llm-web/
```

The exact URL depends on the server configuration. The application derives its base path from the request script location, so links can work when the project is installed in a subdirectory.

## Pages and API routes

### Web pages

| Route | Purpose |
| --- | --- |
| `/` | Dashboard |
| `/machines` | Machine listing |
| `/machines/{id}` | Machine details |
| `/models` | Model listing |
| `/models/{id}` | Model details |
| `/datasets` | Dataset listing |
| `/datasets/{id}` | Dataset details |
| `/runs/{id}` | Individual benchmark run |
| `/compare` | Compare matching benchmark runs |

### JSON API

| Route | Purpose |
| --- | --- |
| `/api/v1/machines` | List machines |
| `/api/v1/machines/{id}` | Retrieve one machine |
| `/api/v1/machines/{id}/runs` | List runs for a machine |
| `/api/v1/models` | List models |
| `/api/v1/models/{id}` | Retrieve one model |
| `/api/v1/datasets` | List datasets |
| `/api/v1/datasets/{id}` | Retrieve one dataset |
| `/api/v1/runs` | List benchmark runs |
| `/api/v1/runs/{id}` | Retrieve one benchmark run |

When installed below a URL prefix, prepend that prefix to the routes above.

## Benchmark comparison

The `/compare` page filters by:

- Dataset
- Model
- Total training-step count

Only runs matching all three selections are included. Each matching run shows the machine and CPU, runtime, steps per second, training loss, validation loss, and run date. Links lead to the corresponding run and machine detail pages.

Throughput is calculated from the number of steps completed in the run divided by its runtime. For resumed runs, the database query uses `steps_this_run` when available and otherwise falls back to `training_steps`.

When comparing hardware, keep the dataset, model configuration, and training-step count the same. Repeated runs help reveal normal performance variation; a single run is not enough to estimate that variation.

## Development and testing

The application uses a small custom router in `core/Router.php`, controllers in `app/Controllers/`, data models in `app/Models/`, templates in `app/Views/`, and shared helpers in `app/Helpers/`.

Before committing PHP changes, check syntax:

```bash
find . -path './.git' -prune -o -name '*.php' -type f -print0 \
  | xargs -0 -n1 php -l
```

Automated PHPUnit tests, a Docker-based test image, and a GitHub Actions workflow are being developed on a feature branch. They are not yet documented as an established, passing CI workflow on `main`; check the repository's Actions tab and branch status for current progress.

## Security notes

- Keep database credentials in the ignored local configuration file.
- Do not commit access tokens, passwords, or production secrets.
- Use a least-privilege database account rather than a database administrator account.
- Restrict access to benchmark and API endpoints as appropriate for your deployment environment.

## Project status

This project is actively developed. Check [TODO.md](https://github.com/oppiet30/tiny-llm-web/blob/main/TODO.md) for the current website checklist and planned improvements.

## Support Tiny LLM

Tiny LLM is free and open source. Optional sponsorships help cover domain renewal, website hosting, and continued development.

[Support Tiny LLM on GitHub Sponsors](https://github.com/sponsors/oppiet30)

## License

See [LICENSE](LICENSE) for the project's license terms.
