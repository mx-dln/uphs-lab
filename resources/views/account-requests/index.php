<?php 
$title = '- Account Requests'; 
$status = $status ?? '';
$counts = $counts ?? [
    'all' => count($requests ?? []),
    'pending' => 0,
    'approved' => 0,
    'rejected' => 0,
];
?>
<?php ob_start(); ?>
<div class="max-w-7xl mx-auto space-y-6 pb-20">

    <!-- Top Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center space-x-3">
                <h1 class="text-2xl font-extrabold text-gray-900 dark:text-white tracking-tight">Account Requests</h1>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-primary-100 text-primary-800 dark:bg-primary-900/40 dark:text-primary-300">
                    Capability for Account Requests
                </span>
            </div>
            <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Review, verify, and approve incoming account requests from faculty, staff, and laboratory custodians</p>
        </div>
        <div class="flex items-center space-x-2">
            <!-- Direct Add Account Button (Icon only plus, text without +) -->
            <button type="button" onclick="openAddAccountModal()" class="inline-flex items-center space-x-2 px-4 py-2.5 bg-primary-600 hover:bg-primary-700 active:bg-primary-800 text-white rounded-xl text-xs font-semibold shadow-sm transition-all cursor-pointer whitespace-nowrap">
                <i data-lucide="plus" class="w-4 h-4 stroke-[2.5]"></i>
                <span>Direct Add Account</span>
            </button>
        </div>
    </div>

    <!-- Status Filter Buttons (Segmented Pill Controls) -->
    <div class="bg-white dark:bg-gray-800 rounded-3xl p-4 shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
            <div class="flex flex-wrap items-center gap-1.5 bg-gray-100/80 dark:bg-gray-700/50 p-1.5 rounded-2xl border border-gray-200/50 dark:border-gray-700">
                <span class="px-2.5 py-1 text-[11px] font-bold text-gray-500 uppercase tracking-wider">Filter Status:</span>
                
                <!-- All -->
                <a href="/account-requests" class="inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-xl font-semibold transition-all <?= empty($status) ? 'bg-white dark:bg-gray-800 text-primary-600 dark:text-primary-400 shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white' ?>">
                    <span>All</span>
                    <span class="px-1.5 py-0.2 rounded-full text-[10px] font-bold <?= empty($status) ? 'bg-primary-100 dark:bg-primary-900/40 text-primary-700 dark:text-primary-300' : 'bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-300' ?>"><?= $counts['all'] ?? count($requests) ?></span>
                </a>

                <!-- Pending -->
                <a href="/account-requests?status=pending" class="inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-xl font-semibold transition-all <?= $status === 'pending' ? 'bg-white dark:bg-gray-800 text-amber-600 dark:text-amber-400 shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white' ?>">
                    <span>Pending</span>
                    <span class="px-1.5 py-0.2 rounded-full text-[10px] font-bold <?= $status === 'pending' ? 'bg-amber-100 dark:bg-amber-900/40 text-amber-800 dark:text-amber-300' : 'bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-300' ?>"><?= $counts['pending'] ?? 0 ?></span>
                </a>

                <!-- Approved -->
                <a href="/account-requests?status=approved" class="inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-xl font-semibold transition-all <?= $status === 'approved' ? 'bg-white dark:bg-gray-800 text-emerald-600 dark:text-emerald-400 shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white' ?>">
                    <span>Approved</span>
                    <span class="px-1.5 py-0.2 rounded-full text-[10px] font-bold <?= $status === 'approved' ? 'bg-emerald-100 dark:bg-emerald-900/40 text-emerald-800 dark:text-emerald-300' : 'bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-300' ?>"><?= $counts['approved'] ?? 0 ?></span>
                </a>

                <!-- Declined -->
                <a href="/account-requests?status=rejected" class="inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-xl font-semibold transition-all <?= $status === 'rejected' ? 'bg-white dark:bg-gray-800 text-rose-600 dark:text-rose-400 shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white' ?>">
                    <span>Declined</span>
                    <span class="px-1.5 py-0.2 rounded-full text-[10px] font-bold <?= $status === 'rejected' ? 'bg-rose-100 dark:bg-rose-900/40 text-rose-800 dark:text-rose-300' : 'bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-300' ?>"><?= $counts['rejected'] ?? 0 ?></span>
                </a>
            </div>

            <div class="text-xs text-gray-500 dark:text-gray-400">
                Total Requests: <span class="font-bold text-gray-900 dark:text-white"><?= count($requests) ?></span>
            </div>
        </div>
    </div>

    <!-- Requests Table -->
    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-700/50 text-gray-500 dark:text-gray-400 font-semibold uppercase text-[11px] tracking-wider border-b border-gray-100 dark:border-gray-700">
                        <th class="px-5 py-4 rounded-l-2xl whitespace-nowrap">Request Ref (Long NR)</th>
                        <th class="px-4 py-4 whitespace-nowrap">Name & ID Number</th>
                        <th class="px-4 py-4 whitespace-nowrap">Email</th>
                        <th class="px-4 py-4 whitespace-nowrap">Department / Segment</th>
                        <th class="px-4 py-4 whitespace-nowrap">Requested Role</th>
                        <th class="px-4 py-4">Reason / Justification</th>
                        <th class="px-4 py-4 text-center whitespace-nowrap">Status</th>
                        <th class="px-5 py-4 rounded-r-2xl text-right whitespace-nowrap" style="white-space: nowrap !important; min-width: 250px !important; width: 250px !important;">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    <?php if (empty($requests)): ?>
                        <tr>
                            <td colspan="8" class="py-12 text-center text-gray-400">
                                <div class="flex flex-col items-center justify-center space-y-2">
                                    <i data-lucide="inbox" class="w-8 h-8 text-gray-300 dark:text-gray-600"></i>
                                    <p class="font-medium">No account requests found matching your filter.</p>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($requests as $ar): ?>
                        <tr class="hover:bg-gray-50/70 dark:hover:bg-gray-700/30 transition-colors">
                            <!-- Long NR Reference -->
                            <td class="px-5 py-4 font-mono font-bold text-primary-600 dark:text-primary-400 whitespace-nowrap align-middle">
                                <?= escape($ar['request_number']) ?>
                            </td>

                            <!-- Name & ID -->
                            <td class="px-4 py-4 align-middle">
                                <span class="font-bold text-gray-900 dark:text-white block"><?= escape($ar['name']) ?></span>
                                <?php if (!empty($ar['id_number'])): ?>
                                    <span class="text-[11px] font-mono text-gray-400 block mt-0.5"><?= escape($ar['id_number']) ?></span>
                                <?php endif; ?>
                            </td>

                            <!-- Email -->
                            <td class="px-4 py-4 text-gray-600 dark:text-gray-300 whitespace-nowrap align-middle">
                                <?= escape($ar['email']) ?>
                            </td>

                            <!-- Department / Segment -->
                            <td class="px-4 py-4 font-medium text-gray-800 dark:text-gray-200 align-middle">
                                <?= escape($ar['department_segment']) ?>
                            </td>

                            <!-- Requested Role -->
                            <td class="px-4 py-4 font-semibold text-indigo-600 dark:text-indigo-400 whitespace-nowrap align-middle">
                                <?= escape($ar['requested_role_name'] ?? 'Faculty') ?>
                            </td>

                            <!-- Reason -->
                            <td class="px-4 py-4 text-gray-500 dark:text-gray-400 max-w-xs truncate align-middle" title="<?= escape($ar['reason'] ?? '') ?>">
                                <?= escape($ar['reason'] ?? 'No reason provided') ?>
                            </td>

                            <!-- Status Badge -->
                            <td class="px-4 py-4 text-center align-middle whitespace-nowrap">
                                <?php if ($ar['status'] === 'approved'): ?>
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300">
                                        <span class="w-1.5 h-1.5 mr-1.5 bg-emerald-500 rounded-full"></span>
                                        Approved
                                    </span>
                                <?php elseif ($ar['status'] === 'rejected'): ?>
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold bg-rose-100 text-rose-800 dark:bg-rose-900/30 dark:text-rose-300">
                                        <span class="w-1.5 h-1.5 mr-1.5 bg-rose-500 rounded-full"></span>
                                        Declined
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300">
                                        <span class="w-1.5 h-1.5 mr-1.5 bg-amber-500 rounded-full animate-pulse"></span>
                                        Pending
                                    </span>
                                <?php endif; ?>
                            </td>

                            <!-- Actions Buttons: Fixed Side-by-Side Flex Layout -->
                            <td class="px-5 py-4 text-right whitespace-nowrap align-middle" style="white-space: nowrap !important; min-width: 250px !important; width: 250px !important;">
                                <?php if ($ar['status'] === 'pending'): ?>
                                    <div class="flex items-center justify-end gap-2" style="display: flex !important; flex-direction: row !important; align-items: center !important; justify-content: flex-end !important; gap: 8px !important; flex-wrap: nowrap !important; white-space: nowrap !important;">
                                        <!-- Approve & Create Button -->
                                        <form method="POST" action="/account-requests/<?= $ar['id'] ?>/approve" style="display: inline-flex !important; margin: 0 !important;">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="px-3.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white rounded-xl text-xs font-semibold shadow-sm shadow-emerald-600/10 transition-all cursor-pointer whitespace-nowrap" style="display: inline-flex !important; flex-direction: row !important; align-items: center !important; gap: 6px !important; white-space: nowrap !important; line-height: 1.25 !important;">
                                                <i data-lucide="check" class="w-3.5 h-3.5 stroke-[2.5]"></i>
                                                <span>Approve & Create</span>
                                            </button>
                                        </form>

                                        <!-- Decline Button -->
                                        <form method="POST" action="/account-requests/<?= $ar['id'] ?>/reject" onsubmit="return confirm('Are you sure you want to decline this account request?');" style="display: inline-flex !important; margin: 0 !important;">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="px-3.5 py-1.5 bg-red-600 hover:bg-red-700 active:bg-red-800 text-white rounded-xl text-xs font-semibold shadow-sm transition-all cursor-pointer whitespace-nowrap" style="display: inline-flex !important; flex-direction: row !important; align-items: center !important; gap: 4px !important; white-space: nowrap !important; line-height: 1.25 !important;">
                                                <i data-lucide="x" class="w-3.5 h-3.5 stroke-[2.5]"></i>
                                                <span>Decline</span>
                                            </button>
                                        </form>
                                    </div>
                                <?php elseif ($ar['status'] === 'approved'): ?>
                                    <span class="inline-flex items-center space-x-1.5 px-2.5 py-1 rounded-xl text-xs font-medium text-emerald-700 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800/50">
                                        <i data-lucide="check-circle-2" class="w-3.5 h-3.5 text-emerald-500"></i>
                                        <span>Approved & Created</span>
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center space-x-1.5 px-2.5 py-1 rounded-xl text-xs font-medium text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-700/50">
                                        <i data-lucide="x-circle" class="w-3.5 h-3.5 text-gray-400"></i>
                                        <span>Declined</span>
                                    </span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Direct Add Account Modal -->
