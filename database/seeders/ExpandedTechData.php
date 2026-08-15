<?php

declare(strict_types=1);

namespace Database\Seeders;

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
        ];
    }

    public static function people(): array
    {
        $leaders = [
            ['workiom','Sinan Hatahet','sinan.hatahet@expanded-tech.test','https://www.linkedin.com/in/shatahet','Co-Founder'],
            ['techtown','Sinan Hatahet','sinan.hatahet.techtown@expanded-tech.test','https://www.linkedin.com/in/shatahet','Co-Founder & CEO'],
            ['writer','Waseem AlShikh','waseem.alshikh@expanded-tech.test','https://www.linkedin.com/in/waseemalshikh','Co-Founder & CTO'],
            ['salehli','AbdulRhman Arnaout','abdulrhman.arnaout@expanded-tech.test','https://www.linkedin.com/in/abdulrhman-arnaout','Co-Founder & CEO'],
            ['wusool','Mohammad Alammar','mohammad.alammar@expanded-tech.test','https://www.linkedin.com/in/mhmd3mmr','Founder'],
            ['quizat','Hamza Hourani','hamza.hourani@expanded-tech.test','https://www.linkedin.com/in/hamza-hourani','Founder'],
            ['yallago','Bashar Saaduddin Al Jbawi','bashar.jbawi@expanded-tech.test','https://www.linkedin.com/in/bashar-saaduddin-al-jbawi','Co-Founder'],
            ['beeorder','Abdel Malek Al-Mouzayen','abdelmalek.mouzayen@expanded-tech.test','https://ae.linkedin.com/in/abdelmalekalmouzayen','CEO & Co-Founder'],
            ['nsave','Amer Baroudi','amer.baroudi@expanded-tech.test','https://uk.linkedin.com/in/amer-baroudi','Co-Founder & CEO'],
            ['tradinos','Abdel Malek Al-Mouzayen','abdelmalek.tradinos@expanded-tech.test','https://ae.linkedin.com/in/abdelmalekalmouzayen','CEO'],
            ['digit_innovation_hub','Abdel Malek Al-Mouzayen','abdelmalek.digit@expanded-tech.test','https://ae.linkedin.com/in/abdelmalekalmouzayen','Ecosystem Mentor'],
            ['ixcoders','Mhd Tarek Almalek','tarek.almalek@expanded-tech.test','https://sy.linkedin.com/in/mhd-tarek-almalek','Co-Founder & COO'],
            ['eb_tech','MHD Yasser Kaziz','yasser.kaziz@expanded-tech.test','https://ae.linkedin.com/in/yasserkaziz','Founder & CEO'],
            ['planlyze','Sara Kataf','sara.kataf@expanded-tech.test','https://sy.linkedin.com/in/sarakataf','CMO & Co-Founder'],
            ['arageek','Ahmad Sufian Bayram','ahmad.arageek@expanded-tech.test','https://www.linkedin.com/in/ahmadsufianbayram','Founder'],
            ['souq','Ronaldo Mouchawar','ronaldo.mouchawar@expanded-tech.test','https://www.linkedin.com/in/ronaldomouchawar','Co-Founder'],
            ['careem','Mudassir Sheikha','mudassir.sheikha@expanded-tech.test','https://www.linkedin.com/in/mudassirsheikha','Co-Founder & CEO'],
            ['tamara','Abdulmajeed Alsukhan','abdulmajeed.alsukhan@expanded-tech.test','https://www.linkedin.com/in/abdulmajeed-alsukhan-13000a58','Co-Founder & CEO'],
            ['anghami','Elie Habib','elie.habib@expanded-tech.test','https://www.linkedin.com/in/eliehabib','Co-Founder'],
            ['tabby','Hosam Arab','hosam.arab@expanded-tech.test','https://ae.linkedin.com/in/hosam','Co-Founder & CEO'],
        ];
        return array_map(static fn(array $p): array => ['key' => $p[0].'-'.substr(md5($p[1]),0,6), 'company' => $p[0], 'name' => $p[1], 'email' => $p[2], 'linkedin' => $p[3], 'role' => $p[4], 'asset' => $p[0].'/'.strtolower(preg_replace('/[^a-z0-9]+/i','-', $p[1]))], $leaders);
    }
}
