<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Menu;
use App\Models\Data_umkm;
use App\Models\User;
use App\Models\history;
use App\Models\Addresse;
use App\Models\EmailOtp;
use App\Models\Favorit;
use App\Models\Rating;
use Illuminate\Support\Facades\DB;
use Exception;

class AdminController extends Controller
{
    /** Node Firebase berisi anak? Mengembalikan isinya (atau null bila kosong/tidak ada). */
    private function firebaseNode(string $path, bool $ordered = false): mixed
    {
        $reference = app('firebase.database')->getReference($path);
        if (! $reference->getSnapshot()->exists()) {
            return null;
        }

        return ($ordered ? $reference->orderByChild('timestamp') : $reference)->getValue();
    }

    public function index()
    {
        try {
            $database = app('firebase.database');
            $count = fn (string $path) => $database->getReference($path)->getSnapshot()->exists()
                ? $database->getReference($path)->getSnapshot()->numChildren()
                : null;

            return view('pages/Admin/dashboard', [
                'data_umkm' => Data_umkm::all(),
                'menu' => Menu::all(),
                'user' => User::all(),
                'transaction' => history::all(),
                'dataNTD' => $count('needToDeliver'),
                'dataOP' => $count('onProgress'),
            ]);
        } catch (Exception $e) {
            report($e);

            return redirect()->back()->with('error', 'error occured');
        }
    }

    public function transacation()
    {
        try {
            $data = history::with(['customer', 'courier'])->get();

            return view('pages.Admin.transaction', [
                'Title' => 'Transactions',
                'datas' => $data->isNotEmpty() ? $data : null,
                'dataNTD' => $this->firebaseNode('needToDeliver', true),
                'dataOP' => $this->firebaseNode('onProgress', true),
            ]);
        } catch (Exception $e) {
            // Dulu dd($e): membocorkan detail galat ke layar. Sekarang dicatat dan admin diberi tahu.
            report($e);

            return redirect()->route('dashboard')->with('error2', 'Data transaksi belum bisa dimuat. Coba lagi nanti.');
        }
    }

    public function convertXls()
    {
        $data = DB::table('histories')->get();
        return response()->json($data);
    }

    public function spreadsheets()
    {
        return redirect("https://docs.google.com/spreadsheets/d/1_NFV2Ih7UnwU4_dyidhky2tlBaQeW7aSQOMf494hckY/edit?usp=sharing");
    }

    public function getUserDetails(Request $request)
    {
        // Validate that user_ids parameter is provided
        $request->validate([
            'user_ids' => 'required|string'
        ]);

        // Convert the comma-separated list of user_ids into an array
        $userIds = explode(',', $request->input('user_ids'));

        // Fetch users based on provided user IDs
        $users = User::whereIn('id', $userIds)->get(['id', 'nama_user', 'no_telp']);

        return response()->json($users);
    }

    /** Daftar akun (disaring di sisi klien: nama, role, status). */
    public function accounts()
    {
        return view('pages.Admin.account', [
            'users' => User::orderBy('nama_user')->get(),
        ]);
    }

    public function editUser(Request $request)
    {
        $id = $request->id_user;

        $request->validate([
            'role' => 'required|in:admin,customer,courier',
            'restriction' => 'required|boolean',
        ]);

        $user = User::findOrFail($id);

        // Admin tidak boleh mengunci dirinya sendiri atau menurunkan admin terakhir
        $losesAdmin = $user->role === 'admin' && $request->role !== 'admin';
        if ($user->is($request->user()) && ($losesAdmin || $request->restriction)) {
            return redirect()->back()->with('error2', 'Anda tidak bisa menurunkan atau menonaktifkan akun yang sedang dipakai.');
        }
        if ($losesAdmin && User::where('role', 'admin')->count() <= 1) {
            return redirect()->back()->with('error2', 'Admin terakhir tidak bisa diturunkan rolenya.');
        }

        $user->role = $request->role;
        $user->restriction = $request->restriction;

        $user->save();

        return redirect()->back()->with('success', 'Akun '.$user->nama_user.' diperbarui.');
    }

    /** Hapus akun (Figma: Hapus Akun). Riwayat transaksi harus tetap utuh, jadi akun yang pernah bertransaksi hanya bisa dinonaktifkan. */
    public function destroyUser(Request $request, User $user)
    {
        if ($user->is($request->user())) {
            return redirect()->back()->with('error2', 'Anda tidak bisa menghapus akun yang sedang dipakai.');
        }
        if ($user->role === 'admin' && User::where('role', 'admin')->count() <= 1) {
            return redirect()->back()->with('error2', 'Admin terakhir tidak bisa dihapus.');
        }
        if (history::where('user_id', $user->id)->orWhere('cour_id', $user->id)->exists()) {
            return redirect()->back()->with('error2', 'Akun '.$user->nama_user.' punya riwayat transaksi. Nonaktifkan saja supaya riwayatnya tetap utuh.');
        }

        DB::transaction(function () use ($user) {
            Addresse::where('user_id', $user->id)->delete();
            Favorit::where('user_id', $user->id)->delete();
            Rating::where('user_id', $user->id)->delete();
            EmailOtp::where('email', $user->email)->delete();
            $user->delete();
        });

        return redirect()->route('account')->with('success', 'Akun '.$user->nama_user.' dihapus.');
    }
}
