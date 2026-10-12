<?php

namespace App\Http\Controllers;

use App\Models\Addresse;
use App\Models\Data_umkm;
use App\Models\Menu;
use App\Models\User;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Kreait\Firebase\Contract\Auth;

class UmkmController extends Controller
{

    public function allDataUmkm()
    {
        try {
            $allUmkm = Data_umkm::all();

            return response()->json([
                'success' => true,
                'message' => 'List Semua Toko',
                'data' => $allUmkm
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menampilkan Toko',
                'data' => ''
            ], 400);
        }
    }

    public function search(Request $request)
    {
        try {
            $keyword = $request->keyword;
            $umkm = Data_umkm::where('nama_umkm', 'like', '%' . $keyword . '%')->get();
            $umkm = $umkm->map(function ($umkms) {
                return $umkms;
            });
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function shopData(Request $request)
    {

        try {
            $shopData = Data_umkm::where('id', $request->id)->first();

            return response()->json([
                'success' => true,
                'message' => 'Data Toko',
                'data' => $shopData
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menampilkan data toko',
                'data' => ''
            ], 400);
        }
    }

    public function getDistance($userId, $umkmId)
    {
        $user = User::all()->find($userId);
        $umkm = Data_umkm::all()->find($umkmId);

        if (!$user || !$umkm) {
            return back()->withErrors(['error' => 'User or UMKM not found.']);
        }

        $userLocation = Addresse::all()->where('user_id', $userId)->where('utama', 1)->first()->geo;
        $umkmLocation = $umkm->geo;

        $apiKey = env('GOOGLE_MAPS_API_KEY');

        $client = new Client();

        $url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=$userLocation&destinations=$umkmLocation&key=$apiKey";

        $response = $client->get($url);

        if ($response->getStatusCode() === 200) {
            $data = json_decode($response->getBody(), true);
            $distance = $data['rows'][0]['elements'][0]['distance']['value'];
            return $distance;
        } else {
            return back()->withErrors(['error' => 'Failed to calculate distance.']);
        }
    }

    /** Kelola Resto: daftar dengan pencarian dan filter kategori menu. */
    public function adminIndex(Request $request)
    {
        $q = trim((string) $request->query('q'));
        $kategori = (string) $request->query('kategori');

        $umkms = Data_umkm::query()
            ->withCount('menu')
            ->when($q !== '', fn ($query) => $query->where(fn ($w) => $w->where('nama_umkm', 'like', "%{$q}%")->orWhere('alamat', 'like', "%{$q}%")))
            ->when(isset(Menu::CATEGORIES[$kategori]), fn ($query) => $query->whereHas('menu', fn ($m) => $m->where('category', $kategori)))
            ->orderBy('nama_umkm')
            ->paginate(10)
            ->withQueryString();

        return view('pages.Admin.umkm', [
            'umkms' => $umkms,
            'q' => $q,
            'kategori' => isset(Menu::CATEGORIES[$kategori]) ? $kategori : '',
        ]);
    }

    /** Tambah Resto (Figma: wizard Informasi, Produk, Selesai). */
    public function create()
    {
        return view('pages.Admin.umkm_create');
    }

    /** Detail Resto: informasi (bisa diedit) + daftar produk. */
    public function edit(Data_umkm $umkm)
    {
        return view('pages.Admin.UMKM_update', [
            'umkm' => $umkm,
            'products' => Menu::where('data_umkm_id', $umkm->id)->get(),
        ]);
    }

    public function storeUmkm(Request $request)
    {
        $staticDate = '2024-01-01 ';
        try {
            $validatedData = $request->validate([
                'user_id' => 'required',
                'nama_umkm' => 'required',
                'logo_umkm' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
                'alamat' => 'required',
                'open_time' => 'required',
                'close_time' => 'required',
                'no_telp_umkm' => 'required',
                'vip' => 'required',
                'alamat' => 'required',
                'link' => 'required',
                'geo' => 'required',
            ]);

            Data_umkm::create([
                'user_id' => $request->user_id,
                'nama_umkm' => $request->nama_umkm,
                'logo_umkm' => $request->logo_umkm->storePublicly($request->nama_umkm, 'public'),
                'alamat' => $request->alamat,
                'open_time' => $staticDate . $request->open_time . ':00',
                'close_time' => $staticDate . $request->close_time . ':00',
                'no_telp_umkm' => $request->no_telp_umkm,
                'vip' => $request->vip,
                "alamat" => $request->alamat,
                "link" => $request->link,
                "geo" => $request->geo,
            ]);
            return redirect()->route('umkm')->with('success', 'Data UMKM berhasil ditambahkan');
        } catch (\Exception $e) {
            return redirect()->back()->with('error2', 'Error');
        }
    }

    public function addProduct(Request $request)
    {
        try {
            $idUmkm = Data_umkm::find($request->umkm)->id;
            $validatedData = $request->validate([
                'umkm' => 'required',
                'nama_makanan' => 'required',
                //'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
                'deskripsi' => 'required',
                'harga' => 'required',
                //'promo' => 'required',
                'category' => 'required',
            ]);
            Menu::create([
                "data_umkm_id" => $idUmkm,
                "nama_makanan" => $request->nama_makanan,
                "deskripsi" => $request->deskripsi,
                "harga" => $request->harga,
                "image" => $request->image->storePublicly('public/' . Data_umkm::find($request->umkm)->nama_umkm),
                "rating" => 0,
                "slug" => "",
                "diskon" => $request->promo,
                "category" => $request->category,
            ]);
            return redirect()->back()->with('success', 'Menu stored successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to store the menu.');
        }
    }

    public function update(Request $request, $id)
    {
        // Divalidasi di luar try/catch supaya pesan error sampai ke form, bukan "Error" generik
        $request->validate([
            'edit_nama_umkm' => 'required|string|max:255',
            'edit_alamat' => 'required|string|max:255',
            'edit_open_time' => 'required|date_format:H:i',
            'edit_close_time' => 'required|date_format:H:i',
            'edit_no_telp_umkm' => 'required|string|max:255',
            'edit_vip' => 'required|in:0,1',
            'edit_link' => 'nullable|string',
            'edit_geo' => 'nullable|string|max:255',
            'logo_umkm' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [], [
            'edit_nama_umkm' => 'nama UMKM',
            'edit_alamat' => 'alamat',
            'edit_open_time' => 'jam buka',
            'edit_close_time' => 'jam tutup',
            'edit_no_telp_umkm' => 'no. telp',
            'edit_vip' => 'VIP',
            'edit_link' => 'link',
            'edit_geo' => 'geo',
            'logo_umkm' => 'logo',
        ]);

        try {
            $umkm = Data_umkm::findOrFail($id);
            $staticDate = '2024-01-01 ';
            $umkm->nama_umkm = $request->edit_nama_umkm;
            $umkm->alamat = $request->edit_alamat;
            $umkm->open_time = $staticDate . $request->edit_open_time . ':00';
            $umkm->close_time = $staticDate . $request->edit_close_time . ':00';
            $umkm->no_telp_umkm = $request->edit_no_telp_umkm;
            $umkm->vip = $request->edit_vip;
            $umkm->link = $request->edit_link;
            $umkm->geo = $request->edit_geo;

            // Check if a new logo is uploaded
            if ($request->hasFile('logo_umkm')) {
                // Store the new logo and update the logo_umkm attribute
                $umkm->logo_umkm = $request->logo_umkm->storePublicly($umkm->nama_umkm, 'public');
            }

            $umkm->save();

            return redirect()->back()->with('success', 'Data UMKM berhasil diupdate');
        } catch (\Exception $e) {
            return redirect()->back()->with('error2', 'Error');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $umkm = Data_umkm::findOrFail($id);

        try {
            $umkm->delete();
            Storage::delete($umkm->nama_umkm . '/' . $umkm->logo_umkm);
            return redirect()->route('umkm')->with('success', 'Resto ' . $umkm->nama_umkm . ' berhasil dihapus.');
        } catch (\Exception $e) {
            report($e);
            return redirect()->back()->with('error2', 'Resto ' . $umkm->nama_umkm . ' gagal dihapus.');
        }
    }
}
