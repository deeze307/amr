<?php

namespace IAServer\Http\Controllers\PizarraDeProduccion;

use Carbon\Carbon;
use IAServer\Http\Controllers\Cogiscan\Cogiscan;
use IAServer\Http\Controllers\Cogiscan\CogiscanDB2;
use IAServer\Http\Controllers\CONFIG\Model\CgsConfig;
use IAServer\Http\Controllers\Email\Email;
use IAServer\Http\Controllers\Redis\RedisCached;
use Illuminate\Http\Request;
use IAServer\Console\Commands;
use IAServer\Http\Requests;
use IAServer\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Mockery\CountValidator\Exception;
use Symfony\Component\Console\Output\ConsoleOutput;

class PizarraController extends Controller
{
    public static function index()
    {
        return view('pizarra.index');
    }
}
