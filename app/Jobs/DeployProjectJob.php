<?php

namespace App\Jobs;

use App\Models\Log;
use App\Models\Project;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Encryption\Encrypter;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;

class DeployProjectJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $project;
    private $database;
    private $path;

    /**
     * Create a new job instance.
     *
     * @param Project $project
     */
    public function __construct(Project $project)
    {
        $this->project = $project;
        $this->database = $this->project->id . '_' . Str::slug($this->project->name, '_');
        $this->path = config('app.project_deploy_path') . $this->project->slug;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $fileSystem = new Filesystem();

        // 2 - Preparar o ambiente
        $this->project->update(['deploy_status' => 1]);

        $this->runCommandWait(['sudo', 'rm', '-rf',  $this->path]);



        // 3 - A criar a base de dados
        $this->project->update(['deploy_status' => 2]);


        // Init DB
        DB::statement('DROP DATABASE IF EXISTS ' . $this->database);
        DB::statement('CREATE DATABASE ' . $this->database);

        // 4 - A criar a pasta do projeto
        $this->project->update(['deploy_status' => 3]);


        // Create Folder

        File::makeDirectory($this->path, 0777, true, true);

        $fileSystem->mirror($this->project->folder, $this->path);

        if(config('app.env') != 'local') {

            $stub = file_get_contents(base_path('stubs/setup.sh'));

            $replacement = str_replace([
                '#--SOURCE_FILE--#',
                '#--SYMBOLIC-LINK--#'
            ], [
                $this->path,
                config('app.project_ln_path'),
            ], $stub);

            file_put_contents($this->path . '/setup.sh', $replacement);
        } else {
            $fileSystem->copy(base_path('stubs/setup.dev.sh'), $this->path . '/setup.sh');
        }


        // 5 - A gerar os ficheiros de configuração
        $this->project->update(['deploy_status' => 4]);


        $this->generateEnv();


        // 4 - A instalar os ficheiros de configuração
        $this->project->update(['deploy_status' => 5]);

        $this->runCommandWait(['yes', '|', 'php', '-d' ,'memory_limit=-1' ,'composer.phar', 'install']);



        // 4 - A realizar as migrações

        $this->project->update(['deploy_status' => 6]);

        $this->runCommandWait(['bash','setup.sh']);

        $this->project->update(['deploy_status' => 7]);
    }

    private function generateEnv() {
        $stub = file_get_contents(base_path('stubs/coreui/.env.deploy.stub'));

        $stub = str_replace([
            '#--PROJECT-NAME--#',
            '#--SANITIZED-PROJECT-NAME-LOWERCASE--#',
            '#--DB-DATABASE--#',
            '#--DB-USERNAME--#',
            '#--DB-PASSWORD--#',
            '#--KEY--#'
        ],
            [
                $this->project->name,
                $this->project->database,
                $this->database,
                config('app.project_db_username'),
                config('app.project_db_password'),
            ], $stub);

        file_put_contents(
            $this->path . '/.env',
            $stub);
    }

    private function delTree($dir): bool
    {

        $files = array_diff(scandir($dir), array('.','..'));
        foreach ($files as $file) {
            (is_dir("$dir/$file")) ? $this->delTree("$dir/$file") : unlink("$dir/$file");
        }
        return rmdir($dir);
    }

    private function runCommandWait(array $command)
    {
        $gitInit = new Process($command);
        $gitInit->setWorkingDirectory($this->path);

        $gitInit->run();
        $gitInit->wait();
        \Illuminate\Support\Facades\Log::debug(
            $gitInit->getErrorOutput());
        \Illuminate\Support\Facades\Log::debug(
            $gitInit->getOutput());
    }
}
