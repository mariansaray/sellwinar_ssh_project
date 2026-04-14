<!DOCTYPE html>
<html lang="sk">
<head><meta charset="UTF-8"></head>
<body>
    <script>
        window.parent.postMessage({
            type: 'sellwinar-registered',
            slug: '{{ $webinar->slug }}',
            redirectUrl: '{{ $redirectUrl }}'
        }, '*');
        // Fallback: redirect the iframe itself after 1s
        setTimeout(function() { window.top.location.href = '{{ $redirectUrl }}'; }, 1000);
    </script>
</body>
</html>
