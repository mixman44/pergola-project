<div class="ctrl-block">

    <div class="ctrl-block-header">
        <span class="ctrl-block-title">
            Image Pergola
        </span>
        <span class="ctrl-block-chevron">▾</span>
    </div>

    <div class="ctrl-block-body">

        <label class="ctrl-file-label" for="pergolaImageUpload">
            <span class="ctrl-file-icon">↑</span>
            <span class="ctrl-file-text" id="pergolaUploadText">Choisir un fichier…</span>
            <input type="file" id="pergolaImageUpload" class="ctrl-file-input" accept="image/*"
                   onchange="document.getElementById('pergolaUploadText').textContent =
                                 this.files[0]?.name ?? 'Choisir un fichier…'">
        </label>

        {{-- Bouton Décrire --}}
        <button id="describePergolaButton" class="ctrl-btn ctrl-btn--secondary">
            Décrire la pergola
        </button>

    </div>

</div>
