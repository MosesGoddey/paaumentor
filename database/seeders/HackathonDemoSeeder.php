<?php

namespace Database\Seeders;

use App\Models\{Hackathon, HackathonTeam, HackathonSubmission, User, Certificate};
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Demo seeder: populates the Hackathons module with events covering every
 * lifecycle stage — open, ongoing, judging, and completed (with scored
 * submissions and placement certificates) — so listings, judge panels and
 * leaderboards all have real content.
 */
class HackathonDemoSeeder extends Seeder
{
    private array $studentPool = [];

    public function run(): void
    {
        $organiser = User::where('student_id', '23cs1008')->first()
                  ?? User::where('role', 'admin')->first();
        $judgeA = $organiser;
        $judgeB = User::where('email', 'amaka@paau.edu.ng')->first() ?? $organiser;

        $this->studentPool = User::where('role', 'mentee')->where('is_active', true)
                                 ->orderBy('id')->pluck('id')->all();

        // ---------- 1. OPEN — registration running ----------
        $open = Hackathon::firstOrCreate(
            ['title' => 'PAAU Innovation Challenge 2026'],
            [
                'description' => 'The flagship innovation contest for PAAU students. Build a working prototype that solves a real problem on campus or in the Anyigba community.',
                'theme'       => 'Technology for Community Impact',
                'rules'       => "1. Teams of 2-4 students.\n2. All code must be written during the hackathon.\n3. Any tech stack is allowed.\n4. Submissions must include a GitHub repository and a demo.",
                'tracks'      => ['Health Tech', 'AgriTech', 'EdTech', 'FinTech'],
                'status'      => 'open',
                'registration_deadline' => now()->addWeeks(2),
                'start_date'  => now()->addWeeks(3),
                'end_date'    => now()->addWeeks(3)->addDays(2),
                'max_team_size' => 4,
                'prizes'      => "1st: ₦150,000 + incubation support\n2nd: ₦100,000\n3rd: ₦50,000",
                'created_by'  => $organiser->id,
            ]
        );
        $this->makeTeam($open, 'Team Pathfinders', 'EdTech', 0, 3);
        $this->makeTeam($open, 'AgroLink',         'AgriTech', 3, 3);

        // ---------- 2. ONGOING — build phase ----------
        $ongoing = Hackathon::firstOrCreate(
            ['title' => 'Campus Solutions Sprint'],
            [
                'description' => 'A 72-hour sprint to digitalise everyday campus processes — hostel clearance, course registration queues, lost & found, and more.',
                'theme'       => 'Digitalise the Campus',
                'tracks'      => ['Student Services', 'Automation'],
                'status'      => 'ongoing',
                'registration_deadline' => now()->subDays(3),
                'start_date'  => now()->subDay(),
                'end_date'    => now()->addDays(2),
                'max_team_size' => 4,
                'prizes'      => "Winner: ₦80,000\nRunner-up: ₦40,000",
                'created_by'  => $organiser->id,
            ]
        );
        $t1 = $this->makeTeam($ongoing, 'QueueBusters',   'Student Services', 6, 3);
        $t2 = $this->makeTeam($ongoing, 'HostelHub',      'Automation',       9, 3);
        $this->draftSubmission($ongoing, $t1, 'SmartQueue', 'A virtual queue system for course registration with SMS position alerts.');

        // ---------- 3. JUDGING — submissions in, judges scoring ----------
        $judging = Hackathon::firstOrCreate(
            ['title' => 'AI for Education Hackathon'],
            [
                'description' => 'Apply artificial intelligence to improve learning outcomes for Nigerian students. Judged on innovation, execution, impact and presentation.',
                'theme'       => 'AI in the Classroom',
                'tracks'      => ['AI Tutoring', 'Assessment', 'Accessibility'],
                'judge_ids'   => [$judgeA->id, $judgeB->id],
                'status'      => 'judging',
                'registration_deadline' => now()->subWeeks(2),
                'start_date'  => now()->subDays(5),
                'end_date'    => now()->subDay(),
                'max_team_size' => 3,
                'prizes'      => "1st: ₦100,000\n2nd: ₦60,000\n3rd: ₦30,000",
                'created_by'  => $organiser->id,
            ]
        );
        $j1 = $this->makeTeam($judging, 'NeuralMinds',  'AI Tutoring',   0, 3);
        $j2 = $this->makeTeam($judging, 'ExamBuddy',    'Assessment',    3, 3);
        $j3 = $this->makeTeam($judging, 'InclusiveAI',  'Accessibility', 6, 2);
        $s1 = $this->submittedSubmission($judging, $j1, 'TutorGPT', 'An AI study companion that generates personalised practice questions from lecture notes.');
        $s2 = $this->submittedSubmission($judging, $j2, 'GradeWise', 'Automated theory-question marking with instant feedback for lecturers.');
        $s3 = $this->submittedSubmission($judging, $j3, 'SignLearn', 'Real-time sign-language captioning for recorded lectures.');
        $this->score($s1, $judgeA->id, 9, 8, 9, 8, 'Impressive working demo and a clear use case.');
        $this->score($s2, $judgeA->id, 7, 8, 8, 7, 'Solid execution; marking accuracy needs more evaluation.');
        // s3 not yet scored — judging still in progress

        // ---------- 4. COMPLETED — results published, certificates issued ----------
        $done = Hackathon::firstOrCreate(
            ['title' => 'CodeFest Anyigba 2026'],
            [
                'description' => 'The premier inter-departmental coding festival. Teams battled for 48 hours to build the most impactful open-source tool.',
                'theme'       => 'Open Source for Good',
                'tracks'      => ['Web', 'Mobile', 'Data'],
                'judge_ids'   => [$judgeA->id, $judgeB->id],
                'status'      => 'completed',
                'registration_deadline' => now()->subMonths(2),
                'start_date'  => now()->subMonths(2)->addWeek(),
                'end_date'    => now()->subMonths(2)->addWeek()->addDays(2),
                'max_team_size' => 4,
                'prizes'      => "1st: ₦120,000\n2nd: ₦70,000\n3rd: ₦40,000",
                'created_by'  => $organiser->id,
            ]
        );
        $c1 = $this->makeTeam($done, 'BinaryBrains', 'Web',    0, 3);
        $c2 = $this->makeTeam($done, 'DataDrifters', 'Data',   3, 3);
        $c3 = $this->makeTeam($done, 'AppSquad',     'Mobile', 6, 3);
        $cs1 = $this->submittedSubmission($done, $c1, 'OpenTranscript', 'Free transcript request and tracking portal for students.');
        $cs2 = $this->submittedSubmission($done, $c2, 'LectureStats', 'Analytics dashboard for departmental performance data.');
        $cs3 = $this->submittedSubmission($done, $c3, 'CampusGo', 'Mobile way-finding app for new students on campus.');
        $this->score($cs1, $judgeA->id, 9, 9, 10, 9, 'Outstanding — production quality.');
        $this->score($cs1, $judgeB->id, 9, 8, 9, 9,  'Very polished, immediately useful.');
        $this->score($cs2, $judgeA->id, 8, 8, 8, 7,  'Strong data work, presentation could improve.');
        $this->score($cs2, $judgeB->id, 7, 8, 8, 8,  'Good insights, well executed.');
        $this->score($cs3, $judgeA->id, 7, 7, 8, 8,  'Nice UX, limited feature depth.');
        $this->score($cs3, $judgeB->id, 7, 6, 7, 8,  'Promising start.');
        // Placement certificates (1st, 2nd, 3rd by score order)
        $this->issueCertificates($done, [$c1->id => '1st', $c2->id => '2nd', $c3->id => '3rd']);

        $this->command->info('✅ Hackathons now in system: ' . Hackathon::count()
            . ' | teams: ' . HackathonTeam::count()
            . ' | submissions: ' . HackathonSubmission::count()
            . ' | hackathon certificates: ' . Certificate::where('type', 'hackathon')->count());
    }

