<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Navigation;

class Navbar extends Component
{
    public function render()
    {
       return view('livewire.navbar', [
            'navItems' => Navigation::with(['subNavigations' => function ($query) {
                $query->where('published', 1);
            }])->get(),
        ]);
    }
}
