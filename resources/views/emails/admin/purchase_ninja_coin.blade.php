<h2>Đơn mua xu Ninja mới</h2>

<p><strong>Người dùng:</strong> {{ $user->name }}</p>
<p><strong>User ID:</strong> {{ $user->id }}</p>

<hr>

<p><strong>Tên nhân vật:</strong> {{ $transaction->character_name }}</p>
<p><strong>Server:</strong> {{ $transaction->server }}</p>
<p><strong>Số tiền:</strong> <b>{{ number_format($transaction->amount) }} đ</b></p>
<p><strong>Đơn giá:</strong> x {{ number_format($transaction->price) }}</p>
<p><strong>Số xu:</strong> {{ number_format($transaction->coin) }}</p>
<p><strong>Thời gian:</strong> {{ $transaction->created_at }}</p>

<hr>

<p>
    👉 Vào trang admin để xử lý giao dịch.
</p>
