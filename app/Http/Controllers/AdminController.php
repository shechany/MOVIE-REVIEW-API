<?php

namespace App\Http\Controllers;

use App\Mail\NewMovie;
use App\Models\Movie;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    public function approveUser(Request $request){
        if(!Auth::check()){
            return response()->json([
                    'success' => false,
                    'message' => 'User is not authenticated.',
                ], 401);
            }
        $validator = Validator::make($request->all(), [
            'user' => 'required|exists:users,id',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $validated = $validator->validated();
        $approveUser = User::where('id',$validated['user'])->update(['approval_status' => 1]);
        if($approveUser){
            return response()->json(['status' => true,'message' => 'user approved successfully.'], 200);
        }else{
            return response()->json(['status' => false,'message' => 'Unable to approve user.'], 400);
        }
    }
    public function addmovie(Request $request){
        if(!Auth::check()){
            return response()->json([
                    'status' => false,
                    'message' => 'User is not authenticated.',
                ], 401);
            }
        $validator = Validator::make($request->all(), [
            'title' => 'required|max:50',
            'description' => 'required|max:100',
            'release_date' => 'required|date',
            'genre' => 'required|exists:genres,id',
            'thumbnail' => 'required|file|mimes:jpg,jpeg,png|max:2048'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $validated = $validator->validated();
        $file = $validated['thumbnail'];

        $filename = time() . '_' . $validated['thumbnail']->getClientOriginalName();
        $file->move(public_path('uploads'), $filename);
        $add_movie = Movie::create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'release_date' => $validated['release_date'],
            'genre' => $validated['genre'],
            'thumbnail' => $filename
        ]);
        if($add_movie){
            try {
                $users = User::where('role',0)->get();
                if($users){
                    foreach ($users as $user) {
                        Mail::to($user->email)->send(new NewMovie($validated['title'],$validated['description'],$validated['release_date']));
                    }
                }
            } catch (\Throwable $th) {
                return response()->json(['status' => true, 'message' => 'Movie added successfully but failed to notify users'],200);
            }
            return response()->json(['status' => true,'message' => 'Movie added successfully.'], 200);
        }else{
            return response()->json(['status' => false,'message' => 'Unable to add movie.'], 400);
        }
    }

    public function updateMovie(Request $request){
        if(!Auth::check()){
            return response()->json([
                    'success' => false,
                    'message' => 'User is not authenticated.',
                ], 401);
            }
        $validator = Validator::make($request->all(), [
            'movie_id' => 'required',
            'title' => 'required|max:50',
            'description' => 'required|max:100',
            'release_date' => 'required|date',
            'genre' => 'required|exists:genres,id',
            'thumbnail' => 'required|file|mimes:jpg,jpeg,png|max:5048'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }
        $validated = $validator->validated();
        $file = $validated['thumbnail'];
        $filename = time() . '_' . $validated['thumbnail']->getClientOriginalName();
        $file->move(public_path('uploads'), $filename);
      
        $movie = Movie::find($validated['movie_id']);
        if($movie){

            $fileToDelete = public_path('uploads/'.$movie->thumbnail);
            
            $movie->title = $validated['title'];
            $movie->description = $validated['description'];
            $movie->release_date = $validated['release_date'];
            $movie->genre = $validated['genre'];
            $movie->thumbnail = $filename;
            
            $movie->save();

            if ($fileToDelete) {                
                unlink($fileToDelete); 
            }

            return response()->json(['status' => true,'message' => 'Movie updated successfully.', 'movie' => $movie]);

        }else{
            return response()->json(['status' => false, 'message' => 'movie not found.'], 404);
        }
    }

    public function deleteMovie($id){
        if(!Auth::check()){
            return response()->json([
                    'success' => false,
                    'message' => 'User is not authenticated.',
                ], 401);
            }
        $validator = Validator::make(['id' => $id], [
                'id' => 'required|integer|exists:movies,id',
            ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $validated = $validator->validated();
        
        $movie = Movie::find($validated['id']);
        if($movie){
            $fileToDelete = public_path('uploads/'.$movie->thumbnail);
            $deleted = $movie->delete();
            if ($fileToDelete) {                
                unlink($fileToDelete); 
            }
            if ($deleted) {
                return response()->json(['status' => true,'message' => 'Movie deleted successfully.'],200);
            } else {
                return response()->json(['status' => false ,'message' => 'Unable to delete movie.'], 400);
            }
        }
       
    }
}
