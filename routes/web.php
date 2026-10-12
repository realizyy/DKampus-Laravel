<?php

use App\Http\Controllers\AdminController;
use App\Models\Addresse;
use App\Models\Menu;
use App\Models\User;
use App\Models\Footer;
use App\Models\Data_umkm;
use App\Models\HomeModel;
use App\Models\PromoModel;
use App\Models\DetailWarungModel;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\CourierController;
use App\Http\Controllers\FavoritController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\UmkmController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UntukKamuController;
use App\Http\Controllers\CsController;
use App\Models\Chat;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Index Page

// Route::get('/', [UntukKamuController::class, 'UntukKamu']);

// Settings Routes
// todo here .... ? - daftar alamat, ubah kata sandi, bersihkan cache, tentang kami, syarat dan ketentuan, beri kami ulasan

// Category Menu Page
Route::get('/kategori/{value}', function ($value) {
    return view('pages.Users.KategoriMenu', [
        'Title' => 'Kategori ' . $value,
        'Kategori' => $value,
        'menus' => Menu::where('category', $value)->get(),
    ]);
});

Route::get('/sheetsData', [AdminController::class, 'convertXls']);

// Ubah Kata Sandi
Route::get('/change-password', function () {
    return view('pages.Users.UbahKataSandi', [
        'Title' => 'Ubah Kata Sandi',
    ]);
})->middleware('auth');

// Rekomendasi Warung page
Route::get('/rekomendasi-warung', function () {
    return view('pages.Users.RekomendasiWarungPage', [
        'Title' => 'Rekomendasi Warung',
        'RekomendasiWarung' => Data_umkm::all(),
    ]);
});

// Rekomendasi Menu page
Route::get('/rekomendasi-menu', function () {
    return view('pages.Users.RekomendasiMenuPage', [
        'Title' => 'Rekomendasi Menu',
        'RekomendasiMenu' => Menu::with('data_umkm')->get(),
    ]);
});

// Halaman Promo: satu tampilan untuk /promo, /promo/semua, /promo/special, dan /promo/category/{value}
$promoPage = function (string $title, ?string $category = null) {
    $menus = Menu::where('diskon', '>', 0)->with('data_umkm')->orderByDesc('diskon')->get();
    $categories = $menus->pluck('category')->filter()->unique()->values();
    if ($category !== null && $category !== '' && strtolower($category) !== 'semua') {
        $menus = $menus->filter(fn ($m) => strtolower((string) $m->category) === strtolower($category))->values();
    }

    return view('pages.Users.Promo', [
        'Title' => $title,
        'menus' => $menus,
        'umkms' => Data_umkm::whereIn('id', $menus->pluck('data_umkm_id')->unique())->get(),
        'categories' => $categories,
        'activeCategory' => $category,
    ]);
};
Route::get('/promo', fn () => $promoPage('Promo'));
Route::get('/promo/semua', fn () => $promoPage('Semua Promo'));
Route::get('/promo/special', fn () => $promoPage('Promo Special'));
Route::get('/promo/category/{value}', fn ($value) => $promoPage('Promo ' . ucfirst($value), $value));

// Halaman lama per kategori, sekarang dilayani oleh /promo/category/{value}
Route::redirect('/promo/makanan', '/promo/category/makanan');
Route::redirect('/promo/minuman', '/promo/category/minuman');
Route::redirect('/promo/cemilan', '/promo/category/cemilan');

