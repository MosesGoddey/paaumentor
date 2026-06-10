<?php

namespace Database\Seeders;

use App\Models\{User, Mentorship, LearningPath, Certificate, Rating, Skill};
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Seeds high-profile senior mentors (including Adebisi Olalekan) that surface
 * on the public landing page's "Meet Our Mentors" section.
 *
 * "Senior Mentor" tier is computed by User::getMentorTierAttribute() from the
 * number of issued 'mentee' certificates on learning paths they mentored
 * (>= 5 = Senior, >= 15 = Lead). So for each mentor we create completed
 * learning paths + issued certificates + ratings to make the tier genuine.
 */
class LandingMentorsSeeder extends Seeder
{
    public function run(): void
    {
        // A shared pool of "graduate" mentees who received certificates.
        // Reused across mentors to avoid cluttering the user table.
        $grads = $this->graduatePool(6);

        $mentors = [
            [
                'first_name' => 'Adebisi', 'last_name' => 'Olalekan',
                'email'      => 'adebisi.olalekan@paau.edu.ng',
                'role'       => 'alumni', 'level' => 'Alumni',
                'bio'        => 'PAAU Computer Science alumnus and Senior Software Engineer. I mentor students on full-stack web development, data structures, and breaking into their first tech roles. 6+ mentees certified.',
                'linkedin_url' => 'https://www.linkedin.com/in/adebisi-olalekan',
                'github_url'   => 'https://github.com/adebisi-olalekan',
                'availability' => 'Weekday evenings & weekends',
                'skills'  => ['Laravel', 'JavaScript', 'React', 'Data Structures', 'System Analysis'],
                'reviews' => [5, 5, 5, 5, 5, 5], // 6 mentees, avg 5.0
            ],
            [
                'first_name' => 'Chidinma', 'last_name' => 'Okeke',
                'email'      => 'chidinma.okeke@paau.edu.ng',
                'role'       => 'alumni', 'level' => 'Alumni',
                'bio'        => 'Backend engineer and PAAU alumna. I help students master databases, clean API design, and exam-ready algorithms.',
                'linkedin_url' => 'https://www.linkedin.com/in/chidinma-okeke',
                'github_url'   => 'https://github.com/chidinma-okeke',
                'availability' => 'Weekends',
                'skills'  => ['PHP', 'MySQL', 'Database Design', 'Algorithms'],
                'reviews' => [5, 5, 5, 5, 4], // 5 mentees, avg 4.8
            ],
            [
                'first_name' => 'Ibrahim', 'last_name' => 'Suleiman',
                'email'      => 'ibrahim.suleiman@paau.edu.ng',
                'role'       => 'mentor', 'level' => '500L',
                'bio'        => '500L finalist and competitive programmer. I coach mentees through C++, data structures, and problem-solving for technical interviews.',
                'linkedin_url' => 'https://www.linkedin.com/in/ibrahim-suleiman',
                'github_url'   => 'https://github.com/ibrahim-suleiman',
                'availability' => 'Weekday afternoons',
                'skills'  => ['C++', 'Data Structures', 'Algorithms', 'Python'],
                'reviews' => [5, 5, 4, 5, 4], // 5 mentees, avg 4.6
            ],
            [
                'first_name' => 'Funmilayo', 'last_name' => 'Adeyemi',
                'email'      => 'funmilayo.adeyemi@paau.edu.ng',
                'role'       => 'alumni', 'level' => 'Alumni',
                'bio'        => 'Product-focused software engineer and PAAU alumna. I mentor on frontend engineering, React, and building portfolio projects that get you hired.',
                'linkedin_url' => 'https://www.linkedin.com/in/funmilayo-adeyemi',
                'github_url'   => 'https://github.com/funmilayo-adeyemi',
                'availability' => 'Evenings & weekends',
                'skills'  => ['React', 'JavaScript', 'HTML/CSS', 'Laravel'],
                'reviews' => [5, 4, 5, 4, 5], // 5 mentees, avg 4.6
            ],
        ];

        foreach ($mentors as $m) {
            $this->makeSeniorMentor($m, $grads);
        }

        $this->command->newLine();
        $this->command->info('✅ Landing-page senior mentors seeded. They now appear in "Meet Our Mentors".');
        $this->command->info('   All accounts use password: password');
    }

