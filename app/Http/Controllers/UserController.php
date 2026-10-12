<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Addresse;
use App\Models\Data_umkm;
use App\Models\Menu;
use App\Models\Footer;
use Illuminate\Http\Request;
use App\Models\HomeModel;
use App\Models\PromoModel;
use Exception;
use Illuminate\Support\Facades\Auth;
use GuzzleHttp\Client;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $alamat = Addresse::where('user_id', Auth::user()->id)->where('utama', 1)->first();
            $listJarak = [];

//            foreach (Data_umkm::all() as $data) {
//                $listJarak[] = $this->calculteDistance($alamat->geo, $data->geo);
//            }
            return view('pages.Users.Homepage', [
                'Banner' => HomeModel::bannerData(),
                'PromoTerlaris' => PromoModel::promoTerlaris(),
                'PengaturanAkun' => HomeModel::pengaturanAkun(),
                'SeputarDkampus' => HomeModel::seputarDkampus(),
                'Carousel' => HomeModel::carouselData(),
                'CarouselDesktop' => HomeModel::carouselDesktopData(),
                'RekomendasiWarung' => Data_umkm::all(),
                'RekomendasiMakanan' => Menu::take(5)->get(),
                'FooterPart1' => Footer::footerPart1(),
                'FooterPart2Beli' => Footer::footerPart2Beli(),
                'FooterPart2Jual' => Footer::footerPart2Jual(),
                'FooterPart3KeamananDanPrivasi' => Footer::footerPart3KeamananDanPrivasi(),
                'FooterPart3IkutiKami' => Footer::footerPart3IkutiKami(),
                'Title' => 'Home',
                'alamat' => $alamat,
                'listJarak' => $listJarak,
            ]);
        } catch (Exception $e) {
            return view('pages.Users.Homepage', [
                'Banner' => HomeModel::bannerData(),
                'PromoTerlaris' => PromoModel::promoTerlaris(),
                'PengaturanAkun' => HomeModel::pengaturanAkun(),
                'SeputarDkampus' => HomeModel::seputarDkampus(),
                'Carousel' => HomeModel::carouselData(),
                'CarouselDesktop' => HomeModel::carouselDesktopData(),
                'RekomendasiWarung' => Data_umkm::all(),
                'RekomendasiMakanan' => Menu::take(5)->get(),
                'FooterPart1' => Footer::footerPart1(),
                'FooterPart2Beli' => Footer::footerPart2Beli(),
                'FooterPart2Jual' => Footer::footerPart2Jual(),
                'FooterPart3KeamananDanPrivasi' => Footer::footerPart3KeamananDanPrivasi(),
                'FooterPart3IkutiKami' => Footer::footerPart3IkutiKami(),
                'Title' => 'Home',
                'listJarak' => null,
            ]);
        }
    }

    private function calculteDistance($from, $to)
    {
        $apiKey = env('GOOGLE_MAPS_API_KEY');

        $client = new Client();

        $url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=$from&destinations=$to&key=$apiKey";

        $response = $client->get($url);

        if ($response->getStatusCode() === 200) {
            $data = json_decode($response->getBody(), true);
            $distance = $data['rows'][0]['elements'][0]['distance']['value'];
            return $distance;
        } else {
            return back()->withErrors(['error2' => 'Failed to calculate distance.']);
        }
    }

    public function login()
    {
        return view('pages.Users.Login', [
            'Title' => 'Log in',
            'PengaturanAkun' => HomeModel::pengaturanAkun(),
            'SeputarDkampus' => HomeModel::seputarDkampus(),
        ]);
    }

    public function register()
    {
        return view('pages.Users.Register', [
            'Title' => 'Register',
        ]);
    }

    public function indexAlamat()
    {
        $userID = Auth::user()->id;
        $alamat = User::find($userID);
        $dataAlamat = $alamat->addresses()->get();
        return view('pages.Users.DaftarAlamat', [
            'Title' => 'Daftar Alamat',
            'alamatUser' => $dataAlamat,
        ]);
    }

    public function daftarAlamat(Request $request)
    {
        // Validasi di luar try/catch supaya galat tampil di form (sebelumnya ditelan lalu redirect diam-diam)
        $request->validate([
            'address' => 'required|string|max:500',
            'namaAlamat' => 'required|string|max:100',
            'link' => 'nullable|string',
            'geo' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:500',
        ], [], ['address' => 'alamat lengkap', 'namaAlamat' => 'nama alamat']);

        try {
            Addresse::create([
                'user_id' => Auth::id(),
                'address' => $request->address,
                // Tanpa Google Places, tautan peta dibuat dari teks alamat
                'link' => $request->link ?: 'https://www.google.com/maps/search/?api=1&query='.urlencode($request->address),
                'nama_alamat' => $request->namaAlamat,
                'notes' => $request->notes,
                'geo' => $request->geo,
                'utama' => 0,
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error2', 'Alamat belum bisa disimpan. Coba lagi.');
        }

        return redirect()->back()->with('success', 'Alamat tersimpan.');
    }

    public function alamatUtama()
    {
        try {
            // Selalu pakai user yang login: id dari request tidak boleh menunjuk alamat milik orang lain
            $userId = Auth::id();
            $address = Addresse::where('user_id', $userId)->findOrFail(request()->id);

            Addresse::where('user_id', $userId)->where('utama', 1)->update(['utama' => 0]);
            $address->update(['utama' => 1]);

            return redirect()->back();
        } catch (Exception $e) {
            return redirect()->back()->with('error2', 'Error');
        }
    }

    public function deleteAlamat()
    {
        try {
            Addresse::where('user_id', Auth::id())->where('id', request()->id)->delete();
            return redirect()->back();
        } catch (Exception $e) {
            return redirect()->back()->with('error2', 'Error');
        }
    }
}
