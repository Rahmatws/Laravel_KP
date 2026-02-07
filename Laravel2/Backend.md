# Dokumentasi Kode Backend - Stock Monitoring Assistant

Dokumen ini berisi kumpulan kode backend untuk aplikasi Stock Monitoring Assistant, termasuk Models, Controllers, Routes, dan skema Database.

---

## 1. Models

### [Category.php](file:///d:/laragon/www/Laravel2/app/Models/Category.php)
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'is_active',
    ];

    public function items()
    {
        return $this->hasMany(Item::class);
    }
}
```

### [Item.php](file:///d:/laragon/www/Laravel2/app/Models/Item.php)
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'purchase_price',
        'sale_price',
        'stock',
        'min_stock',
        'unit',
        'notif_active',
        'category_id',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function changes()
    {
        return $this->hasMany(StockChange::class);
    }
}
```

### [StockChange.php](file:///d:/laragon/www/Laravel2/app/Models/StockChange.php)
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockChange extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'user_id',
        'change_type',
        'qty',
        'note',
        'occurred_at',
    ];

    protected $casts = [
        'occurred_at' => 'datetime',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
```

### [Setting.php](file:///d:/laragon/www/Laravel2/app/Models/Setting.php)
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class Setting extends Model
{
    protected $table = 'settings';
    public $timestamps = false;
    protected $fillable = ['key', 'value'];

    public static function get(string $key, $default = null)
    {
        if (!Schema::hasTable('settings')) {
            return $default;
        }
        $row = static::query()->where('key', $key)->first();
        if (!$row) {
            return $default;
        }
        return static::decode($row->value);
    }

    public static function set(string $key, $value): void
    {
        if (!Schema::hasTable('settings')) {
            return;
        }
        $encoded = static::encode($value);
        static::query()->updateOrCreate(['key' => $key], ['value' => $encoded]);
    }

    protected static function encode($value): string
    {
        if (is_array($value) || is_object($value)) {
            return json_encode($value, JSON_UNESCAPED_UNICODE);
        }
        return (string)$value;
    }

    protected static function decode(string $value)
    {
        $trim = trim($value);
        if ($trim === '') {
            return '';
        }
        $jsonFirst = substr($trim, 0, 1);
        if ($jsonFirst === '{' || $jsonFirst === '[') {
            $decoded = json_decode($trim, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return $decoded;
            }
        }
        return $value;
    }
}
```

### [User.php](file:///d:/laragon/www/Laravel2/app/Models/User.php)
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'role',
        'password',
        'has_imported',
        'has_viewed_details',
        'has_viewed_stock',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
```

---

## 2. Controllers

### [AuthController.php](file:///d:/laragon/www/Laravel2/app/Http/Controllers/AuthController.php)
```php
<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function loginView()
    {
        return view('kp.login_blade');
    }

    public function registerView()
    {
        return view('kp.registrasi');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'fullname' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'in:staff'],
        ]);

        try {
            $user = User::create([
                'name' => $data['fullname'],
                'email' => $data['email'],
                'role' => $data['role'],
                'password' => Hash::make($data['password']),
            ]);
        } catch (\Throwable $e) {
            return back()->with('error', 'Registrasi gagal.')->withInput();
        }

        return redirect('/')->with('success', 'Registrasi berhasil, silakan login.');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            $user = Auth::user();

            if ($user->role === 'admin' || $user->role === 'staff') {
                $user->update([
                    'has_imported' => false,
                    'has_viewed_details' => false,
                    'has_viewed_stock' => false,
                ]);
                return redirect()->route('kp.import');
            }
            return redirect()->route('kp.dashboard');
        }

        return back()->withErrors(['email' => 'Kredensial tidak valid.'])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
```

### [StockController.php](file:///d:/laragon/www/Laravel2/app/Http/Controllers/StockController.php)
*(Hanya menampilkan fungsi-fungsi utama karena ukuran file yang sangat besar)*

Fungsi Utama:
- `dashboard()`: Menampilkan ringkasan stok dan grafik.
- `importSid()`: Memproses import data dari CSV/XLS SID Retail Pro.
- `koreksiStokPost()`: Mencatat penyesuaian stok fisik.
- `processExport()`: Mengekspor laporan ke CSV/Excel/PDF.
- `importPenjualanPost()`: Sinkronisasi data penjualan dari aplikasi eksternal.

---

## 3. Routes

### [web.php](file:///d:/laragon/www/Laravel2/routes/web.php)
```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\StockController;

Route::get('/', [AuthController::class, 'loginView']);

Route::prefix('kp')->name('kp.')->group(function () {
    Route::middleware('onboarding.check')->group(function () {
        Route::get('dashboard', [StockController::class, 'dashboard'])->name('dashboard');
        Route::get('daftar_stok', [StockController::class, 'daftarStok'])->name('daftar_stok');
        Route::get('kelola_notifikasi', [StockController::class, 'kelolaNotifikasi'])->name('kelola_notifikasi');
        Route::get('riwayat_stok', [StockController::class, 'riwayatStok'])->name('riwayat_stok');
        Route::get('koreksi_stok', [StockController::class, 'koreksiStok'])->name('koreksi_stok');
        Route::post('koreksi_stok', [StockController::class, 'koreksiStokPost'])->name('koreksi_stok.post');
        Route::get('kategori_barang', [StockController::class, 'kategoriBarang'])->name('kategori_barang');
        Route::get('export/process', [StockController::class, 'processExport'])->name('export.process');
        Route::get('import_penjualan', [StockController::class, 'importPenjualanView'])->name('import_penjualan');
        Route::post('import_penjualan', [StockController::class, 'importPenjualanPost'])->name('import_penjualan.post');
    });

    Route::view('import', 'kp.import')->name('import');
    Route::post('import', [StockController::class, 'importSid'])->name('import.post');
    Route::get('analisis-stok', [StockController::class, 'grafikStok'])->name('analisis_stok');
    Route::get('detail_barang', [StockController::class, 'detailBarang'])->name('detail_barang');
    Route::post('login', [AuthController::class, 'login'])->name('login.post');
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
});
```

---

## 4. Database Schema (database.sql)

```sql
CREATE TABLE IF NOT EXISTS `categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `categories_name_unique` (`name`)
);

CREATE TABLE IF NOT EXISTS `items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(50) NOT NULL,
  `name` varchar(255) NOT NULL,
  `purchase_price` decimal(12,2) NOT NULL DEFAULT 0.00,
  `sale_price` decimal(12,2) NOT NULL DEFAULT 0.00,
  `stock` int NOT NULL DEFAULT 0,
  `min_stock` int NOT NULL DEFAULT 0,
  `notif_active` tinyint(1) NOT NULL DEFAULT 1,
  `category_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `items_code_unique` (`code`),
  CONSTRAINT `items_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS `stock_changes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `item_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `change_type` enum('in','out','sid_sync') NOT NULL,
  `qty` int NOT NULL,
  `note` varchar(255) DEFAULT NULL,
  `occurred_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `stock_changes_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE
);
```
