<?php

namespace Database\Seeders;

use App\Models\Announcement;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

/**
 * Exhibition announcements based on real Syrian exhibition messaging and formats.
 * Sources reviewed:
 * - https://seife.gov.sy/?lang=en (Damascus International Fair 63: sectors, B2B, visitors, partnerships)
 * - https://hitechexpo.io/ (Syria HiTech: ICT, software, telecom, cybersecurity, digital transformation)
 * - https://www.ablcc.org/news-events/fairs-conferences-exhibitions/the-62nd-damascus-international-fair (fair sectors and visitor motivations)
 * - https://exhibitionmakers.com/en/information-and-communications-technology-exhibition-damascus-syria-hi-tech-damascus-fair-center/ (HiTech objectives and exhibitor/visitor messaging)
 * Image provenance: authentic Damascus Fairgrounds, Syria HiTech, Damascus International Fair,
 * and exhibition photographs collected from public exhibition coverage; each local derivative is
 * stored under database/assets/announcements and attached with preservingOriginal().
 */
final class ExhibitionAnnouncementsSeeder extends Seeder
{
    public function run(): void
    {
        $announcements = [
            // Exhibitors only: participation, operations, B2B, and exhibitor services.
            ['title' => 'Exhibitor registration desk is open', 'receiver' => 'Exhibitors', 'description' => 'Exhibitors can confirm company details, booth contacts, badges, and operating requirements at the exhibition administration desk before opening.', 'image_file' => 'announcements/announcement-01.jpg'],
            ['title' => 'Reserve your B2B matchmaking slots', 'receiver' => 'Exhibitors', 'description' => 'Registered exhibitors may request focused B2B meetings with distributors, investors, public-sector representatives, and professional buyers during the fair.', 'image_file' => 'announcements/announcement-02.jpg'],
            ['title' => 'Exhibitor badge collection and access rules', 'receiver' => 'Exhibitors', 'description' => 'Company representatives should collect exhibitor badges and follow the approved access schedule for loading, setup, demonstrations, and daily closing.', 'image_file' => 'announcements/announcement-03.jpg'],
            ['title' => 'Technology and ICT exhibitors: product demo schedule', 'receiver' => 'Exhibitors', 'description' => 'HiTech-focused exhibitors can submit short product demonstrations covering software, smart devices, cloud, cybersecurity, electronic payment, and digital transformation.', 'image_file' => 'announcements/announcement-04.jpg'],
            ['title' => 'Exhibitor move-in and booth preparation window', 'receiver' => 'Exhibitors', 'description' => 'The operations team has published the booth preparation window. Companies must coordinate deliveries, signage, technical equipment, and safety requirements in advance.', 'image_file' => 'announcements/announcement-05.jpg'],
            ['title' => 'Business matchmaking for construction and infrastructure companies', 'receiver' => 'Exhibitors', 'description' => 'Companies presenting construction, engineering, infrastructure, and reconstruction solutions may submit their priority meeting profiles for matchmaking with qualified visitors.', 'image_file' => 'announcements/announcement-06.jpg'],
            ['title' => 'Exhibitor media and press interview requests', 'receiver' => 'Exhibitors', 'description' => 'Participating companies can register press-interview requests and provide approved company information for the exhibition communications team.', 'image_file' => 'announcements/announcement-07.jpg'],
            ['title' => 'Digital payment and fintech showcase registration', 'receiver' => 'Exhibitors', 'description' => 'Fintech, banking systems, electronic payment, and financial-inclusion exhibitors are invited to submit their showcase details for the dedicated technology programme.', 'image_file' => 'announcements/announcement-08.jpg'],
            ['title' => 'Exhibitor sustainability and energy solutions directory', 'receiver' => 'Exhibitors', 'description' => 'Energy, environment, solar, efficiency, and sustainable-production companies may submit their profiles for inclusion in the fair’s business directory.', 'image_file' => 'announcements/announcement-09.jpg'],
            ['title' => 'Final exhibitor profile and logo review', 'receiver' => 'Exhibitors', 'description' => 'Exhibitors should review the spelling of their company name, sector, booth code, contact details, and logo before the public programme is finalized.', 'image_file' => 'announcements/announcement-10.jpg'],

            // Visitors only: registration, access, programme discovery, and visitor services.
            ['title' => 'Visitor registration for the Damascus International Fair', 'receiver' => 'visitors', 'description' => 'Visitors can register to discover companies and products across technology, construction, food, energy, industry, trade, and services at the Damascus Fairgrounds.', 'image_file' => 'announcements/announcement-11.jpg'],
            ['title' => 'Plan your visit by sector', 'receiver' => 'visitors', 'description' => 'Use the exhibition programme to plan visits to the technology, industry, construction, food, energy, and services areas according to your interests.', 'image_file' => 'announcements/announcement-12.jpg'],
            ['title' => 'HiTech visitor programme: ICT and digital transformation', 'receiver' => 'visitors', 'description' => 'Professional visitors can explore software, telecommunications, smart devices, cybersecurity, cloud integration, electronic payment, and digital-transformation solutions.', 'image_file' => 'announcements/announcement-13.jpg'],
            ['title' => 'Meet Syrian startups and technology builders', 'receiver' => 'visitors', 'description' => 'The startup and innovation areas bring together Syrian founders, developers, service providers, and technology teams presenting practical products and partnerships.', 'image_file' => 'announcements/announcement-14.jpg'],
            ['title' => 'Visitor guidance for public-sector and reconstruction solutions', 'receiver' => 'visitors', 'description' => 'Decision-makers and project teams can use the fair to compare infrastructure, engineering, energy, logistics, and digital services relevant to reconstruction and development.', 'image_file' => 'announcements/announcement-15.jpg'],
            ['title' => 'Discover electronic payment and banking technology', 'receiver' => 'visitors', 'description' => 'Visitors interested in fintech can meet exhibitors presenting electronic payment, banking systems, identity, security, and financial technology solutions.', 'image_file' => 'announcements/announcement-16.jpg'],
            ['title' => 'Visitor information point and accessibility support', 'receiver' => 'visitors', 'description' => 'The visitor information point provides directions, programme guidance, hall information, and assistance with navigating the Damascus Fairgrounds.', 'image_file' => 'announcements/announcement-17.jpg'],
            ['title' => 'Professional visitors: bring your partnership brief', 'receiver' => 'visitors', 'description' => 'Managers, investors, entrepreneurs, and buyers are encouraged to prepare a short partnership brief before meeting companies and exhibitors at the fair.', 'image_file' => 'announcements/announcement-18.jpg'],
            ['title' => 'Family and public visitor evening programme', 'receiver' => 'visitors', 'description' => 'The evening programme combines discovery, demonstrations, and exhibition activities for visitors who want to experience the fair outside standard business hours.', 'image_file' => 'announcements/announcement-19.jpg'],
            ['title' => 'Visitor feedback and fair experience survey', 'receiver' => 'visitors', 'description' => 'Visitors can share feedback about halls, exhibitors, services, and the programme through the exhibition platform to help improve future editions.', 'image_file' => 'announcements/announcement-20.jpg'],

            // Shared audience: public programme, services, safety, and general fair notices.
            ['title' => 'Damascus Fairgrounds weekly programme published', 'receiver' => 'all', 'description' => 'The public programme brings together exhibition hours, technology sessions, business meetings, demonstrations, and sector-focused activities across the fairgrounds.', 'image_file' => 'announcements/announcement-21.jpg'],
            ['title' => 'One fair, multiple sectors and partnership opportunities', 'receiver' => 'all', 'description' => 'The exhibition connects companies, visitors, institutions, and investors across construction, food, technology, trade, energy, sustainability, and industry.', 'image_file' => 'announcements/announcement-22.jpg'],
            ['title' => 'Welcome to the Damascus International Fair', 'receiver' => 'all', 'description' => 'Welcome to a professional meeting point for Syrian, Arab, and international companies, decision-makers, entrepreneurs, and visitors at the Damascus Fairgrounds.', 'image_file' => 'announcements/announcement-23.jpg'],
            ['title' => 'HiTech at Damascus Fairgrounds', 'receiver' => 'all', 'description' => 'HiTech activities highlight ICT infrastructure, software, smart automation, AI, IoT, cybersecurity, cloud services, and digital transformation.', 'image_file' => 'announcements/announcement-24.jpg'],
            ['title' => 'Business meetings and cross-border cooperation', 'receiver' => 'all', 'description' => 'The fair provides a structured setting for introductions, commercial discussions, distribution opportunities, investment conversations, and regional cooperation.', 'image_file' => 'announcements/announcement-25.jpg'],
            ['title' => 'Safety, access, and fairground conduct notice', 'receiver' => 'all', 'description' => 'All participants should follow access controls, staff instructions, emergency routes, equipment safety rules, and the operating guidelines of the Damascus Fairgrounds.', 'image_file' => 'announcements/announcement-26.jpg'],
            ['title' => 'Official exhibition photography and media coverage', 'receiver' => 'all', 'description' => 'The communications team is documenting the fair, its exhibitors, demonstrations, and visitor activity. Media requests should be coordinated with exhibition administration.', 'image_file' => 'announcements/announcement-27.jpg'],
            ['title' => 'Explore innovation from Syrian and regional companies', 'receiver' => 'all', 'description' => 'The programme presents practical products, services, and ideas from Syrian and regional companies across technology, industry, services, and emerging business sectors.', 'image_file' => 'announcements/announcement-28.jpg'],
            ['title' => 'Closing-day programme and final networking hours', 'receiver' => 'all', 'description' => 'The closing programme offers a final opportunity to revisit booths, complete introductions, exchange contacts, and follow up on promising partnerships.', 'image_file' => 'announcements/announcement-29.jpg'],
            ['title' => 'Thank you for participating in the exhibition', 'receiver' => 'all', 'description' => 'Thank you to exhibitors, visitors, speakers, partners, and the exhibition operations teams who contributed to a productive and welcoming fair experience.', 'image_file' => 'announcements/announcement-30.jpg'],
        ];

        foreach ($announcements as $item) {
            $imageFile = $item['image_file'];
            unset($item['image_file']);
            $announcement = Announcement::updateOrCreate(['title' => $item['title']], array_merge($item, ['is_active' => true]));
            $path = database_path('assets/'.$imageFile);
            if (! is_file($path)) {
                throw new \RuntimeException("Missing local announcement asset: database/assets/{$imageFile}");
            }
            $announcement->clearMediaCollection('announcements');
            $announcement->addMedia($path)
                ->preservingOriginal()
                ->usingFileName(basename($path))
                ->toMediaCollection('announcements');
        }
    }
}
