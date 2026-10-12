<?php

namespace App\Http\Controllers;

use App\Models\history;
use App\Models\Rating;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/** Menyimpan rating pembeli untuk pesanan selesai miliknya (satu kali per pesanan). */
class RatingController extends Controller
{
    private const ASPECTS = ['Pengiriman', 'Harga', 'Rasa', 'Kebersihan', 'Kualitas'];

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'history_id' => ['required', 'integer'],
            'rating_pelayanan' => ['required', 'integer', 'between:1,5'],
            'rating_makanan' => ['required', 'integer', 'between:1,5'],
            'aspects' => ['nullable', 'array'],
            'aspects.*' => ['string', 'in:'.implode(',', self::ASPECTS)],
            'review' => ['nullable', 'string', 'max:1000'],
        ], [
            'rating_pelayanan.required' => 'Beri bintang untuk pelayanan.',
            'rating_makanan.required' => 'Beri bintang untuk makanan.',
            'rating_pelayanan.between' => 'Beri bintang untuk pelayanan.',
            'rating_makanan.between' => 'Beri bintang untuk makanan.',
        ]);

        // Hanya pesanan milik pembeli yang login dan sudah selesai
        $history = history::where('id', $data['history_id'])->where('user_id', $request->user()->id)->firstOrFail();
        abort_unless(strtolower((string) $history->status) === 'completed', 403);

        Rating::updateOrCreate(
            ['user_id' => $request->user()->id, 'history_id' => $history->id],
            [
                'umkm_id' => $history->umkm_id ?: null,
                'rating_pelayanan' => $data['rating_pelayanan'],
                'rating_makanan' => $data['rating_makanan'],
                'aspects' => $data['aspects'] ?? [],
                'review' => $data['review'] ?? null,
            ]
        );

        return redirect('/pesanan/status')->with('success', 'Terima kasih! Rating kamu sudah tersimpan.');
    }
}
