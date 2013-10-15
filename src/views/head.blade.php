@section('js-localization.head')
    {{ HTML::script('packages/andywer/js-localization/js/localization.js') }}
    <script type="text/javascript" src="{{ action('JsLocalizationController@createJsMessages') }}"></script>
@stop