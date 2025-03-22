<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;

class RouteInspect extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'route:inspect {name? : The route name to search for}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Inspect routes to find duplicates or specific routes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $routeName = $this->argument('name');
        $routes = Route::getRoutes();
        $this->info('Inspecting routes...');
        
        // If a specific route name is provided, search for it
        if ($routeName) {
            $this->info("Looking for routes named or containing '{$routeName}'...");
            $found = false;
            $matches = [];
            
            foreach ($routes as $route) {
                $name = $route->getName();
                if ($name && (str_contains($name, $routeName))) {
                    $matches[] = [
                        'name' => $name,
                        'uri' => $route->uri(),
                        'methods' => implode('|', $route->methods()),
                        'action' => $route->getActionName()
                    ];
                    $found = true;
                }
            }
            
            if ($found) {
                $this->info("Found " . count($matches) . " routes matching '{$routeName}':");
                $this->table(['Name', 'URI', 'Methods', 'Action'], $matches);
            } else {
                $this->info("No routes found with name containing '{$routeName}'");
            }
            
            return Command::SUCCESS;
        }
        
        // Otherwise, check for duplicate route names
        $this->info("Checking for duplicate route names...");
        
        $routeNames = [];
        $duplicates = [];
        
        foreach ($routes as $route) {
            $name = $route->getName();
            
            if (!$name) {
                continue; // Skip unnamed routes
            }
            
            if (!isset($routeNames[$name])) {
                $routeNames[$name] = [
                    'uri' => $route->uri(),
                    'methods' => implode('|', $route->methods()),
                    'action' => $route->getActionName()
                ];
            } else {
                // Found a duplicate
                if (!isset($duplicates[$name])) {
                    $duplicates[$name] = [
                        $routeNames[$name], // First occurrence
                        [
                            'uri' => $route->uri(),
                            'methods' => implode('|', $route->methods()),
                            'action' => $route->getActionName()
                        ]
                    ];
                } else {
                    // More duplicates of the same name
                    $duplicates[$name][] = [
                        'uri' => $route->uri(),
                        'methods' => implode('|', $route->methods()),
                        'action' => $route->getActionName()
                    ];
                }
            }
        }
        
        if (empty($duplicates)) {
            $this->info("No duplicate route names found!");
        } else {
            $this->error("Found " . count($duplicates) . " duplicate route names:");
            
            foreach ($duplicates as $name => $routes) {
                $this->warn("Duplicate name: {$name}");
                $rows = [];
                foreach ($routes as $index => $route) {
                    $rows[] = [
                        'index' => $index + 1,
                        'uri' => $route['uri'],
                        'methods' => $route['methods'],
                        'action' => $route['action']
                    ];
                }
                $this->table(['#', 'URI', 'Methods', 'Action'], $rows);
                $this->line('------------------------');
            }
        }
        
        return Command::SUCCESS;
    }
}
