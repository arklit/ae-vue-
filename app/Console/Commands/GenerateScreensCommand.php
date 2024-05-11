<?php

namespace App\Console\Commands;

use App\Services\ScreenGeneratorService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

class GenerateScreensCommand extends Command
{
    protected $signature = 'generate:screens';

    protected $description = 'Generate screens and model based on user input';

    public function handle(ScreenGeneratorService $screenGeneratorService): void
    {
        $useModal = $this->askWithCompletion('Do you want to use a modal window instead of an edit screen?', ['yes', 'no'], 'no');
        $useModal = $useModal === 'yes';

        $modelName = $this->askWithCompletion('Enter model name', []);
        $screenTitle = $this->askWithCompletion('Enter screen title', []);
        $menuTitle = $this->askWithCompletion('Enter menu title', []);
        $tableName = strtolower(Str::snake(Str::plural($modelName)));

        $fields = [];
        while (true) {
            $fieldName = $this->askWithCompletion('Enter field name (or "stop" to finish)', []);
            if ($fieldName === 'stop') {
                break;
            }

            $fieldCode = $this->askWithCompletion('Enter field code', []);
            $fieldType = $this->choice('Enter Orchid field class', ['Input', 'Cropper', 'Textarea', 'Checkbox', 'Select'], 0);
            $columnType = $this->choice('Enter column type', ['integer', 'string', 'text', 'boolean', 'json', 'timestamp'], 0);
            $isList = $this->confirm('Should this field be displayed on the list screen?', true);
            $isRequired = $this->confirm('Is this field required?', true);
            $fields[] = [
                'name' => $fieldName,
                'code' => $fieldCode,
                'field_type' => $fieldType,
                'column_type' => $columnType,
                'is_list' => $isList,
                'is_edit' => true,
                'is_required' => $isRequired,
            ];
        }

        if ($useModal) {
            $screenGeneratorService->generateListScreen($modelName, $fields, true);
            $screenGeneratorService->updateRoutes($modelName, true);
            $screenGeneratorService->updateModalValidation($modelName, $tableName, $fields);
        } else {
            // Generate screens, model, etc. using the existing logic
            $screenGeneratorService->generateListScreen($modelName, $fields);
            $screenGeneratorService->generateEditScreen($modelName, $screenTitle, $tableName, $fields);
            $screenGeneratorService->updateRoutes($modelName);
        }

        // These methods are common to both scenarios
        $screenGeneratorService->generateModel($modelName, $tableName, $fields);
        $screenGeneratorService->updateRoutesEnum($modelName, $screenTitle);
        $screenGeneratorService->updateMenu($menuTitle, $modelName);

        $screenGeneratorService->createMigration($modelName, $fields);

        if ($this->confirm('Do you want to run the migrations now?', true)) {
            Artisan::call('migrate');
            $this->info('Migrations run successfully.');
        }

        $this->info('Screens and model generated successfully.');
    }
}
