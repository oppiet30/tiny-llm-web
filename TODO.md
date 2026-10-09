# Tiny LLM Development — TODO

Status reviewed: 2026-10-09.

This checklist tracks the CPU-only TinyGPT training project, MariaDB benchmark database, PHP MVC dashboard, and planned comparison page. Checkboxes record confirmed work versus remaining tasks.

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
- [ ] Verify comparable runs and repeat-run handling for the comparison page.

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

## 4. Benchmark comparison page (`/compare`)

- [x] Inspect current GitHub routing, controllers, model, and views.
- [ ] Agree on initial default dataset, model, and step count.
- [ ] Add a Benchmark model method for comparison queries.
- [ ] Create `app/Controllers/CompareController.php`.
- [ ] Create `app/Views/compare.php`.
- [ ] Register `GET /compare` in `routes/web.php`.
- [ ] Register the controller in `index.php`.
- [ ] Add Compare to the shared navigation.
- [ ] Add dataset/model/step-count filters.
- [ ] Display per-machine steps/sec, runtime, and losses.
- [ ] Handle multiple runs from the same machine.
- [ ] Test filtering, missing results, and benchmark consistency.
- [ ] Commit and push the comparison feature.

**Comparison rule:** Compare only runs with the same dataset, model, and training-step count. Initially show individual runs rather than silently averaging repeated measurements.

## 5. Later improvements

- [ ] Add benchmark performance charts.
- [ ] Compare CPU training throughput across machines.
- [ ] Add repeat-run averages and performance variability.
- [ ] Add additional benchmarks and historical trend views.
- [ ] Improve project documentation and benchmark reproduction instructions.

## Next milestone

Implement `/compare`. Decide whether the default group should be Gutenberg 50 MiB / TinyGPT-853K / 10,000 steps, with selectors for other groups.
