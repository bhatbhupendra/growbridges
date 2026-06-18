<x-app-layout>

<style>
    .page-wrap{
        background:#f3f4f6;
        min-height:calc(100vh - 80px);
        padding:24px;
    }

    .card-box{
        background:#fff;
        border-radius:14px;
        border:1px solid #e5e7eb;
        box-shadow:0 8px 24px rgba(15,23,42,.06);
    }

    .side-box{
        padding:16px;
        font-size:13px;
    }

    .chat-header{
        padding:18px 22px;
        display:flex;
        justify-content:space-between;
        align-items:center;
        margin-bottom:18px;
    }

    .chat-box{
        height:410px;
        overflow-y:auto;
        padding:20px;
        background:#f8fafc;
    }

    .message-row{
        display:flex;
        margin-bottom:12px;
    }

    .message-row.mine{
        justify-content:flex-end;
    }

    .message-row.other{
        justify-content:flex-start;
    }

    .message-bubble{
        max-width:70%;
        padding:11px 15px;
        border-radius:14px;
        font-size:14px;
        line-height:1.5;
        border:1px solid #e5e7eb;
        word-wrap:break-word;
    }

    .message-row.mine .message-bubble{
        background:#0d6efd;
        color:#fff;
        border-bottom-right-radius:4px;
    }

    .message-row.other .message-bubble{
        background:#fff;
        color:#111827;
        border-bottom-left-radius:4px;
    }

    .chat-form{
        padding:16px;
        border-top:1px solid #e5e7eb;
        background:#fff;
        border-radius:0 0 14px 14px;
    }

    .chat-input{
        border-radius:10px;
        height:44px;
    }

    .send-btn{
        border-radius:10px;
        padding:0 24px;
        font-weight:700;
    }

    @media(max-width:991px){
        .page-wrap{
            padding:12px;
        }

        .chat-box{
            height:430px;
        }

        .message-bubble{
            max-width:85%;
        }
    }
</style>

<div class="page-wrap">

    <div class="container-fluid">

        <div class="row g-3">

            <!-- LEFT CHAT AREA -->
            <div class="col-lg-10">

                <!-- Header -->
                <div class="card-box chat-header">
                    <div>
                        <h5 class="mb-1" style="font-weight:800;">
                            Chat with {{ $receiver->name }}
                        </h5>

                        <div class="text-muted" style="font-size:13px;">
                            Send and view messages
                        </div>
                    </div>

                    <button onclick="history.back()" class="btn btn-outline-primary btn-sm">
                        ← Go Back
                    </button>
                </div>

                <!-- Chat Card -->
                <div class="card-box">

                    <div class="chat-box" id="chatBox">

                        @forelse($messages as $message)

                            @php
                                $isMine = $message->sender_id == $sender->id;
                            @endphp

                            <div class="message-row {{ $isMine ? 'mine' : 'other' }}">
                                <div class="message-bubble">
                                    {{ $message->message }}
                                </div>
                            </div>

                        @empty

                            <div class="text-center text-muted mt-5">
                                No messages yet. Start the conversation.
                            </div>

                        @endforelse

                    </div>

                    <form method="POST"
                          action="{{ route('chat.send', [$receiver->id, $sender->id]) }}"
                          class="chat-form">
                        @csrf

                        <div class="d-flex gap-2">
                            <input type="text"
                                   name="message"
                                   class="form-control chat-input"
                                   placeholder="Type message..."
                                   required
                                   autocomplete="off">

                            <button class="btn btn-primary send-btn">
                                Send
                            </button>
                        </div>
                    </form>

                </div>

            </div>

            <!-- RIGHT SUPPORT SIDEBAR -->
            <div class="col-lg-2">

                <div class="card-box side-box">

                    <!-- Contact Section -->
                    <div class="text-center mb-3">

                        <div class="mb-2" style="font-weight:800;">
                            📞 Chat via What's App or Viber
                        </div>

                    </div>

                    <!-- WhatsApp -->
                    <div class="text-center">

                        <div class="mt-2 fw-bold text-success">
                            WhatsApp
                        </div>

                        <small class="text-muted">
                            +819091327070
                        </small>

                        <a href="https://wa.me/819091327070"
                           target="_blank">

                            <img src="{{ asset('images/whatsapp.jpeg') }}"
                                 class="img-fluid rounded border p-1 bg-white mt-2"
                                 alt="WhatsApp QR"
                                 style="max-width:180px;">

                        </a>

                    </div>


                    <!-- Viber -->
                    <div class="text-center">

                        <div class="mt-2 fw-bold" style="color:#7360F2;">
                            Viber
                        </div>

                        <small class="text-muted">
                            +819091327070
                        </small>

                        <a href="viber://chat?number=%2B819091327070">

                            <img src="{{ asset('images/viber.jpeg') }}"
                                 class="img-fluid rounded border p-1 bg-white mt-2"
                                 alt="Viber QR"
                                 style="max-width:180px;">

                        </a>

                    </div>

                    <hr class="my-3">

                    <h6 class="mb-2" style="font-weight:800;">
                        About this page
                    </h6>

                    <div class="text-muted" style="font-size:12px; line-height:1.5;">
                        Chat directly with Grow Bridges admin for student,
                        document and admission support.
                    </div>

                    <hr class="my-3">

                    <div class="mb-2" style="font-weight:800;">
                        Tips
                    </div>

                    <div class="p-2 rounded"
                         style="background:#f8fafc; border:1px solid #e5e7eb; font-size:12px;">
                        New messages will appear at the bottom.
                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

<script>
    const chatBox = document.getElementById('chatBox');

    if (chatBox) {
        chatBox.scrollTop = chatBox.scrollHeight;
    }
</script>

</x-app-layout>