<?php

declare(strict_types=1);

namespace Database\Seeders\RealData;

use App\Models\Company;
use App\Models\Event;
use App\Models\EventHall;
use App\Models\SystemUser;
use App\Services\Shared\QrCodeService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * One-week exhibition programme for the Damascus Fairgrounds MVP.
 *
 * Research provenance used in the editorial choices:
 * - UNDP Syria PSD-2026-Damascus: https://www.undp.org/syria/events/national-private-sector-dialogue2026
 * - SANA National Startup Agenda: https://sana.sy/en/investment/2310305/
 * - Enab Baladi digital transport forum: https://english.enabbaladi.net/archives/2025/10/forum-to-boost-digital-transformation-in-syrias-transport-sector/
 * - Project Syria 2026 venue guide: https://www.project-syria.com/show-hours-and-venue-guide
 * - SYNC'26 / Startup Syria: https://tecore.com/events/sync-26/ and https://www.linkedin.com/company/startup-syria
 * - SANA Work Gate Forum 4: https://sana.sy/en/syria/2274819/
 *
 * The seeded titles are exhibition-programme sessions inspired by those real
 * formats and topics; they are not represented as historical copies of those
 * source events.
 */
final class ExhibitionWeekEventsSeeder extends Seeder
{
    private const WEEK_START = '2026-08-24';


