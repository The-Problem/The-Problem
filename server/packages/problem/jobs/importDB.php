<?php
class ImportDBJob implements IJob {
    public function startexecute($args) {
        echo "Dropping all tables...\n";

        Connection::connect();
        $con = Connection::getconnection();

        $con->multi_query("
        SET FOREIGN_KEY_CHECKS = 0;
        SET @tables = NULL;
        SELECT GROUP_CONCAT('`', table_schema, '`.`', table_name, '`') INTO @tables
          FROM information_schema.tables
          WHERE table_schema = '" . $con->real_escape_string(Connection::$database) . "';
        SET @tables = CONCAT('DROP TABLE ', @tables);
        PREPARE stmt FROM @tables;
        EXECUTE stmt;
        DEALLOCATE PREPARE stmt;
        SET FOREIGN_KEY_CHECKS = 1;
        ");
        do $con->use_result(); while ($con->more_results() && $con->next_result());

        echo "Creating tables &amp; data...\n";
        $filename = getcwd() . DIRECTORY_SEPARATOR .  (count($args) ? $args[0] : "database.sql");
        echo "Using SQL file '" . htmlentities($filename) . "'\n";

        $file = file_get_contents($filename);

        // remove problematic line
        $file = str_replace("/*!40101 SET NAMES utf8mb4 */;", "", $file);

        $con->multi_query($file);
        do $con->use_result(); while ($con->more_results() && $con->next_result());
        if ($con->errno) echo "[ERROR] $con->error ($con->errno)\n";

        echo "Checking for presence of tables...\n";
        $res = $con->query("SHOW TABLES LIKE 'users'");
        if ($con->errno) echo "[ERROR] $con->error ($con->errno)\n";
        if ($res->num_rows <= 0) echo "[WARNING] It doesn't look like the tables imported correctly.\n";
        else echo "Everything looks OK.\n";
        echo "Finished importing tables.\n\n";
    }

    public function endexecute() {

    }
}