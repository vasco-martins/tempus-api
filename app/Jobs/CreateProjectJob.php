<?php

namespace App\Jobs;

use App\Models\Project;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;

class CreateProjectJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private Project $project;

    /**
     * Create a new job instance.
     *
     * @param Project $project
     */
    public function __construct(Project $project)
    {
        $this->project = $project;
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $themeFolder = base_path('themes/coreui');

        File::makeDirectory($this->project->folder, 0777, true, true);

        $filesystem = new Filesystem();
        $filesystem->mirror($themeFolder, $this->project->folder);

//        $this->runGitCommandAndWait(['git', 'init']);
  //      $this->runGitCommandAndWait(['git', 'add', '.']);
    //    $this->runGitCommandAndWait(['git', 'commit', '-m', '"First commit 🚀"']);

        Log::info('Created project directory at ' . $this->project->folder);
    }

    private function runGitCommandAndWait(array $command)
    {
        $gitInit = new Process($command);
        $gitInit->setWorkingDirectory($this->project->folder);
        $gitInit->run();
        $gitInit->wait();
    }
}
