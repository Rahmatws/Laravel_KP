<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ResetAdminOnboardingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Hapus admin lain selain rahmat@gmail.com
        DB::table('users')
            ->where('role', 'admin')
            ->where('email', '!=', 'rahmat@gmail.com')
            ->delete();

        echo "✓ Deleted other admin accounts\n";

        // Reset onboarding flags untuk rahmat@gmail.com
        $user = User::where('email', 'rahmat@gmail.com')->first();

        if ($user) {
            $user->update([
                'has_imported' => false,
                'has_viewed_details' => false,
                'has_viewed_stock' => false,
            ]);

            echo "✓ Reset onboarding flags for {$user->email}\n";
            echo "  - has_imported: false\n";
            echo "  - has_viewed_details: false\n";
            echo "  - has_viewed_stock: false\n";
        } else {
            echo "✗ User rahmat@gmail.com not found!\n";
        }

        // Tampilkan semua admin
        echo "\nCurrent admin users:\n";
        $admins = User::where('role', 'admin')->get(['id', 'name', 'email', 'has_imported', 'has_viewed_details', 'has_viewed_stock']);
        foreach ($admins as $admin) {
            echo "  - {$admin->email} (imported: " . ($admin->has_imported ? 'yes' : 'no') . ", viewed_details: " . ($admin->has_viewed_details ? 'yes' : 'no') . ", viewed_stock: " . ($admin->has_viewed_stock ? 'yes' : 'no') . ")\n";
        }
    }
}
