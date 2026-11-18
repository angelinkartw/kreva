<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Report;
use App\Models\Review;
use App\Models\Store;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /** Tombol "Lapor" untuk produk, toko, dan ulasan. */
    public function store(Request $request)
    {
        $data = $request->validate([
            'type' => ['required', 'in:product,store,review'],
            'id' => ['required', 'integer'],
            'reason' => ['required', 'in:palsu,terlarang,penipuan,kasar,lainnya'],
            'detail' => ['nullable', 'string', 'max:1000'],
        ]);

        $class = ['product' => Product::class, 'store' => Store::class, 'review' => Review::class][$data['type']];
        $class::findOrFail($data['id']);

        $report = Report::create([
            'reporter_id' => $request->user()->id, 'subject_type' => $class, 'subject_id' => $data['id'],
            'reason' => $data['reason'], 'detail' => isset($data['detail']) ? strip_tags($data['detail']) : null,
        ]);

        return response()->json(['message' => 'Laporan diterima.', 'id' => $report->id], 201);
    }
}
