<?php

return [

    'accepted' => ':attribute phải được chấp nhận.',
    'accepted_if' => ':attribute phải được chấp nhận khi :other là :value.',
    'active_url' => ':attribute không phải là URL hợp lệ.',
    'after' => ':attribute phải là ngày sau :date.',
    'after_or_equal' => ':attribute phải là ngày sau hoặc bằng :date.',
    'alpha' => ':attribute chỉ được chứa chữ cái.',
    'alpha_dash' => ':attribute chỉ được chứa chữ cái, số, dấu gạch ngang và gạch dưới.',
    'alpha_num' => ':attribute chỉ được chứa chữ cái và số.',
    'array' => ':attribute phải là một mảng.',
    'before' => ':attribute phải là ngày trước :date.',
    'before_or_equal' => ':attribute phải là ngày trước hoặc bằng :date.',

    'between' => [
        'numeric' => ':attribute phải nằm trong khoảng từ :min đến :max.',
        'file' => ':attribute phải có dung lượng từ :min đến :max KB.',
        'string' => ':attribute phải có độ dài từ :min đến :max ký tự.',
        'array' => ':attribute phải có từ :min đến :max phần tử.',
    ],

    'boolean' => ':attribute phải là true hoặc false.',
    'confirmed' => ':attribute xác nhận không khớp.',
    'current_password' => 'Mật khẩu hiện tại không đúng.',
    'date' => ':attribute không phải là ngày hợp lệ.',
    'date_equals' => ':attribute phải là ngày bằng với :date.',
    'date_format' => ':attribute không đúng định dạng :format.',
    'declined' => ':attribute phải bị từ chối.',
    'declined_if' => ':attribute phải bị từ chối khi :other là :value.',
    'different' => ':attribute và :other phải khác nhau.',
    'digits' => ':attribute phải có :digits chữ số.',
    'digits_between' => ':attribute phải có từ :min đến :max chữ số.',
    'dimensions' => ':attribute có kích thước hình ảnh không hợp lệ.',
    'distinct' => ':attribute có giá trị trùng lặp.',
    'email' => ':attribute không đúng định dạng email.',
    'ends_with' => ':attribute phải kết thúc bằng một trong các giá trị: :values.',
    'enum' => ':attribute được chọn không hợp lệ.',
    'exists' => ':attribute được chọn không tồn tại.',
    'file' => ':attribute phải là một tệp.',
    'filled' => ':attribute không được để trống.',

    'gt' => [
        'numeric' => ':attribute phải lớn hơn :value.',
        'file' => ':attribute phải lớn hơn :value KB.',
        'string' => ':attribute phải dài hơn :value ký tự.',
        'array' => ':attribute phải có nhiều hơn :value phần tử.',
    ],

    'gte' => [
        'numeric' => ':attribute phải lớn hơn hoặc bằng :value.',
        'file' => ':attribute phải lớn hơn hoặc bằng :value KB.',
        'string' => ':attribute phải dài hơn hoặc bằng :value ký tự.',
        'array' => ':attribute phải có ít nhất :value phần tử.',
    ],

    'image' => ':attribute phải là hình ảnh.',
    'in' => ':attribute được chọn không hợp lệ.',
    'in_array' => ':attribute không tồn tại trong :other.',
    'integer' => ':attribute phải là số nguyên.',
    'ip' => ':attribute phải là địa chỉ IP hợp lệ.',
    'ipv4' => ':attribute phải là địa chỉ IPv4 hợp lệ.',
    'ipv6' => ':attribute phải là địa chỉ IPv6 hợp lệ.',
    'json' => ':attribute phải là chuỗi JSON hợp lệ.',

    'lt' => [
        'numeric' => ':attribute phải nhỏ hơn :value.',
        'file' => ':attribute phải nhỏ hơn :value KB.',
        'string' => ':attribute phải ngắn hơn :value ký tự.',
        'array' => ':attribute phải có ít hơn :value phần tử.',
    ],

    'lte' => [
        'numeric' => ':attribute phải nhỏ hơn hoặc bằng :value.',
        'file' => ':attribute phải nhỏ hơn hoặc bằng :value KB.',
        'string' => ':attribute phải ngắn hơn hoặc bằng :value ký tự.',
        'array' => ':attribute không được có nhiều hơn :value phần tử.',
    ],

    'mac_address' => ':attribute phải là địa chỉ MAC hợp lệ.',

    'max' => [
        'numeric' => ':attribute không được lớn hơn :max.',
        'file' => ':attribute không được lớn hơn :max KB.',
        'string' => ':attribute không được dài hơn :max ký tự.',
        'array' => ':attribute không được có nhiều hơn :max phần tử.',
    ],

    'mimes' => ':attribute phải là tệp có định dạng: :values.',
    'mimetypes' => ':attribute phải là tệp có định dạng: :values.',

    'min' => [
        'numeric' => ':attribute phải lớn hơn hoặc bằng :min.',
        'file' => ':attribute phải có dung lượng ít nhất :min KB.',
        'string' => ':attribute phải có ít nhất :min ký tự.',
        'array' => ':attribute phải có ít nhất :min phần tử.',
    ],

    'multiple_of' => ':attribute phải là bội số của :value.',
    'not_in' => ':attribute được chọn không hợp lệ.',
    'not_regex' => ':attribute có định dạng không hợp lệ.',
    'numeric' => ':attribute phải là số.',
    'password' => 'Mật khẩu không đúng.',
    'present' => ':attribute phải tồn tại.',
    'prohibited' => ':attribute không được phép xuất hiện.',
    'prohibited_if' => ':attribute không được phép xuất hiện khi :other là :value.',
    'prohibited_unless' => ':attribute không được phép xuất hiện trừ khi :other thuộc :values.',
    'prohibits' => ':attribute không cho phép :other xuất hiện.',
    'regex' => ':attribute có định dạng không hợp lệ.',
    'required' => ':attribute không được để trống.',
    'required_array_keys' => ':attribute phải chứa các khóa: :values.',
    'required_if' => ':attribute là bắt buộc khi :other là :value.',
    'required_unless' => ':attribute là bắt buộc trừ khi :other thuộc :values.',
    'required_with' => ':attribute là bắt buộc khi có :values.',
    'required_with_all' => ':attribute là bắt buộc khi có đầy đủ :values.',
    'required_without' => ':attribute là bắt buộc khi không có :values.',
    'required_without_all' => ':attribute là bắt buộc khi không có bất kỳ :values nào.',
    'same' => ':attribute và :other phải giống nhau.',

    'size' => [
        'numeric' => ':attribute phải có giá trị là :size.',
        'file' => ':attribute phải có dung lượng :size KB.',
        'string' => ':attribute phải có độ dài :size ký tự.',
        'array' => ':attribute phải chứa :size phần tử.',
    ],

    'starts_with' => ':attribute phải bắt đầu bằng một trong các giá trị: :values.',
    'string' => ':attribute phải là chuỗi.',
    'timezone' => ':attribute phải là múi giờ hợp lệ.',
    'unique' => ':attribute đã tồn tại.',
    'uploaded' => ':attribute tải lên thất bại.',
    'url' => ':attribute không phải là URL hợp lệ.',
    'uuid' => ':attribute phải là UUID hợp lệ.',

    'attributes' => [
        'email' => 'Email',
        'password' => 'Mật khẩu',
        'password_confirmation' => 'Mật khẩu xác nhận',
        'name' => 'Tên hiển thị',
        'phone' => 'Số điện thoại',
        'selling_price' => 'Giá bán',
        'purchase_price' => 'Giá nhập',
        'transfer_pin' => 'Mã pin',
    ],
];
