<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class GenerateJwtSecret extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jwt:generate-secret';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a new JWT secret key and update .env file';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $key = 'JWT_SECRET='.base64_encode(random_bytes(32));
        
        $envFile = base_path('.env');
        
        if (File::exists($envFile)) {
            $contents = File::get($envFile);
            
            // Update or add JWT_SECRET
            if (preg_match('/^JWT_SECRET=.*$/m', $contents)) {
                $contents = preg_replace('/^JWT_SECRET=.*$/m', $key, $contents);
            } else {
                $contents .= "\n" . $key . "\n";
            }
            
            File::put($envFile, $contents);
            
            $this->info('JWT secret has been set successfully.');
            $this->line('<comment>'.$key.'</comment>');
            
            // Clear configuration cache
            if (function_exists('config')) {
                config(['jwt.secret' => $key]);
            }
            
            return 0;
        }
        
        $this->error('.env file not found!');
        return 1;
    }
}
