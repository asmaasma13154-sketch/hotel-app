@'
@extends('layouts.app')
@section('title', 'Chatbot')
@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <h2><i class="bi bi-robot me-2"></i>Assistant Hôtel</h2>
        <div class="card shadow-sm border-0 mt-3">
            <div class="card-body" id="chat-messages" style="height:400px; overflow-y:auto;">
                <div class="msg-bot p-2 mb-2 bg-light rounded">Bonjour ! Comment puis-je vous aider ?</div>
                @foreach($history as $h)
                    <div class="msg-user p-2 mb-2 text-end">{{ $h->message }}</div>
                    <div class="msg-bot p-2 mb-2 bg-light rounded">{{ $h->response }}</div>
                @endforeach
            </div>
            <div class="card-footer d-flex gap-2">
                <input type="text" id="chat-input" class="form-control" placeholder="Posez votre question..." onkeypress="if(event.key==='Enter') sendMessage()">
                <button onclick="sendMessage()" class="btn btn-hotel"><i class="bi bi-send"></i></button>
            </div>
        </div>
        <form method="POST" action="{{ route('chatbot.clear') }}" class="mt-2">
            @csrf @method('DELETE')
            <button class="btn btn-sm btn-outline-danger">Effacer l'historique</button>
        </form>
    </div>
</div>
@endsection
'@ | Set-Content -Path "resources\views\chatbot\index.blade.php" -Encoding UTF8