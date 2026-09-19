<div class="row g-4">
    @forelse($listings as $item)
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 card-hover border-0 shadow-sm overflow-hidden">
                <!-- Top Badge & Type -->
                <div class="position-relative bg-light text-center py-4 border-bottom">
                    @if($item->image_path)
                        <img src="{{ asset('storage/' . $item->image_path) }}" 
                             alt="{{ $item->prod_service_name }}" 
                             loading="lazy" 
                             class="img-fluid" 
                             style="max-height: 180px; object-fit: contain;">
                    @else
                        <div class="py-4 text-muted">
                            <i class="bi {{ $item->item_type === 'service' ? 'bi-tools' : 'bi-box' }} display-4 text-secondary opacity-50"></i>
                        </div>
                    @endif

                    <div class="position-absolute top-0 start-0 m-3">
                        <span class="badge {{ $item->item_type === 'service' ? 'badge-type-service' : 'badge-type-product' }} px-2 py-1 rounded-pill">
                            <i class="bi {{ $item->item_type === 'service' ? 'bi-wrench-adjustable' : 'bi-box-seam' }} me-1"></i>
                            {{ ucfirst($item->item_type) }}
                        </span>
                    </div>

                    <div class="position-absolute top-0 end-0 m-3">
                        <span class="badge bg-white text-dark shadow-sm border px-2 py-1 rounded-pill small">
                            {{ $item->category }}
                        </span>
                    </div>
                </div>

                <div class="card-body d-flex flex-column">
                    <h5 class="card-title fw-bold text-dark mb-1 text-truncate" title="{{ $item->prod_service_name }}">
                        {{ $item->prod_service_name }}
                    </h5>
                    
                    <div class="small text-muted mb-2 d-flex align-items-center gap-1">
                        <i class="bi bi-shop text-primary"></i>
                        <span class="text-truncate">{{ $item->seller->seller_name }}</span>
                        <span>•</span>
                        <i class="bi bi-geo-alt text-danger"></i>
                        <span>{{ $item->seller->city }}</span>
                    </div>

                    <p class="card-text text-muted small flex-grow-1" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                        {{ $item->description }}
                    </p>

                    <div class="pt-3 border-top mt-auto">
                        <div class="d-flex justify-content-between align-items-baseline mb-2">
                            <div>
                                <small class="text-muted d-block" style="font-size: 0.75rem;">Listed Price</small>
                                <span class="fs-5 fw-bold text-primary">₹{{ number_format($item->listed_price, 2) }}</span>
                            </div>
                            <div class="text-end">
                                <small class="text-muted d-block" style="font-size: 0.75rem;">Negotiable Up To</small>
                                <span class="badge bg-light text-dark border">
                                    ₹{{ number_format($item->maximum_rate, 2) }}
                                </span>
                            </div>
                        </div>

                        <!-- Fulfillment info pill -->
                        <div class="p-2 rounded bg-light border small text-muted mb-3 d-flex align-items-center gap-2">
                            <i class="bi {{ $item->item_type === 'service' ? 'bi-person-walking' : 'bi-box-arrow-in-down' }} text-success"></i>
                            <span>
                                @if($item->item_type === 'service')
                                    Available in: <strong>{{ $item->seller->cities->pluck('city_name')->implode(', ') ?: $item->seller->city }}</strong>
                                @else
                                    Fulfillment: <strong>Customer Collection from Seller ({{ $item->seller->city }})</strong>
                                @endif
                            </span>
                        </div>

                        <a href="{{ route('marketplace.show', $item->prod_service_id_pk) }}" class="btn btn-primary w-100 btn-sm">
                            <i class="bi bi-eye me-1"></i> View Details & Negotiate
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12 py-5 text-center">
            <div class="p-5 bg-white rounded shadow-sm border">
                <i class="bi bi-search display-3 text-muted opacity-50 mb-3 d-block"></i>
                <h4 class="fw-bold text-secondary">No listings matched your criteria</h4>
                <p class="text-muted mb-3">Try adjusting your search terms, changing the city filter, or broadening the price range.</p>
                <a href="{{ route('marketplace.index') }}" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-arrow-counterclockwise me-1"></i> Clear Filters
                </a>
            </div>
        </div>
    @endforelse
</div>

<div class="d-flex justify-content-center mt-4">
    {{ $listings->links('pagination::bootstrap-5') }}
</div>
