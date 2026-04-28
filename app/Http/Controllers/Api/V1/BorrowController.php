<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Borrow;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class BorrowController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $request->validate(['book_id' => 'required|exists:books,id']);
        
        $user = $request->user();
        $book = Book::find($request->book_id);

        if (!$book || !$book->isAvailable()) {
            return response()->json(['success' => false, 'message' => 'Livre non disponible'], 400);
        }

        $borrow = Borrow::create([
            'user_name' => $user->name,
            'book_id' => $book->id,
            'borrowed_at' => now(),
            'payment_status' => $user->subscription_status === 'premium' ? 'paid' : 'free'
        ]);

        $book->available = false;
        $book->save();

        return response()->json(['success' => true, 'data' => $borrow], 201);
    }

    public function return(Request $request, int $id): JsonResponse
    {
        $borrow = Borrow::where('id', $id)
            ->where('user_name', $request->user()->name)
            ->whereNull('returned_at')
            ->first();

        if (!$borrow) {
            return response()->json(['success' => false, 'message' => 'Emprunt non trouvé'], 404);
        }

        $borrow->returned_at = now();
        $borrow->save();

        $book = $borrow->book;
        $book->available = true;
        $book->save();

        return response()->json(['success' => true, 'message' => 'Livre retourné']);
    }
}