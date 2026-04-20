-- MySQL dump 10.13  Distrib 8.0.43, for Linux (aarch64)
--
-- Host: localhost    Database: sky_gay2
-- ------------------------------------------------------
-- Server version	8.0.43

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


DROP TABLE IF EXISTS `ks_live`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ks_live` (
`id` int NOT NULL AUTO_INCREMENT,
`username` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
`thumb` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
`gender` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT '' COMMENT '主播性别',
`country` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT '' COMMENT '主播国家',
`hls` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
`cover` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
`created_at` datetime DEFAULT NULL,
`updated_at` datetime DEFAULT NULL,
`model_id` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT '' COMMENT '主播ID',
`show` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT '' COMMENT '状态',
`tag` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci COMMENT '标签',
`status` int DEFAULT '1' COMMENT '状态',
`favorite_oct` int DEFAULT '0' COMMENT '原站收藏数',
`view_oct` int DEFAULT '0' COMMENT '原站观众数',
`view_count` int DEFAULT '0' COMMENT '随机浏览',
`real_view_count` int DEFAULT '0' COMMENT '真浏览',
`favorite_count` int DEFAULT '0' COMMENT '随机收藏',
`real_favorite_count` int DEFAULT '0' COMMENT '真收藏',
`language` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT '' COMMENT '语言',
`comment_ct` int DEFAULT '0' COMMENT '评论数',
`type` int DEFAULT '0' COMMENT '收费类型',
`coins` int DEFAULT '0' COMMENT '收费金币',
`fr_width` int DEFAULT '0' COMMENT '帧宽',
`fr_height` int DEFAULT '0' COMMENT '帧高',
`f_cover` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT '' COMMENT '原始封面地址',
`pay_ct` int DEFAULT '0' COMMENT '支付次数',
`pay_coins` int DEFAULT '0' COMMENT '已支付金币数',
`intro` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci COMMENT '描述',
`reward_ct` int DEFAULT '0' COMMENT '打赏次数',
`reward_coins` int DEFAULT '0' COMMENT '打赏金额',
`sort` int DEFAULT '0' COMMENT '排序',
`like_count` int DEFAULT '0' COMMENT '显示点赞数',
`real_like_count` int DEFAULT '0' COMMENT '点赞数',
PRIMARY KEY (`id`),
KEY `idx_1` (`username`),
KEY `idx_4` (`model_id`),
KEY `idx_5` (`show`),
KEY `mix_idx1` (`tag`(255),`show`,`status`,`view_oct`,`view_count`,`real_view_count`,`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ks_live`
--

LOCK TABLES `ks_live` WRITE;
/*!40000 ALTER TABLE `ks_live` DISABLE KEYS */;
/*!40000 ALTER TABLE `ks_live` ENABLE KEYS */;
UNLOCK TABLES;

DROP TABLE IF EXISTS `ks_live_comment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ks_live_comment` (
`id` int NOT NULL AUTO_INCREMENT,
`live_id` int unsigned NOT NULL DEFAULT '0' COMMENT '直播ID',
`pid` int NOT NULL DEFAULT '0' COMMENT '评论ID,默认0(第一层评论)',
`aff` int NOT NULL DEFAULT '0' COMMENT '用户aff',
`comment` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci COMMENT '留言内容',
`status` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '0:待审核 1:审核通过 2.未通过',
`ipstr` varchar(60) NOT NULL DEFAULT '' COMMENT '用户ip',
`cityname` varchar(100) NOT NULL DEFAULT '' COMMENT '定位城市',
`refuse_reason` varchar(255) NOT NULL DEFAULT '' COMMENT '拒绝通过原因',
`created_at` datetime DEFAULT NULL,
 `updated_at` datetime DEFAULT NULL,
`is_top` int DEFAULT '0' COMMENT '是否置顶 0未置顶 1已置顶',
PRIMARY KEY (`id`),
KEY `live_id` (`live_id`),
KEY `pid` (`pid`),
KEY `aff` (`aff`),
KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='直播评论表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ks_live_comment`
--

LOCK TABLES `ks_live_comment` WRITE;
/*!40000 ALTER TABLE `ks_live_comment` DISABLE KEYS */;
/*!40000 ALTER TABLE `ks_live_comment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ks_live_favorites`
--

DROP TABLE IF EXISTS `ks_live_favorites`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ks_live_favorites` (
`id` int unsigned NOT NULL AUTO_INCREMENT,
`aff` int DEFAULT '0' COMMENT '用户aff',
`live_id` int DEFAULT '0' COMMENT '直播id',
`created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
`updated_at` timestamp NULL DEFAULT NULL COMMENT '更新时间',
PRIMARY KEY (`id`),
KEY `aff` (`aff`) USING BTREE,
KEY `live_id` (`live_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='直播收藏表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ks_live_favorites`
--

LOCK TABLES `ks_live_favorites` WRITE;
/*!40000 ALTER TABLE `ks_live_favorites` DISABLE KEYS */;
/*!40000 ALTER TABLE `ks_live_favorites` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ks_live_like`
--

DROP TABLE IF EXISTS `ks_live_like`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ks_live_like` (
`id` int unsigned NOT NULL AUTO_INCREMENT,
`aff` int DEFAULT '0' COMMENT '用户aff',
`live_id` int DEFAULT '0' COMMENT '直播id',
`created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
`updated_at` timestamp NULL DEFAULT NULL COMMENT '更新时间',
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='直播点赞表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ks_live_like`
--

LOCK TABLES `ks_live_like` WRITE;
/*!40000 ALTER TABLE `ks_live_like` DISABLE KEYS */;
/*!40000 ALTER TABLE `ks_live_like` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ks_live_pay`
--

DROP TABLE IF EXISTS `ks_live_pay`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ks_live_pay` (
`id` int NOT NULL AUTO_INCREMENT,
`aff` int NOT NULL COMMENT '用户aff',
`live_id` int NOT NULL COMMENT '直播id',
`coins` int NOT NULL DEFAULT '0' COMMENT '购买时的价格',
`created_at` timestamp NOT NULL COMMENT '购买时间',
`updated_at` timestamp NOT NULL COMMENT '更新时间',
PRIMARY KEY (`id`) USING BTREE,
KEY `aff` (`aff`,`live_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COMMENT='直播购买记录';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ks_live_pay`
--

LOCK TABLES `ks_live_pay` WRITE;
/*!40000 ALTER TABLE `ks_live_pay` DISABLE KEYS */;
/*!40000 ALTER TABLE `ks_live_pay` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ks_live_related`
--

DROP TABLE IF EXISTS `ks_live_related`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ks_live_related` (
`id` int NOT NULL AUTO_INCREMENT,
`theme_id` int DEFAULT '0',
`live_id` int DEFAULT '0',
`created_at` datetime DEFAULT NULL,
`updated_at` datetime DEFAULT NULL,
PRIMARY KEY (`id`),
KEY `sin_idx1` (`theme_id`),
KEY `sin_idx2` (`live_id`),
KEY `mix_idx1` (`theme_id`,`live_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ks_live_related`
--

LOCK TABLES `ks_live_related` WRITE;
/*!40000 ALTER TABLE `ks_live_related` DISABLE KEYS */;
/*!40000 ALTER TABLE `ks_live_related` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ks_live_theme`
--

DROP TABLE IF EXISTS `ks_live_theme`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ks_live_theme` (
`id` int unsigned NOT NULL AUTO_INCREMENT,
`f_id` varchar(255) NOT NULL DEFAULT '' COMMENT '同步标识',
`name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT '' COMMENT '名字',
`type` int NOT NULL DEFAULT '1' COMMENT '关联类型',
`value` text COMMENT '值',
`desc` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT '' COMMENT '描述',
`sort` int NOT NULL DEFAULT '0' COMMENT '排序',
`status` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '0禁用 1启用',
`created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
`updated_at` timestamp NULL DEFAULT NULL COMMENT '更新时间',
`symbol` int DEFAULT '1' COMMENT '符号类型',
PRIMARY KEY (`id`),
KEY `status` (`status`) USING BTREE,
KEY `mix_idx1` (`f_id`),
KEY `mix_idx3` (`f_id`),
KEY `mix_idx2` (`status`,`sort`,`created_at`,`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COMMENT='直播主题';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ks_live_theme`
--

LOCK TABLES `ks_live_theme` WRITE;
/*!40000 ALTER TABLE `ks_live_theme` DISABLE KEYS */;
INSERT INTO `ks_live_theme` VALUES (7,'','互动玩具',4,'men/interactive-toys,men/sex-toys,men/nipple-toys,men/anal-toys','',100,1,'2025-02-04 09:13:04','2025-02-04 09:18:18',1),(8,'','手机',4,'men/mobile','',90,1,'2025-02-04 09:19:27','2025-02-04 09:19:27',1),(9,'','户外',4,'men/outdoor,men/recordable-publics','',80,1,'2025-02-04 09:20:58','2025-02-04 09:21:33',1);
/*!40000 ALTER TABLE `ks_live_theme` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ks_lottery_base`
--

DROP TABLE IF EXISTS `ks_navigation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ks_navigation` (
`id` int NOT NULL AUTO_INCREMENT,
`title` varchar(64) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL COMMENT '显示名称',
`status` tinyint DEFAULT '0' COMMENT '状态',
`mid_style` tinyint DEFAULT '0' COMMENT '中部样式',
`bot_style` tinyint DEFAULT '0' COMMENT '底部样式',
`created_at` datetime DEFAULT NULL COMMENT '创建时间',
`updated_at` datetime DEFAULT NULL COMMENT '更新时间',
`sort_num` int DEFAULT '0' COMMENT '排序',
`is_aw` tinyint DEFAULT '0' COMMENT '是否暗网 ',
`open_light` tinyint DEFAULT '0' COMMENT '是否开启跑马灯',
`click_num` int DEFAULT '0' COMMENT '点击数',
`is_dm` tinyint DEFAULT '0' COMMENT '是否动漫',
`is_find` tinyint DEFAULT '0' COMMENT '是否发现',
`is_h5` tinyint DEFAULT '0' COMMENT '是否H5',
`h5_url` varchar(255) DEFAULT '' COMMENT 'H5地址',
`is_current` tinyint DEFAULT '0' COMMENT '是否首次选中',
PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ks_navigation`
--

LOCK TABLES `ks_navigation` WRITE;
/*!40000 ALTER TABLE `ks_navigation` DISABLE KEYS */;
INSERT INTO `ks_navigation` VALUES (1,'角色',1,2,2,'2024-10-21 14:56:03','2024-12-03 15:36:38',800,0,0,1604761,0,0,0,'',0),(2,'体型',1,2,2,'2024-10-21 14:56:55','2024-12-03 15:35:53',700,0,0,946783,0,0,0,'',0),(3,'玩法',1,2,2,'2024-10-21 14:57:33','2024-12-03 15:34:01',600,0,0,766367,0,0,0,'',0),(4,'网红泄露',1,2,2,'2024-10-21 14:58:00','2024-12-03 15:36:20',500,0,0,855518,0,0,0,'',0),(5,'其他',1,2,2,'2024-10-21 14:58:21','2024-12-03 15:31:49',400,0,0,334111,0,0,0,'',0),(6,'精品专区',1,2,2,'2024-10-21 14:58:53','2024-12-03 15:34:49',300,0,0,279738,0,0,0,'',0),(7,'二次元CG',1,2,2,'2024-10-21 14:59:32','2024-12-03 15:32:40',200,0,0,369170,0,0,0,'',0),(8,'经典日本GV',1,2,2,'2024-10-21 15:00:22','2024-12-03 15:32:05',100,0,0,363562,0,0,0,'',0),(9,'推荐',1,1,1,'2024-10-21 15:01:16','2025-06-19 17:51:16',999,0,0,13934610,0,0,0,'',1),(10,'发现',1,0,0,'2024-10-21 15:01:41','2024-10-23 21:23:24',900,0,0,0,0,1,0,'',0),(11,'原创精品',1,2,2,'2024-10-22 15:21:05','2024-12-03 15:32:47',70,0,0,242574,0,0,0,'',0),(12,'综艺',1,2,2,'2024-10-30 22:33:37','2024-12-03 15:31:39',10,0,0,168010,0,0,0,'',0),(13,'18岁',1,0,0,'2025-06-19 17:53:08','2025-06-19 21:01:09',99,0,0,0,0,0,1,'http://xlweb.com/zt.html',0);
/*!40000 ALTER TABLE `ks_navigation` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ks_construct`
--

DROP TABLE IF EXISTS `ks_construct`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ks_construct` (
`id` int NOT NULL AUTO_INCREMENT,
`title` varchar(64) NOT NULL COMMENT '导航蓝标签组',
`sub_title` varchar(64) DEFAULT '' COMMENT '副标题',
`sort_num` smallint unsigned NOT NULL COMMENT '排序',
`status` tinyint DEFAULT '0' COMMENT '1 启用',
`created_at` bigint unsigned NOT NULL COMMENT '创建时间',
`updated_at` bigint unsigned NOT NULL COMMENT '修改时间',
`is_recommend` tinyint NOT NULL DEFAULT '0' COMMENT '0 默认  1推荐',
`rating` int DEFAULT '0' COMMENT '打点统计',
`icon` varchar(255) DEFAULT '' COMMENT '图标',
`show_style` tinyint DEFAULT '0' COMMENT '展示样式',
`show_max` int DEFAULT '0' COMMENT '展示数量',
`work_num` int DEFAULT '0' COMMENT '作品数',
`favorites_num` int DEFAULT '0' COMMENT '收藏数',
`nag_id` int DEFAULT '0' COMMENT '导航ID',
`intro` varchar(255) DEFAULT '' COMMENT '简介',
`bg_thumb` varchar(255) DEFAULT '' COMMENT '背景图',
`has_hyh` tinyint DEFAULT '0' COMMENT '是否换一换',
`type` tinyint DEFAULT '0' COMMENT '类型 0普通 1猜你喜欢 <后面可扩展>',
`has_tab` tinyint DEFAULT '0' COMMENT '是否有查询导航栏',
PRIMARY KEY (`id`),
KEY `status` (`status`,`is_recommend`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=DYNAMIC COMMENT='结构表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ks_construct`
--

LOCK TABLES `ks_construct` WRITE;
/*!40000 ALTER TABLE `ks_construct` DISABLE KEYS */;
INSERT INTO `ks_construct` VALUES (1,'今日热点','日更新',10,1,2024,2024,0,0,'',0,0,0,0,9,'','',0,5,0),(2,'精品推荐','',8,1,2024,2024,0,0,'',0,0,36,0,9,'','/upload_01/ads/20241029/2024102916425779584.jpeg',0,0,1),(3,'撸管必备','',1,1,2024,2024,0,0,'',0,0,8,0,9,'','',0,3,0),(4,'大叔','',0,1,2024,2024,0,0,'',0,0,504,0,1,'','/upload_01/ads/20241021/2024102115584526164.jpeg',0,0,1),(6,'直男','',0,1,2024,2024,0,0,'',0,0,723,0,1,'','/upload_01/ads/20241021/2024102116005484996.jpeg',0,0,0),(7,'网红','',0,1,2024,2024,0,0,'',0,0,831,0,1,'','/upload_01/ads/20241021/2024102116023177082.jpeg',0,0,0),(8,'主播','',0,1,2024,2024,0,0,'',0,0,549,0,1,'','/upload_01/ads/20241021/2024102116022077343.jpeg',0,0,0),(9,'教练','',0,1,2024,2024,0,0,'',0,0,979,0,1,'','/upload_01/ads/20241021/2024102116030488539.jpeg',0,0,0),(10,'狼','',0,1,2024,2024,0,0,'/upload_01/ads/20241021/2024102116030444735.png',0,0,843,0,2,'','/upload_01/ads/20241021/2024102116033313714.png',0,0,0),(11,'外卖哥','',0,1,2024,2024,0,0,'',0,0,1026,0,1,'','/upload_01/ads/20241024/2024102420373033311.jpeg',0,0,0),(12,'私教','',0,1,2024,2024,0,0,'',0,0,870,0,1,'','/upload_01/ads/20241021/2024102116040175306.jpeg',0,0,0),(13,'体育生','',0,1,2024,2024,0,0,'',0,0,721,0,1,'','/upload_01/ads/20241021/2024102116042767138.jpeg',0,0,0),(14,'空少','',0,1,2024,2024,0,0,'',0,0,580,0,1,'','/upload_01/ads/20241127/2024112720162360386.jpeg',0,0,0),(15,'大肌霸','',0,1,2024,2024,0,0,'',0,0,665,0,2,'','/upload_01/ads/20241104/2024110421424456902.jpeg',0,0,0),(16,'医生','',0,1,2024,2024,0,0,'',0,0,472,0,1,'','/upload_01/ads/20241024/2024102420442730993.jpeg',0,0,0),(17,'腹肌','',0,1,2024,2024,0,0,'',0,0,483,0,2,'','/upload_01/ads/20241104/2024110421410628312.jpeg',0,0,0),(18,'翘臀','',0,1,2024,2024,0,0,'',0,0,703,0,2,'','/upload_01/ads/20241114/2024111411433022184.jpeg',0,0,0),(19,'狒','',0,1,2024,2024,0,0,'',0,0,687,0,2,'','/upload_01/ads/20241021/2024102116073020118.png',0,0,0),(20,'猴','',0,1,2024,2024,0,0,'',0,0,638,0,2,'','/upload_01/ads/20241021/2024102116074923106.png',0,0,0),(21,'大屌','',0,1,2024,2024,0,0,'',0,0,1246,0,2,'','/upload_01/ads/20241021/2024102118383793042.png',0,0,0),(22,'熊','',0,1,2024,2024,0,0,'',0,0,879,0,2,'','/upload_01/ads/20241021/2024102116115876910.png',0,0,0),(24,'黑人','',0,1,2024,2024,0,0,'',0,0,1260,0,2,'','/upload_01/ads/20241021/2024102116090229508.png',0,0,0),(26,'肌肉男','',0,1,2024,2024,0,0,'',0,0,313,0,2,'','/upload_01/ads/20241021/2024102116104420122.png',0,0,0),(27,'伪娘','',0,1,2024,2024,0,0,'',0,0,695,0,1,'','/upload_01/ads/20241021/2024102116104343439.jpeg',0,0,0),(28,'猪','',0,1,2024,2024,0,0,'',0,0,515,0,2,'','/upload_01/ads/20241021/2024102116110560902.png',0,0,0),(29,'小鲜肉','',0,1,2024,2024,0,0,'',0,0,292,0,1,'','/upload_01/ads/20241024/2024102420490731928.jpeg',0,0,0),(30,'强上','',0,1,2024,2024,0,0,'/upload_01/ads/20241104/2024110423181793231.jpeg',0,0,1276,0,3,'','/upload_01/ads/20241104/2024110423172851471.jpeg',0,0,0),(31,'车震','',0,1,2024,2024,0,0,'/upload_01/ads/20241104/2024110423180723983.jpeg',0,0,493,0,3,'','/upload_01/ads/20241104/2024110423175724792.jpeg',0,0,0),(32,'户外','',0,1,2024,2024,0,0,'/upload_01/ads/20241104/2024110423182545758.png',0,0,1520,0,3,'','/upload_01/ads/20241104/2024110423173879726.png',0,0,0),(33,'扩张','',0,1,2024,2024,0,0,'/upload_01/ads/20241104/2024110423183317370.jpeg',0,0,767,0,3,'','/upload_01/ads/20241104/2024110423183752521.jpeg',0,0,0),(34,'口爆','',0,1,2024,2024,0,0,'/upload_01/ads/20241104/2024110423184392611.png',0,0,2445,0,3,'','/upload_01/ads/20241104/2024110423184931124.png',0,0,0),(35,'射身上','',0,1,2024,2024,0,0,'/upload_01/ads/20241104/2024110423185879445.png',0,0,1635,0,3,'','/upload_01/ads/20241104/2024110423190229210.png',0,0,0),(36,'潮喷','',0,1,2024,2024,0,0,'',0,0,1522,0,3,'','/upload_01/ads/20241021/2024102116315260900.png',0,0,0),(37,'指交','',0,1,2024,2024,0,0,'',0,0,1740,0,3,'','/upload_01/ads/20241021/2024102116321212507.png',0,0,0),(38,'玩具','',0,1,2024,2024,0,0,'',0,0,1541,0,3,'','/upload_01/ads/20241021/2024102116323115490.png',0,0,0),(39,'颜射','',0,1,2024,2024,0,0,'',0,0,1616,0,3,'','/upload_01/ads/20241021/2024102116325056963.png',0,0,0),(40,'深喉','',0,1,2024,2024,0,0,'',0,0,1708,0,3,'','/upload_01/ads/20241021/2024102116331025078.png',0,0,0),(41,'群交','',0,1,2024,2024,0,0,'',0,0,1428,0,3,'','/upload_01/ads/20241021/2024102116332857573.png',0,0,0),(42,'大黑屌','',0,1,2024,2024,0,0,'',0,0,1571,0,2,'','/upload_01/ads/20241104/2024110422484871321.jpeg',0,0,0),(43,'门事件','',0,1,2024,2024,0,0,'',0,0,196,0,4,'','/upload_01/ads/20241021/2024102123080515989.jpeg',0,0,0),(44,'网红泄露','',0,1,2024,2024,0,0,'',0,0,1483,0,4,'','/upload_01/ads/20241021/2024102123094787654.jpeg',0,0,0),(45,'打桩机','',0,1,2024,2024,0,0,'',0,0,1726,0,4,'','/upload_01/ads/20241104/2024110421560585655.png',0,0,0),(46,'网红','',0,0,2024,2024,0,0,'',0,0,1,0,4,'','/upload_01/ads/20241021/2024102123102615951.jpeg',0,0,0),(47,'山东浩浩','',0,0,2024,2024,0,0,'',0,0,132,0,4,'','/upload_01/ads/20241021/2024102123124269805.jpeg',0,0,0),(48,'吖弟险过浪','',0,0,2024,2024,0,0,'',0,0,76,0,4,'','/upload_01/ads/20241021/2024102123145193353.jpeg',0,0,0),(49,'抖音网红','',0,0,2024,2024,0,0,'',0,0,0,0,4,'','/upload_01/ads/20241021/2024102123200950034.jpeg',0,0,0),(50,'男模','',0,1,2024,2024,0,0,'',0,0,266,0,4,'','/upload_01/ads/20241021/2024102123214467551.jpeg',0,0,0),(51,'主播','',0,1,2024,2024,0,0,'',0,0,622,0,4,'','/upload_01/ads/20241021/2024102123294686156.jpeg',0,0,0),(52,'帅鸭','',0,1,2024,2024,0,0,'',0,0,2645,0,4,'','/upload_01/ads/20241021/2024102123315216688.jpeg',0,0,0),(53,'性瘾弟弟','',0,0,2024,2024,0,0,'',0,0,30,0,4,'','/upload_01/ads/20241021/2024102123353313375.png',0,0,0),(54,'搞笑','',0,1,2024,2024,0,0,'',0,0,496,0,5,'','/upload_01/ads/20241104/2024110423281872933.png',0,0,0),(55,'台湾','',0,1,2024,2024,0,0,'',0,0,1441,0,5,'','/upload_01/ads/20241104/2024110423290389244.jpeg',0,0,0),(56,'泰国','',0,1,2024,2024,0,0,'',0,0,445,0,5,'','/upload_01/ads/20241104/2024110423293038869.jpeg',0,0,0),(57,'欧美','',0,1,2024,2024,0,0,'',0,0,822,0,5,'','/upload_01/ads/20241021/2024102123402325797.png',0,0,0),(58,'中文字幕','',0,1,2024,2024,0,0,'',0,0,753,0,5,'','/upload_01/ads/20241021/2024102123404132067.jpeg',0,0,0),(59,'动漫','',0,0,2024,2024,0,0,'',0,0,505,0,5,'','/upload_01/ads/20241021/2024102123405552948.jpeg',0,0,0),(60,'重口味','',0,1,2024,2024,0,0,'',0,0,327,0,5,'','/upload_01/ads/20241021/2024102123410985891.png',0,0,0),(61,'无码','',0,0,2024,2024,0,0,'',0,0,0,0,5,'','/upload_01/ads/20241021/2024102123412494068.png',0,0,0),(62,'cosplay','',0,0,2024,2024,0,0,'',0,0,0,0,5,'','/upload_01/ads/20241021/2024102123413927480.png',0,0,0),(63,'东南亚','',0,1,2024,2024,0,0,'',0,0,519,0,5,'','/upload_01/ads/20241021/2024102123415568284.png',0,0,0),(64,'日韩','',0,1,2024,2024,0,0,'',0,0,724,0,5,'','/upload_01/ads/20241021/2024102123421372729.png',0,0,0),(65,'片商','',0,1,2024,2024,0,0,'',0,0,47,0,6,'','/upload_01/ads/20241021/2024102123454811837.jpeg',0,0,0),(66,'《HORMONE》','',0,1,2024,2024,0,0,'',0,0,29,0,6,'','/upload_01/ads/20241021/2024102123463285456.jpeg',0,0,0),(67,'《GAYDAR》','',0,1,2024,2024,0,0,'',0,0,45,0,6,'','/upload_01/ads/20241021/2024102123470033056.jpeg',0,0,0),(68,'《Hunt》','',0,1,2024,2024,0,0,'',0,0,53,0,6,'','/upload_01/ads/20241021/2024102123471642438.jpeg',0,0,0),(69,'日本CG','',10,1,2024,2024,0,0,'',0,0,70,0,7,'','/upload_01/ads/20241021/2024102123482336384.jpeg',0,0,0),(70,'Jock Studio','',0,1,2024,2024,0,0,'',0,0,6,0,7,'','/upload_01/ads/20241021/2024102123484153470.jpeg',0,0,0),(71,'Sodomy Squad','',0,1,2024,2024,0,0,'',0,0,6,0,7,'','/upload_01/ads/20241021/2024102123490357058.jpeg',0,0,0),(72,'Camp Buddy','',9,1,2024,2024,0,0,'',0,0,30,0,7,'','/upload_01/ads/20241021/2024102123492354669.jpeg',0,0,0),(73,'鬼子专区','',0,1,2024,2024,0,0,'',0,0,192,0,8,'','/upload_01/ads/20241021/2024102123494957364.jpeg',0,0,0),(74,'快乐男生','',1,1,2024,2024,0,0,'',0,0,2,0,11,'','/upload_01/ads/20241022/2024102216545611118.jpeg',0,0,0),(75,'小蓝原创','',10,1,2024,2024,0,0,'',0,0,4,0,11,'','/upload_01/ads/20241022/2024102216402489009.jpeg',0,0,0),(76,'制服','',0,1,2024,2024,0,0,'',0,0,617,0,4,'','/upload_01/ads/20241111/2024111114500272363.jpeg',0,0,0),(77,'SM','',0,1,2024,2024,0,0,'',0,0,793,0,4,'','/upload_01/ads/20241029/2024102920410712401.png',0,0,0),(78,'《G-BOT》','',0,1,2024,2024,0,0,'',0,0,65,0,8,'','/upload_01/ads/20241030/2024103018440375599.jpeg',0,0,0),(79,'《EXFEED》','',0,1,2024,2024,0,0,'',0,0,40,0,8,'','/upload_01/ads/20241030/2024103018453264103.jpeg',0,0,0),(80,'《BRAVO-JAPAN》','',0,1,2024,2024,0,0,'',0,0,44,0,8,'','/upload_01/ads/20241030/2024103018464636902.jpeg',0,0,0),(81,'《仔仔一堂》','',0,1,2024,2024,0,0,'',0,0,21,0,12,'','/upload_01/ads/20241030/2024103022380241961.jpeg',0,0,0),(82,'《男人们的恋爱》','',0,1,2024,2024,0,0,'',0,0,11,0,12,'','/upload_01/ads/20241030/2024103022390277100.jpeg',0,0,0),(84,'G综艺','',4,1,2024,2024,0,0,'',0,0,990,0,12,'','/upload_01/ads/20241030/2024103022433679299.jpeg',0,0,0),(85,'《夏日咖啡男友》','',0,1,2024,2024,0,0,'',0,0,12,0,12,'','/upload_01/ads/20241030/2024103022462158697.jpeg',0,0,0),(86,'入站必刷','',0,1,2024,2024,0,0,'',0,0,0,0,9,'','',0,1,0),(88,'网帅八哥','',0,1,2024,2024,0,0,'',0,0,216,0,11,'','/upload_01/ads/20241104/2024110422005539903.jpeg',0,0,0),(89,'CAP','',0,1,2024,2024,0,0,'',0,0,95,0,11,'','/upload_01/ads/20241104/2024110422024247899.jpeg',0,0,0),(90,'热门新作','',6,1,2024,2024,0,0,'',0,0,0,0,9,'','',0,4,0),(91,'直男发骚','',0,1,2024,2024,0,0,'',0,0,160,0,9,'','',0,2,0),(93,'帅哥飞机','',0,1,2024,2024,0,0,'',0,0,718,0,9,'','',0,0,1),(94,'伪娘勾引','',3,0,2024,2024,0,0,'',0,0,688,0,9,'','',0,0,1),(95,'主奴调教','',4,1,2024,2024,0,0,'',0,0,1165,0,9,'','',0,0,1),(97,'《COAT》','',0,1,2024,2024,0,0,'',0,0,200,0,8,'','/upload_01/ads/20241122/2024112217513541284.jpeg',0,0,0),(98,'《KO》','',0,1,2024,2024,0,0,'',0,0,153,0,8,'','/upload_01/ads/20241122/2024112217514885569.jpeg',0,0,0),(99,'《ACCEED》','',0,1,2024,2024,0,0,'',0,0,190,0,8,'','/upload_01/ads/20241122/2024112217520158834.jpeg',0,0,0),(100,'《G@MES》','',0,1,2024,2024,0,0,'',0,0,96,0,8,'','/upload_01/ads/20241122/2024112217521524815.jpeg',0,0,0);
/*!40000 ALTER TABLE `ks_construct` ENABLE KEYS */;
UNLOCK TABLES;
--
-- Table structure for table `ks_member_hot_rank`
--

DROP TABLE IF EXISTS `ks_member_hot_rank`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ks_member_hot_rank` (
`id` int NOT NULL AUTO_INCREMENT,
`uid` int NOT NULL DEFAULT '0',
`mv_ids` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT '' COMMENT '视频ID(多个逗号隔开)',
`status` tinyint DEFAULT '0',
`sort` int DEFAULT '0',
`created_at` datetime DEFAULT NULL,
`updated_at` datetime DEFAULT NULL,
PRIMARY KEY (`id`),
UNIQUE KEY `uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='热榜用户';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ks_member_hot_rank`
--

LOCK TABLES `ks_member_hot_rank` WRITE;
/*!40000 ALTER TABLE `ks_member_hot_rank` DISABLE KEYS */;
/*!40000 ALTER TABLE `ks_member_hot_rank` ENABLE KEYS */;
UNLOCK TABLES;

DROP TABLE IF EXISTS `ks_post`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ks_post` (
`id` int unsigned NOT NULL AUTO_INCREMENT,
`aff` char(32) NOT NULL DEFAULT '' COMMENT '用户AFF',
`content` text NOT NULL COMMENT '内容',
`is_deleted` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '用户删除标识 0否 1是 ',
`like_num` int unsigned NOT NULL DEFAULT '0' COMMENT '点赞数量',
`comment_num` int unsigned NOT NULL DEFAULT '0' COMMENT '评论数量',
`is_best` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '置精 0否 1是',
`category` tinyint NOT NULL DEFAULT '1' COMMENT '帖子类型 1图片 2视频 3图文',
`refuse_reason` varchar(255) NOT NULL DEFAULT '' COMMENT '拒绝通过的原因',
`photo_num` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '图片数量',
`video_num` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '视频数量',
`is_finished` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '资源是否完成 0否1是',
`ipstr` varchar(60) NOT NULL DEFAULT '' COMMENT '用户ip',
`cityname` varchar(100) NOT NULL DEFAULT '' COMMENT '定位城市',
`topic_id` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '话题ID',
`view_num` int unsigned NOT NULL DEFAULT '0' COMMENT '浏览数量',
`refresh_at` timestamp NULL DEFAULT NULL COMMENT '刷新时间',
`title` varchar(255) NOT NULL DEFAULT '' COMMENT '标题',
`reward_amount` int NOT NULL DEFAULT '0' COMMENT '打赏金币',
`status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0:待审核 1:审核中 2.审核通过 3.未通过 4.被举报',
`created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
`updated_at` timestamp NULL DEFAULT NULL COMMENT '修改时间',
`sort` int NOT NULL DEFAULT '0' COMMENT '排序 越大越前',
`favorite_num` int NOT NULL DEFAULT '0' COMMENT '收藏数',
`reward_num` int NOT NULL DEFAULT '0' COMMENT '打赏次数',
`set_top` tinyint DEFAULT '0',
`_id` int DEFAULT '0',
`price` int NOT NULL DEFAULT '0' COMMENT '支付价格',
`type` tinyint DEFAULT '0' COMMENT '类型',
PRIMARY KEY (`id`),
KEY `aff` (`aff`),
KEY `status` (`status`) USING BTREE,
KEY `is_deleted` (`is_deleted`) USING BTREE,
KEY `is_best` (`is_best`) USING BTREE,
KEY `category` (`category`) USING BTREE,
KEY `topic_id` (`topic_id`) USING BTREE,
KEY `refresh_at` (`refresh_at`) USING BTREE,
KEY `is_finished` (`is_finished`),
KEY `set_top` (`set_top`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COMMENT='帖子表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ks_post`
--
--
-- Table structure for table `ks_post_comment`
--

DROP TABLE IF EXISTS `ks_post_comment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ks_post_comment` (
`id` int NOT NULL AUTO_INCREMENT,
`post_id` int unsigned NOT NULL DEFAULT '0' COMMENT '帖子ID',
`pid` int NOT NULL DEFAULT '0' COMMENT '评论ID,默认0(第一层评论)',
`aff` int NOT NULL DEFAULT '0' COMMENT '用户aff',
`comment` text NOT NULL COMMENT '留言内容',
`status` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '0:待审核\n1:审核通过\n2.未通过\n3.禁言\n',
`is_read` tinyint NOT NULL DEFAULT '0' COMMENT '被回复者是否已读',
`like_num` int NOT NULL DEFAULT '0' COMMENT '此条评论点赞数量',
`video_num` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '视频数量',
`photo_num` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '图片数量',
`ipstr` varchar(60) NOT NULL DEFAULT '' COMMENT '用户ip',
`cityname` varchar(100) NOT NULL DEFAULT '' COMMENT '定位城市',
`complain_num` int unsigned NOT NULL DEFAULT '0' COMMENT '被举报次数',
`refuse_reason` varchar(255) NOT NULL DEFAULT '' COMMENT '拒绝通过原因',
`created_at` timestamp NULL DEFAULT NULL,
`updated_at` timestamp NULL DEFAULT NULL,
`is_finished` int NOT NULL DEFAULT '0' COMMENT '资源是否处理完 0未处理 1已处理',
`is_top` int DEFAULT '0' COMMENT '是否置顶 0未置顶 1已置顶',
PRIMARY KEY (`id`),
KEY `post_id` (`post_id`),
KEY `pid` (`pid`),
KEY `aff` (`aff`),
KEY `status` (`status`),
KEY `is_top` (`is_top`),
KEY `is_finished` (`is_finished`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COMMENT='帖子评论表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ks_post_comment`
--

LOCK TABLES `ks_post_comment` WRITE;
/*!40000 ALTER TABLE `ks_post_comment` DISABLE KEYS */;
/*!40000 ALTER TABLE `ks_post_comment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ks_post_comment_keyword`
--

DROP TABLE IF EXISTS `ks_post_comment_keyword`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ks_post_comment_keyword` (
`id` int unsigned NOT NULL AUTO_INCREMENT,
`keyword` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT '' COMMENT '关键词',
`created_at` timestamp NULL DEFAULT NULL,
`updated_at` timestamp NULL DEFAULT NULL,
PRIMARY KEY (`id`) USING BTREE,
UNIQUE KEY `keyword` (`keyword`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ks_post_comment_keyword`
--

LOCK TABLES `ks_post_comment_keyword` WRITE;
/*!40000 ALTER TABLE `ks_post_comment_keyword` DISABLE KEYS */;
/*!40000 ALTER TABLE `ks_post_comment_keyword` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ks_post_comment_user_like`
--

DROP TABLE IF EXISTS `ks_post_comment_user_like`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ks_post_comment_user_like` (
`id` int unsigned NOT NULL AUTO_INCREMENT,
`aff` int NOT NULL,
`related_id` int NOT NULL,
`post_id` int NOT NULL,
`created_at` timestamp NOT NULL ON UPDATE CURRENT_TIMESTAMP,
`updated_at` timestamp NULL DEFAULT NULL,
PRIMARY KEY (`id`),
KEY `aff` (`aff`),
KEY `related_id` (`related_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ks_post_comment_user_like`
--

LOCK TABLES `ks_post_comment_user_like` WRITE;
/*!40000 ALTER TABLE `ks_post_comment_user_like` DISABLE KEYS */;
/*!40000 ALTER TABLE `ks_post_comment_user_like` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ks_post_media`
--

DROP TABLE IF EXISTS `ks_post_media`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ks_post_media` (
`id` int unsigned NOT NULL AUTO_INCREMENT,
`media_url` varchar(255) NOT NULL DEFAULT '' COMMENT '视频或图片地址',
`cover` varchar(255) NOT NULL DEFAULT '' COMMENT '视频封面',
`thumb_width` int NOT NULL DEFAULT '0' COMMENT '封面宽',
`thumb_height` int NOT NULL DEFAULT '0' COMMENT '封面高',
`pid` int NOT NULL DEFAULT '0' COMMENT '帖子ID',
`aff` char(32) NOT NULL DEFAULT '0' COMMENT '上传用户AFF',
`type` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '类型 1图片 2视频',
`created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
`status` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '0 未转换 1 已转换 2 转换中',
`duration` int NOT NULL DEFAULT '0' COMMENT '视频持续时间',
`updated_at` timestamp NULL DEFAULT NULL COMMENT '更新时间',
`relate_type` tinyint(1) DEFAULT NULL COMMENT '关联类型 1帖子 2评论',
PRIMARY KEY (`id`),
KEY `pid` (`pid`) USING BTREE,
KEY `type` (`type`) USING BTREE,
KEY `status` (`status`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COMMENT='帖子媒体表';
/*!40101 SET character_set_client = @saved_cs_client */;

DROP TABLE IF EXISTS `ks_post_user_like`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ks_post_user_like` (
`id` int unsigned NOT NULL AUTO_INCREMENT,
`aff` int DEFAULT NULL,
`related_id` int DEFAULT NULL,
`type` int DEFAULT NULL,
`created_at` timestamp NULL DEFAULT NULL,
`updated_at` timestamp NULL DEFAULT NULL,
PRIMARY KEY (`id`),
KEY `aff` (`aff`),
KEY `related_id` (`related_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ks_post_user_like`
--

LOCK TABLES `ks_post_user_like` WRITE;
/*!40000 ALTER TABLE `ks_post_user_like` DISABLE KEYS */;
/*!40000 ALTER TABLE `ks_post_user_like` ENABLE KEYS */;
UNLOCK TABLES;
--
-- Table structure for table `ks_porn_category`
--

DROP TABLE IF EXISTS `ks_porn_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ks_porn_category` (
`id` int NOT NULL AUTO_INCREMENT,
`title` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '标题',
`sub_title` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '副标题',
`thumb` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT '' COMMENT '封面',
`rating` int NOT NULL COMMENT '点击量',
`type` tinyint NOT NULL COMMENT '类型 0普通 1最多喜欢 2畅销榜 3最新 4手游',
`is_recommend` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1 推荐',
`show_style` tinyint NOT NULL COMMENT '0:1*3 1:1*2 2:1*1 3:1*N',
`show_max` tinyint NOT NULL DEFAULT '0' COMMENT '默认最大展示数量',
`status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态 ',
`created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
`updated_at` timestamp NULL DEFAULT NULL COMMENT '更新时间',
`sort` int DEFAULT '0' COMMENT '排序',
`works_num` int DEFAULT '0' COMMENT '作品数',
PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC COMMENT='黄游分类';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ks_porn_category`
--

LOCK TABLES `ks_porn_category` WRITE;
/*!40000 ALTER TABLE `ks_porn_category` DISABLE KEYS */;
INSERT INTO `ks_porn_category` VALUES (1,'BL游戏','BL游戏','',0,0,0,0,9,1,'2025-01-31 09:29:21','2025-02-12 14:33:40',100,0);
/*!40000 ALTER TABLE `ks_porn_category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ks_porn_comment`
--

DROP TABLE IF EXISTS `ks_porn_comment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ks_porn_comment` (
`id` int NOT NULL AUTO_INCREMENT,
`porn_id` int unsigned NOT NULL DEFAULT '0' COMMENT '帖子ID',
`pid` int NOT NULL DEFAULT '0' COMMENT '评论ID,默认0(第一层评论)',
`aff` int NOT NULL DEFAULT '0' COMMENT '用户aff',
`comment` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci COMMENT '留言内容',
`status` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '0:待审核 1:审核通过 2.未通过',
`ipstr` varchar(60) NOT NULL DEFAULT '' COMMENT '用户ip',
`cityname` varchar(100) NOT NULL DEFAULT '' COMMENT '定位城市',
`refuse_reason` varchar(255) NOT NULL DEFAULT '' COMMENT '拒绝通过原因',
`created_at` datetime DEFAULT NULL,
`updated_at` datetime DEFAULT NULL,
`is_top` int DEFAULT '0' COMMENT '是否置顶 0未置顶 1已置顶',
PRIMARY KEY (`id`),
KEY `porn_id` (`porn_id`),
KEY `pid` (`pid`),
KEY `aff` (`aff`),
KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='黄游评论表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ks_porn_comment`
--

LOCK TABLES `ks_porn_comment` WRITE;
/*!40000 ALTER TABLE `ks_porn_comment` DISABLE KEYS */;
/*!40000 ALTER TABLE `ks_porn_comment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ks_porn_favorites`
--

DROP TABLE IF EXISTS `ks_porn_favorites`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ks_porn_favorites` (
`id` int unsigned NOT NULL AUTO_INCREMENT,
`aff` int DEFAULT '0' COMMENT '用户aff',
`porn_id` int DEFAULT '0' COMMENT '黄游id',
`created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
`updated_at` timestamp NULL DEFAULT NULL COMMENT '更新时间',
PRIMARY KEY (`id`),
KEY `aff` (`aff`) USING BTREE,
KEY `porn_id` (`porn_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='黄游收藏表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ks_porn_favorites`
--

LOCK TABLES `ks_porn_favorites` WRITE;
/*!40000 ALTER TABLE `ks_porn_favorites` DISABLE KEYS */;
/*!40000 ALTER TABLE `ks_porn_favorites` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ks_porn_game`
--

DROP TABLE IF EXISTS `ks_porn_game`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ks_porn_game` (
`id` int unsigned NOT NULL AUTO_INCREMENT,
`_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT '' COMMENT '资源ID',
`name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT '' COMMENT '标题',
`category_id` int DEFAULT '0' COMMENT '分类ID',
`tags` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT '' COMMENT '标签',
`thumb` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT '' COMMENT '封面',
`remark` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT '' COMMENT '备注',
`intro` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci COMMENT '简介',
`play_intro` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci COMMENT '游戏玩法',
`desc` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci COMMENT '说明',
`type` int DEFAULT '0' COMMENT '类型 0免费 1次数 2金币 3次数和金币',
`coins` int DEFAULT '0' COMMENT '金币',
`is_recommend` tinyint DEFAULT '0' COMMENT '是否推荐',
`is_hot` tinyint DEFAULT '0' COMMENT '是否热门',
`real_like_count` int DEFAULT '0' COMMENT '真实喜欢数',
`like_count` int DEFAULT '0' COMMENT '喜欢数',
`comment_count` int DEFAULT '0' COMMENT '评论数',
`view_count` int DEFAULT '0' COMMENT '浏览数',
`real_view_count` int DEFAULT '0' COMMENT '真实浏览数',
`buy_num` int DEFAULT '0' COMMENT '购买次数',
`buy_coins` int DEFAULT '0' COMMENT '购买总金币',
`buy_fake` int DEFAULT '0' COMMENT '显示解锁量',
`score` int DEFAULT '0' COMMENT '评分',
`sort` int DEFAULT '0' COMMENT '排序',
`status` tinyint DEFAULT '1' COMMENT '状态',
`created_at` datetime DEFAULT CURRENT_TIMESTAMP,
`updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
`refresh_at` datetime DEFAULT NULL COMMENT '刷新时间',
`favorite_ct` int DEFAULT '0' COMMENT '收藏数',
`content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci COMMENT '下载地址及密码',
`real_favorite` int DEFAULT '0' COMMENT '真实收藏数',
PRIMARY KEY (`id`),
KEY `ft_categoy` (`category_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='黄游表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ks_porn_game`
--

LOCK TABLES `ks_porn_game` WRITE;
/*!40000 ALTER TABLE `ks_porn_game` DISABLE KEYS */;
INSERT INTO `ks_porn_game` VALUES (1,'85','[安卓/中文]美股达人[151M/网盘下载]',1,'','/upload_01/upload1/20250208/2025020815491986992.png','',' 类似于音游的打pp的单机游戏，特别短，但是挺带劲的 ','','温馨提示：\n1.配置梯子方法：https://gochatgptcn.com/252.html\n\n2.谷歌网盘限制问题：\n当你访问谷歌网盘链接时，遇到如下情况\n抱歉，您目前无法查看或下载此文件。\n最近查看或下载此文件的用户过多。请稍后再尝试访问此文件。如果您尝试访问的文件特别大或已与很多人共享，那么您最长可能需要等待 24 小时才能查看或下载该文件。如果您在 24 小时仍然无法访问文件，请与您的网域管理员联系。\n这是因为谷歌网盘限制了未登录用户，你登录账号再下载就可以了，没有谷歌账号的话，可以注册一个。\n\n3.解压码教程：\n全站采用境外大厂网盘，杜绝了链接失效的问题，只需要会简单的解压方法即可获取资源。\n苹果手机解压教程请看这里：https://jingyan.baidu.com/article/acf728fd8bc141f8e510a391.html\n安卓手机请看这里：\n先去应用商店安装一个解压专家。\n可以看这个教程：https://jingyan.baidu.com/article/f0e83a2543850b63e5910197.html',1,17,0,0,0,96330,0,963307,0,0,0,987,0,0,1,'2025-02-12 21:07:29','2025-02-12 21:21:07','2025-02-12 21:07:29',16055,'mega盘(大陆需梯子访问)|https://mega.nz/file/EdpTzZqa#AL3VMtAlKLYvpmq9wD_jmmoMH88V8442dtmSFDRFw5s\n谷歌云盘(大陆需梯子访问)|https://drive.google.com/file/d/16J_YZeUXaEI8LD3vLzDkLI3d701tekNC/view?usp=drive_link\n解压码|idoufu.com',0),(2,'84','[PC/英文][BL向]Chimeric Violet[1G/网盘下载]',1,'','/upload_01/upload1/20250208/2025020815491710777.png','',' ','','温馨提示：\n1.配置梯子方法：https://gochatgptcn.com/252.html\n\n2.谷歌网盘限制问题：\n当你访问谷歌网盘链接时，遇到如下情况\n抱歉，您目前无法查看或下载此文件。\n最近查看或下载此文件的用户过多。请稍后再尝试访问此文件。如果您尝试访问的文件特别大或已与很多人共享，那么您最长可能需要等待 24 小时才能查看或下载该文件。如果您在 24 小时仍然无法访问文件，请与您的网域管理员联系。\n这是因为谷歌网盘限制了未登录用户，你登录账号再下载就可以了，没有谷歌账号的话，可以注册一个。\n\n3.解压码教程：\n全站采用境外大厂网盘，杜绝了链接失效的问题，只需要会简单的解压方法即可获取资源。\n苹果手机解压教程请看这里：https://jingyan.baidu.com/article/acf728fd8bc141f8e510a391.html\n安卓手机请看这里：\n先去应用商店安装一个解压专家。\n可以看这个教程：https://jingyan.baidu.com/article/f0e83a2543850b63e5910197.html',1,15,0,0,0,107293,0,643759,0,0,0,816,0,0,1,'2025-02-12 21:07:29','2025-02-12 21:21:07','2025-02-12 21:07:29',11921,'mega盘(大陆需梯子访问)|https://mega.nz/file/5IZ10AQQ#VG1hjnJXxwvwdzBv0sxxmTHNc3YqT_BLcIpAT_7IblA\n谷歌云盘(大陆需梯子访问)|https://drive.google.com/file/d/1-umdTOQg_jkBzE5Uac8LxGdbXhyqXt5P/view?usp=drive_link\n解压码|idoufu.com',0),(3,'83','[汉化/安卓]full service2.0完整版[1.89G/网盘下载]',1,'','/upload_01/upload1/20250208/2025020815491433265.png','',' ​fullservice是一款采用按摩为题材进行打造的BL向的文字恋爱手游，拥有丰富的剧情设定，各具魅力的角色人物，各种不同的故事线路，之前的试玩版广受好评，玩家可以在这里经历完整的剧情，凭借自己的努力去解锁全部的CG和结局。 温馨提示：游戏整体为完整版，体量大小为1.89G，相比之前的试玩版新增加了多条线路剧情和角色，内部已经完全汉化，玩家可以放心的下载游玩。 你要扮演的是一名年轻的男性银行员工Tomoki Nakamoto。由于性格原因，你并不能很好地与周围的人们相处，直到你发现了一家激情满满的会所——Full Service Spa，并在此开启了一段快乐的生活，这款游戏的主角几乎全部都是性格特点各不相同的男性技师哦~ ','','温馨提示：\n1.配置梯子方法：https://gochatgptcn.com/252.html\n\n2.谷歌网盘限制问题：\n当你访问谷歌网盘链接时，遇到如下情况\n抱歉，您目前无法查看或下载此文件。\n最近查看或下载此文件的用户过多。请稍后再尝试访问此文件。如果您尝试访问的文件特别大或已与很多人共享，那么您最长可能需要等待 24 小时才能查看或下载该文件。如果您在 24 小时仍然无法访问文件，请与您的网域管理员联系。\n这是因为谷歌网盘限制了未登录用户，你登录账号再下载就可以了，没有谷歌账号的话，可以注册一个。\n\n3.解压码教程：\n全站采用境外大厂网盘，杜绝了链接失效的问题，只需要会简单的解压方法即可获取资源。\n苹果手机解压教程请看这里：https://jingyan.baidu.com/article/acf728fd8bc141f8e510a391.html\n安卓手机请看这里：\n先去应用商店安装一个解压专家。\n可以看这个教程：https://jingyan.baidu.com/article/f0e83a2543850b63e5910197.html',1,18,0,0,0,84537,0,591759,0,0,0,652,0,0,1,'2025-02-12 21:07:29','2025-02-12 21:21:07','2025-02-12 21:07:29',12076,'mega盘(大陆需梯子访问)|https://mega.nz/file/dVx3jD7B#2xKTaevPxxcXpa7J2crKScZ54XP9T4LfhkvwtLcNEjo\n谷歌云盘(大陆需梯子访问)|https://drive.google.com/file/d/1yL29UW-SN6difTMtOEQuNVZbp80oXG9D/view?usp=drive_link\n解压码|idoufu.com ',0),(4,'82','[PC/英文]Lust_for_Adventure_v8.5[2.38G/网盘下载]',1,'','/upload_01/upload1/20250208/2025020815491121755.png','',' 最新版《欲望之旅》啥种族都有，啥性别也有，几乎都可以XXOO，地图挺大，场景不少，cg也多 ','','温馨提示：\n1.配置梯子方法：https://gochatgptcn.com/252.html\n\n2.谷歌网盘限制问题：\n当你访问谷歌网盘链接时，遇到如下情况\n抱歉，您目前无法查看或下载此文件。\n最近查看或下载此文件的用户过多。请稍后再尝试访问此文件。如果您尝试访问的文件特别大或已与很多人共享，那么您最长可能需要等待 24 小时才能查看或下载该文件。如果您在 24 小时仍然无法访问文件，请与您的网域管理员联系。\n这是因为谷歌网盘限制了未登录用户，你登录账号再下载就可以了，没有谷歌账号的话，可以注册一个。\n\n3.解压码教程：\n全站采用境外大厂网盘，杜绝了链接失效的问题，只需要会简单的解压方法即可获取资源。\n苹果手机解压教程请看这里：https://jingyan.baidu.com/article/acf728fd8bc141f8e510a391.html\n安卓手机请看这里：\n先去应用商店安装一个解压专家。\n可以看这个教程：https://jingyan.baidu.com/article/f0e83a2543850b63e5910197.html',1,16,0,0,0,54687,0,382815,0,0,0,690,0,0,1,'2025-02-12 21:07:29','2025-02-12 21:21:07','2025-02-12 21:07:29',7812,'mega盘(大陆需梯子访问)|https://mega.nz/file/AcRjyTiT#OOyUi368u5ZjEw36j6IUc1ixdZ3F4430JIpCoIWGzcM\n谷歌云盘(大陆需梯子访问)|https://drive.google.com/file/d/1nZT19y3ov11_RahSMw3LIMTqsm5_GQ9v/view?usp=drive_link\n解压码|idoufu.com',0),(5,'81','[安卓/英文]死亡约会_DeadDating完整版[663M/网盘下载]',1,'','/upload_01/upload1/20250208/2025020815491062482.png','',' 注意：在dlc那里输入【LP4%JG97baFCR3%dj%Fx】不然没有h内容   ','','温馨提示：\n1.配置梯子方法：https://gochatgptcn.com/252.html\n\n2.谷歌网盘限制问题：\n当你访问谷歌网盘链接时，遇到如下情况\n抱歉，您目前无法查看或下载此文件。\n最近查看或下载此文件的用户过多。请稍后再尝试访问此文件。如果您尝试访问的文件特别大或已与很多人共享，那么您最长可能需要等待 24 小时才能查看或下载该文件。如果您在 24 小时仍然无法访问文件，请与您的网域管理员联系。\n这是因为谷歌网盘限制了未登录用户，你登录账号再下载就可以了，没有谷歌账号的话，可以注册一个。\n\n3.解压码教程：\n全站采用境外大厂网盘，杜绝了链接失效的问题，只需要会简单的解压方法即可获取资源。\n苹果手机解压教程请看这里：https://jingyan.baidu.com/article/acf728fd8bc141f8e510a391.html\n安卓手机请看这里：\n先去应用商店安装一个解压专家。\n可以看这个教程：https://jingyan.baidu.com/article/f0e83a2543850b63e5910197.html',1,18,0,0,0,66825,0,601429,0,0,0,784,0,0,1,'2025-02-12 21:07:29','2025-02-12 21:21:07','2025-02-12 21:07:29',8353,'mega盘(大陆需梯子访问)|https://mega.nz/file/JVQHEQza#QYlSdHV_JkV7gyugClZhjqwag57Wd1_QIu_7NrmwEAQ\n谷歌云盘(大陆需梯子访问)|https://drive.google.com/file/d/19Qxvgc4rtFShXvuQEybw00RoAnFwxkxx/view?usp=drive_link\n解压码|idoufu.com',0),(6,'80','[PC/中文]SHOTAxMONSTERS 1.41[641M/网盘下载]',1,'','/upload_01/upload1/20250208/2025020815490618552.png','',' 这款是全正tai RPG游戏，包括主角和怪物都是，战斗途中会有爆衣效果，每一层的大BOSS打输的话，还有嘿嘿嘿的CG 当然这还能给主角换装，各式各样的cosplay\n重要的是二周目，可以选择一个攻略对象，到一定程度后就会有嘿嘿嘿的CG看，其他还有一些好感度达成后的，大家可以自己挖掘一下 ','','温馨提示：\n1.配置梯子方法：https://gochatgptcn.com/252.html\n\n2.谷歌网盘限制问题：\n当你访问谷歌网盘链接时，遇到如下情况\n抱歉，您目前无法查看或下载此文件。\n最近查看或下载此文件的用户过多。请稍后再尝试访问此文件。如果您尝试访问的文件特别大或已与很多人共享，那么您最长可能需要等待 24 小时才能查看或下载该文件。如果您在 24 小时仍然无法访问文件，请与您的网域管理员联系。\n这是因为谷歌网盘限制了未登录用户，你登录账号再下载就可以了，没有谷歌账号的话，可以注册一个。\n\n3.解压码教程：\n全站采用境外大厂网盘，杜绝了链接失效的问题，只需要会简单的解压方法即可获取资源。\n苹果手机解压教程请看这里：https://jingyan.baidu.com/article/acf728fd8bc141f8e510a391.html\n安卓手机请看这里：\n先去应用商店安装一个解压专家。\n可以看这个教程：https://jingyan.baidu.com/article/f0e83a2543850b63e5910197.html',1,16,0,0,0,120376,0,722261,0,0,0,878,0,0,1,'2025-02-12 21:07:29','2025-02-12 21:12:12','2025-02-12 21:07:29',15047,'mega盘(大陆需梯子访问)|https://mega.nz/file/5UxTlaTY#bYyMCa2iMzDek1XU5wRF9vy2J6pkHTI3z6xaoH1VkQ4\n谷歌云盘(大陆需梯子访问)|https://drive.google.com/file/d/1NwtnH0EWrqLWDaTjIqa2Wr7wTKzr37XV/view?usp=drive_link\n解压码|idoufu.com',0),(7,'79','[PC/汉化]氾滥原I+氾滥原2014 Ⅱ[2.1G/网盘下载]',1,'','/upload_01/upload1/20250208/2025020815490474443.png','',' ','','温馨提示：\n1.配置梯子方法：https://gochatgptcn.com/252.html\n\n2.谷歌网盘限制问题：\n当你访问谷歌网盘链接时，遇到如下情况\n抱歉，您目前无法查看或下载此文件。\n最近查看或下载此文件的用户过多。请稍后再尝试访问此文件。如果您尝试访问的文件特别大或已与很多人共享，那么您最长可能需要等待 24 小时才能查看或下载该文件。如果您在 24 小时仍然无法访问文件，请与您的网域管理员联系。\n这是因为谷歌网盘限制了未登录用户，你登录账号再下载就可以了，没有谷歌账号的话，可以注册一个。\n\n3.解压码教程：\n全站采用境外大厂网盘，杜绝了链接失效的问题，只需要会简单的解压方法即可获取资源。\n苹果手机解压教程请看这里：https://jingyan.baidu.com/article/acf728fd8bc141f8e510a391.html\n安卓手机请看这里：\n先去应用商店安装一个解压专家。\n可以看这个教程：https://jingyan.baidu.com/article/f0e83a2543850b63e5910197.html',1,10,0,0,0,104172,0,625036,0,0,0,627,0,0,1,'2025-02-12 21:07:29','2025-02-12 21:12:12','2025-02-12 21:07:29',17362,'mega盘(大陆需梯子访问)|https://mega.nz/file/0dIR2Iab#UW8RniuCmJXEdtkXmIHDstU3hcC6vdUf92O9_ssdTQQ\n谷歌云盘(大陆需梯子访问)|https://drive.google.com/file/d/1lPaugjxSUpvxtei-lprd5PqAKamXRMPh/view?usp=drive_link\n解压码|idoufu.com',0),(8,'78','[安卓/汉化]黑猴子-营地(CampBuddy)[1.2G/网盘下载]',1,'','/upload_01/upload1/20250208/2025020815490185261.png','',' 黑猴子营地也叫做​CampBuddy，是一款黑猴子制作出品的经典bl向作品，拥有美式的夏令营童子军故事，中文汉化的剧情版本，配上独特的英文配音。 玩家可以按照自己的想法进行剧情选择，通过选择多条线路等你前来体验，全英配音可能一时间不习惯，但接受之后就还好，相比于玩过的sleep over，full service，cb是感情充分带入感很强的一款游戏，而且我觉得它比棒球在抒情方面都要强过很多，很主要的是它!有!配!音! 可以找个学英语的借口光明正大地玩儿了，它的配音真的超级专业，完全可以感受到浸在那个氛围和情感中的真实。 注意：玩家首次登录游戏可能会在登录界面卡一下，加载的时间会有点长，大家请多多等待，并且，游戏中有些人名翻译可能有点奇怪，大家参考观看啦~下方还配有每名角色的攻略方式，希望能够帮助大家攻略到心仪的角色！ ','','温馨提示：\n1.配置梯子方法：https://gochatgptcn.com/252.html\n\n2.谷歌网盘限制问题：\n当你访问谷歌网盘链接时，遇到如下情况\n抱歉，您目前无法查看或下载此文件。\n最近查看或下载此文件的用户过多。请稍后再尝试访问此文件。如果您尝试访问的文件特别大或已与很多人共享，那么您最长可能需要等待 24 小时才能查看或下载该文件。如果您在 24 小时仍然无法访问文件，请与您的网域管理员联系。\n这是因为谷歌网盘限制了未登录用户，你登录账号再下载就可以了，没有谷歌账号的话，可以注册一个。\n\n3.解压码教程：\n全站采用境外大厂网盘，杜绝了链接失效的问题，只需要会简单的解压方法即可获取资源。\n苹果手机解压教程请看这里：https://jingyan.baidu.com/article/acf728fd8bc141f8e510a391.html\n安卓手机请看这里：\n先去应用商店安装一个解压专家。\n可以看这个教程：https://jingyan.baidu.com/article/f0e83a2543850b63e5910197.html',1,14,0,0,0,59071,0,531640,0,0,0,646,0,0,1,'2025-02-12 21:07:29','2025-02-12 21:12:12','2025-02-12 21:07:29',5907,'mega盘(大陆需梯子访问)|https://mega.nz/file/9dJVEbKI#2ltQXLAcg0gIPZDe9ZITRvQH9Ebx1oKbyreiv0QVT9s\n谷歌云盘(大陆需梯子访问)|https://drive.google.com/file/d/1eqZ4b-VmcsOdupkJhyBy9nI2mVv3EGy1/view?usp=share_link\n解压码|idoufu.com',0),(9,'77','[PC/汉化]爱与欲望之学院~华丽之夜的舞会[480M/网盘下载]',1,'','/upload_01/upload1/20250208/2025020815485937678.png','',' 古早bl游戏，背景设定大致是一个培养未来从事和xing有关的行业的男校，学校里分公关科、宠物科、av科、变熊科（其实叫xp科或者特殊癖好科可能比较恰当一些），校方布置了期末课题要求学生完成，每个科的课题分别是：公关科-将搭档视作顾客直至搭档说出自己满足了为止，宠物科-将搭档视作主人完成其一个愿望，av科-自编自导自行拍摄与搭档主演的av，变熊科-和搭档完成自己最不擅长的玩法，无论哪个科最后都要上交报告给学园长。结束后有一个化妆舞会的活动作为放松。\ncp定死了没法改，一共五对，不过可以自选看哪对的故事。有一个很短的共通线，简单介绍背景和cp，过完以后就可以开始选自己想看的cp了。如果能接受古早画风和有点伤眼的对话文本（字颜色是黄色的有点浅，还是白底的，看久了会不太舒服），问题就不大，从cp定了来看就能知道其实蛮纯爱（？）的，基本都是糖。   注意： 修改注册表以后才能打开游戏，我在压缩包里面放了一个word文档，有图文解释，很简单的。 说实话本来还要安装虚拟光驱、安装本体、安装补丁、安装硬盘版之类的，安装也不是单纯地直接确认就好还要选择性删文件…… 不过我这个弄好了，只要修改注册表就好。因为每个人文件下载后放的位置都不一样，所以注册表必须按照自己的情况改，没办法帮忙弄好。 ','','温馨提示：\n1.配置梯子方法：https://gochatgptcn.com/252.html\n\n2.谷歌网盘限制问题：\n当你访问谷歌网盘链接时，遇到如下情况\n抱歉，您目前无法查看或下载此文件。\n最近查看或下载此文件的用户过多。请稍后再尝试访问此文件。如果您尝试访问的文件特别大或已与很多人共享，那么您最长可能需要等待 24 小时才能查看或下载该文件。如果您在 24 小时仍然无法访问文件，请与您的网域管理员联系。\n这是因为谷歌网盘限制了未登录用户，你登录账号再下载就可以了，没有谷歌账号的话，可以注册一个。\n\n3.解压码教程：\n全站采用境外大厂网盘，杜绝了链接失效的问题，只需要会简单的解压方法即可获取资源。\n苹果手机解压教程请看这里：https://jingyan.baidu.com/article/acf728fd8bc141f8e510a391.html\n安卓手机请看这里：\n先去应用商店安装一个解压专家。\n可以看这个教程：https://jingyan.baidu.com/article/f0e83a2543850b63e5910197.html',1,12,0,0,0,55352,0,387464,0,0,0,699,0,0,1,'2025-02-12 21:07:29','2025-02-12 21:12:12','2025-02-12 21:07:29',6919,'mega盘(大陆需梯子访问)|https://mega.nz/file/0UhC1apZ#c2QNWjtHOiCoEBdoo-zu63pgukP3P9pt5U1s-L2GLvs\n谷歌云盘(大陆需梯子访问)|https://drive.google.com/file/d/1QGLab3yJmxE79owjAx1wPEtGslRUe4O2/view?usp=drive_link\n解压码|idoufu.com',0),(10,'76','[安卓/汉化]wishroom2[33M/网盘下载]',1,'','/upload_01/upload1/20250208/2025020815485794126.png','',' ','','温馨提示：\n1.配置梯子方法：https://gochatgptcn.com/252.html\n\n2.谷歌网盘限制问题：\n当你访问谷歌网盘链接时，遇到如下情况\n抱歉，您目前无法查看或下载此文件。\n最近查看或下载此文件的用户过多。请稍后再尝试访问此文件。如果您尝试访问的文件特别大或已与很多人共享，那么您最长可能需要等待 24 小时才能查看或下载该文件。如果您在 24 小时仍然无法访问文件，请与您的网域管理员联系。\n这是因为谷歌网盘限制了未登录用户，你登录账号再下载就可以了，没有谷歌账号的话，可以注册一个。\n\n3.解压码教程：\n全站采用境外大厂网盘，杜绝了链接失效的问题，只需要会简单的解压方法即可获取资源。\n苹果手机解压教程请看这里：https://jingyan.baidu.com/article/acf728fd8bc141f8e510a391.html\n安卓手机请看这里：\n先去应用商店安装一个解压专家。\n可以看这个教程：https://jingyan.baidu.com/article/f0e83a2543850b63e5910197.html',1,11,0,0,0,64811,0,648118,0,0,0,811,0,0,1,'2025-02-12 21:07:29','2025-02-12 21:12:12','2025-02-12 21:07:29',9258,'mega盘(大陆需梯子访问)|https://mega.nz/file/dYJBlLrB#bEFYynfn8op31u2A6Jd3mN9swCdDyQpv3IBQrpPyjdY\n谷歌云盘(大陆需梯子访问)|https://drive.google.com/file/d/1HTKLQBzjtwTrU3n5vUA-CcsGxnL9T82M/view?usp=drive_link\n解压码|idoufu.com',0),(11,'75','[PC/汉化]DarakueEX0.3p墮落游戲[933M/网盘下载]',1,'','/upload_01/upload1/20250208/2025020815485595373.png','',' 非完全版本，当前版本为0.3 ','','温馨提示：\n1.配置梯子方法：https://gochatgptcn.com/252.html\n\n2.谷歌网盘限制问题：\n当你访问谷歌网盘链接时，遇到如下情况\n抱歉，您目前无法查看或下载此文件。\n最近查看或下载此文件的用户过多。请稍后再尝试访问此文件。如果您尝试访问的文件特别大或已与很多人共享，那么您最长可能需要等待 24 小时才能查看或下载该文件。如果您在 24 小时仍然无法访问文件，请与您的网域管理员联系。\n这是因为谷歌网盘限制了未登录用户，你登录账号再下载就可以了，没有谷歌账号的话，可以注册一个。\n\n3.解压码教程：\n全站采用境外大厂网盘，杜绝了链接失效的问题，只需要会简单的解压方法即可获取资源。\n苹果手机解压教程请看这里：https://jingyan.baidu.com/article/acf728fd8bc141f8e510a391.html\n安卓手机请看这里：\n先去应用商店安装一个解压专家。\n可以看这个教程：https://jingyan.baidu.com/article/f0e83a2543850b63e5910197.html',1,14,0,0,0,80670,0,726031,0,0,0,818,0,0,1,'2025-02-12 21:07:29','2025-02-12 21:12:12','2025-02-12 21:07:29',13445,'mega盘(大陆需梯子访问)|https://mega.nz/file/1EAQwIyZ#7q4bjRXqgtKwyzo3y3XQqzFsBC3FxwHSdZmiEf7BFXw\n谷歌云盘(大陆需梯子访问)|https://drive.google.com/file/d/1WUCfh1JO8aZHXGsNEREZS0qEBO3K_H86/view?usp=drive_link\n解压码|idoufu.com',0),(12,'74','[安卓/汉化]阿尔罗的炼金学院[352M/网盘下载]',1,'','/upload_01/upload1/20250208/2025020815485353901.png','',' ','','温馨提示：\n1.配置梯子方法：https://gochatgptcn.com/252.html\n\n2.谷歌网盘限制问题：\n当你访问谷歌网盘链接时，遇到如下情况\n抱歉，您目前无法查看或下载此文件。\n最近查看或下载此文件的用户过多。请稍后再尝试访问此文件。如果您尝试访问的文件特别大或已与很多人共享，那么您最长可能需要等待 24 小时才能查看或下载该文件。如果您在 24 小时仍然无法访问文件，请与您的网域管理员联系。\n这是因为谷歌网盘限制了未登录用户，你登录账号再下载就可以了，没有谷歌账号的话，可以注册一个。\n\n3.解压码教程：\n全站采用境外大厂网盘，杜绝了链接失效的问题，只需要会简单的解压方法即可获取资源。\n苹果手机解压教程请看这里：https://jingyan.baidu.com/article/acf728fd8bc141f8e510a391.html\n安卓手机请看这里：\n先去应用商店安装一个解压专家。\n可以看这个教程：https://jingyan.baidu.com/article/f0e83a2543850b63e5910197.html',1,11,0,0,0,83604,0,836044,0,0,0,838,0,0,1,'2025-02-12 21:07:29','2025-02-12 21:12:12','2025-02-12 21:07:29',8360,'mega盘(大陆需梯子访问)|https://mega.nz/file/1VgmgATS#6_T3JXurCYDb9PaadSx0XZ9NPOLAIUbIXZ9nZzQsBRo\n谷歌云盘(大陆需梯子访问)|https://drive.google.com/file/d/1UytedbdBzPtQe_N3w9ZQxB_C1Z9ILj10/view?usp=drive_link\n解压码|idoufu.com',0),(13,'73','[PC/英文]BlueLightning[851M/网盘下载]',1,'','/upload_01/upload1/20250208/2025020815484953853.png','',' ','','温馨提示：\n1.配置梯子方法：https://gochatgptcn.com/252.html\n\n2.谷歌网盘限制问题：\n当你访问谷歌网盘链接时，遇到如下情况\n抱歉，您目前无法查看或下载此文件。\n最近查看或下载此文件的用户过多。请稍后再尝试访问此文件。如果您尝试访问的文件特别大或已与很多人共享，那么您最长可能需要等待 24 小时才能查看或下载该文件。如果您在 24 小时仍然无法访问文件，请与您的网域管理员联系。\n这是因为谷歌网盘限制了未登录用户，你登录账号再下载就可以了，没有谷歌账号的话，可以注册一个。\n\n3.解压码教程：\n全站采用境外大厂网盘，杜绝了链接失效的问题，只需要会简单的解压方法即可获取资源。\n苹果手机解压教程请看这里：https://jingyan.baidu.com/article/acf728fd8bc141f8e510a391.html\n安卓手机请看这里：\n先去应用商店安装一个解压专家。\n可以看这个教程：https://jingyan.baidu.com/article/f0e83a2543850b63e5910197.html',1,13,0,0,0,130648,0,914542,0,0,0,761,0,0,1,'2025-02-12 21:07:29','2025-02-12 21:12:12','2025-02-12 21:07:29',18664,'mega盘(大陆需梯子访问)|https://mega.nz/file/IAAxEZBQ#cEbX-7xG4qp6EXpofes6QtP51jMu5xFC4rl-lTs778w\n谷歌云盘(大陆需梯子访问)|https://drive.google.com/file/d/11dB4ZrNRv16keNdp8QmfmySDx_6_TVre/view?usp=drive_link\n解压码|idoufu.com',0),(14,'72','[PC/汉化]sweet pool+攻略+存档[938M/网盘下载]',1,'','/upload_01/upload1/20250208/2025020815484625222.png','',' ','','温馨提示：\n1.配置梯子方法：https://gochatgptcn.com/252.html\n\n2.谷歌网盘限制问题：\n当你访问谷歌网盘链接时，遇到如下情况\n抱歉，您目前无法查看或下载此文件。\n最近查看或下载此文件的用户过多。请稍后再尝试访问此文件。如果您尝试访问的文件特别大或已与很多人共享，那么您最长可能需要等待 24 小时才能查看或下载该文件。如果您在 24 小时仍然无法访问文件，请与您的网域管理员联系。\n这是因为谷歌网盘限制了未登录用户，你登录账号再下载就可以了，没有谷歌账号的话，可以注册一个。\n\n3.解压码教程：\n全站采用境外大厂网盘，杜绝了链接失效的问题，只需要会简单的解压方法即可获取资源。\n苹果手机解压教程请看这里：https://jingyan.baidu.com/article/acf728fd8bc141f8e510a391.html\n安卓手机请看这里：\n先去应用商店安装一个解压专家。\n可以看这个教程：https://jingyan.baidu.com/article/f0e83a2543850b63e5910197.html',1,12,0,0,0,71610,0,501275,0,0,0,686,0,0,1,'2025-02-12 21:07:29','2025-02-12 21:12:12','2025-02-12 21:07:29',10230,'mega盘(大陆需梯子访问)|https://mega.nz/file/VZYGQBra#URyvB-A6fx_I98fgNTKs7m3hQzqBoRfmDqCIgKBkfho\n谷歌云盘(大陆需梯子访问)|https://drive.google.com/file/d/1AkBAaduaNuP76H7gpA-L4pgXU1aLw-N9/view?usp=drive_link\n解压码|idoufu.com',0),(15,'71','[安卓/中文]第101号禁区(商城+金手指可用)[80M/网盘下载]',1,'','/upload_01/upload1/20250208/2025020815484486898.png','','  作品简介\n一觉醒来，不但失去了记忆，还成了手术台上的实验品。\n唯一的提示，是突然出现在枕头下的神秘笔记本。\n原本温柔可靠的哥哥，戴上了陌生的面具。\n想将我置于死地的人，竟然有一张和我一模一样的脸。\n当真相渐渐浮出水面，背后竟然隐藏着一个巨大的计划。\n曾经的我……到底是谁？！ 标签：√调jiao √抖S √禁欲 √鬼玄田 √军装 √强强 √异能 √主角黑化 √高智商腹黑 √废墟 √兄弟 √派系斗争 作品可攻略角色六只 (☆ﾟ∀ﾟ) 【奈尔森】“鲸派”上校，禁欲言周孝文系 【麒止】雇佣兵，保护欲强烈的深情忠犬 【A-02】研究所“最高级实验品”，喜怒无常的病娇蛇精病 【默琛】恩斯欧之家现任首领，温柔+鬼玄田+腹黑+大BOSS+哥哥=？？？ 【A-01】研究所“最高级实验品”，三无冰山暗含隐藏属性 【基诺】“鲨派”少校，霸道又痞气的野心家 ','','温馨提示：\n1.配置梯子方法：https://gochatgptcn.com/252.html\n\n2.谷歌网盘限制问题：\n当你访问谷歌网盘链接时，遇到如下情况\n抱歉，您目前无法查看或下载此文件。\n最近查看或下载此文件的用户过多。请稍后再尝试访问此文件。如果您尝试访问的文件特别大或已与很多人共享，那么您最长可能需要等待 24 小时才能查看或下载该文件。如果您在 24 小时仍然无法访问文件，请与您的网域管理员联系。\n这是因为谷歌网盘限制了未登录用户，你登录账号再下载就可以了，没有谷歌账号的话，可以注册一个。\n\n3.解压码教程：\n全站采用境外大厂网盘，杜绝了链接失效的问题，只需要会简单的解压方法即可获取资源。\n苹果手机解压教程请看这里：https://jingyan.baidu.com/article/acf728fd8bc141f8e510a391.html\n安卓手机请看这里：\n先去应用商店安装一个解压专家。\n可以看这个教程：https://jingyan.baidu.com/article/f0e83a2543850b63e5910197.html',1,19,0,0,0,103823,0,934412,0,0,0,783,0,0,1,'2025-02-12 21:07:29','2025-02-12 21:12:12','2025-02-12 21:07:29',17303,'mega盘(大陆需梯子访问)|https://mega.nz/file/wYIDWaSY#gma4QvPrfiRWKBXUMWE6lOFeULcoQje9lVSuDXQV8aU\n谷歌云盘(大陆需梯子访问)|https://drive.google.com/file/d/1JtGghol9pJTr08qVIQNXt2uhYyYf2BDU/view?usp=drive_link\n解压码|idoufu.com',0),(16,'70','[PC]敗北勇者_聖騎士王子アレク編[214M/网盘下载]',1,'','/upload_01/upload1/20250208/2025020815484224903.png','',' 败北勇者——圣骑士王子alec篇，金发为本篇主角alec，左起分别是：盲眼神官，（背景里的魔王），金发圣骑士Alec，魔眼S手盗贼，大舅哥魔导师 是单人社团做的live2D有声电子小说式BL游戏，就和N+C著名的DMMD啊咎狗之血啊之类的游戏差不多类型。\n但是这个卖点是有live2D， 也就是说H的时候主角会随着呼吸有轻微的动作哦，而不是一张不动的CG。 游玩时间大概是40分钟，主要讲的就是勇者败给魔王以后被言周孝文的故事。 Alec篇是第一个成品，作者之后好像预计还要做勇者小队里其他人的篇章。\n本篇有3场H，40+个live2D motion，主角圣骑士全语音，单一结局（雌堕）。虽然是日文没有汉化，但是剧情非常简单好理解，冲就是了 解压后会有个叫haiboku_yusha_alec的exe文件，直接双击打开就可以开始游玩了。 ','','温馨提示：\n1.配置梯子方法：https://gochatgptcn.com/252.html\n\n2.谷歌网盘限制问题：\n当你访问谷歌网盘链接时，遇到如下情况\n抱歉，您目前无法查看或下载此文件。\n最近查看或下载此文件的用户过多。请稍后再尝试访问此文件。如果您尝试访问的文件特别大或已与很多人共享，那么您最长可能需要等待 24 小时才能查看或下载该文件。如果您在 24 小时仍然无法访问文件，请与您的网域管理员联系。\n这是因为谷歌网盘限制了未登录用户，你登录账号再下载就可以了，没有谷歌账号的话，可以注册一个。\n\n3.解压码教程：\n全站采用境外大厂网盘，杜绝了链接失效的问题，只需要会简单的解压方法即可获取资源。\n苹果手机解压教程请看这里：https://jingyan.baidu.com/article/acf728fd8bc141f8e510a391.html\n安卓手机请看这里：\n先去应用商店安装一个解压专家。\n可以看这个教程：https://jingyan.baidu.com/article/f0e83a2543850b63e5910197.html',1,12,0,0,0,49370,0,444338,0,0,0,842,0,0,1,'2025-02-12 21:07:29','2025-02-12 21:12:12','2025-02-12 21:07:29',5485,'mega盘(大陆需梯子访问)|https://mega.nz/file/9dIX0LyT#SCtNa65AmZL85gN8PD7MUYFJmsJius6SZ4Z8bkkH6qo\n谷歌云盘(大陆需梯子访问)|https://drive.google.com/file/d/14kD7XKaZP0Lr5eA3I9_48QMl_mVu1MXe/view?usp=drive_link\n解压码|idoufu.com',0),(17,'69','[安卓/合集]wishroom14部[589M/网盘下载]',1,'','/upload_01/upload1/20250208/2025020815484092455.png','',' ','','温馨提示：\n1.配置梯子方法：https://gochatgptcn.com/252.html\n\n2.谷歌网盘限制问题：\n当你访问谷歌网盘链接时，遇到如下情况\n抱歉，您目前无法查看或下载此文件。\n最近查看或下载此文件的用户过多。请稍后再尝试访问此文件。如果您尝试访问的文件特别大或已与很多人共享，那么您最长可能需要等待 24 小时才能查看或下载该文件。如果您在 24 小时仍然无法访问文件，请与您的网域管理员联系。\n这是因为谷歌网盘限制了未登录用户，你登录账号再下载就可以了，没有谷歌账号的话，可以注册一个。\n\n3.解压码教程：\n全站采用境外大厂网盘，杜绝了链接失效的问题，只需要会简单的解压方法即可获取资源。\n苹果手机解压教程请看这里：https://jingyan.baidu.com/article/acf728fd8bc141f8e510a391.html\n安卓手机请看这里：\n先去应用商店安装一个解压专家。\n可以看这个教程：https://jingyan.baidu.com/article/f0e83a2543850b63e5910197.html',1,12,0,0,0,122927,0,737566,0,0,0,742,0,0,1,'2025-02-12 21:07:29','2025-02-12 21:12:12','2025-02-12 21:07:29',12292,'mega盘(大陆需梯子访问)|https://mega.nz/file/pBox2bwZ#bjE9Iayukcaatk3B88LCfHZLVAjBvNmyXveMtOyq8_s\n谷歌云盘(大陆需梯子访问)|https://drive.google.com/file/d/16fEn3Vyc9O1trNGmmCpf9uSQIC5FsFq4/view?usp=drive_link\n解压码|idoufu.com',0),(18,'68','[PC/中文]夏有天狼_七星篇vol.7完整版[521M/网盘下载]',1,'','/upload_01/upload1/20250208/2025020815483748478.png','',' ','','温馨提示：\n1.配置梯子方法：https://gochatgptcn.com/252.html\n\n2.谷歌网盘限制问题：\n当你访问谷歌网盘链接时，遇到如下情况\n抱歉，您目前无法查看或下载此文件。\n最近查看或下载此文件的用户过多。请稍后再尝试访问此文件。如果您尝试访问的文件特别大或已与很多人共享，那么您最长可能需要等待 24 小时才能查看或下载该文件。如果您在 24 小时仍然无法访问文件，请与您的网域管理员联系。\n这是因为谷歌网盘限制了未登录用户，你登录账号再下载就可以了，没有谷歌账号的话，可以注册一个。\n\n3.解压码教程：\n全站采用境外大厂网盘，杜绝了链接失效的问题，只需要会简单的解压方法即可获取资源。\n苹果手机解压教程请看这里：https://jingyan.baidu.com/article/acf728fd8bc141f8e510a391.html\n安卓手机请看这里：\n先去应用商店安装一个解压专家。\n可以看这个教程：https://jingyan.baidu.com/article/f0e83a2543850b63e5910197.html',1,18,0,0,0,85705,0,685646,0,0,0,542,0,0,1,'2025-02-12 21:07:29','2025-02-12 21:12:12','2025-02-12 21:07:29',12243,'mega盘(大陆需梯子访问)|https://mega.nz/file/hIIjABhJ#yaTbSzY1PipC5qyAbnnbRTwNCZs_3JRs30gKk-djeeY\n谷歌云盘(大陆需梯子访问)|https://drive.google.com/file/d/1hXTMBqrA-swVolHwwIJhs3pkGDPQ-WIv/view?usp=drive_link\n解压码|idoufu.com',0),(19,'67','[PC/中文]茶室 tearoom[166M/网盘下载]',1,'','/upload_01/upload1/20250208/2025020815483514355.png','',' 在撤所和路过的陌生人… ','','温馨提示：\n1.配置梯子方法：https://gochatgptcn.com/252.html\n\n2.谷歌网盘限制问题：\n当你访问谷歌网盘链接时，遇到如下情况\n抱歉，您目前无法查看或下载此文件。\n最近查看或下载此文件的用户过多。请稍后再尝试访问此文件。如果您尝试访问的文件特别大或已与很多人共享，那么您最长可能需要等待 24 小时才能查看或下载该文件。如果您在 24 小时仍然无法访问文件，请与您的网域管理员联系。\n这是因为谷歌网盘限制了未登录用户，你登录账号再下载就可以了，没有谷歌账号的话，可以注册一个。\n\n3.解压码教程：\n全站采用境外大厂网盘，杜绝了链接失效的问题，只需要会简单的解压方法即可获取资源。\n苹果手机解压教程请看这里：https://jingyan.baidu.com/article/acf728fd8bc141f8e510a391.html\n安卓手机请看这里：\n先去应用商店安装一个解压专家。\n可以看这个教程：https://jingyan.baidu.com/article/f0e83a2543850b63e5910197.html',1,17,0,0,0,152349,0,914094,0,0,0,654,0,0,1,'2025-02-12 21:07:29','2025-02-12 21:12:12','2025-02-12 21:07:29',15234,'mega盘(大陆需梯子访问)|https://mega.nz/file/NA5jUDIZ#aqv1glMGgtSCHhe7YfITBk0v1avH98s5TUy8WaIfxlE\n谷歌云盘(大陆需梯子访问)|https://drive.google.com/file/d/1TIxsCqqmLlZlwdDNYNuZcfXLVgbMcA8j/view?usp=drive_link\n解压码|idoufu.com',0),(20,'66','[PC/英文]瀧勇太郎の生業[273M/网盘下载]',1,'','/upload_01/upload1/20250208/2025020815483492806.png','',' 8°先生的作品风格都是筋 肉 猛 男，器 大 活 好   ','','温馨提示：\n1.配置梯子方法：https://gochatgptcn.com/252.html\n\n2.谷歌网盘限制问题：\n当你访问谷歌网盘链接时，遇到如下情况\n抱歉，您目前无法查看或下载此文件。\n最近查看或下载此文件的用户过多。请稍后再尝试访问此文件。如果您尝试访问的文件特别大或已与很多人共享，那么您最长可能需要等待 24 小时才能查看或下载该文件。如果您在 24 小时仍然无法访问文件，请与您的网域管理员联系。\n这是因为谷歌网盘限制了未登录用户，你登录账号再下载就可以了，没有谷歌账号的话，可以注册一个。\n\n3.解压码教程：\n全站采用境外大厂网盘，杜绝了链接失效的问题，只需要会简单的解压方法即可获取资源。\n苹果手机解压教程请看这里：https://jingyan.baidu.com/article/acf728fd8bc141f8e510a391.html\n安卓手机请看这里：\n先去应用商店安装一个解压专家。\n可以看这个教程：https://jingyan.baidu.com/article/f0e83a2543850b63e5910197.html',1,10,0,0,0,56631,0,509684,0,0,0,765,0,0,1,'2025-02-12 21:07:29','2025-02-12 21:12:12','2025-02-12 21:07:29',8090,'mega盘(大陆需梯子访问)|https://mega.nz/file/VNAVGCaJ#V7xN5UFtL0-mLSQGmaUWdcIqOYkU_L-IHKp5YgprawI\n谷歌云盘(大陆需梯子访问)|https://drive.google.com/file/d/1eL0dr6-g9RUPEorFQktEGJG-Lvak4TN_/view?usp=drive_link\n解压码|idoufu.com',0),(21,'65','[安卓/PC/汉化]LustfulDesires-0.40.1[516M/网盘下载]',1,'','/upload_01/upload1/20250208/2025020815483253324.png','',' 这是一款对话冒险游戏，通过完成任务来推动各个NPC对你的好感度，从而完成攻略。\n在这里你不仅对主要人物进行攻略，对战怪物也看进行魅惑。 请选择对应需要版本下载：     到TG群中下载：传送门（需梯子访问） 不会配置梯子的话请查看这里：https://gochatgptcn.com/252.html 解压码：idoufu.com ','','温馨提示：\n1.配置梯子方法：https://gochatgptcn.com/252.html\n\n2.谷歌网盘限制问题：\n当你访问谷歌网盘链接时，遇到如下情况\n抱歉，您目前无法查看或下载此文件。\n最近查看或下载此文件的用户过多。请稍后再尝试访问此文件。如果您尝试访问的文件特别大或已与很多人共享，那么您最长可能需要等待 24 小时才能查看或下载该文件。如果您在 24 小时仍然无法访问文件，请与您的网域管理员联系。\n这是因为谷歌网盘限制了未登录用户，你登录账号再下载就可以了，没有谷歌账号的话，可以注册一个。\n\n3.解压码教程：\n全站采用境外大厂网盘，杜绝了链接失效的问题，只需要会简单的解压方法即可获取资源。\n苹果手机解压教程请看这里：https://jingyan.baidu.com/article/acf728fd8bc141f8e510a391.html\n安卓手机请看这里：\n先去应用商店安装一个解压专家。\n可以看这个教程：https://jingyan.baidu.com/article/f0e83a2543850b63e5910197.html',1,11,0,0,0,81985,0,573895,0,0,0,557,0,0,1,'2025-02-12 21:07:29','2025-02-12 21:12:12','2025-02-12 21:07:29',13664,'mega盘(大陆需梯子访问)|https://mega.nz/file/EEQkjbrT#PvT9Nj1giH7Tmc08Px42YUidxsHdlaeiFvgYrFWGzqY\n谷歌云盘(大陆需梯子访问)|https://drive.google.com/file/d/1ge70OABEI5FE-JoyF2esiDXfqQh7QED6/view?usp=drive_link\n解压码|idoufu.com',0),(22,'64','[PC/汉化/文字冒险]eraQueenA+0.98魔改版[3.3M/网盘下载]',1,'','/upload_01/upload1/20250208/2025020815483188420.png','',' ','','温馨提示：\n1.配置梯子方法：https://gochatgptcn.com/252.html\n\n2.谷歌网盘限制问题：\n当你访问谷歌网盘链接时，遇到如下情况\n抱歉，您目前无法查看或下载此文件。\n最近查看或下载此文件的用户过多。请稍后再尝试访问此文件。如果您尝试访问的文件特别大或已与很多人共享，那么您最长可能需要等待 24 小时才能查看或下载该文件。如果您在 24 小时仍然无法访问文件，请与您的网域管理员联系。\n这是因为谷歌网盘限制了未登录用户，你登录账号再下载就可以了，没有谷歌账号的话，可以注册一个。\n\n3.解压码教程：\n全站采用境外大厂网盘，杜绝了链接失效的问题，只需要会简单的解压方法即可获取资源。\n苹果手机解压教程请看这里：https://jingyan.baidu.com/article/acf728fd8bc141f8e510a391.html\n安卓手机请看这里：\n先去应用商店安装一个解压专家。\n可以看这个教程：https://jingyan.baidu.com/article/f0e83a2543850b63e5910197.html',1,20,0,0,0,45627,0,365021,0,0,0,559,0,0,1,'2025-02-12 21:07:29','2025-02-12 21:12:12','2025-02-12 21:07:29',7604,'mega盘(大陆需梯子访问)|https://mega.nz/file/YBwAhRwb#vj-y9oOtjFkkDgX9HLrHEH0yvmI6DK3L1AKKZ-01Yak\n谷歌云盘(大陆需梯子访问)|https://drive.google.com/file/d/1b7ROBbo-T_m45nyIszsSeRLWM4k--yTV/view?usp=drive_link\n解压码|idoufu.com',0),(23,'63','[安卓/汉化]阿尔罗训练师[148M/网盘下载]',1,'','/upload_01/upload1/20250208/2025020815482957294.png','',' 这个游戏讲的是你要通过★★行为获得* 液卖钱生存下去。这个小游戏也比较耐玩。 ','','温馨提示：\n1.配置梯子方法：https://gochatgptcn.com/252.html\n\n2.谷歌网盘限制问题：\n当你访问谷歌网盘链接时，遇到如下情况\n抱歉，您目前无法查看或下载此文件。\n最近查看或下载此文件的用户过多。请稍后再尝试访问此文件。如果您尝试访问的文件特别大或已与很多人共享，那么您最长可能需要等待 24 小时才能查看或下载该文件。如果您在 24 小时仍然无法访问文件，请与您的网域管理员联系。\n这是因为谷歌网盘限制了未登录用户，你登录账号再下载就可以了，没有谷歌账号的话，可以注册一个。\n\n3.解压码教程：\n全站采用境外大厂网盘，杜绝了链接失效的问题，只需要会简单的解压方法即可获取资源。\n苹果手机解压教程请看这里：https://jingyan.baidu.com/article/acf728fd8bc141f8e510a391.html\n安卓手机请看这里：\n先去应用商店安装一个解压专家。\n可以看这个教程：https://jingyan.baidu.com/article/f0e83a2543850b63e5910197.html',1,18,0,0,0,74930,0,449584,0,0,0,997,0,0,1,'2025-02-12 21:07:29','2025-02-12 21:12:12','2025-02-12 21:07:29',8325,'mega盘(大陆需梯子访问)|https://mega.nz/file/xJQlSI4Z#iwBB8d44-rjYRZl9LeZLanwR4fAEOJWDbRwEwYMkbFg\n谷歌云盘(大陆需梯子访问)|https://drive.google.com/file/d/1ntpAlCm283cGrtHW2xuqNJ4s1F6AgQbZ/view?usp=drive_link\n解压码|idoufu.com',0),(24,'62','[PC/汉化]邻居大叔[文字冒险][496M/网盘下载]',1,'','/upload_01/upload1/20250208/2025020815482780094.png','',' 这次是馋人的大叔惹，谁不喜欢又帅又壮的大叔呢 ','','温馨提示：\n1.配置梯子方法：https://gochatgptcn.com/252.html\n\n2.谷歌网盘限制问题：\n当你访问谷歌网盘链接时，遇到如下情况\n抱歉，您目前无法查看或下载此文件。\n最近查看或下载此文件的用户过多。请稍后再尝试访问此文件。如果您尝试访问的文件特别大或已与很多人共享，那么您最长可能需要等待 24 小时才能查看或下载该文件。如果您在 24 小时仍然无法访问文件，请与您的网域管理员联系。\n这是因为谷歌网盘限制了未登录用户，你登录账号再下载就可以了，没有谷歌账号的话，可以注册一个。\n\n3.解压码教程：\n全站采用境外大厂网盘，杜绝了链接失效的问题，只需要会简单的解压方法即可获取资源。\n苹果手机解压教程请看这里：https://jingyan.baidu.com/article/acf728fd8bc141f8e510a391.html\n安卓手机请看这里：\n先去应用商店安装一个解压专家。\n可以看这个教程：https://jingyan.baidu.com/article/f0e83a2543850b63e5910197.html',1,11,0,0,0,102696,0,924267,0,0,0,564,0,0,1,'2025-02-12 21:07:29','2025-02-12 21:12:12','2025-02-12 21:07:29',11410,'mega盘(大陆需梯子访问)|https://mega.nz/file/VFQxWSCA#kPq2GeL-Pi5dD_doVObCseiSJUvV3JNTNUhlFDuxYtY\n谷歌云盘(大陆需梯子访问)|https://drive.google.com/file/d/1_7_2ZUf3BsAbAqG1UEIVGHZ87i-50t7L/view?usp=drive_link\n解压码|idoufu.com',0),(25,'61','[PC/机翻]MySoldiers-1.01[1.14G/网盘下载]',1,'','/upload_01/upload1/20250208/2025020815482496571.png','',' 中文切换教程\nPC进入游戏后找到第三个preferences选项➡language➡切换为“Chinese”即可 ','','温馨提示：\n1.配置梯子方法：https://gochatgptcn.com/252.html\n\n2.谷歌网盘限制问题：\n当你访问谷歌网盘链接时，遇到如下情况\n抱歉，您目前无法查看或下载此文件。\n最近查看或下载此文件的用户过多。请稍后再尝试访问此文件。如果您尝试访问的文件特别大或已与很多人共享，那么您最长可能需要等待 24 小时才能查看或下载该文件。如果您在 24 小时仍然无法访问文件，请与您的网域管理员联系。\n这是因为谷歌网盘限制了未登录用户，你登录账号再下载就可以了，没有谷歌账号的话，可以注册一个。\n\n3.解压码教程：\n全站采用境外大厂网盘，杜绝了链接失效的问题，只需要会简单的解压方法即可获取资源。\n苹果手机解压教程请看这里：https://jingyan.baidu.com/article/acf728fd8bc141f8e510a391.html\n安卓手机请看这里：\n先去应用商店安装一个解压专家。\n可以看这个教程：https://jingyan.baidu.com/article/f0e83a2543850b63e5910197.html',1,15,0,0,0,80419,0,562937,0,0,0,661,0,0,1,'2025-02-12 21:07:29','2025-02-12 21:12:12','2025-02-12 21:07:29',8041,'mega盘(大陆需梯子访问)|https://mega.nz/file/0JpnUaqK#wNuZk57bfpSNdKGSrm_1Kt4ywoqFf5PU7nFC2_FIGvw\n谷歌云盘(大陆需梯子访问)|https://drive.google.com/file/d/1b8CcTSGeuteyuYLhYrz4PUKW7SUBwI1y/view?usp=drive_link\n解压码|idoufu.com',0),(26,'60','[PC/汉化]Black Hoops/黑环 v2.9.8.4[710M/网盘下载]',1,'','/upload_01/upload1/20250208/2025020815482220531.png','',' ','','温馨提示：\n1.配置梯子方法：https://gochatgptcn.com/252.html\n\n2.谷歌网盘限制问题：\n当你访问谷歌网盘链接时，遇到如下情况\n抱歉，您目前无法查看或下载此文件。\n最近查看或下载此文件的用户过多。请稍后再尝试访问此文件。如果您尝试访问的文件特别大或已与很多人共享，那么您最长可能需要等待 24 小时才能查看或下载该文件。如果您在 24 小时仍然无法访问文件，请与您的网域管理员联系。\n这是因为谷歌网盘限制了未登录用户，你登录账号再下载就可以了，没有谷歌账号的话，可以注册一个。\n\n3.解压码教程：\n全站采用境外大厂网盘，杜绝了链接失效的问题，只需要会简单的解压方法即可获取资源。\n苹果手机解压教程请看这里：https://jingyan.baidu.com/article/acf728fd8bc141f8e510a391.html\n安卓手机请看这里：\n先去应用商店安装一个解压专家。\n可以看这个教程：https://jingyan.baidu.com/article/f0e83a2543850b63e5910197.html',1,17,0,0,0,58697,0,469583,0,0,0,666,0,0,1,'2025-02-12 21:07:29','2025-02-12 21:11:55','2025-02-12 21:07:29',6521,'mega盘(大陆需梯子访问)|https://mega.nz/file/gEIWSZzb#vj2TmRz7Uk1mtJOu0GF2U2wQUByk8zov3P1WIqxXP7s\n谷歌云盘(大陆需梯子访问)|https://drive.google.com/file/d/1w-uZzJOElUd4Mz8ogcIyQlQ-3PHP25KS/view?usp=drive_link\n解压码|idoufu.com',0),(27,'59','[安卓]贝奥武夫[38M/网盘下载]',1,'','/upload_01/upload1/20250208/2025020815482110792.png','',' 触屏式游戏 ','','温馨提示：\n1.配置梯子方法：https://gochatgptcn.com/252.html\n\n2.谷歌网盘限制问题：\n当你访问谷歌网盘链接时，遇到如下情况\n抱歉，您目前无法查看或下载此文件。\n最近查看或下载此文件的用户过多。请稍后再尝试访问此文件。如果您尝试访问的文件特别大或已与很多人共享，那么您最长可能需要等待 24 小时才能查看或下载该文件。如果您在 24 小时仍然无法访问文件，请与您的网域管理员联系。\n这是因为谷歌网盘限制了未登录用户，你登录账号再下载就可以了，没有谷歌账号的话，可以注册一个。\n\n3.解压码教程：\n全站采用境外大厂网盘，杜绝了链接失效的问题，只需要会简单的解压方法即可获取资源。\n苹果手机解压教程请看这里：https://jingyan.baidu.com/article/acf728fd8bc141f8e510a391.html\n安卓手机请看这里：\n先去应用商店安装一个解压专家。\n可以看这个教程：https://jingyan.baidu.com/article/f0e83a2543850b63e5910197.html',1,19,0,0,0,43108,0,344864,0,0,0,591,0,0,1,'2025-02-12 21:07:29','2025-02-12 21:11:55','2025-02-12 21:07:29',6158,'mega盘(大陆需梯子访问)|https://mega.nz/file/xRJ0GLTS#ZFKA0bKey5i5nb799H-WLYspbABau4g8sA81erSA78E\n谷歌云盘(大陆需梯子访问)|https://drive.google.com/file/d/1Azkn2r56CbeyBVtSW84cIxmwfhw8I7J6/view?usp=drive_link\n解压码|idoufu.com',0),(28,'58','[安卓]罗宾的晨曦冒险[1G/网盘下载]',1,'','/upload_01/upload1/20250208/2025020815481929822.png','',' 游戏名称：robin morningwood adventure ','','温馨提示：\n1.配置梯子方法：https://gochatgptcn.com/252.html\n\n2.谷歌网盘限制问题：\n当你访问谷歌网盘链接时，遇到如下情况\n抱歉，您目前无法查看或下载此文件。\n最近查看或下载此文件的用户过多。请稍后再尝试访问此文件。如果您尝试访问的文件特别大或已与很多人共享，那么您最长可能需要等待 24 小时才能查看或下载该文件。如果您在 24 小时仍然无法访问文件，请与您的网域管理员联系。\n这是因为谷歌网盘限制了未登录用户，你登录账号再下载就可以了，没有谷歌账号的话，可以注册一个。\n\n3.解压码教程：\n全站采用境外大厂网盘，杜绝了链接失效的问题，只需要会简单的解压方法即可获取资源。\n苹果手机解压教程请看这里：https://jingyan.baidu.com/article/acf728fd8bc141f8e510a391.html\n安卓手机请看这里：\n先去应用商店安装一个解压专家。\n可以看这个教程：https://jingyan.baidu.com/article/f0e83a2543850b63e5910197.html',1,17,0,0,0,54345,0,489109,0,0,0,782,0,0,1,'2025-02-12 21:07:29','2025-02-12 21:11:55','2025-02-12 21:07:29',6038,'mega盘(大陆需梯子访问)|https://mega.nz/file/QY5zlBAA#ZCWC-bxPAi__XAAHy4e4b94KGjgD-s2kl34YryftH2k\n谷歌云盘(大陆需梯子访问)|https://drive.google.com/file/d/1U3_8lZKyB3h9uJ5lXNFEDhFUPahLURjK/view?usp=drive_link\n解压码|idoufu.com',0),(29,'57','[安卓]Erocondo埃罗公寓[165M/网盘下载]',1,'','/upload_01/upload1/20250208/2025020815481189978.png','',' ','','温馨提示：\n1.配置梯子方法：https://gochatgptcn.com/252.html\n\n2.谷歌网盘限制问题：\n当你访问谷歌网盘链接时，遇到如下情况\n抱歉，您目前无法查看或下载此文件。\n最近查看或下载此文件的用户过多。请稍后再尝试访问此文件。如果您尝试访问的文件特别大或已与很多人共享，那么您最长可能需要等待 24 小时才能查看或下载该文件。如果您在 24 小时仍然无法访问文件，请与您的网域管理员联系。\n这是因为谷歌网盘限制了未登录用户，你登录账号再下载就可以了，没有谷歌账号的话，可以注册一个。\n\n3.解压码教程：\n全站采用境外大厂网盘，杜绝了链接失效的问题，只需要会简单的解压方法即可获取资源。\n苹果手机解压教程请看这里：https://jingyan.baidu.com/article/acf728fd8bc141f8e510a391.html\n安卓手机请看这里：\n先去应用商店安装一个解压专家。\n可以看这个教程：https://jingyan.baidu.com/article/f0e83a2543850b63e5910197.html',1,13,0,0,0,73073,0,730734,0,0,0,845,0,0,1,'2025-02-12 21:07:29','2025-02-12 21:11:55','2025-02-12 21:07:29',8119,'mega盘(大陆需梯子访问)|https://mega.nz/file/4FxwXDYI#_Rfa9sRxMjvrxtul2uaxn5YBKHPvsKXuaqm3Nb5R-NA\n谷歌云盘(大陆需梯子访问)|https://drive.google.com/file/d/1YJLH0T8pnSPdlET9lz7HxbBiWjxkwxeL/view?usp=drive_link\n解压码|idoufu.com',0),(30,'56','[PC/汉化]角斗士学院[876M/网盘下载]',1,'','/upload_01/upload1/20250208/2025020815480928514.png','',' ','','温馨提示：\n1.配置梯子方法：https://gochatgptcn.com/252.html\n\n2.谷歌网盘限制问题：\n当你访问谷歌网盘链接时，遇到如下情况\n抱歉，您目前无法查看或下载此文件。\n最近查看或下载此文件的用户过多。请稍后再尝试访问此文件。如果您尝试访问的文件特别大或已与很多人共享，那么您最长可能需要等待 24 小时才能查看或下载该文件。如果您在 24 小时仍然无法访问文件，请与您的网域管理员联系。\n这是因为谷歌网盘限制了未登录用户，你登录账号再下载就可以了，没有谷歌账号的话，可以注册一个。\n\n3.解压码教程：\n全站采用境外大厂网盘，杜绝了链接失效的问题，只需要会简单的解压方法即可获取资源。\n苹果手机解压教程请看这里：https://jingyan.baidu.com/article/acf728fd8bc141f8e510a391.html\n安卓手机请看这里：\n先去应用商店安装一个解压专家。\n可以看这个教程：https://jingyan.baidu.com/article/f0e83a2543850b63e5910197.html',1,11,0,0,0,99191,0,694337,0,0,0,681,0,0,1,'2025-02-12 21:07:29','2025-02-12 21:11:55','2025-02-12 21:07:29',16531,'mega盘(大陆需梯子访问)|https://mega.nz/file/4B4ynK7b#-gZY17oTHmg745ohBIIIusw0MxRPmJ46NtqMS6YNJFE\n谷歌云盘(大陆需梯子访问)|https://drive.google.com/file/d/1WtHv-AqFZMMFGhSU2Edka6VsAZRB4m3z/view?usp=drive_link\n解压码|idoufu.com',0),(31,'55','[PC/中文]魔族的成年礼[229M/网盘下载]',1,'','/upload_01/upload1/20250208/2025020815480627024.png','',' ','','温馨提示：\n1.配置梯子方法：https://gochatgptcn.com/252.html\n\n2.谷歌网盘限制问题：\n当你访问谷歌网盘链接时，遇到如下情况\n抱歉，您目前无法查看或下载此文件。\n最近查看或下载此文件的用户过多。请稍后再尝试访问此文件。如果您尝试访问的文件特别大或已与很多人共享，那么您最长可能需要等待 24 小时才能查看或下载该文件。如果您在 24 小时仍然无法访问文件，请与您的网域管理员联系。\n这是因为谷歌网盘限制了未登录用户，你登录账号再下载就可以了，没有谷歌账号的话，可以注册一个。\n\n3.解压码教程：\n全站采用境外大厂网盘，杜绝了链接失效的问题，只需要会简单的解压方法即可获取资源。\n苹果手机解压教程请看这里：https://jingyan.baidu.com/article/acf728fd8bc141f8e510a391.html\n安卓手机请看这里：\n先去应用商店安装一个解压专家。\n可以看这个教程：https://jingyan.baidu.com/article/f0e83a2543850b63e5910197.html',1,15,0,0,0,49964,0,399718,0,0,0,763,0,0,1,'2025-02-12 21:07:30','2025-02-12 21:11:55','2025-02-12 21:07:30',6245,'mega盘(大陆需梯子访问)|https://mega.nz/file/4UoD0apI#U_P_sRshXTeAMACMVa2NDFLHY4XsHqlXnNyCxlVApvQ\n谷歌云盘(大陆需梯子访问)|https://drive.google.com/file/d/1c1mOssJxDgYTWh2vV_wQpJpI39HCNdRz/view?usp=drive_link\n解压码|idoufu.com',0),(32,'54','[PC/机翻]Hell Bride[745M/网盘下载]',1,'','/upload_01/upload1/20250208/2025020815480423078.png','',' ','','温馨提示：\n1.配置梯子方法：https://gochatgptcn.com/252.html\n\n2.谷歌网盘限制问题：\n当你访问谷歌网盘链接时，遇到如下情况\n抱歉，您目前无法查看或下载此文件。\n最近查看或下载此文件的用户过多。请稍后再尝试访问此文件。如果您尝试访问的文件特别大或已与很多人共享，那么您最长可能需要等待 24 小时才能查看或下载该文件。如果您在 24 小时仍然无法访问文件，请与您的网域管理员联系。\n这是因为谷歌网盘限制了未登录用户，你登录账号再下载就可以了，没有谷歌账号的话，可以注册一个。\n\n3.解压码教程：\n全站采用境外大厂网盘，杜绝了链接失效的问题，只需要会简单的解压方法即可获取资源。\n苹果手机解压教程请看这里：https://jingyan.baidu.com/article/acf728fd8bc141f8e510a391.html\n安卓手机请看这里：\n先去应用商店安装一个解压专家。\n可以看这个教程：https://jingyan.baidu.com/article/f0e83a2543850b63e5910197.html',1,10,0,0,0,60403,0,362418,0,0,0,538,0,0,1,'2025-02-12 21:07:30','2025-02-12 21:11:55','2025-02-12 21:07:30',7550,'mega盘(大陆需梯子访问)|https://mega.nz/file/EVZhEDZa#nChht28ncFTp6RmReA0XPD0zPQlg-P3aa2cCsU3r_vU\n谷歌云盘(大陆需梯子访问)|https://drive.google.com/file/d/1TRbdgACwEieaUCHZKvMaJ2ufMh3q15mc/view?usp=drive_link\n解压码|idoufu.com',0),(33,'53','[PC/汉化]DE·M公寓[604M/网盘下载]',1,'','/upload_01/upload1/20250208/2025020815480289715.png','',' ','','温馨提示：\n1.配置梯子方法：https://gochatgptcn.com/252.html\n\n2.谷歌网盘限制问题：\n当你访问谷歌网盘链接时，遇到如下情况\n抱歉，您目前无法查看或下载此文件。\n最近查看或下载此文件的用户过多。请稍后再尝试访问此文件。如果您尝试访问的文件特别大或已与很多人共享，那么您最长可能需要等待 24 小时才能查看或下载该文件。如果您在 24 小时仍然无法访问文件，请与您的网域管理员联系。\n这是因为谷歌网盘限制了未登录用户，你登录账号再下载就可以了，没有谷歌账号的话，可以注册一个。\n\n3.解压码教程：\n全站采用境外大厂网盘，杜绝了链接失效的问题，只需要会简单的解压方法即可获取资源。\n苹果手机解压教程请看这里：https://jingyan.baidu.com/article/acf728fd8bc141f8e510a391.html\n安卓手机请看这里：\n先去应用商店安装一个解压专家。\n可以看这个教程：https://jingyan.baidu.com/article/f0e83a2543850b63e5910197.html',1,10,0,0,0,70705,0,494936,0,0,0,753,0,0,1,'2025-02-12 21:07:30','2025-02-12 21:11:55','2025-02-12 21:07:30',11784,'mega盘(大陆需梯子访问)|https://mega.nz/file/YZB1xJJY#vzTlZyVrUBx0Gjkx3Keq2rfXP7zVzAFRNVaFdENn5AE\n谷歌云盘(大陆需梯子访问)|https://drive.google.com/file/d/1pAGnlwqbIojn4O4PmEkKgWiezyeR5Y6P/view?usp=drive_link\n解压码|idoufu.com',0),(34,'52','[PC/日语]彷徨える狼(全CG)[80M/网盘下载]',1,'','/upload_01/upload1/20250208/2025020815480075478.png','',' ','','温馨提示：\n1.配置梯子方法：https://gochatgptcn.com/252.html\n\n2.谷歌网盘限制问题：\n当你访问谷歌网盘链接时，遇到如下情况\n抱歉，您目前无法查看或下载此文件。\n最近查看或下载此文件的用户过多。请稍后再尝试访问此文件。如果您尝试访问的文件特别大或已与很多人共享，那么您最长可能需要等待 24 小时才能查看或下载该文件。如果您在 24 小时仍然无法访问文件，请与您的网域管理员联系。\n这是因为谷歌网盘限制了未登录用户，你登录账号再下载就可以了，没有谷歌账号的话，可以注册一个。\n\n3.解压码教程：\n全站采用境外大厂网盘，杜绝了链接失效的问题，只需要会简单的解压方法即可获取资源。\n苹果手机解压教程请看这里：https://jingyan.baidu.com/article/acf728fd8bc141f8e510a391.html\n安卓手机请看这里：\n先去应用商店安装一个解压专家。\n可以看这个教程：https://jingyan.baidu.com/article/f0e83a2543850b63e5910197.html',1,12,0,0,0,89652,0,896526,0,0,0,537,0,0,1,'2025-02-12 21:07:30','2025-02-12 21:11:55','2025-02-12 21:07:30',11206,'mega盘(大陆需梯子访问)|https://mega.nz/file/dYJwBKwa#uZEC6uD4luIE5udrhg5ecVRtOUX6DQ4TGpWVlO0BZc4\n谷歌云盘(大陆需梯子访问)|https://drive.google.com/file/d/1WrV69NPHjFUYg7JEqDkjEvbffg1A-Ql1/view?usp=drive_link\n解压码|idoufu.com',0),(35,'51','[安卓]erocando破解版 v0.1.9_mod[165M/网盘下载]',1,'','/upload_01/upload1/20250208/2025020815475520637.png','',' 是无限精力的 建议搭配连点器用，比较适合看cg哒 ','','温馨提示：\n1.配置梯子方法：https://gochatgptcn.com/252.html\n\n2.谷歌网盘限制问题：\n当你访问谷歌网盘链接时，遇到如下情况\n抱歉，您目前无法查看或下载此文件。\n最近查看或下载此文件的用户过多。请稍后再尝试访问此文件。如果您尝试访问的文件特别大或已与很多人共享，那么您最长可能需要等待 24 小时才能查看或下载该文件。如果您在 24 小时仍然无法访问文件，请与您的网域管理员联系。\n这是因为谷歌网盘限制了未登录用户，你登录账号再下载就可以了，没有谷歌账号的话，可以注册一个。\n\n3.解压码教程：\n全站采用境外大厂网盘，杜绝了链接失效的问题，只需要会简单的解压方法即可获取资源。\n苹果手机解压教程请看这里：https://jingyan.baidu.com/article/acf728fd8bc141f8e510a391.html\n安卓手机请看这里：\n先去应用商店安装一个解压专家。\n可以看这个教程：https://jingyan.baidu.com/article/f0e83a2543850b63e5910197.html',1,10,0,0,0,64468,0,515745,0,0,0,898,0,0,1,'2025-02-12 21:07:30','2025-02-12 21:11:55','2025-02-12 21:07:30',9209,'mega盘(大陆需梯子访问)|https://mega.nz/file/ZdBxWbCa#awVytvDbafqlSBIPabH2lRHJcebpPDunAixwrMchHnI\n谷歌云盘(大陆需梯子访问)|https://drive.google.com/file/d/1RzCRZzazbSz2bkmYgk7XVvug82fOUByn/view?usp=drive_link\n解压码|idoufu.com',0),(36,'50','[PC/中文]瑞破调jiao教室vol1：士兵76篇[29M/网盘下载]',1,'','/upload_01/upload1/20250208/2025020815475216986.png','',' 有5个结局，不过he很难打出来 ','','温馨提示：\n1.配置梯子方法：https://gochatgptcn.com/252.html\n\n2.谷歌网盘限制问题：\n当你访问谷歌网盘链接时，遇到如下情况\n抱歉，您目前无法查看或下载此文件。\n最近查看或下载此文件的用户过多。请稍后再尝试访问此文件。如果您尝试访问的文件特别大或已与很多人共享，那么您最长可能需要等待 24 小时才能查看或下载该文件。如果您在 24 小时仍然无法访问文件，请与您的网域管理员联系。\n这是因为谷歌网盘限制了未登录用户，你登录账号再下载就可以了，没有谷歌账号的话，可以注册一个。\n\n3.解压码教程：\n全站采用境外大厂网盘，杜绝了链接失效的问题，只需要会简单的解压方法即可获取资源。\n苹果手机解压教程请看这里：https://jingyan.baidu.com/article/acf728fd8bc141f8e510a391.html\n安卓手机请看这里：\n先去应用商店安装一个解压专家。\n可以看这个教程：https://jingyan.baidu.com/article/f0e83a2543850b63e5910197.html',1,15,0,0,0,70318,0,562549,0,0,0,521,0,0,1,'2025-02-12 21:07:30','2025-02-12 21:11:55','2025-02-12 21:07:30',7031,'mega盘(大陆需梯子访问)|https://mega.nz/file/dcJm2YxQ#OL0IFziasj8ZeZxJBbDJA-COc9s4Pak_xCPI1qqa_M8\n谷歌云盘(大陆需梯子访问)|https://drive.google.com/file/d/1u-tBARk6MFdP2AiaXIWaImNDx9_ONX1L/view?usp=drive_link\n解压码|idoufu.com',0),(37,'49','[PC]Dreadlord_ascension 全CG[327M/网盘下载]',1,'','/upload_01/upload1/20250208/2025020815475063247.png','',' 全CG存档，啥都可以看。\n整体来说是像素风格但也有动态剧情 ','','温馨提示：\n1.配置梯子方法：https://gochatgptcn.com/252.html\n\n2.谷歌网盘限制问题：\n当你访问谷歌网盘链接时，遇到如下情况\n抱歉，您目前无法查看或下载此文件。\n最近查看或下载此文件的用户过多。请稍后再尝试访问此文件。如果您尝试访问的文件特别大或已与很多人共享，那么您最长可能需要等待 24 小时才能查看或下载该文件。如果您在 24 小时仍然无法访问文件，请与您的网域管理员联系。\n这是因为谷歌网盘限制了未登录用户，你登录账号再下载就可以了，没有谷歌账号的话，可以注册一个。\n\n3.解压码教程：\n全站采用境外大厂网盘，杜绝了链接失效的问题，只需要会简单的解压方法即可获取资源。\n苹果手机解压教程请看这里：https://jingyan.baidu.com/article/acf728fd8bc141f8e510a391.html\n安卓手机请看这里：\n先去应用商店安装一个解压专家。\n可以看这个教程：https://jingyan.baidu.com/article/f0e83a2543850b63e5910197.html',1,10,0,0,0,165672,0,994037,0,0,0,980,0,0,1,'2025-02-12 21:07:30','2025-02-12 21:11:55','2025-02-12 21:07:30',27612,'mega盘(大陆需梯子访问)|https://mega.nz/file/lMRi0Rib#-4vIb62pHL-ZJdilICg-h8UY8NIk4K3msn2Q7gydg5g\n谷歌云盘(大陆需梯子访问)|https://drive.google.com/file/d/1iKg7VlKrnU33fxydEIJIpTppvNARrD1C/view?usp=drive_link\n解压码|idoufu.com',0),(38,'48','[PC/安卓/英文]Endless Bounty[75M/网盘下载]',1,'','/upload_01/upload1/20250208/2025020815474841857.png','','   PC版： 安卓版： ','','温馨提示：\n1.配置梯子方法：https://gochatgptcn.com/252.html\n\n2.谷歌网盘限制问题：\n当你访问谷歌网盘链接时，遇到如下情况\n抱歉，您目前无法查看或下载此文件。\n最近查看或下载此文件的用户过多。请稍后再尝试访问此文件。如果您尝试访问的文件特别大或已与很多人共享，那么您最长可能需要等待 24 小时才能查看或下载该文件。如果您在 24 小时仍然无法访问文件，请与您的网域管理员联系。\n这是因为谷歌网盘限制了未登录用户，你登录账号再下载就可以了，没有谷歌账号的话，可以注册一个。\n\n3.解压码教程：\n全站采用境外大厂网盘，杜绝了链接失效的问题，只需要会简单的解压方法即可获取资源。\n苹果手机解压教程请看这里：https://jingyan.baidu.com/article/acf728fd8bc141f8e510a391.html\n安卓手机请看这里：\n先去应用商店安装一个解压专家。\n可以看这个教程：https://jingyan.baidu.com/article/f0e83a2543850b63e5910197.html',1,13,0,0,0,144905,0,869431,0,0,0,962,0,0,1,'2025-02-12 21:07:30','2025-02-12 21:11:55','2025-02-12 21:07:30',24150,'mega盘(大陆需梯子访问)|https://mega.nz/file/9Q5j2YyS#nMwGXjZRK4WtCxsqEGW3j-ekvbT-JuafppuR_8tyHcw\n谷歌云盘(大陆需梯子访问)|https://drive.google.com/file/d/1ecFKRGHIhPn9FM6ThCYgiV8UmaqlVM_r/view?usp=drive_link\n解压码|idoufu.com',0),(39,'47','[PC/汉化]声优男子_Seiyuu Danshi完整配音版[1.35G/网盘下载]',1,'','/upload_01/upload1/20250208/2025020815474614379.png','',' 你将扮演一个新来的配音演员，同时也是一个离家出走的人，即将被他的经纪公司开除。回家不是一个选择，因此开始他的旅程，为了不被成为一个足够好的声音演员！ （虽然可以算是纯爱游戏，但是该有的东西都有，还有触摸互动系统。） ','','温馨提示：\n1.配置梯子方法：https://gochatgptcn.com/252.html\n\n2.谷歌网盘限制问题：\n当你访问谷歌网盘链接时，遇到如下情况\n抱歉，您目前无法查看或下载此文件。\n最近查看或下载此文件的用户过多。请稍后再尝试访问此文件。如果您尝试访问的文件特别大或已与很多人共享，那么您最长可能需要等待 24 小时才能查看或下载该文件。如果您在 24 小时仍然无法访问文件，请与您的网域管理员联系。\n这是因为谷歌网盘限制了未登录用户，你登录账号再下载就可以了，没有谷歌账号的话，可以注册一个。\n\n3.解压码教程：\n全站采用境外大厂网盘，杜绝了链接失效的问题，只需要会简单的解压方法即可获取资源。\n苹果手机解压教程请看这里：https://jingyan.baidu.com/article/acf728fd8bc141f8e510a391.html\n安卓手机请看这里：\n先去应用商店安装一个解压专家。\n可以看这个教程：https://jingyan.baidu.com/article/f0e83a2543850b63e5910197.html',1,20,0,0,0,136881,0,958172,0,0,0,814,0,0,1,'2025-02-12 21:07:30','2025-02-12 21:11:55','2025-02-12 21:07:30',15209,'mega盘(大陆需梯子访问)|https://mega.nz/file/NRBgDZ5b#mj0n4YN5nX4AVqTGM0-BP-o_n-lLhXSwRpb1mKeAb6w\n谷歌云盘(大陆需梯子访问)|https://drive.google.com/file/d/1P0V8nJrmkpmbcM_ahQ_zylKkOa4uDK9C/view?usp=drive_link\n解压码|idoufu.com',0),(40,'46','[PC/汉化]黑猴子_海滩救生员[30M/网盘下载]',1,'','/upload_01/upload1/20250208/2025020815474455271.png','',' 黑猴子海滩救生员是一款采用AVG形式打造的互动剧情类游戏，玩家将在游戏中攻略自己暗恋的猛男前辈，对方正在海滩担任救生员，你也将前往一同兼职工作，不过这个海滩人气低迷，没有什么游客，玩家在结束工作后就可以和猛男学长回自己叔母家住宿，一起喝酒聊天，触发剧情和CG。   ','','温馨提示：\n1.配置梯子方法：https://gochatgptcn.com/252.html\n\n2.谷歌网盘限制问题：\n当你访问谷歌网盘链接时，遇到如下情况\n抱歉，您目前无法查看或下载此文件。\n最近查看或下载此文件的用户过多。请稍后再尝试访问此文件。如果您尝试访问的文件特别大或已与很多人共享，那么您最长可能需要等待 24 小时才能查看或下载该文件。如果您在 24 小时仍然无法访问文件，请与您的网域管理员联系。\n这是因为谷歌网盘限制了未登录用户，你登录账号再下载就可以了，没有谷歌账号的话，可以注册一个。\n\n3.解压码教程：\n全站采用境外大厂网盘，杜绝了链接失效的问题，只需要会简单的解压方法即可获取资源。\n苹果手机解压教程请看这里：https://jingyan.baidu.com/article/acf728fd8bc141f8e510a391.html\n安卓手机请看这里：\n先去应用商店安装一个解压专家。\n可以看这个教程：https://jingyan.baidu.com/article/f0e83a2543850b63e5910197.html',1,11,0,0,0,47706,0,477068,0,0,0,582,0,0,1,'2025-02-12 21:07:30','2025-02-12 21:11:55','2025-02-12 21:07:30',4770,'mega盘(大陆需梯子访问)|https://mega.nz/file/QUoFWSIY#Gjgwy9tpyzh-d9glQyvJSA52t7e65RAYDXWQ8XQ20w0\n谷歌云盘(大陆需梯子访问)|https://drive.google.com/file/d/17tKDKXsRW-nWEv_LPYr1r6kcDhidS9LM/view?usp=drive_link\n解压码|idoufu.com',0),(41,'45','[PC/汉化]我的恋人是神明[161M/网盘下载]',1,'','/upload_01/upload1/20250208/2025020815474234504.png','',' ','','温馨提示：\n1.配置梯子方法：https://gochatgptcn.com/252.html\n\n2.谷歌网盘限制问题：\n当你访问谷歌网盘链接时，遇到如下情况\n抱歉，您目前无法查看或下载此文件。\n最近查看或下载此文件的用户过多。请稍后再尝试访问此文件。如果您尝试访问的文件特别大或已与很多人共享，那么您最长可能需要等待 24 小时才能查看或下载该文件。如果您在 24 小时仍然无法访问文件，请与您的网域管理员联系。\n这是因为谷歌网盘限制了未登录用户，你登录账号再下载就可以了，没有谷歌账号的话，可以注册一个。\n\n3.解压码教程：\n全站采用境外大厂网盘，杜绝了链接失效的问题，只需要会简单的解压方法即可获取资源。\n苹果手机解压教程请看这里：https://jingyan.baidu.com/article/acf728fd8bc141f8e510a391.html\n安卓手机请看这里：\n先去应用商店安装一个解压专家。\n可以看这个教程：https://jingyan.baidu.com/article/f0e83a2543850b63e5910197.html',1,15,0,0,0,110796,0,664780,0,0,0,548,0,0,1,'2025-02-12 21:07:30','2025-02-12 21:11:55','2025-02-12 21:07:30',11079,'mega盘(大陆需梯子访问)|https://mega.nz/file/hBQzSTrI#07CeTbEehhU6-KHDb3cL-XeAy58-bcncEaLC9c_qefw\n谷歌云盘(大陆需梯子访问)|https://drive.google.com/file/d/1n0aLYEQDWe_Xkps5LLDZ7DF8kEvXFZV8/view?usp=drive_link\n解压码|idoufu.com',0),(42,'44','[PC/汉化]夏有天狼完整版_带隐藏结局[296M/网盘下载]',1,'','/upload_01/upload1/20250208/2025020815473585322.png','',' 此为完整版带隐藏结局 ','','温馨提示：\n1.配置梯子方法：https://gochatgptcn.com/252.html\n\n2.谷歌网盘限制问题：\n当你访问谷歌网盘链接时，遇到如下情况\n抱歉，您目前无法查看或下载此文件。\n最近查看或下载此文件的用户过多。请稍后再尝试访问此文件。如果您尝试访问的文件特别大或已与很多人共享，那么您最长可能需要等待 24 小时才能查看或下载该文件。如果您在 24 小时仍然无法访问文件，请与您的网域管理员联系。\n这是因为谷歌网盘限制了未登录用户，你登录账号再下载就可以了，没有谷歌账号的话，可以注册一个。\n\n3.解压码教程：\n全站采用境外大厂网盘，杜绝了链接失效的问题，只需要会简单的解压方法即可获取资源。\n苹果手机解压教程请看这里：https://jingyan.baidu.com/article/acf728fd8bc141f8e510a391.html\n安卓手机请看这里：\n先去应用商店安装一个解压专家。\n可以看这个教程：https://jingyan.baidu.com/article/f0e83a2543850b63e5910197.html',1,14,0,0,0,123502,0,741017,0,0,0,885,0,0,1,'2025-02-12 21:07:30','2025-02-12 21:11:55','2025-02-12 21:07:30',17643,'mega盘(大陆需梯子访问)|https://mega.nz/file/hBAj3K4b#6u8pZso6xSYuNO-Z1FVCOFI7-Uic-_FY5mbhwUT2cGM\n谷歌云盘(大陆需梯子访问)|https://drive.google.com/file/d/16f0NYYu3TiKsb7NY6FJxR6akUvfNOKNw/view?usp=drive_link\n解压码|idoufu.com',0),(43,'43','[PC/日语]裸の勇者[70M/网盘下载]',1,'','/upload_01/upload1/20250208/2025020815473355719.png','',' PS:如果无法打开，请先安装rpg三件套。 \n按重要物品里的那本（小黄）书来排序：\n第一张CG在森林村庄里，你刚来的时候会发现这个地方的旅店没有老板，往上走会看到一个由可通过的树木组成的小道，进入后往下会发现旅店老板在那里自我安慰，接近后触发CG（被抱起来X）\n第二张CG是被森林的狼人BOSS打败后就能解锁（人兽）\n第三张CG在哨兵站（就是一开始有个士兵堵你路的地方），在可以通过后和下面房间里的士兵对话，就能解锁和这两个哨兵嗯嗯啊啊的CG\n第四张CG是被洞穴里的一大群史莱姆击败后触发（粘液PLAY）\n第五张CG是剧情必定触发的，你第一次进入沙漠村庄的地下室就会被热情朴素的当地人欢迎，亲身感受当地淳朴的民风（轮J）\n第六张CG被火山洞穴里的大乌贼击败后触发（角虫手PLAY）\n第七张CG在高塔被那5只哥布林击败后触发（群P而且双管齐下）\n第八张CG也是剧情必定触发的，在井里追寻高塔上黑你箱子的盗贼，找到后和他对话，他会给你一把万能钥匙，可以打开所有铁门，还很诚恳的用身体和你道歉（被坐）\n第九张CG在洞穴用万能钥匙打开铁门救出王子后，在王城旁边的的城市里的旅馆睡觉，王子就会用身体报答你（正入）-\n第十张CG需要收集地图上的金币（这里就不说分别在什么位置了，地毯式搜索就能找齐，另外所有宝箱怪打死后地上都会有个金币，所以一定要记得拿），每5个可以和国王换一次东西，前几次国王会给你装备，最后一次国王没东西给了就被主角强上了（父子通吃）\n第十一张CG被恶龙击败后触发（另外通往恶龙城堡的路是沼泽上面的毒池的那个石碑，把包里贵重物品栏里不知道什么时候多出来的水晶交给沙漠村庄的铁匠打造成剑再和石碑对话就能开启）* H8 `9 A4 U5 D8 c. B2 e\n第十二张CG在和恶龙对话时全选一就能解锁（好像是主角养了一堆男宠）','','温馨提示：\n1.配置梯子方法：https://gochatgptcn.com/252.html\n\n2.谷歌网盘限制问题：\n当你访问谷歌网盘链接时，遇到如下情况\n抱歉，您目前无法查看或下载此文件。\n最近查看或下载此文件的用户过多。请稍后再尝试访问此文件。如果您尝试访问的文件特别大或已与很多人共享，那么您最长可能需要等待 24 小时才能查看或下载该文件。如果您在 24 小时仍然无法访问文件，请与您的网域管理员联系。\n这是因为谷歌网盘限制了未登录用户，你登录账号再下载就可以了，没有谷歌账号的话，可以注册一个。\n\n3.解压码教程：\n全站采用境外大厂网盘，杜绝了链接失效的问题，只需要会简单的解压方法即可获取资源。\n苹果手机解压教程请看这里：https://jingyan.baidu.com/article/acf728fd8bc141f8e510a391.html\n安卓手机请看这里：\n先去应用商店安装一个解压专家。\n可以看这个教程：https://jingyan.baidu.com/article/f0e83a2543850b63e5910197.html',1,12,0,0,0,78715,0,472292,0,0,0,682,0,0,1,'2025-02-12 21:07:30','2025-02-12 21:11:55','2025-02-12 21:07:30',9839,'mega盘(大陆需梯子访问)|https://mega.nz/file/8VREUD7a#Ij-Aqe6fxRkpwYt2WI_QJv-ClbNcU6V4wdqBKTQa6yg\n谷歌云盘(大陆需梯子访问)|https://drive.google.com/file/d/1sxKfrqYrs3e06TYWwQ0s_Wz8S25At46j/view?usp=drive_link\n解压码|idoufu.com',0),(44,'42','[PC/汉化]Just Bros-1.210[1.2G/网盘下载]',1,'','/upload_01/upload1/20250208/2025020815473170003.png','',' ','','温馨提示：\n1.配置梯子方法：https://gochatgptcn.com/252.html\n\n2.谷歌网盘限制问题：\n当你访问谷歌网盘链接时，遇到如下情况\n抱歉，您目前无法查看或下载此文件。\n最近查看或下载此文件的用户过多。请稍后再尝试访问此文件。如果您尝试访问的文件特别大或已与很多人共享，那么您最长可能需要等待 24 小时才能查看或下载该文件。如果您在 24 小时仍然无法访问文件，请与您的网域管理员联系。\n这是因为谷歌网盘限制了未登录用户，你登录账号再下载就可以了，没有谷歌账号的话，可以注册一个。\n\n3.解压码教程：\n全站采用境外大厂网盘，杜绝了链接失效的问题，只需要会简单的解压方法即可获取资源。\n苹果手机解压教程请看这里：https://jingyan.baidu.com/article/acf728fd8bc141f8e510a391.html\n安卓手机请看这里：\n先去应用商店安装一个解压专家。\n可以看这个教程：https://jingyan.baidu.com/article/f0e83a2543850b63e5910197.html',1,16,0,0,0,118576,0,948610,0,0,0,994,0,0,1,'2025-02-12 21:07:30','2025-02-12 21:11:55','2025-02-12 21:07:30',16939,'mega盘(大陆需梯子访问)|https://mega.nz/file/cZ5Ewb5B#Iz_TdyuhJ8gZdzuGwEyuo6yXqlKeYtw5ffLyOYdglnU\n谷歌云盘(大陆需梯子访问)|https://drive.google.com/file/d/1XdexLNeZREMMdX29_nz1yVmNdvbE4Zpg/view?usp=drive_link\n解压码|idoufu.com',0),(45,'41','[PC/汉化]不良クンおさわり強制発射[212M/网盘下载]',1,'','/upload_01/upload1/20250208/2025020815472978772.png','',' 压缩包里含日版和中文汉化版，是免安装的，点开即玩。\n对了，汉化有一点问题请忽略它，里面有攻略。\n这游戏玩法我还是头一次见（maybe我有点孤陋寡闻了），很好玩的，不适合1控控度比较深的人玩 ','','温馨提示：\n1.配置梯子方法：https://gochatgptcn.com/252.html\n\n2.谷歌网盘限制问题：\n当你访问谷歌网盘链接时，遇到如下情况\n抱歉，您目前无法查看或下载此文件。\n最近查看或下载此文件的用户过多。请稍后再尝试访问此文件。如果您尝试访问的文件特别大或已与很多人共享，那么您最长可能需要等待 24 小时才能查看或下载该文件。如果您在 24 小时仍然无法访问文件，请与您的网域管理员联系。\n这是因为谷歌网盘限制了未登录用户，你登录账号再下载就可以了，没有谷歌账号的话，可以注册一个。\n\n3.解压码教程：\n全站采用境外大厂网盘，杜绝了链接失效的问题，只需要会简单的解压方法即可获取资源。\n苹果手机解压教程请看这里：https://jingyan.baidu.com/article/acf728fd8bc141f8e510a391.html\n安卓手机请看这里：\n先去应用商店安装一个解压专家。\n可以看这个教程：https://jingyan.baidu.com/article/f0e83a2543850b63e5910197.html',1,20,0,0,0,50870,0,508703,0,0,0,856,0,0,1,'2025-02-12 21:07:30','2025-02-12 21:11:55','2025-02-12 21:07:30',8478,'mega盘(大陆需梯子访问)|https://mega.nz/file/VFgGTDob#5wntVCRGxsQx3YGAzKjTAG2tlKVjnoAI70jXGjDQoPY\n谷歌云盘(大陆需梯子访问)|https://drive.google.com/file/d/1UnTaKunvgC26hVsAE0uShe0S4XOmOe-S/view?usp=drive_link\n解压码|idoufu.com',0),(46,'40','BL后宫[网页游戏]',1,'','https://imgpublic.ycomesc.live/upload_01/upload1/20250208/2025020815472729873.png','','一个国外很热门的在线冒险BL游戏，让玩家打造属于自己的性感猛男后宫。 PS：虽然没有中文，但是可以使用浏览器自带的翻译功能进行翻译。','','',1,12,0,0,0,4860,0,528685,0,0,0,688,0,0,1,'2025-02-12 21:07:30','2025-02-12 21:11:01','2025-02-12 21:07:30',5874,'地址|https://www.gayharem.com/?ref_id=24&amp;noagev=1&amp;tc1=HH036df829b7107a4898bf5ae48f8515bf&amp;tc2=22384&amp;tc3=49&amp;tc4=SOI&amp;tc5=&amp;tc6=&amp;tc7=&amp;tc8=',0),(47,'39','[安卓/PC/中文]奴隶城堡[339M/网盘下载]',1,'','/upload_01/upload1/20250208/2025020815472551228.png','',' 标签：BL 弟1哥0 奴0 骨科\n讲述的是男主去奴隶岛救哥哥，却没有想到身边抓来奴隶就是哥哥的故事……     不会配置梯子的话请查看这里：https://gochatgptcn.com/252.html ','','温馨提示：\n1.配置梯子方法：https://gochatgptcn.com/252.html\n\n2.谷歌网盘限制问题：\n当你访问谷歌网盘链接时，遇到如下情况\n抱歉，您目前无法查看或下载此文件。\n最近查看或下载此文件的用户过多。请稍后再尝试访问此文件。如果您尝试访问的文件特别大或已与很多人共享，那么您最长可能需要等待 24 小时才能查看或下载该文件。如果您在 24 小时仍然无法访问文件，请与您的网域管理员联系。\n这是因为谷歌网盘限制了未登录用户，你登录账号再下载就可以了，没有谷歌账号的话，可以注册一个。\n\n3.解压码教程：\n全站采用境外大厂网盘，杜绝了链接失效的问题，只需要会简单的解压方法即可获取资源。\n苹果手机解压教程请看这里：https://jingyan.baidu.com/article/acf728fd8bc141f8e510a391.html\n安卓手机请看这里：\n先去应用商店安装一个解压专家。\n可以看这个教程：https://jingyan.baidu.com/article/f0e83a2543850b63e5910197.html',1,20,0,0,0,53224,0,372572,0,0,0,871,0,0,1,'2025-02-12 21:07:30','2025-02-12 21:08:04','2025-02-12 21:07:30',5913,'mega盘(大陆需梯子访问)|https://mega.nz/file/QVw23BxA#wI-uS1r1EbeB6sndjd3pCjTNWackvtZubScgiodxlSM\n谷歌云盘(大陆需梯子访问)|https://drive.google.com/file/d/1O6ShyjMfU3DF0GYzysrQOn1cj47QvqT9/view?usp=drive_link\n解压码|idoufu.com',0),(48,'38','[安卓/IOS]新世界狂欢[158M/官方下载]',1,'','https://imgpublic.ycomesc.live/upload_01/upload1/20250208/2025020815472214102.png','','游戏介绍： 游戏中的角色采全动态制作，玩家可以选择喜欢的角色作为主画面人物，甚至改变角色的服装及其裸露的程度，并做出栩栩如生的动作和难以忍耐的表情，角色的对话也将随着好感度、生日等有丰富的变化，让玩家日日嗑、夜夜嗑！  游戏玩法为回合制战斗 RPG，自由选择最多 5 名角色编成队伍，并透过属性克制与技能通关。战斗中角色若受到伤害，则会依其受伤的程度使衣物破裂裸露出大片肌肤，以及表情上的特殊变化，使战斗中的展演更加丰富具特色。 游戏中的「声音体验」一直是制作团队相当重视的一部分。因此特与日本声优合作帮游戏主要角色配音，打造画面、剧情、语音的沉浸体验，诠释出每位角色独有的个性与喘息，感受男上加男所带来的极致享受。  每位角色均有亲密度设定，持续累积亲密度至一定程度，将可解锁与角色间的亲密剧情！每位角色均有数种Ｈ-Scene 的动态演出，主人公可攻可受的设定，透过动态技术呈现，演绎角色间互动的淋漓汗水以及动态感等细微处，耳边传声历其境的欢愉声，完全沉浸在角色间的缠绵互动中，前所未有的刺激及临场感受，绝对获得极大的满足。  透过游戏专有的蜜话系统，挑选礼物并赠送给角色，增加两人间的亲密度即可呈现更多赤裸的样貌，面红耳赤的神情和娇声喘息，全看你如何运用双手达到舒服的境界。','','温馨提示：\n1.配置梯子方法：https://gochatgptcn.com/252.html\n\n2.谷歌网盘限制问题：\n当你访问谷歌网盘链接时，遇到如下情况\n抱歉，您目前无法查看或下载此文件。\n最近查看或下载此文件的用户过多。请稍后再尝试访问此文件。如果您尝试访问的文件特别大或已与很多人共享，那么您最长可能需要等待 24 小时才能查看或下载该文件。如果您在 24 小时仍然无法访问文件，请与您的网域管理员联系。\n这是因为谷歌网盘限制了未登录用户，你登录账号再下载就可以了，没有谷歌账号的话，可以注册一个。\n\n3.解压码教程：\n全站采用境外大厂网盘，杜绝了链接失效的问题，只需要会简单的解压方法即可获取资源。\n苹果手机解压教程请看这里：https://jingyan.baidu.com/article/acf728fd8bc141f8e510a391.html\n安卓手机请看这里：\n先去应用商店安装一个解压专家。\n可以看这个教程：https://jingyan.baidu.com/article/f0e83a2543850b63e5910197.html',1,15,0,0,0,4545,0,604415,0,0,0,553,0,0,1,'2025-02-12 21:07:30','2025-02-12 21:09:26','2025-02-12 21:07:30',11192,'地址|https://nucarnival.ero-labs.plus/cps/zh/index.html',0),(49,'37','[安卓/汉化]酒倾愁不来[148M/网盘下载]',1,'','/upload_01/upload1/20250208/2025020815472088821.png','',' 进入游戏界面，点击左侧“中文”按钮，即可切换中文\n在禁酒令期间，我曾被迫以事物和清水度日——W.C.菲尔兹\n一个与禁酒令有关的故事，共两条线，两个人物可攻略\n','','温馨提示：\n1.配置梯子方法：https://gochatgptcn.com/252.html\n\n2.谷歌网盘限制问题：\n当你访问谷歌网盘链接时，遇到如下情况\n抱歉，您目前无法查看或下载此文件。\n最近查看或下载此文件的用户过多。请稍后再尝试访问此文件。如果您尝试访问的文件特别大或已与很多人共享，那么您最长可能需要等待 24 小时才能查看或下载该文件。如果您在 24 小时仍然无法访问文件，请与您的网域管理员联系。\n这是因为谷歌网盘限制了未登录用户，你登录账号再下载就可以了，没有谷歌账号的话，可以注册一个。\n\n3.解压码教程：\n全站采用境外大厂网盘，杜绝了链接失效的问题，只需要会简单的解压方法即可获取资源。\n苹果手机解压教程请看这里：https://jingyan.baidu.com/article/acf728fd8bc141f8e510a391.html\n安卓手机请看这里：\n先去应用商店安装一个解压专家。\n可以看这个教程：https://jingyan.baidu.com/article/f0e83a2543850b63e5910197.html',1,13,0,0,0,108534,0,759741,0,0,0,772,0,0,1,'2025-02-12 21:07:30','2025-02-12 21:08:04','2025-02-12 21:07:30',18089,'mega盘(大陆需梯子访问)|https://mega.nz/file/NR5xjKSA#5xoLxnm4v5zcMNiHRVGYUB3sgOXt0aOKbFvC_3y22zw\n谷歌云盘(大陆需梯子访问)|https://drive.google.com/file/d/1707n39u11B_JYH683QFAZb-a7AArcp7j/view?usp=drive_link\n解压码|idoufu.com',0),(50,'36','[安卓/中文]furry-城堡与莫梭提斯[333M/网盘下载]',1,'','/upload_01/upload1/20250208/2025020815471787420.png','',' 虽然没有立绘但真的超棒（而且特别甜） ','','温馨提示：\n1.配置梯子方法：https://gochatgptcn.com/252.html\n\n2.谷歌网盘限制问题：\n当你访问谷歌网盘链接时，遇到如下情况\n抱歉，您目前无法查看或下载此文件。\n最近查看或下载此文件的用户过多。请稍后再尝试访问此文件。如果您尝试访问的文件特别大或已与很多人共享，那么您最长可能需要等待 24 小时才能查看或下载该文件。如果您在 24 小时仍然无法访问文件，请与您的网域管理员联系。\n这是因为谷歌网盘限制了未登录用户，你登录账号再下载就可以了，没有谷歌账号的话，可以注册一个。\n\n3.解压码教程：\n全站采用境外大厂网盘，杜绝了链接失效的问题，只需要会简单的解压方法即可获取资源。\n苹果手机解压教程请看这里：https://jingyan.baidu.com/article/acf728fd8bc141f8e510a391.html\n安卓手机请看这里：\n先去应用商店安装一个解压专家。\n可以看这个教程：https://jingyan.baidu.com/article/f0e83a2543850b63e5910197.html',1,13,0,0,0,111956,0,783696,0,0,0,810,0,0,1,'2025-02-12 21:07:30','2025-02-12 21:08:04','2025-02-12 21:07:30',15993,'mega盘(大陆需梯子访问)|https://mega.nz/file/hFAjiZrD#fvhyMYIC-r2ruI2ogglfAZs-Dcr5RurKNaWRV33Hk5w\n谷歌云盘(大陆需梯子访问)|https://drive.google.com/file/d/1KIiyasCZFUTGts3TssDtqgiak_UxPt86/view?usp=drive_link\n解压码|idoufu.com',0),(51,'35','[安卓]鸣人BL同人游戏[23M/网盘下载]',1,'','/upload_01/upload1/20250208/2025020815471532742.png','',' ','','温馨提示：\n1.配置梯子方法：https://gochatgptcn.com/252.html\n\n2.谷歌网盘限制问题：\n当你访问谷歌网盘链接时，遇到如下情况\n抱歉，您目前无法查看或下载此文件。\n最近查看或下载此文件的用户过多。请稍后再尝试访问此文件。如果您尝试访问的文件特别大或已与很多人共享，那么您最长可能需要等待 24 小时才能查看或下载该文件。如果您在 24 小时仍然无法访问文件，请与您的网域管理员联系。\n这是因为谷歌网盘限制了未登录用户，你登录账号再下载就可以了，没有谷歌账号的话，可以注册一个。\n\n3.解压码教程：\n全站采用境外大厂网盘，杜绝了链接失效的问题，只需要会简单的解压方法即可获取资源。\n苹果手机解压教程请看这里：https://jingyan.baidu.com/article/acf728fd8bc141f8e510a391.html\n安卓手机请看这里：\n先去应用商店安装一个解压专家。\n可以看这个教程：https://jingyan.baidu.com/article/f0e83a2543850b63e5910197.html',1,15,0,0,0,82897,0,497382,0,0,0,891,0,0,1,'2025-02-12 21:07:30','2025-02-12 21:08:04','2025-02-12 21:07:30',11842,'mega盘(大陆需梯子访问)|https://mega.nz/file/1JZnWbyL#IobbRxdJpri66cnjLVJCNsbOL0wWDnxOMBxIa2aOVMY\n谷歌云盘(大陆需梯子访问)|https://drive.google.com/file/d/1k8X9NlmKQoE7EvnraHVBZ0skawvCF-Xy/view?usp=drive_link\n解压码|idoufu.com',0),(52,'34','[PC/中文]天国守卫-Paradiso Guardian正式版[755M/网盘下载]',1,'','/upload_01/upload1/20250208/2025020815471331615.png','',' lvlv大大的天国守卫 ','','温馨提示：\n1.配置梯子方法：https://gochatgptcn.com/252.html\n\n2.谷歌网盘限制问题：\n当你访问谷歌网盘链接时，遇到如下情况\n抱歉，您目前无法查看或下载此文件。\n最近查看或下载此文件的用户过多。请稍后再尝试访问此文件。如果您尝试访问的文件特别大或已与很多人共享，那么您最长可能需要等待 24 小时才能查看或下载该文件。如果您在 24 小时仍然无法访问文件，请与您的网域管理员联系。\n这是因为谷歌网盘限制了未登录用户，你登录账号再下载就可以了，没有谷歌账号的话，可以注册一个。\n\n3.解压码教程：\n全站采用境外大厂网盘，杜绝了链接失效的问题，只需要会简单的解压方法即可获取资源。\n苹果手机解压教程请看这里：https://jingyan.baidu.com/article/acf728fd8bc141f8e510a391.html\n安卓手机请看这里：\n先去应用商店安装一个解压专家。\n可以看这个教程：https://jingyan.baidu.com/article/f0e83a2543850b63e5910197.html',1,13,0,0,0,114394,0,686366,0,0,0,550,0,0,1,'2025-02-12 21:07:30','2025-02-12 21:08:04','2025-02-12 21:07:30',14299,'mega盘(大陆需梯子访问)|https://mega.nz/file/gVgmGQga#U5zw7-bfSbMbpqBcvcfbKnLfEls_Plk732vH0LSNUqQ\n谷歌云盘(大陆需梯子访问)|https://drive.google.com/file/d/1CsR-9snRv0NHBYJahGbDRPe0geZtNYBg/view?usp=drive_link\n解压码|idoufu.com',0),(53,'33','[PC][妄想惑星]働く大人図鑑 -誰も異変に気づけない正式版[467M/網盤下载]',1,'','/upload_01/upload1/20250208/2025020815471242658.png','',' ','','温馨提示：\n1.配置梯子方法：https://gochatgptcn.com/252.html\n\n2.谷歌网盘限制问题：\n当你访问谷歌网盘链接时，遇到如下情况\n抱歉，您目前无法查看或下载此文件。\n最近查看或下载此文件的用户过多。请稍后再尝试访问此文件。如果您尝试访问的文件特别大或已与很多人共享，那么您最长可能需要等待 24 小时才能查看或下载该文件。如果您在 24 小时仍然无法访问文件，请与您的网域管理员联系。\n这是因为谷歌网盘限制了未登录用户，你登录账号再下载就可以了，没有谷歌账号的话，可以注册一个。\n\n3.解压码教程：\n全站采用境外大厂网盘，杜绝了链接失效的问题，只需要会简单的解压方法即可获取资源。\n苹果手机解压教程请看这里：https://jingyan.baidu.com/article/acf728fd8bc141f8e510a391.html\n安卓手机请看这里：\n先去应用商店安装一个解压专家。\n可以看这个教程：https://jingyan.baidu.com/article/f0e83a2543850b63e5910197.html',1,12,0,0,0,69771,0,488402,0,0,0,752,0,0,1,'2025-02-12 21:07:30','2025-02-12 21:08:04','2025-02-12 21:07:30',8721,'mega盘(大陆需梯子访问)|https://mega.nz/file/8Mx0FKpT#f5JTQBfiZlgtgIe-saFCUUMq3r1vxN58N_c5Tg5kPkQ\n谷歌云盘(大陆需梯子访问)|https://drive.google.com/file/d/13uftScK-dCGJWeQBspRGFocBmpEAlaAx/view?usp=drive_link\n解压码|idoufu.com',0),(54,'32','[PC]mugen全男格斗_有声像素动画[2.2G/網盤下载]',1,'','/upload_01/upload1/20250208/2025020815471154947.png','',' 1.mugen是什么？\n简单来说，就是类似于电子擂台的东西，不桐人设计的人物通过mugen可以在这个“舞台”（mugen）上进行对决。\n2.我要怎么看到那些动画？\n通过对应招式的输入，使你操控的人物发动H技。（只有部分人物有H技），下方附送的文件中有招式查询器帮助你更快的找到方法。\n3.图片预览（以个人喜欢的魅魔DDC作展示\n4.如何使用招式查询器\n①解压后打开出招表.EXE\n②蓝色界面左下角点击“打开”键\n③点击你的游戏根目录，找到”data”，进入后点开data文件下的select.def即可\n④左边人物列表会出现一堆看不懂的字符，往下滑动便可看见成排的整齐英文名，找到你想用的人物对应名字便可\n5.人物的简单介绍\n第一行到第四行的第四个为止都是shou，第四行第五个开始到第七行倒数第十二个开始均为非人\n第七行倒数第十二个开始为人类gong，直到第一个女性角色出现开始均为女性gong，\n再往下则是猎奇要素（慎重）\n目前楼楼玩到的DDC也只发现对纳兹，当麻，蜘蛛侠有较全的H互动 ','','温馨提示：\n1.配置梯子方法：https://gochatgptcn.com/252.html\n\n2.谷歌网盘限制问题：\n当你访问谷歌网盘链接时，遇到如下情况\n抱歉，您目前无法查看或下载此文件。\n最近查看或下载此文件的用户过多。请稍后再尝试访问此文件。如果您尝试访问的文件特别大或已与很多人共享，那么您最长可能需要等待 24 小时才能查看或下载该文件。如果您在 24 小时仍然无法访问文件，请与您的网域管理员联系。\n这是因为谷歌网盘限制了未登录用户，你登录账号再下载就可以了，没有谷歌账号的话，可以注册一个。\n\n3.解压码教程：\n全站采用境外大厂网盘，杜绝了链接失效的问题，只需要会简单的解压方法即可获取资源。\n苹果手机解压教程请看这里：https://jingyan.baidu.com/article/acf728fd8bc141f8e510a391.html\n安卓手机请看这里：\n先去应用商店安装一个解压专家。\n可以看这个教程：https://jingyan.baidu.com/article/f0e83a2543850b63e5910197.html',1,17,0,0,0,116002,0,696014,0,0,0,649,0,0,1,'2025-02-12 21:07:30','2025-02-12 21:08:04','2025-02-12 21:07:30',12889,'mega盘(大陆需梯子访问)|https://mega.nz/file/FE4HnLAR#rKr-dzT6IoCexvnLuvaDJ9o2cN0Q03XhfFkYgcp-3xI\n谷歌云盘(大陆需梯子访问)|https://drive.google.com/file/d/1Q6-dvOc9ZRlbIGe6OCwCnM2GFe0E6Erp/view?usp=drive_link\n解压码|idoufu.com',0),(55,'31','[PC/繁体中文]ガチムチでドスケベな家庭教師のお兄さんと過ごす夏[647M/網盤下载]',1,'','/upload_01/upload1/20250208/2025020815470815344.png','',' ','','温馨提示：\n1.配置梯子方法：https://gochatgptcn.com/252.html\n\n2.谷歌网盘限制问题：\n当你访问谷歌网盘链接时，遇到如下情况\n抱歉，您目前无法查看或下载此文件。\n最近查看或下载此文件的用户过多。请稍后再尝试访问此文件。如果您尝试访问的文件特别大或已与很多人共享，那么您最长可能需要等待 24 小时才能查看或下载该文件。如果您在 24 小时仍然无法访问文件，请与您的网域管理员联系。\n这是因为谷歌网盘限制了未登录用户，你登录账号再下载就可以了，没有谷歌账号的话，可以注册一个。\n\n3.解压码教程：\n全站采用境外大厂网盘，杜绝了链接失效的问题，只需要会简单的解压方法即可获取资源。\n苹果手机解压教程请看这里：https://jingyan.baidu.com/article/acf728fd8bc141f8e510a391.html\n安卓手机请看这里：\n先去应用商店安装一个解压专家。\n可以看这个教程：https://jingyan.baidu.com/article/f0e83a2543850b63e5910197.html',1,13,0,0,0,111209,0,889679,0,0,0,532,0,0,1,'2025-02-12 21:07:30','2025-02-12 21:08:04','2025-02-12 21:07:30',13901,'mega盘(大陆需梯子访问)|https://mega.nz/file/wE4BUbiC#cWZkYqw7M4yYRPaDEbRMqtn7NZ6LhZfN65ItwtXNlvg\n谷歌云盘(大陆需梯子访问)|https://drive.google.com/file/d/1jCxpOwRWFHiHurhULrews7w-MRFbxdNK/view?usp=drive_link\n解压码|idoufu.com',0),(56,'30','[PC/汉化]至上之空-Si-Nis-Kanto[1.15G/網盤下载]',1,'','/upload_01/upload1/20250208/2025020815470457061.png','',' AVG蔷薇向游戏里黑手党类的游戏真是不少啊, steal啊，沉默法则啊（揪心，一直没玩完)….. Si-Nis-Kanto里的黑手党之争，勾心斗角，尔虞我诈让我看的好过瘾啊，主角小受也还可以，比较别扭傲娇，但是有担当！ 讲真，这个游戏的配乐啊，画风啊都挺喜欢的，但是大量重复感的cg让人感觉不太走心啊！ 第一条推的线是卡尔洛线，使我大为惊艳，迅速拉高了我对这个游戏的期待值，然后满心欢喜的去推了另外两人雄午，艾西卡，就有点失望了。卡尔洛様，大好きだ！\n游戏一开始的时候，我就看到了蛮区东边势力黑莲会的三位（雄午，千冬，周），以及西边的势力大头ZENCA(卡尔洛，伊格，瓦伦丁)，这两对人马一上来就互懟，还怼出点暧昧出来。比如卡尔洛调戏炸毛的雄午，我还真希望有个结局是他俩一起，毕竟卡尔洛对雄午有着特殊的执着哟，貌似艾西卡线里两方最后结成同盟，同为首领的他们也许未来发展出点什么也未必不可能哟。\n','','温馨提示：\n1.配置梯子方法：https://gochatgptcn.com/252.html\n\n2.谷歌网盘限制问题：\n当你访问谷歌网盘链接时，遇到如下情况\n抱歉，您目前无法查看或下载此文件。\n最近查看或下载此文件的用户过多。请稍后再尝试访问此文件。如果您尝试访问的文件特别大或已与很多人共享，那么您最长可能需要等待 24 小时才能查看或下载该文件。如果您在 24 小时仍然无法访问文件，请与您的网域管理员联系。\n这是因为谷歌网盘限制了未登录用户，你登录账号再下载就可以了，没有谷歌账号的话，可以注册一个。\n\n3.解压码教程：\n全站采用境外大厂网盘，杜绝了链接失效的问题，只需要会简单的解压方法即可获取资源。\n苹果手机解压教程请看这里：https://jingyan.baidu.com/article/acf728fd8bc141f8e510a391.html\n安卓手机请看这里：\n先去应用商店安装一个解压专家。\n可以看这个教程：https://jingyan.baidu.com/article/f0e83a2543850b63e5910197.html',1,16,0,0,0,97596,0,975969,0,0,0,841,0,0,1,'2025-02-12 21:07:30','2025-02-12 21:08:04','2025-02-12 21:07:30',9759,'mega盘(大陆需梯子访问)|https://mega.nz/file/JNY0BBjb#NElr4b3MikTyg-cOu2--hAD6f7bDkbOiPTNZoaSCvpo\n谷歌云盘(大陆需梯子访问)|https://drive.google.com/file/d/13JxjTrWjW56gqOEhEayTBc_uPAZ5uKrI/view?usp=drive_link\n解压码|idoufu.com',0),(57,'29','[PC/汉化]异界转生 这次变成了传说中的勇者呢_IsekaiTenseikakkouke[725M/網盤下载]',1,'','/upload_01/upload1/20250208/2025020815470195701.png','',' ','','温馨提示：\n1.配置梯子方法：https://gochatgptcn.com/252.html\n\n2.谷歌网盘限制问题：\n当你访问谷歌网盘链接时，遇到如下情况\n抱歉，您目前无法查看或下载此文件。\n最近查看或下载此文件的用户过多。请稍后再尝试访问此文件。如果您尝试访问的文件特别大或已与很多人共享，那么您最长可能需要等待 24 小时才能查看或下载该文件。如果您在 24 小时仍然无法访问文件，请与您的网域管理员联系。\n这是因为谷歌网盘限制了未登录用户，你登录账号再下载就可以了，没有谷歌账号的话，可以注册一个。\n\n3.解压码教程：\n全站采用境外大厂网盘，杜绝了链接失效的问题，只需要会简单的解压方法即可获取资源。\n苹果手机解压教程请看这里：https://jingyan.baidu.com/article/acf728fd8bc141f8e510a391.html\n安卓手机请看这里：\n先去应用商店安装一个解压专家。\n可以看这个教程：https://jingyan.baidu.com/article/f0e83a2543850b63e5910197.html',1,15,0,0,0,94207,0,659455,0,0,0,709,0,0,1,'2025-02-12 21:07:30','2025-02-12 21:08:04','2025-02-12 21:07:30',11775,'mega盘(大陆需梯子访问)|https://mega.nz/file/RFw0WCpQ#B8B5YkrqoFNImQ-eH-DDOuAqF1RxA3FHgvFEr5NQDwA\n谷歌云盘(大陆需梯子访问)|https://drive.google.com/file/d/1Jyuf8O-MB8hBIFfOIP_kwPog9WyCPIFT/view?usp=drive_link\n解压码|idoufu.com',0),(58,'28','[PC/中文]坠入苍蓝色的深渊[534M/網盤下载]',1,'','/upload_01/upload1/20250208/2025020815465538166.png','',' 《坠入苍蓝色的深渊 v1.2》是由玩家自制的一款角色扮演游戏。坠入苍蓝色的深渊 v1.2讲述了一个少年在陌生的世界中寻找记忆的故事。坠入苍蓝色的深渊 v1.2游戏画面采用了复古的像素风，带给玩家别样的一下乐趣，有兴趣的玩家可以下载坠入苍蓝色的深渊 v1.2玩玩。\n游戏讲述了一个少年在一个不知名的世界中醒来，发现自己什么都不记得了，想不起来自己是谁，自己为什么在这里。于是他开始进行自己的冒险之旅，寻找自己的记忆。在这过程中他会遇到很多不同的朋友跟他一起冒险，同时还会遇到不同的敌人，需要跟他们展开激烈的战斗。那么最终这个少年能否找到自己的记忆呢？那就让我们拭目以待吧！ ','','温馨提示：\n1.配置梯子方法：https://gochatgptcn.com/252.html\n\n2.谷歌网盘限制问题：\n当你访问谷歌网盘链接时，遇到如下情况\n抱歉，您目前无法查看或下载此文件。\n最近查看或下载此文件的用户过多。请稍后再尝试访问此文件。如果您尝试访问的文件特别大或已与很多人共享，那么您最长可能需要等待 24 小时才能查看或下载该文件。如果您在 24 小时仍然无法访问文件，请与您的网域管理员联系。\n这是因为谷歌网盘限制了未登录用户，你登录账号再下载就可以了，没有谷歌账号的话，可以注册一个。\n\n3.解压码教程：\n全站采用境外大厂网盘，杜绝了链接失效的问题，只需要会简单的解压方法即可获取资源。\n苹果手机解压教程请看这里：https://jingyan.baidu.com/article/acf728fd8bc141f8e510a391.html\n安卓手机请看这里：\n先去应用商店安装一个解压专家。\n可以看这个教程：https://jingyan.baidu.com/article/f0e83a2543850b63e5910197.html',1,12,0,0,0,118354,0,828481,0,0,0,752,0,0,1,'2025-02-12 21:07:30','2025-02-12 21:08:04','2025-02-12 21:07:30',16907,'mega盘(大陆需梯子访问)|https://mega.nz/file/1AwhyZKT#sEg8ljO3VQcpNuqZUJ4aZ-4ZPzbbH2CNuZpUNojxsNs\n谷歌云盘(大陆需梯子访问)|https://drive.google.com/file/d/1nCgM4siqJN89Ev-bZuSemUZC0qBqse8q/view?usp=drive_link\n解压码|idoufu.com',0),(59,'27','[PC/中文]古剑奇谭同人_龙凤奇缘+特典[228M/網盤下载]',1,'','/upload_01/upload1/20250208/2025020815465370896.png','',' 想说非常喜欢游戏的剧本，非常的凄美婉约，膜拜闲锅大人….共2个结局，都说ED1算是HE，ED2比较BE…其实食完觉得ED1也很虐啊！！不过作为BE磁铁的吾辈，一上来就玩出ED2….虐的泪流满面差点散魂…(咳咳,夸张了点…)《龙凤》对《古剑》原作里他们两人相识和分离的过程进行了补完，最后给出了他们的结局。一个约定，伴随着悭臾从小小的水虺修成通天彻地的应龙，而最后他终于能带着长琴，乘奔御风，看尽山河风光……\n嘛，游戏非常的内涵，除了龙凤主西皮，还有火神水神的老夫老妻型，禺京摇光的不打不相识型，度厄司命的说不清道不明型，当然还有伏羲句芒，钟鼓师旷…（就算不萌龙凤也可以玩啊，这么多西皮总有一款适合您….喂喂….）好几对的戳泪点程度完全不下于主cp，而且与主线剧情融合得非常好!而且角色在这么短的剧情内也塑造的比较丰满，实在是厉害！ ','','温馨提示：\n1.配置梯子方法：https://gochatgptcn.com/252.html\n\n2.谷歌网盘限制问题：\n当你访问谷歌网盘链接时，遇到如下情况\n抱歉，您目前无法查看或下载此文件。\n最近查看或下载此文件的用户过多。请稍后再尝试访问此文件。如果您尝试访问的文件特别大或已与很多人共享，那么您最长可能需要等待 24 小时才能查看或下载该文件。如果您在 24 小时仍然无法访问文件，请与您的网域管理员联系。\n这是因为谷歌网盘限制了未登录用户，你登录账号再下载就可以了，没有谷歌账号的话，可以注册一个。\n\n3.解压码教程：\n全站采用境外大厂网盘，杜绝了链接失效的问题，只需要会简单的解压方法即可获取资源。\n苹果手机解压教程请看这里：https://jingyan.baidu.com/article/acf728fd8bc141f8e510a391.html\n安卓手机请看这里：\n先去应用商店安装一个解压专家。\n可以看这个教程：https://jingyan.baidu.com/article/f0e83a2543850b63e5910197.html',1,11,0,0,0,51780,0,414243,0,0,0,710,0,0,1,'2025-02-12 21:07:30','2025-02-12 21:08:04','2025-02-12 21:07:30',7397,'mega盘(大陆需梯子访问)|https://mega.nz/file/NBATSZgL#UD891K68zk-Wv2WwQS-VezSD08mShkSsnIJouHs0yhU\n谷歌云盘(大陆需梯子访问)|https://drive.google.com/file/d/1R0Y1LgyJlIV5QNL-sAV6eDYYBx0MZA7y/view?usp=drive_link\n解压码|idoufu.com',0),(60,'26','[PC/中文][虎兔同人]Next Dream WILD ROSE[24M/網盤下载]',1,'','/upload_01/upload1/20250208/2025020815465092996.png','',' ','','温馨提示：\n1.配置梯子方法：https://gochatgptcn.com/252.html\n\n2.谷歌网盘限制问题：\n当你访问谷歌网盘链接时，遇到如下情况\n抱歉，您目前无法查看或下载此文件。\n最近查看或下载此文件的用户过多。请稍后再尝试访问此文件。如果您尝试访问的文件特别大或已与很多人共享，那么您最长可能需要等待 24 小时才能查看或下载该文件。如果您在 24 小时仍然无法访问文件，请与您的网域管理员联系。\n这是因为谷歌网盘限制了未登录用户，你登录账号再下载就可以了，没有谷歌账号的话，可以注册一个。\n\n3.解压码教程：\n全站采用境外大厂网盘，杜绝了链接失效的问题，只需要会简单的解压方法即可获取资源。\n苹果手机解压教程请看这里：https://jingyan.baidu.com/article/acf728fd8bc141f8e510a391.html\n安卓手机请看这里：\n先去应用商店安装一个解压专家。\n可以看这个教程：https://jingyan.baidu.com/article/f0e83a2543850b63e5910197.html',1,14,0,0,0,73306,0,586448,0,0,0,1000,0,0,1,'2025-02-12 21:07:30','2025-02-12 21:08:04','2025-02-12 21:07:30',7330,'mega盘(大陆需梯子访问)|https://mega.nz/file/0cIHiSYJ#1YTkPt1SrGRTE_lnYNGdeowlA0ExIWYy0nf05WI86vw\n谷歌云盘(大陆需梯子访问)|https://drive.google.com/file/d/1JG1GYF3CpShALhyeeOghVXP1JfpzoBvk/view?usp=drive_link\n解压码|idoufu.com',0),(61,'25','[PC/中文]TB忘却の38[163M/網盤下载]',1,'','/upload_01/upload1/20250208/2025020815464814177.png','',' 忘却的38又称忘却の38(虎兔同人游戏)是一款BL向的恋爱养成游戏，这款游戏是经典动漫兔虎的同人作品，游戏主要围绕将动漫中的两位男主角间的情感故事而展开，而且这是兔虎同人系列的最新续作。\n这款游戏对动画的还原度还是相当的高，并且画质惊人的出色，是款非常精致作品。游戏中的故事发生在兔子与老虎打败了大魔王之后，两位基友的幸福生活，游戏的玩法依然采用了根据情境对选项做出判断的方式，玩家可根据自己的喜好，将剧情引至自己想要的结果上，不同剧情中穿插各种重口味CG。   ','','温馨提示：\n1.配置梯子方法：https://gochatgptcn.com/252.html\n\n2.谷歌网盘限制问题：\n当你访问谷歌网盘链接时，遇到如下情况\n抱歉，您目前无法查看或下载此文件。\n最近查看或下载此文件的用户过多。请稍后再尝试访问此文件。如果您尝试访问的文件特别大或已与很多人共享，那么您最长可能需要等待 24 小时才能查看或下载该文件。如果您在 24 小时仍然无法访问文件，请与您的网域管理员联系。\n这是因为谷歌网盘限制了未登录用户，你登录账号再下载就可以了，没有谷歌账号的话，可以注册一个。\n\n3.解压码教程：\n全站采用境外大厂网盘，杜绝了链接失效的问题，只需要会简单的解压方法即可获取资源。\n苹果手机解压教程请看这里：https://jingyan.baidu.com/article/acf728fd8bc141f8e510a391.html\n安卓手机请看这里：\n先去应用商店安装一个解压专家。\n可以看这个教程：https://jingyan.baidu.com/article/f0e83a2543850b63e5910197.html',1,11,0,0,0,45695,0,411257,0,0,0,649,0,0,1,'2025-02-12 21:07:30','2025-02-12 21:08:04','2025-02-12 21:07:30',6527,'mega盘(大陆需梯子访问)|https://mega.nz/file/9ZASFJCC#fELf1LwfklLjSRxYwwu3R0x-svD8io0gGNVnfzqN4T4\n谷歌云盘(大陆需梯子访问)|https://drive.google.com/file/d/1krPlJb8XsqTdYyTgfaQ4aZyadzbowXhl/view?usp=drive_link\n解压码|idoufu.com',0),(62,'24','[PC]invisible sign-イス(ＩＳＡ) 沉睡之森[1.16G/網盤下载]',1,'','/upload_01/upload1/20250208/2025020815463738974.png','',' 游戏日文名：invisible sign -イス- 眠れる森游戏中文名：invisible sign -ISA- 沉睡之森\n剧情介绍：by BL王道!\n相泽 舜(关 智一),有着奇异的灵力，\n年幼的舜因为无法承受这恐怖的灵力，发展出双重人格，\n能够看见死灵，但畏惧着死灵的舜\n和能够消灭死灵的瞬（喂，喂，这不是阴阳师吗）。\n三宅 真琴（石田 彰）舜青梅竹马的好友，\n其实是舜15年前被绑架的亲哥哥的灵魂，\n为了保护舜的身体不被另一人格瞬抢走，\n联合了舜的好友辽和凛共同抵制瞬。\n另一方面，第二人格的瞬结识了柏木 优斗（福山 润）\n一个和他一样，有着第二人格的人。\n二个有着一样经历的人，因为共同的烦恼，迅速成为好友。\n为了把舜的人格整合，统一起来，\n舜和真琴，瞬和优斗展开了激烈的斗争….. ','','温馨提示：\n1.配置梯子方法：https://gochatgptcn.com/252.html\n\n2.谷歌网盘限制问题：\n当你访问谷歌网盘链接时，遇到如下情况\n抱歉，您目前无法查看或下载此文件。\n最近查看或下载此文件的用户过多。请稍后再尝试访问此文件。如果您尝试访问的文件特别大或已与很多人共享，那么您最长可能需要等待 24 小时才能查看或下载该文件。如果您在 24 小时仍然无法访问文件，请与您的网域管理员联系。\n这是因为谷歌网盘限制了未登录用户，你登录账号再下载就可以了，没有谷歌账号的话，可以注册一个。\n\n3.解压码教程：\n全站采用境外大厂网盘，杜绝了链接失效的问题，只需要会简单的解压方法即可获取资源。\n苹果手机解压教程请看这里：https://jingyan.baidu.com/article/acf728fd8bc141f8e510a391.html\n安卓手机请看这里：\n先去应用商店安装一个解压专家。\n可以看这个教程：https://jingyan.baidu.com/article/f0e83a2543850b63e5910197.html',1,15,0,0,0,145996,0,875978,0,0,0,767,0,0,1,'2025-02-12 21:07:30','2025-02-12 21:08:04','2025-02-12 21:07:30',18249,'mega盘(大陆需梯子访问)|https://mega.nz/file/IcozTZBQ#f2oGQTvJ1IICPQxHxNCl6i7MQj4HJRlOe36jv0V503Q\n谷歌云盘(大陆需梯子访问)|https://drive.google.com/file/d/11zO3OE_cO9uTmtC1zcY4IXL9dL-viNrM/view?usp=drive_link\n解压码|idoufu.com',0),(63,'23','[PC/中文]前进！艾德按摩店！体验版[145M/網盤下载]',1,'','/upload_01/upload1/20250208/2025020815463560368.png','',' ','','温馨提示：\n1.配置梯子方法：https://gochatgptcn.com/252.html\n\n2.谷歌网盘限制问题：\n当你访问谷歌网盘链接时，遇到如下情况\n抱歉，您目前无法查看或下载此文件。\n最近查看或下载此文件的用户过多。请稍后再尝试访问此文件。如果您尝试访问的文件特别大或已与很多人共享，那么您最长可能需要等待 24 小时才能查看或下载该文件。如果您在 24 小时仍然无法访问文件，请与您的网域管理员联系。\n这是因为谷歌网盘限制了未登录用户，你登录账号再下载就可以了，没有谷歌账号的话，可以注册一个。\n\n3.解压码教程：\n全站采用境外大厂网盘，杜绝了链接失效的问题，只需要会简单的解压方法即可获取资源。\n苹果手机解压教程请看这里：https://jingyan.baidu.com/article/acf728fd8bc141f8e510a391.html\n安卓手机请看这里：\n先去应用商店安装一个解压专家。\n可以看这个教程：https://jingyan.baidu.com/article/f0e83a2543850b63e5910197.html',1,15,0,0,0,55823,0,558236,0,0,0,983,0,0,1,'2025-02-12 21:07:30','2025-02-12 21:08:04','2025-02-12 21:07:30',9303,'mega盘(大陆需梯子访问)|https://mega.nz/file/QMJAmLZC#CcRtR2VWllCMnLFcKWuaioSD2XLKME4SXpO8FB01W-M\n谷歌云盘(大陆需梯子访问)|https://drive.google.com/file/d/1beARI76895mFjaaaIjBCdiHjWIZf5q9m/view?usp=drive_link\n解压码|idoufu.com',0),(64,'22','[PC/汉化]腹が減っては雄交尾もできぬ[完整版][656M/網盤下载]',1,'','/upload_01/upload1/20250208/2025020815463219108.png','',' 感觉最赞的是忍者小哥哥，果然只有r/18游戏里的忍者才能穿出忍者服的精髓啊。妄想大大的动图也是一如既往的戳到G碘。\n话说回来这游戏里的宝箱明显是打发叫花子的吧，开个箱100游戏币都没有，然而强迫症的我还是一个个箱子开了下去。无限拖进度，导致现在都还没跟忍者小葛格深入交流。然后遇怪的地图是真心多，一个打怪的塔作者真的就做出7层来也是够敬业哦（累死我了）。\n小建议就是前期可以尽量赶进度，不用刻意练级开箱子，boss还是比较好刚过去的，要练级的话等遇上忍者小葛格，他大概18级左右能学全屏aoc雷遁术，然后从怪物后背偷袭可以抢先额外行动一回合，基本无伤刷怪，速度杠杠滴。 ','','温馨提示：\n1.配置梯子方法：https://gochatgptcn.com/252.html\n\n2.谷歌网盘限制问题：\n当你访问谷歌网盘链接时，遇到如下情况\n抱歉，您目前无法查看或下载此文件。\n最近查看或下载此文件的用户过多。请稍后再尝试访问此文件。如果您尝试访问的文件特别大或已与很多人共享，那么您最长可能需要等待 24 小时才能查看或下载该文件。如果您在 24 小时仍然无法访问文件，请与您的网域管理员联系。\n这是因为谷歌网盘限制了未登录用户，你登录账号再下载就可以了，没有谷歌账号的话，可以注册一个。\n\n3.解压码教程：\n全站采用境外大厂网盘，杜绝了链接失效的问题，只需要会简单的解压方法即可获取资源。\n苹果手机解压教程请看这里：https://jingyan.baidu.com/article/acf728fd8bc141f8e510a391.html\n安卓手机请看这里：\n先去应用商店安装一个解压专家。\n可以看这个教程：https://jingyan.baidu.com/article/f0e83a2543850b63e5910197.html',1,13,0,0,0,57741,0,461929,0,0,0,544,0,0,1,'2025-02-12 21:07:30','2025-02-12 21:08:04','2025-02-12 21:07:30',7217,'mega盘(大陆需梯子访问)|https://mega.nz/file/sNBQCSLD#M5kSTGg_CfG5vJn1d46J7CYk2BcKI8vgpfZQKIoPCWY\n谷歌云盘(大陆需梯子访问)|https://drive.google.com/file/d/1gIhrKC6gVGb_R-kpJYeMupIg6g4IdzEP/view?usp=drive_link\n解压码|idoufu.com',0),(65,'21','[安卓/PC/汉化]梦魇境界-逃避现实 v1.296[582M/網盤下载]',1,'','/upload_01/upload1/20250208/2025020815463048008.jpeg','',' 通过扔骰子来进行流程的奇特游戏，有点DND的味道 游戏主界面点击Load—Preference即可更改语言（电脑版开始以后摁esc也可以，此方法主要是帮助安卓） ','','温馨提示：\n1.配置梯子方法：https://gochatgptcn.com/252.html\n\n2.谷歌网盘限制问题：\n当你访问谷歌网盘链接时，遇到如下情况\n抱歉，您目前无法查看或下载此文件。\n最近查看或下载此文件的用户过多。请稍后再尝试访问此文件。如果您尝试访问的文件特别大或已与很多人共享，那么您最长可能需要等待 24 小时才能查看或下载该文件。如果您在 24 小时仍然无法访问文件，请与您的网域管理员联系。\n这是因为谷歌网盘限制了未登录用户，你登录账号再下载就可以了，没有谷歌账号的话，可以注册一个。\n\n3.解压码教程：\n全站采用境外大厂网盘，杜绝了链接失效的问题，只需要会简单的解压方法即可获取资源。\n苹果手机解压教程请看这里：https://jingyan.baidu.com/article/acf728fd8bc141f8e510a391.html\n安卓手机请看这里：\n先去应用商店安装一个解压专家。\n可以看这个教程：https://jingyan.baidu.com/article/f0e83a2543850b63e5910197.html',1,10,0,0,0,150982,0,905894,0,0,0,651,0,0,1,'2025-02-12 21:07:30','2025-02-12 21:08:04','2025-02-12 21:07:30',16775,'mega盘(大陆需梯子访问)|https://mega.nz/file/0RRgibDD#q74zEQkAD5Lv2lM0nHC-bMLlwU-JcKMMH-EtK2lVGo4\n谷歌云盘(大陆需梯子访问)|https://drive.google.com/file/d/1IdRnm8iiEB6RCwklQr5GKi5JqYMdMayV/view?usp=drive_link\n解压码|idoufu.com',0),(66,'20','[安卓/PC/汉化]欲望奥德赛 v0.26.1[406M/網盤下载]',1,'','/upload_01/upload1/20250208/2025020815462881102.png','',' 剧情推进文字探索类游戏     ','','温馨提示：\n1.配置梯子方法：https://gochatgptcn.com/252.html\n\n2.谷歌网盘限制问题：\n当你访问谷歌网盘链接时，遇到如下情况\n抱歉，您目前无法查看或下载此文件。\n最近查看或下载此文件的用户过多。请稍后再尝试访问此文件。如果您尝试访问的文件特别大或已与很多人共享，那么您最长可能需要等待 24 小时才能查看或下载该文件。如果您在 24 小时仍然无法访问文件，请与您的网域管理员联系。\n这是因为谷歌网盘限制了未登录用户，你登录账号再下载就可以了，没有谷歌账号的话，可以注册一个。\n\n3.解压码教程：\n全站采用境外大厂网盘，杜绝了链接失效的问题，只需要会简单的解压方法即可获取资源。\n苹果手机解压教程请看这里：https://jingyan.baidu.com/article/acf728fd8bc141f8e510a391.html\n安卓手机请看这里：\n先去应用商店安装一个解压专家。\n可以看这个教程：https://jingyan.baidu.com/article/f0e83a2543850b63e5910197.html',1,12,0,0,0,87272,0,610909,0,0,0,682,0,0,1,'2025-02-12 21:07:30','2025-02-12 21:07:43','2025-02-12 21:07:30',14545,'mega盘(大陆需梯子访问)|https://mega.nz/file/kcpwUQ7S#EzbXp7Jv1q2GF747Q1I7b5tKtansZfsJ7apuZmd6uC0\n谷歌云盘(大陆需梯子访问)|https://drive.google.com/file/d/1qGcHukawrD-DsZpBvx5tky0u7JH921XX/view?usp=drive_link\n解压码|idoufu.com',0),(67,'19','[安卓/PC/汉化]異海 ―ORPHAN’S CRADLE v2.0[796M/網盤下载]',1,'','/upload_01/upload1/20250208/2025020815462863923.png','',' Oumi，一个拥有看到不存在事物能力的年轻人。\n在失去了他唯一的直系亲属母亲后，他回到了她的出生地，以履行他最后的职责。 生活在宇智波岛，一个闭海的岛屿。\n他与同船的朋友以及岛上人民的互动，温柔地融化了他的心。 ……然而，闭岛之谜却一点一点地向他袭来…… 相当古早的一部作品，不论代码还是画风都非常古老，看了下居然是win 7 和 xp 时代的游戏 单看代码是千万字符级别的文本，不过实际上因为作品三代版本更迭，很多地方是文本复用导致的，实际文本量大概在五百到七百万左右   ','','温馨提示：\n1.配置梯子方法：https://gochatgptcn.com/252.html\n\n2.谷歌网盘限制问题：\n当你访问谷歌网盘链接时，遇到如下情况\n抱歉，您目前无法查看或下载此文件。\n最近查看或下载此文件的用户过多。请稍后再尝试访问此文件。如果您尝试访问的文件特别大或已与很多人共享，那么您最长可能需要等待 24 小时才能查看或下载该文件。如果您在 24 小时仍然无法访问文件，请与您的网域管理员联系。\n这是因为谷歌网盘限制了未登录用户，你登录账号再下载就可以了，没有谷歌账号的话，可以注册一个。\n\n3.解压码教程：\n全站采用境外大厂网盘，杜绝了链接失效的问题，只需要会简单的解压方法即可获取资源。\n苹果手机解压教程请看这里：https://jingyan.baidu.com/article/acf728fd8bc141f8e510a391.html\n安卓手机请看这里：\n先去应用商店安装一个解压专家。\n可以看这个教程：https://jingyan.baidu.com/article/f0e83a2543850b63e5910197.html',1,10,0,0,0,49672,0,496729,0,0,0,686,0,0,1,'2025-02-12 21:07:30','2025-02-12 21:07:43','2025-02-12 21:07:30',7096,'mega盘(大陆需梯子访问)|https://mega.nz/file/AF5nxbZS#Uq3yeT4uC_5equdhhf2zGzFTYOvjwAyxVStxTUvfLDo\n谷歌云盘(大陆需梯子访问)|https://drive.google.com/file/d/1NKad_dNsF1d5WNweAxDbowknKc9S6c4C/view?usp=drive_link,%20https://drive.google.com/file/d/1DEPHJV1F7e9Z-qho8Ythts3t6MyYHTBI/view?usp=drive_link\n解压码|idoufu.com',0),(68,'18','[安卓/PC/汉化]亲爱的怪物 v1.1.1[226M/網盤下载]',1,'','/upload_01/upload1/20250208/2025020815462625359.png','','  有点母拉拉的画风，不过看CG里面的阿努比斯和orc还蛮对胃口的 简介： 艾伦以为他已经弄清楚了自己的人生。 他刚刚大学毕业，准备去医学院。 然后一封陌生的信从他素不相识的祖父那里寄来。 他被拉上了一条新路，通往一座陌生的宅邸，里面充满了更奇怪的生物。  这些幻想生物根本不应该存在——然而，他们却有些熟悉。 艾伦没有时间去探究谜团，但当他试图离开时，他意识到自己被困住了，魔法怪物也被困住了！ 艾伦必须通过与其中一种幻想生物“结合”来学习魔法，从而解开自己过去的谜团。 否则他永远都无法获得自由！  这是一个成人专属的故事，讲述了人与怪物之间的魔法和爱情，艾伦会逃离魔法宅邸，继续他计划的生活吗——还是他的整个世界都被颠覆了？     PS:安卓版试了下可以运行，但是如果出现报错请尽量不要开启精灵动画（即开始界面选择的那个玩意），如若依然大量报错那就是寄    ','','温馨提示：\n1.配置梯子方法：https://gochatgptcn.com/252.html\n\n2.谷歌网盘限制问题：\n当你访问谷歌网盘链接时，遇到如下情况\n抱歉，您目前无法查看或下载此文件。\n最近查看或下载此文件的用户过多。请稍后再尝试访问此文件。如果您尝试访问的文件特别大或已与很多人共享，那么您最长可能需要等待 24 小时才能查看或下载该文件。如果您在 24 小时仍然无法访问文件，请与您的网域管理员联系。\n这是因为谷歌网盘限制了未登录用户，你登录账号再下载就可以了，没有谷歌账号的话，可以注册一个。\n\n3.解压码教程：\n全站采用境外大厂网盘，杜绝了链接失效的问题，只需要会简单的解压方法即可获取资源。\n苹果手机解压教程请看这里：https://jingyan.baidu.com/article/acf728fd8bc141f8e510a391.html\n安卓手机请看这里：\n先去应用商店安装一个解压专家。\n可以看这个教程：https://jingyan.baidu.com/article/f0e83a2543850b63e5910197.html',1,13,0,0,0,72582,0,508080,0,0,0,753,0,0,1,'2025-02-12 21:07:30','2025-02-12 21:07:43','2025-02-12 21:07:30',7258,'mega盘(大陆需梯子访问)|https://mega.nz/file/VJRwgIyL#LjKefIaWECQ3kai55FjB0u3ceSCqtJ764btBxyIq-vo\n谷歌云盘(大陆需梯子访问)|https://drive.google.com/file/d/1lyZM9PFJ-yjRszGSHMq2ATtCM1A8dGf9/view?usp=drive_link\n解压码|idoufu.com',0),(69,'17','[安卓/PC/汉化]Ergi v0.5.18[840M/網盤下载]',1,'','/upload_01/upload1/20250208/2025020815462492033.png','','   制作相当精良的老东西了，\n这是压制版本，很有可能有某些潜在问题，优点就是体积小，原版实在太大了，有10个G 安卓版完全没测试过，很有可能存在某些严重问题……      ','','温馨提示：\n1.配置梯子方法：https://gochatgptcn.com/252.html\n\n2.谷歌网盘限制问题：\n当你访问谷歌网盘链接时，遇到如下情况\n抱歉，您目前无法查看或下载此文件。\n最近查看或下载此文件的用户过多。请稍后再尝试访问此文件。如果您尝试访问的文件特别大或已与很多人共享，那么您最长可能需要等待 24 小时才能查看或下载该文件。如果您在 24 小时仍然无法访问文件，请与您的网域管理员联系。\n这是因为谷歌网盘限制了未登录用户，你登录账号再下载就可以了，没有谷歌账号的话，可以注册一个。\n\n3.解压码教程：\n全站采用境外大厂网盘，杜绝了链接失效的问题，只需要会简单的解压方法即可获取资源。\n苹果手机解压教程请看这里：https://jingyan.baidu.com/article/acf728fd8bc141f8e510a391.html\n安卓手机请看这里：\n先去应用商店安装一个解压专家。\n可以看这个教程：https://jingyan.baidu.com/article/f0e83a2543850b63e5910197.html',1,14,0,0,0,43681,0,305770,0,0,0,674,0,0,1,'2025-02-12 21:07:30','2025-02-12 21:07:43','2025-02-12 21:07:30',4853,'mega盘(大陆需梯子访问)|https://mega.nz/file/tE5HmSpQ#DewhTWjAEazXAG1RN_REapa6M1HfXEg67Ta76WubmQQ\n谷歌云盘(大陆需梯子访问)|https://drive.google.com/file/d/1sEfAts4M8Ugaz4gF37QP30jpeIE59KOv/view?usp=drive_link\n解压码|idoufu.com',0),(70,'16','[安卓/PC/汉化]雄性猎人 v3.3[1.2G/網盤下载]',1,'','/upload_01/upload1/20250208/2025020815462271197.png','',' 在这款将开放世界元素与图画小说相结合的游戏中，你的目标是帮助实现我们主角的伟大幻想……将镇上所有最英俊、最肌肉的男人据为己有。 为此，您将探索这座城市，在最多样化的场景和情况下寻找猎物。您还可以在电视和互联网上找到您的目标……世界是您狩猎的广阔开放领域。 但显然这不是一件容易的事。有些猎物比其他猎物要复杂得多……这需要大量的对话、创造力、努力、风险，当然还有金钱，为什么不呢？ 每个人的故事都可能有几条路可走。做事的方式没有正确或错误之分……只是你的行为会导致不同的后果……而不仅仅是一种好的和一种坏的。   ','','温馨提示：\n1.配置梯子方法：https://gochatgptcn.com/252.html\n\n2.谷歌网盘限制问题：\n当你访问谷歌网盘链接时，遇到如下情况\n抱歉，您目前无法查看或下载此文件。\n最近查看或下载此文件的用户过多。请稍后再尝试访问此文件。如果您尝试访问的文件特别大或已与很多人共享，那么您最长可能需要等待 24 小时才能查看或下载该文件。如果您在 24 小时仍然无法访问文件，请与您的网域管理员联系。\n这是因为谷歌网盘限制了未登录用户，你登录账号再下载就可以了，没有谷歌账号的话，可以注册一个。\n\n3.解压码教程：\n全站采用境外大厂网盘，杜绝了链接失效的问题，只需要会简单的解压方法即可获取资源。\n苹果手机解压教程请看这里：https://jingyan.baidu.com/article/acf728fd8bc141f8e510a391.html\n安卓手机请看这里：\n先去应用商店安装一个解压专家。\n可以看这个教程：https://jingyan.baidu.com/article/f0e83a2543850b63e5910197.html',1,11,0,0,0,47408,0,474080,0,0,0,720,0,0,1,'2025-02-12 21:07:30','2025-02-12 21:07:43','2025-02-12 21:07:30',7901,'mega盘(大陆需梯子访问)|https://mega.nz/file/NF4yQJZB#ZxIjj_4uUVKe6giQpEXLJQnJnoJrsvgYRavcYiiQlIQ\n谷歌云盘(大陆需梯子访问)|https://drive.google.com/file/d/1MSCRk3OEKR0i5Q7hsxgEQ6uywp-wLhp7/view?usp=drive_link\n解压码|idoufu.com',0),(71,'15','[PC/汉化]Geeks VS Superheros V2.5.6 正篇+番外篇[817M/網盤下载]',1,'','/upload_01/upload1/20250208/2025020815461927354.png','',' 勇敢队长设法逃离了两个极客凌乱的公寓，但却没有受到他们对他的影响…… 两个极客迫切地想要找回他们最喜欢的超级英雄，想尽一切办法修复手中破损的洗脑装置，但他们很快就明白了成为恶棍比漫画书中看起来要困难得多……   ','','温馨提示：\n1.配置梯子方法：https://gochatgptcn.com/252.html\n\n2.谷歌网盘限制问题：\n当你访问谷歌网盘链接时，遇到如下情况\n抱歉，您目前无法查看或下载此文件。\n最近查看或下载此文件的用户过多。请稍后再尝试访问此文件。如果您尝试访问的文件特别大或已与很多人共享，那么您最长可能需要等待 24 小时才能查看或下载该文件。如果您在 24 小时仍然无法访问文件，请与您的网域管理员联系。\n这是因为谷歌网盘限制了未登录用户，你登录账号再下载就可以了，没有谷歌账号的话，可以注册一个。\n\n3.解压码教程：\n全站采用境外大厂网盘，杜绝了链接失效的问题，只需要会简单的解压方法即可获取资源。\n苹果手机解压教程请看这里：https://jingyan.baidu.com/article/acf728fd8bc141f8e510a391.html\n安卓手机请看这里：\n先去应用商店安装一个解压专家。\n可以看这个教程：https://jingyan.baidu.com/article/f0e83a2543850b63e5910197.html',1,13,0,0,0,39812,0,398128,0,0,0,753,0,0,1,'2025-02-12 21:07:30','2025-02-12 21:07:43','2025-02-12 21:07:30',5687,'mega盘(大陆需梯子访问)|https://mega.nz/file/gdp2BAhb#LlCR7mGa7iLrmCYy7QeJt8ygGhzV7BTR5cBmievgL4I\n谷歌云盘(大陆需梯子访问)|https://drive.google.com/file/d/1VvokihLjtO1Xj91EzJAg3tO9PY3qEhgi/view?usp=drive_link\n解压码|idoufu.com',0),(72,'14','[安卓/PC/汉化]Medieval Times_中世纪时代 第一部 重制版1-5章[1.35G/網盤下载]',1,'','/upload_01/upload1/20250208/2025020815461791287.png','',' 看起来是个超大制作的游戏     ','','温馨提示：\n1.配置梯子方法：https://gochatgptcn.com/252.html\n\n2.谷歌网盘限制问题：\n当你访问谷歌网盘链接时，遇到如下情况\n抱歉，您目前无法查看或下载此文件。\n最近查看或下载此文件的用户过多。请稍后再尝试访问此文件。如果您尝试访问的文件特别大或已与很多人共享，那么您最长可能需要等待 24 小时才能查看或下载该文件。如果您在 24 小时仍然无法访问文件，请与您的网域管理员联系。\n这是因为谷歌网盘限制了未登录用户，你登录账号再下载就可以了，没有谷歌账号的话，可以注册一个。\n\n3.解压码教程：\n全站采用境外大厂网盘，杜绝了链接失效的问题，只需要会简单的解压方法即可获取资源。\n苹果手机解压教程请看这里：https://jingyan.baidu.com/article/acf728fd8bc141f8e510a391.html\n安卓手机请看这里：\n先去应用商店安装一个解压专家。\n可以看这个教程：https://jingyan.baidu.com/article/f0e83a2543850b63e5910197.html',1,11,0,0,0,99416,0,894748,0,0,0,795,0,0,1,'2025-02-12 21:07:30','2025-02-12 21:07:43','2025-02-12 21:07:30',14202,'mega盘(大陆需梯子访问)|https://mega.nz/file/YBBSgSSY#_8kxx4CGFrPRLipSATUepRSMs4U1Ynp8hArQvZyr34A\n谷歌云盘(大陆需梯子访问)|https://drive.google.com/file/d/10AjC31IEkpfaIFMabTJETC3-ttV-jJTz/view?usp=drive_link\n解压码|idoufu.com',0),(73,'13','[PC/汉化]まほろばサバイバル 荒岛生存 v1.00[470M/網盤下载]',1,'','/upload_01/upload1/20250208/2025020815461534383.png','',' 已完结的游戏，讲的故事如标题所示，坠机后在岛上和原住民及其他遇难人士一起卓艾最后跑路的故事 ','','温馨提示：\n1.配置梯子方法：https://gochatgptcn.com/252.html\n\n2.谷歌网盘限制问题：\n当你访问谷歌网盘链接时，遇到如下情况\n抱歉，您目前无法查看或下载此文件。\n最近查看或下载此文件的用户过多。请稍后再尝试访问此文件。如果您尝试访问的文件特别大或已与很多人共享，那么您最长可能需要等待 24 小时才能查看或下载该文件。如果您在 24 小时仍然无法访问文件，请与您的网域管理员联系。\n这是因为谷歌网盘限制了未登录用户，你登录账号再下载就可以了，没有谷歌账号的话，可以注册一个。\n\n3.解压码教程：\n全站采用境外大厂网盘，杜绝了链接失效的问题，只需要会简单的解压方法即可获取资源。\n苹果手机解压教程请看这里：https://jingyan.baidu.com/article/acf728fd8bc141f8e510a391.html\n安卓手机请看这里：\n先去应用商店安装一个解压专家。\n可以看这个教程：https://jingyan.baidu.com/article/f0e83a2543850b63e5910197.html',1,18,0,0,0,63294,0,443060,0,0,0,996,0,0,1,'2025-02-12 21:07:30','2025-02-12 21:07:43','2025-02-12 21:07:30',6329,'mega盘(大陆需梯子访问)|https://mega.nz/file/ZZBjRRTI#LpSodJpRU-68Xb2HbcswZI1cmPDxRQUMxVbhm_rGky0\n谷歌云盘(大陆需梯子访问)|https://drive.google.com/file/d/1VoBxP5X5pkfW7hlbaReKSpoDtBjAgOEy/view?usp=drive_link\n解压码|idoufu.com',0),(74,'12','[PC/汉化][UGCP]冲绳奴隶岛[298M/網盤下载]',1,'','/upload_01/upload1/20250208/2025020815461339165.png','',' 游戏简介： 久盖岛…对于冲绳观光岛、水座岛来说，\n它在距离水座岛乘快船30分钟的程度才能到达的位置，是一座很小很小的离岛。 周长约5.2km。海拔55m。基本上没有经过人为改造、残留了自然形态的岛屿。 一切建造物都并不存在，唯有海岸边的洞穴。\n这座岛上唯一的住民只有山羊、鸟、小动物们。\n有些阴郁的繁茂的野生植物。 是无人岛。 谁都不住在那里。也没有观光的机能，这座很小很小的岛曾经全部属于一位男性。 那便是、「您」。 这座很小很小的无人岛…被白色的海滨与青色的大海包裹着，无人停居… 天然的「牢狱」。 这是，将这座岛屿作为世界上最棒的疗养地开发的您的物语。 米青液的白色与绝望的青色渲染出的，肉体的疗养圣地。 ','','温馨提示：\n1.配置梯子方法：https://gochatgptcn.com/252.html\n\n2.谷歌网盘限制问题：\n当你访问谷歌网盘链接时，遇到如下情况\n抱歉，您目前无法查看或下载此文件。\n最近查看或下载此文件的用户过多。请稍后再尝试访问此文件。如果您尝试访问的文件特别大或已与很多人共享，那么您最长可能需要等待 24 小时才能查看或下载该文件。如果您在 24 小时仍然无法访问文件，请与您的网域管理员联系。\n这是因为谷歌网盘限制了未登录用户，你登录账号再下载就可以了，没有谷歌账号的话，可以注册一个。\n\n3.解压码教程：\n全站采用境外大厂网盘，杜绝了链接失效的问题，只需要会简单的解压方法即可获取资源。\n苹果手机解压教程请看这里：https://jingyan.baidu.com/article/acf728fd8bc141f8e510a391.html\n安卓手机请看这里：\n先去应用商店安装一个解压专家。\n可以看这个教程：https://jingyan.baidu.com/article/f0e83a2543850b63e5910197.html',1,15,0,0,0,139697,0,838185,0,0,0,550,0,0,1,'2025-02-12 21:07:30','2025-02-12 21:07:43','2025-02-12 21:07:30',17462,'mega盘(大陆需梯子访问)|https://mega.nz/file/scZF3LIb#09Xrzx2jqUFBt4W2d0UG-XQdvkgNUp86rL8S4TzxVtM\n谷歌云盘(大陆需梯子访问)|https://drive.google.com/file/d/1O5jMYiuS1TEcq7Ic2d3wPBlvqtAJ8um3/view?usp=drive_link\n解压码|idoufu.com',0),(75,'11','[PC/汉化]NO, THANK YOU!!![2.8G/網盤下载]',1,'','/upload_01/upload1/20250208/2025020815461136397.png','',' 比较少见的主攻游戏，关于被车撞了的失忆主角开始在一家暗中从事侦探社工作的酒吧打工的故事。 玩法上除了常见选择肢外还有一个类似折旗的操作，在特殊事件时会出现，根据选择改变后续剧情。 可攻略角色4人，随着攻略角色的不同，事件的解决程度也会不同。每条线都有部分真相，全部推完后才见事件全貌。 ','','温馨提示：\n1.配置梯子方法：https://gochatgptcn.com/252.html\n\n2.谷歌网盘限制问题：\n当你访问谷歌网盘链接时，遇到如下情况\n抱歉，您目前无法查看或下载此文件。\n最近查看或下载此文件的用户过多。请稍后再尝试访问此文件。如果您尝试访问的文件特别大或已与很多人共享，那么您最长可能需要等待 24 小时才能查看或下载该文件。如果您在 24 小时仍然无法访问文件，请与您的网域管理员联系。\n这是因为谷歌网盘限制了未登录用户，你登录账号再下载就可以了，没有谷歌账号的话，可以注册一个。\n\n3.解压码教程：\n全站采用境外大厂网盘，杜绝了链接失效的问题，只需要会简单的解压方法即可获取资源。\n苹果手机解压教程请看这里：https://jingyan.baidu.com/article/acf728fd8bc141f8e510a391.html\n安卓手机请看这里：\n先去应用商店安装一个解压专家。\n可以看这个教程：https://jingyan.baidu.com/article/f0e83a2543850b63e5910197.html',1,14,0,0,0,56458,0,564580,0,0,0,851,0,0,1,'2025-02-12 21:07:30','2025-02-12 21:07:43','2025-02-12 21:07:30',8065,'mega盘(大陆需梯子访问)|https://mega.nz/file/wEo1FBYI#Yf9shldcHj36sbS2wOGNGB8Pq0aqj9I3a30pzGqy2g4\n谷歌云盘(大陆需梯子访问)|https://drive.google.com/file/d/1fTXAYXESljjafd-amWI37ln1j6BGDOrZ/view?usp=drive_link\n解压码|idoufu.com',0),(76,'10','[安卓/中文]杂技演员岩[16M/網盤下载]',1,'','/upload_01/upload1/20250208/2025020815460934977.jpeg','','  ','','温馨提示：\n1.配置梯子方法：https://gochatgptcn.com/252.html\n\n2.谷歌网盘限制问题：\n当你访问谷歌网盘链接时，遇到如下情况\n抱歉，您目前无法查看或下载此文件。\n最近查看或下载此文件的用户过多。请稍后再尝试访问此文件。如果您尝试访问的文件特别大或已与很多人共享，那么您最长可能需要等待 24 小时才能查看或下载该文件。如果您在 24 小时仍然无法访问文件，请与您的网域管理员联系。\n这是因为谷歌网盘限制了未登录用户，你登录账号再下载就可以了，没有谷歌账号的话，可以注册一个。\n\n3.解压码教程：\n全站采用境外大厂网盘，杜绝了链接失效的问题，只需要会简单的解压方法即可获取资源。\n苹果手机解压教程请看这里：https://jingyan.baidu.com/article/acf728fd8bc141f8e510a391.html\n安卓手机请看这里：\n先去应用商店安装一个解压专家。\n可以看这个教程：https://jingyan.baidu.com/article/f0e83a2543850b63e5910197.html',1,16,0,0,0,121320,0,849243,0,0,0,623,0,0,1,'2025-02-12 21:07:30','2025-02-12 21:07:43','2025-02-12 21:07:30',20220,'mega盘(大陆需梯子访问)|https://mega.nz/file/BcRDmDzD#QZ6cDasbVFuD0XbKiakfDIQe6InFHKilC5_KU8RMi44\n谷歌云盘(大陆需梯子访问)|https://drive.google.com/file/d/1hAPcVuxSUq3ski2SdRjZ8Dr7ccZ9fWfL/view?usp=drive_link\n解压码|idoufu.com',0),(77,'9','[安卓/PC/汉化]更新-欲望奥德赛 v0.31.1[509M/網盤下载]',1,'','/upload_01/upload1/20250208/2025020815460742659.png','',' 剧情推进文字探索类游戏 官方更新内容：\n– 哥布林柠檬水剧情：在与Vrigil交谈后山谷漫步中的剧情\n– 现在在19:00-00:00在熊猫旅馆找到爱德华国王并交谈，如果存在控制关系，对话会改变\n– 如果顺从于爱德华国王且资金低于1000，完成哥布林任务后可以要求支付大学学费\n– Eldor周围闲逛新增三个通用场景及六个附带条件的场景\n– 巡查哥布林营地有新场景\n– Damien现在可以从哥布林营地解救了\n– 新增怪癖：粗暴的身体游戏（默认关闭）\n– 新增怪癖：永久颜射效果（默认关闭），每当被射在脸上，屏幕都会覆盖一层精液，至多覆盖八层，直到清洗自己前都会一直存在\n– 如果至少有四名战斗队友，你可以将队员送给哥布林来获得胜利 更新跟进修正如下：\n– 修正少量翻译未显示问题\n– 修正部分选项未翻译问题\n– 补正新增物品及人物的翻译 注意：经查明，新旧核心实际除存档无法向上兼容以外并未存在任何其他冲突，因此自此版本之后，安卓版本仅提供新核心版本作为通用版本，旧核心版本彻底停止制作     ','','温馨提示：\n1.配置梯子方法：https://gochatgptcn.com/252.html\n\n2.谷歌网盘限制问题：\n当你访问谷歌网盘链接时，遇到如下情况\n抱歉，您目前无法查看或下载此文件。\n最近查看或下载此文件的用户过多。请稍后再尝试访问此文件。如果您尝试访问的文件特别大或已与很多人共享，那么您最长可能需要等待 24 小时才能查看或下载该文件。如果您在 24 小时仍然无法访问文件，请与您的网域管理员联系。\n这是因为谷歌网盘限制了未登录用户，你登录账号再下载就可以了，没有谷歌账号的话，可以注册一个。\n\n3.解压码教程：\n全站采用境外大厂网盘，杜绝了链接失效的问题，只需要会简单的解压方法即可获取资源。\n苹果手机解压教程请看这里：https://jingyan.baidu.com/article/acf728fd8bc141f8e510a391.html\n安卓手机请看这里：\n先去应用商店安装一个解压专家。\n可以看这个教程：https://jingyan.baidu.com/article/f0e83a2543850b63e5910197.html',1,15,0,0,0,115738,0,810168,0,0,0,922,0,0,1,'2025-02-12 21:07:30','2025-02-12 21:07:43','2025-02-12 21:07:30',11573,'mega盘(大陆需梯子访问)|https://mega.nz/file/EdgSwTiZ#0vS5b2tj3GPIKNJN8CE9Lqnguy83X4NxQ58gS3qVB_4\n谷歌云盘(大陆需梯子访问)|https://drive.google.com/file/d/1ojGdscO7ucdIsHDL0zvj9qL-id3IZeij/view?usp=drive_link\n解压码|idoufu.com',0),(78,'8','[PC/中文]Hikoto After School V1.1.2[151M/網盤下载]',1,'','/upload_01/upload1/20250208/2025020815460646066.png','',' 互动小游戏。 ','','温馨提示：\n1.配置梯子方法：https://gochatgptcn.com/252.html\n\n2.谷歌网盘限制问题：\n当你访问谷歌网盘链接时，遇到如下情况\n抱歉，您目前无法查看或下载此文件。\n最近查看或下载此文件的用户过多。请稍后再尝试访问此文件。如果您尝试访问的文件特别大或已与很多人共享，那么您最长可能需要等待 24 小时才能查看或下载该文件。如果您在 24 小时仍然无法访问文件，请与您的网域管理员联系。\n这是因为谷歌网盘限制了未登录用户，你登录账号再下载就可以了，没有谷歌账号的话，可以注册一个。\n\n3.解压码教程：\n全站采用境外大厂网盘，杜绝了链接失效的问题，只需要会简单的解压方法即可获取资源。\n苹果手机解压教程请看这里：https://jingyan.baidu.com/article/acf728fd8bc141f8e510a391.html\n安卓手机请看这里：\n先去应用商店安装一个解压专家。\n可以看这个教程：https://jingyan.baidu.com/article/f0e83a2543850b63e5910197.html',1,11,0,0,0,41248,0,412480,0,0,0,728,0,0,1,'2025-02-12 21:07:30','2025-02-12 21:07:43','2025-02-12 21:07:30',5156,'mega盘(大陆需梯子访问)|https://mega.nz/file/lRwQUSCa#_DXwUy-9zEX9zU-cDqJpW4SllFhltUpqvs7kD5vMAKs\n谷歌云盘(大陆需梯子访问)|https://drive.google.com/file/d/1TZ_Rxg0IEd98CezIjSGN6Zhc5Sm8bG7n/view?usp=drive_link\n解压码|idoufu.com',0),(79,'7','[PC/中文]EROTAS2_来自妖精的试炼[377M/網盤下载]',1,'','/upload_01/upload1/20250208/2025020815460552901.png','',' EROTAS2（3D引擎，扶他，bl，正大可微量DIY） 内容简介： 如果你的操作还可以那就能进入1的状态（仅仅对于扶他，对男恶魔还是只能当0）， 如果你的操作比较差劲，那就会一直处于0的状态， 路上可以抱走一只特别瑟瑟的奶牛小姐姐放到自己家的园子里面， 奶牛小姐姐赛高~勇者正大的衣服都特别酷特别瑟瑟特别可爱~值得一玩~ ','','温馨提示：\n1.配置梯子方法：https://gochatgptcn.com/252.html\n\n2.谷歌网盘限制问题：\n当你访问谷歌网盘链接时，遇到如下情况\n抱歉，您目前无法查看或下载此文件。\n最近查看或下载此文件的用户过多。请稍后再尝试访问此文件。如果您尝试访问的文件特别大或已与很多人共享，那么您最长可能需要等待 24 小时才能查看或下载该文件。如果您在 24 小时仍然无法访问文件，请与您的网域管理员联系。\n这是因为谷歌网盘限制了未登录用户，你登录账号再下载就可以了，没有谷歌账号的话，可以注册一个。\n\n3.解压码教程：\n全站采用境外大厂网盘，杜绝了链接失效的问题，只需要会简单的解压方法即可获取资源。\n苹果手机解压教程请看这里：https://jingyan.baidu.com/article/acf728fd8bc141f8e510a391.html\n安卓手机请看这里：\n先去应用商店安装一个解压专家。\n可以看这个教程：https://jingyan.baidu.com/article/f0e83a2543850b63e5910197.html',1,13,0,0,0,112774,0,789422,0,0,0,522,0,0,1,'2025-02-12 21:07:30','2025-02-12 21:07:43','2025-02-12 21:07:30',12530,'mega盘(大陆需梯子访问)|https://mega.nz/file/QIB21ZhZ#OiiWq6xFbtwUmvpp0HHT9j_C82e9uzAWUu83z8CgIXs\n谷歌云盘(大陆需梯子访问)|https://drive.google.com/file/d/1x5WMRn6moZIlB49ZZssq6vkDZ-rLe2AQ/view?usp=drive_link\n解压码|idoufu.com',0),(80,'6','[安卓/PC/汉化]更新-LustfulDesires-0.72[970M/网盘下载]',1,'','/upload_01/upload1/20250208/2025020815460350597.png','',' 这是一款对话冒险游戏，通过完成任务来推动各个NPC对你的好感度，从而完成攻略。\n在这里你不仅对主要人物进行攻略，对战怪物也看进行魅惑。   请选择对应需要版本下载：     不会配置梯子的话请查看这里：https://gochatgptcn.com/252.html 解压码：idoufu.com ','','温馨提示：\n1.配置梯子方法：https://gochatgptcn.com/252.html\n\n2.谷歌网盘限制问题：\n当你访问谷歌网盘链接时，遇到如下情况\n抱歉，您目前无法查看或下载此文件。\n最近查看或下载此文件的用户过多。请稍后再尝试访问此文件。如果您尝试访问的文件特别大或已与很多人共享，那么您最长可能需要等待 24 小时才能查看或下载该文件。如果您在 24 小时仍然无法访问文件，请与您的网域管理员联系。\n这是因为谷歌网盘限制了未登录用户，你登录账号再下载就可以了，没有谷歌账号的话，可以注册一个。\n\n3.解压码教程：\n全站采用境外大厂网盘，杜绝了链接失效的问题，只需要会简单的解压方法即可获取资源。\n苹果手机解压教程请看这里：https://jingyan.baidu.com/article/acf728fd8bc141f8e510a391.html\n安卓手机请看这里：\n先去应用商店安装一个解压专家。\n可以看这个教程：https://jingyan.baidu.com/article/f0e83a2543850b63e5910197.html',1,17,0,0,0,107077,0,642466,0,0,0,745,0,0,1,'2025-02-12 21:07:30','2025-02-12 21:07:43','2025-02-12 21:07:30',11897,'mega盘(大陆需梯子访问)|https://mega.nz/file/pBAXmRwT#YAAgUDHmBPr-ZM4ALfX_zE-MNNYdkHkOQPXUFz2DrXk\n谷歌云盘(大陆需梯子访问)|https://drive.google.com/file/d/1acJhYrknRgD3WDbQZD9AwnaRpdzJQ2h1/view?usp=drive_link\n解压码|idoufu.com',0),(81,'5','[安卓/PC/汉化]更新-欲望奥德赛 v0.36.1[579M/網盤下载]',1,'','/upload_01/upload1/20250208/2025020815455960165.png','',' 剧情推进文字探索类游戏 官方更新内容： – 部分前版本WIP内容已新增完毕\n– 现在夜晚在阿克拉二层裸奔可触发新的涩情剧情\n– 新角色哈基姆（部分内容名字翻译被手动更改为哈基米）\n– 若条件满足-80 船员尊重，或 Rylan 和 Arthur 是您的主人且被允许超你，可在日出谷小睡后触发小段剧情\n– 20点之后，酒馆内会触发新剧情\n– 部分内容翻译已重置（中文翻译相应重译）\n– 在新场景中部分角色会开始小便了\n– 修正部分恶性bug – 新角色哈基米和亚瑟之间有新联动剧情\n– Eldor有新增任务\n– Elion有新剧情\n– Snake捐赠满额并等待2~4天后有新增剧情\n– 新角色Keyris（依然是吓孕立绘\n– 新星球Nova Nexus（仅用于完成新角色任务）\n– Galiano有新剧情 – UI进行部分修改简化（感觉会产生什么bug）\n– 新增Eldor吸血鬼剧情任务\n– Snake新增浪漫剧情\n– 新角色Damien在哥布林营地有新增剧情 – 新增魔法大学剧情（核心大更新）\n– 投票新增Edward国王剧情 – 魔法大学剧情第二日已完成，第三日部分WIP\n– 新增炼药系统     ','','温馨提示：\n1.配置梯子方法：https://gochatgptcn.com/252.html\n\n2.谷歌网盘限制问题：\n当你访问谷歌网盘链接时，遇到如下情况\n抱歉，您目前无法查看或下载此文件。\n最近查看或下载此文件的用户过多。请稍后再尝试访问此文件。如果您尝试访问的文件特别大或已与很多人共享，那么您最长可能需要等待 24 小时才能查看或下载该文件。如果您在 24 小时仍然无法访问文件，请与您的网域管理员联系。\n这是因为谷歌网盘限制了未登录用户，你登录账号再下载就可以了，没有谷歌账号的话，可以注册一个。\n\n3.解压码教程：\n全站采用境外大厂网盘，杜绝了链接失效的问题，只需要会简单的解压方法即可获取资源。\n苹果手机解压教程请看这里：https://jingyan.baidu.com/article/acf728fd8bc141f8e510a391.html\n安卓手机请看这里：\n先去应用商店安装一个解压专家。\n可以看这个教程：https://jingyan.baidu.com/article/f0e83a2543850b63e5910197.html',1,17,0,0,0,73926,0,591415,0,0,0,756,0,0,1,'2025-02-12 21:07:30','2025-02-12 21:07:43','2025-02-12 21:07:30',7392,'mega盘(大陆需梯子访问)|https://mega.nz/file/xAZmFYgB#0rhBdftuE6V0rH-daCth7cZj0RrXRxieFzmwpO9O6ws\n谷歌云盘(大陆需梯子访问)|https://drive.google.com/file/d/1qLWcq-0_7MvXhFUTVW_AyYbDYYrxrNXM/view?usp=drive_link\n解压码|idoufu.com',0),(82,'4','[PC/中文]地下室的恶魔[565M/網盤下载]',1,'','/upload_01/upload1/20250208/2025020815455666601.png','',' 寄住在叔叔家的男主在叔叔战死后，在叔叔的地下室里发现了一个被封印的恶魔….. 攻略(点击查看)： ','','温馨提示：\n1.配置梯子方法：https://gochatgptcn.com/252.html\n\n2.谷歌网盘限制问题：\n当你访问谷歌网盘链接时，遇到如下情况\n抱歉，您目前无法查看或下载此文件。\n最近查看或下载此文件的用户过多。请稍后再尝试访问此文件。如果您尝试访问的文件特别大或已与很多人共享，那么您最长可能需要等待 24 小时才能查看或下载该文件。如果您在 24 小时仍然无法访问文件，请与您的网域管理员联系。\n这是因为谷歌网盘限制了未登录用户，你登录账号再下载就可以了，没有谷歌账号的话，可以注册一个。\n\n3.解压码教程：\n全站采用境外大厂网盘，杜绝了链接失效的问题，只需要会简单的解压方法即可获取资源。\n苹果手机解压教程请看这里：https://jingyan.baidu.com/article/acf728fd8bc141f8e510a391.html\n安卓手机请看这里：\n先去应用商店安装一个解压专家。\n可以看这个教程：https://jingyan.baidu.com/article/f0e83a2543850b63e5910197.html',1,10,0,0,0,35155,0,351550,0,0,0,574,0,0,1,'2025-02-12 21:07:30','2025-02-12 21:07:43','2025-02-12 21:07:30',5022,'mega盘(大陆需梯子访问)|https://mega.nz/file/FQp2wYiR#iG52EatvTv2ykxoZVwpjh-uCk5tmWORNDrHeU9KL32k\n谷歌云盘(大陆需梯子访问)|https://drive.google.com/file/d/1oaJYY9EEM4xqI72m8oXK7G-cW9hvo9Rs/view?usp=drive_link\n解压码|idoufu.com',0),(83,'3','[PC/日语]Slow Damage[5.5G/網盤下载]',1,'','/upload_01/upload1/20250208/2025020815455533263.png','',' ','','温馨提示：\n1.配置梯子方法：https://gochatgptcn.com/252.html\n\n2.谷歌网盘限制问题：\n当你访问谷歌网盘链接时，遇到如下情况\n抱歉，您目前无法查看或下载此文件。\n最近查看或下载此文件的用户过多。请稍后再尝试访问此文件。如果您尝试访问的文件特别大或已与很多人共享，那么您最长可能需要等待 24 小时才能查看或下载该文件。如果您在 24 小时仍然无法访问文件，请与您的网域管理员联系。\n这是因为谷歌网盘限制了未登录用户，你登录账号再下载就可以了，没有谷歌账号的话，可以注册一个。\n\n3.解压码教程：\n全站采用境外大厂网盘，杜绝了链接失效的问题，只需要会简单的解压方法即可获取资源。\n苹果手机解压教程请看这里：https://jingyan.baidu.com/article/acf728fd8bc141f8e510a391.html\n安卓手机请看这里：\n先去应用商店安装一个解压专家。\n可以看这个教程：https://jingyan.baidu.com/article/f0e83a2543850b63e5910197.html',1,10,0,0,0,103553,0,828425,0,0,0,939,0,0,1,'2025-02-12 21:07:30','2025-02-12 21:07:43','2025-02-12 21:07:30',17258,'mega盘(大陆需梯子访问)|https://mega.nz/file/lYR1zJZT#MNY5murbP6FiBwK-tAkJ06QviuHGeof8vcXLVKBMwm8\n谷歌云盘(大陆需梯子访问)|https://drive.google.com/file/d/1g38hdY_rGk9Dt1rlMmv41YNTMHjIUzxd/view?usp=drive_link\n解压码|idoufu.com',0),(84,'2','[PC/中文]荒兰岛1.0正式版[833M/網盤下载]',1,'','/upload_01/upload1/20250208/2025020815455216596.png','',' 威族大大第二部末世RPG，评价很高的一部RPG，几乎没有任何短板 在末世，作为实验体761复活的你，将要面临什么呢？ ','','温馨提示：\n1.配置梯子方法：https://gochatgptcn.com/252.html\n\n2.谷歌网盘限制问题：\n当你访问谷歌网盘链接时，遇到如下情况\n抱歉，您目前无法查看或下载此文件。\n最近查看或下载此文件的用户过多。请稍后再尝试访问此文件。如果您尝试访问的文件特别大或已与很多人共享，那么您最长可能需要等待 24 小时才能查看或下载该文件。如果您在 24 小时仍然无法访问文件，请与您的网域管理员联系。\n这是因为谷歌网盘限制了未登录用户，你登录账号再下载就可以了，没有谷歌账号的话，可以注册一个。\n\n3.解压码教程：\n全站采用境外大厂网盘，杜绝了链接失效的问题，只需要会简单的解压方法即可获取资源。\n苹果手机解压教程请看这里：https://jingyan.baidu.com/article/acf728fd8bc141f8e510a391.html\n安卓手机请看这里：\n先去应用商店安装一个解压专家。\n可以看这个教程：https://jingyan.baidu.com/article/f0e83a2543850b63e5910197.html',1,15,0,0,0,77479,0,542359,0,0,0,721,0,0,1,'2025-02-12 21:07:30','2025-02-12 21:07:43','2025-02-12 21:07:30',9684,'mega盘(大陆需梯子访问)|https://mega.nz/file/RQZVyZyY#mGbZIJ02PZolIRHy2T-AJBeBn9-TZ_-bkoWUPNVXxQQ\n谷歌云盘(大陆需梯子访问)|https://drive.google.com/file/d/1E7kpVf4I10MQpTpajPru_Zl2ntQkQisv/view?usp=drive_link\n解压码|idoufu.com',0),(85,'1','[PC/中文]HUNKY city v0.92 [272M/網盤下载]',1,'','/upload_01/upload1/20250208/2025020815444964985.png','',' 死的亡约会续作-猛男城市 小男孩在一个全是猛男的城市里将会有怎样浪漫的冒险呢？ ','','温馨提示：\n1.配置梯子方法：https://gochatgptcn.com/252.html\n\n2.谷歌网盘限制问题：\n当你访问谷歌网盘链接时，遇到如下情况\n抱歉，您目前无法查看或下载此文件。\n最近查看或下载此文件的用户过多。请稍后再尝试访问此文件。如果您尝试访问的文件特别大或已与很多人共享，那么您最长可能需要等待 24 小时才能查看或下载该文件。如果您在 24 小时仍然无法访问文件，请与您的网域管理员联系。\n这是因为谷歌网盘限制了未登录用户，你登录账号再下载就可以了，没有谷歌账号的话，可以注册一个。\n\n3.解压码教程：\n全站采用境外大厂网盘，杜绝了链接失效的问题，只需要会简单的解压方法即可获取资源。\n苹果手机解压教程请看这里：https://jingyan.baidu.com/article/acf728fd8bc141f8e510a391.html\n安卓手机请看这里：\n先去应用商店安装一个解压专家。\n可以看这个教程：https://jingyan.baidu.com/article/f0e83a2543850b63e5910197.html',1,20,0,0,0,90990,0,545944,0,0,0,987,0,0,1,'2025-02-12 21:07:30','2025-02-12 21:07:43','2025-02-12 21:07:30',12998,'mega盘(大陆需梯子访问)|https://mega.nz/file/MFgWQLTS#TlS-XTLmOPYx-oQljbxyD1dQyrRmCAxiv69jpZE4u6g\n谷歌云盘(大陆需梯子访问)|https://drive.google.com/file/d/1ClwcCFbbZIBe7QUoWLrwr4lPVnSKDTsV/view?usp=drive_link\n解压码|idoufu.com',0);
/*!40000 ALTER TABLE `ks_porn_game` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ks_porn_like`
--

DROP TABLE IF EXISTS `ks_porn_like`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ks_porn_like` (
`id` int unsigned NOT NULL AUTO_INCREMENT,
`aff` int DEFAULT '0' COMMENT '用户aff',
`porn_id` int DEFAULT '0' COMMENT '图集id',
`created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
`updated_at` timestamp NULL DEFAULT NULL COMMENT '更新时间',
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='黄游点赞表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ks_porn_like`
--

LOCK TABLES `ks_porn_like` WRITE;
/*!40000 ALTER TABLE `ks_porn_like` DISABLE KEYS */;
/*!40000 ALTER TABLE `ks_porn_like` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ks_porn_media`
--

DROP TABLE IF EXISTS `ks_porn_media`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ks_porn_media` (
`id` int unsigned NOT NULL AUTO_INCREMENT,
`media_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '视频或图片地址',
`cover` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '视频封面',
`thumb_width` int NOT NULL DEFAULT '0' COMMENT '封面宽',
`thumb_height` int NOT NULL DEFAULT '0' COMMENT '封面高',
`pid` int NOT NULL DEFAULT '0' COMMENT '黄游ID',
`aff` char(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '0' COMMENT '上传用户AFF',
`type` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '类型 1图片 2视频',
`created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
`status` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '0 未转换 1 已转换 2 转换中',
`duration` int NOT NULL DEFAULT '0' COMMENT '视频持续时间',
`updated_at` timestamp NULL DEFAULT NULL COMMENT '更新时间',
`relate_type` tinyint(1) DEFAULT NULL COMMENT '关联类型 1黄游 2评论',
PRIMARY KEY (`id`),
KEY `pid` (`pid`) USING BTREE,
KEY `type` (`type`) USING BTREE,
KEY `status` (`status`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='黄游媒体表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ks_porn_media`
--

LOCK TABLES `ks_porn_media` WRITE;
/*!40000 ALTER TABLE `ks_porn_media` DISABLE KEYS */;
INSERT INTO `ks_porn_media` VALUES (1,'/upload_01/upload1/20250208/2025020816134228755.png','/upload_01/upload1/20250208/2025020816134228755.png',0,0,1,'0',1,'2025-02-12 14:07:29',1,0,'2025-02-12 14:07:29',1),(2,'/upload_01/upload1/20250208/2025020816134618670.png','/upload_01/upload1/20250208/2025020816134618670.png',0,0,1,'0',1,'2025-02-12 14:07:29',1,0,'2025-02-12 14:07:29',1),(3,'/upload_01/upload1/20250208/2025020816133563767.png','/upload_01/upload1/20250208/2025020816133563767.png',0,0,2,'0',1,'2025-02-12 14:07:29',1,0,'2025-02-12 14:07:29',1),(4,'/upload_01/upload1/20250208/2025020816133946463.png','/upload_01/upload1/20250208/2025020816133946463.png',0,0,2,'0',1,'2025-02-12 14:07:29',1,0,'2025-02-12 14:07:29',1),(5,'/upload_01/upload1/20250208/2025020816132975555.png','/upload_01/upload1/20250208/2025020816132975555.png',0,0,3,'0',1,'2025-02-12 14:07:29',1,0,'2025-02-12 14:07:29',1),(6,'/upload_01/upload1/20250208/2025020816133183733.png','/upload_01/upload1/20250208/2025020816133183733.png',0,0,3,'0',1,'2025-02-12 14:07:29',1,0,'2025-02-12 14:07:29',1),(7,'/upload_01/upload1/20250208/2025020816133372294.png','/upload_01/upload1/20250208/2025020816133372294.png',0,0,3,'0',1,'2025-02-12 14:07:29',1,0,'2025-02-12 14:07:29',1),(8,'/upload_01/upload1/20250208/2025020816132045517.png','/upload_01/upload1/20250208/2025020816132045517.png',0,0,4,'0',1,'2025-02-12 14:07:29',1,0,'2025-02-12 14:07:29',1),(9,'/upload_01/upload1/20250208/2025020816132476173.png','/upload_01/upload1/20250208/2025020816132476173.png',0,0,4,'0',1,'2025-02-12 14:07:29',1,0,'2025-02-12 14:07:29',1),(10,'/upload_01/upload1/20250208/2025020816132628092.png','/upload_01/upload1/20250208/2025020816132628092.png',0,0,4,'0',1,'2025-02-12 14:07:29',1,0,'2025-02-12 14:07:29',1),(11,'/upload_01/upload1/20250208/2025020816131464325.png','/upload_01/upload1/20250208/2025020816131464325.png',0,0,5,'0',1,'2025-02-12 14:07:29',1,0,'2025-02-12 14:07:29',1),(12,'/upload_01/upload1/20250208/2025020816131652016.png','/upload_01/upload1/20250208/2025020816131652016.png',0,0,5,'0',1,'2025-02-12 14:07:29',1,0,'2025-02-12 14:07:29',1),(13,'/upload_01/upload1/20250208/2025020816131763796.png','/upload_01/upload1/20250208/2025020816131763796.png',0,0,5,'0',1,'2025-02-12 14:07:29',1,0,'2025-02-12 14:07:29',1),(14,'/upload_01/upload1/20250208/2025020816130962714.png','/upload_01/upload1/20250208/2025020816130962714.png',0,0,6,'0',1,'2025-02-12 14:07:29',1,0,'2025-02-12 14:07:29',1),(15,'/upload_01/upload1/20250208/2025020816131293887.png','/upload_01/upload1/20250208/2025020816131293887.png',0,0,6,'0',1,'2025-02-12 14:07:29',1,0,'2025-02-12 14:07:29',1),(16,'/upload_01/upload1/20250208/2025020816130576742.png','/upload_01/upload1/20250208/2025020816130576742.png',0,0,7,'0',1,'2025-02-12 14:07:29',1,0,'2025-02-12 14:07:29',1),(17,'/upload_01/upload1/20250208/2025020816130788601.png','/upload_01/upload1/20250208/2025020816130788601.png',0,0,7,'0',1,'2025-02-12 14:07:29',1,0,'2025-02-12 14:07:29',1),(18,'/upload_01/upload1/20250208/2025020816130353854.png','/upload_01/upload1/20250208/2025020816130353854.png',0,0,8,'0',1,'2025-02-12 14:07:29',1,0,'2025-02-12 14:07:29',1),(19,'/upload_01/upload1/20250208/2025020816125359766.png','/upload_01/upload1/20250208/2025020816125359766.png',0,0,9,'0',1,'2025-02-12 14:07:29',1,0,'2025-02-12 14:07:29',1),(20,'/upload_01/upload1/20250208/2025020816125657211.png','/upload_01/upload1/20250208/2025020816125657211.png',0,0,9,'0',1,'2025-02-12 14:07:29',1,0,'2025-02-12 14:07:29',1),(21,'/upload_01/upload1/20250208/2025020816125977964.png','/upload_01/upload1/20250208/2025020816125977964.png',0,0,9,'0',1,'2025-02-12 14:07:29',1,0,'2025-02-12 14:07:29',1),(22,'/upload_01/upload1/20250208/2025020816130183126.png','/upload_01/upload1/20250208/2025020816130183126.png',0,0,9,'0',1,'2025-02-12 14:07:29',1,0,'2025-02-12 14:07:29',1),(23,'/upload_01/upload1/20250208/2025020816125131710.png','/upload_01/upload1/20250208/2025020816125131710.png',0,0,10,'0',1,'2025-02-12 14:07:29',1,0,'2025-02-12 14:07:29',1),(24,'/upload_01/upload1/20250208/2025020816124559063.png','/upload_01/upload1/20250208/2025020816124559063.png',0,0,11,'0',1,'2025-02-12 14:07:29',1,0,'2025-02-12 14:07:29',1),(25,'/upload_01/upload1/20250208/2025020816124744807.png','/upload_01/upload1/20250208/2025020816124744807.png',0,0,11,'0',1,'2025-02-12 14:07:29',1,0,'2025-02-12 14:07:29',1),(26,'/upload_01/upload1/20250208/2025020816123922509.png','/upload_01/upload1/20250208/2025020816123922509.png',0,0,12,'0',1,'2025-02-12 14:07:29',1,0,'2025-02-12 14:07:29',1),(27,'/upload_01/upload1/20250208/2025020816124241410.png','/upload_01/upload1/20250208/2025020816124241410.png',0,0,12,'0',1,'2025-02-12 14:07:29',1,0,'2025-02-12 14:07:29',1),(28,'/upload_01/upload1/20250208/2025020816123747455.png','/upload_01/upload1/20250208/2025020816123747455.png',0,0,13,'0',1,'2025-02-12 14:07:29',1,0,'2025-02-12 14:07:29',1),(29,'/upload_01/upload1/20250208/2025020816123238110.png','/upload_01/upload1/20250208/2025020816123238110.png',0,0,14,'0',1,'2025-02-12 14:07:29',1,0,'2025-02-12 14:07:29',1),(30,'/upload_01/upload1/20250208/2025020816123028632.png','/upload_01/upload1/20250208/2025020816123028632.png',0,0,15,'0',1,'2025-02-12 14:07:29',1,0,'2025-02-12 14:07:29',1),(31,'/upload_01/upload1/20250208/2025020816122475776.png','/upload_01/upload1/20250208/2025020816122475776.png',0,0,16,'0',1,'2025-02-12 14:07:29',1,0,'2025-02-12 14:07:29',1),(32,'/upload_01/upload1/20250208/2025020816122629389.png','/upload_01/upload1/20250208/2025020816122629389.png',0,0,16,'0',1,'2025-02-12 14:07:29',1,0,'2025-02-12 14:07:29',1),(33,'/upload_01/upload1/20250208/2025020816122272685.png','/upload_01/upload1/20250208/2025020816122272685.png',0,0,17,'0',1,'2025-02-12 14:07:29',1,0,'2025-02-12 14:07:29',1),(34,'/upload_01/upload1/20250208/2025020816121082405.png','/upload_01/upload1/20250208/2025020816121082405.png',0,0,18,'0',1,'2025-02-12 14:07:29',1,0,'2025-02-12 14:07:29',1),(35,'/upload_01/upload1/20250208/2025020816121977908.png','/upload_01/upload1/20250208/2025020816121977908.png',0,0,18,'0',1,'2025-02-12 14:07:29',1,0,'2025-02-12 14:07:29',1),(36,'/upload_01/upload1/20250208/2025020816120266330.png','/upload_01/upload1/20250208/2025020816120266330.png',0,0,19,'0',1,'2025-02-12 14:07:29',1,0,'2025-02-12 14:07:29',1),(37,'/upload_01/upload1/20250208/2025020816120632347.png','/upload_01/upload1/20250208/2025020816120632347.png',0,0,19,'0',1,'2025-02-12 14:07:29',1,0,'2025-02-12 14:07:29',1),(38,'/upload_01/upload1/20250208/2025020816115687121.png','/upload_01/upload1/20250208/2025020816115687121.png',0,0,20,'0',1,'2025-02-12 14:07:29',1,0,'2025-02-12 14:07:29',1),(39,'/upload_01/upload1/20250208/2025020816114979584.png','/upload_01/upload1/20250208/2025020816114979584.png',0,0,21,'0',1,'2025-02-12 14:07:29',1,0,'2025-02-12 14:07:29',1),(40,'/upload_01/upload1/20250208/2025020816115284503.png','/upload_01/upload1/20250208/2025020816115284503.png',0,0,21,'0',1,'2025-02-12 14:07:29',1,0,'2025-02-12 14:07:29',1),(41,'/upload_01/upload1/20250208/2025020816115419768.png','/upload_01/upload1/20250208/2025020816115419768.png',0,0,21,'0',1,'2025-02-12 14:07:29',1,0,'2025-02-12 14:07:29',1),(42,'/upload_01/upload1/20250208/2025020816113954572.png','/upload_01/upload1/20250208/2025020816113954572.png',0,0,22,'0',1,'2025-02-12 14:07:29',1,0,'2025-02-12 14:07:29',1),(43,'/upload_01/upload1/20250208/2025020816114264376.png','/upload_01/upload1/20250208/2025020816114264376.png',0,0,22,'0',1,'2025-02-12 14:07:29',1,0,'2025-02-12 14:07:29',1),(44,'/upload_01/upload1/20250208/2025020816113062692.png','/upload_01/upload1/20250208/2025020816113062692.png',0,0,23,'0',1,'2025-02-12 14:07:29',1,0,'2025-02-12 14:07:29',1),(45,'/upload_01/upload1/20250208/2025020816113284937.png','/upload_01/upload1/20250208/2025020816113284937.png',0,0,23,'0',1,'2025-02-12 14:07:29',1,0,'2025-02-12 14:07:29',1),(46,'/upload_01/upload1/20250208/2025020816112365629.png','/upload_01/upload1/20250208/2025020816112365629.png',0,0,24,'0',1,'2025-02-12 14:07:29',1,0,'2025-02-12 14:07:29',1),(47,'/upload_01/upload1/20250208/2025020816111718285.png','/upload_01/upload1/20250208/2025020816111718285.png',0,0,25,'0',1,'2025-02-12 14:07:29',1,0,'2025-02-12 14:07:29',1),(48,'/upload_01/upload1/20250208/2025020816111989829.png','/upload_01/upload1/20250208/2025020816111989829.png',0,0,25,'0',1,'2025-02-12 14:07:29',1,0,'2025-02-12 14:07:29',1),(49,'/upload_01/upload1/20250208/2025020816112136415.png','/upload_01/upload1/20250208/2025020816112136415.png',0,0,25,'0',1,'2025-02-12 14:07:29',1,0,'2025-02-12 14:07:29',1),(50,'/upload_01/upload1/20250208/2025020816111184976.png','/upload_01/upload1/20250208/2025020816111184976.png',0,0,26,'0',1,'2025-02-12 14:07:29',1,0,'2025-02-12 14:07:29',1),(51,'/upload_01/upload1/20250208/2025020816111368076.png','/upload_01/upload1/20250208/2025020816111368076.png',0,0,26,'0',1,'2025-02-12 14:07:29',1,0,'2025-02-12 14:07:29',1),(52,'/upload_01/upload1/20250208/2025020816111640834.png','/upload_01/upload1/20250208/2025020816111640834.png',0,0,26,'0',1,'2025-02-12 14:07:29',1,0,'2025-02-12 14:07:29',1),(53,'/upload_01/upload1/20250208/2025020816110845461.png','/upload_01/upload1/20250208/2025020816110845461.png',0,0,27,'0',1,'2025-02-12 14:07:29',1,0,'2025-02-12 14:07:29',1),(54,'/upload_01/upload1/20250208/2025020816110495840.png','/upload_01/upload1/20250208/2025020816110495840.png',0,0,28,'0',1,'2025-02-12 14:07:29',1,0,'2025-02-12 14:07:29',1),(55,'/upload_01/upload1/20250208/2025020816110693736.png','/upload_01/upload1/20250208/2025020816110693736.png',0,0,28,'0',1,'2025-02-12 14:07:29',1,0,'2025-02-12 14:07:29',1),(56,'/upload_01/upload1/20250208/2025020816110028961.png','/upload_01/upload1/20250208/2025020816110028961.png',0,0,29,'0',1,'2025-02-12 14:07:29',1,0,'2025-02-12 14:07:29',1),(57,'/upload_01/upload1/20250208/2025020816110243490.png','/upload_01/upload1/20250208/2025020816110243490.png',0,0,29,'0',1,'2025-02-12 14:07:29',1,0,'2025-02-12 14:07:29',1),(58,'/upload_01/upload1/20250208/2025020816105665485.png','/upload_01/upload1/20250208/2025020816105665485.png',0,0,30,'0',1,'2025-02-12 14:07:29',1,0,'2025-02-12 14:07:29',1),(59,'/upload_01/upload1/20250208/2025020816105277107.png','/upload_01/upload1/20250208/2025020816105277107.png',0,0,31,'0',1,'2025-02-12 14:07:30',1,0,'2025-02-12 14:07:30',1),(60,'/upload_01/upload1/20250208/2025020816104867633.png','/upload_01/upload1/20250208/2025020816104867633.png',0,0,32,'0',1,'2025-02-12 14:07:30',1,0,'2025-02-12 14:07:30',1),(61,'/upload_01/upload1/20250208/2025020816105016051.png','/upload_01/upload1/20250208/2025020816105016051.png',0,0,32,'0',1,'2025-02-12 14:07:30',1,0,'2025-02-12 14:07:30',1),(62,'/upload_01/upload1/20250208/2025020816104586066.png','/upload_01/upload1/20250208/2025020816104586066.png',0,0,33,'0',1,'2025-02-12 14:07:30',1,0,'2025-02-12 14:07:30',1),(63,'/upload_01/upload1/20250208/2025020816104793867.png','/upload_01/upload1/20250208/2025020816104793867.png',0,0,33,'0',1,'2025-02-12 14:07:30',1,0,'2025-02-12 14:07:30',1),(64,'/upload_01/upload1/20250208/2025020816104236144.png','/upload_01/upload1/20250208/2025020816104236144.png',0,0,34,'0',1,'2025-02-12 14:07:30',1,0,'2025-02-12 14:07:30',1),(65,'/upload_01/upload1/20250208/2025020816104397984.png','/upload_01/upload1/20250208/2025020816104397984.png',0,0,34,'0',1,'2025-02-12 14:07:30',1,0,'2025-02-12 14:07:30',1),(66,'/upload_01/upload1/20250208/2025020816104099740.png','/upload_01/upload1/20250208/2025020816104099740.png',0,0,35,'0',1,'2025-02-12 14:07:30',1,0,'2025-02-12 14:07:30',1),(67,'/upload_01/upload1/20250208/2025020816103534635.png','/upload_01/upload1/20250208/2025020816103534635.png',0,0,36,'0',1,'2025-02-12 14:07:30',1,0,'2025-02-12 14:07:30',1),(68,'/upload_01/upload1/20250208/2025020816103819471.png','/upload_01/upload1/20250208/2025020816103819471.png',0,0,36,'0',1,'2025-02-12 14:07:30',1,0,'2025-02-12 14:07:30',1),(69,'/upload_01/upload1/20250208/2025020816103166420.png','/upload_01/upload1/20250208/2025020816103166420.png',0,0,37,'0',1,'2025-02-12 14:07:30',1,0,'2025-02-12 14:07:30',1),(70,'/upload_01/upload1/20250208/2025020816103326755.png','/upload_01/upload1/20250208/2025020816103326755.png',0,0,37,'0',1,'2025-02-12 14:07:30',1,0,'2025-02-12 14:07:30',1),(71,'/upload_01/upload1/20250208/2025020816102775988.png','/upload_01/upload1/20250208/2025020816102775988.png',0,0,38,'0',1,'2025-02-12 14:07:30',1,0,'2025-02-12 14:07:30',1),(72,'/upload_01/upload1/20250208/2025020816103074950.png','/upload_01/upload1/20250208/2025020816103074950.png',0,0,38,'0',1,'2025-02-12 14:07:30',1,0,'2025-02-12 14:07:30',1),(73,'/upload_01/upload1/20250208/2025020816101717496.png','/upload_01/upload1/20250208/2025020816101717496.png',0,0,39,'0',1,'2025-02-12 14:07:30',1,0,'2025-02-12 14:07:30',1),(74,'/upload_01/upload1/20250208/2025020816101982504.png','/upload_01/upload1/20250208/2025020816101982504.png',0,0,39,'0',1,'2025-02-12 14:07:30',1,0,'2025-02-12 14:07:30',1),(75,'/upload_01/upload1/20250208/2025020816102186238.png','/upload_01/upload1/20250208/2025020816102186238.png',0,0,39,'0',1,'2025-02-12 14:07:30',1,0,'2025-02-12 14:07:30',1),(76,'/upload_01/upload1/20250208/2025020816102434263.png','/upload_01/upload1/20250208/2025020816102434263.png',0,0,39,'0',1,'2025-02-12 14:07:30',1,0,'2025-02-12 14:07:30',1),(77,'/upload_01/upload1/20250208/2025020816101250800.png','/upload_01/upload1/20250208/2025020816101250800.png',0,0,40,'0',1,'2025-02-12 14:07:30',1,0,'2025-02-12 14:07:30',1),(78,'/upload_01/upload1/20250208/2025020816101482088.png','/upload_01/upload1/20250208/2025020816101482088.png',0,0,40,'0',1,'2025-02-12 14:07:30',1,0,'2025-02-12 14:07:30',1),(79,'/upload_01/upload1/20250208/2025020816100811644.png','/upload_01/upload1/20250208/2025020816100811644.png',0,0,41,'0',1,'2025-02-12 14:07:30',1,0,'2025-02-12 14:07:30',1),(80,'/upload_01/upload1/20250208/2025020816101096077.png','/upload_01/upload1/20250208/2025020816101096077.png',0,0,41,'0',1,'2025-02-12 14:07:30',1,0,'2025-02-12 14:07:30',1),(81,'/upload_01/upload1/20250208/2025020816100412413.png','/upload_01/upload1/20250208/2025020816100412413.png',0,0,42,'0',1,'2025-02-12 14:07:30',1,0,'2025-02-12 14:07:30',1),(82,'/upload_01/upload1/20250208/2025020816100652800.png','/upload_01/upload1/20250208/2025020816100652800.png',0,0,42,'0',1,'2025-02-12 14:07:30',1,0,'2025-02-12 14:07:30',1),(83,'/upload_01/upload1/20250208/2025020816095789037.png','/upload_01/upload1/20250208/2025020816095789037.png',0,0,43,'0',1,'2025-02-12 14:07:30',1,0,'2025-02-12 14:07:30',1),(84,'/upload_01/upload1/20250208/2025020816095931811.png','/upload_01/upload1/20250208/2025020816095931811.png',0,0,43,'0',1,'2025-02-12 14:07:30',1,0,'2025-02-12 14:07:30',1),(85,'/upload_01/upload1/20250208/2025020816095345546.png','/upload_01/upload1/20250208/2025020816095345546.png',0,0,44,'0',1,'2025-02-12 14:07:30',1,0,'2025-02-12 14:07:30',1),(86,'/upload_01/upload1/20250208/2025020816095547783.png','/upload_01/upload1/20250208/2025020816095547783.png',0,0,44,'0',1,'2025-02-12 14:07:30',1,0,'2025-02-12 14:07:30',1),(87,'/upload_01/upload1/20250208/2025020816094812231.png','/upload_01/upload1/20250208/2025020816094812231.png',0,0,45,'0',1,'2025-02-12 14:07:30',1,0,'2025-02-12 14:07:30',1),(88,'/upload_01/upload1/20250208/2025020816094943913.png','/upload_01/upload1/20250208/2025020816094943913.png',0,0,45,'0',1,'2025-02-12 14:07:30',1,0,'2025-02-12 14:07:30',1),(89,'/upload_01/upload1/20250208/2025020816095157755.png','/upload_01/upload1/20250208/2025020816095157755.png',0,0,45,'0',1,'2025-02-12 14:07:30',1,0,'2025-02-12 14:07:30',1),(95,'/upload_01/upload1/20250208/2025020816092058684.png','/upload_01/upload1/20250208/2025020816092058684.png',0,0,47,'0',1,'2025-02-12 14:07:30',1,0,'2025-02-12 14:07:30',1),(96,'/upload_01/upload1/20250208/2025020816092714555.png','/upload_01/upload1/20250208/2025020816092714555.png',0,0,47,'0',1,'2025-02-12 14:07:30',1,0,'2025-02-12 14:07:30',1),(111,'/upload_01/upload1/20250208/2025020816083965692.png','/upload_01/upload1/20250208/2025020816083965692.png',0,0,49,'0',1,'2025-02-12 14:07:30',1,0,'2025-02-12 14:07:30',1),(112,'/upload_01/upload1/20250208/2025020816084059215.png','/upload_01/upload1/20250208/2025020816084059215.png',0,0,49,'0',1,'2025-02-12 14:07:30',1,0,'2025-02-12 14:07:30',1),(113,'/upload_01/upload1/20250208/2025020816084224943.png','/upload_01/upload1/20250208/2025020816084224943.png',0,0,49,'0',1,'2025-02-12 14:07:30',1,0,'2025-02-12 14:07:30',1),(114,'/upload_01/upload1/20250208/2025020816083540604.png','/upload_01/upload1/20250208/2025020816083540604.png',0,0,50,'0',1,'2025-02-12 14:07:30',1,0,'2025-02-12 14:07:30',1),(115,'/upload_01/upload1/20250208/2025020816083767791.png','/upload_01/upload1/20250208/2025020816083767791.png',0,0,50,'0',1,'2025-02-12 14:07:30',1,0,'2025-02-12 14:07:30',1),(116,'/upload_01/upload1/20250208/2025020816083499284.png','/upload_01/upload1/20250208/2025020816083499284.png',0,0,51,'0',1,'2025-02-12 14:07:30',1,0,'2025-02-12 14:07:30',1),(117,'/upload_01/upload1/20250208/2025020816083164420.png','/upload_01/upload1/20250208/2025020816083164420.png',0,0,52,'0',1,'2025-02-12 14:07:30',1,0,'2025-02-12 14:07:30',1),(118,'/upload_01/upload1/20250208/2025020816083280070.png','/upload_01/upload1/20250208/2025020816083280070.png',0,0,52,'0',1,'2025-02-12 14:07:30',1,0,'2025-02-12 14:07:30',1),(119,'/upload_01/upload1/20250208/2025020816082815597.png','/upload_01/upload1/20250208/2025020816082815597.png',0,0,53,'0',1,'2025-02-12 14:07:30',1,0,'2025-02-12 14:07:30',1),(120,'/upload_01/upload1/20250208/2025020816082937677.png','/upload_01/upload1/20250208/2025020816082937677.png',0,0,53,'0',1,'2025-02-12 14:07:30',1,0,'2025-02-12 14:07:30',1),(121,'/upload_01/upload1/20250208/2025020816082552984.png','/upload_01/upload1/20250208/2025020816082552984.png',0,0,54,'0',1,'2025-02-12 14:07:30',1,0,'2025-02-12 14:07:30',1),(122,'/upload_01/upload1/20250208/2025020816082737845.png','/upload_01/upload1/20250208/2025020816082737845.png',0,0,54,'0',1,'2025-02-12 14:07:30',1,0,'2025-02-12 14:07:30',1),(123,'/upload_01/upload1/20250208/2025020816081735521.png','/upload_01/upload1/20250208/2025020816081735521.png',0,0,55,'0',1,'2025-02-12 14:07:30',1,0,'2025-02-12 14:07:30',1),(124,'/upload_01/upload1/20250208/2025020816082057294.png','/upload_01/upload1/20250208/2025020816082057294.png',0,0,55,'0',1,'2025-02-12 14:07:30',1,0,'2025-02-12 14:07:30',1),(125,'/upload_01/upload1/20250208/2025020816082376094.png','/upload_01/upload1/20250208/2025020816082376094.png',0,0,55,'0',1,'2025-02-12 14:07:30',1,0,'2025-02-12 14:07:30',1),(126,'/upload_01/upload1/20250208/2025020816081366937.png','/upload_01/upload1/20250208/2025020816081366937.png',0,0,56,'0',1,'2025-02-12 14:07:30',1,0,'2025-02-12 14:07:30',1),(127,'/upload_01/upload1/20250208/2025020816081578010.png','/upload_01/upload1/20250208/2025020816081578010.png',0,0,56,'0',1,'2025-02-12 14:07:30',1,0,'2025-02-12 14:07:30',1),(128,'/upload_01/upload1/20250208/2025020816081180483.png','/upload_01/upload1/20250208/2025020816081180483.png',0,0,57,'0',1,'2025-02-12 14:07:30',1,0,'2025-02-12 14:07:30',1),(129,'/upload_01/upload1/20250208/2025020816080993143.png','/upload_01/upload1/20250208/2025020816080993143.png',0,0,58,'0',1,'2025-02-12 14:07:30',1,0,'2025-02-12 14:07:30',1),(130,'/upload_01/upload1/20250208/2025020816080516461.png','/upload_01/upload1/20250208/2025020816080516461.png',0,0,59,'0',1,'2025-02-12 14:07:30',1,0,'2025-02-12 14:07:30',1),(131,'/upload_01/upload1/20250208/2025020816080758395.png','/upload_01/upload1/20250208/2025020816080758395.png',0,0,59,'0',1,'2025-02-12 14:07:30',1,0,'2025-02-12 14:07:30',1),(132,'/upload_01/upload1/20250208/2025020816080234956.png','/upload_01/upload1/20250208/2025020816080234956.png',0,0,60,'0',1,'2025-02-12 14:07:30',1,0,'2025-02-12 14:07:30',1),(133,'/upload_01/upload1/20250208/2025020816080463113.png','/upload_01/upload1/20250208/2025020816080463113.png',0,0,60,'0',1,'2025-02-12 14:07:30',1,0,'2025-02-12 14:07:30',1),(134,'/upload_01/upload1/20250208/2025020816080143531.png','/upload_01/upload1/20250208/2025020816080143531.png',0,0,61,'0',1,'2025-02-12 14:07:30',1,0,'2025-02-12 14:07:30',1),(135,'/upload_01/upload1/20250208/2025020816075712729.png','/upload_01/upload1/20250208/2025020816075712729.png',0,0,62,'0',1,'2025-02-12 14:07:30',1,0,'2025-02-12 14:07:30',1),(136,'/upload_01/upload1/20250208/2025020816075995759.png','/upload_01/upload1/20250208/2025020816075995759.png',0,0,62,'0',1,'2025-02-12 14:07:30',1,0,'2025-02-12 14:07:30',1),(137,'/upload_01/upload1/20250208/2025020816075218565.png','/upload_01/upload1/20250208/2025020816075218565.png',0,0,63,'0',1,'2025-02-12 14:07:30',1,0,'2025-02-12 14:07:30',1),(138,'/upload_01/upload1/20250208/2025020816075543729.png','/upload_01/upload1/20250208/2025020816075543729.png',0,0,63,'0',1,'2025-02-12 14:07:30',1,0,'2025-02-12 14:07:30',1),(139,'/upload_01/upload1/20250208/2025020816074826133.png','/upload_01/upload1/20250208/2025020816074826133.png',0,0,64,'0',1,'2025-02-12 14:07:30',1,0,'2025-02-12 14:07:30',1),(140,'/upload_01/upload1/20250208/2025020816074617849.jpeg','/upload_01/upload1/20250208/2025020816074617849.jpeg',0,0,65,'0',1,'2025-02-12 14:07:30',1,0,'2025-02-12 14:07:30',1),(141,'/upload_01/upload1/20250208/2025020816074777244.jpeg','/upload_01/upload1/20250208/2025020816074777244.jpeg',0,0,65,'0',1,'2025-02-12 14:07:30',1,0,'2025-02-12 14:07:30',1),(142,'/upload_01/upload1/20250208/2025020816074346554.png','/upload_01/upload1/20250208/2025020816074346554.png',0,0,66,'0',1,'2025-02-12 14:07:30',1,0,'2025-02-12 14:07:30',1),(143,'/upload_01/upload1/20250208/2025020816073675651.png','/upload_01/upload1/20250208/2025020816073675651.png',0,0,67,'0',1,'2025-02-12 14:07:30',1,0,'2025-02-12 14:07:30',1),(144,'/upload_01/upload1/20250208/2025020816073928985.png','/upload_01/upload1/20250208/2025020816073928985.png',0,0,67,'0',1,'2025-02-12 14:07:30',1,0,'2025-02-12 14:07:30',1),(145,'/upload_01/upload1/20250208/2025020816074120914.png','/upload_01/upload1/20250208/2025020816074120914.png',0,0,67,'0',1,'2025-02-12 14:07:30',1,0,'2025-02-12 14:07:30',1),(146,'/upload_01/upload1/20250208/2025020816073227161.png','/upload_01/upload1/20250208/2025020816073227161.png',0,0,68,'0',1,'2025-02-12 14:07:30',1,0,'2025-02-12 14:07:30',1),(147,'/upload_01/upload1/20250208/2025020816073436778.png','/upload_01/upload1/20250208/2025020816073436778.png',0,0,68,'0',1,'2025-02-12 14:07:30',1,0,'2025-02-12 14:07:30',1),(148,'/upload_01/upload1/20250208/2025020816073578126.png','/upload_01/upload1/20250208/2025020816073578126.png',0,0,68,'0',1,'2025-02-12 14:07:30',1,0,'2025-02-12 14:07:30',1),(149,'/upload_01/upload1/20250208/2025020816072820082.png','/upload_01/upload1/20250208/2025020816072820082.png',0,0,69,'0',1,'2025-02-12 14:07:30',1,0,'2025-02-12 14:07:30',1),(150,'/upload_01/upload1/20250208/2025020816073081320.png','/upload_01/upload1/20250208/2025020816073081320.png',0,0,69,'0',1,'2025-02-12 14:07:30',1,0,'2025-02-12 14:07:30',1),(151,'/upload_01/upload1/20250208/2025020816072347811.png','/upload_01/upload1/20250208/2025020816072347811.png',0,0,70,'0',1,'2025-02-12 14:07:30',1,0,'2025-02-12 14:07:30',1),(152,'/upload_01/upload1/20250208/2025020816072528534.png','/upload_01/upload1/20250208/2025020816072528534.png',0,0,70,'0',1,'2025-02-12 14:07:30',1,0,'2025-02-12 14:07:30',1),(153,'/upload_01/upload1/20250208/2025020816072179063.png','/upload_01/upload1/20250208/2025020816072179063.png',0,0,71,'0',1,'2025-02-12 14:07:30',1,0,'2025-02-12 14:07:30',1),(154,'/upload_01/upload1/20250208/2025020816071956792.png','/upload_01/upload1/20250208/2025020816071956792.png',0,0,72,'0',1,'2025-02-12 14:07:30',1,0,'2025-02-12 14:07:30',1),(155,'/upload_01/upload1/20250208/2025020816071691380.png','/upload_01/upload1/20250208/2025020816071691380.png',0,0,73,'0',1,'2025-02-12 14:07:30',1,0,'2025-02-12 14:07:30',1),(156,'/upload_01/upload1/20250208/2025020816071486084.png','/upload_01/upload1/20250208/2025020816071486084.png',0,0,74,'0',1,'2025-02-12 14:07:30',1,0,'2025-02-12 14:07:30',1),(157,'/upload_01/upload1/20250208/2025020816071128422.png','/upload_01/upload1/20250208/2025020816071128422.png',0,0,75,'0',1,'2025-02-12 14:07:30',1,0,'2025-02-12 14:07:30',1),(158,'/upload_01/upload1/20250208/2025020816071357157.png','/upload_01/upload1/20250208/2025020816071357157.png',0,0,75,'0',1,'2025-02-12 14:07:30',1,0,'2025-02-12 14:07:30',1),(159,'/upload_01/upload1/20250208/2025020816070939222.jpeg','/upload_01/upload1/20250208/2025020816070939222.jpeg',0,0,76,'0',1,'2025-02-12 14:07:30',1,0,'2025-02-12 14:07:30',1),(160,'/upload_01/upload1/20250208/2025020816070688956.png','/upload_01/upload1/20250208/2025020816070688956.png',0,0,77,'0',1,'2025-02-12 14:07:30',1,0,'2025-02-12 14:07:30',1),(161,'/upload_01/upload1/20250208/2025020816070345402.png','/upload_01/upload1/20250208/2025020816070345402.png',0,0,78,'0',1,'2025-02-12 14:07:30',1,0,'2025-02-12 14:07:30',1),(162,'/upload_01/upload1/20250208/2025020816070516094.png','/upload_01/upload1/20250208/2025020816070516094.png',0,0,78,'0',1,'2025-02-12 14:07:30',1,0,'2025-02-12 14:07:30',1),(163,'/upload_01/upload1/20250208/2025020816065891415.png','/upload_01/upload1/20250208/2025020816065891415.png',0,0,79,'0',1,'2025-02-12 14:07:30',1,0,'2025-02-12 14:07:30',1),(164,'/upload_01/upload1/20250208/2025020816070089426.png','/upload_01/upload1/20250208/2025020816070089426.png',0,0,79,'0',1,'2025-02-12 14:07:30',1,0,'2025-02-12 14:07:30',1),(165,'/upload_01/upload1/20250208/2025020816065441480.png','/upload_01/upload1/20250208/2025020816065441480.png',0,0,80,'0',1,'2025-02-12 14:07:30',1,0,'2025-02-12 14:07:30',1),(166,'/upload_01/upload1/20250208/2025020816065584788.png','/upload_01/upload1/20250208/2025020816065584788.png',0,0,80,'0',1,'2025-02-12 14:07:30',1,0,'2025-02-12 14:07:30',1),(167,'/upload_01/upload1/20250208/2025020816065664922.png','/upload_01/upload1/20250208/2025020816065664922.png',0,0,80,'0',1,'2025-02-12 14:07:30',1,0,'2025-02-12 14:07:30',1),(168,'/upload_01/upload1/20250208/2025020816065075575.png','/upload_01/upload1/20250208/2025020816065075575.png',0,0,81,'0',1,'2025-02-12 14:07:30',1,0,'2025-02-12 14:07:30',1),(169,'/upload_01/upload1/20250208/2025020816064079025.png','/upload_01/upload1/20250208/2025020816064079025.png',0,0,82,'0',1,'2025-02-12 14:07:30',1,0,'2025-02-12 14:07:30',1),(170,'/upload_01/upload1/20250208/2025020816064118074.png','/upload_01/upload1/20250208/2025020816064118074.png',0,0,82,'0',1,'2025-02-12 14:07:30',1,0,'2025-02-12 14:07:30',1),(171,'/upload_01/upload1/20250208/2025020816064334393.png','/upload_01/upload1/20250208/2025020816064334393.png',0,0,82,'0',1,'2025-02-12 14:07:30',1,0,'2025-02-12 14:07:30',1),(172,'/upload_01/upload1/20250208/2025020816064629749.png','/upload_01/upload1/20250208/2025020816064629749.png',0,0,82,'0',1,'2025-02-12 14:07:30',1,0,'2025-02-12 14:07:30',1),(173,'/upload_01/upload1/20250208/2025020816064814602.png','/upload_01/upload1/20250208/2025020816064814602.png',0,0,82,'0',1,'2025-02-12 14:07:30',1,0,'2025-02-12 14:07:30',1),(174,'/upload_01/upload1/20250208/2025020816063363231.png','/upload_01/upload1/20250208/2025020816063363231.png',0,0,83,'0',1,'2025-02-12 14:07:30',1,0,'2025-02-12 14:07:30',1),(175,'/upload_01/upload1/20250208/2025020816063691890.png','/upload_01/upload1/20250208/2025020816063691890.png',0,0,83,'0',1,'2025-02-12 14:07:30',1,0,'2025-02-12 14:07:30',1),(176,'/upload_01/upload1/20250208/2025020816063834710.png','/upload_01/upload1/20250208/2025020816063834710.png',0,0,83,'0',1,'2025-02-12 14:07:30',1,0,'2025-02-12 14:07:30',1),(177,'/upload_01/upload1/20250208/2025020816062120175.png','/upload_01/upload1/20250208/2025020816062120175.png',0,0,84,'0',1,'2025-02-12 14:07:30',1,0,'2025-02-12 14:07:30',1),(178,'/upload_01/upload1/20250208/2025020816062231463.png','/upload_01/upload1/20250208/2025020816062231463.png',0,0,84,'0',1,'2025-02-12 14:07:30',1,0,'2025-02-12 14:07:30',1),(179,'/upload_01/upload1/20250208/2025020816062332650.png','/upload_01/upload1/20250208/2025020816062332650.png',0,0,84,'0',1,'2025-02-12 14:07:30',1,0,'2025-02-12 14:07:30',1),(180,'/upload_01/upload1/20250208/2025020816062519250.png','/upload_01/upload1/20250208/2025020816062519250.png',0,0,84,'0',1,'2025-02-12 14:07:30',1,0,'2025-02-12 14:07:30',1),(181,'/upload_01/upload1/20250208/2025020816062735535.png','/upload_01/upload1/20250208/2025020816062735535.png',0,0,84,'0',1,'2025-02-12 14:07:30',1,0,'2025-02-12 14:07:30',1),(182,'/upload_01/upload1/20250208/2025020816063096781.png','/upload_01/upload1/20250208/2025020816063096781.png',0,0,84,'0',1,'2025-02-12 14:07:30',1,0,'2025-02-12 14:07:30',1),(183,'/upload_01/upload1/20250208/2025020816045368291.png','/upload_01/upload1/20250208/2025020816045368291.png',0,0,85,'0',1,'2025-02-12 14:07:30',1,0,'2025-02-12 14:07:30',1),(184,'/upload_01/upload1/20250208/2025020816045695609.png','/upload_01/upload1/20250208/2025020816045695609.png',0,0,85,'0',1,'2025-02-12 14:07:30',1,0,'2025-02-12 14:07:30',1),(185,'/upload_01/upload1/20250208/2025020816045624309.png','/upload_01/upload1/20250208/2025020816045624309.png',0,0,85,'0',1,'2025-02-12 14:07:30',1,0,'2025-02-12 14:07:30',1),(186,'/upload_01/upload1/20250208/2025020816050315189.png','/upload_01/upload1/20250208/2025020816050315189.png',0,0,85,'0',1,'2025-02-12 14:07:30',1,0,'2025-02-12 14:07:30',1),(187,'/upload_01/upload1/20250208/2025020816050688406.png','/upload_01/upload1/20250208/2025020816050688406.png',0,0,85,'0',1,'2025-02-12 14:07:30',1,0,'2025-02-12 14:07:30',1),(188,'/upload_01/upload1/20250208/2025020816050828198.png','/upload_01/upload1/20250208/2025020816050828198.png',0,0,85,'0',1,'2025-02-12 14:07:30',1,0,'2025-02-12 14:07:30',1),(189,'/upload_01/upload1/20250208/2025020816051227939.png','/upload_01/upload1/20250208/2025020816051227939.png',0,0,85,'0',1,'2025-02-12 14:07:30',1,0,'2025-02-12 14:07:30',1),(190,'upload_01/upload1/20250208/2025020816084382676.webp','upload_01/upload1/20250208/2025020816084382676.webp',0,0,48,'0',1,'2025-02-12 14:09:26',1,0,'2025-02-12 14:09:26',1),(191,'upload_01/upload1/20250208/2025020816084536920.webp','upload_01/upload1/20250208/2025020816084536920.webp',0,0,48,'0',1,'2025-02-12 14:09:26',1,0,'2025-02-12 14:09:26',1),(192,'upload_01/upload1/20250208/2025020816084743550.webp','upload_01/upload1/20250208/2025020816084743550.webp',0,0,48,'0',1,'2025-02-12 14:09:26',1,0,'2025-02-12 14:09:26',1),(193,'upload_01/upload1/20250208/2025020816084890091.webp','upload_01/upload1/20250208/2025020816084890091.webp',0,0,48,'0',1,'2025-02-12 14:09:26',1,0,'2025-02-12 14:09:26',1),(194,'upload_01/upload1/20250208/2025020816084856093.png','upload_01/upload1/20250208/2025020816084856093.png',0,0,48,'0',1,'2025-02-12 14:09:26',1,0,'2025-02-12 14:09:26',1),(195,'upload_01/upload1/20250208/2025020816085122623.png','upload_01/upload1/20250208/2025020816085122623.png',0,0,48,'0',1,'2025-02-12 14:09:26',1,0,'2025-02-12 14:09:26',1),(196,'upload_01/upload1/20250208/2025020816085325070.png','upload_01/upload1/20250208/2025020816085325070.png',0,0,48,'0',1,'2025-02-12 14:09:26',1,0,'2025-02-12 14:09:26',1),(197,'upload_01/upload1/20250208/2025020816085587241.png','upload_01/upload1/20250208/2025020816085587241.png',0,0,48,'0',1,'2025-02-12 14:09:26',1,0,'2025-02-12 14:09:26',1),(198,'upload_01/upload1/20250208/2025020816085882033.png','upload_01/upload1/20250208/2025020816085882033.png',0,0,48,'0',1,'2025-02-12 14:09:26',1,0,'2025-02-12 14:09:26',1),(199,'upload_01/upload1/20250208/2025020816093184054.png','upload_01/upload1/20250208/2025020816093184054.png',0,0,46,'0',1,'2025-02-12 14:11:01',1,0,'2025-02-12 14:11:01',1),(200,'upload_01/upload1/20250208/2025020816093664762.png','upload_01/upload1/20250208/2025020816093664762.png',0,0,46,'0',1,'2025-02-12 14:11:01',1,0,'2025-02-12 14:11:01',1),(201,'upload_01/upload1/20250208/2025020816093884819.png','upload_01/upload1/20250208/2025020816093884819.png',0,0,46,'0',1,'2025-02-12 14:11:01',1,0,'2025-02-12 14:11:01',1),(202,'upload_01/upload1/20250208/2025020816094290234.png','upload_01/upload1/20250208/2025020816094290234.png',0,0,46,'0',1,'2025-02-12 14:11:01',1,0,'2025-02-12 14:11:01',1),(203,'upload_01/upload1/20250208/2025020816094562102.png','upload_01/upload1/20250208/2025020816094562102.png',0,0,46,'0',1,'2025-02-12 14:11:01',1,0,'2025-02-12 14:11:01',1);
/*!40000 ALTER TABLE `ks_porn_media` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ks_porn_pay`
--

DROP TABLE IF EXISTS `ks_porn_pay`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ks_porn_pay` (
`id` int NOT NULL AUTO_INCREMENT,
`aff` int NOT NULL COMMENT '用户aff',
`coins` int NOT NULL DEFAULT '0' COMMENT '购买时的价格',
`porn_id` int NOT NULL COMMENT '黄游id',
`type` tinyint NOT NULL DEFAULT '1' COMMENT '类型 1 购买',
`status` tinyint(1) DEFAULT '0' COMMENT '状态 0 未支付 1已完成',
`created_at` timestamp NOT NULL COMMENT '购买时间',
`updated_at` timestamp NOT NULL COMMENT '更新时间',
PRIMARY KEY (`id`) USING BTREE,
KEY `aff` (`aff`,`porn_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci ROW_FORMAT=COMPACT COMMENT='黄游购买记录';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ks_porn_pay`
--

LOCK TABLES `ks_porn_pay` WRITE;
/*!40000 ALTER TABLE `ks_porn_pay` DISABLE KEYS */;
/*!40000 ALTER TABLE `ks_porn_pay` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ks_porn_tags`
--

DROP TABLE IF EXISTS `ks_porn_tags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ks_porn_tags` (
`id` int unsigned NOT NULL AUTO_INCREMENT,
`name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '标签',
`sort` int NOT NULL COMMENT '排序',
`status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '列表显示状态',
`created_at` timestamp NULL DEFAULT NULL,
`updated_at` timestamp NULL DEFAULT NULL,
PRIMARY KEY (`id`) USING BTREE,
UNIQUE KEY `name` (`name`),
KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC COMMENT='黄游标签表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ks_porn_tags`
--

LOCK TABLES `ks_porn_tags` WRITE;
/*!40000 ALTER TABLE `ks_porn_tags` DISABLE KEYS */;
INSERT INTO `ks_porn_tags` VALUES (1,'安卓游戏',100,1,'2025-01-31 09:48:39','2025-01-31 09:49:08'),(2,'IOS游戏',80,1,'2025-01-31 09:49:21','2025-01-31 09:49:21'),(3,'PC游戏',70,1,'2025-01-31 09:49:38','2025-01-31 09:49:38');
/*!40000 ALTER TABLE `ks_porn_tags` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ks_picture`
--

DROP TABLE IF EXISTS `ks_picture`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ks_picture` (
`id` int NOT NULL AUTO_INCREMENT,
`p_id` varchar(20) NOT NULL DEFAULT '0' COMMENT '资源中心id',
`title` varchar(255) NOT NULL DEFAULT '' COMMENT '标题',
`desc` varchar(255) NOT NULL DEFAULT '' COMMENT '描述',
`thumb` varchar(255) NOT NULL DEFAULT '' COMMENT '封面图',
`category_id` int NOT NULL DEFAULT '0' COMMENT '图集分类ID',
`tags` varchar(255) NOT NULL DEFAULT '' COMMENT '标签',
`is_free` tinyint NOT NULL DEFAULT '0' COMMENT '0 免费 1 vip 2  钻石（金币）',
`rating` int NOT NULL DEFAULT '0' COMMENT '浏览数',
`favorites` int NOT NULL DEFAULT '0' COMMENT '收藏人数',
`refresh_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '刷新时间',
`recommend` tinyint NOT NULL DEFAULT '0' COMMENT '是否推荐',
`coins` int NOT NULL DEFAULT '0' COMMENT '金币',
`status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1上架0下架',
`total` int NOT NULL DEFAULT '0' COMMENT '总图',
PRIMARY KEY (`id`) USING BTREE,
KEY `idx_title` (`title`) USING BTREE,
FULLTEXT KEY `tags` (`tags`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 ROW_FORMAT=DYNAMIC COMMENT='图集表';
/*!40101 SET character_set_client = @saved_cs_client */;
--
-- Table structure for table `ks_picture_favorites`
--

DROP TABLE IF EXISTS `ks_picture_favorites`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ks_picture_favorites` (
`id` int unsigned NOT NULL AUTO_INCREMENT,
`uid` int NOT NULL DEFAULT '0' COMMENT '用户id',
`zy_id` int NOT NULL DEFAULT '0' COMMENT '小说id',
`created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP COMMENT '创建更新时间',
PRIMARY KEY (`id`),
KEY `user_id` (`uid`),
KEY `comics_id` (`zy_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci COMMENT='小说收藏表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ks_picture_favorites`
--

LOCK TABLES `ks_picture_favorites` WRITE;
/*!40000 ALTER TABLE `ks_picture_favorites` DISABLE KEYS */;
/*!40000 ALTER TABLE `ks_picture_favorites` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ks_picture_pay`
--

DROP TABLE IF EXISTS `ks_picture_pay`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ks_picture_pay` (
`id` int NOT NULL AUTO_INCREMENT,
`uid` int NOT NULL COMMENT '用户id',
`coins` int NOT NULL DEFAULT '0' COMMENT '购买时的价格',
`zy_id` int NOT NULL COMMENT '资源小说编号id',
`type` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT '1' COMMENT '类型 购买 次数 赠送',
`created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP COMMENT '购买时间',
PRIMARY KEY (`id`) USING BTREE,
KEY `uid` (`uid`,`zy_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci ROW_FORMAT=COMPACT COMMENT='小说购买记录';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ks_picture_pay`
--

LOCK TABLES `ks_picture_pay` WRITE;
/*!40000 ALTER TABLE `ks_picture_pay` DISABLE KEYS */;
/*!40000 ALTER TABLE `ks_picture_pay` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ks_picture_src`
--

DROP TABLE IF EXISTS `ks_picture_src`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ks_picture_src` (
`id` int NOT NULL AUTO_INCREMENT,
`picture_id` int NOT NULL DEFAULT '0' COMMENT '图集ID',
`img_url` varchar(255) NOT NULL DEFAULT '' COMMENT '图片地址',
`img_width` varchar(10) NOT NULL DEFAULT '0' COMMENT '图片宽',
`img_height` varchar(10) NOT NULL DEFAULT '0' COMMENT '图片高',
PRIMARY KEY (`id`) USING BTREE,
KEY `m_id` (`picture_id`) USING BTREE,
KEY `img_url` (`img_url`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 ROW_FORMAT=DYNAMIC COMMENT='图集图片文件';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ks_picture_src`
--

DROP TABLE IF EXISTS `ks_picture_tab`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ks_picture_tab` (
`tab_id` int NOT NULL AUTO_INCREMENT,
`tab_name` varchar(20) NOT NULL COMMENT '导航蓝标签组',
`tags_str` varchar(1000) DEFAULT NULL COMMENT '标签',
`sort_num` smallint unsigned NOT NULL COMMENT '排序',
`status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 启用',
`is_tab` tinyint(1) NOT NULL COMMENT '主页展示',
`is_category` tinyint(1) NOT NULL COMMENT '分类过滤展示',
`show_style` enum('H-1*N','V-3*N','V-2*N','') DEFAULT NULL,
`show_number` tinyint DEFAULT '6',
PRIMARY KEY (`tab_id`),
KEY `status` (`status`),
KEY `is_tab` (`is_tab`),
KEY `is_category` (`is_category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 ROW_FORMAT=DYNAMIC COMMENT='漫画tab栏';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ks_picture_tab`
--

LOCK TABLES `ks_picture_tab` WRITE;
/*!40000 ALTER TABLE `ks_picture_tab` DISABLE KEYS */;
INSERT INTO `ks_picture_tab` VALUES (1,'可爱男孩','小奶狗,可爱男孩,小鲜肉',6,1,1,1,'V-2*N',6),(2,'亚洲帅哥','台湾写真,台湾,日本,韩系',4,1,0,1,'V-3*N',6),(12,'诱惑','诱惑,湿身,健身自拍',11,1,1,1,'V-3*N',6),(13,'写真','写真,男体写真,大包,床照',1,1,1,1,'V-2*N',6),(14,'肌肉帅哥','体育生,肌肉,胸肌,腹肌',39,1,1,1,'H-1*N',6),(21,'泰国写真','泰国',7,1,0,1,'V-3*N',6),(22,'日本,男体写真,小鲜肉','腹肌',2,1,1,1,'V-2*N',6),(23,'韩系','体育生,肌肉,韩系',3,1,0,1,'H-1*N',6),(24,'空少','空少',4,1,0,1,'H-1*N',6),(25,'湿身','湿身',5,1,0,1,'H-1*N',6),(26,'大包','大包',7,1,0,1,'H-1*N',6),(27,'大陆写真','体育生,肌肉,腹肌,健身自拍',27,1,0,1,'H-1*N',6);
/*!40000 ALTER TABLE `ks_picture_tab` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ks_picture_tags`
--

DROP TABLE IF EXISTS `ks_picture_tags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ks_picture_tags` (
`id` int unsigned NOT NULL AUTO_INCREMENT,
`name` varchar(50) NOT NULL DEFAULT '' COMMENT '标签',
`sort_num` int DEFAULT NULL COMMENT '排序',
`created_at` int NOT NULL DEFAULT '0' COMMENT '创建时间',
`updated_at` int DEFAULT NULL,
`img_url` varchar(255) NOT NULL DEFAULT '' COMMENT '标签封面图',
`home` tinyint NOT NULL COMMENT '首页显示',
`status` tinyint NOT NULL DEFAULT '1' COMMENT '列表显示状态',
`user_up` tinyint NOT NULL DEFAULT '1' COMMENT '允许用户上传',
`horizontal_img` varchar(255) NOT NULL COMMENT '横向图片',
`description` varchar(255) NOT NULL COMMENT '描述',
PRIMARY KEY (`id`) USING BTREE,
UNIQUE KEY `vid` (`name`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 ROW_FORMAT=DYNAMIC COMMENT='标签';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ks_picture_tags`
--

LOCK TABLES `ks_picture_tags` WRITE;
/*!40000 ALTER TABLE `ks_picture_tags` DISABLE KEYS */;
INSERT INTO `ks_picture_tags` VALUES (99,'小鲜肉',0,0,NULL,'',0,1,1,'',''),(100,'精致',0,0,NULL,'',0,1,1,'',''),(101,'韩系',0,0,NULL,'',0,1,1,'',''),(102,'可爱男孩',0,0,NULL,'',0,1,1,'',''),(103,'健身自拍',0,0,NULL,'',0,1,1,'',''),(104,'日本',0,0,NULL,'',0,1,1,'',''),(105,'韩国',0,0,NULL,'',0,1,1,'',''),(106,'泰国',0,0,NULL,'',0,1,1,'',''),(107,'腹肌',0,0,NULL,'',0,1,1,'',''),(108,'胸肌',0,0,NULL,'',0,1,1,'',''),(109,'肌肉',0,0,NULL,'',0,1,1,'',''),(110,'湿身',0,0,NULL,'',0,1,1,'',''),(111,'床照',0,0,NULL,'',0,1,1,'',''),(112,'体育生',0,0,NULL,'',0,1,1,'',''),(113,'台湾',0,0,NULL,'',0,1,1,'',''),(114,'空少',0,0,NULL,'',0,1,1,'',''),(115,'捆绑',0,0,NULL,'',0,1,1,'',''),(116,'正装',0,0,NULL,'',0,1,1,'',''),(117,'大包',0,0,NULL,'',0,1,1,'',''),(118,'小奶狗',0,0,NULL,'',0,1,1,'',''),(119,'男体写真',0,0,NULL,'',0,1,1,'',''),(120,'诱惑',0,0,NULL,'',0,1,1,'',''),(121,'台湾写真',3,0,NULL,'',0,1,1,'',''),(122,'写真',0,0,NULL,'',0,1,1,'','');
/*!40000 ALTER TABLE `ks_picture_tags` ENABLE KEYS */;
UNLOCK TABLES;
--
-- Table structure for table `ks_cartoon`
--

DROP TABLE IF EXISTS `ks_cartoon`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ks_cartoon` (
`id` int NOT NULL AUTO_INCREMENT,
`category_id` int DEFAULT '0' COMMENT '分类ID',
`title` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '影片标题',
`desc` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT '' COMMENT '简介',
`actors` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '演员',
`category` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '分类',
`country` varchar(255) NOT NULL DEFAULT '' COMMENT '国家',
`directors` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '导演',
`is_series` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 电影 2电视剧',
`is_free` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0=免费,1=vip,2=金币',
`cover` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '封面',
`tags` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '影片标签',
`langs` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '语言',
`year_released` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '影片上映年',
`video_num` int NOT NULL DEFAULT '0' COMMENT '视频数量',
`like_count` int unsigned NOT NULL DEFAULT '0' COMMENT '点赞数',
`play_count` int unsigned NOT NULL DEFAULT '0' COMMENT '播放数',
`com_count` int unsigned NOT NULL DEFAULT '0' COMMENT '评论数',
`pay_count` int NOT NULL DEFAULT '0' COMMENT '售卖次数',
`status` tinyint NOT NULL DEFAULT '0' COMMENT '0下架1上架',
`refresh_at` datetime DEFAULT NULL COMMENT '刷新时间',
`created_at` datetime DEFAULT NULL COMMENT '创建时间',
`source_id` varchar(32) NOT NULL COMMENT '采集资源ID 采集识别',
PRIMARY KEY (`id`) USING BTREE,
KEY `refresh_at` (`refresh_at`) USING BTREE,
KEY `title` (`title`(255)) USING BTREE,
KEY `status` (`status`) USING BTREE,
KEY `source_id` (`source_id`) USING BTREE,
KEY `ft_category` (`category_id`) USING BTREE,
FULLTEXT KEY `full_tags` (`tags`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='动漫表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ks_cartoon`
--

LOCK TABLES `ks_cartoon` WRITE;
/*!40000 ALTER TABLE `ks_cartoon` DISABLE KEYS */;
INSERT INTO `ks_cartoon` VALUES (1,1,'[BL耽美 ]爱情可以分割吗','','未知','BL耽美','未知','未知',0,0,'/upload_01/upload1/20250214/2025021422044254546.jpeg','','未知','未知',1,0,0,0,0,1,'2025-02-15 00:03:45','2025-02-15 00:03:45','fhg_712'),(2,1,'[BL耽美 ]恋爱暴君ova1','','未知','BL耽美','未知','未知',0,0,'/upload_01/upload1/20250214/2025021422044166990.jpeg','','未知','未知',1,0,0,0,0,1,'2025-02-15 00:03:45','2025-02-15 00:03:45','fhg_711'),(3,1,'[BL耽美 ]恋爱暴君ova2','','未知','BL耽美','未知','未知',0,0,'/upload_01/upload1/20250214/2025021422044017359.jpeg','','未知','未知',1,0,0,0,0,1,'2025-02-15 00:03:45','2025-02-15 00:03:45','fhg_710'),(4,1,'[BL耽美 ]Tight rope 钢索危情 01','','未知','BL耽美','未知','未知',0,0,'/upload_01/upload1/20250214/2025021423131694922.jpeg','','未知','未知',1,0,0,0,0,1,'2025-02-15 00:03:45','2025-02-15 00:03:45','fhg_709'),(5,1,'[BL耽美 ]保育员的求婚 上','','未知','BL耽美','未知','未知',0,0,'/upload_01/upload1/20250214/2025021422043871004.jpeg','','未知','未知',1,0,0,0,0,1,'2025-02-15 00:03:45','2025-02-15 00:03:45','fhg_51'),(6,1,'[BL耽美 ]授课','','未知','BL耽美','未知','未知',0,0,'/upload_01/upload1/20250214/2025021422043576722.jpeg','','未知','未知',1,0,0,0,0,1,'2025-02-15 00:03:46','2025-02-15 00:03:46','fhg_1');
/*!40000 ALTER TABLE `ks_cartoon` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ks_cartoon_category`
--

DROP TABLE IF EXISTS `ks_cartoon_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ks_cartoon_category` (
`id` int NOT NULL AUTO_INCREMENT,
`title` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '标题',
`sub_title` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '副标题',
`thumb` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT '' COMMENT '封面',
`rating` int NOT NULL COMMENT '点击量',
`type` tinyint NOT NULL COMMENT '类型 0普通 1最多喜欢 2畅销榜 3最新',
`is_recommend` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1 推荐',
`show_style` tinyint NOT NULL COMMENT '0:1*3 1:1*2 2:1*1 3:1*N',
`show_max` tinyint NOT NULL DEFAULT '0' COMMENT '默认最大展示数量',
`status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态 ',
`created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
`updated_at` timestamp NULL DEFAULT NULL COMMENT '更新时间',
`sort` int DEFAULT '0' COMMENT '排序',
PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC COMMENT='动漫分类';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ks_cartoon_category`
--

LOCK TABLES `ks_cartoon_category` WRITE;
/*!40000 ALTER TABLE `ks_cartoon_category` DISABLE KEYS */;
INSERT INTO `ks_cartoon_category` VALUES (1,'BL耽美','BL耽美','',0,0,0,0,9,1,'2025-02-03 09:02:32','2025-02-14 17:09:58',100);
/*!40000 ALTER TABLE `ks_cartoon_category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ks_cartoon_chapters`
--

DROP TABLE IF EXISTS `ks_cartoon_chapters`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ks_cartoon_chapters` (
`id` int NOT NULL AUTO_INCREMENT,
`pid` int NOT NULL DEFAULT '0' COMMENT '动漫ID',
`cover` varchar(255) NOT NULL DEFAULT '' COMMENT '封面',
`source` varchar(255) NOT NULL DEFAULT '' COMMENT '影片资源 电影',
`duration` int unsigned NOT NULL DEFAULT '0' COMMENT '时长秒',
`width` int NOT NULL DEFAULT '0' COMMENT '宽度',
`height` int NOT NULL DEFAULT '0' COMMENT '高度',
`sort` int NOT NULL DEFAULT '0' COMMENT '剧集排序',
`type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 电影 2电视剧',
`coins` int unsigned NOT NULL DEFAULT '0' COMMENT '定价',
`is_free` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否免费 0 收费 1 免费',
`like_count` int NOT NULL DEFAULT '0' COMMENT '点赞数',
`play_count` int NOT NULL DEFAULT '0' COMMENT '播放数',
`com_count` int NOT NULL DEFAULT '0' COMMENT '评论数',
`pay_count` int DEFAULT '0' COMMENT '售卖次数',
`status` tinyint NOT NULL DEFAULT '0' COMMENT '0下架1上架',
`refresh_at` datetime DEFAULT NULL COMMENT '刷新时间',
`created_at` datetime DEFAULT NULL COMMENT '创建时间',
`source_id` int NOT NULL DEFAULT '0' COMMENT '资源ID 采集识别',
`source_video_id` varchar(32) NOT NULL DEFAULT '0' COMMENT '资源视频ID 采集识别',
`title` varchar(255) NOT NULL DEFAULT '' COMMENT '标题',
PRIMARY KEY (`id`) USING BTREE,
KEY `status` (`status`) USING BTREE,
KEY `pid` (`pid`) USING BTREE,
KEY `source_id` (`source_id`) USING BTREE,
KEY `source_video_id` (`source_video_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='动漫视频表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ks_cartoon_chapters`
--

LOCK TABLES `ks_cartoon_chapters` WRITE;
/*!40000 ALTER TABLE `ks_cartoon_chapters` DISABLE KEYS */;
INSERT INTO `ks_cartoon_chapters` VALUES (1,1,'/upload_01/xiao/20250214/2025021419483948415.jpg','/videos4/081a3c3dd7d676b80b9f6fe677237546/081a3c3dd7d676b80b9f6fe677237546.m3u8',3183,0,0,1,1,0,1,0,0,0,0,1,'2025-02-15 00:03:45','2025-02-15 00:03:45',0,'fhg_33506','1'),(2,2,'/upload_01/xiao/20250214/2025021421242813865.jpg','/videos4/59f68e635ca17e74d2f7717e8780d3c3/59f68e635ca17e74d2f7717e8780d3c3.m3u8',3577,0,0,1,1,0,1,0,0,0,0,1,'2025-02-15 00:03:45','2025-02-15 00:03:45',0,'fhg_33535','1'),(3,3,'/upload_01/xiao/20250214/2025021421251945468.jpg','/videos4/07fee81ca523d5ff504a824966b6f8f7/07fee81ca523d5ff504a824966b6f8f7.m3u8',3597,0,0,1,1,0,1,0,0,0,0,1,'2025-02-15 00:03:45','2025-02-15 00:03:45',0,'fhg_33536','1'),(4,4,'/upload_01/xiao/20250214/2025021422093857406.jpg','/videos4/29247003a8a6c14abd6f4168533046f4/29247003a8a6c14abd6f4168533046f4.m3u8',2847,0,0,1,1,0,1,0,0,0,0,1,'2025-02-15 00:03:45','2025-02-15 00:03:45',0,'fhg_33540','1'),(5,5,'/upload_01/xiao/20250214/2025021419494268947.jpg','/videos4/7dfa55ed1029e259497d6d32e64401d2/7dfa55ed1029e259497d6d32e64401d2.m3u8',953,0,0,1,1,0,1,0,0,0,0,1,'2025-02-15 00:03:45','2025-02-15 00:03:45',0,'fhg_76938','1'),(6,6,'/upload_01/xiao/20250214/2025021419501759580.jpg','/videos4/760ee4024e532944899753ecb69d4eff/760ee4024e532944899753ecb69d4eff.m3u8',193,0,0,1,1,0,1,0,0,0,0,1,'2025-02-15 00:03:46','2025-02-15 00:03:46',0,'fhg_78969','1');
/*!40000 ALTER TABLE `ks_cartoon_chapters` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ks_cartoon_comment`
--

DROP TABLE IF EXISTS `ks_cartoon_comment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ks_cartoon_comment` (
`id` int NOT NULL AUTO_INCREMENT,
`cartoon_id` int unsigned NOT NULL DEFAULT '0' COMMENT '动漫ID',
`pid` int NOT NULL DEFAULT '0' COMMENT '评论ID,默认0(第一层评论)',
`aff` int NOT NULL DEFAULT '0' COMMENT '用户aff',
`comment` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci COMMENT '留言内容',
`status` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '0:待审核 1:审核通过 2.未通过',
`ipstr` varchar(60) NOT NULL DEFAULT '' COMMENT '用户ip',
`cityname` varchar(100) NOT NULL DEFAULT '' COMMENT '定位城市',
`refuse_reason` varchar(255) NOT NULL DEFAULT '' COMMENT '拒绝通过原因',
`created_at` datetime DEFAULT NULL,
`updated_at` datetime DEFAULT NULL,
`is_top` int DEFAULT '0' COMMENT '是否置顶 0未置顶 1已置顶',
`like_num` int DEFAULT '0' COMMENT '点赞数',
PRIMARY KEY (`id`),
KEY `cartoon_id` (`cartoon_id`),
KEY `pid` (`pid`),
KEY `aff` (`aff`),
KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='动漫评论表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ks_cartoon_comment`
--

LOCK TABLES `ks_cartoon_comment` WRITE;
/*!40000 ALTER TABLE `ks_cartoon_comment` DISABLE KEYS */;
/*!40000 ALTER TABLE `ks_cartoon_comment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ks_cartoon_comment_likes`
--

DROP TABLE IF EXISTS `ks_cartoon_comment_likes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ks_cartoon_comment_likes` (
`id` int NOT NULL AUTO_INCREMENT,
`uid` int NOT NULL COMMENT '用户ID',
`comment_id` int NOT NULL COMMENT '评论ID',
`created_at` datetime DEFAULT NULL,
`updated_at` datetime DEFAULT NULL,
PRIMARY KEY (`id`),
KEY `uid` (`uid`),
KEY `comment_id` (`comment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='动漫评论点赞记录';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ks_cartoon_comment_likes`
--

LOCK TABLES `ks_cartoon_comment_likes` WRITE;
/*!40000 ALTER TABLE `ks_cartoon_comment_likes` DISABLE KEYS */;
/*!40000 ALTER TABLE `ks_cartoon_comment_likes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ks_cartoon_like`
--

DROP TABLE IF EXISTS `ks_cartoon_like`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ks_cartoon_like` (
`id` int unsigned NOT NULL AUTO_INCREMENT,
`aff` int DEFAULT '0' COMMENT '用户aff',
`cartoon_id` int DEFAULT '0' COMMENT '动漫id',
`created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
`updated_at` timestamp NULL DEFAULT NULL COMMENT '更新时间',
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='动漫点赞表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ks_cartoon_like`
--

LOCK TABLES `ks_cartoon_like` WRITE;
/*!40000 ALTER TABLE `ks_cartoon_like` DISABLE KEYS */;
/*!40000 ALTER TABLE `ks_cartoon_like` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ks_cartoon_pay`
--

DROP TABLE IF EXISTS `ks_cartoon_pay`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ks_cartoon_pay` (
`id` int NOT NULL AUTO_INCREMENT,
`uid` int NOT NULL COMMENT '用户id',
`coins` int NOT NULL DEFAULT '0' COMMENT '购买时的价格',
`video_id` int NOT NULL COMMENT '视频ID',
`created_at` datetime NOT NULL COMMENT '购买时间',
`cartoon_id` int NOT NULL DEFAULT '0' COMMENT '动漫ID',
PRIMARY KEY (`id`) USING BTREE,
KEY `uid` (`uid`,`video_id`) USING BTREE,
KEY `created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='动漫视频购买记录';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ks_cartoon_pay`
--

LOCK TABLES `ks_cartoon_pay` WRITE;
/*!40000 ALTER TABLE `ks_cartoon_pay` DISABLE KEYS */;
/*!40000 ALTER TABLE `ks_cartoon_pay` ENABLE KEYS */;
UNLOCK TABLES;
-- Table structure for table `ks_story`
--

DROP TABLE IF EXISTS `ks_story`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ks_story` (
`id` int NOT NULL AUTO_INCREMENT,
`origin_id` varchar(60) DEFAULT '',
`title` varchar(255) NOT NULL DEFAULT '' COMMENT '标题',
`desc` varchar(255) NOT NULL COMMENT '描述',
`thumb` varchar(255) DEFAULT '' COMMENT '封面图',
`category_id` int DEFAULT '0' COMMENT '小说系列id',
`is_free` tinyint NOT NULL DEFAULT '0' COMMENT '0 免费 1 vip 2  钻石（金币）',
`rating` int NOT NULL DEFAULT '0' COMMENT '点击量浏览数',
`favorites` int NOT NULL DEFAULT '0' COMMENT '收藏人数',
`recommend` tinyint NOT NULL DEFAULT '0' COMMENT '是否推荐',
`type` tinyint NOT NULL DEFAULT '1' COMMENT '1文字 2有声',
`status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1上架0下架',
`refresh_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '刷新时间',
`tags` varchar(255) DEFAULT 'null' COMMENT '标签',
`update_time` varchar(255) DEFAULT '完结' COMMENT '更新时间 周一 - 周日',
`is_finish` tinyint(1) DEFAULT '1' COMMENT '完结状态 1 已完结 0未完结',
`coins` int DEFAULT '0' COMMENT '购买时价格',
`author` varchar(20) NOT NULL DEFAULT '',
`newest_series` int NOT NULL COMMENT '最新章节',
PRIMARY KEY (`id`) USING BTREE,
KEY `idx_title` (`title`) USING BTREE,
FULLTEXT KEY `tags` (`tags`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 ROW_FORMAT=DYNAMIC COMMENT='小说表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ks_story`
--
LOCK TABLES `ks_story` WRITE;
/*!40000 ALTER TABLE `ks_story` DISABLE KEYS */;
INSERT INTO `ks_story` VALUES (1,'1_sy','白天鹅和他的心机老公','白天鹅和他的心机老公 - 张大吉','',0,1,46984,0,1,1,1,'2024-11-30 12:44:37','穿越重生,玄幻网游,玄幻','2022-07-13 14:27:16',1,0,'白天鹅和他的心机老公 - 张大吉',1),(2,'2_sy','重生成心机纨绔的黑月光','重生成心机纨绔的黑月光 - 扇景','',0,1,92039,0,1,1,1,'2024-11-30 13:19:53','穿越重生,武侠,玄幻','2022-07-13 14:24:34',1,0,'重生成心机纨绔的黑月光 - 扇景',1),(3,'3_sy','这辈子不玩攻略游戏了','这辈子不玩攻略游戏了 - 只雀','',0,1,81703,1,1,1,1,'2024-12-01 19:30:08','玄幻网游,武侠,玄幻','2022-07-13 14:23:26',1,0,'这辈子不玩攻略游戏了 - 只雀',1),(4,'4_sy','御医嫡女在五零','御医嫡女在五零 - 朝露晨曦','',0,1,80806,0,1,1,1,'2024-11-30 13:23:55','穿越重生,武侠,玄幻','2022-07-13 14:21:51',1,0,'御医嫡女在五零 - 朝露晨曦',1),(5,'5_sy','我在娱乐圈当对照组[古穿今]','我在娱乐圈当对照组[古穿今] - 闻久','',0,1,38416,0,1,1,1,'2024-11-30 13:28:12','穿越重生,武侠,玄幻','2022-07-13 14:21:10',1,0,'我在娱乐圈当对照组[古穿今] - 闻久',1),(6,'6_sy','清穿之土著不好惹','清穿之土著不好惹 - 半个水瓶','',0,1,87498,0,1,1,1,'2024-11-30 13:30:39','穿越重生,武侠,玄幻','2022-07-13 14:18:59',1,0,'清穿之土著不好惹 - 半个水瓶',1),(7,'7_sy','藏娇（穿书）','藏娇（穿书） - 小小小邪子','',0,1,52813,0,1,1,1,'2024-11-30 13:37:30','穿越重生,武侠,玄幻','2022-07-13 14:17:26',1,0,'藏娇（穿书） - 小小小邪子',1),(8,'8_sy','重生龙王后我把自己上交了','重生龙王后我把自己上交了 - 其希','',0,1,68554,0,1,1,1,'2024-11-30 13:40:31','穿越重生,武侠,玄幻','2022-07-12 16:03:04',1,0,'重生龙王后我把自己上交了 - 其希',1),(9,'9_sy','我在豪门狗血小说里当攻','我在豪门狗血小说里当攻 - 生歌','',0,1,35025,16,1,1,1,'2024-12-01 01:25:12','穿越重生,武侠,玄幻','2022-07-12 16:02:32',1,0,'我在豪门狗血小说里当攻 - 生歌',1),(10,'10_sy','女配靠抽卡建设荒星','女配靠抽卡建设荒星 - 虎皮喵','',0,1,13395,0,1,1,1,'2024-11-30 13:46:31','穿越重生,武侠,玄幻','2022-07-12 16:00:43',1,0,'女配靠抽卡建设荒星 - 虎皮喵',1),(11,'11_sy','绿茶公主[快穿]','绿茶公主[快穿] - 余微之','',0,1,4294,0,1,1,1,'2024-11-30 13:53:41','穿越重生,武侠,玄幻','2022-07-12 16:00:02',1,0,'绿茶公主[快穿] - 余微之',1),(12,'12_sy','惊爆！我的神棍老公是仙尊','惊爆！我的神棍老公是仙尊 - 耳十','',0,1,44120,7,1,1,1,'2024-06-27 11:20:03','穿越重生,武侠,玄幻','2022-07-12 15:58:52',1,0,'惊爆！我的神棍老公是仙尊 - 耳十',1),(13,'13_sy','绑定花钱系统后我爆红了','绑定花钱系统后我爆红了 - 云殊年','',0,1,77891,1,1,1,1,'2024-12-01 23:23:42','穿越重生,武侠,玄幻','2022-07-12 15:52:53',1,0,'绑定花钱系统后我爆红了 - 云殊年',1),(14,'14_sy','穿成年代文反派的漂亮后妈[七零]','穿成年代文反派的漂亮后妈[七零] - 酒筝','',0,1,46302,1,1,1,1,'2024-12-01 23:02:47','穿越重生,武侠,玄幻','2022-07-12 15:52:50',1,0,'穿成年代文反派的漂亮后妈[七零] - 酒',1),(15,'15_sy','重生后我被病娇宿敌宠上天','重生后我被病娇宿敌宠上天 - 岁岁红','',0,1,62677,0,1,1,1,'2024-11-30 13:56:33','穿越重生,武侠,玄幻','2022-07-11 16:04:00',1,0,'重生后我被病娇宿敌宠上天 - 岁岁红',1),(16,'16_sy','重生后道侣成了死对头','重生后道侣成了死对头 - 烟雨沫凉','',0,1,62903,2,1,1,1,'2024-12-01 13:31:01','穿越重生,武侠,玄幻','2022-07-11 16:03:24',1,0,'重生后道侣成了死对头 - 烟雨沫凉',1),(17,'17_sy','渣攻，你爹来咯！','渣攻，你爹来咯！ - 不吃姜糖','',0,1,71246,0,1,1,1,'2023-09-18 14:02:57','穿越重生,武侠,玄幻','2022-07-11 16:01:37',1,0,'渣攻，你爹来咯！ - 不吃姜糖',1),(18,'18_sy','重生后，末世小祖宗虐暴娱乐圈','重生后，末世小祖宗虐暴娱乐圈 - 陆小黎','',0,1,29437,4,1,1,1,'2024-12-01 09:32:50','穿越重生,武侠,玄幻','2022-07-11 16:01:13',1,0,'重生后，末世小祖宗虐暴娱乐圈 - 陆小黎',1),(19,'19_sy','校园文男主死对头的初恋','校园文男主死对头的初恋 - 林绵绵','',0,1,37729,0,1,1,1,'2024-11-30 14:00:03','穿越重生,武侠,玄幻','2022-07-11 16:00:19',1,0,'校园文男主死对头的初恋 - 林绵绵',1),(20,'20_sy','女霸总穿成作精上求生恋综后','女霸总穿成作精上求生恋综后 - 查干湖水怪','',0,1,27013,0,1,1,1,'2024-11-30 14:06:50','穿越重生,武侠,玄幻','2022-07-11 15:59:43',1,0,'女霸总穿成作精上求生恋综后 - 查干湖水',1),(21,'21_sy','综生存指南','综生存指南 - 即十','',0,1,38126,0,1,1,1,'2024-11-30 14:10:38','穿越重生,武侠,玄幻','2022-07-11 15:57:39',1,0,'综生存指南 - 即十',1),(22,'22_sy','继母不慈','继母不慈 - 张佳音','',0,1,81642,1,1,1,1,'2024-12-01 23:04:55','穿越重生,武侠,玄幻','2022-07-11 15:56:40',1,0,'继母不慈 - 张佳音',1),(23,'23_sy','对照组女配靠赌石在综艺爆红','对照组女配靠赌石在综艺爆红 - 肴北','',0,1,80618,0,1,1,1,'2024-11-30 14:18:23','穿越重生,武侠,玄幻','2022-07-11 15:55:56',1,0,'对照组女配靠赌石在综艺爆红 - 肴北',1),(24,'24_sy','带着智脑宠夫郎[穿越]','带着智脑宠夫郎[穿越] - 糖花糕','',0,1,9911,1,1,1,1,'2024-12-01 23:10:09','穿越重生,武侠,玄幻','2022-07-11 15:53:51',1,0,'带着智脑宠夫郎[穿越] - 糖花糕',1),(25,'25_sy','大理寺卿破案超神','大理寺卿破案超神 - 凤九幽','',0,1,45351,0,1,1,1,'2024-11-30 14:18:48','穿越重生,武侠,玄幻','2022-07-11 15:51:33',1,0,'大理寺卿破案超神 - 凤九幽',1),(26,'26_sy','被嘲不婚不育，我在七零怒生三胎','被嘲不婚不育，我在七零怒生三胎 - 陆小黎','',0,1,30514,0,1,1,1,'2024-11-30 14:20:52','穿越重生,武侠,玄幻','2022-07-11 15:49:17',1,0,'被嘲不婚不育，我在七零怒生三胎 - 陆小',1),(27,'27_sy','黑月光洗白计划','黑月光洗白计划 - 泸酒','',0,1,72054,0,1,1,1,'2024-11-30 14:27:05','穿越重生,武侠,玄幻','2022-07-11 15:49:15',1,0,'黑月光洗白计划 - 泸酒',1),(28,'28_sy','渣攻，你爹来咯！','渣攻，你爹来咯！ - 不吃姜糖','',0,1,5835,0,1,1,1,'2024-11-30 14:33:30','穿越重生,武侠,玄幻','2022-07-09 20:22:18',1,0,'渣攻，你爹来咯！ - 不吃姜糖',1),(29,'29_sy','小可怜穿成豪门小娇妻','小可怜穿成豪门小娇妻 - 眠眠咩','',0,1,67677,0,1,1,1,'2024-11-30 14:45:42','穿越重生,武侠,玄幻','2022-07-09 20:21:24',1,0,'小可怜穿成豪门小娇妻 - 眠眠咩',1),(30,'30_sy','穿回来后异世伴侣成了网游BOSS','穿回来后异世伴侣成了网游BOSS - 杏遥未晚','',0,1,73015,0,1,1,1,'2024-11-30 16:57:11','穿越重生,武侠,玄幻','2022-07-09 20:21:22',1,0,'穿回来后异世伴侣成了网游BOSS - 杏',1),(31,'31_sy','病美人玩转下克上系统[快穿]','病美人玩转下克上系统[快穿] - 卿云艾艾','',0,1,9856,1,1,1,1,'2024-12-01 23:13:38','穿越重生,武侠,玄幻','2022-07-09 20:20:19',1,0,'病美人玩转下克上系统[快穿] - 卿云艾',1),(32,'32_sy','把反派大佬变成了小甜甜','把反派大佬变成了小甜甜 - 小阿悬','',0,1,57470,0,1,1,1,'2024-11-30 15:03:07','穿越重生,武侠,玄幻','2022-07-09 20:20:17',1,0,'把反派大佬变成了小甜甜 - 小阿悬',1),(33,'33_sy','重生后前世夫君成了王爷','重生后前世夫君成了王爷 - 雨雪巍凉','',0,1,28212,0,1,1,1,'2024-11-30 15:16:42','穿越重生,武侠,玄幻','2022-07-08 21:00:01',1,0,'重生后前世夫君成了王爷 - 雨雪巍凉',1),(34,'34_sy','　拯救悲情反派进行时！','　拯救悲情反派进行时！ - 碉堡堡','',0,1,81717,0,1,1,1,'2024-11-30 15:37:07','穿越重生,武侠,玄幻','2022-07-08 20:59:29',1,0,'　拯救悲情反派进行时！ - 碉堡堡',1),(35,'35_sy','与新帝一起重生后','与新帝一起重生后 - 四喜秋秋','',0,1,14788,0,1,1,1,'2024-11-30 17:34:53','穿越重生,武侠,玄幻','2022-07-08 20:59:27',1,0,'与新帝一起重生后 - 四喜秋秋',1),(36,'36_sy','我走后，全帝国追悔莫及[星际]','我走后，全帝国追悔莫及[星际] - 青别','',0,1,96841,0,1,1,1,'2024-11-30 17:56:41','穿越重生,武侠,玄幻','2022-07-08 20:58:33',1,0,'我走后，全帝国追悔莫及[星际] - 青别',1),(37,'37_sy','我靠画画走上人生巅峰','我靠画画走上人生巅峰 - 漫呢','',0,1,49180,0,1,1,1,'2024-11-30 17:59:33','穿越重生,武侠,玄幻','2022-07-08 20:58:29',1,0,'我靠画画走上人生巅峰 - 漫呢',1),(38,'38_sy','傻徒儿今天又走火入魔了','傻徒儿今天又走火入魔了 - 明月不染霜','',0,1,98548,2,1,1,1,'2024-12-01 13:55:12','穿越重生,武侠,玄幻','2022-07-08 20:57:06',1,0,'傻徒儿今天又走火入魔了 - 明月不染霜',1),(39,'39_sy','漂亮omega少爷是白切黑','漂亮omega少爷是白切黑 - 日千引','',0,1,33434,1,1,1,1,'2024-12-01 23:17:41','穿越重生,武侠,玄幻','2022-07-08 20:56:51',1,0,'漂亮omega少爷是白切黑 - 日千引',1),(40,'40_sy','美食up穿成暴娇夫郎之后','美食up穿成暴娇夫郎之后 - 茶茶茶茶大','',0,1,45070,0,1,1,1,'2024-11-30 18:04:40','穿越重生,武侠,玄幻','2022-07-08 20:55:57',1,0,'美食up穿成暴娇夫郎之后 - 茶茶茶茶大',1),(41,'41_sy','反派他美貌值爆表[快穿]','反派他美貌值爆表[快穿] - 尽酒','',0,1,34852,6,1,1,1,'2024-12-01 06:22:20','穿越重生,武侠,玄幻','2022-07-08 20:54:24',1,0,'反派他美貌值爆表[快穿] - 尽酒',1),(42,'42_sy','快穿之主角持续崩坏中','快穿之主角持续崩坏中 - 在下三难','',0,1,34855,0,1,1,1,'2024-11-30 18:19:26','穿越重生,武侠,玄幻','2022-07-08 20:54:21',1,0,'快穿之主角持续崩坏中 - 在下三难',1),(43,'43_sy','[快穿]万人迷与邪神恋爱日常','[快穿]万人迷与邪神恋爱日常 - 林荫少女','',0,1,95954,0,1,1,1,'2024-11-30 18:26:38','穿越重生,武侠,玄幻','2022-07-08 20:49:14',1,0,'[快穿]万人迷与邪神恋爱日常 - 林荫少',1),(44,'44_sy','错嫁很甜：听闻王爷第一醋','错嫁很甜：听闻王爷第一醋 - 夜听阑','',0,1,9663,0,1,1,1,'2024-11-30 18:32:26','穿越重生,武侠,玄幻','2022-07-08 20:49:12',1,0,'错嫁很甜：听闻王爷第一醋 - 夜听阑',1),(45,'45_sy','主神老攻他又宠又怂［快穿］','主神老攻他又宠又怂［快穿］ - 酿甜','',0,1,25139,11,1,1,1,'2024-12-01 06:13:04','穿越重生,武侠,玄幻','2022-07-07 20:23:20',1,0,'主神老攻他又宠又怂［快穿］ - 酿甜',1),(46,'46_sy','重生之就是要虐渣','重生之就是要虐渣 - 宫墨翎','',0,1,91036,0,1,1,1,'2024-11-30 18:33:48','穿越重生,武侠,玄幻','2022-07-07 20:22:42',1,0,'重生之就是要虐渣 - 宫墨翎',1),(47,'47_sy','重生之倒追学长','重生之倒追学长 - 江暗','',0,1,57255,0,1,1,1,'2024-11-30 18:38:36','穿越重生,武侠,玄幻','2022-07-07 20:22:24',1,0,'重生之倒追学长 - 江暗',1),(48,'48_sy','美强惨男配满级重生了','美强惨男配满级重生了 - 且拂','',0,1,55540,0,1,1,1,'2024-11-30 18:40:59','穿越重生,武侠,玄幻','2022-07-07 20:21:17',1,0,'美强惨男配满级重生了 - 且拂',1),(49,'49_sy','满级大佬穿成假少爷后爆红了','满级大佬穿成假少爷后爆红了 - 简行之','',0,1,73955,0,1,1,1,'2024-11-30 18:49:21','穿越重生,武侠,玄幻','2022-07-07 20:20:37',1,0,'满级大佬穿成假少爷后爆红了 - 简行之',1),(50,'50_sy','和反派大佬协议结婚后','和反派大佬协议结婚后 - 暴走女巫','',0,1,79106,0,1,1,1,'2024-11-30 18:53:30','穿越重生,武侠,玄幻','2022-07-07 20:19:55',1,0,'和反派大佬协议结婚后 - 暴走女巫',1),(51,'51_sy','穿成大反派的雪狼后','穿成大反派的雪狼后 - 西丛鸦','',0,1,25867,0,1,1,1,'2024-11-30 18:57:07','穿越重生,武侠,玄幻','2022-07-07 20:19:18',1,0,'穿成大反派的雪狼后 - 西丛鸦',1),(52,'52_sy','穿成残疾大佬的炮灰原配','穿成残疾大佬的炮灰原配 - 双倍薯条','',0,1,40882,0,1,1,1,'2024-11-30 19:00:10','穿越重生,武侠,玄幻','2022-07-07 20:18:26',1,0,'穿成残疾大佬的炮灰原配 - 双倍薯条',1),(53,'53_sy','被偏执反派盯上后[快穿]','被偏执反派盯上后[快穿] - 九月霏烟','',0,1,42309,0,1,1,1,'2024-11-30 19:00:17','穿越重生,武侠,玄幻','2022-07-07 20:18:25',1,0,'被偏执反派盯上后[快穿] - 九月霏烟',1),(54,'54_sy','我养了一只最强哨兵','我养了一只最强哨兵 - 夢柔MiRou','',0,1,24562,0,1,1,1,'2024-11-30 19:02:28','穿越重生,武侠,玄幻','2022-07-06 15:15:28',1,0,'我养了一只最强哨兵 - 夢柔MiRou',1);



--
-- Table structure for table `ks_story_favorites`
--

DROP TABLE IF EXISTS `ks_story_favorites`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ks_story_favorites` (
`id` int unsigned NOT NULL AUTO_INCREMENT,
`uid` int NOT NULL DEFAULT '0' COMMENT '用户id',
`zy_id` int NOT NULL DEFAULT '0' COMMENT '小说id',
`created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP COMMENT '创建更新时间',
PRIMARY KEY (`id`),
KEY `user_id` (`uid`),
KEY `comics_id` (`zy_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci COMMENT='小说收藏表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ks_story_favorites`
--

LOCK TABLES `ks_story_favorites` WRITE;
/*!40000 ALTER TABLE `ks_story_favorites` DISABLE KEYS */;
/*!40000 ALTER TABLE `ks_story_favorites` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ks_story_pay`
--

DROP TABLE IF EXISTS `ks_story_pay`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ks_story_pay` (
`id` int NOT NULL AUTO_INCREMENT,
`uid` int NOT NULL COMMENT '用户id',
`coins` int NOT NULL DEFAULT '0' COMMENT '购买时的价格',
`zy_id` int NOT NULL COMMENT '资源小说编号id',
`type` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT '1' COMMENT '类型 购买 次数 赠送',
`created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP COMMENT '购买时间',
PRIMARY KEY (`id`) USING BTREE,
KEY `uid` (`uid`,`zy_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci ROW_FORMAT=COMPACT COMMENT='小说购买记录';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ks_story_pay`
--

LOCK TABLES `ks_story_pay` WRITE;
/*!40000 ALTER TABLE `ks_story_pay` DISABLE KEYS */;
/*!40000 ALTER TABLE `ks_story_pay` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ks_story_series`
--

DROP TABLE IF EXISTS `ks_story_series`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ks_story_series` (
`id` int NOT NULL AUTO_INCREMENT,
`story_id` int NOT NULL DEFAULT '0',
`series` int NOT NULL DEFAULT '1' COMMENT '章节',
`title` varchar(255) NOT NULL DEFAULT '' COMMENT '名称',
`is_free` tinyint NOT NULL DEFAULT '0' COMMENT '是否限免 0 免费 1 vip 2钻石',
`views_count` int NOT NULL DEFAULT '0' COMMENT '总历史点击数',
`status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1上架0下架',
`created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '创建时间',
`updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '更新时间',
`url` varchar(255) NOT NULL COMMENT '小说cdn路径',
PRIMARY KEY (`id`) USING BTREE,
KEY `idx_story_id` (`story_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 ROW_FORMAT=COMPACT COMMENT='小说章节表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ks_story_series`
--
--
-- Table structure for table `ks_story_tab`
--

DROP TABLE IF EXISTS `ks_story_tab`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ks_story_tab` (
`tab_id` int NOT NULL AUTO_INCREMENT,
`tab_name` varchar(20) NOT NULL COMMENT '导航蓝标签组',
`tags_str` varchar(1000) DEFAULT NULL COMMENT '标签',
`sort_num` smallint unsigned NOT NULL COMMENT '排序',
`status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 启用',
`is_tab` tinyint(1) NOT NULL COMMENT '主页展示',
`is_category` tinyint(1) NOT NULL COMMENT '分类过滤展示',
`show_style` enum('H-1*N','V-3*N','V-2*N','') DEFAULT NULL,
`show_number` tinyint DEFAULT '6',
PRIMARY KEY (`tab_id`),
KEY `status` (`status`),
KEY `is_tab` (`is_tab`),
KEY `is_category` (`is_category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 ROW_FORMAT=DYNAMIC COMMENT='漫画tab栏';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ks_story_tab`
--

LOCK TABLES `ks_story_tab` WRITE;
/*!40000 ALTER TABLE `ks_story_tab` DISABLE KEYS */;
INSERT INTO `ks_story_tab` VALUES (1,'SM','家庭,乱伦,激情',6,1,0,0,'V-2*N',6),(2,'古装武侠','古代架空,武侠,玄幻',4,1,1,1,'V-3*N',6),(12,'都市故事','现代都市,都市',11,1,1,1,'V-3*N',6),(13,'家庭故事','BL同人,家庭',1,1,1,1,'V-2*N',6),(14,'情感百味','家庭,情感',39,1,0,1,'V-2*N',6),(21,'校园风景','校园',8,1,1,1,'H-1*N',6),(22,'职场故事','娱乐圈,职场',10,1,0,1,'H-1*N',6),(23,'玄幻世界','玄幻网游,玄幻',20,1,1,1,'H-1*N',6);
/*!40000 ALTER TABLE `ks_story_tab` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ks_story_tags`
--

DROP TABLE IF EXISTS `ks_story_tags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ks_story_tags` (
`id` int unsigned NOT NULL AUTO_INCREMENT,
`name` varchar(50) NOT NULL DEFAULT '' COMMENT '标签',
`sort_num` int DEFAULT NULL COMMENT '排序',
`created_at` int NOT NULL DEFAULT '0' COMMENT '创建时间',
`updated_at` int DEFAULT NULL,
`img_url` varchar(255) NOT NULL DEFAULT '' COMMENT '标签封面图',
`home` tinyint NOT NULL COMMENT '首页显示',
`status` tinyint NOT NULL DEFAULT '1' COMMENT '列表显示状态',
`user_up` tinyint NOT NULL DEFAULT '1' COMMENT '允许用户上传',
`horizontal_img` varchar(255) NOT NULL COMMENT '横向图片',
`description` varchar(255) NOT NULL COMMENT '描述',
PRIMARY KEY (`id`) USING BTREE,
UNIQUE KEY `vid` (`name`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 ROW_FORMAT=DYNAMIC COMMENT='标签';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ks_story_tags`
--

LOCK TABLES `ks_story_tags` WRITE;
/*!40000 ALTER TABLE `ks_story_tags` DISABLE KEYS */;
INSERT INTO `ks_story_tags` VALUES (99,'玄幻',0,0,NULL,'',0,1,1,'',''),(100,'武侠',0,0,NULL,'',0,1,1,'',''),(101,'都市',0,0,NULL,'',0,1,1,'',''),(102,'情感',0,0,NULL,'',0,1,1,'',''),(103,'校园',0,0,NULL,'',0,1,1,'',''),(104,'现代都市',0,0,NULL,'',0,1,1,'',''),(105,'家庭',0,0,NULL,'',0,1,1,'',''),(106,'大学生',0,0,NULL,'',0,1,1,'',''),(107,'健身',0,0,NULL,'',0,1,1,'',''),(108,'肌肉男',0,0,NULL,'',0,1,1,'',''),(109,'壮受',0,0,NULL,'',0,1,1,'',''),(110,'乡村',0,0,NULL,'',0,1,1,'',''),(111,'职场',0,0,NULL,'',0,1,1,'',''),(112,'娱乐圈',0,0,NULL,'',0,1,1,'',''),(113,'玄幻网游',0,0,NULL,'',0,1,1,'',''),(114,'搞笑',0,0,NULL,'',0,1,1,'',''),(115,'激情',0,0,NULL,'',0,1,1,'',''),(116,'穿越重生',0,0,NULL,'',0,1,1,'',''),(117,'BL同人',0,0,NULL,'',0,1,1,'',''),(118,'古代架空',0,0,NULL,'',0,1,1,'','');
/*!40000 ALTER TABLE `ks_story_tags` ENABLE KEYS */;
UNLOCK TABLES;
--
-- Table structure for table `ks_find`
--

DROP TABLE IF EXISTS `ks_find`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ks_find` (
`id` int unsigned NOT NULL AUTO_INCREMENT,
`uuid` char(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
`title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
`img` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT ' 3  json ',
`coins` int NOT NULL,
`total_coins` int NOT NULL,
`created_at` int NOT NULL,
`status` tinyint NOT NULL COMMENT '0  1 ',
`like` int NOT NULL,
`reply` int NOT NULL,
`is_match` tinyint(1) NOT NULL DEFAULT '0',
`is_back` tinyint(1) NOT NULL DEFAULT '0',
`vid` int unsigned NOT NULL DEFAULT '0',
`is_top` tinyint(1) NOT NULL DEFAULT '0',
`is_finish` tinyint NOT NULL DEFAULT '0',
PRIMARY KEY (`id`),
KEY `uuid` (`uuid`),
KEY `created_at` (`created_at`),
KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
--
-- Table structure for table `ks_find_append`
--

DROP TABLE IF EXISTS `ks_find_append`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ks_find_append` (
`id` int unsigned NOT NULL AUTO_INCREMENT,
`find_id` int unsigned NOT NULL,
`find_uuid` char(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
`from_uuid` char(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
`coins` int NOT NULL,
`created_at` int NOT NULL,
PRIMARY KEY (`id`),
KEY `find_id` (`find_id`),
KEY `find_uuid` (`find_uuid`),
KEY `from_uuid` (`from_uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
LOCK TABLES `ks_find_append` WRITE;
/*!40000 ALTER TABLE `ks_find_append` DISABLE KEYS */;
INSERT INTO `ks_find_append` VALUES (1,5,'55724ce0e155682b10c7e40417d90684','55724ce0e155682b10c7e40417d90684',5,1697037895),(2,11,'c9e651bca8d63f3334b85163c946a83c','eb8db877920bc8a6039d8753c861bdcf',2,1697201420),(3,11,'c9e651bca8d63f3334b85163c946a83c','eb8db877920bc8a6039d8753c861bdcf',2,1697201423),(4,22,'eb8db877920bc8a6039d8753c861bdcf','fc5e05034de869a451a53f0aec689279',2,1697304236),(5,50,'6897a8787696ffdb7ef87e288d0c2b31','6897a8787696ffdb7ef87e288d0c2b31',5,1697560349),(6,50,'6897a8787696ffdb7ef87e288d0c2b31','6897a8787696ffdb7ef87e288d0c2b31',5,1697724266),(7,69,'55724ce0e155682b10c7e40417d90684','55724ce0e155682b10c7e40417d90684',5,1697887975),(8,175,'9b1f7a946e5c073b0d15a2ea76af8654','9b1f7a946e5c073b0d15a2ea76af8654',40,1698481770),(9,151,'0f2bd358448263d0c04dec0f49be96aa','0f2bd358448263d0c04dec0f49be96aa',2,1698588247),(10,175,'9b1f7a946e5c073b0d15a2ea76af8654','157b7aae453f474e1fe361f478bb6c77',2,1698594738),(11,175,'9b1f7a946e5c073b0d15a2ea76af8654','37f460ca44d02f8f2abaaeb1a463a10d',3,1698714683),(12,273,'a0a2593a050388562e5f374efec7a292','4f043f8c8ff653aad91380c782f0bf90',20,1699556804),(13,282,'ad7859d2ab5a311c2a18d9a0bd3975c5','ad7859d2ab5a311c2a18d9a0bd3975c5',5,1699716376),(14,282,'ad7859d2ab5a311c2a18d9a0bd3975c5','ad7859d2ab5a311c2a18d9a0bd3975c5',10,1699716518),(15,319,'2ccb66a076f941397950ffe477e24bee','2ccb66a076f941397950ffe477e24bee',5,1700139206),(16,332,'eb8db877920bc8a6039d8753c861bdcf','eb8db877920bc8a6039d8753c861bdcf',10,1700308608),(17,371,'7bff5cdeafc641d02d447429549bfb8d','7bff5cdeafc641d02d447429549bfb8d',10,1701059434),(18,371,'7bff5cdeafc641d02d447429549bfb8d','7bff5cdeafc641d02d447429549bfb8d',20,1701059460),(19,371,'7bff5cdeafc641d02d447429549bfb8d','7bff5cdeafc641d02d447429549bfb8d',5,1701059607),(20,403,'d3595e4d1c11541a2b98967cffb916f5','d3595e4d1c11541a2b98967cffb916f5',20,1701187896),(21,430,'724386bdfbc732411d00a2523e852864','724386bdfbc732411d00a2523e852864',8,1701571502),(22,475,'9b1bd106b1f40e5c7aa2d490bbd5c06e','9b1bd106b1f40e5c7aa2d490bbd5c06e',5,1702188801),(23,475,'9b1bd106b1f40e5c7aa2d490bbd5c06e','9b1bd106b1f40e5c7aa2d490bbd5c06e',5,1702188831),(24,483,'6dc34a308979d9751c5a0286de60a32e','6dc34a308979d9751c5a0286de60a32e',30,1702270351),(25,492,'29306aebe720ab967a9055a80fdf262a','29306aebe720ab967a9055a80fdf262a',40,1702482411),(26,511,'172a61e619f4ae8fd9dbc1f1116ae8ff','0f55cce117ddb30629e09f37ca49c272',5,1702827574),(27,531,'1e6cb09c97181f38d11b4fec3d69814c','7a90cce9e38b162b401ef48496c40449',2,1703086852),(28,548,'21fb13e941427eff228ee81d2560cff6','21fb13e941427eff228ee81d2560cff6',5,1703388474),(29,560,'21fb13e941427eff228ee81d2560cff6','21fb13e941427eff228ee81d2560cff6',3,1703508405),(30,560,'21fb13e941427eff228ee81d2560cff6','3c111b35cf5f64fe8b83e5043e4d0edb',2,1703531242),(31,566,'48a7d909354d0150ec5a0f466ee67332','48a7d909354d0150ec5a0f466ee67332',30,1703747345),(32,592,'45c5b224a2340f01164670e06af803a7','45c5b224a2340f01164670e06af803a7',15,1703926288),(33,609,'96f511c07fd194948ca82675277c5c6c','96f511c07fd194948ca82675277c5c6c',15,1704071398),(34,619,'1404596344f558ada98662a31bafc912','3c9b6651d19108b1ef8b0ac2852869ca',10,1704456162),(35,642,'48b4d6c40155facb44714846d1775a3b','48b4d6c40155facb44714846d1775a3b',5,1704586856),(36,642,'48b4d6c40155facb44714846d1775a3b','48b4d6c40155facb44714846d1775a3b',10,1704733203),(37,688,'bfb648210e667378bff619339d70ac09','bfb648210e667378bff619339d70ac09',30,1705108924),(38,687,'ecf78cc3c20f13455be3f8f6f2d4f8c8','ecf78cc3c20f13455be3f8f6f2d4f8c8',4,1705222956),(39,688,'bfb648210e667378bff619339d70ac09','252f33fe24b5d6fece2d2071afb609b0',2,1705308463),(40,786,'913ad6da688110a13c5c6cb2bda22b36','913ad6da688110a13c5c6cb2bda22b36',2,1705860651),(41,787,'1e92a9def2c6af0fd03518e0f4a50fde','1e92a9def2c6af0fd03518e0f4a50fde',15,1705930143),(42,794,'d18de5463ae3dbc3b9340cd09fd39116','d18de5463ae3dbc3b9340cd09fd39116',20,1705985838),(43,821,'19f59a77155e40176ad258919bb0f788','19f59a77155e40176ad258919bb0f788',20,1706399405),(44,896,'bf0c311a212a19dcf2bf406f71c5f96a','bf0c311a212a19dcf2bf406f71c5f96a',15,1707019097),(45,932,'f7d5dcfd68caf55f82f4c4c4aad71a85','f7d5dcfd68caf55f82f4c4c4aad71a85',50,1707382756),(46,932,'f7d5dcfd68caf55f82f4c4c4aad71a85','ce82fd0af94a3a6b15af13b4add82cb8',3,1707533297),(47,997,'9a964494d97e79d95f46e5acc1735ea8','9a964494d97e79d95f46e5acc1735ea8',5,1707719178),(48,1053,'2bf9d04f589dbefd18eb4403e92f6f6e','2bf9d04f589dbefd18eb4403e92f6f6e',50,1707971445),(49,1061,'33e2072df66da2ad277f5baa04a4ec3a','33e2072df66da2ad277f5baa04a4ec3a',60,1708053954),(50,1071,'fd4f0ea3866d82e534b563ee1cda78a6','fd4f0ea3866d82e534b563ee1cda78a6',15,1708110681),(51,1090,'b59f5cf5d6b7181ef2098b770a85bfa1','b59f5cf5d6b7181ef2098b770a85bfa1',29,1708139881),(52,1104,'09a0cf42bd7a582151d6e662ac18123a','09a0cf42bd7a582151d6e662ac18123a',10,1708183427),(53,1071,'fd4f0ea3866d82e534b563ee1cda78a6','024a314bc567de843f9b791cf60ced51',3,1708217516),(54,1071,'fd4f0ea3866d82e534b563ee1cda78a6','024a314bc567de843f9b791cf60ced51',3,1708217526),(55,1053,'2bf9d04f589dbefd18eb4403e92f6f6e','b284401194c8d4191b13330f4e3921c2',1,1708275105),(56,1053,'2bf9d04f589dbefd18eb4403e92f6f6e','b284401194c8d4191b13330f4e3921c2',1,1708275130),(57,886,'a85442b2395040860c1d5a5d4fe8008a','9e9cdc05d60e5e912763b6237ec13b90',2,1708559187),(58,1143,'c03a60d3d800aa8a8f3f120fb0a28793','c03a60d3d800aa8a8f3f120fb0a28793',15,1708576469),(59,1137,'b32aa746818cd0224ab8545a41e0b171','bb00551766eca486f580868832417eb0',4,1708588516),(60,1153,'ef806b755fee5b9764e8cb1554901034','7920d6c254eafcd3f18c14c6cfbb2f58',5,1708811677),(61,1211,'0f55cce117ddb30629e09f37ca49c272','0f55cce117ddb30629e09f37ca49c272',5,1709093587),(62,1224,'56ae3c6521a9d51aca50861d076b0aab','56ae3c6521a9d51aca50861d076b0aab',20,1709395102),(63,1281,'fb01f6d92e88369392134093df332021','53d9535af9aaad7e595a14ee8ba5fc06',1,1710039117),(64,1324,'319c6725e918b503e959911b83fb36c2','319c6725e918b503e959911b83fb36c2',10,1710509572),(65,1324,'319c6725e918b503e959911b83fb36c2','336b07d846e89fb9447f1325e8c03502',10,1710605655),(66,1344,'bee118a5ddb436c2969f4f72ddd47aec','bee118a5ddb436c2969f4f72ddd47aec',15,1710613893),(67,1372,'3e1e740604d511acb8cea9713a0fe430','3e1e740604d511acb8cea9713a0fe430',10,1710954893),(68,1372,'3e1e740604d511acb8cea9713a0fe430','3e1e740604d511acb8cea9713a0fe430',20,1711020743),(69,1390,'f4eabefe87a1acea139526769c200327','5bd9d338ba41ac9f02538831523d1b37',1,1711424568),(70,1415,'133f02b0c2d37a9b647790b5f961dd56','133f02b0c2d37a9b647790b5f961dd56',10,1711552495),(71,1415,'133f02b0c2d37a9b647790b5f961dd56','133f02b0c2d37a9b647790b5f961dd56',20,1711552536),(72,1418,'133f02b0c2d37a9b647790b5f961dd56','9c68650a3747569fa946c5b8c1abb2ad',10,1711687088),(73,1427,'bb3be3f67a77d431809e997a62e5526a','bb3be3f67a77d431809e997a62e5526a',10,1711701507),(74,1456,'e5b8376026a15e778ef77ac9324af259','e5b8376026a15e778ef77ac9324af259',10,1711946100),(75,1522,'ebd6cc3837d24f7824240bf35d48d51f','ebd6cc3837d24f7824240bf35d48d51f',10,1712810308),(76,1522,'ebd6cc3837d24f7824240bf35d48d51f','ebd6cc3837d24f7824240bf35d48d51f',40,1712810336),(77,1602,'1dac8f0993a36d449643d9557d008d5b','1dac8f0993a36d449643d9557d008d5b',5,1713440019),(78,1626,'d85dac8bf37cb7977fa90b7217a82d53','d85dac8bf37cb7977fa90b7217a82d53',5,1713602732),(79,1626,'d85dac8bf37cb7977fa90b7217a82d53','d85dac8bf37cb7977fa90b7217a82d53',1,1713690303),(80,1631,'feac5ded392c2a350104b35dee7b1d09','6a7c3d41a15781a390776f56413d0a27',1,1713966002),(81,1637,'fd327c7ad9463f31ab9b671185a0a56f','2cb0c967bf5b6f3c463dc5fa114cbcb1',1,1714004366),(82,1670,'450cc5ee9b67b37a89ebcf88408bc6bf','450cc5ee9b67b37a89ebcf88408bc6bf',10,1714063772),(83,1695,'4b8ef8be1b570f7122a37d64533b9bfd','4b8ef8be1b570f7122a37d64533b9bfd',10,1714297738),(84,1690,'ae3e57df8cdffb8e85c3b2925c1e82c9','ae3e57df8cdffb8e85c3b2925c1e82c9',20,1714387807),(85,1675,'b5eeaaff77bf853ea359550a2b9d51a4','b5eeaaff77bf853ea359550a2b9d51a4',10,1714430994),(86,1716,'51bfcf6612b38f339809fef41cffa1d4','51bfcf6612b38f339809fef41cffa1d4',10,1714524555),(87,1718,'1e9f7e87eab068f025691a786bd767eb','1e9f7e87eab068f025691a786bd767eb',20,1714587186),(88,1718,'1e9f7e87eab068f025691a786bd767eb','1e9f7e87eab068f025691a786bd767eb',20,1714587222),(89,1729,'2bdc06d5987ee168226a8e2fb09ebaf1','2bdc06d5987ee168226a8e2fb09ebaf1',10,1714649207),(90,1707,'6228a5a1c7b36db9aaf71052236c355e','6228a5a1c7b36db9aaf71052236c355e',20,1714664983),(91,1748,'57edec663b04d1d0e732f44be2dc5f4e','57edec663b04d1d0e732f44be2dc5f4e',10,1714709599),(92,1748,'57edec663b04d1d0e732f44be2dc5f4e','57edec663b04d1d0e732f44be2dc5f4e',10,1714709682),(93,1729,'2bdc06d5987ee168226a8e2fb09ebaf1','2bdc06d5987ee168226a8e2fb09ebaf1',5,1714835445),(94,1747,'130a61d22d65098130384c517bbb5cee','130a61d22d65098130384c517bbb5cee',5,1714839085),(95,1784,'41ad1e3605f1d6bf9533b39ba98f09af','41ad1e3605f1d6bf9533b39ba98f09af',40,1714845556),(96,1774,'d62c52ea2cd7ffa35d1a17e4ee033800','d62c52ea2cd7ffa35d1a17e4ee033800',20,1714897235),(97,1784,'41ad1e3605f1d6bf9533b39ba98f09af','8416a4ba00136f56e7c4e4b6d484ee81',2,1715012113),(98,1804,'b5735659a5a2659a67a319364ead12e9','b5735659a5a2659a67a319364ead12e9',2,1715103116),(99,1810,'772a60c70117dddeb1b4e149d1cdb544','772a60c70117dddeb1b4e149d1cdb544',40,1715260961),(100,1821,'5a8fa6c241a67587247222f16a0c317a','5a8fa6c241a67587247222f16a0c317a',25,1715277690),(101,1839,'a32f6c414adf99019f2fd5a455d4d280','a32f6c414adf99019f2fd5a455d4d280',20,1715694449),(102,1876,'b5d6dcb12331447c59b767f0b6c5b1af','b5d6dcb12331447c59b767f0b6c5b1af',2,1715932695),(103,1915,'ff561058961b0248cf9d9e62ad5e0a3f','ff561058961b0248cf9d9e62ad5e0a3f',15,1716388529),(104,1967,'f0caec778454031c5de3c99e7d226578','f0caec778454031c5de3c99e7d226578',5,1717170629),(105,1976,'3ac1a1925e73a9c14742a713eaef3aa3','3ac1a1925e73a9c14742a713eaef3aa3',5,1717244318),(106,1979,'9d13a9f4930a66a310e2675a524ba962','9d13a9f4930a66a310e2675a524ba962',8,1717409225),(107,2006,'9de1ca5de3ac45b42c389795e4c8ca2d','9de1ca5de3ac45b42c389795e4c8ca2d',10,1717500247),(108,2003,'971540eb368de869486fd50c1d8157bd','971540eb368de869486fd50c1d8157bd',10,1717513718),(109,2017,'4491e78d31fd8962254bf71f0428a628','272feafb02f1020c4b01242e03dd8816',10,1717546563),(110,2017,'4491e78d31fd8962254bf71f0428a628','4491e78d31fd8962254bf71f0428a628',15,1717563612),(111,2017,'4491e78d31fd8962254bf71f0428a628','639fdf5de47064aca4b6b46f7d960bd3',1,1717617898),(112,2017,'4491e78d31fd8962254bf71f0428a628','c4c7e61d611270048c1ecd3ecaf38786',3,1717696756),(113,2017,'4491e78d31fd8962254bf71f0428a628','3b772d0887d48335b730ff65d357eca6',1,1717770939),(114,2033,'f85a19082a06fd74d570f8894a3d02d3','92d6cee996bcccf9d32ca77e5afffbff',1,1717772007),(115,2024,'1e29a1aff6c6f63af843bbd53fb7ad14','1e29a1aff6c6f63af843bbd53fb7ad14',5,1717843664),(116,2033,'f85a19082a06fd74d570f8894a3d02d3','25cbd6046742cf7bd378e686849f9009',1,1717851286),(117,2017,'4491e78d31fd8962254bf71f0428a628','25cbd6046742cf7bd378e686849f9009',3,1717851679),(118,2064,'58f524f928820e632884cc28f2e19f14','58f524f928820e632884cc28f2e19f14',15,1717959099),(119,2056,'8b3ca239b8e5dc597cf86346f35ba4fd','8b3ca239b8e5dc597cf86346f35ba4fd',5,1717984708),(120,2072,'89d02ded04978e659479b405efe351a7','89d02ded04978e659479b405efe351a7',15,1718002054),(121,2086,'d85dac8bf37cb7977fa90b7217a82d53','d85dac8bf37cb7977fa90b7217a82d53',5,1718106070),(122,2077,'3954ff8f5346a75362d277b19c3e655c','3954ff8f5346a75362d277b19c3e655c',10,1718203272),(123,2044,'02a8c90081b83fabc8a23b04c37cc43e','02a8c90081b83fabc8a23b04c37cc43e',20,1718243780),(124,2113,'54e44dbaa6c7301740cccf6f1a6b6196','54e44dbaa6c7301740cccf6f1a6b6196',10,1718457732),(125,2132,'a30e8257148034b40c2899853b684872','a30e8257148034b40c2899853b684872',10,1718641948),(126,2161,'160fe0fe71f4007afc1d528df32928f9','160fe0fe71f4007afc1d528df32928f9',1,1718925150),(127,2180,'31b5ab2c45ebceb57917a66460f46735','31b5ab2c45ebceb57917a66460f46735',10,1719137132),(128,2205,'3d571bf911a51975b90b581e83e426e7','3d571bf911a51975b90b581e83e426e7',5,1719473361),(129,2215,'19e452999ad8b33bb6ecab97cb9cda7c','19e452999ad8b33bb6ecab97cb9cda7c',10,1719555811),(130,2253,'83c2c1500a7ef78feb20b06ddedb2fd3','83c2c1500a7ef78feb20b06ddedb2fd3',2,1719903217),(131,2282,'c28faf925fc3f18bd2a4df1ea1212011','c28faf925fc3f18bd2a4df1ea1212011',10,1719943882),(132,2292,'1e1a82055f59dffcacfe62676ef7a0f5','1e1a82055f59dffcacfe62676ef7a0f5',10,1720286282),(133,2292,'1e1a82055f59dffcacfe62676ef7a0f5','1e1a82055f59dffcacfe62676ef7a0f5',10,1720286292),(134,2292,'1e1a82055f59dffcacfe62676ef7a0f5','1e1a82055f59dffcacfe62676ef7a0f5',20,1720286306),(135,2292,'1e1a82055f59dffcacfe62676ef7a0f5','1e1a82055f59dffcacfe62676ef7a0f5',20,1720286320),(136,2349,'ebd6d70f379479d8490d7b1897da2f1a','e886c607553555915a46745f262ea8f3',5,1720610105),(137,2378,'acf10b6a32bd9a2cb9cd2acc8d22b3f3','acf10b6a32bd9a2cb9cd2acc8d22b3f3',1,1720805474),(138,2389,'65799388d9bcbd34599a6acd39c44a96','65799388d9bcbd34599a6acd39c44a96',20,1720851305),(139,2389,'65799388d9bcbd34599a6acd39c44a96','65799388d9bcbd34599a6acd39c44a96',10,1720880733),(140,2402,'873beb61fa1fda3f146126bd1e06fbe5','873beb61fa1fda3f146126bd1e06fbe5',5,1720909329),(141,2452,'286ff8289a5d78bab1050208723b1681','286ff8289a5d78bab1050208723b1681',30,1721184821),(142,2433,'1653453c894f000c831a1e00de204b09','336b07d846e89fb9447f1325e8c03502',20,1721191959),(143,2477,'d9048b8c8b7f0961d9083abb3408877b','d9048b8c8b7f0961d9083abb3408877b',20,1721327686),(144,2477,'d9048b8c8b7f0961d9083abb3408877b','d9048b8c8b7f0961d9083abb3408877b',20,1721327704),(145,2516,'098d8474227090731606a3c25d9cc96b','2efff2380b02b08c944e7bc76eaa0e0e',10,1721470733),(146,2559,'506afe1567befb6071b5bba6cd5189d7','506afe1567befb6071b5bba6cd5189d7',10,1721586266),(147,2570,'ebe5a59e537baaa7c6bdfd80efb5d471','ebe5a59e537baaa7c6bdfd80efb5d471',10,1721671930),(148,2492,'e16c9d25f768679515df6299ca711fa6','7a3e0cdfeb130bd02abbd09905e52fa7',2,1721677839),(149,2586,'01d37e06c2cab5aebd21297b35b6492a','01d37e06c2cab5aebd21297b35b6492a',5,1721838056),(150,2624,'a375005fee42c2fe01947020aa2cae8d','a375005fee42c2fe01947020aa2cae8d',25,1721973399),(151,2631,'34523239d8b65d2b61242aea354ab19f','34523239d8b65d2b61242aea354ab19f',5,1721980322),(152,2650,'21fb13e941427eff228ee81d2560cff6','21fb13e941427eff228ee81d2560cff6',5,1722263745),(153,2650,'21fb13e941427eff228ee81d2560cff6','21fb13e941427eff228ee81d2560cff6',10,1722263754),(154,2674,'f88c0d03f1ed8db737649db81ca75290','f88c0d03f1ed8db737649db81ca75290',40,1722279429),(155,2674,'f88c0d03f1ed8db737649db81ca75290','f88c0d03f1ed8db737649db81ca75290',40,1722279475),(156,2661,'c78f90ff13f60a798702c9ba8942c1ea','c78f90ff13f60a798702c9ba8942c1ea',1,1722342231),(157,2673,'db210017bb7a52237b5236d2661fe2dd','db210017bb7a52237b5236d2661fe2dd',20,1722391099),(158,2707,'620789541f5d87b29f392142b4d4f507','620789541f5d87b29f392142b4d4f507',5,1722437360),(159,2740,'19a06107b2f175a8decba0c46fa3915c','19a06107b2f175a8decba0c46fa3915c',5,1722632407),(160,2759,'71f60548b4503945e41bce870c880768','71f60548b4503945e41bce870c880768',5,1722740356),(161,2738,'719ab911cd3361e72dc290dc2561ba62','8080b5532e293676fd496efc44b579f2',10,1722789044),(162,2784,'87f20a142d8a49afa64042d0808d049d','87f20a142d8a49afa64042d0808d049d',2,1722930709),(163,2753,'90e289d505818ef0d9a960bd7765fdd4','6ea2bb68a76349193f12b0ec6a5149fc',15,1722956619),(164,2753,'90e289d505818ef0d9a960bd7765fdd4','00819c8f29e8d71cec2228e061addab1',1,1722972314),(165,2838,'32c8c3f46a1ee7411ba3cd7b2a8a4573','32c8c3f46a1ee7411ba3cd7b2a8a4573',20,1723261352),(166,2864,'f405da06d26b40f1ce940afd481dc77f','f405da06d26b40f1ce940afd481dc77f',10,1723429776),(167,2904,'1b9ec9c1b5a18e599f69e3548baf87f3','1b9ec9c1b5a18e599f69e3548baf87f3',8,1723714684),(168,2904,'1b9ec9c1b5a18e599f69e3548baf87f3','1b9ec9c1b5a18e599f69e3548baf87f3',8,1723742597),(169,2904,'1b9ec9c1b5a18e599f69e3548baf87f3','1b9ec9c1b5a18e599f69e3548baf87f3',40,1723743417),(170,2904,'1b9ec9c1b5a18e599f69e3548baf87f3','57edec663b04d1d0e732f44be2dc5f4e',1,1723793719),(171,2895,'c9e651bca8d63f3334b85163c946a83c','c9e651bca8d63f3334b85163c946a83c',1,1723824984),(172,2885,'9d85f1089cb2bf829c0bd72fe96c0f33','9d85f1089cb2bf829c0bd72fe96c0f33',5,1723916752),(173,2904,'1b9ec9c1b5a18e599f69e3548baf87f3','1b9ec9c1b5a18e599f69e3548baf87f3',3,1723996134),(174,2909,'a92d2e5d5ac02be314387a1b137f42f2','a92d2e5d5ac02be314387a1b137f42f2',20,1723998171),(175,2909,'a92d2e5d5ac02be314387a1b137f42f2','a92d2e5d5ac02be314387a1b137f42f2',20,1723998205),(176,2945,'9206152350c14ef2d1f259d616967567','9206152350c14ef2d1f259d616967567',3,1724005347),(177,2914,'34f311c929890ebd597fb61b4d790be3','34f311c929890ebd597fb61b4d790be3',10,1724007434),(178,2944,'d3a7c9a074235d496353aa24d5ca9ec2','5a50e6887d04bc26e7f7841fc657306d',10,1724138283),(179,2967,'10e6baaaa3c12e1f427349b9ce07286d','10e6baaaa3c12e1f427349b9ce07286d',10,1724155212),(180,2967,'10e6baaaa3c12e1f427349b9ce07286d','10e6baaaa3c12e1f427349b9ce07286d',10,1724299829),(181,2970,'dc83dbefee7ca3a221056fb0c1956c15','336b07d846e89fb9447f1325e8c03502',20,1724356551),(182,3005,'e8bb518c3acb3a9225b9909d56994db1','e8bb518c3acb3a9225b9909d56994db1',5,1724420005),(183,3018,'03c09b97bcc28005964d91f8b598dc53','205a9476421a07c938c8bb44bb67aaf5',1,1724505863),(184,3004,'2d687540f87a92f344464fb222b89130','2d687540f87a92f344464fb222b89130',10,1724511134),(185,3018,'03c09b97bcc28005964d91f8b598dc53','8c51ff8d3bfc3c34fdf71ffd3f422f73',1,1724594328),(186,3018,'03c09b97bcc28005964d91f8b598dc53','8c51ff8d3bfc3c34fdf71ffd3f422f73',1,1724594370),(187,3018,'03c09b97bcc28005964d91f8b598dc53','614cc0b0debe59904b89d163ee92c124',2,1724599628),(188,3178,'759d80ca880d9501e9c5f13db1d479f2','759d80ca880d9501e9c5f13db1d479f2',10,1725453209),(189,3178,'759d80ca880d9501e9c5f13db1d479f2','759d80ca880d9501e9c5f13db1d479f2',10,1725453238),(190,3189,'d512919ed272d57f7cb3095de46c566b','d512919ed272d57f7cb3095de46c566b',5,1725599447),(191,3198,'16454278213b6ed12220081dee9f661f','16454278213b6ed12220081dee9f661f',4,1725682411),(192,3198,'16454278213b6ed12220081dee9f661f','16454278213b6ed12220081dee9f661f',10,1725682659),(193,3233,'030c30b3b742bc64b5f0af68cbde2770','030c30b3b742bc64b5f0af68cbde2770',5,1726076284),(194,3234,'af536c483fd33d641e754216c7bcc208','af536c483fd33d641e754216c7bcc208',10,1726143128),(195,3239,'3d4f77de6edbb30021e26e2ce5a17a5e','3d4f77de6edbb30021e26e2ce5a17a5e',10,1726158651),(196,3254,'6780ca7d4baf89b8068ae19cd61c06a3','6780ca7d4baf89b8068ae19cd61c06a3',5,1726546732),(197,3274,'e1fd5b06d5dfc7e9b34e3ad9c8eef13b','e1fd5b06d5dfc7e9b34e3ad9c8eef13b',10,1726586414),(198,3290,'078f590496d668a536c686be4e7041ee','078f590496d668a536c686be4e7041ee',10,1726755937),(199,3294,'70e8b3150ea984cbc183d5632b439225','70e8b3150ea984cbc183d5632b439225',15,1726789759),(200,3308,'9e3b8d7861e57f93373b1ba541889033','dc9f9cc1a75dbb5837b4d351a7bfc8c6',10,1726919530),(201,3306,'931a648472a3d8e976aab20cb4859d7e','931a648472a3d8e976aab20cb4859d7e',20,1726982736),(202,3314,'7ee56dc52de7c22235e635ae86b55874','7ee56dc52de7c22235e635ae86b55874',10,1727027047),(203,3312,'f91aa0d45907c5e87336fa39b62f2460','5a50e6887d04bc26e7f7841fc657306d',5,1727156138),(204,3347,'98f827b9dbb0d727936cd179f25fb5e6','98f827b9dbb0d727936cd179f25fb5e6',10,1727193194),(205,3369,'85fb457b18ab1e563e5bddc3fa83bedb','85fb457b18ab1e563e5bddc3fa83bedb',100,1727442297),(206,3364,'afe11de4d2104c43e2125bfd378b833f','afe11de4d2104c43e2125bfd378b833f',10,1727597230),(207,3407,'e85cd5b7e72534d8688a2c31eea3d96b','ebf3386295fc0f3f244c6b9124a01e18',5,1727938626),(208,3424,'992f4a2c2cbcf723d1414952afbf7001','992f4a2c2cbcf723d1414952afbf7001',5,1728040825),(209,3424,'992f4a2c2cbcf723d1414952afbf7001','992f4a2c2cbcf723d1414952afbf7001',10,1728040832),(210,3433,'a10325978ce42f5489e9d40fd68440a3','a10325978ce42f5489e9d40fd68440a3',30,1728225242),(211,3458,'6283113fd692685a4dc819a133031240','6283113fd692685a4dc819a133031240',10,1728628013),(212,3481,'ed07ecba2435f68d3c47797200148544','ed07ecba2435f68d3c47797200148544',5,1728739203),(213,3465,'2d687540f87a92f344464fb222b89130','f04e5bd73be42000b95a46b318762db2',1,1728804823),(214,3465,'2d687540f87a92f344464fb222b89130','99da513f541c1ed59e4793ca4d3d6fb8',4,1728835329),(215,3465,'2d687540f87a92f344464fb222b89130','99da513f541c1ed59e4793ca4d3d6fb8',4,1728835333),(216,3465,'2d687540f87a92f344464fb222b89130','99da513f541c1ed59e4793ca4d3d6fb8',4,1728835346),(217,3491,'3d06c9d6025d79ae7170c9823e785f1a','3d06c9d6025d79ae7170c9823e785f1a',5,1728896612),(218,3512,'be05e24f4ef711d9f058ba571493cb08','be05e24f4ef711d9f058ba571493cb08',10,1728984539),(219,3519,'deafef3a3989ff6f56ea2b78a896feb7','5f27db678064ba9fde4264471292793c',5,1729172126),(220,3537,'0f2bd358448263d0c04dec0f49be96aa','0f2bd358448263d0c04dec0f49be96aa',12,1729350052),(221,3526,'1877a3c68d4c34ffea63e3901198a9b2','1877a3c68d4c34ffea63e3901198a9b2',10,1729397848),(222,3563,'30289f5089761434864b09b484fdcaab','5f27db678064ba9fde4264471292793c',10,1729518410),(223,3563,'30289f5089761434864b09b484fdcaab','726fe9cebd1086b8f091c24c86e35422',1,1729527230),(224,3538,'1b6daa0d5f6174435e853238d90e8a2c','1b6daa0d5f6174435e853238d90e8a2c',3,1729531552),(225,3594,'6283113fd692685a4dc819a133031240','b5d6d758fb7aa3c7b051f79131beb86f',5,1730011760),(226,3598,'a4f54171e936c9a83e65ee8303be5548','5f27db678064ba9fde4264471292793c',10,1730040119),(227,3594,'6283113fd692685a4dc819a133031240','b92068723e954a16ac300e6943a207fc',5,1730060831),(228,3594,'6283113fd692685a4dc819a133031240','0dacdbbe1c4076f808e5862b79a89d9c',5,1730066149),(229,3617,'32c7ba439f6de9c0f9b3260d1d4f7ed1','32c7ba439f6de9c0f9b3260d1d4f7ed1',5,1730136766),(230,3617,'32c7ba439f6de9c0f9b3260d1d4f7ed1','32c7ba439f6de9c0f9b3260d1d4f7ed1',5,1730136774),(231,3613,'0ea57fb1ce8ed14fb739dfe7dc6beaf2','84a7233ab04e595243886a2cedd68025',1,1730140271),(232,3591,'e0ba146e3fb2f9d6dbcafe377ec63d54','e0ba146e3fb2f9d6dbcafe377ec63d54',10,1730155929),(233,3591,'e0ba146e3fb2f9d6dbcafe377ec63d54','e0ba146e3fb2f9d6dbcafe377ec63d54',10,1730155962),(234,3660,'457f52ec2db8b8cf7fd9036e4c243370','457f52ec2db8b8cf7fd9036e4c243370',5,1730402945),(235,3646,'a83aa4de8c36efd3366c6751a0983d06','a83aa4de8c36efd3366c6751a0983d06',5,1730530474),(236,3680,'6773fedd78fc542b54f4a9ec995a4c9e','6773fedd78fc542b54f4a9ec995a4c9e',1,1730711213),(237,3671,'f40aeaae3cc22e357643ea11dc37369c','458eb3962e7a189b683a74d5f2bebd72',20,1730730974),(238,3706,'575c473f2be91db5dd63eed648431955','575c473f2be91db5dd63eed648431955',15,1730997262),(239,3756,'c1749ddbbd3ad0f51493d0e7a7530728','c1749ddbbd3ad0f51493d0e7a7530728',15,1731215563),(240,3716,'d3ee9657dd31a77a6799d4e5848b3fdd','d3ee9657dd31a77a6799d4e5848b3fdd',60,1731232858),(241,3751,'abef14972c2db72c57491ba0019e27ac','abef14972c2db72c57491ba0019e27ac',5,1731261438),(242,3756,'c1749ddbbd3ad0f51493d0e7a7530728','c1749ddbbd3ad0f51493d0e7a7530728',15,1731300585),(243,3768,'d3c4f18945db05b610710174330ade3a','d3c4f18945db05b610710174330ade3a',10,1731403555),(244,3769,'d3c4f18945db05b610710174330ade3a','d3c4f18945db05b610710174330ade3a',10,1731403565),(245,3767,'cbe07eccbc673db4cedc54901e08f904','cbe07eccbc673db4cedc54901e08f904',30,1731549328),(246,3799,'3360b155f069a17aaaef06a386178ad9','3360b155f069a17aaaef06a386178ad9',10,1731665307),(247,3799,'3360b155f069a17aaaef06a386178ad9','3360b155f069a17aaaef06a386178ad9',9,1731716063),(248,3821,'b806f674a774d1bb74ec68fa7655e2cb','b806f674a774d1bb74ec68fa7655e2cb',20,1732013491),(249,3903,'a8c1a6ca9e3a2f50bd119f95ab93230e','947ad36f2553462d3214b859b64c15f2',2,1732531244),(250,3894,'79207a21d12c551cc6aa0925a578be05','79207a21d12c551cc6aa0925a578be05',18,1732621076),(251,3974,'9376e337242016318fd3a7163bf5c28e','9376e337242016318fd3a7163bf5c28e',5,1732930205);
/*!40000 ALTER TABLE `ks_find_append` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ks_find_look`
--

DROP TABLE IF EXISTS `ks_find_look`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ks_find_look` (
`id` int NOT NULL AUTO_INCREMENT,
`find_id` int NOT NULL,
`uuid` char(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
`create_at` int NOT NULL,
PRIMARY KEY (`id`),
UNIQUE KEY `find_id` (`find_id`,`uuid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
--
-- Table structure for table `ks_find_reply`
--

DROP TABLE IF EXISTS `ks_find_reply`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ks_find_reply` (
`id` int unsigned NOT NULL AUTO_INCREMENT,
`find_id` int NOT NULL,
`uuid` char(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
`status` tinyint NOT NULL DEFAULT '0',
`created_at` int NOT NULL,
`praize` int NOT NULL,
`comment` int NOT NULL,
`is_accept` tinyint NOT NULL,
`coins` int NOT NULL DEFAULT '0',
PRIMARY KEY (`id`),
KEY `tbr_find_reply_find_id_uuid_index` (`find_id`,`uuid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
--
-- Table structure for table `ks_find_reply_comment`
--

DROP TABLE IF EXISTS `ks_find_reply_comment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ks_find_reply_comment` (
`id` int NOT NULL AUTO_INCREMENT,
`find_id` int NOT NULL DEFAULT '0',
`reply_id` int NOT NULL,
`uuid` char(32) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL COMMENT 'UUID',
`to_uuid` char(32) NOT NULL DEFAULT '',
`comment` varchar(255) NOT NULL DEFAULT '',
`is_checked` tinyint NOT NULL DEFAULT '1',
`like_num` int NOT NULL DEFAULT '0',
`reply_num` int unsigned NOT NULL DEFAULT '0',
`created_at` int NOT NULL DEFAULT '0',
PRIMARY KEY (`id`),
KEY `mv_id` (`reply_id`),
KEY `uuid` (`uuid`),
KEY `c_id` (`find_id`),
KEY `is_checked` (`is_checked`),
KEY `comment` (`comment`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;
--
-- Table structure for table `ks_find_reply_likes`
--

DROP TABLE IF EXISTS `ks_find_reply_likes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ks_find_reply_likes` (
`id` int NOT NULL AUTO_INCREMENT,
`uuid` char(32) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL COMMENT 'ID',
`reply_id` int NOT NULL,
PRIMARY KEY (`id`),
UNIQUE KEY `uuid` (`uuid`,`reply_id`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;


DROP TABLE IF EXISTS `ks_find_reply_likes_comment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ks_find_reply_likes_comment` (
`id` int NOT NULL AUTO_INCREMENT,
`uuid` char(32) NOT NULL COMMENT 'ID',
`comment_id` int NOT NULL,
PRIMARY KEY (`id`),
UNIQUE KEY `uuid` (`uuid`,`comment_id`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

DROP TABLE IF EXISTS `ks_original`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ks_original` (
`id` int NOT NULL AUTO_INCREMENT,
`title` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '影片标题',
`desc` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT '' COMMENT '简介',
`actors` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '演员',
`category` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '分类',
`country` varchar(255) NOT NULL DEFAULT '' COMMENT '国家',
`directors` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '导演',
`is_series` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 电影 2电视剧',
`cover` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '封面',
`tags` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '影片标签',
`langs` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '语言',
`year_released` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '影片上映年',
`video_num` int NOT NULL DEFAULT '0' COMMENT '视频数量',
`like_count` int unsigned NOT NULL DEFAULT '0' COMMENT '点赞数',
`play_count` int unsigned NOT NULL DEFAULT '0' COMMENT '播放数',
`com_count` int unsigned NOT NULL DEFAULT '0' COMMENT '评论数',
`pay_count` int NOT NULL DEFAULT '0' COMMENT '售卖次数',
`status` tinyint NOT NULL DEFAULT '0' COMMENT '0下架1上架',
`refresh_at` datetime DEFAULT NULL COMMENT '刷新时间',
`created_at` datetime DEFAULT NULL COMMENT '创建时间',
`source_id` varchar(32) NOT NULL COMMENT '采集资源ID 采集识别',
`last_see` datetime DEFAULT NULL COMMENT '最后观看时间',
`hot_c_month` int DEFAULT '0' COMMENT '本月观看次数',
PRIMARY KEY (`id`) USING BTREE,
KEY `refresh_at` (`refresh_at`) USING BTREE,
KEY `title` (`title`(255)) USING BTREE,
KEY `status` (`status`) USING BTREE,
KEY `source_id` (`source_id`) USING BTREE,
FULLTEXT KEY `full_tags` (`tags`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='原创数据表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ks_original`
--

LOCK TABLES `ks_original` WRITE;
/*!40000 ALTER TABLE `ks_original` DISABLE KEYS */;
INSERT INTO `ks_original` VALUES (1,'菲利普看见了','7岁的菲利普非常仰慕着兄长赛巴斯汀，既是他的榜样、他的知己、也是他的一切。但就在某天，他无意间从门缝中撞见赛巴斯汀与好友的亲暱互动，这一幕让菲利普顿时不知所措……     ☆房间裡的哥哥，真的是我认识...','乔瑟夫瓦德佛格,奥古斯特席尔格霍姆','男同','瑞典','娜塔莉阿尔瓦雷斯梅森',0,'/upload/upload/20240611/2024061122361996008.jpeg','男同志,青春恋爱,性向启蒙,同志子女,欧洲,短片','','2015',1,2,79,3,4,1,NULL,'2024-06-24 16:43:15','5',NULL,0),(2,'爱上明星四分卫','就在最重要的比赛即将到来的前一晚，受到众人关注的四分卫布兰登面临到巨大的抉择。如果离开小镇去追求更优渥的未来，势必也得和他最亲密的战友詹姆斯分开……     ☆体坛巨星的奋力一击！留下来，还是跟你走？...','凯兰鲁德,卡尔森博特曼','男同','美国','安德斯海尔德',0,'/upload/upload/20240611/2024061122362312495.jpeg','男同志,青春恋爱,运动,北美,短片','','2015',1,4,159,0,3,1,NULL,'2024-06-24 16:43:15','6',NULL,0),(3,'游回你身边','就在最重要的比赛即将到来的前一晚，受到众人关注的四分卫布兰登面临到巨大的抉择。如果离开小镇去追求更优渥的未来，势必也得和他最亲密的战友詹姆斯分开……     ☆体坛巨星的奋力一击！留下来，还是跟你走？...','凯兰鲁德,卡尔森博特曼','男同','美国','安德斯海尔德',0,'/upload/upload/20240611/2024061122362716042.jpeg','男同志,青春恋爱,运动,北美,短片','','2015',1,1,138,0,3,1,NULL,'2024-06-24 16:43:15','7',NULL,0),(4,'恶虫幻影','经过与网友的一夜激情后，皮匠里欧的神智开始有些错乱，在联络不上对方的同时，他也深信自己的身体遭到了臭虫的侵袭……     ☆你就像这隻虫般，在我的体内如．影．随．行……   ☆《我的完美日常》名导文温...','阿迪尔梅基,布莱斯米其林','男同','法国','克莱门汀德克伦,雷米佐丹奴',0,'/upload/upload/20240611/2024061122363245241.jpeg','男同志,约炮,奇幻,欧洲,短片','','2022',1,3,105,0,3,1,NULL,'2024-06-24 16:43:15','8',NULL,0),(5,'午夜热舞','经过与网友的一夜激情后，皮匠里欧的神智开始有些错乱，在联络不上对方的同时，他也深信自己的身体遭到了臭虫的侵袭……     ☆你就像这隻虫般，在我的体内如．影．随．行……   ☆《我的完美日常》名导文温...','阿迪尔梅基,布莱斯米其林','男同','法国','克莱门汀德克伦,雷米佐丹奴',0,'/upload/upload/20240611/2024061122363676808.jpeg','男同志,约炮,奇幻,欧洲,短片','','2022',1,0,55,0,2,1,NULL,'2024-06-24 16:43:15','9',NULL,0),(6,'你好陌生人：电影版','哈维尔在校外营队再次碰到米可，早在半年前就发展出一段鲜为人知的过往的他们，似乎又回到原点。即使装作没事，但火热的情意却已默默出卖了他们。哈维尔和米可之间会有所转变吗？     ☆突破距离限制，只为和你...','托尼拉布鲁斯卡,JC阿尔坎塔拉','男同','菲律宾','杜温巴塔札',0,'/upload/upload/20240611/2024061122364159962.jpeg','男同志,青春恋爱,旅游,菲律宾,电影','','2021',1,0,133,0,3,1,NULL,'2024-06-24 16:43:15','10',NULL,0),(7,'不够完整的我们','这是一封男人写给从未谋面的「他」的情书。他的嗓音、指尖、肌肤和那股温暖都是男人所渴望的，但我们真的需要透过一个对象才能完整自己吗？还是缺少的是我们与自己的内在连结？     ☆我等的人会是谁，何时才会...','庞特斯利德拜,科里迈克史密斯','男同','美国','萨夏克布特',0,'/upload/upload/20240611/2024061122364635311.jpeg','男同志,爱情,音乐舞蹈,北美,短片','','2023',1,1,56,0,1,1,NULL,'2024-06-24 16:43:15','11',NULL,0),(8,'在我坟上起舞','那年夏天，我在海边拍戏，演一具漂浮的尸体，他以为我自杀，于是「救」起了我。在那之后，我对他一见钟情，我问他等我死后能不能来参加我的葬礼，他不再能忍受，决定离我而去……     ☆即使一切都是臆想，我也...','胡楷翔,黄亭开','男同','中国','胡楷翔',0,'/upload/upload/20240611/2024061122365038307.jpeg','男同志,青春恋爱,中国及香港,短片','','2022',1,0,154,0,1,1,NULL,'2024-06-24 16:43:15','12',NULL,0),(9,'我的隔离罗曼史','疫情爆发时，汤姆开始居家工作，而他失联已久的暗恋对象肯卓克也从夏威夷返国，两人因此在线上重逢。正当汤姆思索着该如何拿捏两人的距离时，肯卓克却意外染上新冠肺炎……     ☆暧昧总在封城时……克服疫情，...','詹姆森布雷克,乔奥康斯坦西亚','男同','菲律宾','小巴比伯尼法西欧',0,'/upload/upload/20240611/2024061122365551663.jpeg','男同志,爱情,菲律宾,电影','','2020',1,0,58,0,1,1,NULL,'2024-06-24 16:43:15','13',NULL,0),(10,'再爱爱一次','製药公司的执行长浅仓祐树始终被过去的回忆困扰着，就在某天散步时，他意外看见一位和他思念的男子长得一模一样的年轻人。为了重温旧梦，优纪决定服下返老还童的药物……    ☆《强暴直男追缉令》资深粉红电影导...','仲井间稜,中野隼斗','男同','日本','国泽实',0,'/upload/upload/20240611/2024061122370176679.jpeg','男同志,爱情,情欲,奇幻,日本,电影','','2023',1,1,111,0,0,1,NULL,'2024-06-24 16:43:15','14',NULL,0),(11,'你好陌生人','影集简介：   当米可和好友们一起参与线上机智问答活动时，校内的当红篮球明星哈维尔突然误入，而他的无礼与轻浮也让米可感到被冒犯。然而，两人的缘分其实才正要展开……     ☆学业、爱情双得意？高颜值冤...','托尼拉布鲁斯卡,JC阿尔坎塔拉','男同','菲律宾','彼得森瓦加斯',2,'/upload/upload/20240611/2024061122370590460.jpeg','男同志,青春恋爱,菲律宾,影集','','2020',8,0,90,0,0,1,NULL,'2024-06-24 16:43:15','15',NULL,0),(12,'出柜G念日','那达夫出柜一週年了，老妈洛尼特召集亲朋好友，并大费周章地举办了一场别具意义的盛大派对。然而，身为主角的那达夫却略显不自在，没想到更尴尬的还在后头！     ☆放下偏见，其实没这麽容易……   ☆直击同...','诺姆卡梅利,朵莉特蕾芙亚里,席芙拉米尔斯坦','男同','以色列','尼夫曼祖尔',0,'/upload/upload/20240611/2024061122371049109.jpeg','男同志,出柜,同志子女,喜剧,中东,短片','','2022',1,0,22,0,0,1,NULL,'2024-06-24 16:43:16','16',NULL,0),(13,'错爱一夜情','年轻的建筑师乌迪终于愿意成全男友尼姆罗德的提议：尝试三人性爱。然而，当他们与年轻气盛的欧尔体验了近乎完美的床事后，乌迪开始动摇，并深陷无法自拔的迷恋……     ☆一次三人行，有人动真情   ☆网友直...','汤姆乔多洛夫,阿萨夫佩瑞,欧尔亚瑟','男同','以色列','里奥尔索洛卡',0,'/upload/upload/20240611/2024061122371415250.jpeg','男同志,爱情,约炮,中东,短片','','2018',1,1,219,0,5,1,NULL,'2024-06-24 16:43:16','17',NULL,0),(14,'镜中迷惑','在曼彻斯特的一间同志酒吧，厕所裡的镜子见证了许多男人之间的「特殊」奇遇……     ☆狭窄的厕所裡，每天都有你想像不到的事情发生！   ☆《公园小情人》其一导演尼尔伊利执导处女作，选用英剧《皮囊》《无...','连恩鲍伊,乔迪莱瑟姆','男同','英国','尼尔伊利',0,'/upload/upload/20240611/2024061122371994371.jpeg','男同志,情欲,欧洲,短片','','2015',1,0,48,0,1,1,NULL,'2024-06-24 16:43:16','18',NULL,0),(15,'病潮来袭','1983年的巴西，当众人正在欢喜迎接新年时，从国外归来的生物学家苏札诺发觉自己身体的异状。即使绝望却也不愿放弃的他，开始和跨性别歌手萝丝及学生温贝托作伴，一起在危机前线并肩抗疫。     ☆这是一个改...','强尼马萨洛,蕾娜塔卡尔瓦荷,维克多卡米洛','男同','巴西','罗德里戈德奥利维拉',0,'/upload/upload/20240611/2024061122372422370.jpeg','多元性别,跨性别,男同志,HIV,音乐舞蹈,欧洲,电影','','2021',1,1,207,0,2,1,NULL,'2024-06-24 16:43:16','19',NULL,0),(16,'激浪舞池','在看尽交友软体上无数则充满歧视和挑衅意味的文字后，马克收到朋友的邀请，踏入象徵自由的舞厅。在拥挤的空间裡，马克注意到了浑身性感的里欧，撩得他身体发热、难以自拔……     ☆《忠犬之死》导演发扬Bal...','潘特里诺,哈维尔德里昂','男同','西班牙','克里斯蒂安西查斯',0,'/upload/upload/20240611/2024061122372933582.jpeg','男同志,情欲,音乐舞蹈,欧洲,短片','','2023',1,0,73,0,1,1,NULL,'2024-06-24 16:43:16','20',NULL,0),(17,'今天谁当零','在看尽交友软体上无数则充满歧视和挑衅意味的文字后，马克收到朋友的邀请，踏入象徵自由的舞厅。在拥挤的空间裡，马克注意到了浑身性感的里欧，撩得他身体发热、难以自拔……     ☆《忠犬之死》导演发扬Bal...','潘特里诺,哈维尔德里昂','男同','西班牙','克里斯蒂安西查斯',0,'/upload/upload/20240611/2024061122373478898.jpeg','男同志,情欲,音乐舞蹈,欧洲,短片','','2023',1,6,138,0,0,1,NULL,'2024-06-24 16:43:16','21',NULL,0),(18,'哥儿们的诱惑','在看尽交友软体上无数则充满歧视和挑衅意味的文字后，马克收到朋友的邀请，踏入象徵自由的舞厅。在拥挤的空间裡，马克注意到了浑身性感的里欧，撩得他身体发热、难以自拔……     ☆《忠犬之死》导演发扬Bal...','潘特里诺,哈维尔德里昂','男同','西班牙','克里斯蒂安西查斯',0,'/upload/upload/20240611/2024061122373854498.jpeg','男同志,情欲,音乐舞蹈,欧洲,短片','','2023',1,0,73,1,3,1,NULL,'2024-06-24 16:43:16','22',NULL,0),(19,'两个儿子一个柜子','1986年，苦于无法做自己的亨利意外地穿越时空，与30年后的班相遇。同样的房间，不同的时间，缔结了深厚友谊的两位青少年，正面临着类似的人生境遇。     ☆时代更迭，歧视也仍在蔓延   ☆《公园小情人...','汤米奈特,卡拉斯贝尔曼','男同','英国','洛伊德艾尔摩根',0,'/upload/upload/20240611/2024061122374332529.jpeg','男同志,霸凌,同志子女,奇幻,欧洲,短片','','2015',1,1,130,0,3,1,NULL,'2024-06-24 16:43:16','23',NULL,0),(20,'重口味玩很大','一名来自德国的游客在交友软体上约了两位以色列帅弟，打算来场最「哈扣」的跨国交流。但随着招式愈来愈多、口味愈变愈重，男孩们似乎快招架不住了！     ☆你可以硬起来，但你不能硬着来！   ☆《泳漾水男孩...','奥利莱泽罗维奇,奥默佩雷尔曼斯特里克斯','男同','以色列','摩许罗森塔尔',0,'/upload/upload/20240611/2024061122375220644.jpeg','男同志,约炮,喜剧,中东,短片','','2016',1,17,853,1,0,1,NULL,'2024-06-24 16:43:16','25',NULL,0),(21,'困兽之爱','一名来自德国的游客在交友软体上约了两位以色列帅弟，打算来场最「哈扣」的跨国交流。但随着招式愈来愈多、口味愈变愈重，男孩们似乎快招架不住了！     ☆你可以硬起来，但你不能硬着来！   ☆《泳漾水男孩...','奥利莱泽罗维奇,奥默佩雷尔曼斯特里克斯','男同','以色列','摩许罗森塔尔',0,'/upload/upload/20240611/2024061122375788865.jpeg','男同志,约炮,喜剧,中东,短片','','2016',1,1,141,0,7,1,NULL,'2024-06-24 16:43:16','26',NULL,0);


DROP TABLE IF EXISTS `ks_original_comment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ks_original_comment` (
`id` int NOT NULL AUTO_INCREMENT,
`original_id` int unsigned NOT NULL DEFAULT '0' COMMENT '原创ID',
`pid` int NOT NULL DEFAULT '0' COMMENT '评论ID,默认0(第一层评论)',
`aff` int NOT NULL DEFAULT '0' COMMENT '用户aff',
`content` text NOT NULL COMMENT '留言内容',
`status` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '0:待审核\n1:审核通过\n2.未通过\n3.禁言\n',
`like_num` int NOT NULL DEFAULT '0' COMMENT '此条评论点赞数量',
`ipstr` varchar(60) NOT NULL DEFAULT '' COMMENT '用户ip',
`cityname` varchar(100) NOT NULL DEFAULT '' COMMENT '定位城市',
`complain_num` int unsigned NOT NULL DEFAULT '0' COMMENT '被举报次数',
`refuse_reason` varchar(255) NOT NULL DEFAULT '' COMMENT '拒绝通过原因',
`created_at` timestamp NULL DEFAULT NULL,
`updated_at` timestamp NULL DEFAULT NULL,
PRIMARY KEY (`id`),
KEY `original_id` (`original_id`),
KEY `pid` (`pid`),
KEY `aff` (`aff`),
KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='原创评论表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ks_original_comment`
--

LOCK TABLES `ks_original_comment` WRITE;
/*!40000 ALTER TABLE `ks_original_comment` DISABLE KEYS */;
INSERT INTO `ks_original_comment` VALUES (1,1,0,24237193,'傻儿子，叫爸爸',1,1,'170.187.227.176','火星',0,'','2024-06-24 11:57:28','2024-06-24 12:05:22'),(2,102,0,25954430,'傻儿子，叫爸爸',1,1,'170.187.227.176','火星',0,'','2024-07-01 08:05:23','2024-07-01 08:05:28'),(3,102,0,24975148,'精品中的精品',1,1,'170.187.227.176','火星',0,'','2024-07-01 08:28:09','2024-07-03 13:14:28'),(4,100,0,24237193,'还不错哦',1,0,'170.187.227.176','火星',0,'','2024-07-01 08:35:31','2024-07-01 08:35:31'),(5,59,0,24237193,'还不错',1,0,'170.187.227.176','火星',0,'','2024-07-01 08:43:10','2024-07-01 08:43:10'),(6,102,0,23810708,'很不错的视频',1,0,'143.42.77.60','火星',0,'','2024-07-03 12:32:53','2024-07-03 13:24:01'),(7,65,0,26044012,'颠鸾倒凤不知天地为何物',1,1,'170.187.227.176','火星',0,'','2024-07-06 09:03:56','2024-08-23 04:32:58'),(8,380,0,26096739,'好看的',1,0,'172.104.168.225','火星',0,'','2024-07-10 04:30:39','2024-07-10 04:30:39'),(9,380,0,26096739,'刺激',1,0,'172.104.168.225','火星',0,'','2024-07-10 07:25:16','2024-07-10 07:25:16'),(10,340,0,26096739,'好看好看',1,0,'172.104.168.225','火星',0,'','2024-07-10 14:40:14','2024-07-10 14:40:14'),(11,343,0,273571,'推荐的都是看不了的。',1,0,'183.198.148.197','河北衡水',0,'','2024-07-11 20:50:43','2024-07-11 20:50:43'),(12,356,0,164554,'激情澎湃',1,0,'2a09:bac5:626b:1246::1d2:7a','火星',0,'','2024-07-12 15:09:00','2024-07-12 15:09:00'),(13,61,0,23488957,'好盆油',1,0,'2408:8221:3513:ce60:7df5:a824:8069:3312','中国河南安阳',0,'','2024-07-15 21:25:47','2024-07-15 21:25:47'),(14,372,0,22018387,'不能看后四分钟',1,0,'156.251.179.239','火星',0,'','2024-07-17 19:57:52','2024-07-17 19:57:52'),(15,372,0,22018387,'啊啊啊啊啊啊为什么不能看后半段啊',1,0,'156.251.179.239','火星',0,'','2024-07-19 20:09:19','2024-07-19 20:09:19'),(16,244,0,18207644,'我去',1,0,'240e:448:4c00:8d25:44c5:b7ff:fe73:fcb6','新疆',0,'','2024-07-22 18:57:28','2024-07-22 18:57:28'),(17,385,0,10862168,'哈哈',1,0,'103.1.158.31','香港香港',0,'','2024-08-01 15:06:24','2024-08-01 15:06:24'),(18,376,0,152716,'看片误入高端局',1,1,'2409:8a14:73:af60:1412:297f:e4c9:48a1','中国辽宁沈阳',0,'','2024-08-02 12:19:39','2024-10-19 22:48:32'),(19,384,0,26613070,'。。。',1,0,'39.158.33.69','江西上饶',0,'','2024-08-09 04:29:54','2024-08-09 04:29:54'),(20,380,0,26613070,'。。。。',1,0,'39.158.33.69','江西上饶',0,'','2024-08-09 04:30:04','2024-08-09 04:30:04'),(21,460,0,12141796,'我还以为男主会被肏呢',1,3,'240e:33d:4700:3100:45a1:da7d:ea4b:e388','中国河南新乡',0,'','2024-08-11 16:51:25','2024-11-09 11:25:24'),(22,282,0,503738,'为啥这么卡？卡的都看不了',1,0,'240e:348:133d:83b0:d91:9591:f255:1df5','新疆',0,'','2024-08-13 19:10:54','2024-08-13 19:10:54'),(24,298,0,11085719,'贺飞',1,0,'116.252.240.158','广西南宁',0,'','2024-08-25 19:29:18','2024-08-25 19:29:18'),(25,384,0,26696110,'…-',1,0,'120.227.141.180','湖南',0,'','2024-08-29 18:51:35','2024-08-29 18:51:35'),(26,460,0,1370755,'看了半天都没亲过嘴，看来是两个直男演员',1,1,'2409:8d80:620b:7816:8c0f:eeff:fe26:fac6','火星',0,'','2024-09-05 21:19:12','2024-11-13 05:55:53'),(29,18,0,25733657,'所有的视频都太卡了',1,0,'120.229.149.190','广东东莞',0,'','2024-09-12 17:35:39','2024-09-12 17:35:39'),(30,369,0,25136127,'乖听话，爸爸一会儿请你吃冰棍',1,0,'2409:8934:1cd5:754:a5ed:48d0:3677:879e','中国福建厦门',0,'','2024-09-17 18:11:14','2024-09-17 18:11:14'),(31,379,0,251207,'王瀚',1,0,'2409:8a62:c31:f8a0:a555:414a:9ada:58d','中国四川成都',0,'','2024-09-17 22:28:02','2024-09-17 22:28:02'),(32,379,0,251207,'王瀚',1,0,'2409:8a62:c31:f8a0:a555:414a:9ada:58d','中国四川成都',0,'','2024-09-17 22:28:09','2024-09-17 22:28:09'),(33,349,0,10049243,'吸血鬼',1,0,'2409:8929:a137:a217::1','中国浙江',0,'','2024-10-06 09:56:32','2024-10-06 09:56:32'),(34,460,0,13754,'应该拍男主被內射，然后舔他流出牛奶的屁眼才对',1,2,'180.142.174.14','广西南宁',0,'','2024-10-10 13:16:46','2024-11-16 07:39:25'),(35,460,0,16786276,'你给我出来',1,0,'2409:8a50:4d2:130:790b:63c:9e3f:d827','中国湖南长沙',0,'','2024-10-14 11:24:05','2024-10-14 11:24:05'),(36,460,0,16786276,'这两可真有缘',1,0,'2409:8a50:4d2:130:790b:63c:9e3f:d827','中国湖南长沙',0,'','2024-10-14 11:27:37','2024-10-30 07:01:28'),(37,20,0,27454518,'好多',1,0,'240e:47e:32e9:5502:17ff:448b:23eb:271e','中国广东',0,'','2024-10-17 14:47:01','2024-10-17 14:47:01'),(39,375,0,26096739,'好看',1,1,'172.104.168.225','火星',0,'','2024-11-04 10:13:07','2024-11-04 10:13:19'),(40,1457,0,26555760,'怎么看不了',1,0,'2409:8903:a801:fef7:d624:70f:a5bd:8827','中国天津天津',0,'','2024-11-18 03:37:51','2024-11-18 03:37:51'),(42,1380,0,27928183,'操你妈',1,0,'27.190.170.138','河北唐山',0,'','2024-11-23 00:10:16','2024-11-23 00:10:16');
/*!40000 ALTER TABLE `ks_original_comment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ks_original_comment_user_like`
--

DROP TABLE IF EXISTS `ks_original_comment_user_like`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ks_original_comment_user_like` (
`id` int unsigned NOT NULL AUTO_INCREMENT,
`aff` int NOT NULL,
`related_id` int NOT NULL,
`original_id` int NOT NULL DEFAULT '0',
`created_at` timestamp NULL DEFAULT NULL,
`updated_at` timestamp NULL DEFAULT NULL,
PRIMARY KEY (`id`),
KEY `aff` (`aff`),
KEY `related_id` (`related_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ks_original_comment_user_like`
--

LOCK TABLES `ks_original_comment_user_like` WRITE;
/*!40000 ALTER TABLE `ks_original_comment_user_like` DISABLE KEYS */;
/*!40000 ALTER TABLE `ks_original_comment_user_like` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ks_original_pay`
--

DROP TABLE IF EXISTS `ks_original_pay`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ks_original_pay` (
`id` int NOT NULL AUTO_INCREMENT,
`uid` int NOT NULL COMMENT '用户id',
`coins` int NOT NULL DEFAULT '0' COMMENT '购买时的价格',
`video_id` int NOT NULL COMMENT '视频ID',
`created_at` datetime NOT NULL COMMENT '购买时间',
`original_id` int NOT NULL DEFAULT '0' COMMENT '原创ID',
PRIMARY KEY (`id`) USING BTREE,
KEY `uid` (`uid`,`video_id`) USING BTREE,
KEY `created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='原创视频购买记录';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ks_original_pay`
--

LOCK TABLES `ks_original_pay` WRITE;
/*!40000 ALTER TABLE `ks_original_pay` DISABLE KEYS */;
/*!40000 ALTER TABLE `ks_original_pay` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ks_original_tags`
--

DROP TABLE IF EXISTS `ks_original_tags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ks_original_tags` (
`id` int unsigned NOT NULL AUTO_INCREMENT,
`name` varchar(50) NOT NULL DEFAULT '' COMMENT '标签',
`category` varchar(50) NOT NULL DEFAULT '' COMMENT '分类',
`sort_num` int NOT NULL DEFAULT '0' COMMENT '排序',
`status` tinyint DEFAULT '1' COMMENT '状态',
PRIMARY KEY (`id`) USING BTREE,
KEY `cate` (`category`) USING BTREE,
KEY `sort_num` (`sort_num`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='原创标签';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ks_original_tags`
--

LOCK TABLES `ks_original_tags` WRITE;
/*!40000 ALTER TABLE `ks_original_tags` DISABLE KEYS */;
INSERT INTO `ks_original_tags` VALUES (1,'电影','type',110,1),(2,'短片','type',100,1),(3,'影集','type',130,1),(4,'花絮','type',0,0),(5,'原创','plot',0,0),(6,'青春恋爱','plot',0,0),(7,'喜剧','plot',0,0),(8,'出柜','plot',99,1),(9,'改编作品','plot',0,0),(10,'爱情','plot',0,0),(11,'运动','plot',0,0),(12,'奇幻','plot',0,0),(13,'职场恋爱','plot',0,0),(14,'性向启蒙','plot',0,0),(15,'同志家长','plot',0,0),(16,'同志子女','plot',0,0),(17,'情欲','plot',0,0),(18,'惊悚','plot',0,0),(19,'出轨','plot',0,0),(20,'旅游','plot',0,0),(21,'音乐舞蹈','plot',0,0),(22,'美食','plot',0,0),(23,'年龄差恋爱','plot',0,0),(24,'华人','plot',0,0),(25,'纪录','plot',0,0),(26,'分手','plot',0,0),(27,'婚姻关系','plot',0,0),(28,'HIV','plot',0,0),(29,'宗教','plot',0,0),(30,'霸凌','plot',0,0),(31,'约炮','plot',0,0),(32,'BDSM','plot',0,0),(33,'变装','plot',0,0),(34,'节庆','plot',0,0),(35,'动画','plot',0,1),(36,'综艺','plot',0,0),(37,'台湾','area',0,1),(38,'日本','area',0,1),(39,'韩国','area',0,1),(40,'泰国','area',0,1),(41,'菲律宾','area',0,1),(42,'中国及香港','area',0,1),(43,'印度','area',0,1),(44,'亚洲','area',0,1),(45,'北美','area',0,1),(46,'拉丁美洲','area',0,1),(47,'欧洲','area',0,1),(48,'纽澳','area',0,1),(49,'中东','area',0,1),(50,'非洲','area',0,1),(51,'男同志','lgbt',0,1),(52,'女同志','lgbt',0,1),(53,'双性恋','lgbt',0,1),(54,'跨性别','lgbt',0,1),(55,'多元性别','lgbt',0,1),(59,'校园','plot',110,0),(60,'腐向','plot',100,1),(61,'传记','type',100,1),(62,'剧情','type',99,1),(63,'喜剧','plot',98,0),(64,'家庭','plot',97,0),(65,'运动','plot',96,1),(66,'校园','type',98,1),(67,'奇幻','plot',95,1),(68,'惊悚','plot',94,0),(69,'喜剧','type',97,1),(70,'现代','plot',93,1),(71,'古装','plot',92,1),(72,'家庭','type',96,1),(73,'犯罪','plot',91,1),(74,'悬疑','plot',90,1),(75,'运动','type',95,0),(76,'HE','plot',89,1),(77,'耽改','plot',87,1),(78,'腐向','type',94,0),(79,'小说改编','plot',86,1),(81,'漫画改编','plot',85,1),(82,'惊悚','type',92,1),(83,'现代','type',91,0),(84,'古装','type',90,0),(85,'犯罪','type',89,0),(86,'悬疑','type',88,0),(87,'HE','type',87,0),(88,'BE','type',86,0),(89,'小说改编','type',85,0),(91,'真实改编','type',84,0),(92,'微电影','type',83,1),(93,'BE','plot',88,0),(94,'奇幻','type',93,0);
/*!40000 ALTER TABLE `ks_original_tags` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ks_original_user_like`
--

DROP TABLE IF EXISTS `ks_original_user_like`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ks_original_user_like` (
`id` int NOT NULL AUTO_INCREMENT,
`original_id` int NOT NULL DEFAULT '0',
`uid` int NOT NULL DEFAULT '0',
`updated_at` timestamp NULL DEFAULT NULL,
`created_at` timestamp NULL DEFAULT NULL,
PRIMARY KEY (`id`) USING BTREE,
KEY `original_id` (`original_id`) USING BTREE,
KEY `uid` (`uid`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='原创用户点赞记录';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ks_original_user_like`
--

LOCK TABLES `ks_original_user_like` WRITE;
/*!40000 ALTER TABLE `ks_original_user_like` DISABLE KEYS */;
/*!40000 ALTER TABLE `ks_original_user_like` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ks_original_video`
--

DROP TABLE IF EXISTS `ks_original_video`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ks_original_video` (
`id` int NOT NULL AUTO_INCREMENT,
`pid` int NOT NULL DEFAULT '0' COMMENT '原创ID',
`cover` varchar(255) NOT NULL DEFAULT '' COMMENT '封面',
`source` varchar(255) NOT NULL DEFAULT '' COMMENT '影片资源 电影',
`duration` int unsigned NOT NULL DEFAULT '0' COMMENT '时长秒',
`width` int NOT NULL DEFAULT '0' COMMENT '宽度',
`height` int NOT NULL DEFAULT '0' COMMENT '高度',
`sort` int NOT NULL DEFAULT '0' COMMENT '剧集排序',
`type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 电影 2电视剧',
`coins` int unsigned NOT NULL DEFAULT '0' COMMENT '定价',
`is_free` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否免费 0 收费 1 免费',
`like_count` int NOT NULL DEFAULT '0' COMMENT '点赞数',
`play_count` int NOT NULL DEFAULT '0' COMMENT '播放数',
`com_count` int NOT NULL DEFAULT '0' COMMENT '评论数',
`pay_count` int DEFAULT '0' COMMENT '售卖次数',
`status` tinyint NOT NULL DEFAULT '0' COMMENT '0下架1上架',
`refresh_at` datetime DEFAULT NULL COMMENT '刷新时间',
`created_at` datetime DEFAULT NULL COMMENT '创建时间',
`source_id` int NOT NULL DEFAULT '0' COMMENT '资源ID 采集识别',
`source_video_id` varchar(32) NOT NULL DEFAULT '0' COMMENT '资源视频ID 采集识别',
`title` varchar(255) NOT NULL DEFAULT '' COMMENT '标题',
PRIMARY KEY (`id`) USING BTREE,
KEY `status` (`status`) USING BTREE,
KEY `pid` (`pid`) USING BTREE,
KEY `source_id` (`source_id`) USING BTREE,
KEY `source_video_id` (`source_video_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='原创视频表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ks_original_video`
--

LOCK TABLES `ks_original_video` WRITE;
/*!40000 ALTER TABLE `ks_original_video` DISABLE KEYS */;
INSERT INTO `ks_original_video` VALUES (1,1,'/upload_01/xiao/20240618/2024061815202468220.jpg','/videos4/c78e599c2c76fbb0148af50811aa4c19/c78e599c2c76fbb0148af50811aa4c19.m3u8',669,1920,1080,1,1,4,0,0,0,0,4,1,NULL,'2024-06-24 16:43:15',0,'4',''),(2,2,'/upload_01/xiao/20240618/2024061815204573275.jpg','/videos4/042b14ec91fa190213ecf29af1bbc4eb/042b14ec91fa190213ecf29af1bbc4eb.m3u8',1106,1920,1080,1,1,5,0,0,0,0,3,1,NULL,'2024-06-24 16:43:15',0,'5',''),(3,3,'/upload_01/xiao/20240618/2024061815214327127.jpg','/videos4/5b36839290d6e0d5ba815b569943a48b/5b36839290d6e0d5ba815b569943a48b.m3u8',603,1920,1080,1,1,4,0,0,0,0,3,1,NULL,'2024-06-24 16:43:15',0,'6',''),(4,4,'/upload_01/xiao/20240618/2024061815214425932.jpg','/videos4/d94b8df88960622a62d1cbe796da4f41/d94b8df88960622a62d1cbe796da4f41.m3u8',995,1920,1080,1,1,5,0,0,0,0,3,1,NULL,'2024-06-24 16:43:15',0,'7',''),(5,5,'/upload_01/xiao/20240618/2024061815214375459.jpg','/videos4/40d05dd9c003390ca27dc18c5ef52bd9/40d05dd9c003390ca27dc18c5ef52bd9.m3u8',800,1920,1080,1,1,4,0,0,0,0,2,1,NULL,'2024-06-24 16:43:15',0,'8',''),(6,6,'/upload_01/xiao/20240618/2024061815214857382.jpg','/videos4/079d338e0b524b615edd1f1ec749ab96/079d338e0b524b615edd1f1ec749ab96.m3u8',6082,1920,1080,1,1,13,0,0,0,0,3,1,NULL,'2024-06-24 16:43:15',0,'9',''),(7,7,'/upload_01/xiao/20240618/2024061815232236848.jpg','/videos4/7240ef737222954a8450ad77463eb680/7240ef737222954a8450ad77463eb680.m3u8',955,1920,1080,1,1,5,0,0,0,0,1,1,NULL,'2024-06-24 16:43:15',0,'10',''),(8,8,'/upload_01/xiao/20240618/2024061815232272823.jpg','/videos4/37e96d21ec1b0feb79115f3f7c0f3fa2/37e96d21ec1b0feb79115f3f7c0f3fa2.m3u8',1223,1920,1080,1,1,7,0,0,0,0,1,1,NULL,'2024-06-24 16:43:15',0,'11',''),(9,9,'/upload_01/xiao/20240618/2024061815331892303.jpg','/videos4/d6e6adad722fe83e7e9b08e667bf711e/d6e6adad722fe83e7e9b08e667bf711e.m3u8',5848,1920,1080,1,1,13,0,0,0,0,1,1,NULL,'2024-06-24 16:43:15',0,'20',''),(10,10,'/upload_01/xiao/20240618/2024061815361595495.jpg','/videos4/02c3ecf415583c4c046b113eb3516c54/02c3ecf415583c4c046b113eb3516c54.m3u8',3662,1920,1080,1,1,10,0,0,0,0,0,1,NULL,'2024-06-24 16:43:15',0,'24',''),(11,11,'/upload_01/xiao/20240618/2024061815232256969.jpg','/videos4/1d0a2c4bb206a4b1064ec289c3e6c524/1d0a2c4bb206a4b1064ec289c3e6c524.m3u8',1299,1920,1080,1,2,0,0,0,0,0,0,1,NULL,'2024-06-24 16:43:15',0,'12',''),(12,11,'/upload_01/xiao/20240618/2024061815251630886.jpg','/videos4/717884bb319ed1cb2fb8fe691adeb852/717884bb319ed1cb2fb8fe691adeb852.m3u8',1235,1920,1080,2,2,5,0,0,0,0,0,1,NULL,'2024-06-24 16:43:15',0,'13',''),(13,11,'/upload_01/xiao/20240618/2024061815251614375.jpg','/videos4/78b83087c7cad5da588a5c66d21877b9/78b83087c7cad5da588a5c66d21877b9.m3u8',969,1920,1080,3,2,5,0,0,0,0,0,1,NULL,'2024-06-24 16:43:15',0,'14',''),(14,11,'/upload_01/xiao/20240618/2024061815270343574.jpg','/videos4/87d5d069d4ff62575d249e1be4a17973/87d5d069d4ff62575d249e1be4a17973.m3u8',1416,1920,1080,4,2,5,0,0,0,0,0,1,NULL,'2024-06-24 16:43:16',0,'16',''),(15,11,'/upload_01/xiao/20240618/2024061815305678315.jpg','/videos4/cbad2836578ccf070bcb0fa995c4af62/cbad2836578ccf070bcb0fa995c4af62.m3u8',1518,1920,1080,5,2,5,0,0,0,0,0,1,NULL,'2024-06-24 16:43:16',0,'17',''),(16,11,'/upload_01/xiao/20240618/2024061815322924947.jpg','/videos4/9452e6f53c3b46201d2f4bd390244360/9452e6f53c3b46201d2f4bd390244360.m3u8',1380,1920,1080,6,2,5,0,0,0,0,0,1,NULL,'2024-06-24 16:43:16',0,'18',''),(17,11,'/upload_01/xiao/20240618/2024061815322951368.jpg','/videos4/c46cb4300cb37c7ef9a1a53842e63a15/c46cb4300cb37c7ef9a1a53842e63a15.m3u8',1418,1920,1080,7,2,5,0,0,0,0,0,1,NULL,'2024-06-24 16:43:16',0,'19',''),(18,11,'/upload_01/xiao/20240618/2024061815344827882.jpg','/videos4/a684b7ab87820f29df228ddf6dfeda58/a684b7ab87820f29df228ddf6dfeda58.m3u8',1636,1920,1080,8,2,5,0,0,0,0,0,1,NULL,'2024-06-24 16:43:16',0,'21',''),(19,12,'/upload/upload/20240611/2024061122371049109.jpeg','/videos4/408fa76f9ebc7ef823da703b89541e19/408fa76f9ebc7ef823da703b89541e19.m3u8',968,1920,1080,1,1,5,0,0,0,0,0,1,NULL,'2024-06-24 16:43:16',0,'26',''),(20,13,'/upload/upload/20240611/2024061122371415250.jpeg','/videos4/54094ee663542218d89c190d5a3557df/54094ee663542218d89c190d5a3557df.m3u8',1303,1920,1080,1,1,7,0,0,0,0,5,1,NULL,'2024-06-24 16:43:16',0,'28',''),(21,14,'/upload_01/xiao/20240618/2024061815270222149.jpg','/videos4/e81c000e04c220b97d530b3ef42ace7d/e81c000e04c220b97d530b3ef42ace7d.m3u8',699,1920,1080,1,1,4,0,0,0,0,1,1,NULL,'2024-06-24 16:43:16',0,'15',''),(22,15,'/upload_01/xiao/20240618/2024061815470365945.jpg','/videos4/b2bbde525b6026af902ee729e0db39f1/b2bbde525b6026af902ee729e0db39f1.m3u8',6424,1920,1080,1,1,13,0,0,0,0,2,1,NULL,'2024-06-24 16:43:16',0,'41',''),(23,16,'/upload_01/xiao/20240618/2024061815492460954.jpg','/videos4/47074d7acb047ed688baa13e5c02d3f3/47074d7acb047ed688baa13e5c02d3f3.m3u8',1917,1920,1080,1,1,8,0,0,0,0,1,1,NULL,'2024-06-24 16:43:16',0,'44',''),(24,17,'/upload_01/xiao/20240618/2024061815535487726.jpg','/videos4/570d0d69bde49613b1e2ed34d66e9414/570d0d69bde49613b1e2ed34d66e9414.m3u8',560,1920,1080,1,1,0,0,0,0,0,0,1,NULL,'2024-06-24 16:43:16',0,'45',''),(25,18,'/upload_01/xiao/20240618/2024061815541269370.jpg','/videos4/c3b178379e99b3d6f8fde6d30fc32cf3/c3b178379e99b3d6f8fde6d30fc32cf3.m3u8',1088,1920,1080,1,1,4,0,0,0,0,3,1,NULL,'2024-06-24 16:43:16',0,'46',''),(26,19,'/upload_01/xiao/20240618/2024061815544033740.jpg','/videos4/ac5fd519609ced396a55db446eec7740/ac5fd519609ced396a55db446eec7740.m3u8',1093,1920,1080,1,1,4,0,0,0,0,3,1,NULL,'2024-06-24 16:43:16',0,'47',''),(27,20,'/upload_01/xiao/20240618/2024061815544114900.jpg','/videos4/2d0ca388a85c78929fe3913ef4f3081b/2d0ca388a85c78929fe3913ef4f3081b.m3u8',482,1920,1080,1,1,0,0,0,0,0,0,1,NULL,'2024-06-24 16:43:16',0,'48',''),(28,21,'/upload_01/xiao/20240618/2024061815550555690.jpg','/videos4/00b1c693f1abdfb359c0a88987a623bd/00b1c693f1abdfb359c0a88987a623bd.m3u8',1413,1920,1080,1,1,5,0,0,0,0,7,1,NULL,'2024-06-24 16:43:16',0,'49',''),(29,22,'/upload_01/xiao/20240618/2024061815553587302.jpg','/videos4/8db49dc269dcf76d679d85d25012df46/8db49dc269dcf76d679d85d25012df46.m3u8',1175,1920,1080,1,1,5,0,0,0,0,1,1,NULL,'2024-06-24 16:43:16',0,'50',''),(30,23,'/upload_01/xiao/20240618/2024061815555824812.jpg','/videos4/8ece58128f035a9f1186ca0d030abfc4/8ece58128f035a9f1186ca0d030abfc4.m3u8',1017,1920,1080,1,1,5,0,0,0,0,0,1,NULL,'2024-06-24 16:43:16',0,'51',''),(31,24,'/upload_01/xiao/20240618/2024061815565757671.jpg','/videos4/c942a5516c0329080f41a5033152be4c/c942a5516c0329080f41a5033152be4c.m3u8',3815,1920,1080,1,1,10,0,0,0,0,1,1,NULL,'2024-06-24 16:43:16',0,'52',''),(32,25,'/upload_01/xiao/20240618/2024061815581361282.jpg','/videos4/9f6bb9fc24e33dd47b87a50000cdb507/9f6bb9fc24e33dd47b87a50000cdb507.m3u8',1490,1920,1080,1,1,7,0,0,0,0,2,1,NULL,'2024-06-24 16:43:16',0,'53','');




--
-- Table structure for table `ks_topic`
--

DROP TABLE IF EXISTS `ks_topic`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ks_topic` (
`id` bigint NOT NULL AUTO_INCREMENT,
`uid` int NOT NULL COMMENT 'uid',
`is_top` tinyint NOT NULL COMMENT '是否置顶',
`title` varchar(255) NOT NULL COMMENT '合集标题',
`desp` varchar(255) NOT NULL COMMENT '合集介绍',
`image` varchar(255) NOT NULL COMMENT '合集图片',
`video_count` int NOT NULL COMMENT '视频数量',
`like_count` int NOT NULL COMMENT '点赞数量',
`mv_id_str` varchar(512) NOT NULL COMMENT '视频id',
`status` tinyint NOT NULL COMMENT '状态',
`refresh_at` int NOT NULL DEFAULT '0' COMMENT '刷新时间',
`play_count` int NOT NULL DEFAULT '0' COMMENT '视频播放量',
`origin_coins` int NOT NULL DEFAULT '0' COMMENT '原价',
`coins` int NOT NULL DEFAULT '0' COMMENT '实际金币',
PRIMARY KEY (`id`),
KEY `idx_uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='官方的合集';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ks_topic`
--

LOCK TABLES `ks_topic` WRITE;
/*!40000 ALTER TABLE `ks_topic` DISABLE KEYS */;
INSERT INTO `ks_topic` VALUES (2,8959271,0,'日本精品gv','合理里面为大家整理了优质的日本gv视频，请大家尽情的欣赏，喜欢的辛苦动动发财的销售点关注点点赞','/upload_01/ads/20240703/2024070315462812072.jpeg',16,56992,'517872',1,0,64929,93,56),(5,0,0,'极品伪娘合集','','/upload_01/ads/20240705/2024070515050577396.jpeg',35,140,'',1,0,64327,143,88),(6,0,0,'鲜肉奶狗合集','','/upload_01/ads/20240705/2024070515035982269.jpeg',49,402,'',1,0,133119,189,112),(8,0,0,'约炮系列合集','','/upload_01/ads/20240705/2024070515441026941.jpeg',46,196,'',1,0,80339,198,118),(9,0,0,'偷拍系列合集','','/upload_01/ads/20240705/2024070517054719797.jpeg',46,422,'',1,0,242083,179,107),(10,0,0,'网红solo合集','','/upload_01/ads/20240705/2024070517592717257.jpeg',42,331,'',1,0,154757,163,102),(11,0,0,'时间静止系列合集','','/upload_01/ads/20240705/2024070522582199367.jpeg',37,390,'',1,0,58687,137,82),(12,0,0,'小蓝原创合集','','/upload_01/ads/20240705/2024070523102962586.jpeg',4,103,'',1,0,122680,72,45),(13,0,0,'日本CG合集','','/upload_01/ads/20240706/2024070615305764924.jpeg',20,364,'',1,0,241478,160,100),(14,0,0,'《HORMONE》精品合集','','/upload_01/ads/20240706/2024070615381222823.jpeg',18,452,'',1,0,162938,118,78),(15,0,0,'偷拍直男大屌撸射合集','本合集里面收集了大量的偷拍直男撸大屌的视频','/upload_01/ads/20240708/2024070817433591796.jpeg',32,184,'',1,0,45483,200,120),(16,0,0,'勾引色诱合集','','/upload_01/ads/20240708/2024070820063861321.jpeg',28,160,'',1,0,59903,122,73),(17,0,0,'强上系列合集','','/upload_01/ads/20240708/2024070820273728871.jpeg',39,277,'',1,0,59858,157,94),(18,0,0,'迷奸系列合集','','/upload_01/ads/20240708/2024070820463882600.jpeg',25,550,'',1,0,102724,103,62),(19,0,0,'SM捆绑调教系列合集','','/upload_01/ads/20240708/2024070821205922419.jpeg',38,292,'',1,0,68553,159,95),(21,0,0,'山东浩浩系列合集','','/upload_01/ads/20240708/2024070823505837946.jpeg',39,322,'',1,0,85137,148,88),(22,0,0,'网红泄露系列合集','','/upload_01/ads/20240709/2024070912054424724.jpeg',30,196,'',1,0,142214,109,65),(23,0,0,'老头乐系列合集','','/upload_01/ads/20240709/2024070912144665222.png',31,159,'',1,0,148539,117,70),(24,0,0,'《GAYDAR》精品合集','','/upload_01/ads/20240709/2024070912234761186.jpeg',20,801,'',1,0,1172130,182,110),(25,0,0,'《Hunt》精品合集','','/upload_01/ads/20240709/2024070912280849984.jpeg',20,227,'',1,0,58608,139,83),(26,0,0,'《FRESH MAN》精品合集','','/upload_01/ads/20240709/2024070912321282595.jpeg',18,128,'',1,0,20037,89,53),(27,0,0,'车震系列合集','','/upload_01/ads/20240709/2024070912431326001.jpeg',33,164,'',1,0,41941,126,75),(28,0,0,'空少系列合集','','/upload_01/ads/20240709/2024070912551182756.jpeg',20,199,'',1,0,47906,77,48),(29,0,0,'父子乱伦系列合集','','/upload_01/ads/20240709/2024070915242372671.jpeg',37,345,'',1,0,77417,153,102),(30,0,0,'兄弟乱伦系列合集','','/upload_01/ads/20240709/2024070915342815530.jpeg',19,382,'',1,0,94405,77,46),(31,0,0,'人妖系列合集','','/upload_01/ads/20240709/2024070915454566808.jpeg',35,67,'',1,0,44730,140,84),(32,0,0,'精品动漫合集','','/upload_01/ads/20240709/2024070918542546093.jpeg',52,539,'',1,0,244622,223,133),(33,0,0,'网红冲浪小哥合集','','/upload_01/ads/20240709/2024070916121590529.jpeg',48,184,'',1,0,27228,194,146),(34,0,0,'伪娘小辛玩直男合集','伪娘小辛勾引直男系列','/upload_01/ads/20240709/2024070917175314568.jpeg',27,128,'',1,0,27493,96,57),(35,0,0,'金主采访拍摄','金主采访小鲜肉 主打一个真实','',38,261,'',1,0,77135,135,71),(36,0,0,'黑皮肌肉控精','黑皮肌肉男孩 系列 满足的你视觉盛宴','/upload_01/ads/20240709/2024070917230617818.jpeg',64,430,'',1,0,62069,256,153),(38,0,0,'伪娘清子玩大吊直男系列','网黄伪娘清子姐姐玩大屌系列','/upload_01/ads/20240709/2024070917144168067.jpeg',61,233,'',1,0,39996,244,146),(39,0,0,'伪娘明明玩直男系列','网黄伪娘明明姐姐勾引直男系列','/upload_01/ads/20240709/2024070917092948827.jpeg',17,146,'',1,0,8615,68,40),(40,0,0,'外卖小哥被调教系列合集','调教外卖小哥 刺激','/upload_01/ads/20240709/2024070917405392760.jpeg',20,284,'',1,0,49345,80,48),(41,0,0,'健身房筋肉系列合集','健身房激情做爱','/upload_01/ads/20240709/2024070917483126779.jpeg',28,129,'',1,0,36214,118,68),(42,0,0,'网黄东北旺仔合集','网黄东北旺仔激情做爱系列','/upload_01/ads/20240709/2024070918132918279.png',44,309,'',1,0,54013,176,105),(43,0,0,'帅哥飞机合集','各类帅哥打飞机系列','/upload_01/ads/20240709/2024070918205320076.jpeg',97,531,'',1,0,35045,290,174),(44,0,0,'吖弟险过浪合集','','/upload_01/ads/20240709/2024070920320532221.jpeg',40,410,'',1,0,83876,115,69),(45,0,0,'军警系列合集','','/upload_01/ads/20240709/2024070920455038516.jpeg',24,464,'',1,0,97781,104,62),(46,0,0,'小舅子和姐夫偷情系列合集','','/upload_01/ads/20240710/2024071012132834020.jpeg',37,331,'',1,0,75246,150,90),(47,0,0,'《Jock Studio》精品合集','','/upload_01/ads/20240710/2024071014580511660.jpeg',6,472,'',1,0,46255,30,18),(48,0,0,'《Sodomy Squad》精品合集','','/upload_01/ads/20240710/2024071015054027438.jpeg',6,88,'',1,0,19354,42,25),(49,0,0,'按摩店系列合集','','/upload_01/ads/20240710/2024071016030199611.jpeg',24,363,'',1,0,43768,100,60),(50,0,0,'抖音网红视频流出系列','抖音网红视频流出系列，帅哥们啊，为什么不小心，视频就这样流出了，每个合集20部，以后会陆续更新，敬请期待。','/upload_01/ads/20240719/2024071914480676523.jpeg',20,352,'',1,0,90814,81,48),(51,0,0,'群交聚会淫乱现场系列-1','群交聚会淫乱现场系列-1。此合集中有欧美群交和亚洲群交，场面混乱，超级刺激。','/upload_01/ads/20240719/2024071914585676393.jpeg',25,172,'',1,0,66733,101,60),(52,0,0,'柳乃堂-20帅哥MB','柳乃堂-20帅哥MB','/upload_01/ads/20240719/2024071915061245281.jpeg',21,171,'',1,0,34778,76,45),(53,0,0,'健身教练大吊性瘾小狼做爱系列','健身教练大吊性瘾小狼做爱系列','/upload_01/ads/20240719/2024071915274635556.jpeg',22,195,'',1,0,18921,90,54),(54,0,0,'花钱玩直男系列','花钱玩直男系列，这个系列里面有光头佬花钱玩直男，也有直男直播系列。','/upload_01/ads/20240719/2024071915231172939.jpeg',24,209,'',1,0,31974,85,51),(55,0,0,'抖音网红合集','抖音网红线下约炮系列','/upload_01/ads/20240719/2024071916124987077.png',26,233,'',1,0,57218,104,62),(56,0,0,'保安系列合集','保安做爱系列  真实刺激','/upload_01/ads/20240719/2024071916180359934.jpeg',20,118,'',1,0,30434,80,48),(57,0,0,'小偷合集','偷东西被抓，总是要付出点什么的','/upload_01/ads/20240719/2024071916300288710.jpeg',11,97,'',1,0,7959,44,26),(58,0,0,'泰国鲜肉合集','泰国小鲜肉  妖娆的魅力','/upload_01/ads/20240719/2024071916372886879.jpeg',22,89,'',1,0,9663,105,63),(59,0,0,'ktv操逼合集','灯红酒绿的ktv里 疯狂做爱','/upload_01/ads/20240719/2024071916471494947.jpeg',48,209,'',1,0,95817,196,117),(60,0,0,'极品帅气兵哥哥','各种好看的兵哥哥','/upload_01/ads/20240720/2024072012055451613.jpeg',23,1035,'',1,1721448299,177419,95,57),(61,0,0,'偷吃合集','偷吃出轨系列，紧张刺激','/upload_01/ads/20240720/2024072012100055271.jpeg',105,671,'',1,0,332533,322,193),(62,0,0,'高颜值露脸帅哥','都是帅哥，阅片无数当中的精品。','/upload_01/ads/20240720/2024072012343261220.jpeg',18,838,'',1,1721449860,193814,70,42),(63,0,0,'壮熊合集','壮熊打桩机系列','/upload_01/ads/20240720/2024072012301859683.jpeg',35,327,'',1,0,109887,143,85),(64,0,0,'高中生合集','高中生小鲜肉做爱系列','/upload_01/ads/20240720/2024072012382570087.jpeg',85,624,'',1,0,168498,315,189),(65,0,0,'体育生合集','筋肉体育生做爱系列','/upload_01/ads/20240720/2024072012445134591.jpeg',46,434,'',1,0,145211,199,119),(66,0,0,'日本天菜帅哥','经典日本帅气男优，只选帅的','/upload_01/ads/20240720/2024072012521859247.jpeg',16,1546,'',1,1721451096,123233,56,33),(67,0,0,'主持人合集','正装主持人精彩的私生活','/upload_01/ads/20240720/2024072014471029756.jpeg',30,227,'',1,0,37266,113,67),(68,0,0,'天国守卫','天国守卫cg剧情合集','/upload_01/ads/20240720/2024072015155342979.jpeg',3,496,'',1,1721459546,72708,14,8),(69,0,0,'各种翻车系列合集','各种翻车系列合集，包括偷拍翻车，伪娘翻车，特别是伪娘翻车的对白。相当有意思。','/upload_01/ads/20240720/2024072015280345929.jpeg',16,677,'',1,1721460434,151309,42,25),(70,0,0,'日本男优五十岚系列','可1可0，可主可奴，全能型选手','/upload_01/ads/20240722/2024072215585139707.jpeg',35,163,'',1,0,36342,142,85),(71,0,0,'正装制服 西装社畜','正装制服帅哥','/upload_01/ads/20240722/2024072216025178436.jpeg',41,163,'',1,0,42041,168,100),(72,0,0,'剧情为辅 性爱为主','一些带剧情的爱情动作影片','/upload_01/ads/20240722/2024072216070452348.jpeg',18,132,'',1,0,36150,75,45),(73,0,0,'实录直男卖菊合集','经济压力下的直男们，只能偶尔卖菊贴补家用','/upload_01/ads/20240722/2024072215400846722.jpeg',39,271,'',1,0,52449,156,93),(74,0,0,'我的女装日记合集','收录伪娘女装的极品男人日常','/upload_01/ads/20240722/2024072215541894476.jpeg',12,151,'',1,0,46245,30,18),(75,0,0,'医生合集','性感医生做爱系列','/upload_01/ads/20240722/2024072215482999520.jpeg',32,125,'',1,0,50572,130,78),(76,0,0,'教师合集','教书育人的老师  也得做爱','/upload_01/ads/20240722/2024072215505424835.jpeg',45,132,'',1,0,58912,161,96),(77,0,0,'装修工合集','劳动人民的性爱','/upload_01/ads/20240722/2024072215542796133.jpeg',47,93,'',1,0,28834,170,102),(78,0,0,'清洁工合集','性感清洁工 不仅打扫家  也包括雇主身体','/upload_01/ads/20240722/2024072216004291422.jpeg',47,82,'',1,0,10462,174,104),(79,0,0,'景先生合集','网黄景先生做爱系列','/upload_01/ads/20240723/2024072311261355644.jpeg',32,240,'',1,0,44948,136,81),(80,0,0,'厕所偷拍合集','真实厕所偷拍系列','/upload_01/ads/20240723/2024072311392412092.jpeg',32,245,'',1,0,112626,105,63),(81,0,0,'按摩师合集','勾引按摩师傅做爱系列','/upload_01/ads/20240723/2024072311480858898.jpeg',35,282,'',1,0,99465,140,84),(82,0,0,'星星队长合集','网黄星星队长系列','/upload_01/ads/20240723/2024072311545033833.jpeg',32,449,'',1,0,124682,120,72),(83,0,0,'户外大战系列','户外大战类型的','/upload_01/ads/20240723/2024072316113749074.jpeg',39,120,'',1,0,59942,161,97),(84,0,0,'滴滴司机合集','勾引滴滴司机做爱系列','/upload_01/ads/20240723/2024072312024833672.jpeg',42,309,'',1,0,54635,138,82),(85,0,0,'极品控射合集','该合集为精选极具观赏性的控射影片。控射就是在射精的边缘徘徊，不断抑制精液射出来达到持续的快感，当控射手允许射精或者失控时，这种快感将会爆发，精液如火山喷发一般势不可挡，快感退却，回味无穷。如果继续下去，还有可能潮喷，进入下一阶段的高潮。','/upload_01/ads/20240723/2024072312484535594.jpeg',19,317,'',1,0,56630,78,46),(86,0,0,'恋足舔脚、调教系列','主人的脚就是你的天堂，主人汗液在脚趾间流淌，脱下性感的皮鞋，雄性气息混杂着皮革味扑面而来。足尖充满了雄性荷尔蒙，那里的味道浓郁又芬芳。足弓是脚最性感的位置，仿佛就是上帝为足交开的窗户。拜倒在主人脚下，释放你的天性，让这双脚送你去往天堂。','/upload_01/ads/20240723/2024072312523176019.jpeg',27,259,'',1,0,58030,92,55),(87,0,0,'肌肉、鲜肉帅哥飞机系列','各类肌肉、鲜肉帅哥挑逗你的视觉神经，浓稠精液向你喷涌而来，只为你嘴角那一抹鲜甜。','/upload_01/ads/20240723/2024072312580844781.jpeg',13,198,'',1,0,20889,52,31),(91,0,0,'金主采访拍摄合集','各种为了钱被金主拍视频的直男们','/upload_01/ads/20240723/2024072319535337812.jpeg',18,222,'',1,0,103541,73,43),(92,0,0,'时间静止系列精选2','时间静止，随心所欲强暴，各种场景泄欲','/upload_01/ads/20240723/2024072319595475091.jpeg',20,251,'',1,0,15164,71,42),(93,0,0,'白袜合集','白袜做爱系列','/upload_01/ads/20240724/2024072411573430375.jpeg',35,180,'',1,0,70682,151,90),(94,0,0,'眼镜合集','眼镜帅哥做爱系列','/upload_01/ads/20240724/2024072412022690117.jpeg',37,89,'',1,0,51868,156,93),(96,0,0,'绿帽合集','绿帽男的日常 主打一个真实','/upload_01/ads/20240724/2024072412104649178.jpeg',36,198,'',1,0,90404,143,85),(97,0,0,'肌肉犬合集','肌肉犬爆操系列 满足你的视觉','/upload_01/ads/20240724/2024072412153823870.jpeg',32,234,'',1,0,18223,129,77),(98,0,0,'性感男优做爱系列','性感男优做爱系列','/upload_01/ads/20240724/2024072412205163774.jpeg',18,80,'',1,0,13092,72,43),(99,0,0,'虐待系列合集','虐待系列合集，包含捆绑，喝尿，打嘴巴子等','/upload_01/ads/20240724/2024072412273611628.jpeg',33,260,'',1,0,68977,128,76),(101,0,0,'开发直男的快乐1','迷玩、醉酒、睡睡各种直男被开发','/upload_01/ads/20240724/2024072412372318693.png',20,192,'',1,0,26706,65,39),(102,0,0,'英国华威大学划艇队挂历拍摄现场','英国华威赛艇队每年都会招收一些小鲜肉，并且每年都会拍一组全裸照片，制作成一本日历，售卖日历的收入会用来做慈善。','/upload_01/ads/20240724/2024072412425748715.jpeg',15,135,'',1,0,10766,45,27),(103,0,0,'韩国网红系列合集','韩国网红系列，超帅的颜值，超大的屌','/upload_01/ads/20240724/2024072412491653845.jpeg',30,106,'',1,0,30094,116,69),(104,0,0,'强制撸喷合集','小鲜肉强制撸喷系列','/upload_01/ads/20240724/2024072412493071403.jpeg',23,173,'',1,0,20899,86,51),(105,0,0,'程序员合集','程序员私房事系列','/upload_01/ads/20240725/2024072511592941346.jpeg',48,70,'',1,0,21103,192,115),(106,0,0,'性玩具合集','猛1秒变性爱玩具','/upload_01/ads/20240725/2024072512040490938.jpeg',63,155,'',1,0,30795,209,125),(107,0,0,'子羽哥哥合集','网黄子羽哥哥做爱系列','/upload_01/ads/20240725/2024072512123645064.jpeg',47,243,'',1,0,102514,192,115),(108,0,0,'修理厂的激情合集','与修车工不得不说的秘密','/upload_01/ads/20240725/2024072512114872570.jpeg',69,65,'',1,0,18666,247,148),(109,0,0,'便利店售卖员 合集','勾引便利店小帅哥系列','/upload_01/ads/20240725/2024072512145394867.jpeg',23,85,'',1,0,11341,81,48),(110,0,0,'做爱偷拍合集','真实偷拍系列','/upload_01/ads/20240726/2024072612085844717.jpeg',30,217,'',1,0,166162,108,64),(111,0,0,'帅哥出台合集','骚气男模出台 好好操一晚上','/upload_01/ads/20240726/2024072612130744543.jpeg',46,87,'',1,0,37597,163,97),(112,0,0,'制服合集','各类制服无套做爱系列','/upload_01/ads/20240726/2024072612182145696.jpeg',44,91,'',1,0,27381,143,85),(113,0,0,'强奸合集','得不到心 那就得到身体','/upload_01/ads/20240726/2024072612221376510.jpeg',32,342,'',1,0,123236,134,80),(114,0,0,'警察合集','公职警察的私生活 人民公仆也得做爱','/upload_01/ads/20240726/2024072612255442242.jpeg',41,157,'',1,0,51973,169,101),(116,0,0,'圣诞狂欢合集','','/upload_01/ads/20240726/2024072612541934474.jpeg',37,45,'',1,0,9799,154,92),(117,0,0,'醉酒迷晕开苞玩射系列','专门收藏喝醉酒迷晕的鲜肉，直男，体育生，将他们开苞或者撸射。不知情况下的直男们被迫失去自己第一次，想想就刺激！','/upload_01/ads/20240726/2024072615044444108.jpeg',55,377,'',1,0,101564,198,118),(118,0,0,'抖音里那些帅哥们的人前人后','抖音帅哥的各种反差','/upload_01/ads/20240726/2024072615311397668.jpeg',17,556,'',1,0,253618,69,41),(119,0,0,'肌肉网红斯壮','肌肉网红斯壮','/upload_01/ads/20240726/2024072615504070291.jpeg',20,177,'',1,0,32627,79,47),(120,0,0,'迷玩系列合集','各种迷玩系列合集','/upload_01/ads/20240726/2024072616010537450.jpeg',35,375,'',1,0,76846,119,71),(121,0,0,'无套内射合集','无套内射小帅哥系列','/upload_01/ads/20240727/2024072712003871029.jpeg',34,184,'',1,0,71585,149,89),(122,0,0,'捆绑合集','捆绑调教系列','/upload_01/ads/20240727/2024072712064847664.jpeg',47,287,'',1,0,64431,192,115),(123,0,0,'潜规则合集','被老板潜规则，为了工作付出身体','/upload_01/ads/20240727/2024072712132934970.jpeg',31,127,'',1,0,74422,127,76),(125,0,0,'销售员合集','为了业务出卖自己系列','/upload_01/ads/20240727/2024072712233318777.jpeg',38,81,'',1,0,27407,141,84),(126,0,0,'游泳馆合集','游泳馆内的洗浴室 多人P','/upload_01/ads/20240727/2024072712413930668.jpeg',34,89,'',1,0,19667,139,83),(127,0,0,'金主采访拍摄合集','各种为了钱被金主拍视频的直男们','/upload_01/ads/20240729/2024072912220125803.jpeg',17,124,'',1,0,86994,68,40),(128,0,0,'成都gay圈合集','激情四射的成都','/upload_01/ads/20240729/2024072912280451171.png',37,166,'',1,0,43550,133,79),(129,0,0,'胡须熟男合集','胡须男约炮系列，胡须白袜，是不是长在你审美点上','/upload_01/ads/20240729/2024072912312432723.jpeg',40,67,'',1,0,16629,136,81),(130,0,0,'男浴室合集','男浴室做爱系列','/upload_01/ads/20240729/2024072912363074676.jpeg',42,107,'',1,0,16578,149,89),(131,0,0,'肥臀大屌合集','肥臀大屌激情做爱系列','/upload_01/ads/20240729/2024072912400996152.jpeg',36,75,'',1,0,23108,143,85),(132,0,0,'柒公子合集','网帅柒公子的做爱日常','/upload_01/ads/20240729/2024072912434157660.png',39,122,'',1,0,22458,130,78),(133,0,0,'日本性感男优做爱系列合集','日本性感男优做爱系列','/upload_01/ads/20240729/2024072914470123510.jpeg',37,103,'',1,0,14597,111,66),(134,0,0,'极品帅气兵哥哥合集2','各种好看的兵哥哥','/upload_01/ads/20240729/2024072914552133979.jpeg',33,249,'',1,0,55996,134,80),(135,0,0,'群交聚会淫乱现场系列-2','群交聚会淫乱现场系列','/upload_01/ads/20240729/2024072914592352954.jpeg',36,229,'',1,0,20268,139,83),(136,0,0,'超火网红kk睡不着系列合集','超火网红kk睡不着系列合集','/upload_01/ads/20240729/2024072915034585413.jpeg',17,276,'',1,0,29178,59,35),(137,0,0,'大屌黑人合集','天生大鸡巴的黑人，天生的打桩机','/upload_01/ads/20240730/2024073012184432330.jpeg',37,152,'',1,0,39704,150,90),(138,0,0,'无毛菊合集','爆操无毛菊弟弟系列，光光滑滑','/upload_01/ads/20240730/2024073012222220561.jpeg',38,106,'',1,0,24000,129,77),(139,0,0,'男优冲修斗合集','日本男友冲修斗激情做爱系列','/upload_01/ads/20240730/2024073012251329142.jpeg',29,129,'',1,0,43936,117,70),(140,0,0,'天菜帅哥Aliray合集','网黄天菜帅哥Aliray做爱系列','/upload_01/ads/20240730/2024073012293335348.jpeg',28,110,'',1,0,17012,112,67),(141,0,0,'帅气工程师做爱合集','正装帅气工程师做爱系列','/upload_01/ads/20240730/2024073012334113791.jpeg',38,85,'',1,0,43225,119,71),(142,0,0,'军警系列合集2','军警系列','/upload_01/ads/20240730/2024073012193749147.jpeg',33,269,'',1,0,60622,111,66),(143,0,0,'快乐风男大合集','快乐风男大合集','/upload_01/ads/20240730/2024073012312021383.jpeg',40,693,'',1,0,89707,105,63),(144,0,0,'迷奸大吊直男系列','迷奸大吊直男系列','/upload_01/ads/20240730/2024073012452694301.jpeg',34,524,'',1,0,105941,140,84),(145,0,0,'按摩室的春光系列','按摩室的春光系列。放个帘子就肏','/upload_01/ads/20240730/2024073015045225274.jpeg',34,240,'',1,0,17495,132,79),(146,0,0,'偷拍直男尿尿合集','偷拍直男尿尿合集','/upload_01/ads/20240730/2024073015105924417.jpeg',21,355,'',1,0,53029,84,50),(147,0,0,'韩国欧巴合集','和帅气的韩国欧巴的做爱系列','/upload_01/ads/20240731/2024073112541842709.jpeg',49,149,'',1,0,20041,193,115),(148,0,0,'小先森合集','网黄小先森做爱系列','/upload_01/ads/20240731/2024073112421235388.jpeg',24,125,'',1,0,26681,98,58),(149,0,0,'快递员合集','本来送快递的  送着送着就把自己交代出去了','/upload_01/ads/20240731/2024073112325647026.jpeg',61,200,'',1,0,40669,200,120),(150,0,0,'绿帽老公合集','绿帽奴的性癖 把心爱的老婆送给别人  心里才能得到满足','/upload_01/ads/20240731/2024073112274039231.jpeg',18,167,'',1,0,18736,64,38),(151,0,0,'肌肉猎人合集','肌肉猎人激情做爱系列','/upload_01/ads/20240731/2024073112252095131.jpeg',40,260,'',1,0,30151,144,86),(152,0,0,'舞蹈生合集','优质舞蹈生被操系列','/upload_01/ads/20240801/2024080112253786326.jpeg',37,216,'',1,0,82497,150,90),(153,0,0,'八哥合集','网黄八哥做爱系列，多人群P','/upload_01/ads/20240801/2024080112231416102.jpeg',43,467,'',1,0,129620,179,107),(154,0,0,'禁欲合集','禁欲解禁之后的疯狂','/upload_01/ads/20240801/2024080112174417283.jpeg',54,203,'',1,0,41559,195,117),(155,0,0,'极限暴露合集','在人来人往的区域做爱 紧张刺激','/upload_01/ads/20240801/2024080112125983586.jpeg',47,229,'',1,0,34161,147,88),(156,0,0,'纹身痞子合集','纹身痞子做爱系列','/upload_01/ads/20240801/2024080112093346542.jpeg',38,101,'',1,0,26006,153,91),(157,0,0,'正装系列2','正装系列','/upload_01/ads/20240801/2024080115040786676.jpeg',28,125,'',1,0,100480,115,69),(158,0,0,'伦勃朗系列合集','性虐调教，粗口抽打羞辱，让一个外表爷们的汉子一步步沦为跨下骚犬','/upload_01/ads/20240801/2024080115293725469.jpeg',20,248,'',1,0,58200,73,43),(159,0,0,'浩源学长合集','浩源学长合集','/upload_01/ads/20240801/2024080115440671020.jpeg',20,151,'',1,0,8471,61,36),(160,0,0,'捆绑系列2','捆绑系列2','/upload_01/ads/20240801/2024080115510293311.jpeg',18,128,'',1,0,17976,72,43),(161,0,0,'控射玩弄调教系列2','控射玩弄调教系列2','/upload_01/ads/20240801/2024080116002674647.jpeg',28,193,'',1,0,55434,114,68),(162,0,0,'佐罗S合集','网黄佐罗S做爱系列','/upload_01/ads/20240802/2024080212270229762.jpeg',20,159,'',1,0,44863,80,48),(163,0,0,'优质校草合集','帅气的优质校草做爱打飞机系列','/upload_01/ads/20240802/2024080212215561359.jpeg',24,180,'',1,0,43768,96,57),(164,0,0,'丁字裤鲜肉合集','丁字裤鲜肉做爱系列，情趣内裤是不是你的最爱','/upload_01/ads/20240802/2024080212172631329.jpeg',43,76,'',1,0,14477,154,92),(165,0,0,'车震合集','在车上做爱才是最刺激的吧','/upload_01/ads/20240802/2024080212150619364.jpeg',21,105,'',1,0,21263,76,45),(166,0,0,'办公室合集','在办公室激情打桩系列','/upload_01/ads/20240802/2024080212120216653.jpeg',63,114,'',1,0,33504,238,142),(167,0,0,'厕所偷拍系列','厕所偷拍系列','/upload_01/ads/20240802/2024080215330763117.jpeg',29,225,'',1,0,81735,118,70),(168,0,0,'《明明只是个烟雾弹配角，却得到完美王子的宠爱》','《明明只是个烟雾弹配角，却得到完美王子的宠爱》1-8集','/upload_01/ads/20240802/2024080215582377658.jpeg',8,653,'',1,1722585484,74229,40,24),(169,0,0,'经典日本GV合集2','经典日本GV合集2','/upload_01/ads/20240802/2024080216042193902.jpeg',25,157,'',1,0,88161,126,75),(170,0,0,'伪娘按摩店合集','伪娘按摩店合集','/upload_01/ads/20240802/2024080221102780833.jpeg',18,79,'',1,0,36472,56,33),(171,0,0,'无套群P盛宴','无套群P盛宴','/upload_01/ads/20240802/2024080221125932463.jpeg',27,193,'',1,0,21797,97,58),(172,0,0,'帅气奶狗合集','帅气奶狗做爱系列','/upload_01/ads/20240803/2024080311595024629.jpeg',50,276,'',1,0,28631,179,107),(173,0,0,'换夫合集','换夫做爱系列 体验不一样的鸡巴','/upload_01/ads/20240803/2024080312055370436.jpeg',40,131,'',1,0,19095,140,84),(174,0,0,'大学生下海合集','帅气大学生为钱出卖自己','/upload_01/ads/20240803/2024080312102841862.png',29,132,'',1,0,12044,82,49),(175,0,0,'入室抢劫合集','歹徒入室抢劫劫色不劫财，满屏的壮男肌肉太性感啦','/upload_01/ads/20240803/2024080312172672465.jpeg',67,141,'',1,0,25873,271,162),(176,0,0,'BLUEMEN 蓝男色2 合集','BLUEMEN 蓝男色 最新系列','/upload_01/ads/20240803/2024080312201652351.jpeg',43,171,'',1,0,43273,173,103),(177,0,0,'精品SM系列','精品SM系列','/upload_01/ads/20240803/2024080312521165854.jpeg',32,167,'',1,0,65744,131,78),(178,0,0,'抖音网红视频流出系列','抖音网红视频流出系列','/upload_01/ads/20240803/2024080317215334597.jpeg',16,261,'',1,0,55375,67,40),(179,0,0,'高颜值露脸帅哥系列','高颜值露脸帅哥','/upload_01/ads/20240803/2024080317250131351.png',37,279,'',1,0,170920,152,91),(180,0,0,'光头佬花钱玩直男系列','光头佬花钱玩直男系列','/upload_01/ads/20240803/2024080317274428674.jpeg',35,152,'',1,0,15070,111,66),(181,0,0,'黑皮肌肉控精系列','黑皮肌肉控精系列','/upload_01/ads/20240803/2024080317304656061.jpeg',16,165,'',1,0,14936,53,31),(182,0,0,'女记者采访合集','女记者采访直男，大屌撸射的刺激','/upload_01/ads/20240805/2024080512382599861.jpeg',35,271,'',1,0,56139,143,85),(183,0,0,'健身教练合集第二季','性感健身教练做爱系列第二季来啦','/upload_01/ads/20240805/2024080512350561315.jpeg',37,178,'',1,0,50085,154,92),(184,0,0,'已婚男偷吃合集','已婚男士在外与自己男朋友偷吃系列','/upload_01/ads/20240805/2024080512324370465.jpeg',32,190,'',1,0,34871,114,68),(186,0,0,'澜学长合集','网黄澜学长约炮做爱系列','/upload_01/ads/20240805/2024080512225826006.jpeg',63,213,'',1,0,35949,213,127),(187,0,0,'户外勾引第二弹','户外勾引第二弹来啦，只要你够骚，没什么男搞不到','/upload_01/ads/20240805/2024080512264635663.jpeg',52,203,'',1,0,26320,181,108),(188,0,0,'足交系列2','足交系列2','/upload_01/ads/20240805/2024080515001615917.jpeg',20,139,'',1,0,37855,78,46),(189,0,0,'强上迷醉直男合集2','强上迷醉直男合集2','/upload_01/ads/20240805/2024080515060973740.png',35,367,'',1,0,96195,141,84),(190,0,0,'按摩勾引直男合集','按摩勾引直男合集','/upload_01/ads/20240805/2024080515102087836.jpeg',20,102,'',1,0,8529,70,42),(191,0,0,'欧美合集精选','欧美合集精选','/upload_01/ads/20240805/2024080515140039466.jpeg',30,83,'',1,0,33166,127,76),(192,0,0,'薄肌小鲜肉系列','薄肌小鲜肉系列','/upload_01/ads/20240805/2024080515200624683.jpeg',19,118,'',1,0,12527,66,39),(193,0,0,'帅气兵哥合集第二弹','极品帅气兵哥第二弹来啦，军人也得有性生活','/upload_01/ads/20240806/2024080611472746615.jpeg',45,266,'',1,0,38925,160,96),(194,0,0,'实习生合集','社畜实习生挨操系列','/upload_01/ads/20240806/2024080611444141794.jpeg',43,102,'',1,0,37843,178,106),(195,0,0,'性瘾小狼合集第二弹','网黄性瘾小狼激情做爱系列第二弹','/upload_01/ads/20240806/2024080611425683080.jpeg',28,146,'',1,0,24217,115,69),(196,0,0,'壮熊合集第二弹','壮熊做爱系列第二弹','/upload_01/ads/20240806/2024080611393820185.jpeg',48,150,'',1,0,89344,197,118),(197,0,0,'网黄安德烈合集','网黄安德烈做爱系列','/upload_01/ads/20240806/2024080611341565975.jpeg',24,206,'',1,0,64208,93,55),(198,0,0,'调教系列合集','调教系列合集','/upload_01/ads/20240806/2024080611482650272.jpeg',18,228,'',1,0,98505,75,45),(199,0,0,'偷拍合集','偷拍合集','/upload_01/ads/20240806/2024080611510715819.png',15,131,'',1,0,26421,56,33),(200,0,0,'抖音网红反差合集','抖音里那些帅哥们的人前人后','/upload_01/ads/20240806/2024080611580333959.jpeg',23,196,'',1,0,54409,93,55),(201,0,0,'动漫系列合集2','动漫系列合集2','/upload_01/ads/20240806/2024080612040592785.jpeg',25,89,'',1,0,42297,95,57),(202,0,0,'伪娘精选合集','伪娘精选合集','/upload_01/ads/20240806/2024080612074799251.jpeg',24,94,'',1,0,49442,98,58),(204,0,0,'帅哥飞机系列2','帅哥飞机系列2','/upload_01/ads/20240807/2024080716222222758.jpeg',19,164,'',1,0,13132,0,0),(205,0,0,'欧美大屌肌肉男合集','欧美大屌肌肉男合集','/upload_01/ads/20240807/2024080716255638784.jpeg',36,81,'',1,0,12002,121,72),(206,0,0,'精品SM系列合集2','精品SM系列','/upload_01/ads/20240807/2024080716324847913.jpeg',19,187,'',1,0,58038,89,53),(207,0,0,'表哥合集','表哥合集','/upload_01/ads/20240807/2024080716375834959.jpeg',35,142,'',1,0,57104,142,85),(208,0,0,'光头金主的快乐生活','光头金主的快乐生活','/upload_01/ads/20240807/2024080716410074459.jpeg',18,112,'',1,0,18298,70,42),(209,0,0,'19cm巨根合集','19cm巨根做爱系列，满足你得性幻想','/upload_01/ads/20240808/2024080811200128534.jpeg',44,268,'',1,0,37053,175,105),(210,0,0,'群P合集第二弹','群P乱交系列，看看你喜欢哪种','/upload_01/ads/20240808/2024080811265310716.jpeg',33,124,'',1,0,38722,138,82),(211,0,0,'泰国大屌合集','泰国大屌约炮系列','/upload_01/ads/20240808/2024080811333744899.jpeg',49,79,'',1,0,10109,196,117),(212,0,0,'正装奴合集','正装奴上门干炮系列','/upload_01/ads/20240808/2024080811363513758.jpeg',33,200,'',1,0,48705,133,79),(213,0,0,'勾引直男合集','漂亮妖妖勾引直男系列','/upload_01/ads/20240808/2024080811581064374.jpeg',37,110,'',1,0,19518,151,90),(214,0,0,'以按摩之名','以按摩之名','/upload_01/ads/20240808/2024080814551899969.jpeg',20,201,'',1,0,89791,85,51),(215,0,0,'小野猫的少年们','小野猫的少年们','/upload_01/ads/20240808/2024080815002211617.jpeg',39,191,'',1,0,62079,120,72),(216,0,0,'调教系列2','调教系列2','/upload_01/ads/20240808/2024080815131975186.jpeg',16,166,'',1,0,45318,66,39),(217,0,0,'面试男模系列','面试男模系列','/upload_01/ads/20240808/2024080815164148901.jpeg',36,255,'',1,0,71191,146,87),(218,0,0,'情趣调情系列','情趣调情系列','/upload_01/ads/20240808/2024080815221484304.jpeg',20,119,'',1,0,17018,81,48),(219,0,0,'纹身大屌合集','纹身大吊操骚逼系列','/upload_01/ads/20240809/2024080912305757529.jpeg',55,112,'',1,0,92191,227,136),(220,0,0,'口爆合集','口交口到激动射了一嘴','/upload_01/ads/20240809/2024080912262795868.jpeg',41,220,'',1,0,60412,165,99),(221,0,0,'黑白配合集','黑人驴吊无套嫩白帅哥系列','/upload_01/ads/20240809/2024080912202226257.jpeg',57,61,'',1,0,24593,216,129),(222,0,0,'按摩师合集第二弹','按摩师的诱惑','/upload_01/ads/20240809/2024080912161083097.jpeg',51,198,'',1,0,77091,211,126),(223,0,0,'网黄周也合集','骚零周也挨操系列','/upload_01/ads/20240809/2024080912082811434.jpeg',39,317,'',1,0,83155,156,93),(224,0,0,'日本男优五十岚系列2','日本男优五十岚系列2','/upload_01/ads/20240809/2024080912162344332.jpeg',20,85,'',1,0,15271,67,40),(225,0,0,'眼镜纹身痞帅男合集','眼镜纹身痞帅男合集','/upload_01/ads/20240809/2024080912202146111.jpeg',6,104,'',1,0,5175,17,10),(226,0,0,'黎铭勾引直男系列','黎铭勾引直男系列','/upload_01/ads/20240809/2024080912232570333.jpeg',17,221,'',1,0,31837,59,35),(227,0,0,'宇哥合集','宇哥合集','/upload_01/ads/20240809/2024080912271557820.png',19,141,'',1,0,24397,48,28),(228,0,0,'国产正装系列','国产正装系列','/upload_01/ads/20240809/2024080912293846646.jpeg',20,129,'',1,0,6233,75,45),(229,0,0,'唐荣宏合集','网黄唐荣宏做爱系列','/upload_01/ads/20240810/2024081011482278304.jpeg',16,218,'',1,0,36627,64,38),(230,0,0,'欧亚大战合集','欧洲和亚洲帅哥激情做爱系列','/upload_01/ads/20240810/2024081011363491808.jpeg',24,86,'',1,0,6947,83,49),(231,0,0,'公厕骚奴合集','公厕户外激情做爱系列','/upload_01/ads/20240810/2024081011395251594.jpeg',30,124,'',1,0,39942,119,71),(232,0,0,'神崎凉合集','男优神崎凉激情做爱系列','/upload_01/ads/20240810/2024081011554773749.jpeg',39,115,'',1,0,27766,148,88),(233,0,0,'朴智宇合集','网黄朴智宇勾引系列','/upload_01/ads/20240810/2024081011443234275.png',11,86,'',1,0,7211,44,26),(234,0,0,'暴力深喉系列','暴力深喉系列','/upload_01/ads/20240810/2024081011592668975.jpeg',8,157,'',1,0,20356,30,18),(235,0,0,'帅哥撸管合集','帅哥撸管','/upload_01/ads/20240810/2024081012011740450.jpeg',13,172,'',1,0,12157,45,27),(236,0,0,'犬奴养成记','犬奴养成记','/upload_01/ads/20240810/2024081012050593340.jpeg',19,126,'',1,0,11740,71,42),(237,0,0,'网红男优精品合集','网红男优视频精品合集','/upload_01/ads/20240810/2024081012092053343.jpeg',22,71,'',1,0,6341,82,49),(238,0,0,'各种鲜肉帅哥合集','各种鲜肉帅哥合集','/upload_01/ads/20240810/2024081012132298137.jpeg',37,143,'',1,0,59476,147,88),(239,0,0,'保安合集第二弹','勾引制服保安野外激情打桩','/upload_01/ads/20240812/2024081212032210034.jpeg',46,160,'',1,0,54900,159,95),(240,0,0,'高校生合集','高校生做爱系列','/upload_01/ads/20240812/2024081212090271221.png',62,131,'',1,0,29828,216,129),(241,0,0,'补习老师合集','补课时和老师的激情系列','/upload_01/ads/20240812/2024081212123686896.jpeg',40,140,'',1,0,44108,137,82),(242,0,0,'直男恰饭合集','直男恰饭系列','/upload_01/ads/20240812/2024081212180679633.jpeg',36,444,'',1,0,115975,142,85),(244,0,0,'勾引直男室友合集','勾引直男室友打炮做爱，兔子也吃窝边草','/upload_01/ads/20240812/2024081212251452791.jpeg',40,145,'',1,0,40029,125,75),(245,0,0,'伪娘精选合集2','伪娘精选合集','/upload_01/ads/20240812/2024081214503192159.jpeg',17,72,'',1,0,49942,69,41),(246,0,0,'大乱交狂欢盛宴','大乱交狂欢盛宴','/upload_01/ads/20240812/2024081214533590352.jpeg',39,166,'',1,0,10883,134,80),(247,0,0,'阿雅约艹记','阿雅约艹记','/upload_01/ads/20240812/2024081214570326512.jpeg',11,35,'',1,0,17388,43,25),(248,0,0,'日本小鲜肉帅哥系列2','日本小鲜肉帅哥系列','/upload_01/ads/20240812/2024081215005527990.jpeg',20,68,'',1,0,4529,55,33),(249,0,0,'直男体育生合集','直男体育生合集','/upload_01/ads/20240812/2024081215242577683.jpeg',22,183,'',1,0,38650,91,54),(250,0,0,'纹身骚狗合集','纹身骚狗激情做爱系列','/upload_01/ads/20240813/2024081311405912094.jpeg',24,66,'',1,0,10471,90,54),(251,0,0,'腹肌帅哥合集','八块腹肌帅哥激情啪啪啪系列','/upload_01/ads/20240813/2024081311440230670.jpeg',40,91,'',1,0,13302,162,97),(252,0,0,'叠罗汉合集','在床上激情叠罗汉系列','/upload_01/ads/20240813/2024081311461838783.jpeg',43,84,'',1,0,21170,152,91),(253,0,0,'男护士合集','住院被大屌护士鸡奸 系列','/upload_01/ads/20240813/2024081311485697441.jpeg',37,104,'',1,0,20926,128,76),(254,0,0,'失禁合集','极品肉便器干到失禁系列','/upload_01/ads/20240813/2024081311510852561.jpeg',33,216,'',1,0,73822,141,84),(255,0,0,'调教玩操各类西装制服帅哥','调教，玩操各类西装制服帅哥','/upload_01/ads/20240813/2024081312431812269.jpeg',26,122,'',1,0,42943,108,64),(256,0,0,'腹肌体育生帅哥系列2','腹肌体育生帅哥系列','/upload_01/ads/20240813/2024081312462295690.jpeg',17,152,'',1,0,25209,72,43),(257,0,0,'乱伦系列2','伦理----乱伦系列','/upload_01/ads/20240813/2024081312520644973.jpeg',22,164,'',1,0,43676,89,53),(258,0,0,'大屌凯文系列合集','凯文系列','/upload_01/ads/20240813/2024081312561937154.jpeg',19,901,'',1,1723524945,68886,79,47),(259,0,0,'各种捆绑系列','各种捆绑','/upload_01/ads/20240813/2024081314482699765.jpeg',16,876,'',1,1723531659,112539,66,39),(260,0,0,'医生合集第二弹','医生引诱系列第二弹','/upload_01/ads/20240814/2024081412034851588.jpeg',41,99,'',1,0,29214,165,99),(261,0,0,'制服合集第二弹','制服做爱系列第二弹','/upload_01/ads/20240814/2024081412071145921.jpeg',37,119,'',1,0,17270,140,84),(262,0,0,'痴汉合集','被痴汉猥亵系列','/upload_01/ads/20240814/2024081412092877083.jpeg',39,131,'',1,0,27344,128,76),(263,0,0,'中年熟男合集','中年熟男成熟的气质，是你喜欢的类型吗','/upload_01/ads/20240814/2024081412123911257.jpeg',41,130,'',1,0,29972,168,100),(264,0,0,'游泳教练合集','游泳教练捡肥皂吃大屌做爱','/upload_01/ads/20240814/2024081412143257818.jpeg',48,69,'',1,0,11258,147,88),(265,0,0,'小爱姐姐的直男们','小爱姐姐的直男们','/upload_01/ads/20240814/2024081412265986696.jpeg',9,83,'',1,0,14449,27,16),(266,0,0,'山东小飞合集','山东小飞合集','/upload_01/ads/20240814/2024081412302334731.jpeg',34,77,'',1,0,17593,121,72),(267,0,0,'骚货养成日记','骚货养成日记','/upload_01/ads/20240814/2024081412350966072.jpeg',16,116,'',1,0,12151,56,33),(268,0,0,'情色按摩服务合集','情色按摩服务合集','/upload_01/ads/20240814/2024081412425160836.jpeg',18,109,'',1,0,18215,69,41),(269,0,0,'多人运动系列','多人运动系列','/upload_01/ads/20240814/2024081412464761585.jpeg',20,95,'',1,0,10225,77,46),(270,0,0,'抖音网红系列2','抖音网红系列2','/upload_01/ads/20240815/2024081518230876562.jpeg',19,163,'',1,0,34879,78,46),(271,0,0,'角色扮演剧情系列','角色扮演剧情系列','/upload_01/ads/20240815/2024081519574690549.jpeg',17,66,'',1,0,8431,59,35),(272,0,0,'欧美腹肌大屌帅哥','欧美腹肌大屌帅哥','/upload_01/ads/20240815/2024081519592162266.jpeg',20,84,'',1,0,5027,70,42),(273,0,0,'直男鲜肉系列','直男鲜肉系列','/upload_01/ads/20240815/2024081520035871001.jpeg',16,142,'',1,0,9989,62,37),(274,0,0,'大胸腹肌系列','大胸腹肌系列','/upload_01/ads/20240815/2024081520083018712.jpeg',21,147,'',1,0,38018,90,54),(275,0,0,'城市猎人合集','城市猎人激情做爱系列','/upload_01/ads/20240816/2024081612252143009.jpeg',23,197,'',1,0,13860,68,40),(277,0,0,'阳光弟弟合集','阳光弟弟激情做爱系列','/upload_01/ads/20240816/2024081612412954280.jpeg',23,103,'',1,0,14558,92,55),(278,0,0,'裁缝师合集','裁缝帅哥利用职业之便激情做爱','/upload_01/ads/20240816/2024081612430872689.jpeg',19,48,'',1,0,6095,82,49),(279,0,0,'电梯做爱合集','在电梯里 封闭的空间更适合做爱哦','/upload_01/ads/20240816/2024081612451129459.jpeg',30,141,'',1,0,17044,100,60),(280,0,0,'探花合集','国民探花系列','/upload_01/ads/20240816/2024081612374017332.jpeg',35,124,'',1,0,9464,126,75),(281,0,0,'男模帅哥系列合集','男模帅哥系列合集','/upload_01/ads/20240816/2024081617311091967.jpeg',18,90,'',1,0,22189,72,43),(282,0,0,'帅气美男子凯文系列【2】','帅气美男子凯文系列【2】','/upload_01/ads/20240816/2024081617363538496.jpeg',18,120,'',1,0,14124,71,42),(283,0,0,'经典拳交集合','经典拳交集合','/upload_01/ads/20240816/2024081620560923369.jpeg',17,146,'',1,0,55578,0,0),(284,0,0,'鸟洞系列合集','鸟洞系列合集','/upload_01/ads/20240816/2024081621243899608.jpeg',21,200,'',1,0,97957,84,50),(285,0,0,'调教直男系列2','调教直男系列2','/upload_01/ads/20240816/2024081621281929518.jpeg',22,121,'',1,0,18587,81,48),(286,0,0,'公厕调教合集第二弹','公厕调教系列第二弹来啦','/upload_01/ads/20240817/2024081712052657290.jpeg',43,108,'',1,0,61038,168,100),(287,0,0,'纹身薄肌合集','纹身薄肌帅哥做爱系列','/upload_01/ads/20240817/2024081712064292650.jpeg',23,55,'',1,0,6268,87,52),(288,0,0,'学弟约学长合集','学弟约学长做爱系列','/upload_01/ads/20240817/2024081712093221695.jpeg',38,93,'',1,0,48850,161,96),(289,0,0,'体育生弟弟合集','体育生帅弟弟激情做爱系列','/upload_01/ads/20240817/2024081712144950148.jpeg',39,193,'',1,0,27104,150,90),(290,0,0,'眼镜帅哥合集','斯文眼睛帅哥，看着斯文，做爱就像打桩机狂野的很','/upload_01/ads/20240817/2024081712142065148.jpeg',28,76,'',1,0,12211,108,64),(291,0,0,'各种圣水调教系列合集','各种圣水调教系列合集','/upload_01/ads/20240817/2024081715461598902.jpeg',31,168,'',1,0,48196,124,74),(292,0,0,'变态鞭打调教系列合集','鞭打调教系列合集','/upload_01/ads/20240817/2024081715533242831.jpeg',12,81,'',1,0,13288,43,25),(293,0,0,'酒店约炮系列合集2','酒店约炮系列合集','/upload_01/ads/20240817/2024081715573399717.jpeg',25,51,'',1,0,24917,101,60),(294,0,0,'大肌霸系列','大肌霸系列','/upload_01/ads/20240817/2024081716010926068.jpeg',21,81,'',1,0,25087,87,52),(295,0,0,'台湾网黄帅哥系列','台湾网黄帅哥系列','/upload_01/ads/20240817/2024081716062652068.jpeg',25,77,'',1,0,22995,101,60),(296,0,0,'腹肌小奶狗合集','调教腹肌小奶狗系列','/upload_01/ads/20240819/2024081912021710129.png',36,71,'',1,0,12107,117,70),(297,0,0,'性感大叔合集','性感大叔激情做爱系列','/upload_01/ads/20240819/2024081912035162324.jpeg',22,92,'',1,0,10830,90,54),(298,0,0,'臭脚爸爸合集','臭脚爸爸激情做爱系列','/upload_01/ads/20240819/2024081912062849005.jpeg',35,260,'',1,0,60515,152,91),(299,0,0,'牛老师生殖课堂合集','牛老师生殖课堂，青春期趣事分享系列','/upload_01/ads/20240819/2024081912073891983.jpeg',26,478,'',1,0,89779,104,62),(301,0,0,'主播草粉合集','优质主播草粉系列','/upload_01/ads/20240819/2024081912102970615.jpeg',32,80,'',1,0,21637,119,71),(302,0,0,'黑人巨屌系列','黑人巨屌系列','/upload_01/ads/20240819/2024081915420717322.jpeg',19,107,'',1,0,15979,80,48),(303,0,0,'欧美腹肌帅哥小吉诺','欧美腹肌帅哥小吉诺','/upload_01/ads/20240819/2024081915451431819.jpeg',15,61,'',1,0,1882,58,34),(304,0,0,'鲜肉集中营','鲜肉集中营','/upload_01/ads/20240819/2024081915475052887.jpeg',20,107,'',1,0,78178,87,52),(305,0,0,'男优宏翔视频合集','男优宏翔视频合集','/upload_01/ads/20240819/2024081915511659193.jpeg',17,205,'',1,0,52688,68,40),(306,0,0,'酒醉玩弄直男系列','酒醉玩弄直男系列','/upload_01/ads/20240819/2024081915573882963.jpeg',22,218,'',1,0,33335,88,52),(307,0,0,'勾引客人合集','勾引客人操逼系列','/upload_01/ads/20240820/2024082012040939179.jpeg',39,68,'',1,0,18000,158,94),(308,0,0,'体校合集','体校内的激情做爱系列','/upload_01/ads/20240820/2024082012070583789.jpeg',27,340,'',1,0,49180,104,62),(309,0,0,'导演诱玩合集','导演亲自示范，私下诱玩系列','/upload_01/ads/20240820/2024082012083523779.jpeg',34,81,'',1,0,21689,124,74),(310,0,0,'退役兵合集','退役兵激情做爱系列','/upload_01/ads/20240820/2024082012102577926.jpeg',32,189,'',1,0,39008,114,68),(312,0,0,'品屌合集','品尝各式大屌系列','/upload_01/ads/20240820/2024082012203466192.jpeg',31,83,'',1,0,35326,127,76),(313,0,0,'极品性瘾弟弟合集','极品性瘾弟弟合集','/upload_01/ads/20240820/2024082015015653578.jpeg',19,86,'',1,0,8387,68,40),(314,0,0,'控射玩弄鲜嫩大肉JB','控射玩弄鲜嫩大肉JB','/upload_01/ads/20240820/2024082015064919132.jpeg',21,82,'',1,0,30745,89,53),(315,0,0,'呻吟超骚肌肉男系列','呻吟超骚肌肉男系列','/upload_01/ads/20240820/2024082015103863874.jpeg',22,131,'',1,0,17710,75,45),(316,0,0,'密室游戏屋激情系列','密室游戏屋激情系列','/upload_01/ads/20240820/2024082015132879031.jpeg',33,63,'',1,0,21992,131,78),(317,0,0,'东北帅哥系列合集','东北帅哥系列合集','/upload_01/ads/20240820/2024082015171377963.jpeg',28,209,'',1,0,79708,116,69),(318,0,0,'制服帅哥合集','制服帅哥做爱系列','/upload_01/ads/20240821/2024082111325639881.jpeg',26,100,'',1,0,10539,107,64),(319,0,0,'男模帅哥合集第二弹','男模帅哥激情做爱','/upload_01/ads/20240821/2024082111395033210.jpeg',32,99,'',1,0,30135,132,79),(320,0,0,'东北冰少合集','网黄东北冰少做爱系列','/upload_01/ads/20240821/2024082111421875421.jpeg',39,107,'',1,0,41126,133,79),(321,0,0,'金主调教合集第二弹','金主调教小奶狗系列第二弹','/upload_01/ads/20240821/2024082111460574908.jpeg',28,124,'',1,0,66162,106,63),(322,0,0,'户外地铁合集','户外地图激情做爱系列','/upload_01/ads/20240821/2024082111493537306.jpeg',31,90,'',1,0,24627,116,69),(323,0,0,'打屁股系列合集','打屁股系列合集','/upload_01/ads/20240821/2024082112473941951.jpeg',19,105,'',1,0,18127,58,34),(324,0,0,'19厘米大肌霸克里斯系列','19厘米大肌霸克里斯系列','/upload_01/ads/20240821/2024082112530833530.jpeg',19,97,'',1,0,12250,68,40),(325,0,0,'言嘉佑合集','言嘉佑合集','/upload_01/ads/20240821/2024082114430132414.jpeg',16,196,'',1,0,37375,67,40),(326,0,0,'3P群交系列','3P群交系列','/upload_01/ads/20240821/2024082114575974980.jpeg',19,91,'',1,0,4995,63,37),(327,0,0,'飞机系列3','飞机系列3','/upload_01/ads/20240821/2024082115031317944.jpeg',25,116,'',1,0,58243,104,62),(328,0,0,'腹肌奶爸合集','腹肌奶爸激情做爱','/upload_01/ads/20240822/2024082212000410566.jpeg',17,63,'',1,0,7948,68,40),(329,0,0,'网红体育生teetw合集','网红体育生teetw激情做爱系列','/upload_01/ads/20240822/2024082212012158783.jpeg',21,119,'',1,0,17662,63,37),(330,0,0,'勾引表弟合集','表哥表弟  近亲相奸','/upload_01/ads/20240822/2024082212025093217.jpeg',24,78,'',1,0,11788,82,49),(331,0,0,'发烧被奸合集','生病发烧还被操','/upload_01/ads/20240822/2024082212040414395.jpeg',15,98,'',1,0,23675,58,34),(332,0,0,'扳弯直男合集','掰弯直男的情色之旅','/upload_01/ads/20240822/2024082212062948128.jpeg',36,244,'',1,0,50664,113,67),(333,0,0,'勾引直男帅哥合集','勾引直男帅哥合集','/upload_01/ads/20240822/2024082214493439098.png',19,108,'',1,0,19564,68,40),(334,0,0,'体育生过瘾粗口合集','体育生过瘾粗口合集','/upload_01/ads/20240822/2024082214525881643.jpeg',20,297,'',1,0,34777,61,36),(335,0,0,'筋壮多汁鲜肉喷射合集','筋壮多汁鲜肉喷射合集','/upload_01/ads/20240822/2024082214563069644.jpeg',19,99,'',1,0,59955,77,46),(336,0,0,'猛男系列合集','猛男系列合集','/upload_01/ads/20240822/2024082215015339822.jpeg',19,58,'',1,0,24197,84,50),(337,0,0,'绿帽男系列合集','绿帽男系列合集','/upload_01/ads/20240822/2024082215075061900.jpeg',33,139,'',1,0,77672,131,78),(338,0,0,'调教狗奴合集','调教骚狗奴，做爱系列','/upload_01/ads/20240823/2024082311545541970.jpeg',26,199,'',1,0,33227,105,63),(339,0,0,'星星队长合集第二弹','星星队长激情做爱系列第二弹来啦','/upload_01/ads/20240823/2024082312003755738.jpeg',26,143,'',1,0,19084,86,51),(340,0,0,'发小变炮友合集','青梅竹马的发小变恋人','/upload_01/ads/20240823/2024082312141953464.png',15,95,'',1,0,11134,46,27),(341,0,0,'监禁合集','监禁调教帅哥系列','/upload_01/ads/20240823/2024082312153589645.jpeg',52,127,'',1,0,20309,199,119),(342,0,0,'直男沦陷合集','真心换真情  直男都得沦陷','/upload_01/ads/20240823/2024082312182319682.png',39,144,'',1,0,29017,119,71),(343,0,0,'01互换反攻互攻系列','01互换反攻互攻系列','/upload_01/ads/20240823/2024082315242054985.jpeg',19,182,'',1,0,54992,81,48),(344,0,0,'天菜学生情侣开房做爱合集','天菜学生情侣开房做爱合集','/upload_01/ads/20240823/2024082315270762239.jpeg',20,95,'',1,0,12228,66,39),(345,0,0,'圣诞合集2','圣诞合集2','/upload_01/ads/20240823/2024082315325511121.jpeg',17,32,'',1,0,6852,69,41),(346,0,0,'狩猎直男合集','狩猎直男合集','/upload_01/ads/20240823/2024082315384890302.jpeg',22,114,'',1,0,25592,80,48),(347,0,0,'鲜肉奶狗合集','鲜肉奶狗合集','/upload_01/ads/20240823/2024082315521996107.jpeg',20,80,'',1,0,29874,87,52),(348,0,0,'正装狗合集','正装狗奴激情做爱系列','/upload_01/ads/20240824/2024082412130997106.jpeg',24,141,'',1,0,37543,97,58),(349,0,0,'东北冰少合集第二弹','网黄东北冰少做爱系列第二弹来啦','/upload_01/ads/20240824/2024082412160861466.jpeg',17,100,'',1,0,21296,60,36),(350,0,0,'工程师合集第二弹','帅气工程师激情做爱系列第二弹','/upload_01/ads/20240824/2024082412191728983.jpeg',52,87,'',1,0,18110,203,121),(351,0,0,'网黄体育生欧阳合集','抖音黑皮体育生欧阳MB激情做爱系列','/upload_01/ads/20240824/2024082412245496227.jpeg',20,147,'',1,0,20721,72,43),(352,0,0,'男神收割机合集','男神收割机系列，精彩不容错过','/upload_01/ads/20240824/2024082412284489857.jpeg',30,81,'',1,0,16672,88,52),(353,0,0,'日本精品gv 2','日本精品gv 2','/upload_01/ads/20240824/2024082415200958536.jpeg',20,137,'',1,0,50127,128,76),(354,0,0,'公厕故事','公厕故事','/upload_01/ads/20240824/2024082415261352246.jpeg',30,184,'',1,0,35351,104,62),(355,0,0,'网红mb超帅鲜肉啪啪合集','网红mb超帅鲜肉啪啪','/upload_01/ads/20240824/2024082415331124044.jpeg',14,90,'',1,0,13268,55,33),(356,0,0,'贱狗调教系列','贱狗调教系列','/upload_01/ads/20240824/2024082415364033493.jpeg',29,137,'',1,0,38603,120,72),(357,0,0,'熊熊/肉壮系列合集','熊熊/肉壮系列合集','/upload_01/ads/20240824/2024082415410773790.jpeg',32,139,'',1,0,41764,121,72),(358,0,0,'丁字裤小零合集','穿性感丁字裤的小零挨操系列','/upload_01/ads/20240826/2024082612233824444.jpeg',34,52,'',1,0,6631,117,70),(359,0,0,'消防队合集','消防队员的激情系列','/upload_01/ads/20240826/2024082612254898302.jpeg',35,293,'',1,0,42437,110,66),(360,0,0,'网黄张泽合集','网黄张泽帅气露脸做爱系列','/upload_01/ads/20240826/2024082612282950068.jpeg',29,163,'',1,0,48866,118,70),(361,0,0,'飞机杯盲测合集','飞机杯盲测，工欲善其事必先利其器','/upload_01/ads/20240826/2024082612303128842.jpeg',33,108,'',1,0,19074,124,74),(362,0,0,'纹身体育生合集第二弹','纹身体育生系列第二弹来啦','/upload_01/ads/20240826/2024082612320952144.jpeg',12,74,'',1,0,4921,49,29),(363,0,0,'电动马达系列合集','电动马达系列合集','/upload_01/ads/20240826/2024082614563127874.jpeg',20,92,'',1,0,7411,64,38),(364,0,0,'农民工激情系列合集','农民工激情系列合集','/upload_01/ads/20240826/2024082615025988859.jpeg',33,184,'',1,0,60302,113,67),(365,0,0,'兵哥哥合集2','兵哥哥合集2','/upload_01/ads/20240826/2024082615104764431.jpeg',22,206,'',1,0,62355,91,54),(366,0,0,'直男专访合集','直男专访合集','/upload_01/ads/20240826/2024082615152799522.jpeg',20,120,'',1,0,18978,78,46),(367,0,0,'师生同窗性交系列','师生同窗性交系列','/upload_01/ads/20240826/2024082615212614269.jpeg',27,88,'',1,0,9051,78,46),(368,0,0,'性瘾小狼合集第三弹','网黄性瘾小狼操逼系列第三弹来啦','/upload_01/ads/20240827/2024082711575622586.jpeg',36,129,'',1,0,32197,150,90),(369,0,0,'肉壮大叔合集','肉壮大叔激情做爱系列 是你喜欢的类型吗','/upload_01/ads/20240827/2024082711594530535.jpeg',30,186,'',1,0,45181,103,61),(370,0,0,'冲浪小哥合集第二弹','网红冲浪小哥做爱系列第二弹来啦','/upload_01/ads/20240827/2024082712021022238.jpeg',25,96,'',1,0,13426,100,60),(371,0,0,'职场性爱合集','职场社畜  性爱系列','/upload_01/ads/20240827/2024082712042284282.jpeg',30,79,'',1,0,25390,113,67),(372,0,0,'宿舍室友做爱合集','在宿舍同吃同睡的室友，何尝不是日久见真情呢','/upload_01/ads/20240827/2024082712063922946.jpeg',29,160,'',1,0,73801,119,71),(373,0,0,'SM调教系列2','SM调教系列2','/upload_01/ads/20240827/2024082715525282031.jpeg',16,153,'',1,0,51112,65,39),(374,0,0,'男优激情系列','男优激情系列','/upload_01/ads/20240827/2024082716100356001.jpeg',19,34,'',1,0,16591,76,45),(375,0,0,'自慰飞机系列合集','自慰飞机系列','/upload_01/ads/20240827/2024082716500512863.jpeg',17,139,'',1,0,57536,80,48),(376,0,0,'五十岚系列2','五十岚系列2','/upload_01/ads/20240827/2024082716431437391.jpeg',38,110,'',1,0,14814,129,77),(377,0,0,'抖音网红系列','抖音网红系列','/upload_01/ads/20240827/2024082717151750957.jpeg',15,164,'',1,0,59909,64,38),(378,0,0,'已婚男合集','已婚男在外偷约系列','/upload_01/ads/20240828/2024082811541922226.jpeg',33,171,'',1,0,26978,95,57),(380,0,0,'骚儿子合集','猛插骚儿子系列','/upload_01/ads/20240828/2024082811595074555.jpeg',30,194,'',1,0,28643,94,56),(381,0,0,'网帅八哥合集第二弹','网帅八哥激情做爱第二弹','/upload_01/ads/20240828/2024082812013690286.jpeg',25,165,'',1,0,34642,105,63),(382,0,0,'兵哥合集第三弹','兵哥约炮第三弹来啦！','/upload_01/ads/20240828/2024082812045362409.jpeg',24,210,'',1,0,107332,105,63),(383,0,0,'直播玩鸟裸聊合集','直播玩鸟裸聊 骚话不断','/upload_01/ads/20240828/2024082812085029317.jpeg',30,69,'',1,0,67118,128,76),(384,0,0,'正装系列合集','正装系列合集','/upload_01/ads/20240828/2024082815483717574.png',29,191,'',1,0,123552,125,75),(385,0,0,'剧情合集','剧情合集','/upload_01/ads/20240828/2024082816003654741.jpeg',22,163,'',1,0,90961,93,55),(386,0,0,'群P盛宴合集','群P盛宴合集','/upload_01/ads/20240828/2024082817011761514.jpeg',24,149,'',1,0,19707,99,59),(387,0,0,'欧美帅哥系列合集','欧美帅哥系列合集','/upload_01/ads/20240828/2024082817040752636.jpeg',18,80,'',1,0,28484,72,43),(388,0,0,'最新光头金主合集','最新光头金主合集','/upload_01/ads/20240828/2024082817070235140.jpeg',19,122,'',1,0,19389,70,42),(389,0,0,'驴屌男神合集','驴屌男神的插入 就问你受得了嘛','/upload_01/ads/20240829/2024082911573696176.jpeg',30,219,'',1,0,19521,93,55),(390,0,0,'肉壮小熊合集','肉壮小熊发情骚动','/upload_01/ads/20240829/2024082911590880674.jpeg',18,87,'',1,0,15625,74,44),(391,0,0,'女记者专访合集第二弹','女记者采访，激情裸聊第二弹','/upload_01/ads/20240829/2024082912033479732.jpeg',33,136,'',1,0,40659,154,92),(392,0,0,'真实偷拍合集','真实偷拍系列，主打一个刺激','/upload_01/ads/20240829/2024082912065197434.jpeg',17,253,'',1,0,87012,69,41),(393,0,0,'邻居大叔合集','与邻居大叔激情做爱系列','/upload_01/ads/20240829/2024082912091714531.jpeg',30,179,'',1,0,24572,111,66),(394,0,0,'体育生合集2','体育生合集2','/upload_01/ads/20240829/2024082915150332768.jpeg',19,229,'',1,0,115652,82,49),(395,0,0,'足交踩射系列','足交踩射系列','/upload_01/ads/20240829/2024082915185615732.jpeg',17,103,'',1,0,20631,57,34),(396,0,0,'学校宿舍系列合集','学校宿舍系列合集','/upload_01/ads/20240829/2024082915225364630.png',18,366,'',1,0,39633,61,36),(397,0,0,'性奴养成记','性奴养成记','/upload_01/ads/20240829/2024082915265618340.jpeg',16,148,'',1,0,25519,65,39),(398,0,0,'以按摩之名3','以按摩之名3','/upload_01/ads/20240829/2024082915320155168.jpeg',18,151,'',1,0,60411,68,40),(399,0,0,'老攻合集','老攻与骚受的爱恨情仇','/upload_01/ads/20240830/2024083011555349026.jpeg',39,135,'',1,0,24504,139,83),(400,0,0,'优质校草合集第二弹','优质校草激情做爱系列第二弹','/upload_01/ads/20240830/2024083012010083137.jpeg',21,167,'',1,0,46538,89,53),(401,0,0,'健壮爷们合集','健壮爷们激情打桩系列','/upload_01/ads/20240830/2024083012003115581.jpeg',33,127,'',1,0,17420,119,71),(402,0,0,'快递员合集第二弹','快递小哥勾引系列','/upload_01/ads/20240830/2024083012040134126.jpeg',37,163,'',1,0,39275,145,87),(403,0,0,'性感男模合集','帅气的性感男模满足你对另一半完美的形象','/upload_01/ads/20240830/2024083012054286064.jpeg',24,80,'',1,0,14398,96,57),(404,0,0,'帅哥飞机系列3','帅哥飞机系列','/upload_01/ads/20240830/2024083016113017944.jpeg',16,145,'',1,0,16245,55,33),(405,0,0,'KTV激情系列','KTV激情系列','/upload_01/ads/20240830/2024083016173867386.png',26,168,'',1,0,56240,87,52),(406,0,0,'金钱诱惑系列2','金钱诱惑系列2','/upload_01/ads/20240830/2024083016453459026.jpeg',28,117,'',1,0,32034,113,67),(408,0,0,'高中生合集2','高中生合集2','/upload_01/ads/20240830/2024083016522795453.jpeg',15,245,'',1,0,40813,62,37),(409,0,0,'偷吃系列合集','偷吃系列合集','/upload_01/ads/20240830/2024083016571965451.jpeg',21,137,'',1,0,69959,78,46),(410,0,0,'鲜肉学弟合集','鲜肉学弟激情做爱系列','/upload_01/ads/20240831/2024083111543926912.jpeg',23,98,'',1,0,15773,94,56),(411,0,0,'金主钞能力合集','金主钞能力调教小骚货系列','/upload_01/ads/20240831/2024083111571640881.jpeg',27,256,'',1,0,73784,80,48),(412,0,0,'正装直男合集第二弹','调教正装直男系列第二弹来啦','/upload_01/ads/20240831/2024083111591683751.png',30,102,'',1,0,26223,107,64),(413,0,0,'舞蹈生合集','肢体柔软的艺校舞蹈生，极品炮架子','/upload_01/ads/20240831/2024083112013685136.png',29,212,'',1,0,23178,90,54),(414,0,0,'山东浩浩系列第二弹','网黄好好激情做爱第二弹来啦 不可错过哦','/upload_01/ads/20240831/2024083112053113172.png',29,191,'',1,0,25280,106,63),(415,0,0,'车震系列合集2','车震系列合集2','/upload_01/ads/20240831/2024083115273862716.jpeg',18,121,'',1,0,23786,66,39),(416,0,0,'偷情系列合集','偷情系列合集','/upload_01/ads/20240831/2024083115345439504.jpeg',18,130,'',1,0,41864,75,45),(417,0,0,'精品剧情合集','精品剧情合集','/upload_01/ads/20240831/2024083115385437938.jpeg',37,128,'',1,0,31433,150,90),(418,0,0,'大屌男合集','大屌男合集','/upload_01/ads/20240831/2024083115482375728.jpeg',21,142,'',1,0,57862,89,53),(419,0,0,'大学生合集','大学生合集','/upload_01/ads/20240831/2024083115530479091.jpeg',21,193,'',1,0,53620,97,58),(420,0,0,'体育生合集第三弹','体育生系列第三弹来了','/upload_01/ads/20240902/2024090212301815404.jpeg',20,275,'',1,0,189900,91,54),(421,0,0,'偷吃合集第二弹','在家偷吃系列，惊险刺激','/upload_01/ads/20240902/2024090212333821348.jpeg',28,131,'',1,0,14467,108,64),(422,0,0,'金主钞能力合集第二弹','金主钞能力系列第二弹','/upload_01/ads/20240902/2024090212392373785.jpeg',34,256,'',1,0,72287,143,85),(423,0,0,'职场性爱合集第二弹','职场性爱系列第二弹','/upload_01/ads/20240902/2024090212423661731.jpeg',30,103,'',1,0,12362,100,60),(424,0,0,'兄弟乱伦合集','兄弟乱伦系列，兔子不吃窝边草  那是因为不香，香香甜甜的窝边草  谁能抵抗','/upload_01/ads/20240902/2024090212452415468.jpeg',25,259,'',1,0,26292,88,52),(425,0,0,'表哥系列合集','表哥系列合集','/upload_01/ads/20240902/2024090215210225890.jpeg',18,113,'',1,0,26363,75,45),(426,0,0,'欧美剧情系列','欧美剧情系列','/upload_01/ads/20240902/2024090215245032455.jpeg',20,168,'',1,0,9926,67,40),(427,0,0,'兵哥哥合集3','兵哥哥合集3','/upload_01/ads/20240902/2024090215494345132.jpeg',33,242,'',1,0,67657,116,69),(428,0,0,'鲜肉集中营2','鲜肉集中营2','/upload_01/ads/20240902/2024090216314859028.jpeg',16,119,'',1,0,69594,63,37),(429,0,0,'抖音帅哥系列合集','抖音帅哥系列合集','/upload_01/ads/20240902/2024090216430098737.jpeg',23,226,'',1,0,61391,98,58),(430,0,0,'薄肌小奶狗合集','薄肌小奶狗激情做爱系列','/upload_01/ads/20240903/2024090311320167816.jpeg',25,166,'',1,0,13422,90,54),(431,0,0,'眼镜帅哥合集第二弹','儒雅斯文的眼镜帅哥是你的菜吗','/upload_01/ads/20240903/2024090311345968905.jpeg',27,62,'',1,0,6816,107,64),(432,0,0,'Seankk系列合集','Seankk激情做爱系列','/upload_01/ads/20240903/2024090311380485546.jpeg',20,523,'',1,0,59209,94,56),(433,0,0,'伪娘珍爱姐合集','伪娘珍爱姐勾引小鲜肉系列','/upload_01/ads/20240903/2024090311411255716.jpeg',13,131,'',1,0,48094,45,27),(434,0,0,'正装熊合集','与正装熊激情打炮系列','/upload_01/ads/20240903/2024090311434389954.jpeg',17,137,'',1,0,33958,68,40),(435,0,0,'小蓝精品合集','小蓝精选合集','/upload_01/ads/20240903/2024090315040319308.jpeg',12,161,'',1,0,127713,105,63),(436,0,0,'高中生系列合集','高中生系列合集','/upload_01/ads/20240903/2024090315184876994.png',42,472,'',1,0,68865,138,82),(437,0,0,'男优精品合集','男优精品合集','/upload_01/ads/20240903/2024090315554632614.jpeg',29,162,'',1,0,14074,116,69),(438,0,0,'东北大肌霸合集','东北大肌霸合集','/upload_01/ads/20240903/2024090316034799936.jpeg',25,243,'',1,0,43239,107,64),(439,0,0,'日本精品GV合集','经典日本GV','/upload_01/ads/20240903/2024090316142458328.jpeg',20,164,'',1,0,48759,124,74),(440,0,0,'已婚直男合集','调教打桩已婚直男','/upload_01/ads/20240904/2024090411510966528.jpeg',23,152,'',1,0,43550,85,51),(441,0,0,'直男猎手合集','直男猎手激情打桩系列','/upload_01/ads/20240904/2024090411571496564.jpeg',29,408,'',1,0,75182,112,67),(442,0,0,'奶狗学弟合集','约操奶狗学弟系列  激情做爱','/upload_01/ads/20240904/2024090412000196650.jpeg',25,203,'',1,0,20909,89,53),(443,0,0,'性感男模第二弹','性感男模第二弹来啦','/upload_01/ads/20240904/2024090412021796092.jpeg',26,87,'',1,0,7972,97,58),(444,0,0,'网黄川仔合集','网黄川仔激情做爱系列','/upload_01/ads/20240904/2024090412034552870.jpeg',25,223,'',1,0,40220,101,60),(445,0,0,'勾引合集','勾引合集','/upload_01/ads/20240904/2024090416381776332.jpeg',20,192,'',1,0,58109,91,54),(446,0,0,'制服诱惑系列','制服诱惑系列','/upload_01/ads/20240904/2024090417223071777.jpeg',27,466,'',1,1725441654,65718,88,52),(447,0,0,'金主钞能力系列合集','金主系列合集','/upload_01/ads/20240904/2024090417261980262.png',15,198,'',1,0,46348,69,41),(448,0,0,'迷醉系列合集2','迷醉系列合集2','/upload_01/ads/20240904/2024090417344369748.jpeg',22,496,'',1,0,87328,59,35),(449,0,0,'野外放纵系列合集','野外放纵系列合集','/upload_01/ads/20240904/2024090417390774099.jpeg',20,147,'',1,0,21473,82,49),(450,0,0,'翘臀直男合集','翘臀直男合集','/upload_01/ads/20240904/2024090417425341240.jpeg',19,108,'',1,0,14787,69,41),(451,0,0,'调教开苞合集','调教开苞小鲜肉系列','/upload_01/ads/20240905/2024090512162849793.jpeg',16,281,'',1,0,27663,35,21),(452,0,0,'忧伤的凹凸曼合集','网黄忧伤的凹凸曼操逼系列','/upload_01/ads/20240905/2024090512174866353.jpeg',20,62,'',1,0,9506,66,39),(453,0,0,'直男学长合集','勾引直男学长系列','/upload_01/ads/20240905/2024090512190466233.jpeg',19,133,'',1,0,16089,40,24),(454,0,0,'多人无套合集','多人无套男体盛宴系列','/upload_01/ads/20240905/2024090512234183746.jpeg',26,488,'',1,0,161604,101,60),(455,0,0,'舍友打桩合集','与舍友继续做爱打桩系列','/upload_01/ads/20240905/2024090512252535237.png',26,238,'',1,0,47883,77,46),(456,0,0,'男神宏翔合集','男神宏翔合集','/upload_01/ads/20240905/2024090515532511145.jpeg',17,297,'',1,0,29622,72,43),(457,0,0,'台湾网黄系列合集','台湾网黄系列合集','/upload_01/ads/20240905/2024090515582393653.jpeg',33,169,'',1,0,19860,138,82),(458,0,0,'直男下海记','直男下海记','/upload_01/ads/20240905/2024090516101815794.jpeg',20,230,'',1,0,32076,84,50),(459,0,0,'大屌鲜肉合集','大屌鲜肉合集','/upload_01/ads/20240905/2024090516140382207.jpeg',18,111,'',1,0,11911,74,44),(460,0,0,'MB啪啪合集','MB啪啪合集','/upload_01/ads/20240905/2024090516424531946.jpeg',17,206,'',1,0,32823,68,40),(461,0,0,'柳乃堂系列合集','柳乃堂系列合集','/upload_01/ads/20240906/2024090616050727747.jpeg',19,247,'',1,0,21962,63,37),(462,0,0,'时间静止系列合集2','时间静止系列合集2','/upload_01/ads/20240906/2024090616543829526.jpeg',18,336,'',1,0,34190,87,52),(463,0,0,'黎铭系列合集','黎铭系列合集','/upload_01/ads/20240906/2024090616585326897.png',20,351,'',1,0,38257,62,37),(464,0,0,'调教帅哥系列合集','调教帅哥系列合集','/upload_01/ads/20240906/2024090617023398774.jpeg',17,243,'',1,0,62864,76,45),(465,0,0,'应聘男模的帅弟弟','应聘男模的帅弟弟','/upload_01/ads/20240906/2024090617055038911.png',20,212,'',1,0,18031,68,40),(466,0,0,'诱奸正太合集','诱奸正太激情打桩系列','/upload_01/ads/20240907/2024090711510237704.jpeg',30,678,'',1,0,95536,122,73),(467,0,0,'壮熊合集第三弹','壮熊大叔激情做爱系列','/upload_01/ads/20240907/2024090711530463985.jpeg',23,156,'',1,0,35401,84,50),(468,0,0,'网黄航仔合集','网黄航仔激情做爱系列','/upload_01/ads/20240907/2024090711545825119.jpeg',20,196,'',1,0,37865,74,44),(469,0,0,'浴室偷拍合集','浴室偷拍系列  主打一个真实','/upload_01/ads/20240907/2024090711570963473.jpeg',17,349,'',1,0,50691,58,34),(470,0,0,'脂包肌帅哥合集','脂包肌帅哥激情做爱系列','/upload_01/ads/20240907/2024090711591932193.jpeg',21,220,'',1,0,76508,88,52),(471,0,0,'最新兵哥哥合集','最新兵哥哥合集','/upload_01/ads/20240907/2024090715274696938.jpeg',14,212,'',1,0,32146,57,34),(472,0,0,'山东浩浩系列合集2','山东浩浩系列合集2','/upload_01/ads/20240907/2024090715383515109.png',26,205,'',1,0,14058,72,43),(473,0,0,'山东小飞精品原创合集','山东小飞精品原创合集','/upload_01/ads/20240907/2024090715412638799.jpeg',19,148,'',1,0,8753,65,39),(474,0,0,'各种鲜肉合集','各种鲜肉合集','/upload_01/ads/20240907/2024090715450775847.jpeg',18,149,'',1,0,148632,80,48),(475,0,0,'猛男合集','猛男合集','/upload_01/ads/20240907/2024090715491279257.jpeg',26,150,'',1,0,43799,109,65),(476,0,0,'亚洲小伙对战欧洲猛男合集','亚洲小伙对战欧洲猛男，黄白配','/upload_01/ads/20240909/2024090912132870002.jpeg',23,80,'',1,0,11872,95,57),(477,0,0,'黑白配合集第二弹','黑人大战白人第二弹来啦','/upload_01/ads/20240909/2024090912171942322.jpeg',35,85,'',1,0,10114,114,68),(478,0,0,'精液的味道合集','精液的味道，大口吞精系列','/upload_01/ads/20240909/2024090912184756252.jpeg',19,253,'',1,0,34978,71,42),(479,0,0,'浴室激情合集','浴室激情，无套打桩','/upload_01/ads/20240909/2024090912203029954.jpeg',26,92,'',1,0,18457,97,58),(480,0,0,'白皙帅哥合集','白皙帅哥激情做爱系列','/upload_01/ads/20240909/2024090912215088870.jpeg',14,128,'',1,0,12021,50,30),(481,0,0,'淫荡医生系列','淫荡医生系列','/upload_01/ads/20240909/2024090915025239970.jpeg',27,177,'',1,0,28858,108,64),(482,0,0,'捆绑玩射合集','捆绑玩射合集','/upload_01/ads/20240909/2024090915070683282.jpeg',16,230,'',1,0,43335,70,42),(483,0,0,'网红合集','网红合集','/upload_01/ads/20240909/2024090915155160338.jpeg',25,475,'',1,0,127800,112,67),(484,0,0,'冲浪小哥系列合集','冲浪小哥系列合集','/upload_01/ads/20240909/2024090915195220013.jpeg',19,508,'',1,1725866361,48626,78,46),(485,0,0,'粗屌帅哥系列','粗屌帅哥系列','/upload_01/ads/20240909/2024090915231988540.jpeg',26,159,'',1,0,58023,114,68),(486,0,0,'迷系列第二弹','迷系列第二弹来啦','/upload_01/ads/20240910/2024091011514210345.jpeg',25,569,'',1,0,96181,114,68),(487,0,0,'农民工激情系列第二弹','农民兄弟也需要私生活呀','/upload_01/ads/20240910/2024091011540187382.jpeg',25,231,'',1,0,61233,82,49),(488,0,0,'帅哥情侣合集','帅哥情侣激情做爱系列','/upload_01/ads/20240910/2024091011561947501.jpeg',29,174,'',1,0,20910,99,59),(489,0,0,'大叔户外合集','大叔户外激情打桩系列','/upload_01/ads/20240910/2024091011585025729.jpeg',20,157,'',1,0,30528,72,43),(490,0,0,'棒棒糖合集','棒棒糖似的大屌你喜欢吗','/upload_01/ads/20240910/2024091012002570772.png',19,89,'',1,0,9576,76,45),(491,0,0,'篮球队长合集','帅气篮球队长被调教成骚狗','/upload_01/ads/20240911/2024091114474640913.jpeg',20,251,'',1,0,32155,74,44),(493,0,0,'健壮黑鬼合集','健壮黑鬼激情打桩系列','/upload_01/ads/20240911/2024091114530341309.jpeg',20,318,'',1,0,32923,75,45),(494,0,0,'处男小鲜肉合集','处男小鲜肉初体验','/upload_01/ads/20240911/2024091114563415827.jpeg',16,235,'',1,0,32205,64,38),(495,0,0,'男大学生合集第二弹','男大学生也需要性生活啊','/upload_01/ads/20240911/2024091115013618467.png',18,363,'',1,0,56992,84,50),(497,0,0,'鸟洞合集','鸟洞合集','/upload_01/ads/20240911/2024091121281086089.jpeg',33,348,'',1,0,51116,114,68),(498,0,0,'综艺合集','综艺合集','/upload_01/ads/20240911/2024091121304073161.jpeg',34,298,'',1,0,63406,110,66),(499,0,0,'原创合集','原创合集','/upload_01/ads/20240911/2024091121324720794.png',24,283,'',1,0,45096,97,58),(500,0,0,'外卖员合集','外卖员合集','/upload_01/ads/20240911/2024091121403445876.jpeg',16,356,'',1,0,72131,66,39),(501,0,0,'乱伦合集','乱伦合集','/upload_01/ads/20240911/2024091121421736606.jpeg',21,284,'',1,0,56762,91,54),(502,0,0,'黑皮体育生合集','黑皮体育生的激情做爱系列','/upload_01/ads/20240912/2024091214475140180.jpeg',22,391,'',1,0,59427,99,59),(503,0,0,'直男学长合集第二弹','直男学长系列第二弹来啦','/upload_01/ads/20240912/2024091214510042743.png',24,283,'',1,0,50020,86,51),(504,0,0,'欧美精选合集','欧美精选系列 满足你对爱人的幻想','/upload_01/ads/20240912/2024091214530822788.png',26,316,'',1,0,22522,83,49),(505,0,0,'台湾网黄帅哥系列第二弹','台湾网黄帅哥系列，说话嗲嗲的台湾男生是不是你的菜呢','/upload_01/ads/20240912/2024091215041771499.png',34,210,'',1,0,57007,141,84),(506,0,0,'帅气小奶狗合集','帅气小奶狗系列','/upload_01/ads/20240912/2024091215062290310.png',22,407,'',1,0,44034,74,44),(507,0,0,'《GAYDAR》最新精品合集','《GAYDAR》最新系列','/upload_01/ads/20240912/2024091215552965769.jpeg',22,759,'',1,1730186171,945260,187,112),(508,0,0,'极品帅哥美少年合集','极品帅哥美少年合集','/upload_01/ads/20240912/2024091217344783656.jpeg',30,184,'',1,0,27319,119,71),(509,0,0,'经典日本GV','经典日本GV','/upload_01/ads/20240912/2024091217395847699.jpeg',17,408,'',1,0,67924,113,67),(510,0,0,'贱狗调教系列2','贱狗调教系列2','/upload_01/ads/20240912/2024091217485693421.jpeg',17,270,'',1,0,38714,70,42),(511,0,0,'时间停止合集','时间停止合集','/upload_01/ads/20240912/2024091217535718170.jpeg',16,465,'',1,0,35372,65,39),(512,0,0,'《天降男友》合集','《天降男友》合集','/upload_01/ads/20241011/2024101111301356308.jpeg',6,254,'',1,0,8857,20,12);
/*!40000 ALTER TABLE `ks_topic` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ks_topic_like`
--

DROP TABLE IF EXISTS `ks_topic_like`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ks_topic_like` (
`id` bigint NOT NULL AUTO_INCREMENT,
`uid` int NOT NULL COMMENT '用户uid',
`topic_id` int NOT NULL COMMENT '合集id',
PRIMARY KEY (`id`),
KEY `idx_uid` (`uid`),
KEY `idx_topic_id` (`topic_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='官方合集点赞';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ks_topic_like`
--

LOCK TABLES `ks_topic_like` WRITE;
/*!40000 ALTER TABLE `ks_topic_like` DISABLE KEYS */;
/*!40000 ALTER TABLE `ks_topic_like` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ks_topic_pay`
--

DROP TABLE IF EXISTS `ks_topic_pay`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ks_topic_pay` (
`id` int NOT NULL AUTO_INCREMENT,
`uid` int NOT NULL COMMENT '用户id',
`coins` int NOT NULL DEFAULT '0' COMMENT '购买时的价格',
`topic_id` int NOT NULL COMMENT '合集id',
`type` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT '1' COMMENT '类型 购买',
`created_at` datetime NOT NULL COMMENT '购买时间',
PRIMARY KEY (`id`) USING BTREE,
KEY `uid` (`uid`,`topic_id`) USING BTREE,
KEY `created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='合集购买记录';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ks_topic_pay`
--

LOCK TABLES `ks_topic_pay` WRITE;
/*!40000 ALTER TABLE `ks_topic_pay` DISABLE KEYS */;
/*!40000 ALTER TABLE `ks_topic_pay` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ks_topic_relation`
--

DROP TABLE IF EXISTS `ks_topic_relation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ks_topic_relation` (
`id` int NOT NULL AUTO_INCREMENT,
`topic_id` int NOT NULL,
`mv_id` int NOT NULL,
PRIMARY KEY (`id`) USING BTREE,
KEY `topic_id` (`topic_id`,`mv_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ks_topic_relation`
--

--
-- Table structure for table `ks_mh`
--

DROP TABLE IF EXISTS `ks_mh`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ks_mh` (
`id` int NOT NULL AUTO_INCREMENT,
`origin_id` varchar(20) DEFAULT NULL,
`title` varchar(255) NOT NULL DEFAULT '' COMMENT '标题',
`description` text COMMENT '描述',
`author` varchar(20) NOT NULL DEFAULT '' COMMENT '作者',
`category_id` int NOT NULL DEFAULT '0' COMMENT '漫画分类标识',
`uid` int NOT NULL DEFAULT '0' COMMENT '用户id',
`bg_thumb` varchar(200) DEFAULT '' COMMENT '详情背景图',
`thumb` varchar(200) DEFAULT '' COMMENT '封面图',
`favorites` int NOT NULL DEFAULT '0' COMMENT '收藏人数',
`tags` varchar(255) DEFAULT NULL COMMENT '标签',
`is_finish` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态 0 未完结， 1已完结',
`update_time` varchar(255) NOT NULL DEFAULT '完结' COMMENT '更新时间 周一 - 周日',
`recommend` tinyint NOT NULL DEFAULT '0' COMMENT '是否推荐',
`status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1上架0下架',
`is_free` tinyint NOT NULL DEFAULT '1' COMMENT '0 免费 1 vip 2  钻石（金币）',
`refresh_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP COMMENT '刷新时间',
`rating` int NOT NULL DEFAULT '1' COMMENT '浏览数',
`coins` int NOT NULL DEFAULT '0' COMMENT '定价',
`from` tinyint NOT NULL DEFAULT '0' COMMENT '来源',
`newest_series` int NOT NULL DEFAULT '1' COMMENT '最近更新到的章节',
`type` varchar(10) NOT NULL DEFAULT '' COMMENT ' 类型  long  short  single',
PRIMARY KEY (`id`),
KEY `title` (`title`) USING BTREE,
KEY `status` (`is_finish`) USING BTREE,
KEY `recommend` (`recommend`) USING BTREE,
KEY `type` (`type`),
FULLTEXT KEY `tags` (`tags`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 ROW_FORMAT=DYNAMIC COMMENT='漫画表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ks_mh`
--

LOCK TABLES `ks_mh` WRITE;
/*!40000 ALTER TABLE `ks_mh` DISABLE KEYS */;
INSERT INTO `ks_mh` VALUES (1,'1','15少女漂流记','漂流到地中海无人孤岛的15名性感少女，在这里展开了意想不到的性感物语让你在疲乏的日子里，能放松心情又大饱眼福的悬疑漫画！','蓝蓝',1,0,'','/data/cover/1.jpg',40,'全彩,韩漫,剧情,悬疑,强奸',1,'周四',0,0,2,'2022-06-23 03:20:12',11319733,10,0,48,'long'),(2,'2','Erostica~征服美女记','一直以来被武林众美女无视的魔夜慈悲的Erostica女神千辛万苦召唤的英雄竟是...','蓝蓝',2,0,'','/data/cover/2.jpg',7,'韩漫,全彩,穿越,动作,异世界',1,'完结',0,0,1,'2022-06-23 03:20:12',4044420,0,0,44,'long'),(3,'3','Masochist-潜藏的欲望','我对自己一无所知...太肮胀了...我想摆脱这样的自己Masochist...便是潜藏在我灵魂深处的那个真实的自我...！','蓝蓝',3,0,'','/data/cover/3.jpg',12,'SM,露出,韩漫,全彩,OL,剧情',1,'完结',0,0,1,'2022-06-23 03:20:12',383540,0,0,24,'long'),(4,'4','S商店的她','在成人用品店工作的她在销售之前总喜欢以身示范, 甚至连电插头!“想要和我亲身体验吗?”','蓝蓝',4,0,'','/data/cover/4.jpg',84,'韩漫,全彩,性玩具,调教,剧情,露出',1,'周二',0,0,1,'2022-06-23 03:20:12',8802135,0,0,75,'long'),(5,'5','七公主','我离开的一年后, 她去了另一个世界等着吧! 我会狠狠的报复你们!让你们深深陷入死亡的恐惧与绝望当中!','蓝蓝',5,0,'','/data/cover/5.jpg',13,'韩漫,全彩,剧情,动作,后宫',1,'周四',0,0,1,'2022-06-23 03:20:12',1629847,0,0,52,'long'),(6,'6','三人行？','OMG！你就是我？我就是你？忙碌的生活中如果有分身就完美了连爱情都分成两半的姜修赫到底该怎么做？','蓝蓝',6,0,'','/data/cover/6.jpg',6,'幻想,剧情,韩漫,全彩',1,'完结',0,0,1,'2022-06-23 03:20:12',2938828,0,0,28,'long'),(7,'7','亲爱的你-Liebling！','伴随着中世纪的诅咒一同出现在的骑士老爷爷! 太勋为了解除诅咒而和他发生了关系…这样下去, 会变成gay的! 弥漫着粉红气息的幻想穿越BL!','蓝蓝',7,0,'','/data/cover/7.jpg',7,'BL,长篇,穿越,剧情',1,'周四',0,0,1,'2022-06-23 03:20:12',2033157,0,0,99,'long'),(8,'8','任何小姐','残酷现实的解决师化身为任何人的暗黑英雄','蓝蓝',8,0,'','/data/cover/8.jpg',5,'剧情,奇幻冒险,韩漫,英雄系',1,'周六',0,0,1,'2022-06-23 03:20:12',626839,0,0,31,'long'),(9,'9','优质女人','她就像是这污浊世界里的一股纯洁氧气不断的用自己的身体安慰孤寂的男人们！一个如同天使般的女人~ 真正爱她的男人到底在哪里呢？','蓝蓝',9,0,'','/data/cover/9.jpg',31,'剧情',1,'周四',0,0,1,'2022-06-23 03:20:12',5527269,0,0,48,'long'),(10,'10','体感情趣用品','被人用自己性器做成情趣用品之后身心不受控制，这到底是怎么回事...','蓝蓝',10,0,'','/data/cover/10.jpg',31,'剧情,幽默搞笑',1,'周三',0,0,1,'2022-06-23 03:20:12',16362246,0,0,41,'long'),(11,'11','你和我的小秘密','能让我达到最大快感的命中注定的那个她…到底会是谁呢？快点让我爽翻天吧~','蓝蓝',11,0,'','/data/cover/11.jpg',13,'剧情',1,'周三',0,0,1,'2022-06-23 03:20:12',3298095,0,0,60,'long'),(12,'12','初恋的女儿','相爱却未能相守的初恋女友她的女儿代替她来到我身边了吗?初恋的女儿, 危险又刺激的爱情!','蓝蓝',12,0,'','/data/cover/12.jpg',29,'剧情,浪漫爱情',1,'周三',0,0,1,'2022-06-23 03:20:12',5433684,0,0,50,'long'),(13,'13','卖身契约','我是个为钱而变卖妻子的人渣虽然爱着妻子, 但却更想活下去...','蓝蓝',13,0,'','/data/cover/13.jpg',93,'剧情',1,'周日',0,0,1,'2022-06-23 03:20:12',18147225,0,0,50,'long'),(14,'14','去幸岛','一个男人的天堂，女人的地狱欢迎来到&ldquo;去幸岛&rdquo;...&hearts;','蓝蓝',14,0,'','/data/cover/14.jpg',38,'剧情',1,'周五',0,0,1,'2022-06-23 03:20:12',3400958,0,0,40,'long'),(15,'15','反乌托邦游戏','被抓到无人岛的24名男女！必须在这里度过10个月才可获得5亿奖金在这个肉欲横流的社会，人们逐渐展露本性&hellip;&hellip;','蓝蓝',15,0,'','/data/cover/15.jpg',88,'剧情',1,'周六',0,0,1,'2022-06-23 03:20:12',16740221,0,0,136,'long'),(16,'16','只为满足你','下半身麻痹的丈夫为了满足妻子提出了一个让人难以理解又面红耳赤的提议~这下你不会因为性欲而离开我了吧','蓝蓝',16,0,'','/data/cover/16.jpg',22,'剧情,浪漫爱情',1,'周一',0,0,1,'2022-06-23 03:20:12',6666972,0,0,53,'long'),(17,'17','同居','朋友的女朋友允熙,这便是我们的初遇。','蓝蓝',17,0,'','/data/cover/17.jpg',8,'浪漫爱情,剧情',1,'完结',0,0,1,'2022-06-23 03:20:12',1261745,0,0,16,'long'),(18,'18','大叔','意外找上平民作家的姻缘名声大噪后的小说家道德大叔与&quot;她们&quot;的三角罗曼史的结局是?','蓝蓝',18,0,'','/data/cover/18.jpg',8,'剧情,浪漫爱情',1,'完结',0,0,1,'2022-06-23 03:20:12',1494867,0,0,24,'long'),(19,'19','大声说爱我','因为过去的阴影而无法正常恋爱的男主直到遇见了六年不见的青梅竹马&hellip;','蓝蓝',19,0,'','/data/cover/19.jpg',3,'浪漫爱情,剧情',1,'完结',0,0,1,'2022-06-23 03:20:12',944478,0,0,32,'long'),(20,'20','女职员们','小出版社意外的成功, 使我成了暴发户! 换车换房子, 什么都要换新的! 那么...女人也换一次...试试?','蓝蓝',20,0,'','/data/cover/20.jpg',29,'剧情',1,'周四',0,0,1,'2022-06-23 03:20:12',2798439,0,0,24,'long'),(21,'21','她的心声','从没跟我说过真心话的你！现在你内心所想，我通通听得到！！','蓝蓝',21,0,'','/data/cover/21.jpg',9,'剧情',1,'周四',0,0,1,'2022-06-23 03:20:12',2374443,0,0,48,'long'),(22,'22','妻子的情人','我的妻子有了炮友!对我性冷淡的妻子竟浑身赤裸勾引外卖小哥&hellip;&hellip;!!!!要离婚? 不! 我也找一个炮友就好了~','蓝蓝',22,0,'','/data/cover/22.jpg',11,'剧情',1,'周一',0,0,1,'2022-06-23 03:20:12',3382503,0,0,100,'long'),(23,'23','孤岛拼图','暴风雨来袭的孤岛酒店里一名女房客全身赤裸着坠楼而死就此拉开了接下来一切疯狂举动和揭露人性欲望的序幕','蓝蓝',23,0,'','/data/cover/23.jpg',4,'剧情',1,'周五',0,0,1,'2022-06-23 03:20:12',1940640,0,0,28,'long'),(24,'24','家有双妻','我有两个老婆, 而且她们还是闺蜜！甚至我们还住在一起！怎么样~ 刺不刺激, 爽不爽啊~','蓝蓝',24,0,'','/data/cover/24.jpg',21,'剧情',1,'周日',0,0,1,'2022-06-23 03:20:12',5626271,0,0,33,'long'),(25,'25','尸去本性','突然的异变与人性的丑陋到底哪一个才最黑暗!','蓝蓝',25,0,'','/data/cover/25.jpg',5,'恐怖,惊悚',1,'完结',0,0,1,'2022-06-23 03:20:12',2320474,0,0,21,'long'),(26,'26','岳母家的刺激生活（完结）','（完结）什么..!! 炮友怀孕了..?!而丈母娘居然是我儿时的初恋对象?!这命运的玩笑有点儿开大了..','蓝蓝',26,0,'','/data/cover/26.jpg',218,'剧情',1,'周日',0,0,1,'2022-06-23 03:20:12',151850108,0,0,50,'long'),(27,'27','巧手妇产科','欲望，越是被禁止，越引人去触及释放所有欲望的地方，欢迎来到巧手妇产科!','蓝蓝',27,0,'','/data/cover/27.jpg',3,'剧情,浪漫爱情',1,'周五',0,0,1,'2022-06-23 03:20:12',1683018,0,0,33,'long'),(28,'28','性爱百分百','被甩男意外与隔壁女人滚床单靠意淫人妻被侵才能勃起的男人和成为3P炮友的18年青梅竹马!! 关于疯狂勃起的一百个故事！！','蓝蓝',28,0,'','/data/cover/28.jpg',4,'漫画式小说',1,'周二',0,0,1,'2022-06-23 03:20:12',1793532,0,0,48,'long'),(29,'29','恋上闺蜜的爸爸','在闺蜜家寄宿后开始了一段禁忌而又隐密的恋爱&hellip;...','蓝蓝',29,0,'','/data/cover/29.jpg',15,'浪漫爱情,剧情',1,'周一',0,0,1,'2022-06-23 03:20:12',1910939,0,0,75,'long'),(30,'30','成人俱乐部','成人俱乐部里超乎想象的刺激与享受~带你领略前所未有的性爱地下城！','蓝蓝',30,0,'','/data/cover/30.jpg',54,'剧情',1,'周二',0,0,1,'2022-06-23 03:20:12',5843790,0,0,24,'long'),(31,'31','成人竞技场','为了因为债务而被讨债公司胁迫的男友 选择投身成人摔角的主人公到底最后的命运会是如何呢!?','蓝蓝',31,0,'','/data/cover/31.jpg',7,'剧情',1,'完结',0,0,1,'2022-06-23 03:20:12',3687890,0,0,25,'long'),(32,'32','拜托了人妻','在成人网站上身经百战受人膜拜的我!现实中却是个母胎单身某天网站上相识的哥哥说让我勾引嫂子&hellip;','蓝蓝',32,0,'','/data/cover/32.jpg',16,'剧情',1,'周二',0,0,1,'2022-06-23 03:20:12',3345626,0,0,24,'long'),(33,'33','新来的女邻居','隔壁搬来一位超正点的人妻!每晚从隔壁传来的销魂叫声令我崩溃丈夫出差后，她便开始有意无意地诱惑我...','蓝蓝',33,0,'','/data/cover/33.jpg',15,'剧情',0,'周四',0,0,1,'2022-06-28 08:43:52',6378841,0,0,0,'long'),(34,'34','未亡人','等待着父亲的死亡出现在我面前的可疑的她...我所有的计划都开始动摇!','蓝蓝',34,0,'','/data/cover/34.jpg',14,'剧情',1,'周三',0,0,1,'2022-06-23 03:20:12',823894,0,0,51,'long'),(35,'35','枷锁','我年轻漂亮，纯洁是什么？我只是利用自己得到我想要的未来遇到那个家伙之前，我一直都是人生的赢家&hellip;。','蓝蓝',35,0,'','/data/cover/35.jpg',17,'剧情',1,'周日',0,0,1,'2022-06-23 03:20:12',2478164,0,0,93,'long'),(36,'36','梦蝶','继承家业的驱魔师，去因一连串的事件面临灭门的危机被往生公司聘用后，开启了业余驱魔师的道路......','蓝蓝',36,0,'','/data/cover/36.jpg',23,'剧情',1,'完结',0,0,1,'2022-06-23 03:20:12',3854531,0,0,26,'long'),(37,'37','欲望人妻','对过去安分守己的自己感到后悔现在开始...我要为自己生活了！','蓝蓝',37,0,'','/data/cover/37.jpg',14,'剧情',1,'周六',0,0,1,'2022-06-23 03:20:12',1594923,0,0,66,'long'),(38,'38','湿乐园','我是名快递员厌倦了每天偷看别人做爱, 只能自慰的日子无意间碰到的巨屌, 是否能满足我的欲望?','蓝蓝',38,0,'','/data/cover/38.jpg',36,'剧情',1,'周二',0,0,1,'2022-06-23 03:20:12',10561312,0,0,83,'long'),(39,'39','漫画吧的秀晶','在漫画吧与秀晶重逢单恋的初恋能否修成正果？','蓝蓝',39,0,'','/data/cover/39.jpg',4,'剧情',1,'周三',0,0,1,'2022-06-23 03:20:12',2628472,0,0,46,'long'),(40,'40','炼狱鬼岛','韩国南部的某处...连名字都没有的一个岛屿父母过世，沦为性奴的我好不容易逃出生天10年后为了复仇再次踏入这片炼狱......','蓝蓝',40,0,'','/data/cover/40.jpg',24,'剧情',1,'完结',0,0,1,'2022-06-23 03:20:12',3211188,0,0,44,'long'),(41,'41','爱情契约','为了出名不择手段, 毫无底线, 交付出身体与真心而最终换来的又会是什么呢？让人心疼又震惊的女明星上位记','蓝蓝',41,0,'','/data/cover/41.jpg',13,'剧情',1,'周一',0,0,1,'2022-06-23 03:20:12',2267622,0,0,75,'long'),(42,'42','王牌经纪人','你以为天下的经纪人全都一样吗？偶像？女演员？韩流明星？一个眼神就让她们脱光光','蓝蓝',42,0,'','/data/cover/42.jpg',68,'剧情',1,'周五',0,0,1,'2022-06-23 03:20:12',16888709,0,0,42,'long'),(43,'43','生存游戏','&quot;不是你死就是我亡!&quot;30个人为了获得重生，展开了残酷的争斗!一场在废弃学校中展开的生存游戏!','蓝蓝',43,0,'','/data/cover/43.jpg',17,'恐怖,惊悚',1,'周五',0,0,1,'2022-06-23 03:20:12',14260693,0,0,60,'long'),(44,'44','经纪人','&quot;黑道传说&quot;进军娱乐圈!可迎接他的只有倒闭的公司和令人头痛的演员!为了责任他只能默默的承受','蓝蓝',44,0,'','/data/cover/44.jpg',10,'剧情',1,'周一',0,0,1,'2022-06-23 03:20:12',1145330,0,0,95,'long'),(45,'45','继母','她是大我10岁的继母，是个童颜巨乳的完美女神但是她为什么把丝瓜夹在两腿之间呢!?这难道是要诱惑我吗？啊~我已经没办法再忍下去了!!','蓝蓝',45,0,'','/data/cover/45.jpg',79,'剧情',1,'周五',0,0,1,'2022-06-23 03:20:12',35785625,0,0,51,'long'),(46,'46','羁绊','桂末子. 难忘的名字她像个不速之客闯进了我的生活从此和她的羁绊剪不断理还乱','蓝蓝',46,0,'','/data/cover/46.jpg',3,'剧情',1,'周日',0,0,1,'2022-06-23 03:20:12',389703,0,0,62,'long'),(47,'47','美人为馅','从学生时代就暗恋的好朋友原本近水楼台先得月的他复学后却面临着被人捷足先登的危机...','蓝蓝',47,0,'','/data/cover/47.jpg',8,'剧情',1,'周三',0,0,1,'2022-06-23 03:20:12',985945,0,0,50,'long');


--
-- Table structure for table `ks_mh_favorites`
--

DROP TABLE IF EXISTS `ks_mh_favorites`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ks_mh_favorites` (
`id` int unsigned NOT NULL AUTO_INCREMENT,
`uid` int NOT NULL DEFAULT '0' COMMENT '用户id',
`mh_id` int NOT NULL DEFAULT '0' COMMENT '漫画id',
`created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP COMMENT '创建更新时间',
PRIMARY KEY (`id`),
KEY `user_id` (`uid`),
KEY `comics_id` (`mh_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci COMMENT='漫画收藏表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ks_mh_favorites`
--

LOCK TABLES `ks_mh_favorites` WRITE;
/*!40000 ALTER TABLE `ks_mh_favorites` DISABLE KEYS */;
/*!40000 ALTER TABLE `ks_mh_favorites` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ks_mh_pay`
--

DROP TABLE IF EXISTS `ks_mh_pay`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ks_mh_pay` (`id` int NOT NULL AUTO_INCREMENT,`uid` int NOT NULL COMMENT '用户id',`coins` int NOT NULL DEFAULT '0' COMMENT '购买时的价格',`mh_id` int NOT NULL COMMENT '漫画id',`type` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT '1' COMMENT '类型 购买 次数 赠送',`created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP COMMENT '购买时间',PRIMARY KEY (`id`) USING BTREE,KEY `uid` (`uid`,`mh_id`) USING BTREE,KEY `created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci ROW_FORMAT=COMPACT COMMENT='漫画购买记录';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ks_mh_pay`
--

LOCK TABLES `ks_mh_pay` WRITE;
/*!40000 ALTER TABLE `ks_mh_pay` DISABLE KEYS */;
/*!40000 ALTER TABLE `ks_mh_pay` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ks_mh_series`
--

DROP TABLE IF EXISTS `ks_mh_series`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ks_mh_series` (
`id` int NOT NULL AUTO_INCREMENT,
`pid` int NOT NULL COMMENT '漫画编号 id',
`episode` smallint NOT NULL DEFAULT '1' COMMENT '章节编号',
`thumb` varchar(255) NOT NULL DEFAULT '' COMMENT '封面url',
`from` int NOT NULL DEFAULT '0',
PRIMARY KEY (`id`),
KEY `pid` (`pid`),
KEY `episode` (`episode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COMMENT='连载韩漫剧集表';
/*!40101 SET character_set_client = @saved_cs_client */;


LOCK TABLES `ks_mh_series` WRITE;
/*!40000 ALTER TABLE `ks_mh_series` DISABLE KEYS */;
INSERT INTO `ks_mh_series` VALUES (1,1,1,'',0),(2,1,2,'',0),(3,1,3,'',0),(4,1,4,'',0),(5,1,5,'',0),(6,1,6,'',0),(7,1,7,'',0),(8,1,8,'',0),(9,1,9,'',0),(10,1,10,'',0),(11,1,11,'',0),(12,1,12,'',0),(13,1,13,'',0),(14,1,14,'',0),(15,1,15,'',0),(16,1,16,'',0),(17,1,17,'',0),(18,1,18,'',0),(19,1,19,'',0),(20,1,20,'',0),(21,1,21,'',0),(22,1,22,'',0),(23,1,23,'',0),(24,1,24,'',0),(25,1,25,'',0),(26,1,26,'',0),(27,1,27,'',0),(28,1,28,'',0),(29,1,29,'',0),(30,1,30,'',0),(31,1,31,'',0),(32,1,32,'',0),(33,1,33,'',0),(34,1,34,'',0),(35,1,35,'',0),(36,1,36,'',0),(37,1,37,'',0),(38,1,38,'',0),(39,1,39,'',0),(40,1,40,'',0),(41,1,41,'',0),(42,1,42,'',0),(43,1,43,'',0),(44,1,44,'',0),(45,1,45,'',0),(46,1,46,'',0),(47,1,47,'',0);

DROP TABLE IF EXISTS `ks_mh_src`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ks_mh_src` (
`id` int NOT NULL AUTO_INCREMENT,
`m_id` int NOT NULL DEFAULT '0' COMMENT '漫画ID，剧集ID',
`s_id` int NOT NULL DEFAULT '1' COMMENT '章节ID，单本默认1',
`img_url` varchar(255) DEFAULT '' COMMENT '图片地址',
`img_width` varchar(10) NOT NULL DEFAULT '0',
`img_height` varchar(10) NOT NULL DEFAULT '0',
`from` tinyint NOT NULL DEFAULT '0',
PRIMARY KEY (`id`),
KEY `m_id` (`m_id`) USING BTREE,
KEY `s_id` (`s_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COMMENT='漫画图片文件';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ks_mh_src`
--

LOCK TABLES `ks_mh_src` WRITE;
/*!40000 ALTER TABLE `ks_mh_src` DISABLE KEYS */;
INSERT INTO `ks_mh_src` VALUES (1,1,1,'/data/1/1/0.jpg','720','500',0),(2,1,1,'/data/1/1/1.jpg','720','500',0),(3,1,1,'/data/1/1/2.jpg','720','500',0),(4,1,1,'/data/1/1/3.jpg','720','500',0),(5,1,1,'/data/1/1/4.jpg','720','700',0),(6,1,1,'/data/1/1/5.jpg','720','700',0),(7,1,1,'/data/1/1/6.jpg','720','700',0),(8,1,1,'/data/1/1/7.jpg','720','700',0),(9,1,1,'/data/1/1/8.jpg','720','700',0),(10,1,1,'/data/1/1/9.jpg','720','700',0),(11,1,1,'/data/1/1/10.jpg','720','700',0),(12,1,1,'/data/1/1/11.jpg','720','700',0),(13,1,1,'/data/1/1/12.jpg','720','700',0),(14,1,1,'/data/1/1/13.jpg','720','700',0),(15,1,1,'/data/1/1/14.jpg','720','700',0),(16,1,1,'/data/1/1/15.jpg','720','700',0),(17,1,1,'/data/1/1/16.jpg','720','700',0),(18,1,1,'/data/1/1/17.jpg','720','700',0),(19,1,1,'/data/1/1/18.jpg','720','700',0),(20,1,1,'/data/1/1/19.jpg','720','700',0),(21,1,1,'/data/1/1/20.jpg','720','700',0),(22,1,1,'/data/1/1/21.jpg','720','700',0),(23,1,1,'/data/1/1/22.jpg','720','700',0),(24,1,1,'/data/1/1/23.jpg','720','700',0),(25,1,1,'/data/1/1/24.jpg','720','700',0),(26,1,1,'/data/1/1/25.jpg','720','700',0),(27,1,1,'/data/1/1/26.jpg','720','700',0),(28,1,1,'/data/1/1/27.jpg','720','700',0),(29,1,1,'/data/1/1/28.jpg','720','700',0),(30,1,1,'/data/1/1/29.jpg','720','700',0),(31,1,1,'/data/1/1/30.jpg','720','700',0),(32,1,1,'/data/1/1/31.jpg','720','700',0),(33,1,1,'/data/1/1/32.jpg','720','700',0),(34,1,1,'/data/1/1/33.jpg','720','700',0),(35,1,1,'/data/1/1/34.jpg','720','700',0),(36,1,1,'/data/1/1/35.jpg','720','700',0),(37,1,1,'/data/1/1/36.jpg','720','700',0),(38,1,1,'/data/1/1/37.jpg','720','700',0),(39,1,1,'/data/1/1/38.jpg','720','700',0),(40,1,1,'/data/1/1/39.jpg','720','700',0),(41,1,1,'/data/1/1/40.jpg','720','700',0),(42,1,1,'/data/1/1/41.jpg','720','700',0),(43,1,1,'/data/1/1/42.jpg','720','700',0),(44,1,1,'/data/1/1/43.jpg','720','700',0),(45,1,1,'/data/1/1/44.jpg','720','700',0),(46,1,1,'/data/1/1/45.jpg','720','700',0),(47,1,1,'/data/1/1/46.jpg','720','700',0);

--
-- Table structure for table `ks_mh_tab`
--

DROP TABLE IF EXISTS `ks_mh_tab`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ks_mh_tab` (
`tab_id` int NOT NULL AUTO_INCREMENT,
`tab_name` varchar(20) NOT NULL COMMENT '导航蓝标签组',
`tags_str` varchar(1000) DEFAULT NULL COMMENT '标签',
`sort_num` smallint unsigned NOT NULL COMMENT '排序',
`status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 启用',
`is_tab` tinyint(1) NOT NULL COMMENT '主页展示',
`is_category` tinyint(1) NOT NULL COMMENT '分类过滤展示',
`show_style` enum('H-1*N','V-3*N','V-2*N','') DEFAULT NULL,
`show_number` tinyint DEFAULT '6',
PRIMARY KEY (`tab_id`),
KEY `status` (`status`),
KEY `is_tab` (`is_tab`),
KEY `is_category` (`is_category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 ROW_FORMAT=DYNAMIC COMMENT='漫画tab栏';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ks_mh_tab`
--

LOCK TABLES `ks_mh_tab` WRITE;
/*!40000 ALTER TABLE `ks_mh_tab` DISABLE KEYS */;
INSERT INTO `ks_mh_tab` VALUES (1,'SM','调教,SM',55,1,1,1,'V-2*N',6),(2,'女装/伪娘','女装,伪娘',4,1,0,1,'V-3*N',6),(12,'韩漫','韩漫',11,1,1,1,'V-3*N',6),(13,'校园','教师,校园',2,0,0,0,'V-3*N',6),(14,'同人作品','同人',39,1,0,0,'V-2*N',6),(15,'幻想世界','人外,吸血鬼,幻想,兽耳,穿越',9,1,1,0,'V-3*N',6),(16,'职场风云','总裁,办公室,职场',8,0,0,0,'V-3*N',6),(17,'国漫，总裁,办公室,职场','总裁,办公室,职场',30,0,0,0,'H-1*N',6),(18,'国漫,总裁,办公室,职场','总裁,办公室,职场',12,1,0,0,'H-1*N',6),(19,'女装','女装',1,1,0,1,'H-1*N',6),(20,'伪娘','伪娘',1,0,0,0,'H-1*N',6),(21,'校园','校园',5,1,0,1,'H-1*N',6),(22,'日漫','日漫',21,1,0,1,'V-3*N',6),(23,'国漫','国漫',20,1,0,1,'V-2*N',4),(24,'首页推荐','推荐',1,0,0,0,'H-1*N',6);
/*!40000 ALTER TABLE `ks_mh_tab` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ks_mh_tags`
--

DROP TABLE IF EXISTS `ks_mh_tags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ks_mh_tags` (
`id` int unsigned NOT NULL AUTO_INCREMENT,
`name` varchar(50) NOT NULL DEFAULT '' COMMENT '标签',
`sort_num` int DEFAULT NULL COMMENT '排序',
`created_at` int NOT NULL DEFAULT '0' COMMENT '创建时间',
`updated_at` int DEFAULT NULL,
`img_url` varchar(255) NOT NULL DEFAULT '' COMMENT '标签封面图',
`home` tinyint NOT NULL COMMENT '首页显示',
`status` tinyint NOT NULL DEFAULT '1' COMMENT '列表显示状态',
`user_up` tinyint NOT NULL DEFAULT '1' COMMENT '允许用户上传',
`horizontal_img` varchar(255) NOT NULL COMMENT '横向图片',
`description` varchar(255) NOT NULL COMMENT '描述',
PRIMARY KEY (`id`) USING BTREE,
UNIQUE KEY `vid` (`name`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 ROW_FORMAT=DYNAMIC COMMENT='标签';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ks_mh_tags`
--

LOCK TABLES `ks_mh_tags` WRITE;
/*!40000 ALTER TABLE `ks_mh_tags` DISABLE KEYS */;
INSERT INTO `ks_mh_tags` VALUES (1,'剧情',0,0,NULL,'',0,1,1,'',''),(2,'韩漫',0,0,NULL,'',0,1,1,'',''),(3,'同人',0,0,NULL,'',0,1,1,'',''),(4,'壮受',0,0,NULL,'',0,1,1,'',''),(5,'伪娘',0,0,NULL,'',0,1,1,'',''),(6,'悬疑',0,0,NULL,'',0,1,1,'',''),(7,'职场',0,0,NULL,'',0,1,1,'',''),(8,'SM',0,0,NULL,'',0,1,1,'',''),(9,'穿越',0,0,NULL,'',0,1,1,'',''),(10,'调教',0,0,NULL,'',0,1,1,'',''),(11,'女装',0,0,NULL,'',0,1,1,'',''),(12,'校园',0,0,NULL,'',0,1,1,'',''),(13,'古代',0,0,NULL,'',0,1,1,'',''),(14,'兽耳',0,0,NULL,'',0,1,1,'',''),(15,'幻想',0,0,NULL,'',0,1,1,'',''),(16,'办公室',0,0,NULL,'',0,1,1,'',''),(17,'总裁',0,0,NULL,'',0,1,1,'',''),(18,'吸血鬼',0,0,NULL,'',0,1,1,'',''),(19,'人外',0,0,NULL,'',0,1,1,'',''),(20,'日漫',0,0,NULL,'',0,1,1,'',''),(21,'短篇',0,0,NULL,'',0,1,1,'',''),(22,'全彩',55,0,NULL,'',0,1,1,'',''),(23,'三角恋',10,0,NULL,'',0,1,1,'',''),(24,'教师',12,0,NULL,'',0,1,1,'',''),(25,'年下',25,0,NULL,'',0,1,1,'',''),(26,'国漫',32,0,NULL,'',0,1,1,'',''),(27,'运动',21,0,NULL,'',0,1,1,'',''),(28,'青春',12,0,NULL,'',0,1,1,'',''),(29,'虐恋',12,0,NULL,'',0,1,1,'',''),(30,'黑道',6,0,NULL,'',0,1,1,'',''),(31,'搞笑',1,0,NULL,'',0,1,1,'',''),(32,'清秀',6,0,NULL,'',0,1,1,'',''),(33,'推荐',3,0,NULL,'',0,1,1,'',''),(34,'可爱',0,0,NULL,'',0,1,1,'','');
/*!40000 ALTER TABLE `ks_mh_tags` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ks_user_download`
--

DROP TABLE IF EXISTS `ks_user_download`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ks_user_download` (
`id` int NOT NULL AUTO_INCREMENT,
`aff` int NOT NULL DEFAULT '0' COMMENT 'aff',
`val` int NOT NULL DEFAULT '0' COMMENT '下载次数',
`total` int NOT NULL COMMENT '总下载次数',
`total_money` int NOT NULL DEFAULT '0' COMMENT '累计充值',
`created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
`updated_at` timestamp NULL DEFAULT NULL COMMENT '更新时间',
PRIMARY KEY (`id`),
KEY `aff` (`aff`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='用户视频下载次数表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ks_user_download`
--

LOCK TABLES `ks_user_download` WRITE;
/*!40000 ALTER TABLE `ks_user_download` DISABLE KEYS */;
/*!40000 ALTER TABLE `ks_user_download` ENABLE KEYS */;
UNLOCK TABLES;


--
-- Table structure for table `ks_feedback_reward`
--

DROP TABLE IF EXISTS `ks_feedback_reward`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ks_feedback_reward` (
`id` int unsigned NOT NULL AUTO_INCREMENT,
`aff` int NOT NULL DEFAULT '0' COMMENT '用户aff',
`type` tinyint NOT NULL DEFAULT '1' COMMENT '反馈类型',
`content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT '内容',
`replay` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT '回复内容',
`images` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT '图片',
`status` tinyint NOT NULL DEFAULT '0' COMMENT '状态',
`created_at` timestamp NULL DEFAULT NULL,
`updated_at` timestamp NULL DEFAULT NULL,
PRIMARY KEY (`id`) USING BTREE,
KEY `aff` (`aff`) USING BTREE,
KEY `status` (`status`) USING BTREE,
KEY `type` (`type`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC COMMENT='有奖反馈表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ks_feedback_reward`
--

LOCK TABLES `ks_feedback_reward` WRITE;
/*!40000 ALTER TABLE `ks_feedback_reward` DISABLE KEYS */;
/*!40000 ALTER TABLE `ks_feedback_reward` ENABLE KEYS */;
UNLOCK TABLES;



