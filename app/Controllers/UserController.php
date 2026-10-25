<?php

namespace App\Controllers;

use App\Models\User;
use Bpjs\Framework\Helpers\BaseController;
use Bpjs\Framework\Core\Request;
use Bpjs\Framework\Helpers\View;

class UserController extends BaseController
{
    // Controller logic here
    private const ROLES = ['SUPERADMIN', 'ADMIN', 'VIEWER'];

    /* ============================================================
       LIST — GET /admin/users/list
       ============================================================ */
    public function list(Request $request)
    {
        $q = trim((string) ($request->q ?? ''));
        $query = User::query();

        if ($q !== '') {
            $query->where(function ($s) use ($q) {
                $s->where('name',     'LIKE', "%{$q}%")
                  ->orWhere('username', 'LIKE', "%{$q}%");
            });
        }

        $rows = $query->orderBy('name', 'ASC')->get();

        return $this->json([
            'data' => array_map(fn($u) => [
                'id'         => (int) $u->id,
                'name'       => $u->name,
                'username'   => $u->username,
                'role'       => $u->role,
                'created_at' => $u->created_at,
            ], $rows),
        ], 200);
    }

    /* ============================================================
       STORE — POST /admin/users
       ============================================================ */
    public function store(Request $request)
    {
        if (!$request->validate([
            'name'     => 'required|max:100',
            'username' => 'required|max:50',
            'role'     => 'required',
            'password' => 'required|min:6',
        ])) return $this->json(['errors' => $request->errors()], 422);

        $username = strtolower(trim((string) $request->username));

        if (User::query()->where('username', '=', $username)->first()) {
            return $this->json(['errors' => ['username' => 'Username sudah dipakai.']], 422);
        }

        $u = User::create([
            'name'     => trim((string) $request->name),
            'username' => $username,
            'password' => password_hash((string) $request->password, PASSWORD_DEFAULT),
            'role'     => in_array($request->role, self::ROLES, true)
                          ? $request->role : 'VIEWER',
        ]);

        return $this->json([
            'message' => 'User ditambahkan.',
            'data'    => ['id' => (int) $u->id],
        ], 201);
    }

    /* ============================================================
       UPDATE — PUT /admin/users/{id}
       ============================================================ */
    public function update(Request $request, $id)
    {
        $u = User::find((int) $id);
        if (!$u) return $this->json(['message' => 'User tidak ditemukan.'], 404);

        $username = strtolower(trim((string) $request->username));

        $dup = User::query()
            ->where('username', '=', $username)
            ->where('id', '!=', (int) $id)
            ->first();
        if ($dup) {
            return $this->json(['errors' => ['username' => 'Username sudah dipakai user lain.']], 422);
        }

        $payload = [
            'name'     => trim((string) $request->name),
            'username' => $username,
            'role'     => in_array($request->role, self::ROLES, true)
                          ? $request->role : $u->role,
        ];

        $newPass = (string) $request->password;
        if ($newPass !== '') {
            if (strlen($newPass) < 6) {
                return $this->json(['errors' => ['password' => 'Password minimal 6 karakter.']], 422);
            }
            $payload['password'] = password_hash($newPass, PASSWORD_DEFAULT);
        }

        $u->update($payload);
        return $this->json(['message' => 'User diperbarui.'], 200);
    }

    /* ============================================================
       DESTROY — DELETE /admin/users/{id}
       ============================================================ */
    public function destroy($id)
    {
        $u = User::find((int) $id);
        if (!$u) return $this->json(['message' => 'User tidak ditemukan.'], 404);

        // Cegah hapus akun sendiri
        $me = $_SESSION['user']['id'] ?? null;
        if ($me && (int) $me === (int) $id) {
            return $this->json(['message' => 'Tidak bisa menghapus akun yang sedang login.'], 422);
        }

        // Minimal harus ada 1 SUPERADMIN
        if ($u->role === 'SUPERADMIN') {
            $countSuper = User::query()->where('role', '=', 'SUPERADMIN')->get();
            if (count($countSuper) <= 1) {
                return $this->json(['message' => 'Tidak bisa menghapus SUPERADMIN terakhir.'], 422);
            }
        }

        $u->delete();
        return $this->json(['message' => 'User dihapus.'], 200);
    }
}
