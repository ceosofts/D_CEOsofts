<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class NpmInstall extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'npm:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Display instructions for installing npm dependencies';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=======================================================');
        $this->info('                 NPM INSTALLATION                       ');
        $this->info('=======================================================');
        $this->info('');
        
        // Check if package.json exists
        if (!File::exists(base_path('package.json'))) {
            $this->error('package.json not found!');
            $this->info('Please create a package.json file first.');
            return Command::FAILURE;
        }
        
        $this->info('To install Node.js dependencies, please run the following commands:');
        $this->info('');
        $this->line('cd ' . base_path());
        $this->line('npm install');
        $this->info('');
        $this->info('After installation is complete, you can run:');
        $this->info('');
        $this->line('npm run dev     # For development with hot-reload');
        $this->line('npm run build   # For production build');
        $this->info('');
        $this->info('If you encounter any issues with the Vite configuration,');
        $this->info('you can update your vite.config.js and package.json files.');
        $this->info('');

        // Display current package.json content
        $this->info('Current package.json content:');
        $packageJson = json_decode(File::get(base_path('package.json')), true);
        $this->table(
            ['Key', 'Value'],
            $this->formatPackageJson($packageJson)
        );
        
        return Command::SUCCESS;
    }
    
    /**
     * Format package.json for display in console table
     */
    protected function formatPackageJson($packageJson, $prefix = '')
    {
        $result = [];
        
        foreach ($packageJson as $key => $value) {
            $fullKey = $prefix ? "$prefix.$key" : $key;
            
            if (is_array($value)) {
                if ($key === 'dependencies' || $key === 'devDependencies') {
                    $depsArray = [];
                    foreach ($value as $depKey => $depValue) {
                        $depsArray[] = "$depKey: $depValue";
                    }
                    $result[] = [$fullKey, implode(", ", $depsArray)];
                } else {
                    $result = array_merge($result, $this->formatPackageJson($value, $fullKey));
                }
            } else {
                $result[] = [$fullKey, $value];
            }
        }
        
        return $result;
    }
}