    public function run(): void
    {
        $halls = EventHall::query()->whereIn('number', [
            'M1', 'M2', 'M3', 'M3.1', 'M3.2', 'M4', 'M5', 'M6', 'M6.1',
        ])->pluck('id', 'number');

        foreach ($this->manifest() as $index => $definition) {
            if (isset($definition['company'])) {
                $eventable = Company::query()->where('name', $definition['company'])->first();
                if (! $eventable) {
                    throw new \RuntimeException("Missing event company: {$definition['company']}");
                }
            } else {
                $eventable = SystemUser::query()->where('email', $definition['speaker_email'])->first();
                if (! $eventable) {
                    throw new \RuntimeException("Missing event speaker user: {$definition['speaker_email']}");
                }
            }

            $startAt = Carbon::parse(self::WEEK_START.' '.$definition['start'])
                ->addDays($index % 7);
            $endAt = $startAt->copy()->addHours($definition['duration']);

            $event = Event::query()->updateOrCreate(
                [
                    'eventable_type' => $eventable::class,
                    'eventable_id' => $eventable->getKey(),
                    'title' => $definition['title'],
                ],
                [
                    'event_hall_id' => $halls[$definition['hall']],
                    'description' => $definition['description'],
                    'type' => $definition['type'],
                    'status' => 'approved',
                    'start_at' => $startAt,
                    'duration' => $definition['duration'],
                    'end_at' => $endAt,
                ],
            );

            $event->speakers()->delete();
            $event->speakers()->createMany(array_map(
                static fn (string $name): array => ['name' => $name],
                $definition['speakers'],
            ));

            $token = $event->qr_token ?: 'E-'.$event->id.'-'.Str::random(10);
            $event->forceFill(['qr_token' => $token])->saveQuietly();
            $event->clearMediaCollection('qr_code');
            $event->addMediaFromString(app(QrCodeService::class)->generateSvg($token))
                ->usingFileName("{$token}.svg")
                ->toMediaCollection('qr_code');

            $assetKey = str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT);
            $assetFiles = glob(database_path('assets/events/event-'.$assetKey.'.*')) ?: [];
            if ($assetFiles === []) {
                throw new \RuntimeException("Missing local event asset: database/assets/events/event-{$assetKey}.*");
            }
            $event->clearMediaCollection('event-logo');
            $event->addMedia($assetFiles[0])
                ->preservingOriginal()
                ->usingFileName(basename($assetFiles[0]))
                ->toMediaCollection('event-logo');
        }
    }

    /** @return list<array<string, mixed>> */
    private function manifest(): array
    {
        $shared = 'A practical exhibition session designed for Syrian companies, public institutions, founders, students, and investors to exchange applied solutions and partnership opportunities.';

        return [
            ['title' => 'Syria Startup Agenda: From Idea to Investable Company', 'company' => 'TechTown', 'hall' => 'M4', 'start' => '10:00', 'duration' => 2, 'type' => 'conference', 'speakers' => ['Abdul-Salam Haykal', 'Sinan Hatahet', 'Talal al-Hilali'], 'image_url' => 'https://files.manuscdn.com/user_upload_by_module/session_file/310519663888804691/KUscAnJpECrXVska.jpg', 'description' => $shared.' Inspired by the national startup agenda launched by the Ministry of Communications and Information Technology in Damascus, with emphasis on regulation, financing, market access, and job creation.'],
            ['title' => 'Public–Private Dialogue on Syria’s Digital Economy', 'company' => 'Syria Smart', 'hall' => 'M3', 'start' => '14:00', 'duration' => 2, 'type' => 'conference', 'speakers' => ['Abdul-Salam Haykal', 'Mohammad Nidal al-Shaar', 'Anas Ajaj'], 'image_url' => 'https://files.manuscdn.com/user_upload_by_module/session_file/310519663888804691/KUscAnJpECrXVska.jpg', 'description' => $shared.' A policy dialogue on predictable regulation, digital services, investment climate, and cooperation between ministries and technology providers.'],
            ['title' => 'Digital Wallets, Trust and Financial Inclusion', 'company' => 'Sham Cash', 'hall' => 'M2', 'start' => '11:00', 'duration' => 2, 'type' => 'workshop', 'speakers' => ['Mohammad Fawzy Sukkar', 'Hosam Arab', 'Amer Baroudi'], 'image_url' => 'https://files.manuscdn.com/user_upload_by_module/session_file/310519663888804691/qgrmylvAFdQGtcnu.jpg', 'description' => $shared.' A fintech workshop covering digital-wallet adoption, customer trust, payment infrastructure, and responsible financial access in Syria.'],
            ['title' => 'Enterprise Cloud and Business Transformation Clinic', 'company' => 'SAP', 'hall' => 'M6', 'start' => '16:00', 'duration' => 2, 'type' => 'lecture', 'speakers' => ['Arvind Krishna', 'Nizar Zarka', 'Mhd Tarek Almalek'], 'image_url' => 'https://files.manuscdn.com/user_upload_by_module/session_file/310519663888804691/OYfKAGnWbZhsXNfd.jpg', 'description' => $shared.' A practical enterprise session on cloud adoption, ERP modernisation, data governance, and digital operating models.'],
            ['title' => 'Smart Connectivity and Digital Infrastructure for Syria', 'company' => 'Huawei', 'hall' => 'M3.1', 'start' => '10:30', 'duration' => 2, 'type' => 'lecture', 'speakers' => ['Nizar Zarka', 'Abdul-Salam Haykal', 'Anas Ajaj'], 'image_url' => 'https://files.manuscdn.com/user_upload_by_module/session_file/310519663888804691/YeMnNRpwVPRkAyBA.jpg', 'description' => $shared.' A technology briefing on resilient connectivity, cloud infrastructure, smart devices, and the skills required for national digital transformation.'],
            ['title' => 'AI for Arabic Content, Knowledge and Public Services', 'company' => 'WRITER', 'hall' => 'M5', 'start' => '13:00', 'duration' => 2, 'type' => 'workshop', 'speakers' => ['Waseem AlShikh', 'Ahmad Sufian Bayram', 'Sinan Hatahet'], 'image_url' => 'https://files.manuscdn.com/user_upload_by_module/session_file/310519663888804691/OYfKAGnWbZhsXNfd.jpg', 'description' => $shared.' A focused workshop on generative AI, Arabic-language content, knowledge products, responsible adoption, and public-service use cases.'],
            ['title' => 'Founder Stories from Damascus to the Region', 'company' => 'BeeOrder', 'hall' => 'M1', 'start' => '17:00', 'duration' => 1, 'type' => 'lecture', 'speakers' => ['Abdel Malek Al-Mouzayen', 'Hamza Hourani', 'Bashar Saaduddin Al Jbawi'], 'image_url' => 'https://files.manuscdn.com/user_upload_by_module/session_file/310519663888804691/YeMnNRpwVPRkAyBA.jpg', 'description' => $shared.' A founder conversation about product-market fit, delivery operations, regional scaling, and building teams from Syria.'],
            ['title' => 'Mobility, Logistics and the Digital Transport Transition', 'company' => 'Wusool', 'hall' => 'M6.1', 'start' => '11:30', 'duration' => 2, 'type' => 'conference', 'speakers' => ['Mohammad Alammar', 'Abdul-Salam Haykal', 'Mohammad Yaser Bernieh'], 'image_url' => 'https://files.manuscdn.com/user_upload_by_module/session_file/310519663888804691/RXSEfpFOzVfctscR.jpg', 'description' => $shared.' Inspired by reporting on Syria’s digital transport forum, this session addresses route optimisation, digital payments, logistics data, and entrepreneurship in transport.'],
            ['title' => 'Syria Engineering Talent and the Future of Work', 'company' => 'noon', 'hall' => 'M4', 'start' => '15:00', 'duration' => 2, 'type' => 'conference', 'speakers' => ['Nizar Zarka', 'Sami Hijazi', 'Hamza Hourani'], 'image_url' => 'https://files.manuscdn.com/user_upload_by_module/session_file/310519663888804691/RXSEfpFOzVfctscR.jpg', 'description' => $shared.' A talent forum on engineering careers, remote work, diaspora collaboration, internships, and building export-ready technology teams.'],
            ['title' => 'Startup Pitch Arena: Syrian Products with Regional Potential', 'company' => 'DIGIT Innovation Hub', 'hall' => 'M3.2', 'start' => '18:00', 'duration' => 2, 'type' => 'conference', 'speakers' => ['Abdel Malek Al-Mouzayen', 'Sara Kataf', 'Amer Baroudi'], 'image_url' => 'https://files.manuscdn.com/user_upload_by_module/session_file/310519663888804691/KUscAnJpECrXVska.jpg', 'description' => $shared.' A moderated pitch and feedback session for early-stage teams working on software, fintech, commerce, mobility, and applied AI.'],
            ['title' => 'Building Reliable SaaS and ERP Products', 'company' => 'IXCoders', 'hall' => 'M2', 'start' => '10:00', 'duration' => 2, 'type' => 'workshop', 'speakers' => ['Mhd Tarek Almalek', 'MHD Yasser Kaziz', 'Mohammad Fawzy Sukkar'], 'image_url' => 'https://files.manuscdn.com/user_upload_by_module/session_file/310519663888804691/RXSEfpFOzVfctscR.jpg', 'description' => $shared.' A hands-on engineering workshop about product architecture, ERP workflows, quality assurance, and delivering maintainable software for Syrian businesses.'],
            ['title' => 'Jobs, Skills and the Syrian Technology Workforce', 'company' => 'E.B. Tech', 'hall' => 'M6', 'start' => '13:30', 'duration' => 2, 'type' => 'conference', 'speakers' => ['MHD Yasser Kaziz', 'Sami Hijazi', 'Feryal Tulaimat'], 'image_url' => 'https://files.manuscdn.com/user_upload_by_module/session_file/310519663888804691/qgrmylvAFdQGtcnu.jpg', 'description' => $shared.' Inspired by the Work Gate Forum format, this session connects employers, universities, training providers, and young professionals around practical technology skills and hiring.'],
            ['title' => 'Fintech Compliance and Responsible Growth', 'company' => 'nsave', 'hall' => 'M3', 'start' => '16:30', 'duration' => 2, 'type' => 'lecture', 'speakers' => ['Amer Baroudi', 'Hosam Arab', 'Mohammad Fawzy Sukkar'], 'image_url' => 'https://files.manuscdn.com/user_upload_by_module/session_file/310519663888804691/RXSEfpFOzVfctscR.jpg', 'description' => $shared.' A regional fintech discussion on compliance, customer protection, cross-border access, and sustainable product growth.'],
            ['title' => 'Arabic Digital Media, AI and Knowledge Platforms', 'company' => 'AraGeek', 'hall' => 'M5', 'start' => '11:00', 'duration' => 2, 'type' => 'conference', 'speakers' => ['Ahmad Sufian Bayram', 'Waseem AlShikh', 'Mohammad Fawzy Sukkar'], 'image_url' => 'https://files.manuscdn.com/user_upload_by_module/session_file/310519663888804691/tntFCxqSpLhwjTVf.jpg', 'description' => $shared.' A media and technology forum on Arabic digital publishing, AI-assisted editorial workflows, audience trust, and knowledge entrepreneurship.'],
            ['title' => 'E-Commerce, Marketplaces and Consumer Trust', 'company' => 'noon', 'hall' => 'M1', 'start' => '14:30', 'duration' => 2, 'type' => 'lecture', 'speakers' => ['Nizar Zarka', 'Ronaldo Mouchawar', 'Mohammad Alammar'], 'image_url' => 'https://files.manuscdn.com/user_upload_by_module/session_file/310519663888804691/RXSEfpFOzVfctscR.jpg', 'description' => $shared.' A marketplace session on fulfilment, customer experience, seller enablement, payments, and expanding digital commerce in the region.'],
            ['title' => 'Digital Payments and the New Consumer Journey', 'company' => 'Tabby', 'hall' => 'M2', 'start' => '17:00', 'duration' => 1, 'type' => 'lecture', 'speakers' => ['Hosam Arab', 'Abdulmajeed Alsukhan', 'Amer Baroudi'], 'image_url' => 'https://files.manuscdn.com/user_upload_by_module/session_file/310519663888804691/RXSEfpFOzVfctscR.jpg', 'description' => $shared.' A product talk on flexible payments, consumer behaviour, merchant tools, and responsible financial technology.'],
            ['title' => 'Music Technology, Creative Industries and the Syrian Market', 'company' => 'Anghami', 'hall' => 'M3.1', 'start' => '19:00', 'duration' => 1, 'type' => 'lecture', 'speakers' => ['Elie Habib', 'Ahmad Sufian Bayram', 'Madonna Bishara'], 'image_url' => 'https://files.manuscdn.com/user_upload_by_module/session_file/310519663888804691/tntFCxqSpLhwjTVf.jpg', 'description' => $shared.' A creative-industry conversation about streaming, rights, digital audiences, music technology, and opportunities for Syrian creators.'],
            ['title' => 'Private Sector Recovery and Investment Matchmaking', 'company' => 'Syrian Investment Authority', 'hall' => 'M4', 'start' => '10:00', 'duration' => 2, 'type' => 'conference', 'speakers' => ['Talal al-Hilali', 'Mohammad Nidal al-Shaar', 'Abdul-Salam Haykal'], 'image_url' => 'https://files.manuscdn.com/user_upload_by_module/session_file/310519663888804691/KUscAnJpECrXVska.jpg', 'description' => $shared.' Inspired by the UNDP private-sector dialogue, this matchmaking session focuses on investment readiness, finance, trade, production, logistics, and partnerships.'],
            ['title' => 'Digital Transformation for Public Transport and Logistics', 'speaker_email' => 'abdul.salam.haykal@government-hall11.test', 'hall' => 'M6.1', 'start' => '12:00', 'duration' => 2, 'type' => 'workshop', 'speakers' => ['Abdul-Salam Haykal', 'Mohammad Yaser Bernieh', 'Anas Ajaj'], 'image_url' => 'https://files.manuscdn.com/user_upload_by_module/session_file/310519663888804691/RXSEfpFOzVfctscR.jpg', 'description' => $shared.' A speaker-led workshop on public-sector digital services, transport data, logistics coordination, and technology procurement.'],
            ['title' => 'Heritage Digitisation and Open Cultural Data', 'speaker_email' => 'mustafa.al.mousa@government-hall11.test', 'hall' => 'M5', 'start' => '15:30', 'duration' => 2, 'type' => 'lecture', 'speakers' => ['Mustafa al-Mousa', 'Ahmad Sufian Bayram', 'Waseem AlShikh'], 'image_url' => 'https://files.manuscdn.com/user_upload_by_module/session_file/310519663888804691/tntFCxqSpLhwjTVf.jpg', 'description' => $shared.' A public-interest lecture on digitising heritage collections, cultural archives, public access, and responsible use of technology for preservation.'],
            ['title' => 'Women Founders and Inclusive Innovation in Syria', 'speaker_email' => 'hind.kabawat@government-hall11.test', 'hall' => 'M3.2', 'start' => '11:00', 'duration' => 2, 'type' => 'conference', 'speakers' => ['Hind Kabawat', 'Sara Kataf', 'Feryal Tulaimat'], 'image_url' => 'https://files.manuscdn.com/user_upload_by_module/session_file/310519663888804691/KUscAnJpECrXVska.jpg', 'description' => $shared.' A dialogue on women-led ventures, inclusive finance, skills, ecosystem support, and pathways from ideas to sustainable companies.'],
            ['title' => 'Startup Weekend Syria: Build, Validate and Pitch', 'speaker_email' => 'hamza.hourani@expanded-tech.test', 'hall' => 'M6', 'start' => '09:30', 'duration' => 3, 'type' => 'workshop', 'speakers' => ['Hamza Hourani', 'Bashar Saaduddin Al Jbawi', 'Abdel Malek Al-Mouzayen'], 'image_url' => 'https://files.manuscdn.com/user_upload_by_module/session_file/310519663888804691/FcWdREHzEBFGpoaF.jpg', 'description' => $shared.' A practical builder workshop inspired by Startup Weekend Syria: teams validate a problem, build a prototype, and present a concise pitch to mentors.'],
            ['title' => 'Technology Export Readiness for Syrian SMEs', 'speaker_email' => 'sinan.hatahet@expanded-tech.test', 'hall' => 'M1', 'start' => '14:00', 'duration' => 2, 'type' => 'conference', 'speakers' => ['Sinan Hatahet', 'Waseem AlShikh', 'MHD Yasser Kaziz'], 'image_url' => 'https://files.manuscdn.com/user_upload_by_module/session_file/310519663888804691/RXSEfpFOzVfctscR.jpg', 'description' => $shared.' A founder-led session on export positioning, service delivery, international sales, data protection, and building repeatable technology operations.'],
            ['title' => 'Innovation, Research and the Journey to Market', 'speaker_email' => 'talal.hilali@government-hall11.test', 'hall' => 'M2', 'start' => '16:00', 'duration' => 2, 'type' => 'conference', 'speakers' => ['Talal al-Hilali', 'Sara Kataf', 'Abdul-Salam Haykal'], 'image_url' => 'https://files.manuscdn.com/user_upload_by_module/session_file/310519663888804691/KUscAnJpECrXVska.jpg', 'description' => $shared.' A research-to-market session on universities, applied science, startup formation, technology transfer, and investment pathways.'],
            ['title' => 'Closing Forum: A Five-Year Roadmap for Syria’s Innovation Economy', 'speaker_email' => 'abdul.salam.haykal@government-hall11.test', 'hall' => 'M4', 'start' => '18:00', 'duration' => 2, 'type' => 'conference', 'speakers' => ['Abdul-Salam Haykal', 'Talal al-Hilali', 'Sinan Hatahet', 'Mohammad Fawzy Sukkar'], 'image_url' => 'https://files.manuscdn.com/user_upload_by_module/session_file/310519663888804691/KUscAnJpECrXVska.jpg', 'description' => $shared.' A closing plenary synthesising the week’s discussions on startups, infrastructure, skills, finance, public–private cooperation, and regional growth.'],
        ];
    }
}
