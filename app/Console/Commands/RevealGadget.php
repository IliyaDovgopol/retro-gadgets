<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Gadget;
use Illuminate\Support\Facades\Log;

class RevealGadget extends Command
{
    protected $signature = 'gadgets:reveal-one';
    protected $description = 'Makes one hidden gadget visible';

    public function handle(): int
    {
        // Wait for a random time (0–55 minutes) to simulate natural publishing
        sleep(random_int(0, 55) * 60);

        $today = now()->toDateString();

        // Allow only one publication per day
        $alreadyPublishedToday = Gadget::whereDate('published_at', $today)
            ->where('is_visible', true)
            ->exists();

        if ($alreadyPublishedToday) {
            Log::info('🕐 Already published a gadget today');
            return Command::SUCCESS;
        }

        // ~20% chance to publish today
        if (random_int(1, 100) > 20) {
            Log::info('❌ Skipping publication (random chance)');
            return Command::SUCCESS;
        }

        // Find the first hidden gadget
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
        return Command::SUCCESS;
    }
}
