<?php

declare(strict_types=1);

namespace Database\Seeders\RealData;

final class ExpandedTechData
{
    /** Every LinkedIn URL below is a provenance comment/source attached to the record. */
    public static function companies(): array
    {
        return [
            'workiom' => ['name' => 'Workiom', 'sector' => 'SaaS & Workflow Automation', 'description' => 'A cloud workspace for building custom business applications and workflows.', 'linkedin' => 'https://www.linkedin.com/company/workiom', 'website' => 'https://www.workiom.com', 'year' => 2017, 'domain' => 'workiom.com'],
            'techtown' => ['name' => 'TechTown', 'sector' => 'Technology Innovation Hub', 'description' => 'A Syrian digital innovation and entrepreneurship hub connected with technology ecosystem development.', 'linkedin' => 'https://www.linkedin.com/company/techtown', 'website' => 'https://techtown.org', 'year' => 2018, 'domain' => 'techtown.org'],
            'writer' => ['name' => 'WRITER', 'sector' => 'Generative AI', 'description' => 'An enterprise generative AI platform co-founded by Syrian technologist Waseem AlShikh.', 'linkedin' => 'https://www.linkedin.com/company/writer-inc', 'website' => 'https://writer.com', 'year' => 2020, 'domain' => 'writer.com'],
            'salehli' => ['name' => 'Salehli', 'sector' => 'On-demand Services Technology', 'description' => 'A Syrian-founded platform for trusted on-demand services.', 'linkedin' => 'https://www.linkedin.com/in/abdulrhman-arnaout', 'website' => 'https://salehli.com', 'year' => 2023, 'domain' => 'salehli.com'],
            'wusool' => ['name' => 'Wusool', 'sector' => 'Mobility Technology', 'description' => 'A Syrian mobility and transportation technology venture.', 'linkedin' => 'https://www.linkedin.com/in/mhmd3mmr', 'website' => 'https://wusool.app', 'year' => 2023, 'domain' => 'wusool.app'],
            'quizat' => ['name' => 'Quizat', 'sector' => 'Consumer Technology', 'description' => 'A Syrian digital consumer platform founded by Hamza Hourani and co-founder Bashar Saaduddin Al Jbawi.', 'linkedin' => 'https://www.linkedin.com/in/hamza-hourani', 'website' => 'https://quizat.app', 'year' => 2024, 'domain' => 'quizat.app'],
            'yallago' => ['name' => 'YallaGo', 'sector' => 'Mobility & Delivery Technology', 'description' => 'A Syrian mobility and delivery application.', 'linkedin' => 'https://www.linkedin.com/in/bashar-saaduddin-al-jbawi', 'website' => 'https://yallago.app', 'year' => 2023, 'domain' => 'yallago.app'],
            'beeorder' => ['name' => 'BeeOrder', 'sector' => 'Delivery Technology', 'description' => 'A Syrian delivery technology company co-founded by Abdel Malek Al-Mouzayen.', 'linkedin' => 'https://ae.linkedin.com/in/abdelmalekalmouzayen', 'website' => 'https://beeorder.app', 'year' => 2018, 'domain' => 'beeorder.app'],
            'nsave' => ['name' => 'nsave', 'sector' => 'Fintech', 'description' => 'A global banking and financial-access fintech co-founded by Amer Baroudi.', 'linkedin' => 'https://uk.linkedin.com/in/amer-baroudi', 'website' => 'https://www.nsave.com', 'year' => 2022, 'domain' => 'nsave.com'],
            'tradinos' => ['name' => 'Tradinos', 'sector' => 'Software Solutions', 'description' => 'A Damascus-based software solutions company led by Abdel Malek Al-Mouzayen.', 'linkedin' => 'https://de.linkedin.com/company/tradinos', 'website' => 'https://tradinos.com', 'year' => 2011, 'domain' => 'tradinos.com'],
            'digit_innovation_hub' => ['name' => 'DIGIT Innovation Hub', 'sector' => 'Technology Entrepreneurship Hub', 'description' => 'A Syrian innovation hub that supports young technology entrepreneurs and digital innovation.', 'linkedin' => 'https://www.linkedin.com/company/digit-innovation-hub', 'website' => 'https://www.linkedin.com/company/digit-innovation-hub', 'year' => 2022, 'domain' => 'linkedin.com'],
            'ixcoders' => ['name' => 'IXCoders', 'sector' => 'Software Development', 'description' => 'A Syrian software development house building web, mobile, SaaS and ERP systems.', 'linkedin' => 'https://www.linkedin.com/company/ixcoders', 'website' => 'https://ixcoders.com', 'year' => 2014, 'domain' => 'ixcoders.com'],
            'eb_tech' => ['name' => 'E.B. Tech', 'sector' => 'ERP & Software Development', 'description' => 'A Syrian programming and ERP software company led by MHD Yasser Kaziz.', 'linkedin' => 'https://ae.linkedin.com/in/yasserkaziz', 'website' => 'https://www.linkedin.com/in/yasserkaziz', 'year' => 2020, 'domain' => 'linkedin.com'],
            'planlyze' => ['name' => 'Planlyze', 'sector' => 'Applied AI', 'description' => 'An AI tool that analyzes startup ideas for the Syrian market.', 'linkedin' => 'https://sy.linkedin.com/in/sarakataf', 'website' => 'https://planlyze.com', 'year' => 2024, 'domain' => 'planlyze.com'],
            'arageek' => ['name' => 'AraGeek', 'sector' => 'Technology Media & AI', 'description' => 'A prominent Arabic technology and knowledge platform associated with Ahmad Sufian Bayram.', 'linkedin' => 'https://www.linkedin.com/in/ahmadsufianbayram', 'website' => 'https://www.arageek.com', 'year' => 2011, 'domain' => 'arageek.com'],
            'souq' => ['name' => 'Souq.com', 'sector' => 'E-commerce Technology', 'description' => 'Historical regional e-commerce pioneer co-founded by Syrian entrepreneur Ronaldo Mouchawar and acquired by Amazon.', 'linkedin' => 'https://www.linkedin.com/in/ronaldomouchawar', 'website' => 'https://www.amazon.com', 'year' => 2005, 'domain' => 'amazon.com'],
            'careem' => ['name' => 'Careem', 'sector' => 'Super App & Mobility', 'description' => 'The region’s major super app for mobility, delivery and payments.', 'linkedin' => 'https://www.linkedin.com/company/careem', 'website' => 'https://www.careem.com', 'year' => 2012, 'domain' => 'careem.com'],
            'tamara' => ['name' => 'Tamara', 'sector' => 'Fintech', 'description' => 'A Saudi financial technology company offering flexible payments and financial services.', 'linkedin' => 'https://www.linkedin.com/company/tamara', 'website' => 'https://tamara.co', 'year' => 2020, 'domain' => 'tamara.co'],
            'anghami' => ['name' => 'Anghami', 'sector' => 'Consumer Technology', 'description' => 'A leading MENA music streaming and consumer technology platform.', 'linkedin' => 'https://www.linkedin.com/company/anghami', 'website' => 'https://www.anghami.com', 'year' => 2012, 'domain' => 'anghami.com'],
            'tabby' => ['name' => 'Tabby', 'sector' => 'Fintech', 'description' => 'A leading MENA financial services and flexible-payments platform.', 'linkedin' => 'https://www.linkedin.com/company/tabbypay', 'website' => 'https://tabby.ai', 'year' => 2019, 'domain' => 'tabby.ai'],
            // LinkedIn provenance: https://lk.linkedin.com/company/nooncom/ and https://www.linkedin.com/posts/nooncom_techtalent-syria-activity-7462041397160828928-8738
            'noon' => ['name' => 'noon', 'sector' => 'E-commerce & Technology', 'description' => 'A regional digital ecosystem with a technology centre and engineering presence in Damascus, Syria.', 'linkedin' => 'https://lk.linkedin.com/company/nooncom/', 'website' => 'https://www.noon.com', 'year' => 2017, 'domain' => 'noon.com'],
        ];
    }

