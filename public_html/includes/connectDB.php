<?php
/**
 * PHP Wiki to conenct to MySQL POD
 *
 * PHP Version: 7.2.9
 *
 * @category PHP
 * @package  Wiki/includes
 * @author   Michael Pritchard <twitter: @MikeAPritch>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://wikicars.000webhostapp.com/includes/connectDB.php
 * File:    connectDB.php
 * Date:    2018-10-05
 *
 * Requires:
 * MySQL connection variables (MySQLi or PDO can be used)
 * ($host, $dbname, $user, $password, $options)
 *
 * v1.0 created the include
 * ==============================================================================
 */
// ($host, $dbname, $user, $password, $options)
require '../htconfig/dbConfigwiki.php';

// Test connection to the database
try {   // PDO Connection string
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $password, $options);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch(PDOException $e) {
    echo "Error unable to connect: <br/>" . $e->getMessage();
}

?>