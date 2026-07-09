@extends('../layouts/main')  

@section('head')
    @yield('subhead')
    <?php  $uri = Route::current()->getName(); ?>
@endsection 
  
@section('content') 
                <div id="kt_aside" class="aside aside-dark aside-hoverable" data-kt-drawer="true" data-kt-drawer-name="aside" data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true" data-kt-drawer-width="{default:'200px', '300px': '250px'}" data-kt-drawer-direction="start" data-kt-drawer-toggle="#kt_aside_mobile_toggle" style="z-index: 111">
					<div class="aside-logo flex-column-auto" id="kt_aside_logo"> 
						<a href="#">
							<img alt="Logo" src="<?= env('APP_ASSETS') ?>assets/media/logos/epicor-logo-dark.png" class="h-25px logo" />
						</a> 
						<div id="kt_aside_toggle" class="btn btn-icon w-auto px-0 btn-active-color-primary aside-toggle" data-kt-toggle="true" data-kt-toggle-state="active" data-kt-toggle-target="body" data-kt-toggle-name="aside-minimize">
							<span class="svg-icon svg-icon-1 rotate-180">
								<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
									<path opacity="0.5" d="M14.2657 11.4343L18.45 7.25C18.8642 6.83579 18.8642 6.16421 18.45 5.75C18.0358 5.33579 17.3642 5.33579 16.95 5.75L11.4071 11.2929C11.0166 11.6834 11.0166 12.3166 11.4071 12.7071L16.95 18.25C17.3642 18.6642 18.0358 18.6642 18.45 18.25C18.8642 17.8358 18.8642 17.1642 18.45 16.75L14.2657 12.5657C13.9533 12.2533 13.9533 11.7467 14.2657 11.4343Z" fill="black" />
									<path d="M8.2657 11.4343L12.45 7.25C12.8642 6.83579 12.8642 6.16421 12.45 5.75C12.0358 5.33579 11.3642 5.33579 10.95 5.75L5.40712 11.2929C5.01659 11.6834 5.01659 12.3166 5.40712 12.7071L10.95 18.25C11.3642 18.6642 12.0358 18.6642 12.45 18.25C12.8642 17.8358 12.8642 17.1642 12.45 16.75L8.2657 12.5657C7.95328 12.2533 7.95328 11.7467 8.2657 11.4343Z" fill="black" />
								</svg>
							</span> 
						</div> 
					</div>
					 
					<div class="aside-menu flex-column-fluid"> 
						<div class="hover-scroll-overlay-y my-5 my-lg-5" id="kt_aside_menu_wrapper" data-kt-scroll="true" data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-height="auto" data-kt-scroll-dependencies="#kt_aside_logo, #kt_aside_footer" data-kt-scroll-wrappers="#kt_aside_menu" data-kt-scroll-offset="0">  
							<div class="menu menu-column menu-title-gray-800 menu-state-title-primary menu-state-icon-primary menu-state-bullet-primary menu-arrow-gray-500" id="#kt_aside_menu" data-kt-menu="true"> 
                            @foreach ($menu_level_1 as $item)  
                                <div class="menu-item">
                                    <div class="menu-content pb-2">
                                        <span class="menu-section text-muted text-uppercase fs-8 ls-1">{{ $item->menu_name }}</span>
                                    </div>
                                </div> 
                            @foreach ($menu_level_4 as $item4)
                                @if ($item4->sub_group_id == $item->sub_group_id) 
                                    <div class="menu-item"> 
                                        <a class="menu-link {{ $item4->active }}" href="<?php echo env('BASE_URL') ?>/{{ $item4->menu }}">
                                            <span class="menu-icon"> 
                                                <span class="svg-icon svg-icon-2">
                                                    <?php echo $item4->icon ?>
                                                </span> 
                                            </span>
                                            <span class="menu-title">{{ $item4->menu_name }}</span>
                                        </a>
                                    </div>
                                @endif
                            @endforeach

                            @foreach ($menu_level_2 as $item2)
                                @if ($item2->group_id == $item->group_id) 
                                    <div data-kt-menu-trigger="click" class="menu-item menu-accordion {{ $item2->active }}">
                                        <span class="menu-link">
                                            <span class="menu-icon"> 
                                                <span class="svg-icon svg-icon-2">
                                                    <?php echo $item2->icon ?>
                                                </span> 
                                            </span>
                                            <span class="menu-title">{{ $item2->menu_name }}</span>
                                            <span class="menu-arrow"></span>
                                        </span>

                                    <?php $total_item_3 = 0 ; ?>
                                    @foreach ($menu_level_3 as $item3)
                                        @if (($item3->sub_group_id - 1) == $item2->sub_group_id) 
                                            <?php $total_item_3++; ?>
                                        @endif 
                                    @endforeach

                                @if ($total_item_3 > 0) 
                                    @foreach ($menu_level_3 as $item3)
                                        @if ($item3->group_id == $item2->group_id) 
                                        <div class="menu-sub menu-sub-accordion menu-active-bg">
                                            <div data-kt-menu-trigger="click" class="menu-item menu-accordion {{ $item3->active }}">
                                                <span class="menu-link">
                                                    <span class="menu-bullet">
                                                        <span class="bullet bullet-dot"></span>
                                                    </span>
                                                        <span class="menu-title">{{ $item3->menu_name }}</span>
                                                        <span class="menu-arrow"></span>
                                                    </span>
                                    
                                                    <div class="menu-sub menu-sub-accordion menu-active-bg"> 
                                                        @foreach ($menu_level_4 as $item4)
                                                            @if ($item4->sub_group_id == $item3->sub_group_id)  
                                                                <div class="menu-item">
                                                                    <a class="menu-link {{ $item4->active }}" href="{{ env('BASE_URL') }}/{{ $item4->menu }}">
                                                                        <span class="menu-bullet">
                                                                            <span class="bullet bullet-dot"></span>
                                                                        </span>
                                                                        <span class="menu-title">{{ $item4->menu_name }}</span>
                                                                    </a>
                                                                </div>
                                                            @endif 
                                                        @endforeach 
                                                    </div> 
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach 
                                @else  
                                    <div class="menu-sub menu-sub-accordion menu-active-bg"> 
                                        @foreach ($menu_level_4 as $item4)
                                            @if ($item4->sub_group_id == $item2->sub_group_id)  
                                                <div class="menu-item">
                                                    <a class="menu-link {{ $item4->active }}" href="{{ env('BASE_URL') }}/{{ $item4->menu }}">
                                                        <span class="menu-bullet">
                                                            <span class="bullet bullet-dot"></span>
                                                        </span>
                                                        <span class="menu-title">{{ $item4->menu_name }}</span>
                                                    </a>
                                                </div>
                                            @endif 
                                        @endforeach  
                                    </div> 
                                @endif 
                            </div>
                        @endif
                        @endforeach 
                        @endforeach
                  
								<div class="menu-item">
									<div class="menu-content">
										<div class="separator mx-1 my-4"></div>
									</div>
								</div>
								<div class="menu-item">
									<a class="menu-link" href="#">
										<span class="menu-icon"> 
											<span class="svg-icon svg-icon-2">
												<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
													<path d="M16.95 18.9688C16.75 18.9688 16.55 18.8688 16.35 18.7688C15.85 18.4688 15.75 17.8688 16.05 17.3688L19.65 11.9688L16.05 6.56876C15.75 6.06876 15.85 5.46873 16.35 5.16873C16.85 4.86873 17.45 4.96878 17.75 5.46878L21.75 11.4688C21.95 11.7688 21.95 12.2688 21.75 12.5688L17.75 18.5688C17.55 18.7688 17.25 18.9688 16.95 18.9688ZM7.55001 18.7688C8.05001 18.4688 8.15 17.8688 7.85 17.3688L4.25001 11.9688L7.85 6.56876C8.15 6.06876 8.05001 5.46873 7.55001 5.16873C7.05001 4.86873 6.45 4.96878 6.15 5.46878L2.15 11.4688C1.95 11.7688 1.95 12.2688 2.15 12.5688L6.15 18.5688C6.35 18.8688 6.65 18.9688 6.95 18.9688C7.15 18.9688 7.35001 18.8688 7.55001 18.7688Z" fill="black" />
													<path opacity="0.3" d="M10.45 18.9687C10.35 18.9687 10.25 18.9687 10.25 18.9687C9.75 18.8687 9.35 18.2688 9.55 17.7688L12.55 5.76878C12.65 5.26878 13.25 4.8687 13.75 5.0687C14.25 5.1687 14.65 5.76878 14.45 6.26878L11.45 18.2688C11.35 18.6688 10.85 18.9687 10.45 18.9687Z" fill="black" />
												</svg>
											</span>
											<!--end::Svg Icon-->
										</span>
										<span class="menu-title">Changelog v8.1</span>
									</a>
								</div>
							</div> 
						</div> 
					</div> 
				</div> 
 
    <div id="kt_header" class="header align-items-stretch" style="z-index: 110"> 
        <div class="container-fluid d-flex align-items-stretch justify-content-between"> 
            <div class="d-flex align-items-center d-lg-none ms-n3 me-1" title="Show aside menu">
                <div class="btn btn-icon btn-active-light-primary w-30px h-30px w-md-40px h-md-40px" id="kt_aside_mobile_toggle"> 
                    <span class="svg-icon svg-icon-2x mt-1">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <path d="M21 7H3C2.4 7 2 6.6 2 6V4C2 3.4 2.4 3 3 3H21C21.6 3 22 3.4 22 4V6C22 6.6 21.6 7 21 7Z" fill="black" />
                            <path opacity="0.3" d="M21 14H3C2.4 14 2 13.6 2 13V11C2 10.4 2.4 10 3 10H21C21.6 10 22 10.4 22 11V13C22 13.6 21.6 14 21 14ZM22 20V18C22 17.4 21.6 17 21 17H3C2.4 17 2 17.4 2 18V20C2 20.6 2.4 21 3 21H21C21.6 21 22 20.6 22 20Z" fill="black" />
                        </svg>
                    </span> 
                </div>
            </div> 
            <div class="d-flex align-items-center flex-grow-1 flex-lg-grow-0">
                <a href="#" class="d-lg-none">
                    <img alt="Logo" src="<?= env('APP_ASSETS') ?>assets/media/logos/epicor-logo.png" class="h-25px mt-1" /> 
                </a>
            </div>
            <!--end::Mobile logo-->
            <!--begin::Wrapper-->
            <div class="d-flex align-items-stretch justify-content-between flex-lg-grow-1">
                <!--begin::Navbar-->
                <div class="d-flex align-items-stretch" id="kt_header_nav">
                    <!--begin::Menu wrapper-->
                    <div class="header-menu align-items-stretch" data-kt-drawer="true" data-kt-drawer-name="header-menu" data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true" data-kt-drawer-width="{default:'200px', '300px': '250px'}" data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_header_menu_mobile_toggle" data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_body', lg: '#kt_header_nav'}">
                        <!--begin::Menu-->
                        <div class="menu menu-lg-rounded menu-column menu-lg-row menu-state-bg menu-title-gray-700 menu-state-title-primary menu-state-icon-primary menu-state-bullet-primary menu-arrow-gray-400 fw-bold my-5 my-lg-0 align-items-stretch" id="#kt_header_menu" data-kt-menu="true">
                            <div class="menu-item me-lg-1"> 
                                    <span class="menu-title">
                                        <?php 
                                        date_default_timezone_set("Asia/Bangkok");
                                        $mydate=getdate(date("U"));
                                        echo "$mydate[weekday], $mydate[month] $mydate[mday], $mydate[year]";
                                        ?>
                                    </span>
                            
                            </div> 
                        </div>
                        <!--end::Menu-->
                    </div>
                    <!--end::Menu wrapper-->
                </div>
                <!--end::Navbar-->
                <!--begin::Topbar-->
                <div class="d-flex align-items-stretch flex-shrink-0">
                    <!--begin::Toolbar wrapper-->
                    <div class="d-flex align-items-stretch flex-shrink-0"> 
                        <!--end::Quick links-->
                        <!--begin::User-->
                        <div class="d-flex align-items-center ms-1 ms-lg-3" id="kt_header_user_menu_toggle">
                            <!--begin::Menu wrapper-->
                            <div class="cursor-pointer symbol symbol-30px symbol-md-40px" data-kt-menu-trigger="click" data-kt-menu-attach="parent" data-kt-menu-placement="bottom-end">
                                <img src="<?= env('APP_ASSETS') ?>assets/media/avatars/<?= auth()->user()->avatar ?>" alt="{{ auth()->user()->full_name }}" />
                            </div>
                            <!--begin::Menu-->
                            <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg menu-state-primary fw-bold py-4 fs-6 w-275px" data-kt-menu="true">
                                <!--begin::Menu item-->
                                <div class="menu-item px-3">
                                    <div class="menu-content d-flex align-items-center px-3">
                                        <!--begin::Avatar-->
                                        <div class="symbol symbol-50px me-5">
                                            <img alt="{{ auth()->user()->full_name }}" src="<?= env('APP_ASSETS') ?>assets/media/avatars/<?= auth()->user()->avatar ?>" />
                                        </div>
                                        <!--end::Avatar-->
                                        <!--begin::Username-->
                                        <div class="d-flex flex-column">
                                            <div class="fw-bolder d-flex align-items-center fs-5">{{ auth()->user()->username }}
                                            </div>
                                            <a href="#" class="fw-bold text-muted text-hover-primary fs-9">{{ auth()->user()->full_name }}</a>
                                        </div>
                                        <!--end::Username-->
                                    </div>
                                </div>  
                                <div class="separator my-2"></div> 
                                <div class="menu-item px-5">
                                    <div class="menu-content px-5">
                                    <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <label class="form-check form-switch form-check-custom form-check-solid pulse pulse-success" for="kt_user_menu_dark_mode_toggle"> 
                                            <input class="form-check-input w-30px h-20px" type="checkbox" value="1" name="mode" id="kt_user_menu_dark_mode_toggle" checked />
                                            <span class="pulse-ring ms-n1"></span>
                                            <span class="form-check-label text-gray-600 fs-7">Sign Out</span> 
                                        </label>
                                    </a>
                                 
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;"> @csrf </form>
                                    </div>
                                </div>
                                <!--end::Menu item-->
                            </div>
                            <!--end::Menu-->
                            <!--end::Menu wrapper-->
                        </div>
                        <!--end::User -->
                    
                        <!--end::Heaeder menu toggle-->
                    </div>
                    <!--end::Toolbar wrapper-->
                </div>
                <!--end::Topbar-->
            </div>
            <!--end::Wrapper-->
        </div>
        <!--end::Container-->
    </div>

@yield('subcontent')
 
@endsection