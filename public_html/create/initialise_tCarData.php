<!DOCTYPE html>
<html>
<body>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<?php
/**
 * PHP Wiki to Create the Car Upgrades tree table
 *
 * PHP Version: 7.2.10 (probably 5.3+)
 *
 * @category PHP
 * @package  Wiki
 * @author   Michael Pritchard <twitter: @MikeAPritch>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://wikicars.000webhostapp.com/initialise/initialise_tCarData.php
 * File:    initialise_tCarData.php
 * Date:    2018-10-05
 *
 * This script creates the tCarData table
 * Using CarData.csv file in the same directory as the script.
 *
 * Using MySQL PDO
 * =====================================
 */

//header('Content-Type: text/html; charset=utf-8');

$filename = "CarData";
$tableName = "tCarData";
{
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
echo '$numRows : '.$numRows.'<br />';
}
// PDO connection variables
require '../../htconfig/dbConfigwiki.php';

// See: https://phpdelusions.net/pdo_examples/connect_to_mysql
// use 'utf8mb4'
try { // PDO Connection string
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=$charset",
        $user, $password, $options
    );
}
catch(PDOException $e) {
    echo "Error unable to connect: <br/>" . $e->getMessage();
    echo "<br/>";
}

{// DROP table
$drop_SQL = "DROP TABLE ".$tableName;

try {   // PDO Connection string
    $stmt = $pdo->prepare($drop_SQL);
    $stmt->execute();
    echo 'Table Dropped.<br /><br />';
}
catch(PDOException $e) {
    $e=null;
    echo '<span style="color:red; ">
            FAILED to DROP table.'.$tableName.'</span>
            <br /><br />';
    //echo "Error: " . $e->getMessage();
}
}
/*
CREATE TABLE IF NOT EXISTS CarData (
    `Car` VARCHAR(53) CHARACTER SET utf8,
    `BasePR` NUMERIC(4, 1),
    `RD_PR` NUMERIC(4, 1),
    `RD_Upg` INT,
    `FU_PR` NUMERIC(4, 1),
    `FU_Upg` INT,
    `Seq` INT
);
INSERT INTO CarData VALUES
    ('ARIEL ATOM 3.5',44.4,54.2,3222222,62.9,8444333,1),

 */
// SQL script to create the table
$createTable_SQL =  "CREATE TABLE $tableName (
    `ID`            INT ( 7 ) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `Car`           VARCHAR( 60 )  CHARACTER SET utf8,
    `BasePR`        NUMERIC(4, 1),
    `RD_PR`         NUMERIC(4, 1),
    `RD_Upg`        INT,
    `FU_PR`         NUMERIC(4, 1),
    `FU_Upg`        INT,
    `Seq`           INT
);";

echo "<p>About to create: $tableName<br/>$createTable_SQL</p><br/>";

try {   // PDO Connection string
    $stmt = $pdo->prepare($createTable_SQL);
    $stmt->execute();
    echo '<p>' . $tableName . ' created</p>';
}
catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}

echo "<br /><hr /><br />";
// Prepare the data from the csv file to be inserted into SQL
$table_SQLinsert = "INSERT INTO $tableName (
    `Car`,
    `BasePR`,
    `RD_PR`,
    `RD_Upg`,
    `FU_PR`,
    `FU_Upg`,
    `Seq`
    )
    VALUES (
    :Car,
    :BasePR,
    :RD_PR,
    :RD_Upg,
    :FU_PR,
    :FU_Upg,
    :Seq
    );";
$stmt = $pdo->prepare($table_SQLinsert);
// Bind the variable, that will be used, to the SQL Value
$stmt->bindParam(':Car',    $car,    PDO::PARAM_STR, 60);
$stmt->bindParam(':BasePR', $basePR, PDO::PARAM_STR, 5);
$stmt->bindParam(':RD_PR',  $rdPR,   PDO::PARAM_STR, 5);
$stmt->bindParam(':FU_PR',  $fuPR,   PDO::PARAM_STR, 5);
$stmt->bindParam(':RD_Upg', $rdUpg,  PDO::PARAM_INT);
$stmt->bindParam(':FU_Upg', $fuUpg,  PDO::PARAM_INT);
$stmt->bindParam(':Seq',    $seq,    PDO::PARAM_INT);


$i = 0;
// Loop through each line of the CSV file, add each item to the SQL database
//( [0] => ARIEL [1] => ARIEL ATOM 3.5 [2] => 1 [3] => Engine [4] => Cold Air Intake
// [5] => 30 Minutes [6] => 0.6 [7] => 13500 [8] => 3 [9] => 5 [10] => 14 )
// :BasePR,
// :RD_PR,
// :RD_Upg,
// :FU_PR,
// :FU_Upg,
// :Seq

foreach ($tableData as $row) {
    $car =      $row[0];
    $basePR =   $row[1];
    $rdPR =     $row[2];
    $rdUpg =    $row[3];
    $fuPR =     $row[4];
    $fuUpg =    $row[5];
    $seq =      $row[6];
    {
        $stmt->execute();
        $i ++;      // increase the count, if successful
    }
    // catch(PDOException $e)
    // {
    //     echo "<br/>Error adding tcarFU:<br/>" . $e->getMessage();
    //     echo "<p>Car: $car</p>";
    //     print_r($row);
    // }
}
// Confirm the number of cars added
echo "<p>$i cars added to $tableName</p>";

//Close PDO connection
$pdo = null;

?>

</body>
</html>