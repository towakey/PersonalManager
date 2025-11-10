<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class InstallController extends Controller
{
    public function show()
    {
        if (File::exists(storage_path('installed'))) {
            abort(404);
        }

        $requirements = $this->checkRequirements();
        $permissions = $this->checkPermissions();
        $all_checks_passed = !in_array(false, $requirements) && !in_array(false, $permissions);

        return view('install', compact('requirements', 'permissions', 'all_checks_passed'));
    }

    public function store(Request $request)
    {
        Log::info('Installation process started.');

        if (File::exists(storage_path('installed'))) {
            Log::warning('Installation already exists. Aborting.');
            abort(404);
        }

        $request->validate([
            'db_host' => 'required|string',
            'db_port' => 'required|numeric',
            'db_database' => 'required|string',
            'db_username' => 'required|string',
            'db_password' => 'nullable|string',
            'admin_name' => 'required|string|max:255',
            'admin_email' => 'required|string|email|max:255',
            'admin_password' => 'required|string|min:8|confirmed',
        ]);

        try {
            Log::info('Step 1: Creating .env file.');
            $this->createEnvFile($request);

            Log::info('Step 2: Setting up database connection.');
            $this->setupDatabaseConnection($request);

            Log::info('Step 3: Dropping all tables.');
            Schema::dropAllTables();

            Log::info('Step 4: Running migrations.');
            Artisan::call('migrate', ['--force' => true]);

            Log::info('Step 5: Creating admin user.');
            User::create([
                'name' => $request->admin_name,
                'email' => $request->admin_email,
                'password' => Hash::make($request->admin_password),
            ]);

            Log::info('Step 6: Creating installed flag file.');
            File::put(storage_path('installed'), 'PersonalManager was installed on ' . now());

            Log::info('Installation successful.');

        } catch (\Exception $e) {
            Log::error('Installation failed: ' . $e->getMessage());
            // Clean up .env if something went wrong
            if (File::exists(base_path('.env'))) {
                File::delete(base_path('.env'));
            }
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }

        return redirect(url('/install/complete'));
    }

    public function complete()
    {
        if (!File::exists(storage_path('installed'))) {
            return redirect(url('/install'));
        }
        return view('install-complete');
    }

    private function checkRequirements(): array
    {
        return [
            'PHP version >= 8.2' => version_compare(PHP_VERSION, '8.2', '>='),
            'BCMath PHP Extension' => extension_loaded('bcmath'),
            'Ctype PHP Extension' => extension_loaded('ctype'),
            'Fileinfo PHP Extension' => extension_loaded('fileinfo'),
            'JSON PHP Extension' => extension_loaded('json'),
            'Mbstring PHP Extension' => extension_loaded('mbstring'),
            'OpenSSL PHP Extension' => extension_loaded('openssl'),
            'PDO PHP Extension' => extension_loaded('pdo'),
            'Tokenizer PHP Extension' => extension_loaded('tokenizer'),
            'XML PHP Extension' => extension_loaded('xml'),
        ];
    }

    private function checkPermissions(): array
    {
        return [
            'storage/framework' => is_writable(storage_path('framework')),
            'storage/logs' => is_writable(storage_path('logs')),
            'bootstrap/cache' => is_writable(base_path('bootstrap/cache')),
        ];
    }

    private function createEnvFile(Request $request)
    {
        $envTemplate = File::get(base_path('.env.example'));

        $appKey = 'base64:'.base64_encode(Str::random(32));
        $appIdentifier = (string) Str::uuid();
        $baseUrl = $request->getSchemeAndHttpHost() . '/PersonalManager/public';

        $envContent = str_replace(
            [
                'APP_KEY=',
                'APP_URL=http://localhost',
                'DB_CONNECTION=sqlite',
                '# DB_HOST=127.0.0.1',
                '# DB_PORT=3306',
                '# DB_DATABASE=laravel',
                '# DB_USERNAME=root',
                '# DB_PASSWORD=',
            ],
            [
                'APP_KEY=' . $appKey,
                'APP_URL=' . $baseUrl,
                'DB_CONNECTION=mysql',
                'DB_HOST=' . $request->db_host,
                'DB_PORT=' . $request->db_port,
                'DB_DATABASE=' . $request->db_database,
                'DB_USERNAME=' . $request->db_username,
                'DB_PASSWORD=' . $request->db_password,
            ],
            $envTemplate
        );

        $envContent .= "\nAPP_IDENTIFIER=" . $appIdentifier . "\n";

        File::put(base_path('.env'), $envContent);

        // Reload the config with the new .env file
        Artisan::call('config:clear');
        Artisan::call('config:cache');
    }

    private function setupDatabaseConnection(Request $request)
    {
        // Connect without database
        config([
            'database.connections.mysql_setup' => [
                'driver' => 'mysql',
                'host' => $request->db_host,
                'port' => $request->db_port,
                'database' => null,
                'username' => $request->db_username,
                'password' => $request->db_password,
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
                'prefix' => '',
                'strict' => true,
                'engine' => null,
            ]
        ]);

        // Create the database
        DB::connection('mysql_setup')->statement('CREATE DATABASE IF NOT EXISTS ' . $request->db_database);

        // Set the default connection to the new database
        config([
            'database.default' => 'mysql',
            'database.connections.mysql.host' => $request->db_host,
            'database.connections.mysql.port' => $request->db_port,
            'database.connections.mysql.database' => $request->db_database,
            'database.connections.mysql.username' => $request->db_username,
            'database.connections.mysql.password' => $request->db_password,
        ]);

        // Purge the old connection
        DB::purge('mysql');
        // Reconnect to the database
        DB::reconnect('mysql');
    }
}
