<?php
/**
 * Runtime end, include at the end of a script to dispay the runtime data,
 * use fu_runtime.php at the beginning of the script to initialise.
 *
 * PHP Version: 7.2.10
 *
 * @category PHP
 * @package  Wiki
 * @author   Michael Pritchard <twitter: @MikeAPritch>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     http://localhost/wiki/includes/runtime_end.php
 * File:  includes\runtime_end.php
 * Date:  2018-09-18
 * ==============================================================================
 */

$time_end = microtime(true);

//dividing with 60 will give the execution time in minutes otherwise seconds
$execution_time = ($time_end - $time_start);

//execution time of the script
echo '<p><b>Total Execution Time:</b> '.gmdate("H:i:s", $execution_time) .'</p>';
// if you get weird results, use number_format((float) $execution_time, 10)

$ru = getrusage();
echo "<p>This process used " . rutime($ru, $rustart, "utime")/1000 .
    " s for its computations</p>";
echo "<p>It spent " . rutime($ru, $rustart, "stime")/1000 .
    " s in system calls</p>";

?>