@php
    // Détecter quelle variable de pagination est disponible
    $paginator = $ventes ?? $clients ?? $produits ?? $factures ?? $appros ?? null;
@endphp

@if($paginator && $paginator->hasPages())
<div class="d-flex justify-content-between align-items-center px-3 py-2">
    <small class="text-muted">
        Affichage de {{ $paginator->firstItem() }} à {{ $paginator->lastItem() }}
        sur {{ $paginator->total() }} résultats
    </small>
    <nav>
        <ul class="pagination pagination-sm mb-0">
            {{-- Précédent --}}
            <li class="page-item {{ $paginator->onFirstPage() ? 'disabled' : '' }}">
                <a class="page-link ajax-page" href="#"
                   data-page="{{ $paginator->currentPage() - 1 }}">
                    ‹
                </a>
            </li>

            {{-- Pages --}}
            @for($i = 1; $i <= $paginator->lastPage(); $i++)
            <li class="page-item {{ $paginator->currentPage() == $i ? 'active' : '' }}">
                <a class="page-link ajax-page" href="#"
                   data-page="{{ $i }}">
                    {{ $i }}
                </a>
            </li>
            @endfor

            {{-- Suivant --}}
            <li class="page-item {{ !$paginator->hasMorePages() ? 'disabled' : '' }}">
                <a class="page-link ajax-page" href="#"
                   data-page="{{ $paginator->currentPage() + 1 }}">
                    ›
                </a>
            </li>
        </ul>
    </nav>
</div>
@endif