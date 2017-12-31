<?php

/**
 * @Author: ahmadnorin
 * @Date:   2017-11-27 23:39:21
 * @Last Modified by:   ahmadnorin
 * @Last Modified time: 2017-11-28 00:29:26
 */

namespace Bantenprov\LaravelApiManager\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use File;

class RouteCommands extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laravel-api-manager:add-route';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add api manager to route';

    /**
     * Create a new command instance.
     *
     * @return void
     */

    protected $drip;

    public function __construct()
    {
        parent::__construct();

    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */

    protected function content()
    {

        $replace_middleware = File::get(__DIR__.'/../stubs/route.stub');
        return $replace_middleware;
    }

   protected function contentApi()
   {

       $replace_middleware = File::get(__DIR__.'/../stubs/routeApi.stub');
       return $replace_middleware;
   }


    public function handle()
    {
        $this->info('Route add success'); 
        File::append(base_path('routes/web.php'),"\n".$this->content());
        File::append(base_path('routes/api.php'),"\n".$this->contentApi());
    }
}
