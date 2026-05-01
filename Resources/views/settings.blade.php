<div class="col-xs-12">
    <h3>{{ __('Pretix Integration') }}</h3>

    @include('partials/flash_messages')

    <form class="form-horizontal margin-top" method="POST" action="{{ route('settings.save', ['section' => 'pretixintegration']) }}">
        {{ csrf_field() }}

        <div class="form-group{{ $errors->has('settings.base_url') ? ' has-error' : '' }}">
            <label for="pretix_base_url" class="col-sm-3 control-label">
                {{ __('Pretix Base URL') }}
            </label>
            <div class="col-sm-6">
                <input
                    type="url"
                    class="form-control"
                    name="settings[base_url]"
                    id="pretix_base_url"
                    value="{{ old('settings.base_url', $settings['base_url'] ?? '') }}"
                    placeholder="https://pretix.eu"
                >
                <p class="help-block">
                    {{ __('Root URL of your Pretix installation, e.g.') }} <code>https://pretix.eu</code>
                </p>
                @if ($errors->has('settings.base_url'))
                    <span class="help-block text-danger">{{ $errors->first('settings.base_url') }}</span>
                @endif
            </div>
        </div>

        <div class="form-group{{ $errors->has('settings.api_token') ? ' has-error' : '' }}">
            <label for="pretix_api_token" class="col-sm-3 control-label">
                {{ __('API Token') }}
            </label>
            <div class="col-sm-6">
                <input
                    type="text"
                    class="form-control"
                    name="settings[api_token]"
                    id="pretix_api_token"
                    value="{{ old('settings.api_token', $settings['api_token'] ?? '') }}"
                    placeholder="e1l6gq2ye72thbwkacj7jbri7a7tvxe614ojv8y…"
                    autocomplete="off"
                >
                <p class="help-block">
                    {!! __('Create a token in Pretix under <strong>Settings &rsaquo; Teams &rsaquo; [Team] &rsaquo; API Keys</strong>. The team needs the &ldquo;Can view orders&rdquo; permission.') !!}
                </p>
                @if ($errors->has('settings.api_token'))
                    <span class="help-block text-danger">{{ $errors->first('settings.api_token') }}</span>
                @endif
            </div>
        </div>

        <div class="form-group{{ $errors->has('settings.organizer') ? ' has-error' : '' }}">
            <label for="pretix_organizer" class="col-sm-3 control-label">
                {{ __('Organizer Slug') }}
            </label>
            <div class="col-sm-6">
                <input
                    type="text"
                    class="form-control"
                    name="settings[organizer]"
                    id="pretix_organizer"
                    value="{{ old('settings.organizer', $settings['organizer'] ?? '') }}"
                    placeholder="myorganization"
                >
                <p class="help-block">
                    {!! __('The short name from your Pretix URL. For <code>https://pretix.eu/myorg/</code> the slug is <code>myorg</code>.') !!}
                </p>
                @if ($errors->has('settings.organizer'))
                    <span class="help-block text-danger">{{ $errors->first('settings.organizer') }}</span>
                @endif
            </div>
        </div>

        <div class="form-group margin-top">
            <div class="col-sm-6 col-sm-offset-3">
                <button type="submit" class="btn btn-primary">
                    {{ __('Save') }}
                </button>
            </div>
        </div>

    </form>
</div>
