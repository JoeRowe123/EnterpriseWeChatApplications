<?php

use yii\db\Migration;

/**
 * Class m181128_101327_first
 */
class m181128_101327_first extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $sql = /** @lang text */
            <<<TAG
/*
 Navicat Premium Data Transfer

 Source Server         : 127.0.0.1-5.7
 Source Server Type    : MySQL
 Source Server Version : 50718
 Source Host           : 127.0.0.1
 Source Database       : shop

 Target Server Type    : MySQL
 Target Server Version : 50718
 File Encoding         : utf-8

 Date: 11/28/2018 18:25:00 PM
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
--  Table structure for `auth_assignment`
-- ----------------------------
DROP TABLE IF EXISTS `auth_assignment`;
CREATE TABLE `auth_assignment` (
  `item_name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `user_id` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`item_name`,`user_id`),
  KEY `auth_assignment_user_id_idx` (`user_id`),
  CONSTRAINT `auth_assignment_ibfk_1` FOREIGN KEY (`item_name`) REFERENCES `auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
--  Records of `auth_assignment`
-- ----------------------------
BEGIN;
INSERT INTO `auth_assignment` VALUES ('系统超级管理员', '3', '1543398106');
COMMIT;

-- ----------------------------
--  Table structure for `auth_item`
-- ----------------------------
DROP TABLE IF EXISTS `auth_item`;
CREATE TABLE `auth_item` (
  `name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `type` smallint(6) NOT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `rule_name` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  `data` blob,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`name`),
  KEY `rule_name` (`rule_name`),
  KEY `idx-auth_item-type` (`type`),
  CONSTRAINT `auth_item_ibfk_1` FOREIGN KEY (`rule_name`) REFERENCES `auth_rule` (`name`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
--  Records of `auth_item`
-- ----------------------------
BEGIN;
INSERT INTO `auth_item` VALUES ('/*', '2', null, null, null, '1543398106', '1543398106'), ('/admin/assignment/index', '2', null, null, null, '1543400257', '1543400257'), ('/admin/menu/index', '2', null, null, null, '1543400295', '1543400295'), ('/admin/permission/index', '2', null, null, null, '1543400271', '1543400271'), ('/admin/role/index', '2', null, null, null, '1543400264', '1543400264'), ('/admin/route/index', '2', null, null, null, '1543400278', '1543400278'), ('/admin/rule/index', '2', null, null, null, '1543400288', '1543400288'), ('/admin/user/index', '2', null, null, null, '1543400248', '1543400248'), ('/super/config/index', '2', null, null, null, '1543400110', '1543400110'), ('系统超级管理员', '2', null, null, null, '1543398106', '1543398106');
COMMIT;

-- ----------------------------
--  Table structure for `auth_item_child`
-- ----------------------------
DROP TABLE IF EXISTS `auth_item_child`;
CREATE TABLE `auth_item_child` (
  `parent` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `child` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`parent`,`child`),
  KEY `child` (`child`),
  CONSTRAINT `auth_item_child_ibfk_1` FOREIGN KEY (`parent`) REFERENCES `auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `auth_item_child_ibfk_2` FOREIGN KEY (`child`) REFERENCES `auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
--  Records of `auth_item_child`
-- ----------------------------
BEGIN;
INSERT INTO `auth_item_child` VALUES ('系统超级管理员', '/*');
COMMIT;

-- ----------------------------
--  Table structure for `auth_rule`
-- ----------------------------
DROP TABLE IF EXISTS `auth_rule`;
CREATE TABLE `auth_rule` (
  `name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `data` blob,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
--  Table structure for `config`
-- ----------------------------
DROP TABLE IF EXISTS `config`;
CREATE TABLE `config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key` varchar(100) NOT NULL,
  `value` json NOT NULL,
  `memo` text COMMENT '备注',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0 草稿, 1 生效',
  `create_user` int(11) NOT NULL DEFAULT '0',
  `update_user` int(11) NOT NULL DEFAULT '0',
  `created_at` int(11) NOT NULL DEFAULT '0',
  `updated_at` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `index_key` (`key`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COMMENT='配置表';

-- ----------------------------
--  Table structure for `menu`
-- ----------------------------
DROP TABLE IF EXISTS `menu`;
CREATE TABLE `menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL,
  `parent` int(11) DEFAULT NULL,
  `route` varchar(255) DEFAULT NULL,
  `order` int(11) DEFAULT NULL,
  `data` blob,
  PRIMARY KEY (`id`),
  KEY `parent` (`parent`),
  CONSTRAINT `menu_ibfk_1` FOREIGN KEY (`parent`) REFERENCES `menu` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Records of `menu`
-- ----------------------------
BEGIN;
INSERT INTO `menu` VALUES ('1', '系统设置', null, null, null, 0x676c79706869636f6e20676c79706869636f6e2d636f67), ('2', '系统配置', '1', '/super/config/index', null, null), ('3', '权限管理', null, null, null, 0x676c79706869636f6e20676c79706869636f6e2d75736572), ('5', '权限分配', '3', '/admin/assignment/index', null, null), ('6', '角色列表', '3', '/admin/role/index', null, null), ('7', '权限列表', '3', '/admin/permission/index', null, null), ('8', '路由列表', '3', '/admin/route/index', null, null), ('9', '规则列表', '3', '/admin/rule/index', null, null), ('10', '菜单管理', '3', '/admin/menu/index', null, null);
COMMIT;

-- ----------------------------
--  Table structure for `migration`
-- ----------------------------
DROP TABLE IF EXISTS `migration`;
CREATE TABLE `migration` (
  `version` varchar(180) NOT NULL,
  `apply_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
--  Records of `migration`
-- ----------------------------
BEGIN;
INSERT INTO `migration` VALUES ('m130524_201442_init', '1543397296'), ('m140506_102106_rbac_init', '1543397562'), ('m140602_111327_create_menu_table', '1543397346'), ('m160312_050000_create_user', '1543397346'), ('m170907_052038_rbac_add_index_on_auth_assignment_user_id', '1543397562');
COMMIT;

-- ----------------------------
--  Table structure for `user`
-- ----------------------------
DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `auth_key` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `password_hash` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password_reset_token` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `status` smallint(6) NOT NULL DEFAULT '10',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `password_reset_token` (`password_reset_token`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
--  Records of `user`
-- ----------------------------
BEGIN;
INSERT INTO `user` VALUES ('3', 'pcyanglei', '21PB5xEfFanMuBnHlB0tG1R-7wV46KVi', '$2y$13\$fG1HWNF.rA6ebT5OWoFAV.BktTntndir1pEwAYFZgdF.C1p4KzxuK', null, '12@qq.com', '10', '1543398106', '1543398106');
COMMIT;

SET FOREIGN_KEY_CHECKS = 1;

TAG;
        $this->execute($sql);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m181128_101327_first cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m181128_101327_first cannot be reverted.\n";

        return false;
    }
    */
}
