/**
* Skip import during onboarding - go directly to dashboard
*/
public function skipImport(Request $request)
{
$user = auth()->user();

if ($user && $user->role === 'admin') {
$user->update([
'has_imported' => true,
'has_viewed_details' => true,
'has_viewed_stock' => true,
]);

return redirect()->route('kp.dashboard')
->with('success', 'Onboarding dilewati. Anda dapat mengimport data kapan saja dari menu Import CSV SID.');
}

return redirect()->route('kp.import');
}

/**
* Complete Daftar Stok step during onboarding
*/
public function completeDaftarStok(Request $request)
{
$user = auth()->user();

if ($user && $user->role === 'admin' && !$user->has_viewed_stock) {
$user->update(['has_viewed_stock' => true]);

return redirect()->route('kp.dashboard')
->with('success', 'Setup selesai! Selamat datang di dashboard.');
}

return redirect()->route('kp.dashboard');
}