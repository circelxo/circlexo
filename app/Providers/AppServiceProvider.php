<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use TomatoPHP\FilamentTypes\Facades\FilamentTypes;
use TomatoPHP\FilamentTypes\Services\Contracts\Type;
use TomatoPHP\FilamentTypes\Services\Contracts\TypeFor;
use TomatoPHP\FilamentTypes\Services\Contracts\TypeOf;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->environment() == 'production') {
            $this->app['request']->server->set('HTTPS', true);
        }

        $this->setSchemaDefaultLength();

        Validator::extend('base64image', function ($attribute, $value, $parameters, $validator) {
            $explode = explode(',', $value);
            $allow = ['png', 'jpg', 'svg', 'jpeg'];
            $format = str_replace(
                [
                    'data:image/',
                    ';',
                    'base64',
                ],
                [
                    '', '', '',
                ],
                $explode[0]
            );

            // check file format
            if (! in_array($format, $allow)) {
                return false;
            }

            // check base64 format
            if (! preg_match('%^[a-zA-Z0-9/+]*={0,2}$%', $explode[1])) {
                return false;
            }

            return true;
        });

        FilamentTypes::register([
            TypeFor::make('home')
                ->label('Home Sections')
                ->types([
                    TypeOf::make('hero-section')
                        ->label('Hero Section'),
                    TypeOf::make('feature-section')
                        ->label('Feature Section'),
                    TypeOf::make('testimonials-section')
                        ->label('Testimonials Section'),
                ]),
            TypeFor::make('dashboard')
                ->label('Dashboard Sections')
                ->types([
                    TypeOf::make('widget')
                        ->label('Dashboard Widgets'),
                    TypeOf::make('sidebar-menu')
                        ->label('Sidebar Menu')
                        ->register([
                            Type::make('https://docs.3x1.io/circlexo')
                                ->name([
                                    'ar' => 'طريقة الاستخدام',
                                    'en' => 'Docs',
                                ])
                                ->icon('phosphor-book-bookmark-duotone'),
                            Type::make('https://github.com/orgs/circlexo/discussions')
                                ->name([
                                    'ar' => 'الأسئلة الشائعة',
                                    'en' => 'Questions',
                                ])
                                ->icon('phosphor-chat-duotone'),
                            Type::make('https://github.com/orgs/circlexo/discussions')
                                ->name([
                                    'ar' => 'الأسئلة الشائعة',
                                    'en' => 'Questions',
                                ])
                                ->icon('phosphor-chat-duotone'),
                        ]),
                ]),
        ]);
    }

    private function setSchemaDefaultLength(): void
    {
        try {
            Schema::defaultStringLength(191);
        } catch (\Exception $exception) {
        }
    }
}
