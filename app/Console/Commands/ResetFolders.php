<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ResetFolders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'folders:reset';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Resets all project related folders';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $this->line('Cleaning projects folder');
        $this->delTree(base_path('/projects/'));
        File::makeDirectory(base_path('/projects/'), 0777, true, true);


        $this->line('Cleaning zip folder');
        $this->delTree(base_path('/zipfolders/'));
        File::makeDirectory(base_path('/zipfolders/'), 0777, true, true);

        $this->line('Cleaning server folder');

        return 0;
    }

    private function delTree($dir): bool
    {

        $files = array_diff(scandir($dir), array('.','..'));
        foreach ($files as $file) {
            (is_dir("$dir/$file")) ? $this->delTree("$dir/$file") : unlink("$dir/$file");
        }
        return rmdir($dir);
    }

}
