<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Company;
use App\Models\SystemUser;
use Illuminate\Database\Seeder;
use RuntimeException;

final class GovernmentHall11MediaSeeder extends Seeder
{
    public function run(): void
    {
        $companyRoot = database_path('assets/government_hall11_companies');
        $peopleRoot = database_path('assets/people');

        foreach (GovernmentHall11Data::companies() as $slug => $definition) {
            $company = Company::query()->where('name', $definition['name'])->firstOrFail();
            $root = $companyRoot.'/'.$slug;
            $logos = glob($root.'/logo/*') ?: [];
            $gallery = glob($root.'/gallery/*') ?: [];
            $logo = $this->oneReadable($logos, 'logo', $slug);
            $gallery = array_values(array_filter($gallery, 'is_file'));
            if (count($gallery) < 2) {
                throw new RuntimeException("Government company {$slug} needs at least two gallery images.");
            }
            $logoHash = hash_file('sha256', $logo);
            $seen = [];
            foreach ($gallery as $path) {
                if (hash_file('sha256', $path) === $logoHash) {
                    throw new RuntimeException("Government gallery {$path} duplicates its logo.");
                }
                $hash = hash_file('sha256', $path);
                if (isset($seen[$hash])) {
                    throw new RuntimeException("Government gallery {$slug} contains duplicate images.");
                }
                $seen[$hash] = true;
            }

            $company->clearMediaCollection('logo');
            $company->clearMediaCollection('gallery');
            $company->addMedia($logo)->preservingOriginal()->usingFileName(basename($logo))->toMediaCollection('logo');
            foreach ($gallery as $path) {
                $company->addMedia($path)->preservingOriginal()->usingFileName(basename($path))->toMediaCollection('gallery');
            }
        }

        foreach (GovernmentHall11Data::people() as $person) {
            $user = SystemUser::query()->where('email', $person['email'])->firstOrFail();
            $paths = glob($peopleRoot.'/'.$person['asset'].'/avatar/*') ?: [];
            $avatar = $this->oneReadable($paths, 'avatar', $person['asset']);
            $user->clearMediaCollection('avatar');
            $user->addMedia($avatar)->preservingOriginal()->usingFileName(basename($avatar))->toMediaCollection('avatar');
        }
    }

    private function oneReadable(array $paths, string $kind, string $key): string
    {
        foreach ($paths as $path) {
            if (is_file($path) && filesize($path) > 0) {
                return $path;
            }
        }
        throw new RuntimeException("Missing government {$kind} asset for {$key}.");
    }
}
