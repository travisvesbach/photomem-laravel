<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Directory;

class DirectoriesController extends Controller
{
    public function index() {
        return view('directories.index');
    }
}
