<?php

namespace Database\Seeders;

use App\Models\Announcement;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class AnnouncementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $announcements = [
            [
                'title' => 'Welcome to EMS - Exhibition Management System',
                'receiver' => 'all',
                'description' => 'Welcome to the official Exhibition Management System. Explore booths, events, and services easily through our platform.',
                'image_file' => 'fawzy.jpg',
                'is_active' => true,
            ],
            [
                'title' => 'RBCs Volunteer Team Support',
                'receiver' => 'visitors',
                'description' => 'The RBCs volunteer team is available across the exhibition halls to assist you and document the daily seminars.',
                'image_file' => 'RBCs.png',
                'is_active' => true,
            ],
            [
                'title' => 'Al-Awael Software Solutions Booth',
                'receiver' => 'all',
                'description' => 'Visit Al-Awael booth in the main hall to discover cutting-edge enterprise software and mobile application solutions.',
                'image_file' => 'alawael.png',
                'is_active' => true,
            ],
            [
                'title' => 'Elba3eth Tech Sponsorship',
                'receiver' => 'Exhibitors',
                'description' => 'We are proud to announce Elba3eth as a technical sponsor. Join their session on hardware automation and embedded systems.',
                'image_file' => 'Elba3eth.png',
                'is_active' => true,
            ],
            [
                'title' => 'Elsaadeh Hospitality Services',
                'receiver' => 'all',
                'description' => 'Elsaadeh provides premium hospitality and refreshment services throughout the conference palace for all attendees.',
                'image_file' => 'elsaadeh.png',
                'is_active' => true,
            ],
            [
                'title' => 'RGBs: UI/UX & Dark Mode Workshop',
                'receiver' => 'visitors',
                'description' => 'Join the RGBs team for an interactive workshop focusing on modern user interface design and implementing perfect dark mode themes.',
                'image_file' => 'RGBs.jpg',
                'is_active' => true,
            ],
            [
                'title' => 'Backend API Integration Masterclass',
                'receiver' => 'Exhibitors',
                'description' => 'A technical masterclass detailing how to build and optimize backend APIs for seamless mobile frontend integration.',
                'image_file' => 'fawzy.jpg',
                'is_active' => true,
            ],
            [
                'title' => 'Daily Exhibition Coverage by RBCs',
                'receiver' => 'all',
                'description' => 'Stay updated! The RBCs team will be publishing daily summaries and lecture transcripts directly to the platform.',
                'image_file' => 'RBCs.png',
                'is_active' => true,
            ],
            [
                'title' => 'Al-Awael Competitive Programming Challenge',
                'receiver' => 'all',
                'description' => 'Participate in the algorithmic problem-solving challenge hosted by Al-Awael and win exclusive tech prizes.',
                'image_file' => 'alawael.png',
                'is_active' => true,
            ],
            [
                'title' => 'Hardware Prototypes Exhibition',
                'receiver' => 'all',
                'description' => 'Explore the latest automated grading machines and color sensor prototypes at the Elba3eth engineering pavilion.',
                'image_file' => 'Elba3eth.png',
                'is_active' => true,
            ],
            [
                'title' => 'Exclusive VIP Lounge Access',
                'receiver' => 'Exhibitors',
                'description' => 'VIP ticket holders can now access the exclusive lounge managed by Elsaadeh for private meetings and networking.',
                'image_file' => 'elsaadeh.png',
                'is_active' => true,
            ],
            [
                'title' => 'RGBs Graphic Design Showcase',
                'receiver' => 'all',
                'description' => 'Check out the visual identity and graphic design showcase presented by the creative minds at RGBs.',
                'image_file' => 'RGBs.jpg',
                'is_active' => false, // Example of an inactive announcement
            ],
            [
                'title' => 'Scheduled System Maintenance',
                'receiver' => 'all',
                'description' => 'Please note that the EMS backend servers will undergo brief maintenance tonight to optimize database performance.',
                'image_file' => 'fawzy.jpg',
                'is_active' => true,
            ],
            [
                'title' => 'Volunteer Shift Registration Deadline',
                'receiver' => 'visitor',
                'description' => 'Attention all RBCs members: Please confirm your exhibition hall shifts before the end of the day.',
                'image_file' => 'RBCs.png',
                'is_active' => true,
            ],
            [
                'title' => 'Closing Ceremony Sponsored by Al-Awael',
                'receiver' => 'all',
                'description' => 'Join us for the grand closing ceremony of the exhibition, proudly sponsored by Al-Awael Tech.',
                'image_file' => 'alawael.png',
                'is_active' => true,
            ],
        ];

        foreach ($announcements as $item) {
            $imageFileName = $item['image_file'];
            unset($item['image_file']);
            $announcement = Announcement::create($item);
            $imagePath = database_path('assets/' . $imageFileName);

            if (File::exists($imagePath)) {
                $announcement->addMedia($imagePath)
                    ->preservingOriginal()
                    ->toMediaCollection('announcements');
            }
        }
    }
}
