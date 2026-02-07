<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Item;
use App\Models\Setting;
use App\Models\StockChange;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Shuchkin\SimpleXLS;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Cache;

class StockController extends Controller
{
    public function dashboard(Request $request)
    {
        $days = max((int) $request->query('days', 14), 1);
        $to = now()->endOfDay();
        $from = now()->subDays($days - 1)->startOfDay();
        $totalItems = Item::count();
        $notifOn = (int) Setting::get('notif.global.active', cache()->get('notif.global.active', 1));
        $mode = (string) Setting::get('notif.global.mode', cache()->get('notif.global.mode', 'per_item'));
        $defaultMin = (int) Setting::get('notif.global.default_min', cache()->get('notif.global.default_min', 10));
        $categoryDefaults = [];
        if ($mode === 'category') {
            foreach (Category::orderBy('name')->get() as $cat) {
                $categoryDefaults[$cat->name] = (int) Setting::get('notif.category.' . $cat->name . '.default_min', $defaultMin);
            }
        }
        $itemsQ = Item::with('category')->get();
        $lowCount = 0;
        $zeroCount = Item::where('stock', 0)->count();
        foreach ($itemsQ as $it) {
            $catName = $it->category ? $it->category->name : null;
            $catMin = $catName !== null && array_key_exists($catName, $categoryDefaults) ? (int) $categoryDefaults[$catName] : $defaultMin;
            $threshold = $mode === 'global' ? $defaultMin : max((int) ($it->min_stock ?? 0), ($mode === 'category' ? $catMin : $defaultMin));
            if ((int) $it->stock > 0 && (int) $it->stock < $threshold) {
                $lowCount++;
            }
        }
        $transactionsToday = StockChange::whereBetween('occurred_at', [now()->startOfDay(), now()->endOfDay()])->count();
        $criticalItems = collect();
        $criticalCount = 0;
        if ($notifOn === 1) {
            if ($mode === 'global') {
                $baseQuery = Item::where('stock', '<', $defaultMin);
                $criticalCount = $baseQuery->count();
                $criticalItems = $baseQuery->orderBy('updated_at', 'desc')->limit(10)->get();
            } elseif ($mode === 'category') {
                $allCritical = $itemsQ->filter(function ($it) use ($categoryDefaults, $defaultMin) {
                    $catName = $it->category ? $it->category->name : null;
                    $catMin = $catName !== null && array_key_exists($catName, $categoryDefaults) ? (int) $categoryDefaults[$catName] : $defaultMin;
                    $threshold = max((int) ($it->min_stock ?? 0), $catMin);
                    return (int) $it->stock < $threshold;
                });
                $criticalCount = $allCritical->count();
                $criticalItems = $allCritical->sortByDesc('updated_at')->take(10)->values();
            } else {
                $baseQuery = Item::where('notif_active', 1)
                    ->where(function ($w) use ($defaultMin) {
                        $w->where(function ($q) use ($defaultMin) {
                            $q->where('min_stock', '>=', $defaultMin)->whereColumn('stock', '<', 'min_stock');
                        })->orWhere(function ($q) use ($defaultMin) {
                            $q->where(function ($x) use ($defaultMin) {
                                $x->whereNull('min_stock')
                                    ->orWhere('min_stock', '<=', 0)
                                    ->orWhere('min_stock', '<', $defaultMin);
                            })->where('stock', '<', $defaultMin);
                        });
                    });
                $criticalCount = $baseQuery->count();
                $criticalItems = $baseQuery->orderBy('updated_at', 'desc')->limit(10)->get();
            }
        }
        // Helper for abbreviation
        $abbreviate = function ($name) {
            $name = trim($name);
            if (strlen($name) <= 12)
                return $name;
            if (stripos($name, 'Thinwall') !== false)
                return 'Thin/TW';
            if (stripos($name, 'Plastik') !== false)
                return 'Plastik..';
            if (stripos($name, 'Kertas') !== false)
                return 'Kertas..';
            $parts = explode(' ', $name);
            return substr($parts[0], 0, 10) . (count($parts) > 1 ? '..' : '');
        };

        $stocksByCategoryRaw = Item::select('category_id', DB::raw('SUM(stock) as total'))
            ->groupBy('category_id')->get();

        $chartCategoryLabels = [];
        $chartCategoryValues = [];

        foreach ($stocksByCategoryRaw as $row) {
            $cat = Category::find($row->category_id);
            $name = $cat ? $cat->name : 'Tanpa Kategori';
            $chartCategoryLabels[] = $abbreviate($name);
            $chartCategoryValues[] = (int) $row->total;
        }

        $stocksByCategory = $stocksByCategoryRaw->map(function ($row) {
            $cat = Category::find($row->category_id);
            return ['label' => $cat ? $cat->name : 'Tanpa Kategori', 'value' => (int) $row->total];
        })->values();

        $zeroItems = Item::where('stock', 0)->pluck('id')->all();
        $frequentZero = collect();
        if (count($zeroItems) > 0) {
            $frequentZero = StockChange::whereIn('item_id', $zeroItems)
                ->where('change_type', 'out')
                ->where('occurred_at', '>=', now()->subDays(60))
                ->select('item_id', DB::raw('COUNT(*) as cnt'))
                ->groupBy('item_id')->orderByDesc('cnt')->limit(10)->get()->map(function ($row) {
                    $item = Item::find($row->item_id);
                    return ['label' => $item ? $item->name : ('#' . $row->item_id), 'value' => (int) $row->cnt];
                })->values();
        }
        $topLowest = Item::orderBy('stock')->orderBy('name')->limit(10)->get()->map(function ($it) {
            return ['label' => $it->name, 'value' => (int) $it->stock];
        })->values();
        $rangeIn = (int) StockChange::whereBetween('occurred_at', [$from, $to])->where('change_type', 'in')->sum('qty');
        $rangeOut = (int) StockChange::whereBetween('occurred_at', [$from, $to])->where('change_type', 'out')->sum('qty');

        $byDay = StockChange::whereBetween('occurred_at', [$from, $to])
            ->select(
                DB::raw('DATE(occurred_at) as d'),
                DB::raw("SUM(CASE WHEN change_type='in' THEN qty ELSE 0 END) as in_qty"),
                DB::raw("SUM(CASE WHEN change_type='out' THEN qty ELSE 0 END) as out_qty")
            )
            ->groupBy(DB::raw('DATE(occurred_at)'))
            ->orderBy(DB::raw('DATE(occurred_at)'))
            ->get();

        $labels = [];
        $map = [];
        foreach ($byDay as $row) {
            $map[(string) $row->d] = ['in' => (int) $row->in_qty, 'out' => (int) $row->out_qty];
        }
        for ($i = 0; $i < $days; $i++) {
            $d = $from->copy()->addDays($i)->format('Y-m-d');
            $labels[] = $from->copy()->addDays($i)->format('d/m');
        }

        $criticalFreq = array_fill(0, $days, 0);

        // Bar Chart Data (Paginated Items)
        $barPage = max((int) $request->query('bar_page', 1), 1);
        $barLimit = 10;
        $barItems = Item::orderBy('id')->paginate($barLimit, ['*'], 'bar_page', $barPage);

        $chartBarLabels = $barItems->map(function ($it) use ($abbreviate) {
            $name = $abbreviate($it->name ?? 'Item ' . $it->id);
            // Robust UTF-8 cleaning
            return preg_replace('/[\x00-\x1F\x7F]/', '', $name);
        })->values()->toArray();
        $chartBarValues = $barItems->map(fn($it) => (int) $it->stock)->values()->toArray();

        // Line Chart Data (Paginated Movements per Item)
        $linePage = max((int) $request->query('line_page', 1), 1);
        $lineLimit = 5;
        $lineItems = Item::orderBy('id')->paginate($lineLimit, ['*'], 'line_page', $linePage);

        $lineData = [];
        foreach ($lineItems as $item) {
            $itemMovements = StockChange::where('item_id', $item->id)
                ->whereBetween('occurred_at', [$from, $to])
                ->select(
                    DB::raw('DATE(occurred_at) as d'),
                    DB::raw("SUM(CASE WHEN change_type='in' THEN qty ELSE 0 END) as in_qty"),
                    DB::raw("SUM(CASE WHEN change_type='out' THEN qty ELSE 0 END) as out_qty")
                )
                ->groupBy(DB::raw('DATE(occurred_at)'))
                ->get()
                ->keyBy('d');

            $inValues = [];
            $outValues = [];
            for ($i = 0; $i < $days; $i++) {
                $d = $from->copy()->addDays($i)->format('Y-m-d');
                $inValues[] = (int) ($itemMovements[$d]->in_qty ?? 0);
                $outValues[] = (int) ($itemMovements[$d]->out_qty ?? 0);
            }

            $lineData[] = [
                'name' => $item->name,
                'in' => $inValues,
                'out' => $outValues
            ];
        }

        $lineIn = [];
        $lineOut = [];
        if (count($lineData) > 0) {
            $lineIn = $lineData[0]['in'];
            $lineOut = $lineData[0]['out'];
        }

        if ($request->ajax()) {
            return response()->json([
                'bar' => [
                    'labels' => $chartBarLabels,
                    'values' => $chartBarValues,
                    'current_page' => $barItems->currentPage(),
                    'last_page' => $barItems->lastPage(),
                ],
                'line' => [
                    'data' => $lineData,
                    'labels' => $labels,
                    'current_page' => $lineItems->currentPage(),
                    'last_page' => $lineItems->lastPage(),
                ]
            ]);
        }

        return view('kp.dashboard', [
            'totalItems' => $totalItems,
            'lowCount' => $lowCount,
            'zeroCount' => $zeroCount,
            'transactionsToday' => $transactionsToday,
            'criticalItems' => $criticalItems,
            'criticalCount' => $criticalCount,
            'stocksByCategory' => $stocksByCategory,
            'frequentZero' => $frequentZero,
            'topLowest' => $topLowest,
            'rangeIn' => $rangeIn,
            'rangeOut' => $rangeOut,
            'labels' => $labels,
            'lineIn' => $lineIn,
            'lineOut' => $lineOut,
            'chartBarLabels' => $chartBarLabels,
            'chartBarValues' => $chartBarValues,
            'barCurrentPage' => $barItems->currentPage(),
            'barLastPage' => $barItems->lastPage(),
            'lineData' => $lineData,
            'lineCurrentPage' => $lineItems->currentPage(),
            'lineLastPage' => $lineItems->lastPage(),
            'criticalFreq' => $criticalFreq,
            'mode' => $mode,
            'defaultMin' => $defaultMin,
            'categoryDefaults' => $categoryDefaults,
        ]);
    }

