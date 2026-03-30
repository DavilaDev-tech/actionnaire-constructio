<div class="input-group" style="max-width:350px">
    <span class="input-group-text bg-white border-end-0"
          style="border-radius:10px 0 0 10px;border-color:#e5e7eb">
        <i class="bi bi-search text-muted small" id="icone-recherche-{{ $id }}"></i>
    </span>
    <input type="text"
           id="recherche-{{ $id }}"
           class="form-control border-start-0 border-end-0"
           placeholder="{{ $placeholder ?? 'Rechercher...' }}"
           value="{{ $valeur ?? '' }}"
           autocomplete="off"
           style="border-color:#e5e7eb;font-size:0.875rem">
    <button type="button"
            id="effacer-{{ $id }}"
            class="btn bg-white border border-start-0"
            style="border-radius:0 10px 10px 0;border-color:#e5e7eb;
                   display:{{ isset($valeur) && $valeur ? 'block' : 'none' }}"
            onclick="effacerRecherche('{{ $id }}')">
        <i class="bi bi-x text-muted"></i>
    </button>
</div>