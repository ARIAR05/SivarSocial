<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function __construct()
    {
        // Elimina el middleware 'auth' para permitir acceso público a home
    }

    public function __invoke()
    {
        // Si el usuario está autenticado, mostrar posts de quienes sigue
        if (Auth::check()) {
            $ids = Auth::user()->following->pluck('id')->toArray();
            $posts = Post::whereIn('user_id', $ids)
                ->with('user')
                ->latest()
                ->paginate(10)
                ->onEachSide(2);
        } else {
            // Si no está autenticado, mostrar todos los posts
            $posts = Post::with('user')
                ->latest()
                ->paginate(10)
                ->onEachSide(2);
        }
        // Obtener todos los usuarios para mostrar perfiles
        $users = \App\Models\User::latest()->get();
        return view('home', [
            'posts' => $posts,
            'users' => $users,
        ]);
    }
}