// Galeri komponen UI (hanya lingkungan lokal)
if (app()->isLocal()) {
    Route::view('/_ui', 'dev.ui');

    // Pratinjau halaman auth dengan data contoh: /_ui/auth/{otp|otp-reset|reset-request|reset-new|success}
    Route::get('/_ui/auth/{page}', function (string $page) {
        return match ($page) {
            'otp' => view('auth.otp-verify', ['Title' => 'Verifikasi Akun', 'email' => 'contoh@gmail.com', 'purpose' => 'register', 'resendIn' => 59]),
            'otp-error' => view('auth.otp-verify', ['Title' => 'Verifikasi Akun', 'email' => 'contoh@gmail.com', 'purpose' => 'register', 'resendIn' => 0])->withErrors(['code' => 'Kode yang kamu masukkan salah.']),
            'reset-request' => view('auth.reset-request', ['Title' => 'Atur Ulang Kata Sandi']),
            'reset-new' => view('auth.reset-new', ['Title' => 'Kata Sandi Baru']),
            'success' => view('auth.success', ['Title' => 'Akun Berhasil Dibuat', 'heading' => 'Akun Berhasil Dibuat', 'message' => 'Selamat! Anda telah berhasil membuat akun baru. Ingat, jangan bagikan informasi akun ini kepada siapa pun, ya!', 'cta' => 'Masuk']),
            default => abort(404),
        };
    });

    // Pratinjau halaman customer dengan data contoh: /_ui/customer/{cart|checkout|pay|status|status-order|rating|chats|chat-room|jastip|alamat}
    Route::get('/_ui/customer/{page}', function (string $page) {
        $user = \App\Support\DevFixtures::customerUser();
        \Illuminate\Support\Facades\Auth::setUser($user);
        $addresses = \App\Support\DevFixtures::addresses();

        return match ($page) {
            'cart' => view('pages.Users.Pesanan', [
                'Title' => 'Pesanan', 'data' => request('empty') ? null : \App\Support\DevFixtures::cartItems(), 'namaUMKM' => 'Bakmi Jogja Piyaman, Karawaci',
                'AddressList' => $addresses, 'alamatUtama' => (object) ['id' => 1, 'nama_alamat' => 'Pondok Eden'],
            ]),
            'status' => view('pages.Users.Status', ['Title' => 'Status', 'custId' => 101, 'data' => request('empty') ? null : collect(\App\Support\DevFixtures::customerHistory()), 'previewLive' => request('empty') ? [] : \App\Support\DevFixtures::liveOrders()]),
            'status-order' => view('pages.Users.StatusOrder', ['Title' => 'Status Order', 'status' => 'On Delivery', 'date' => '12 Juni 2024 19:04', 'nama_driver' => 'Yanto Joseph', 'courId' => 7, 'nama_umkm' => 'Bakmi Jogja Piyaman, Karawaci', 'orderId' => 'tr00000008', 'subTotal' => 70000, 'ongkir' => 4000, 'orders' => \App\Support\DevFixtures::cartItems()]),
            'rating' => view('pages.Users.Rating', ['Title' => 'Rating', 'data' => (object) ['id' => 11], 'dataUMKM' => (object) ['nama_umkm' => 'Bakmi Jogja Piyaman, Karawaci', 'logo_umkm' => 'warmingUp.svg'], 'Orders' => [['quantity' => 2, 'name' => 'Mie'], ['quantity' => 1, 'name' => 'Bakmie']]]),
            'chats' => view('pages.Users.ChatPage', ['Title' => 'list-chat', 'custId' => 101, 'previewChats' => request('empty') ? [] : \App\Support\DevFixtures::previewChats()]),
            'chat-room' => view('pages.Users.ChatRoomPage', ['Title' => 'room-chat', 'custId' => 101, 'courId' => 7, 'date' => null, 'cour_name' => 'Yanto Joseph', 'wa' => '+6281234567013', 'previewMessages' => collect(\App\Support\DevFixtures::previewMessages())->map(fn ($m) => $m['role'] === 'driver' ? $m : $m)->all()]),
            'jastip' => view('pages.Users.JastipPage', ['Title' => 'Jastip', 'AddressList' => $addresses, 'alamatUtama' => (object) ['id' => 1, 'nama_alamat' => 'Pondok Eden']]),
            'pay-jastip' => view('pages.Users.payJastip', ['Title' => 'Pay', 'orderID' => 'tr00000007895676', 'carts' => ['item1' => ['jumlah' => 2, 'nama' => 'Galon', 'catatan' => 'Warung Bu Yati'], 'item2' => ['jumlah' => 1, 'nama' => 'Roti tawar', 'catatan' => 'Alfamart']], 'subtotal' => 40000, 'umkmCount' => 2, 'ongkir' => 6000, 'nama_umkm' => 'Jastip']),
            'alamat' => view('pages.Users.DaftarAlamat', ['Title' => 'Daftar Alamat', 'alamatUser' => collect([['id' => 1, 'user_id' => 101, 'nama_alamat' => 'Pondok Eden', 'address' => 'Jl. H. Umar, Citeureup, Dayeuhkolot, Bandung', 'link' => 'https://maps.google.com/?q=Pondok+Eden', 'utama' => 1, 'notes' => 'Gerbang hijau'], ['id' => 2, 'user_id' => 101, 'nama_alamat' => 'Kampus Fakultas Teknik', 'address' => 'Jl. Telekomunikasi No. 1, Bandung', 'link' => '', 'utama' => 0, 'notes' => null]])]),
            'checkout' => view('pages.Users.CheckoutPage', ['Title' => 'Checkout', 'carts' => \App\Support\DevFixtures::cartItems(), 'nama_umkm' => 'Bakmi Jogja Piyaman, Karawaci', 'total' => 70000, 'ongkir' => 4000]),
            'pay' => view('pages.Users.Pay', ['Title' => 'Pay', 'carts' => \App\Support\DevFixtures::cartItems(), 'nama_umkm' => 'Bakmi Jogja Piyaman, Karawaci', 'orderID' => 'tr00000007895675', 'total' => 74000]),
            default => abort(404),
        };
    });

    // Pratinjau halaman dengan data contoh (tanpa Firebase/database): /_ui/courier/{dashboard|orders|history|history-detail|profile|chats|chat-room}
    Route::get('/_ui/courier/{page}', function (string $page) {
        \Illuminate\Support\Facades\Auth::setUser(\App\Support\DevFixtures::courierUser());

        return match ($page) {
            'dashboard' => view('pages.Courier.dashboard', \App\Support\DevFixtures::courierDashboard((bool) request('empty'))),
            'orders' => view('pages.Courier.orderspage', ['Title' => 'Daftar Orderan', 'navActive' => 'courierorder', 'previewOrders' => request('empty') ? [] : \App\Support\DevFixtures::previewOrders()]),
            'history' => view('pages.Courier.riwayat', ['Title' => 'History', 'navActive' => 'cour.history', 'data' => collect(request('empty') ? [] : \App\Support\DevFixtures::courierHistory()), 'nama_umkm' => [1 => 'Raja Seafood - Sukabirus', 2 => 'Mie Ahong - Sukabirus']]),
            'history-detail' => view('pages.Courier.riwayatdetail', ['Title' => 'Detail history', 'navActive' => 'cour.history', 'cust_name' => 'Kevin Pratama', 'status' => 'completed', 'custId' => 101, 'umkm' => 'Raja Seafood - Sukabirus', 'id' => 'tr00000007895675', 'total' => 80600, 'ongkir' => 3000, 'created_at' => \Illuminate\Support\Carbon::parse('2024-06-12 19:04:00')]),
            'profile' => view('pages.Courier.profile', ['Title' => 'Profile', 'navActive' => 'profile']),
            'chats' => view('pages.Courier.chatpage', ['Title' => 'Chat', 'navActive' => 'dashboardCourier', 'courId' => 7, 'previewChats' => request('empty') ? [] : \App\Support\DevFixtures::previewChats()]),
            'chat-room' => view('pages.Courier.chatroom', ['Title' => 'room-chat', 'navActive' => 'dashboardCourier', 'custId' => 101, 'courId' => 7, 'date' => null, 'cust_name' => 'Kevin Pratama', 'wa' => '+6281298765432', 'previewMessages' => \App\Support\DevFixtures::previewMessages()]),
            default => abort(404),
        };
    });
}

