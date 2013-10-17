@section('js-localization.head')
    {{ HTML::script('/js-localization/localization.js') }}
    <script type="text/javascript" src="{{ action('JsLocalizationController@createJsMessages') }}"></script>
@stop