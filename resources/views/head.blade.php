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

@section('js-localization.head.exported')
    @if (file_exists(config('js-localization.storage_path') . '/js-localization.min.js'))
    <script src="{{ url('/vendor/js-localization/js-localization.min.js') }}"></script>
    @else
    <script src="{{ url('/js-localization/localization.js') }}"></script>
    @endif
    
    @if (Config::get('js-localization.config'))
    <script src="{{ url('/vendor/js-localization/config.js') }}"></script>
    @endif

    @if (Config::get('js-localization.split_export_files') && file_exists(config('js-localization.storage_path') . '/lang-' . App::getLocale() . '.js'))
    <script src="{{ url('/vendor/js-localization/lang-' . App::getLocale() . '.js') }}"></script>
    @else
    <script src="{{ url('/vendor/js-localization/messages.js') }}"></script>
    @endif
    <script>
        Lang.setLocale("{{ App::getLocale() }}");
    </script>
@stop