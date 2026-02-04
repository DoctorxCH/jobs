<x-layouts.pixel title="{{ __('main.privacy_title') }}">
    @php
        $sections = [
            [
                'title' => '1. Prevádzkovateľ osobných údajov',
                'pro' => <<<'HTML'
<p>Prevádzkovateľom osobných údajov je:</p>
<p><strong>M&amp;M Media s. r. o.</strong><br>Pražská 11, 811 04 Bratislava – Staré Mesto<br>IČO: 48 090 727<br>E‑mail: support@365jobs.sk</p>
<p>(ďalej len „Prevádzkovateľ“)</p>
HTML,
                'gag_title' => '1. Kto sme',
                'gag' => <<<'HTML'
<p>Sme 365jobs.sk a prevádzkujeme túto platformu. Kontakt: support@365jobs.sk.</p>
HTML,
            ],
            [
                'title' => '2. Rozsah spracúvaných osobných údajov',
                'pro' => <<<'HTML'
<p>Prevádzkovateľ spracúva výlučne údaje nevyhnutné na prevádzku platformy 365jobs.sk.</p>
<h3 class="mt-4 text-sm font-semibold">2.1 Údaje Zamestnávateľov a tímových používateľov</h3>
<ul class="list-disc pl-5">
    <li>meno a priezvisko</li>
    <li>e‑mailová adresa</li>
    <li>telefónne číslo (ak je vyplnené)</li>
    <li>názov spoločnosti</li>
    <li>identifikačné a fakturačné údaje</li>
    <li>údaje zadané do formulárov na Webovej stránke</li>
    <li>systémové nastavenia účtu</li>
    <li>rozpracované pracovné ponuky (drafty)</li>
    <li>pozvánky (invitations)</li>
</ul>
<h3 class="mt-4 text-sm font-semibold">2.2 Údaje Uchádzačov o prácu</h3>
<ul class="list-disc pl-5">
    <li>Prevádzkovateľ neprevádzkuje kandidátske účty ani databázu životopisov.</li>
    <li>Spracúvané sú výlučne údaje, ktoré Uchádzač dobrovoľne uvedie v kontaktnom formulári pri reakcii na Inzerát: meno (ak je uvedené), e‑mailová adresa, obsah správy.</li>
    <li>Ukladá sa výlučne prvá správa odoslaná cez kontaktný formulár.</li>
    <li>Následná komunikácia prebieha mimo platformy (e‑mail, ATS) a Prevádzkovateľ k nej nemá prístup.</li>
</ul>
HTML,
                'gag_title' => '2. Aké údaje vidíme (a nevidíme)',
                'gag' => <<<'HTML'
<ul class="list-disc pl-5">
    <li>Vidíme to, čo sami vyplníte.</li>
    <li>Vidíme firmy, účty, inzeráty, drafty, pozvánky.</li>
    <li>Vidíme prvú správu, ktorú kandidát pošle firme cez formulár.</li>
    <li>Nevidíme vaše e‑maily po prvej správe.</li>
    <li>Nevidíme súkromnú komunikáciu.</li>
    <li>Nemáme databázu životopisov ani kandidátske účty.</li>
</ul>
HTML,
            ],
            [
                'title' => '3. Komunikácia a správy',
                'pro' => <<<'HTML'
<p>Prevádzkovateľ uchováva:</p>
<ul class="list-disc pl-5">
    <li>komunikáciu Zamestnávateľ → administrátor platformy,</li>
    <li>prvú správu Uchádzač → Zamestnávateľ odoslanú cez Webovú stránku.</li>
</ul>
<p>Účel: technická podpora, riešenie sporov, prevencia zneužívania, zabezpečenie funkčnosti systému.</p>
HTML,
                'gag_title' => '3. Správy',
                'gag' => <<<'HTML'
<p>Ukladáme len prvú správu od kandidáta. Ďalšia komunikácia ide mimo platformy.</p>
HTML,
            ],
            [
                'title' => '4. Technické a prevádzkové údaje',
                'pro' => <<<'HTML'
<h3 class="text-sm font-semibold">4.1 IP adresy</h3>
<p>Prevádzkovateľ spracúva IP adresy z dôvodu bezpečnosti, ochrany pred zneužívaním a evidencie incidentov.</p>
<p>Doba uchovávania IP adries: maximálne 6 mesiacov, pokiaľ právne predpisy nevyžadujú dlhšie uchovanie.</p>
<h3 class="mt-4 text-sm font-semibold">4.2 Údaje o zariadení a prehliadači</h3>
<p>Automaticky spracúvané technické údaje:</p>
<ul class="list-disc pl-5">
    <li>typ a verzia prehliadača</li>
    <li>operačný systém</li>
    <li>rozlíšenie obrazovky</li>
    <li>jazykové nastavenia</li>
    <li>dátum a čas prístupu</li>
</ul>
<p>Tieto údaje sú spracúvané výlučne na technické, bezpečnostné a analytické účely.</p>
HTML,
                'gag_title' => '4. IP adresy',
                'gag' => <<<'HTML'
<ul class="list-disc pl-5">
    <li>IP adresy si ukladáme 6 mesiacov.</li>
    <li>Nie kvôli špehovaniu, ale kvôli bezpečnosti.</li>
</ul>
HTML,
            ],
            [
                'title' => '5. Cookies',
                'pro' => <<<'HTML'
<h3 class="text-sm font-semibold">5.1 Vlastné cookies</h3>
<ul class="list-disc pl-5">
    <li>základná funkčnosť Webovej stránky</li>
    <li>ukladanie obľúbených pracovných ponúk</li>
    <li>zachovanie nastavení používateľa</li>
</ul>
<p>Tieto cookies nie sú reklamné a nevyžadujú osobitný súhlas, pokiaľ to právne predpisy umožňujú.</p>
<h3 class="mt-4 text-sm font-semibold">5.2 Cookies tretích strán</h3>
<p>Webová stránka používa cookies Google Analytics výhradne na meranie návštevnosti a správania používateľov; nie na zobrazovanie reklám ani remarketing.</p>
<p>Údaje z analytiky môžu byť použité na zlepšenie funkčnosti Webovej stránky a zobrazovanie odporúčaných pracovných ponúk vo výsledkoch vyhľadávania.</p>
HTML,
                'gag_title' => '5. Cookies (jednoducho)',
                'gag' => <<<'HTML'
<ul class="list-disc pl-5">
    <li>Naše cookies: aby web fungoval a aby ste mali obľúbené joby.</li>
    <li>Google cookies: len štatistiky, žiadne reklamy.</li>
    <li>Výsledok: lepšie odporúčané joby, nie reklamy.</li>
</ul>
HTML,
            ],
            [
                'title' => '6. Právny základ spracúvania',
                'pro' => <<<'HTML'
<p>Osobné údaje sú spracúvané na základe:</p>
<ul class="list-disc pl-5">
    <li>plnenia zmluvy</li>
    <li>oprávneného záujmu Prevádzkovateľa</li>
    <li>zákonných povinností</li>
    <li>súhlasu (ak je vyžadovaný)</li>
</ul>
HTML,
                'gag_title' => '6. Prečo to celé robíme',
                'gag' => <<<'HTML'
<ul class="list-disc pl-5">
    <li>Aby web fungoval.</li>
    <li>Aby bol bezpečný.</li>
    <li>Aby ste mali lepší zážitok.</li>
</ul>
HTML,
            ],
            [
                'title' => '7. Prístup k údajom',
                'pro' => <<<'HTML'
<p>K osobným údajom majú prístup:</p>
<ul class="list-disc pl-5">
    <li>Prevádzkovateľ a jeho poverené osoby,</li>
    <li>technickí dodávatelia (hosting, analytika) v nevyhnutnom rozsahu.</li>
</ul>
<p>Údaje nie sú predávané ani poskytované na reklamné účely tretím stranám.</p>
HTML,
                'gag_title' => '7. Predaj dát?',
                'gag' => <<<'HTML'
<p><strong>❌ Nie. Nikdy.</strong></p>
HTML,
            ],
            [
                'title' => '8. Uchovávanie údajov',
                'pro' => <<<'HTML'
<p>Osobné údaje sú uchovávané:</p>
<ul class="list-disc pl-5">
    <li>počas trvania zmluvného vzťahu,</li>
    <li>po dobu nevyhnutnú na splnenie zákonných povinností,</li>
    <li>po dobu ochrany právnych nárokov Prevádzkovateľa.</li>
</ul>
HTML,
                'gag_title' => '8. Vaše práva',
                'gag' => <<<'HTML'
<ul class="list-disc pl-5">
    <li>Môžete sa opýtať, čo o vás máme.</li>
    <li>Môžete žiadať opravu alebo zmazanie.</li>
    <li>Môžete sa sťažovať (ak chcete).</li>
</ul>
HTML,
            ],
            [
                'title' => '9. Práva dotknutých osôb',
                'pro' => <<<'HTML'
<p>Dotknutá osoba má právo:</p>
<ul class="list-disc pl-5">
    <li>na prístup k údajom,</li>
    <li>na opravu,</li>
    <li>na vymazanie (ak to právo umožňuje),</li>
    <li>na obmedzenie spracúvania,</li>
    <li>namietať spracúvanie,</li>
    <li>podať sťažnosť na Úrad na ochranu osobných údajov SR.</li>
</ul>
HTML,
                'gag_title' => '9. Záver',
                'gag' => <<<'HTML'
<ul class="list-disc pl-5">
    <li>Zbierame minimum.</li>
    <li>Nerobíme shady veci.</li>
    <li>A snažíme sa byť normálni.</li>
</ul>
HTML,
            ],
            [
                'title' => '10. Záver',
                'pro' => <<<'HTML'
<p>Tieto Zásady ochrany osobných údajov sú platné odo dňa zverejnenia a sú dostupné na Webovej stránke.</p>
HTML,
                'gag_title' => '10. ✅ Hotovo',
                'gag' => <<<'HTML'
<p>GDPR je hotové. Zbierame minimum a sme normálni.</p>
HTML,
            ],
        ];
    @endphp

    <section class="mx-auto flex w-full max-w-6xl flex-col gap-6">
        <div class="pixel-frame p-8">
            <h1 class="text-3xl font-bold text-slate-900">
                {{ $page?->title ?? __('main.privacy_default_title') }}
            </h1>
            <p class="mt-2 text-sm text-slate-600">{{ __('main.last_updated') }} {{ $page?->updated_at?->format('d.m.Y') ?? now()->format('d.m.Y') }}</p>
        </div>

        <div class="pixel-frame p-6">
            <div class="grid gap-6 lg:grid-cols-[2fr_1fr]">
                <div class="text-xs uppercase tracking-[0.2em] text-slate-500">365jobs.sk – profesionálna verzia (GDPR)</div>
                <div class="text-xs uppercase tracking-[0.2em] text-slate-500">😎 GDPR – „GAG“ / USER‑FRIENDLY</div>
            </div>

            <div class="mt-6 space-y-8">
                @foreach ($sections as $section)
                    <div class="grid gap-6 lg:grid-cols-[2fr_1fr]">
                        <div>
                            <h2 class="text-sm font-semibold text-slate-900">{{ $section['title'] }}</h2>
                            <div class="prose prose-sm max-w-none text-slate-700">{!! $section['pro'] !!}</div>
                        </div>
                        <div>
                            <h2 class="text-sm font-semibold text-slate-900">{{ $section['gag_title'] }}</h2>
                            <div class="prose prose-sm max-w-none text-slate-700">{!! $section['gag'] !!}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
</x-layouts.pixel>
