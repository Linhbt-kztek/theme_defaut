@php($value = $status ?? 0)
@if((string) $value === '1')
    <span class="badge bg-success status-badge">Hoạt động</span>
@else
    <span class="badge bg-secondary status-badge">Ngừng</span>
@endif
