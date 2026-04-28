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
    // About us
    public function about(){
        return view("pages.about.index");
    }
    public function parent($parent){
        return view("pages.{$parent}", compact('parent'));
    }

}
