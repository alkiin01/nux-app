<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class MenuController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $my_id = Auth::user()->id;
        $uri = explode("/", url()->current());
        if (count($uri) < 5) {
            $menu = $this->menu($my_id, 'Setup');
        } else {
            $menu = $this->menu($my_id, $uri[4]);
        }
        $data['head_title'] = $menu['head_title'];
        $data['menu_level_1'] = $menu['menu_level_1'];
        $data['menu_level_2'] = $menu['menu_level_2'];
        $data['menu_level_3'] = $menu['menu_level_3'];
        $data['menu_level_4'] = $menu['menu_level_4'];
        return view('menu.data_menu', $data);
    }
    public function front_table(Request $request)
    {
        $search = $request->input('search_custom');
        $query = Menu::query();

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('sequence_id', 'like', "%{$search}%")
                    ->orWhere('level_menu_id', 'like', "%{$search}%")
                    ->orWhere('group_id', 'like', "%{$search}%")
                    ->orWhere('sub_group_id', 'like', "%{$search}%")
                    ->orWhere('menu', 'like', "%{$search}%")
                    ->orWhere('menu_name', 'like', "%{$search}%")
                    ->orWhere('icon', 'like', "%{$search}%");
            });
        }

        $filteredCount = $query->count();

        $start = $request->input('start', 0);
        $length = $request->input('length', 10);
        $page = max(1, intval($start / $length) + 1);

        $menus = $query->orderBy('group_id', 'asc')->paginate($length, ['*'], 'page', $page);

        $data = collect($menus->items())->map(function ($menu, $index) use ($menus) {
            $encryptedId = Crypt::encryptString($menu->id);
            $editButton = '<button onclick="edit_menu(\'' . $encryptedId . '\')" class="btn btn-sm btn-light-warning">
             <span id="svg_form_view_doc" class="svg-icon svg-icon-2" style="margin-left: -7px;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <path opacity="0.3" d="M10 4H21C21.6 4 22 4.4 22 5V7H10V4Z" fill="black"></path>
                                    <path opacity="0.3" d="M10.3 15.3L11 14.6L8.70002 12.3C8.30002 11.9 7.7 11.9 7.3 12.3C6.9 12.7 6.9 13.3 7.3 13.7L10.3 16.7C9.9 16.3 9.9 15.7 10.3 15.3Z" fill="black"></path><path d="M10.4 3.60001L12 6H21C21.6 6 22 6.4 22 7V19C22 19.6 21.6 20 21 20H3C2.4 20 2 19.6 2 19V4C2 3.4 2.4 3 3 3H9.20001C9.70001 3 10.2 3.20001 10.4 3.60001ZM11.7 16.7L16.7 11.7C17.1 11.3 17.1 10.7 16.7 10.3C16.3 9.89999 15.7 9.89999 15.3 10.3L11 14.6L8.70001 12.3C8.30001 11.9 7.69999 11.9 7.29999 12.3C6.89999 12.7 6.89999 13.3 7.29999 13.7L10.3 16.7C10.5 16.9 10.8 17 11 17C11.2 17 11.5 16.9 11.7 16.7Z" fill="black"></path>
                                </svg>
                                </span>
                                <span id="spinner_form_view_doc" class="spinner-border spinner-border-sm svg-icon svg-icon-1x" style="display: none;"></span>  
            </button>';
            $deleteButton = '<button onclick="confirmDelete(\'' . $encryptedId . '\')" class="btn btn-sm btn-light-danger">
            <span id="svg_form_delete_doc" class="svg-icon svg-icon-2" style="margin-left: -7px;">
                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1"> 
                                    <defs/>
                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <rect x="0" y="0" width="24" height="24"/>
                                        <path d="M6,8 L18,8 L17.106535,19.6150447 C17.04642,20.3965405 16.3947578,21 15.6109533,21 L8.38904671,21 C7.60524225,21 6.95358004,20.3965405 6.89346498,19.6150447 L6,8 Z M8,10 L8.45438229,14.0894406 L15.5517885,14.0339036 L16,10 L8,10 Z" fill="#000000" fill-rule="nonzero"/>
                                        <path d="M14,4.5 L14,3.5 C14,3.22385763 13.7761424,3 13.5,3 L10.5,3 C10.2238576,3 10,3.22385763 10,3.5 L10,4.5 L5.5,4.5 C5.22385763,4.5 5,4.72385763 5,5 L5,5.5 C5,5.77614237 5.22385763,6 5.5,6 L18.5,6 C18.7761424,6 19,5.77614237 19,5.5 L19,5 C19,4.72385763 18.7761424,4.5 18.5,4.5 L14,4.5 Z" fill="#000000" opacity="0.3"/>
                                    </g>
                                </svg>
                                </span>
                                <span id="spinner_form_delete_doc" class="spinner-border spinner-border-sm svg-icon svg-icon-1x" style="display: none;"></span>  
            </button>';
            return [
                'no' => $menus->firstItem() + $index,
                'id' => $encryptedId,
                'sequence_id' => $menu->sequence_id,
                'level_menu_id' => $menu->level_menu_id,
                'group_id' => $menu->group_id,
                'sub_group_id' => $menu->sub_group_id,
                'menu' => $menu->menu,
                'menu_name' => $menu->menu_name,
                'icon' => $menu->icon,
                'action' => $editButton . ' ' . $deleteButton,
            ];
        });

        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => Menu::count(),
            'recordsFiltered' => $filteredCount,
            'data' => $data,
        ]);
    }
    public function create_form()
    {
        return view('menu.create_menu');
    }
    public function edit_form(Request $request)
    {
        $r_id = Crypt::decryptString($request->id);
        $data = DB::table('t100_menus')
            ->where('id', $r_id)
            ->first();

        if (!$data) {
            return response()->json([
                'status' => false,
                'message' => 'Data tidak ditemukan'
            ], 404);
        }
        $group = DB::table('t100_menus')
            ->where('id', $data->group_id)
            ->first();
        $data->group_name = $group->menu_name ?? '';

        $subGroup = DB::table('t100_menus')
            ->where('id', $data->sub_group_id)
            ->first();
        $data->sub_group_name = $subGroup->menu_name ?? '';

        return view('menu.edit_menu', [
            'data' => $data
        ]);
    }
    public function get_groups(Request $request)
    {
        $search = $request->input('search', '');

        $groups = DB::table('t100_menus')
            ->select('group_id', 'menu_name')
            ->where('level_menu_id', 1)
            ->distinct()
            ->when(!empty($search), function ($q) use ($search) {
                $q->where('menu_name', 'like', "%{$search}%")
                    ->orWhere('group_id', 'like', "%{$search}%");
            })
            ->get();

        $results = $groups->map(function ($item) {
            return [
                'id' => $item->group_id,
                'text' => $item->group_id . ' - ' . $item->menu_name
            ];
        });

        return response()->json([
            'results' => $results,
            'pagination' => ['more' => false]
        ]);
    }
    public function get_sub_groups(Request $request)
    {
        $group_id = $request->input('group_id');
        $search = $request->input('search', '');

        $sub_groups = DB::table('t100_menus')
            ->whereIn('level_menu_id', [1, 2])
            ->select('sub_group_id', 'menu_name')
            ->distinct()
            ->where('group_id', $group_id)
            ->when(!empty($search), function ($q) use ($search) {
                $q->where('menu_name', 'like', "%{$search}%")
                    ->orWhere('group_id', 'like', "%{$search}%");
            })
            ->get();

        $results = $sub_groups->map(function ($item) {
            return [
                'id' => $item->sub_group_id . '~' . $item->menu_name,
                'text' => $item->sub_group_id . ' - ' . $item->menu_name
            ];
        });

        return response()->json([
            'results' => $results,
            'pagination' => ['more' => false]
        ]);

    }
    public function save_menu(Request $request)
    {
        $val = Validator::make($request->all(), [
            'sequence_id' => 'required',
            'level_id' => 'required',
            'group_id' => 'required',
            'sub_group_id' => 'required',
            'menu_name' => 'required|max:50|unique:t100_menus,menu_name',
            'menu_url' => 'required|max:50|unique:t100_menus,menu',
            'icon' => 'nullable'
        ]);
        if ($val->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation Failed',
                'errors' => $val->errors()
            ], 422);
        }
        try {
            $sub_group = explode('~', $request->sub_group_id);
            $id = DB::table('t100_menus')->max('id') + 1;
            DB::table('t100_menus')
                ->insert([
                    'id' => $id,
                    'sequence_id' => $request->sequence_id,
                    'level_menu_id' => $request->level_id,
                    'group_id' => $request->group_id,
                    'sub_group_id' => $sub_group[0],
                    'menu_name' => $request->menu_name,
                    'menu' => $request->menu_url,
                    'icon' => $request->icon ?? ''
                ]);
            return response()->json([
                'status' => true,
                'message' => 'Menu created successfully'
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }
    public function save_menu_edit(Request $request)
    {
        $id = (int) Crypt::decryptString($request->ref_doc);
        $val = Validator::make($request->all(), [
            'sequence_id' => 'required',
            'level_id' => 'required',
            'group_id' => 'required',
            'sub_group_id' => 'required',
            'menu_name' => ['required', 'max:50', Rule::unique('t100_menus', 'menu_name')->ignore($id)],
            'menu_url' => ['required', 'max:50', Rule::unique('t100_menus', 'menu')->ignore($id)],
            'icon' => 'nullable'
        ]);
        if ($val->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation Failed',
                'errors' => $val->errors()
            ], 422);
        }
        try {
            $sub_group = explode('~', $request->sub_group_id);
            DB::table('t100_menus')
                ->where('id', $id)
                ->update([
                    'sequence_id' => $request->sequence_id,
                    'level_menu_id' => $request->level_id,
                    'group_id' => $request->group_id,
                    'sub_group_id' => $sub_group[0],
                    'menu_name' => $request->menu_name,
                    'menu' => $request->menu_url,
                    'icon' => $request->icon ?? ''
                ]);
            return response()->json([
                'status' => true,
                'message' => 'Menu updated successfully'
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }
    public function delete_data_menu(Request $request)
    {
        $id = Crypt::decryptString($request->id);
        DB::table('t100_menus')
            ->where('id', $id)
            ->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'Delete successfully'
        ], 200);
    }
}
