<?php

namespace App\Console\Commands;

use App\Helpers\FileToArrayConverter;
use App\Jobs\UserImport;
use App\Models\FileImport;
use Illuminate\Bus\Batch;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;

class ImportUsers extends Command
{
    protected $signature = 'import:users
                            {filePath? : The full path to the file to import}';

    protected $description = 'Import users from a source json file';

    protected Batch $usersImportBatch;

    protected FileImport | null $fileImport;

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $filePath = $this->argument('filePath') ?: base_path('challenge.json');

        if ($this->attemptRestartImport($filePath) === false) {
            $this->info('Import started.');

            $jobs = $this->convertFileToJobs($filePath);
            $this->dispatchJobs($jobs);
        }

        $this->newLine();
        $this->info('Import finished.');

        return 0;
    }

    protected function attemptRestartImport(string $filePath): mixed
    {
        $this->fileImport = FileImport::query()->where('file_path', $filePath)->first();

        if ($this->fileImport === null) {
            return false;
        }

        $batch = Bus::findBatch($this->fileImport->job_batch_id);

        if ($batch->finished()) {
            $this->fileImport->delete();

            return false;
        }

        $this->info('Import re-started.');

        return $this->makeUserImportProgressBar($batch);
    }

    protected function convertFileToJobs(string $filePath): array
    {
        $fileSize = filesize($filePath);

        $this->fileImport            = new FileImport();
        $this->fileImport->file_path = $filePath;

        $this->info('Reading file...');

        $usersFromFile = FileToArrayConverter::make($filePath);

        $this->info('Converting file to jobs...');

        $fileToJobBar = $this->output->createProgressBar($fileSize);

        $jobs = [];

        foreach ($usersFromFile as $user) {
            $jobs[] = (new UserImport($user));
            $fileToJobBar->advance();
        }

        $fileToJobBar->finish();

        $this->newLine();
        $this->info('File converted to jobs.');

        return $jobs;
    }

    protected function dispatchJobs(array $jobs): bool
    {
        $this->newLine();
        $this->info('Starting jobs...');

        $this->usersImportBatch = Bus::batch($jobs)
            ->name('Users import')
            ->dispatch();

        $this->fileImport->job_batch_id = $this->usersImportBatch->id;
        $this->fileImport->save();

        $this->info('Total jobs started: ' . $this->usersImportBatch->totalJobs);

        $this->makeUserImportProgressBar($this->usersImportBatch);

        return true;
    }

    protected function makeUserImportProgressBar(Batch $batchJob): bool
    {
        $progressBar = $this->output->createProgressBar($batchJob->totalJobs);
        $progressBar->setFormat('verbose');

        while (($batchJob = $batchJob->fresh()) && !$batchJob->finished()) {
            $progressBar->setProgress($batchJob->processedJobs());
        }

        $this->fileImport->delete();

        $progressBar->finish();

        return true;
    }
}
