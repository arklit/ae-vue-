<?php

namespace App\Services;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ScreenGeneratorService
{
    private function getStub(string $stubFileName): string
    {
        $stubPath = app_path('Orchid/Console/Stubs/' . $stubFileName);
        return File::get($stubPath);
    }

    private function replacePlaceholders(string $content, array $replacements): string
    {
        return str_replace(array_keys($replacements), array_values($replacements), $content);
    }

    private function createFile(string $filePath, string $content): void
    {
        if (!File::exists(dirname($filePath))) {
            File::makeDirectory(dirname($filePath), 0777, true);
        }
        File::put($filePath, $content);
        File::chmod($filePath, 0777);
    }

    private function generateListFieldsString(array $fields, bool $isList = false): string
    {
        $fieldsString = '';
        foreach ($fields as $field) {
            if ($field['is_list'] === $isList) {
                $fieldsString .= "TD::make('{$field['code']}', '{$field['name']}')->sort()->filter(),\n";
            }
        }
        return $fieldsString;
    }

    private function generateEditFields(array $fields): string
    {
        $fieldsString = '';
        foreach ($fields as $field) {
            if ($field['is_edit']) {
                $fieldsString .= "{$field['field_type']}::make('item.{$field['code']}')->title('{$field['name']}')";
                if ($field['is_required']) {
                    $fieldsString .= "->required()";
                }
                $fieldsString .= ",\n";
            }
        }
        return $fieldsString;
    }

    private function generateRulesString(array $fields, string $tableName, bool $isEdit = false): string
    {
        $rulesString = "\t\t\t'title' => ['bail', 'required', 'max:255'],\n\t\t\t'sort' => ['bail', 'required'],\n\t\t\t'code' => ['bail', 'nullable' ,'regex:~^[A-Za-z0-9\\-_]*$~'";
        if (!$isEdit) {
            $rulesString .= ", Rule::unique('{$tableName}')->ignore(\$id)],\n";
        } else {
            $rulesString .= "],\n";
        }

        foreach ($fields as $field) {
            $rulesString .= str_repeat(' ', 16) . "'{$field['code']}' => ['bail', " . ($field['is_required'] ? "'required'," : "") . " 'max:255'],\n";
        }
        return $rulesString;
    }

    private function generateMessagesString(array $fields): string
    {
        $messagesString = "\t\t\t'title.required' => 'Введите заголовок',\n\t\t\t'title.max' => 'Заголовок не может быть длиннее 255 символов',\n\t\t\t'sort.required' => 'Введите порядок сортировки',\n\t\t\t'code.regex' => 'В коде допустимы только цифры и латинские буквы',\n\t\t\t'code.unique' => 'Код должен быть уникальным',\n";
        foreach ($fields as $field) {
            $lowerFieldName = strtolower($field['name']);
            $upperFieldName = strtoupper($field['name']);

            $messagesString .= str_repeat(' ', 16) . "'{$field['code']}.max' => '{$upperFieldName} не может быть длиннее 255 символов',\n";
            if ($field['is_required']) {
                $messagesString .= str_repeat(' ', 16) . "'{$field['code']}.required' => 'Введите {$lowerFieldName}',\n";
            }
        }
        return $messagesString;
    }

    private function generateAllowedSorts(array $fields): string
    {
        $allowedSorts = '';
        foreach ($fields as $field) {
            $allowedSorts .= "'{$field['code']}', ";
        }
        return $allowedSorts;
    }

    private function generateAllowedFilters(array $fields): string
    {
        $allowedFilters = '';
        foreach ($fields as $field) {
            $allowedFilters .= "'{$field['code']}' => Like::class, ";
        }
        return $allowedFilters;
    }


    private function generateScreenClass(string $modelName, string $screenType): string
    {
        return "App\\Orchid\\Screens\\" . $modelName . "\\" . $modelName . $screenType;
    }

    private function generateUseStatement(string $class): string
    {
        return "use " . $class . ";\n";
    }

    private function generateRouteStatement(string $modelName, string $listScreenClass, ?string $editScreenClass): string
    {
        $routeStatement = "OrchidHelper::setAdminRoutes(OrchidRoutes::" . strtoupper(Str::snake($modelName)) . "->value, " . class_basename($listScreenClass) . "::class";
        if ($editScreenClass) {
            $routeStatement .= ", " . class_basename($editScreenClass) . "::class";
        }
        $routeStatement .= ");\n";
        return $routeStatement;
    }

