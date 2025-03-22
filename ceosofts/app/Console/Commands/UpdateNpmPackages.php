<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class UpdateNpmPackages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'npm:update {--fix-postcss : Fix PostCSS configuration issues}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update NPM package.json and related frontend config files';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Updating frontend configuration files...');
        
        // Update package.json
        $this->updatePackageJson();
        
        // Fix PostCSS config if requested
        if ($this->option('fix-postcss')) {
            $this->fixPostCssConfig();
        }
        
        // Update Vite config
        $this->updateViteConfig();
        
        // Instructions for the user
        $this->info("\nAll configuration files have been updated!");
        $this->info("\nTo complete the setup, run:");
        $this->info("npm install");
        $this->info("npm run dev");
        
        return Command::SUCCESS;
    }
    
    /**
     * Update package.json with necessary dependencies
     */
    protected function updatePackageJson()
    {
        $packageJsonPath = base_path('package.json');
        
        if (!File::exists($packageJsonPath)) {
            $this->error('package.json not found.');
            return;
        }
        
        $packageJson = json_decode(File::get($packageJsonPath), true);
        
        // Make sure devDependencies exists
        if (!isset($packageJson['devDependencies'])) {
            $packageJson['devDependencies'] = [];
        }
        
        // Make sure dependencies exists
        if (!isset($packageJson['dependencies'])) {
            $packageJson['dependencies'] = [];
        }
        
        // Add/update required devDependencies
        $packageJson['devDependencies'] = array_merge($packageJson['devDependencies'], [
            'autoprefixer' => '^10.4.16',
            'postcss' => '^8.4.31',
            'axios' => '^1.6.1',
            'laravel-vite-plugin' => '^0.8.0',
            'vite' => '^4.0.0',
            '@vitejs/plugin-vue' => '^4.2.3',
            'sass' => '^1.62.1'
        ]);
        
        // Add/update required dependencies
        $packageJson['dependencies'] = array_merge($packageJson['dependencies'], [
            'bootstrap' => '^5.3.0',
            '@popperjs/core' => '^2.11.7',
            'alpinejs' => '^3.12.0',
            'jquery' => '^3.7.0'
        ]);
        
        // Write updated package.json
        File::put(
            $packageJsonPath,
            json_encode($packageJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
        );
        
        $this->info('Updated package.json with required dependencies');
    }
    
    /**
     * Fix PostCSS configuration issue
     */
    protected function fixPostCssConfig()
    {
        $postCssConfigPath = base_path('postcss.config.js');
        
        // Create ESM compatible PostCSS config
        $postCssConfigContent = <<<'EOD'
export default {
  plugins: {
    autoprefixer: {},
  },
}
EOD;
        
        File::put($postCssConfigPath, $postCssConfigContent);
        $this->info('Created ES module compatible postcss.config.js');
    }
    
    /**
     * Update Vite configuration
     */
    protected function updateViteConfig()
    {
        $viteConfigPath = base_path('vite.config.js');
        
        if (!File::exists($viteConfigPath)) {
            $this->error('vite.config.js not found.');
            return;
        }
        
        // Update the vite.config.js content
        $viteConfigContent = <<<'EOD'
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
    ],
    resolve: {
        alias: {
            '$': 'jquery',
        },
    },
    css: {
        devSourcemap: true,
    },
    build: {
        sourcemap: true,
    },
});
EOD;
        
        File::put($viteConfigPath, $viteConfigContent);
        $this->info('Updated vite.config.js');
    }
}
