<?php

namespace App\Services;

use App\Models\User;

class UserService
{
    /**
     * Lấy thông tin người dùng theo ID.
     *
     * @param  int  $id
     * @return \App\Models\User
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function getById($id)
    {
        return User::findOrFail($id);
    }

    /**
     * Lấy danh sách tất cả người dùng.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll()
    {
        return User::all();
    }

    /**
     * Tạo mới người dùng.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    public function create(array $data)
    {
        return User::create($data);
    }

    /**
     * Cập nhật người dùng.
     *
     * @param  int    $id
     * @param  array  $data
     * @return \App\Models\User
     */
    public function update($id, array $data)
    {
        $user = User::findOrFail($id);
        $user->update($data);
        return $user;
    }

    /**
     * Xóa người dùng.
     *
     * @param  int  $id
     * @return bool|null
     */
    public function delete($id)
    {
        $user = User::findOrFail($id);
        return $user->delete();
    }
}
