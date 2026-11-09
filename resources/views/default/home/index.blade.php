@use('App\Kraft\SiteDates')
@use('Illuminate\Support\Str')
@extends('default.layouts.layout')
@section('content')
    @php
        $stories = $news->getCollection();
        $featured = $stories->first();
        $publicAddress = $publicSite['communityAddress'];
    @endphp
    <x-extensions.slot name="site.home.before" :context="['news' => $news]" />
    <section class="atelier-hero" aria-labelledby="atelier-heading">
        <div class="atelier-intro">
            <p class="atelier-kicker">{{ $publicSite['name'] }} ·
                {{ __('theme::messages.home.welcome.kicker') }}</p>
            <h1 id="atelier-heading">{{ $publicSite['homeTitle'] ?: __('theme::messages.home.welcome.title') }}</h1>
            <p class="atelier-description">
                {{ $publicSite['homeDescription'] ?: $publicSite['description'] ?: __('theme::messages.home.welcome.description') }}
            </p>
            <div class="actions atelier-hero-actions">
                @if ($publicAddress)
                    <a class="btn btn-primary" href="#community-connection">{{ __('theme::messages.home.welcome.join') }} <i
                            class="bi bi-arrow-up-right" aria-hidden="true"></i></a>
                @elseif ($capabilities->hasNews)
                    <a class="btn btn-primary" href="#news">{{ __('theme::messages.home.news.title') }} <i
                            class="bi bi-arrow-down-right" aria-hidden="true"></i></a>
                @endif
                @auth
                    <a class="atelier-text-link" href="{{ route('account.index') }}">{{ __('site.auth.account.title') }} <i
                            class="bi bi-arrow-right" aria-hidden="true"></i></a>
                @else
                    @if ($capabilities->canRegister)
                        <a class="atelier-text-link" href="{{ route('register') }}">{{ __('theme::messages.home.members.join') }}
                            <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
                    @endif
                @endauth
            </div>
        </div>
        <div class="atelier-spotlight" id="atelier-spotlight" data-ajax-pagination-related="home-news-list">
            @if ($featured)
                @php
                    $featuredCover = $featured->coverAsset();
                @endphp
                <article @class([
                    'atelier-feature',
                    'atelier-feature-with-image' => $featuredCover,
                ])>
                    @if ($featuredCover)
                        <a class="atelier-feature-image" href="{{ $featured->url() }}" tabindex="-1" aria-hidden="true">
                            <img src="{{ $featured->coverUrl() }}"
                                alt="{{ $featured->cover_image_id ? $featured->title : ($featuredCover->displayText('alt') ?: $featured->title) }}"
                                width="{{ $featuredCover->width }}" height="{{ $featuredCover->height }}"
                                fetchpriority="high">
                        </a>
                    @endif
                    <div class="atelier-feature-body">
                        <div class="atelier-meta">
                            <span class="atelier-label">{{ __('theme::messages.home.news.featured') }}</span>
                            <time
                                datetime="{{ $featured->published_at?->toAtomString() }}">{{ app(SiteDates::class)->format($featured->published_at, 'd M Y') }}</time>
                        </div>
                        <h2><a href="{{ $featured->url() }}">{{ $featured->title }}</a></h2>
                        <p>{{ $featured->summary ?: Str::limit(strip_tags($featured->body), 150) }}</p>
                        <a class="atelier-text-link"
                            href="{{ $featured->url() }}">{{ __('theme::messages.home.news.read') }}
                            <i class="bi bi-arrow-up-right" aria-hidden="true"></i></a>
                    </div>
                </article>
            @else
                <div class="atelier-community">
                    <span class="atelier-community-mark" aria-hidden="true"><i class="bi bi-people"></i></span>
                    <span class="atelier-label">{{ __('theme::messages.home.welcome.kicker') }}</span>
                    <h2>{{ __('theme::messages.home.members.title') }}</h2>
                    <p>{{ __('theme::messages.home.masthead.description') }}</p>
                    @auth
                        <a class="atelier-text-link" href="{{ route('account.index') }}">{{ __('site.auth.account.title') }} <i
                                class="bi bi-arrow-up-right" aria-hidden="true"></i></a>
                    @else
                        <a class="atelier-text-link" href="{{ route('login') }}">{{ __('common.labels.login') }} <i
                                class="bi bi-arrow-up-right" aria-hidden="true"></i></a>
                    @endauth
                </div>
            @endif
        </div>
    </section>
    @include('default.home.partials.slider')
    @include('default.home.partials.welcome')
    @if ($capabilities->hasNews)
        <section class="atelier-journal" id="news" aria-labelledby="atelier-news-heading">
            <div class="atelier-section-heading">
                <h2 id="atelier-news-heading">{{ __('theme::messages.home.news.title') }}</h2>
                <a class="atelier-text-link" href="{{ route('site.news.index') }}">{{ __('site.home.all_news') }} <i
                        class="bi bi-arrow-up-right" aria-hidden="true"></i></a>
            </div>
            <div id="home-news-list" data-ajax-pagination tabindex="-1">
                <div class="atelier-news-grid">
                    @foreach ($stories->skip(1) as $entry)
                        @include('default.content.partials.card')
                    @endforeach
                    @if ($stories->isEmpty())
                        <p class="text-body-secondary">{{ __('theme::messages.home.news.empty') }}</p>
                    @elseif ($stories->count() === 1)
                        <p class="text-body-secondary">{{ __('theme::messages.home.news.end') }}</p>
                    @endif
                </div>
                <div class="mt-4">{{ $news->links() }}</div>
            </div>
        </section>
    @endif
    @if ($publicAddress || $communityConnection['players_online'] !== null)
        <div class="atelier-connection vstack gap-4">
            @include('default.home.partials.community-connection')
        </div>
    @endif
    <x-widgets.zone name="home" :context="['news' => $news]" />
    <x-extensions.slot name="site.home.after" :context="['news' => $news]" />
@endsection
