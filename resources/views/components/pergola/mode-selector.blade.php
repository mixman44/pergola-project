<div class="ctrl-block is-open">
    <div class="ctrl-block-header">
        <span class="ctrl-block-title">
            Mode de génération
        </span>
        <span class="ctrl-block-chevron">▾</span>
    </div>

    <div class="ctrl-block-body">
        <div class="ctrl-select-wrap">
            <select id="modeSelect" class="ctrl-select">
                @foreach(\App\Enums\PergolaModeEnum::options() as $key => $label)
                    <option value="{{ $key }}">{{ $label }}</option>
                @endforeach
            </select>
            <span class="ctrl-select-arrow">▾</span>
        </div>
    </div>
</div>
