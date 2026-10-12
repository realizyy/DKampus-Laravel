<?php

namespace App\Support;

use App\Models\Data_umkm;
use App\Models\Menu;
use App\Models\User;
use App\Models\history;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;

/**
 * Data contoh untuk pratinjau lokal (/_ui/*): menampilkan halaman tanpa menyentuh Firebase/database nyata.
 * Hanya dipakai oleh route yang terdaftar bila app()->isLocal().
 */
class DevFixtures
{
    public static function courierUser(): User
    {
        return new User(['nama_user' => 'Yanto Joseph', 'email' => 'yantojoseph@example.test', 'no_telp' => '081234567013', 'role' => 'courier']);
    }

    /** @return array<int, array<string, mixed>> */
    public static function courierOrders(): array
    {
        return [
            [
                'nama_umkm' => 'Raja Seafood - Sukabirus',
                'umkm_link_address' => 'https://maps.google.com/?q=Sukabirus',
                'umkm_address' => 'Jl. Sukabirus No. 22',
                'nama_penerima' => 'Kevin',
                'cust_link_address' => 'https://maps.google.com/?q=Pondok+Eden',
                'cust_address' => 'Kost Pondok Eden',
                'orders' => [
                    ['jumlah' => 1, 'nama' => 'Kepiting Goreng', 'catatan' => '-'],
                    ['jumlah' => 2, 'nama' => 'Lemon Squash', 'catatan' => 'Es dipisah'],
                ],
                'total' => 80600,
                'ongkir' => 3000,
            ],
            [
                'nama_umkm' => 'Mie Ahong - Sukabirus',
                'umkm_link_address' => 'https://maps.google.com/?q=Mie+Ahong',
                'umkm_address' => 'Jl. Sukabirus No. 31',
                'nama_penerima' => 'Dinda',
                'cust_link_address' => 'https://maps.google.com/?q=Asrama',
                'cust_address' => 'Asrama Putri Blok C',
                'orders' => [['jumlah' => 1, 'nama' => 'Mie Chilli Oil', 'catatan' => 'Pedas level 2']],
                'total' => 24000,
                'ongkir' => 4000,
            ],
        ];
    }

    /** Data untuk Courier/dashboard. */
    public static function courierDashboard(bool $empty = false): array
    {
        $orders = $empty ? [] : self::courierOrders();

        return [
            'Title' => 'Dashboard Courier',
            'navActive' => 'dashboardCourier',
            'orders' => $orders,
            'custId' => $empty ? [] : [101, 102],
            'nama_penerima' => array_column($orders, 'nama_penerima'),
            'no_telp_umkm' => $empty ? [] : ['081222106092', '081222106093'],
            'no_telp_cust' => $empty ? [] : ['081298765432', '081377788899'],
        ];
    }

    /** Order "needToDeliver" untuk Courier/orderspage. */
    public static function previewOrders(): array
    {
        $base = self::courierOrders();

        return [
            ['id' => '-Nxa1', 'data' => $base[0] + ['ongkir' => 4000, 'jarak' => 1200, 'timestamp' => '12 Juni 2024 19:04', 'notesAlamat' => 'Kamar 12, lantai 2']],
            ['id' => '-Nxa2', 'data' => $base[1] + ['jarak' => 650, 'timestamp' => '12 Juni 2024 19:11', 'notesAlamat' => '-']],
        ];
    }

    /** Beberapa riwayat (objek history belum tersimpan). */
    public static function courierHistory(): array
    {
        $rows = [
            ['id' => 1, 'user_id' => 101, 'umkm_id' => 1, 'item' => '1 Kepiting Goreng', 'order_id' => 'tr00000007895675', 'status' => 'completed', 'harga' => 80600, 'ongkir' => 3000, 'created_at' => '2024-06-12 19:04:00'],
            ['id' => 2, 'user_id' => 102, 'umkm_id' => 2, 'item' => '1 Mie Chilli Oil', 'order_id' => 'tr00000007895674', 'status' => 'completed', 'harga' => 24000, 'ongkir' => 3000, 'created_at' => '2024-06-11 14:10:00'],
            ['id' => 3, 'user_id' => 103, 'umkm_id' => 0, 'item' => 'Titip beli galon & roti', 'order_id' => 'tr00000007895673', 'status' => 'canceled', 'harga' => 31000, 'ongkir' => 5000, 'created_at' => '2024-06-11 09:30:00'],
        ];

        return collect($rows)->map(function ($r) {
            $h = new history($r);
            $h->id = $r['id'];
            $h->created_at = \Illuminate\Support\Carbon::parse($r['created_at']);

            return $h;
        })->all();
    }