    public function grafikStok(Request $request)
    {
        $days = (int) $request->query('days', 7);
        $allowedDays = [7, 30, 90];
        if (!in_array($days, $allowedDays))
            $days = 7;

        $to = now()->endOfDay();
        $from = now()->subDays($days - 1)->startOfDay();

        // 1. Bar Chart: Stock vs Min Stock (Horizontal)
        // Fetch all items to show comprehensive data
        $barItems = Item::select('id', 'name', 'stock', 'min_stock')->orderBy('stock', 'desc')->get();
        $chartBarLabels = $barItems->map(fn($it) => preg_replace('/[\x00-\x1F\x7F]/', '', $it->name))->toArray();
        $chartBarStock = $barItems->map(fn($it) => (int) $it->stock)->toArray();
        $chartBarMin = $barItems->map(fn($it) => (int) ($it->min_stock ?? 0))->toArray();

        // 2. Line Chart: Trend History
        $lineData = [];
        $topMovedItems = StockChange::whereBetween('occurred_at', [$from, $to])
            ->select('item_id', DB::raw('COUNT(*) as cnt'))
            ->groupBy('item_id')
            ->orderByDesc('cnt')
            ->limit(7) // Top 7 most active items
            ->pluck('item_id');

        $labels = [];
        for ($i = 0; $i < $days; $i++) {
            $labels[] = $from->copy()->addDays($i)->format('d/m');
        }

        foreach ($topMovedItems as $itemId) {
            $item = Item::find($itemId);
            if (!$item)
                continue;

            $movements = StockChange::where('item_id', $itemId)
                ->whereBetween('occurred_at', [$from, $to])
                ->select(DB::raw('DATE(occurred_at) as d'), DB::raw('SUM(qty) as qty'), 'change_type')
                ->groupBy('d', 'change_type')
                ->get();

            // Calculate daily stock position (approximate for trend)
            $dailyStock = [];
            $currentStock = $item->stock; // This is end stock
            // We need to work backwards or forwards. 
            // Simpler: Just show movements (In - Out) or simplified trend.
            // Requirement says "Pergerakan Stok" -> usually stock level over time or flow.
            // Let's metric: Net flow per day.

            $dataPoints = array_fill(0, $days, 0);
            foreach ($movements as $mov) {
                // Find index
                $idx = (int) $from->diffInDays(\Carbon\Carbon::parse($mov->d));
                if ($idx >= 0 && $idx < $days) {
                    if ($mov->change_type === 'in')
                        $dataPoints[$idx] += $mov->qty;
                    else
                        $dataPoints[$idx] -= $mov->qty;
                }
            }

            $lineData[] = [
                'name' => preg_replace('/[\x00-\x1F\x7F]/', '', $item->name),
                'data' => $dataPoints,
                'borderColor' => sprintf('#%06X', mt_rand(0, 0xFFFFFF))
            ];
        }

        // 3. Insights
        // Biggest Drop
        $biggestDropItem = null;
        $maxDrop = 0;
        // Simple logic: Item with most 'out' qty in period
        $topOut = StockChange::whereBetween('occurred_at', [$from, $to])
            ->where('change_type', 'out')
            ->select('item_id', DB::raw('SUM(qty) as total_out'))
            ->groupBy('item_id')
            ->orderByDesc('total_out')
            ->first();

        if ($topOut) {
            $i = Item::find($topOut->item_id);
            if ($i) {
                $biggestDropItem = [
                    'name' => $i->name,
                    'total' => $topOut->total_out
                ];
            }
        }

        $avgDecline = 0;
        $totalOut = StockChange::whereBetween('occurred_at', [$from, $to])->where('change_type', 'out')->sum('qty');
        if ($totalOut > 0)
            $avgDecline = round($totalOut / $days);

        $restockCount = Item::whereColumn('stock', '<', 'min_stock')->count();

        return view('kp.analisis_stok', compact(
            'chartBarLabels',
            'chartBarStock',
            'chartBarMin',
            'lineData',
            'labels',
            'days',
            'biggestDropItem',
            'avgDecline',
            'restockCount'
        ));
    }

    public function detailBarang(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $status = $request->query('status'); // null|low|zero|all
        $category = $request->query('category'); // category name
        $importBatch = $request->query('import_batch');
        $query = Item::query()->with('category');

        if (session('import_ids')) {
            $query->whereIn('id', session('import_ids'));
        } elseif ($importBatch) {
            // If importBatch param exists but session is expired (e.g. manual refresh),
            // we can't filter reliably by ID. We could show all or try timestamp.
            // Given user feedback "table shows more", it's safer to attempt timestamp fallback ONLY if we don't have IDs.
            // But strictly speaking, if session is gone, the "Report" is gone.
            // So showing full list is actually correct "Reset" behavior.
            // However, let's keep the timestamp fallback just in case the redirect kept params but dropped session?
            // No, session flash is reliable for the immediate request.
            // Let's rely on session. If session is gone, show all.
        }
        if ($q !== '') {
            $qClean = preg_replace('/\s+/', ' ', $q);
            $words = explode(' ', $qClean);
            $query->where(function ($w) use ($words) {
                foreach ($words as $word) {
                    $word = trim($word);
                    if ($word === '')
                        continue;
                    $pattern = '%' . $word . '%';
                    $w->where(function ($sub) use ($pattern) {
                        $sub->where('code', 'like', $pattern)
                            ->orWhere('name', 'like', $pattern)
                            ->orWhereHas('category', function ($c) use ($pattern) {
                                $c->where('name', 'like', $pattern);
                            });
                    });
                }
            });
        }
        $notifOn = (int) Setting::get('notif.global.active', cache()->get('notif.global.active', 1));
        $mode = (string) Setting::get('notif.global.mode', cache()->get('notif.global.mode', 'per_item'));
        $defaultMin = (int) Setting::get('notif.global.default_min', cache()->get('notif.global.default_min', 10));
        $categoryDefaults = [];
        if ($mode === 'category') {
            foreach (Category::orderBy('name')->get() as $cat) {
                $categoryDefaults[$cat->name] = (int) Setting::get('notif.category.' . $cat->name . '.default_min', $defaultMin);
            }
        }
        if ($status === 'low') {
            if ($mode === 'global') {
                $query->where('stock', '>', 0)->where('stock', '<', $defaultMin);
            } elseif ($mode === 'category') {
                $query->where('stock', '>', 0);
            } else {
                $query->where('stock', '>', 0)->where(function ($w) use ($defaultMin) {
                    $w->where(function ($q) use ($defaultMin) {
                        $q->where('min_stock', '>=', $defaultMin)->whereColumn('stock', '<', 'min_stock');
                    })->orWhere(function ($q) use ($defaultMin) {
                        $q->where(function ($x) use ($defaultMin) {
                            $x->whereNull('min_stock')
                                ->orWhere('min_stock', '<=', 0)
                                ->orWhere('min_stock', '<', $defaultMin);
                        })->where('stock', '<', $defaultMin);
                    });
                });
            }
        } elseif ($status === 'zero') {
            $query->where('stock', '=', 0);
        }
        if ($category !== null && $category !== '') {
            $normalizedCat = str_replace(' ', '', $category);
            $query->where(function ($w) use ($category, $normalizedCat) {
                // 1. Match by Category Name (Relation) - Primary
                $w->whereHas('category', function ($c) use ($category) {
                    $c->where('name', 'LIKE', $category);
                })
                    // 2. OR match if Code contains Category Name (User's logic)
                    ->orWhere('code', 'like', '%' . $category . '%')
                    // 3. OR match if Name contains Category Name
                    ->orWhere('name', 'like', '%' . $category . '%')
                    // 4. Handle stripped spaces for looser matching
                    ->orWhereRaw("REPLACE(code, ' ', '') LIKE ?", ['%' . $normalizedCat . '%'])
                    ->orWhereRaw("REPLACE(name, ' ', '') LIKE ?", ['%' . $normalizedCat . '%']);
            });
        }
        if (session('import_ids')) {
            $ids = session('import_ids');
            $query->whereIn('id', $ids);
            // Fetch unique items first
            $fetched = $query->get()->keyBy('id');

            // Reconstruct the item list based on the original ID list (preserving order and duplicates)
            $items = collect($ids)->map(function ($id) use ($fetched) {
                return $fetched->get($id);
            })->filter()->values(); // Filter nulls just in case
        } else {
            $items = $query->orderBy('name')->get();
        }
        if ($status === 'low' && $mode === 'category') {
            $items = $items->filter(function ($it) use ($categoryDefaults, $defaultMin) {
                $catName = $it->category ? $it->category->name : null;
                $catMin = $catName !== null && array_key_exists($catName, $categoryDefaults) ? (int) $categoryDefaults[$catName] : $defaultMin;
                $threshold = max((int) ($it->min_stock ?? 0), $catMin);
                return (int) $it->stock > 0 && (int) $it->stock < $threshold;
            })->values();
        }
        $categories = Category::orderBy('name')->get();
        if ($notifOn !== 1) {
            $criticalItems = collect();
            $criticalCount = 0;
        } elseif ($mode === 'global') {
            $baseQuery = Item::where('stock', '<', $defaultMin);
            $criticalCount = $baseQuery->count();
            $criticalItems = $baseQuery->orderBy('name')->limit(20)->get();
        } elseif ($mode === 'category') {
            $base = Item::with('category')->where('notif_active', 1)->get();
            $allCritical = $base->filter(function ($it) use ($categoryDefaults, $defaultMin) {
                $catName = $it->category ? $it->category->name : null;
                $catMin = $catName !== null && array_key_exists($catName, $categoryDefaults) ? (int) $categoryDefaults[$catName] : $defaultMin;
                $threshold = max((int) ($it->min_stock ?? 0), $catMin);
                return (int) $it->stock < $threshold;
            });
            $criticalCount = $allCritical->count();
            $criticalItems = $allCritical->sortBy('name')->take(20)->values();
        } else {
            $baseQuery = Item::where('notif_active', 1)
                ->where(function ($w) use ($defaultMin) {
                    $w->where(function ($q) use ($defaultMin) {
                        $q->where('min_stock', '>=', $defaultMin)->whereColumn('stock', '<', 'min_stock');
                    })->orWhere(function ($q) use ($defaultMin) {
                        $q->where(function ($x) use ($defaultMin) {
                            $x->whereNull('min_stock')
                                ->orWhere('min_stock', '<=', 0)
                                ->orWhere('min_stock', '<', $defaultMin);
                        })->where('stock', '<', $defaultMin);
                    });
                });
            $criticalCount = $baseQuery->count();
            $criticalItems = $baseQuery->orderBy('name')->limit(20)->get();
        }
        return view('kp.detail_barang', compact('items', 'q', 'status', 'category', 'categories', 'criticalCount', 'mode', 'defaultMin', 'notifOn', 'criticalItems', 'categoryDefaults', 'importBatch'));
    }

    public function completeOnboarding()
    {
        if (auth()->check()) {
            auth()->user()->update(['has_viewed_details' => true]);
        }

        return redirect()->route('kp.dashboard')
            ->with('success', 'Selamat datang! Anda sekarang dapat mengakses semua fitur aplikasi.');
    }

