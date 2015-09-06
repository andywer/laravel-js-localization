@section('js-localization.head')
    <script type="text/javascript" src="{{ url('/js-localization/localization.js') }}"></script>
    <script type="text/javascript" src="{{ url('/js-localization/messages') }}"></script>

    @if(Config::get('js-localization.config'))
        <script type="text/javascript" src="{{ url('/js-localization/config') }}"></script>
    @endif

    <script type="text/javascript">
        Lang.setLocale("{{ App::getLocale() }}");
    </script>
@stop

@section('js-localization.head.all_in_one')
    <script type="text/javascript" src="{{ url('/js-localization/all.js') }}"></script>
    <script type="text/javascript">
        Lang.setLocale("{{ App::getLocale() }}");
    </script>
@stop
