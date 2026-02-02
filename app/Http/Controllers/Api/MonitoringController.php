<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class MonitoringController extends Controller
{
    public function index()
    {
        try {
            // Cek Koneksi Database
            DB::connection()->getPdo();
            $dbStatus = 'Connected';

            // Cek Koneksi Redis
            Cache::store('redis')->get('test');
            $redisStatus = 'Connected';

            return response()->json([
                'status' => 'success',
                'monitoring' => [
                    'database' => $dbStatus,
                    'redis' => $redisStatus,
                    'server_time' => now()->toDateTimeString(),
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'System issue: ' . $e->getMessage()
            ], 500);
        }
    }
}