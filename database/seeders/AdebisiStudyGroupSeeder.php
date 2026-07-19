<?php

namespace Database\Seeders;

use App\Models\{StudyGroup, StudyGroupMember, StudyGroupMessage, User};
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Demo seeder: fills study groups owned by Adebisi Lekan (the demo mentor
 * account) with plenty of members and a realistic chat history, so the
 * Study Groups pages look alive in screenshots and live demos.
 */
class AdebisiStudyGroupSeeder extends Seeder
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

        // 1. A few extra students so the member list is plentiful and varied
        $extraStudents = [
            ['Chinelo',  'Nwosu',    '22CS0721', '300L', 'Computer Science'],
            ['Musa',     'Ibrahim',  '23CS0114', '200L', 'Computer Science'],
            ['Precious', 'Adamu',    '22CS0455', '300L', 'Computer Science'],
            ['Victor',   'Okafor',   '21CS0332', '400L', 'Computer Science'],
            ['Aisha',    'Yakubu',   '23MT0208', '200L', 'Mathematics'],
            ['Daniel',   'Ede',      '22PH0119', '300L', 'Physics'],
        ];

        foreach ($extraStudents as [$first, $last, $sid, $level, $dept]) {
            User::firstOrCreate(['student_id' => $sid], [
                'first_name'  => $first,
                'last_name'   => $last,
                'email'       => strtolower($first . '.' . $last) . '@paau.edu.ng',
                'password'    => Hash::make('password'),
                'role'        => 'mentee',
                'department'  => $dept,
                'level'       => $level,
                'is_verified' => true,
                'is_active'   => true,
            ]);
        }

        // 2. The showcase group, owned & administered by Adebisi
        $group = StudyGroup::firstOrCreate(
            ['name' => 'Web Development Study Circle', 'created_by' => $adebisi->id],
            [
                'topic'       => 'Web Development',
                'description' => 'Weekly deep-dives into HTML, CSS, JavaScript, PHP and Laravel. We build real projects together and review each other\'s code.',
                'max_members' => 30,
                'is_open'     => true,
            ]
        );

        StudyGroupMember::firstOrCreate(
            ['study_group_id' => $group->id, 'user_id' => $adebisi->id],
            ['role' => 'admin']
        );

        // 3. Fill it with every eligible user (students & mentors, no staff)
        $eligible = User::where('is_active', true)
            ->whereNotIn('role', ['admin', 'verifier'])
            ->where('id', '!=', $adebisi->id)
            ->orderBy('id')
            ->get();

        foreach ($eligible as $candidate) {
            if ($group->members()->count() >= $group->max_members) break;
            StudyGroupMember::firstOrCreate(
                ['study_group_id' => $group->id, 'user_id' => $candidate->id],
                ['role' => 'member']
            );
        }

        // 4. A believable chat history (only if the group chat is empty-ish)
        if ($group->messages()->count() < 3) {
            $memberIds = $group->members()->pluck('user_id')->all();
            $chat = [
                [$adebisi->id, 'Welcome everyone to the Web Development Study Circle! We meet every Saturday at 4 PM.', 6],
                [null,         'Thanks for adding me! Looking forward to the sessions.', 6],
                [null,         'Can we cover Laravel routing this week? I keep mixing up route parameters.', 5],
                [$adebisi->id, 'Good suggestion — Saturday we\'ll do routing and controllers, then a mini exercise.', 5],
                [null,         'I built the to-do app from last week\'s task. Should I share the GitHub link here?', 4],
                [$adebisi->id, 'Yes please, drop the link. Everyone else — code reviews are how we improve.', 4],
                [null,         'The flexbox cheat-sheet someone shared was really helpful for my project 👍', 3],
                [null,         'Is the Saturday session on video call or chat only?', 2],
                [$adebisi->id, 'Video call — I\'ll start it from the group, you\'ll all get a notification to join.', 2],
                [null,         'Perfect. See everyone Saturday!', 1],
            ];

            $others = array_values(array_diff($memberIds, [$adebisi->id]));
            foreach ($chat as $i => [$senderId, $body, $daysAgo]) {
                StudyGroupMessage::create([
                    'study_group_id' => $group->id,
                    'sender_id'      => $senderId ?? $others[$i % count($others)],
                    'body'           => $body,
                    'type'           => 'text',
                    'created_at'     => now()->subDays($daysAgo)->addMinutes($i * 17),
                    'updated_at'     => now()->subDays($daysAgo)->addMinutes($i * 17),
                ]);
            }
        }

        // 5. Top up any other groups Adebisi owns (e.g. the "joseph" group)
        $otherGroups = StudyGroup::where('created_by', $adebisi->id)
                                 ->where('id', '!=', $group->id)->get();
        foreach ($otherGroups as $og) {
            foreach ($eligible->take(9) as $candidate) {
                if ($og->members()->count() >= min(10, $og->max_members)) break;
                StudyGroupMember::firstOrCreate(
                    ['study_group_id' => $og->id, 'user_id' => $candidate->id],
                    ['role' => 'member']
                );
            }
        }

        $this->command->info("✅ '{$group->name}' — " . $group->members()->count() . '/' . $group->max_members . ' members, ' . $group->messages()->count() . ' messages.');
        foreach ($otherGroups as $og) {
            $this->command->info("   Also topped up '{$og->name}' → " . $og->members()->count() . ' members.');
        }
    }
}
