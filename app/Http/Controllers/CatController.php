<?php

namespace App\Http\Controllers;

use App\Models\Cat;
use Illuminate\Http\Request;

class CatController extends Controller
{
    public function index()
    {
        $cats = Cat::orderBy('id')->get();
        return view('cats.index', compact('cats'));
    }
} 