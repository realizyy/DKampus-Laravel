<?php

namespace App\Http\Controllers;

use App\Models\Data_umkm;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Exception;

class MenuController extends Controller
{
    public function products(Request $request)
    {
        try {
            $products = Menu::all();

            return response()->json([
                'success' => true,
                'message' => 'List Semua Menu',
                'data' => $products
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menampilkan menu',
                'data' => ''
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

    public function search_item(Request $request)
    {
        try {
            $menus = Menu::with('data_umkm')->where('nama_makanan', 'like', '%' . $request->query('query') . '%')->get();


            return response()->json($menus);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ]);
        }
    }

    public function data_umkm()
    {
        return $this->belongsTo(Data_umkm::class);
    }

    public function search(Request $request)
    {
        try {
            $keyword = $request->keyword;
            $menus = Menu::where('nama_makanan', 'like', "%{$keyword}%")
                ->with('data_umkm')
                ->get();
            $menus = $menus->map(function ($menu) {
                $menu->nama_umkm = $menu->data_umkm->nama_umkm;
                return $menu;
            });
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ]);
        }
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
    }

    public function simpan($id, Request $request)
    {
        try {
            $userID = Auth::user()->id;
            $database = app('firebase.database');
            $saved = Menu::find($id);
            $idMakanan = $saved->id;
            $umkmID = $saved->data_umkm_id;
            $jumlah = $request->input('quantity');
            $existingUmkmID = $database->getReference('cart/' . $userID . '/orders')->getSnapshot()->exists();

            // Harga yang disimpan di keranjang sudah dipotong diskon (konsisten dengan halaman detail)
            $hargaFinal = (float) $saved->diskon > 0 ? (int) round($saved->harga * (1 - $saved->diskon / 100)) : $saved->harga;

            $postData = [
                'id' => $idMakanan,
                'nama' => $saved->nama_makanan,
                'harga' => $hargaFinal,
                'harga_asli' => $saved->harga,
                'umkm_id' => $umkmID,
                'jumlah' => $jumlah,
                'catatan' => $request->input('catatan') ?? "-"
            ];

            if ($existingUmkmID) {
                $items = $database->getReference('cart/' . $userID . '/orders')->getValue();
                $itemKeys = array_keys($items);
                $currentUmkmID = $items[$itemKeys[0]]['umkm_id'];

                if ($currentUmkmID != $umkmID) {
                    $database->getReference('cart/' . $userID . '/orders')->remove();
                    $database->getReference('cart/' . $userID . '/orders/item1')->set($postData);
                } else {
                    $itemExists = false;
                    foreach ($items as $key => $item) {
                        if ($item['id'] == $idMakanan) {
                            $database->getReference('cart/' . $userID . '/orders/' . $key . '/jumlah')->set($jumlah);
                            $itemExists = true;
                            break;
                        }
                    }

                    if (!$itemExists) {
                        $newItemNumber = $this->findNextAvailableItemNumber($items);
                        $itemKey = 'item' . $newItemNumber;
                        $database->getReference('cart/' . $userID . '/orders/' . $itemKey)->set($postData);
                    }
                }
            } else {
                $database->getReference('cart/' . $userID . '/orders/item1')->set($postData);
            }

            $total = $this->calculateTotalPrice($userID);
            $database->getReference('cart/' . $userID . '/total')->set($total);
            $database->getReference('cart/' . $userID . '/kategori')->set('makanan');
            $database->getReference('cart/' . $userID . '/umkm_address')->set(Data_umkm::find($umkmID)->alamat);
            $database->getReference('cart/' . $userID . '/umkm_link_address')->set(Data_umkm::find($umkmID)->link);

            return redirect()->back()->with('status', 'success');
        } catch (Exception $e) {
            return redirect()->back()->with('error2', 'Error');
        }
    }

    private function findNextAvailableItemNumber($items)
    {
        $existingNumbers = array_map(function ($key) {
            return intval(str_replace('item', '', $key));
        }, array_keys($items));

        $i = 1;
        while (in_array($i, $existingNumbers)) {
            $i++;
        }
        return $i;
    }

    private function calculateTotalPrice($userID)
    {
        try {
            $database = app('firebase.database');
            $total = 0;
            $items = $database->getReference('cart/' . $userID . '/orders')->getValue();

            if ($items) {
                foreach ($items as $item) {
                    $pricePerItem = $item['harga'];
                    $quantityPerItem = $item['jumlah'];
                    $total += $pricePerItem * $quantityPerItem;
                }
            }

            return $total;
        } catch (Exception $e) {
            return 0;
        }
    }

