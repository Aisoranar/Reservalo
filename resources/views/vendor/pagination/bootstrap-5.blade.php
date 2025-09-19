@if ($paginator->hasPages())
    <nav aria-label="Paginación de resultados" class="mt-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
            {{-- Información de resultados --}}
            <div class="text-muted">
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-list text-primary"></i>
                    <span class="fw-medium">
                        Mostrando 
                        <span class="badge bg-primary text-white">{{ $paginator->firstItem() }}</span>
                        a 
                        <span class="badge bg-primary text-white">{{ $paginator->lastItem() }}</span>
                        de 
                        <span class="badge bg-secondary text-white">{{ $paginator->total() }}</span>
                        resultados
                    </span>
                </div>
            </div>

            {{-- Navegación de páginas --}}
            <ul class="pagination pagination-sm mb-0 shadow-sm">
                {{-- Botón Primera página --}}
                @if ($paginator->currentPage() > 3)
                    <li class="page-item">
                        <a class="page-link border-0 rounded-3 me-1" href="{{ $paginator->url(1) }}" title="Primera página">
                            <i class="fas fa-angle-double-left"></i>
                        </a>
                    </li>
                @endif

                {{-- Botón Anterior --}}
                @if ($paginator->onFirstPage())
                    <li class="page-item disabled">
                        <span class="page-link border-0 rounded-3 me-1 bg-light text-muted" title="Página anterior">
                            <i class="fas fa-chevron-left"></i>
                        </span>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link border-0 rounded-3 me-1" href="{{ $paginator->previousPageUrl() }}" rel="prev" title="Página anterior">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    </li>
                @endif

                {{-- Elementos de paginación --}}
                @foreach ($elements as $element)
                    {{-- "Three Dots" Separator --}}
                    @if (is_string($element))
                        <li class="page-item disabled">
                            <span class="page-link border-0 rounded-3 me-1 bg-light text-muted">{{ $element }}</span>
                        </li>
                    @endif

                    {{-- Array Of Links --}}
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <li class="page-item active">
                                    <span class="page-link border-0 rounded-3 me-1 bg-primary text-white fw-bold shadow-sm">{{ $page }}</span>
                                </li>
                            @else
                                <li class="page-item">
                                    <a class="page-link border-0 rounded-3 me-1" href="{{ $url }}" title="Página {{ $page }}">{{ $page }}</a>
                                </li>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                {{-- Botón Siguiente --}}
                @if ($paginator->hasMorePages())
                    <li class="page-item">
                        <a class="page-link border-0 rounded-3 me-1" href="{{ $paginator->nextPageUrl() }}" rel="next" title="Página siguiente">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    </li>
                @else
                    <li class="page-item disabled">
                        <span class="page-link border-0 rounded-3 me-1 bg-light text-muted" title="Página siguiente">
                            <i class="fas fa-chevron-right"></i>
                        </span>
                    </li>
                @endif

                {{-- Botón Última página --}}
                @if ($paginator->currentPage() < $paginator->lastPage() - 2)
                    <li class="page-item">
                        <a class="page-link border-0 rounded-3" href="{{ $paginator->url($paginator->lastPage()) }}" title="Última página">
                            <i class="fas fa-angle-double-right"></i>
                        </a>
                    </li>
                @endif
            </ul>
        </div>

        {{-- Información adicional en móviles --}}
        <div class="d-md-none mt-3 text-center">
            <div class="badge bg-light text-dark border">
                <i class="fas fa-mobile-alt me-1"></i>
                Página {{ $paginator->currentPage() }} de {{ $paginator->lastPage() }}
            </div>
        </div>
    </nav>

    {{-- Estilos adicionales --}}
    <style>
        .pagination .page-link {
            transition: all 0.3s ease;
            min-width: 40px;
            text-align: center;
        }
        
        .pagination .page-link:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }
        
        .pagination .page-item.active .page-link {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(13, 110, 253, 0.3);
        }
        
        .pagination .page-link:focus {
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
        }
        
        .pagination .page-item.disabled .page-link {
            opacity: 0.6;
        }
    </style>
@endif