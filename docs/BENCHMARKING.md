# Reproducing and interpreting TinyGPT benchmarks

Training runs in [oppiet30/tiny-llm](https://github.com/oppiet30/tiny-llm); this repository displays its MariaDB results. The instructions below describe the current trainer. Record the training commit for each experiment because settings and timing boundaries can change.

## Prepare a comparable workload

Use the same training commit, prepared dataset files, model architecture, batch size, learning rate, evaluation frequency, and checkpoint frequency on every machine. Record the Python and PyTorch versions, CPU, OS, thread counts, power settings, and other significant workloads. Allow the machine to settle between repeats; run at least three fresh measurements when practical.

The current trainer uses CPU execution, seed 1337, batch size 16, block size 128, embedding size 128, four heads, four layers, dropout 0.1, and AdamW at learning rate 0.0003. Evaluation runs every 250 steps with ten batches per split. The default checkpoint interval is 250 steps. Model parameter count and its generated name depend on vocabulary size; confirm the printed model and dataset details rather than assuming every dataset produces TinyGPT-853K.

From a clone of the training repository, follow its README to create a virtual environment and install dependencies. Use an existing prepared dataset containing `meta.json`, `train.bin`, and `val.bin`. To prepare a single text file in a separate directory:

```bash
python prepare_data.py --input /path/to/corpus.txt --output-dir data-benchmark
```

Check the dataset name and tokenizer mapping in `meta.json`. Use the same descriptive dataset identity across machines; names alone do not prove the files match. Preserve hashes and environment information alongside the results:

```bash
mkdir -p benchmark-records
git rev-parse HEAD > benchmark-records/training-commit.txt
git diff > benchmark-records/training-local-changes.patch
python --version > benchmark-records/python-version.txt
python -m pip freeze > benchmark-records/packages.txt
sha256sum data-benchmark/meta.json data-benchmark/train.bin data-benchmark/val.bin \
  > benchmark-records/dataset-sha256.txt
lscpu > benchmark-records/cpu.txt
```

Replace `data-benchmark` with the actual prepared directory. Do not overwrite an existing experiment's records.

## Run and retain the results

For a fresh 10,000-step measurement with four PyTorch CPU threads:

```bash
set -o pipefail
python train.py --data-dir data-benchmark --steps 10000 --threads 4 \
  --checkpoint-interval 250 --checkpoint-dir checkpoints/repeat-1 \
  2>&1 | tee benchmark-records/repeat-1.log
```

Run repeats with new checkpoint directories and log names, without `--resume`. Choose thread counts deliberately and record them; the CPU's available logical processors and PyTorch's active thread count are different fields. A common thread count compares machines under that setting; separately testing each machine's best count is a different experiment.

Resuming uses a target step, not an additional-step count:

```bash
python train.py --data-dir data-benchmark --steps 10000 --threads 4 \
  --checkpoint-interval 250 --checkpoint-dir checkpoints/resumed \
  --resume checkpoints/repeat-1/checkpoint-00009000.pt
```

Use a checkpoint from the same dataset and configuration. This example completes 1,000 new steps from step 9,000. The trainer restores optimizer and RNG state when present, and preserves the training RNG during evaluation. This does not guarantee identical results across different PyTorch versions or hardware. Do not benchmark an already-completed checkpoint as a zero-step run.

The trainer writes the SQL filename to its output under `benchmarks/`. Preserve that file with the log. Import the exact generated file on a database-connected machine:

```bash
mariadb -h YOUR_DB_HOST -u YOUR_WRITER_USER -p tiny_llm_benchmarks \
  < benchmarks/YOUR_GENERATED_FILE.sql
```

Current fallback files require the training repository's `001_add_upload_id.sql` migration to have been applied once. Follow that repository's README for migration and optional `--upload` configuration. Direct upload remains opt-in; Euclid uses the SQL fallback. Reimporting the same current SQL file preserves its upload ID and avoids a duplicate run; a fresh training invocation creates a separate measurement.

## What the timer includes

`real_seconds` is the trainer's elapsed wall time measured with `time.perf_counter()`. Timing starts immediately before the training loop and stops after final loss evaluation. It includes batch creation, forward/backward passes, optimizer updates, periodic evaluations, intermediate checkpoint writes and copies, and loop logging. It excludes dataset/model setup, checkpoint loading before the loop, final checkpoint saving, SQL generation, and database upload. It is an end-to-end training-window measurement, not an isolated compute-kernel measurement.

External shell timing includes additional startup and output work, so do not substitute it for `real_seconds` or combine timing methods without identifying the difference. CPU user/system time is not currently populated by this trainer's generated benchmark record.

## Throughput, comparisons, and history

The website calculates:

```text
steps_per_second = COALESCE(steps_this_run, training_steps) / NULLIF(real_seconds, 0)
```

For a fresh run, `steps_this_run` equals the target. For a resumed run, it equals target minus starting step. For example, 1,000 completed steps in 100 seconds means 10 steps/sec, even if the final target is 10,000. Older rows without `steps_this_run` use the full target; verify resumed legacy rows before drawing conclusions.

`/compare` filters by dataset, model, and total target steps only. It does not enforce matching thread counts, PyTorch versions, batch sizes, checkpoint intervals, start steps, or timing conventions. Inspect run details and experiment records for those differences. Resumed segments can appear alongside fresh runs with the same target; their throughput is valid for their measured segment, but their evaluation and checkpoint overhead can differ.

Machine averages are the arithmetic mean of valid per-run throughputs, with each run weighted equally. They are not total completed steps divided by total elapsed time. The statistics table shows valid run count, minimum, maximum, and sample standard deviation using `n - 1`; one run gives `N/A` for standard deviation. Null, nonnumeric, nonfinite, zero, and negative throughput values are excluded from charts and statistics, while matching runs remain in the results table.

Average bars scale to the fastest machine mean; individual-run bars scale to the fastest valid run and link to run details. Bar widths are relative to the current selection, not a fixed cross-page scale. Training and validation loss describe the sampled evaluation batches, not hardware speed.

`/history` shows recorded throughput chronologically with dataset, model, and machine filters. It does not filter by total training steps; a trend can mix workloads or software versions. Check run details before interpreting a rise or fall as a hardware improvement.

## Verify an imported run

Open `/runs/{id}` and confirm the machine, dataset, model, target, completed steps, runtime, and losses. Select its workload on `/compare`, try both chart modes, and inspect `/history`. Keep raw logs and SQL so unexpected values can be checked against the original measurement.
