<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Company;
use App\Models\SystemUser;
use Illuminate\Database\Seeder;
use RuntimeException;

final class RealTechMediaSeeder extends Seeder
{
    public function run(): void
    {
        $companyRoot = database_path('assets/real_tech_companies');
        $peopleRoot = database_path('assets/people');

        foreach (RealTechData::companies() as $slug => $definition) {
            $company = Company::query()->where('name', $definition['name'])->firstOrFail();
            $companyPath = $companyRoot.'/'.$slug;
            $logos = glob($companyPath.'/logo/*') ?: [];
            $gallery = glob($companyPath.'/gallery/*') ?: [];
            if (count($logos) < 1 || count($gallery) < 2) {
                throw new RuntimeException("Missing verified company media for {$slug}; Seeder stopped before attaching incomplete data.");
            }
            if (! $company->getFirstMedia('logo')) {
                $company->addMedia($logos[0])->usingFileName(basename($logos[0]))->preservingOriginal()->toMediaCollection('logo');
            }
            foreach ($gallery as $path) {
                $filename = basename($path);
                if (! $company->media()->where('collection_name', 'gallery')->where('file_name', $filename)->exists()) {
                    $company->addMedia($path)->usingFileName($filename)->preservingOriginal()->toMediaCollection('gallery');
                }
            }
        }

        foreach (RealTechData::people() as $person) {
            $systemUser = SystemUser::query()->where('email', $person['email'])->firstOrFail();
            $avatarFiles = glob($peopleRoot.'/'.$person['asset'].'/avatar/*') ?: [];
            $path = $avatarFiles[0] ?? '';
            if (! is_file($path)) {
                throw new RuntimeException("Missing verified avatar for {$person['key']}: {$path}");
            }
            if (! $systemUser->getFirstMedia('avatar')) {
                $systemUser->addMedia($path)->usingFileName(basename($path))->preservingOriginal()->toMediaCollection('avatar');
            }
        }
    }
}
