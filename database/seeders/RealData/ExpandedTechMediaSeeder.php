<?php

declare(strict_types=1);

namespace Database\Seeders\RealData;

use App\Models\Company;
use App\Models\SystemUser;
use Illuminate\Database\Seeder;
use RuntimeException;

final class ExpandedTechMediaSeeder extends Seeder
{
    public function run(): void
    {
        $companyRoot = database_path('assets/expanded_tech_companies');
        $peopleRoot = database_path('assets/people/systemUsers');

        foreach (ExpandedTechData::companies() as $slug => $definition) {
            $company = Company::query()->where('name', $definition['name'])->firstOrFail();
            $path = $companyRoot.'/'.$slug;
            $logos = array_values(array_filter(glob($path.'/logo/*') ?: [], 'is_file'));
            $gallery = array_values(array_filter(glob($path.'/gallery/*') ?: [], 'is_file'));
            if (count($logos) < 1 || count($gallery) < 2) {
                continue;
            }
            $logoHash = hash_file('sha256', $logos[0]);
            $galleryHashes = [];
            foreach ($gallery as $file) {
                $name = strtolower(basename($file));
                if (preg_match('/linkedin|instagram|facebook|brand-asset|brand-logo|verified-search|public-search|logo/', $name)) {
                    throw new RuntimeException('Invalid expanded company media for '.$slug.': gallery filename looks like a logo or social-platform placeholder: '.basename($file));
                }
                $hash = hash_file('sha256', $file);
                if ($hash === $logoHash) {
                    throw new RuntimeException('Invalid expanded company media for '.$slug.': gallery file duplicates the logo: '.basename($file));
                }
                if (isset($galleryHashes[$hash])) {
                    throw new RuntimeException('Invalid expanded company media for '.$slug.': duplicate gallery files '.basename($galleryHashes[$hash]).' and '.basename($file));
                }
                $galleryHashes[$hash] = $file;
            }
            $existingLogo = $company->getFirstMedia('logo');
            if (! $existingLogo || ! is_file($existingLogo->getPath())) {
                $existingLogo?->delete();
                $company->addMedia($logos[0])->usingFileName(basename($logos[0]))->preservingOriginal()->toMediaCollection('logo');
            }
            foreach ($gallery as $file) {
                $name = basename($file);
                $existingGallery = $company->media()->where('collection_name', 'gallery')->where('file_name', $name)->first();
                if (! $existingGallery || ! is_file($existingGallery->getPath())) {
                    $existingGallery?->delete();
                    $company->addMedia($file)->usingFileName($name)->preservingOriginal()->toMediaCollection('gallery');
                }
            }
        }

        foreach (ExpandedTechData::people() as $person) {
            $user = SystemUser::query()->where('email', $person['email'])->firstOrFail();
            $files = array_values(array_filter(glob($peopleRoot.'/'.$person['asset'].'/avatar/*') ?: [], 'is_file'));
            if (! $files) {
                throw new RuntimeException('Missing expanded avatar for '.$person['name'].' at '.$peopleRoot.'/'.$person['asset'].'/avatar');
            }
            $existingAvatar = $user->getFirstMedia('avatar');
            if (! $existingAvatar || ! is_file($existingAvatar->getPath())) {
                $existingAvatar?->delete();
                $user->addMedia($files[0])->usingFileName(basename($files[0]))->preservingOriginal()->toMediaCollection('avatar');
            }
        }
    }
}
