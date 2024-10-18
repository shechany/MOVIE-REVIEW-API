<?php

namespace App\Http\Controllers;

use App\Mail\EmailSender;
use App\Models\Movie;
use App\Models\Review;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function registration(Request $request){
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|max:50',
            'last_name' => 'required|max:50',
            'email_address' => 'required|email:rfc,dns|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $validated = $validator->validated();
        $createUser = User::create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email_address'],
            'password' => bcrypt($validated['password'])
        ]);

        if($createUser){
            try {
                $admins = User::where('role',1)->get();
                if($admins){
                    foreach ($admins as $admin) {
                        Mail::to($admin->email)->send(new EmailSender($validated['first_name']." ".$validated['last_name']));
                    }
                }
            } catch (\Throwable $th) {
                return response()->json(['status' => true, 'message' => 'Registration successful but failed to notify admin'],200);
            }
            
            return response()->json(['status' => true, 'message' => 'Registration successful.'],200);
        }else{
            return response()->json(['status' => false, 'message' => 'Unable to complete registration.'],400);
        }
    }

    public function login(Request $request){
        $validator = Validator::make($request->all(), [
            'email_address' => 'required|max:50',
            'password' => 'required|max:50',
        ]); 

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $validated = $validator->validated();

        if (auth()->attempt([
            'email' => $validated['email_address'],
            'password' => $request['password']
        ])) {
            $token = auth()->user()->createToken('PAT-AFRICRED')->accessToken;
            return response()->json(['status' => true,'message' => 'Logged in successfully.','access_token' => $token], 200);
        } else {
            return response()->json(['status' => false,'message' => 'Unable to log you in, check your username and password.'], 401);
        }
    }

    public function reviewMovie(Request $request){
        if(!Auth::check()){
        return response()->json([
                'success' => false,
                'message' => 'User is not authenticated.',
            ], 401);
        }
        $user = Auth::user();
        $validator = Validator::make($request->all(), [
            'movie_id' => 'required',
            'comment' => 'required|max:100',
            'rating' => 'required|integer|min:1|max:5',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }
        $validated = $validator->validated();
        if($user->approval_status == 1){
            $addReview = Review::create([
                'movie_id' => $validated['movie_id'], 
                'comment' => $validated['comment'],
                'rating' => $validated['rating']
            ]);
            if($addReview){
                return response()->json(['status' => true,'message' => 'Review added successfully.','review' => $addReview], 200);
            }else{
                return response()->json(['status' => false,'message' => 'Unable to add review.'], 400);
            }
        }else{
            return response()->json(['status' => false,'message' => 'Your account has not yet been approved.'], 400);
        }
        
    }

    public function movieSearchBytitle(Request $request){
        $validator = Validator::make($request->all(), [
            'title' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }
        $validated = $validator->validated();
        $movie = Movie::where('title', 'LIKE', '%'.$validated['title'].'%')->get();
        if($movie){
            return response()->json(['status' => true, 'movie' => $movie]);
        }else{
            return response()->json(['status' => false, 'message' => 'Unable to search for movie']);
        }
    }

    public function movieSearchBygenre(Request $request){
        $validator = Validator::make($request->all(), [
            'genre' => 'required|integer|exists:genres,id',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }
        $validated = $validator->validated();
        $movie = Movie::where('genre',$validated['genre'])->get();
        if($movie){
            return response()->json(['status' => true, 'movie' => $movie]);
        }else{
            return response()->json(['status' => false, 'message' => 'Unable to search for movie']);
        }
    }
}
