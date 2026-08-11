   <div class="gradient-border radius-22">
       <div class="ps-box ps-sidebar static-menu">
           <div class="ps-price d-flex">
               @if (isset($bootcamp_details->is_paid) && $bootcamp_details->is_paid == 0)
                   <h4 class="g-title">{{ get_phrase('Free') }}</h4>
               @elseif (isset($bootcamp_details->discount_flag) && $bootcamp_details->discount_flag == 1)
                   <h4 class="g-title">
                       {{ currency($bootcamp_details->price - $bootcamp_details->discount_price, 2) }}
                   </h4>
                   <del>{{ currency($bootcamp_details->price, 2) }}</del>
               @else
                   <h4 class="g-title">{{ currency($bootcamp_details->price, 2) }}</h4>
               @endif
           </div>


           @php
               if (isset(auth()->user()->id)) {
                   $is_purchased = DB::table('bootcamp_purchases')
                       ->where('user_id', auth()->user()->id)
                       ->where('bootcamp_id', $bootcamp_details->id)
                       ->where('status', 1)
                       ->exists();

                   $pending_bootcamp_payment = DB::table('offline_payments')
                       ->where('user_id', auth()->user()->id)
                       ->where('item_type', 'bootcamp')
                       ->where('items', $bootcamp_details->id)
                       ->where('status', 0)
                       ->first();
               }
           @endphp
           @if (isset(auth()->user()->id))
               @if ($pending_bootcamp_payment)
                   <a href="{{ route('theme.purchase.bootcamp', $bootcamp_details->id) }}" class="eBtn gradient w-100 mb-3">
                       <img src="{{ asset('assets/frontend/default/image/enroll.png') }}" alt="...">
                       {{ get_phrase('Processing') }}</a>
               @else
                   @if ($is_purchased)
                       <a href="{{ route('theme.my.bootcamp.details', $bootcamp_details->slug) }}"
                           class="eBtn gradient w-100 mb-3">
                           <img src="{{ asset('assets/frontend/default/image/enroll.png') }}" alt="...">
                           {{ get_phrase('Show In Collection') }}</a>
                   @else
                       <a href="{{ route('theme.purchase.bootcamp', $bootcamp_details->id) }}" class="eBtn gradient w-100">
                           <img src="{{ asset('assets/frontend/default/image/enroll.png') }}" alt="...">
                           {{ get_phrase($bootcamp_details->is_paid ? 'Buy Bootcamp' : 'Enroll Bootcamp') }}
                       </a>
                   @endif
               @endif
           @else
               <a href="{{ route('theme.purchase.bootcamp', $bootcamp_details->id) }}" class="eBtn gradient w-100">
                   <img src="{{ asset('assets/frontend/default/image/enroll.png') }}" alt="...">
                   {{ get_phrase($bootcamp_details->is_paid ? 'Buy Bootcamp' : 'Enroll Bootcamp') }}</a>
           @endif

           <ul class="ps-side-feature">
               <li class="d-flex justify-content-between align-items-center">
                   <span>
                       <i class="fas fa-users"></i>
                       <p>الطلاب</p>
                   </span>
                   {{ bootcamp_enrolls($bootcamp_details->id) }}
               </li>
               <li class="d-flex justify-content-between align-items-center">
                   <span class="align-items-center">
                       <i class="fas fa-book-open"></i>
                       <p>الوحدات</p>
                   </span>
                   {{ count_bootcamp_modules($bootcamp_details->id) }}

               </li>
               <li class="d-flex justify-content-between align-items-center">
                   <span>
                       <i class="fas fa-video"></i>
                       <p>الحصص المباشرة</p>
                   </span>
                   {{ count_bootcamp_classes($bootcamp_details->id) }}
               </li>
               <li class="d-flex justify-content-between align-items-center">
                   <span>
                       <i class="fas fa-file-alt"></i>
                       <p>الموارد</p>
                   </span>
                   نعم
               </li>
               <li class="d-flex justify-content-between align-items-center">
                   <span>
                       <i class="fas fa-play-circle"></i>
                       <p>تسجيلات الحصص</p>
                   </span>
                   نعم
               </li>
           </ul>





       </div>
   </div>
   </div>
   <style>
       :root {
           --primary-color: #0891b2;
           --primary-dark: #3a56d5;
           --secondary-color: #6b7385;
           --light-bg: #f8f9fa;
           --border-color: #e9ecef;
           --success-color: #28a745;
           --text-dark: #2d3748;
           --text-light: #718096;
           --radius: 12px;
           --shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
           --transition: all 0.3s ease;
       }



       .gradient-border {
           background: linear-gradient(135deg, #001987 0%, #764ba2 100%);
           padding: 2px;
           border-radius: var(--radius);
           box-shadow: var(--shadow);
           width: 100%;
           max-width: 360px;
       }

       .ps-box {
           background: white;
           border-radius: calc(var(--radius) - 2px);
           padding: 24px;
           width: 100%;
       }

       .ps-price {
           color: black !important;
           display: flex;
           align-items: center;
           justify-content: center;
           gap: 12px;
           margin-bottom: 24px;
           padding-bottom: 20px;
           border-bottom: 1px solid var(--border-color);
       }

       .g-title {
           color: black !important;

           font-size: 28px;
           font-weight: 700;
           color: var(--primary-color);
       }

       del {
           color: var(--text-light);
           font-size: 18px;
       }

       .eBtn {
           display: flex;
           align-items: center;
           justify-content: center;
           gap: 10px;
           background: #0891b2;
           color: white;
           text-decoration: none;
           padding: 14px 20px;
           border-radius: 8px;
           font-weight: 600;
           font-size: 16px;
           transition: var(--transition);
           margin-bottom: 20px;
           box-shadow: 0 4px 1px #0891b2;
       }

       .eBtn:hover {
           transform: translateY(-2px);
           box-shadow: 0 6px 15px #0891b2;
           color: white;
       }

       .eBtn.processing {
           background: linear-gradient(135deg, #ffc107, #ff9800);
           box-shadow: 0 4px 10px rgba(255, 193, 7, 0.3);
       }

       .eBtn.processing:hover {
           box-shadow: 0 6px 15px rgba(255, 193, 7, 0.4);
       }

       .eBtn img {
           width: 20px;
           height: 20px;
           filter: brightness(0) invert(1);
       }

       .ps-side-feature {
           list-style: none;
           margin-bottom: 24px;
       }

       .ps-side-feature li {
           display: flex;
           justify-content: space-between;
           align-items: center;
           padding: 14px 0;
           border-bottom: 1px solid var(--border-color);
       }

       .ps-side-feature li:last-child {
           border-bottom: none;
       }

       .ps-side-feature li span {
           display: flex;
           align-items: center;
           gap: 12px;
           color: var(--text-dark);
       }

       .ps-side-feature li span i,
       .ps-side-feature li span svg {
           color: var(--primary-color);
           width: 20px;
           text-align: center;
       }

       .ps-side-feature li span p {
           margin: 0;
           font-weight: 500;
       }









       @media (max-width: 768px) {
           .gradient-border {
               max-width: 100%;
           }

           body {
               padding: 15px;
           }
       }
   </style>
