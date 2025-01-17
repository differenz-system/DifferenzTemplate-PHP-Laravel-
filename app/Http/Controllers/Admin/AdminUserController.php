<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;

class AdminUserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return view('admin.pages.user.index');
    }


    public function user_list(Request $request)
    {
        $data = User::select([
            'ProfilePicture',
            'FirstName',
            'LastName',
            'Email',
            'DeviceType',
            'SocialType',
            'UserId'
        ])->where('Role', 1)->where('IsDelete', 0)->get();

        return Datatables::of($data)
            ->editColumn('ProfilePicture', function ($data) {
                if ($data->ProfilePicture == '' || $data->ProfilePicture == null) {
                    $image = url('storage/admin/images/profile_pic/user-not-found.png');
                } else {
                    if (file_exists(public_path('storage/web/images/profile/' . $data->ProfilePicture))) {
                        $image = url('storage/web/images/profile') . '/' . $data->ProfilePicture;
                    } else {
                        $image = url('storage/admin/images/profile_pic/user-not-found.png');
                    }
                }
                return $image;
            })
            ->editColumn('Name', function ($data) {
                return $data->FirstName . ' ' . $data->LastName;
            })
            ->editColumn('DeviceType', function ($data) {
                if ($data->DeviceType == 1) return 'IOS';
                if ($data->DeviceType == 2) return 'Android';
                if ($data->DeviceType == 3) return 'Website';
                if ($data->DeviceType == 4) return 'Admin';
                return 'No User';
            })
            ->editColumn('SocialType', function ($data) {
                if ($data->SocialType == 1) return 'Facebook';
                if ($data->SocialType == 2) return 'Google';
                return 'Normal';
            })
            ->addColumn('action', function ($data) {
                $btn = '<a title="Delete user" href="' . route('user.destroy', [$data->UserId]) . '" class="delete-link">' .
                    '<i class="fa fa-trash"></i>' .
                    '</a>';
                return $btn;
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    
    public function destroy($id)
    {
        $GetUser = User::where('UserId', $id)->where('IsDelete', 0)->first();
        if ($GetUser) {
            User::where('UserId', $id)->update(['IsDelete' => 1, 'updated_at' => Carbon::now()->format('Y-m-d H:i:s')]);
            return json_encode(array('flag' => true));
        } else {
            return json_encode(array('flag' => false));
        }
    }
}
