{{-- Layout and design by WhileD0S <https://vk.com/whiled0s>  --}}
@extends('layouts.global')

@section('content_global')
    <div id="side-content" class="z-depth-2">
        <div id="sidebar" style="position: relative;">
            <p id="s-btn-c">
                <button id="btn-menu-c" class="btn waves-effect"><i class="fa fa-arrow-left"></i></button>
            </p>
            @if($isAuth)
                <p id="name">{{ $username }}</p>
                <div id="profile-block">
                    <p id="balance"><i class="fa fa-database fa-left"></i>
                        @lang('sidebar.main.balance'):
                        <span id="balance-span">
                            {{ $balance }}
                        </span>
                            {!! $currency !!}
                    </p>
                    <!--<p id="rank"><i class="fa fa-star fa-left"></i>Ранг: <span>Beginner</span></p>-->
                    <a href="{{ $catalogUrl }}" class="btn info-color btn-block">
                        <i class="fa fa-list fa-lg fa-left"></i>
                        @lang('sidebar.main.catalog')
                    </a>
                    <a href="{{ route('cart', ['server' => $currentServer->getId()]) }}" class="btn info-color btn-block">
                        <i class="fa fa-shopping-cart fa-left fa-lg"></i>
                        @lang('sidebar.main.cart')
                    </a>
                    <a href="{{ route('fillupbalance', ['server' => $currentServer->getId()]) }}" class="btn btn-warning btn-block">
                        <i class="fa fa-credit-card fa-left fa-lg"></i>
                        @lang('sidebar.main.fillupbalance')
                    </a>
                    <a href="{{ $logoutUrl }}" class="btn danger-color btn-block">
                        <i class="fa fa-times fa-left fa-lg"></i>
                        @lang('sidebar.main.logout')
                    </a>
                </div>
            @endif
            @if(!$isAuth)
                <div id="profile-block">
                    <a href="{{ $catalogUrl }}" class="btn info-color btn-block">
                        <i class="fa fa-list fa-lg fa-left"></i>
                        @lang('sidebar.main.catalog')
                    </a>
                    <a href="{{ $cartUrl }}" class="btn info-color btn-block">
                        <i class="fa fa-shopping-cart fa-left fa-lg"></i>
                        @lang('sidebar.main.cart')
                    </a>
                    @if($canEnter)
                        <a href="{{ $signinUrl }}" class="btn btn-warning btn-block">
                            <i class="fa fa-key fa-left fa-lg"></i>
                            @lang('sidebar.main.signin')
                        </a>
                    @endif
                </div>
            @endif
            @if($isAuth)
                <div class="l-shop-collapse">
                    <p class="a-b-header">@lang('sidebar.profile.title')</p>
                    @if($character)
                        <p>
                            <a href="{{ route('profile.character', ['server' => $currentServer->getId()]) }}" class="btn btn-info btn-block"><i class="fa fa-user left"></i>@lang('sidebar.profile.character')</a>
                        </p>
                    @endif
                    <p>
                        <a href="{{ route('profile.settings', ['server' => $currentServer->getId()]) }}" class="btn btn-info btn-block"><i class="fa fa-gear left"></i>@lang('sidebar.profile.settings')</a>
                    </p>
                    <div class="ad-btn-block">
                        <button class="btn btn-info btn-block admin-menu-btn"><i class="fa fa-info left"></i>@lang('sidebar.profile.information.name')</button>
                        <ul class="ad-btn-list">
                            <a href="{{ route('profile.payments', ['server' => $currentServer->getId()]) }}" class="waves-effect">@lang('sidebar.profile.information.nodes.payments')</a>
                            <a href="{{ route('profile.cart', ['server' => $currentServer->getId()]) }}" class="waves-effect">@lang('sidebar.profile.information.nodes.cart')</a>
                        </ul>
                    </div>
                </div>
            @endif
            @if ($isAdmin)
                {{-- Sidebar admin section --}}
                @include('components.admin_sidebar', ['currentServer' => $currentServer])
            @endif
            <div id="server-block">
                <button id="chose-server" class="btn btn-warning btn-block">
                    <i class="fa fa-chevron-right fa-left left"></i>@lang('sidebar.servers')
                </button>
                <div id="server-list" class="servers text-left">
                    @foreach($servers as $server)
                        @if($server->isEnabled() or is_admin())
                            <a class="waves-effect white-text" href="{{ route('catalog', ['server' => $server->getId()]) }}"> @if(!$server->isEnabled()) <i class="fa fa-power-off fa-left" title="Сервер отключен"></i> @endif {{ $server->getName() }}</a>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    @if($news)
        <div id="news-content" class="z-depth-1">
            <button id="news-back" class="btn"><i class="fa fa-arrow-right"></i></button>
            <div id="news-block">
                @if(!count($news))
                    <div class="alert alert-info text-center">
                        @lang('content.shop.news.empty')
                    </div>
                @endif

                @foreach($news as $one)
                    <div class="news-preview z-depth-1">
                        <h3 class="news-pre-header">{{ $one->getTitle() }}</h3>
                        <p class="news-pre-text">{{ short_string($one->getContent(), 150) }}</p>
                        <a href="{{ route('news', ['server' => $currentServer->getId(), 'id' => $one->getId()]) }}" class="btn btn-info btn-sm btn-block mt-1">@lang('content.shop.news.read_more')</a>
                    </div>
                @endforeach
            </div>
            @if($newsCount >= s_get('news.first_portion', 15))
                <button id="news-load-more" class="btn btn-blue-grey btn-block mt-1" data-url="{{ route('news.load_more', ['server' => $currentServer->getId()]) }}"><i class="fa fa-plus"></i></button>
            @endif
        </div>
    @endif

    <div id="content">
        <div id="topbar" class="z-depth-1">
            <div class="row">
                <div class="col-8" id="topbar-content-1">
                    <button id="btn-menu" class="btn"><i class="fa fa-bars"></i></button>
                    <a href="{{ route('servers') }}">
                        <span id="topbar-server">@lang('content.shop.server_name') <span id="tbs-name">{{ $currentServer->getName() }}</span></span>
                    </a>
                </div>

                <div class="col-4 text-right" id="topbar-content-2">
                    @if(s_get('monitoring.enabled') && count($monitoring) !== 0)
                        <button id="btn-monitoring" class="btn topbar-btn"><i class="fa fa-bar-chart"></i></button>
                    @endif
                    @if($news)
                        <button id="btn-news" class="btn topbar-btn"><i class="fa fa-newspaper-o"></i></button>
                    @endif
                </div>
            </div>
        </div>

        <!-- Content -->
        @yield('content')
        <!-- End content -->

        <div id="footer">
            <div id="f-first">
                <p>2017<i class="fa fa-copyright fa-left fa-right"></i>Copyright : {{ $shopName }}</p>
            </div>

            <div id="f-second">
                <a href="https://github.com/D3lph1/L-shop" target="_blank" class="btn btn-info"><i class="fa fa-github fa-lg fa-left"></i>Github</a>
            </div>
        </div>
    </div>
@endsection

@if(s_get('monitoring.enabled'))
    @component('components.modal')
        @slot('id')
            monitoring-modal
        @endslot
        @slot('title')
            @lang('content.monitoring.title')
        @endslot
        @slot('buttons')
            <button type="button" class="btn btn-outline-warning" data-dismiss="modal">@lang('content.monitoring.cancel')</button>
        @endslot

        @foreach($monitoring as $server)
            <div class="md-form text-left">
                <h4>{{ get_server_by_id($servers, $server->getServerId())->getName() }}</h4>

                @if(is_null($server->getNow()) or is_null($server->getTotal()))
                    <div class="progress">
                        <div class="progress-bar progress-bar-danger danger-color" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%;">
                            @lang('content.monitoring.error')
                        </div>
                    </div>
                @else
                    <div class="progress">
                        @if($server->getNow() === -1)
                            <div class="progress-bar progress-bar-danger danger-color" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%;">
                                @lang('content.monitoring.server_disabled')
                            </div>
                        @else
                            <div class="progress-bar info-color" role="progressbar" aria-valuenow="{{ $server->getNow() }}" aria-valuemin="0" aria-valuemax="{{ $server->getTotal() }}" style="min-width: 12%; width: {{ ($server->getNow() / $server->getTotal()) * 100 }}%;">
                                {{ $server->getNow() }} / {{ $server->getTotal() }}
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        @endforeach
    @endcomponent
@endif
