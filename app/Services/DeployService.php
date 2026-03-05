<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class DeployService
{
    /**
     * Deploy a project to shared hosting via FTP.
     */
    public function deployViaFtp(array $credentials, string $projectPath): array
    {
        $steps = [];

        try {
            // 1. Connect to FTP
            $steps[] = $this->step('Connecting to FTP server...', 'running');
            $ftp = $this->connectFtp($credentials);

            if (!$ftp) {
                return $this->deployError('FTP connection failed. Check your host, username, and password.', $steps);
            }
            $steps[0] = $this->step('FTP connected to ' . $credentials['host'], 'done');

            // 2. Upload files
            $steps[] = $this->step('Uploading project files...', 'running');
            $uploaded = $this->uploadDirectory($ftp, $projectPath, $credentials['remote_path']);
            $steps[count($steps) - 1] = $this->step("Uploaded {$uploaded} files", 'done');

            // 3. Configure .env
            $steps[] = $this->step('Configuring .env on server...', 'running');
            $this->uploadEnv($ftp, $credentials);
            $steps[count($steps) - 1] = $this->step('.env configured with DB credentials', 'done');

            // 4. Set permissions
            $steps[] = $this->step('Setting file permissions...', 'running');
            $this->setPermissions($ftp, $credentials['remote_path']);
            $steps[count($steps) - 1] = $this->step('Permissions set (755/644)', 'done');

            // 5. Close connection
            ftp_close($ftp);

            $steps[] = $this->step('Deployment complete! ✅', 'done');

            return [
                'success' => true,
                'message' => 'Deployed successfully to ' . $credentials['host'],
                'steps'   => $steps,
                'url'     => 'http://' . $credentials['host'],
            ];

        } catch (\Exception $e) {
            Log::error('Deploy error: ' . $e->getMessage());
            return $this->deployError($e->getMessage(), $steps);
        }
    }

    /**
     * Validate FTP credentials without deploying.
     */
    public function testConnection(array $credentials): bool
    {
        $ftp = $this->connectFtp($credentials);
        if ($ftp) {
            ftp_close($ftp);
            return true;
        }
        return false;
    }

    /**
     * Connect to FTP and return resource.
     */
    private function connectFtp(array $credentials): mixed
    {
        $ftp = ftp_connect($credentials['host'], $credentials['port'] ?? 21, 30);
        if (!$ftp) return false;

        $loggedIn = ftp_login($ftp, $credentials['username'], $credentials['password']);
        if (!$loggedIn) {
            ftp_close($ftp);
            return false;
        }

        ftp_pasv($ftp, true); // Passive mode for shared hosting firewalls
        return $ftp;
    }

    /**
     * Recursively upload a local directory to FTP.
     */
    private function uploadDirectory(mixed $ftp, string $localPath, string $remotePath): int
    {
        $count = 0;

        // Create remote directory if it doesn't exist
        @ftp_mkdir($ftp, $remotePath);

        $items = scandir($localPath);
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') continue;

            // Skip vendor, node_modules, .git
            if (in_array($item, ['vendor', 'node_modules', '.git', '.env'])) continue;

            $localItem  = $localPath . '/' . $item;
            $remoteItem = $remotePath . '/' . $item;

            if (is_dir($localItem)) {
                $count += $this->uploadDirectory($ftp, $localItem, $remoteItem);
            } else {
                if (ftp_put($ftp, $remoteItem, $localItem, FTP_BINARY)) {
                    $count++;
                }
            }
        }

        return $count;
    }

    /**
     * Upload .env file with database credentials from user's input.
     */
    private function uploadEnv(mixed $ftp, array $credentials): void
    {
        $envContent = view('deploy.env-template', [
            'db_host'     => '127.0.0.1',
            'db_name'     => $credentials['db_name'],
            'db_user'     => $credentials['db_username'],
            'db_password' => $credentials['db_password'],
            'app_url'     => 'http://' . $credentials['host'],
        ])->render();

        // Write temp file then upload
        $tempFile = tempnam(sys_get_temp_dir(), 'ryaan_env_');
        file_put_contents($tempFile, $envContent);
        ftp_put($ftp, $credentials['remote_path'] . '/.env', $tempFile, FTP_ASCII);
        unlink($tempFile);
    }

    /**
     * Set correct permissions for Laravel on shared hosting.
     */
    private function setPermissions(mixed $ftp, string $remotePath): void
    {
        // Directories: 755, Files: 644
        ftp_chmod($ftp, 0755, $remotePath . '/storage');
        ftp_chmod($ftp, 0755, $remotePath . '/bootstrap/cache');
        ftp_chmod($ftp, 0644, $remotePath . '/.env');
    }

    private function step(string $message, string $status): array
    {
        return ['message' => $message, 'status' => $status];
    }

    private function deployError(string $message, array $steps): array
    {
        $steps[] = $this->step('Deployment failed: ' . $message, 'error');
        return ['success' => false, 'message' => $message, 'steps' => $steps];
    }
}