    /** Daftar percakapan untuk Courier/chatpage. */
    public static function previewChats(): array
    {
        return [
            ['custId' => '101', 'name' => 'Kevin Pratama', 'message' => 'Sudah ya, makasih kak', 'timestamp' => now()->format('Y-m-d H:i:s')],
            ['custId' => '102', 'name' => 'Dinda Ayu', 'message' => 'qrcode-payment.png', 'timestamp' => now()->subDay()->format('Y-m-d H:i:s')],
            ['custId' => '103', 'name' => 'Rizky', 'message' => 'Apakah pesanan sudah sesuai?', 'timestamp' => now()->subDays(4)->format('Y-m-d H:i:s')],
        ];
    }

    /** Pesan untuk Courier/chatroom. */
    public static function previewMessages(): array
    {
        $today = now()->format('Y-m-d');

        return [
            ['role' => 'driver', 'msg' => 'Halo kak, pesanannya sedang saya antar.', 'timestamp' => "$today 19:04:00"],
            ['role' => 'driver', 'msg' => 'Ini kode QR untuk pembayaran', 'timestamp' => "$today 19:04:30"],
            ['role' => 'driver', 'msg' => '/qrcode-payment.png', 'timestamp' => "$today 19:04:40"],
            ['role' => 'customer', 'msg' => 'Sudah ya', 'timestamp' => "$today 19:06:00"],
            ['role' => 'driver', 'msg' => 'Apakah pesanan sudah sesuai?', 'timestamp' => "$today 19:08:00"],
        ];
    }

    public static function customerUser(): User
    {
        $user = new User(['nama_user' => 'Kevin Pratama', 'email' => 'kevin@example.test', 'no_telp' => '081298765432', 'role' => 'customer']);
        $user->id = 101;
        $user->setRelation('addresses', collect([]));

        return $user;
    }

    /** Isi keranjang (Firebase cart/{uid}/orders) untuk Users/Pesanan. */
    public static function cartItems(): array
    {
        return [
            'item1' => ['id' => 1, 'nama' => 'Mie Ayam', 'harga' => 20000, 'umkm_id' => 1, 'jumlah' => 2, 'catatan' => 'Pedas level 2'],
            'item2' => ['id' => 2, 'nama' => 'Bakmie Ayam', 'harga' => 30000, 'umkm_id' => 1, 'jumlah' => 1, 'catatan' => '-'],
        ];
    }

    public static function addresses(): array
    {
        return [
            ['id' => 1, 'nama_alamat' => 'Pondok Eden', 'address' => 'Jl. H. Umar, Citeureup, Dayeuhkolot, Bandung'],
            ['id' => 2, 'nama_alamat' => 'Kampus Fakultas Teknik', 'address' => 'Jl. Telekomunikasi No. 1, Bandung'],
        ];
    }

    /** Riwayat pesanan customer (objek history belum tersimpan). */
    public static function customerHistory(): array
    {
        $rows = [
            ['id' => 11, 'user_id' => 101, 'cour_id' => 7, 'umkm_id' => 1, 'item' => '1 Mie Ayam, 1 Bakmie Ayam', 'order_id' => 'tr00000007895675', 'status' => 'completed', 'harga' => 50000, 'ongkir' => 3000, 'created_at' => '2024-06-12 19:04:00'],
            ['id' => 12, 'user_id' => 101, 'cour_id' => 7, 'umkm_id' => 0, 'item' => 'Titip beli galon', 'order_id' => 'tr00000007895674', 'status' => 'canceled', 'harga' => 21000, 'ongkir' => 5000, 'created_at' => '2024-06-10 09:30:00'],
        ];

        return collect($rows)->map(function ($r) {
            $h = new history($r);
            $h->id = $r['id'];
            $h->created_at = \Illuminate\Support\Carbon::parse($r['created_at']);

            return $h;
        })->all();
    }

