<?php

namespace App\Listeners;
use App\Events\LocaleStatusToggled;
use Illuminate\Support\Facades\File;
class HandleLocalePostToggle {
    public array $steps = [];
    public function handle(LocaleStatusToggled $event) {
        $key = $event->key;
        $langPath = base_path("lang/{$key}");
        if (File::exists($langPath)) {
            $this->steps[] = [
                'step' => 'Check lang folder',
                'status' => 'success',
                'message' => "Folder '{$key}' exists"
            ];
        } else {
            try {
                File::makeDirectory($langPath, 0755, true);
                $this->steps[] = [
                    'step' => 'Create lang folder',
                    'status' => 'success',
                    'message' => "Folder '{$key}' created"
                ];
            } catch (\Exception $e) {
                $this->steps[] = [
                    'step' => 'Create lang folder',
                    'status' => 'failed',
                    'message' => $e->getMessage()
                ];
            }
        }

        $filePath = $langPath . "/{$key}.php";
        if (File::exists($filePath)) {
            $this->steps[] = [
                'step' => 'Check lang file',
                'status' => 'success',
                'message' => "File '{$key}.php' already exists"
            ];
        } else {
            try {
                $content = "<?php\n\nreturn [];\n";
                File::put($filePath, $content);
                $this->steps[] = [
                    'step' => 'Create lang file',
                    'status' => 'success',
                    'message' => "File '{$key}.php' created successfully"
                ];
            } catch (\Exception $e) {
                $this->steps[] = [
                    'step' => 'Create lang file',
                    'status' => 'failed',
                    'message' => $e->getMessage()
                ];
            }
        }
        return $this->steps;
    }
}