    public static function people(): array
    {
        $leaders = [
            ['memberships' => [['company' => 'workiom', 'role' => 'Co-Founder'], ['company' => 'techtown', 'role' => 'Co-Founder & CEO']], 'name' => 'Sinan Hatahet', 'email' => 'sinan.hatahet@expanded-tech.test', 'linkedin' => 'https://www.linkedin.com/in/shatahet'],
            ['memberships' => [['company' => 'writer', 'role' => 'Co-Founder & CTO']], 'name' => 'Waseem AlShikh', 'email' => 'waseem.alshikh@expanded-tech.test', 'linkedin' => 'https://www.linkedin.com/in/waseemalshikh'],
            ['memberships' => [['company' => 'salehli', 'role' => 'Co-Founder & CEO']], 'name' => 'AbdulRhman Arnaout', 'email' => 'abdulrhman.arnaout@expanded-tech.test', 'linkedin' => 'https://www.linkedin.com/in/abdulrhman-arnaout'],
            ['memberships' => [['company' => 'wusool', 'role' => 'Founder']], 'name' => 'Mohammad Alammar', 'email' => 'mohammad.alammar@expanded-tech.test', 'linkedin' => 'https://www.linkedin.com/in/mhmd3mmr'],
            ['memberships' => [['company' => 'quizat', 'role' => 'Founder']], 'name' => 'Hamza Hourani', 'email' => 'hamza.hourani@expanded-tech.test', 'linkedin' => 'https://www.linkedin.com/in/hamza-hourani'],
            ['memberships' => [['company' => 'yallago', 'role' => 'Co-Founder']], 'name' => 'Bashar Saaduddin Al Jbawi', 'email' => 'bashar.jbawi@expanded-tech.test', 'linkedin' => 'https://www.linkedin.com/in/bashar-saaduddin-al-jbawi'],
            ['memberships' => [['company' => 'beeorder', 'role' => 'CEO & Co-Founder'], ['company' => 'tradinos', 'role' => 'CEO'], ['company' => 'digit_innovation_hub', 'role' => 'Ecosystem Mentor']], 'name' => 'Abdel Malek Al-Mouzayen', 'email' => 'abdelmalek.mouzayen@expanded-tech.test', 'linkedin' => 'https://ae.linkedin.com/in/abdelmalekalmouzayen'],
            ['memberships' => [['company' => 'nsave', 'role' => 'Co-Founder & CEO']], 'name' => 'Amer Baroudi', 'email' => 'amer.baroudi@expanded-tech.test', 'linkedin' => 'https://uk.linkedin.com/in/amer-baroudi'],
            ['memberships' => [['company' => 'ixcoders', 'role' => 'Co-Founder & COO']], 'name' => 'Mhd Tarek Almalek', 'email' => 'tarek.almalek@expanded-tech.test', 'linkedin' => 'https://sy.linkedin.com/in/mhd-tarek-almalek'],
            ['memberships' => [['company' => 'eb_tech', 'role' => 'Founder & CEO']], 'name' => 'MHD Yasser Kaziz', 'email' => 'yasser.kaziz@expanded-tech.test', 'linkedin' => 'https://ae.linkedin.com/in/yasserkaziz'],
            ['memberships' => [['company' => 'planlyze', 'role' => 'CMO & Co-Founder']], 'name' => 'Sara Kataf', 'email' => 'sara.kataf@expanded-tech.test', 'linkedin' => 'https://sy.linkedin.com/in/sarakataf'],
            ['memberships' => [['company' => 'arageek', 'role' => 'Founder']], 'name' => 'Ahmad Sufian Bayram', 'email' => 'ahmad.arageek@expanded-tech.test', 'linkedin' => 'https://www.linkedin.com/in/ahmadsufianbayram'],
            ['memberships' => [['company' => 'souq', 'role' => 'Co-Founder']], 'name' => 'Ronaldo Mouchawar', 'email' => 'ronaldo.mouchawar@expanded-tech.test', 'linkedin' => 'https://www.linkedin.com/in/ronaldomouchawar'],
            ['memberships' => [['company' => 'careem', 'role' => 'Co-Founder & CEO']], 'name' => 'Mudassir Sheikha', 'email' => 'mudassir.sheikha@expanded-tech.test', 'linkedin' => 'https://www.linkedin.com/in/mudassirsheikha'],
            ['memberships' => [['company' => 'tamara', 'role' => 'Co-Founder & CEO']], 'name' => 'Abdulmajeed Alsukhan', 'email' => 'abdulmajeed.alsukhan@expanded-tech.test', 'linkedin' => 'https://www.linkedin.com/in/abdulmajeed-alsukhan-13000a58'],
            ['memberships' => [['company' => 'anghami', 'role' => 'Co-Founder']], 'name' => 'Elie Habib', 'email' => 'elie.habib@expanded-tech.test', 'linkedin' => 'https://www.linkedin.com/in/eliehabib'],
            ['memberships' => [['company' => 'tabby', 'role' => 'Co-Founder & CEO']], 'name' => 'Hosam Arab', 'email' => 'hosam.arab@expanded-tech.test', 'linkedin' => 'https://ae.linkedin.com/in/hosam'],
            ['memberships' => [['company' => 'noon', 'role' => 'Vice President of Special Projects Technology']], 'name' => 'Nizar Zarka', 'email' => 'nizar.zarka@expanded-tech.test', 'linkedin' => 'https://ae.linkedin.com/in/nizar-zarka-phd-7ab22114'],
        ];

        return array_map(static function (array $person): array {
            $memberships = $person['memberships'];
            return [
                'key' => $memberships[0]['company'].'-'.substr(md5($person['name']), 0, 6),
                'company' => $memberships[0]['company'],
                'companies' => array_values(array_unique(array_column($memberships, 'company'))),
                'memberships' => $memberships,
                'name' => $person['name'],
                'email' => $person['email'],
                'linkedin' => $person['linkedin'],
                'role' => $memberships[0]['role'],
                'asset' => strtolower(preg_replace('/[^a-z0-9]+/i', '-', $person['name'])),
            ];
        }, $leaders);
    }
}
