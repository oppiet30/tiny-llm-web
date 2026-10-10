# Tiny LLM Development — TODO

Status reviewed: 2026-10-10.

This checklist tracks the CPU-only TinyGPT training project, MariaDB benchmark database, PHP MVC dashboard, comparison charts, and automated tests. Checkboxes record confirmed work versus remaining tasks.

## 1. Tiny LLM training environment

- [x] Set up CPU-only TinyGPT training.
- [x] Establish training on Dell, OptiPlex 3040, Euclid, Mini, and Raspberry Pi 5.
- [x] Run initial 3,000-step Huckleberry Finn benchmarks on all five machines.
- [x] Create Gutenberg 50 MiB training dataset.
- [x] Complete 10,000-step Gutenberg runs on Euclid, OptiPlex 3040, Dell, and Mini.
- [x] Complete Euclid's 10,000-step Huckleberry Finn training and import results (run #14; 3.241 steps/sec).
- [ ] Complete missing benchmark runs, including Raspberry Pi 5 Gutenberg.

## 2. MariaDB benchmark database

- [x] Create `tiny_llm_benchmarks` database and tables.
- [x] Define relationships among machines, models, datasets, and benchmark runs.
- [x] Add schema constraints and foreign keys.
- [x] Create `benchmark_leaderboard` view.
- [x] Correct steps-per-second calculation for resumed runs.
- [x] Version `database/schema.sql` in the Tiny LLM training repository.
- [x] Import and verify Mini Gutenberg benchmark as run #13.
- [x] Import and verify Euclid Huckleberry Finn 10,000-step benchmark as run #14.
- [ ] Import future benchmark results as training completes.
- [x] Verify comparable runs and repeat-run handling for the comparison page.

## 3. PHP MVC website

- [x] Build front controller and custom PHP router.
- [x] Create machine listing and machine detail pages.
- [x] Create benchmark dashboard and individual run detail pages.
- [x] Create model and dataset listing/detail pages.
- [x] Implement versioned JSON API endpoints under `/api/v1`.
- [x] Add shared navigation and CSS styling.
- [x] Verify `/runs/13` returns HTTP 200 and displays Mini results.
- [x] Commit 24 updated application files as `47ed13f`.
- [x] Push `47ed13f` to `oppiet30/tiny-llm-web` main.
- [ ] Test application pages and API endpoints after future updates.
- [x] Add PHPUnit 13 unit tests for router, duration formatting, and benchmark summary calculations (PR #2).
- [x] Add a PHP 8.5 Docker test environment and GitHub Actions CI checks (PR #1).
- [x] Add a clickable GitHub Actions status badge to README.
- [x] Remove misplaced `train.py` from the web repository (PR #4); training code remains in `oppiet30/tiny-llm`.
- [x] Confirm benchmark charts render on Dell's Apache/PHP installation after switching to the feature branch; PR #8 merged.
- [ ] Verify narrow-screen chart and statistics-table layout with real data.

## 4. Benchmark comparison page (`/compare`)

- [x] Inspect current GitHub routing, controllers, model, and views.
- [x] Agree on initial default dataset, model, and step count.
- [x] Add a Benchmark model method for comparison queries.
- [x] Create `app/Controllers/CompareController.php`.
- [x] Create `app/Views/compare.php`.
- [x] Register `GET /compare` in `routes/web.php`.
- [x] Register the controller in `index.php`.
- [x] Add Compare to the shared navigation.
- [x] Add dataset/model/step-count filters.
- [x] Display per-machine steps/sec, runtime, and losses.
- [x] Handle multiple runs from the same machine.
- [x] Test filtering, missing results, and benchmark consistency.
- [x] Commit and push the comparison feature.

**Comparison rule:** Compare only runs with the same dataset, model, and training-step count. Continue listing individual runs; show machine-level averages separately, without silently replacing individual measurements.

## 5. Later improvements

- [x] Add responsive CSS-based benchmark performance charts to `/compare` (PR #3).
- [x] Compare mean CPU training throughput across machines for matching workload filters (PR #3).
- [x] Add repeat-run count, mean, minimum, maximum, and sample standard deviation (PR #3).
- [x] Expand the project README with installation, routes, API, and development guidance.
- [x] Add historical trend views with dataset/model/machine filters (PR #7).
- [ ] Add additional benchmark measurements as training completes.
- [x] Improve benchmark reproduction instructions and document the measurement methodology in `docs/BENCHMARKING.md`.
- [x] Add database-backed integration tests with disposable MariaDB fixtures.
- [x] Add machine-average and individual-run comparison chart modes with regression tests (PR #8).

## Next milestone

Verify the narrow-screen layout on Dell, complete missing training benchmarks (including Raspberry Pi 5 Gutenberg), and import their results. Smoke-test application pages and API endpoints after updates. The `/compare` default remains Gutenberg 50 MiB / TinyGPT-853K / 10,000 steps; selectors allow other groups.
