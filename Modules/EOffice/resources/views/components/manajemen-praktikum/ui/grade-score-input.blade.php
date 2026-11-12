@props(['model'=>"''",'unavailable'=>false,'wide'=>false,'label'=>'Nilai','disabled'=>false,'required'=>false,'integer'=>false])
<input {{ $attributes }} type="text" inputmode="decimal" data-grade-integer="{{ $integer ? '1' : '0' }}" class="mp-input mp-grade-score" aria-label="{{ $label }}" placeholder="0–100" autocomplete="off"
    @if($unavailable) value="—" @else
    :value="{{ $integer ? "(String($model ?? '') === '' ? '' : Number($model).toFixed(0))" : "String($model ?? '').replace('.',',')" }}"
    @input="const value=$el.value.replace(',','.'); const valid={{ $integer ? '/^[0-9]{1,3}$/' : '/^[0-9]{1,3}([.][0-9]{0,2})?$/' }}.test(value) &amp;&amp; Number(value)&gt;=0 &amp;&amp; Number(value)&lt;=100; {{ $model }}=valid?value:''; if(!valid)$el.value='';"
    @mousedown.stop
    x-effect="$el.setCustomValidity(String({{ $model }} ?? '')==='' || (/^\d{1,3}(\.\d{0,2})?$/.test(String({{ $model }} ?? '')) &amp;&amp; Number({{ $model }})&lt;=100) ? '' : 'Masukkan nilai 0–100, maksimal dua angka desimal.')"
    @endif
    @disabled($disabled || $unavailable) @required($required)
    style="box-sizing:border-box;height:{{ $wide?44:32 }}px;line-height:20px;width:{{ $wide?'100%':'64px' }};padding:4px 6px;border:1px solid #DFE1E7;border-radius:8px;font-size:{{ $wide?13:12 }}px;text-align:{{ $wide?'left':'center' }};background:white">
