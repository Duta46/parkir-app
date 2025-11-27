<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LandingPageController extends Controller
{
    public function index()
    {
        // data yang bisa dikirim ke view; sesuaikan jika perlu
        $campus = [
            'name' => 'Universitas PGRI Kanjuruhan Malang',
            'email' => 'email@unikama.ac.id',
            'phone' => '(+62341) 801488',
            'address' => 'Jl. S. Supriadi No.48 Malang Jawa Timur, Indonesia',
        ];

        // list gambar carousel di public/images/landing/
        $images = [
            'assets/images/campus-1.jpg',
            'assets/images/campus-2.jpg',
            'assets/images/parking-1.jpg',
        ];

        return view('landing-page', compact('campus', 'images'));
    }
}
