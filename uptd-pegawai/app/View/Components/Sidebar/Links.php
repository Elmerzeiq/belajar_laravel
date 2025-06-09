<?php

namespace App\View\Components\Sidebar;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Links extends Component
{
    /**
     * Create a new component instance.
     */
    public string $title, $route, $icon, $active;
    public function __construct($title, $route, $icon)
    {
        $this->title = $title;
        $this->route = $route;
        $this->icon = $icon;
        $basePath = explode('.', $route);
        $this->active = request()->routeIs($basePath) ? 'bg-blue-300 Text-white' : '';
        //
    }

    public function generatePath($route)
    {
        if (str_contains($route, '.')) {
            $path = explode('.', $route);
            return route($path[0] . '.*');
        } else {
            return route($route);
        }
    }
    public function render(): View|Closure|string
    {
        return view('components.sidebar.links');
    }
}
