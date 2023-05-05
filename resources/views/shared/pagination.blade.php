@if($paginator->lastPage() > 1)

<div class="mt4 mb6">
  <div class="pagination-btn-group">
    @if($paginator->currentPage() == 1)
      <div class="pagination-btn tr">
        <a class="w-100 w-auto-ns" href="{{ $paginator->url(2) }}">แสดงหน้าถัดไป <i class="fas fa-arrow-right"></i></a>
      </div>
    @elseif($paginator->currentPage() < $paginator->lastPage())
      <div class="pagination-btn clearfix tl tr-ns">
        <a class="w-50 w-auto-ns fl fn-ns dib mr0 mr3-ns" href="{{ $paginator->url($paginator->currentPage()-1) }}"><i class="fas fa-arrow-left"></i> ก่อนหน้า</a>
        <a class="w-50 w-auto-ns fl fn-ns dib tr" href="{{ $paginator->url($paginator->currentPage()+1) }}">ถัดไป <i class="fas fa-arrow-right"></i></a>
      </div>
      <!-- <div class="clearfix dn db-ns">
        <div class="pagination-btn fr w-50 w-auto-ns tr">
          <a href="{{ $paginator->url($paginator->currentPage()+1) }}">ถัดไป <i class="fas fa-arrow-right"></i></a>
        </div>
        <div class="pagination-btn fr w-50 w-auto-ns">
          <a href="{{ $paginator->url($paginator->currentPage()-1) }}"><i class="fas fa-arrow-left"></i> ก่อนหน้า</a>
        </div>
      </div> -->
    @elseif($paginator->currentPage() == $paginator->lastPage())
      <div class="pagination-btn tl tr-ns">
        <a class="w-100 w-auto-ns" href="{{ $paginator->url($paginator->lastPage()-1) }}"><i class="fas fa-arrow-left"></i> แสดงก่อนหน้า</a>
      </div>
    @endif
  </div>
</div>

@endif