<div class="row">
    <div class="col-5">
        {{ ucfirst($label) }}
    </div>
    <div class="col-7">
        @if($label == 'email')
        <a href="mailto:{{ $data }}">{{ $data }}</a>
        @elseif ($label == 'youtube')
        <a href="https://www.youtube.com/channel/{{ $data }}">{{ $data }}</a>
        @elseif ($label == 'facebook')
        <a href="https://www.facebook.com/{{ $data }}">{{ $data }}</a>
        @elseif ($label == 'twitter')
        <a href="https://twitter.com/{{ $data }}">{{ $data }}</a>
        @elseif ($label == 'tiktok')
        <a href="https://www.tiktok.com/{{ '@'.$data }}">{{ $data }}</a>
        @elseif ($label == 'instagram')
        <a href="https://www.instagram.com/{{ $data }}">{{ $data }}</a>
        @elseif ($label == 'twitchtv')
        <a href="https://www.twitch.tv/{{ $data }}">{{ $data }}</a>
        @else
        {{ $data }}
        @endif
    </div>
</div>