    /** Create (or fetch) a pool of graduate mentees who will hold certificates. */
    private function graduatePool(int $count): array
    {
        $names = [
            ['Tunde', 'Bakare'], ['Ngozi', 'Eze'], ['Yusuf', 'Bello'],
            ['Blessing', 'Anokwu'], ['Samuel', 'Ojo'], ['Halima', 'Garba'],
        ];

        $grads = [];
        for ($i = 0; $i < $count; $i++) {
            [$first, $last] = $names[$i];
            $grads[] = User::firstOrCreate(
                ['email' => "grad" . ($i + 1) . "@paau.edu.ng"],
                [
                    'first_name'  => $first,
                    'last_name'   => $last,
                    'student_id'  => 'PAAUGRAD' . ($i + 1),
                    'password'    => Hash::make('password'),
                    'role'        => 'mentee',
                    'department'  => 'Computer Science',
                    'level'       => '400L',
                    'is_verified' => true,
                    'is_active'   => true,
                ]
            );
        }
        return $grads;
    }

    private function makeSeniorMentor(array $m, array $grads): void
    {
        $mentor = User::firstOrCreate(['email' => $m['email']], [
            'first_name'    => $m['first_name'],
            'last_name'     => $m['last_name'],
            'password'      => Hash::make('password'),
            'role'          => $m['role'],
            'department'    => 'Computer Science',
            'level'         => $m['level'],
            'bio'           => $m['bio'],
            'linkedin_url'  => $m['linkedin_url'] ?? null,
            'github_url'    => $m['github_url'] ?? null,
            'availability'  => $m['availability'] ?? 'Flexible',
            'is_verified'   => true,
            'is_active'     => true,
            'mentor_status' => 'active',
        ]);

        // Ensure required visibility flags even if the account already existed.
        $mentor->update([
            'role'          => $m['role'],
            'bio'           => $m['bio'],
            'linkedin_url'  => $m['linkedin_url'] ?? $mentor->linkedin_url,
            'github_url'    => $m['github_url'] ?? $mentor->github_url,
            'availability'  => $m['availability'] ?? $mentor->availability,
            'is_verified'   => true,
            'is_active'     => true,
            'mentor_status' => 'active',
        ]);

        // Attach skills (type 'has').
        foreach ($m['skills'] as $skillName) {
            $skill = Skill::firstOrCreate(['name' => $skillName]);
            $mentor->skills()->syncWithoutDetaching([$skill->id => ['type' => 'has']]);
        }

        // For each review, create a completed mentee: mentorship + completed path
        // + issued certificate + rating. This drives the Senior Mentor tier.
        $primarySkill = $m['skills'][0] ?? 'Software Development';

        foreach ($m['reviews'] as $idx => $score) {
            $grad = $grads[$idx % count($grads)];

            $mentorship = Mentorship::firstOrCreate(
                ['mentor_id' => $mentor->id, 'mentee_id' => $grad->id],
                [
                    'status'     => 'active',
                    'goal'       => "Master {$primarySkill}",
                    'topic'      => $primarySkill,
                    'started_at' => now()->subMonths(6),
                ]
            );

            $path = LearningPath::firstOrCreate(
                ['mentor_id' => $mentor->id, 'mentee_id' => $grad->id, 'title' => "{$primarySkill} Mastery"],
                [
                    'description' => "A structured, completed learning path in {$primarySkill}.",
                    'status'      => 'completed',
                    'due_date'    => now()->subMonth(),
                ]
            );

            Certificate::firstOrCreate(
                ['certificate_id' => "PAAU-{$mentor->id}-{$grad->id}"],
                [
                    'user_id'          => $grad->id,
                    'learning_path_id' => $path->id,
                    'type'             => 'mentee',
                    'issued_at'        => now()->subMonths(rand(1, 5)),
                ]
            );

            Rating::firstOrCreate(
                ['mentorship_id' => $mentorship->id, 'rater_id' => $grad->id, 'ratee_id' => $mentor->id],
                [
                    'score'  => $score,
                    'review' => $this->reviewText($idx, $mentor->first_name),
                ]
            );
        }

        $mentor->refresh();
        $this->command->info(sprintf(
            '  • %s — %d certified mentees → %s (★ %.1f)',
            $mentor->full_name,
            $mentor->completed_mentees_count,
            $mentor->mentor_tier_label,
            $mentor->average_rating
        ));
    }

    private function reviewText(int $i, string $name): string
    {
        $reviews = [
            "{$name} is an outstanding mentor — clear explanations and genuinely invested in my growth.",
            "Patient, knowledgeable, and always available. Helped me finally understand the hard topics.",
            "Best mentorship experience I've had at PAAU. Highly recommend.",
            "Structured, practical guidance that took me from beginner to confident. Thank you!",
            "Incredible support throughout my learning path. {$name} truly cares about students.",
            "Gave me real-world advice and a clear roadmap. I landed an internship because of this.",
        ];
        return $reviews[$i % count($reviews)];
    }
}
