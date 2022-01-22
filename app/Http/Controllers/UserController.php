<?php

namespace App\Http\Controllers;

use App\Models\Property;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function not_found()
    {
        $title = "Page Not Found";
        $menu = 'none';
        $data = compact('title', 'menu');
        return view('errors.404', $data);
    }
    public function loginForm()
    {
        $title = "Log In";
        $data = compact('title');
        return view('User.UserLogIn', $data);
    }
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = User::select('*')->where('email', $request->email)->get();
        // $user = User::get()->where('email', $request->email);

        // dd($user);

        // if ($user[0]->password == md5($request->password)) {
        if (Hash::check($request->password, $user[0]->password)) {
            $request->session()->put('user', $user[0]->toArray());
            return redirect(route('userHome'));
        } else {
            $request->validate([
                'password' => 'password'
            ]);
        }
    }

    //sending to sign up form
    public function signupForm()
    {
        $title = "Sign In";
        $data = compact('title');
        return view('User.UserSignUp', $data);
    }

    //signing up
    public function signup(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required',
            'conf_password' => 'required|same:password'
        ]);

        $user = new User;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        // $user->password = md5($request->password);
        $user->save();

        return redirect(url('/login'));
        // dd($pass);
    }

    //managing login logout
    public function logout(Request $request)
    {
        $request->session()->forget('user');
        // $request->session()->flush();
        return redirect(url(route('userHome')));
    }

    //sending home
    public function userHome(Request $request)
    {
        // dd($data);
        $title = "Home";
        $menu = "home";
        $featuredPro = Property::with('Cate', 'City')->where('featured', true)->get();
        // dd($featuredPro);
        $data = compact('title', 'menu', 'featuredPro');
        return view('frontend.home', $data);
    }
    public function show_category(Request $request)
    {
        $valid = validator($request->route()->parameters(), [
            'cate' => 'exists:categories,slug_name'
        ])->validate();
        $cate = $request->route()->parameter('cate');
        $title = $cate;
        $menu = 'category';
        $data = compact('title', 'menu');
        return view('frontend.category', $data);
    }
    public function show_city(Request $request)
    {
        $valid = validator($request->route()->parameters(), [
            'city' => 'exists:cities,slug_city'
        ])->validate();
        $city = $request->route()->parameter('city');
    }
}
