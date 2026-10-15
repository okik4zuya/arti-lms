<?php
// Real-artifact check: Laravel license code -> real local Worker -> real arti-license / arti-admin CLIs.
// Boots Laravel against the TESTING database only and aborts otherwise.
putenv('DB_DATABASE=arti_lms_testing'); $_ENV['DB_DATABASE'] = $_SERVER['DB_DATABASE'] = 'arti_lms_testing';
foreach (['CACHE_STORE' => 'array', 'SESSION_DRIVER' => 'array', 'QUEUE_CONNECTION' => 'sync'] as $k => $v) { putenv("$k=$v"); $_ENV[$k] = $_SERVER[$k] = $v; }

$root = 'c:/laragon6/www/arti-lms';
require $root.'/vendor/autoload.php';
$app = require $root.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

if (config('database.connections.mysql.database') !== 'arti_lms_testing') { fwrite(STDERR, "refusing: not the testing DB\n"); exit(2); }

$vars = [];
foreach (file('C:/Users/user/arti-secrets/dev/dev.vars', FILE_IGNORE_NEW_LINES) as $l) { if (str_contains($l, '=')) { [$a, $b] = explode('=', $l, 2); $vars[$a] = trim($b); } }
config(['services.licensing.url' => 'http://127.0.0.1:8760/v1', 'services.licensing.admin_token' => $vars['ADMIN_TOKEN']]);

use App\Actions\Licensing\{IssueLicense, IssueRequest, SyncLicense};
use App\Models\LicenseIssuance;
use App\Services\Licensing\{LicenseApi, LicenseApiException};

$PY = 'C:/Users/user/.arti/python/python.exe';
$CLI = 'C:/Users/user/.arti/tools/arti-license/cli.py';
$ADM = 'C:/Users/user/arti-services/release/arti_admin.py';
$run = (string) time();
$tmp = sys_get_temp_dir().'/arti-lic-verify-'.$run; @mkdir($tmp, 0777, true);
$fail = 0;
function check(string $name, bool $ok, string $detail = ''): void { global $fail; echo ($ok ? 'PASS  ' : 'FAIL  ').$name.($ok ? '' : "   <- $detail")."\n"; if (! $ok) $fail++; }
function sh(array $cmd, array $env): array {
    $p = proc_open($cmd, [1 => ['pipe', 'w'], 2 => ['pipe', 'w']], $pipes, null, array_merge(getenv(), $env));
    $out = stream_get_contents($pipes[1]).stream_get_contents($pipes[2]); return [proc_close($p), trim($out)];
}
$admEnv = ['ARTI_LICENSE_API' => 'http://127.0.0.1:8760/v1', 'ARTI_ADMIN_TOKEN' => $vars['ADMIN_TOKEN']];
function machine(string $name): array { global $tmp, $run, $admEnv; @mkdir("$tmp/$name"); return $admEnv + ['ARTI_HOME' => "$tmp/$name", 'ARTI_MACHINE_ID' => "machine-$name-$run"]; }

$issue = app(IssueLicense::class);
$sync = app(SyncLicense::class);

// 1. issue through the real Worker; key stored encrypted
$row = $issue->handle(new IssueRequest('v1-'.$run.'@example.test', 'Verify One', 'individual', 12, 'V-'.$run.'-1', amountIdr: 99000));
check('issue returns an active row with key and key_id', $row->status === 'active' && filled($row->key) && filled($row->key_id), json_encode($row->toArray()));
check('stored key is ciphertext in the DB', DB::table('license_issuances')->where('id', $row->id)->value('key') !== $row->key);
$key1 = $row->key; $kid1 = $row->key_id;

// 2. the key from the admin page really activates with the shipped client
[$rc, $out] = sh([$PY, '-I', $CLI, 'activate', $key1], machine('a'));
check('arti-license activate accepts the key issued from Laravel', $rc === 0, $out);

// 3. sync sees the activation
$sync->handle($row->refresh());
check('sync reads 1 activation', $row->refresh()->activations_count === 1, (string) $row->activations_count);

