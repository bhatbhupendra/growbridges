<x-app-layout>
<div class="container">

    <h3>Chat with {{ $receiver->name }} <button style="color: #007bff; text-decoration: none; font-weight: bold;" onclick="history.back()">Go Back</button></h3>

    <div style="border:1px solid #ddd; height:500px; overflow-y:auto; padding:15px; background:#f9f9f9;">
        @foreach($messages as $message)
            @php
                $isMine = $message->sender_id == $sender->id;
            @endphp

            <div style="text-align: {{ $isMine ? 'right' : 'left' }}; margin-bottom:10px;">
                <span style="
                    display:inline-block;
                    padding:10px 15px;
                    border-radius:10px;
                    background: {{ $isMine ? '#d1e7dd' : '#ffffff' }};
                    border:1px solid #ddd;
                    max-width:70%;
                ">
                    {{ $message->message }}
                </span>
            </div>
        @endforeach
    </div>

    <form method="POST" action="{{ route('chat.send', [$receiver->id, $sender->id]) }}" style="margin-top:15px;">
        @csrf

        <div style="display:flex; gap:10px;">
            <input type="text" name="message" class="form-control" placeholder="Type message..." required>

            <button class="btn btn-primary">
                Send
            </button>
        </div>
    </form>

</div>

</x-app-layout>