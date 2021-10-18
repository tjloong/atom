<?php

namespace Jiannius\Atom\Components;

use Illuminate\View\Component;

class Gtm extends Component
{
    public $config;
    public $noscript;

    /**
     * Create the component instance.
     *
     * @param boolean $noscript
     * @return void
     */
    public function __construct($noscript = false)
    {
        $this->noscript = $noscript;
        $this->config = (object)config('atom.gtm');
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|\Closure|string
     */
    public function render()
    {
        if (config('atom.gtm.id') && env('APP_ENV') === 'production') {
            return view('atom::components.gtm', [
                'disabled' => collect(config('atom.gtm.exclude_paths'))->contains(fn($path) => request()->is($path)),
            ]);
        }
    }
}