// Detail Routes
Route::get('/detail-warung/{umkm:nama_umkm}', function (Data_umkm $umkm) {
    return view('pages.Users.DetailWarung', [
        'nama_umkm' => $umkm->nama_umkm,
        'alamat' => $umkm->alamat,
        'rating' => $umkm->rating,
        'CardFood' => $umkm->menu,
        'umkm_img' => $umkm->logo_umkm,
        'open_time' => $umkm->open_time,
        'close_time' => $umkm->close_time,
        'rating' => $umkm->rating,
        'BannerFade' => DetailWarungModel::bannerDetail(),
        'Title' => 'Detail-Warung',
        'PengaturanAkun' => HomeModel::pengaturanAkun(),
        'SeputarDkampus' => HomeModel::seputarDkampus(),
    ]);
});

// Menu Controller
Route::post('/detail-makanan/{id}', [MenuController::class, 'simpan']);

Route::get('/detail-makanan/{menu:nama_makanan}', function (Menu $menu) {
    return view('pages.Users.DetailMakanan', [
        'Title' => 'Detail-Makanan',
        'PengaturanAkun' => HomeModel::pengaturanAkun(),
        'SeputarDkampus' => HomeModel::seputarDkampus(),
        'menu_id' => $menu->id,
        'images' => $menu->image,
        'umkm_nama' => Data_umkm::where('id', $menu->data_umkm_id)->first()->nama_umkm,
        'nama_makanan' => $menu->nama_makanan,
        'rating' => $menu->rating,
        'hargaAsli' => $menu->harga,
        'diskon' => $menu->diskon,
        'harga' => number_format((float) $menu->diskon > 0 ? round($menu->harga * (1 - $menu->diskon / 100)) : $menu->harga, 0, ',', '.'),
        'deskripsi' => $menu->deskripsi,
        'CardFood' => Menu::where('data_umkm_id', $menu->data_umkm_id)->get(),
    ]);
})->name('detail-makanan');

