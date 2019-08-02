<!DOCTYPE html>
<html>

<body>

    <?php
    /**
     * PHP Wiki to Create the Wiki Database
     *
     * Save the Wiki user name password, database name
     * to c:\htconfig\dbConfigwikiadd.php
     *
     * PHP Version: 7.2.9
     *
     * @package  Wiki
     * @author   Michael Pritchard <twitter: @MikeAPritch>
     * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
     * @link     http://localhost/WikiV2/public_html/initialise/initialiseWikiDatabase.php
     * File:    initialiseWikiDatabase.php
     * Date:    2018-10-05
     *
     * This script creates the Wiki Database
     * using MySQL PDO
     *
     * =====================================
     */

    require '..\..\htconfig\dbConfigwiki.php';

    $host = "127.0.0.1";      // localhost on LINUX

    $root = "root";           // Account with permission to create DBs
    $root_password = "MySQL";      // Password for the account.

    try {
        $dbh = new PDO("mysql:host=$host", $root, $root_password);

        $dbh->exec(
            "CREATE DATABASE `$dbname`;
            CREATE USER '$user'@'localhost' IDENTIFIED BY '$password';
            GRANT ALL ON `$dbname`.* TO '$user'@'localhost';
            FLUSH PRIVILEGES;"
        );
        echo 'Script run OK, database created';
    } catch (PDOException $e) {
        echo "DB ERROR: " . $e->getMessage();
        echo '<br/>';
        die(print_r($dbh->errorInfo(), true));
    }


    $pdo = null;

    ?>
</body>

</html>