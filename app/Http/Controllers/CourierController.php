<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Addresse;
use App\Models\HomeModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use App\Models\User;
use App\Models\Data_umkm;
use App\Models\history;
use Exception;
use Illuminate\Support\Facades\Storage;
use PhpParser\Node\Expr\FuncCall;

class CourierController extends Controller
{
    public function index()
    {
        $courId = Auth::id();
        $viewData = [
            'Title' => 'Dashboard Courier',
            'PengaturanAkun' => HomeModel::pengaturanAkun(),
            'SeputarDkampus' => HomeModel::seputarDkampus(),
            'cour_name' => optional(User::find($courId))->nama_user,
            'orders' => [],
            'custId' => [],
            'nama_penerima' => [],
            'no_telp_umkm' => [],
            'no_telp_cust' => [],
        ];

        try {
            $database = app('firebase.database');
            $onProgress = $database->getReference('onProgress/');

            if (! $onProgress->getSnapshot()->exists()) {
                return view('pages/Courier/dashboard', $viewData);
            }

            // Hanya membaca: ambil order milik kurir ini (key onProgress = "custId-courId")
            foreach ($onProgress->getChildKeys() as $key) {
                $parts = explode('-', $key);
                if (count($parts) < 2 || $parts[1] != $courId) {
                    continue;
                }

                $custId = $parts[0];
                $path = 'onProgress/' . $custId . '-' . $courId;
                $itemKeys = $database->getReference($path . '/orders')->getChildKeys();
                $umkmId = isset($itemKeys[0])
                    ? $database->getReference($path . '/orders/' . $itemKeys[0] . '/umkm_id')->getValue()
                    : null;

                $viewData['orders'][] = $database->getReference($path)->getValue();
                $viewData['custId'][] = $custId;
                $viewData['nama_penerima'][] = $database->getReference($path . '/nama_penerima')->getValue();
                $viewData['no_telp_umkm'][] = ($umkmId === null || $umkmId == 'Jastip')
                    ? '-'
                    : (optional(Data_umkm::find($umkmId))->no_telp_umkm ?? '-');
                // Satu nomor per order (sebelumnya semua order memakai nomor pelanggan terakhir)
                $viewData['no_telp_cust'][] = optional(User::find($custId))->no_telp ?? '-';
            }
        } catch (Exception $e) {
            // Jangan redirect()->back(): dari dashboard itu bisa berputar tanpa akhir.
            Log::warning('Courier dashboard: gagal membaca data order', ['error' => $e->getMessage()]);
            $viewData['orders'] = [];
            session()->now('error2', 'Data pesanan belum bisa dimuat, coba lagi nanti.');
        }

        return view('pages/Courier/dashboard', $viewData);
    }

    public function listOrder()
    {
        try {
            return view('pages/Courier/orderspage', [
                'Title' => 'Order',
            ]);
        } catch (Exception $e) {
            return redirect()->back()->with('error2', 'Error');
        }
    }

