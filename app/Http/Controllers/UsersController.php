<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request; 
use Illuminate\Support\Facades\Auth;  
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Application;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Image;
use File;

class UsersController extends Controller  
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $my_id = Auth::user()->id;
        $uri = explode("/", url()->current());
        $segment_number = env('SEGMENT_NUM');
        if (count($uri) <= $segment_number) {
            $menu = $this->menu($my_id, 'home');
        } else {
            $menu = $this->menu($my_id, $uri[$segment_number]);
        }
        $data['head_title'] = $menu['head_title'];
        $data['menu_level_1'] = $menu['menu_level_1'];
        $data['menu_level_2'] = $menu['menu_level_2'];
        $data['menu_level_3'] = $menu['menu_level_3'];
        $data['menu_level_4'] = $menu['menu_level_4'];
        return view('users.users_index', $data);
    }
    public function create_form(Request $request)
    {
        return view('users.user_form');
    }
    public function show(Request $request)
    {
        $id = Crypt::decryptString($request->id);
        $detail_users = User::where('id', $id)->first();
        if ($detail_users) {
            return view('users.users_show', [
                'data' => $detail_users
            ]);
        } else {
            return response()->json([
                'success' => false,
                'msg_process' => 'Data tidak ditemukan'
            ]);
        }
    }
    public function submit_store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|unique:users,username',
            'full_name' => 'required',
            'call_name' => 'required',
            'email' => 'required|email',
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'signature' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $userData = [
            'username' => $request->username,
            'full_name' => $request->full_name,
            'call_name' => $request->call_name,
            'email' => $request->email,
            'password' => Hash::make($request->username),
            'gender_id' => $request->gender,
            'phone_num' => $request->phone_number,
            'created_by' => Auth::user()->id,
            'created_at' => Carbon::now(),
        ];

        if ($request->hasFile('avatar')) {
            $avatarFile = $this->uploadAvatar($request->file('avatar'), null);
            if ($avatarFile) {
                $userData['avatar'] = $avatarFile;
            }
        }

        if ($request->hasFile('signature')) {
            $signatureFile = $this->uploadSignature($request->file('signature'), null);
            if ($signatureFile) {
                $userData['signature'] = $signatureFile;
            }
        }

        $user = User::create($userData);

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil disimpan',
            'encrypted_id' => Crypt::encryptString($user->id)
        ]);
    }
    public function submit_edit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'full_name' => 'required',
            'call_name' => 'required',
            'email' => 'required|email',

            'avatar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'signature' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        if ($validator->fails()) {

            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);

        }

        $id = $request->user_id;

        $user = User::find($id);

        if (!$user) {

            return response()->json([
                'success' => false,
                'message' => 'User tidak ditemukan'
            ]);

        }

        $updateData = [
            'username' => $request->username,
            'full_name' => $request->full_name,
            'call_name' => $request->call_name,
            'email' => $request->email,
            'gender_id' => $request->gender,
            'phone_num' => $request->phone_number,
            'updated_by' => Auth::user()->id,
            'updated_at' => Carbon::now(),
        ];

        if ($request->hasFile('avatar')) {

            $avatarFile = $this->uploadAvatar(
                $request->file('avatar'),
                $id
            );

            if ($avatarFile) {
                $updateData['avatar'] = $avatarFile;
            }
        }

        if ($request->hasFile('signature')) {

            $signatureFile = $this->uploadSignature(
                $request->file('signature'),
                $id
            );

            if ($signatureFile) {
                $updateData['signature'] = $signatureFile;
            }
        }
        // User::find($id)->update($updateData);
        $user->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil diupdate'
        ]);
    }
    private function uploadAvatar($file, $userId)
    {
        try {
            $path = public_path('avatar');

            if (!File::isDirectory($path)) {
                File::makeDirectory($path, 0755, true);
            }

            $fileName = Carbon::now()->timestamp . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move($path, $fileName);

            return $fileName;
        } catch (\Exception $e) {
            return null;
        }
    }
    private function uploadSignature($file, $userId)
    {
        try {
            $path = public_path('signature');

            if (!File::isDirectory($path)) {
                File::makeDirectory($path, 0755, true);
            }

            $fileName = Carbon::now()->timestamp . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move($path, $fileName);

            return $fileName;
        } catch (\Exception $e) {
            return null;
        }
    }
    public function front_table(Request $request)
    {
        $search = $request->input('search_custom');
        $query = User::query();
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                    ->orWhere('full_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }
        $filteredCount = $query->count();
        $start = $request->input('start');
        $length = $request->input('length');
        $page = $start / $length + 1;

        $users = $query->latest()->paginate($length, ['*'], 'page', $page);
        $data = collect($users->items())->map(function ($user, $index) use ($users) {
            $encryptedId = Crypt::encryptString($user->id);
            $editButton = '<button onclick="edit_user(\'' . $encryptedId . '\')" class="btn btn-sm btn-success">
            <span class="svg-icon svg-icon-2">
                <i class="fa fa-edit"></i>
            </span>
            <span class="spinner-border spinner-border-sm align-middle ms-2" style="display: none;"></span>
            <span id="btn_txt_edit_user">Edit</span>
            </button>';
            $deleteButton = '<button onclick="deleteUser(\'' . $encryptedId . '\')" class="btn btn-sm btn-danger">
            <span class="svg-icon svg-icon-2">
                <i class="fa fa-trash"></i>
            </span>
            <span class="spinner-border spinner-border-sm align-middle ms-2" style="display: none;"></span>
            <span id="btn_txt_delete_user">Delete</span>
            </button>';

            return [
                'no' => $users->firstItem() + $index,
                'id' => $encryptedId,
                'username' => $user->username,
                'full_name' => $user->full_name,
                'email' => $user->email,
                'action' => $editButton . ' ' . $deleteButton,
            ];
        });

        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => User::count(),
            'recordsFiltered' => $filteredCount,
            'data' => $data,
        ]);
    }
    public function get_menu(Request $request)
    {
        $page = $request->page ?? 1;
        $search = $request->search ?? '';
        $data = DB::table('t100_menus as a')
            ->when($search, function ($q) use ($search) {
                $q->where('a.menu_name', 'LIKE', "%{$search}%");
            })
            ->select('a.id', 'a.menu_name', 'a.menu')
            ->orderBy('a.menu_name')
            ->paginate(50, ['*'], 'page', $page);
        return response()->json([
            'results' => collect($data->items())->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->menu . ' - ' . $item->menu_name,
                ];
            }),
            'pagination' => [
                'more' => $data->hasMorePages()
            ]
        ]);
    }
    public function submit_add_menu(Request $request)
    {
        $user_id = crypt::decryptString($request->user_id);
        $menu_id = $request->menu_id;
        $dataUser = User::find($user_id);
        if (!$dataUser) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak ditemukan'
            ]);
        }
        $dataMenu = DB::table('t100_menus')->where('id', $menu_id)->first();
        if (!$dataMenu) {
            return response()->json([
                'success' => false,
                'message' => 'Menu tidak ditemukan'
            ]);
        }
        $existingMenu = DB::table('t100_user_menus')
            ->where('user_id', $user_id)
            ->where('menu_id', $menu_id)
            ->first();

        if ($existingMenu) {
            return response()->json([
                'success' => false,
                'message' => 'Menu sudah ditambahkan untuk user ini'
            ]);
        }
        $insertRows = [
            'user_id' => $user_id,
            'menu_id' => $dataMenu->id,
        ];
        DB::beginTransaction();
        try {
            DB::table('t100_user_menus')->insert($insertRows);
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Menu berhasil ditambahkan',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
    public function user_menu_table(Request $request)
    {
        $user_id = crypt::decryptString($request->user_id);
        $search = $request->input('search_custom');
        $totalCount = DB::table('t100_user_menus')
            ->where('user_id', $user_id)
            ->count();

        $query = DB::table('t100_user_menus as um')
            ->leftjoin('t100_menus as m', 'm.id', '=', 'um.menu_id')
            ->where('um.user_id', $user_id)
            ->select('m.id', 'm.menu_name', 'm.menu');

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('m.menu_name', 'like', "%{$search}%")
                    ->orWhere('m.menu', 'like', "%{$search}%");
            });
        }

        $filteredCount = $query->count();
        $start = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 10);

        if ($length <= 0)
            $length = 10;
        $page = (int) floor($start / $length) + 1;
        $users = $query->orderBy('m.menu_name', 'asc')->paginate($length, ['*'], 'page', $page);
        $data = collect($users->items())->map(function ($user, $index) use ($users) {
            $encryptedId = Crypt::encryptString($user->id);
            $deleteButton = '<button onclick="deleteMenu(\'' . $encryptedId . '\')" class="btn btn-sm btn-danger">
            <span class="svg-icon svg-icon-2">
                <i class="fa fa-trash"></i>
            </span>
            <span class="spinner-border spinner-border-sm align-middle ms-2" style="display: none;"></span>
            <span id="btn_txt_delete_user">Delete</span>
            </button>';

            return [
                'no' => $users->firstItem() + $index,
                'name' => $user->menu_name,
                'url' => url($user->menu),
                'action' => $deleteButton,
            ];
        });

        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $totalCount,
            'recordsFiltered' => $filteredCount,
            'data' => $data,
        ]);
    }
    public function delete_menu(Request $request)
    {
        try {
            $id = Crypt::decryptString($request->id);
            DB::table('t100_user_menus')->where('menu_id', $id)->delete();
            return response()->json([
                'success' => true,
                'message' => 'Menu berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus menu: ' . $e->getMessage()
            ]);
        }
    }
    public function delete_user(Request $request)
    {
        $encryptedId = $request->user_id;
        try {
            $id = Crypt::decryptString($encryptedId);
            User::destroy($id);
            DB::table('t100_user_menus')->where('user_id',$id)->delete();
            return response()->json([
                'success' => true,
                'message' => 'User berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus user: ' . $e->getMessage()
            ]);
        }
    }
}
