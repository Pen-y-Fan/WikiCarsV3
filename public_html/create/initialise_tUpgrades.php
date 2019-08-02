<!DOCTYPE html>
<html>

<body>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <?php
    /**
     * PHP Wiki to Create the Car Upgrades tree table
     *
     * PHP Version: 7.2.2
     *
     * @category PHP
     * @package  Wiki
     * @author   Michael Pritchard <twitter: @MikeAPritch>
     * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
     * @link     http://localhost/wiki/initialise/initialiseUpgradesTree.php
     * public_html\initialise\initialise_tUpgrades.php
     * File:  initialise_tUpgrades.php
     * Date:  2018-08-05
     *
     * This script creates the Car Fully Upgraded table
     * Using CarFU.csv file in the same directory as the script.
     *
     * Using MySQL PDO
     * Make Car Seq Upgrade Description Duration PR RD GC
     * VARCHAR VARCHAR INT VARCHAR VARCHAR VARCHAR VARCHAR INT INT
     * 13 53 1 14 47 18 3 7 3

     * =====================================
     */

    //header('Content-Type: text/html; charset=utf-8');

    // MySQL connection variables
    // MySQLi or PDO
    require '../../htconfig/dbConfigwiki.php';
    ini_set('max_execution_time', 300); //300 seconds = 5 minutes
    // See: https://phpdelusions.net/pdo_examples/connect_to_mysql
    // use 'utf8mb4'
    try { // PDO Connection string
        $pdo = new PDO(
            "mysql:host=$host;dbname=$dbname;charset=$charset",
            $user,
            $password,
            $options
        );
    } catch (PDOException $e) {
        echo "Error unable to connect: <br/>" . $e->getMessage();
        echo "<br/>";
    }
    $filename = "CarsUpgrades";
    $tableName = "tUpgrades"; {
        // read CSV data file

        $file = fopen("$filename.csv", "r");
        $i = 0;
        while (!feof($file)) {
            $thisLine = utf8_encode(fgets($file));
            $tableData[$i] = explode(",", $thisLine);
            $i++;
        }

        fclose($file);

        /*
echo "<p>";
print_r($tableData);
echo "<p>";
*/

        $numRows = sizeof($tableData);
        echo '$numRows : ' . $numRows . '<br />';
    } { // DROP table
        $drop_SQL = "DROP TABLE " . $tableName;

        try {   // PDO Connection string
            $stmt = $pdo->prepare($drop_SQL);
            $stmt->execute();
            echo 'Table Dropped.<br /><br />';
        } catch (PDOException $e) {
            echo '<span style="color:red; ">
            FAILED to DROP table.' . $tableName . '</span>
            <br /><br />';
            echo "Error: " . $e->getMessage();
        }
    }
    /*
 * Make VARCHAR 13
 * Car VARCHAR 53
 * Seq INT 1
 * Description VARCHAR 47
 * Duration VARCHAR 18
 * PR VARCHAR 3
 * RD INT 7
 * GC INT 3
 * Upgrade VARCHAR 14
 */
    // SQL script to create the table
    $createTable_SQL =  "CREATE TABLE $tableName (
    ID              INT ( 7 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    Car             VARCHAR( 60 ) NOT NULL,
    Make            VARCHAR ( 13 ),
    Seq             INT ( 2 ),
    Upgrade         VARCHAR ( 14 ),
    Description     VARCHAR ( 47 ),
    Duration        VARCHAR ( 18 ),
    PR              VARCHAR ( 5 ),
    RD              VARCHAR ( 7 ),
    GC              INT ( 3 )
);";

    echo "<p>About to create: $tableName<br/>$createTable_SQL</p><br/>";

    try {   // PDO Connection string
        $stmt = $pdo->prepare($createTable_SQL);
        $stmt->execute();
        echo '<p>' . $tableName . ' created</p>';
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }

    echo "<br /><hr /><br />";
    // Prepare the data from the csv file to be inserted into SQL
    $table_SQLinsert = "INSERT INTO $tableName (
    Car,
    Make,
    Seq,
    Upgrade,
    Description,
    Duration,
    PR,
    RD,
    GC
    )
    VALUES (
    :Car,
    :Make,
    :Seq,
    :Upgrade,
    :Description,
    :Duration,
    :PR,
    :RD,
    :GC
    );";
    $stmt = $pdo->prepare($table_SQLinsert);
    // Bind the variable, that will be used, to the SQL Value
    $stmt->bindParam(':Car', $car, PDO::PARAM_STR, 60);
    $stmt->bindParam(':Make', $make, PDO::PARAM_STR, 13);
    $stmt->bindParam(':Seq', $seq, PDO::PARAM_INT);
    $stmt->bindParam(':Upgrade', $upgrade, PDO::PARAM_STR, 14);
    $stmt->bindParam(':Description', $description, PDO::PARAM_STR, 47);
    $stmt->bindParam(':Duration', $duration, PDO::PARAM_STR, 18);
    $stmt->bindParam(':PR', $pr, PDO::PARAM_STR, 5);
    $stmt->bindParam(':RD', $rd, PDO::PARAM_INT);
    $stmt->bindParam(':GC', $gc, PDO::PARAM_INT);

    $carCount = 0;
    $currentCar = '';
    $i = 0;
    // Loop through each line of the CSV file, add each item to the SQL database
    //( [0] => ARIEL [1] => ARIEL ATOM 3.5 [2] => 1 [3] => Engine [4] => Cold Air Intake
    // [5] => 30 Minutes [6] => 0.6 [7] => 13500 [8] => 3 [9] => 5 [10] => 14 )
    foreach ($tableData as $row) {
        $make = $row[0];
        $car = $row[1];
        $seq = $row[2];
        $upgrade =  $row[3];
        $description = $row[4];
        $duration = $row[5];
        $pr = $row[6];
        $rd = $row[7];
        $gc = $row[8];
        //Check for a blank RD, change to 0
        if ($rd == '') {
            $rd = 0;
        }
        if ($currentCar != $car) {
            $carCount++;
            echo "$carCount - Car: $car<br/>";
            $currentCar = $car;
        }

        try {
            $stmt->execute();
            $i++;      // increase the count, if successful
        } catch (PDOException $e) {
            echo "<br/>Error adding tcarFU:<br/>" . $e->getMessage();
            echo "<p>Car: $car</p>";
            print_r($row);
        }
    }
    // Confirm the number of cars added
    echo "<p>$i upgrades added to $tableName</p>";
    echo "<p>$carCount cars added</p>";
    $pdo = null;



    ?>

</body>

</html>