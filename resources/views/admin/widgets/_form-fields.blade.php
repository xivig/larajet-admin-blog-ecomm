@php
    $p = $prefix ?? 'add';
@endphp

<div class="row g-3 mb-3">
    <div class="col-md-6">
        <label for="{{ $p }}WidgetArea" class="form-label small fw-bold">Placement Area *</label>
        <select name="area" id="{{ $p }}WidgetArea" class="form-select rounded-3 js-widget-area" required>
            @foreach($allAreas as $aKey => $aTitle)
                <option value="{{ $aKey }}" {{ ($selectedArea ?? '') === $aKey ? 'selected' : '' }}>{{ $aTitle }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6">
        <label for="{{ $p }}WidgetType" class="form-label small fw-bold">Widget Type *</label>
        <select name="widget_type" id="{{ $p }}WidgetType" class="form-select rounded-3 js-widget-type" required>
            @foreach($widgetTypes as $wKey => $wMeta)
                <option value="{{ $wKey }}">{{ $wMeta['label'] }} - {{ $wMeta['desc'] }}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="mb-3">
    <label for="{{ $p }}WidgetTitle" class="form-label small fw-bold">Display Title *</label>
    <input type="text" name="title" id="{{ $p }}WidgetTitle" class="form-control rounded-3 js-widget-title" placeholder="e.g. Platform, Stay Ahead, Popular Articles" required>
</div>

<div class="row g-3 mb-3">
    <div class="col-md-6 js-limit-group">
        <label for="{{ $p }}WidgetLimit" class="form-label small fw-bold">Items Count Limit</label>
        <input type="number" name="limit" id="{{ $p }}WidgetLimit" value="" min="1" max="50" class="form-control rounded-3 js-widget-limit" placeholder="Auto (leave empty for default)">
        <small class="text-muted">Optional cap on items rendered in list</small>
    </div>
    <div class="col-md-6">
        <label class="form-label small fw-bold">Visibility State</label>
        <div class="form-check form-switch mt-2">
            <input class="form-check-input js-widget-status" type="checkbox" name="status" value="1" id="{{ $p }}WidgetStatus" checked>
            <label class="form-check-label small fw-semibold" for="{{ $p }}WidgetStatus">Enabled &amp; Visible</label>
        </div>
    </div>
</div>

<div class="mb-3 d-none js-menu-group">
    <label for="{{ $p }}WidgetMenuLocation" class="form-label small fw-bold">Synchronized Menu Slot</label>
    <select name="menu_location" id="{{ $p }}WidgetMenuLocation" class="form-select rounded-3 js-widget-menu-location">
        <option value="">-- Automatic / Active Pages --</option>
        @foreach($menuLocations as $mLoc => $mLabel)
            <option value="{{ $mLoc }}">{{ $mLabel }} ({{ $mLoc }})</option>
        @endforeach
    </select>
    <small class="text-muted">Links pull live from Navigation Menu Builder for this slot.</small>
</div>

<div class="row g-3 mb-3 d-none js-brand-group">
    <div class="col-md-6">
        <label for="{{ $p }}WidgetEmail" class="form-label small fw-bold">Contact Email</label>
        <input type="text" name="email" id="{{ $p }}WidgetEmail" class="form-control rounded-3 js-widget-email" placeholder="support@xivig.com">
    </div>
    <div class="col-md-6">
        <label for="{{ $p }}WidgetPhone" class="form-label small fw-bold">Contact Phone</label>
        <input type="text" name="phone" id="{{ $p }}WidgetPhone" class="form-control rounded-3 js-widget-phone" placeholder="+91 98367 26662">
    </div>
</div>

<div class="mb-3 js-content-group">
    <label for="{{ $p }}WidgetContent" class="form-label small fw-bold">Description / Lead Text</label>
    <textarea name="content" id="{{ $p }}WidgetContent" rows="3" class="form-control rounded-3 js-widget-content" placeholder="Optional descriptive copy shown inside the widget..."></textarea>
</div>

<div class="mb-3 d-none js-ad-group">
    <label for="{{ $p }}WidgetAdCode" class="form-label small fw-bold">HTML / Embed Snippet</label>
    <textarea name="ad_code" id="{{ $p }}WidgetAdCode" rows="4" class="form-control rounded-3 font-monospace small js-widget-ad-code" placeholder="<div class='...'>Custom embed or banner HTML</div>"></textarea>
</div>
