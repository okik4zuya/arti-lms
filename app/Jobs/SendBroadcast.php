<?php

namespace App\Jobs;

use App\Mail\BroadcastMail;
use App\Models\Broadcast;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Throwable;

/**
 * Queues one BroadcastMail per recipient. Safe to run again after a crash or a queue outage: a person
 * is recorded in broadcast_deliveries before their mail is queued, and recorded people are skipped, so
 * the second run only picks up whoever is left.
 */
class SendBroadcast implements ShouldQueue
{
    use Dispatchable, Queueable;

    public int $tries = 1;

    public int $timeout = 900;

    public function __construct(public int $broadcastId) {}

    public function handle(): void
    {
        $broadcast = Broadcast::find($this->broadcastId);

        if (! $broadcast || $broadcast->status === Broadcast::SENT) {
            return;
        }

        $broadcast->recipients()->orderBy('id')->chunkById(200, function ($subscribers) use ($broadcast) {
            foreach ($subscribers as $subscriber) {
                $recorded = DB::table('broadcast_deliveries')->insertOrIgnore([
                    'broadcast_id' => $broadcast->id,
                    'subscriber_id' => $subscriber->id,
                    'queued_at' => now(),
                ]);

                if ($recorded === 0) {
                    continue;
                }

                try {
                    Mail::to($subscriber->email)->queue(new BroadcastMail($subscriber, $broadcast));
                } catch (Throwable $e) {
                    // Forget this person so a later run retries them, then stop: the queue is probably down.
                    DB::table('broadcast_deliveries')
                        ->where('broadcast_id', $broadcast->id)
                        ->where('subscriber_id', $subscriber->id)
                        ->delete();

                    throw $e;
                }
            }
        });

        $broadcast->forceFill([
            'status' => Broadcast::SENT,
            'sent_at' => now(),
            'recipients_count' => DB::table('broadcast_deliveries')->where('broadcast_id', $broadcast->id)->count(),
        ])->save();
    }
}
