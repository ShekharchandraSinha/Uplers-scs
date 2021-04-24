<?php

namespace App\Http\Controllers\BackendController;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Models\User;
use Helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('backend.user.index');
    }

    public function indexSearchPaginateSort(Request $request)
    {
        $draw = $request->input('draw');
        $start = $request->input('start');
        $rowPerPage = $request->input('length'); // Rows display per page
        $columnIndex = $request->input('order')[0]['column']; // Column index
        $columnName = $request->input('columns')[$columnIndex]['data']; // Column name
        $columnSortOrder = $request->input('order')[0]['dir']; // asc or desc
        $searchValue = $request->input('search')['value']; // Search value


        $userBaseQuery = new User();

        if ($searchValue != '' && isset($searchValue)) {
            $userBaseQuery = $userBaseQuery->where(function ($q) use ($searchValue) {
                $q->where('name', 'like', '%' . $searchValue . '%')
                    ->orWhere('email', 'like', '%' . $searchValue . '%')
                    ->orWhere('mobile', 'like', '%' . $searchValue . '%');
            });
        }

        $totalRecords = $userBaseQuery->get()->count();

        $dataList = $userBaseQuery
            ->orderBy($columnName, $columnSortOrder)
            ->skip($start)->limit($rowPerPage)
            ->get();

        foreach ($dataList as $key => $file) {
            $itemInPageNumber = ($key + 1);

            $file['index'] = $itemInPageNumber + $start;
        }


        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecords,
            "data" => $dataList
        );

        return response()->json($response);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('backend.user.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserRequest $request)
    {
        $firstName = $request->input('first-name');
        $lastName = $request->input('last-name');

        $newItem = new User();
        $newItem->first_name = $firstName;
        $newItem->last_name = $lastName;
        $newItem->name = $firstName . ' ' . $lastName;
        $newItem->email = $request->email;
        $newItem->mobile = $request->mobile;
        $newItem->password = bcrypt($request->password);
        $newItem->active = (isset($request->active)) ? $request->active : 0;

        if ($newItem->save()) {
            return redirect()->route('admin.user.index')->with(['success' => 'Success in saving item.']);
        }
        return redirect()->back()->withErrors(['Error when saving, please try again.'])->withInput();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $item = User::find($id);
        if (isset($item)) {
            return view('backend.user.edit', compact('item'));
        }
        return redirect()->back()->withErrors(['Item not found.']);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UserRequest $request, $id)
    {
        $firstName = $request->input('first-name');
        $lastName = $request->input('last-name');
        $item = User::find($id);
        if (isset($item)) {
            $item->first_name = $firstName;
            $item->last_name = $lastName;
            $item->name = $firstName . ' ' . $lastName;
            $item->email = $request->email;
            $item->mobile = $request->mobile;

            if (isset($request->password)) {
                $item->password = bcrypt($request->password);
            }
            $item->active = (isset($request->active)) ? $request->active : 0;

            if ($item->save()) {
                return redirect()->route('admin.user.index')->with(['success' => 'Success in updating item.']);
            }
            return redirect()->back()->withErrors(['Error when updating, please try again.'])->withInput();
        }
        return redirect()->route('admin.user.index')->withErrors(['Item not found.']);
    }
}
