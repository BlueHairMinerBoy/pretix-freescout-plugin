<div class="section-heading">
    {{ __('Pretix Integration') }}
</div>
<div class="section-sub-heading">
    {{ __('Connect FreeScout to your Pretix ticketing system to display customer bookings in the conversation sidebar.') }}
</div>

<div class="form-group{{ $errors->has('settings.base_url') ? ' has-error' : '' }}">
    <label for="pretix_base_url" class="col-sm-2 control-label">
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
            {{ __('The root URL of your Pretix installation. For hosted Pretix this is') }} <code>https://pretix.eu</code>.
        </p>
        @if ($errors->has('settings.base_url'))
            <span class="help-block text-danger">{{ $errors->first('settings.base_url') }}</span>
        @endif
    </div>
</div>

<div class="form-group{{ $errors->has('settings.api_token') ? ' has-error' : '' }}">
    <label for="pretix_api_token" class="col-sm-2 control-label">
        {{ __('API Token') }}
    </label>
    <div class="col-sm-6">
        <input
            type="text"
            class="form-control"
            name="settings[api_token]"
            id="pretix_api_token"
            value="{{ old('settings.api_token', $settings['api_token'] ?? '') }}"
            placeholder="e1l6gq2ye72thbwkacj7jbri7a7tvxe614ojv8ybureain92ocub46t5gab5966k"
            autocomplete="off"
        >
        <p class="help-block">
            {!! __('Your Pretix API token. Create one in Pretix under <strong>Settings → Teams → [Team] → API Keys</strong>. The team needs "Can view orders" permission.') !!}
        </p>
        @if ($errors->has('settings.api_token'))
            <span class="help-block text-danger">{{ $errors->first('settings.api_token') }}</span>
        @endif
    </div>
</div>

<div class="form-group{{ $errors->has('settings.organizer') ? ' has-error' : '' }}">
    <label for="pretix_organizer" class="col-sm-2 control-label">
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
            {!! __('The organizer short name from your Pretix URL. For <code>https://pretix.eu/myorg/</code> the slug is <code>myorg</code>.') !!}
        </p>
        @if ($errors->has('settings.organizer'))
            <span class="help-block text-danger">{{ $errors->first('settings.organizer') }}</span>
        @endif
    </div>
</div>
