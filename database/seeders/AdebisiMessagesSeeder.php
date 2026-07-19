<?php

namespace Database\Seeders;

use App\Models\{User, Mentorship, Conversation, Message};
use Illuminate\Database\Seeder;

/**
 * Demo seeder: populates direct-message conversations between
 * Adebisi Lekan (23cs1008) and each of his active mentees, so the
 * Messages/inbox screens have realistic, topic-aware chat history.
 *
 * Run AFTER AdebisiMenteesSeeder (needs the active mentorships).
 */
class AdebisiMessagesSeeder extends Seeder
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

        // Topic-specific talking points: [concept, explanation, feedback]
        $byTopic = [
            'Laravel' => [
                'how migrations work',
                'Migrations are version control for your database — each one defines a schema change you can run or roll back with artisan.',
                'Use Eloquent relationships instead of manual joins, and keep your controllers thin.',
            ],
            'JavaScript' => [
                'the difference between let and const',
                'Use const by default and let only when you need to reassign. Both are block-scoped, unlike var.',
                'Reach for async/await over nested callbacks — it keeps asynchronous code readable.',
            ],
            'React' => [
                'when to use useEffect',
                'useEffect runs side effects after render. Give it a dependency array so it only re-runs when those values change.',
                'Break large components into smaller reusable ones and lift state up only when you really need to.',
            ],
            'Python' => [
                'list comprehensions',
                'They let you build a list in one readable line, e.g. [x*2 for x in items if x > 0].',
                'Name your variables clearly and add short docstrings to each function.',
            ],
            'Data Structures' => [
                'when to use a hash map vs an array',
                'Hash maps give you O(1) lookups by key; arrays are better for ordered, index-based access.',
                'Always reason about time complexity before you pick a data structure.',
            ],
            'HTML/CSS' => [
                'flexbox alignment',
                'Use justify-content for the main axis and align-items for the cross axis. flex-wrap helps on smaller screens.',
                'Keep your CSS organised and use rem units so spacing scales nicely.',
            ],
            'PHP' => [
                'how associative arrays work',
                'They map string keys to values, e.g. $user["name"] — great for structured data before you move to objects.',
                'Always sanitise user input and use prepared statements for any database query.',
            ],
            'MySQL' => [
                'the difference between INNER and LEFT JOIN',
                'INNER JOIN returns only matching rows; LEFT JOIN keeps every row from the left table even without a match.',
                'Add indexes on the columns you filter or join on to keep queries fast.',
            ],
        ];

        $default = [
            'the core concepts',
            'Start with the fundamentals and build small projects — understanding beats memorising.',
            'Great progress. Keep practising consistently and review your own code critically.',
        ];

        $mentorships = Mentorship::where('mentor_id', $adebisi->id)
                                 ->where('status', 'active')
                                 ->get();

        if ($mentorships->isEmpty()) {
            $this->command->warn('No active mentorships for Adebisi — run AdebisiMenteesSeeder first.');
            return;
        }

        $seeded = 0;

        foreach ($mentorships as $idx => $mentorship) {
            $mentee = $mentorship->mentee;
            if (!$mentee) continue;

            $topic       = $mentorship->topic ?? 'programming';
            $first       = $mentee->first_name ?: 'there';
            [$concept, $explain, $feedback] = $byTopic[$topic] ?? $default;

            $conversation = Conversation::firstOrCreate(
                ['mentorship_id' => $mentorship->id],
            );

            // Don't double-seed if messages already exist for this conversation.
            if ($conversation->messages()->exists()) {
                continue;
            }

            // Scripted, topic-aware thread. M = mentor (Adebisi), U = mentee.
            $thread = [
                ['M', "Hi {$first}! Great to have you on board for {$topic}. Have you set up your environment yet?"],
                ['U', "Hi Adebisi, yes I've got everything installed. Really excited to start with {$topic}!"],
                ['M', "Perfect. I've shared a learning path — try to finish the first task before our session this week."],
                ['U', "Will do. Quick question though — could you explain {$concept}? I'm a bit stuck on Task 1."],
                ['M', "{$explain} Send me your code once you've tried and I'll review it."],
                ['U', "Just submitted it. Thank you so much 🙏"],
                ['M', "Reviewed it — solid work! One note: {$feedback} Overall you're doing really well."],
                ['U', "That's super helpful. When should we schedule the next session?"],
                ['M', "Let's keep the same time next week. Keep up the momentum, {$first}!"],
                ['U', "Sounds good. Thanks again for all the guidance!"],
            ];

            // Spread messages from ~3 weeks ago up to the last day or two.
            $cursor = now()->subWeeks(3)->subDays($idx % 3)->setTime(9, 0);
            $last   = null;

            foreach ($thread as $j => [$who, $text]) {
                // Advance time between messages (minutes to hours, occasional day jump).
                $cursor = $cursor->copy()->addMinutes([7, 22, 240, 55, 1440, 15, 900, 33, 1440, 18][$j] ?? 30);

                $senderId = $who === 'M' ? $adebisi->id : $mentee->id;

                // Everything is read, except we leave the final mentee message
                // unread on the first two threads so the mentor sees an unread badge.
                $leaveUnread = $who === 'U'
                    && $j === array_key_last($thread)
                    && $seeded < 2;

                $msg = Message::create([
                    'conversation_id' => $conversation->id,
                    'sender_id'       => $senderId,
                    'body'            => $text,
                    'type'            => 'text',
                    'read_at'         => $leaveUnread ? null : $cursor->copy()->addMinutes(3),
                    'created_at'      => $cursor,
                    'updated_at'      => $cursor,
                ]);

                $last = $msg;
            }

            if ($last) {
                $conversation->update(['last_message_at' => $last->created_at]);
            }

            $seeded++;
        }

        $this->command->info("✅ Seeded direct-message threads for {$seeded} of Adebisi's mentees.");
    }
}
