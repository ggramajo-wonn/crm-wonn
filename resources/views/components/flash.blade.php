@php
  // Show and clear one-time flash message (prevents duplicates if component is included twice)
  $keys = ['ok', 'success', 'status', 'info', 'warning', 'error', 'danger', 'message'];
  $msg  = null;
  foreach ($keys as $k) {
      if (session()->has($k)) {
          // pull() gets and forgets so subsequent includes won't show it again
          $msg = session()->pull($k);
          break;
      }
  }
@endphp

@if(!empty($msg))
  @php
    $text = is_array($msg) ? implode(' ', array_filter($msg)) : (string)$msg;
    // Basic style selection (green for ok/success/status, amber for info/warning, red for error/danger)
    $cls = 'border-emerald-900/50 bg-emerald-950/70 text-emerald-300';
    if (str_contains(strtolower($text), 'error') || str_contains(strtolower($text), 'fall')) {
        $cls = 'border-red-900/50 bg-red-950/70 text-red-200';
    } elseif (str_contains(strtolower($text), 'advert') || str_contains(strtolower($text), 'warn')) {
        $cls = 'border-amber-900/50 bg-amber-950/70 text-amber-200';
    }
  @endphp
  <div class="mb-4 rounded-lg border p-3 {{ $cls }}">
    {{ $text }}
  </div>
@endif