    /** Pesanan aktif (Firebase) untuk tab Proses. */
    public static function liveOrders(): array
    {
        return [
            ['umkm' => 'Bakmi Jogja Piyaman, Karawaci', 'date' => '12 Juni 2024 19:04', 'status' => 'On Delivery', 'items' => '2 Mie Ayam, 1 Bakmie Ayam', 'orderId' => 'tr00000008', 'total' => 74000, 'courId' => '7'],
            ['umkm' => 'Mie Ahong - Sukabirus', 'date' => '12 Juni 2024 19:11', 'status' => 'Searching', 'items' => '1 Mie Chilli Oil', 'orderId' => 'tr00000009', 'total' => 22000, 'courId' => ''],
        ];
    }

    // ---- Admin -------------------------------------------------------------------------

    public static function adminUser(): User
    {
        $user = new User(['nama_user' => 'Rina Pratama', 'email' => 'rina@example.test', 'no_telp' => '081234567890', 'role' => 'admin']);
        $user->id = 1;

        return $user;
    }

    /** Daftar akun contoh (objek User belum tersimpan). */
    public static function adminUsers(): \Illuminate\Support\Collection
    {
        $rows = [
            [1, 'Rina Pratama', 'rina@example.test', '081234567890', 'admin', 0, '2023-08-14'],
            [2, 'Abdul Aziz', 'abdulaziz@gmail.com', '089659660111', 'customer', 0, '2023-12-02'],
            [3, 'Abdul Somad', 'abdulsomad@gmail.com', '089659660222', 'courier', 0, '2023-12-05'],
            [4, 'Aldi Simbolon', 'alditaher@gmail.com', '089659660333', 'customer', 0, '2024-01-11'],
            [5, 'Bayu Adji', 'bayuadji@gmail.com', '089659660444', 'customer', 0, '2024-01-19'],
            [6, 'Benjamin Wong', 'benjaminwong@gmail.com', '089659660240', 'customer', 1, '2023-12-10'],
            [7, 'Calvin Lim', 'calvinlim@gmail.com', '089659660555', 'courier', 1, '2024-02-03'],
            [8, 'Charlie Saputra', 'charliesaputra@gmail.com', '089659660666', 'customer', 0, '2024-02-21'],
            [9, 'Dahlan Mansur', 'dahlanmansur@gmail.com', '089659660777', 'courier', 0, '2024-03-08'],
        ];

        return collect($rows)->map(function ($r) {
            $u = new User(['nama_user' => $r[1], 'email' => $r[2], 'no_telp' => $r[3], 'role' => $r[4]]);
            $u->id = $r[0];
            $u->restriction = $r[5];
            $u->created_at = Carbon::parse($r[6]);

            return $u;
        });
    }

    /** @return array<int, array{0: string, 1: string, 2: string, 3: int}> nama, alamat, jam buka-tutup, jumlah produk */
    private static function umkmRows(): array
    {
        return [
            ['Ayam Bude - Sukabirus', 'Jl. Sukabirus No. 11', '09:00', '21:30', 6, 1],
            ['Ayam Bakar Surabaya - Sukapura', 'Jl. Sukapura No. 26', '10:00', '22:00', 4, 0],
            ['Ayam Cak Lontong - Sukabirus', 'Jl. Sukabirus No. 31', '10:00', '21:00', 5, 0],
            ['Ayam Bakar Maknyos - Sukabirus', 'Jl. Sukabirus No. 56', '08:30', '22:00', 3, 1],
            ['Raja Seafood - Sukabirus', 'Jl. Sukabirus No. 22', '11:00', '23:00', 8, 0],
            ['Mie Ahong - Sukabirus', 'Jl. Sukabirus No. 8', '09:30', '20:30', 5, 0],
            ['Kedai Teh Poci', 'Jl. Telekomunikasi No. 3', '07:00', '19:00', 7, 0],
            ['Roti Bakar Pak Mamat', 'Jl. Radio Palasari No. 14', '16:00', '23:30', 4, 0],
        ];
    }

    /** Daftar resto sebagai paginator (objek Data_umkm belum tersimpan). */
    public static function adminUmkms(): LengthAwarePaginator
    {
        $items = collect(self::umkmRows())->map(function ($r, $i) {
            $u = new Data_umkm(['nama_umkm' => $r[0], 'alamat' => $r[1], 'logo_umkm' => 'tidak-ada.jpg', 'no_telp_umkm' => '08123456789'.$i, 'vip' => $r[5], 'rating' => 4.0 + ($i % 5) / 10]);
            $u->id = $i + 1;
            $u->open_time = '2024-01-01 '.$r[2].':00';
            $u->close_time = '2024-01-01 '.$r[3].':00';
            $u->setAttribute('menu_count', $r[4]);

            return $u;
        });

        return new LengthAwarePaginator($items, $items->count(), 10, 1, ['path' => url('/_ui/admin/umkm')]);
    }

