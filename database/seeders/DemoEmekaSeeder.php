<?php

namespace Database\Seeders;

use App\Models\{User, Mentorship, MentorSession, LearningPath, CertificateRequest, Assessment, AssessmentQuestion};
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\{DB, Hash};

class DemoEmekaSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Emeka Williams as a mentee
        $emeka = User::firstOrCreate(['email' => 'emeka@paau.edu.ng'], [
            'first_name'  => 'Emeka',
            'last_name'   => 'Williams',
            'student_id'  => '22CS0055',
            'password'    => Hash::make('password'),
            'role'        => 'mentee',
            'department'  => 'Computer Science',
            'level'       => '300L',
            'is_verified' => true,
            'is_active'   => true,
        ]);
        $this->command->info("User: {$emeka->full_name} (ID {$emeka->id})");

        // 2. Find Amaka — existing seeded mentor
        $amaka = User::where('email', 'amaka@paau.edu.ng')->firstOrFail();
        $this->command->info("Mentor: {$amaka->full_name} (ID {$amaka->id})");

        // 3. Mentorship
        $mentorship = Mentorship::firstOrCreate(
            ['mentor_id' => $amaka->id, 'mentee_id' => $emeka->id],
            [
                'status'     => 'active',
                'goal'       => 'Master Laravel web development',
                'topic'      => 'Laravel',
                'started_at' => now()->subMonths(2),
            ]
        );
        $this->command->info("Mentorship ID: {$mentorship->id}");

        // 4. Learning path
        $path = LearningPath::firstOrCreate(
            ['mentor_id' => $amaka->id, 'mentee_id' => $emeka->id, 'title' => 'Laravel Web Development'],
            [
                'description' => 'A comprehensive introduction to Laravel covering routing, controllers, Eloquent ORM, and Blade templating.',
                'status'      => 'active',
                'due_date'    => now()->addMonths(1),
            ]
        );
        $this->command->info("LearningPath ID: {$path->id}");

        // 4b. Module + tasks + graded submissions (makes path isComplete() → true)
        $module = DB::table('learning_modules')->where('learning_path_id', $path->id)->first();
        if (!$module) {
            $moduleId = DB::table('learning_modules')->insertGetId([
                'learning_path_id' => $path->id,
                'title'            => 'Laravel Fundamentals',
                'order'            => 1,
                'created_at'       => now(),
                'updated_at'       => now(),
            ]);
        } else {
            $moduleId = $module->id;
        }

        $taskDefs = [
            ['title' => 'Build a Basic CRUD App',              'desc' => 'Create a simple task manager with routes, controller, model, and Blade views.'],
            ['title' => 'Implement User Authentication',        'desc' => 'Set up Laravel Breeze or manual auth with login, register, and middleware protection.'],
            ['title' => 'Design a Database with Eloquent ORM',  'desc' => 'Create migrations for at least three related tables and define Eloquent relationships.'],
        ];

        $existingTasks = DB::table('learning_tasks')->where('learning_module_id', $moduleId)->count();
        if ($existingTasks === 0) {
            foreach ($taskDefs as $i => $t) {
                $taskId = DB::table('learning_tasks')->insertGetId([
                    'learning_module_id' => $moduleId,
                    'title'              => $t['title'],
                    'description'        => $t['desc'],
                    'order'              => $i + 1,
                    'max_score'          => 100,
                    'is_locked'          => false,
                    'created_at'         => now(),
                    'updated_at'         => now(),
                ]);

                // Emeka's graded submission for each task
                $alreadySubmitted = DB::table('task_submissions')
                    ->where('learning_task_id', $taskId)
                    ->where('user_id', $emeka->id)
                    ->exists();

                if (!$alreadySubmitted) {
                    DB::table('task_submissions')->insert([
                        'learning_task_id' => $taskId,
                        'user_id'          => $emeka->id,
                        'notes'            => "Completed {$t['title']}. I followed the steps from the session notes and tested all features.",
                        'status'           => 'graded',
                        'score'            => rand(78, 95),
                        'feedback'         => 'Well done! Clean code and good structure. A few minor improvements possible but overall excellent work.',
                        'created_at'       => now()->subDays(rand(5, 20)),
                        'updated_at'       => now()->subDays(rand(1, 4)),
                    ]);
                }
            }
        }

        $taskCount = DB::table('learning_tasks')->where('learning_module_id', $moduleId)->count();
        $gradedCount = DB::table('task_submissions')
            ->join('learning_tasks', 'learning_tasks.id', '=', 'task_submissions.learning_task_id')
            ->where('learning_tasks.learning_module_id', $moduleId)
            ->where('task_submissions.user_id', $emeka->id)
            ->where('task_submissions.status', 'graded')
            ->count();
        $this->command->info("Tasks: {$taskCount} total, {$gradedCount} graded by Emeka → path complete: " . ($gradedCount === $taskCount && $taskCount > 0 ? 'YES' : 'NO'));

        // 5. Three completed sessions (only create what's missing)
        $existing = MentorSession::where('mentorship_id', $mentorship->id)
            ->where('status', 'completed')->count();
        $titles = ['Laravel Routing & Controllers', 'Eloquent ORM & Migrations', 'Blade Templates & Auth'];
        for ($i = $existing; $i < 3; $i++) {
            MentorSession::create([
                'mentorship_id'    => $mentorship->id,
                'title'            => $titles[$i],
                'type'             => 'video',
                'status'           => 'completed',
                'scheduled_at'     => now()->subWeeks(3 - $i),
                'duration_minutes' => 60,
                'call_outcome'     => 'answered',
            ]);
        }
        $total = MentorSession::where('mentorship_id', $mentorship->id)->where('status', 'completed')->count();
        $this->command->info("Completed sessions: {$total}/3");

        // 6. Certificate request — always reset to pending_assessment for the demo
        $certRequest = CertificateRequest::firstOrCreate(
            ['learning_path_id' => $path->id, 'mentee_id' => $emeka->id],
            ['mentor_id' => $amaka->id, 'status' => 'pending_assessment']
        );

        // Reset to pending_assessment and clear prior attempts so the demo is fresh
        $certRequest->update([
            'status'               => 'pending_assessment',
            'assessment_score'     => null,
            'assessment_passed_at' => null,
            'mentor_reflection'    => null,
            'verifier_id'          => null,
            'verifier_note'        => null,
            'verified_at'          => null,
        ]);
        DB::table('assessment_attempts')->where('certificate_request_id', $certRequest->id)->delete();

        $this->command->info("CertificateRequest ID: {$certRequest->id} | Status: {$certRequest->fresh()->status}");

        // 7. Assessment record
        $assessment = Assessment::firstOrCreate(
            ['learning_path_id' => $path->id],
            [
                'passing_score'         => 70,
                'time_per_question'     => 90,
                'questions_per_attempt' => 10,
                'questions_ready'       => false,
            ]
        );

        // 8. Create 15 Laravel questions
        if ($assessment->questions()->count() < 15) {
            $assessment->questions()->delete();

            $questions = [
                ['q' => 'What command creates a new Laravel controller?',                      'opts' => ['php artisan make:controller', 'php artisan create:controller', 'php artisan generate:controller', 'php artisan new:controller'], 'ans' => 0],
                ['q' => 'Which method defines a GET route in Laravel?',                        'opts' => ['Route::get()', 'Route::post()', 'Route::put()', 'Route::fetch()'],                                                              'ans' => 0],
                ['q' => 'What is Eloquent in Laravel?',                                        'opts' => ['A templating engine', 'An ORM for database interaction', 'A caching library', 'A testing framework'],                           'ans' => 1],
                ['q' => 'Which file stores environment variables in Laravel?',                 'opts' => ['.env', 'config.php', 'settings.json', '.htaccess'],                                                                              'ans' => 0],
                ['q' => 'What does `php artisan migrate` do?',                                'opts' => ['Creates a migration file', 'Runs all pending migrations', 'Rolls back the last migration', 'Seeds the database'],                'ans' => 1],
                ['q' => 'What is the default templating engine in Laravel?',                  'opts' => ['Twig', 'Smarty', 'Blade', 'Mustache'],                                                                                           'ans' => 2],
                ['q' => 'How do you retrieve all records from a User model?',                 'opts' => ['User::all()', 'User::find()', 'User::get()', 'User::fetch()'],                                                                   'ans' => 0],
                ['q' => 'Which middleware protects routes from unauthenticated users?',       'opts' => ['guest', 'auth', 'verified', 'admin'],                                                                                            'ans' => 1],
                ['q' => 'What does `php artisan make:model Post -m` do?',                     'opts' => ['Creates a model only', 'Creates a model and a migration', 'Creates a migration only', 'Creates a model and controller'],         'ans' => 1],
                ['q' => 'What is the purpose of `@csrf` in a Blade form?',                   'opts' => ['Styles the form', 'Validates form fields', 'Includes a CSRF token for security', 'Submits the form via AJAX'],                   'ans' => 2],
                ['q' => 'Which Eloquent method inserts a new record?',                        'opts' => ['User::insert()', 'User::add()', 'User::create()', 'User::new()'],                                                                'ans' => 2],
                ['q' => 'Where are web route definitions stored in Laravel?',                 'opts' => ['app/routes.php', 'routes/web.php', 'config/routes.php', 'resources/routes.php'],                                                'ans' => 1],
                ['q' => 'What does the `with()` method do in Eloquent?',                     'opts' => ['Adds a WHERE clause', 'Eager loads relationships', 'Groups results', 'Joins two tables'],                                        'ans' => 1],
                ['q' => 'Which command clears the application cache in Laravel?',             'opts' => ['php artisan clear:cache', 'php artisan cache:flush', 'php artisan cache:clear', 'php artisan flush:cache'],                      'ans' => 2],
                ['q' => 'What is the purpose of Laravel service providers?',                  'opts' => ['Define database schemas', 'Bootstrap and bind services into the container', 'Manage user sessions', 'Handle HTTP requests'],     'ans' => 1],
            ];

            foreach ($questions as $i => $q) {
                AssessmentQuestion::create([
                    'assessment_id'  => $assessment->id,
                    'question'       => $q['q'],
                    'options'        => $q['opts'],
                    'correct_answer' => $q['ans'],
                    'points'         => 1,
                    'order'          => $i + 1,
                ]);
            }

            $assessment->update(['questions_ready' => true, 'questions_generated_at' => now()]);
        }

        $qCount = $assessment->questions()->count();
        $this->command->info("Assessment ID: {$assessment->id} | Questions: {$qCount} | Ready: " . ($assessment->fresh()->questions_ready ? 'YES' : 'NO'));

        $this->command->newLine();
        $this->command->info('✅ Demo ready!');
        $this->command->info("   Login: emeka@paau.edu.ng / password");
        $this->command->info("   Assessment URL: /assessment/{$certRequest->id}");
        $this->command->newLine();
    }
}
