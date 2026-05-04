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
 
    // computerNetwork
    public function computerNetwork(){
        return view("pages.computer_network.index");
    }
    public function computerWorld(){
        return view("pages.computerWorld.index");
    }
    public function accessControl(){
        return view("pages.accessControl.index");
    }
    public function printerWorld(){
        return view("pages.printerWorld.index");
    }
 
}