    public function takeOrder()
    {
        try {
            $courId = Auth::user()->id;
            $id = request()->input('orderId');
            $database = app('firebase.database');
            // if ($database->getReference('onProgress')->getSnapshot()->exists() && $database->getReference('onProgress')->getSnapshot()->hasChildren()) {
            //     $refId = $database->getReference('onProgress')->getChildKeys();
            //     foreach ($refId as $ids) {
            //         $parts = explode("-", $ids);
            //         $custId = $parts[0];
            //         $refOP = $database->getReference('onProgress/' . $custId . '-' . $courId)->getSnapshot()->exists();
            //         if (!$refOP) {
            //             $data = $database->getReference('needToDeliver/' . $id)->getValue();
            //             $database->getReference('onProgress/' . $id . $courId)->set($data);
            //             $database->getReference('onProgress/' . $id . $courId . '/status')->set('on Delivery');
            //             date_default_timezone_set('Asia/Jakarta');
            //             $timestamp = date('Y-m-d H:i:s');
            //             $custId = explode("-", $id);
            //             $cust_name = User::find($custId[0])->nama_user;
            //             $cour_name = User::find($courId)->nama_user;
            //             $postData = [
            //                 'msgs' => [
            //                     'msg' => "Apakah pesanan sudah sesuai",
            //                     'timestamp' => $timestamp,
            //                     'role' => "driver",
            //                 ],
            //             ];
            //             $database->getReference('chats/' . $id . $courId)->push()->set($postData);
            //             $database->getReference('chats/' . $id . $courId . '/cust_name')->set($cust_name);
            //             $database->getReference('chats/' . $id . $courId . '/cour_name')->set($cour_name);
            //             $database->getReference('chats/' . $id . $courId . '/courNewMssg')->set(1);
            //             $database->getReference('chats/' . $id . $courId . '/custNewMssg')->set(0);
            //             $database->getReference('needToDeliver/' . $id)->remove();
            //             return redirect('courier/dashboard');
            //         } else {
            //             return redirect()->back()->with('error2', 'Anda Masih Memiliki Pesanan Yang Belum Selesai');
            //         }
            //     }
            // } else {
            $data = $database->getReference('needToDeliver/' . $id)->getValue();
            $database->getReference('onProgress/' . $id . $courId)->set($data);
            $database->getReference('onProgress/' . $id . $courId . '/status')->set('on Delivery');
            date_default_timezone_set('Asia/Jakarta');
            $timestamp = date('Y-m-d H:i:s');
            $custId = explode("-", $id);
            $cust_name = User::find($custId[0])->nama_user;
            $cour_name = User::find($courId)->nama_user;
            $postData = [
                'msgs' => [
                    'msg' => "Apakah pesanan sudah sesuai",
                    'timestamp' => $timestamp,
                    'role' => "driver",
                ],
            ];
            $database->getReference('chats/' . $id . $courId)->push()->set($postData);
            $database->getReference('chats/' . $id . $courId . '/cust_name')->set($cust_name);
            $database->getReference('chats/' . $id . $courId . '/cour_name')->set($cour_name);
            $database->getReference('chats/' . $id . $courId . '/courNewMssg')->set(1);
            $database->getReference('chats/' . $id . $courId . '/custNewMssg')->set(0);
            $database->getReference('needToDeliver/' . $id)->remove();
            return redirect('courier/dashboard');
            // }
        } catch (Exception $e) {
            return redirect()->back()->with('error2', 'Error');
        }
    }

    public function listChat()
    {
        try {
            $currentUserId = Auth::user()->id;
            return view('pages/Courier/chatpage', [
                'Title' => 'list-chat',
                'courId' => $currentUserId,
            ]);
        } catch (Exception $e) {
            return redirect()->back()->with('error2', 'Error');
        }
    }

    public function roomChat(Request $request)
    {
        try {
            $courId = Auth::user()->id;
            $custId = $request->input('custId');
            $database = app('firebase.database');
            $cust_name = $database->getReference('chats/' . $custId . '-' . $courId . '/cust_name')->getValue();
            $refKey = $database->getReference('chats/' . $custId . '-' . $courId)->getChildKeys();
            $date = null;
            foreach ($refKey as $key) {
                $date = $database->getReference('chats/' . $custId . '-' . $courId . '/' . $key . '/msgs/timestamp')->getValue();
                if ($date != null) {
                    break;
                }
            }
            $wa = (string) optional(User::find($custId))->no_telp;
            if (substr($wa, 0, 2) === '08') {
                $convertedNumber = '+62' . substr($wa, 1);
            } else {
                $convertedNumber = 'Invalid phone number format';
            }
            return view('pages/Courier/chatroom', [
                'Title' => 'room-chat',
                'custId' => $custId,
                'courId' => $courId,
                'date' => $date,
                'cust_name' => $cust_name,
                'wa' => $convertedNumber,
            ]);
        } catch (Exception $e) {
            return redirect()->back()->with('error2', 'Error');
        }
    }

