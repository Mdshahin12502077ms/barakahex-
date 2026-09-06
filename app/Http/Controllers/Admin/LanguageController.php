<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\Admin\LanguageDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LanguageRequest;
use App\Repositories\LanguageRepository;
use Brian2694\Toastr\Facades\Toastr;
use Exception;
use App\Models\Setting;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LanguageController extends Controller
{
    protected $language;

    public function __construct(LanguageRepository $language)
    {
        $this->language = $language;
    }

    public function index(LanguageDataTable $dataTable)
    {
        try {
            $data = [
                'flags' => $this->language->flags(),
            ];

            return $dataTable->render('admin.language.all-language', $data);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function create(): View|Factory|string|Application
    {
        try {
            $flags = $this->language->flags();

            return view('admin.language.add-language', compact('flags'));
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function store(LanguageRequest $request): JsonResponse
    {
        if (isDemoMode()) {
            $data = [
                'status' => 'danger',
                'error' => __('this_function_is_disabled_in_demo_server'),
                'title' => 'error',
            ];

            return response()->json($data);
        }
        DB::beginTransaction();
        try {
            $this->language->store($request->all());

            cache()->forget('languages');
            DB::commit();

            return response()->json(['success' => __('create_successful')]);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json(['error' => $e->getMessage()]);
        }
    }

    public function show($id)
    {
        //
    }

    public function edit($id): JsonResponse
    {
        try {
            $language = $this->language->get($id);
            $data = [
                'id' => $language->id,
                'name' => $language->name,
                'locale' => $language->locale,
                'flag' => $language->flag,
                'text_direction' => $language->text_direction == 'rtl',
                'status' => (bool) $language->status,
            ];



            return response()->json($data);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    public function update(LanguageRequest $request, $id): JsonResponse|RedirectResponse
    {
        if (isDemoMode()) {
            $data = [
                'status' => 'danger',
                'error' => __('this_function_is_disabled_in_demo_server'),
                'title' => 'error',
            ];

            return response()->json($data);
        }
        DB::beginTransaction();
        try {
            $this->language->update($request->all(), $id);
            cache()->forget('languages');
            DB::commit();

            return response()->json(['success' => __('update_successful')]);

        } catch (Exception $e) {
            DB::rollBack();

            return response()->json(['error' => $e->getMessage()]);
        }
    }

    public function destroy($id): JsonResponse
    {
        if (isDemoMode()) {
            $data = [
                'status' => 'danger',
                'message' => __('this_function_is_disabled_in_demo_server'),
                'title' => 'error',
            ];

            return response()->json($data);
        }
        try {
            $this->language->destroy($id);
            cache()->forget('languages');

            $data = [
                'status' => 'success',
                'message' => __('delete_successful'),
                'title' => __('success'),
            ];

            return response()->json($data);
        } catch (Exception $e) {
            $data = [
                'status' => 400,
                'message' => $e->getMessage(),
                'title' => 'error',
            ];

            return response()->json($data);
        }
    }

    public function statusChange(Request $request): JsonResponse
    {
        if (isDemoMode()) {
            $data = [
                'status' => 'danger',
                'message' => __('this_function_is_disabled_in_demo_server'),
                'title' => 'error',
            ];

            return response()->json($data);
        }
        try {
            $this->language->statusChange($request);
            cache()->forget('languages');

            $data = [
                'status' => 200,
                'message' => __('update_successful'),
                'title' => 'success',
            ];

            return response()->json($data);
        } catch (Exception $e) {
            $data = [
                'status' => 400,
                'message' => $e->getMessage(),
                'title' => 'error',
            ];

            return response()->json($data);
        }
    }

    public function directionChange(Request $request): JsonResponse
    {
        if (isDemoMode()) {
            $data = [
                'status' => 'danger',
                'message' => __('this_function_is_disabled_in_demo_server'),
                'title' => 'error',
            ];

            return response()->json($data);
        }
        try {
            $this->language->directionChange($request->all());
            $data = [
                'status' => 200,
                'message' => __('update_successful'),
                'title' => 'success',
            ];

            return response()->json($data);
        } catch (Exception $e) {
            $data = [
                'status' => 400,
                'message' => $e->getMessage(),
                'title' => 'error',
            ];

            return response()->json($data);
        }
    }

    public function translationPage(Request $request): View|Factory|RedirectResponse|Application
    {
        try {
            $language = $this->language->get($request->lang);
            $file = base_path('lang/' . $language->locale . '.json');
            if (!file_exists($file)) {
                $en_file = base_path('lang/en.json');
                copy($en_file, $file);
            }
            $translations = json_decode(file_get_contents($file), true);
            if ($request->q) {
                $translations = array_filter($translations, function ($key) use ($request) {
                    return str_contains($key, $request->q);
                }, ARRAY_FILTER_USE_KEY);
            }

            $data = [
                'language' => $language,
                'languages' => $this->language->activeLanguage(),
                'translations' => $translations,
                'search_query' => $request->q,
            ];

            return view('admin.language.translation', $data);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    // public function updateTranslations(Request $request, $language): JsonResponse
    // {
    //     try {
    //         $translations     = json_decode($request->translations, true);
    //         $keys             = array_column(json_decode($request->keys, true), 'value');
    //         $values           = array_column($translations, 'value');
    //         $translation_keys = array_combine($keys, $values);
    //         $language         = $this->language->get($language);
    //         $path             = base_path('lang/'.$language->locale.'.json');
    //         file_put_contents($path, json_encode($translation_keys, JSON_PRETTY_PRINT));
    //         Toastr::success(__('update_successful'));
    //         return response()->json([
    //             'success' => __('update_successful'),
    //         ]);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'error' => $e->getMessage(),
    //         ]);
    //     }
    // }
    public function updateTranslations(Request $request, $language): JsonResponse
    {
        try {
            // Decode the request payloads
            $translations = json_decode($request->translations, true);
            $keys = array_column(json_decode($request->keys, true), 'value');
            $values = array_column($translations, 'value');
            $translation_keys = array_combine($keys, $values);

            // Get the language and file path
            $language = $this->language->get($language);
            $path = base_path('lang/' . $language->locale . '.json');

            // Get existing translations from the file
            $existing = file_exists($path) ? json_decode(file_get_contents($path), true) : [];

            // Merge new keys into existing ones
            $merged = array_merge($existing, $translation_keys);

            // Write the updated translations back to the file
            file_put_contents($path, json_encode($merged, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

            Toastr::success(__('update_successful'));

            return response()->json([
                'success' => __('update_successful'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
