<h2>Có đơn nạp Carrot mới</h2>

<p><strong>Mã giao dịch:</strong> #{{ $transaction->id }}</p>
<p><strong>Người dùng:</strong> {{ $user->name }} (ID: {{ $user->id }})</p>

<hr>

<p><strong>Game:</strong> {{ $transaction->game_type }}</p>
<p><strong>Tài khoản:</strong> {{ $transaction->username }}</p>
<p><strong>Server:</strong> {{ $transaction->server }}</p>

<hr>

<p><strong>Mệnh giá Carrot:</strong> {{ number_format($transaction->amount) }}</p>
<p><strong>Giá tiền:</strong> {{ number_format($transaction->price) }}đ</p>
<p><strong>Thời gian:</strong> {{ $transaction->created_at }}</p>

<p>
    👉 Vào trang admin để xử lý giao dịch.
</p>
