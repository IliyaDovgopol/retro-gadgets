<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Gadget;
use App\Services\PriceUpdater;
use Illuminate\Support\Facades\Log;

class RevealGadget extends Command
{
    protected $signature = 'gadgets:reveal-one';
    protected $description = 'Makes one hidden gadget visible and updates prices';

    public function handle(): int
    {
		Log::info('📆 RevealGadget: started');
        // Random delay to simulate natural behavior
		$hour = now()->hour;
		if ($hour < 9 || $hour > 20) {
			Log::info("🌙 Skipping: current hour $hour is outside allowed range");
			return Command::SUCCESS;
		}
        sleep(random_int(0, 55) * 60);

        $today = now()->toDateString();

        // Skip if already published this week
        $publishedThisWeek = Gadget::whereBetween('published_at', [now()->startOfWeek(), now()])
            ->where('is_visible', true)
            ->exists();

        if ($publishedThisWeek) {
            Log::info('🕐 Already published this week');
            return Command::SUCCESS;
        }

        // ~30% chance to publish this week
        if (random_int(1, 100) > 30) {
            Log::info('❌ Skipping (random chance)');
            return Command::SUCCESS;
        }

        // Find first hidden gadget
        $gadget = Gadget::where('is_visible', false)
            ->orderBy('created_at')
            ->first();

        if (!$gadget) {
            Log::info('🎉 No hidden gadgets left');
            return Command::SUCCESS;
        }

        // Reveal gadget
        $gadget->update([
            'is_visible' => true,
            'published_at' => now(),
        ]);

        Log::info("✅ Published: {$gadget->name}");

        // Update prices after publishing
        try {
            app(PriceUpdater::class)->updatePrices();
            Log::info("💰 Prices updated for {$gadget->name}");
        } catch (\Throwable $e) {
            Log::error("❗ Price update error: " . $e->getMessage());
        }

        return Command::SUCCESS;
    }
}