    private function makeTeam(Hackathon $h, string $name, string $track, int $poolOffset, int $size): HackathonTeam
    {
        $team = HackathonTeam::firstOrCreate(
            ['hackathon_id' => $h->id, 'name' => $name],
            ['track' => $track, 'join_code' => strtoupper(Str::random(8))]
        );

        $members = array_slice($this->studentPool, $poolOffset % max(count($this->studentPool) - $size, 1), $size);
        foreach ($members as $i => $userId) {
            DB::table('hackathon_team_members')->insertOrIgnore([
                'team_id'    => $team->id,
                'user_id'    => $userId,
                'is_lead'    => $i === 0,
                'created_at' => now(), 'updated_at' => now(),
            ]);
        }
        return $team;
    }

    private function draftSubmission(Hackathon $h, HackathonTeam $t, string $title, string $desc): HackathonSubmission
    {
        return HackathonSubmission::firstOrCreate(
            ['hackathon_id' => $h->id, 'team_id' => $t->id],
            ['title' => $title, 'description' => $desc, 'status' => 'draft',
             'github_url' => 'https://github.com/paau-teams/' . Str::slug($title)]
        );
    }

    private function submittedSubmission(Hackathon $h, HackathonTeam $t, string $title, string $desc): HackathonSubmission
    {
        return HackathonSubmission::firstOrCreate(
            ['hackathon_id' => $h->id, 'team_id' => $t->id],
            ['title' => $title, 'description' => $desc, 'status' => 'submitted',
             'github_url'   => 'https://github.com/paau-teams/' . Str::slug($title),
             'demo_url'     => 'https://' . Str::slug($title) . '.demo.paau.edu.ng',
             'submitted_at' => $h->end_date ?? now()->subDay()]
        );
    }

    private function score(HackathonSubmission $s, int $judgeId, int $inn, int $exe, int $imp, int $pre, string $notes): void
    {
        DB::table('hackathon_scores')->updateOrInsert(
            ['submission_id' => $s->id, 'judge_id' => $judgeId],
            ['innovation' => $inn, 'execution' => $exe, 'impact' => $imp, 'presentation' => $pre,
             'notes' => $notes, 'created_at' => now(), 'updated_at' => now()]
        );
    }

    private function issueCertificates(Hackathon $h, array $placementsByTeam): void
    {
        foreach ($placementsByTeam as $teamId => $placement) {
            $memberIds = DB::table('hackathon_team_members')->where('team_id', $teamId)->pluck('user_id');
            foreach ($memberIds as $userId) {
                Certificate::firstOrCreate(
                    ['certificate_id' => "PAAU-H-{$h->id}-{$teamId}-{$userId}"],
                    [
                        'user_id'           => $userId,
                        'learning_path_id'  => null,
                        'hackathon_team_id' => $teamId,
                        'placement'         => $placement,
                        'type'              => 'hackathon',
                        'issued_at'         => $h->end_date ?? now(),
                    ]
                );
            }
        }
    }
}