    public static function adminUmkm(): Data_umkm
    {
        $u = self::adminUmkms()->first();
        $u->link = 'https://maps.google.com/?q=Sukabirus';
        $u->geo = '-6.9738,107.6301';
        $u->created_at = Carbon::parse('2023-12-10');

        return $u;
    }

    /** Produk contoh untuk satu resto (objek Menu belum tersimpan). */
    public static function adminMenus(): \Illuminate\Support\Collection
    {
        $rows = [
            [1, 'Ayam Bakar Signature', 'ayam & bebek', 18000, 30],
            [2, 'Es Teh Signature', 'minuman', 5000, 0],
            [3, 'Nasi Liwet Komplit', 'aneka nasi', 22000, 0],
            [4, 'Ayam Geprek Level 5', 'ayam & bebek', 16000, 10],
        ];

        return collect($rows)->map(function ($r) {
            $m = new Menu(['nama_makanan' => $r[1], 'deskripsi' => 'Menu andalan resto', 'image' => 'tidak-ada.jpg', 'harga' => $r[3], 'diskon' => $r[4], 'category' => $r[2], 'data_umkm_id' => 1]);
            $m->id = $r[0];
            $m->setRelation('data_umkm', self::adminUmkm());

            return $m;
        });
    }

    public static function adminMenusPaginated(): LengthAwarePaginator
    {
        $menus = self::adminMenus();

        return new LengthAwarePaginator($menus, $menus->count(), 10, 1, ['path' => url('/_ui/admin/product')]);
    }

    /** Riwayat transaksi contoh (objek history belum tersimpan, relasi diisi manual). */
    public static function adminHistory(): \Illuminate\Support\Collection
    {
        $users = self::adminUsers();
        $rows = [
            [21, 2, 3, 'tr00000007895675', 'completed', 50000, 3000, 1200, '2024-06-12 19:04:00'],
            [22, 4, 9, 'tr00000007895674', 'canceled', 21000, 5000, 2600, '2024-06-10 09:30:00'],
            [23, 5, 3, 'tr00000007895673', 'completed', 38000, 3000, 900, '2024-06-09 12:15:00'],
        ];

        return collect($rows)->map(function ($r) use ($users) {
            $h = new history(['user_id' => $r[1], 'cour_id' => $r[2], 'umkm_id' => 0, 'item' => '[]', 'order_id' => $r[3], 'status' => $r[4], 'harga' => $r[5], 'ongkir' => $r[6], 'jarak' => $r[7]]);
            $h->id = $r[0];
            $h->created_at = Carbon::parse($r[8]);
            $h->setRelation('customer', $users->firstWhere('id', $r[1]));
            $h->setRelation('courier', $users->firstWhere('id', $r[2]));

            return $h;
        });
    }

    /** Percakapan Customer Service contoh untuk Admin/chatpage (kunci = id customer). */
    public static function adminChats(): array
    {
        $today = now()->format('Y-m-d');
        $yesterday = now()->subDay()->format('Y-m-d');

        return [
            '101' => ['name' => 'Kevin Pratama', 'messages' => [
                ['role' => 'customer', 'msg' => 'Halo kak, pesanan saya belum sampai', 'timestamp' => "$today 10:02:00"],
                ['role' => 'admin', 'msg' => 'Halo Kevin, kami cek dulu ke driver ya.', 'timestamp' => "$today 10:04:00"],
                ['role' => 'customer', 'msg' => 'Baik kak, terima kasih', 'timestamp' => "$today 10:05:00"],
            ]],
            '102' => ['name' => 'Dinda Ayu', 'messages' => [
                ['role' => 'customer', 'msg' => 'Bisa ganti alamat pengantaran?', 'timestamp' => "$yesterday 16:40:00"],
                ['role' => 'admin', 'msg' => 'Bisa kak, silakan kirim alamat barunya.', 'timestamp' => "$yesterday 16:44:00"],
            ]],
            '103' => ['name' => 'Rizky', 'messages' => [
                ['role' => 'customer', 'msg' => 'Terima kasih banyak!', 'timestamp' => now()->subDays(4)->format('Y-m-d').' 20:10:00'],
            ]],
        ];
    }
}
