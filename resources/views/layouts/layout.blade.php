<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
@include('layouts.header')

    <div class="wrapper">
        <div class="main-header">
            <!-- Logo Header -->
            <div class="logo-header" style="background-color:@if(env("APP_Color")) {{ env("APP_Color") }} @else #000000 @endif">
                <a href="{{ route('home') }}" class="logo" style="left: 50%; margin-left: -22.6125px">
                    <img src="{{ env('APP_Logo') }}" style="height: 50px; max-width: 200px" alt="navbar brand" class="navbar-brand">
                </a>
                <div class="mr-auto">
                    <button class="navbar-toggler sidenav-toggler ml-auto" type="button" data-toggle="collapse" data-target="collapse" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon">
                            <i class="icon-menu"></i>
                        </span>
                    </button>
                </div>
                <ul class="nav navbar-nav ml-auto d-lg-none">
                    @if( session('locale') )
                        @if( session('locale') == "en" )
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('lang', ['locale' => 'de']) }}"><img style="height: 30px" src="../assets/img/icons/Germany-Flag.png"><span class="sr-only">(current)</span></a>
                            </li>
                        @elseif( session('locale') == "de" )
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('lang', ['locale' => 'en']) }}"><img style="height: 30px" src="../assets/img/icons/United-Kingdom-flag.png"><span class="sr-only">(current)</span></a>
                            </li>
                        @endif
                    @else
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('lang', ['locale' => 'en']) }}"><img style="height: 30px" src="../assets/img/icons/United-Kingdom-flag.png"><span class="sr-only">(current)</span></a>
                        </li>
                    @endif
                </ul>
            </div>
            <!-- End Logo Header -->
            <nav class="navbar navbar-header navbar-dark navbar-expand-sm justify-content-end" style="background-color:@if(env("APP_Color")) {{ env("APP_Color") }} @else #000000 @endif">
                    <ul class="nav navbar-nav">

                            @if( session('locale') )
                                @if( session('locale') == "en" )
                                    <li  class="nav-item">
                                        <a class="nav-link" href="{{ route('lang', ['locale' => 'de']) }}"><img style="height: 30px" src="../assets/img/icons/Germany-Flag.png"><span class="sr-only">(current)</span></a>
                                    </li>
                                @elseif( session('locale') == "de" )
                                    <li  class="nav-item">
                                        <a class="nav-link" href="{{ route('lang', ['locale' => 'en']) }}"><img style="height: 30px" src="../assets/img/icons/United-Kingdom-flag.png"><span class="sr-only">(current)</span></a>
                                    </li>
                                @endif
                            @else
                                <li  class="nav-item">
                                    <a class="nav-link" href="{{ route('lang', ['locale' => 'en']) }}"><img style="height: 30px" src="../assets/img/icons/United-Kingdom-flag.png"><span class="sr-only">(current)</span></a>
                                </li>
                            @endif
                            <li class="nav-item">
                                <a class="nav-link btn btn-outline-light" href="{{ route('faq-help') }}" style="height: 50px; width: 50px"><span class="fa fa-question text-light"></span></a>
                            </li>
                    </ul>
            </nav>
        </div>
        <!-- Sidebar -->
        @guest
        @else
            <div class="sidebar sidebar-style-2">
                <div class="sidebar-wrapper" style="padding-bottom: 0px !important">
                    <div class="sidebar-content" style="padding-bottom: 0px !important">
                        <ul class="nav">

                                @cannot('isgatekeeper')
                                <li class="nav-item">
                                    <a href="{{ route('myVisitors') }}">
                                        <i class="fas fa-layer-group"></i>
                                        <h6>@lang('main.visitorMasterData')</h6>
                                    </a>
                                </li>
                                @endcan
                                @can('isgatekeeper')
                                    <li class="nav-item">
                                        <a href="{{ route('gatekeeperVisitors') }}">
                                            <i class="fas fa-layer-group"></i>
                                            <h6>@lang('main.visitorMasterData')</h6>
                                        </a>
                                    </li>
                                @endcan
                                <li class="nav-item">
                                    <a href="{{ route('newVisitor') }}">
                                        <i class="fas fa-layer-group"></i>
                                        <h6>@lang('main.addNewVisitor')</h6>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('advanceRegistration') }}">
                                        <i class="fas fa-layer-group"></i>
                                        <h6>@lang('main.createAdvancedRegistration')</h6>
                                    </a>
                                </li>
                                @canany(['isemployee'])
                                    <li class="nav-item">
                                        <a href="{{ route('myAdvanceRegistration') }}">
                                            <i class="fas fa-layer-group"></i>
                                            <h6>@lang('main.advanceRegistrationOverview')</h6>
                                        </a>
                                    </li>
                                @endcan
                                @canany(['isgatekeeper','isadmin', 'issuperadmin'])
                                    <li class="nav-item">
                                        <a href="{{ route('gatekeeperAdvanceRegistration') }}">
                                            <i class="fas fa-layer-group"></i>
                                            <h6>@lang('main.advanceRegistrationOverview')</h6>
                                        </a>
                                    </li>
                                @endcan
                                @can('isgatekeeper')
                                    <li class="nav-item">
                                        <a href="{{ route('Visits') }}">
                                            <i class="fas fa-layer-group"></i>
                                            <h6>@lang('main.visits')</h6>
                                        </a>
                                    </li>
                                @endcan
                            @canany(['isadmin', 'issuperadmin'])

                                <li class="nav-item">
                                    <a href="{{ route("email.overview") }}">
                                        <i class="fas fa-layer-group"></i>
                                        <h6>@lang("main.emailTemplates")</h6>
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a href="{{ route("area.permission") }}">
                                        <i class="fas fa-layer-group"></i>
                                        <h6>@lang("main.areaPermission")</h6>
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a href="{{ route("admin.settings") }}">
                                        <i class="fas fa-layer-group"></i>
                                        <h6>@lang("main.ulpoadDocuments")</h6>
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a href="{{ route("users") }}">
                                        <i class="fas fa-layer-group"></i>
                                        <h6>@lang("main.changeUserRoles")</h6>
                                    </a>
                                </li>
                            @endcan

                                <li class="nav-item">
                                    <a href="#historySubMenue" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                                            <i class="fas fa-layer-group"></i>
                                            <h6>@lang('main.history')</h6>
                                        </a>
                                    <ul class="@if(!Request::is('history/*')) collapse @endif list-unstyled" id="historySubMenue">
                                        @canany(['isadmin', 'issuperadmin'])
                                            <li class="nav-item @if(Request::fullurl() == route('action.history.log')) active @endif">
                                                <a href="{{ route('action.history.log') }}"><h6>@lang('main.actionHistory')</h6></a>
                                            </li>
                                        @endcan
                                        <li class="nav-item @if(Request::fullurl() == route('visits.history.log')) active @endif">
                                            <a href="{{ route('visits.history.log') }}"><h6>@lang('main.visitsHistory')</h6></a>
                                        </li>
                                    </ul>
                                </li>

                            @canany(['isadmin', 'issuperadmin'])
                                <li class="nav-item">
                                    <a href="{{ route("admin.visitor.table") }}">
                                        <i class="fas fa-layer-group"></i>
                                        <h6>@lang("main.deleteData")</h6>
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a href="{{ route('Visits') }}">
                                        <i class="fas fa-layer-group"></i>
                                        <h6>@lang('main.visits')</h6>
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a href="{{ route('badgeOverview') }}">
                                        <i class="fas fa-layer-group"></i>
                                        <h6>@lang('main.overviewBadges')</h6>
                                    </a>
                                </li>
                            @endcan

                            <li class="nav-item">
                                <a href="{{ route('profile') }}">
                                    <i class="fas fa-layer-group"></i>
                                    <h6>@lang('main.profileAbsence') {{ Auth::user()->name }} <br/></h6>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="{{ route('logout') }}">
                                    <i class="fas fa-layer-group"></i>
                                    <h6>@lang('main.logout')</h6>
                                </a>
                            </li>
                            <!--
                            <div class="nav-item w-100 p-3" style="position: absolute; bottom: 0px">
                                <li>
                                    <a href="{{ route('profile') }}">
                                        <i class="fas fa-layer-group"></i>
                                        <h6>{{ Auth::user()->name }} <br/></h6>
                                    </a>
                                </li>

                                <li>
                                    <a href="{{ route('logout') }}">
                                        <i class="fas fa-layer-group"></i>
                                        <h6>@lang('main.logout')</h6>
                                    </a>
                                </li>

                            </div> -->

                        </ul>
                    </div>
                </div>
            </div>
    @endguest
    @guest
    <!-- End Sidebar -->
    <div class="main-panel w-100">
        <div class="content">
            <div class="page-inner">
                <div class="page-category">@yield('content')</div>
            </div>
        </div>
    </div>
    @else
    <!-- End Sidebar -->
        <div class="main-panel">
            <div class="content">
                <div class="page-inner">
                    <div class="page-category">@yield('content')</div>
                </div>
            </div>
        </div>
    @endguest
</div>
@include('layouts.bottomScripts')
@yield('scripts')
@yield('contentSscripts')
</html>