    public function daftarStok(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $main = $request->query('main', 'all');
        $category = $request->query('category');
        $sort = $request->query('sort', 'stock_asc');
        $query = Item::query()->with('category');
        if ($q !== '') {
            $qClean = preg_replace('/\s+/', ' ', $q);
            $words = explode(' ', $qClean);
            $query->where(function ($w) use ($words) {
                foreach ($words as $word) {
                    $word = trim($word);
                    if ($word === '')
                        continue;
                    $pattern = '%' . $word . '%';
                    $w->where(function ($sub) use ($pattern) {
                        $sub->where('code', 'like', $pattern)
                            ->orWhere('name', 'like', $pattern)
                            ->orWhereHas('category', function ($c) use ($pattern) {
                                $c->where('name', 'like', $pattern);
                            });
                    });
                }
            });
        }
        // ... rest of method

        if ($main === 'low') {
            $notifOn = (int) Setting::get('notif.global.active', cache()->get('notif.global.active', 1));
            $mode = (string) Setting::get('notif.global.mode', cache()->get('notif.global.mode', 'per_item'));
            $defaultMin = (int) Setting::get('notif.global.default_min', cache()->get('notif.global.default_min', 10));
            if ($mode === 'global') {
                $query->where('stock', '>', 0)->where('stock', '<', $defaultMin);
            } elseif ($mode === 'category') {
                $query->where('stock', '>', 0);
            } else {
                $query->where('stock', '>', 0)->where(function ($w) use ($defaultMin) {
                    $w->where(function ($q) use ($defaultMin) {
                        $q->where('min_stock', '>=', $defaultMin)->whereColumn('stock', '<', 'min_stock');
                    })->orWhere(function ($q) use ($defaultMin) {
                        $q->where(function ($x) use ($defaultMin) {
                            $x->whereNull('min_stock')
                                ->orWhere('min_stock', '<=', 0)
                                ->orWhere('min_stock', '<', $defaultMin);
                        })->where('stock', '<', $defaultMin);
                    });
                });
            }
        } elseif ($main === 'zero') {
            $query->where('stock', '=', 0);
        }
        if ($category !== null && $category !== '') {
            $normalizedCat = str_replace(' ', '', $category);
            $query->where(function ($w) use ($category, $normalizedCat) {
                $w->whereHas('category', function ($c) use ($category) {
                    $c->where('name', $category);
                })
                    ->orWhere('code', 'like', '%' . $category . '%')
                    ->orWhere('name', 'like', '%' . $category . '%')
                    ->orWhereRaw("REPLACE(code, ' ', '') LIKE ?", ['%' . $normalizedCat . '%'])
                    ->orWhereRaw("REPLACE(name, ' ', '') LIKE ?", ['%' . $normalizedCat . '%']);
            });
        }
        if ($sort === 'stock_desc') {
            $query->orderByDesc('stock')->orderBy('name');
        } elseif ($sort === 'name_asc') {
            $query->orderBy('name');
        } elseif ($sort === 'name_desc') {
            $query->orderByDesc('name');
        } else {
            $query->orderBy('stock')->orderBy('name');
        }
        $mode = (string) Setting::get('notif.global.mode', cache()->get('notif.global.mode', 'per_item'));
        $defaultMin = (int) Setting::get('notif.global.default_min', cache()->get('notif.global.default_min', 10));
        $categoryDefaults = [];
        if ($mode === 'category') {
            foreach (Category::orderBy('name')->get() as $cat) {
                $categoryDefaults[$cat->name] = (int) Setting::get('notif.category.' . $cat->name . '.default_min', $defaultMin);
            }
        }
        if ($main === 'low' && $mode === 'category') {
            $raw = $query->get();
            $filtered = $raw->filter(function ($it) use ($categoryDefaults, $defaultMin) {
                $catName = $it->category ? $it->category->name : null;
                $catMin = $catName !== null && array_key_exists($catName, $categoryDefaults) ? (int) $categoryDefaults[$catName] : $defaultMin;
                $threshold = max((int) ($it->min_stock ?? 0), $catMin);
                return (int) $it->stock > 0 && (int) $it->stock < $threshold;
            })->values();
            $page = max((int) $request->query('page', 1), 1);
            $perPage = 10;
            $total = $filtered->count();
            $results = $filtered->forPage($page, $perPage)->values();
            $items = new \Illuminate\Pagination\LengthAwarePaginator($results, $total, $perPage, $page, ['path' => $request->url(), 'query' => $request->query()]);
        } else {
            $items = $query->paginate(10)->withQueryString();
        }
        $categories = Category::orderBy('name')->get();
        $notifOn = (int) Setting::get('notif.global.active', cache()->get('notif.global.active', 1));
        if ($notifOn !== 1) {
            $criticalItems = collect();
            $criticalCount = 0;
        } elseif ($mode === 'global') {
            $baseQuery = Item::where('stock', '<', $defaultMin);
            $criticalCount = $baseQuery->count();
            $criticalItems = $baseQuery->orderBy('name')->limit(20)->get();
        } elseif ($mode === 'category') {
            $base = Item::with('category')->where('notif_active', 1)->get();
            $allCritical = $base->filter(function ($it) use ($categoryDefaults, $defaultMin) {
                $catName = $it->category ? $it->category->name : null;
                $catMin = $catName !== null && array_key_exists($catName, $categoryDefaults) ? (int) $categoryDefaults[$catName] : $defaultMin;
                $threshold = max((int) ($it->min_stock ?? 0), $catMin);
                return (int) $it->stock < $threshold;
            });
            $criticalCount = $allCritical->count();
            $criticalItems = $allCritical->sortBy('name')->take(20)->values();
        } else {
            $baseQuery = Item::where('notif_active', 1)
                ->where(function ($w) use ($defaultMin) {
                    $w->where(function ($q) use ($defaultMin) {
                        $q->where('min_stock', '>=', $defaultMin)->whereColumn('stock', '<', 'min_stock');
                    })->orWhere(function ($q) use ($defaultMin) {
                        $q->where(function ($x) use ($defaultMin) {
                            $x->whereNull('min_stock')
                                ->orWhere('min_stock', '<=', 0)
                                ->orWhere('min_stock', '<', $defaultMin);
                        })->where('stock', '<', $defaultMin);
                    });
                });
            $criticalCount = $baseQuery->count();
            $criticalItems = $baseQuery->orderBy('name')->limit(20)->get();
        }
        return view('kp.daftar_stok', compact('items', 'categories', 'q', 'main', 'category', 'sort', 'criticalCount', 'mode', 'defaultMin', 'notifOn', 'criticalItems', 'categoryDefaults'));
    }

    public function riwayatStok(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Filter for 'sid_sync' to show only Import History Logs as requested
        $query = StockChange::with(['item', 'user'])
            ->where('change_type', 'sid_sync');

        if ($startDate) {
            $query->whereDate('occurred_at', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('occurred_at', '<=', $endDate);
        }

        $changes = $query->orderBy('occurred_at', 'desc')
            ->limit(500) // Increase limit slightly
            ->get();

        return view('kp.riwayat_stok', compact('changes', 'startDate', 'endDate'));
    }

    public function koreksiStok(Request $request)
    {
        if (auth()->user()->role !== 'admin') {
            return redirect()->route('kp.dashboard')->with('error', 'Akses ditolak. Fitur ini khusus Admin.');
        }
        $items = Item::orderBy('name')->get();
        $users = \App\Models\User::orderBy('name')->get();
        $recent = StockChange::with(['item', 'user'])
            ->where('note', 'like', '%\"type\":\"correction\"%')
            ->orderBy('occurred_at', 'desc')->limit(20)->get();
        $rows = $recent->map(function ($c) {
            $note = [];
            try {
                $note = json_decode((string) $c->note, true) ?: [];
            } catch (\Throwable $e) {
            }
            return [
                'item' => $c->item ? $c->item->name : '',
                'before' => (int) ($note['before'] ?? 0),
                'after' => (int) ($note['after'] ?? 0),
                'diff' => (int) ($note['after'] ?? 0) - (int) ($note['before'] ?? 0),
                'user' => $c->user ? $c->user->name : '',
            ];
        })->values();
        return view('kp.koreksi_stok', compact('items', 'users', 'rows'));
    }

    public function koreksiStokPost(Request $request)
    {
        if (auth()->user()->role !== 'admin') {
            return redirect()->route('kp.dashboard')->with('error', 'Akses ditolak. Fitur ini khusus Admin.');
        }
        $data = $request->validate([
            'item_id' => ['required', 'integer'],
            'physical' => ['required', 'integer', 'min:0'],
            'reason' => ['nullable', 'string'],
            'date' => ['nullable', 'date'],
            'user_id' => ['nullable', 'integer'],
        ]);
        $item = Item::find((int) $data['item_id']);
        if (!$item) {
            return back()->with('error', 'Barang tidak ditemukan.');
        }
        $before = (int) $item->stock;
        $after = (int) $data['physical'];
        $diff = $after - $before;
        $occurred = $data['date'] ? \Carbon\Carbon::parse((string) $data['date']) : now();
        $userId = $data['user_id'] ?? (auth()->id() ?: \App\Models\User::query()->value('id'));
        $item->stock = $after;
        $item->save();
        StockChange::create([
            'item_id' => $item->id,
            'user_id' => $userId,
            'change_type' => $diff >= 0 ? 'in' : 'out',
            'qty' => abs($diff),
            'note' => json_encode([
                'type' => 'correction',
                'reason' => (string) ($data['reason'] ?? ''),
                'before' => $before,
                'after' => $after,
            ], JSON_UNESCAPED_UNICODE),
            'occurred_at' => $occurred,
        ]);
        return redirect()->route('kp.koreksi_stok')->with('success', 'Koreksi stok berhasil disimpan.');
    }

    public function kelolaNotifikasi()
    {
        if (auth()->user()->role !== 'admin') {
            return redirect()->route('kp.dashboard')->with('error', 'Akses ditolak.');
        }
        $items = Item::with('category')->orderBy('name')->get();
        $notifOn = (int) Setting::get('notif.global.active', cache()->get('notif.global.active', 1));
        $mode = (string) Setting::get('notif.global.mode', cache()->get('notif.global.mode', 'per_item'));
        $defaultMin = (int) Setting::get('notif.global.default_min', cache()->get('notif.global.default_min', 10));
        $date = (string) Setting::get('notif.global.date', cache()->get('notif.global.date', ''));
        $categoryDefaults = [];
        if ($mode === 'category') {
            foreach (Category::orderBy('name')->get() as $cat) {
                $categoryDefaults[$cat->name] = (int) Setting::get('notif.category.' . $cat->name . '.default_min', $defaultMin);
            }
        }
        if ($notifOn !== 1) {
            $critical = collect();
        } elseif ($mode === 'global') {
            $critical = Item::where('stock', '<', $defaultMin)->orderBy('name')->get();
        } elseif ($mode === 'category') {
            $base = Item::with('category')->where('notif_active', 1)->get();
            $critical = $base->filter(function ($it) use ($categoryDefaults, $defaultMin) {
                $catName = $it->category ? $it->category->name : null;
                $catMin = $catName !== null && array_key_exists($catName, $categoryDefaults) ? (int) $categoryDefaults[$catName] : $defaultMin;
                $threshold = max((int) ($it->min_stock ?? 0), $catMin);
                return (int) $it->stock < $threshold;
            })->sortBy('name')->values();
        } else {
            $critical = Item::where('notif_active', 1)
                ->where(function ($w) use ($defaultMin) {
                    $w->where(function ($q) use ($defaultMin) {
                        $q->where('min_stock', '>=', $defaultMin)->whereColumn('stock', '<', 'min_stock');
                    })->orWhere(function ($q) use ($defaultMin) {
                        $q->where(function ($x) use ($defaultMin) {
                            $x->whereNull('min_stock')
                                ->orWhere('min_stock', '<=', 0)
                                ->orWhere('min_stock', '<', $defaultMin);
                        })->where('stock', '<', $defaultMin);
                    });
                })
                ->orderBy('name')
                ->get();
        }
        return view('kp.kelola_notifikasi', [
            'items' => $items,
            'critical' => $critical,
            'settings' => [
                'notif_on' => $notifOn,
                'mode' => $mode,
                'default_min' => $defaultMin,
                'date' => $date,
            ],
            'categoryDefaults' => $categoryDefaults
        ]);
    }

    public function toggleNotif(Request $request)
    {
        if (auth()->user()->role !== 'admin') {
            return response()->json(['ok' => false, 'message' => 'Akses ditolak.'], 403);
        }
        $code = (string) $request->input('code', '');
        $active = $request->boolean('active', null);
        if ($code === '' || $active === null) {
            return response()->json(['ok' => false, 'message' => 'Parameter tidak lengkap.'], 422);
        }
        $item = Item::where('code', $code)->first();
        if (!$item) {
            return response()->json(['ok' => false, 'message' => 'Barang tidak ditemukan.'], 404);
        }
        $item->notif_active = $active ? 1 : 0;
        $item->save();
        return response()->json(['ok' => true, 'notif_active' => (int) $item->notif_active]);
    }

    public function saveNotif(Request $request)
    {
        if (auth()->user()->role !== 'admin') {
            return redirect()->route('kp.dashboard')->with('error', 'Akses ditolak.');
        }
        $data = $request->validate([
            'notif_on' => ['required'],
            'default_min' => ['required', 'integer', 'min:0'],
            'mode' => ['required', 'in:per_item,global,category'],
            'date' => ['nullable', 'string'],
        ]);
        $on = in_array(strtolower((string) $data['notif_on']), ['1', 'on', 'true', 'yes', 'aktif'], true) ? 1 : 0;
        \App\Models\Setting::set('notif.global.active', $on);
        \App\Models\Setting::set('notif.global.default_min', (int) $data['default_min']);
        \App\Models\Setting::set('notif.global.mode', (string) $data['mode']);
        \App\Models\Setting::set('notif.global.date', (string) ($data['date'] ?? ''));
        cache()->forever('notif.global.active', $on);
        cache()->forever('notif.global.default_min', (int) $data['default_min']);
        cache()->forever('notif.global.mode', (string) $data['mode']);
        cache()->forever('notif.global.date', (string) ($data['date'] ?? ''));
        return redirect()->route('kp.kelola_notifikasi')->with('success', 'Konfigurasi notifikasi disimpan.');
    }

    public function kategoriBarang(Request $request)
    {
        if (auth()->user()->role !== 'admin') {
            return redirect()->route('kp.dashboard')->with('error', 'Akses ditolak.');
        }
        $q = trim((string) $request->query('q', ''));
        $perPage = (int) $request->query('per_page', 10);
        if ($perPage <= 0) {
            $perPage = 10;
        } elseif ($perPage > 100) {
            $perPage = 100;
        }

        $query = Category::query();
        if ($q !== '') {
            $qClean = preg_replace('/\s+/', ' ', $q);
            $words = explode(' ', $qClean);
            $query->where(function ($w) use ($words) {
                foreach ($words as $word) {
                    $word = trim($word);
                    if ($word === '')
                        continue;
                    $w->where('name', 'like', '%' . $word . '%');
                }
            });
        }

        $paginator = $query->orderBy('name')->paginate($perPage)->withQueryString();

        $categories = $paginator->getCollection();
        foreach ($categories as $cat) {
            $categoriesItemsCount = Item::where(function ($w) use ($cat) {
                $w->where('name', 'like', '%' . $cat->name . '%')
                    ->orWhere('code', 'like', '%' . $cat->name . '%');
            })->count();
            $cat->items_count = $categoriesItemsCount;
        }
        $paginator->setCollection($categories);

        return view('kp.kategori_barang', [
            'categories' => $paginator,
            'q' => $q,
            'perPage' => $perPage,
        ]);
    }

    public function storeCategory(Request $request)
    {
        if (auth()->user()->role !== 'admin') {
            return redirect()->route('kp.dashboard')->with('error', 'Akses ditolak.');
        }
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'is_active' => ['required', 'boolean'],
        ]);

