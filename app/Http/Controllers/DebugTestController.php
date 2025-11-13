<?php

namespace App\Http\Controllers;

use App\Services\DebugLogger;
use Illuminate\Http\Request;

class DebugTestController extends Controller
{
    private DebugLogger $debugLogger;

    public function __construct(DebugLogger $debugLogger)
    {
        $this->debugLogger = $debugLogger;
    }

    /**
     * デバッグ機能のテスト用メソッド
     */
    public function test()
    {
        // デバッグモードが有効か確認
        if (!$this->debugLogger->isDebugMode()) {
            return response()->json([
                'message' => 'デバッグモードが無効です。.envファイルでAPP_DEBUG_MODE=trueを設定してください。',
                'debug_mode' => false
            ]);
        }

        // 各種ログレベルでテスト
        $this->debugLogger->info('これはインフォメーションログです', ['user_id' => 1, 'action' => 'test']);
        $this->debugLogger->warning('これは警告ログです', ['warning_type' => 'test']);
        $this->debugLogger->error('これはエラーログです', ['error_code' => 'TEST_ERROR']);
        $this->debugLogger->debug('これはデバッグログです', ['debug_data' => ['key' => 'value']]);

        // 本日のログを取得
        $todayLogs = $this->debugLogger->getTodayLogs();

        return response()->json([
            'message' => 'デバッグログを書き込みました',
            'debug_mode' => true,
            'log_file' => $this->debugLogger->getTodayLogPath(),
            'logs' => $todayLogs
        ]);
    }

    /**
     * 古いログファイルを削除
     */
    public function clean(Request $request)
    {
        $days = $request->get('days', 30);
        $this->debugLogger->cleanOldLogs($days);

        return response()->json([
            'message' => "{$days}日より古いログファイルを削除しました"
        ]);
    }
}
