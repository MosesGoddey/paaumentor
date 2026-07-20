<?php

namespace Database\Seeders;

use App\Models\{User, LearningPath};
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Demo seeder: gives Emeka Williams (23cs1010) additional learning paths
 * from his mentor Adebisi Lekan — each with modules, tasks and graded
 * submissions at different stages so the Learning Paths page shows a
 * realistic mix of progress.
 */
class EmekaLearningPathsSeeder extends Seeder
{
    public function run(): void
    {
        $adebisi = User::where('student_id', '23cs1008')->first();
        $emeka   = User::where('student_id', '23cs1010')->first();

        if (!$adebisi || !$emeka) {
            $this->command->warn('Adebisi or Emeka not found — skipping.');
            return;
        }

        // [title, description, modules => [module => [task, task, ...]], graded count, due offset weeks]
        $paths = [
            [
                'title'       => 'Web Development with Flask',
                'description' => 'Build and deploy real web applications with Python Flask — routing, templates, forms and databases.',
                'modules'     => [
                    'Flask Foundations' => [
                        'Set up a Flask project and create three routes',
                        'Build a Jinja2 template with a shared base layout',
                    ],
                    'Data & Forms' => [
                        'Create a registration form with validation',
                        'Connect SQLite and persist form submissions',
                    ],
                ],
                'graded'  => 2,
                'due'     => 4,
            ],
            [
                'title'       => 'SQL & Database Design',
                'description' => 'From tables and joins to indexes and normalisation — everything you need to design real schemas.',
                'modules'     => [
                    'Query Fundamentals' => [
                        'Write SELECT queries with WHERE, ORDER BY and LIMIT',
                        'Join two tables to answer a business question',
                    ],
                    'Schema Design' => [
                        'Design a normalised schema for a bookshop',
                    ],
                ],
                'graded'  => 1,
                'due'     => 6,
            ],
            [
                'title'       => 'Git & Team Collaboration',
                'description' => 'Version control workflows used in real teams — branches, pull requests and resolving conflicts.',
                'modules'     => [
                    'Git Essentials' => [
                        'Initialise a repo and make 5 meaningful commits',
                        'Create a feature branch and merge it cleanly',
                        'Resolve a merge conflict and explain your approach',
                    ],
                ],
                'graded'  => 3,
                'due'     => -1, // already completed
            ],
        ];

        $created = 0;

        foreach ($paths as $p) {
            $totalTasks = collect($p['modules'])->flatten()->count();
            $completed  = $p['graded'] >= $totalTasks;

            $path = LearningPath::firstOrCreate(
                ['mentor_id' => $adebisi->id, 'mentee_id' => $emeka->id, 'title' => $p['title']],
                [
                    'description' => $p['description'],
                    'status'      => $completed ? 'completed' : 'active',
                    'due_date'    => now()->addWeeks($p['due']),
                ]
            );

            if (DB::table('learning_modules')->where('learning_path_id', $path->id)->exists()) {
                continue; // already seeded
            }

            $gradedLeft = $p['graded'];
            $moduleNo   = 0;

            foreach ($p['modules'] as $moduleTitle => $tasks) {
                $moduleNo++;
                $moduleId = DB::table('learning_modules')->insertGetId([
                    'learning_path_id' => $path->id,
                    'title'            => $moduleTitle,
                    'order'            => $moduleNo,
                    'created_at'       => now(), 'updated_at' => now(),
                ]);

                foreach ($tasks as $t => $taskTitle) {
                    $taskId = DB::table('learning_tasks')->insertGetId([
                        'learning_module_id' => $moduleId,
                        'title'              => $taskTitle,
                        'description'        => 'Complete this task and submit your work with brief notes on your approach.',
                        'order'              => $t + 1,
                        'max_score'          => 100,
                        'is_locked'          => false,
                        'created_at'         => now(), 'updated_at' => now(),
                    ]);

                    if ($gradedLeft > 0) {
                        $gradedLeft--;
                        $weeksAgo = $gradedLeft + 1;
                        DB::table('task_submissions')->insert([
                            'learning_task_id' => $taskId,
                            'user_id'          => $emeka->id,
                            'notes'            => 'Submitted: ' . $taskTitle . '. Included a short write-up of my approach.',
                            'status'           => 'graded',
                            'score'            => rand(78, 95),
                            'feedback'         => 'Well done — clear approach and working solution. Keep the momentum going.',
                            'created_at'       => now()->subWeeks($weeksAgo),
                            'updated_at'       => now()->subWeeks($weeksAgo),
                        ]);
                    }
                }
            }

            $created++;
        }

        $this->command->info("✅ Added {$created} learning paths for Emeka Williams (mentor: Adebisi Lekan).");
    }
}
