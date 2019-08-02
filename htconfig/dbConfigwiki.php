<?php

/**
 * Connection information for wikidata database
 *
 * PHP Version 7.1.19 (probably 5.3)
 *
 * @category WikiCar
 * @package  Wiki
 * @author   Michael Pritchard <twitter: @MikeAPritch>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     na
 * File:     dbConfigWiki.php
 * Date:     2018-10-05
 */

$host=      "localhost";  // localhost on LINUX
// $host =      '127.0.0.1';    // 127.0.0.1 on Windows
$dbname =    'wikidb';    // Name of your database
$user =      'wikiUser';    // Your DB user name
$password =  'password';    // Your DB password

$charset = "utf8mb4";
$options = [
   PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
   PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
   PDO::ATTR_EMULATE_PREPARES   => false,
   PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
];
