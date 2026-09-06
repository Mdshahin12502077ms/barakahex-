<?php

namespace App\Http\Controllers\Admin\WebsiteSetting;

use App\DataTables\Admin\WebsiteFeaturedataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\WebsiteFeatureRequest;
use App\Repositories\LanguageRepository;
use App\Repositories\Admin\WebsiteFeatureRepository;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WebsiteFeatureController extends Controller
{
    protected $featureRepository;

    public function __construct(WebsiteFeatureRepository $featureRepository)
    {
        $this->featureRepository = $featureRepository;
    }

    public function index(WebsiteFeaturedataTable $dataTable)
    {
        return $dataTable->render('admin.website.feature.index');
    }

    public function create(Request $request, LanguageRepository $language): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Contracts\Foundation\Application
    {

        $lang        = $request->lang ?? app()->getLocale();
        $data        = [
            'lang'                 => $lang,
        ];
        return view('admin.website.feature.create', $data);
    }

    public function store(WebsiteFeatureRequest $request): \Illuminate\Http\JsonResponse
    {
        if (isDemoMode()) {
            $data = [
                'status' => 'danger',
                'error'  => __('this_function_is_disabled_in_demo_server'),
                'title'  => 'error',
            ];

            return response()->json($data);
        }

        DB::beginTransaction();
        try {
            $this->featureRepository->store($request->all());
            Toastr::success(__('create_successful'));

            DB::commit();

            return response()->json([
                'success' => __('create_successful'),
                'route'   => route('features.index'),
            ]);
        }catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['error' => 'Something went wrong, please try again']);
        }
    }

    public function edit($id, LanguageRepository $language, Request $request): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Contracts\Foundation\Application
    {
        try {
            $feature = $this->featureRepository->find($id);
            $lang        = $request->lang ?? app()->getLocale();
            $data        = [
                'lang'                 => $lang,
                'feature_language' => $this->featureRepository->getByLang($id, $lang),
                'feature'          => $feature,
            ];

            return view('admin.website.feature.edit', $data);
        }catch (\Exception $e) {
            Toastr::error('Something went wrong, please try again');

            return back();
        }
    }

    public function update(WebsiteFeatureRequest $request, $id): \Illuminate\Http\JsonResponse
    {
        if (isDemoMode()) {
            $data = [
                'status' => 'danger',
                'error'  => __('this_function_is_disabled_in_demo_server'),
                'title'  => 'error',
            ];

            return response()->json($data);
        }

        DB::beginTransaction();
        try {
            $this->featureRepository->update($request->all(), $id);
            Toastr::success(__('update_successful'));
            DB::commit();

            return response()->json([
                'success' => __('update_successful'),
                'route'   => route('features.index'),
            ]);
        }catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['error' => 'Something went wrong, please try again']);
        }
    }

    public function destroy($id): \Illuminate\Http\JsonResponse
    {
        if (isDemoMode()) {
            $data = [
                'status'  => 'danger',
                'message' => __('this_function_is_disabled_in_demo_server'),
                'title'   => 'error',
            ];

            return response()->json($data);
        }
        try {
            $this->featureRepository->destroy($id);
            Toastr::success(__('delete_successful'));
            $data = [
                'status'  => 'success',
                'message' => __('delete_successful'),
                'title'   => __('success'),
            ];

            return response()->json($data);
        }catch (\Exception $e) {
            $data = [
                'status'  => 'danger',
                'message' => 'Something went wrong, please try again',
                'title'   => __('error'),
            ];

            return response()->json($data);
        }
    }

    public function statusChange(Request $request): \Illuminate\Http\JsonResponse
    {
        if (isDemoMode()) {
            $data = [
                'status'  => 'danger',
                'message' => __('this_function_is_disabled_in_demo_server'),
                'title'   => 'error',
            ];

            return response()->json($data);
        }
        try {
            $this->featureRepository->status($request->all());
            $data = [
                'status'  => 200,
                'message' => __('update_successful'),
                'title'   => 'success',
            ];

            return response()->json($data);
        }catch (\Exception $e) {
            $data = [
                'status'  => 400,
                'message' => 'Something went wrong, please try again',
                'title'   => 'error',
            ];

            return response()->json($data);
        }
    }
}
