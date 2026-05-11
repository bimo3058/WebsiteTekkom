{{-- profile/partials/settings-panel-notifikasi.blade.php --}}
<p class="panel-title">Push Notifications</p>
<p class="panel-sub">Get alerts for anything changes</p>

@foreach([
    ['label'=>'Transaction Confirmation','desc'=>'Sent automatically to the customer after they place their order.','on'=>true],
    ['label'=>'Transaction Edited','desc'=>'Sent to the customer after their order is edited (if you select this option).','on'=>false],
    ['label'=>'Transaction Invoice','desc'=>'Sent to the customer when the order has an outstanding balance.','on'=>true],
    ['label'=>'Transaction Cancelled','desc'=>'Sent automatically to the customer if their order is cancelled (if you select this option).','on'=>true],
    ['label'=>'Transaction Refund','desc'=>'Sent automatically to the customer if their order is refunded (if you select this option).','on'=>true],
    ['label'=>'Payment Error','desc'=>"Sent automatically to the customer if their payment can't be processed during checkout.",'on'=>false],
] as $n)
<div style="display:flex;align-items:flex-start;justify-content:space-between;padding:16px 0;border-bottom:1px solid #f1f5f9;">
    <div style="padding-right:24px;">
        <p style="font-size:13px;font-weight:600;color:#0f172a;margin-bottom:2px;">{{ $n['label'] }}</p>
        <p style="font-size:12px;color:#64748b;line-height:1.5;">{{ $n['desc'] }}</p>
    </div>
    <div class="toggle-track {{ $n['on'] ? 'on' : 'off' }}" style="flex-shrink:0;margin-top:2px;">
        <div class="toggle-thumb"></div>
    </div>
</div>
@endforeach