<?php

namespace App\Support;

use App\Models\history;
use App\Models\User;

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
}
