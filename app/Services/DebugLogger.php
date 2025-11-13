<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Config;
use Carbon\Carbon;

class DebugLogger
{
    private string $debugPath;
    private bool $debugMode;

    public function __construct()
    {
        $this->debugPath = storage_path('debug');
        $this->debugMode = Config::get('app.debug_mode', false);
    }

    /**
     * デバッグログを書き込む
     *
     * @param string $message ログメッセージ
     * @param array $context 追加コンテキスト
     * @param string $level ログレベル (info, warning, error, debug)
     * @return void
     */
    public function log(string $message, array $context = [], string $level = 'info'): void
    {
        if (!$this->debugMode) {
            return;
        }

        $timestamp = Carbon::now()->format('Y-m-d H:i:s');
        $logFile = $this->debugPath . '/' . date('Y-m-d') . '_debug.log';
        
        $contextStr = empty($context) ? '' : ' | Context: ' . json_encode($context, JSON_UNESCAPED_UNICODE);
        $logEntry = "[{$timestamp}] [{$level}] {$message}{$contextStr}" . PHP_EOL;
        
        File::append($logFile, $logEntry);
    }

    /**
     * インフォログ
     */
    public function info(string $message, array $context = []): void
    {
        $this->log($message, $context, 'info');
    }

    /**
     * 警告ログ
     */
    public function warning(string $message, array $context = []): void
    {
        $this->log($message, $context, 'warning');
    }

    /**
     * エラーログ
     */
    public function error(string $message, array $context = []): void
    {
        $this->log($message, $context, 'error');
    }

    /**
     * デバッグログ
     */
    public function debug(string $message, array $context = []): void
    {
        $this->log($message, $context, 'debug');
    }

    /**
     * デバッグモードが有効か確認
     */
    public function isDebugMode(): bool
    {
        return $this->debugMode;
    }

    /**
     * 古いログファイルを削除（指定日数より古いファイル）
     *
     * @param int $days 保持日数
     * @return void
     */
    public function cleanOldLogs(int $days = 30): void
    {
        if (!$this->debugMode) {
            return;
        }

        $files = File::glob($this->debugPath . '/*_debug.log');
        $cutoffDate = Carbon::now()->subDays($days);

        foreach ($files as $file) {
            $fileDate = Carbon::createFromFormat('Y-m-d', basename($file, '_debug.log'));
            if ($fileDate && $fileDate->lt($cutoffDate)) {
                File::delete($file);
            }
        }
    }

    /**
     * 本日のログファイルパスを取得
     */
    public function getTodayLogPath(): string
    {
        return $this->debugPath . '/' . date('Y-m-d') . '_debug.log';
    }

    /**
     * 本日のログ内容を取得
     */
    public function getTodayLogs(): string
    {
        $logFile = $this->getTodayLogPath();
        return File::exists($logFile) ? File::get($logFile) : '';
    }
}
