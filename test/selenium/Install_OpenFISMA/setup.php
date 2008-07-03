<?
/**
 * Setup for Install_OpenFISMA Selenium Test Case
 *
 * Lookup the target database credentials. Drop the schema and recreate
 * it. Return the database credentials to the browser in a form that
 * the Selenium script will recognize.
 */

echo "<html><head><title></title></head><body>
<div id='dbuser'>ci</div>
<div id='dbpass'>ci</div>
<div id='dbname'>ci</div>
</body></html>";
?>
