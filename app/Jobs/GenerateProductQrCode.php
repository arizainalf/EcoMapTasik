<?php
namespace App\Jobs;

use App\Models\Product;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class GenerateProductQrCode implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $product;

    public function __construct(Product $product)
    {
        $this->product = $product;
    }

    public function handle()
    {
        $productUrl = route('products.show', $this->product->id);

        $result = Builder::create()
            ->writer(new PngWriter())
            ->data($productUrl)
            ->size(300)
            ->margin(10)
            ->build();

        $path = 'qrcodes/products/' . $this->product->slug . $this->product->id . '.png';

        Storage::disk('public')->put($path, $result->getString());

        $this->product->updateQuietly(['qr_code_path' => $path]);
    }
}
