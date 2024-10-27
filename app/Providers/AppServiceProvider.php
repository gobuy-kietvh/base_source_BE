<?php

namespace App\Providers;

use App\Extensions\CarbonExtension;
use App\Libs\ValueUtil;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Query\{
    Builder,
    JoinClause,
};
use Illuminate\Support\Facades\{App, Date, Response};
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

        Builder::macro('whereValidStatus', function (): Builder {
            /** @var Builder $this */
            $tableName = $this->from;

            if ($this instanceof JoinClause) {
                $tableName = $this->table;
            }

            return $this
                ->where(
                    "{$tableName}.status",
                    '<>',
                    ValueUtil::constToValue('common.status.INVALID'),
                );
        });
        Date::useClass(CarbonExtension::class);
        Response::macro('streamDownloadCSV', function (string $filePath, string $fileName) {
            return response()->streamDownload(function () use ($filePath) {
                $stream = fopen($filePath, 'r');
                while (ob_get_level() > 0) {
                    ob_end_flush();
                }
                fpassthru($stream);

                // Delete the file after sending it
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }, $fileName, [
                'Content-Type' => 'text/csv',
            ]);
        });
    }
}
