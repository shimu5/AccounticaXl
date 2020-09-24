/*
Navicat MySQL Data Transfer

Source Server         : My PC
Source Server Version : 50141
Source Host           : localhost:3306
Source Database       : ngo_v2_bp

Target Server Type    : MYSQL
Target Server Version : 50141
File Encoding         : 65001

Date: 2014-03-16 11:14:14
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `job`
-- ----------------------------
DROP TABLE IF EXISTS `job`;
CREATE TABLE `job` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `job_name` varchar(100) NOT NULL,
  `job_type` varchar(20) NOT NULL COMMENT 'once, recurring',
  `schedule_id` int(11) NOT NULL,
  `command_text` text,
  `command_type` varchar(20) NOT NULL,
  `last_runtime` datetime DEFAULT NULL,
  `last_status` text,
  `next_run_in_day` date DEFAULT NULL,
  `is_deleted` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of job
-- ----------------------------

-- ----------------------------
-- Table structure for `job_history`
-- ----------------------------
DROP TABLE IF EXISTS `job_history`;
CREATE TABLE `job_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `schedule_id` int(11) NOT NULL,
  `start_date_time` datetime NOT NULL,
  `end_date_time` datetime DEFAULT NULL,
  `status` tinyint(4) NOT NULL,
  `return_text` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `schedule_id_time_unique` (`schedule_id`,`start_date_time`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of job_history
-- ----------------------------

-- ----------------------------
-- Table structure for `schedule`
-- ----------------------------
DROP TABLE IF EXISTS `schedule`;
CREATE TABLE `schedule` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `schedule_name` varchar(100) NOT NULL,
  `schedule_type` varchar(20) DEFAULT NULL COMMENT 'Daily, Weekly, Monthly',
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `repeat_in_type` varchar(10) DEFAULT NULL COMMENT 'Min, Hour',
  `repeat_in_num` int(11) NOT NULL DEFAULT '0' COMMENT '1-24, 1-60',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of schedule
-- ----------------------------

-- ----------------------------
-- Table structure for `schedule_daily`
-- ----------------------------
DROP TABLE IF EXISTS `schedule_daily`;
CREATE TABLE `schedule_daily` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `schedule_id` int(11) NOT NULL,
  `recurs_in` int(11) NOT NULL COMMENT 'num_of_days',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of schedule_daily
-- ----------------------------

-- ----------------------------
-- Table structure for `schedule_monthly`
-- ----------------------------
DROP TABLE IF EXISTS `schedule_monthly`;
CREATE TABLE `schedule_monthly` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `schedule_id` int(11) NOT NULL,
  `recurs_in` int(11) NOT NULL COMMENT 'every (1-12) months',
  `month_days` text NOT NULL COMMENT 'serialize data of month day',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of schedule_monthly
-- ----------------------------

-- ----------------------------
-- Table structure for `schedule_times`
-- ----------------------------
DROP TABLE IF EXISTS `schedule_times`;
CREATE TABLE `schedule_times` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `schedule_id` int(11) NOT NULL,
  `time` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of schedule_times
-- ----------------------------

-- ----------------------------
-- Table structure for `schedule_weekly`
-- ----------------------------
DROP TABLE IF EXISTS `schedule_weekly`;
CREATE TABLE `schedule_weekly` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `schedule_id` int(11) NOT NULL,
  `recurs_in` tinyint(4) NOT NULL COMMENT 'every (1-5) weeks',
  `week_days` text NOT NULL COMMENT 'serialize (sat-fri day)',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of schedule_weekly
-- ----------------------------
