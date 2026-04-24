@if($enabled)
@php
    $envConfig = config('envbar.environments_config.' . $environment, []);
    $position = config('envbar.position', 'top');
    $theme = config('envbar.theme', 'auto');
    $show = config('envbar.show', []);
    $collapsible = config('envbar.collapsible', true);
    $switcher = config('envbar.switcher', ['enabled' => false]);
    $faviconOverlay = config('envbar.favicon_overlay', false);

    $label = $envConfig['label'] ?? strtoupper($environment);
    $bgColor = $envConfig['background_color'] ?? '#6c757d';
    $textColor = $envConfig['text_color'] ?? '#ffffff';
    $icon = $envConfig['icon'] ?? '🔔';

    $segments = [];

    $segments[] = '<span class="envbar-env" style="font-weight:bold;">' . e($icon) . '&nbsp;&nbsp;' . e($label) . '</span>';

    if (!empty($show['app_name'])) {
        $segments[] = '<span class="envbar-item">' . e(config('app.name', 'Laravel')) . '</span>';
    }
    if (!empty($show['php_version'])) {
        $segments[] = '<span class="envbar-item">PHP ' . e($metadata['php_version'] ?? phpversion()) . '</span>';
    }
    if (!empty($show['laravel_version'])) {
        $segments[] = '<span class="envbar-item">Laravel ' . e(app()->version()) . '</span>';
    }
    if (!empty($show['git_branch']) && !empty($metadata['git_branch'])) {
        $segments[] = '<span class="envbar-item">branch: ' . e($metadata['git_branch']) . '</span>';
    }
    if (!empty($show['git_commit']) && !empty($metadata['git_commit'])) {
        $segments[] = '<span class="envbar-item">commit: ' . e(substr($metadata['git_commit'], 0, 7)) . '</span>';
    }
    if (!empty($show['hostname'])) {
        $segments[] = '<span class="envbar-item">host: ' . e(gethostname()) . '</span>';
    }
    if (!empty($show['database'])) {
        $segments[] = '<span class="envbar-item">db: ' . e(config('database.connections.' . config('database.default') . '.database', '—')) . '</span>';
    }
    if (!empty($show['user']) && auth()->check()) {
        $segments[] = '<span class="envbar-item">user: ' . e(auth()->user()->name) . '</span>';
    }
    if (!empty($show['timestamp'])) {
        $segments[] = '<span class="envbar-item">' . e(now()->format('H:i:s')) . '</span>';
    }

    // Custom data providers
    foreach ($providers ?? [] as $provider) {
        $providerIcon = $provider->icon();
        $providerValue = $provider->value();
        $displayValue = is_array($providerValue) ? implode(', ', $providerValue) : $providerValue;
        $iconHtml = $providerIcon ? e($providerIcon) . ' ' : '';
        $segments[] = '<span class="envbar-item">' . $iconHtml . e($provider->label()) . ': ' . e($displayValue) . '</span>';
    }
@endphp

<style>
@font-face {
    font-family: 'Inter';
    font-style: normal;
    font-weight: 100 900;
    font-display: swap;
    src: url('https://fonts.gstatic.com/s/inter/v20/UcC73FwrK3iLTeHuS_nVMrMxCp50SjIa25L7SUc.woff2') format('woff2');
    unicode-range: U+0100-02BA, U+02BD-02C5, U+02C7-02CC, U+02CE-02D7, U+02DD-02FF, U+0304, U+0308, U+0329, U+1D00-1DBF, U+1E00-1E9F, U+1EF2-1EFF, U+2020, U+20A0-20AB, U+20AD-20C0, U+2113, U+2C60-2C7F, U+A720-A7FF;
}
@font-face {
    font-family: 'Inter';
    font-style: normal;
    font-weight: 100 900;
    font-display: swap;
    src: url('https://fonts.gstatic.com/s/inter/v20/UcC73FwrK3iLTeHuS_nVMrMxCp50SjIa1ZL7.woff2') format('woff2');
    unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+0304, U+0308, U+0329, U+2000-206F, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;
}
</style>

<div id="envbar"
     data-position="{{ $position }}"
     data-collapsible="{{ $collapsible ? '1' : '0' }}"
     data-favicon-overlay="{{ $faviconOverlay ? '1' : '0' }}"
     data-bg-color="{{ $bgColor }}"
     data-label="{{ $label }}"
     style="
        all: initial;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0;
        position: fixed;
        {{ $position === 'bottom' ? 'bottom: 0;' : 'top: 0;' }}
        left: 0;
        right: 0;
        z-index: 2147483647;
        background: {{ $bgColor }};
        color: {{ $textColor }};
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
        font-size: 13px;
        line-height: 1;
        height: 24px;
        padding: 0 12px;
        box-shadow: 0 {{ $position === 'bottom' ? '-2px' : '2px' }} 6px rgba(0,0,0,.2);
        transition: transform .3s ease, opacity .3s ease;
     "
