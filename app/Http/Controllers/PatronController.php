<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Patron;
use App\Models\Book;
use App\Models\PatronBook;
use Illuminate\Support\Facades\Validator;
class PatronController extends Controller
{
    public function storePatron(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string',
                'email' => 'required|email|unique:patrons',
                'address' => 'required|string', // Add address validation
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 400);
            }

            $patron = Patron::create($request->all());

            return response()->json(['message' => 'Patron created successfully', 'patron' => $patron], 201);
        } catch (Exception $e) {
            return response()->json(['message' => 'An error occurred', 'error' => $e->getMessage()], 500);
        }
    }

    public function updatePatron(Request $request, $id)
    {
        try {
            $patron = Patron::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'name' => 'required|string',
                'email' => 'required|email|unique:patrons,email,' . $patron->id,
                'address' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 400);
            }

            $patron->update($request->all());

            return response()->json(['message' => 'Patron updated successfully', 'patron' => $patron], 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'An error occurred', 'error' => $e->getMessage()], 500);
        }
    }

    public function getPatrons()
    {
        try {
        $patrons = Patron::get()->toArray();
        return response()->json($patrons);
        } catch (Exception $e) {
            return response()->json(['message' => 'An error occurred', 'error' => $e->getMessage()], 500);
        }

    }

    public function destroy($id)
    {
        try{
            $patron = Patron::findOrFail($id);
            $patron->delete();

            return response()->json(['message' => 'Patron deleted successfully'], 204);
        } catch (Exception $e) {
            return response()->json(['message' => 'An error occurred', 'error' => $e->getMessage()], 500);
         }

    }

    public function borrowBook(Request $request, $patronId, $bookId)
    {
        try {
            $patron = Patron::findOrFail($patronId);
            $book = Book::findOrFail($bookId);

            // Check if the book is already borrowed by the patron
            if ($patron->borrowedBooks->contains($book)) {
                return response()->json(['message' => 'Book is already borrowed by this patron'], 400);
            }

            $validator = Validator::make($request->all(), [
                'due_date' => 'required|date',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 400);
            }

            // Logic for borrowing the book and tracking due date
            $dueDate = $request->input('due_date');
            $patron->borrowedBooks()->attach($book, [
                'borrowed_at' => now(),
                'due_date' => $dueDate,
            ]);

            return response()->json(['message' => 'Book borrowed successfully'], 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'An error occurred', 'error' => $e->getMessage()], 500);
        }
    }
    public function returnBook(Request $request, $patronId, $bookId)
    {
        try {
            $patron = Patron::findOrFail($patronId);
            $book = Book::findOrFail($bookId);

            // Check if the book is borrowed by the patron
            if (!$patron->borrowedBooks->contains($book)) {
                return response()->json(['message' => 'Book is not borrowed by this patron'], 400);
            }

            // Logic for returning the book and updating return date
            $patronBook = PatronBook::where('patron_id', $patron->id)
                ->where('book_id', $book->id)
                ->first();

            $patronBook->update([
                'returned_at' => now(),
            ]);

            return response()->json(['message' => 'Book returned successfully'], 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'An error occurred', 'error' => $e->getMessage()], 500);
        }
    }


}
