@extends('layouts.app')
 
@section('content')

<style>
/* CPANEL-SAFE LANDING DASHBOARD: scoped here so replacing this Blade file is enough. */
.landing-dashboard,
.landing-dashboard * { box-sizing: border-box; }
.landing-dashboard { width:min(100% - 2rem,1240px)!important; margin:1.5rem auto 2rem!important; color:#172033!important; }
.landing-dashboard svg { display:block!important; width:1.15rem!important; height:1.15rem!important; min-width:1.15rem!important; min-height:1.15rem!important; max-width:1.15rem!important; max-height:1.15rem!important; fill:none!important; stroke:currentColor!important; }
.landing-dashboard__intro { display:flex!important; align-items:flex-end!important; justify-content:space-between!important; gap:1rem!important; margin:0 0 1rem!important; padding:.2rem .15rem!important; }
.landing-dashboard__eyebrow,.landing-widget__kicker { display:block!important; margin:0 0 .25rem!important; color:#1d4ed8!important; font-size:.72rem!important; font-weight:800!important; letter-spacing:.1em!important; text-transform:uppercase!important; }
.landing-dashboard__intro h2 { margin:0!important; font-size:clamp(1.35rem,2vw,1.8rem)!important; line-height:1.2!important; color:#111827!important; }
.landing-dashboard__intro p { margin:.35rem 0 0!important; color:#64748b!important; font-size:.92rem!important; }
.landing-widget-grid { display:grid!important; grid-template-columns:minmax(0,1.6fr) minmax(300px,.9fr)!important; gap:1rem!important; align-items:stretch!important; }
.landing-dashboard section.landing-widget,.landing-widget { margin:0!important; padding:1.1rem!important; border:1px solid #dbe3ee!important; border-radius:1rem!important; background:#fff!important; box-shadow:0 8px 24px rgba(15,23,42,.06)!important; overflow:hidden!important; }
.landing-widget--notices,.landing-widget--downloads { min-height:340px!important; }
.landing-widget--bmi { margin-top:1rem!important; }
.landing-widget__header,.landing-bmi-header { display:flex!important; align-items:flex-start!important; justify-content:space-between!important; gap:1rem!important; margin:0!important; }
.landing-widget__heading { display:flex!important; align-items:center!important; gap:.75rem!important; min-width:0!important; }
.landing-widget__heading h3 { margin:0!important; color:#111827!important; font-size:1.15rem!important; line-height:1.25!important; }
.landing-widget__icon,.landing-dashboard .notice-accordion__icon,.landing-dashboard .download-card__icon { width:2.5rem!important; height:2.5rem!important; min-width:2.5rem!important; min-height:2.5rem!important; max-width:2.5rem!important; max-height:2.5rem!important; border-radius:.75rem!important; display:inline-flex!important; align-items:center!important; justify-content:center!important; flex:0 0 2.5rem!important; }
.landing-widget__icon svg,.landing-dashboard .notice-accordion__icon svg,.landing-dashboard .download-card__icon svg { width:1.2rem!important; height:1.2rem!important; min-width:1.2rem!important; min-height:1.2rem!important; max-width:1.2rem!important; max-height:1.2rem!important; }
.landing-widget__icon--blue { background:#eaf2ff!important; color:#1d4ed8!important; }
.landing-widget__icon--red { background:#fff0f1!important; color:#dc2626!important; }
.landing-widget__icon--green { background:#eafaf4!important; color:#0f766e!important; }
.landing-widget__count { width:2rem!important; height:2rem!important; min-width:2rem!important; border-radius:999px!important; display:inline-flex!important; align-items:center!important; justify-content:center!important; background:#eef4ff!important; color:#1d4ed8!important; font-size:.78rem!important; font-weight:800!important; }
.landing-widget__description { margin:.65rem 0 .9rem!important; color:#64748b!important; font-size:.86rem!important; }
.landing-dashboard .notice-accordion { display:grid!important; gap:.65rem!important; }
.landing-dashboard .notice-accordion__item { margin:0!important; border:1px solid #dbe3ee!important; border-radius:.85rem!important; background:#fff!important; overflow:hidden!important; }
.landing-dashboard .notice-accordion__trigger { width:100%!important; min-height:74px!important; display:grid!important; grid-template-columns:2.5rem minmax(0,1fr) 1.5rem!important; align-items:center!important; gap:.75rem!important; margin:0!important; padding:.75rem!important; border:0!important; border-radius:0!important; background:#fff!important; color:#172033!important; text-align:left!important; cursor:pointer!important; box-shadow:none!important; }
.landing-dashboard .notice-accordion__trigger:hover,.landing-dashboard .notice-accordion__trigger[aria-expanded=true] { background:#f8fbff!important; }
.landing-dashboard .notice-accordion__summary { display:block!important; min-width:0!important; }
.landing-dashboard .notice-accordion__summary strong { display:block!important; margin:0 0 .38rem!important; color:#111827!important; font-size:.91rem!important; line-height:1.3!important; }
.landing-dashboard .notice-accordion__meta { display:flex!important; flex-wrap:wrap!important; align-items:center!important; gap:.35rem .8rem!important; color:#526277!important; font-size:.75rem!important; }
.landing-dashboard .notice-accordion__meta span { display:inline-flex!important; align-items:center!important; gap:.3rem!important; max-width:100%!important; }
.landing-dashboard .notice-accordion__meta svg { width:.9rem!important; height:.9rem!important; min-width:.9rem!important; min-height:.9rem!important; max-width:.9rem!important; max-height:.9rem!important; stroke-width:1.8!important; }
.landing-dashboard .notice-accordion__chevron { display:flex!important; align-items:center!important; justify-content:center!important; color:#64748b!important; }
.landing-dashboard .notice-accordion__chevron svg { width:1rem!important; height:1rem!important; min-width:1rem!important; min-height:1rem!important; max-width:1rem!important; max-height:1rem!important; transition:transform .2s ease!important; }
.landing-dashboard .notice-accordion__trigger[aria-expanded=true] .notice-accordion__chevron svg { transform:rotate(180deg)!important; }
.landing-dashboard .notice-accordion__panel[hidden] { display:none!important; }
.landing-dashboard .notice-accordion__content { padding:.15rem .9rem .95rem 4rem!important; background:#f8fbff!important; color:#475569!important; font-size:.85rem!important; }
.landing-dashboard .notice-detail-grid { display:grid!important; grid-template-columns:repeat(3,minmax(0,1fr))!important; gap:.6rem!important; margin:.75rem 0!important; }
.landing-dashboard .notice-detail-grid div { padding:.6rem!important; border:1px solid #e2e8f0!important; border-radius:.65rem!important; background:#fff!important; }
.landing-dashboard .notice-detail-grid dt { color:#64748b!important; font-size:.68rem!important; text-transform:uppercase!important; letter-spacing:.06em!important; }
.landing-dashboard .notice-detail-grid dd { margin:.18rem 0 0!important; color:#172033!important; font-size:.82rem!important; }
.landing-dashboard .download-card-grid { display:grid!important; gap:.65rem!important; }
.landing-dashboard .download-card { display:grid!important; grid-template-columns:2.5rem minmax(0,1fr)!important; gap:.7rem!important; align-items:start!important; margin:0!important; padding:.75rem!important; border:1px solid #dbe3ee!important; border-radius:.85rem!important; background:#fff!important; box-shadow:none!important; }
.landing-dashboard .download-card__body { min-width:0!important; }
.landing-dashboard .download-card__topline { display:flex!important; flex-wrap:wrap!important; gap:.4rem!important; align-items:center!important; color:#64748b!important; font-size:.7rem!important; }
.landing-dashboard .download-card__format { background:#eef2f7!important; color:#334155!important; padding:.12rem .34rem!important; border-radius:.3rem!important; font-weight:800!important; }
.landing-dashboard .download-card h4 { margin:.35rem 0 .15rem!important; color:#111827!important; font-size:.91rem!important; line-height:1.3!important; }
.landing-dashboard .download-card p { margin:0!important; color:#64748b!important; font-size:.78rem!important; }
.landing-dashboard .download-card__action { grid-column:1/-1!important; width:100%!important; display:flex!important; align-items:center!important; justify-content:space-between!important; margin-top:.1rem!important; padding:.55rem .65rem!important; border-radius:.6rem!important; background:#edf4ff!important; color:#1d4ed8!important; font-size:.78rem!important; font-weight:800!important; text-decoration:none!important; }
.landing-dashboard .download-card__action svg { width:.95rem!important; height:.95rem!important; min-width:.95rem!important; min-height:.95rem!important; max-width:.95rem!important; max-height:.95rem!important; }
.landing-bmi-header p { max-width:520px!important; margin:.1rem 0 0!important; color:#64748b!important; font-size:.86rem!important; text-align:right!important; }
.bmi-dashboard-layout { display:grid!important; grid-template-columns:minmax(260px,.8fr) minmax(0,1.45fr)!important; gap:1rem!important; margin-top:1rem!important; align-items:stretch!important; }
.bmi-guidance-card { padding:1rem!important; border:1px solid #d8eee8!important; border-radius:.85rem!important; background:#f3fbf8!important; }
.bmi-guidance-card>strong { color:#0f766e!important; }
.bmi-guidance-card p { margin:.35rem 0 .8rem!important; color:#526277!important; font-size:.82rem!important; }
.bmi-range-list { display:grid!important; grid-template-columns:repeat(2,minmax(0,1fr))!important; gap:.5rem!important; }
.bmi-range-list span { display:block!important; padding:.55rem!important; border-radius:.6rem!important; background:#fff!important; color:#475569!important; font-size:.74rem!important; }
.bmi-range-list b { display:block!important; color:#0f766e!important; }
.landing-dashboard .bmi-modern-card { padding:1rem!important; border:1px solid #dbe3ee!important; border-radius:.85rem!important; background:#f8fafc!important; }
.landing-dashboard .bmi-modern-grid { display:grid!important; grid-template-columns:repeat(2,minmax(0,1fr))!important; gap:.7rem!important; }
.landing-dashboard .bmi-field { display:grid!important; gap:.28rem!important; }
.landing-dashboard .bmi-field label { color:#334155!important; font-size:.76rem!important; font-weight:700!important; }
.landing-dashboard .bmi-field input,.landing-dashboard .bmi-field select { width:100%!important; min-width:0!important; height:42px!important; margin:0!important; padding:.55rem .65rem!important; border:1px solid #ccd6e3!important; border-radius:.6rem!important; background:#fff!important; color:#172033!important; font:inherit!important; box-shadow:none!important; }
.landing-dashboard .bmi-modern-submit { width:100%!important; min-height:43px!important; margin:.75rem 0 0!important; padding:.65rem 1rem!important; border:0!important; border-radius:.62rem!important; background:#1d4ed8!important; color:#fff!important; font-weight:800!important; cursor:pointer!important; }
.landing-dashboard .bmi-modern-result { margin-top:.7rem!important; display:grid!important; grid-template-columns:1fr auto auto!important; gap:.6rem!important; align-items:center!important; padding:.65rem .75rem!important; border-radius:.65rem!important; background:#edf4ff!important; color:#1e3a8a!important; }
.landing-dashboard .bmi-modern-result[hidden] { display:none!important; }
.landing-empty-state { padding:1rem!important; border:1px dashed #cbd5e1!important; border-radius:.75rem!important; color:#64748b!important; }
@media (max-width:960px){.landing-widget-grid{grid-template-columns:1fr!important}.landing-widget--notices,.landing-widget--downloads{min-height:0!important}.bmi-dashboard-layout{grid-template-columns:1fr!important}.landing-bmi-header{display:block!important}.landing-bmi-header p{text-align:left!important;margin-top:.55rem!important}}
@media (max-width:640px){.landing-dashboard{width:min(100% - 1rem,1240px)!important;margin:1rem auto!important}.landing-dashboard__intro{display:block!important}.landing-dashboard section.landing-widget,.landing-widget{padding:.85rem!important;border-radius:.85rem!important}.landing-dashboard .notice-accordion__trigger{grid-template-columns:2.25rem minmax(0,1fr) 1.2rem!important;padding:.65rem!important}.landing-dashboard .notice-accordion__icon{width:2.25rem!important;height:2.25rem!important;min-width:2.25rem!important;min-height:2.25rem!important;max-width:2.25rem!important;max-height:2.25rem!important}.landing-dashboard .notice-accordion__content{padding:.15rem .7rem .8rem!important}.landing-dashboard .notice-detail-grid{grid-template-columns:1fr!important}.landing-dashboard .bmi-modern-grid,.bmi-range-list{grid-template-columns:1fr!important}.landing-dashboard .bmi-modern-result{grid-template-columns:1fr auto!important}.landing-dashboard .bmi-modern-result__category{grid-column:1/-1!important}}

/* Strong landing-page overrides for the bulletin poster and download list. */
.landing-dashboard .download-card-grid {
    display:grid!important;
    grid-template-columns:1fr!important;
    gap:.7rem!important;
}
.landing-dashboard .download-card {
    display:grid!important;
    grid-template-columns:2.7rem minmax(0,1fr) auto!important;
    align-items:center!important;
    gap:.8rem!important;
    width:100%!important;
    padding:.8rem!important;
}
.landing-dashboard .download-card__body {
    min-width:0!important;
    overflow:hidden!important;
}
.landing-dashboard .download-card h4 {
    margin:.28rem 0 .15rem!important;
    font-size:.92rem!important;
    line-height:1.35!important;
    overflow-wrap:anywhere!important;
    word-break:normal!important;
}
.landing-dashboard .download-card__action {
    grid-column:auto!important;
    width:auto!important;
    min-width:108px!important;
    margin:0!important;
    justify-content:center!important;
    gap:.4rem!important;
    white-space:nowrap!important;
}
.landing-dashboard .download-card__action.is-disabled {
    color:#94a3b8!important;
    background:#f1f5f9!important;
    cursor:not-allowed!important;
}
.landing-dashboard .bulletin-actions {
    display:flex!important;
    flex-wrap:wrap!important;
    gap:.55rem!important;
    margin-top:.8rem!important;
}
.landing-dashboard .landing-action-button {
    min-height:40px!important;
    display:inline-flex!important;
    align-items:center!important;
    justify-content:center!important;
    padding:.58rem .85rem!important;
    border:0!important;
    border-radius:.62rem!important;
    background:#1d4ed8!important;
    color:#fff!important;
    font:inherit!important;
    font-size:.8rem!important;
    font-weight:800!important;
    cursor:pointer!important;
}
@media (max-width:640px) {
    .landing-dashboard .download-card {
        grid-template-columns:2.5rem minmax(0,1fr)!important;
    }
    .landing-dashboard .download-card__action {
        grid-column:1/-1!important;
        width:100%!important;
    }
}

</style>
<section class="landing-hero">
    <div class="landing-hero-slider" id="landing-hero-slider" aria-roledescription="carousel" aria-label="eKesihatan highlights" aria-live="polite">
        <div class="landing-hero-slider__track">
            <figure class="landing-hero-slider__slide">
                <img src="{{ asset('images/intern.jpg') }}" alt="Interns at Unit Kesihatan UiTM">
            </figure>
            <figure class="landing-hero-slider__slide">
                <img src="{{ asset('images/inside.jpg') }}" alt="Inside the clinic reception area">
            </figure>
            <figure class="landing-hero-slider__slide">
                <img src="{{ asset('images/1000langkah.jpg') }}" alt="1000 langkah healthy activity event">
            </figure>
            <figure class="landing-hero-slider__slide">
                <img src="{{ asset('images/santuniKomuniti.jpg') }}" alt="Santuni komuniti health outreach session">
            </figure>
        </div>
        <button type="button" class="landing-hero-slider__nav landing-hero-slider__nav--prev" aria-label="Previous slide">
            <span aria-hidden="true">&#10094;</span>
        </button>
        <button type="button" class="landing-hero-slider__nav landing-hero-slider__nav--next" aria-label="Next slide">
            <span aria-hidden="true">&#10095;</span>
        </button>
        <div class="landing-hero-slider__indicators">
            <button type="button" class="landing-hero-slider__dot is-active" data-slide-index="0" aria-label="Go to slide 1" aria-current="true"></button>
            <button type="button" class="landing-hero-slider__dot" data-slide-index="1" aria-label="Go to slide 2"></button>
            <button type="button" class="landing-hero-slider__dot" data-slide-index="2" aria-label="Go to slide 3"></button>
            <button type="button" class="landing-hero-slider__dot" data-slide-index="3" aria-label="Go to slide 4"></button>
        </div>
    </div>
</section>

<div class="landing-dashboard" aria-label="eKesihatan information centre">
    <div class="landing-dashboard__intro">
        <div>
            <span class="landing-dashboard__eyebrow" data-i18n="Health Information Centre">Health Information Centre</span>
            <h2 data-i18n="Clinic updates, useful forms and health tools">Clinic updates, useful forms and health tools</h2>
            <p data-i18n="Everything you need is organised into clear, easy-to-use widgets.">Everything you need is organised into clear, easy-to-use widgets.</p>
        </div>
    </div>

    <div class="landing-widget-grid">
        <section class="landing-widget landing-widget--notices" aria-labelledby="clinic-notices-title">
            <div class="landing-widget__header">
                <div class="landing-widget__heading">
                    <span class="landing-widget__icon landing-widget__icon--blue" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M5 4h14a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Z"/>
                            <path d="M7 8h6M7 12h10M7 16h10"/>
                            <path d="M16 8h1"/>
                        </svg>
                    </span>
                    <div>
                        <span class="landing-widget__kicker" data-i18n="Latest updates">Latest updates</span>
                        <h3 id="clinic-notices-title" data-i18n="Clinic Notices and Bulletins">Clinic Notices and Bulletins</h3>
                    </div>
                </div>
                <span class="landing-widget__count" aria-label="{{ $bulletins->count() }} notices">{{ $bulletins->count() }}</span>
            </div>
            <p class="landing-widget__description" data-i18n="View the latest clinic programmes, campaigns and announcements.">View the latest clinic programmes, campaigns and announcements.</p>

            <div class="notice-accordion" data-notice-accordion>
                @forelse ($bulletins as $bulletin)
                    @php
                        $posterUrl = $bulletin->poster_path ? asset($bulletin->poster_path) : null;
                        $accordionId = 'bulletin-details-' . $bulletin->id;
                    @endphp
                    <article class="notice-accordion__item" id="bulletin-{{ $bulletin->id }}">
                        <button type="button" class="notice-accordion__trigger" aria-expanded="false" aria-controls="{{ $accordionId }}">
                            <span class="notice-accordion__icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="4" y="3" width="16" height="18" rx="2"/>
                                    <path d="M8 7h8M8 11h8M8 15h5"/>
                                </svg>
                            </span>
                            <span class="notice-accordion__summary">
                                <strong>{{ $bulletin->title }}</strong>
                                <span class="notice-accordion__meta">
                                    @if ($bulletin->event_date)
                                        <span><svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4M8 3v4M3 10h18"/></svg>{{ $bulletin->event_date->format('d M Y') }}</span>
                                    @endif
                                    @if ($bulletin->event_time)
                                        <span><svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>{{ $bulletin->event_time }}</span>
                                    @endif
                                    @if ($bulletin->location)
                                        <span><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="2.5"/></svg>{{ $bulletin->location }}</span>
                                    @endif
                                </span>
                            </span>
                            <span class="notice-accordion__chevron" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6"/></svg></span>
                        </button>
                        <div id="{{ $accordionId }}" class="notice-accordion__panel" hidden>
                            <div class="notice-accordion__content">
                                @if ($bulletin->summary)<p class="notice-accordion__lead">{{ $bulletin->summary }}</p>@endif
                                @if ($bulletin->event_date || $bulletin->event_time || $bulletin->location)
                                    <dl class="notice-detail-grid">
                                        @if ($bulletin->event_date)<div><dt data-i18n="Date">Date</dt><dd>{{ $bulletin->event_date->format('d M Y (l)') }}</dd></div>@endif
                                        @if ($bulletin->event_time)<div><dt data-i18n="Time">Time</dt><dd>{{ $bulletin->event_time }}</dd></div>@endif
                                        @if ($bulletin->location)<div><dt data-i18n="Location">Location</dt><dd>{{ $bulletin->location }}</dd></div>@endif
                                    </dl>
                                @endif
                                @if ($bulletin->details)<p>{{ $bulletin->details }}</p>@endif
                                @if ($posterUrl)
                                    <div class="bulletin-actions">
                                        <a class="landing-action-button" href="{{ $posterUrl }}" target="_blank" rel="noopener noreferrer">View Full Poster</a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="landing-empty-state"><strong data-i18n="No clinic notices yet">No clinic notices yet</strong><span data-i18n="New clinic updates will appear here.">New clinic updates will appear here.</span></div>
                @endforelse
            </div>
        </section>

        <section class="landing-widget landing-widget--downloads" aria-labelledby="forms-downloads-title">
            <div class="landing-widget__header">
                <div class="landing-widget__heading">
                    <span class="landing-widget__icon landing-widget__icon--red" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H7a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7Z"/><path d="M14 2v5h5M12 11v6m0 0 3-3m-3 3-3-3"/></svg>
                    </span>
                    <div><span class="landing-widget__kicker" data-i18n="Useful resources">Useful resources</span><h3 id="forms-downloads-title" data-i18n="Forms and Downloads">Forms and Downloads</h3></div>
                </div>
                <span class="landing-widget__count" aria-label="{{ $downloadableForms->count() }} forms">{{ $downloadableForms->count() }}</span>
            </div>
            <p class="landing-widget__description" data-i18n="Download commonly used clinic forms and supporting documents.">Download commonly used clinic forms and supporting documents.</p>
            <div class="download-card-grid">
                @forelse ($downloadableForms as $downloadableForm)
                    @php
                        $relativeFilePath = ltrim(str_replace('\\', '/', $downloadableForm->file_path), '/');
                        $liveFilePath = dirname(base_path()) . '/public_html/' . $relativeFilePath;
                        $projectFilePath = public_path($relativeFilePath);
                        $absoluteFilePath = is_file($liveFilePath) ? $liveFilePath : $projectFilePath;
                        $fileExists = is_file($absoluteFilePath);
                        $fileSize = $fileExists ? filesize($absoluteFilePath) : null;
                        if ($fileSize === null) { $fileSizeLabel = 'Size unavailable'; }
                        elseif ($fileSize >= 1024 * 1024) { $fileSizeLabel = number_format($fileSize / (1024 * 1024), 1) . ' MB'; }
                        else { $fileSizeLabel = number_format($fileSize / 1024, 1) . ' KB'; }
                        $fileExtension = strtoupper(pathinfo($relativeFilePath, PATHINFO_EXTENSION)) ?: 'FILE';
                        $downloadFilename = \Illuminate\Support\Str::slug($downloadableForm->title) . '.' . strtolower($fileExtension);
                    @endphp
                    <article class="download-card">
                        <div class="download-card__icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M7 3h7l4 4v14H7z"/><path d="M14 3v5h5"/><path d="M9.5 14h5M9.5 17h4"/></svg></div>
                        <div class="download-card__body"><div class="download-card__topline"><span class="download-card__format">{{ $fileExtension }}</span><span>{{ $fileSizeLabel }}</span></div><h4>{{ $downloadableForm->title }}</h4>@if ($downloadableForm->description)<p>{{ $downloadableForm->description }}</p>@endif</div>
                        @if ($fileExists)
                            <a class="download-card__action" href="{{ asset($relativeFilePath) }}" download="{{ $downloadFilename }}"><span data-i18n="Download">Download</span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 3v12m0 0 4-4m-4 4-4-4"/><path d="M5 20h14"/></svg></a>
                        @else
                            <span class="download-card__action is-disabled" data-i18n="Not Available">Not Available</span>
                        @endif
                    </article>
                @empty
                    <div class="landing-empty-state landing-empty-state--wide"><strong data-i18n="No forms available">No forms available</strong><span data-i18n="Downloadable clinic forms will appear here.">Downloadable clinic forms will appear here.</span></div>
                @endforelse
            </div>
        </section>
    </div>

    <section class="landing-widget landing-widget--bmi" aria-labelledby="bmi-calculator-title">
        <div class="landing-bmi-header">
            <div class="landing-widget__heading">
                <span class="landing-widget__icon landing-widget__icon--green" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.6l-1-1a5.5 5.5 0 0 0-7.8 7.8l1 1L12 21l7.8-7.6 1-1a5.5 5.5 0 0 0 0-7.8Z"/><path d="M7 12h2l1.2-2.5L12.5 15l1.5-3h3"/></svg>
                </span>
                <div><span class="landing-widget__kicker" data-i18n="Quick health check">Quick health check</span><h3 id="bmi-calculator-title" data-i18n="BMI Calculator">BMI Calculator</h3></div>
            </div>
            <p data-i18n="Enter your height and weight to get an estimated body mass index.">Enter your height and weight to get an estimated body mass index.</p>
        </div>
        <div class="bmi-dashboard-layout">
            <div class="bmi-guidance-card">
                <strong data-i18n="For guidance only">For guidance only</strong>
                <p data-i18n="BMI is a screening estimate and does not replace professional medical advice.">BMI is a screening estimate and does not replace professional medical advice.</p>
                <div class="bmi-range-list" aria-label="BMI reference ranges"><span><b>&lt;18.5</b> Underweight</span><span><b>18.5–24.9</b> Normal</span><span><b>25–29.9</b> Overweight</span><span><b>30+</b> Obesity</span></div>
            </div>
            <div class="bmi-modern-card">
                <form id="landing-bmi-form" class="bmi-modern-form">
                    <div class="bmi-modern-grid">
                        <div class="bmi-field"><label for="landing-bmi-sex" data-i18n="Sex">Sex</label><select id="landing-bmi-sex" name="sex" required><option value="male" data-i18n="Male">Male</option><option value="female" data-i18n="Female">Female</option><option value="other" data-i18n="Other">Other</option></select></div>
                        <div class="bmi-field"><label for="landing-bmi-age" data-i18n="Age">Age</label><input id="landing-bmi-age" name="age" type="number" min="1" max="120" placeholder="e.g. 22" required></div>
                        <div class="bmi-field"><label for="landing-bmi-height" data-i18n="Height (cm)">Height (cm)</label><input id="landing-bmi-height" name="height_cm" type="number" step="0.1" min="50" max="250" placeholder="e.g. 165" required></div>
                        <div class="bmi-field"><label for="landing-bmi-weight" data-i18n="Weight (kg)">Weight (kg)</label><input id="landing-bmi-weight" name="weight_kg" type="number" step="0.1" min="10" max="300" placeholder="e.g. 58" required></div>
                    </div>
                    <button class="bmi-modern-submit" type="submit" data-i18n="Calculate BMI">Calculate BMI</button>
                </form>
                <div id="landing-bmi-result" class="bmi-modern-result" hidden aria-live="polite"><span data-i18n="Your estimated BMI">Your estimated BMI</span><strong id="landing-bmi-value">-</strong><span id="landing-bmi-category" class="bmi-modern-result__category">-</span></div>
            </div>
        </div>
    </section>
</div>

<section class="landing-connect">
    @php
        $facebookPreview = file_exists(public_path('images/facebook.png'))
            ? 'images/facebook.png'
            : 'images/inside.jpg';
        $instagramPreview = file_exists(public_path('images/instagram.png'))
            ? 'images/instagram.png'
            : 'images/intern.jpg';
        $tiktokPreview = file_exists(public_path('images/tiktok.jpg'))
            ? 'images/tiktok.jpg'
            : 'images/1000langkah.jpg';
    @endphp
    <h3 class="landing-section-title landing-section-title--social" data-i18n="Explore More!">EXPLORE MORE!</h3>
    <p class="landing-connect__intro" data-i18n="Keep updated with us on our social media.">
        Keep updated with us on our social media.
    </p>
    <div class="landing-connect__grid">
        <a
            class="connect-card"
            href="https://www.facebook.com/UKesUiTMPerlis/"
            target="_blank"
            rel="noopener noreferrer"
            aria-label="Facebook Unit Kesihatan UiTM Perlis"
        >
            <div class="connect-card__media">
                <img src="{{ asset($facebookPreview) }}" alt="Facebook page preview for Unit Kesihatan UiTM Perlis" loading="lazy">
            </div>
            <div class="connect-card__label" data-i18n="Facebook">Facebook</div>
        </a>
        <a
            class="connect-card"
            href="https://www.instagram.com/unitkesihatanuitmperlis/?hl=en"
            target="_blank"
            rel="noopener noreferrer"
            aria-label="Instagram Unit Kesihatan UiTM Perlis"
        >
            <div class="connect-card__media">
                <img src="{{ asset($instagramPreview) }}" alt="Instagram page preview for Unit Kesihatan UiTM Perlis" loading="lazy">
            </div>
            <div class="connect-card__label" data-i18n="Instagram">Instagram</div>
        </a>
        <a
            class="connect-card"
            href="https://www.tiktok.com/@newhealthuitmperlis"
            target="_blank"
            rel="noopener noreferrer"
            aria-label="Tiktok New Health UiTM Perlis"
        >
            <div class="connect-card__media">
                <img src="{{ asset($tiktokPreview) }}" alt="Tiktok page preview for New Health UiTM Perlis" loading="lazy">
            </div>
            <div class="connect-card__label" data-i18n="Tiktok">Tiktok</div>
        </a>
    </div>
</section>
 
<script>
    (function () {
        const initializeSlider = () => {
            const slider = document.getElementById('landing-hero-slider');
            if (!slider || slider.dataset.sliderReady === 'true') {
                return;
            }

            const track = slider.querySelector('.landing-hero-slider__track');
            if (!track) {
                return;
            }

            const slides = Array.from(track.querySelectorAll('.landing-hero-slider__slide'));
            const indicators = Array.from(slider.querySelectorAll('.landing-hero-slider__dot'));
            const previousButton = slider.querySelector('.landing-hero-slider__nav--prev');
            const nextButton = slider.querySelector('.landing-hero-slider__nav--next');
            if (slides.length < 2) {
                return;
            }

            slider.dataset.sliderReady = 'true';

            // Apply core layout styles in JS as a fallback
            // when browser cache serves stale CSS.
            slider.style.overflow = 'hidden';
            track.style.display = 'flex';
            track.style.transition = 'transform 700ms ease';
            slides.forEach((slide) => {
                slide.style.minWidth = '100%';
            });

            const intervalMs = 4500;
            let currentIndex = 0;
            let intervalId = null;

            const updateIndicators = () => {
                indicators.forEach((indicator, index) => {
                    indicator.classList.toggle('is-active', index === currentIndex);
                    indicator.setAttribute('aria-current', index === currentIndex ? 'true' : 'false');
                });
            };

            const goToSlide = (index) => {
                currentIndex = (index + slides.length) % slides.length;
                track.style.transform = `translateX(-${currentIndex * 100}%)`;
                updateIndicators();
            };

            const startAutoSwipe = () => {
                if (intervalId) {
                    window.clearInterval(intervalId);
                }

                intervalId = window.setInterval(() => {
                    goToSlide(currentIndex + 1);
                }, intervalMs);
            };

            const showNextSlide = () => {
                goToSlide(currentIndex + 1);
            };

            const showPreviousSlide = () => {
                goToSlide(currentIndex - 1);
            };

            slider.addEventListener('mouseenter', () => {
                if (intervalId) {
                    window.clearInterval(intervalId);
                }
            });

            slider.addEventListener('mouseleave', () => {
                startAutoSwipe();
            });

            indicators.forEach((indicator) => {
                indicator.addEventListener('click', () => {
                    const index = Number(indicator.dataset.slideIndex);
                    if (Number.isNaN(index)) {
                        return;
                    }

                    goToSlide(index);
                    startAutoSwipe();
                });
            });

            if (previousButton) {
                previousButton.addEventListener('click', () => {
                    showPreviousSlide();
                    startAutoSwipe();
                });
            }

            if (nextButton) {
                nextButton.addEventListener('click', () => {
                    showNextSlide();
                    startAutoSwipe();
                });
            }

            goToSlide(0);
            startAutoSwipe();
        };

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initializeSlider, { once: true });
        } else {
            initializeSlider();
        }
    })();
</script>

<script>
    (function () {
        const accordions = Array.from(document.querySelectorAll('[data-notice-accordion]'));

        accordions.forEach((accordion) => {
            const triggers = Array.from(accordion.querySelectorAll('.notice-accordion__trigger'));

            triggers.forEach((trigger) => {
                trigger.addEventListener('click', () => {
                    const panelId = trigger.getAttribute('aria-controls');
                    const panel = panelId ? document.getElementById(panelId) : null;
                    if (!panel) {
                        return;
                    }

                    const shouldOpen = trigger.getAttribute('aria-expanded') !== 'true';

                    triggers.forEach((otherTrigger) => {
                        const otherPanelId = otherTrigger.getAttribute('aria-controls');
                        const otherPanel = otherPanelId ? document.getElementById(otherPanelId) : null;
                        otherTrigger.setAttribute('aria-expanded', 'false');
                        if (otherPanel) {
                            otherPanel.hidden = true;
                        }
                    });

                    trigger.setAttribute('aria-expanded', shouldOpen ? 'true' : 'false');
                    panel.hidden = !shouldOpen;
                });
            });
        });
    })();
</script>

<script>
    (function () {
        const form = document.getElementById('landing-bmi-form');
        if (!form) {
            return;
        }
 
        const result = document.getElementById('landing-bmi-result');
        const bmiValue = document.getElementById('landing-bmi-value');
        const bmiCategory = document.getElementById('landing-bmi-category');
        const heightInput = document.getElementById('landing-bmi-height');
        const weightInput = document.getElementById('landing-bmi-weight');
 
        const getCategory = (bmi) => {
            if (bmi < 18.5) {
                return 'Underweight';
            }
            if (bmi < 25) {
                return 'Normal';
            }
            if (bmi < 30) {
                return 'Overweight';
            }
            return 'Obese';
        };
 
        form.addEventListener('submit', (event) => {
            event.preventDefault();
 
            const heightCm = parseFloat(heightInput.value);
            const weightKg = parseFloat(weightInput.value);
 
            if (!heightCm || !weightKg) {
                return;
            }
 
            const bmi = weightKg / Math.pow(heightCm / 100, 2);
            const rounded = Math.round(bmi * 10) / 10;
 
            bmiValue.textContent = rounded.toFixed(1);
            bmiCategory.textContent = getCategory(rounded);
            result.hidden = false;
        });
    })();
</script>

@endsection