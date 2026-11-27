<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

ERROR - 2026-11-27 16:11:25 --> 404 Page Not Found: Theme/plugins
ERROR - 2026-11-27 16:12:25 --> 404 Page Not Found: Theme/plugins
ERROR - 2026-11-27 20:12:29 --> Query error: Unknown column 'c.item_sales_price' in 'field list' - Invalid query: SELECT `a`.`id`, `a`.`sales_code`, `a`.`sales_date`, `b`.`customer_name`, `b`.`customer_code`, `a`.`grand_total`, `a`.`paid_amount`, `a`.`store_id`, `c`.`item_sales_price`, `c`.`item_sales_qty`, `e`.`category_name`, `c`.`item_sales_qty` as `sales_qty`, `d`.`item_name`
FROM `db_sales` as `a`
LEFT JOIN `db_salesitems` as `c` ON `c`.`sales_id` = `a`.`id`
LEFT JOIN `db_items` as `d` ON d.`id`= c.`item_id`
RIGHT JOIN `db_category` as `e` ON e.`id`= d.`category_id` 
LEFT JOIN `db_customers` as `b` ON b.`id`= a.`customer_id`
WHERE `a`.`sales_status` = 'Final'
AND (`a`.`sales_date` >= '2026-11-27' and `a`.`sales_date` <= '2026-11-27')
AND `a`.`store_id` = '2'
ORDER BY `a`.`sales_date` DESC, `a`.`sales_code` DESC
ERROR - 2026-11-27 16:17:09 --> 404 Page Not Found: Theme/plugins
ERROR - 2026-11-27 17:29:00 --> 404 Page Not Found: Faviconico/index
ERROR - 2026-11-27 17:31:10 --> 404 Page Not Found: Faviconico/index
