<?php

namespace App\Jobs;

use App\Models\Project;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Symfony\Component\Process\Process;

class DeleteProjectJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $project;

    /**
     * Create a new job instance.
     *
     * @param Project $project
     */
    public function __construct(Project $project)
    {
        $this->project = $project;
    }


    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if(is_dir($this->project->folder)) {
            $this->runCommandWait(['sudo', 'rm', '-rf',  $this->project->folder], '/home/');
        }

        if(is_dir(config('app.project_deploy_path') . $this->project->slug)) {
            $this->runCommandWait(['sudo', 'rm', '-rf',  config('app.project_deploy_path') . $this->project->slug], '/home/');
        }


    }

    private function delTree($dir): bool
    {

        $files = array_diff(scandir($dir), array('.','..'));
        foreach ($files as $file) {
            (is_dir("$dir/$file")) ? $this->delTree("$dir/$file") : unlink("$dir/$file");
        }
        return rmdir($dir);
    }

    private function runCommandWait(array $command, $path = null)
    {
        if($path == null) $path = $this->path;

        $gitInit = new Process($command);
        $gitInit->setWorkingDirectory($path);

        $gitInit->run();
        $gitInit->wait();
        \Illuminate\Support\Facades\Log::debug(
            $gitInit->getErrorOutput());
        \Illuminate\Support\Facades\Log::debug(
            $gitInit->getOutput());
    }
}
