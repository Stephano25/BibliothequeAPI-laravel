<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Services\OpenAIService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class BookController extends Controller
{
    protected OpenAIService $openAIService;

    public function __construct(OpenAIService $openAIService)
    {
        $this->openAIService = $openAIService;
    }

    public function getSummary(int $id): JsonResponse
    {
        $book = Book::with('author')->find($id);

        if (!$book) {
            return response()->json(['success' => false, 'message' => 'Livre non trouvé'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'summary' => $book->summary ?? 'Aucun résumé disponible',
                'book_id' => $book->id,
                'title' => $book->title,
                'author' => $book->author->full_name
            ]
        ]);
    }

    public function generateSummary(Request $request, int $id): JsonResponse
    {
        $book = Book::with('author')->find($id);

        if (!$book) {
            return response()->json(['success' => false, 'message' => 'Livre non trouvé'], 404);
        }

        if ($book->summary) {
            return response()->json([
                'success' => true,
                'data' => ['summary' => $book->summary, 'generated_by' => 'existing']
            ]);
        }

        try {
            $summary = $this->openAIService->generateSummary(
                $book->title,
                $book->author->full_name,
                $book->year
            );

            $book->summary = $summary;
            $book->save();

            return response()->json([
                'success' => true,
                'data' => ['summary' => $summary, 'generated_by' => 'openai']
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Service OpenAI indisponible'], 503);
        }
    }

    public function smartSearch(Request $request): JsonResponse
    {
        $request->validate(['description' => 'required|string|min:3']);

        try {
            $keywords = $this->openAIService->extractKeywords($request->description);
            $query = Book::with('author');

            foreach ($keywords as $keyword) {
                $query->where('title', 'LIKE', "%{$keyword}%")
                      ->orWhereHas('author', fn($q) => $q->where('first_name', 'LIKE', "%{$keyword}%")
                                                           ->orWhere('last_name', 'LIKE', "%{$keyword}%"));
            }

            return response()->json([
                'success' => true,
                'data' => ['keywords' => $keywords, 'results' => $query->limit(20)->get()]
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Erreur de recherche'], 503);
        }
    }
}