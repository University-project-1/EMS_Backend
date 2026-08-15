<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enum\BusinessSectors;

final class RealTechData
{
    /**
     * Every company below has a local logo and at least two local gallery files
     * under database/assets/real_tech_companies/{slug}/ before this dataset is used.
     */
    public static function companies(): array
    {
        return [
            // LinkedIn: https://be.linkedin.com/company/syriatel | Source: https://www.syriatel.sy/
            'syriatel' => ['name' => 'Syriatel Mobile Telecom', 'sector' => BusinessSectors::TELECOMMUNICATIONS->value, 'website' => 'https://www.syriatel.sy/', 'linkedin' => 'https://be.linkedin.com/company/syriatel', 'description' => 'Syrian mobile network operator providing telecommunications, connectivity, data, and digital services.', 'year' => 2000],
            // LinkedIn: https://www.linkedin.com/company/syriasmart-sy/ | Source: https://www.syriasmart.net/
            'syria-smart' => ['name' => 'Syria Smart', 'sector' => BusinessSectors::TECH->value, 'website' => 'http://www.syriasmart.net/', 'linkedin' => 'https://www.linkedin.com/company/syriasmart-sy/', 'description' => 'Syrian IT services and consulting company focused on maintenance, advisory, and information-technology solutions.', 'year' => 2012],
            // LinkedIn: https://www.linkedin.com/company/paymera-sy/ | Source: https://paymera.sy/
            'paymera' => ['name' => 'Paymera', 'sector' => BusinessSectors::FINANCE->value, 'website' => 'https://paymera.sy/', 'linkedin' => 'https://www.linkedin.com/company/paymera-sy/', 'description' => 'Syrian digital-payment and fintech company providing payment infrastructure and financial services.', 'year' => 2025],
            // LinkedIn/source note: official website is verified; official LinkedIn company page was not confirmed.
            'sham-cash' => ['name' => 'Sham Cash', 'sector' => BusinessSectors::FINANCE->value, 'website' => 'https://shamcash.sy/', 'linkedin' => 'https://www.linkedin.com/posts/kshaar_advisory_sham-cash-a-risky-escape-route-sham-activity-7383383070797852672-5ZOK', 'description' => 'Syrian digital-wallet application for sending, receiving, and managing financial transactions.', 'year' => 2024],
            // LinkedIn: https://www.linkedin.com/company/88ninety/ | Source: https://88ninety.com/
            '88ninety' => ['name' => '88ninety', 'sector' => BusinessSectors::TECH->value, 'website' => 'https://88ninety.com/', 'linkedin' => 'https://www.linkedin.com/company/88ninety/', 'description' => 'Technology and digital-product company creating smart software solutions for forward-thinking enterprises.', 'year' => 2018],
            // LinkedIn: https://sy.linkedin.com/company/veracodia | Source: https://veracodia.com/
            'veracodia' => ['name' => 'VeraCodia', 'sector' => BusinessSectors::TECH->value, 'website' => 'https://veracodia.com/', 'linkedin' => 'https://sy.linkedin.com/company/veracodia', 'description' => 'Damascus-based technology solutions provider for software development, digital transformation, IT consulting, and Syrian tech talent.', 'year' => 2025],
            // LinkedIn: https://www.linkedin.com/company/mtn-syria/ | Source: https://www.mtn.com/
            'mtn-syria' => ['name' => 'MTN Syria', 'sector' => BusinessSectors::TELECOMMUNICATIONS->value, 'website' => 'https://www.mtn.com/', 'linkedin' => 'https://www.linkedin.com/company/mtn-syria/', 'description' => 'Syrian telecommunications operator and part of the MTN brand history in Syria.', 'year' => 2007],
            // LinkedIn: https://www.linkedin.com/company/microsoft/ | Source: https://www.microsoft.com/
            'microsoft' => ['name' => 'Microsoft', 'sector' => BusinessSectors::TECH->value, 'website' => 'https://www.microsoft.com/', 'linkedin' => 'https://www.linkedin.com/company/microsoft/', 'description' => 'Global technology company focused on software, cloud computing, developer tools, AI, and enterprise services.', 'year' => 1975],
            // LinkedIn: https://www.linkedin.com/company/google/ | Source: https://about.google/
            'google' => ['name' => 'Google', 'sector' => BusinessSectors::TECH->value, 'website' => 'https://about.google/', 'linkedin' => 'https://www.linkedin.com/company/google/', 'description' => 'Global technology company operating search, cloud, AI, advertising, and digital-product platforms.', 'year' => 1998],
            // LinkedIn: https://www.linkedin.com/company/apple/ | Source: https://www.apple.com/
            'apple' => ['name' => 'Apple', 'sector' => BusinessSectors::TECH->value, 'website' => 'https://www.apple.com/', 'linkedin' => 'https://www.linkedin.com/company/apple/', 'description' => 'Global technology company designing consumer devices, operating systems, services, and developer platforms.', 'year' => 1976],
            // LinkedIn: https://www.linkedin.com/company/amazon/ | Source: https://www.amazon.com/
            'amazon' => ['name' => 'Amazon', 'sector' => BusinessSectors::TECH->value, 'website' => 'https://www.amazon.com/', 'linkedin' => 'https://www.linkedin.com/company/amazon/', 'description' => 'Global technology and commerce company operating cloud, logistics, marketplace, devices, and digital services.', 'year' => 1994],
            // LinkedIn: https://www.linkedin.com/company/nvidia/ | Source: https://www.nvidia.com/
            'nvidia' => ['name' => 'NVIDIA', 'sector' => BusinessSectors::TECH->value, 'website' => 'https://www.nvidia.com/', 'linkedin' => 'https://www.linkedin.com/company/nvidia/', 'description' => 'Global computing company specializing in accelerated computing, graphics, AI infrastructure, and platforms.', 'year' => 1993],
            // LinkedIn: https://www.linkedin.com/company/ibm/ | Source: https://www.ibm.com/
            'ibm' => ['name' => 'IBM', 'sector' => BusinessSectors::TECH->value, 'website' => 'https://www.ibm.com/', 'linkedin' => 'https://www.linkedin.com/company/ibm/', 'description' => 'Global enterprise technology company focused on hybrid cloud, AI, consulting, and infrastructure.', 'year' => 1911],
            // LinkedIn: https://www.linkedin.com/company/oracle/ | Source: https://www.oracle.com/
            'oracle' => ['name' => 'Oracle', 'sector' => BusinessSectors::TECH->value, 'website' => 'https://www.oracle.com/', 'linkedin' => 'https://www.linkedin.com/company/oracle/', 'description' => 'Global enterprise software and cloud-infrastructure company.', 'year' => 1977],
            // LinkedIn: https://www.linkedin.com/company/cisco/ | Source: https://www.cisco.com/
            'cisco' => ['name' => 'Cisco', 'sector' => BusinessSectors::TECH->value, 'website' => 'https://www.cisco.com/', 'linkedin' => 'https://www.linkedin.com/company/cisco/', 'description' => 'Global networking, cybersecurity, observability, and enterprise-technology company.', 'year' => 1984],
            // LinkedIn: https://www.linkedin.com/company/sap/ | Source: https://www.sap.com/
            'sap' => ['name' => 'SAP', 'sector' => BusinessSectors::TECH->value, 'website' => 'https://www.sap.com/', 'linkedin' => 'https://www.linkedin.com/company/sap/', 'description' => 'Global enterprise application and business-technology company.', 'year' => 1972],
            // LinkedIn: https://www.linkedin.com/company/huawei/ | Source: https://www.huawei.com/
            'huawei' => ['name' => 'Huawei', 'sector' => BusinessSectors::TECH->value, 'website' => 'https://www.huawei.com/', 'linkedin' => 'https://www.linkedin.com/company/huawei/', 'description' => 'Global information and communications technology infrastructure and smart-device company.', 'year' => 1987],
        ];
    }

