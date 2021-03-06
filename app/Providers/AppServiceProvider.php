<?php

namespace App\Providers;

use Dingo\Api\Exception\ValidationHttpException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use DB;
use Log;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->environment() !== 'production') {
            DB::listen(function ($query) {
                $sql = str_replace('?', '%s', $query->sql);
                $sql = sprintf($sql, ...$query->bindings);
                Log::info('sql', [$sql, $query->time]);
            });
        }
        Schema::defaultStringLength(191);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->environment() !== 'production') {
            $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
        }
        $this->registerDingoApiExceptionHandler();
    }


    public function registerDingoApiExceptionHandler()
    {
        $apiHandler = app('Dingo\Api\Exception\Handler');
        $apiHandler->register(
            function (\Illuminate\Auth\AuthenticationException $exception) {
                return response(
                    [
                        'status_code' => 401,
                        'code' => 401.1,
                        'message' => trans('auth.please_login_first')
                    ], 401
                );
            }
        );
        $apiHandler->register(
            function (\Illuminate\Auth\Access\AuthorizationException $exception) {
                return response(
                    [
                        'status_code' => 401,
                        'code' => 401.3,
                        'message' => $exception->getMessage() == 'This action is unauthorized.' || empty($exception->getMessage())
                            ? trans('auth.no_permission') : $exception->getMessage()
                    ], 401
                );
            }
        );
        $apiHandler->register(
            function (\Illuminate\Database\Eloquent\ModelNotFoundException $exception) {
                return response(
                    [
                        'status_code' => 404,
                        'code' => 404,
                        //todo 这里的错误显示需要处理
                        'message' => $exception->getMessage()
                    ], 404
                );
            }
        );
        $apiHandler->register(
            function (ValidationException $exception) {
                throw new ValidationHttpException($exception->validator->errors());
            }
        );
        $apiHandler->register(
            function (QueryException $exception) {
                if ($this->app->environment() !== 'production') {
                    //throw new HttpException(500, $exception->getSql());
                    throw $exception;
                } else {
                    // todo log
                    throw new HttpException(500);
                }
            }
        );
    }
}
