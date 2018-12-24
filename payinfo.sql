/*
 Navicat MySQL Data Transfer

 Source Server         : clark-data
 Source Server Type    : MySQL
 Source Server Version : 50628
 Source Host           : bj-cdb-lzup1m7e.sql.tencentcdb.com:62513
 Source Schema         : payinfo

 Target Server Type    : MySQL
 Target Server Version : 50628
 File Encoding         : 65001

 Date: 23/12/2018 13:59:36
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for pay_info
-- ----------------------------
DROP TABLE IF EXISTS `pay_info`;
CREATE TABLE `pay_info` (
  `pid` int(10) NOT NULL AUTO_INCREMENT,
  `username` varchar(150) DEFAULT NULL,
  `d_price` decimal(10,2) DEFAULT NULL COMMENT '订单金额',
  `t_price` decimal(10,2) DEFAULT NULL COMMENT '真实付款',
  `email` varchar(150) DEFAULT NULL,
  `mobile` varchar(20) DEFAULT NULL,
  `type` int(10) DEFAULT NULL COMMENT '1支付宝，2微信，3QQ',
  `state` int(10) DEFAULT NULL COMMENT '0待支付，1：支付成功，2，审核中，3：拒绝支付 ',
  `rand_num` int(10) DEFAULT NULL COMMENT '转账备注随机码',
  `time` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`pid`),
  KEY `state` (`state`) USING BTREE,
  KEY `pid` (`pid`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8;

SET FOREIGN_KEY_CHECKS = 1;
