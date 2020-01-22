@if ($count = $campaign->deliverables->where('platform', $platform)->where('type', $type)->sum('quantity'))
<span class="bg-light p-2 m-1 rounded">{{ $count }}x
    {{ Html::image('img/icon-'.strtolower($platform).'.png', $platform, ['class' => 'img-fluid icon-sm']) }} {{ $type }}
</span>
@endif