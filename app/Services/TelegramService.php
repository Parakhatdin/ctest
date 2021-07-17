<?php


namespace App\Services;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    public function hande(Request $request)
    {
        Log::info($request->getContent());
    }
}
