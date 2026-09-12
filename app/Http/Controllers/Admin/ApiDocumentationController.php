<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ApiDocumentationController extends Controller
{
    public function apiDocumentation(){
        $apiDocumentation = setting('api_documentation');
        if (empty($apiDocumentation) && file_exists(public_path('Barakahex_Courier_API_Documentation.pdf'))) {
            $apiDocumentation = 'Barakahex_Courier_API_Documentation.pdf';
        }
        return view('merchant.api_documentation', compact('apiDocumentation'));
    }
}
