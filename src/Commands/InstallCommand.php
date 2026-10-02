<?php

namespace KaizenDev\ModularApi\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class InstallCommand extends Command
{
    /**
     * O nome e a assinatura do console command.
     *
     * @var string
     */
    protected $signature = 'modular:install';

    /**
     * A descrição do console command.
     *
     * @var string
     */
    protected $description = 'Instala e configura a arquitetura base de Monolito Modular na sua aplicação.';

    /**
     * Executa o comando no console.
     */
    public function handle(): void
    {
        $this->info('🚀 Iniciando a instalação da Arquitetura Modular (KaizenDev)...');

        // 1. Instalar a pasta Base (O coração do sistema)
        $this->publishBaseModule();

        // 2. Instalar o ModuleServiceProvider
        $this->publishModuleServiceProvider();

        // 3. Questionário (Wizard) para limpeza da arquitetura padrão
        $this->runCleanupWizard();

        $this->info('🎉 Instalação concluída com sucesso! Você já pode utilizar o comando "php artisan make:module Nome".');
    }

    /**
     * Copia o módulo Base do pacote para a aplicação.
     */
    private function publishBaseModule(): void
    {
        $stubPath = __DIR__ . '/../../stubs/Base';
        $destinationPath = app_path('Http/Modules/Base');

        if (!File::isDirectory($destinationPath)) {
            File::makeDirectory($destinationPath, 0755, true);
        }

        File::copyDirectory($stubPath, $destinationPath);
        $this->info('✅ Módulo Base ejetado em app/Http/Modules/Base.');
    }

    /**
     * Copia o ModuleServiceProvider e injeta ele no bootstrap/providers.php
     */
    private function publishModuleServiceProvider(): void
    {
        // Copiar o arquivo
        $stubPath = __DIR__ . '/../../stubs/Providers/ModuleServiceProvider.stub';
        $destinationPath = app_path('Providers/ModuleServiceProvider.php');

        File::copy($stubPath, $destinationPath);
        $this->info('✅ ModuleServiceProvider ejetado em app/Providers.');

        // Registrar no bootstrap/providers.php (Laravel 11+)
        $providersFile = base_path('bootstrap/providers.php');
        
        if (File::exists($providersFile)) {
            $content = File::get($providersFile);
            
            if (!str_contains($content, 'App\Providers\ModuleServiceProvider::class')) {
                // Injeta antes do fechamento do array
                $content = preg_replace(
                    "/(];)/",
                    "    App\Providers\ModuleServiceProvider::class,\n];",
                    $content
                );
                File::put($providersFile, $content);
                $this->info('✅ ModuleServiceProvider registrado automaticamente em bootstrap/providers.php.');
            }
        }
    }

    /**
     * Executa o Wizard de limpeza da estrutura padrão.
     */
    private function runCleanupWizard(): void
    {
        $this->line('');
        $this->warn('--- Limpeza de Arquitetura Padrão (Opcional) ---');
        $this->line('Para manter sua API puramente modular, você pode excluir os diretórios padrões do Laravel.');

        if ($this->confirm('Deseja excluir a pasta padrão "app/Models"?', true)) {
            if (File::isDirectory(app_path('Models'))) {
                File::deleteDirectory(app_path('Models'));
                $this->info('🗑️  Pasta app/Models removida.');
            }
        }

        if ($this->confirm('Deseja excluir a pasta padrão "app/Http/Controllers"?', true)) {
            if (File::isDirectory(app_path('Http/Controllers'))) {
                File::deleteDirectory(app_path('Http/Controllers'));
                $this->info('🗑️  Pasta app/Http/Controllers removida.');
            }
        }

        if ($this->confirm('Deseja excluir as migrations padrões (User, Password Resets, etc)?', true)) {
            $migrationsPath = database_path('migrations');
            if (File::isDirectory($migrationsPath)) {
                $files = File::files($migrationsPath);
                foreach ($files as $file) {
                    File::delete($file);
                }
                $this->info('🗑️  Migrations padrões removidas.');
            }
        }
        
        if ($this->confirm('Deseja excluir as factories padrões (UserFactory, etc)?', true)) {
            $factoriesPath = database_path('factories');
            if (File::isDirectory($factoriesPath)) {
                $files = File::files($factoriesPath);
                foreach ($files as $file) {
                    File::delete($file);
                }
                $this->info('🗑️  Factories padrões removidas.');
            }
        }

        if ($this->confirm('Deseja excluir a pasta padrão "database/seeders" (DatabaseSeeder padrão)?', true)) {
            $seedersPath = database_path('seeders');
            if (File::isDirectory($seedersPath)) {
                File::deleteDirectory($seedersPath);
                $this->info('🗑️  Pasta database/seeders removida.');
            }
        }

        if ($this->confirm('Deseja excluir a pasta padrão "app/Jobs" (caso exista)?', true)) {
            $jobsPath = app_path('Jobs');
            if (File::isDirectory($jobsPath)) {
                File::deleteDirectory($jobsPath);
                $this->info('🗑️  Pasta app/Jobs removida.');
            }
        }

        if ($this->confirm('Deseja excluir a pasta padrão "app/Http/Requests" (caso exista)?', true)) {
            $requestsPath = app_path('Http/Requests');
            if (File::isDirectory($requestsPath)) {
                File::deleteDirectory($requestsPath);
                $this->info('🗑️  Pasta app/Http/Requests removida.');
            }
        }

        if ($this->confirm('Deseja excluir a pasta padrão "app/Http/Resources" (caso exista)?', true)) {
            $resourcesPath = app_path('Http/Resources');
            if (File::isDirectory($resourcesPath)) {
                File::deleteDirectory($resourcesPath);
                $this->info('🗑️  Pasta app/Http/Resources removida.');
            }
        }
    }
}
