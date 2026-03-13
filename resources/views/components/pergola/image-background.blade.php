<div class="ctrl-block is-open">

    <div class="ctrl-block-header">
        <span class="ctrl-block-title">
            Image de fond
        </span>
        <span class="ctrl-block-chevron">▾</span>
    </div>

    <div class="ctrl-block-body">

        <label class="ctrl-file-label" for="imageUpload">
            <span class="ctrl-file-icon">↑</span>
            <span class="ctrl-file-text" id="imageUploadText">Choisir un fichier…</span>
            <input type="file" id="imageUpload" class="ctrl-file-input" accept="image/*"
                   onchange="document.getElementById('imageUploadText').textContent =
                             this.files[0]?.name ?? 'Choisir un fichier…'">
        </label>

        {{-- Bouton Appliquer --}}
        <button id="applyButton" class="ctrl-btn ctrl-btn--secondary">
            Appliquer le fond
        </button>

    </div>

</div>
