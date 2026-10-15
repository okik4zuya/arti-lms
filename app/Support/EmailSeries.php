<?php

namespace App\Support;

use App\Models\Subscriber;

/**
 * The welcome series: four emails after confirmation. Step N is due `after_days` days after the person
 * confirmed, and never sooner than MIN_GAP_HOURS after the previous one, so a scheduler that was off for a
 * week does not send everything at once. The copy lives in resources/views/mail/series/{step}.blade.php.
 */
final class EmailSeries
{
    public const MIN_GAP_HOURS = 20;

    public const STEPS = [
        1 => ['after_days' => 1, 'subject' => 'Kenapa AI bisa mengarang referensi (dan kenapa itu berbahaya)'],
        2 => ['after_days' => 3, 'subject' => 'Kerja kemarin hilang lagi? Cerita tentang Rithah'],
        3 => ['after_days' => 6, 'subject' => 'Seperti apa ARTi bekerja, tanpa istilah rumit'],
        4 => ['after_days' => 10, 'subject' => 'Tawaran dari ARTi, dan batasnya yang perlu Anda tahu'],
    ];

    public static function count(): int
    {
        return count(self::STEPS);
    }

    public static function subject(int $step): string
    {
        return self::STEPS[$step]['subject'];
    }

    /** The step this person should get next, or null when none is due yet (or the series is done). */
    public static function dueStep(Subscriber $subscriber): ?int
    {
        $next = $subscriber->series_step + 1;

        if ($next > self::count() || ! $subscriber->isActive() || $subscriber->buyer_at !== null) {
            return null;
        }

        if ($subscriber->confirmed_at->copy()->addDays(self::STEPS[$next]['after_days'])->isFuture()) {
            return null;
        }

        if ($subscriber->series_sent_at && $subscriber->series_sent_at->gt(now()->subHours(self::MIN_GAP_HOURS))) {
            return null;
        }

        return $next;
    }
}
