<?php

// app/Http/Controllers/FavoriteController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function index()
    {
        // Devuelve un array vacío o los favoritos del usuario
        return response()->json([]);
    }
}

?>
