<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;

class CommentSeeder extends Seeder
{
    /**
     * Seed realistic editorial and engineering reader comments across published articles.
     */
    public function run(): void
    {
        $userIds = User::pluck('id')->all();
        $postIds = Post::orderBy('id')->pluck('id')->all();

        if (empty($userIds) || empty($postIds)) {
            return;
        }

        $comments = [
            'Strong breakdown of the failure modes around tail latency—especially isolating read replicas from background analytical jobs.',
            'We rolled out a similar connection-pooler topology last quarter and saw p99 checkout latency drop from 420ms to 68ms.',
            'The contrast-ratio edge cases over photographic scrims caught us off guard during our light-mode launch too. Scoped token rules fixed it cleanly.',
            'Appreciate the concrete numbers on NAT gateway egress optimization. Moving S3 and container registry pulls to VPC endpoints paid for itself in a week.',
            'Great perspective on keeping domain modules decoupled inside a modular monolith before committing to network boundaries.',
            'How are you handling schema evolution on the outbox relay table during zero-downtime blue/green migrations?',
            'Clear comparison between vectorized execution and row-oriented indexes. Our billing dashboard queries went from 14s to under 300ms.',
            'Security teams rarely talk enough about passkey recovery flows in enterprise environments—this struck the right balance between UX and compliance.',
            'Switching our CI pipeline to deterministic Nix environments cut flaky integration builds down below 0.5%.',
            'Helpful reminder that evaluation harnesses need adversarial edge cases just as much as golden happy-path prompts.',
            'The idempotency key lifecycle section should be required reading for every payments integration team.',
            'We adopted error-budget burn alerts with two-window multi-burn-rate thresholds and reduced midnight pages by over 60%.',
            'Bookmarking this for our design-system audit next sprint—fluid clamped type scales eliminated half our mobile media queries.',
            'Excellent deep dive. Did you benchmark KV-cache quantization accuracy impact on code-generation tasks specifically?',
            'Clean explanation of RBAC versus policy-based authorization boundaries inside multi-tenant workspaces.',
            'Having ephemeral preview databases per pull request transformed how our PMs and QA sign off on UI changes.',
            'Practical trade-offs throughout. Avoiding premature sharding until a single tuned primary saturates saves years of operational overhead.',
            'Replaying production trace samples against staging before canary promotion caught two subtle serialization regressions for us.',
            'Solid ROI framework for developer experience initiatives—tying PR cycle time directly to deployment batch size resonated with leadership.',
            'Crisp walkthrough! Combining signed SLSA provenance attestations with admission policies closed our container integrity gap cleanly.',
        ];

        foreach ($postIds as $index => $postId) {
            // Add 1-2 thoughtful comments on the top 25 posts so discussion threads look active and authentic
            if ($index >= 25) {
                break;
            }
            $countForPost = ($index < 15) ? 2 : 1;
            for ($c = 0; $c < $countForPost; $c++) {
                $text = $comments[($index + $c * 7) % count($comments)];
                $userId = $userIds[($index * 3 + $c) % count($userIds)];
                Comment::create([
                    'user_id' => $userId,
                    'post_id' => $postId,
                    'comment' => $text,
                    'status' => true,
                    'created_at' => now()->subHours(($index * 5) + ($c * 2) + 1),
                    'updated_at' => now()->subHours(($index * 5) + ($c * 2) + 1),
                ]);
            }
        }
    }
}
