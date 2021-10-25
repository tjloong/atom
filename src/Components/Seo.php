<?php

namespace Jiannius\Atom\Components;

use Illuminate\View\Component;

class Seo extends Component
{
    public $config;
    public $disabled;
    public $hreflang;
    public $canonical;
    public $jsonld;
    public $title;

    /**
     * Create the component instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->config = (object)config('atom.seo');
        $this->disabled = collect(config('atom.seo.exclude_paths'))->contains(fn($path) => request()->is($path));
        $this->hreflang = collect(config('atom.seo.hreflang'))->first(fn($value, $key) => request()->is($key));
        $this->canonical = collect(config('atom.seo.canonical'))->first(fn($value, $key) => request()->is($key));
        $this->jsonld = config('atom.seo.jsonld') ?? [
            '@context' => 'http://schema.org',
            '@type' => 'Website',
            'url' => url()->current(),
            'name' => config('atom.seo.title'),
        ];

        $env = strtoupper(env('APP_ENV'));
        $appname = config('app.name');

        if ($title = config('atom.seo.title')) $this->title = $title;
        else if ($env === 'PRODUCTION') $this->title = $appname;
        else $this->title = "[$env]$appname";
}

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|\Closure|string
     */
    public function render()
    {
        return view('atom::components.seo');
    }
}