// 4. repeat order_ref: no second license
$again = $issue->handle(new IssueRequest('v1-'.$run.'@example.test', 'Verify One', 'individual', 12, 'V-'.$run.'-1'));
check('repeating an order_ref returns the same row, same key', $again->id === $row->id && $again->key === $key1);

// 5. revoke through the CLI, sync -> revoked; activation of a new machine refused
[$rc, $out] = sh([$PY, '-I', $ADM, 'revoke', $kid1, '--reason', 'verify'], $admEnv);
$sync->handle($row->refresh());
check('CLI revoke is picked up by sync (Worker wins)', $row->refresh()->status === 'revoked', $row->status);
[$rc, $out] = sh([$PY, '-I', $CLI, 'activate', $key1], machine('b'));
check('revoked key no longer activates on a new machine', $rc !== 0, $out);
sh([$PY, '-I', $ADM, 'revoke', $kid1, '--undo'], $admEnv);
$sync->handle($row->refresh());
check('undo revoke is picked up', $row->refresh()->status === 'active');

// 6. rotate through the CLI: stored key becomes stale
sh([$PY, '-I', $ADM, 'resend', $kid1, '--show'], $admEnv);
$sync->handle($row->refresh(), verifyKey: true);
check('a key rotated elsewhere is flagged stale', $row->refresh()->key_stale === true);

// 7. lost issue response: Worker issued, Laravel never saw the key
$api = app(LicenseApi::class);
$lost = $api->issue(['email' => 'v2-'.$run.'@example.test', 'name' => 'Lost', 'tier' => 'individual', 'months' => 12, 'order' => 'V-'.$run.'-2']);
$rec = $issue->handle(new IssueRequest('v2-'.$run.'@example.test', 'Lost', 'individual', 12, 'V-'.$run.'-2'));
check('409 without a local key rotates and stores a working key', filled($rec->key) && $rec->key !== $lost['key'] && $rec->status === 'active', json_encode($rec->toArray()));
[$rc, $out] = sh([$PY, '-I', $CLI, 'activate', $rec->key], machine('c'));
check('the recovered key activates', $rc === 0, $out);
[$rc, $out] = sh([$PY, '-I', $CLI, 'activate', $lost['key']], machine('d'));
check('the lost original key no longer works', $rc !== 0, $out);

// 8. unreachable Worker leaves pending; retry later works
config(['services.licensing.url' => 'http://127.0.0.1:8769/v1']);
try { app()->forgetInstance(LicenseApi::class); app(IssueLicense::class)->handle(new IssueRequest('v3-'.$run.'@example.test', 'Down', 'individual', 12, 'V-'.$run.'-3')); check('unreachable throws', false); }
catch (LicenseApiException $e) { check('unreachable Worker throws and leaves a pending row', $e->isUnreachable() && LicenseIssuance::where('order_ref', 'V-'.$run.'-3')->value('status') === 'pending'); }
config(['services.licensing.url' => 'http://127.0.0.1:8760/v1']);
$p = LicenseIssuance::where('order_ref', 'V-'.$run.'-3')->first();
$done = app(SyncLicense::class)->handle($p);
check('retry after the Worker is back issues exactly one license', $done->status === 'active' && LicenseIssuance::where('order_ref', 'V-'.$run.'-3')->count() === 1);

// 9. key not in logs
$logs = glob($root.'/storage/logs/*.log') ?: [];
$leak = false; foreach ($logs as $f) { $c = file_get_contents($f); foreach ([$key1, $rec->key, $lost['key'], $done->key] as $k) { if ($k && str_contains($c, $k)) $leak = true; } }
check('no key in storage/logs', ! $leak);

// cleanup of the test rows (testing DB only)
LicenseIssuance::where('order_ref', 'like', 'V-'.$run.'-%')->get()->each->delete();
echo $fail ? "\n$fail FAILED\n" : "\nALL PASSED\n";
exit($fail ? 1 : 0);
