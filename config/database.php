<?php
/**
 * HMI IT Telkom - PDO Database Connection
 * Singleton pattern untuk koneksi MySQL
 * 
 * ENVIRONMENT: cPanel Hosting (hmiittelkom.com)
 */

define('DB_HOST', 'localhost');
define('DB_NAME', 'hmiq9437_web-utama');
define('DB_USER', 'hmiq9437_eka');
define('DB_PASS', 'R@zhed12');
define('DB_CHARSET', 'utf8mb4');

/**
 * Get PDO database connection instance
 * @return PDO
 */
function getDB(): PDO
{
    static $pdo = null;

    if ($pdo === null) {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            die('<div style="font-family:Inter,sans-serif;padding:40px;text-align:center;">'
                . '<h2 style="color:#c0392b;">Koneksi Database Gagal</h2>'
                . '<p>Pastikan database <strong>' . DB_NAME . '</strong> telah dibuat dan kredensial benar.</p>'
                . '<p style="color:#999;font-size:12px;">Error: ' . htmlspecialchars($e->getMessage()) . '</p>'
                . '</div>');
        }
    }

    return $pdo;
}
