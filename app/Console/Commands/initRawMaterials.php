<?php

namespace IAServer\Console\Commands;

use Carbon\Carbon;
use IAServer\Http\Controllers\AMI\AmiController;
use IAServer\Http\Controllers\AMR\AmrController;
use IAServer\Http\Controllers\Redis\RedisController;
use Illuminate\Console\Command;

class initRawMaterials extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'initRawMaterials';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Inicialización de Materiales';

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
     * @return mixed
     */
    public function handle()
    {
        $amrController = new AmiController();
        $amrController->initRawMaterials();
    }
}
