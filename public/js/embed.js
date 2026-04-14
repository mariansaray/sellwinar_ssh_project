/**
 * Sellwinar Embed Script
 * Usage: <script src="https://sellwinar.com/js/embed.js" data-webinar="SLUG" data-tenant="TENANT-SLUG"></script>
 *
 * For smart video embed:
 * <script src="https://sellwinar.com/js/embed.js" data-smart-video="WEBINAR-ID" data-tenant="TENANT-SLUG"></script>
 */
(function() {
    'use strict';

    var script = document.currentScript;
    if (!script) return;

    var tenant = script.getAttribute('data-tenant');
    var webinarSlug = script.getAttribute('data-webinar');
    var smartVideoId = script.getAttribute('data-smart-video');
    var baseUrl = script.src.replace('/js/embed.js', '');

    if (webinarSlug && tenant) {
        // Registration form embed
        var container = document.createElement('div');
        container.id = 'sellwinar-registration-' + webinarSlug;
        script.parentNode.insertBefore(container, script.nextSibling);

        var iframe = document.createElement('iframe');
        iframe.src = baseUrl + '/embed/register/' + tenant + '/' + webinarSlug;
        iframe.style.width = '100%';
        iframe.style.border = 'none';
        iframe.style.minHeight = '400px';
        iframe.style.borderRadius = '12px';
        iframe.setAttribute('loading', 'lazy');
        iframe.setAttribute('allow', 'autoplay');
        container.appendChild(iframe);

        // Auto-resize iframe
        window.addEventListener('message', function(e) {
            if (e.data && e.data.type === 'sellwinar-resize' && e.data.slug === webinarSlug) {
                iframe.style.height = e.data.height + 'px';
            }
            if (e.data && e.data.type === 'sellwinar-registered' && e.data.slug === webinarSlug) {
                // Registration complete — can redirect or show success
                if (e.data.redirectUrl) {
                    window.location.href = e.data.redirectUrl;
                }
            }
        });
    }

    if (smartVideoId && tenant) {
        // Smart video player embed
        var container = document.createElement('div');
        container.id = 'sellwinar-player-' + smartVideoId;
        script.parentNode.insertBefore(container, script.nextSibling);

        var iframe = document.createElement('iframe');
        iframe.src = baseUrl + '/embed/player/' + smartVideoId;
        iframe.style.width = '100%';
        iframe.style.border = 'none';
        iframe.style.aspectRatio = '16/9';
        iframe.style.borderRadius = '12px';
        iframe.setAttribute('loading', 'lazy');
        iframe.setAttribute('allow', 'autoplay; fullscreen');
        iframe.setAttribute('allowfullscreen', '');
        container.appendChild(iframe);

        window.addEventListener('message', function(e) {
            if (e.data && e.data.type === 'sellwinar-resize' && e.data.id === smartVideoId) {
                iframe.style.height = e.data.height + 'px';
            }
        });
    }
})();
