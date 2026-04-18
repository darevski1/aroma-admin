<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PublicController extends Controller
{
    // cennobots page
    public function cennobots(){
        return view("pages.cennobots.index");
    }
}
