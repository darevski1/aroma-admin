<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Navigation;

class Navbar extends Component
{
    public function render()
    {
       return view('livewire.navbar', [
            'navItems' => Navigation::where('published', true)
                ->with(['subNavigations' => function ($query) {
                    $query->where('published', true)->orderBy('position');
                }])
                ->orderBy('position')
                ->get(),
        ]);
    }
}
