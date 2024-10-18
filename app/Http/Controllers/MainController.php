<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class MainController extends Controller
{
    public function ratingAggregation($id){
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
        $averageRating = Review::where('movie_id', $validated['id'])->avg('rating');
        if($averageRating){
            return response()->json(['status' => true,'averageRating' => $averageRating]);
        }else{
            return response()->json(['status' => false,'message' => 'Unable to calculate average rating']);
        }
    }

    public function fetchMovies(){
        $movies = Movie::with('genre')->paginate(10);
        
        return response()->json(['status' => true, 'movies' => $movies]);
     }
 
}
