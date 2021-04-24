<?php

namespace App\Http\Controllers\BackendController;

use App\Helpers;
use App\Http\Controllers\Statics\Countries;
use App\Http\Controllers\Controller;
use App\Http\Requests\PortfolioGalleryRequest;
use App\Http\Requests\PortfolioPreviewDetailsUpdate;
use App\Http\Requests\PortfolioProfilePhotoRequest;
use App\Http\Requests\PortfolioRequest;
use App\Models\Esp;
use App\Models\Framework;
use App\Models\Pms;
use App\Models\Portfolio;
use App\Models\PortfolioGallery;
use App\Models\PreviewPortfolio;
use App\Models\PreviewPortfolioGallery;
use Auth;
use Carbon\Carbon;
use File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Str;

class PortfolioController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('backend.portfolio.index');
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


        $modelBaseQuery = new Portfolio();

        if ($searchValue != '' && isset($searchValue)) {
            $modelBaseQuery = $modelBaseQuery->where('portfolios.name', 'like', '%' . $searchValue . '%')
                ->orWhere('portfolios.designation', 'like', '%' . $searchValue . '%')
                ->orWhere('portfolios.skill_level', 'like', '%' . $searchValue . '%');
        }

        $totalRecords = $modelBaseQuery->get()->count();

        $dataList = $modelBaseQuery
            ->skip($start)->limit($rowPerPage)
            ->orderBy($columnName, $columnSortOrder)
            ->get();

        foreach ($dataList as $key => $data) {
            $itemInPageNumber = ($key + 1);
            $data['index'] = $itemInPageNumber + $start;
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
        return view('backend.portfolio.create');
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PortfolioRequest $request)
    {

        $firstName = $request->input('first-name');
        $lastName = $request->input('last-name');
        $randomDigit = Helpers::randomDigit(3);

        $newItem = new Portfolio();
        $newItem->first_name = $firstName;
        $newItem->last_name = $lastName;
        $newItem->name = $firstName . ' ' . $lastName;
        $newItem->email = $request->email;
        $newItem->mobile = $request->mobile;
        $newItem->slug = $this->generateAndCheckSlug($firstName, $lastName, $randomDigit);
        $newItem->active = true;

        if ($newItem->save()) {
            return redirect()->route('admin.portfolio.edit', $newItem->id)->with(['success' => 'Success in saving details.']);
        }
        return redirect()->back()->withErrors(['Error when saving, please try again.']);
    }

    public function generateAndCheckSlug($firstName, $lastName, $randomDigit)
    {
        $slug =  Str::slug($firstName . ' ' . $lastName . ' ' . $randomDigit);

        $checkSlugUniqueness = Portfolio::where('slug', $slug)->first();
        if (isset($checkSlugUniqueness)) {
            $randomDigit = Helpers::randomDigit(3);
            return $this->generateAndCheckSlug($firstName, $lastName, $randomDigit);
        }

        return $slug;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $availableTemplates = ["Template 1" => "1",];
        if (Auth::user()->id) {
            $availableTemplates["Template 2"] = "2";
        }

        $portfolio = Portfolio::with('galleryImages')->find($id);
        if (isset($portfolio)) {

            // create copy of portfolio;
            $portfolioCopy = new PreviewPortfolio();
            $portfolioCopy->temp_version_id = time();
            $portfolioCopy->portfolio_id = $portfolio->id;
            $portfolioCopy->profile_photo = $portfolio->profile_photo;
            $portfolioCopy->first_name = $portfolio->first_name;
            $portfolioCopy->last_name = $portfolio->last_name;
            $portfolioCopy->name = $portfolio->name;
            $portfolioCopy->email = $portfolio->email;
            $portfolioCopy->mobile = $portfolio->mobile;
            $portfolioCopy->slug = $portfolio->slug;
            $portfolioCopy->template_id = $portfolio->template_id;
            $portfolioCopy->designation = $portfolio->designation;
            $portfolioCopy->skill_level = $portfolio->skill_level;
            $portfolioCopy->section_data = $portfolio->section_data;
            $portfolioCopy->active = $portfolio->active;
            $portfolioCopy->save();

            // create copy of portfolio gallery
            foreach ($portfolio->galleryImages as $key => $image) {
                $portfolioGalleryCopy = new PreviewPortfolioGallery();
                $portfolioGalleryCopy->source_id = $image->id;
                $portfolioGalleryCopy->temp_version_id = $portfolioCopy->temp_version_id;
                $portfolioGalleryCopy->image_hash = $image->image_hash;
                $portfolioGalleryCopy->save();
            }

            $portfolioCopy = PreviewPortfolio::with('galleryImages')->where('temp_version_id', $portfolioCopy->temp_version_id)->first();
            $portfolioCopy['confirmed'] = $portfolio->confirmed;

            $countries = Countries::countries();
            $espModel = collect();
            $pmsModel = collect();
            $frameworkModel = collect();

            if (isset($portfolioCopy->section_data) && $portfolioCopy->section_data != "") {
                $parsedJson = json_decode($portfolioCopy->section_data);
                foreach ($parsedJson as $parse) {
                    if ($parse->value == 'esp') {
                        $espModel = Esp::whereIn('id', $parse->model)->orWhere('active', true)->get();
                    } else if ($parse->value == 'pms') {
                        $pmsModel = Pms::whereIn('id', $parse->model)->orWhere('active', true)->get();
                    } else if ($parse->value == 'frameworks') {
                        $frameworkModel = Framework::whereIn('id', $parse->model)->orWhere('active', true)->get();
                    }
                }
            } else {
                $espModel = Esp::where('active', true)->get();
                $pmsModel = Pms::where('active', true)->get();
                $frameworkModel = Framework::where('active', true)->get();
            }

            return view('backend.portfolio.edit', compact('portfolioCopy', 'availableTemplates', 'countries', 'espModel', 'pmsModel', 'frameworkModel'));
        }
        return redirect()->route('admin.portfolio.index')->withErrors(['Portfolio not found.']);
    }

    /**
     * Check validity of url slug
     */
    public function editSlugCheck(Request $request, $id)
    {
        $valid = Validator::make($request->only('slug'), [
            'slug' => 'required|unique:portfolios,slug,' . $id,
        ], [
            'slug.required' => 'Url slug cannot be empty',
            'slug.unique' => 'This slug is already in use.',

        ]);

        if (!$valid->fails()) {
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false, 'error' => $valid->errors()->first()]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(PortfolioRequest $request, $portfolioId)
    {
        $portfolio = Portfolio::find($portfolioId);
        if (isset($portfolio)) {

            // Save new images
            if (isset($request->to_erase_gallery_image_hashes) && $request->to_erase_gallery_image_hashes != "" && sizeof($request->to_erase_gallery_image_hashes) > 0) {
                $imageHashes = $request->to_erase_gallery_image_hashes;

                // remove from db if dont exist in hashes array
                PortfolioGallery::where('portfolio_id', $portfolioId)->whereIn('image_hash', $imageHashes)->delete();

                foreach ($imageHashes as $key => $hash) {
                    Storage::disk('project_public')->delete('portfolio-gallery/' . $hash);
                }
            }

            if (isset($request->gallery_image_hashes) && $request->gallery_image_hashes != "" && sizeof($request->gallery_image_hashes) > 0) {
                $imageHashes = $request->gallery_image_hashes;

                foreach ($imageHashes as $key => $image) {
                    $galleryImageCheck = PortfolioGallery::where(['portfolio_id' => $portfolioId, 'image_hash' => $image])->first();
                    if (!isset($galleryImageCheck)) {
                        $newGallery = new PortfolioGallery();
                        $newGallery->portfolio_id = $portfolioId;
                        $newGallery->image_hash = $image;
                        $newGallery->save();
                    }
                }
            }


            // Update Json
            $firstName = $request->input('first-name');
            $lastName = $request->input('last-name');
            $portfolio->profile_photo = (isset($request->profile_image_hash) && $request->profile_image_hash != '') ? $request->profile_image_hash : null;
            $portfolio->first_name = $firstName;
            $portfolio->last_name = $lastName;
            $portfolio->name = $firstName . ' ' . $lastName;
            $portfolio->email = $request->email;
            $portfolio->mobile = $request->mobile;
            $portfolio->slug = $request->slug;

            $portfolio->template_id = $request->template;
            $portfolio->designation = $request->designation;
            $portfolio->skill_level = $request->skill_level;
            $portfolio->section_data = $request->section_data;
            $portfolio->active = $request->has('active');

            if (!$portfolio->confirmed) {
                $portfolio->confirmed = true;
                $portfolio->confirmed_by = Auth::user()->id;
                $portfolio->confirmed_at = Carbon::now();
            }

            $portfolioCopy = PreviewPortfolio::where('portfolio_id', $portfolioId)->first();

            // Flush preview instance from the db
            PreviewPortfolioGallery::where('temp_version_id', $portfolioCopy->temp_version_id)->delete();
            PreviewPortfolio::where('portfolio_id', $portfolioId)->delete();

            if ($portfolio->save()) {
                return redirect()->back()->with(['success' => 'Success in saving details.']);
            }


            return redirect()->route('admin.portfolio.edit', $portfolioId)->withErrors(['Saving failed, please try again.']);
        }

        return redirect()->route('admin.portfolio.index')->withErrors(['Portfolio not found.']);
    }

    /*
    * Test and preview
    */
    public function portfolioPreview(Request $request, $previewId, $highlightSection = "n_a")
    {
        $hiddenPdf = true;
        $hiddenLayout = true;
        $portfolio = PreviewPortfolio::with('galleryImages')->where('temp_version_id', $previewId)->first();
        if (isset($portfolio)) {
            $esps = collect();
            $pms = collect();
            $frameworks = collect();

            $sectionData = [];
            $parsedJson = [];
            if (isset($portfolio->section_data) && $portfolio->section_data != "") {
                $parsedJson = json_decode($portfolio->section_data);
                foreach ($parsedJson as $parse) {
                    if ($parse->value == 'esp') {
                        $esps = Esp::whereIn('id', $parse->model)->where('active', true)->get();
                    } else if ($parse->value == 'pms') {
                        $pms = Pms::whereIn('id', $parse->model)->where('active', true)->get();
                    } else if ($parse->value == 'frameworks') {
                        $frameworks = Framework::whereIn('id', $parse->model)->where('active', true)->get();
                    }
                }
            }

            foreach ($parsedJson as $key => $value) {
                $type = (isset($value->type)) ? $value->type : 'default';
                $sectionData[$value->value] = $value;
            }

            $portfolioSlug = 'dummy';
            return view('frontend.template' . $portfolio->template_id, compact('hiddenPdf', 'hiddenLayout', 'portfolioSlug', 'highlightSection', 'portfolio', 'sectionData', 'esps', 'pms', 'frameworks'));
        } else {
            return abort(404);
        }
    }

    public function portfolioPreviewStore(PortfolioPreviewDetailsUpdate $request, $previewId)
    {
        $firstName = $request->input('first-name');
        $lastName = $request->input('last-name');

        $item = PreviewPortfolio::with('galleryImages')->where('temp_version_id', $previewId)->first();
        if (isset($item)) {
            $item->profile_photo = (isset($request->profile_image_hash) && $request->profile_image_hash != '') ? $request->profile_image_hash : null;
            $item->first_name = $firstName;
            $item->last_name = $lastName;
            $item->name = $firstName . ' ' . $lastName;
            $item->email = $request->email;
            $item->mobile = $request->mobile;

            $item->template_id = $request->template;
            $item->designation = $request->designation;
            $item->skill_level = $request->skill_level;
            $item->section_data = $request->section_data;

            if ($item->save()) {

                if (isset($request->to_erase_gallery_image_hashes) && $request->to_erase_gallery_image_hashes != "" && sizeof($request->to_erase_gallery_image_hashes) > 0) {
                    $imageHashes = $request->to_erase_gallery_image_hashes;

                    // remove from db if dont exist in hashes array
                    PreviewPortfolioGallery::where('temp_version_id', $previewId)->whereIn('image_hash', $imageHashes)->delete();

                    // Delete from disk
                    foreach ($imageHashes as $key => $value) {
                        Storage::disk('project_public')->delete('portfolio-profile/' . $value);
                    }
                }

                // Save new gallery images
                if (isset($request->gallery_image_hashes) && $request->gallery_image_hashes != "" && sizeof($request->gallery_image_hashes) > 0) {
                    $imageHashes = $request->gallery_image_hashes;

                    // add if from image hashes if dont exist in db
                    foreach ($imageHashes as $key => $image) {
                        $galleryImageCheck = PreviewPortfolioGallery::where(['temp_version_id' => $previewId, 'image_hash' => $image])->first();
                        if (!isset($galleryImageCheck)) {
                            $newGallery = new PreviewPortfolioGallery();
                            $newGallery->temp_version_id = $previewId;
                            $newGallery->image_hash = $image;
                            $newGallery->save();
                        }
                    }
                }

                return response(['success' => true]);
            }

            return response(['success' => false]);
        }

        return response(['success' => false]);
    }


    /**
     * Handle profile uploads
     */
    public function profilePhotoUpload(PortfolioProfilePhotoRequest $request)
    {
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            if ($file->isValid()) {
                $file->store('portfolio-profile', 'project_public');
                return response()->json(['status' => 'success', 'fileHash' => $file->hashName()]);
            }
        }
        return response()->json(['status' => 'failure']);
    }

    /**
     * Handle gallery uploads
     */
    public function galleryUpload(PortfolioGalleryRequest $request)
    {
        $fileHashes = [];
        if ($request->hasFile('file')) {
            foreach ($request->file('file') as $index => $fileObject) {
                $file = $request->file[$index];
                if ($file->isValid()) {
                    // Store via laravel storage api
                    $file->store('portfolio-gallery', 'project_public');
                    array_push($fileHashes, $fileObject->hashName());
                }
            }
        }

        return response()->json(['status' => 'success', 'fileHashes' => $fileHashes]);
    }
}
