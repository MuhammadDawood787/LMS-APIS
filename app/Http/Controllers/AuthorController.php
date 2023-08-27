<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Author;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Exception;

class AuthorController extends Controller
{
    public function login(Request $request)
    {
        try {
            $credentials = $request->only('email', 'password');

            if (Auth::attempt($credentials)) {
                $user = Auth::user();
                $token = $user->createToken('api-token')->plainTextToken;

                return response()->json([
                    'token' => $token,
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                    ],
                ]);
            } else {
                throw ValidationException::withMessages([
                    'email' => __('auth.failed'),
                ]);
            }
        } catch (ValidationException $e) {
            return response()->json(['message' => $e->getMessage()], 401);
        } catch (Exception $e) {
            return response()->json(['message' => 'An error occurred', 'error' => $e->getMessage()], 500);
        }
    }
    public function getAuthors()
    {
        try {
                $authors = Author::with('books')->get()->toArray();
                return response()->json($authors);
        } catch (Exception $e) {
            return response()->json(['message' => 'An error occurred', 'error' => $e->getMessage()], 500);
        }

    }

    public function createAuthor(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'first_name' => 'required|string',
                'last_name' => 'required|string',
                'books' => 'nullable|array',
                'books.*' => 'exists:books,id',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 400);
            }

            $author = Author::create($request->all());

            // Attach books to the author if provided
            if ($request->has('books')) {
                $author->books()->sync($request->input('books'));
            }

            return response()->json(['message' => 'Author Registered Successfully'], 201);
        } catch (Exception $e) {
            return response()->json(['message' => 'An error occurred', 'error' => $e->getMessage()], 500);
        }
    }

    public function getSingleAuthor($id)
    {
        try {
                $author = Author::with('books')->findOrFail($id);
                return response()->json($author,200);

        } catch (Exception $e) {
            return response()->json(['message' => 'An error occurred', 'error' => $e->getMessage()], 500);
        }
    }


    public function updateAuthor(Request $request, $id)
    {

        try {


            $author = Author::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'first_name' => 'required|string',
                'last_name' => 'required|string',
                'books' => 'nullable|array',
                'books.*' => 'exists:books,id',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 400);
            }

            $author->update($request->all());

            if ($request->has('books')) {
                $author->books()->sync($request->input('books'));
            }

            return response()->json(['message' => 'Author Record Updated'], 201);
        } catch (Exception $e) {
            return response()->json(['message' => 'An error occurred', 'error' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $author = Author::findOrFail($id);
            $author->delete();
            return response()->json(['message' => 'Author deleted successfully'], 204);
        } catch (Exception $e) {
            return response()->json(['message' => 'An error occurred', 'error' => $e->getMessage()], 500);
        }
    }

    public function authorBooks($authorId)
    {
        try{
            $author = Author::findOrFail($authorId);
            $books = $author->books;
            return response()->json($books,200);
        }catch (Exception $e) {
            return response()->json(['message' => 'An error occurred', 'error' => $e->getMessage()], 500);
        }

    }
}

