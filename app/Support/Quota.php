<?php

namespace App\Support;

use App\Models\Order;
use App\Models\Promotion;
use Illuminate\Database\UniqueConstraintViolationException;

/**
 * Places in the launch batch and in promotions. A place is a number stored on the order; the unique index
 * on that number is what makes two simultaneous payments unable to share the last one. This class only picks
 * the lowest free number: if two callers pick the same, the second INSERT/UPDATE fails and the caller retries.
 *
 * A pending order holds its place until `expires_at`; expiring an order gives the place back.
 */
class Quota
{
    /** Give back the places of pending orders whose payment window has closed. */
    public static function releaseExpired(): int
    {
        return Order::where('status', Order::PENDING)->where('expires_at', '<=', now())
            ->update(['status' => Order::EXPIRED, 'launch_slot' => null, 'promo_slot' => null, 'updated_at' => now()]);
    }

    public static function launchHeld(): int
    {
        return Order::whereNotNull('launch_slot')->count();
    }

    public static function promoHeld(Promotion $promotion): int
    {
        return Order::where('promotion_id', $promotion->id)->whereNotNull('promo_slot')->count();
    }

    public static function lowestFreeLaunchSlot(int $quota): ?int
    {
        return static::lowestFree(Order::whereNotNull('launch_slot')->pluck('launch_slot')->all(), $quota);
    }

    public static function lowestFreePromoSlot(Promotion $promotion): ?int
    {
        return $promotion->quota === null
            ? null
            : static::lowestFree(Order::where('promotion_id', $promotion->id)->whereNotNull('promo_slot')->pluck('promo_slot')->all(), $promotion->quota);
    }

    /**
     * Take a place back for an order that expired and was then paid. Returns false when none is left.
     */
    public static function reclaim(Order $order, int $launchQuota): bool
    {
        $needsNoPlace = $order->price_tier === 'normal'
            || ($order->price_tier === 'promo' && ($order->promotion === null || $order->promotion->quota === null));

        if ($needsNoPlace || $order->launch_slot !== null || $order->promo_slot !== null) {
            return true;
        }

        for ($attempt = 0; $attempt < 5; $attempt++) {
            $slot = $order->price_tier === 'launch'
                ? static::lowestFreeLaunchSlot($launchQuota)
                : ($order->promotion ? static::lowestFreePromoSlot($order->promotion) : null);

            if ($slot === null) {
                return false;
            }

            try {
                $column = $order->price_tier === 'launch' ? 'launch_slot' : 'promo_slot';
                Order::whereKey($order->id)->update([$column => $slot]);
                $order->refresh();

                return true;
            } catch (UniqueConstraintViolationException) {
                continue;
            }
        }

        return false;
    }

    /** @param  list<int>  $used */
    private static function lowestFree(array $used, int $quota): ?int
    {
        $used = array_flip($used);

        for ($slot = 1; $slot <= $quota; $slot++) {
            if (! isset($used[$slot])) {
                return $slot;
            }
        }

        return null;
    }
}
