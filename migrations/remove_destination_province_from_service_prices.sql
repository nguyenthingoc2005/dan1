-- Migration: Remove destination_id and province_id from service_prices table
-- Date: 2024

-- Step 1: Drop foreign key constraints
ALTER TABLE `service_prices` 
DROP FOREIGN KEY IF EXISTS `service_prices_ibfk_2`,
DROP FOREIGN KEY IF EXISTS `service_prices_ibfk_3`;

-- Step 2: Drop indexes
ALTER TABLE `service_prices` 
DROP INDEX IF EXISTS `destination_id`,
DROP INDEX IF EXISTS `province_id`;

-- Step 3: Drop columns
ALTER TABLE `service_prices` 
DROP COLUMN IF EXISTS `destination_id`,
DROP COLUMN IF EXISTS `province_id`;

