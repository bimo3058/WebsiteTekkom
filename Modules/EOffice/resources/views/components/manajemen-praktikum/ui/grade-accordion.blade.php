
@props(['separator'=>false,'state'=>'expanded'])
@once
<style>
    .mp-grade-card-header:hover{background:#F9FAFB!important}
    .mp-grade-row:hover td.mp-grade-tint{background:var(--mp-grade-hover,#E0E7FF)!important}
    .mp-grade-row:hover td.mp-grade-predicate{filter:brightness(.95)}
    .mp-grade-tint,.mp-grade-predicate{transition:background-color .2s ease,filter .2s ease}
    .mp-ta-revision-cell.mp-bulk-grade-cell{cursor:cell;user-select:none}
    .mp-ta-revision-cell.mp-bulk-grade-cell.is-selected{box-shadow:inset 0 0 0 2px var(--selection-color,#0B266E);background:var(--selection-background)!important}
    .mp-ta-revision-cell.is-selected::after{content:"";position:absolute;inset:3px;border:1px solid var(--selection-color,#0B266E);border-radius:5px;pointer-events:none}
    .mp-ta-revision-cell.col-tugas{--selection-color:#4338CA;--selection-background:#C7D2FE}
    .mp-ta-revision-cell.col-prak{--selection-color:#15803D;--selection-background:#BBF7D0}
    .mp-ta-revision-cell.col-lap{--selection-color:#A16207;--selection-background:#FEF08A}
    .mp-ta-revision-cell.col-resp{--selection-color:#C2410C;--selection-background:#FED7AA}
    .mp-final-score-cell{cursor:cell;user-select:none}
    .mp-grade-row .mp-final-score-cell.is-selected{background:#FEF08A!important;box-shadow:inset 0 0 0 2px #A16207}
    .mp-final-score-cell.is-selected::after{content:'';position:absolute;inset:3px;border:1px solid #A16207;border-radius:5px;pointer-events:none}
    .mp-grade-disclosure { display:grid;grid-template-columns:minmax(0,1fr);grid-template-rows:0fr;min-width:0;max-width:100%;opacity:0;transition:grid-template-rows .24s ease,opacity .2s ease; }
    .mp-grade-disclosure.is-open { grid-template-rows:1fr;opacity:1; }
    .mp-grade-disclosure.has-separator.is-open { border-top:1px solid #DFE1E7; }
    .mp-grade-disclosure > div { min-height:0;min-width:0;max-width:100%; }
    @media(prefers-reduced-motion:reduce) { .mp-grade-disclosure { transition:none; } }
</style>
@endonce
<div class="mp-grade-disclosure{{ $separator ? ' has-separator' : '' }}" :class="{'is-open':{{ $state }}}" :inert="!{{ $state }}" :aria-hidden="!{{ $state }}"
    x-data="{settled:false,animationTimer:null}"
    x-init="settled={{ $state }};$watch('{{ $state }}',value=>{settled=false;clearTimeout(animationTimer);animationTimer=setTimeout(()=>settled=value,240)})">
    <div :style="settled?'overflow:visible':'overflow:hidden'">{{ $slot }}</div>
</div>
