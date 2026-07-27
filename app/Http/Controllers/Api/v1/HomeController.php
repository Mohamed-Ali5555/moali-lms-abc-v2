<?php

namespace App\Http\Controllers\Api\v1;

use App\Models\Coupon;
use Modules\BookStore\App\Models\Book;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Modules\Theme\App\Models\theme_feature;
use Modules\Theme\App\Models\theme_setting;

class HomeController extends Controller
{

  public function getBooks(){
      $books = Book::where('status', 1)
        ->orderBy('sort', 'ASC')
        ->get();
   
        return response()->json([
          'status'  => true,
          'message' => 'Books retrieved successfully',
          'books'   => $books,
        ], 200);
      }

      public function getBookDetails($id)
      {
          $book = Book::where('status', 1)->find($id);
      
          if (!$book) {
              return response()->json([
                  'status'  => false,
                  'message' => 'Book not found',
                  'book'    => null,
              ], 404);
          }
      
          return response()->json([
              'status'  => true,
              'message' => 'Book retrieved successfully',
              'book'    => $book,
          ], 200);
      }
      public function getFeatures()
      {
          $themeFeatures = theme_feature::where('status', 1)->get();
      
          if ($themeFeatures->isEmpty()) {
              return response()->json([
                  'status'   => false,
                  'message'  => 'No features found',
                  'features' => [],
              ], 404);
          }
      
          return response()->json([
              'status'   => true,
              'message'  => 'Features retrieved successfully',
              'features' => $themeFeatures,
          ], 200);
      }

      public function getThemeSettings()
      {
          $imageKeys = ['logo', 'dark_logo', 'thumbnail', 'dark_thumbnail'];
          $statusKeys = ['book_status', 'course_status', 'sub_status', 'terms_status', 'technical_status', 'subscriptions_view'];

          $settings = theme_setting::all()->mapWithKeys(function ($setting) use ($imageKeys, $statusKeys) {
              $value = $setting->description;

              if (in_array($setting->type, $imageKeys) && $value) {
                  $value = asset($value);
              } elseif (in_array($setting->type, $statusKeys)) {
                  $value = (int) $value;
              }

              return [$setting->type => $value];
          });

          $colors = get_active_theme_colors();

          return response()->json([
              'status'   => true,
              'message'  => 'Theme settings retrieved successfully',
              'settings' => $settings,
              'colors'   => $colors,
          ], 200);
      }

}
