<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Company;
use App\Models\SystemUser;
use Illuminate\Database\Seeder;
use RuntimeException;

final class ExpandedTechMediaSeeder extends Seeder
{
    public function run(): void
    {
        $companyRoot = database_path('assets/expanded_tech_companies');
        $peopleRoot = database_path('assets/people');
        foreach (ExpandedTechData::companies() as $slug => $definition) {
            $company = Company::query()->where('name', $definition['name'])->firstOrFail();
            $path = $companyRoot.'/'.$slug;
            $logos = glob($path.'/logo/*') ?: [];
            $gallery = glob($path.'/gallery/*') ?: [];
            if (count($logos) < 1 || count($gallery) < 2) {
                throw new RuntimeException("Missing expanded company media for {$slug}; expected one logo and at least two gallery files.");
            }
            if (! $company->getFirstMedia('logo')) {
                $company->addMedia($logos[0])->usingFileName(basename($logos[0]))->toMediaCollection('logo');
            }
            foreach ($gallery as $file) {
                $name = basename($file);
                if (! $company->media()->where('collection_name','gallery')->where('file_name',$name)->exists()) {
                    $company->addMedia($file)->usingFileName($name)->toMediaCollection('gallery');
                }
            }
        }
        foreach (ExpandedTechData::people() as $person) {
            $user = SystemUser::query()->where('email', $person['email'])->firstOrFail();
            $files = glob($peopleRoot.'/'.$person['asset'].'/avatar/*') ?: [];
            if (! $files) {
                throw new RuntimeException("Missing expanded avatar for {$person['name']} at {$peopleRoot}/{$person['asset']}/avatar");
            }
            if (! $user->getFirstMedia('avatar')) {
                $user->addMedia($files[0])->usingFileName(basename($files[0]))->toMediaCollection('avatar');
            }
        }
    }
}
