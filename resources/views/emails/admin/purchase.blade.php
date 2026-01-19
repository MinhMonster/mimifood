<h2>Đơn mua tài khoản mới</h2>

<p><strong>Người dùng:</strong> {{ $user->name }}</p>
<p><strong>User ID:</strong> {{ $user->id }}</p>

<hr>

<p><strong>Loại tài khoản:</strong> {{ strtoupper($history->account_type) }}</p>
<p><strong>Mã tài khoản:</strong> {{ number_format($history->account_code) }}</p>
<p><strong>Giá bán:</strong> {{ number_format($history->selling_price) }} đ</p>
<p><strong>Giá nhập:</strong> {{ number_format($history->purchase_price) }} đ</p>
<p><strong>Thời gian:</strong> {{ $history->created_at }}</p>

<hr>

<p>
    👉 Vào trang admin để xử lý giao dịch.
</p>
