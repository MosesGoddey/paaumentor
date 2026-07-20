<?php

namespace Database\Seeders;

use App\Models\{User, Skill, SkillExchange, SkillExchangeRequest};
use Illuminate\Database\Seeder;

/**
 * Demo seeder: populates the Skill Exchange marketplace with a diverse set
 * of "offering ↔ seeking" listings from real students, a few pending
 * requests for realism, and one guaranteed mutual match for the primary
 * demo account (Emeka Williams, 23cs1010).
 */
class SkillExchangeSeeder extends Seeder
{
    public function run(): void
    {
        // ---- Make sure Emeka has a wanted skill so a mutual match can exist.
        $emeka = User::where('student_id', '23cs1010')->first();
        if ($emeka) {
            $python = Skill::firstOrCreate(['name' => 'Python']);
            $emeka->skills()->syncWithoutDetaching([$python->id => ['type' => 'wants']]);
        }

        // [student_id, offering, seeking, description]
        $listings = [
            // Guaranteed mutual match for Emeka: offers Python (he wants),
            // seeks JavaScript (he has).
            ['PAAUGRAD2', 'Python', 'JavaScript',
                'I can teach you Python from basics to data handling. In return, help me get comfortable with modern JavaScript and the DOM.'],

            ['PAAUGRAD1', 'React', 'UI/UX Design',
                'Frontend dev comfortable with React & hooks. Looking to swap for someone who can teach me Figma and design fundamentals.'],
            ['PAAUGRAD3', 'Data Structures', 'Web Development',
                'Strong on algorithms and DSA for interviews. Want to learn how to build and deploy a real web app in return.'],
            ['PAAUGRAD4', 'Graphic Design', 'Digital Marketing',
                'I design posters, logos and social graphics. Seeking someone to teach me digital marketing and running ad campaigns.'],
            ['PAAUGRAD5', 'Video Editing', 'Photography',
                'Premiere Pro & CapCut editor. Willing to trade editing lessons for photography and lighting basics.'],
            ['PAAUGRAD6', 'Technical Writing', 'SEO',
                'I write clear docs and articles. Looking to learn SEO so my writing actually ranks.'],
            ['22CS0721', 'HTML/CSS', 'JavaScript',
                'Can build clean, responsive layouts. Need help leveling up my JavaScript for interactivity.'],
            ['23CS0114', 'PHP', 'Laravel',
                'Solid with core PHP. Want a hand moving into Laravel — routing, Eloquent and Blade.'],
            ['22CS0455', 'MySQL', 'Data Analysis',
                'Comfortable designing schemas and writing SQL. Keen to learn data analysis with Pandas.'],
            ['21CS0332', 'Machine Learning', 'Cloud Computing',
                'I build ML models in Python. Looking to trade for AWS / cloud deployment know-how.'],
            ['23MT0208', 'Statistics', 'Python',
                'Maths student strong in statistics and probability. Want to learn Python to apply it practically.'],
            ['22PH0119', 'Circuit Design', 'Arduino Programming',
                'Physics student who loves electronics. Trade circuit theory for Arduino & embedded coding.'],
            ['23cs1043', 'Cybersecurity', 'Networking',
                'Into ethical hacking and security basics. Want to strengthen my computer networking fundamentals.'],
            ['23cs1005', 'Flutter', 'Backend APIs',
                'I build cross-platform mobile apps with Flutter. Seeking someone to teach me building REST APIs.'],
        ];

        $created = 0;
        $ownerIds = [];

        foreach ($listings as [$sid, $offering, $seeking, $description]) {
            $owner = User::where('student_id', $sid)->first();
            if (!$owner) continue;

            $ex = SkillExchange::firstOrCreate(
                ['user_id' => $owner->id, 'offering' => $offering, 'seeking' => $seeking],
                ['description' => $description, 'is_active' => true]
            );

            if ($ex->wasRecentlyCreated) $created++;
            $ownerIds[] = $owner->id;
        }

        // ---- Add a few pending requests on some listings for realism.
        $requesters = User::whereIn('student_id', ['23cs1012', '23CS1011', '22CS0055'])->get();
        $someListings = SkillExchange::whereIn('user_id', $ownerIds)->latest()->take(4)->get();

        $reqCount = 0;
        foreach ($someListings as $i => $listing) {
            $requester = $requesters[$i % max($requesters->count(), 1)] ?? null;
            if (!$requester || $requester->id === $listing->user_id) continue;

            $r = SkillExchangeRequest::firstOrCreate(
                ['exchange_id' => $listing->id, 'requester_id' => $requester->id],
                [
                    'message' => "Hi! I'd love to swap — I can help with what you're seeking. When are you free to start?",
                    'status'  => 'pending',
                ]
            );
            if ($r->wasRecentlyCreated) $reqCount++;
        }

        $total = SkillExchange::count();
        $this->command->info("✅ Skill Exchange: created {$created} new listings ({$total} total), {$reqCount} pending requests. Emeka now has a mutual match.");
    }
}
