<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Gadget;
use App\Services\GadgetService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GadgetServiceTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_returns_grouped_prices_for_existing_gadget()
    {
        $gadget = Gadget::factory()->create([
            'slug' => 'nokia-3310',
        ]);

        $gadget->prices()->createMany([
            ['source' => 'eBay', 'price' => 10.99, 'image_url' => 'https://example.com/1.jpg'],
            ['source' => 'eBay', 'price' => 12.49, 'image_url' => 'https://example.com/2.jpg'],
            ['source' => 'Prom', 'price' => 500, 'image_url' => 'https://example.com/3.jpg'],
        ]);

        $service = new GadgetService();
        $result = $service->getGadgetWithGroupedPrices('nokia-3310');

        $this->assertArrayHasKey('gadget', $result);
        $this->assertArrayHasKey('ebayPrices', $result);
        $this->assertArrayHasKey('ebayImages', $result);
        $this->assertCount(2, $result['ebayPrices']);
        $this->assertCount(2, $result['ebayImages']);
        $this->assertEquals($gadget->id, $result['gadget']->id);
    }
}