    public static function people(): array
    {
        return [
            // LinkedIn: https://sy.linkedin.com/in/mohammed-fawzy-sukkar | local project asset: database/assets/people/mohammed_fawzy_sukkar/avatar/fawzy.jpg
            ['key' => 'mohammed_fawzy_sukkar', 'company' => '88ninety', 'name' => 'Mohammed Fawzy Sukkar', 'email' => 'mohammed.fawzy.sukkar@example.test', 'role' => 'Technology / Company System User', 'linkedin' => 'https://sy.linkedin.com/in/mohammed-fawzy-sukkar', 'source' => 'https://www.linkedin.com/company/88ninety/', 'asset' => 'mohammed_fawzy_sukkar'],
            // LinkedIn: https://www.linkedin.com/in/sami-m-hijazi | Company: https://www.linkedin.com/company/88ninety
            ['key' => 'sami_hijazi', 'company' => '88ninety', 'name' => 'Sami Hijazi', 'email' => 'sami.hijazi@example.test', 'role' => 'Founder / Senior AI and .NET Solutions Architect', 'linkedin' => 'https://www.linkedin.com/in/sami-m-hijazi', 'source' => 'https://www.linkedin.com/company/88ninety', 'asset' => 'sami_hijazi'],
            // LinkedIn: https://www.linkedin.com/in/salim-hijazi-6592a619 | Company listing: https://www.linkedin.com/company/88ninety
            ['key' => 'salim_hijazi', 'company' => '88ninety', 'name' => 'Salim Hijazi', 'email' => 'salim.hijazi@example.test', 'role' => '.NET Backend Engineer / Azure Cloud and API Development', 'linkedin' => 'https://www.linkedin.com/in/salim-hijazi-6592a619', 'source' => 'https://www.linkedin.com/company/88ninety', 'asset' => 'salim_hijazi'],
            // LinkedIn: https://tr.linkedin.com/in/feryal-tulaimat-647bb324 | Company association: https://www.linkedin.com/company/88ninety
            ['key' => 'feryal_tulaimat', 'company' => '88ninety', 'name' => 'Feryal Tulaimat', 'email' => 'feryal.tulaimat@example.test', 'role' => 'Software Developer / Scrum Master', 'linkedin' => 'https://tr.linkedin.com/in/feryal-tulaimat-647bb324', 'source' => 'https://www.linkedin.com/company/88ninety', 'asset' => 'feryal_tulaimat'],
            // LinkedIn: https://www.linkedin.com/posts/syriatel_we-are-pleased-to-welcome-mr-mohamad-mo mojehed-activity-7463311876454559744-3qTy
            ['key' => 'mohamad_mojahed_kouja', 'company' => 'syriatel', 'name' => 'Mohamad Mojahed Kouja', 'email' => 'mohamad.mojahed.kouja@example.test', 'role' => 'Chief Financial Officer', 'linkedin' => 'https://www.linkedin.com/in/mohamad-mojahed-kouja/', 'source' => 'https://www.linkedin.com/posts/syriatel_we-are-pleased-to-welcome-mr-mohamad-activity-7463311876454559744-3qTy', 'asset' => 'mohamad_mojahed_kouja'],
            // LinkedIn: https://ca.linkedin.com/in/sulafa-asfari-a1550349 | Company: https://sy.linkedin.com/company/syriatel
            ['key' => 'sulafa_asfari', 'company' => 'syriatel', 'name' => 'Sulafa Asfari', 'email' => 'sulafa.asfari@example.test', 'role' => 'Head of Employee Welfare Section / HR Department', 'linkedin' => 'https://ca.linkedin.com/in/sulafa-asfari-a1550349', 'source' => 'https://sy.linkedin.com/company/syriatel', 'asset' => 'sulafa_asfari'],
            // LinkedIn: https://www.linkedin.com/in/majd-habash-141b27194 | Public Syriatel association: https://www.linkedin.com/posts/nour-nassouri-hr_syriatel-activity-7387064655455625218-cTWC
            ['key' => 'majd_habash', 'company' => 'syriatel', 'name' => 'Majd Habash', 'email' => 'majd.habash@example.test', 'role' => 'HR Director / Talent Management and Organizational Development', 'linkedin' => 'https://www.linkedin.com/in/majd-habash-141b27194', 'source' => 'https://www.linkedin.com/posts/nour-nassouri-hr_syriatel-activity-7387064655455625218-cTWC', 'asset' => 'majd_habash'],
            // LinkedIn: https://sy.linkedin.com/in/samy-alnaimy
            ['key' => 'mhd_samy_alnaimy', 'company' => 'paymera', 'name' => 'Mhd Samy AlNaimy', 'email' => 'mhd.samy.alnaimy@example.test', 'role' => 'Development Manager', 'linkedin' => 'https://sy.linkedin.com/in/samy-alnaimy', 'source' => 'https://www.linkedin.com/company/paymera-sy/', 'asset' => 'mhd_samy_alnaimy'],
            // LinkedIn: https://sy.linkedin.com/company/veracodia (public employee listing includes Hamza Rabah)
            ['key' => 'hamza_rabah', 'company' => 'veracodia', 'name' => 'Hamza Rabah', 'email' => 'hamza.rabah@example.test', 'role' => 'Technology Founder / Team Member', 'linkedin' => 'https://www.linkedin.com/in/hamza-rabah/', 'source' => 'https://veracodia.com/', 'asset' => 'hamza_rabah'],
            // LinkedIn: https://sy.linkedin.com/in/anas-ajaj-5b80351b
            ['key' => 'anas_ajaj', 'company' => 'mtn-syria', 'name' => 'Anas Ajaj', 'email' => 'anas.ajaj@example.test', 'role' => 'Chief Human Resources Officer', 'linkedin' => 'https://sy.linkedin.com/in/anas-ajaj-5b80351b', 'source' => 'https://www.linkedin.com/company/mtn-syria/', 'asset' => 'anas_ajaj'],
            // Official Microsoft profile: https://news.microsoft.com/source/exec/satya-nadella/ | LinkedIn search profile: https://www.linkedin.com/in/satyanadella/
            ['key' => 'satya_nadella', 'company' => 'microsoft', 'name' => 'Satya Nadella', 'email' => 'satya.nadella@example.test', 'role' => 'Chairman and Chief Executive Officer', 'linkedin' => 'https://www.linkedin.com/in/satyanadella/', 'source' => 'https://news.microsoft.com/source/exec/satya-nadella/', 'asset' => 'satya_nadella'],
            // Official NVIDIA profile: https://www.nvidia.com/en-us/about-nvidia/board-of-directors/jensen-huang/
            ['key' => 'jensen_huang', 'company' => 'nvidia', 'name' => 'Jensen Huang', 'email' => 'jensen.huang@example.test', 'role' => 'Founder and Chief Executive Officer', 'linkedin' => 'https://www.linkedin.com/in/jenhsunhuang/', 'source' => 'https://www.nvidia.com/en-us/about-nvidia/board-of-directors/jensen-huang/', 'asset' => 'jensen_huang'],
            // Official Google profile: https://blog.google/authors/sundar-pichai/
            ['key' => 'sundar_pichai', 'company' => 'google', 'name' => 'Sundar Pichai', 'email' => 'sundar.pichai@example.test', 'role' => 'Chief Executive Officer', 'linkedin' => 'https://www.linkedin.com/in/sundarpichai/', 'source' => 'https://blog.google/authors/sundar-pichai/', 'asset' => 'sundar_pichai'],
            // Public Apple leadership source: https://www.apple.com/leadership/tim-cook/
            ['key' => 'tim_cook', 'company' => 'apple', 'name' => 'Tim Cook', 'email' => 'tim.cook@example.test', 'role' => 'Chief Executive Officer', 'linkedin' => 'https://www.linkedin.com/in/timcook/', 'source' => 'https://www.apple.com/leadership/tim-cook/', 'asset' => 'tim_cook'],
            // Public Amazon leadership source: https://www.aboutamazon.com/about-us/leadership
            ['key' => 'andy_jassy', 'company' => 'amazon', 'name' => 'Andy Jassy', 'email' => 'andy.jassy@example.test', 'role' => 'President and Chief Executive Officer', 'linkedin' => 'https://www.linkedin.com/in/andy-jassy/', 'source' => 'https://www.aboutamazon.com/about-us/leadership', 'asset' => 'andy_jassy'],
            // Official IBM profile: https://www.ibm.com/leadership/arvind-krishna
            ['key' => 'arvind_krishna', 'company' => 'ibm', 'name' => 'Arvind Krishna', 'email' => 'arvind.krishna@example.test', 'role' => 'Chairman and Chief Executive Officer', 'linkedin' => 'https://www.linkedin.com/in/arvindkrishna/', 'source' => 'https://www.ibm.com/leadership/arvind-krishna', 'asset' => 'arvind_krishna'],
            // Official Oracle profile: https://www.oracle.com/corporate/executives/safra-catz/
            ['key' => 'safra_catz', 'company' => 'oracle', 'name' => 'Safra Catz', 'email' => 'safra.catz@example.test', 'role' => 'Chief Executive Officer', 'linkedin' => 'https://www.linkedin.com/in/safra-catz/', 'source' => 'https://www.oracle.com/corporate/executives/safra-catz/', 'asset' => 'safra_catz'],
            // Public Cisco leadership source: https://www.cisco.com/c/en/us/about/leadership.html
            ['key' => 'chuck_robbins', 'company' => 'cisco', 'name' => 'Chuck Robbins', 'email' => 'chuck.robbins@example.test', 'role' => 'Chair and Chief Executive Officer', 'linkedin' => 'https://www.linkedin.com/in/chuckrobbins/', 'source' => 'https://www.cisco.com/c/en/us/about/leadership.html', 'asset' => 'chuck_robbins'],
        ];
    }
}
