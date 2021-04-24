<?php

namespace App\Http\Controllers\BackendController;

use App\Http\Controllers\Controller;
use App\Http\Requests\PmsRequest;
use App\Models\Pms;
use App\Helpers;
use Illuminate\Http\Request;

class PmsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('backend.pms.index');
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


        $modelBaseQuery = new Pms();

        if ($searchValue != '' && isset($searchValue)) {
            $modelBaseQuery = $modelBaseQuery->where(function ($q) use ($searchValue) {
                $q->where('title', 'like', '%' . $searchValue . '%');
            });
        }

        $totalRecords = $modelBaseQuery->get()->count();

        $dataList = $modelBaseQuery
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
        return view('backend.pms.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PmsRequest $request)
    {
        if ($request->hasFile('icon') && $request->file('icon')->isValid()) {

            $icon = $request->file('icon');
            $iconHash = $request->icon->hashName();

            Helpers::resizeImage($icon, $iconHash, '/pms');

            // perform storage op
            $newItem = new Pms();
            $newItem->icon = $iconHash;
            $newItem->title = $request->title;
            $newItem->active = $request->has('active');

            if ($newItem->save()) {
                return redirect()->route('admin.pms.index')->with(['success' => 'Success in saving item.']);
            }
            return redirect()->back()->withErrors(['Error when saving, please try again.'])->withInput();
        }
        return redirect()->back()->withErrors(['Invalid file was uploaded.']);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $item = Pms::find($id);
        if (isset($item)) {
            return view('backend.pms.edit', compact('item'));
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
    public function update(PmsRequest $request, $id)
    {
        $item = Pms::find($id);
        if (isset($item)) {

            if ($request->hasFile('icon')) {
                if ($request->file('icon')->isValid()) {

                    $icon = $request->file('icon');
                    $iconHash = $request->icon->hashName();

                    Helpers::resizeImage($icon, $iconHash, '/pms');

                    $item->icon = $iconHash;
                } else {
                    return redirect()->back()->withErrors(['Invalid file was uploaded.'])->withInput();
                }
            }

            $item->title = $request->title;
            $item->active = $request->has('active');

            if ($item->save()) {
                return redirect()->route('admin.pms.index')->with(['success' => 'Success in updating item.']);
            }
            return redirect()->back()->withErrors(['Error when updating, please try again.'])->withInput();
        }
        return redirect()->route('admin.pms.index')->withErrors(['Item not found.']);
    }
}
