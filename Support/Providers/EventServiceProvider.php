<?php

namespace Illuminate\Foundation\Support\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event handler mappings for the application.
     *
     * @var array
     */
    protected $listen = [];

    /**
     * The event listeners for the application.
     *
     * @var array
     */
    protected $listeners = [];

    /**
     * The subscriber classes to register.
     *
     * @var array
     */
    protected $subscribe = [];

    /**
     * Register the application's event listeners.
     *
     * @return void
     */
    public function boot()
    {
        foreach ($this->listens() as $event => $listeners) {
            foreach ($listeners as $listener) {
                Event::listen($event, $listener);
            }
        }

        foreach ($this->subscribe as $subscriber) {
            Event::subscribe($subscriber);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function register()
    {
        //
    }

    /**
     * Get the events and handlers.
     *
     * @return array
     */
    public function listens()
    {
        return array_merge_recursive(collect($this->listeners)->filter(function ($listener) {
            return class_exists($listener, false);
        })->flatMap(function ($listener) {
            return collect($listener::$hears)->map(function ($event) use ($listener) {
                return ['event' => $event, 'listeners' => [$listener]];
            })->all();
        })->groupBy('event')->map(function ($listeners) {
            return $listeners->pluck('listeners')->flatten(1)->all();
        })->all(), $this->listen);
    }

    /**
     * Get all of the registered listeners.
     *
     * @return array
     */
    public function listeners()
    {
        return $this->listeners;
    }
}
