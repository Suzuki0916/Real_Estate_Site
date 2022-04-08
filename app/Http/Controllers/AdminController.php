<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\City;
use App\Models\Facilities;
use App\Models\gallary;
use App\Models\Property;
use App\Models\Reviews;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserData;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    //auth starts

    //sending to Dashboard
    public function dashboard(Request $request)
    {
        $title = 'Dashboard';
        $menu = 'dashboard';

        $newUsers = User::with('Data')->whereMonth('created_at', Carbon::now()->month)->latest()->get();
        $newReviews = Reviews::with('Users')->whereMonth('created_at', Carbon::now()->month)->latest()->get();
        $newProperty = Property::whereMonth('created_at', Carbon::now()->month)->latest()->get();
        // dd($newUsers);

        $data = compact('title', 'menu', 'newUsers', 'newReviews', 'newProperty');
        return view('AdminPanel.dashboard.dashboard')->with($data);
    }
    //sending to adminlogin page
    public function loginPage()
    {
        $title = "Log In";
        $status = false;

        $data = compact('status', 'title');
        return view('AdminPanel.AdminUser.AdminLogin')->with($data);
    }
    //logging In
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();

        if (Hash::check($request->password, $user->password)) {
            $id = $user->id;
            $user = User::with('Data', 'Reviews')->findOrFail($id);
            if ($user->type == "A" || $user->type == "R") {
                $request->session()->put('AdminUser', $user->toArray());
                return redirect(route('AdminHome'));
            } else {
                return redirect(route('userHome'));
            }
        } else {
            $request->validate([
                'password' => 'password'
            ]);
        }
    }
    public function logout(Request $request)
    {
        $request->session()->forget('AdminUser');
        // $request->session()->flush();

        return redirect(url(route('AdminLoginPage')));
    }

    //auth ends

    //category starts
    public function list_category(Request $request)
    {

        $title = "Category List";
        $menu = "category";
        $cate = Category::latest()->get();

        $data = compact('title', 'menu', 'cate');
        return view('AdminPanel.category.list', $data);
    }

    public function add_category(Request $request)
    {
        $title = "Add Category";
        $menu = "category";

        $data = compact('title', 'menu');
        return view('AdminPanel.category.form', $data);
    }

    public function category_added(Request $request)
    {
        $valid = $request->validate([
            'name' => 'required|unique:categories,name',
            'image' => 'required|mimes:png,jpg'
        ]);
        $cate = new Category;
        $cate->name = $request->name;
        $cate->slug_name = str_slug($request->name);
        $image = $request->file('image');
        $iname = date('Ym') . '-' . rand() . '.' . $image->extension();
        $store = $image->storeAs('public/images', $iname);
        if ($store) {
            $cate->image = $iname;
        }
        $cate->save();

        $request->session()->flash('msg', 'Added...');
        $request->session()->flash('msgst', 'success');

        return redirect(route('list_category'));
    }

    public function del_category(Request $request)
    {
        $valid = validator($request->route()->parameters(), [
            'id' => 'exists:categories,id'
        ])->validate();
        $id = $request->route()->parameter('id');

        if ($valid) {
            $cate = Category::findorfail($id);
            $pro = Property::where('category', $id)->get();
            if ($pro->count() > 0) {
                $request->session()->flash('msg', 'Can not delete this category, there are properties listed in this category');
                $request->session()->flash('msgst', 'danger');
            } else {
                $image = $cate->image;
                Storage::delete('public/images/' . $image);
                $cate->delete();
                $request->session()->flash('msg', 'Deleted...');
                $request->session()->flash('msgst', 'success');
            }
        }

        return redirect(route('list_category'));
    }

    public function edit_category(Request $request)
    {
        $valid = validator($request->route()->parameters(), [
            'id' => 'exists:categories,id'
        ])->validate();
        $id = $request->route()->parameter('id');

        if ($valid) {
            $cate = Category::findorfail($id);
        }

        $title = "Edit Category";
        $menu = "category";

        $data = compact('title', 'menu', 'cate');
        return view('AdminPanel.category.form', $data);
    }

    public function category_edited(Request $request)
    {
        $valid = validator($request->route()->parameters(), [
            'id' => 'exists:categories,id'
        ])->validate();
        $id = $request->route()->parameter('id');
        $request->validate([
            'name' => 'required|unique:categories,name,' . $id,
            'image' => 'mimes:png,jpg'
        ]);
        $cate = Category::findorfail($id);
        $cate->name = $request->name;
        $cate->slug_name = str_slug($request->name);
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $iname = date('Ym') . '-' . rand() . '.' . $image->extension();
            Storage::delete('public/images/' . $cate->image);
            $store = $image->storeAs('public/images', $iname);
            if ($store) {
                $cate->image = $iname;
            }
        }
        $cate->save();

        $request->session()->flash('msg', 'Edited...');
        $request->session()->flash('msgst', 'success');

        return redirect(route('list_category'));
    }
    //category ends

    //Cities Starts

    public function list_cities(Request $request)
    {
        $title = "Cities List";
        $menu = "cities";
        $city = City::latest()->get();

        $data = compact('title', 'menu', 'city');
        return view('AdminPanel.cities.list', $data);
    }
    public function add_cities(Request $request)
    {
        $title = "Add Cities";
        $menu = "cities";

        $data = compact('title', 'menu');
        return view('AdminPanel.cities.form', $data);
    }
    public function cities_added(Request $request)
    {
        $valid = $request->validate([
            'city' => 'required|unique:cities,city',
            'status' => 'boolean'
        ]);
        $city = new City;
        $city->city = $request->city;
        $city->slug_city = str_slug($request->city);
        if ($request->status == 1) {
            $city->status = $request->status;
        } else {
            $city->status = '0';
        }
        $city->save();

        $request->session()->flash('msg', 'Added...');
        $request->session()->flash('msgst', 'success');

        return redirect(route('list_cities'));
    }
    public function del_cities(Request $request)
    {
        $valid = validator($request->route()->parameters(), [
            'id' => 'exists:cities,id'
        ])->validate();
        $id = $request->route()->parameter('id');

        if ($valid) {
            $city = City::findorfail($id);
            $pro = Property::where('city', $id)->get();
            if ($pro->count() > 0) {
                $request->session()->flash('msg', 'Can not delete this city, there are properties listed in this city');
                $request->session()->flash('msgst', 'danger');
            } else {
                $city->delete();
                $request->session()->flash('msg', 'Deleted...');
                $request->session()->flash('msgst', 'success');
            }
        }

        return redirect(route('list_cities'));
    }
    public function edit_cities(Request $request)
    {
        $valid = validator($request->route()->parameters(), [
            'id' => 'exists:cities,id'
        ])->validate();
        $id = $request->route()->parameter('id');

        if ($valid) {
            $city = City::findorfail($id);
        }

        $title = "Edit Cities";
        $menu = "cities";

        $data = compact('title', 'menu', 'city');
        return view('AdminPanel.cities.form', $data);
    }
    public function cities_edited(Request $request)
    {
        $valid = validator($request->route()->parameters(), [
            'id' => 'exists:cities,id'
        ])->validate();
        $id = $request->route()->parameter('id');
        $request->validate([
            'city' => 'required|unique:cities,city,' . $id,
            'status' => 'boolean'
        ]);
        $city = City::findorfail($id);
        $city->city = $request->city;
        $city->slug_city = str_slug($request->city);
        if ($request->status == 1) {
            $city->status = $request->status;
        } else {
            $city->status = '0';
        }
        $city->save();

        $request->session()->flash('msg', 'Edited...');
        $request->session()->flash('msgst', 'success');

        return redirect(route('list_cities'));
    }
    //Cities Ends

    //Facilities starts
    public function list_facilities(Request $request)
    {
        $title = "Facilities List";
        $menu = "facilities";
        $faci = Facilities::latest()->get();

        $data = compact('title', 'menu', 'faci');
        return view('AdminPanel.facilities.list', $data);
    }
    public function add_facilities(Request $request)
    {
        $title = "Add Facility";
        $menu = "facilities";

        $data = compact('title', 'menu');
        return view('AdminPanel.facilities.form', $data);
    }
    public function facilities_added(Request $request)
    {
        $valid = $request->validate([
            'faci' => 'required|unique:facilities,faci',
            'color' => 'required',
        ]);
        // dd($request);
        $faci = new Facilities;
        $faci->faci = $request->faci;
        $faci->slug_faci = str_slug($request->faci);
        $faci->fa = $request->fa;
        $faci->color = $request->color;
        $faci->save();

        $request->session()->flash('msg', 'Added...');
        $request->session()->flash('msgst', 'success');

        return redirect(route('list_facilities'));
    }
    public function del_facilities(Request $request)
    {
        $valid = validator($request->route()->parameters(), [
            'id' => 'exists:facilities,id'
        ])->validate();
        $id = $request->route()->parameter('id');

        if ($valid) {
            $faci = Facilities::findorfail($id);
            $faci->delete();
        }

        $request->session()->flash('msg', 'Deleted...');
        $request->session()->flash('msgst', 'danger');

        return redirect(route('list_facilities'));
    }
    public function edit_facilities(Request $request)
    {
        $valid = validator($request->route()->parameters(), [
            'id' => 'exists:facilities,id'
        ])->validate();
        $id = $request->route()->parameter('id');

        if ($valid) {
            $faci = Facilities::findorfail($id);
        }

        $title = "Edit Facility";
        $menu = "facilities";

        $data = compact('title', 'menu', 'faci');
        return view('AdminPanel.facilities.form', $data);
    }
    public function facilities_edited(Request $request)
    {
        $valid = validator($request->route()->parameters(), [
            'id' => 'exists:facilities,id'
        ])->validate();
        $id = $request->route()->parameter('id');
        $request->validate([
            'faci' => 'required|unique:facilities,faci,' . $id,
            'color' => 'required',
        ]);
        $faci = Facilities::findorfail($id);
        $faci->faci = $request->faci;
        $faci->slug_faci = str_slug($request->faci);
        $faci->fa = $request->fa;
        $faci->color = $request->color;
        $faci->save();

        $request->session()->flash('msg', 'Edited...');
        $request->session()->flash('msgst', 'success');

        return redirect(route('list_facilities'));
    }
    //Facilities ends

    //Properties starts
    public function list_properties(Request $request)
    {
        $title = "Properties List";
        $menu = "properties";
        $pro = Property::with('Cate', 'City')->latest()->get();

        $data = compact('title', 'menu', 'pro');
        return view('AdminPanel.properties.list', $data);
    }
    public function add_properties(Request $request)
    {
        $title = "Add Property";
        $menu = "properties";
        $city = City::select('id', 'city')->where('status', '=', '1')->get();
        $cate = Category::select('id', 'name')->get();
        $faci = Facilities::select('*')->get();

        $data = compact('title', 'menu', 'city', 'cate', 'faci');
        return view('AdminPanel.properties.form', $data);
    }
    public function properties_added(Request $request)
    {
        $valid = $request->validate([
            'title' => 'required',
            'price' => 'required|numeric|min:0|max:999999999',
            'purpose' => 'required',
            'category' => 'required',
            'image' => 'required|mimes:png,jpg',
            'fe_image' => 'required|mimes:png,jpg',
            'floorplan' => 'mimes:png,jpg',
            'rooms' => 'required|numeric',
            'bathrooms' => 'required|numeric',
            'city' => 'required',
            'address' => 'required|max:191',
            'cont_ph' => 'required|min:9|max:11',
            'cont_em' => 'required|email',
            'area' => 'required|numeric',
            // 'description' => 'string',
        ]);
        $pro = new Property;
        $pro->title = $request->title;
        $pro->title_slug = str_slug($request->title);
        $pro->price = $request->price;
        $pro->purpose = $request->purpose;
        $pro->category = $request->category;
        $pro->city = $request->city;
        $pro->rooms = $request->rooms;
        $pro->bathrooms = $request->bathrooms;
        $pro->address = $request->address;
        $pro->cont_ph = $request->cont_ph;
        $pro->cont_em = $request->cont_em;
        $pro->faci = $request->faci ? json_encode($request->faci, true) : null;
        $pro->featured = $request->featured ? true : false;
        $pro->public = $request->public ? true : false;
        $pro->area = $request->area ? $request->area : null;
        $pro->description = $request->description ? $request->description : null;
        $pro->video = $request->video ? $request->video : null;
        $pro->map = $request->map ? $request->map : null;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $iname = date('Ym') . '-' . rand() . '.' . $image->extension();
            $store = $image->storeAs('public/property', $iname);
            if ($store) {
                $pro->image = $iname;
            }
        }
        if ($request->hasFile('fe_image')) {
            $image = $request->file('fe_image');
            $iname = date('Ym') . '-' . rand() . '.' . $image->extension();
            $store = $image->storeAs('public/property', $iname);
            if ($store) {
                $pro->fe_image = $iname;
            }
        }
        if ($request->hasFile('floorplan')) {
            $image = $request->file('floorplan');
            $iname = date('Ym') . '-' . rand() . '.' . $image->extension();
            $store = $image->storeAs('public/property', $iname);
            if ($store) {
                $pro->floorplan = $iname;
            }
        }
        $pro->save();


        $request->session()->flash('msg', 'Added...');
        $request->session()->flash('msgst', 'success');

        return redirect(route('list_properties'));
    }
    public function del_properties(Request $request)
    {
        $valid = validator($request->route()->parameters(), [
            'id' => 'exists:properties,id'
        ])->validate();
        $id = $request->route()->parameter('id');

        if ($valid) {
            $pro = Property::findorfail($id);
            if ($pro->fe_image) {
                Storage::delete('public/property/' . $pro->fe_image);
            }
            if ($pro->image) {
                Storage::delete('public/property/' . $pro->image);
            }
            if ($pro->floorplan) {
                Storage::delete('public/property/' . $pro->floorplan);
            }
            $gal = gallary::where('pro_id', $id)->get();
            if ($gal) {
                foreach ($gal as $img) {
                    Storage::delete('public/gallary/' . $id . '/' . $img->gal_image);
                }
                $gal = gallary::where('pro_id', $id);
                $gal->delete();
            }
            $reviews = Reviews::where('pro_id', $id);
            $reviews->delete();
            $pro->delete();
        }

        $request->session()->flash('msg', 'Deleted...');
        $request->session()->flash('msgst', 'danger');

        return redirect(route('list_properties'));
    }
    public function edit_properties(Request $request)
    {
        $valid = validator($request->route()->parameters(), [
            'id' => 'exists:properties,id'
        ])->validate();
        $id = $request->route()->parameter('id');

        if ($valid) {
            $pro = Property::findorfail($id);
            $pro_faci = json_decode($pro->faci, true);
        }

        $title = "Edit Property";
        $menu = "properties";

        $city = City::select('id', 'city')->where('status', '=', '1')->get();
        $cate = Category::select('id', 'name')->get();
        $faci = Facilities::select('*')->get();

        $data = compact('title', 'menu', 'pro', 'pro_faci', 'city', 'cate', 'faci');
        return view('AdminPanel.properties.form', $data);
    }
    public function properties_edited(Request $request)
    {
        $valid = validator($request->route()->parameters(), [
            'id' => 'exists:properties,id'
        ])->validate();
        $id = $request->route()->parameter('id');
        $request->validate([
            'title' => 'required',
            'price' => 'required|numeric|min:0|max:999999999',
            'purpose' => 'required',
            'category' => 'required',
            'image' => 'mimes:png,jpg',
            'fe_image' => 'mimes:png,jpg',
            'floorplan' => 'mimes:png,jpg',
            'rooms' => 'required|numeric',
            'bathrooms' => 'required|numeric',
            'city' => 'required',
            'address' => 'required|max:191',
            'cont_ph' => 'required',
            'cont_em' => 'required|email',
            'area' => 'required|numeric',
            // 'description' => 'string',
        ]);
        $pro = Property::findorfail($id);
        $pro->title = $request->title;
        $pro->title_slug = str_slug($request->title);
        $pro->price = $request->price;
        $pro->purpose = $request->purpose;
        $pro->category = $request->category;
        $pro->city = $request->city;
        $pro->rooms = $request->rooms;
        $pro->bathrooms = $request->bathrooms;
        $pro->address = $request->address;
        $pro->cont_ph = $request->cont_ph;
        $pro->cont_em = $request->cont_em;
        $pro->faci = json_encode($request->faci, true);
        $pro->featured = $request->featured ? true : false;
        $pro->public = $request->public ? true : false;
        $pro->area = $request->area ? $request->area : null;
        $pro->description = $request->description ? $request->description : null;
        $pro->video = $request->video ? $request->video : null;
        $pro->map = $request->map ? $request->map : null;
        if ($request->hasFile('image')) {
            Storage::delete('public/property/' . $pro->image);
            $image = $request->file('image');
            $iname = date('Ym') . '-' . rand() . '.' . $image->extension();
            $store = $image->storeAs('public/property', $iname);
            if ($store) {
                $pro->image = $iname;
            }
        }
        if ($request->hasFile('fe_image')) {
            Storage::delete('public/property/' . $pro->fe_image);
            $image = $request->file('fe_image');
            $iname = date('Ym') . '-' . rand() . '.' . $image->extension();
            $store = $image->storeAs('public/property', $iname);
            if ($store) {
                $pro->fe_image = $iname;
            }
        }
        if ($request->hasFile('floorplan')) {
            Storage::delete('public/property/' . $pro->floorplan);
            $image = $request->file('floorplan');
            $iname = date('Ym') . '-' . rand() . '.' . $image->extension();
            $store = $image->storeAs('public/property', $iname);
            if ($store) {
                $pro->floorplan = $iname;
            }
        }
        $pro->save();

        $request->session()->flash('msg', 'Edited...');
        $request->session()->flash('msgst', 'success');

        return redirect(route('list_properties'));
    }
    //Properties ends

    //Gallary starts
    public function list_gallary(Request $request)
    {
        $title = "Images Gallary";
        $menu = "gallary";

        $gal = gallary::with('Property')->latest()->get();

        $data = compact('title', 'menu', 'gal');
        return view('AdminPanel.gallary.list', $data);
    }
    public function get_gallary(Request $request)
    {
        $title = "Images Gallary";
        $menu = "gallary";

        $valid = validator($request->route()->parameters(), [
            'id' => 'exists:properties,id'
        ])->validate();
        $id = $request->route()->parameter('id');
        $pro = Property::where('id', $id)->first();
        $gal = gallary::with('Property')->where('pro_id', '=', $id)->get();

        $data = compact('title', 'menu', 'pro', 'gal', 'id');
        return view('AdminPanel.gallary.list', $data);
    }
    public function set_gallary(Request $request)
    {
        $request->validate([
            'gallary[]' => 'image|mimes:png,jpg'
        ]);

        $valid = validator($request->route()->parameters(), [
            'id' => 'exists:properties,id'
        ])->validate();
        $id = $request->route()->parameter('id');
        $images = $request->file('gallary');

        foreach ($images as $img) {
            $image = $img;
            $gal = new gallary;
            $gal->pro_id = $id;
            $iname = date('Ym') . '-' . rand() . '.' . $image->extension();
            $store = $image->storeAs('public/gallary/' . $id . '/', $iname);
            if ($store) {
                $gal->gal_image = $iname;
            }
            $gal->save();
        }

        return redirect(route('get_gallary', $id));
    }
    public function del_gallary(Request $request)
    {
        $valid = validator($request->route()->parameters(), [
            'id' => 'exists:properties,id',
            'gid' => 'exists:gallaries,id'
        ])->validate();
        $id = $request->route()->parameter('id');
        $gid = $request->route()->parameter('gid');

        if ($valid) {
            $gal = gallary::findorfail($gid);
            if ($gal->gal_image) {
                Storage::delete('public/gallary/' . $id . '/' . $gal->gal_image);
            }
            $gal->delete();
        }

        $request->session()->flash('msg', 'Deleted...');
        $request->session()->flash('msgst', 'danger');

        // return redirect(route('get_gallary', $id));
        return redirect()->back();
    }
    //Gallary ends

    //Reviews starts
    public function list_reviews(Request $request)
    {
        $title = "Reviews List";
        $menu = "reviews";

        $reviews = Reviews::with('Users', 'Property')
            // ->where('pro_id', $id)
            ->latest()
            ->get();
        // dd($reviews->toArray());

        $data = compact('title', 'menu', 'reviews');
        return view('AdminPanel.reviews.list', $data);
    }
    public function get_reviews(Request $request)
    {
        $title = "Reviews List";
        $menu = "reviews";

        $valid = validator($request->route()->parameters(), [
            'id' => 'exists:properties,id'
        ])->validate();
        $id = $request->route()->parameter('id');
        $pro = Property::where('id', $id)->first();
        $reviews = Reviews::with('Users', 'Property')
            ->where('pro_id', $id)
            ->latest()
            ->get();
        // dd($reviews);

        $data = compact('title', 'menu', 'pro', 'reviews', 'id');
        return view('AdminPanel.reviews.list', $data);
    }
    public function del_reviews(Request $request)
    {
        $valid = validator($request->route()->parameters(), [
            // 'id' => 'exists:properties,id',
            'rid' => 'exists:reviews,id'
        ])->validate();
        // $id = $request->route()->parameter('id');
        $rid = $request->route()->parameter('rid');

        if ($valid) {
            $rev = Reviews::findorfail($rid);
            $rev->delete();
        }

        $request->session()->flash('msg', 'Deleted...');
        $request->session()->flash('msgst', 'danger');

        // return redirect(route('get_reviews', $id));
        return redirect()->back();
    }
    //Reviews ends

    //Users starts
    public function list_users(Request $request)
    {
        $title = "Users List";
        $menu = "users";
        $user = $request->session()->get('AdminUser');
        $usersData = User::with('Data')->where('type', '!=', 'R')->latest()->get()->except($user['id']);

        $data = compact('title', 'menu', 'usersData');
        return view('AdminPanel.users.list', $data);
    }
    public function del_users(Request $request)
    {
        $valid = validator($request->route()->parameters(), [
            'id' => 'exists:users,id'
        ])->validate();
        $id = $request->route()->parameter('id');

        if ($valid) {
            $usersData = User::findorfail($id);
            $user_data = UserData::findorfail($id);
            Storage::delete('public/userdata/' . $user_data->image);
            $user_reviews = Reviews::where('u_id', $id);
            $user_reviews->delete();
            $usersData->delete();
            $user_data->delete();
        }

        $request->session()->flash('msg', 'Deleted...');
        $request->session()->flash('msgst', 'danger');

        return redirect(route('list_users'));
    }
    public function type_users(Request $request)
    {
        if ($request->ajax()) {
            $valid = validator($request->all(), [
                'id' => 'exists:users,id'
            ])->validate();

            $id = $request->id;
            $typ = $request->typ;

            if ($valid) {
                $usersData = User::findorfail($id);
                $usersData->type = $typ;
                $res = $usersData->save();

                if ($res) {
                    return json_encode(array('message' => 'Account type Changed...', 'status' => true));
                } else {
                    return json_encode(array('message' => 'Account type Changing failed', 'status' => false));
                }
            }
        }
    }
    //Users ends

    //Chng Password Starts
    public function chng_password(Request $request)
    {
        $title = "Change Password";
        $menu = "chng_password";

        $data = compact('title', 'menu');
        return view('AdminPanel.chng_password.form', $data);
    }
    public function save_password(Request $request)
    {
        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|confirmed'
        ]);
        $id = $request->session()->get('AdminUser')['id'];
        $user = User::findOrFail($id);

        $user->password = Hash::make($request->new_password);
        $user->save();
        $request->session()->forget('AdminUser');

        return redirect()->back();
    }
    //Chng Password Ends
}