//Favorit Routes
Route::get('/favorit', function () {
    $menus = Menu::whereIn('id', \App\Support\Favorites::ids())->with('data_umkm')->get();

    return view('pages.Users.Favorit', [
        'Title' => 'Favorit',
        'FavoritMakanan' => $menus,
        'FavoritWarung' => Data_umkm::whereIn('id', $menus->pluck('data_umkm_id')->unique())->get(),
        'Rekomendasi' => Menu::with('data_umkm')->take(4)->get(),
    ]);
});

//Favorite Routes
//Route::get('/favorit', function () {
//    return view('pages.Users.FavoritPage', [
//        'Title' => 'Favorit',
//        'CardFood' => Menu::all(),
//    ]);
//});

// Login & Register Routes
Route::get('/masuk', [UserController::class, 'login']);
Route::get('/daftar', [UserController::class, 'register']);
// Alamat lama dari prototipe statis -> alur nyata
Route::redirect('/input-registrasi', '/daftar');
Route::redirect('/code-verification', '/verifikasi-akun');
Route::get('/akun-berhasil', [PageController::class, 'accountCreated'])->name('account.created');
Route::get('/kata-sandi-berhasil', [PageController::class, 'passwordReset'])->name('reset.done');

// Onboarding dan halaman Seputar Dkampus
Route::get('/mulai', [PageController::class, 'onboarding'])->name('onboarding');
Route::get('/tentang-kami', [PageController::class, 'about'])->name('about');
Route::get('/syarat-ketentuan', [PageController::class, 'terms'])->name('terms');
Route::get('/kebijakan-privasi', [PageController::class, 'privacy'])->name('privacy');

//customer Routes
Route::middleware(['check.customer.role'])->group(
    function () {
        Route::get('/', [UserController::class, 'index'])->name('homepage');
    }
);



