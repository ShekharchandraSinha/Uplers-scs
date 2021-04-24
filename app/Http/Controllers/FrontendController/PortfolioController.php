<?php

namespace App\Http\Controllers\FrontendController;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Statics\DummyJsonData;
use App\Models\Esp;
use App\Models\Framework;
use App\Models\Pms;
use App\Models\Portfolio;
use App\Models\PortfolioGallery;
use Illuminate\Http\Request;
use Spiritix\Html2Pdf\Converter;
use Spiritix\Html2Pdf\Input\StringInput;
use Spiritix\Html2Pdf\Output\DownloadOutput;

class PortfolioController extends Controller
{
    public function index(Request $request, $portfolioSlug, $templateId = null)
    {

        $hiddenPdf  = true;
        $hiddenLayout  = true;
        if ($portfolioSlug == 'dummy') {
            $portfolio = new Portfolio();
            $portfolio->name = 'John Doe';
            $portfolio->template_id = $templateId ?? 1;
            $portfolio->designation = 'Designation';
            $portfolio->skill_level = 'Skill Level';
            $portfolio->section_data = DummyJsonData::sectionData();
            $galleryImages = collect();
            for ($i = 0; $i < 6; $i++) {
                $gallery = new PortfolioGallery();
                $gallery->image_hash = 'dummy/image-placeholder.png';
                $galleryImages->push($gallery);
            }
            $portfolio['galleryImages'] = collect($galleryImages);
        } else {
            // $hiddenLayout = false;
            $hiddenPdf = false;
            $portfolio = Portfolio::with('galleryImages')->where(['slug' => $portfolioSlug, 'confirmed' => true, 'active' => true])->first();
        }

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
                        $esps = Esp::whereIn('id', $parse->model)->get();
                    } else if ($parse->value == 'pms') {
                        $pms = Pms::whereIn('id', $parse->model)->get();
                    } else if ($parse->value == 'frameworks') {
                        $frameworks = Framework::whereIn('id', $parse->model)->get();
                    }
                }
            }

            // generate new mapping
            foreach ($parsedJson as $key => $value) {
                $type = (isset($value->type)) ? $value->type : 'default';
                $sectionData[$value->value] = $value;
            }

            $highlightSection = "n_a";
            return view('frontend.template' . $portfolio->template_id, compact('hiddenPdf', 'hiddenLayout', 'portfolioSlug', 'highlightSection', 'portfolio', 'sectionData', 'esps', 'pms', 'frameworks'));
        }

        return abort(404);
    }

    public function downloadPdf(Request $request, $portfolioSlug, $pageHeight = 1000)
    {
        $hiddenPdf  = true;
        $hiddenLayout  = true;
        $portfolio = Portfolio::with('galleryImages')->where(['slug' => $portfolioSlug, 'confirmed' => true, 'active' => true])->first();
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
                        $esps = Esp::whereIn('id', $parse->model)->get();
                    } else if ($parse->value == 'pms') {
                        $pms = Pms::whereIn('id', $parse->model)->get();
                    } else if ($parse->value == 'frameworks') {
                        $frameworks = Framework::whereIn('id', $parse->model)->get();
                    }
                }
            }

            // generate new mapping
            foreach ($parsedJson as $key => $value) {
                $type = (isset($value->type)) ? $value->type : 'default';
                $sectionData[$value->value] = $value;
            }

            $highlightSection = "n_a";
            $html = view('frontend.template' . $portfolio->template_id, compact('hiddenPdf', 'hiddenLayout', 'portfolioSlug', 'highlightSection', 'portfolio', 'sectionData', 'esps', 'pms', 'frameworks'))->render();

            $input = new StringInput();
            $input->setHtml($html);

            $converter = new Converter($input, new DownloadOutput());
            $converter->setOptions([
                'printBackground' => true,
                'height' => $pageHeight,
                'width' => 1920,
                'pageRanges' => '-1',
            ]);

            $output = $converter->convert();
            $output->download($portfolio->slug . '.pdf');
        }
    }

    public function updateLayout(Request $request, $portfolioSlug)
    {
        $layout = $request->layout;
        $portfolio = Portfolio::where(['slug' => $portfolioSlug, 'confirmed' => true, 'active' => true])->first();
        if (isset($portfolio)) {
            $portfolio->layout = $layout;
            if ($portfolio->save()) {
                return response()->json(['success' => true]);
            }
            return response()->json(['success' => false]);
        }
        return response()->json(['success' => false]);
    }
}
