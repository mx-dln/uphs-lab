ALTER TABLE attendance ADD COLUMN `time_out` TIME NULL AFTER `time_in`;
ALTER TABLE attendance DROP INDEX `unique_attendance`;
ALTER TABLE attendance ADD INDEX `idx_attendance_student_lab_date` (`student_id`, `laboratory_id`, `attendance_date`);
