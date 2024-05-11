<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Orchid\Attachment\Models\Attachment;

class ClearAttachmentCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:name';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $records = Attachment::query()->get();

        foreach ($records as $index => $record) {
            $file = Storage::disk('public')->path($record->physicalPath());
            if (file_exists($file)) {
                // TODO убрать из коллекции все записи с таким хэшем
                continue;

            } else {
                // удалить из БД все записи с таким хэшем
                Attachment::query()->where('hash', '=', $record->hash)->delete();
                // убрать из коллекции все записи с таким хэшем
//                $ids = $records->where('hash', '=',  $record->hash)->pluck('id');
            }
        }

        return Command::SUCCESS;
    }
}