    public function uploadChatImageCour(Request $request)
    {
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('public/chatsImg');
            $url = Storage::url($path);

            return response()->json(['success' => true, 'imageUrl' => $url]);
        }

        return response()->json(['success' => false, 'error' => 'No image uploaded']);
    }

    public function completeOrder(Request $request)
    {
        try {
            $courId = Auth::user()->id;
            $custId = $request->input('custId');
            $database = app('firebase.database');

            $check = $request->validate([
                'bukti' => 'required|file|mimes:jpeg,jpg,png,heic|max:4096',
            ]);

            if ($check) {
                $file = $request->file('bukti')->store('/public/payment/driver');
                $fileName = basename($file);

                $harga = $database->getReference('onProgress/' . $custId . '-' . $courId . '/total')->getValue();
                $ongkir = $database->getReference('onProgress/' . $custId . '-' . $courId . '/ongkir')->getValue();
                $item = $database->getReference('onProgress/' . $custId . '-' . $courId . '/orders')->getValue();
                $orderId = $database->getReference('onProgress/' . $custId . '-' . $courId . '/orderID')->getValue();
                // $bukti = $database->getReference('onProgress/' . $custId . '-' . $courId . '/bukti')->getValue();
                $jarak = $database->getReference('onProgress/' . $custId . '-' . $courId . '/jarak')->getValue();
                $kategori = $database->getReference('onProgress/' . $custId . '-' . $courId . '/kategori')->getValue();
                // dd($item);
                $itemNum = $database->getReference('onProgress/' . $custId . '-' . $courId . '/orders')->getChildKeys();
                $umkmId = $database->getReference('onProgress/' . $custId . '-' . $courId . '/orders' . '/' . $itemNum[0] . '/umkm_id')->getValue();
                $namaJumlahArray = [];
                foreach ($item as $order) {
                    $namaJumlahArray[] = $order['jumlah'] . ' ' . $order['nama'] . ' (' . $order['catatan'] . ')';
                }

                $joinedNamaJumlah = implode(', ', $namaJumlahArray);
                history::create([
                    'user_id' => $custId,
                    'cour_id' => $courId,
                    'umkm_id' => $umkmId,
                    'item' => $joinedNamaJumlah,
                    'harga' => $harga,
                    'ongkir' => $ongkir,
                    'jarak' => $jarak,
                    'status' => 'completed',
                    'bukti' => null,
                    'bukti_akhir' => $fileName,
                    'alasan' => null,
                    'total_driver' => $request->input('total-price'),
                    'kategori' => $kategori,
                    'order_id' => $orderId,
                ]);

                $database->getReference('onProgress/' . $custId . '-' . $courId)->remove();
                return redirect()->back();
            } else {
                return redirect()->back()->with('error2', 'File Bukti Tidak Valid');
            }
        } catch (Exception $e) {
            // dd($e);
            return redirect()->back()->with('error2', 'Gagal Menyelesaikan Order');
        }
    }

    public function cancelOrder(Request $request)
    {
        try {
            $courId = Auth::user()->id;
            $custId = $request->input('custId');
            $database = app('firebase.database');

            $check = $request->validate([
                'bukti_batal' => 'required|file|mimes:jpeg,jpg,png,heic|max:4096',
            ]);

            if ($check) {
                $file = $request->file('bukti_batal')->store('/public/payment/driver');
                $fileName = basename($file);
                $orderId = $database->getReference('onProgress/' . $custId . '-' . $courId . '/orderID')->getValue();
                $harga = $database->getReference('onProgress/' . $custId . '-' . $courId . '/total')->getValue();
                $ongkir = $database->getReference('onProgress/' . $custId . '-' . $courId . '/ongkir')->getValue();
                $jarak = $database->getReference('onProgress/' . $custId . '-' . $courId . '/jarak')->getValue();
                $item = $database->getReference('onProgress/' . $custId . '-' . $courId . '/orders')->getValue();
                // $bukti = $database->getReference('onProgress/' . $custId . '-' . $courId . '/bukti')->getValue();
                $itemNum = $database->getReference('onProgress/' . $custId . '-' . $courId . '/orders')->getChildKeys();
                $kategori = $database->getReference('onProgress/' . $custId . '-' . $courId . '/kategori')->getValue();
                $umkmId = $database->getReference('onProgress/' . $custId . '-' . $courId . '/orders' . '/' . $itemNum[0] . '/umkm_id')->getValue();
                $namaJumlahArray = [];
                foreach ($item as $order) {
                    $namaJumlahArray[] = $order['jumlah'] . ' ' . $order['nama'] . ' (' . $order['catatan'] . ')';
                }

                $joinedNamaJumlah = implode(', ', $namaJumlahArray);
                history::create([
                    'user_id' => $custId,
                    'cour_id' => $courId,
                    'umkm_id' => $umkmId,
                    'item' => $joinedNamaJumlah,
                    'harga' => $harga,
                    'ongkir' => $ongkir,
                    'jarak' => $jarak,
                    'status' => 'canceled',
                    'bukti' => null,
                    'bukti_akhir' => $fileName,
                    'alasan' => $request->input('alasan'),
                    'total_driver' => $request->input('total-price-cancel'),
                    'kategori' => $kategori,
                    'order_id' => $orderId,
                ]);
                $database->getReference('onProgress/' . $custId . '-' . $courId)->remove();
                return redirect()->back()->with('success', 'Berhasil Membatalkan Orderan');
            }
        } catch (Exception $e) {
            // dd($e);
            return redirect()->back()->with('error2', 'gagal menghapus data');
        }
    }

    public function history()
    {
        $data = Auth::user()->courHistory()->orderByDesc('created_at')->get();
        $namaUmkm = Data_umkm::whereIn('id', $data->pluck('umkm_id')->filter()->unique())->pluck('nama_umkm', 'id');

        return view('pages/Courier/riwayat', [
            'Title' => 'History',
            'data' => $data,
            'nama_umkm' => $namaUmkm,
        ]);
    }

    public function detailHistory(Request $request)
    {
        $history = history::where('id', $request->id)
            ->where('cour_id', Auth::id())
            ->where('user_id', $request->custId)
            ->first();

        abort_if($history === null, 404);

        $namaUmkm = $history->umkm_id == 0
            ? 'Jastip'
            : (optional(Data_umkm::find($history->umkm_id))->nama_umkm ?? 'UMKM tidak tersedia');

        return view('pages/Courier/riwayatdetail', [
            'Title' => 'Detail history' . ' | #TRX' . strtoupper(substr($history->order_id, 0, 10)),
            'cust_name' => optional(User::find($history->user_id))->nama_user ?? 'Pelanggan tidak ditemukan',
            'status' => $history->status,
            'custId' => $history->user_id,
            'umkm' => $namaUmkm,
            'id' => $history->order_id,
            'total' => $history->harga,
            'ongkir' => $history->ongkir,
            'created_at' => $history->created_at,
        ]);
    }

    public function waCour(Request $request)
    {
        $custId = $request->input('custId');
        $customer = User::find($custId);
        abort_if($customer === null, 404);
        $wa = (string) $customer->no_telp;
        if (substr($wa, 0, 2) === '08') {
            $convertedNumber = '+62' . substr($wa, 1);
        } else {
            $convertedNumber = 'Invalid phone number format';
        }
        return redirect()->away('https://wa.me/' . $convertedNumber);
    }

    public function updatePhone(Request $request)
    {
        $validated = $request->validateWithBag('updatePhone', [
            'no_telp' => ['required', 'string', 'max:13', Rule::unique('users', 'no_telp')->ignore(Auth::id())],
        ]);

        Auth::user()->update(['no_telp' => $validated['no_telp']]);

        return back()->with('status', 'phone-updated');
    }
}
