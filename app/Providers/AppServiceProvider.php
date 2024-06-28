<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Domain\Repositories\FileRepositoryInterface;
use App\Data\Repositories\FileRepository;
use App\Domain\UseCases\UploadFileChunkUseCase;
use App\Domain\UseCases\FinalizeUploadUseCase;
use App\Domain\UseCases\EncryptFileUseCase;
use App\Domain\UseCases\DecryptFileUseCase;
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
        $this->app->singleton(UploadFileChunkUseCase::class, function ($app) {
            return new UploadFileChunkUseCase($app->make(FileRepositoryInterface::class));
        });

        $this->app->singleton(FinalizeUploadUseCase::class, function ($app) {
            return new FinalizeUploadUseCase($app->make(FileRepositoryInterface::class));
        });

        $this->app->singleton(EncryptFileUseCase::class, function ($app) {
            return new EncryptFileUseCase(
                $app->make(FileRepositoryInterface::class),
                hex2bin($app['config']->get('app.aes_secret_key')),
                (int) $app['config']->get('constants.CHUNK_SIZE')
            );
        });

        $this->app->singleton(DecryptFileUseCase::class, function ($app) {
            return new DecryptFileUseCase(
                $app->make(FileRepositoryInterface::class),
                hex2bin($app['config']->get('app.aes_secret_key')),
                (int) $app['config']->get('constants.CHUNK_SIZE')
            );
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
