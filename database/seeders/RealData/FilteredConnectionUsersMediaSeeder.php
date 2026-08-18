<?php

declare(strict_types=1);

namespace Database\Seeders\RealData;

use App\Models\User;
use Illuminate\Database\Seeder;

final class FilteredConnectionUsersMediaSeeder extends Seeder
{
    public function run(): void
    {
        // Ordinary mobile-app users are isolated under the dedicated people/users root.
        $root = database_path('assets/people/users');
        $hashes = [];
        $missing = 0;

        foreach (FilteredConnectionUsersData::users() as $definition) {
            $user = User::query()->where('email', $definition['email'])->firstOrFail();
            $files = array_values(array_filter(glob($root.'/'.$definition['slug'].'/avatar/*') ?: [], 'is_file'));
            if (count($files) === 0) {
                $missing++;
                continue;
            }

            $file = $files[0];
            $hash = hash_file('sha256', $file);
            if (isset($hashes[$hash])) {
                $this->command?->warn('Duplicate local avatar skipped for '.$definition['first_name'].' '.$definition['last_name'].'; same file content as '.$hashes[$hash]);
                continue;
            }
            $hashes[$hash] = $definition['slug'];

            $existing = $user->getFirstMedia('user-avatars');
            if (! $existing || ! is_file($existing->getPath()) || $existing->file_name !== basename($file)) {
                $existing?->delete();
                $user->addMedia($file)
                    ->usingFileName(basename($file))
                    ->preservingOriginal()
                    ->toMediaCollection('user-avatars');
            }
        }

        if ($missing > 0) {
            $this->command?->warn($missing.' approved ordinary users still need locally verified avatar assets.');
        }
    }
}

