<?php
/**
 * Cron job script to update overdue statuses
 * Run this script daily to automatically mark transactions as overdue
 * 
 * Usage: php update_overdue.php
 * Or add to crontab: 0 0 * * * /usr/bin/php /path/to/smart-library/cron/update_overdue.php
 */

require_once '../config/penalty_manager.php';

try {
    $penalty_manager = new PenaltyManager();
    $updated_count = $penalty_manager->updateOverdueStatus();
    
    echo "[" . date('Y-m-d H:i:s') . "] Updated {$updated_count} transactions to overdue status\n";
    
    // Log to file
    $log_file = '../logs/overdue_update.log';
    $log_dir = dirname($log_file);
    
    if (!is_dir($log_dir)) {
        mkdir($log_dir, 0755, true);
    }
    
    file_put_contents($log_file, "[" . date('Y-m-d H:i:s') . "] Updated {$updated_count} transactions to overdue status\n", FILE_APPEND | LOCK_EX);
    
} catch (Exception $e) {
    echo "[" . date('Y-m-d H:i:s') . "] Error: " . $e->getMessage() . "\n";
    
    // Log error
    $log_file = '../logs/overdue_update.log';
    $log_dir = dirname($log_file);
    
    if (!is_dir($log_dir)) {
        mkdir($log_dir, 0755, true);
    }
    
    file_put_contents($log_file, "[" . date('Y-m-d H:i:s') . "] Error: " . $e->getMessage() . "\n", FILE_APPEND | LOCK_EX);
}
?>