    public function store(Request $request)
    {
        $validate = $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        $findUmkm = Data_umkm::find($request->umkm);

        try {
            Menu::create([
                "data_umkm_id" => $findUmkm->id,
                "nama_makanan" => $request->nama_makanan,
                "deskripsi" => $request->deskripsi,
                "harga" => $request->harga,
                "image" => $request->image->storePublicly('public/' . $findUmkm->nama_umkm),
                "rating" => 0,
                "promo" => $request->promo,
                "category" => $request->category,
            ]);
            return redirect()->back()->with('success', 'Menu stored successfully.');
        } catch (\Exception $e) {
            dd($e);
            return redirect()->back()->with('error', 'Failed to store the menu.');
        }
    }

    /** Produk: semua menu dengan pencarian, filter resto dan kategori. */
    public function adminIndex(Request $request)
    {
        $q = trim((string) $request->query('q'));
        $kategori = isset(Menu::CATEGORIES[(string) $request->query('kategori')]) ? (string) $request->query('kategori') : '';
        $umkmId = (int) $request->query('umkm');

        $menus = Menu::query()
            ->with('data_umkm')
            ->when($q !== '', fn ($query) => $query->where('nama_makanan', 'like', "%{$q}%"))
            ->when($kategori !== '', fn ($query) => $query->where('category', $kategori))
            ->when($umkmId > 0, fn ($query) => $query->where('data_umkm_id', $umkmId))
            ->orderBy('data_umkm_id')
            ->orderBy('nama_makanan')
            ->paginate(10)
            ->withQueryString();

        return view('pages.Admin.product', [
            'umkms' => Data_umkm::orderBy('nama_umkm')->get(),
            'menus' => $menus,
            'q' => $q,
            'kategori' => $kategori,
            'umkmId' => $umkmId,
        ]);
    }

    /** Form edit produk satu halaman (juga dipakai dari daftar produk resto). */
    public function edit(Menu $menu)
    {
        return view('pages.Admin.product_update', [
            'model' => $menu,
            'umkm' => Data_umkm::pluck('nama_umkm', 'id'),
        ]);
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'edit_umkm' => 'required|exists:data_umkms,id',
            'edit_nama_makanan' => 'required|string',
            'edit_deskripsi' => 'required|string',
            'edit_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'edit_harga' => 'required|numeric|min:0',
            // "promo" di form = persentase diskon (kolom menus.diskon)
            'edit_promo' => 'nullable|numeric|min:0|max:100',
            'edit_category' => 'required|string',
        ], [], [
            'edit_umkm' => 'UMKM',
            'edit_nama_makanan' => 'nama makanan',
            'edit_deskripsi' => 'deskripsi',
            'edit_image' => 'gambar',
            'edit_harga' => 'harga',
            'edit_promo' => 'diskon',
            'edit_category' => 'kategori',
        ]);

        $menu = Menu::findOrFail($id);

        $data = [
            'data_umkm_id' => $validatedData['edit_umkm'],
            'nama_makanan' => $validatedData['edit_nama_makanan'],
            'deskripsi' => $validatedData['edit_deskripsi'],
            'harga' => $validatedData['edit_harga'],
            'diskon' => $validatedData['edit_promo'] ?? 0,
            'category' => $validatedData['edit_category'],
        ];

        // Gambar opsional: kalau tidak diunggah, gambar lama dipertahankan
        if ($request->hasFile('edit_image')) {
            $umkm = Data_umkm::findOrFail($validatedData['edit_umkm']);
            $data['image'] = $request->file('edit_image')->storePublicly('public/' . $umkm->nama_umkm);
        }

        $menu->update($data);

        return redirect()->back()->with('success', 'Menu item updated successfully.');
    }

    public function destroy(Menu $menu)
    {
        DB::beginTransaction();
        try {
            $MenuCount = Menu::where('data_umkm_id', $menu->data_umkm_id)->count();
            if ($MenuCount == 1) {
                session()->flash('error2', 'Product ' . $menu->nama_makanan . ' Gagal Dihapus, karena UMKM ' . $menu->data_umkm->nama_umkm . ' hanya memiliki 1 product');
            }
            Menu::findOrFail($menu->id)->delete();
            Storage::delete('public/' . $menu->data_umkm->nama_umkm . '/' . $menu->image);
            DB::commit();
            session()->flash('success', 'Product ' . $menu->nama_makanan . ' Berhasil Dihapus');
            return redirect()->back();
        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
            session()->flash('error2', 'Product ' . $menu->nama_makanan . ' Gagal Dihapus');
            return redirect()->back();
        }
    }
}