        try {
            Category::create([
                'name' => $data['name'],
                'is_active' => (int) $data['is_active'],
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->route('kp.kategori_barang')->with('error', 'Kategori dengan nama tersebut sudah ada.');
        }

        return redirect()->route('kp.kategori_barang')->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function updateCategory(Request $request, Category $category)
    {
        if (auth()->user()->role !== 'admin') {
            return redirect()->route('kp.dashboard')->with('error', 'Akses ditolak.');
        }
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'is_active' => ['required', 'boolean'],
        ]);

        try {
            $category->update([
                'name' => $data['name'],
                'is_active' => (int) $data['is_active'],
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->route('kp.kategori_barang')->with('error', 'Kategori dengan nama tersebut sudah ada.');
        }

        return redirect()->route('kp.kategori_barang')->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroyCategory(Category $category)
    {
        if (auth()->user()->role !== 'admin') {
            return redirect()->route('kp.dashboard')->with('error', 'Akses ditolak.');
        }
        $category->delete();
        return redirect()->route('kp.kategori_barang')->with('success', 'Kategori berhasil dihapus.');
    }

    public function editDetailBarangView($id)
    {
        $item = Item::findOrFail($id);
        return view('kp.edit_detail_barang', compact('item'));
    }

    public function updateItem(Request $request)
    {
        $data = $request->validate([
            'id' => ['required', 'integer'],
            'code' => ['required', 'string'],
            'name' => ['required', 'string'],
            'purchase_price' => ['required', 'numeric', 'min:0'],
            'sale_price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'min_stock' => ['required', 'integer', 'min:0'],
            // Add unit validation (optional, can be string)
            'unit' => ['nullable', 'string', 'max:20'],
        ]);
        $item = Item::find($data['id']);
        if (!$item) {
            return back()->with('error', 'Barang tidak ditemukan.');
        }
        $before = (int) $item->stock;
        $updates = [
            'name' => $data['name'],
            'purchase_price' => (float) $data['purchase_price'],
            'sale_price' => (float) $data['sale_price'],
            'stock' => (int) $data['stock'],
            'min_stock' => (int) $data['min_stock'],
        ];
        if (isset($data['unit'])) {
            $updates['unit'] = $data['unit'];
        }
        $item->update($updates);
        $after = (int) $item->stock;
        $diff = $after - $before;
        if ($diff !== 0) {
            StockChange::create([
                'item_id' => $item->id,
                'user_id' => auth()->id() ?: \App\Models\User::query()->value('id'),
                'change_type' => $diff >= 0 ? 'in' : 'out',
                'qty' => abs($diff),
                'note' => json_encode(['type' => 'edit', 'before' => $before, 'after' => $after], JSON_UNESCAPED_UNICODE),
                'occurred_at' => now(),
            ]);
        }
        $params = $request->only(['q', 'status', 'category']);
        return redirect()->route('kp.detail_barang', $params)->with('success', 'Data barang berhasil diperbarui.');
    }

    public function importSid(Request $request)
    {
        $request->validate([
            'sid_file' => ['required', 'file'],
            'import_date' => ['nullable', 'date'],
        ]);

        $file = $request->file('sid_file');
        $importDate = $request->input('import_date')
            ? \Carbon\Carbon::parse($request->input('import_date'))
            : now()->startOfSecond();

        // ... (file validation) ...
        if (!$file->isValid() || ($file->getSize() ?? 0) <= 0) {
            return back()->with('error', 'Upload gagal atau file kosong.');
        }
        $ext = strtolower($file->getClientOriginalExtension());
        $path = $file->getPathname();

        // ... (formatting checks) ...
        if (!is_file($path)) {
            return back()->with('error', 'File upload sementara tidak ditemukan. Coba ulangi upload.');
        }
        $allowed = ['csv', 'txt', 'xls', 'xlsx'];
        if (!in_array($ext, $allowed, true)) {
            return back()->with('error', 'Format file tidak didukung. Gunakan CSV, TXT, XLS, atau XLSX.');
        }

        // ... (headers parsing logic unchanged until loop) ...
        $normalize = function ($s) {
            $s = (string) $s;
            $s = trim(strtolower($s));
            $s = str_replace([' ', '-', '.', '/', '\\'], '_', $s);
            return $s;
        };

        $aliases = [
            'code' => ['code', 'kode', 'kode_barang'],
            'name' => ['name', 'nama', 'nama_barang'],
            'purchase_price' => ['purchase_price', 'harga_beli', 'beli', 'harga_be'],
            'sale_price' => ['sale_price', 'harga_jual', 'jual'],
            'stock' => ['stock', 'stok', 'qty', 'jumlah'],
            'unit' => ['unit', 'satuan', 'sat', 'uom'],
            'min_stock' => ['min_stock', 'minimum', 'min', 'stok_min'],
            'notif_active' => ['notif_active', 'notif', 'notifikasi'],
            'category' => ['category', 'kategori', 'group'],
        ];

        // ... (Reading file content logic) ...
        // Re-implementing just the necessary parts to ensure context is correct

        // Use SimpleXLS if excel
        if (in_array($ext, ['xls', 'xlsx'], true)) {
            if ($serialized = file_get_contents($path)) {
                if ($xls = \Shuchkin\SimpleXLS::parse($path)) {
                    $rows = $xls->rows();
                } else {
                    // Try IOFactory
                    try {
                        $spreadsheet = IOFactory::load($path);
                        $rows = $spreadsheet->getActiveSheet()->toArray();
                    } catch (\Throwable $e) {
                        return back()->with('error', 'Gagal membaca file Excel.');
                    }
                }
            }
        }

        // Use CSV logic if csv/txt (Assuming CSV parsing logic is similar to what was there, just referencing it)
        if ($request->has('preview')) {
            // Preview Logic
            $previewRows = [];
            $headers = [];

            if (in_array($ext, ['xls', 'xlsx'], true)) {
                if ($serialized = file_get_contents($path)) {
                    if ($xls = \Shuchkin\SimpleXLS::parse($path)) {
                        $rawRows = $xls->rows();
                        if (!empty($rawRows)) {
                            $headers = array_map(function ($h) {
                                return trim((string) $h);
                            }, $rawRows[0]);
                            $previewRows = array_slice($rawRows, 1, 50);
                        }
                    } else {
                        try {
                            $spreadsheet = IOFactory::load($path);
                            $rawRows = $spreadsheet->getActiveSheet()->toArray();
                            if (!empty($rawRows)) {
                                $headers = array_map(function ($h) {
                                    return trim((string) $h);
                                }, $rawRows[0]);
                                $previewRows = array_slice($rawRows, 1, 50);
                            }
                        } catch (\Throwable $e) {
                        }
                    }
                }
            } elseif (in_array($ext, ['csv', 'txt'], true)) {
                $probe = @file_get_contents($path, false, null, 0, 16384) ?: '';
                $delims = [',' => substr_count($probe, ','), ';' => substr_count($probe, ';'), "\t" => substr_count($probe, "\t"), '|' => substr_count($probe, '|')];
                arsort($delims);
                $separator = key($delims) ?: ',';

                $handle = fopen($path, 'r');
                if ($handle) {
                    $rawHeaders = fgetcsv($handle, 0, $separator);
                    $headers = $rawHeaders ? array_map('trim', $rawHeaders) : [];
                    $count = 0;
                    while (($row = fgetcsv($handle, 0, $separator)) !== false && $count < 50) {
                        $previewRows[] = $row;
                        $count++;
                    }
                    fclose($handle);
                }
            }
            return view('kp.import', ['preview' => ['headers' => $headers, 'rows' => $previewRows]]);
        }

        // --- PROCESS IMPORT ---

        // Prepare delimiter for CSV processing
        $likelyDelim = ',';
        if (in_array($ext, ['csv', 'txt'], true)) {
            $probe = @file_get_contents($path, false, null, 0, 16384) ?: '';
            $delims = [
                ',' => substr_count($probe, ','),
                ';' => substr_count($probe, ';'),
                "\t" => substr_count($probe, "\t"),
                '|' => substr_count($probe, '|'),
            ];
            arsort($delims);
            $likelyDelim = array_key_first($delims);
        }

        $colMap = [];
        $rows = [];
        $headerIndex = 0;
        $headers = null;

        if (in_array($ext, ['csv', 'txt'], true)) {
            $handle = fopen($path, 'r');
            if (!$handle) {
                return back()->with('error', 'Tidak dapat membuka file yang diunggah.');
            }
            $headers = null;
            $tries = 0;
            while (($row = fgetcsv($handle, 0, $likelyDelim ?: ',')) !== false && $tries < 50) {
                $tries++;
                $norm = array_map($normalize, $row);
                if (in_array('kode_barang', $norm, true) || in_array('kode', $norm, true)) {
                    $headers = $row;
                    break;
                }
                $empty = true;
                foreach ($row as $cell) {
                    if (trim((string) $cell) !== '') {
                        $empty = false;
                        break;
                    }
                }
                if ($empty) {
                    continue;
                }
            }
            if (!$headers) {
                fclose($handle);
                return back()->with('error', 'Header tidak ditemukan. Pastikan ada kolom KODE BARANG dan NAMA BARANG.');
            }
            $headerMap = [];
            foreach ($headers as $i => $h) {
                $headerMap[$normalize($h)] = $i;
            }
            $col = [];
            foreach ($aliases as $key => $names) {
                $col[$key] = null;
                foreach ($names as $n) {
                    if (array_key_exists($n, $headerMap)) {
                        $col[$key] = $headerMap[$n];
                        break;
                    }
                }
            }
            if ($col['code'] === null || $col['name'] === null) {
                fclose($handle);
                return back()->with('error', 'File harus memiliki kolom kode/kode_barang dan nama/nama_barang.');
            }
            $imported = 0;
            $updated = 0;
            $userId = auth()->id() ?: \App\Models\User::query()->value('id');
            $affectedIds = [];
            // Ensure importDate is available (it's passed to create, but good to be explicit if scope issues existed, though here it's fine)

            DB::beginTransaction();
            try {
                while (($row = fgetcsv($handle, 0, $likelyDelim ?: ',')) !== false) {
                    $code = trim((string) ($row[$col['code']] ?? ''));
                    $name = trim((string) ($row[$col['name']] ?? ''));
                    if ($code === '' && $name === '') {
                        continue;
                    }
                    // Use null if column not mapped, to allow partial updates
                    $purchase = $col['purchase_price'] !== null ? (float) preg_replace('/[^0-9\.\-]/', '', (string) $row[$col['purchase_price']]) : null;
                    $sale = $col['sale_price'] !== null ? (float) preg_replace('/[^0-9\.\-]/', '', (string) $row[$col['sale_price']]) : null;
                    $stock = $col['stock'] !== null ? (int) preg_replace('/[^0-9\-]/', '', (string) $row[$col['stock']]) : null;
                    // Read Unit
                    $unit = $col['unit'] !== null ? trim((string) ($row[$col['unit']] ?? 'pcs')) : null;
                    if ($unit === '')
                        $unit = 'pcs';

                    $minStock = $col['min_stock'] !== null ? (int) preg_replace('/[^0-9\-]/', '', (string) $row[$col['min_stock']]) : null;
                    $notifRaw = $col['notif_active'] !== null ? strtolower(trim((string) $row[$col['notif_active']])) : '1';
                    // If column missing, default to Keep Existing (null). But if mapped, parse it.
                    // Actually notif_active default was '1', we can keep strict defaults or allow null.
                    // For safety, let's allow null and default to 1 for NEW items only.
                    $notif = $col['notif_active'] !== null ? (in_array($notifRaw, ['1', 'true', 'ya', 'aktif', 'on', 'y'], true) ? 1 : 0) : null;

                    $categoryName = $col['category'] !== null ? trim((string) $row[$col['category']]) : null;
                    $categoryId = null;
                    if ($categoryName !== null && $categoryName !== '') {
                        $category = Category::firstOrCreate(
                            ['name' => $categoryName],
                            ['is_active' => 1]
                        );
                        $categoryId = $category->id;
                    }

                    // Build Values Array filtering nulls
                    $values = [
                        'name' => $name === '' ? $code : $name,
                    ];
                    if ($purchase !== null)
                        $values['purchase_price'] = $purchase;
                    if ($sale !== null)
                        $values['sale_price'] = $sale;
                    if ($stock !== null)
                        $values['stock'] = $stock;
                    if ($minStock !== null)
                        $values['min_stock'] = $minStock;
                    if ($unit !== null)
                        $values['unit'] = $unit;
                    if ($notif !== null)
                        $values['notif_active'] = $notif;
                    if ($categoryId !== null)
                        $values['category_id'] = $categoryId;

                    $existing = Item::where('code', $code)->where('name', $values['name'])->first();
                    $itemId = null;
                    // Determine stock for logging: Use imported value or existing value
                    $logStock = $stock;

                    if ($existing) {
                        $itemId = $existing->id;
                        $beforeStock = $existing->stock;
                        $logStock = $stock !== null ? $stock : $beforeStock;

                        // Check if data is identical (skip if no changes)
                        $hasChanges = false;
                        foreach ($values as $key => $value) {
                            if ($existing->$key != $value) {
                                $hasChanges = true;
                                break;
                            }
                        }

                        if ($hasChanges) {
                            $existing->timestamps = false;
                            $existing->fill($values);
                            $existing->updated_at = $importDate;
                            $existing->save();
                            $updated++;
                            $existing->save();
                            $updated++;


                            // Log Diff for Dashboard (Only if stock changed)
                            if ($stock !== null) {
                                $diff = $stock - $beforeStock;
                                if ($diff !== 0) {
                                    StockChange::create([
                                        'item_id' => $existing->id,
                                        'user_id' => $userId,
                                        'change_type' => $diff >= 0 ? 'in' : 'out',
                                        'qty' => abs($diff),
                                        'note' => json_encode(['type' => 'import_update', 'source' => 'SID Retail Pro'], JSON_UNESCAPED_UNICODE),
                                        'occurred_at' => $importDate,
                                    ]);
                                }
                            }
                        }
                    } else {
                        // For NEW items, apply defaults for missing fields
                        if (!isset($values['stock']))
                            $values['stock'] = 0;
                        if (!isset($values['purchase_price']))
                            $values['purchase_price'] = 0;
                        if (!isset($values['sale_price']))
                            $values['sale_price'] = 0;
                        if (!isset($values['min_stock']))
                            $values['min_stock'] = 0;
                        if (!isset($values['notif_active']))
                            $values['notif_active'] = 1;
                        if (!isset($values['unit']))
                            $values['unit'] = 'pcs';

                        $newItem = Item::create(array_merge(['code' => $code, 'created_at' => $importDate, 'updated_at' => $importDate], $values));
                        $itemId = $newItem->id;
                        $imported++;
                        $affectedIds[] = $itemId;
                        $logStock = $values['stock'];

                        // New Item -> Diff is full amount
                        if ($logStock > 0) {
                            StockChange::create([
                                'item_id' => $newItem->id,
                                'user_id' => $userId,
                                'change_type' => 'in',
                                'qty' => $logStock,
                                'note' => json_encode(['type' => 'import_new', 'source' => 'SID Retail Pro'], JSON_UNESCAPED_UNICODE),
                                'occurred_at' => $importDate,
                            ]);
                        }
                    }

                    if ($itemId) {
                        $affectedIds[] = $itemId;
                    }



                    // IMPORT HISTORY LOG (Absolute State) - Log for EVERY processed item
                    if ($itemId) {
                        StockChange::create([
                            'item_id' => $itemId,
                            'user_id' => $userId,
                            'change_type' => 'sid_sync',
                            'qty' => $logStock, // Use resolved stock (imported or existing)
                            'note' => json_encode([
                                'source' => 'SID Retail Pro',
                                'unit' => $values['unit'] ?? ($existing->unit ?? 'pcs'),
                                'absolute_stock' => $logStock
                            ], JSON_UNESCAPED_UNICODE),
                            'occurred_at' => $importDate,
                        ]);
                    }
                }
                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                fclose($handle);
                return back()->with('error', 'Terjadi kesalahan saat import CSV: ' . $e->getMessage());
            }
            fclose($handle);

            if (auth()->check()) {
                auth()->user()->update(['has_imported' => true]);
            }

            // Use Session Flash for IDs to ensure perfect sync with the banner
            $totalProcessed = count($affectedIds);

            return redirect()->route('kp.detail_barang', ['import_batch' => $importDate->timestamp])
                ->with('import_success', true)
                ->with('import_count', $totalProcessed)
                ->with('new_item_count', $imported)
                ->with('import_ids', $affectedIds);
        }

        if ($ext === 'xls') {
            $sheet = null;
            $lastErr = null;
            $probe = @file_get_contents($path, false, null, 0, 16384) ?: '';
            $isHtml = stripos($probe, '<html') !== false || stripos($probe, '<table') !== false;
            $hasNewline = strpos($probe, "\n") !== false || strpos($probe, "\r") !== false;
            $isZip = substr($probe, 0, 2) === 'PK';
            $delims = [
                ',' => substr_count($probe, ','),
                ';' => substr_count($probe, ';'),
                "\t" => substr_count($probe, "\t"),
                '|' => substr_count($probe, '|'),
            ];
            arsort($delims);
            $likelyDelim = array_key_first($delims);
            $looksCsv = $hasNewline && ($delims[','] > 5 || $delims[';'] > 5 || $delims["\t"] > 5 || $delims['|'] > 5);
            if ($isHtml) {
                try {
                    $htmlReader = new \PhpOffice\PhpSpreadsheet\Reader\Html();
                    $spreadsheet = $htmlReader->load($path);
                    $sheet = $spreadsheet->getActiveSheet()->toArray();
                } catch (\Throwable $e) {
                    $lastErr = $e->getMessage();
                }
            }
            if (!$sheet && $looksCsv) {
                try {
                    $csvReader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
                    if (method_exists($csvReader, 'setDelimiter')) {
                        $csvReader->setDelimiter($likelyDelim ?: ',');
                    }
                    if (method_exists($csvReader, 'setEnclosure')) {
                        $csvReader->setEnclosure('"');
                    }
                    $spreadsheet = $csvReader->load($path);
                    $sheet = $spreadsheet->getActiveSheet()->toArray();
                } catch (\Throwable $e) {
                    $lastErr = $e->getMessage();
                }
            }
            if (!$sheet) {
                try {
                    $xls = SimpleXLS::parse($path);
                    if ($xls) {
                        $sheet = $xls->rows();
                    }
                } catch (\Throwable $e) {
                    $lastErr = $e->getMessage();
                }
            }
            if (!$sheet) {
                try {
                    $reader = IOFactory::createReader('Xls');
                    $reader->setReadDataOnly(true);
                    $spreadsheet = $reader->load($path);
                    $sheet = $spreadsheet->getActiveSheet()->toArray();
                } catch (\Throwable $e) {
                    $lastErr = $e->getMessage();
                    if ($isZip) {
                        try {
                            $reader = IOFactory::createReader('Xlsx');
                            $reader->setReadDataOnly(true);
                            $spreadsheet = $reader->load($path);
                            $sheet = $spreadsheet->getActiveSheet()->toArray();
                        } catch (\Throwable $e2) {
                            $lastErr = $e2->getMessage();
                        }
                    }
                    if (!$sheet) {
                        try {
                            if ($isHtml || stripos($probe, '<table') !== false) {
                                $dom = new \DOMDocument();
                                libxml_use_internal_errors(true);
                                $dom->loadHTML($probe);
                                libxml_clear_errors();
                                $rowsArr = [];
                                foreach ($dom->getElementsByTagName('tr') as $tr) {
                                    $cells = [];
                                    foreach ($tr->getElementsByTagName('th') as $th) {
                                        $cells[] = trim($th->textContent);
                                    }
                                    if (count($cells) === 0) {
                                        foreach ($tr->getElementsByTagName('td') as $td) {
                                            $cells[] = trim($td->textContent);
                                        }
                                    }
                                    if (count($cells) > 0) {
                                        $rowsArr[] = $cells;
                                    }
                                }
                                if (count($rowsArr) > 0) {
                                    $sheet = $rowsArr;
                                }
                            }
                        } catch (\Throwable $e3) {
                            $lastErr = $e3->getMessage();
                        }
                        if (!$sheet) {
                            return back()->with('error', 'Gagal membaca file XLS.');
                        }
                    }
                }
            }
            if (!$sheet || count($sheet) === 0) {
                return back()->with('error', 'File XLS kosong.');
            }
            $headerIndex = null;
            $headerMap = [];
            for ($i = 0; $i < count($sheet); $i++) {
                $row = $sheet[$i];
                $norm = array_map($normalize, $row);
                if (in_array('kode_barang', $norm, true) || in_array('kode', $norm, true)) {
                    $headerIndex = $i;
                    foreach ($row as $idx => $val) {
                        $headerMap[$normalize((string) $val)] = $idx;
                    }
                    break;
                }
            }
            if ($headerIndex === null) {
                return back()->with('error', 'Header XLS tidak ditemukan. Pastikan ada kolom KODE BARANG dan NAMA BARANG.');
            }
            $col = [];
            foreach ($aliases as $key => $names) {
                $col[$key] = null;
                foreach ($names as $n) {
                    if (array_key_exists($n, $headerMap)) {
                        $col[$key] = $headerMap[$n];
                        break;
                    }
                }
            }
            if ($col['code'] === null || $col['name'] === null) {
                return back()->with('error', 'File harus memiliki kolom kode/kode_barang dan nama/nama_barang.');
            }
            $imported = 0;
            $updated = 0;
            $userId = auth()->id() ?: \App\Models\User::query()->value('id');

            DB::beginTransaction();
            try {
                for ($r = $headerIndex + 1; $r < count($sheet); $r++) {
                    $row = $sheet[$r];
                    $code = trim((string) ($row[$col['code']] ?? ''));
                    $name = trim((string) ($row[$col['name']] ?? ''));
                    if ($code === '' && $name === '') {
                        continue;
                    }
                    // Handle Partial Updates (null if missing)
                    $purchase = $col['purchase_price'] !== null ? (float) preg_replace('/[^0-9\.\-]/', '', (string) ($row[$col['purchase_price']] ?? '')) : null;
                    $sale = $col['sale_price'] !== null ? (float) preg_replace('/[^0-9\.\-]/', '', (string) ($row[$col['sale_price']] ?? '')) : null;
                    $stock = $col['stock'] !== null ? (int) preg_replace('/[^0-9\-]/', '', (string) ($row[$col['stock']] ?? '')) : null;
                    $minStock = $col['min_stock'] !== null ? (int) preg_replace('/[^0-9\-]/', '', (string) ($row[$col['min_stock']] ?? '')) : null;
                    $notifRaw = $col['notif_active'] !== null ? strtolower(trim((string) ($row[$col['notif_active']] ?? ''))) : null;
                    $notif = $notifRaw !== null ? (in_array($notifRaw, ['1', 'true', 'ya', 'aktif', 'on', 'y'], true) ? 1 : 0) : null;

                    $categoryName = $col['category'] !== null ? trim((string) ($row[$col['category']] ?? '')) : null;
                    $categoryId = null;
                    if ($categoryName !== null && $categoryName !== '') {
                        $category = Category::firstOrCreate(
                            ['name' => $categoryName],
                            ['is_active' => 1]
                        );
                        $categoryId = $category->id;
                    }

                    $values = [
                        'name' => $name === '' ? $code : $name,
                    ];
                    if ($purchase !== null)
                        $values['purchase_price'] = $purchase;
                    if ($sale !== null)
                        $values['sale_price'] = $sale;
                    if ($stock !== null)
                        $values['stock'] = $stock;
                    if ($minStock !== null)
                        $values['min_stock'] = $minStock;
                    if ($notif !== null)
                        $values['notif_active'] = $notif;
                    if ($categoryId !== null)
                        $values['category_id'] = $categoryId;

                    // Attempt to find unit if column exists (alias map check needed but let's default to pcs or try to read)
                    if (isset($col['unit']) && $col['unit'] !== null) {
                        $values['unit'] = trim((string) ($row[$col['unit']] ?? 'pcs'));
                        if ($values['unit'] === '')
                            $values['unit'] = 'pcs';
                    }

                    $existing = Item::where('code', $code)->first();
                    $itemId = null;
                    $logStock = $stock;

                    if ($existing) {
                        $itemId = $existing->id;
                        $beforeStock = $existing->stock;
                        $logStock = $stock !== null ? $stock : $beforeStock;

                        $existing->update($values);
                        $updated++;

                        // Log Diff for Dashboard Flow
                        if ($stock !== null) {
                            $diff = $stock - $beforeStock;
                            if ($diff !== 0) {
                                StockChange::create([
                                    'item_id' => $existing->id,
                                    'user_id' => $userId,
                                    'change_type' => $diff >= 0 ? 'in' : 'out',
                                    'qty' => abs($diff),
                                    'note' => json_encode(['type' => 'import_update', 'source' => 'SID Retail Pro'], JSON_UNESCAPED_UNICODE),
                                    'occurred_at' => $importDate,
                                ]);
                            }
                        }
                    } else {
                        // New Item Defaults
                        if (!isset($values['stock']))
                            $values['stock'] = 0;
                        if (!isset($values['purchase_price']))
                            $values['purchase_price'] = 0;
                        if (!isset($values['sale_price']))
                            $values['sale_price'] = 0;
                        if (!isset($values['min_stock']))
                            $values['min_stock'] = 0;
                        if (!isset($values['notif_active']))
                            $values['notif_active'] = 1;
                        if (!isset($values['unit']))
                            $values['unit'] = 'pcs';

                        $newItem = Item::create(array_merge(['code' => $code], $values));
                        $itemId = $newItem->id;
                        $imported++;
                        $logStock = $values['stock'];

                        if ($logStock > 0) {
                            StockChange::create([
                                'item_id' => $newItem->id,
                                'user_id' => $userId,
                                'change_type' => 'in',
                                'qty' => $logStock,
                                'note' => json_encode(['type' => 'import_new', 'source' => 'SID Retail Pro'], JSON_UNESCAPED_UNICODE),
                                'occurred_at' => $importDate,
                            ]);
                        }
                    }

                    // IMPORT HISTORY LOG (Absolute State)
                    if ($itemId) {
                        StockChange::create([
                            'item_id' => $itemId,
                            'user_id' => $userId,
                            'change_type' => 'sid_sync',
                            'qty' => $logStock, // Use resolved stock
                            'note' => json_encode([
                                'source' => 'SID Retail Pro',
                                'unit' => $values['unit'] ?? ($existing->unit ?? 'pcs'),
                                'absolute_stock' => $logStock
                            ], JSON_UNESCAPED_UNICODE),
                            'occurred_at' => $importDate,
                        ]);
                    }
                }
                DB::commit();
            } catch (\Throwable $e) {
                DB::rollBack();
                return back()->with('error', 'Gagal memproses XLS: ' . $e->getMessage());
            }

            // Update user onboarding status
            if (auth()->check()) {
                auth()->user()->update(['has_imported' => true]);
            }

            return redirect()->route('kp.detail_barang')->with('success', "Import selesai. Tambah: {$imported}, Update: {$updated}");
        } elseif ($ext === 'xlsx') {
            if (!class_exists(\ZipArchive::class)) {
                return back()->with('error', 'File XLSX memerlukan ekstensi ZIP PHP (ext-zip). Aktifkan ext-zip lalu ulangi.');
            }
            try {
                $spreadsheet = IOFactory::load($path);
            } catch (\Throwable $e) {
                return back()->with('error', 'Gagal membaca file XLSX: ' . $e->getMessage());
            }
            $worksheet = $spreadsheet->getActiveSheet();
            $sheet = $worksheet->toArray();
            if (!$sheet || count($sheet) === 0) {
                return back()->with('error', 'File XLSX kosong.');
            }
            $headerIndex = null;
            $headerMap = [];
            for ($i = 0; $i < count($sheet); $i++) {
                $row = $sheet[$i];
                $norm = array_map($normalize, $row);
                if (in_array('kode_barang', $norm, true) || in_array('kode', $norm, true)) {
                    $headerIndex = $i;
                    foreach ($row as $idx => $val) {
                        $headerMap[$normalize((string) $val)] = $idx;
                    }
                    break;
                }
            }
            if ($headerIndex === null) {
                return back()->with('error', 'Header XLSX tidak ditemukan. Pastikan ada kolom KODE BARANG dan NAMA BARANG.');
            }
            $col = [];
            foreach ($aliases as $key => $names) {
                $col[$key] = null;
                foreach ($names as $n) {
                    if (array_key_exists($n, $headerMap)) {
                        $col[$key] = $headerMap[$n];
                        break;
                    }
                }
            }
            if ($col['code'] === null || $col['name'] === null) {
                return back()->with('error', 'File harus memiliki kolom kode/kode_barang dan nama/nama_barang.');
            }
            $imported = 0;
            $updated = 0;
            $userId = auth()->id() ?: \App\Models\User::query()->value('id');

            DB::beginTransaction();
            try {
                for ($r = $headerIndex + 1; $r < count($sheet); $r++) {
                    $row = $sheet[$r];
                    $code = trim((string) ($row[$col['code']] ?? ''));
                    $name = trim((string) ($row[$col['name']] ?? ''));
                    if ($code === '' && $name === '') {
                        continue;
                    }
                    // Handle Partial Updates (null if missing)
                    $purchase = $col['purchase_price'] !== null ? (float) preg_replace('/[^0-9\.\-]/', '', (string) ($row[$col['purchase_price']] ?? '')) : null;
                    $sale = $col['sale_price'] !== null ? (float) preg_replace('/[^0-9\.\-]/', '', (string) ($row[$col['sale_price']] ?? '')) : null;
                    $stock = $col['stock'] !== null ? (int) preg_replace('/[^0-9\-]/', '', (string) ($row[$col['stock']] ?? '')) : null;
                    $minStock = $col['min_stock'] !== null ? (int) preg_replace('/[^0-9\-]/', '', (string) ($row[$col['min_stock']] ?? '')) : null;
                    $notifRaw = $col['notif_active'] !== null ? strtolower(trim((string) ($row[$col['notif_active']] ?? ''))) : null;
                    $notif = $notifRaw !== null ? (in_array($notifRaw, ['1', 'true', 'ya', 'aktif', 'on', 'y'], true) ? 1 : 0) : null;

                    $categoryName = $col['category'] !== null ? trim((string) ($row[$col['category']] ?? '')) : null;
                    $categoryId = null;
                    if ($categoryName !== null && $categoryName !== '') {
                        $category = Category::firstOrCreate(
                            ['name' => $categoryName],
                            ['is_active' => 1]
                        );
                        $categoryId = $category->id;
                    }

                    $values = [
                        'name' => $name === '' ? $code : $name,
                    ];
                    if ($purchase !== null)
                        $values['purchase_price'] = $purchase;
                    if ($sale !== null)
                        $values['sale_price'] = $sale;
                    if ($stock !== null)
                        $values['stock'] = $stock;
                    if ($minStock !== null)
                        $values['min_stock'] = $minStock;
                    if ($notif !== null)
                        $values['notif_active'] = $notif;
                    if ($categoryId !== null)
                        $values['category_id'] = $categoryId;

                    // Attempt to find unit if column exists (alias map check needed but let's default to pcs or try to read)
                    if (isset($col['unit']) && $col['unit'] !== null) {
                        $values['unit'] = trim((string) ($row[$col['unit']] ?? 'pcs'));
                        if ($values['unit'] === '')
                            $values['unit'] = 'pcs';
                    }

                    $existing = Item::where('code', $code)->first();
                    $itemId = null;
                    $logStock = $stock;

                    if ($existing) {
                        $itemId = $existing->id;
                        $beforeStock = $existing->stock;
                        $logStock = $stock !== null ? $stock : $beforeStock;

                        $existing->update($values);
                        $updated++;

                        // Log Diff for Dashboard Flow
                        if ($stock !== null) {
                            $diff = $stock - $beforeStock;
                            if ($diff !== 0) {
                                StockChange::create([
                                    'item_id' => $existing->id,
                                    'user_id' => $userId,
                                    'change_type' => $diff >= 0 ? 'in' : 'out',
                                    'qty' => abs($diff),
                                    'note' => json_encode(['type' => 'import_update', 'source' => 'SID Retail Pro'], JSON_UNESCAPED_UNICODE),
                                    'occurred_at' => $importDate,
                                ]);
                            }
                        }
                    } else {
                        // New Item Defaults
                        if (!isset($values['stock']))
                            $values['stock'] = 0;
                        if (!isset($values['purchase_price']))
                            $values['purchase_price'] = 0;
                        if (!isset($values['sale_price']))
                            $values['sale_price'] = 0;
                        if (!isset($values['min_stock']))
                            $values['min_stock'] = 0;
                        if (!isset($values['notif_active']))
                            $values['notif_active'] = 1;
                        if (!isset($values['unit']))
                            $values['unit'] = 'pcs';

                        $newItem = Item::create(array_merge(['code' => $code], $values));
                        $itemId = $newItem->id;
                        $imported++;
                        $logStock = $values['stock'];

                        if ($logStock > 0) {
                            StockChange::create([
                                'item_id' => $newItem->id,
                                'user_id' => $userId,
                                'change_type' => 'in',
                                'qty' => $logStock,
                                'note' => json_encode(['type' => 'import_new', 'source' => 'SID Retail Pro'], JSON_UNESCAPED_UNICODE),
                                'occurred_at' => $importDate,
                            ]);
                        }
                    }

                    // IMPORT HISTORY LOG (Absolute State)
                    if ($itemId) {
                        StockChange::create([
                            'item_id' => $itemId,
                            'user_id' => $userId,
                            'change_type' => 'sid_sync',
                            'qty' => $logStock, // Use resolved stock
                            'note' => json_encode([
                                'source' => 'SID Retail Pro',
                                'unit' => $values['unit'] ?? ($existing->unit ?? 'pcs'),
                                'absolute_stock' => $logStock
                            ], JSON_UNESCAPED_UNICODE),
                            'occurred_at' => $importDate,
                        ]);
                    }
                }
                DB::commit();
            } catch (\Throwable $e) {
                DB::rollBack();
                return back()->with('error', 'Gagal memproses XLSX: ' . $e->getMessage());
            }

            // Update user onboarding status
            if (auth()->check()) {
                auth()->user()->update(['has_imported' => true]);
            }

            return redirect()->route('kp.detail_barang')->with('success', "Import selesai. Tambah: {$imported}, Update: {$updated}");
        } else {
            return back()->with('error', 'Format file tidak didukung. Gunakan CSV atau XLS.');
        }
    }

    public function previewSid(Request $request)
    {
        $request->validate([
            'sid_file' => ['required', 'file'],
        ]);
        $file = $request->file('sid_file');
        if (!$file->isValid() || ($file->getSize() ?? 0) <= 0) {
            return back()->with('error', 'Upload gagal atau file kosong.');
        }
        $path = $file->getPathname();
        $ext = strtolower($file->getClientOriginalExtension());
        if ($ext === 'zip') {
            if (!class_exists(\ZipArchive::class)) {
                return back()->with('error', 'Ekstensi ZIP PHP belum aktif. Aktifkan ext-zip untuk memproses .zip.');
            }
            $zip = new \ZipArchive();
            if ($zip->open($path) === true) {
                $targetIndex = -1;
                $targetExt = null;
                for ($i = 0; $i < $zip->numFiles; $i++) {
                    $name = $zip->getNameIndex($i);
                    $e = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                    if (in_array($e, ['xls', 'xlsx', 'csv', 'txt'], true)) {
                        $targetIndex = $i;
                        $targetExt = $e;
                        break;
                    }
                }
                if ($targetIndex >= 0) {
                    $tmp = tempnam(sys_get_temp_dir(), 'sidzip_');
                    file_put_contents($tmp, $zip->getFromIndex($targetIndex));
                    $path = $tmp;
                    $ext = $targetExt;
                } else {
                    $zip->close();
                    return back()->with('error', 'File .zip tidak mengandung CSV/XLS/XLSX yang didukung.');
                }
                $zip->close();
            } else {
                return back()->with('error', 'Tidak dapat membuka file .zip.');
            }
        }
        $normalize = function ($s) {
            $s = (string) $s;
            $s = trim(strtolower($s));
            $s = str_replace([' ', '-', '.', '/', '\\'], '_', $s);
            return $s;
        };
        $headers = [];
        $rows = [];
        if (in_array($ext, ['csv', 'txt'], true)) {
            $probe = @file_get_contents($path, false, null, 0, 16384) ?: '';
            $delims = [
                ',' => substr_count($probe, ','),
                ';' => substr_count($probe, ';'),
                "\t" => substr_count($probe, "\t"),
                '|' => substr_count($probe, '|'),
            ];
            arsort($delims);
            $likelyDelim = array_key_first($delims);
            $handle = fopen($path, 'r');
            if (!$handle) {
                return back()->with('error', 'Tidak dapat membuka file yang diunggah.');
            }
            $found = false;
            $buffer = [];
            $tries = 0;
            while (($r = fgetcsv($handle, 0, $likelyDelim ?: ',')) !== false && $tries < 200) {
                $tries++;
                $norm = array_map($normalize, $r);
                if (in_array('kode_barang', $norm, true) || in_array('kode', $norm, true)) {
                    $headers = $r;
                    $found = true;
                    break;
                }
                $buffer[] = $r;
            }
            if (!$found) {
                $headers = $buffer[0] ?? [];
                $rows = array_slice($buffer, 1, 50);
            } else {
                $count = 0;
                while (($r = fgetcsv($handle, 0, $likelyDelim ?: ',')) !== false && $count < 50) {
                    $rows[] = $r;
                    $count++;
                }
            }
            fclose($handle);
        } elseif ($ext === 'xls' || $ext === 'xlsx') {
            $sheet = null;
            if ($ext === 'xls') {
                $probe = @file_get_contents($path, false, null, 0, 16384) ?: '';
                $isHtml = stripos($probe, '<html') !== false || stripos($probe, '<table') !== false;
                $hasNewline = strpos($probe, "\n") !== false || strpos($probe, "\r") !== false;
                $isZip = substr($probe, 0, 2) === 'PK';
                $delims = [
                    ',' => substr_count($probe, ','),
                    ';' => substr_count($probe, ';'),
                    "\t" => substr_count($probe, "\t"),
                    '|' => substr_count($probe, '|'),
                ];
                arsort($delims);
                $likelyDelim = array_key_first($delims);
                $looksCsv = $hasNewline && ($delims[','] > 5 || $delims[';'] > 5 || $delims["\t"] > 5 || $delims['|'] > 5);
                if ($isHtml) {
                    try {
                        $htmlReader = new \PhpOffice\PhpSpreadsheet\Reader\Html();
                        $spreadsheet = $htmlReader->load($path);
                        $sheet = $spreadsheet->getActiveSheet()->toArray();
                    } catch (\Throwable $e) {
                    }
                }
                if (!$sheet && $looksCsv) {
                    try {
                        $csvReader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
                        $csvReader->setDelimiter($likelyDelim ?: ',');
                        $csvReader->setEnclosure('"');
                        $spreadsheet = $csvReader->load($path);
                        $sheet = $spreadsheet->getActiveSheet()->toArray();
                    } catch (\Throwable $e) {
                    }
                }
                if (!$sheet) {
                    try {
                        $xls = SimpleXLS::parse($path);
                        if ($xls) {
                            $sheet = $xls->rows();
                        }
                    } catch (\Throwable $e) {
                    }
                }
                if (!$sheet) {
                    try {
                        $reader = IOFactory::createReader('Xls');
                        $reader->setReadDataOnly(true);
                        $spreadsheet = $reader->load($path);
                        $sheet = $spreadsheet->getActiveSheet()->toArray();
                    } catch (\Throwable $e) {
                        if ($isZip) {
                            try {
                                $reader = IOFactory::createReader('Xlsx');
                                $reader->setReadDataOnly(true);
                                $spreadsheet = $reader->load($path);
                                $sheet = $spreadsheet->getActiveSheet()->toArray();
                            } catch (\Throwable $e2) {
                            }
                        }
                    }
                }
            } else {
                if (!class_exists(\ZipArchive::class)) {
                    return back()->with('error', 'File XLSX memerlukan ekstensi ZIP PHP (ext-zip). Aktifkan ext-zip lalu ulangi.');
                }
                try {
                    $spreadsheet = IOFactory::load($path);
                    $sheet = $spreadsheet->getActiveSheet()->toArray();
                } catch (\Throwable $e) {
                }
            }
            if (!$sheet || count($sheet) === 0) {
                return back()->with('error', 'Tidak dapat membaca isi file.');
            }
            $headerIndex = null;
            for ($i = 0; $i < count($sheet); $i++) {
                $row = $sheet[$i];
                $norm = array_map($normalize, $row);
                if (in_array('kode_barang', $norm, true) || in_array('kode', $norm, true)) {
                    $headerIndex = $i;
                    break;
                }
            }
            if ($headerIndex === null) {
                $headers = $sheet[0];
                $rows = array_slice($sheet, 1, 50);
            } else {
                $headers = $sheet[$headerIndex];
                $rows = array_slice($sheet, $headerIndex + 1, 50);
            }
        } else {
            return back()->with('error', 'Format file tidak didukung. Gunakan CSV, TXT, XLS, atau XLSX.');
        }
        return view('kp.import', ['preview' => ['headers' => $headers, 'rows' => $rows]]);
    }

    /**
     * Skip import during onboarding - go directly to dashboard
     */
    public function skipImport(Request $request)
    {
        $user = auth()->user();

        if ($user && ($user->role === 'admin' || $user->role === 'staff')) {
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

        if ($user && ($user->role === 'admin' || $user->role === 'staff') && !$user->has_viewed_stock) {
            $user->update(['has_viewed_stock' => true]);

            return redirect()->route('kp.dashboard')
                ->with('success', 'Setup selesai! Selamat datang di dashboard.');
        }

        return redirect()->route('kp.dashboard');
    }
    public function processExport(Request $request)
    {
        $type = $request->query('type', 'stok-akhir');
        $format = $request->query('format', 'csv');
        $start = $request->query('start');
        $end = $request->query('end');
        $itemId = $request->query('item_id');

        $headers = [];
        $dataRows = [];
        $filename = '';

        // All Categories for Inferred Logic
        $allCategories = Category::pluck('name')->toArray();

        // Date Range logic: User says "Rentang tanggal pada export selalu mengacu pada tanggal import"
        // Meaning we look at StockChange where change_type = 'sid_sync' (or all changes?)
        // Usually, for SID context, we look at 'sid_sync' records.

        $from = $start ? \Carbon\Carbon::parse($start)->startOfDay() : null;
        $to = $end ? \Carbon\Carbon::parse($end)->endOfDay() : null;

        if ($type === 'stok-akhir') {
            $filename = ($format === 'csv') ? 'CSV_Master_Stok' : 'Laporan_Stok_Akhir_' . date('Y-m-d_H-i');
            $headers = ['Kode Barang', 'Nama Barang', 'Kategori', 'Stok', 'Unit', 'Minimum', 'Status', 'Harga Beli', 'Harga Jual'];
            $query = Item::with('category');
            if ($from && $to) {
                $query->whereHas('changes', function ($q) use ($from, $to) {
                    $q->whereBetween('occurred_at', [$from, $to]);
                });
            }
            $items = $query->orderBy('name')->get();

            foreach ($items as $item) {
                $catName = $item->category ? $item->category->name : $this->inferCategory($item, $allCategories);
                $status = $item->stock <= 0 ? 'Habis' : ($item->stock < $item->min_stock ? 'Menipis' : 'Aman');
                $dataRows[] = [
                    $item->code,
                    $item->name,
                    $catName,
                    $item->stock,
                    $item->unit,
                    $item->min_stock,
                    $status,
                    $item->purchase_price,
                    $item->sale_price
                ];
            }
        } elseif ($type === 'riwayat-stok') {
            $filename = ($format === 'csv') ? 'CSV_Riwayat_Stok' : 'Laporan_Riwayat_Stok_' . date('Y-m-d_H-i');
            $headers = ['Tanggal Import', 'Kode Barang', 'Nama Barang', 'Kategori', 'Qty Masuk', 'Unit', 'User'];

            $query = StockChange::with(['item.category', 'user'])->where('change_type', 'sid_sync');
            if ($from && $to) {
                $query->whereBetween('occurred_at', [$from, $to]);
            }
            $changes = $query->orderBy('occurred_at', 'desc')->get();

            foreach ($changes as $c) {
                $item = $c->item;
                $catName = $item && $item->category ? $item->category->name : ($item ? $this->inferCategory($item, $allCategories) : '-');
                $dataRows[] = [
                    $c->occurred_at->format('Y-m-d H:i'),
                    $item->code ?? '-',
                    $item->name ?? '-',
                    $catName,
                    $c->qty,
                    $item->unit ?? 'pcs',
                    $c->user->name ?? 'System'
                ];
            }
        } elseif ($type === 'barang-menipis') {
            $filename = ($format === 'csv') ? 'CSV_Laporan_Menipis' : 'Laporan_Barang_Menipis_' . date('Y-m-d_H-i');
            $headers = ['Kode Barang', 'Nama Barang', 'Kategori', 'Stok Saat Ini', 'Minimum', 'Unit', 'Harga Jual'];

            // Logic copied from Dashboard/Daftar Stok to ensure consistency
            $notifOn = (int) Setting::get('notif.global.active', cache()->get('notif.global.active', 1));
            $mode = (string) Setting::get('notif.global.mode', cache()->get('notif.global.mode', 'per_item'));
            $defaultMin = (int) Setting::get('notif.global.default_min', cache()->get('notif.global.default_min', 10));
            $categoryDefaults = [];

            if ($mode === 'category') {
                foreach (Category::orderBy('name')->get() as $cat) {
                    $categoryDefaults[$cat->name] = (int) Setting::get('notif.category.' . $cat->name . '.default_min', $defaultMin);
                }
            }

            // Fetch items based on mode
            $items = collect();

            if ($mode === 'global') {
                // Global: Stock < defaultMin
                $items = Item::with('category')
                    ->where('stock', '<', $defaultMin)
                    ->orderBy('name')
                    ->get();
            } elseif ($mode === 'category') {
                // Category: Filter via PHP as per dashboard implementation
                $rawItems = Item::with('category')->get();
                $items = $rawItems->filter(function ($it) use ($categoryDefaults, $defaultMin) {
                    $catName = $it->category ? $it->category->name : null;
                    $catMin = $catName !== null && array_key_exists($catName, $categoryDefaults) ? (int) $categoryDefaults[$catName] : $defaultMin;
                    $threshold = max((int) ($it->min_stock ?? 0), $catMin);
                    return (int) $it->stock < $threshold;
                })->sortBy('name');
            } else {
                // Per Item (Default): Complex query logic
                $items = Item::with('category')
                    ->where(function ($w) use ($defaultMin) {
                        $w->where(function ($q) use ($defaultMin) {
                            $q->where('min_stock', '>=', $defaultMin)->whereColumn('stock', '<', 'min_stock');
                        })->orWhere(function ($q) use ($defaultMin) {
                            $q->where(function ($x) use ($defaultMin) {
                                $x->whereNull('min_stock')
                                    ->orWhere('min_stock', '<=', 0);
                            })->where('stock', '<', $defaultMin);
                        });
                    })
                    ->orderBy('name')
                    ->get();
            }

            foreach ($items as $item) {
                // Re-calculate threshold for display correctness
                $catName = $item->category ? $item->category->name : $this->inferCategory($item, $allCategories);
                if ($mode === 'global') {
                    $threshold = $defaultMin;
                } elseif ($mode === 'category') {
                    $catMin = $catName !== '-' && array_key_exists($catName, $categoryDefaults) ? $categoryDefaults[$catName] : $defaultMin;
                    $threshold = max((int) ($item->min_stock ?? 0), $catMin);
                } else {
                    $threshold = (int) $item->min_stock > 0 ? (int) $item->min_stock : $defaultMin;
                }

                $dataRows[] = [
                    $item->code,
                    $item->name,
                    $catName,
                    $item->stock,
                    $threshold, // Show the Effective Minimum used for this alert
                    $item->unit,
                    $item->sale_price
                ];
            }

        } elseif ($type === 'per-barang') {
            $item = Item::with('category')->find($itemId);
            if (!$item)
                return back()->with('error', 'Barang tidak ditemukan.');

            $filename = 'Laporan_Detail_' . str_replace(' ', '_', $item->name) . '_' . date('Y-m-d_H-i');
            $headers = ['Tanggal Import', 'Qty Masuk', 'Unit', 'User', 'Catatan'];

            $query = StockChange::with('user')->where('item_id', $itemId)->where('change_type', 'sid_sync');
            if ($from && $to) {
                $query->whereBetween('occurred_at', [$from, $to]);
            }
            $changes = $query->orderBy('occurred_at', 'desc')->get();

            foreach ($changes as $c) {
                $dataRows[] = [
                    $c->occurred_at->format('Y-m-d H:i'),
                    $c->qty,
                    $item->unit,
                    $c->user->name ?? 'System',
                    $c->note
                ];
            }
        }

        if ($format === 'pdf') {
            return view('kp.export_pdf_preview', [
                'type' => $type,
                'headers' => $headers,
                'rows' => $dataRows,
                'start' => $start,
                'end' => $end,
                'filename' => $filename
            ]);
        }

        if ($format === 'excel') {
            return $this->exportExcelGeneric($headers, $dataRows, $filename);
        }

        if ($format === 'csv') {
            return $this->exportCsvGeneric($headers, $dataRows, $filename);
        }

        return back()->with('error', 'Format tidak didukung.');
    }

    private function inferCategory($item, $allCategories)
    {
        $code = $item->code ?? '';
        $name = $item->name ?? '';
        foreach ($allCategories as $catName) {
            if (stripos($code, $catName) !== false || stripos($name, $catName) !== false) {
                return $catName;
            }
        }
        return '-';
    }

    private function exportExcelGeneric($headers, $dataRows, $filename)
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header
        $col = 'A';
        foreach ($headers as $h) {
            $sheet->setCellValue($col . '1', $h);
            $sheet->getStyle($col . '1')->getFont()->setBold(true);
            $col++;
        }

        // Data
        $row = 2;
        foreach ($dataRows as $rowData) {
            $col = 'A';
            foreach ($rowData as $val) {
                $sheet->setCellValue($col . $row, $val);
                $col++;
            }
            $row++;
        }

        // Auto size columns
        $lastCol = $sheet->getHighestColumn();
        foreach (range('A', $lastCol) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename . '.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    private function exportCsvGeneric($headers, $dataRows, $filename)
    {
        return response()->streamDownload(function () use ($headers, $dataRows) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $headers); // Header

            foreach ($dataRows as $rowData) {
                fputcsv($handle, $rowData);
            }
            fclose($handle);
        }, $filename . '.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }

    public function importPenjualanView()
    {
        return view('kp.import_penjualan');
    }

    public function importPenjualanPost(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file',
        ]);

        $file = $request->file('csv_file');
        $handle = fopen($file->getRealPath(), 'r');

        // Deteksi delimiter (koma atau titik koma)
        $firstLine = fgets($handle);
        $delimiter = (strpos($firstLine, ';') !== false) ? ';' : ',';
        rewind($handle);

        $header = fgetcsv($handle, 1000, $delimiter);

        $successCount = 0;
        $errorCount = 0;
        $errors = [];

        DB::beginTransaction();
        try {
            while (($data = fgetcsv($handle, 1000, $delimiter)) !== FALSE) {
                // Format CSV dari App 2:
                // Kolom D (index 3) = Kode Barang
                // Kolom F (index 5) = Jumlah Terjual
                $code = trim($data[3] ?? '');
                $qty = (int) ($data[5] ?? 0);

                if (empty($code) || $qty <= 0) {
                    continue;
                }

                $item = Item::where('code', $code)->first();
                if ($item) {
                    $oldStock = $item->stock;
                    $item->stock -= $qty;
                    $item->save();

                    StockChange::create([
                        'item_id' => $item->id,
                        'user_id' => Auth::id(),
                        'change_type' => 'out',
                        'qty' => $qty,
                        'note' => json_encode([
                            'source' => 'Sync App 2 (Flashdisk)',
                            'old_stock' => $oldStock,
                            'new_stock' => $item->stock
                        ]),
                        'occurred_at' => now(),
                    ]);
                    $successCount++;
                } else {
                    $errorCount++;
                    $errors[] = "Barang '$code' tidak terdaftar di sistem stok.";
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memproses file: ' . $e->getMessage());
        } finally {
            fclose($handle);
        }

        $msg = "Berhasil sinkronisasi $successCount data penjualan dari Aplikasi 2.";
        if ($errorCount > 0) {
            $msg .= " $errorCount data gagal (barang tidak ditemukan).";
        }

        return redirect()->route('kp.daftar_stok')
            ->with($errorCount > 0 ? 'warning' : 'success', $msg)
            ->with('import_errors', $errors);
    }
}