    private function generateDefaultColumnsCode(): string
    {
        return "\$table->id();\n\t\t\t\$table->boolean('is_active')->comment('Активность')->default(true);\n\t\t\t\$table->string('title')->comment('Заголовок');\n\t\t\t\$table->string('code')->comment('Код');\n\t\t\t\$table->integer('sort')->default(0)->comment('Сортировка');\n";
    }

    private function generateColumnsCode(array $fields): string
    {
        $columnsCode = '';
        $lastFieldIndex = count($fields) - 1;
        foreach ($fields as $index => $field) {
            $columnsCode .= "\t\t\t\$table->{$field['column_type']}('{$field['code']}')->nullable()->comment('{$field['name']}');";
            if ($index !== $lastFieldIndex) {
                $columnsCode .= "\n";
            }
        }
        return $columnsCode;
    }

    public function generateListScreen(string $modelName, array $fields, bool $useModal = false): void
    {
        $stubFileName = $useModal ? 'list-screen-modal.stub' : 'list-screen.stub';
        $listScreenContent = $this->getStub($stubFileName);
        $fieldsString = $this->generateListFieldsString($fields, true);
        $replacements = [
            'DirectoryName' => $modelName,
            'ProtoModel' => $modelName,
            'StubListScreen' => $modelName . 'ListScreen',
            '//..fields' => $fieldsString,
            'PROTO_MODEL' => strtoupper(Str::snake($modelName))
        ];
        $listScreenContent = $this->replacePlaceholders($listScreenContent, $replacements);
        $newListScreenPath = app_path('Orchid/Screens/' . $modelName . '/' . $modelName . 'ListScreen.php');
        $this->createFile($newListScreenPath, $listScreenContent);

        if ($useModal) {
            $this->generateModal($modelName, $fields);
        }
    }

    public function generateModal(string $modelName, array $fields): void
    {
        $modalContent = $this->getStub('modal.stub');

        $fieldsString = $this->generateEditFields($fields);
        $replacements = ['ProtoModalModel' => $modelName . 'Modal', '//fields' => $fieldsString];
        $modalContent = $this->replacePlaceholders($modalContent, $replacements);

        $newModalPath = app_path('Orchid/Screens/Modals/' . $modelName . 'Modal.php');
        $this->createFile($newModalPath, $modalContent);
    }

    public function generateEditScreen(string $modelName, string $screenTitle, string $tableName, array $fields): void
    {
        $editScreenContent = $this->getStub('edit-screen.stub');

        $fieldsString = $this->generateEditFields($fields);
        $rulesString = $this->generateRulesString($fields, $tableName, true);
        $messagesString = $this->generateMessagesString($fields);
        $replacements = [
            'ITEM' => $screenTitle,
            'DirectoryName' => $modelName,
            'ProtoModel' => $modelName,
            'StubEditScreen' => $modelName . 'EditScreen',
            '//..fields' => $fieldsString,
            'PROTO_MODEL' => strtoupper(Str::snake($modelName)),
            '//..rules' => $rulesString,
            '//..messages' => $messagesString
        ];
        $editScreenContent = $this->replacePlaceholders($editScreenContent, $replacements);

        $newEditScreenPath = app_path('Orchid/Screens/' . $modelName . '/' . $modelName . 'EditScreen.php');
        $this->createFile($newEditScreenPath, $editScreenContent);
    }

    public function generateModel(string $modelName, string $tableName, array $fields): void
    {
        $modelContent = $this->getStub('model.stub');

        $allowedSorts = $this->generateAllowedSorts($fields);
        $allowedFilters = $this->generateAllowedFilters($fields);
        $replacements = [
            'ModelName' => $modelName,
            'model_table' => $tableName,
            '//..allowedSorts' => $allowedSorts,
            '//..allowedFilters' => $allowedFilters
        ];
        $modelContent = $this->replacePlaceholders($modelContent, $replacements);

        $newModelPath = app_path('Models/' . $modelName . '.php');
        $this->createFile($newModelPath, $modelContent);
    }

