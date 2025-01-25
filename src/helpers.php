<?php

if (!function_exists('event')) {
    /**
     * Dispatch an event and call the listeners.
     *
     * @param ...$args
     * @return void
     */
    function event(...$args): void
    {
        /**
         * By default, event function comes from Laravel framework. At the moment this function is disabled
         * because it cannot be used with this library since there is no event manager.
         * @see https://github.com/laravel/framework/blob/11.x/src/Illuminate/Foundation/helpers.php#L462
         */
    }
}
