<?php

namespace App\Http\Controllers\Admin\WebsiteSetting;

use App\DataTables\Admin\WebsiteAboutDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\WebsiteAboutRequest;
use App\Repositories\LanguageRepository;
use App\Repositories\Admin\WebsiteAboutRepository;
use App\Repositories\SettingRepository;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WebsiteAboutController extends Controller
{
    protected $aboutRepository;
    protected $setting;

    public function __construct(WebsiteAboutRepository $aboutRepository, SettingRepository $setting)
    {
        $this->aboutRepository = $aboutRepository;
        $this->setting         = $setting;

    }

    public function index(WebsiteAboutDataTable $dataTable)
    {
        return $dataTable->render('admin.website.about.index');
    }

    public function create(Request $request, LanguageRepository $language): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Contracts\Foundation\Application
    {

        $lang        = $request->lang ?? app()->getLocale();
        $data        = [
            'lang'                 => $lang,
        ];
        return view('admin.website.about.create', $data);
    }

    public function store(WebsiteAboutRequest $request): \Illuminate\Http\JsonResponse
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
            $this->aboutRepository->store($request->all());
            Toastr::success(__('create_successful'));

            DB::commit();

            return response()->json([
                'success' => __('create_successful'),
                'route'   => route('abouts.index'),
            ]);
        }catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['error' => 'Something went wrong, please try again']);
        }
    }

    public function edit($id, LanguageRepository $language, Request $request): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Contracts\Foundation\Application
    {
        try {
            $testimonial = $this->aboutRepository->find($id);
            $lang        = $request->lang ?? app()->getLocale();
            $data        = [
                'lang'                 => $lang,
                'about_language'       => $this->aboutRepository->getByLang($id, $lang),
                'about'                => $testimonial,
            ];

            return view('admin.website.about.edit', $data);
        }catch (\Exception $e) {
            Toastr::error('Something went wrong, please try again');

            return back();
        }
    }

    public function update(WebsiteAboutRequest $request, $id): \Illuminate\Http\JsonResponse
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
            $this->aboutRepository->update($request->all(), $id);
            Toastr::success(__('update_successful'));
            DB::commit();

            return response()->json([
                'success' => __('update_successful'),
                'route'   => route('abouts.index'),
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
            $this->aboutRepository->destroy($id);
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
            $this->aboutRepository->status($request->all());
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

    public function imageUpdate(Request $request)
    {
        if (isDemoMode()) {
            Toastr::error(__('this_function_is_disabled_in_demo_server'));
            return back();
        }
        try {
            $this->setting->update($request);

            Toastr::success(__('update_successful'));
            DB::commit();

            return back();
        }catch (\Exception $e) {
            DB::rollBack();
            Toastr::error(__('Something went wrong, please try again'));

        }
    }
}
