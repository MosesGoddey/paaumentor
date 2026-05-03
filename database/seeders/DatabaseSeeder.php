<?php

namespace Database\Seeders;

use App\Models\{User, Skill};
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $skills = [
            ['name' => 'Laravel',            'category' => 'Web'],
            ['name' => 'PHP',                'category' => 'Web'],
            ['name' => 'JavaScript',         'category' => 'Web'],
            ['name' => 'React',              'category' => 'Web'],
            ['name' => 'HTML/CSS',           'category' => 'Web'],
            ['name' => 'Python',             'category' => 'Programming'],
            ['name' => 'C++',               'category' => 'Programming'],
            ['name' => 'Java',               'category' => 'Programming'],
            ['name' => 'Data Structures',    'category' => 'Algorithms'],
            ['name' => 'Algorithms',         'category' => 'Algorithms'],
            ['name' => 'MySQL',              'category' => 'Database'],
            ['name' => 'Database Design',    'category' => 'Database'],
            ['name' => 'Machine Learning',   'category' => 'AI/ML'],
            ['name' => 'UML',                'category' => 'Software Engineering'],
            ['name' => 'System Analysis',    'category' => 'Software Engineering'],
        ];

        foreach ($skills as $skill) {
            Skill::firstOrCreate(['name' => $skill['name']], $skill);
        }

        $admin = User::firstOrCreate(['email' => 'admin@paau.edu.ng'], [
            'first_name'  => 'Admin',
            'last_name'   => 'PAAUMENTOR',
            'student_id'  => 'ADMIN001',
            'password'    => Hash::make('password'),
            'role'        => 'admin',
            'department'  => 'Computer Science',
            'level'       => 'Staff',
            'is_verified' => true,
            'is_active'   => true,
        ]);

        $mentorsData = [
            ['first_name'=>'Amaka',    'last_name'=>'Okonkwo',   'email'=>'amaka@paau.edu.ng',   'student_id'=>'20CS1021','level'=>'400L', 'bio'=>'Passionate 400L CS student specializing in web development and algorithms. Tutored 12+ students through Laravel and DSA.', 'skills'=>['Laravel','React','Data Structures','Algorithms']],
            ['first_name'=>'Fatima',   'last_name'=>'Abdullahi', 'email'=>'fatima@paau.edu.ng',  'student_id'=>'19CS0088','level'=>'500L', 'bio'=>'500L finalists focusing on algorithms and competitive programming.', 'skills'=>['Algorithms','C++','Data Structures']],
            ['first_name'=>'Kingsley', 'last_name'=>'Musa',      'email'=>'kingsley@paau.edu.ng','student_id'=>'20CS1045','level'=>'400L', 'bio'=>'Database enthusiast and 400L student. Expert in MySQL normalization and ER diagrams.', 'skills'=>['MySQL','Database Design','PHP']],
            ['first_name'=>'Chukwudi', 'last_name'=>'Ibe',       'email'=>'c.ibe@alumni.paau.ng','student_id'=>'ALU2022CI','level'=>'Alumni','role'=>'alumni', 'bio'=>'PAAU CS alumni working as a senior software engineer. Specializes in Python, ML, and backend systems.', 'skills'=>['Python','Machine Learning','JavaScript']],
        ];

        foreach ($mentorsData as $md) {
            $mentor = User::firstOrCreate(['email' => $md['email']], [
                'first_name'   => $md['first_name'],
                'last_name'    => $md['last_name'],
                'student_id'   => $md['student_id'],
                'password'     => Hash::make('password'),
                'role'         => $md['role'] ?? 'mentor',
                'department'   => 'Computer Science',
                'level'        => $md['level'],
                'bio'          => $md['bio'],
                'is_verified'  => true,
                'is_active'    => true,
                'availability' => 'Weekdays 4–7PM, Weekends',
            ]);

            $skillIds = Skill::whereIn('name', $md['skills'])->pluck('id')
                             ->mapWithKeys(fn($id) => [$id => ['type' => 'has']]);
            $mentor->skills()->sync($skillIds);
        }

        $mentee = User::firstOrCreate(['email' => '23cs1004@paau.edu.ng'], [
            'first_name'  => 'Moses',
            'last_name'   => 'Joseph',
            'student_id'  => '23CS1004',
            'password'    => Hash::make('password'),
            'role'        => 'mentee',
            'department'  => 'Computer Science',
            'level'       => '300L',
            'is_verified' => true,
            'is_active'   => true,
        ]);

        $wantedIds = Skill::whereIn('name', ['Laravel','Data Structures','React'])->pluck('id')
                          ->mapWithKeys(fn($id) => [$id => ['type' => 'wants']]);
        $mentee->skills()->sync($wantedIds);

        echo "\n✅ Seeding complete!\n";
        echo "   Admin:  admin@paau.edu.ng / password\n";
        echo "   Mentee: 23CS1004 / password\n";
        echo "   Mentors: amaka@paau.edu.ng / password (and others)\n\n";
    }
}