<div id="addAccountModal" class="hidden fixed inset-0 z-50 bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-2xl max-w-lg w-full overflow-hidden border border-gray-200 dark:border-gray-700 animate-fade-in" onclick="event.stopPropagation()">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
            <div class="flex items-center space-x-2">
                <div class="w-8 h-8 rounded-lg bg-primary-50 dark:bg-primary-900/40 text-primary-600 flex items-center justify-center">
                    <i data-lucide="user-plus" class="w-4 h-4"></i>
                </div>
                <div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white">Direct Add Account</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Create administrator, HR, or faculty accounts immediately</p>
                </div>
            </div>
            <button type="button" onclick="closeAddAccountModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 cursor-pointer">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form method="POST" action="/dashboard/account/add" class="p-6 space-y-4">
            <?= csrf_field() ?>
            <input type="hidden" name="redirect_to" value="/account-requests">
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-300 mb-1">Full Name</label>
                    <input type="text" name="name" required placeholder="Prof. Jane Doe" class="w-full px-3.5 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl text-xs text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-300 mb-1">Email Address</label>
                    <input type="email" name="email" required placeholder="jdoe@uphs.edu.ph" class="w-full px-3.5 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl text-xs text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-300 mb-1">Password</label>
                    <input type="password" name="password" required placeholder="••••••••" class="w-full px-3.5 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl text-xs text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-300 mb-1">System Role</label>
                    <select name="role_id" required class="w-full px-3.5 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl text-xs text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500">
                        <option value="4" selected>Faculty</option>
                        <option value="6">Human Resources (HR)</option>
                        <option value="2">Laboratory Administrator</option>
                        <option value="1">Super Administrator</option>
                        <option value="3">Laboratory Custodian</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-300 mb-1">Department / Segment Assignment</label>
                <input type="text" name="department_segment" placeholder="e.g. Human Resources / College of Computer Studies" class="w-full px-3.5 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl text-xs text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500">
                <p class="text-[11px] text-gray-400 mt-1">Assign department or lab segment.</p>
            </div>

            <div class="pt-3 border-t border-gray-100 dark:border-gray-700 flex items-center justify-end space-x-2">
                <button type="button" onclick="closeAddAccountModal()" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-xl text-xs font-semibold cursor-pointer">
                    Cancel
                </button>
                <button type="submit" class="px-5 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-xl text-xs font-semibold shadow-sm cursor-pointer">
                    Create Account
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openAddAccountModal() {
    const modal = document.getElementById('addAccountModal');
    if (modal) modal.classList.remove('hidden');
}
function closeAddAccountModal() {
    const modal = document.getElementById('addAccountModal');
    if (modal) modal.classList.add('hidden');
}
</script>

<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/app.php'; ?>
