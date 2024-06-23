<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Domain\Repositories\FileRepositoryInterface;
use App\Data\Repositories\FileRepository;
use App\Domain\UseCases\UploadFileUseCase;
use App\Domain\UseCases\EncryptFileUseCase;
use App\Domain\UseCases\DecryptFileUseCase;
use App\Domain\UseCases\ValidateEncryptionUseCase;
use App\Domain\UseCases\FileUseCases;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Repos
        $this->app->singleton(FileRepositoryInterface::class, FileRepository::class);

        // Use Cases
        $this->app->singleton(UploadFileUseCase::class, function ($app) {
            return new UploadFileUseCase($app->make(FileRepositoryInterface::class));
        });

        $this->app->singleton(EncryptFileUseCase::class, function ($app) {
            return new EncryptFileUseCase($app->make(FileRepositoryInterface::class));
        });

        $this->app->singleton(DecryptFileUseCase::class, function ($app) {
            return new DecryptFileUseCase($app->make(FileRepositoryInterface::class));
        });

        $this->app->singleton(ValidateEncryptionUseCase::class, function ($app) {
            return new ValidateEncryptionUseCase($app->make(FileRepositoryInterface::class));
        });

        $this->app->singleton(FileUseCases::class, function ($app) {
            return new FileUseCases();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
