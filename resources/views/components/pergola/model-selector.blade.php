
<div class="ctrl-block is-open">

    <div class="ctrl-block-header">
        <span class="ctrl-block-title">
            Modèle IA
        </span>
        <span class="ctrl-block-chevron">▾</span>
    </div>

    <div class="ctrl-block-body">
        <div class="ctrl-select-wrap">
            <select id="modelSelect" class="ctrl-select">
                @foreach(\App\Enums\PergolaModelEnum::values() as $model)
                    <option value="{{ $model }}">{{ $model }}</option>
                @endforeach
            </select>
            <span class="ctrl-select-arrow">▾</span>
        </div>
    </div>
</div>

