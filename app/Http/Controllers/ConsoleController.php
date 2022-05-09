<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Alkhachatryan\LaravelWebConsole\LaravelWebConsole;

class ConsoleController extends Controller
{
    public function index() {
        return LaravelWebConsole::show();
     }
}
