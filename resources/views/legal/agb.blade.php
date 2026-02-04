<x-layouts.pixel title="{{ __('main.terms_title') }}">
    @php
        $sections = [
            [
                'title' => '1. Prevádzkovateľ',
                'pro' => <<<'HTML'
<p>Prevádzkovateľom webovej stránky 365jobs.sk (ďalej len „Webová stránka“) je M&amp;M Media s. r. o., so sídlom Pražská 11, 811 04 Bratislava – Staré Mesto, IČO: 48 090 727, zapísaná v Obchodnom registri Mestského súdu Bratislava III, oddiel: Sro, vložka č.: 103242/B (ďalej len „Prevádzkovateľ“).</p>
<ul class="list-disc pl-5">
    <li>Kontaktný e‑mail: support@365jobs.sk.</li>
    <li>Prevádzkovateľ nie je agentúrou práce ani sprostredkovateľom zamestnania podľa zákona č. 5/2004 Z. z.; poskytuje výlučne technologickú a inzertnú platformu.</li>
</ul>
HTML,
                'gag_title' => '1. Kto sme',
                'gag' => <<<'HTML'
<ul class="list-disc pl-5">
    <li>Prevádzkujeme 365jobs.sk.</li>
    <li>Nie sme agentúra, len platforma na inzeráty.</li>
</ul>
HTML,
            ],
            [
                'title' => '2. Definícia pojmov',
                'pro' => <<<'HTML'
<ol class="list-decimal pl-5">
    <li>Zamestnávateľ – právnická osoba alebo fyzická osoba – podnikateľ využívajúca Webovú stránku.</li>
    <li>Tímový používateľ – osoba s prístupom do účtu Zamestnávateľa; za jej konanie zodpovedá Zamestnávateľ.</li>
    <li>Účet – používateľský účet Zamestnávateľa.</li>
    <li>Inzerát – pracovná ponuka zverejnená Zamestnávateľom.</li>
    <li>Služby – najmä zverejnenie Inzerátu, Top inzerát, Top partner, reklamné a doplnkové služby.</li>
    <li>Cenník – aktuálny prehľad cien a kreditových sadzieb.</li>
    <li>Kredity – interné platobné jednotky platformy; nejde o menu ani elektronické peniaze.</li>
    <li>Uchádzač – fyzická osoba reagujúca na Inzerát bez registrácie.</li>
</ol>
HTML,
                'gag_title' => '2. Pojmy jednoducho',
                'gag' => <<<'HTML'
<ul class="list-disc pl-5">
    <li>Zamestnávateľ = firma.</li>
    <li>Účet = firemný účet.</li>
    <li>Uchádzač = človek, ktorý reaguje na inzerát.</li>
</ul>
HTML,
            ],
            [
                'title' => '3. Vznik zmluvného vzťahu',
                'pro' => <<<'HTML'
<ol class="list-decimal pl-5">
    <li>Zmluvný vzťah vzniká registráciou Zamestnávateľa alebo objednaním služby.</li>
    <li>Zamestnávateľ potvrdzuje, že sa oboznámil s VOP a súhlasí s nimi.</li>
    <li>Zamestnávateľ zodpovedá za správnosť všetkých zadaných údajov.</li>
</ol>
HTML,
                'gag_title' => '3. Kedy vzniká dohoda',
                'gag' => <<<'HTML'
<ul class="list-disc pl-5">
    <li>Registrujete sa alebo objednáte službu → vzniká dohoda.</li>
    <li>Za údaje ručíte vy.</li>
</ul>
HTML,
            ],
            [
                'title' => '4. Účet a prístup',
                'pro' => <<<'HTML'
<ol class="list-decimal pl-5">
    <li>Zamestnávateľ zodpovedá za všetky aktivity vykonané prostredníctvom svojho Účtu.</li>
    <li>Prevádzkovateľ má technický prístup k údajom zadaným do formulárov na Webovej stránke, k rozpracovaným inzerátom (drafty), pozvánkam (invitations) a systémovým nastaveniam účtu, a to výlučne na účely prevádzky, podpory, bezpečnosti a kontroly systému.</li>
    <li>Prevádzkovateľ nezodpovedá za škody spôsobené zneužitím Účtu bez jeho zavinenia.</li>
</ol>
HTML,
                'gag_title' => '4. Čo vidíme',
                'gag' => <<<'HTML'
<ul class="list-disc pl-5">
    <li>Vidíme, čo do webu vyplníte.</li>
    <li>Vidíme drafty inzerátov a pozvánky.</li>
    <li>Nerobíme to zo zvedavosti, ale aby web fungoval.</li>
</ul>
HTML,
            ],
            [
                'title' => '5. Poskytované služby',
                'pro' => <<<'HTML'
<ol class="list-decimal pl-5">
    <li>Prevádzkovateľ poskytuje služby podľa aktuálneho Cenníka.</li>
    <li>Služby sú poskytované bez záruky výsledku (napr. počet reakcií, obsadenie pozície).</li>
    <li>Prevádzkovateľ negarantuje nepretržitú dostupnosť Webovej stránky ani zobrazenie Inzerátov bez prerušenia.</li>
</ol>
HTML,
                'gag_title' => '5. Služby',
                'gag' => <<<'HTML'
<ul class="list-disc pl-5">
    <li>Dávame priestor na inzeráty a doplnky podľa cenníka.</li>
    <li>Výsledok negarantujeme.</li>
</ul>
HTML,
            ],
            [
                'title' => '6. Objednávky a aktivácia',
                'pro' => <<<'HTML'
<ol class="list-decimal pl-5">
    <li>Služby sa objednávajú výlučne prostredníctvom Webovej stránky.</li>
    <li>Objednávka je záväzná jej potvrdením alebo úhradou.</li>
    <li>Prevádzkovateľ si vyhradzuje právo službu pozastaviť alebo nezačať poskytovať pri omeškaní platby, porušení VOP alebo z bezpečnostných či technických dôvodov.</li>
</ol>
HTML,
                'gag_title' => '6. Objednávky',
                'gag' => <<<'HTML'
<ul class="list-disc pl-5">
    <li>Objednáva sa iba cez web.</li>
    <li>Nezaplatené alebo problémové objednávky môžeme pozastaviť.</li>
</ul>
HTML,
            ],
            [
                'title' => '7. Kredity',
                'pro' => <<<'HTML'
<ol class="list-decimal pl-5">
    <li>Kredity slúžia na časové čerpanie služieb.</li>
    <li>Kredity nie sú vyplatiteľné, nie sú prevoditeľné a nie je možné ich vymeniť za peniaze.</li>
    <li>Pri skrátení doby zverejnenia Inzerátu sa vráti 50 % nevyužitých kreditov (zaokrúhlenie nahor).</li>
    <li>Pri archivovaní Inzerátu alebo pri porušení VOP sa kredity nevracajú.</li>
    <li>Úprava obsahu Inzerátu je bezplatná, pokiaľ nedochádza k predĺženiu jeho trvania.</li>
</ol>
HTML,
                'gag_title' => '7. Kredity',
                'gag' => <<<'HTML'
<ul class="list-disc pl-5">
    <li>Sú to body, nie peniaze.</li>
    <li>Nedajú sa vybrať ani previesť.</li>
    <li>Skrátite inzerát → polovicu vrátime.</li>
    <li>Porušíte pravidlá → nevraciame nič.</li>
</ul>
HTML,
            ],
            [
                'title' => '8. Pravidlá Inzerátov',
                'pro' => <<<'HTML'
<ol class="list-decimal pl-5">
    <li>Inzerát musí byť pravdivý, zákonný a nediskriminačný.</li>
    <li>Zakázané sú najmä ponuky vyžadujúce poplatok od uchádzača, MLM, investičné a erotické ponuky, klamlivé alebo duplicitné inzeráty.</li>
    <li>Prevádzkovateľ si vyhradzuje právo kedykoľvek a bez uvedenia dôvodu Inzerát nezverejniť, pozastaviť, odstrániť alebo obmedziť jeho viditeľnosť.</li>
    <li>Za takéto opatrenia nevzniká nárok na náhradu ani vrátenie kreditov.</li>
</ol>
HTML,
                'gag_title' => '8. Inzeráty',
                'gag' => <<<'HTML'
<ul class="list-disc pl-5">
    <li>Môžeme ich kedykoľvek stiahnuť z webu.</li>
    <li>Aj bez vysvetľovania.</li>
    <li>Kredity späť? Väčšinou nie.</li>
</ul>
HTML,
            ],
            [
                'title' => '9. Uchádzači o prácu',
                'pro' => <<<'HTML'
<ol class="list-decimal pl-5">
    <li>Uchádzači nemajú používateľský účet.</li>
    <li>Webová stránka neprevádzkuje databázu životopisov.</li>
    <li>Reakcia na Inzerát prebieha priamo medzi Uchádzačom a Zamestnávateľom.</li>
    <li>Prevádzkovateľ nie je stranou pracovnoprávneho vzťahu.</li>
</ol>
HTML,
                'gag_title' => '9. Uchádzači',
                'gag' => <<<'HTML'
<ul class="list-disc pl-5">
    <li>Uchádzači nemajú účet.</li>
    <li>Nemáme databázu CV.</li>
    <li>Firma sa s kandidátom rieši priamo.</li>
</ul>
HTML,
            ],
            [
                'title' => '10. Dostupnosť a výpadky',
                'pro' => <<<'HTML'
<ol class="list-decimal pl-5">
    <li>Webová stránka je poskytovaná v režime „ako je dostupná“ (as‑is).</li>
    <li>Prevádzkovateľ nezodpovedá za technické výpadky, údržbu, vyššiu moc ani zásahy tretích strán.</li>
    <li>Pri dlhodobejších výpadkoch môže Prevádzkovateľ primerane predĺžiť dobu zverejnenia Inzerátu, avšak bez nároku na finančnú refundáciu.</li>
</ol>
HTML,
                'gag_title' => '10. Výpadky',
                'gag' => <<<'HTML'
<ul class="list-disc pl-5">
    <li>Web môže spadnúť alebo byť v údržbe.</li>
    <li>Ak výpadok trvá dlho, môžeme inzerát predĺžiť.</li>
    <li>Peniaze späť? ❌</li>
</ul>
HTML,
            ],
            [
                'title' => '11. Blokovanie používateľov',
                'pro' => <<<'HTML'
<ol class="list-decimal pl-5">
    <li>Prevádzkovateľ si vyhradzuje právo zablokovať alebo zrušiť Účet aj bez uvedenia dôvodu.</li>
    <li>Blokovaním účtu nevzniká nárok na vrátenie kreditov ani náhradu škody.</li>
</ol>
HTML,
                'gag_title' => '11. Ban',
                'gag' => <<<'HTML'
<ul class="list-disc pl-5">
    <li>Účet vieme zablokovať.</li>
    <li>Aj bez dôvodu.</li>
</ul>
HTML,
            ],
            [
                'title' => '12. Zodpovednosť',
                'pro' => <<<'HTML'
<ol class="list-decimal pl-5">
    <li>Prevádzkovateľ nezodpovedá za obsah Inzerátov.</li>
    <li>Prevádzkovateľ negarantuje reakcie uchádzačov, úspešné obsadenie pozície ani trvanie pracovného vzťahu.</li>
    <li>Zamestnávateľ zodpovedá za všetky právne dôsledky obsahu Inzerátu.</li>
</ol>
HTML,
                'gag_title' => '12. Výsledky',
                'gag' => <<<'HTML'
<ul class="list-disc pl-5">
    <li>Negarantujeme kandidátov ani úspech.</li>
    <li>Za obsah inzerátu zodpovedá firma.</li>
</ul>
HTML,
            ],
            [
                'title' => '13. Záverečné ustanovenia',
                'pro' => <<<'HTML'
<ol class="list-decimal pl-5">
    <li>Prevádzkovateľ si vyhradzuje právo VOP meniť.</li>
    <li>Právne vzťahy sa riadia právnym poriadkom Slovenskej republiky.</li>
    <li>Tieto VOP sú platné a účinné odo dňa zverejnenia.</li>
</ol>
HTML,
                'gag_title' => '13. Právo',
                'gag' => <<<'HTML'
<ul class="list-disc pl-5">
    <li>Platí slovenské právo.</li>
    <li>Najprv sa dohodnime, potom riešme súdy.</li>
</ul>
HTML,
            ],
        ];
    @endphp

    <section class="mx-auto flex w-full max-w-6xl flex-col gap-6">
        <div class="pixel-frame p-8">
            <h1 class="text-3xl font-bold text-slate-900">
                {{ $page?->title ?? __('main.terms_default_title') }}
            </h1>
            <p class="mt-2 text-sm text-slate-600">{{ __('main.last_updated') }} {{ $page?->updated_at?->format('d.m.Y') ?? now()->format('d.m.Y') }}</p>
        </div>

        <div class="pixel-frame p-6">
            <div class="grid gap-6 lg:grid-cols-[2fr_1fr]">
                <div class="text-xs uppercase tracking-[0.2em] text-slate-500">365jobs.sk – profesionálna verzia</div>
                <div class="text-xs uppercase tracking-[0.2em] text-slate-500">😎 VOP – „GAG“ / USER‑FRIENDLY</div>
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

            @if ($page?->effective_from)
                <p class="mt-8 text-xs text-slate-500">
                    {{ __('main.terms_effective_from', ['date' => $page->effective_from->format('d.m.Y')]) }}
                </p>
            @endif
        </div>
    </section>
</x-layouts.pixel>
