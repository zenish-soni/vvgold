<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="{{route('dashboard')}}">Home</a>
        </li>
        @if(!empty($typeURL) && !empty($type))
        <li class="breadcrumb-item">
            <a href="{{$typeURL}}">
                {{$type}}
            </a>
        </li>
        @endif
        <li class="breadcrumb-item active">{{$title}}</li>
    </ol>
</nav>