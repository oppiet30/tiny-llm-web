-- Integration-only schema derived from tiny-llm/database/schema.sql.
-- Do not use this fixture to initialize production databases.

CREATE TABLE `machines` (
  `machine_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `hostname` varchar(255) NOT NULL,
  `cpu_model` varchar(255) NOT NULL,
  `cpu_cores` smallint(5) unsigned DEFAULT NULL,
  `cpu_threads` smallint(5) unsigned DEFAULT NULL,
  `ram_mb` int(10) unsigned DEFAULT NULL,
  `operating_system` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`machine_id`),
  UNIQUE KEY `uq_machine_hostname` (`hostname`),
  CONSTRAINT `chk_machine_cpu_cores` CHECK (`cpu_cores` is null or `cpu_cores` > 0),
  CONSTRAINT `chk_machine_cpu_threads` CHECK (`cpu_threads` is null or `cpu_threads` > 0),
  CONSTRAINT `chk_machine_ram` CHECK (`ram_mb` is null or `ram_mb` > 0),
  CONSTRAINT `chk_machine_cpu_thread_count` CHECK (`cpu_cores` is null or `cpu_threads` is null or `cpu_threads` >= `cpu_cores`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `models` (
  `model_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `parameter_count` bigint(20) unsigned DEFAULT NULL,
  `n_embd` int(10) unsigned DEFAULT NULL,
  `n_head` int(10) unsigned DEFAULT NULL,
  `n_layer` int(10) unsigned DEFAULT NULL,
  `block_size` int(10) unsigned DEFAULT NULL,
  `dropout` decimal(6,5) DEFAULT NULL,
  `tokenizer` varchar(100) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`model_id`),
  UNIQUE KEY `uq_model_name` (`name`),
  CONSTRAINT `chk_model_parameter_count` CHECK (`parameter_count` is null or `parameter_count` > 0),
  CONSTRAINT `chk_model_n_embd` CHECK (`n_embd` is null or `n_embd` > 0),
  CONSTRAINT `chk_model_n_head` CHECK (`n_head` is null or `n_head` > 0),
  CONSTRAINT `chk_model_n_layer` CHECK (`n_layer` is null or `n_layer` > 0),
  CONSTRAINT `chk_model_block_size` CHECK (`block_size` is null or `block_size` > 0),
  CONSTRAINT `chk_model_dropout` CHECK (`dropout` is null or `dropout` >= 0 and `dropout` <= 1),
  CONSTRAINT `chk_model_attention_heads` CHECK (`n_embd` is null or `n_head` is null or `n_head` > 0 and `n_embd` MOD `n_head` = 0)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `datasets` (
  `dataset_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `total_characters` bigint(20) unsigned DEFAULT NULL,
  `training_characters` bigint(20) unsigned DEFAULT NULL,
  `validation_characters` bigint(20) unsigned DEFAULT NULL,
  `vocabulary_size` int(10) unsigned DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`dataset_id`),
  UNIQUE KEY `uq_dataset_name` (`name`),
  CONSTRAINT `chk_dataset_total_characters` CHECK (`total_characters` is null or `total_characters` > 0),
  CONSTRAINT `chk_dataset_training_characters` CHECK (`training_characters` is null or `training_characters` > 0),
  CONSTRAINT `chk_dataset_validation_characters` CHECK (`validation_characters` is null or `validation_characters` > 0),
  CONSTRAINT `chk_dataset_vocabulary_size` CHECK (`vocabulary_size` is null or `vocabulary_size` > 0),
  CONSTRAINT `chk_dataset_character_counts` CHECK (`total_characters` is null or `training_characters` is null or `validation_characters` is null or `training_characters` <= `total_characters` and `validation_characters` <= `total_characters` - `training_characters`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `benchmark_runs` (
  `run_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `machine_id` int(10) unsigned NOT NULL,
  `dataset_id` int(10) unsigned NOT NULL,
  `model_id` int(10) unsigned NOT NULL,
  `start_step` int(10) unsigned DEFAULT NULL,
  `training_steps` int(10) unsigned NOT NULL,
  `steps_this_run` int(10) unsigned DEFAULT NULL,
  `batch_size` int(10) unsigned DEFAULT NULL,
  `learning_rate` decimal(12,10) DEFAULT NULL,
  `pytorch_version` varchar(100) DEFAULT NULL,
  `pytorch_threads` smallint(5) unsigned DEFAULT NULL,
  `interop_threads` smallint(5) unsigned DEFAULT NULL,
  `train_loss` decimal(12,8) DEFAULT NULL,
  `validation_loss` decimal(12,8) DEFAULT NULL,
  `real_seconds` decimal(12,3) DEFAULT NULL,
  `user_seconds` decimal(12,3) DEFAULT NULL,
  `system_seconds` decimal(12,3) DEFAULT NULL,
  `run_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `notes` text DEFAULT NULL,
  PRIMARY KEY (`run_id`),
  KEY `idx_machine` (`machine_id`),
  KEY `idx_dataset` (`dataset_id`),
  KEY `idx_model` (`model_id`),
  KEY `idx_run_date` (`run_date`),
  CONSTRAINT `fk_run_dataset` FOREIGN KEY (`dataset_id`) REFERENCES `datasets` (`dataset_id`),
  CONSTRAINT `fk_run_machine` FOREIGN KEY (`machine_id`) REFERENCES `machines` (`machine_id`),
  CONSTRAINT `fk_run_model` FOREIGN KEY (`model_id`) REFERENCES `models` (`model_id`),
  CONSTRAINT `chk_training_steps` CHECK (`training_steps` > 0),
  CONSTRAINT `chk_steps_this_run` CHECK (`steps_this_run` is null or `steps_this_run` > 0),
  CONSTRAINT `chk_start_step` CHECK (`start_step` is null or `start_step` <= `training_steps`),
  CONSTRAINT `chk_step_range` CHECK (`start_step` is null or `steps_this_run` is null or `start_step` + `steps_this_run` <= `training_steps`),
  CONSTRAINT `chk_batch_size` CHECK (`batch_size` is null or `batch_size` > 0),
  CONSTRAINT `chk_learning_rate` CHECK (`learning_rate` is null or `learning_rate` > 0),
  CONSTRAINT `chk_real_seconds` CHECK (`real_seconds` is null or `real_seconds` > 0),
  CONSTRAINT `chk_train_loss` CHECK (`train_loss` is null or `train_loss` >= 0),
  CONSTRAINT `chk_validation_loss` CHECK (`validation_loss` is null or `validation_loss` >= 0)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO machines (machine_id, hostname, cpu_model) VALUES
  (1, 'test1', 'Test CPU A'),
  (2, 'test2', 'Test CPU B');
INSERT INTO models (model_id, name, parameter_count) VALUES
  (1, 'TinyGPT-Test', 853000),
  (2, 'TinyGPT-Other', 821000);
INSERT INTO datasets (dataset_id, name) VALUES
  (1, 'Test Gutenberg'),
  (2, 'Other Dataset');
INSERT INTO benchmark_runs
  (run_id, machine_id, dataset_id, model_id, start_step, training_steps, steps_this_run, real_seconds, train_loss, validation_loss, run_date)
VALUES
  (1, 1, 1, 1, 0,    10000, 10000, 2000.000, 2.0, 2.1, '2026-10-01 12:00:00'),
  (2, 1, 1, 1, 9000, 10000, 1000,   100.000, 1.8, 1.9, '2026-10-02 12:00:00'),
  (3, 2, 1, 1, 0,    10000, 10000, 4000.000, 2.2, 2.3, '2026-10-03 12:00:00'),
  (4, 2, 2, 1, 0,    10000, 10000, 3000.000, 2.4, 2.5, '2026-10-04 12:00:00'),
  (5, 2, 1, 2, 0,    10000, 10000, 3000.000, 2.4, 2.5, '2026-10-05 12:00:00'),
  (6, 2, 1, 1, 0,     3000,  3000, 1200.000, 2.4, 2.5, '2026-10-06 12:00:00');
