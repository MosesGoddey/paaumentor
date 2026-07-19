<?php

namespace Database\Seeders;

use App\Models\{User, Mentorship, MentorSession, LearningPath, Rating};
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Demo seeder: gives Adebisi Lekan (23cs1008) a full mentee roster —
 * active mentorships with session history, learning paths with graded
 * progress, mentee ratings, and a couple of pending requests so every
 * mentor-side dashboard panel has real content.
 */
class AdebisiMenteesSeeder extends Seeder
{
    public function run(): void
    {
        $adebisi = User::where('student_id', '23cs1008')
                       ->orWhere('email', 'ksu.edu.org@gmail.com')
                       ->first();

        if (!$adebisi) {
            $this->command->warn('Adebisi Lekan (23cs1008) not found — skipping.');
            return;
        }

        // [student_id, topic, sessions, path?, progress tasks graded (of 3)]
        $roster = [
            ['23CS1011', 'Laravel',          4, 'Laravel Backend Mastery',        3], // Audu Peter — completed
            ['23cs1012', 'JavaScript',       3, 'Modern JavaScript Foundations',  2], // Buhari Muhamad
            ['PAAUGRAD1','React',            3, 'React Component Patterns',       1], // Tunde Bakare
            ['PAAUGRAD2','Python',           2, 'Python for Problem Solving',     2], // Ngozi Eze
            ['PAAUGRAD3','Data Structures',  2, null,                             0], // Yusuf Bello
            ['22CS0721', 'HTML/CSS',         1, 'Responsive Web Design',          1], // Chinelo Nwosu
            ['23CS0114', 'PHP',              1, null,                             0], // Musa Ibrahim
            ['22CS0455', 'MySQL',            2, null,                             0], // Precious Adamu
        ];

        $sessionTitles = [
            'Introduction & Goal Setting', 'Core Concepts Walkthrough',
            'Hands-on Project Session',    'Code Review & Feedback',
            'Debugging Techniques',        'Career & Portfolio Advice',
        ];

        $reviews = [
            'Adebisi explains difficult concepts so clearly. Best mentor I could ask for.',
            'Patient, structured and genuinely invested in my progress.',
            'The weekly sessions transformed how I approach coding problems.',
            'Great feedback on every task I submitted. Highly recommend him.',
            'He pushed me to build real projects, not just watch tutorials.',
            'Always available when I get stuck. Learned so much this semester.',
        ];

        $added = 0;
        foreach ($roster as $i => [$sid, $topic, $sessionCount, $pathTitle, $gradedTasks]) {
            $mentee = User::where('student_id', $sid)->first();
            if (!$mentee) continue;

            $mentorship = Mentorship::firstOrCreate(
                ['mentor_id' => $adebisi->id, 'mentee_id' => $mentee->id],
                [
                    'status'     => 'active',
                    'topic'      => $topic,
                    'goal'       => "Become confident in {$topic} through weekly guided practice",
                    'started_at' => now()->subMonths(rand(2, 5)),
                ]
            );
            if ($mentorship->status !== 'active') $mentorship->update(['status' => 'active']);
            $added++;

            // Completed sessions spread over recent months (feeds engagement chart)
            $existing = MentorSession::where('mentorship_id', $mentorship->id)->count();
            for ($s = $existing; $s < $sessionCount; $s++) {
                MentorSession::create([
                    'mentorship_id'    => $mentorship->id,
                    'title'            => $sessionTitles[($i + $s) % count($sessionTitles)],
                    'type'             => ['video', 'voice', 'chat'][($i + $s) % 3],
                    'status'           => 'completed',
                    'scheduled_at'     => now()->subWeeks($sessionCount - $s)->subDays($i % 4),
                    'duration_minutes' => [45, 60, 90][($i + $s) % 3],
                    'call_outcome'     => 'answered',
                ]);
            }

            // Learning path with genuine graded progress
            if ($pathTitle) {
                $path = LearningPath::firstOrCreate(
                    ['mentor_id' => $adebisi->id, 'mentee_id' => $mentee->id, 'title' => $pathTitle],
                    [
                        'description' => "A structured {$topic} programme with practical, assessable tasks.",
                        'status'      => $gradedTasks >= 3 ? 'completed' : 'active',
                        'due_date'    => now()->addWeeks(6),
                    ]
                );

                if (DB::table('learning_modules')->where('learning_path_id', $path->id)->doesntExist()) {
                    $moduleId = DB::table('learning_modules')->insertGetId([
                        'learning_path_id' => $path->id,
                        'title'            => "{$topic} Fundamentals",
                        'order'            => 1,
                        'created_at'       => now(), 'updated_at' => now(),
                    ]);

                    for ($t = 1; $t <= 3; $t++) {
                        $taskId = DB::table('learning_tasks')->insertGetId([
                            'learning_module_id' => $moduleId,
                            'title'              => "{$topic} Task {$t}",
                            'description'        => "Practical exercise {$t} for {$topic}.",
                            'order'              => $t,
                            'max_score'          => 100,
                            'is_locked'          => false,
                            'created_at'         => now(), 'updated_at' => now(),
                        ]);

                        if ($t <= $gradedTasks) {
                            DB::table('task_submissions')->insert([
                                'learning_task_id' => $taskId,
                                'user_id'          => $mentee->id,
                                'notes'            => "Completed {$topic} Task {$t} with all requirements.",
                                'status'           => 'graded',
                                'score'            => rand(75, 96),
                                'feedback'         => 'Solid work — clean, correct and well structured.',
                                'created_at'       => now()->subWeeks(4 - $t),
                                'updated_at'       => now()->subWeeks(4 - $t),
                            ]);
                        }
                    }
                }
            }

            // Rating from most mentees (drives leaderboard + profile stars)
            if ($i < 6) {
                Rating::firstOrCreate(
                    ['mentorship_id' => $mentorship->id, 'rater_id' => $mentee->id, 'ratee_id' => $adebisi->id],
                    ['score' => [5, 5, 4, 5, 4, 5][$i], 'review' => $reviews[$i]]
                );
            }
        }

        // Two upcoming scheduled sessions (fills "Upcoming Sessions" panel)
        $firstMs = Mentorship::where('mentor_id', $adebisi->id)->where('status', 'active')->first();
        if ($firstMs && MentorSession::where('mentorship_id', $firstMs->id)->where('status', 'scheduled')->doesntExist()) {
            MentorSession::create([
                'mentorship_id'    => $firstMs->id,
                'title'            => 'Weekly Progress Check-in',
                'type'             => 'video',
                'status'           => 'scheduled',
                'scheduled_at'     => now()->addDays(2)->setTime(16, 0),
                'duration_minutes' => 60,
            ]);
            MentorSession::create([
                'mentorship_id'    => $firstMs->id,
                'title'            => 'Project Code Review',
                'type'             => 'chat',
                'status'           => 'scheduled',
                'scheduled_at'     => now()->addDays(5)->setTime(18, 30),
                'duration_minutes' => 45,
            ]);
        }

        // Two pending requests (activates the "Mentorship Requests" panel)
        foreach ([['21CS0332', 'Frontend Engineering'], ['23MT0208', 'Python']] as [$sid, $topic]) {
            $requester = User::where('student_id', $sid)->first();
            if ($requester) {
                Mentorship::firstOrCreate(
                    ['mentor_id' => $adebisi->id, 'mentee_id' => $requester->id],
                    [
                        'status' => 'pending',
                        'topic'  => $topic,
                        'goal'   => "I would love your guidance on {$topic} — I'm preparing for internship applications.",
                    ]
                );
            }
        }

        $active   = Mentorship::where('mentor_id', $adebisi->id)->where('status', 'active')->count();
        $pending  = Mentorship::where('mentor_id', $adebisi->id)->where('status', 'pending')->count();
        $sessions = MentorSession::whereIn('mentorship_id', Mentorship::where('mentor_id', $adebisi->id)->pluck('id'))->count();
        $paths    = LearningPath::where('mentor_id', $adebisi->id)->count();
        $rating   = round($adebisi->ratings()->avg('score'), 1);

        $this->command->info("✅ Adebisi Lekan now has: {$active} active mentees, {$pending} pending requests, {$sessions} sessions, {$paths} learning paths, ★ {$rating} rating.");
    }
}
