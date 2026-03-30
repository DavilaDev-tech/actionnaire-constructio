<table class="table hector-table mb-0">
    <thead>
        <tr>
            <th>N° Vente</th>
            <th>Client</th>
            <th>Date</th>
            <th>Montant</th>
            <th>Statut</th>
            <th>Vendeur</th>
            <th class="text-end">Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse($ventes as $vente)
        @php
            $initiale = strtoupper(substr($vente->client->nom, 0, 1));
            $colors   = ['#F97316','#3B82F6','#10B981','#8B5CF6','#EF4444','#F59E0B'];
            $color    = $colors[ord($initiale) % count($colors)];
        @endphp
        <tr>
            {{-- N° Vente --}}
            <td>
                <span class="fw-semibold" style="color:#111827;font-family:monospace;
                             font-size:0.82rem">
                    {{ $vente->numero_vente }}
                </span>
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
                        {{ $vente->client->nom }}
                    </span>
                </div>
            </td>

            {{-- Date --}}
            <td style="color:#9CA3AF;font-size:0.82rem">
                {{ $vente->date_vente->format('d/m/Y') }}
            </td>

            {{-- Montant --}}
            <td>
                <span class="fw-semibold" style="color:#111827">
                    {{ number_format($vente->montant_total, 0, ',', ' ') }}
                    <span style="color:#9CA3AF;font-weight:400;font-size:0.78rem"> F</span>
                </span>
            </td>

            {{-- Statut --}}
            <td>
                <span class="statut-badge statut-{{ $vente->statut }}">
                    {{ ucfirst(str_replace('_', ' ', $vente->statut)) }}
                </span>
            </td>

            {{-- Vendeur --}}
            <td style="color:#9CA3AF;font-size:0.82rem">
                {{ $vente->user->prenom ?? '—' }}
            </td>

            {{-- Actions --}}
            <td>
                <div class="d-flex gap-1 justify-content-end align-items-center">
                    <a href="{{ route('ventes.show', $vente) }}"
                       class="btn-action view" title="Voir">
                        <i class="bi bi-eye"></i>
                    </a>

                    @if(!in_array($vente->statut, ['livree', 'annulee']))
                    <form action="{{ route('ventes.statut', $vente) }}" method="POST">
                        @csrf @method('PATCH')
                        <select name="statut"
                                class="select-statut"
                                onchange="this.form.submit()"
                                title="Changer le statut">
                            <option value="en_attente"
                                {{ $vente->statut=='en_attente' ? 'selected':'' }}>
                                En attente
                            </option>
                            <option value="confirmee"
                                {{ $vente->statut=='confirmee' ? 'selected':'' }}>
                                Confirmée
                            </option>
                            <option value="livree">Livrée</option>
                            <option value="annulee">Annulée</option>
                        </select>
                    </form>
                    @endif

                    @if($vente->statut == 'annulee')
                    <form action="{{ route('ventes.destroy', $vente) }}"
                          method="POST"
                          onsubmit="return confirm('Supprimer cette vente ?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn-action del" title="Supprimer">
                            <i class="bi bi-trash"></i>
                        </button>
                    </form>
                    @endif
                </div>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="7" class="text-center py-5">
                <div style="color:#D1D5DB">
                    <i class="bi bi-cart" style="font-size:2.5rem;display:block;margin-bottom:12px"></i>
                    <div style="font-size:0.875rem;color:#9CA3AF">Aucune vente trouvée</div>
                </div>
            </td>
        </tr>
        @endforelse
    </tbody>
</table>