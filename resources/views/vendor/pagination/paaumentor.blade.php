@if ($paginator->hasPages())
<nav style="display:flex;align-items:center;justify-content:space-between;gap:12px;font-size:0.82rem;flex-wrap:wrap">
  <span style="color:var(--text-3)">
    Showing {{ $paginator->firstItem() }}–{{ $paginator->lastItem() }} of {{ $paginator->total() }} results
  </span>
  <div style="display:flex;align-items:center;gap:4px">

    {{-- Previous --}}
    @if ($paginator->onFirstPage())
      <span style="padding:5px 10px;border:1px solid var(--border);border-radius:6px;color:var(--text-3);cursor:not-allowed;user-select:none">&lsaquo;</span>
    @else
      <a href="{{ $paginator->previousPageUrl() }}" style="padding:5px 10px;border:1px solid var(--border);border-radius:6px;color:var(--text-2);text-decoration:none;transition:background 0.15s" onmouseover="this.style.background='var(--bg-2)'" onmouseout="this.style.background='transparent'">&lsaquo;</a>
    @endif

    {{-- Page numbers --}}
    @foreach ($elements as $element)
      @if (is_string($element))
        <span style="padding:5px 8px;color:var(--text-3)">…</span>
      @endif
      @if (is_array($element))
        @foreach ($element as $page => $url)
          @if ($page == $paginator->currentPage())
            <span style="padding:5px 10px;border-radius:6px;background:#1e3a8a;color:#fff;font-weight:600;min-width:32px;text-align:center">{{ $page }}</span>
          @else
            <a href="{{ $url }}" style="padding:5px 10px;border:1px solid var(--border);border-radius:6px;color:var(--text-2);text-decoration:none;min-width:32px;text-align:center;transition:background 0.15s" onmouseover="this.style.background='var(--bg-2)'" onmouseout="this.style.background='transparent'">{{ $page }}</a>
          @endif
        @endforeach
      @endif
    @endforeach

    {{-- Next --}}
    @if ($paginator->hasMorePages())
      <a href="{{ $paginator->nextPageUrl() }}" style="padding:5px 10px;border:1px solid var(--border);border-radius:6px;color:var(--text-2);text-decoration:none;transition:background 0.15s" onmouseover="this.style.background='var(--bg-2)'" onmouseout="this.style.background='transparent'">&rsaquo;</a>
    @else
      <span style="padding:5px 10px;border:1px solid var(--border);border-radius:6px;color:var(--text-3);cursor:not-allowed;user-select:none">&rsaquo;</span>
    @endif

  </div>
</nav>
@endif
