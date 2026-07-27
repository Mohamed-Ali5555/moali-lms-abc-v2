<?php

namespace Modules\Theme\App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\Builder_page;
use App\Models\Category;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\Message;
use App\Models\Message_code;
use App\Models\Review;
use App\Models\User;
use Illuminate\Support\Facades\Session;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use DB;
use Modules\BookStore\App\Models\Book;
use Illuminate\Http\Request;
use Modules\Theme\App\Models\theme_feature;
use App\Models\CartItem;
use Modules\Theme\App\Models\theme_setting;
use Modules\Theme\App\Models\theme_legal_section;

class HomeController extends Controller
{
    public function index()
    {
        $page_data['blogs'] = Blog::where('status', 1)->orderBy('is_popular', 'desc')->orderBy('id', 'desc')->take(3)->get();
        $page_data['reviews'] = Review::query()->latest('id')->take(20)->get();
        $page_data['categories'] = Category::where('parent_id', 0)->where('status', 1)->get();
        $page_data['features'] = theme_feature::where('status', 1)->get();
        $page_data['homeCourses'] = Course::with(['category.parent'])
            ->where('status', 'active')
            ->where('show_on_home', 1)
            ->latest('id')
            ->take(10)
            ->get();
        $books = Book::where('status', 1)->orderBy('sort', 'ASC')->get();

        if (auth()->check()) {
            $cartItems = CartItem::where('user_id', auth()->user()->id)
                ->whereNotNull('book_id')
                ->pluck('qty', 'book_id')
                ->toArray();
        } else {
            $cartItems = CartItem::whereNotNull('book_id')
                ->pluck('qty', 'book_id')
                ->toArray();
        }

        return view('theme::index', $page_data, compact('cartItems', 'books'));
    }

    public function termsCondition()
    {
        $page_data['terms'] = theme_legal_section::ofType('terms')->active()->ordered()->get();
        $page_data['privacy'] = theme_legal_section::ofType('privacy')->active()->ordered()->get();

        // Fallback for legacy single HTML setting
        if ($page_data['terms']->isEmpty()) {
            $legacy = get_theme_settings('terms_condition');
            if ($legacy) {
                $page_data['legacy_terms'] = $legacy;
            }
        }

        return view('theme::termsCondition.terms_condition', $page_data);
    }
}
