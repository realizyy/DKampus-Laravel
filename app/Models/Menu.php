<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Nicolaslopezj\Searchable\SearchableTrait;

class Menu extends Model
{
    use HasFactory, SearchableTrait;

    /** Kategori yang ditawarkan di form admin; nilai disimpan apa adanya di menus.category. */
    public const CATEGORIES = [
        'minuman' => 'Minuman',
        'jajanan' => 'Jajanan',
        'aneka nasi' => 'Aneka Nasi',
        'ayam & bebek' => 'Ayam & Bebek',
        'cepat saji' => 'Cepat Saji',
        'roti' => 'Roti',
        'bakso & soto' => 'Bakso & Soto',
        'bakmie' => 'Bakmie',
        'mie' => 'Mie',
        'sate' => 'Sate',
    ];

    protected $guarded = [
        'id',        
    ];

    protected $searchable = [
        'columns' => [
            'nama_makanan' => 10,            
            'kategori' => 10,
            'deskripsi' => 10, 
            'data_umkms.nama_umkm' => 10,        
        ],
        'joins' => [
            'data_umkms' => ['menus.data_umkm_id', 'data_umkms.id'],                      
        ],
    ];

    // relation with data_umkm
    public function data_umkm()
    {
        return $this->belongsTo(Data_umkm::class);
    }

}
