CREATE TABLE IF NOT EXISTS `ks_marketing_lottery_activity` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `creator_uid` int unsigned DEFAULT NULL,
  `show_start_at` datetime DEFAULT NULL,
  `start_at` datetime DEFAULT NULL,
  `end_at` datetime DEFAULT NULL,
  `daily_limit` int NOT NULL DEFAULT '0',
  `daily_send_limit` int NOT NULL DEFAULT '0',
  `per_user_limit` int NOT NULL DEFAULT '0',
  `total_limit` int NOT NULL DEFAULT '0',
  `receive_valid_days` int NOT NULL DEFAULT '0',
  `activity_image` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rule_text` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `config` json DEFAULT NULL,
  `extra_config` json DEFAULT NULL,
  `icon` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `intro` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `activity_type` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'lottery',
  `trigger_scenario` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_mla_status_time` (`status`,`start_at`,`end_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `ks_marketing_lottery_prize` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `activity_id` bigint unsigned NOT NULL,
  `name` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `prize_desc` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `prize_image` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `prize_icon` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `prize_type` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'thanks',
  `is_win` tinyint NOT NULL DEFAULT '1',
  `status` tinyint NOT NULL DEFAULT '1',
  `sort_order` int NOT NULL DEFAULT '0',
  `weight` int NOT NULL DEFAULT '0',
  `win_probability` int NOT NULL DEFAULT '0',
  `total_stock` int NOT NULL DEFAULT '-1',
  `issued_count` int NOT NULL DEFAULT '0',
  `per_user_cap` int NOT NULL DEFAULT '0',
  `coins_amount` int NOT NULL DEFAULT '0',
  `vip_days` int NOT NULL DEFAULT '0',
  `vip_product_id` int unsigned DEFAULT NULL,
  `extra` json DEFAULT NULL,
  `coins_random_min` int NOT NULL DEFAULT '0',
  `coins_random_max` int NOT NULL DEFAULT '0',
  `vip_random_product_ids` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_mlp_activity` (`activity_id`,`status`,`sort_order`),
  KEY `idx_mlp_act_weight` (`activity_id`,`status`,`weight`,`sort_order`),
  KEY `idx_mlp_act_win_probability` (`activity_id`,`status`,`win_probability`,`sort_order`),
  KEY `idx_mlp_act_is_win` (`activity_id`,`is_win`,`status`),
  KEY `idx_mlp_type_stock` (`activity_id`,`prize_type`,`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `ks_marketing_lottery_play` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `activity_id` bigint unsigned NOT NULL,
  `uid` int unsigned NOT NULL,
  `status` tinyint NOT NULL DEFAULT '0',
  `extra` json DEFAULT NULL,
  `expire_at` datetime DEFAULT NULL,
  `remark` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `idempotency_key` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `source_order_id` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_day` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_mlp_idempo` (`idempotency_key`),
  KEY `idx_mlp_act_uid` (`activity_id`,`uid`,`created_at`),
  KEY `idx_mlp_available` (`activity_id`,`uid`,`status`,`expire_at`),
  KEY `idx_mlp_source_order` (`source_order_id`),
  KEY `idx_mlp_day_uid_status` (`activity_id`,`uid`,`created_day`,`status`),
  KEY `idx_mlp_day_activity` (`activity_id`,`created_day`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `ks_marketing_lottery_redemption` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `play_id` bigint unsigned NOT NULL,
  `uid` int unsigned NOT NULL,
  `activity_id` bigint unsigned NOT NULL,
  `activity_name` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `prize_id` bigint unsigned DEFAULT NULL,
  `prize_name` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `is_win` tinyint NOT NULL DEFAULT '1',
  `status` tinyint NOT NULL DEFAULT '0',
  `admin_uid` int unsigned DEFAULT NULL,
  `remark` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `grant_snapshot` json DEFAULT NULL,
  `prize_snapshot` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_mlr_play` (`play_id`),
  KEY `idx_mlr_uid_status` (`uid`,`status`),
  KEY `idx_mlr_act_name` (`activity_id`,`activity_name`),
  KEY `idx_mlr_prize_name` (`prize_id`,`prize_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `ks_marketing_daily_sign_activity` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL DEFAULT '',
  `status` tinyint NOT NULL DEFAULT '1',
  `show_start_at` datetime DEFAULT NULL,
  `start_at` datetime DEFAULT NULL,
  `end_at` datetime DEFAULT NULL,
  `daily_coins` int unsigned NOT NULL DEFAULT '0',
  `cycle_days` int unsigned NOT NULL DEFAULT '7',
  `bonus_vip_days` int unsigned NOT NULL DEFAULT '7',
  `bonus_vip_level` int unsigned NOT NULL DEFAULT '1',
  `rule_text` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_status_time` (`status`,`start_at`,`end_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `ks_marketing_daily_sign_log` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `activity_id` int unsigned NOT NULL DEFAULT '0',
  `uid` int unsigned NOT NULL DEFAULT '0',
  `sign_date` date NOT NULL,
  `continuous_day` int unsigned NOT NULL DEFAULT '1',
  `daily_coins` int unsigned NOT NULL DEFAULT '0',
  `bonus_vip_days` int unsigned NOT NULL DEFAULT '0',
  `is_bonus` tinyint NOT NULL DEFAULT '0',
  `remark` varchar(255) NOT NULL DEFAULT '',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_activity_uid_date` (`activity_id`,`uid`,`sign_date`),
  KEY `idx_uid_date` (`uid`,`sign_date`),
  KEY `idx_activity_date` (`activity_id`,`sign_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET @activity_name := CAST(0xE6AF8FE697A5E7ADBEE588B0E98081E98791E5B881 AS CHAR CHARACTER SET utf8mb4);
SET @activity_rule := CAST(0xE6B4BBE58AA8E69C9FE997B4E6AF8FE697A5E7ADBEE588B0E5BE97E98791E5B881EFBC8CE8BF9EE7BBADE7ADBEE588B037E5A4A9E9A29DE5A496E5A596E58AB137E5A4A9E4BC9AE59198 AS CHAR CHARACTER SET utf8mb4);

INSERT INTO `ks_marketing_daily_sign_activity`
  (`name`, `status`, `start_at`, `end_at`, `daily_coins`, `cycle_days`, `bonus_vip_days`, `bonus_vip_level`, `rule_text`, `created_at`, `updated_at`)
SELECT @activity_name, 1, NULL, NULL, 10, 7, 7, 1, @activity_rule, NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM `ks_marketing_daily_sign_activity` LIMIT 1);

SET @lottery_name := CAST(0xE58585E580BCE68ABDE5A596E6B4BBE58AA8 AS CHAR CHARACTER SET utf8mb4);
SET @lottery_rule := CAST(0xE58585E580BCE68890E58A9FE5908EE58FAFE8EB7E5BE97E68ABDE5A596E69CBAE4BC9AEFBC8CE8AF7E59CA8E6B4BBE58AA8E9A1B5E58685E68ABDE5A596E38082 AS CHAR CHARACTER SET utf8mb4);
SET @prize_coins := CAST(0x3530E98791E5B881 AS CHAR CHARACTER SET utf8mb4);
SET @prize_vip := CAST(0xE99A8FE69CBAE4BC9AE59198 AS CHAR CHARACTER SET utf8mb4);
SET @prize_thanks := CAST(0xE8B0A2E8B0A2E58F82E4B88E AS CHAR CHARACTER SET utf8mb4);

INSERT INTO `ks_marketing_lottery_activity`
  (`id`, `name`, `status`, `creator_uid`, `show_start_at`, `start_at`, `end_at`, `daily_limit`, `daily_send_limit`, `per_user_limit`, `total_limit`, `receive_valid_days`, `activity_image`, `rule_text`, `config`, `extra_config`, `icon`, `intro`, `activity_type`, `trigger_scenario`, `created_at`, `updated_at`)
SELECT 1, @lottery_name, 1, 1, NULL, NULL, NULL, 300, 0, 0, 10000, 0, '', @lottery_rule,
       JSON_OBJECT('min_amount', 1, 'step_amount', 100, 'amount_stat_type', 'multiple'),
       JSON_OBJECT('min_amount', 1, 'step_amount', 100, 'amount_stat_type', 'multiple'),
       '', '', 'lottery', 'pay_success', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM `ks_marketing_lottery_activity` WHERE id = 1);

UPDATE `ks_marketing_lottery_activity`
SET `rule_text` = '充值成功后可获得抽奖机会，请在活动页内抽奖。'
WHERE id = 1 AND (`rule_text` IS NULL OR `rule_text` = '');

INSERT INTO `ks_marketing_lottery_prize`
  (`activity_id`, `name`, `prize_desc`, `prize_image`, `prize_icon`, `prize_type`, `is_win`, `status`, `sort_order`, `weight`, `win_probability`, `total_stock`, `issued_count`, `per_user_cap`, `coins_amount`, `vip_days`, `vip_product_id`, `extra`, `coins_random_min`, `coins_random_max`, `vip_random_product_ids`, `created_at`, `updated_at`)
SELECT 1, @prize_coins, @prize_coins, '', '', 'coins', 1, 1, 10, 30, 30, -1, 0, 0, 50, 0, NULL, JSON_OBJECT(), 0, 0, NULL, NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM `ks_marketing_lottery_prize` WHERE activity_id = 1);

INSERT INTO `ks_marketing_lottery_prize`
  (`activity_id`, `name`, `prize_desc`, `prize_image`, `prize_icon`, `prize_type`, `is_win`, `status`, `sort_order`, `weight`, `win_probability`, `total_stock`, `issued_count`, `per_user_cap`, `coins_amount`, `vip_days`, `vip_product_id`, `extra`, `coins_random_min`, `coins_random_max`, `vip_random_product_ids`, `created_at`, `updated_at`)
SELECT 1, @prize_vip, @prize_vip, '', '', 'vip', 1, 1, 20, 20, 20, -1, 0, 0, 0, 0, NULL, JSON_OBJECT(), 0, 0, JSON_ARRAY(), NOW(), NOW()
WHERE (SELECT COUNT(*) FROM `ks_marketing_lottery_prize` WHERE activity_id = 1) = 1;

INSERT INTO `ks_marketing_lottery_prize`
  (`activity_id`, `name`, `prize_desc`, `prize_image`, `prize_icon`, `prize_type`, `is_win`, `status`, `sort_order`, `weight`, `win_probability`, `total_stock`, `issued_count`, `per_user_cap`, `coins_amount`, `vip_days`, `vip_product_id`, `extra`, `coins_random_min`, `coins_random_max`, `vip_random_product_ids`, `created_at`, `updated_at`)
SELECT 1, @prize_thanks, @prize_thanks, '', '', 'thanks', 0, 1, 99, 0, 0, -1, 0, 0, 0, 0, NULL, JSON_OBJECT(), 0, 0, NULL, NOW(), NOW()
WHERE (SELECT COUNT(*) FROM `ks_marketing_lottery_prize` WHERE activity_id = 1) = 2;

SET @name_parent := CAST(0xE890A5E99480E6B4BBE58AA8 AS CHAR CHARACTER SET utf8mb4);
SET @name_activity := CAST(0xE68ABDE5A596E6B4BBE58AA8 AS CHAR CHARACTER SET utf8mb4);
SET @name_award := CAST(0xE5A596E9A1B9E9858DE7BDAE AS CHAR CHARACTER SET utf8mb4);
SET @name_play := CAST(0xE68ABDE5A596E69CBAE4BC9A AS CHAR CHARACTER SET utf8mb4);
SET @name_redeem := CAST(0xE58591E5A596E8AEB0E5BD95 AS CHAR CHARACTER SET utf8mb4);
SET @name_sign_activity := CAST(0xE7ADBEE588B0E6B4BBE58AA8 AS CHAR CHARACTER SET utf8mb4);
SET @name_sign_log := CAST(0xE7ADBEE588B0E8AEB0E5BD95 AS CHAR CHARACTER SET utf8mb4);

SET @parent_id := (
    SELECT id FROM ks_admin_menu
    WHERE pid = 0 AND controller = '' AND action = '' AND name = @name_parent
    LIMIT 1
);

INSERT INTO ks_admin_menu
    (name, icon, value, controller, action, pid, level, sort, status, created_at)
SELECT @name_parent, 'fa-gift', '', '', '', 0, 1, 0, 'yes', NOW()
WHERE @parent_id IS NULL;

SET @parent_id := IFNULL(@parent_id, LAST_INSERT_ID());

UPDATE ks_admin_menu
SET name = @name_parent, icon = 'fa-gift', status = 'yes'
WHERE id = @parent_id;

INSERT INTO ks_admin_menu
    (name, icon, value, controller, action, pid, level, sort, status, created_at)
SELECT menu_name, '', '', controller_name, 'index', @parent_id, 2, menu_sort, 'yes', NOW()
FROM (
    SELECT @name_activity AS menu_name, 'marketinglotteryactivity' AS controller_name, 10 AS menu_sort
    UNION ALL SELECT @name_award, 'marketinglotteryaward', 20
    UNION ALL SELECT @name_play, 'marketinglotteryplay', 30
    UNION ALL SELECT @name_redeem, 'marketinglotteryredeem', 40
    UNION ALL SELECT @name_sign_activity, 'marketingdailysignactivity', 50
    UNION ALL SELECT @name_sign_log, 'marketingdailysignlog', 60
) AS menus
WHERE NOT EXISTS (
    SELECT 1 FROM ks_admin_menu
    WHERE controller = menus.controller_name AND action = 'index'
);

UPDATE ks_admin_menu
SET pid = @parent_id, level = 2, status = 'yes'
WHERE controller IN (
    'marketinglotteryactivity',
    'marketinglotteryaward',
    'marketinglotteryplay',
    'marketinglotteryredeem',
    'marketingdailysignactivity',
    'marketingdailysignlog'
) AND action = 'index';

UPDATE ks_admin_menu SET name = @name_activity, sort = 10 WHERE controller = 'marketinglotteryactivity' AND action = 'index';
UPDATE ks_admin_menu SET name = @name_award, sort = 20 WHERE controller = 'marketinglotteryaward' AND action = 'index';
UPDATE ks_admin_menu SET name = @name_play, sort = 30 WHERE controller = 'marketinglotteryplay' AND action = 'index';
UPDATE ks_admin_menu SET name = @name_redeem, sort = 40 WHERE controller = 'marketinglotteryredeem' AND action = 'index';
UPDATE ks_admin_menu SET name = @name_sign_activity, sort = 50 WHERE controller = 'marketingdailysignactivity' AND action = 'index';
UPDATE ks_admin_menu SET name = @name_sign_log, sort = 60 WHERE controller = 'marketingdailysignlog' AND action = 'index';

SET @marketing_rule_ids := (
    SELECT GROUP_CONCAT(id ORDER BY id)
    FROM ks_admin_menu
    WHERE id = @parent_id
       OR (pid = @parent_id AND controller IN (
            'marketinglotteryactivity',
            'marketinglotteryaward',
            'marketinglotteryplay',
            'marketinglotteryredeem',
            'marketingdailysignactivity',
            'marketingdailysignlog'
       ))
);

UPDATE ks_admin_role
SET rule = (
    SELECT GROUP_CONCAT(DISTINCT rid ORDER BY rid SEPARATOR ',')
    FROM (
        SELECT CAST(jt.rid AS UNSIGNED) AS rid
        FROM JSON_TABLE(
            CONCAT('["', REPLACE(rule, ',', '","'), '"]'),
            '$[*]' COLUMNS (rid VARCHAR(20) PATH '$')
        ) AS jt
        WHERE jt.rid <> ''
        UNION ALL
        SELECT CAST(jt2.rid AS UNSIGNED) AS rid
        FROM JSON_TABLE(
            CONCAT('["', REPLACE(@marketing_rule_ids, ',', '","'), '"]'),
            '$[*]' COLUMNS (rid VARCHAR(20) PATH '$')
        ) AS jt2
        WHERE jt2.rid <> ''
    ) AS merged
)
WHERE id = 1 AND @marketing_rule_ids IS NOT NULL;
