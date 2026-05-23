<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} - @yield('title', 'Hôtels')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root { --primary: #1a3c5e; --gold: #c9a84c; }
        body { font-family: 'Segoe UI', sans-serif; }
        .navbar { background: var(--primary) !important; }
        .btn-hotel { background: var(--gold); color: white; border: none; }
        .btn-hotel:hover { background: #b8963d; color: white; }
        .star-gold { color: var(--gold); }
        /* Chat widget */
        #chat-widget { position: fixed; bottom: 20px; right: 20px; z-index: 9999; }
        #chat-box { width: 360px; height: 480px; background: white; border-radius: 16px;
                    box-shadow: 0 8px 32px rgba(0,0,0,0.18); display: none; flex-direction: column; }
        #chat-box.open { display: flex; }
        #chat-messages { flex: 1; overflow-y: auto; padding: 16px; }
        .msg-user { background: var(--primary); color: white; border-radius: 16px 16px 4px 16px;
                    padding: 10px 14px; margin: 6px 0 6px auto; max-width: 80%; }
        .msg-bot  { background: #f0f4f8; color: #1a1a1a; border-radius: 16px 16px 16px 4px;
                    padding: 10px 14px; margin: 6px auto 6px 0; max-width: 85%; }
        .msg-typing { opacity: 0.6; font-style: italic; }
        #chat-toggle { width: 56px; height: 56px; border-radius: 50%; background: var(--primary);
                       color: white; border: none; font-size: 24px; box-shadow: 0 4px 16px rgba(0,0,0,0.2); }
    </style>
    @stack('styles')
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container">
        <a class="navbar-brand fw-bold" href="{{ route('home') }}">
            <i class="bi bi-building"></i> HotelApp
        </a>
        <div class="navbar-nav ms-auto">
            <a class="nav-link" href="{{ route('hotels.index') }}">Hôtels</a>
            @auth
                <a class="nav-link" href="{{ route('reservations.index') }}">Mes réservations</a>
                @role('admin')
                    <a class="nav-link" href="{{ route('admin.dashboard') }}">
                        <i class="bi bi-speedometer2"></i> Admin
                    </a>
                @endrole
                <form method="POST" action="{{ route('logout') }}" class="d-inline">
                    @csrf
                    <button class="btn btn-sm btn-outline-light ms-2">Déconnexion</button>
                </form>
            @else
                <a class="nav-link" href="{{ route('login') }}">Connexion</a>
                <a class="nav-link" href="{{ route('register') }}">S'inscrire</a>
            @endauth
        </div>
    </div>
</nav>

<main class="container py-4">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif
    @yield('content')
</main>

<!-- Chatbot Widget -->
@auth
<div id="chat-widget">
    <div id="chat-box">
        <div class="d-flex align-items-center p-3" style="background: var(--primary); border-radius: 16px 16px 0 0;">
            <i class="bi bi-robot text-white me-2 fs-5"></i>
            <span class="text-white fw-bold">Assistant Hôtel</span>
            <button onclick="toggleChat()" class="btn-close btn-close-white ms-auto"></button>
        </div>
        <div id="chat-messages">
            <div class="msg-bot">Bonjour ! Je suis votre assistant hôtelier. Comment puis-je vous aider ?</div>
        </div>
        <div class="p-2 border-top d-flex gap-2">
            <input type="text" id="chat-input" class="form-control form-control-sm"
                   placeholder="Posez votre question..." onkeypress="if(event.key==='Enter')sendMessage()">
            <button onclick="sendMessage()" class="btn btn-sm btn-hotel">
                <i class="bi bi-send"></i>
            </button>
        </div>
    </div>
    <button id="chat-toggle" onclick="toggleChat()" title="Assistant IA">
        <i class="bi bi-chat-dots"></i>
    </button>
</div>

<script>
function toggleChat() {
    document.getElementById('chat-box').classList.toggle('open');
}

async function sendMessage() {
    const input = document.getElementById('chat-input');
    const msg = input.value.trim();
    if (!msg) return;

    const messages = document.getElementById('chat-messages');

    // Afficher le message utilisateur
    messages.innerHTML += `<div class="msg-user">${msg}</div>`;
    input.value = '';

    // Afficher "en train d'écrire..."
    const typingId = 'typing-' + Date.now();
    messages.innerHTML += `<div class="msg-bot msg-typing" id="${typingId}">En train d'écrire...</div>`;
    messages.scrollTop = messages.scrollHeight;

    try {
        const res = await fetch('/api/chatbot', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ message: msg })
        });

        const data = await res.json();
        document.getElementById(typingId).remove();

        if (data.success) {
            messages.innerHTML += `<div class="msg-bot">${data.reply}</div>`;
        } else {
            messages.innerHTML += `<div class="msg-bot text-danger">Erreur : ${data.message}</div>`;
        }
    } catch (err) {
        document.getElementById(typingId).remove();
        messages.innerHTML += `<div class="msg-bot text-danger">Service temporairement indisponible.</div>`;
    }

    messages.scrollTop = messages.scrollHeight;
}
</script>
@endauth

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>