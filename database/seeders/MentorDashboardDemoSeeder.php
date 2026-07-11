<?php

namespace Database\Seeders;

use App\Models\{User, Mentorship, MentorSession, LearningPath};
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\{DB, Hash};

/**
 * Fills a demo mentor's dashboard so every widget has content:
 * upcoming sessions, completed sessions spread across the last six
 * months (engagement chart), and in-progress learning paths they
 * mentor. Safe to re-run (idempotent).
 */
class MentorDashboardDemoSeeder extends Seeder
{
    public function run(): void
    {
        // Enrich whichever demo mentor accounts exist.
        $mentors = User::where('student_id', '23cs1008')
            ->orWhere('email', 'adebisi.olalekan@paau.edu.ng')
            ->get();

        if ($mentors->isEmpty()) {
            $this->command->warn('No demo mentor found (23cs1008 / adebisi.olalekan@paau.edu.ng) — nothing to seed.');
            return;
        }

        foreach ($mentors as $mentor) {
            $this->enrich($mentor);
        }

        $this->command->newLine();
        $this->command->info('✅ Mentor dashboard demo data ready.');
    }

    private function enrich(User $mentor): void
    {
        $this->command->info("Enriching: {$mentor->full_name} ({$mentor->email})");

        // ---- Ensure at least two active mentorships ----
        $mentorships = Mentorship::where('mentor_id', $mentor->id)->where('status', 'active')->get();

        $needed = 2 - $mentorships->count();
        $demoMentees = [
            ['first_name' => 'Grace',  'last_name' => 'Monday',   'email' => 'grace.monday@paau.edu.ng',   'student_id' => '25CS2001', 'topic' => 'Python'],
            ['first_name' => 'Emeka',  'last_name' => 'Williams', 'email' => 'emeka@paau.edu.ng',          'student_id' => '22CS0055', 'topic' => 'Python'],
        ];
        for ($i = 0; $i < $needed; $i++) {
            $md = $demoMentees[$i];
            $mentee = User::firstOrCreate(['email' => $md['email']], [
                'first_name'  => $md['first_name'],
                'last_name'   => $md['last_name'],
                'student_id'  => $md['student_id'],
                'password'    => Hash::make('password'),
                'role'        => 'mentee',
                'department'  => 'Computer Science',
                'level'       => '100L',
                'is_verified' => true,
                'is_active'   => true,
            ]);
            $mentorships->push(Mentorship::firstOrCreate(
                ['mentor_id' => $mentor->id, 'mentee_id' => $mentee->id],
                ['status' => 'active', 'goal' => "Master {$md['topic']}", 'topic' => $md['topic'], 'started_at' => now()->subMonths(4)]
            ));
        }

        // ---- Upcoming (scheduled) sessions ----
        $upcoming = [
            ['title' => 'Python OOP Deep Dive',        'in_days' => 2, 'type' => 'video'],
            ['title' => 'Code Review: Mini Project',   'in_days' => 5, 'type' => 'chat'],
        ];
        foreach ($upcoming as $i => $s) {
            $ms = $mentorships[$i % $mentorships->count()];
            MentorSession::firstOrCreate(
                ['mentorship_id' => $ms->id, 'title' => $s['title']],
                [
                    'type'             => $s['type'],
                    'status'           => 'scheduled',
                    'scheduled_at'     => now()->addDays($s['in_days'])->setTime(16, 0),
                    'duration_minutes' => 60,
                ]
            );
        }

        // ---- Completed sessions across the last 6 months (engagement chart) ----
        // monthsAgo => how many sessions that month; includes the current month
        // so the "+N this month" KPI trend has data.
        $spread = [5 => 1, 4 => 2, 3 => 2, 2 => 3, 1 => 2, 0 => 2];
        $titles = ['Intro & Goal Setting', 'Python Basics', 'Control Flow Practice', 'Functions Workshop',
                   'Debugging Session', 'Lists & Dictionaries', 'Project Planning', 'OOP Basics',
                   'File Handling', 'Practice Problems', 'Progress Review', 'Exam Prep'];
        $t = 0;
        foreach ($spread as $monthsAgo => $count) {
            for ($i = 0; $i < $count; $i++) {
                $ms   = $mentorships[$t % $mentorships->count()];
                $when = now()->subMonths($monthsAgo)->startOfMonth()->addDays(3 + $i * 7)->setTime(16, 0);
                if ($when->isFuture()) $when = now()->subDays(2 + $i);
                MentorSession::firstOrCreate(
                    ['mentorship_id' => $ms->id, 'title' => $titles[$t % count($titles)] . ' #' . ($t + 1)],
                    [
                        'type'             => ['video', 'voice', 'chat'][$t % 3],
                        'status'           => 'completed',
                        'scheduled_at'     => $when,
                        'duration_minutes' => 60,
                        'call_outcome'     => 'answered',
                    ]
                );
                $t++;
            }
        }

        // ---- In-progress learning paths mentored by this mentor ----
        $pathDefs = [
            ['title' => 'Python Fundamentals',        'graded' => 2, 'total' => 3],  // 67%
            ['title' => 'Data Structures in Python',  'graded' => 1, 'total' => 4],  // 25%
        ];
        foreach ($pathDefs as $i => $pd) {
            $ms   = $mentorships[$i % $mentorships->count()];
            $path = LearningPath::firstOrCreate(
                ['mentor_id' => $mentor->id, 'mentee_id' => $ms->mentee_id, 'title' => $pd['title']],
                ['description' => "Structured path in {$pd['title']}.", 'status' => 'active', 'due_date' => now()->addMonths(2)]
            );

            $moduleId = DB::table('learning_modules')->where('learning_path_id', $path->id)->value('id')
                ?? DB::table('learning_modules')->insertGetId([
                    'learning_path_id' => $path->id, 'title' => 'Core Module', 'order' => 1,
                    'created_at' => now(), 'updated_at' => now(),
                ]);

            $existing = DB::table('learning_tasks')->where('learning_module_id', $moduleId)->count();
            for ($n = $existing; $n < $pd['total']; $n++) {
                $taskId = DB::table('learning_tasks')->insertGetId([
                    'learning_module_id' => $moduleId,
                    'title'              => $pd['title'] . ' — Task ' . ($n + 1),
                    'description'        => 'Practical exercise assessed by the mentor.',
                    'order'              => $n + 1,
                    'max_score'          => 100,
                    'is_locked'          => false,
                    'created_at'         => now(), 'updated_at' => now(),
                ]);
                if ($n < $pd['graded']) {
                    DB::table('task_submissions')->insert([
                        'learning_task_id' => $taskId,
                        'user_id'          => $ms->mentee_id,
                        'notes'            => 'Submitted my solution with test output.',
                        'status'           => 'graded',
                        'score'            => rand(75, 95),
                        'feedback'         => 'Good work — keep it up.',
                        'created_at'       => now()->subDays(rand(5, 25)),
                        'updated_at'       => now()->subDays(rand(1, 4)),
                    ]);
                }
            }
        }

        $done      = MentorSession::whereIn('mentorship_id', $mentorships->pluck('id'))->where('status', 'completed')->count();
        $scheduled = MentorSession::whereIn('mentorship_id', $mentorships->pluck('id'))->where('status', 'scheduled')->where('scheduled_at', '>=', now())->count();
        $paths     = LearningPath::where('mentor_id', $mentor->id)->count();
        $this->command->info("  mentorships: {$mentorships->count()} | completed sessions: {$done} | upcoming: {$scheduled} | paths mentored: {$paths}");
    }
}
