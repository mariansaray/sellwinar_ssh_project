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
    <div class="p-4">
        @if($errors->any())
            <div class="mb-3 bg-red-50 border-l-4 border-red-500 rounded-lg p-3">
                @foreach($errors->all() as $error)
                    <p class="text-sm text-red-700">{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ url('/embed/register/' . $tenant->slug . '/' . $webinar->slug) }}" class="space-y-3">
            @csrf
            <div>
                <input type="text" name="first_name" placeholder="Vaše meno" value="{{ old('first_name') }}"
                       class="w-full px-4 py-3 border border-gray-200 rounded-lg text-sm focus:border-purple-600 focus:outline-none focus:ring-2 focus:ring-purple-600/20">
            </div>
            <div>
                <input type="email" name="email" placeholder="Váš e-mail *" required value="{{ old('email') }}"
                       class="w-full px-4 py-3 border border-gray-200 rounded-lg text-sm focus:border-purple-600 focus:outline-none focus:ring-2 focus:ring-purple-600/20">
            </div>
            @if(!empty($config['require_phone']))
            <div>
                <input type="tel" name="phone" placeholder="Telefón" value="{{ old('phone') }}"
                       class="w-full px-4 py-3 border border-gray-200 rounded-lg text-sm focus:border-purple-600 focus:outline-none focus:ring-2 focus:ring-purple-600/20">
            </div>
            @endif
            <button type="submit"
                    class="w-full py-3 text-white font-semibold rounded-lg transition-all hover:-translate-y-px"
                    style="background: {{ $config['primary_color'] ?? '#6C3AED' }}">
                {{ $config['cta_text'] ?? 'Registrovať sa zadarmo' }}
            </button>
        </form>
    </div>

    <script>
        // Send height to parent for auto-resize
        function sendHeight() {
            var height = document.body.scrollHeight;
            window.parent.postMessage({ type: 'sellwinar-resize', slug: '{{ $webinar->slug }}', height: height }, '*');
        }
        sendHeight();
        new MutationObserver(sendHeight).observe(document.body, { childList: true, subtree: true });
    </script>
</body>
</html>
