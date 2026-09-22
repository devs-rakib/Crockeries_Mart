-- Migration: Update category icon_class from Font Awesome to Bootstrap Icons
-- Date: 2026-09-20

UPDATE `categories` SET `icon_class` = 'bi bi-egg-fried'         WHERE `id` = 1;
UPDATE `categories` SET `icon_class` = 'bi bi-cup-hot'           WHERE `id` = 2;
UPDATE `categories` SET `icon_class` = 'bi bi-record-circle'     WHERE `id` = 3;
UPDATE `categories` SET `icon_class` = 'bi bi-cake'              WHERE `id` = 4;
UPDATE `categories` SET `icon_class` = 'bi bi-hand-index-thumb'  WHERE `id` = 5;
UPDATE `categories` SET `icon_class` = 'bi bi-box-seam'          WHERE `id` = 6;
UPDATE `categories` SET `icon_class` = 'bi bi-cup-straw'         WHERE `id` = 7;
UPDATE `categories` SET `icon_class` = 'bi bi-gear-wide-connected' WHERE `id` = 8;
