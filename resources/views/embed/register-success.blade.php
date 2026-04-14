<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; margin: 0; background: transparent; }</style>
</head>
<body>
    <div class="p-6 text-center">
        <div class="w-12 h-12 mx-auto mb-3 rounded-full bg-green-50 flex items-center justify-center">
            <svg class="w-6 h-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
        </div>
        <h3 class="text-lg font-bold text-gray-800 mb-1">Ďakujeme za registráciu!</h3>
        <p class="text-sm text-gray-500">Pošleme vám pripomienku pred začiatkom webinára.</p>
    </div>
    <script>
        window.parent.postMessage({ type: 'sellwinar-registered', slug: '{{ $webinar->slug }}' }, '*');
        window.parent.postMessage({ type: 'sellwinar-resize', slug: '{{ $webinar->slug }}', height: document.body.scrollHeight }, '*');
    </script>
</body>
</html>
