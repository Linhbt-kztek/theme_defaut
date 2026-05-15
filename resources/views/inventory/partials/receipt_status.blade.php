@if($status === 'completed')
    <span class="badge bg-success status-badge">Hoàn tất</span>
@elseif($status === 'cancelled')
    <span class="badge bg-danger status-badge">Đã hủy</span>
@else
    <span class="badge bg-secondary status-badge">Nháp</span>
@endif
