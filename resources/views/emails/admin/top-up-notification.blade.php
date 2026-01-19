<h2>Yêu cầu nạp tiền mới</h2>

<p><strong>Người dùng:</strong> {{ $user->name }}</p>
<p><strong>User ID:</strong> {{ $user->id }}</p>

<hr>

<p><strong>Mã giao dịch:</strong> #{{ $transaction->id }}</p>
<p><strong>Số tiền:</strong> {{ number_format($transaction->amount) }} VND</p>
<p><strong>Trạng thái:</strong> {{ strtoupper($transaction->status) }}</p>
<p><strong>Ghi chú:</strong> {{ $transaction->note ?? '—' }}</p>
<p><strong>Thời gian:</strong> {{ $transaction->transaction_at }}</p>

<hr>

<p>
    👉 Vào trang admin để xử lý giao dịch.
</p>