    public function updateModalValidation(string $modalName, string $tableName, array $fields): void
    {
        $validationPath = app_path('Enums/ModalValidation.php');
        $validationContent = File::get($validationPath);

        $rulesString = $this->generateRulesString($fields, $tableName);
        $messagesString = $this->generateMessagesString($fields);

        $caseTitle = strtoupper($modalName) . '_MODAL';
        $newCase = "    case {$caseTitle} = '{$modalName}Modal';\n//case-place";
        $validationContent = str_replace('//case-place', $newCase, $validationContent);

        $newRules = "            self::{$caseTitle}->value => [\n{$rulesString}            ],\n//rules-place";
        $validationContent = str_replace('//rules-place', $newRules, $validationContent);

        $newMessages = "            self::{$caseTitle}->value => [\n{$messagesString}            ],\n//messages-place";
        $validationContent = str_replace('//messages-place', $newMessages, $validationContent);

        File::put($validationPath, $validationContent);
    }

    public function updateRoutesEnum(string $screenName, string $screenTitle): void
    {
        // Получаем путь к файлу
        $enumPath = app_path('Enums/OrchidRoutes.php');

        // Получаем содержимое файла
        $enumContent = File::get($enumPath);

        // Добавляем новый case в enum
        $newCase = "case " . strtoupper(Str::snake($screenName)) . " = '" . strtolower(Str::kebab($screenName)) . "';\n    ";
        $enumContent = str_replace('//case-place', $newCase . '//case-place', $enumContent);

        // Добавляем новую запись в getTitle
        $newTitle = "self::" . strtoupper(Str::snake($screenName)) . "->value => '" . $screenTitle . "',\n            ";
        $enumContent = str_replace('//title-place', $newTitle . '//title-place', $enumContent);

        // Добавляем новую запись в isSingle
        $newSingle = "self::" . strtoupper(Str::snake($screenName)) . "->value => false,\n            ";
        $enumContent = str_replace('//single-place', $newSingle . '//single-place', $enumContent);

        // Сохраняем изменения в файле
        File::put($enumPath, $enumContent);
    }

    public function updateMenu(string $menuTitle, string $modelName): void
    {
        // Получаем путь к файлу
        $providerPath = app_path('Orchid/PlatformProvider.php');

        // Получаем содержимое файла
        $providerContent = File::get($providerPath);

        // Добавляем новый пункт меню
        $newMenu = "Menu::make('" . $menuTitle . "')->route(OrchidRoutes::" . strtoupper(Str::snake($modelName)) . "->list())->icon(''),\n            ";
        $providerContent = str_replace('//menu-place', $newMenu . '//menu-place', $providerContent);

        // Сохраняем изменения в файле
        File::put($providerPath, $providerContent);
    }

    public function updateRoutes(string $modelName, bool $useModal = false): void
    {
        $routesPath = base_path('routes/platform.php');
        $routesContent = File::get($routesPath);

        $listScreenClass = $this->generateScreenClass($modelName, 'ListScreen');
        $editScreenClass = $useModal ? null : $this->generateScreenClass($modelName, 'EditScreen');

        $newUseListScreen = $this->generateUseStatement($listScreenClass);
        $routesContent = str_replace('//use-place', $newUseListScreen . '//use-place', $routesContent);

        if (!$useModal) {
            $newUseEditScreen = $this->generateUseStatement($editScreenClass);
            $routesContent = str_replace('//use-place', $newUseEditScreen . '//use-place', $routesContent);
        }

        $newRoute = $this->generateRouteStatement($modelName, $listScreenClass, $editScreenClass);
        $routesContent = str_replace('//route-place', $newRoute . '//route-place', $routesContent);

        File::put($routesPath, $routesContent);
    }

    public function createMigration(string $modelName, array $fields): void
    {
        $tableName = Str::snake(Str::plural($modelName));
        Artisan::call('make:migration', ['name' => "create_{$tableName}_table", '--create' => $tableName]);

        $migrationsPath = database_path('migrations');
        $migrationFile = collect(File::files($migrationsPath))->last();
        $migrationContent = File::get($migrationFile);

        $migrationContent = preg_replace('/\$table->id\(\);\s*/', '', $migrationContent, 1);

        $defaultColumnsCode = $this->generateDefaultColumnsCode();
        $columnsCode = $this->generateColumnsCode($fields);

        $schemaCreateLine = "Schema::create('{$tableName}', function (Blueprint \$table) {";
        $migrationContent = str_replace($schemaCreateLine, $schemaCreateLine . "\n" . $defaultColumnsCode . $columnsCode, $migrationContent);

        File::put($migrationFile, $migrationContent);
    }
}
