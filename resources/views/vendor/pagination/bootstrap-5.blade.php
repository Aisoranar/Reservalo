@if ($paginator->hasPages())
    <nav aria-label="Paginación de resultados">
        <div class="d-flex justify-content-between align-items-center">
            {{-- Información de resultados --}}
            <div class="text-muted">
                <small>
                    <i class="fas fa-list me-1"></i>
                    Mostrando 
                    <span class="fw-bold text-primary">{{ $paginator->firstItem() }}</span>
                    a 
                    <span class="fw-bold text-primary">{{ $paginator->lastItem() }}</span>
                    de 
                    <span class="fw-bold text-primary">{{ $paginator->total() }}</span>
                    resultados
                </small>
            </div>

            {{-- Navegación de páginas --}}
            <ul class="pagination pagination-sm mb-0">
                {{-- Botón Primera página --}}
                @if ($paginator->currentPage() > 3)
                    <li class="page-item">
                        <a class="page-link" href="{{ $paginator->url(1) }}" title="Primera página">
                            <i class="fas fa-angle-double-left"></i>
                        </a>
                    </li>
                @endif

                {{-- Botón Anterior --}}
                @if ($paginator->onFirstPage())
                    <li class="page-item disabled">
                        <span class="page-link" title="Página anterior">
                            <i class="fas fa-chevron-left"></i>
                        </span>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev" title="Página anterior">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    </li>
                @endif

                {{-- Elementos de paginación --}}
                @foreach ($elements as $element)
                    {{-- "Three Dots" Separator --}}
                    @if (is_string($element))
                        <li class="page-item disabled">
                            <span class="page-link">{{ $element }}</span>
                        </li>
                    @endif

                    {{-- Array Of Links --}}
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <li class="page-item active">
                                    <span class="page-link bg-primary border-primary fw-bold">{{ $page }}</span>
                                </li>
                            @else
                                <li class="page-item">
                                    <a class="page-link" href="{{ $url }}" title="Página {{ $page }}">{{ $page }}</a>
                                </li>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                {{-- Botón Siguiente --}}
                @if ($paginator->hasMorePages())
                    <li class="page-item">
                        <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next" title="Página siguiente">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    </li>
                @else
                    <li class="page-item disabled">
                        <span class="page-link" title="Página siguiente">
                            <i class="fas fa-chevron-right"></i>
                        </span>
                    </li>
                @endif

                {{-- Botón Última página --}}
                @if ($paginator->currentPage() < $paginator->lastPage() - 2)
                    <li class="page-item">
                        <a class="page-link" href="{{ $paginator->url($paginator->lastPage()) }}" title="Última página">
                            <i class="fas fa-angle-double-right"></i>
                        </a>
                    </li>
                @endif
            </ul>
        </div>

        {{-- Información adicional en móviles --}}
        <div class="d-md-none mt-2 text-center">
            <small class="text-muted">
                <i class="fas fa-mobile-alt me-1"></i>
                Página {{ $paginator->currentPage() }} de {{ $paginator->lastPage() }}
            </small>
        </div>
    </nav>
@endif