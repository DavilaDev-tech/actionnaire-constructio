
    {{-- Table --}}
    <div class="table-responsive">
        <table class="table hector-table mb-0">
            <thead>
                <tr>
                    <th>N° Facture</th>
                    <th>N° Vente</th>
                    <th>Client</th>
                    <th>Date</th>
                    <th>Montant</th>
                    <th>Statut</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($factures as $facture)
                @php
                    $initiale   = strtoupper(substr($facture->vente->client->nom, 0, 1));
                    $colors     = ['#F97316','#3B82F6','#10B981','#8B5CF6','#EF4444','#F59E0B'];
                    $color      = $colors[ord($initiale) % count($colors)];
                    $montantPaye = $facture->montant_paye ?? 0;
                    $pct        = $facture->montant > 0
                        ? min(100, round(($montantPaye / $facture->montant) * 100))
                        : 0;
                @endphp
                <tr>
                    {{-- N° Facture --}}
                    <td>
                        <span class="fw-semibold"
                              style="color:#111827;font-family:monospace;font-size:0.82rem">
                            {{ $facture->numero }}
                        </span>
                    </td>

                    {{-- N° Vente --}}
                    <td style="color:#9CA3AF;font-size:0.82rem">
                        {{ $facture->vente->numero_vente }}
                    </td>

                    {{-- Client --}}
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div style="width:30px;height:30px;border-radius:50%;
                                        background:{{ $color }}20;color:{{ $color }};
                                        display:flex;align-items:center;justify-content:center;
                                        font-weight:700;font-size:0.75rem;flex-shrink:0">
                                {{ $initiale }}
                            </div>
                            <span style="color:#111827;font-weight:500">
                                {{ $facture->vente->client->nom }}
                            </span>
                        </div>
                    </td>

                    {{-- Date --}}
                    <td style="color:#9CA3AF;font-size:0.82rem">
                        {{ $facture->created_at->format('d/m/Y') }}
                    </td>

                    {{-- Montant --}}
                    <td>
                        <span class="fw-semibold" style="color:#111827">
                            {{ number_format($facture->montant, 0, ',', ' ') }}
                            <span style="color:#9CA3AF;font-weight:400;font-size:0.78rem"> F</span>
                        </span>
                    </td>

                 
                    {{-- Statut --}}
                    <td>
                        <span class="statut-badge statut-{{ $facture->statut }}">
                            {{ ucfirst(str_replace('_', ' ', $facture->statut)) }}
                        </span>
                    </td>

                    {{-- Actions --}}
                    <td>
                        <div class="d-flex gap-1 justify-content-end">
                            <a href="{{ route('factures.show', $facture) }}"
                               class="btn-action view" title="Voir">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('factures.telecharger', $facture) }}"
                               class="btn-action download" title="Télécharger PDF">
                                <i class="bi bi-download"></i>
                            </a>
                            @if($facture->statut == 'non_payee')
                            <form action="{{ route('factures.marquer-payee', $facture) }}"
                                  method="POST">
                                @csrf @method('PATCH')
                                <button type="submit"
                                        class="btn-action check"
                                        title="Marquer comme payée">
                                    <i class="bi bi-check-circle"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-5">
                        <div>
                            <i class="bi bi-receipt"
                               style="font-size:2.5rem;display:block;
                                      margin-bottom:12px;color:#D1D5DB"></i>
                            <div style="font-size:0.875rem;color:#9CA3AF">
                                Aucune facture trouvée
                            </div>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($factures->hasPages())
    <div style="border-top:1px solid #F9FAFB;padding:12px 20px">
        {{ $factures->links() }}
    </div>
    @endif

</div>