>
    {{-- Segments --}}
    <div style="display:flex; align-items:center; gap:0; flex:1; justify-content:center;">
        @foreach($segments as $i => $segment)
            @if($i > 0)
                <span style="opacity:.4; margin:0 10px; font-size:11px;">│</span>
            @endif
            {!! $segment !!}
        @endforeach
    </div>

    {{-- Switcher --}}
    @if(!empty($switcher['enabled']) && !empty($switcher['environments']))
        <div style="display:flex; align-items:center; gap:6px; margin-left:12px; border-left:1px solid rgba(255,255,255,.25); padding-left:12px;">
            @foreach($switcher['environments'] as $envName => $envUrl)
                @if($envName !== $environment)
                    <a href="{{ $envUrl }}{{ request()->getRequestUri() }}"
                       style="color:{{ $textColor }}; text-decoration:none; opacity:.7; font-size:11px; padding:2px 6px; border-radius:3px; border:1px solid rgba(255,255,255,.3);"
                       onmouseover="this.style.opacity='1'"
                       onmouseout="this.style.opacity='.7'"
                       title="Switch to {{ $envName }}"
                    >{{ strtoupper($envName) }}</a>
                @endif
            @endforeach
        </div>
    @endif

    {{-- Collapse button --}}
    @if($collapsible)
        <button id="envbar-toggle"
                onclick="window.__envbarToggle()"
                style="
                    all: unset;
                    cursor: pointer;
                    margin-left: 10px;
                    font-size: 16px;
                    line-height: 1;
                    color: {{ $textColor }};
                    opacity: .7;
                    padding: 0 2px;
                "
                onmouseover="this.style.opacity='1'"
                onmouseout="this.style.opacity='.7'"
                title="Minimize"
        >
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" width="20" height="20">
                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 15.75 7.5-7.5 7.5 7.5" />
            </svg>
        </button>
    @endif
</div>

{{-- Collapsed pill --}}
@if($collapsible)
<div id="envbar-pill"
     onclick="window.__envbarToggle()"
     style="
        all: initial;
        display: none;
        position: fixed;
        {{ $position === 'bottom' ? 'bottom: 8px;' : 'top: 8px;' }}
        left: 8px;
        z-index: 2147483647;
        background: {{ $bgColor }};
        color: {{ $textColor }};
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
        font-size: 11px;
        font-weight: 700;
        line-height: 1;
        padding: 5px 10px;
        border-radius: 4px;
        cursor: pointer;
        box-shadow: 0 2px 8px rgba(0,0,0,.25);
        opacity: .85;
        transition: opacity .2s ease;
     "
     onmouseover="this.style.opacity='1'"
     onmouseout="this.style.opacity='.85'"
     title="Expand environment bar"
>{{ $icon }}&nbsp;{{ $label }}</div>
@endif

<script>
(function() {
    var STORAGE_KEY = 'envbar_collapsed';
    var bar = document.getElementById('envbar');
    var pill = document.getElementById('envbar-pill');
    var position = bar.getAttribute('data-position');
    var collapsible = bar.getAttribute('data-collapsible') === '1';
    var faviconOverlay = bar.getAttribute('data-favicon-overlay') === '1';

    function applyBodyOffset(show) {
        if (show) {
            document.body.style['margin' + (position === 'bottom' ? 'Bottom' : 'Top')] = '24px';
        } else {
            document.body.style['margin' + (position === 'bottom' ? 'Bottom' : 'Top')] = '';
        }
    }

    function setState(collapsed) {
        if (collapsed) {
            bar.style.transform = position === 'bottom' ? 'translateY(100%)' : 'translateY(-100%)';
            bar.style.opacity = '0';
            bar.style.pointerEvents = 'none';
            if (pill) pill.style.display = 'inline-block';
            applyBodyOffset(false);
        } else {
            bar.style.transform = 'translateY(0)';
            bar.style.opacity = '1';
            bar.style.pointerEvents = '';
            if (pill) pill.style.display = 'none';
            applyBodyOffset(true);
        }
    }

    window.__envbarToggle = function() {
        var isCollapsed = localStorage.getItem(STORAGE_KEY) === '1';
        var newState = !isCollapsed;
        localStorage.setItem(STORAGE_KEY, newState ? '1' : '0');
        setState(newState);
    };

    // Init
    if (collapsible && localStorage.getItem(STORAGE_KEY) === '1') {
        setState(true);
    } else {
        setState(false);
    }

    // Favicon overlay
    if (faviconOverlay) {
        var bgColor = bar.getAttribute('data-bg-color');
        var label = bar.getAttribute('data-label');
        var link = document.querySelector('link[rel*="icon"]') || document.createElement('link');

        var canvas = document.createElement('canvas');
        canvas.width = 64;
        canvas.height = 64;
        var ctx = canvas.getContext('2d');

        var existingFavicon = link.href;
        if (existingFavicon) {
            var img = new Image();
            img.crossOrigin = 'anonymous';
            img.onload = function() {
                ctx.drawImage(img, 0, 0, 64, 64);
                drawBadge(ctx, bgColor, label);
                applyFavicon(link, canvas);
            };
            img.onerror = function() {
                ctx.fillStyle = '#ddd';
                ctx.fillRect(0, 0, 64, 64);
                drawBadge(ctx, bgColor, label);
                applyFavicon(link, canvas);
            };
            img.src = existingFavicon;
        } else {
            ctx.fillStyle = '#ddd';
            ctx.fillRect(0, 0, 64, 64);
            drawBadge(ctx, bgColor, label);
            applyFavicon(link, canvas);
        }
    }

    function drawBadge(ctx, color, text) {
        var badgeH = 20;
        ctx.fillStyle = color;
        ctx.fillRect(0, 64 - badgeH, 64, badgeH);
        ctx.fillStyle = '#fff';
        ctx.font = 'bold 14px sans-serif';
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';
        ctx.fillText(text.substring(0, 3), 32, 64 - badgeH / 2);
    }

    function applyFavicon(link, canvas) {
        link.type = 'image/png';
        link.rel = 'icon';
        link.href = canvas.toDataURL('image/png');
        document.head.appendChild(link);
    }
})();
</script>
@endif
