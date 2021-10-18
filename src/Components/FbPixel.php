<?php

namespace Jiannius\Atom\Components;

use Illuminate\View\Component;

class FbPixel extends Component
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
        $this->config = (object)config('atom.fbpixel');
        $this->noscript = $noscript;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|\Closure|string
     */
    public function render()
    {
        if (config('atom.fbpixel.id') && env('APP_ENV') === 'production') {
            return view('atom::components.fb-pixel', [
                'disabled' => collect(config('atom.fbpixel.exclude_paths'))->contains(fn($path) => request()->is($path)),
            ]);
        }
    }
}