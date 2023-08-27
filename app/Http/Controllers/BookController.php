<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use Illuminate\Support\Facades\Validator;
class BookController extends Controller
{

     // This Method is To Store Book
    public function storeBook(Request $request)
    {
        try {
            // Validate the incoming request data
            $validator = Validator::make($request->all(), [
                'title' => 'required|string',
                'description' => 'required|string',
                'isbn' => 'required|unique:books',
                'publication_date' => 'required|date',
                'author_ids' => 'required|array',
                'author_ids.*' => 'exists:authors,id',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 400);
            }

            $data = $request->except('author_ids');

            $book = Book::create($data);

            $book->authors()->attach($request->input('author_ids'));

            return response()->json(['message' => 'Book created successfully', 'book' => $book], 201);
        } catch (Exception $e) {
            return response()->json(['message' => 'An error occurred', 'error' => $e->getMessage()], 500);
        }

    }
    //  Update Book Record
    public function updateBook(Request $request, $id)
    {

        try {
            $book = Book::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'title' => 'required|string',
                'description' => 'required|string',
                'isbn' => 'required|unique:books,isbn,' . $book->id,
                'publication_date' => 'required|date',
                'author_ids' => 'required|array',
                'author_ids.*' => 'exists:authors,id',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 400);
            }

            // Remove the author_ids from the request data
            $data = $request->except('author_ids');

            $book->update($data);

            $book->authors()->sync($request->input('author_ids'));

            return response()->json(['message' => 'Book updated successfully', 'book' => $book]);
        } catch (Exception $e) {
            return response()->json(['message' => 'An error occurred', 'error' => $e->getMessage()], 500);
        }
    }
    // This Method is for Getting all Books Record
    public function getBooks()
    {
        try {
        $books = Book::with('authors')->get()->toArray();
        return response()->json($books);
        } catch (Exception $e) {
            return response()->json(['message' => 'An error occurred', 'error' => $e->getMessage()], 500);
        }

    }
    // Show Record of single book
    public function getSinglebook($id)
    {
        try {
        $book = Book::with('authors')->findOrFail($id);
        return response()->json($book);
        } catch (Exception $e) {
        return response()->json(['message' => 'An error occurred', 'error' => $e->getMessage()], 500);
        }
    }

    // This Method is Search for books by title and author
    public function searchBook(Request $request)
    {
        try{
            $title = $request->input('title');
            $authorFirstName = $request->input('author_first_name');

            $query = Book::query();

            if ($title) {
                $query->where('title', 'LIKE', "%$title%");
            }

            if ($authorFirstName) {
                $query->whereHas('authors', function ($query) use ($authorFirstName) {
                    $query->where('first_name', 'LIKE', "%$authorFirstName%");
                });
            }

            $books = $query->get();
            return response()->json($books,200);
        } catch (Exception $e) {
            return response()->json(['message' => 'An error occurred', 'error' => $e->getMessage()], 500);
        }

    }

    // destroy book record
    public function destroy($id)
    {
        try{
            $book = Book::findOrFail($id);
            $book->delete();

            return response()->json(['message' => 'Book deleted successfully'], 204);
        } catch (Exception $e) {
            return response()->json(['message' => 'An error occurred', 'error' => $e->getMessage()], 500);
         }

    }

    // Fetch all authors of a book
    public function bookAuthors($bookId)
    {
        try{
        $book = Book::findOrFail($bookId);
        $authors = $book->authors;
        return response()->json($authors,200);
       } catch (Exception $e) {
        return response()->json(['message' => 'An error occurred', 'error' => $e->getMessage()], 500);
       }
    }

}