Route::middleware(['auth', 'verified', 'check.hasloggin'])->group(function () {
    //Pesanan Routes
    Route::get('/pesanan', [CartController::class, 'index']);
    Route::post('/detail-makanan/{id}', [MenuController::class, 'simpan']);
    Route::post('/product', [MenuController::class, 'simpan'])->name('product.store');
    Route::get('/pesanan/status', [CartController::class, 'status']);
    Route::post('/pesanan/status/detail', [CartController::class, 'StatusOrder'])->name('status.order');
    Route::post('/detail-Order', [CartController::class, 'detailHistory'])->name('historydetail.cust');
    Route::post('/rating', [CartController::class, 'rating'])->name('rating');
    Route::post('/rating/kirim', [\App\Http\Controllers\RatingController::class, 'store'])->name('rating.store');
    Route::get('/pesanan', [CartController::class, 'index']);
    Route::delete('/pesanan/delete', [CartController::class, 'destroy'])->name('cart.delete');
    Route::post("/pesanan/update-quantity", [CartController::class, 'updateQuantity'])->name('update.quantity');
    Route::post('/checkout', [CartController::class, 'checkout'])->name('checkout');
    Route::post('/pay/order/', [CartController::class, 'order'])->name('order');
    Route::post('/checkout/confirm', [CartController::class, 'confirmPay'])->name('confirm.pay');
    // User Chat page
    Route::get('/chats', [ChatController::class, 'listChat']);
    Route::post('/room-chat', [ChatController::class, 'roomChat'])->name('room.chat');
    Route::post('/chatWa', [ChatController::class, 'wa'])->name('chatWa');
    Route::post('/uploadChatImage', [ChatController::class, 'uploadChatImage']);
    Route::post('/chatbot', [CsController::class, 'bot'])->name('chatbot');
    // Daftar Alamat
    Route::get('/daftar-alamat', [UserController::class, 'indexAlamat'])->name('alamat');
    Route::post('/daftar_alamat', [UserController::class, 'daftarAlamat'])->name('daftar.alamat');
    Route::post('/defaul-address', [UserController::class, 'alamatUtama'])->name('alamatUtama');
    Route::post('/delete-address', [UserController::class, 'deleteAlamat'])->name('delete.alamat');

    // Jastip Page
    Route::get('/jastip', [CartController::class, 'jastipIndex'])->name('jastip.index');
    Route::post('/jastip-post', [CartController::class, 'jastip'])->name('jastip.order');

    // Cs Help
    Route::post('/help', [CsController::class, 'start'])->name('cs.help');
});


// Courier Routes
Route::middleware(['auth', 'verified', 'check.courier.role'])->prefix('courier')->group(function () {
    Route::post('/orders/delete', [CourierController::class, 'cancelOrder'])->name('delete.orders');
    Route::post('orders/complete', [CourierController::class, 'completeOrder'])->name('complete.orders');
    Route::get('/dashboard', [CourierController::class, 'index'])->name('dashboardCourier');
    Route::get('/chats', [CourierController::class, 'listChat'])->name('chatpage');
    Route::post('/chatWaCour', [CourierController::class, 'waCour'])->name('chatWaCour');
    Route::post('/room-chat', [CourierController::class, 'roomChat'])->name('room.chat.courier');
    Route::post('/uploadChatImageCour', [CourierController::class, 'uploadChatImageCour']);
    Route::get('/history', [CourierController::class, 'history'])->name('cour.history');
    Route::post('/detail-history', [CourierController::class, 'detailHistory'])->name('historydetail');
    Route::view(
        '/profile',
        'pages/Courier/profile',
        [
            'Title' => 'Profile',
        ]
    )->name('profile');
    Route::patch('/profile/phone', [CourierController::class, 'updatePhone'])->name('cour.profile.phone');

    Route::get('/order', [CourierController::class, 'listOrder'])->name('courierorder');
    Route::post('/take-Order', [CourierController::class, 'takeOrder'])->name('take.order');
});

