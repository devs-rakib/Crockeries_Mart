-- Expand order_status ENUM to support full order lifecycle
ALTER TABLE `orders`
  MODIFY COLUMN `order_status` ENUM(
    'pending','confirmed','processing','shipped',
    'out_for_delivery','delivered',
    'cancelled','returned','refunded','failed'
  ) NOT NULL DEFAULT 'pending';
