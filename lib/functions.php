<?php
/**
 * Backup Function Library
 * File: lib/functions.php
 */

require_once __DIR__ . '/database.php';

class Backup {

    public static function run() {
        $host = DB_HOST;
        $user = DB_USER;
        $pass = DB_PASS;
        $dbname = DB_NAME;

        // Folder backup
        $backupDir = __DIR__ . '/../backups';
        if (!is_dir($backupDir)) {
            mkdir($backupDir, 0777, true);
        }

        // Nama file dengan timestamp
        $filename = 'backup_' . date('Y-m-d_H-i-s') . '.sql';
        $filepath = $backupDir . '/' . $filename;

        // Command mysqldump
        $command = "mysqldump --column-statistics=0 -h $host -u $user";

        if (!empty($pass)) {
            $command .= " -p$pass";
        }

        $command .= " $dbname > \"$filepath\"";

        exec($command, $output, $result);

        if ($result === 0 && file_exists($filepath)) {
            return [
                'success' => true,
                'file' => $filename
            ];
        }

        return [
            'success' => false
        ];
    }

    public static function getFiles() {
        $files = glob(__DIR__ . '/../backups/*.sql');
        return array_map('basename', $files);
    }
}
