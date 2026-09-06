<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\Admin\PageRepository;
use App\Models\Page;
use App;

class PrivacyPolicyController extends Controller
{

    public function index(Request $request, $link, PageRepository $pageRepository)
    {
        $page              = $pageRepository->findByLink($link);
        $lang              = $request->lang ?? app()->getLocale();
        $menu_quick_link   = headerFooterMenu('footer_quick_link_menu', $lang);
        $menu_useful_link  = headerFooterMenu('footer_useful_link_menu');
        $data['page_info'] = $pageRepository->getByLang($page->id, $lang);

        $data              = [
            'menu_quick_links'  => $menu_quick_link,
            'menu_useful_links' => $menu_useful_link,
            'lang'              => $request->lang ?? app()->getLocale(),
            'menu_language'     => headerFooterMenu('header_menu', $lang),
            'page_info'         => $pageRepository->getByLang($page->id, $lang),
        ];

        return view('website.page.privacy_policy', $data);
    }
}