// Admin Routes
Route::middleware(['auth', 'verified', 'check.admin.role'])->prefix('admin')->group(function () {
    Route::post('/updateUser', [AdminController::class, 'editUser'])->name('editUser');
    Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');
    // Closure (bukan Route::view): query harus jalan saat request, bukan saat route didaftarkan,
    // kalau tidak aplikasi tidak bisa boot di database kosong (migrate:fresh, test).
    Route::get('/umkm', function () {
        return view('pages/Admin/umkm', [
            'umkms' => Data_umkm::paginate(10),
        ]);
    })->name('umkm');
    Route::post('/umkm-save', [UmkmController::class, 'storeUmkm'])->name('umkm.save');
    Route::post('/products', [UmkmController::class, 'addProduct'])->name('save.product');
    Route::get('/product', function () {
        return view('pages/Admin/product', [
            'model' => new Menu(),
            'umkms' => Data_umkm::all(),
            'menus' => Menu::paginate(10),
            'button' => 'SIMPAN',
            'route' => "product.store",
            'method' => 'POST'
        ]);
    })->name('product');



    // edit & delete product route
    //to edit product form
    Route::get('/product/{menu}/edit', function (Menu $menu) {
        return view('pages/Admin/product_update', [
            'model' => $menu,
            'umkm' => Data_umkm::pluck('nama_umkm', 'id'),
            'button' => 'UPDATE',
            'route' => ['product.update', $menu->id],
            'method' => 'PUT'
        ]);
    })->name('product.edit');

    //data from edit product form
    Route::put('/product/{id}', [MenuController::class, 'update'])->name('productUpdate');
    //delete product

    Route::delete('/product/{menu}', [MenuController::class, 'destroy'])->name('productDestroy');

    // edit & delete umkm route

    //to edit umkm form
    Route::get('/umkm/{umkm}/edit', function (Data_umkm $umkm) {
        return view('pages/Admin/UMKM_update', [
            'umkm' => $umkm,
            'products' => Menu::where('data_umkm_id', $umkm->id)->get(),
        ]);
    })->name('umkmEdit');

    //data from edit umkm form
    Route::put('/umkm/{id}', [UmkmController::class, 'update'])->name('umkmUpdate');
    //delete umkm

    Route::delete('/umkm/{id}', [UmkmController::class, 'destroy'])->name('umkmDestroy');

    //transaction route
    Route::get('/transaction', [AdminController::class, 'transacation'])->name('transaction');

    //account-user management route
    Route::get('/account', function () {
        return view('pages/Admin/account', [
            'users' => \App\Models\User::all(),
        ]);
    })->name('account');

    //chat route
    Route::view(
        '/chats',
        'pages/Admin/chatpage',
        [
            'Title' => 'Chat',
        ]
    )->name('chatpage.admin');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


Route::post('/favoritStore/{menuId}', [FavoritController::class, 'favoritStore'])->middleware('auth')->name('favorite.add');


//searching routes
Route::get('/search/menu', [MenuController::class, 'search'])->name('search.menu');
Route::get('/search/umkm', [UmkmController::class, 'search'])->name('search.umkm');
Route::get('/search', function (Request $request) {
    $keyword = $request->keyword;
    $umkms = Data_umkm::where('nama_umkm', 'like', '%' . $keyword . '%')->get();
    $menus = Menu::where('nama_makanan', 'like', '%' . $keyword . '%')->with('data_umkm')->get();
    $menus = $menus->map(function ($menu) {
        $menu->nama_umkm = $menu->data_umkm->nama_umkm;
        return $menu;
    });
    return view('pages.Users.SearchPage', [
        'Title' => 'Search ' . ucfirst($keyword),
        'umkms' => $umkms,
        'menus' => $menus,
    ]);
});

Route::post('/register-token', [NotificationController::class, 'registerToken'])->name('register.token');
Route::post('/send-notification', [NotificationController::class, 'sendNotification'])->name('send.notification');
Route::post('/send-notification-cour', [NotificationController::class, 'sendNotificationToCouriers'])->name('send.notificationCour');
Route::get('/wa.me/{phone}', [ChatController::class, 'redirectWhatsApp']);
Route::get('/spreadsheets', [AdminController::class, 'spreadsheets']);
Route::get('/userDetails', [AdminController::class, 'getUserDetails']);
// Route::get('/sendTeleGroup', [CartController::class, 'sendToTeleGroup'])->name('sendNotifTele');


require __DIR__ . '/auth.php';
