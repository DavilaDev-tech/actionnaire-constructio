<table class="table hector-table mb-0">
    <thead>
        <tr>
            <th>N° Vente</th>
            <th>Client</th>
            <th>Adresse</th>
            <th>Date livraison</th>
            <th>Statut</th>
            <th class="text-end">Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse($livraisons as $livraison)
        @php
            $initiale = strtoupper(substr($livraison->client->nom, 0, 1));
            $colors   = ['#F97316','#3B82F6','#10B981','#8B5CF6','#EF4444','#F59E0B'];
            $color    = $colors[ord($initiale) % count($colors)];
        @endphp
        <tr>
            {{-- N° Vente --}}
            <td>
                <span class="fw-semibold"
                      style="color:#111827;font-family:monospace;font-size:0.82rem">
                    {{ $livraison->vente->numero_vente }}
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
                        {{ $livraison->client->nom }}
                    </span>
                </div>
            </td>

            {{-- Adresse --}}
            <td>
                <div style="color:#374151;font-size:0.82rem">
                    <i class="bi bi-geo-alt me-1" style="color:#9CA3AF"></i>
                    {{ Str::limit($livraison->adresse_livraison, 30) }}
                </div>
            </td>

            {{-- Date --}}
            <td style="color:#9CA3AF;font-size:0.82rem">
                @if($livraison->date_livraison)
                    <i class="bi bi-calendar3 me-1"></i>
                    {{ $livraison->date_livraison->format('d/m/Y') }}
                @else
                    <span style="color:#D1D5DB">—</span>
                @endif
            </td>

            {{-- Statut --}}
            <td>
                <span class="statut-badge statut-{{ $livraison->statut }}">
                    @if($livraison->statut == 'en_attente')
                        ⏳ En attente
                    @elseif($livraison->statut == 'en_cours')
                        🚚 En cours
                    @else
                        ✓ Livrée
                    @endif
                </span>
            </td>

            {{-- Actions --}}
            <td>
                <div class="d-flex gap-1 justify-content-end align-items-center">
                    <a href="{{ route('livraisons.show', $livraison) }}"
                       class="btn-action view" title="Voir">
                        <i class="bi bi-eye"></i>
                    </a>

                    @if(!$livraison->isLivree())
                    <a href="{{ route('livraisons.edit', $livraison) }}"
                       class="btn-action edit" title="Modifier">
                        <i class="bi bi-pencil"></i>
                    </a>
                    <form action="{{ route('livraisons.statut', $livraison) }}"
                          method="POST">
                        @csrf @method('PATCH')
                        <select name="statut"
                                class="select-statut"
                                onchange="this.form.submit()"
                                title="Changer le statut">
                            <option value="en_attente"
                                {{ $livraison->statut=='en_attente' ? 'selected':'' }}>
                                En attente
                            </option>
                            <option value="en_cours"
                                {{ $livraison->statut=='en_cours' ? 'selected':'' }}>
                                En cours
                            </option>
                            <option value="livree">Livrée ✓</option>
                        </select>
                    </form>
                    @endif
                </div>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="6" class="text-center py-5">
                <div>
                    <i class="bi bi-truck"
                       style="font-size:2.5rem;display:block;
                              margin-bottom:12px;color:#D1D5DB"></i>
                    <div style="font-size:0.875rem;color:#9CA3AF">
                        Aucune livraison trouvée
                    </div>
                </div>
            </td>
        </tr>
        @endforelse
    </tbody>
</table>