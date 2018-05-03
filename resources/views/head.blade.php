@section('js-localization.head')
    <script src="{{ url('/js-localization/localization.js') }}"></script>
    <script src="{{ url('/js-localization/messages') }}"></script>

    @if(Config::get('js-localization.config'))
        <script src="{{ url('/js-localization/config') }}"></script>
    @endif

    <script>
        Lang.setLocale("{{ App::getLocale() }}");
    </script>
@stop

@section('js-localization.head.all_in_one')
    <script src="{{ url('/js-localization/all.js') }}"></script>
    <script>
        Lang.setLocale("{{ App::getLocale() }}");
    </script>
@stop
