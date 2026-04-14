<?php

namespace Gillobis\Envbar\View\Components;

use Gillobis\Envbar\EnvbarManager;
use Illuminate\View\Component;

class Envbar extends Component
{
    public function __construct(protected EnvbarManager $manager) {}

    public function render()
    {
        return view('envbar::envbar');
    }
}
