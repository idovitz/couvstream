-- phpMyAdmin SQL Dump
-- version 2.8.1-Debian-1~dapper1
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Generatie Tijd: 07 Jan 2008 om 09:42
-- Server versie: 5.0.22
-- PHP Versie: 5.1.2
-- 
-- Database: `couvstream`
-- 

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `cameras`
-- 

CREATE TABLE `cameras` (
  `cid` int(4) NOT NULL,
  `name` varchar(255) collate utf8_unicode_ci NOT NULL,
  `ip` varchar(15) collate utf8_unicode_ci NOT NULL,
  `ptz` tinyint(1) NOT NULL,
  `blocked` tinyint(1) NOT NULL default '0',
  `format_cif` tinyint(1) NOT NULL,
  `child_name` varchar(255) collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`cid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `decoders`
-- 

CREATE TABLE `decoders` (
  `did` int(4) NOT NULL,
  `name` varchar(255) collate utf8_unicode_ci NOT NULL,
  `ip` varchar(15) collate utf8_unicode_ci NOT NULL,
  `cid` int(4) default NULL,
  PRIMARY KEY  (`did`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `groups`
-- 

CREATE TABLE `groups` (
  `uid` varchar(255) collate utf8_unicode_ci NOT NULL,
  `cid` int(4) NOT NULL,
  PRIMARY KEY  (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- 
-- Gegevens worden uitgevoerd voor tabel `groups`
-- 

INSERT INTO `groups` (`uid`, `cid`) VALUES ('viewer', -1),
('admin', 0);

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `sessions`
-- 

CREATE TABLE `sessions` (
  `sid` varchar(40) collate utf8_unicode_ci NOT NULL,
  `uid` varchar(20) collate utf8_unicode_ci NOT NULL,
  `expiration_date` datetime NOT NULL,
  `ip` varchar(15) collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`sid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `tracking`
-- 

CREATE TABLE `tracking` (
  `uid` varchar(255) collate utf8_unicode_ci NOT NULL,
  `client_ip_address` varchar(15) collate utf8_unicode_ci NOT NULL,
  `download_date` datetime NOT NULL,
  `download_path` varchar(255) collate utf8_unicode_ci NOT NULL,
  `download_size` int(8) NOT NULL,
  PRIMARY KEY  (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `users`
-- 

CREATE TABLE `users` (
  `uid` varchar(255) collate utf8_unicode_ci NOT NULL,
  `password` varchar(255) collate utf8_unicode_ci NOT NULL,
  `name` varchar(255) collate utf8_unicode_ci NOT NULL,
  `expiredate` datetime default NULL,
  `startdate` datetime default NULL,
  PRIMARY KEY  (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- 
-- Gegevens worden uitgevoerd voor tabel `users`
-- 

INSERT INTO `users` (`uid`, `password`, `name`, `expiredate`, `startdate`) VALUES ('admin', 'changeme', '', '2030-12-31 00:00:00', '0000-00-00 00:00:00'),
('viewer', 'changeme', '', '2030-12-31 00:00:00', '0000-00-00 00:00:00');

