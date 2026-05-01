<?php

namespace Modules\PretixIntegration\Providers;

use Illuminate\Support\ServiceProvider;

class PretixServiceProvider extends ServiceProvider
{
    const MODULE_ALIAS = 'pretixintegration';

    public function boot()
    {
        $this->loadViewsFrom(__DIR__ . '/../Resources/views', self::MODULE_ALIAS);
        $this->registerRoutes();
        $this->registerHooks();
    }

    private function registerRoutes()
    {
        if (app()->routesAreCached()) {
            return;
        }
        \Route::group([
            'middleware' => ['web', 'auth', 'roles'],
            'prefix'     => \Helper::getSubdirectory(),
            'namespace'  => 'Modules\PretixIntegration\Http\Controllers',
        ], function () {
            \Route::post('/pretixintegration/ajax', [
                'uses'    => 'PretixController@ajax',
                'laroute' => true,
            ])->name('pretixintegration.ajax');
        });
    }

    public function register()
    {
    }

    private function registerHooks()
    {
        $this->registerSidebarHook();
        $this->registerAssetHooks();
        $this->registerSettingsHooks();
    }

    private function registerSidebarHook()
    {
        \Eventy::addAction('conversation.after_prev_convs', function ($customer, $conversation, $mailbox) {
            $email = $customer->getMainEmail();
            if (!$email) {
                return;
            }
            echo \View::make('pretixintegration::partials/sidebar', [
                'customer_email'  => $email,
                'conversation_id' => $conversation->id,
            ])->render();
        }, 20, 3);
    }

    private function registerAssetHooks()
    {
        \Eventy::addFilter('javascripts', function ($javascripts) {
            $javascripts[] = \Module::getPublicPath(self::MODULE_ALIAS) . '/js/module.js';
            return $javascripts;
        });

        \Eventy::addFilter('stylesheets', function ($stylesheets) {
            $stylesheets[] = \Module::getPublicPath(self::MODULE_ALIAS) . '/css/module.css';
            return $stylesheets;
        });
    }

    private function registerSettingsHooks()
    {
        \Eventy::addFilter('settings.sections', function ($sections) {
            $sections[self::MODULE_ALIAS] = [
                'title' => __('Pretix Integration'),
                'icon'  => 'tag',
                'order' => 500,
            ];
            return $sections;
        }, 15, 1);

        \Eventy::addFilter('settings.view', function ($view, $section) {
            if ($section !== self::MODULE_ALIAS) {
                return $view;
            }
            return 'pretixintegration::settings';
        }, 20, 2);

        \Eventy::addFilter('settings.section_settings', function ($settings, $section) {
            if ($section !== self::MODULE_ALIAS) {
                return $settings;
            }
            $settings['base_url']  = \Option::get('pretixintegration.base_url', '');
            $settings['api_token'] = \Option::get('pretixintegration.api_token', '');
            $settings['organizer'] = \Option::get('pretixintegration.organizer', '');
            return $settings;
        }, 20, 2);

        \Eventy::addFilter('settings.after_save', function ($response, $request, $section, $settings) {
            if ($section !== self::MODULE_ALIAS) {
                return $response;
            }
            \Option::set('pretixintegration.base_url',  rtrim($request->input('settings.base_url', ''), '/'));
            \Option::set('pretixintegration.api_token', $request->input('settings.api_token', ''));
            \Option::set('pretixintegration.organizer', trim($request->input('settings.organizer', '')));
            return $response;
        }, 20, 4);
    }
}
